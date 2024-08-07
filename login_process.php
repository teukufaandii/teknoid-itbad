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
        $redirectUrl = "Surat/dashboard"; // Default redirection URL

        // Determine the redirection URL based on user role
        if ($_SESSION['akses'] == 'Admin') {
            $redirectUrl = "Surat/pengaturan_akun";
        } elseif ($_SESSION['akses'] == 'Rektor' || $_SESSION['akses'] == 'Warek1' || $_SESSION['akses'] == 'Warek2' || $_SESSION['akses'] == 'Warek3') {
            $redirectUrl = "Surat/dashboard";
        } elseif ($_SESSION['akses'] == 'User') {
            $redirectUrl = "Surat/dashboard";
        } elseif (
            $_SESSION['akses'] == 'bpm'
            || $_SESSION['akses'] == 'lp3m'
            || $_SESSION['akses'] == 'halal_center'
            || $_SESSION['akses'] == 'PKAD'
            || $_SESSION['akses'] == 'PSIPP'
            || $_SESSION['akses'] == 'CHED'
            || $_SESSION['akses'] == 'PSDOD'
            || $_SESSION['akses'] == 'upt_perpus'
            || $_SESSION['akses'] == 'akademik'
            || $_SESSION['akses'] == 'it_lab'
            || $_SESSION['akses'] == 'umum'
            || $_SESSION['akses'] == 'sdm'
            || $_SESSION['akses'] == 'keuangan'
            || $_SESSION['akses'] == 'marketing'
            || $_SESSION['akses'] == 'kui_k'
            || $_SESSION['akses'] == 'kmhs'
            || $_SESSION['akses'] == 'ppik'
            || $_SESSION['akses'] == 'Olga'
            || $_SESSION['akses'] == 'KMPM'
            || $_SESSION['akses'] == 'Alpiniste'
            || $_SESSION['akses'] == 'Nasbung'
            || $_SESSION['akses'] == 'Kopma'
            || $_SESSION['akses'] == 'Kummis'
            || $_SESSION['akses'] == 'IMM'
            || $_SESSION['akses'] == 'TS'
            || $_SESSION['akses'] == 'BEM'
            || $_SESSION['akses'] == 'DPM'
            || $_SESSION['akses'] == 'IMDIMENSI'
            || $_SESSION['akses'] == 'IMSISFO'
            || $_SESSION['akses'] == 'IMTI'
            || $_SESSION['akses'] == 'IMARS'
            || $_SESSION['akses'] == 'IMMADA'
            || $_SESSION['akses'] == 'IMAKSI'
            || $_SESSION['akses'] == 'pusat_bisnis'
            || $_SESSION['akses'] == 'formasi'
            || $_SESSION['akses'] == 'ppik_kmhs'
        ) {
            $redirectUrl = "Surat/dashboard";
        } elseif ($_SESSION['akses'] == 'Humas' || $_SESSION['akses'] == 'Sekretaris') {
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
