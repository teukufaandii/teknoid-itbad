<?php
var_dump($_POST);

if (isset($_POST['delete'])) {

    include("koneksi.php");

    // Sanitasi input
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $id_srt = mysqli_real_escape_string($koneksi, $_POST['id_srt']);

    // Query pertama
    $sql = "DELETE FROM tb_surat_dis WHERE kode_surat = '" . $id . "'";

    // Jalankan query pertama
    if (mysqli_query($koneksi, $sql)) {
        echo "Deleted from tb_surat_dis with kode_surat = " . $id;
    } else {
        // Jika query pertama gagal, jalankan query kedua
        $sql2 = "DELETE FROM tb_surat_dis WHERE id_srt = '" . $id_srt . "'";

        if (mysqli_query($koneksi, $sql2)) {
            echo "Deleted from tb_surat_dis with kode_surat = " . $id_srt;
        } else {
            echo "Error deleting record: " . mysqli_error($koneksi);
        }
    }

    // Tutup koneksi (opsional)
    mysqli_close($koneksi);

    exit(); // Pastikan script berhenti setelah eksekusi
}
