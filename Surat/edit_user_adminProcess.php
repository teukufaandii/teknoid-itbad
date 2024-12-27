<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
include "koneksi.php";
include "logout-checker.php";

// Hash the password using SHA256
$hashed_password = hash('sha256', $_POST['password']);

// Using parameterized query to prevent SQL Injection
$stmt = $koneksi->prepare("UPDATE tb_pengguna SET noinduk=?, nama_lengkap=?, jabatan=?, akses=?, password=?, email=?, no_hp=? WHERE noinduk=?");
$stmt->bind_param("ssssssss", $_POST['noinduk'], $_POST['nama_lengkap'], $_POST['jabatan'], $_POST['akses'], $hashed_password, $_POST['email'], $_POST['no_telepon'], $_POST['noinduk']);

// Execute the query
$sqln = $stmt->execute();

if ($sqln) {
    echo '<script language="javascript" type="text/javascript">
          alert("Data pengguna berhasil disimpan.");</script>';
} else {
    echo "Oops! Something Wrong, Try Again!";
}
echo "<META HTTP-EQUIV='Refresh' Content='1; URL=pengaturan_akun.php'>";
?>