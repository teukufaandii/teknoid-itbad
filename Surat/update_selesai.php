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

    // cabang
    // Start transaction
    mysqli_autocommit($koneksi, false);

    // Check if status_selesai is already true
    $check_status_query = "SELECT status_selesai, status_selesai2, status_selesai3, status_selesai4, status_selesai5, status_selesai6, status_selesai7 FROM tb_surat_dis WHERE id_surat = '$id'";
    $result_check_status = mysqli_query($koneksi, $check_status_query);

    $check_disposisi_query = "SELECT tanggal_eksekutor, tanggal_eksekutor2, tanggal_eksekutor3, tanggal_eksekutor4, tanggal_eksekutor5, tanggal_eksekutor6, tanggal_eksekutor7,
                                    catatan_selesai, catatan_selesai2, catatan_selesai3, catatan_selesai4, catatan_selesai5, catatan_selesai6, catatan_selesai7,
                                    nama_selesai, nama_selesai2, nama_selesai3, nama_selesai4, nama_selesai5, nama_selesai6, nama_selesai7 FROM tb_disposisi WHERE id_surat = '$id'";
    $result_check_disposisi = mysqli_query($koneksi, $check_disposisi_query);

    if (!$result_check_status) {
        mysqli_rollback($koneksi);
        echo "Gagal memeriksa status surat: " . mysqli_error($koneksi);
        mysqli_close($koneksi);
        exit;
    }

    if (!$result_check_disposisi) {
        mysqli_rollback($koneksi);
        echo "Gagal memeriksa disposisi surat: " . mysqli_error($koneksi);
        mysqli_close($koneksi);
        exit;
    }

    $row_status = mysqli_fetch_assoc($result_check_status);
    $status_selesai = $row_status['status_selesai'];
    $status_selesai2 = $row_status['status_selesai2'];
    $status_selesai3 = $row_status['status_selesai3'];
    $status_selesai4 = $row_status['status_selesai4'];
    $status_selesai5 = $row_status['status_selesai5'];
    $status_selesai6 = $row_status['status_selesai6'];
    $status_selesai7 = $row_status['status_selesai7'];

    $row_disposisi = mysqli_fetch_assoc($result_check_disposisi);
    $tanggal_eksekutor = $row_disposisi['tanggal_eksekutor'];
    $tanggal_eksekutor2 = $row_disposisi['tanggal_eksekutor2'];
    $tanggal_eksekutor3 = $row_disposisi['tanggal_eksekutor3'];
    $tanggal_eksekutor4 = $row_disposisi['tanggal_eksekutor4'];
    $tanggal_eksekutor5 = $row_disposisi['tanggal_eksekutor5'];
    $tanggal_eksekutor6 = $row_disposisi['tanggal_eksekutor6'];
    $tanggal_eksekutor7 = $row_disposisi['tanggal_eksekutor7'];

    $catatan_selesai = $row_disposisi['catatan_selesai'];
    $catatan_selesai2 = $row_disposisi['catatan_selesai2'];
    $catatan_selesai3 = $row_disposisi['catatan_selesai3'];
    $catatan_selesai4 = $row_disposisi['catatan_selesai4'];
    $catatan_selesai5 = $row_disposisi['catatan_selesai5'];
    $catatan_selesai6 = $row_disposisi['catatan_selesai6'];
    $catatan_selesai7 = $row_disposisi['catatan_selesai7'];

    $nama_selesai = $row_disposisi['nama_selesai'];
    $nama_selesai2 = $row_disposisi['nama_selesai2'];
    $nama_selesai3 = $row_disposisi['nama_selesai3'];
    $nama_selesai4 = $row_disposisi['nama_selesai4'];
    $nama_selesai5 = $row_disposisi['nama_selesai5'];
    $nama_selesai6 = $row_disposisi['nama_selesai6'];
    $nama_selesai7 = $row_disposisi['nama_selesai7'];

    // Update tb_surat_dis based on status conditions
    if (!$status_selesai) {
        // If status_selesai is false, update status_selesai to true
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai = true, status_baca = true WHERE id_surat = '$id'";
    } elseif ($status_selesai && !$status_selesai2) {
        // If status_selesai is true but status_selesai2 is false, update status_selesai2 to true
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai2 = true WHERE id_surat = '$id'";
    } elseif ($status_selesai && $status_selesai2 && !$status_selesai3) {
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai3 = true WHERE id_surat = '$id'";
    } elseif ($status_selesai && $status_selesai2 && $status_selesai3 && !$status_selesai4) {
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai4 = true WHERE id_surat = '$id'";
    } elseif ($status_selesai && $status_selesai2 && $status_selesai3 && $status_selesai4 && !$status_selesai5) {
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai5 = true WHERE id_surat = '$id'";
    } elseif ($status_selesai && $status_selesai2 && $status_selesai3 && $status_selesai4 && $status_selesai5 && !$status_selesai6) {
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai6 = true WHERE id_surat = '$id'";
    } elseif ($status_selesai && $status_selesai2 && !$status_selesai3 && $status_selesai4 && $status_selesai5 && $status_selesai6 && !$status_selesai7) {
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai7 = true WHERE id_surat = '$id'";
    } else {
        // If both status_selesai and status_selesai2 are true, handle accordingly (optional, based on your application logic)
        echo "Surat sudah dalam status selesai yang final.";
        mysqli_close($koneksi);
        exit;
    }
    // cabang

    // Determine the update query based on the user's role and action
    if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Humas') {
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_baca = true WHERE id_surat = '$id'";
        $jabatan = $_SESSION['jabatan'];
        $tanggal_eksekutor = date("Y-m-d");
        $asal_surat = $_POST['asalsurat'];

        //disposisi
        if ($action == 'selesai') {
            $update_query_disposisi = "UPDATE tb_disposisi, tb_surat_dis SET 
            tb_disposisi.tanggal_eksekutor = '$tanggal_eksekutor',
            tb_disposisi.catatan_selesai = '$catatan',
            tb_disposisi.nama_selesai = '$asal_surat',
            tb_surat_dis.status_selesai = true
            WHERE tb_disposisi.id_surat = '$id' AND tb_surat_dis.id_surat = '$id';";
        } elseif ($action == 'tolak') {
            $update_query_disposisi = "UPDATE tb_disposisi, tb_surat_dis SET
            tb_disposisi.tanggal_eksekutor = '$tanggal_eksekutor', 
            tb_disposisi.catatan_tolak = '$catatan',
            tb_disposisi.nama_penolak = '$asal_surat',
            tb_surat_dis.status_tolak = true
            tb_surat_dis.status_baca = true
            WHERE tb_disposisi.id_surat = '$id' AND tb_surat_dis.id_surat = '$id';";
        }
    } else {
        // Update for other roles
        if (
            $action == 'selesai' && !$tanggal_eksekutor && !$nama_selesai && !$catatan_selesai
        ) {
            $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai = '$catatan', nama_selesai = '$asal_surat', tanggal_eksekutor=CURDATE() WHERE id_surat = '$id'";
        } elseif (
            $action == 'selesai' && $tanggal_eksekutor && $nama_selesai && $catatan_selesai &&
            !$tanggal_eksekutor2 && !$nama_selesai2 && !$catatan_selesai2
        ) {
            $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai2 = '$catatan', nama_selesai2 = '$asal_surat', tanggal_eksekutor2=CURDATE() WHERE id_surat = '$id'";
        } elseif (
            $action == 'selesai' && $tanggal_eksekutor && $nama_selesai && $catatan_selesai &&
            $tanggal_eksekutor2 && $nama_selesai2 && $catatan_selesai2 &&
            !$tanggal_eksekutor3 && !$nama_selesai3 && !$catatan_selesai3
        ) {
            $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai3 = '$catatan', nama_selesai3 = '$asal_surat', tanggal_eksekutor3=CURDATE() WHERE id_surat = '$id'";
        } elseif (
            $action == 'selesai' && $tanggal_eksekutor && $nama_selesai && $catatan_selesai &&
            $tanggal_eksekutor2 && $nama_selesai2 && $catatan_selesai2 &&
            $tanggal_eksekutor3 && $nama_selesai3 && $catatan_selesai3 &&
            !$tanggal_eksekutor4 && !$nama_selesai4 && !$catatan_selesai4
        ) {
            $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai4 = '$catatan', nama_selesai4 = '$asal_surat', tanggal_eksekutor4=CURDATE() WHERE id_surat = '$id'";
        } elseif (
            $action == 'selesai' && $tanggal_eksekutor && $nama_selesai && $catatan_selesai &&
            $tanggal_eksekutor2 && $nama_selesai2 && $catatan_selesai2 &&
            $tanggal_eksekutor3 && $nama_selesai3 && $catatan_selesai3 &&
            $tanggal_eksekutor4 && $nama_selesai4 && $catatan_selesai4 &&
            !$tanggal_eksekutor5 && !$nama_selesai5 && !$catatan_selesai5
        ) {
            $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai5 = '$catatan', nama_selesai5 = '$asal_surat', tanggal_eksekutor5=CURDATE() WHERE id_surat = '$id'";
        } elseif (
            $action == 'selesai' && $tanggal_eksekutor && $nama_selesai && $catatan_selesai &&
            $tanggal_eksekutor2 && $nama_selesai2 && $catatan_selesai2 &&
            $tanggal_eksekutor3 && $nama_selesai3 && $catatan_selesai3 &&
            $tanggal_eksekutor4 && $nama_selesai4 && $catatan_selesai4 &&
            $tanggal_eksekutor5 && $nama_selesai5 && $catatan_selesai5 &&
            !$tanggal_eksekutor6 && !$nama_selesai6 && !$catatan_selesai6
        ) {
            $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai6 = '$catatan', nama_selesai6 = '$asal_surat', tanggal_eksekutor6=CURDATE() WHERE id_surat = '$id'";
        } elseif (
            $action == 'selesai' && $tanggal_eksekutor && $nama_selesai && $catatan_selesai &&
            $tanggal_eksekutor2 && $nama_selesai2 && $catatan_selesai2 &&
            $tanggal_eksekutor3 && $nama_selesai3 && $catatan_selesai3 &&
            $tanggal_eksekutor4 && $nama_selesai4 && $catatan_selesai4 &&
            $tanggal_eksekutor5 && $nama_selesai5 && $catatan_selesai5 &&
            $tanggal_eksekutor6 && $nama_selesai6 && $catatan_selesai6 &&
            !$tanggal_eksekutor7 && !$nama_selesai7 && !$catatan_selesai7
        ) {
            $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai7 = '$catatan', nama_selesai7 = '$asal_surat', tanggal_eksekutor7=CURDATE() WHERE id_surat = '$id'";
        }
    }
    // cabang

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

    mysqli_close($koneksi);
} else {
    echo "ID surat, catatan disposisi, atau action tidak diterima.";
}
