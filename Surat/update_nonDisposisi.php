<?php

session_start(); // Move session_start to the top

include 'koneksi.php';
include "logout-checker.php";

// Periksa koneksi ke database jika belum terkoneksi

// Periksa jika metode yang digunakan adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data yang dikirim melalui permintaan POST
    $id_surat = $_POST["id_surat"];
    $kode_surat = $_POST["kode_surat"];
    $diteruskan_ke = $_POST["diteruskan_ke"];

    // Lakukan query untuk memperbarui data di tabel tb_surat_dis
    $sql_update = "UPDATE tb_surat_dis SET kode_surat=?, status_baca=1 WHERE id_surat=?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssi", $kode_surat, $id_surat);

    // Jalankan query update
    if ($stmt_update->execute()) {
        // Insert ke tb_disposisi
        $tanggal_disposisi = date("Y-m-d");
        $sql_insert_disposisi = "UPDATE tb_disposisi ( status_disposisi1, dispo1, tanggal_disposisi1) VALUES (?, ?, 1, ?, ?) WHERE id_surat = ?";
        $stmt_insert_disposisi = $conn->prepare($sql_insert_disposisi);
        $stmt_insert_disposisi->bind_param("sssi",  $_SESSION['jabatan'], $tanggal_disposisi, $id_surat);

        if ($stmt_insert_disposisi->execute()) {
            echo "Data berhasil diperbarui dan disposisi berhasil ditambahkan";
        } else {
            echo "Error: " . $sql_insert_disposisi . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }

    // Tutup koneksi ke database jika sudah selesai
    $conn->close();
} else {
    // Kirim respons ke klien jika metode yang digunakan bukan POST
    echo "Terjadi kesalahan, Mohon coba lagi!";
}

?>
