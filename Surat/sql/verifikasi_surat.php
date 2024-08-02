<?php
session_start();
include '../koneksi.php';

// Periksa apakah session username telah diatur
if (!isset($_SESSION['pengguna_type'])) {
    echo 'Unauthorized';
    exit;
}

if (isset($_POST['id_srt'])) {
    $id_srt = $_POST['id_srt'];

    // Update status verifikasi surat
    $stmt = $conn->prepare("UPDATE tb_srt_dosen SET verifikasi = 1 WHERE id_srt = ?");
    $stmt->bind_param("s", $id_srt);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'error';
}
?>
