<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_srt = $_POST['id_srt'];
    $memo = $_POST['memo'];

    // Cek apakah memo sudah ada untuk surat ini
    $stmt = $conn->prepare("SELECT memo FROM tb_srt_dosen WHERE id_srt = ?");
    $stmt->bind_param("i", $id_srt);
    $stmt->execute();
    $stmt->bind_result($existingMemo);
    $stmt->fetch();
    $stmt->close();

    if ($existingMemo) {
        // Update memo
        $stmt = $conn->prepare("UPDATE tb_srt_dosen SET memo = ? WHERE id_srt = ?");
        $stmt->bind_param("si", $memo, $id_srt);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    } else {
        // Insert new memo
        $stmt = $conn->prepare("UPDATE tb_srt_dosen SET memo = ? WHERE id_srt = ?");
        $stmt->bind_param("si", $memo, $id_srt);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $stmt->close();
    }
}
?>
