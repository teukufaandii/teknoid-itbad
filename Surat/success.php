<?php
include 'koneksi.php';

session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
$id_srt = isset($_GET['id_srt']) ? $_GET['id_srt'] : 'Undefined';
$jabatan = isset($_SESSION['jabatan']) ? $_SESSION['jabatan'] : 'Undefined';

$query = "SELECT jenis_insentif FROM tb_srt_dosen WHERE id_srt = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_srt);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $jenis = $row['jenis_insentif'];
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Success</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../logo itbad.png">
    <link rel="stylesheet" href="css/success.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                <?php if ($jabatan == 'Dosen' && !empty($jenis)) { ?>
                    window.location.href = "./surat_keluar_nondis";
                <?php } elseif (($jabatan == 'S2 Keuangan Syariah' ||
                    $jabatan == 'S1 SI' || $jabatan == 'S1 TI' || $jabatan == 'S1 DKV' ||
                    $jabatan == 'S1 Arsitektur' || $jabatan == 'S1 Manajemen' ||
                    $jabatan == 'S1 Akuntansi') && empty($jenis)) { ?>
                    window.location.href = "./surat_keluar_honorium";
                <?php } else { ?>
                    window.location.href = "./surat_keluar";
                <?php } ?>
            }, 3000);
        });
    </script>
</head>

<body>
    <div class="container text-center">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6">
                <div class="success-animation mt-5">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                    </svg>
                    <h1 class="display-4">Sukses!</h1>
                    <p class="lead">Surat berhasil dikirim!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>