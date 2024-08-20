<?php
include '../koneksi.php';

header('Content-Type: application/json'); // Set the Content-Type to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_srt = $_POST['id_srt'];

    if ($stmt = $conn->prepare("SELECT memo FROM tb_srt_dosen WHERE id_srt = ?")) {
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
    } else {
        // If the statement could not be prepared, send an error response
        echo json_encode(['exists' => false, 'error' => 'Database query failed']);
    }
} else {
    echo json_encode(['exists' => false, 'error' => 'Invalid request method']);
}
?>
