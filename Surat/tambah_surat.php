<?php
session_start(); // Mulai sesi jika belum dimulai
include "logout-checker.php";
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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
  // Ambil nilai input dari form
  $asal_surat = $_POST['asal_surat'];
  $tujuan_surat = $_POST['tujuan_surat'];
  $deskripsi = $_POST['deskripsi'];
  $no_hp = $_POST['no_hp'];
  $id_jenis_surat = $_POST['jenis_surat']; // Mengambil id jenis surat yang dipilih
  $perihal = $_POST['perihal'];
  $tanggal_surat = date("Y-m-d");

  // File upload logic
  $file_berkas_name = $_FILES['file_berkas']['name'];
  $file_berkas_tmp = $_FILES['file_berkas']['tmp_name'];


  // Move uploaded files to desired location
  $upload_berkas_dir = "uploads/berkas/";

  if (move_uploaded_file($file_berkas_tmp, $upload_berkas_dir . $file_berkas_name)) {
    // Insert data into database
    $sql = "INSERT INTO tb_surat_dis (asal_surat, tujuan_surat, no_hp, perihal, jenis_surat, tanggal_surat, deskripsi) 
        VALUES ('$asal_surat', '$tujuan_surat', '$perihal', '$no_hp', '$id_jenis_surat', '$tanggal_surat', '$deskripsi')";


    // Validasi ekstensi file
    $allowed_extension = array('pdf');
    $file_berkas_extension = pathinfo($_FILES['file_berkas']['name'], PATHINFO_EXTENSION);

    if (!in_array($file_berkas_extension, $allowed_extension)) {
        echo "Maaf, hanya file PDF yang diperbolehkan diunggah.";
        exit();
    }


    // Validasi ukuran file
    $max_file_size = 10 * 1024 * 1024; // 10MB dalam byte
    if ($_FILES['file_berkas']['size'] > $max_file_size) {
        echo "Maaf, ukuran file tidak boleh melebihi 10MB.";
        exit();
    }




    if ($conn->query($sql) === TRUE) {
        // Ambil ID surat yang baru saja dimasukkan
        $id_surat_baru = $conn->insert_id;

        // Simpan informasi file berkas ke dalam tabel file_berkas
        $sql_file_berkas = "INSERT INTO file_berkas (id_surat, nama_berkas) VALUES ('$id_surat_baru', '$file_berkas_name')";
        $conn->query($sql_file_berkas);

        $update_url_sql = "UPDATE tb_surat_dis SET diteruskan_ke = 'Humas' WHERE id_surat = '$id_surat_baru'";
        if ($conn->query($update_url_sql) === TRUE) {
            echo "Data berhasil disimpan.";
        } else {
            echo "Error: " . $update_url_sql . "<br>" . $conn->error;
        }

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
                        <a href="surat_keluar.php"><button class="back">Kembali</button></a>
                    </div>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form" enctype="multipart/form-data">
                    <div class="inputfield" hidden>
                        <label for=""></label>
                        <div class="custom_select" hidden>
                            <select name="jenis_surat" id="" class="select" required hidden>
                                <?php
                                // Query untuk mendapatkan jenis surat dari tabel tb_jenis
                                $query_jenis_surat = "SELECT * FROM tb_jenis";
                                $result_jenis_surat = $conn->query($query_jenis_surat);

                                // Periksa apakah ada hasil query
                                if ($result_jenis_surat->num_rows > 0) {
                                    // Loop melalui setiap baris hasil query
                                    while ($row_jenis_surat = $result_jenis_surat->fetch_assoc()) {
                                        // Tentukan apakah opsi harus dipilih secara otomatis jika kd_jenissurat adalah 3
                                        if ($row_jenis_surat['kd_jenissurat'] == 3) {
                                            // Tampilkan opsi jenis surat "Non Disposisi" dan buat dipilih secara otomatis
                                            echo "<option value='" . $row_jenis_surat['kd_jenissurat'] . "' selected>" . $row_jenis_surat['nama_jenis'] . "</option>";
                                        }
                                    }
                                } else {
                                    echo "<option value='' disabled>Tidak ada jenis surat yang tersedia</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>



                        <div class="inputfield">
                            <label for="">Asal Surat*</label>
                            <input type="text" class="input" name="asal_surat" placeholder="Masukkan Asal Surat" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" readonly>
                        </div>
 
                        <div class="inputfield">
                            <label for="">Perihal*</label>
                            <input type="text" class="input" name="perihal" placeholder="Masukkan Perihal" required>
                        </div>

                        <div class="inputfield">
                            <label for="">Nomor Surat</label>
                            <input type="text" class="input" name="no_surat" placeholder="Masukkan Nomor Surat">
                        </div>
                        
                        <div class="inputfield">
                        <label for="">Tujuan Surat*</label>
                        <div class="custom_select">
                            <select type="text" class="input" name="tujuan_surat" placeholder="Masukkan Tujuan Surat" autocomplete="off" required>
                                <option hidden></option>
                                <option>Rektor</option>
                                <option>Warek 1</option>
                                <option>Warek 2</option>
                                <option>Warek 3</option>
                                <option>Sekertaris</option>
                                <option>Dekan FTD</option>
                                <option>Dekan FEB</option>
                                <option>Prodi S1 Sistem Informasi</option>
                                <option>Prodi S1 Teknologi Informasi</option>
                                <option>Prodi S1 Desain Komunikasi Visual</option>
                                <option>Prodi S1 Arsitektur</option>
                                <option>Prodi S1 Manajemen</option>
                                <option>Prodi S1 Akuntansi</option>
                                <option>Prodi D3 Akuntansi</option>
                                <option>Prodi D3 keuangan dan perbankan Syariah</option>
                                <option>Pasca Sarjana</option>
                                <option>Unit Keuangan</option>
                                <option>Unit Umum</option>
                                <option>Unit Marketing</option>
                                <option>Unit Akademik</option>
                                <option>Unit Perpustakaan</option>
                                <option>Unit LP3M</option>
                                <option>Unit KUI dan Kerjasama</option>
                                <option>Unit PPIK dan Kemahasiswaan</option>
                                <option>Unit IT & Laboratorium</option>
                                <option>Dosen</option>
                                    <option>Mahasiswa</option>
                                <!-- Daftar tujuan surat lainnya -->
                            </select>
                        </div>
                    </div>

                        <div class="inputfield">
                            <label for="">Nomor Telepon*</label>
                            <input type="number" class="input" name="no_hp" placeholder="Masukkan Nomor Telepon" required>
                        </div>

                        <div class="inputfield">
                            <label for="">Deskripsi Singkat</label>
                            <input type="text" class="input" name="deskripsi" placeholder="Masukkan Deskripsi Singkat" maxlength="200">
                        </div>

                        <div class="inputfield">
                            <label for="">Unggah Berkas Surat</label>
                            <input type="file" class="input" name="file_berkas" id="file_berkas" value="PDF only" accept="application/pdf">
                            <p style='color:red'> max size 10mb*</p>
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
    </body>
</html>
<?php
    $conn->close();
?>