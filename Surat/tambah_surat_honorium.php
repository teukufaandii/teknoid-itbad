<?php
session_start(); // Mulai sesi jika belum dimulai
include "logout-checker.php";
include "koneksi.php";

require __DIR__ . '/vendor/autoload.php';

$fullname = $_SESSION['nama_lengkap'];

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai input dari form
    $jenis_surat = $_POST['jenis_surat']; // Mengambil id jenis surat yang dipilih
    $asal_surat = $_POST['asal_surat'];
    $nm_kegiatan = $_POST['nm_kegiatan'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal_surat = date("d-m-Y");
    $ke_keuangan = "keuangan";


    // Move uploaded files to desired location
    $upload_berkas_dir = "uploads/honorium";

    // Check if both files are uploaded or not
    $is_berkas_uploaded = !empty($_FILES['file_berkas']['name']) && is_uploaded_file($_FILES['file_berkas']['tmp_name']);

    if (!$is_berkas_uploaded) {
        echo "Maaf, anda harus mengunggah file berkas.";
        exit();
    }

    // Validasi ukuran file
    $max_file_size = 10 * 1024 * 1024; // 10MB dalam byte
    if ($is_berkas_uploaded && $_FILES['file_berkas']['size'] > $max_file_size) {
        echo "Maaf, ukuran file berkas surat tidak boleh melebihi 10MB.";
        exit();
    }

    // Insert data into database
    $sql = "INSERT INTO tb_srt_honor (asal_surat, jenis_surat, nm_kegiatan, deskripsi, tanggal_surat) 
            VALUES ('$asal_surat', '$jenis_surat', '$nm_kegiatan', '$deskripsi', '$tanggal_surat')";

    if (mysqli_query($conn, $sql)) {
        // If the query was successful, redirect to success.php
        header('Location: success.php');
        exit(); // Ensure no further code is executed
    } else {
        // If the query failed, get the error code and message
        $error_code = mysqli_errno($conn);
        $error_message = mysqli_error($conn);
    }

    if ($conn->query($sql) === TRUE) {
        // Ambil ID surat yang baru saja dimasukkan
        $id_surat_baru = $conn->insert_id;

        // Simpan informasi file berkas ke dalam tabel file_berkas
        if ($is_berkas_uploaded) {
            $file_berkas_name = uniqid() . '_' . $_FILES['file_berkas']['name']; // Mendapatkan nama file yang diunggah
            $sql_file_berkas = "INSERT INTO tb_srt_honor (id_surat, berkas) VALUES ('$id_surat_baru', '$file_berkas_name')";
            $conn->query($sql_file_berkas);
            move_uploaded_file($_FILES['file_berkas']['tmp_name'], $upload_berkas_dir . $file_berkas_name); // Simpan file dengan nama yang sesuai
        }

        $update_url_sql = "UPDATE tb_srt_honor SET diteruskan_ke = 'keuangan' WHERE id_surat = '$id_surat_baru'";
        if ($conn->query($update_url_sql) === TRUE)

            echo "<script> setTimeout(function() {
            window.location.href = 'surat_keluar.php';}, 1000);
            </script>";
    } else {
        // Tampilkan pesan error SQL
        echo "Error: " . $sql . "<br>" . $conn->error;
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
                
                    <input name="jenis_surat" id="jenis_surat" value="7" hidden>
                               
                    <div class="inputfield">
                        <label for="">Asal Surat</label>
                        <input class="input" type="text" name="asal_surat" value="<?php echo $fullname ?>" readonly>
                    </div>

                    <div class="inputfield">
                        <label for="ttl">Nama Kegiatan*</label>
                        <input type="text" class="input" name="nm_kegiatan" id="nm_kegiatan" placeholder="Masukkan Nama Kegiatan">
                    </div>

                    <div class="inputfield">
                        <label for="">Deskripsi Singkat*</label>
                        <input type="text" class="input" name="deskripsi" placeholder="Masukkan Deskripsi Singkat" autocomplete="off" maxlength="100" required>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var select = document.getElementById("unitSelect");

                            select.addEventListener("change", function() {
                                if (this.value === "") {
                                    this.selectedIndex = -1; // Reset the selected index to none
                                }
                            });
                        });
                    </script>

                    <div class="inputfield">
                        <label for="">Unggah Berkas Surat</label>
                        <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                        <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                    </div>

                    <div class="btn-kirim">
                        <div class="floatFiller">ff</div>
                        <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                        <button class="btn" type="button" onclick="showConfirmationPopup()">Kirim</button>
                    </div>
                </form>

            </div>
            <?php include './footer.php'; ?>
        </div>
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

    <script src="js/dashboard-js.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>

<?php
// Tutup koneksi database
$conn->close();
?>