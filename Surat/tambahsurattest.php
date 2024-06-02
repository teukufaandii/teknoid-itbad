<?php
session_start(); // Mulai sesi jika belum dimulai

// Database connection
$servername = "localhost";
$username = "root"; // your MySQL username
$password = ""; // your MySQL password
$dbname = "db_teknoid"; // your database name
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Query untuk mengambil jenis surat dari tabel tb_jenis
$query_jenis_surat = "SELECT * FROM tb_jenis";
$result_jenis_surat = $conn->query($query_jenis_surat);

// Array untuk menyimpan opsi jenis surat
$jenis_surat_options = array();

// Periksa apakah query berhasil dieksekusi dan hasilnya ditemukan
if ($result_jenis_surat && $result_jenis_surat->num_rows > 0) {
    // Loop melalui hasil query untuk menambahkan opsi jenis surat ke dalam array
    while ($row_jenis_surat = $result_jenis_surat->fetch_assoc()) {
        $jenis_surat_options[] = $row_jenis_surat['nama_jenis']; // Simpan nama jenis surat
    }
}



// Fungsi untuk menghasilkan angka romawi dari angka biasa
function intToRoman($num) {
  $n = intval($num);
  $res = '';

  // Array untuk konversi angka romawi
  $roman_numerals = array(
      'M'  => 1000,
      'CM' => 900,
      'D'  => 500,
      'CD' => 400,
      'C'  => 100,
      'XC' => 90,
      'L'  => 50,
      'XL' => 40,
      'X'  => 10,
      'IX' => 9,
      'V'  => 5,
      'IV' => 4,
      'I'  => 1
  );

  foreach ($roman_numerals as $roman => $number) {
      // Hitung jumlah simbol romawi yang diperlukan
      $matches = intval($n / $number);
      // Tambahkan simbol romawi ke hasil
      $res .= str_repeat($roman, $matches);
      // Kurangi nilai yang telah ditambahkan ke hasil dari nilai asli
      $n = $n % $number;
  }

  return $res;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
  // Ambil nilai input dari form
  $asal_surat = $_POST['asal_surat'];
  $tujuan_surat = $_POST['tujuan_surat'];
  $deskripsi = $_POST['deskripsi'];
  $no_hp = $_POST['no_hp'];

  $perihal = $_POST['perihal'];
  $tanggal_surat = date("Y-m-d");

// Ambil id jenis surat yang dipilih
$id_jenis_surat = $conn->query("SELECT kd_jenissurat FROM tb_jenis WHERE nama_jenis = '" . $_POST['jenis_surat'] . "'")->fetch_assoc()['kd_jenissurat'];

// File upload logic
$file_berkas_name = $_FILES['file_berkas']['name'];
$file_berkas_tmp = $_FILES['file_berkas']['tmp_name'];
$file_laporan_name = $_FILES['file_laporan']['name'];
$file_laporan_tmp = $_FILES['file_laporan']['tmp_name'];
  

  // File upload logic
  $file_berkas_name = $_FILES['file_berkas']['name'];
  $file_berkas_tmp = $_FILES['file_berkas']['tmp_name'];
  $file_laporan_name = $_FILES['file_laporan']['name'];
  $file_laporan_tmp = $_FILES['file_laporan']['tmp_name'];

  // Mendapatkan nomor surat terakhir untuk bulan ini
  $current_month = date('n');
  $query_last_number = "SELECT MAX(SUBSTRING(kode_surat, 1, 3)) AS last_number FROM tb_surat_dis WHERE MONTH(tanggal_surat) = '$current_month'";
  $result_last_number = $conn->query($query_last_number);
  $last_number_row = $result_last_number->fetch_assoc();
  $last_number = $last_number_row['last_number'];

  // Jika tidak ada nomor surat untuk bulan ini, atur ke 0
  if ($last_number === null) {
      $last_number = 0;
  }

  // Nomor surat berikutnya
  $next_number = $last_number + 1;
  // Format nomor surat agar menjadi 3 digit dengan leading zeros
  $next_number_formatted = sprintf('%03d', $next_number);
  // Konversi angka bulan menjadi angka romawi
  $roman_month = intToRoman($current_month);
  // Tahun saat ini
  $current_year = date('Y');

  // Kode surat otomatis
  $kode_surat_otomatis = "$next_number_formatted/ITBAD/$roman_month/$current_year";

  // Move uploaded files to desired location
  $upload_berkas_dir = "uploads/berkas/";
  $upload_laporan_dir = "uploads/laporan/";

  if (move_uploaded_file($file_berkas_tmp, $upload_berkas_dir . $file_berkas_name) && move_uploaded_file($file_laporan_tmp, $upload_laporan_dir . $file_laporan_name)) {
    // Insert data into database
    $sql = "INSERT INTO tb_surat_dis (asal_surat, kode_surat, tujuan_surat, no_hp, perihal, jenis_surat, tanggal_surat, deskripsi) 
        VALUES ('$asal_surat', '$kode_surat_otomatis', '$tujuan_surat', '$no_hp', '$perihal', '$id_jenis_surat', '$tanggal_surat', '$deskripsi')";


    // Validasi ekstensi file
    $allowed_extension = array('pdf');
    $file_berkas_extension = pathinfo($_FILES['file_berkas']['name'], PATHINFO_EXTENSION);
    $file_laporan_extension = pathinfo($_FILES['file_laporan']['name'], PATHINFO_EXTENSION);

    if (!in_array($file_berkas_extension, $allowed_extension) || !in_array($file_laporan_extension, $allowed_extension)) {
        echo "Maaf, hanya file PDF yang diperbolehkan diunggah.";
        exit();
    }

    // Validasi ukuran file
    $max_file_size = 10 * 1024 * 1024; // 10MB dalam byte
    if ($_FILES['file_berkas']['size'] > $max_file_size || $_FILES['file_laporan']['size'] > $max_file_size) {
        echo "Maaf, ukuran file tidak boleh melebihi 10MB.";
        exit();
    }


    if ($conn->query($sql) === TRUE) {
        // Ambil ID surat yang baru saja dimasukkan
        $id_surat_baru = $conn->insert_id;

        // Simpan informasi file berkas ke dalam tabel file_berkas
        $sql_file_berkas = "INSERT INTO file_berkas (id_surat, nama_berkas) VALUES ('$id_surat_baru', '$file_berkas_name')";
        $conn->query($sql_file_berkas);

        // Simpan informasi file laporan ke dalam tabel file_laporan
        $sql_file_laporan = "INSERT INTO file_laporan (id_surat, nama_laporan) VALUES ('$id_surat_baru', '$file_laporan_name')";
        $conn->query($sql_file_laporan);

        echo "Data berhasil disimpan.";
        // Redirect to success page
        header("Location: surat_keluar.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Maaf, terjadi kesalahan saat mengunggah file.";
}
}
?>


<!doctype html>
<html lang="en">
    <head>
        <title>Tambah Surat - Teknoid</title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/x-icon" href="../logo itbad.png">
        <link href="css/dashboard-style.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    </head>
    <body>
        <!-- sidenav -->
        <?php include "sidenav.php" ?>

        <!-- content --> 
        <div class="content" id="Content">
            <!-- topnav -->
            <?php include "topnav.php" ?>
            
            <div class="mainContent" id="mainContent">
                <div class="contentBox">
                    <div class="pageInfo">  
                        <h3>Tambah Surat</h3>
                        <button class="back">Kembali</button>
                    </div>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form" enctype="multipart/form-data">
                        <div class="inputfield">
                            <label for="">Jenis Surat*</label>
                            <div class="custom_select">
                            <select name="jenis_surat">
                                
                            <?php
// Query untuk mendapatkan jenis surat dari tabel tb_jenis
$query_jenis_surat = "SELECT * FROM tb_jenis";
$result_jenis_surat = $conn->query($query_jenis_surat);

// Inisialisasi variabel jenis surat options
$jenis_surat_options = array();

// Periksa apakah ada hasil query
if ($result_jenis_surat->num_rows > 0) {
    // Loop melalui setiap baris hasil query
    while($row_jenis_surat = $result_jenis_surat->fetch_assoc()) {
        // Tampilkan opsi jenis surat
        $jenis_surat_options[] = "<option value='" . ($row_jenis_surat['nama_jenis'] == 'Surat Permohonan' ? 'Surat Permohonan' : 'Surat Laporan') . "'>" . $row_jenis_surat['nama_jenis'] . "</option>";
    }
} else {
    // Jika tidak ada hasil query, tampilkan opsi Tidak ada jenis surat yang tersedia
    $jenis_surat_options[] = '<option value="">Tidak ada jenis surat yang tersedia</option>';
}

// Tampilkan opsi jenis surat
echo implode("\n", $jenis_surat_options);
?>
                            </select>
                            </div>
                        </div>

                        <div class="inputfield">
                            <label for="">Asal Surat</label>
                            <input type="text" class="input" id="asal-surat" name="asal_surat" placeholder="Masukkan Asal Surat" autocomplete="off" required list="asal-surat-list">
                            <?php
                            // Query untuk mendapatkan tujuan surat dari tabel tb_asal
                            $query_asal_surat = "SELECT * FROM tb_asal";
                            $result_asal_surat = $conn->query($query_asal_surat);

                            // Inisialisasi variabel asal surat options
                            $asal_surat_options = "";

                            // Periksa apakah ada hasil query
                            if ($result_asal_surat->num_rows > 0) {
                                // Loop melalui setiap baris hasil query
                                while($row_asal_surat = $result_asal_surat->fetch_assoc()) {
                                    // Tampilkan opsi asal surat
                                    $asal_surat_options .= "<option value='" . $row_asal_surat['nama_asal'] . "'>" . $row_asal_surat['nama_asal'] . "</option>";
                                }
                            } else {
                                // Jika tidak ada hasil query, tampilkan opsi Tidak ada asal surat yang tersedia
                                $asal_surat_options = '<option value="">Tidak ada asal surat yang tersedia</option>';
                            }

                            // Tampilkan opsi asal surat
                            echo "<datalist id='asal-surat-list'>" . $asal_surat_options . "</datalist>";
                            ?>
                        </div> 

                        <div class="inputfield">
                            <label for="">Perihal*</label>
                            <input type="text" class="input" name="perihal" placeholder="Masukkan Perihal" autocomplete="off" required>
                        </div>

                        <div class="inputfield">
                            <label for="">Nomor Surat</label>
                            <input type="text" class="input" name="no_surat" placeholder="Masukkan Nomor Surat">
                        </div>

                        <div class="inputfield">
                            <label for="">Tujuan Surat*</label>
                            <input type="text" class="input" id="tujuan-surat" name="tujuan_surat" placeholder="Masukkan Tujuan Surat" autocomplete="off" required list="tujuan-surat-list">
                            <?php
// Query untuk mendapatkan tujuan surat dari tabel tb_tujuan
$query_tujuan_surat = "SELECT * FROM tb_tujuan";
$result_tujuan_surat = $conn->query($query_tujuan_surat);

// Inisialisasi variabel tujuan surat options
$tujuan_surat_options = "";

// Periksa apakah ada hasil query
if ($result_tujuan_surat->num_rows > 0) {
    // Loop melalui setiap baris hasil query
    while($row_tujuan_surat = $result_tujuan_surat->fetch_assoc()) {
        // Tampilkan opsi tujuan surat
        $tujuan_surat_options .= "<option value='" . $row_tujuan_surat['nama_tujuan'] . "'>" . $row_tujuan_surat['nama_tujuan'] . "</option>";
    }
} else {
    // Jika tidak ada hasil query, tampilkan opsi Tidak ada tujuan surat yang tersedia
    $tujuan_surat_options = '<option value="">Tidak ada tujuan surat yang tersedia</option>';
}

// Tampilkan opsi tujuan surat
echo "<datalist id='tujuan-surat-list'>" . $tujuan_surat_options . "</datalist>";
?>
                        </div>

                        <div class="inputfield">
                            <label for="">Nomor Telepon*</label>
                            <input type="number" class="input" name="no_hp" placeholder="Masukkan Nomor Telepon" required>
                        </div>

                        <div class="inputfield">
                            <label for="">Deskripsi Singkat</label>
                            <input type="text" class="input" name="deskripsi" placeholder="Masukkan Deskripsi Singkat" autocomplete="off" maxlength="100">
                        </div>

                        <div class="inputfield">
                            <label for="">Unggah Berkas Surat</label>
                            <input type="file"  class="input" name="file_berkas" id="file_berkas" accept="application/pdf">
                        </div>

                        <div class="inputfield" name="file_laporan">                            
                            <label for="">Unggah Berkas Laporan</label>
                            <input type="file" class="input" name="file_laporan" id="file_laporan" accept="application/pdf">
                           
                        </div>

                        <div class="btn-kirim">
                            <div class="floatFiller">ff</div>
                            <input class="btn" type="submit" name="submit" value="Kirim">
                        </div>
                    </form>
                    
                </div>
            </div>
            <?php include './footer.php'; ?>
        </div>
        <script src="js/dashboard-js.js"></script>
        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
        <script>
    // Ambil referensi elemen HTML yang diperlukan
    const jenisSuratSelect = document.querySelector('[name="jenis_surat"]');
    const fileBerkasInput = document.querySelector('[name="file_berkas"]');
    const fileLaporanInput = document.querySelector('[name="file_laporan"]');

    // Tambahkan event listener untuk elemen jenis surat
    jenisSuratSelect.addEventListener('change', () => {
        // Periksa apakah jenis surat yang dipilih adalah Surat Laporan
        if (jenisSuratSelect.value === 'Surat Laporan') {
            // Tampilkan elemen input file laporan
            fileLaporanInput.style.display = 'block';
        } else {
            // Sembunyikan elemen input file laporan
            fileLaporanInput.style.display = 'none';
        }
    });

    // Inisialisasi status awal elemen input file laporan
    if (jenisSuratSelect.value !== 'Surat Laporan') {
        fileLaporanInput.style.display = 'none';
    }
</script>
   </body>
</html>
<?php
    $conn->close();
?>
