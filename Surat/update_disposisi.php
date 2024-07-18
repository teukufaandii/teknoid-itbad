<?php
session_start();
include 'koneksi.php';
include 'logout-checker.php';

$id_surat = $_POST['id_surat'] ?? null;
$catatan_disposisi = $_POST['catatan_disposisi'];
$keputusan_disposisi = $_POST['keputusan'];
$diteruskan_ke = $_POST['diteruskan'];
$perihal = $_POST['perihal'];

// Check if diteruskan_ke is a string and try to decode it as JSON
if (is_string($diteruskan_ke)) {
    $diteruskan_ke_decoded = json_decode($diteruskan_ke, true);

    // Check if json_decode was successful and diteruskan_ke_decoded is not empty
    if (is_array($diteruskan_ke_decoded) && !empty($diteruskan_ke_decoded)) {
        $diteruskan_ke = $diteruskan_ke_decoded;
    } else {
        // Attempt to handle as comma-separated values
        $diteruskan_ke = explode(',', $diteruskan_ke);
    }
}

// Ensure diteruskan_ke is an array and remove empty values
if (!is_array($diteruskan_ke)) {
    $diteruskan_ke = [];
}

$diteruskan_ke = array_filter(array_map('trim', $diteruskan_ke)); // Remove empty values and trim whitespace

if (empty($diteruskan_ke)) {
    echo "Data diteruskan_ke tidak valid atau kosong";
    exit;
}

if ($id_surat !== null && !empty($id_surat)) {
    // Check if id_surat exists in tb_surat_dis
    $sql_check_surat = "SELECT id_surat, diteruskan_ke FROM tb_surat_dis WHERE id_surat = ?";
    $stmt_check_surat = $koneksi->prepare($sql_check_surat);
    $stmt_check_surat->bind_param("i", $id_surat);
    $stmt_check_surat->execute();
    $stmt_check_surat->store_result();

    if ($stmt_check_surat->num_rows > 0) {
        $stmt_check_surat->bind_result($id_surat, $diteruskan_now);
        $stmt_check_surat->fetch();

        $diteruskan_now_array = json_decode($diteruskan_now, true) ?? [];
        // Remove current user's access from diteruskan_now_array
        $diteruskan_now_array = array_diff($diteruskan_now_array, [$_SESSION['akses']]);
        // Add new values from frontend
        $diteruskan_ke = array_merge($diteruskan_now_array, $diteruskan_ke);
        $diteruskan_ke = array_values(array_unique($diteruskan_ke)); // Ensure unique values and reindex array
        $diteruskan_ke_json = json_encode($diteruskan_ke);

        // Check status disposisi before updating
        $sql_check_status_disposisi = "SELECT status_disposisi1, status_disposisi2, status_disposisi3, status_disposisi4, status_disposisi5 FROM tb_disposisi WHERE id_surat = ?";
        $stmt_check_status_disposisi = $koneksi->prepare($sql_check_status_disposisi);
        $stmt_check_status_disposisi->bind_param("i", $id_surat);
        $stmt_check_status_disposisi->execute();
        $stmt_check_status_disposisi->store_result();
        $stmt_check_status_disposisi->bind_result($status_disposisi1, $status_disposisi2, $status_disposisi3, $status_disposisi4, $status_disposisi5);
        $stmt_check_status_disposisi->fetch();

        // Set variabel untuk tanggal disposisi
        $tanggal_disposisi = date("Y-m-d");

        // Check conditions for disposisi and update based on conditions
        if (!$status_disposisi1 && !$status_disposisi2 && !$status_disposisi3 && !$status_disposisi4 && !$status_disposisi5) {
            // Kondisi Disposisi 1
            $sql_insert = "INSERT INTO tb_disposisi (id_surat, catatan_disposisi, diteruskan_ke, keputusan_disposisi1, status_disposisi1, dispo1, tanggal_disposisi1, perihal) VALUES (?, ?, ?, ?, 1, ?, ?, ?)";
            $stmt_insert = $koneksi->prepare($sql_insert);
            $stmt_insert->bind_param("issssss", $id_surat, $catatan_disposisi, $diteruskan_ke_json, $keputusan_disposisi, $_SESSION['jabatan'], $tanggal_disposisi, $perihal);
            if ($stmt_insert->execute()) {
                // Update diteruskan_ke in tb_surat_dis
                $sql_update_diteruskan_ke = "UPDATE tb_surat_dis SET diteruskan_ke = ?, status_baca = true WHERE id_surat = ?";
                $stmt_update_diteruskan_ke = $koneksi->prepare($sql_update_diteruskan_ke);
                $stmt_update_diteruskan_ke->bind_param("si", $diteruskan_ke_json, $id_surat);
                $stmt_update_diteruskan_ke->execute();

                echo "Disposisi berhasil diperbarui";
            } else {
                echo "Gagal memperbarui disposisi: " . $stmt_insert->error;
            }
        } elseif ($status_disposisi1 && !$status_disposisi2 && !$status_disposisi3 && !$status_disposisi4 && !$status_disposisi5) {
            // Kondisi Disposisi 2
            $sql_update_disposisi = "UPDATE tb_disposisi SET keputusan_disposisi2 = ?, catatan_disposisi2 = ?, status_disposisi2 = 1, diteruskan_ke = ?, dispo2 = ?, tanggal_disposisi2 = ? WHERE id_surat = ?";
            $stmt_update_disposisi = $koneksi->prepare($sql_update_disposisi);
            $stmt_update_disposisi->bind_param("sssssi", $keputusan_disposisi, $catatan_disposisi, $diteruskan_ke_json, $_SESSION['jabatan'], $tanggal_disposisi, $id_surat);
            if ($stmt_update_disposisi->execute()) {
                // Update diteruskan_ke
                $sql_update_status_baca = "UPDATE tb_surat_dis SET diteruskan_ke = ? WHERE id_surat = ?";
                $stmt_update_status_baca = $koneksi->prepare($sql_update_status_baca);
                $stmt_update_status_baca->bind_param("si", $diteruskan_ke_json, $id_surat);
                $stmt_update_status_baca->execute();
                echo "Disposisi berhasil diperbarui";
            } else {
                echo "Gagal memperbarui disposisi";
            }
        } elseif ($status_disposisi1 && $status_disposisi2 && !$status_disposisi3 && !$status_disposisi4 && !$status_disposisi5) {
            // Kondisi Disposisi 3
            $sql_update_disposisi = "UPDATE tb_disposisi SET keputusan_disposisi3 = ?, catatan_disposisi3 = ?, status_disposisi3 = 1, dispo3 = ?, tanggal_disposisi3 = ?, diteruskan_ke = ? WHERE id_surat = ?";
            $stmt_update_disposisi = $koneksi->prepare($sql_update_disposisi);
            $stmt_update_disposisi->bind_param("sssssi", $keputusan_disposisi, $catatan_disposisi, $_SESSION['jabatan'], $tanggal_disposisi, $diteruskan_ke_json, $id_surat);
            if ($stmt_update_disposisi->execute()) {
                // Update diteruskan_ke
                $sql_update_status_baca = "UPDATE tb_surat_dis SET diteruskan_ke = ? WHERE id_surat = ?";
                $stmt_update_status_baca = $koneksi->prepare($sql_update_status_baca);
                $stmt_update_status_baca->bind_param("si", $diteruskan_ke_json, $id_surat);
                $stmt_update_status_baca->execute();
                echo "Disposisi berhasil diperbarui";
            } else {
                echo "Gagal memperbarui disposisi";
            }
        } elseif ($status_disposisi1 && $status_disposisi2 && $status_disposisi3 && !$status_disposisi4 && !$status_disposisi5) {
            // Kondisi Disposisi 4
            $sql_update_disposisi = "UPDATE tb_disposisi SET keputusan_disposisi4 = ?, catatan_disposisi4 = ?, status_disposisi4 = 1, dispo4 = ?, tanggal_disposisi4 = ?, diteruskan_ke = ? WHERE id_surat = ?";
            $stmt_update_disposisi = $koneksi->prepare($sql_update_disposisi);
            $stmt_update_disposisi->bind_param("sssssi", $keputusan_disposisi, $catatan_disposisi, $_SESSION['jabatan'], $tanggal_disposisi, $diteruskan_ke_json, $id_surat);
            if ($stmt_update_disposisi->execute()) {
                // Update diteruskan_ke
                $sql_update_status_baca = "UPDATE tb_surat_dis SET diteruskan_ke = ? WHERE id_surat = ?";
                $stmt_update_status_baca = $koneksi->prepare($sql_update_status_baca);
                $stmt_update_status_baca->bind_param("si", $diteruskan_ke_json, $id_surat);
                $stmt_update_status_baca->execute();
                echo "Disposisi berhasil diperbarui";
            } else {
                echo "Gagal memperbarui disposisi";
            }
        } elseif ($status_disposisi1 && $status_disposisi2 && $status_disposisi3 && $status_disposisi4 && !$status_disposisi5) {
            // Kondisi Disposisi 5
            $sql_update_disposisi = "UPDATE tb_disposisi SET keputusan_disposisi5 = ?, catatan_disposisi5 = ?, status_disposisi5 = 1, dispo5 = ?, tanggal_disposisi5 = ?, diteruskan_ke = ? WHERE id_surat = ?";
            $stmt_update_disposisi = $koneksi->prepare($sql_update_disposisi);
            $stmt_update_disposisi->bind_param("sssssi", $keputusan_disposisi, $catatan_disposisi, $_SESSION['jabatan'], $tanggal_disposisi, $diteruskan_ke_json, $id_surat);
            if ($stmt_update_disposisi->execute()) {
                // Update diteruskan_ke
                $sql_update_status_baca = "UPDATE tb_surat_dis SET diteruskan_ke = ? WHERE id_surat = ?";
                $stmt_update_status_baca = $koneksi->prepare($sql_update_status_baca);
                $stmt_update_status_baca->bind_param("si", $diteruskan_ke_json, $id_surat);
                $stmt_update_status_baca->execute();
                echo "Disposisi berhasil diperbarui";
            } else {
                echo "Gagal memperbarui disposisi";
            }
        } elseif ($status_disposisi1 && $status_disposisi2 && $status_disposisi3 && $status_disposisi4 && $status_disposisi5 && !$status_disposisi6) {
            // Kondisi Disposisi 6
            $sql_update_disposisi = "UPDATE tb_disposisi SET keputusan_disposisi6 = ?, catatan_disposisi6 = ?, status_disposisi6 = 1, dispo6 = ?, tanggal_disposisi6 = ?, diteruskan_ke = ? WHERE id_surat = ?";
            $stmt_update_disposisi = $koneksi->prepare($sql_update_disposisi);
            $stmt_update_disposisi->bind_param("sssssi", $keputusan_disposisi, $catatan_disposisi, $_SESSION['jabatan'], $tanggal_disposisi, $diteruskan_ke_json, $id_surat);
            if ($stmt_update_disposisi->execute()) {
                // Update diteruskan_ke
                $sql_update_status_baca = "UPDATE tb_surat_dis SET diteruskan_ke = ? WHERE id_surat = ?";
                $stmt_update_status_baca = $koneksi->prepare($sql_update_status_baca);
                $stmt_update_status_baca->bind_param("si", $diteruskan_ke_json, $id_surat);
                $stmt_update_status_baca->execute();
                echo "Disposisi berhasil diperbarui";
            } else {
                echo "Gagal memperbarui disposisi";
            }
        } elseif ($status_disposisi1 && $status_disposisi2 && $status_disposisi3 && $status_disposisi4 && $status_disposisi5 && $status_disposisi6 && !$status_disposisi7) {
            // Kondisi Disposisi 7
            $sql_update_disposisi = "UPDATE tb_disposisi SET keputusan_disposisi7 = ?, catatan_disposisi7 = ?, status_disposisi7 = 1, dispo7 = ?, tanggal_disposisi7 = ?, diteruskan_ke = ? WHERE id_surat = ?";
            $stmt_update_disposisi = $koneksi->prepare($sql_update_disposisi);
            $stmt_update_disposisi->bind_param("sssssi", $keputusan_disposisi, $catatan_disposisi, $_SESSION['jabatan'], $tanggal_disposisi, $diteruskan_ke_json, $id_surat);
            if ($stmt_update_disposisi->execute()) {
                // Update diteruskan_ke
                $sql_update_status_baca = "UPDATE tb_surat_dis SET diteruskan_ke = ? WHERE id_surat = ?";
                $stmt_update_status_baca = $koneksi->prepare($sql_update_status_baca);
                $stmt_update_status_baca->bind_param("si", $diteruskan_ke_json, $id_surat);
                $stmt_update_status_baca->execute();
                echo "Disposisi berhasil diperbarui";
            } else {
                echo "Gagal memperbarui disposisi";
            }
        } elseif ($status_disposisi1 && $status_disposisi2 && $status_disposisi3 && $status_disposisi4 && $status_disposisi5 && $status_disposisi6 && $status_disposisi7 && !$status_disposisi8 ) {
            // Kondisi Disposisi 8
            $sql_update_disposisi = "UPDATE tb_disposisi SET keputusan_disposisi8 = ?, catatan_disposisi8 = ?, status_disposisi8 = 1, dispo8 = ?, tanggal_disposisi8 = ?, diteruskan_ke = ? WHERE id_surat = ?";
            $stmt_update_disposisi = $koneksi->prepare($sql_update_disposisi);
            $stmt_update_disposisi->bind_param("sssssi", $keputusan_disposisi, $catatan_disposisi, $_SESSION['jabatan'], $tanggal_disposisi, $diteruskan_ke_json, $id_surat);
            if ($stmt_update_disposisi->execute()) {
                // Update diteruskan_ke
                $sql_update_status_baca = "UPDATE tb_surat_dis SET diteruskan_ke = ? WHERE id_surat = ?";
                $stmt_update_status_baca = $koneksi->prepare($sql_update_status_baca);
                $stmt_update_status_baca->bind_param("si", $diteruskan_ke_json, $id_surat);
                $stmt_update_status_baca->execute();
                echo "Disposisi berhasil diperbarui";
            } else {
                echo "Gagal memperbarui disposisi";
            }
        } elseif ($status_disposisi1 && $status_disposisi2 && $status_disposisi3 && $status_disposisi4 && $status_disposisi5 && $status_disposisi6 && $status_disposisi7 && $status_disposisi8 && !$status_disposisi9 ) {
            // Kondisi Disposisi 9
            $sql_update_disposisi = "UPDATE tb_disposisi SET keputusan_disposisi9 = ?, catatan_disposisi9 = ?, status_disposisi9 = 1, dispo9 = ?, tanggal_disposisi9 = ?, diteruskan_ke = ? WHERE id_surat = ?";
            $stmt_update_disposisi = $koneksi->prepare($sql_update_disposisi);
            $stmt_update_disposisi->bind_param("sssssi", $keputusan_disposisi, $catatan_disposisi, $_SESSION['jabatan'], $tanggal_disposisi, $diteruskan_ke_json, $id_surat);
            if ($stmt_update_disposisi->execute()) {
                // Update diteruskan_ke
                $sql_update_status_baca = "UPDATE tb_surat_dis SET diteruskan_ke = ? WHERE id_surat = ?";
                $stmt_update_status_baca = $koneksi->prepare($sql_update_status_baca);
                $stmt_update_status_baca->bind_param("si", $diteruskan_ke_json, $id_surat);
                $stmt_update_status_baca->execute();
                echo "Disposisi berhasil diperbarui";
            } else {
                echo "Gagal memperbarui disposisi";
            }
        } elseif ($status_disposisi1 && $status_disposisi2 && $status_disposisi3 && $status_disposisi4 && $status_disposisi5 && $status_disposisi6 && $status_disposisi7 && $status_disposisi8 && $status_disposisi9 && !$status_disposisi10) {
            // Kondisi Disposisi 10
            $sql_update_disposisi = "UPDATE tb_disposisi SET keputusan_disposisi10 = ?, catatan_disposisi10 = ?, status_disposisi10 = 1, dispo10 = ?, tanggal_disposisi10 = ?, diteruskan_ke = ? WHERE id_surat = ?";
            $stmt_update_disposisi = $koneksi->prepare($sql_update_disposisi);
            $stmt_update_disposisi->bind_param("sssssi", $keputusan_disposisi, $catatan_disposisi, $_SESSION['jabatan'], $tanggal_disposisi, $diteruskan_ke_json, $id_surat);
            if ($stmt_update_disposisi->execute()) {
                // Update diteruskan_ke
                $sql_update_status_baca = "UPDATE tb_surat_dis SET diteruskan_ke = ? WHERE id_surat = ?";
                $stmt_update_status_baca = $koneksi->prepare($sql_update_status_baca);
                $stmt_update_status_baca->bind_param("si", $diteruskan_ke_json, $id_surat);
                $stmt_update_status_baca->execute();
                echo "Disposisi berhasil diperbarui";
            } else {
                echo "Gagal memperbarui disposisi";
            }
        }
    } else {
        echo "Surat tidak ditemukan";
    }
} else {
    echo "ID surat tidak valid";
}
