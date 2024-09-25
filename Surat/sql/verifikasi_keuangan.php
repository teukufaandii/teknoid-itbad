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

// Check current value of verifikasi_keuangan
$stmt = $conn->prepare("SELECT verifikasi FROM tb_srt_dosen WHERE id_srt = ?");
$stmt->bind_param("s", $id_srt);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['verifikasi'] == 0) {
    // If verifikasi_keuangan is 0, display error message
    echo "Harap verifikasi terlebih dahulu";
} else {
    // If verifikasi_keuangan is 1, update the row
    $stmt = $conn->prepare("UPDATE tb_srt_dosen SET verifikasi_keuangan = 1 WHERE id_srt = ?");
    $stmt->bind_param("s", $id_srt);
    $stmt->execute();
}

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
