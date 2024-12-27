<?php

$token = $_POST["token"];
$token_hash = hash("sha256", $token);

$koneksi = include 'koneksi.php';

$sql = "SELECT * FROM tb_pengguna WHERE reset_token = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    echo "<script>alert('Token tidak ditemukan.'); window.location='index';</script>";
}

if (strlen($_POST["password"]) < 8) {
    echo "<script>alert('Kata sandi minimal mengandung 8 karakter.'); window.location='reset-password.php';</script>";
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    echo "<script>alert('Kata sandi minimal mengandung setidaknya 1 huruf.'); window.location='reset-password.php';</script>";
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    echo "<script>alert('Kata sandi minimal mengandung setidaknya 1 angka.'); window.location='reset-password.php';</script>";
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    echo "<script>alert('Kata sandi harus cocok.'); window.location='reset-password.php';</script>";
}

// Hash the password using SHA-256
$password = hash("sha256", $_POST["password"]);

$sql = "UPDATE tb_pengguna SET password = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id_pg = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ss", $password, $user["id_pg"]);
$stmt->execute();

echo "<script>alert('Kata sandi telah diperbaharui!, Silakan login kembali.'); window.location='index.php';</script>";
