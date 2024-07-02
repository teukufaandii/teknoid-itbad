<?php
// Lakukan koneksi ke database jika belum dilakukan
include 'koneksi.php';
include 'logout-checker.php';

// Pastikan $id, $catatan, dan $action ada dalam permintaan POST
if (isset($_POST['id']) && isset($_POST['catatan_disposisi']) && isset($_POST['action'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan_disposisi']);
    $action = $_POST['action'];
    $executor = $_POST['asalsurat'];

    // Ambil session jabatan
    session_start();
    $session_jabatan = $_SESSION['jabatan'];

    // Mulai transaksi
    mysqli_autocommit($koneksi, false);

    if ($action == 'tolak') {
        // Update status_tolak dan tb_disposisi untuk tolak
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_tolak = true, status_baca = true, diteruskan_ke = null WHERE id_surat = ?";
        $update_query_disposisi = "UPDATE tb_disposisi SET catatan_tolak = ?, nama_penolak = ?, WHERE id_surat = ?";

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
                    // Perbarui dispo1 hingga dispo10 jika kosong
                    $update_dispo_query = "
                        UPDATE tb_disposisi 
                        SET dispo1 = IF(dispo1 IS NULL OR dispo1 = '', ?, dispo1),
                            dispo2 = IF((dispo1 IS NOT NULL AND dispo1 != '') AND (dispo2 IS NULL OR dispo2 = ''), ?, dispo2),
                            dispo3 = IF((dispo1 IS NOT NULL AND dispo1 != '') AND (dispo2 IS NOT NULL AND dispo2 != '') AND (dispo3 IS NULL OR dispo3 = ''), ?, dispo3),
                            dispo4 = IF((dispo1 IS NOT NULL AND dispo1 != '') AND (dispo2 IS NOT NULL AND dispo2 != '') AND (dispo3 IS NOT NULL AND dispo3 != '') AND (dispo4 IS NULL OR dispo4 = ''), ?, dispo4),
                            dispo5 = IF((dispo1 IS NOT NULL AND dispo1 != '') AND (dispo2 IS NOT NULL AND dispo2 != '') AND (dispo3 IS NOT NULL AND dispo3 != '') AND (dispo4 IS NOT NULL AND dispo4 != '') AND (dispo5 IS NULL OR dispo5 = ''), ?, dispo5),
                            dispo6 = IF((dispo1 IS NOT NULL AND dispo1 != '') AND (dispo2 IS NOT NULL AND dispo2 != '') AND (dispo3 IS NOT NULL AND dispo3 != '') AND (dispo4 IS NOT NULL AND dispo4 != '') AND (dispo5 IS NOT NULL AND dispo5 != '') AND (dispo6 IS NULL OR dispo6 = ''), ?, dispo6),
                            dispo7 = IF((dispo1 IS NOT NULL AND dispo1 != '') AND (dispo2 IS NOT NULL AND dispo2 != '') AND (dispo3 IS NOT NULL AND dispo3 != '') AND (dispo4 IS NOT NULL AND dispo4 != '') AND (dispo5 IS NOT NULL AND dispo5 != '') AND (dispo6 IS NOT NULL AND dispo6 != '') AND (dispo7 IS NULL OR dispo7 = ''), ?, dispo7),
                            dispo8 = IF((dispo1 IS NOT NULL AND dispo1 != '') AND (dispo2 IS NOT NULL AND dispo2 != '') AND (dispo3 IS NOT NULL AND dispo3 != '') AND (dispo4 IS NOT NULL AND dispo4 != '') AND (dispo5 IS NOT NULL AND dispo5 != '') AND (dispo6 IS NOT NULL AND dispo6 != '') AND (dispo7 IS NOT NULL AND dispo7 != '') AND (dispo8 IS NULL OR dispo8 = ''), ?, dispo8),
                            dispo9 = IF((dispo1 IS NOT NULL AND dispo1 != '') AND (dispo2 IS NOT NULL AND dispo2 != '') AND (dispo3 IS NOT NULL AND dispo3 != '') AND (dispo4 IS NOT NULL AND dispo4 != '') AND (dispo5 IS NOT NULL AND dispo5 != '') AND (dispo6 IS NOT NULL AND dispo6 != '') AND (dispo7 IS NOT NULL AND dispo7 != '') AND (dispo8 IS NOT NULL AND dispo8 != '') AND (dispo9 IS NULL OR dispo9 = ''), ?, dispo9),
                            dispo10 = IF((dispo1 IS NOT NULL AND dispo1 != '') AND (dispo2 IS NOT NULL AND dispo2 != '') AND (dispo3 IS NOT NULL AND dispo3 != '') AND (dispo4 IS NOT NULL AND dispo4 != '') AND (dispo5 IS NOT NULL AND dispo5 != '') AND (dispo6 IS NOT NULL AND dispo6 != '') AND (dispo7 IS NOT NULL AND dispo7 != '') AND (dispo8 IS NOT NULL AND dispo8 != '') AND (dispo9 IS NOT NULL AND dispo9 != '') AND (dispo10 IS NULL OR dispo10 = ''), ?, dispo10)
                        WHERE id_surat = ?
                    ";
                    $stmt_dispo = mysqli_prepare($koneksi, $update_dispo_query);
                    mysqli_stmt_bind_param($stmt_dispo, "ssssssssssi", $session_jabatan, $session_jabatan, $session_jabatan, $session_jabatan, $session_jabatan, $session_jabatan, $session_jabatan, $session_jabatan, $session_jabatan, $session_jabatan, $id);

                    if (mysqli_stmt_execute($stmt_dispo)) {
                        mysqli_commit($koneksi);
                        echo "Status berhasil diperbarui";
                    } else {
                        mysqli_rollback($koneksi);
                        echo "Gagal memperbarui disposisi: " . mysqli_error($koneksi);
                    }
                    mysqli_stmt_close($stmt_dispo);
                } else {
                    mysqli_rollback($koneksi);
                    echo "Gagal memperbarui catatan disposisi: " . mysqli_error($koneksi);
                }
                mysqli_stmt_close($stmt2);
            } else {
                // Jika entri belum ada, tambahkan kolom baru untuk id_surat ini
                $insert_query = "INSERT INTO tb_disposisi (id_surat, catatan_tolak, nama_penolak, dispo1) VALUES (?, ?, ?, ?)";
                $stmt_insert = mysqli_prepare($koneksi, $insert_query);
                mysqli_stmt_bind_param($stmt_insert, "isss", $id, $catatan, $executor, $session_jabatan);

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
        echo "Action tidak valid.";
    }
} else {
    echo "ID surat, catatan disposisi, atau action tidak diterima.";
}
?>
