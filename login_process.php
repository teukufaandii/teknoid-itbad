<?php
session_start();

// Connect to the database
include 'koneksi.php';

// Get the submitted username and password
$username = $_POST['noinduk'];
$password = $_POST['password'];

// Check if the Pengguna exists
$query = "SELECT * FROM tb_pengguna WHERE noinduk='$username' LIMIT 1";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 1) {
    // User exists, now check the password
    $data = mysqli_fetch_assoc($result);
    if ($password == $data['password']) {
        // Password matches, set session variables
        $_SESSION['pengguna_type'] = 'pengguna';
        $_SESSION['pengguna_id'] = $data['id_pg'];
        $_SESSION['pengguna'] = $data['noinduk'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['jabatan'] = $data['jabatan'];
        $_SESSION['akses'] = $data['akses'];
        $_SESSION['password'] = $data['password'];
        $_SESSION['email'] = $data['email'];
        $_SESSION['phone_number'] = $data['no_hp'];

        // Create a success message
        $message = "Anda Berhasil Masuk, Selamat Datang " . $_SESSION['nama_lengkap'] . "!";

        if($_SESSION['akses'] == 'Admin'){
            $redirectUrl = "surat/pengaturan_akun";
        } else {
            $redirectUrl = "Surat/dashboard";
        }

        // Redirect to login_success.php
        header("Location: login_success.php?message=" . urlencode($message) . "&redirectUrl=" . urlencode($redirectUrl));
        exit();
    } else {
        // Password is incorrect
        echo "<script>alert('Password yang Anda masukan salah!'); window.location='index';</script>";
        exit();
    }
} else {
    // User doesn't exist
    echo "<script>alert('Username yang Anda masukan tidak terdaftar!'); window.location='index';</script>";
    exit();
}
