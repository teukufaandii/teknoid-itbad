<?php
session_start();
include "koneksi.php";
include "logout-checker.php";
// Periksa apakah pengguna telah login
if (!isset($_SESSION['pengguna'])) {
    // Redirect atau tindakan lain jika pengguna tidak diotorisasi
    header("Location: login.php");
    exit;
}

// Periksa apakah data yang diperlukan telah diterima dari formulir
if (isset($_POST['password']) && isset($_POST['email']) && isset($_POST['no_hp'])) {
    // Persiapkan dan jalankan pernyataan SQL untuk memperbarui data pengguna
    $stmt = $koneksi->prepare("UPDATE tb_pengguna SET password=?, email=?, no_hp=? WHERE noinduk=?");
    $stmt->bind_param("ssss", $_POST['password'], $_POST['email'], $_POST['no_hp'], $_SESSION['pengguna']);

    // Jalankan pernyataan SQL
    $sqln = $stmt->execute();

    // Periksa apakah query berhasil dieksekusi
    // Periksa apakah aksi berhasil dilakukan
    if ($sqln) {
        // Tampilkan alert menggunakan JavaScript
        echo '<script>alert("Data pengguna berhasil disimpan.");</script>';
        // Redirect ke halaman dashboard menggunakan JavaScript setelah pengguna menekan OK pada alert
        echo '<script>window.location.href = "dashboard.php";</script>';
        exit; // Keluar dari skrip PHP
    } else {
        // Tampilkan pesan kesalahan jika terjadi masalah dalam eksekusi query
        echo '<script language="javascript" type="text/javascript">
              alert("Terjadi kesalahan saat menyimpan data pengguna. Silakan coba lagi.");
              window.location.href = "dashboard.php"; // Redirect setelah alert
              </script>';
        exit;
    }
} else {
    // Redirect ke halaman dashboard jika data yang diperlukan tidak diterima dari formulir
    header("Location: dashboard.php");
    exit;
}
?>
