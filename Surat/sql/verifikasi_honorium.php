<?php
session_start();
include '../koneksi.php';

// Periksa apakah session username telah diatur
if (!isset($_SESSION['pengguna_type'])) {
    echo 'Unauthorized';
    exit;
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("UPDATE tb_srt_honor SET status = 1 WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();

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
