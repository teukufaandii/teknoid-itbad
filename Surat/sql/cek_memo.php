<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_srt = $_POST['id_srt'];

    $stmt = $conn->prepare("SELECT memo FROM tb_srt_dosen WHERE id_srt = ?");
    $stmt->bind_param("i", $id_srt);
    $stmt->execute();
    $stmt->bind_result($memo);
    $stmt->fetch();
    $stmt->close();

    if ($memo) {
        echo json_encode(['exists' => true, 'memo' => $memo]);
    } else {
        echo json_encode(['exists' => false]);
    }
}
?>
