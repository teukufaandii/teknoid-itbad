<?php
session_start();
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_srt = $_POST['id_srt'];
    $memo = $_POST['memo'];

    // Check if a memo already exists for the given id_srt
    $stmt = $conn->prepare("SELECT memo FROM tb_srt_dosen WHERE id_srt = ?");
    $stmt->bind_param("i", $id_srt);
    $stmt->execute();
    $stmt->bind_result($existingMemo);
    $stmt->fetch();
    $stmt->close();

    if ($existingMemo) {
        echo 'exists';
        $conn->close();
        exit;
    }

    // Insert the memo if it doesn't already exist
    $stmt = $conn->prepare("UPDATE tb_srt_dosen SET memo = ? WHERE id_srt = ?");
    $stmt->bind_param("si", $memo, $id_srt);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
    $conn->close();
}
?>