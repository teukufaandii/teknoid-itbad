<?php
session_start();

include 'koneksi.php';
include "logout-checker.php";

if (isset($_POST['id']) && isset($_POST['catatan_disposisi']) && isset($_POST['action'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan_disposisi']);
    $asal_surat = isset($_SESSION['jabatan']) ? $_SESSION['jabatan'] : 'Unknown'; // Use session's jabatan
    $action = mysqli_real_escape_string($koneksi, $_POST['action']); // Get action parameter
    $kode_surat = mysqli_real_escape_string($koneksi, $_POST['kode_surat']);

    // Determine the update query based on the user's role and action
    if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Humas') { // tambahkan kondisi update disposisi
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai = true, kode_surat = '$kode_surat', status_baca = true WHERE id_surat = '$id'";
        $jabatan = $_SESSION['jabatan'];
        $tanggal_disposisi1 = date("Y-m-d");

        if ($action == 'selesai') {
            $update_query_disposisi = "UPDATE tb_disposisi SET dispo1 = '$jabatan', tanggal_disposisi1 = '$tanggal_disposisi1', status_disposisi1 = true, catatan_selesai = '$catatan', nama_selesai = '$asal_surat' WHERE id_surat = '$id'";
        } elseif ($action == 'tolak') {
            $update_query_disposisi = "UPDATE tb_disposisi SET dispo1 = '$jabatan', tanggal_disposisi1 = '$tanggal_disposisi1', status_disposisi1 = true, catatan_selesai = '$catatan', nama_penolak = '$asal_surat' WHERE id_surat = '$id'";
        }
    } else {
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai = true WHERE id_surat = '$id'";

        if ($action == 'selesai') {
            $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai = '$catatan', nama_selesai = '$asal_surat', tanggal_eksekutor=CURDATE() WHERE id_surat = '$id'";
        } elseif ($action == 'tolak') {
            $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai = '$catatan', nama_penolak = '$asal_surat', tanggal_eksekutor=CURDATE() WHERE id_surat = '$id'";
        }
    }

    // Start transaction
    mysqli_autocommit($koneksi, false);

    // Update tb_surat_dis
    if (mysqli_query($koneksi, $update_query_surat_dis)) {
        // Check if entry for id_surat exists in tb_disposisi
        $check_query = "SELECT COUNT(*) as count FROM tb_disposisi WHERE id_surat = '$id'";
        $result_check = mysqli_query($koneksi, $check_query);
        $row = mysqli_fetch_assoc($result_check);
        $count = $row['count'];

        if ($count > 0) {
            // If entry exists, update tb_disposisi
            if (mysqli_query($koneksi, $update_query_disposisi)) {
                mysqli_commit($koneksi);
                echo "Status berhasil diperbarui";
            } else {
                mysqli_rollback($koneksi);
                echo "Gagal memperbarui status pada tabel tb_disposisi: " . mysqli_error($koneksi);
            }
        } else {
            // If entry does not exist, insert new entry into tb_disposisi
            $insert_query = "INSERT INTO tb_disposisi (id_surat, " . ($action == 'selesai' ? "catatan_selesai, nama_selesai" : ($action == 'tolak' ? "catatan_selesai, nama_penolak" : "catatan_terima, nama_terima")) . ") VALUES ('$id', '$catatan', '$asal_surat')";
            if (mysqli_query($koneksi, $insert_query)) {
                mysqli_commit($koneksi);
                echo "Status berhasil diperbarui";
            } else {
                mysqli_rollback($koneksi);
                echo "Gagal memperbarui status pada tabel tb_disposisi: " . mysqli_error($koneksi);
            }
        }
    } else {
        mysqli_rollback($koneksi);
        echo "Gagal memperbarui status pada tabel tb_surat_dis: " . mysqli_error($koneksi);
    }
} else {
    echo "ID surat, catatan disposisi, atau action tidak diterima.";
}

mysqli_close($koneksi);
