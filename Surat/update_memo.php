<?php
include 'koneksi.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'kirim') {
    $memo = $_POST['memo'];
    $id_srt = $_POST['id_srt'];

    $sql = "UPDATE tb_srt_dosen SET memo = ? WHERE id_srt = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("si", $memo, $id_srt);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
} elseif ($action === 'verifikasi') {
    $id_srt = $_POST['id_srt'];

    $sql = "UPDATE tb_srt_dosen SET verifikasi = 1 WHERE id_srt = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id_srt);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
}
