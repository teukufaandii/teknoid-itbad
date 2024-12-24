<?php
session_start();
include 'koneksi.php';

if (isset($_POST['id'])) {
    $id_surat = $_POST['id'];
    $result = $conn->query("SELECT status_baca FROM tb_surat_dis WHERE id_surat = '$id_surat'");
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row); // Return the status_baca as JSON
    } else {
        echo json_encode(['status_baca' => null]); // Return null if not found
    }
} else {
    echo json_encode(['status_baca' => null]); // Return null if no ID provided
}
$conn->close();
?>