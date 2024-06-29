<?php
// Lakukan koneksi ke database jika belum dilakukan
include 'koneksi.php';
include "logout-checker.php";

// Pastikan $id, $catatan, dan $action ada dalam permintaan POST
if (isset($_POST['id']) && isset($_POST['catatan_disposisi']) && isset($_POST['action'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan_disposisi']);
    $action = $_POST['action'];
    $executor = $_POST['asalsurat'];

    // Mulai transaksi
    mysqli_autocommit($koneksi, false);

    if ($action == 'selesai') {
        // Update status_selesai dan tb_disposisi untuk selesai
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai = true, status_baca = true WHERE id_surat = ?";
        $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai = ?, nama_selesai = ? WHERE id_surat = ?";
    } elseif ($action == 'tolak') {
        // Update status_tolak dan tb_disposisi untuk tolak
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_tolak = true, status_baca = true WHERE id_surat = ?";
        $update_query_disposisi = "UPDATE tb_disposisi SET catatan_tolak = ?, nama_penolak = ? WHERE id_surat = ?";
    }

    // Persiapkan statement untuk tb_surat_dis
    $stmt1 = mysqli_prepare($koneksi, $update_query_surat_dis);
    mysqli_stmt_bind_param($stmt1, "i", $id);

    if (mysqli_stmt_execute($stmt1)) {
        // Periksa apakah entri untuk id_surat ini sudah ada di tb_disposisi
        $check_query = "SELECT COUNT(*) as count FROM tb_disposisi WHERE id_surat = $id";
        $result_check = mysqli_query($koneksi, $check_query);
        $row = mysqli_fetch_assoc($result_check);
        $count = $row['count'];

        if ($count > 0) {
            // Jika entri sudah ada, lanjutkan dengan pembaruan catatan disposisi
            $stmt2 = mysqli_prepare($koneksi, $update_query_disposisi);
            mysqli_stmt_bind_param($stmt2, "ssi", $catatan, $executor, $id);

            if (mysqli_stmt_execute($stmt2)) {
                mysqli_commit($koneksi);
                echo "Status berhasil diperbarui";
            } else {
                mysqli_rollback($koneksi);
                echo "Gagal memperbarui catatan disposisi: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt2);
        } else {
            // Jika entri belum ada, tambahkan kolom baru untuk id_surat ini
            $insert_query = "INSERT INTO tb_disposisi (id_surat, " . ($action == 'selesai' ? "catatan_selesai, nama_selesai" : "catatan_tolak, nama_penolak") . ") VALUES (?, ?, ?)";
            $stmt_insert = mysqli_prepare($koneksi, $insert_query);
            mysqli_stmt_bind_param($stmt_insert, "iss", $id, $catatan, $executor);

            if (mysqli_stmt_execute($stmt_insert)) {
                mysqli_commit($koneksi);
                echo "Status berhasil diperbarui";
            } else {
                mysqli_rollback($koneksi);
                echo "Gagal memperbarui catatan disposisi: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt_insert);
        }
    } else {
        mysqli_rollback($koneksi);
        echo "Gagal memperbarui status: " . mysqli_error($koneksi);
    }
    mysqli_stmt_close($stmt1);
} else {
    echo "ID surat, catatan disposisi, atau action tidak diterima.";
}
?>
