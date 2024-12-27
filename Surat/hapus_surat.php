<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json'); // Set the content type to JSON

if (isset($_GET['id'])) {
    $id_surat = $_GET['id'];

    // Cek status_baca dari surat yang ingin dihapus
    $result = $conn->query("SELECT status_baca FROM tb_surat_dis WHERE id_surat = '$id_surat'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['status_baca'] == 0) {
            // Jika status_baca = 0, hapus surat
            $delete_sql = "DELETE FROM tb_surat_dis WHERE id_surat = '$id_surat'";
            if ($conn->query($delete_sql) === TRUE) {
                // Hapus dari tb_disposisi dengan id_surat yang sama
                $delete_disposisi_sql = "DELETE FROM tb_disposisi WHERE id_surat = '$id_surat'";
                if ($conn->query($delete_disposisi_sql) === TRUE) {
                    echo json_encode(['success' => true, 'message' => 'Surat dan disposisi berhasil dihapus.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Surat berhasil dihapus, tetapi gagal menghapus disposisi.', 'error' => $conn->error]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus surat.', 'error' => $conn->error]);
            }
        } else {
            // Jika status_baca = 1, tidak bisa dihapus
            echo json_encode(['success' => false, 'message' => 'Surat tidak dapat dihapus karena sudah dibaca.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Surat tidak ditemukan.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID surat tidak valid.']);
}
$conn->close();
?>