<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
include "logout-checker.php";
// Database connection
include 'koneksi.php';

require __DIR__ . '/vendor/autoload.php';

// Twilio credentials
$twilioAccountSid = 'AC79cd2a708304e613cfcad1ad359c1e7e';
$twilioAuthToken = 'a8ec8cad0a82ab944c71177f65301a5e';
$twilioPhoneNumber = '+14155238886'; // Your Twilio phone number for WhatsApp 

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$ip_address = $_SERVER['REMOTE_ADDR'];
$time_window = 60; // 60 detik
$max_requests = 5; // Maksimal 5 permintaan
$timestamp = time();

// Variabel untuk batas waktu
$start_time = $timestamp - $time_window;

// Query untuk menghitung jumlah permintaan dalam periode waktu tertentu
$query_rate_limit = "SELECT COUNT(*) as request_count FROM request_log WHERE ip_address = ? AND timestamp > ?";
$stmt = $conn->prepare($query_rate_limit);
$stmt->bind_param("si", $ip_address, $start_time); // Gunakan variabel $start_time
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['request_count'] >= $max_requests) {
    die("Terlalu banyak permintaan. Silakan coba lagi nanti.");
}

// Query untuk mencatat permintaan
$log_request = "INSERT INTO request_log (ip_address, timestamp) VALUES (?, ?)";
$stmt = $conn->prepare($log_request);
$stmt->bind_param("si", $ip_address, $timestamp);
$stmt->execute();

function sendWhatsAppMessage($to, $message)
{
    global $twilioAccountSid, $twilioAuthToken, $twilioPhoneNumber;

    // Initialize Twilio client
    $twilio = new Twilio\Rest\Client($twilioAccountSid, $twilioAuthToken);

    try {
        // Send WhatsApp message
        $message = $twilio->messages->create(
            "whatsapp:$to", // to
            [
                "from" => "whatsapp:$twilioPhoneNumber", // from
                "body" => $message,
            ]
        );
        return $message->sid; // Return message SID on success
    } catch (Exception $e) {
        return false; // Return false on failure
    }
}

// Fungsi untuk menghasilkan angka romawi dari angka biasa
function intToRoman($num)
{
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
    // Ambil nilai input dari form dan sanitasi
    $asal_surat = $conn->real_escape_string($_POST['asal_surat']);
    $tujuan_surat = $conn->real_escape_string($_POST['tujuan_surat']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);
    $email = $conn->real_escape_string($_POST['email']);
    $id_jenis_surat = $conn->real_escape_string($_POST['jenis_surat']);
    $perihal = $conn->real_escape_string($_POST['perihal']);
    $tanggal_surat = date("Y-m-d");
    $no_surat = $conn->real_escape_string($_POST['no_surat']);

    // Mendapatkan tahun saat ini
    $current_year = date('Y');

    // Mendapatkan nomor surat terakhir untuk tahun ini
    $query_last_number = "SELECT MAX(SUBSTRING(kode_surat, 1, 3)) AS last_number FROM tb_surat_dis WHERE YEAR(tanggal_surat) = ?";
    $stmt = $conn->prepare($query_last_number);
    $stmt->bind_param("i", $current_year);
    $stmt->execute();
    $result_last_number = $stmt->get_result();
    $last_number_row = $result_last_number->fetch_assoc();
    $last_number = $last_number_row['last_number'];

    // Jika tidak ada nomor surat untuk tahun ini, atur ke 0
    if ($last_number === null) {
        $last_number = 0;
    }

    // Nomor surat berikutnya
    $next_number = intval($last_number) + 1;
    // Format nomor surat agar menjadi 3 digit dengan leading zeros
    $next_number_formatted = sprintf('%03d', $next_number);
    // Konversi angka bulan menjadi angka romawi
    $current_month = date('n');
    $roman_month = intToRoman($current_month);

    // Kode surat otomatis
    $kode_surat_otomatis = "$next_number_formatted/ITBAD/$roman_month/$current_year";

    // Move uploaded files to desired location
    $upload_berkas_dir = "uploads/berkas/";
    $upload_laporan_dir = "uploads/laporan/";

    // Check if both files are uploaded or not
    $is_berkas_uploaded = !empty($_FILES['file_berkas']['name']) && is_uploaded_file($_FILES['file_berkas']['tmp_name']);
    $is_laporan_uploaded = !empty($_FILES['file_laporan']['name']) && is_uploaded_file($_FILES['file_laporan']['tmp_name']);

    if (!$is_berkas_uploaded && !$is_laporan_uploaded) {
        echo "Maaf, anda harus mengunggah setidaknya satu file (berkas atau laporan).";
        exit();
    }

    // Validasi ukuran file
    $max_file_size = 10 * 1024 * 1024; // 10MB dalam byte
    $allowed_file_types = ['application/pdf']; // Allowed MIME types

    if ($is_berkas_uploaded) {
        $file_berkas_type = $_FILES['file_berkas']['type'];
        if ($_FILES['file_berkas']['size'] > $max_file_size) {
            header("Location: error.php?error=filesize");
            exit();
        }
        if (!in_array($file_berkas_type, $allowed_file_types)) {
            header("Location: error.php?error=filetype");
            exit();
        }
    }

    if ($is_laporan_uploaded) {
        $file_laporan_type = $_FILES['file_laporan']['type'];
        if ($_FILES['file_laporan']['size'] > $max_file_size) {
            header("Location: error.php?error=filesize");
            exit();
        }
        if (!in_array($file_laporan_type, $allowed_file_types)) {
            header("Location: error.php?error=filetype");
            exit();
        }
    }

    // Insert data into database using prepared statement
    $sql = "INSERT INTO tb_surat_dis (asal_surat, kode_surat, tujuan_surat, no_hp, perihal, nomor_surat, jenis_surat, tanggal_surat, deskripsi, email, status_selesai, status_tolak) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $asal_surat, $kode_surat_otomatis, $tujuan_surat, $no_hp, $perihal, $no_surat, $id_jenis_surat, $tanggal_surat, $deskripsi, $email);

    if ($stmt->execute()) {
        // Ambil ID surat yang baru saja dimasukkan
        $id_surat_baru = $conn->insert_id;

        $sql_new_entry = "INSERT INTO tb_disposisi (id_surat, perihal, diteruskan_ke) VALUES (?, ?, 'Rektor')";
        $stmt_new_entry = $conn->prepare($sql_new_entry);
        $stmt_new_entry->bind_param("is", $id_surat_baru, $perihal);

        if ($stmt_new_entry->execute()) {
        } else {
            // Tampilkan pesan error jika gagal ```php
            echo "Error: " . $stmt_new_entry->error;
        }

        if ($is_berkas_uploaded) {
            $file_berkas_name = uniqid() . '_' . $_FILES['file_berkas']['name']; // Mendapatkan nama file yang diunggah
            $sql_file_berkas = "INSERT INTO file_berkas (id_surat, nama_berkas) VALUES (?, ?)";
            $stmt_file_berkas = $conn->prepare($sql_file_berkas);
            $stmt_file_berkas->bind_param("is", $id_surat_baru, $file_berkas_name);
            $stmt_file_berkas->execute();
            move_uploaded_file($_FILES['file_berkas']['tmp_name'], $upload_berkas_dir . $file_berkas_name); // Simpan file dengan nama yang sesuai
        }

        // Simpan informasi file laporan ke dalam tabel file_laporan
        if ($is_laporan_uploaded) {
            $file_laporan_name = uniqid() . '_' . $_FILES['file_laporan']['name'];
            $sql_file_laporan = "INSERT INTO file_laporan (id_surat, nama_laporan) VALUES (?, ?)";
            $stmt_file_laporan = $conn->prepare($sql_file_laporan);
            $stmt_file_laporan->bind_param("is", $id_surat_baru, $file_laporan_name);
            $stmt_file_laporan->execute();
            move_uploaded_file($_FILES['file_laporan']['tmp_name'], $upload_laporan_dir . $file_laporan_name);
        }

        $update_url_sql = "UPDATE tb_surat_dis SET diteruskan_ke = 'Rektor' WHERE id_surat = ?";
        $stmt_update_url = $conn->prepare($update_url_sql);
        $stmt_update_url->bind_param("i", $id_surat_baru);
        if ($stmt_update_url->execute()) {
            echo "<script> setTimeout(function() {
            window.location.href = 'surat_keluar.php';}, 1000);
            </script>";
        }
    } else {
        // Tampilkan pesan error SQL
        echo "Error: " . $stmt->error;
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <a href="surat_keluar.php"> <button class="back">Kembali</button> </a>
                </div>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form" enctype="multipart/form-data">
                    <div class="inputfield">
                        <label for="">Jenis Surat*</label>
                        <div class="custom_select">
                            <select name="jenis_surat" id="jenis_surat">
                                <?php
                                // Query untuk mendapatkan jenis surat dari tabel tb_jenis
                                $query_jenis_surat = "SELECT * FROM tb_jenis LIMIT 2";
                                $result_jenis_surat = $conn->query($query_jenis_surat);

                                // Periksa apakah ada hasil query
                                if ($result_jenis_surat->num_rows > 0) {
                                    // Loop melalui setiap baris hasil query
                                    while ($row_jenis_surat = $result_jenis_surat->fetch_assoc()) {
                                        // Tampilkan opsi jenis surat
                                        echo "<option value='" . $row_jenis_surat['kd_jenissurat'] . "'>" . $row_jenis_surat['nama_jenis'] . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>Tidak ada jenis surat yang tersedia</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="inputfield">
                        <label for="">Asal Surat</label>
                        <input class="input" type="text" name="asal_surat" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" readonly>
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
                        <div class="custom_select">
                            <select type="text" class="input" name="tujuan_surat" placeholder="Masukkan Tujuan Surat" autocomplete="off" required>
                                <option hidden disabled selected value="">Pilih Unit</option>
                                <option>Rektor</option>
                                <option>Wakil Rektor 1</option>
                                <option>Wakil Rektor 2</option>
                                <option>Wakil Rektor 3</option>
                                <option>Direktur Pasca Sarjana</option>
                                <option>Dekan FTD</option>
                                <option>Dekan FEB</option>
                                <option>Prodi S2 Keuangan Syariah</option>
                                <option>Prodi S1 Sistem Informasi</option>
                                <option>Prodi S1 Teknologi Informasi</option>
                                <option>Prodi S1 Desain Komunikasi Visual</option>
                                <option>Prodi S1 Arsitektur</option>
                                <option>Prodi S1 Manajemen</option>
                                <option>Prodi S1 Akuntansi</option>
                                <option>Prodi D3 Akuntansi</option>
                                <option>Prodi D3 Keuangan dan Perbankan</option>
                                <option>Unit Humas</option>
                                <option>Unit Keuangan</option>
                                <option>Unit Umum</option>
                                <option>Unit Marketing</option>
                                <option>Unit BPM</option>
                                <option>Unit Akademik</option>
                                <option>Unit Perpustakaan</option>
                                <option>Unit LP3M</option>
                                <option>Unit KUI dan Kerjasama</option>
                                <option>Unit PPIK dan Kemahasiswaan</option>
                                <option>Unit IT & Laboratorium</option>
                                <!-- Daftar tujuan surat lainnya -->
                            </select>
                        </div>
                    </div>

                    <div class="inputfield">
                        <label for="">Nomor Telepon*</label>
                        <input type="number" class="input" name="no_hp" placeholder="Masukkan Nomor Telepon" value="<?php echo isset($_SESSION['phone_number']) ? $_SESSION['phone_number'] : ''; ?>" required>
                    </div>

                    <div class="inputfield">
                        <label for="">Email*</label>
                        <input type="text" class="input" name="email" placeholder="Masukkan Alamat Email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" readonly>
                    </div>

                    <div class="inputfield">
                        <label for="">Deskripsi Singkat*</label>
                        <input type="text" class="input" name="deskripsi" placeholder="Masukkan Deskripsi Singkat" autocomplete="off" maxlength="100" required>
                    </div>

                    <div class="inputfield">
                        <label for="">Unggah Berkas Surat</label>
                        <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                        <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                    </div>

                    <div class="inputfieldhidden" id="uploadBerkasLaporan" style="display: none;">
                        <div class="inputfield">
                            <label for="">Unggah Berkas Laporan</label>
                            <input type="file" class="input" name="file_laporan" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                        </div>
                    </div>

                    <div class="btn-kirim">
                        <div class="floatFiller">ff</div>
                        <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                        <button class="btn" type="button" onclick="showConfirmationPopup()">Kirim</button>
                    </div>
                </form>

            </div>
        </div>
        <?php include './footer.php'; ?>
    </div>

    <script>
        // Function to show SweetAlert confirmation popup
        function showConfirmationPopup() {
            Swal.fire({
                title: 'Konfirmasi?',
                text: 'Anda yakin ingin mengirim surat?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, kirim!',
                cancelButtonText: 'Tidak, periksa kembali',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, trigger form submission
                    document.getElementById('submitForm').click();
                }
            });
        }

        // Function to handle form submission response
        function handleFormSubmissionResponse(success) {
            if (success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'The letter has been sent successfully.',
                    icon: 'success'
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to send the letter. Please try again later.',
                    icon: 'error'
                });
            }
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var jenisSuratDropdown = document.getElementById('jenis_surat');
            var uploadBerkasLaporan = document.getElementById('uploadBerkasLaporan');

            // Event listener untuk perubahan pada dropdown jenis surat
            jenisSuratDropdown.addEventListener('change', function() {
                var selectedOption = this.value;

                // Tampilkan elemen unggah berkas laporan jika jenis surat yang dipilih adalah "Surat Laporan" (nilai 2)
                if (selectedOption === '2') {
                    uploadBerkasLaporan.style.display = 'block';
                    // Tambahkan atribut required ke input file laporan
                    document.querySelector('input[name="file_laporan"]').setAttribute('required', 'required');
                } else {
                    uploadBerkasLaporan.style.display = 'none';
                    // Hapus atribut required dari input file laporan jika jenis surat yang dipilih bukan "Surat Laporan"
                    document.querySelector('input[name="file_laporan"]').removeAttribute('required');
                }
            });

            // Inisialisasi tampilan berdasarkan nilai default dropdown saat halaman dimuat
            var initialOption = jenisSuratDropdown.value;
            if (initialOption === '2') {
                uploadBerkasLaporan.style.display = 'block';
                document.querySelector('input[name="file_laporan"]').setAttribute('required', 'required');
            } else {
                uploadBerkasLaporan.style.display = 'none';
                document.querySelector('input[name="file_laporan"]').removeAttribute('required');
            }
        });
    </script>
    <script src="js/dashboard-js.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>

<?php
// Tutup koneksi database
$conn->close();
?>