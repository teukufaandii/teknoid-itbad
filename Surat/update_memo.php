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
        // Check if the memo field is already filled
        $sql_check = "SELECT memo FROM tb_srt_dosen WHERE id_srt = ?";
        $stmt_check = $koneksi->prepare($sql_check);
        $stmt_check->bind_param("i", $id_srt);
        $stmt_check->execute();
        $stmt_check->bind_result($existing_memo);
        $stmt_check->fetch();

        $memoFilled = !empty($existing_memo);
        echo json_encode(['success' => true, 'memoFilled' => $memoFilled]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
} elseif ($action === 'verifikasi') {
    $id_srt = $_POST['id_srt'];

    $sql = "UPDATE tb_srt_dosen SET verifikasi = 1 WHERE id_srt = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id_srt);

    if ($stmt->execute()) {
        // Check if the verifikasi field is already filled
        $sql_check = "SELECT verifikasi FROM tb_srt_dosen WHERE id_srt = ?";
        $stmt_check = $koneksi->prepare($sql_check);
        $stmt_check->bind_param("i", $id_srt);
        $stmt_check->execute();
        $stmt_check->bind_result($existing_verifikasi);
        $stmt_check->fetch();

        $verifikasiFilled = ($existing_verifikasi == 1);
        echo json_encode(['success' => true, 'verifikasiFilled' => $verifikasiFilled]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
}
