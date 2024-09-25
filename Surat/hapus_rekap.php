<?php
var_dump($_POST);

if (isset($_POST['delete'])) {
    include("koneksi.php");

    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    
    // Query pertama
    $sql = "DELETE FROM tb_surat_dis WHERE kode_surat = '" . $id . "'";
    echo $sql;
    if (mysqli_query($koneksi, $sql)) {
        echo "Deleted";
    } else {
        echo "Error deleting record from tb_surat_dis: " . mysqli_error($koneksi);
    }

    // Query kedua
    $sql2 = "DELETE FROM tb_srt_dosen WHERE id_srt = '" . $id . "'";
    echo $sql2;
    if (mysqli_query($koneksi, $sql2)) {
        echo "Deleted";
    } else {
        echo "Error deleting record from tb_srt_dosen: " . mysqli_error($koneksi);
        exit();
    }

    // Query ketiga
    $sql3 = "DELETE FROM tb_srt_honor WHERE id = '" . $id . "'";
    echo $sql3;
    if (mysqli_query($koneksi, $sql3)) {
        echo "Deleted";
    } else {
        echo "Error deleting record from tb_srt_honor: " . mysqli_error($koneksi);
        exit();
    }

    // Tutup koneksi (opsional)
    mysqli_close($koneksi);

    exit();
}