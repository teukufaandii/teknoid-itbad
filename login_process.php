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

        if ($_SESSION['akses'] == 'Admin') {
            echo '<script language="javascript" type="text/javascript">
            alert("Anda Berhasil Masuk, Selamat Datang '.$_SESSION['jabatan'].'!");</script>';
            echo "<meta http-equiv='refresh' content='0; url=Surat/pengaturan_akun'>";
            exit();
        }elseif ($_SESSION['akses'] == 'Rektor') {
            echo '<script language="javascript" type="text/javascript">
            alert("Anda Berhasil Masuk, Selamat Datang Bapak '.$_SESSION['jabatan'].'!");</script>';
            echo "<meta http-equiv='refresh' content='0; url=Surat/dashboard'>";
            exit();
        }elseif ($_SESSION['akses'] == 'Warek1' || $_SESSION['akses'] == 'Warek2' || $_SESSION['akses'] == 'Warek3') {
            echo '<script language="javascript" type="text/javascript">
            alert("Anda Berhasil Masuk, Selamat Datang '.$_SESSION['jabatan'].'!");</script>';
            echo "<meta http-equiv='refresh' content='0; url=Surat/dashboard'>";
            exit();
        }elseif ($_SESSION['akses'] == 'direkPasca' || 
                $_SESSION['akses'] == 'DekanFTD' || 
                $_SESSION['akses'] == 'DekanFEB' || 
                $_SESSION['jabatan'] == 'S1 SI' || 
                $_SESSION['jabatan'] == 'S1 TI' ||
                $_SESSION['jabatan'] == 'S1 DKV' || 
                $_SESSION['jabatan'] == 'S1 Arsitektur' ||
                $_SESSION['jabatan'] == 'S1 Manajemen' || 
                $_SESSION['jabatan'] == 'S1 Akuntansi' || 
                $_SESSION['jabatan'] == 'Prodi D3 Akuntansi' || 
                $_SESSION['jabatan'] == 'Prodi D3 Keuangan dan Perbankan' || 
                $_SESSION['jabatan'] == 'Prodi S2 Keuangan Syariah') {
            echo '<script language="javascript" type="text/javascript">
            alert("Anda Berhasil Masuk, Selamat Datang '.$_SESSION['jabatan'].'!");</script>';
            echo "<meta http-equiv='refresh' content='0; url=Surat/dashboard'>";
            exit();
        }elseif ($_SESSION['akses'] == 'User') {
            echo '<script language="javascript" type="text/javascript">
            alert("Anda Berhasil Masuk, Selamat Datang '.$_SESSION['nama_lengkap'].'!");</script>';
            echo "<meta http-equiv='refresh' content='0; url=Surat/dashboard'>";
            exit();
        }elseif ( $_SESSION['akses'] == 'bpm' 
                || $_SESSION['akses'] == 'lp3m' 
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
                ) {
            echo '<script language="javascript" type="text/javascript">
            alert("Anda Berhasil Masuk, Selamat Datang '.$_SESSION['jabatan'].'!");</script>';
            echo "<meta http-equiv='refresh' content='0; url=Surat/dashboard'>";
            exit();
        }elseif ($_SESSION['akses'] == 'Humas') {
            echo '<script language="javascript" type="text/javascript">
            alert("Anda Berhasil Masuk, Selamat Datang '.$_SESSION['jabatan'].'!");</script>';
            echo "<meta http-equiv='refresh' content='0; url=Surat/dashboard'>";
            exit();
        }elseif ($_SESSION['akses'] == 'Sekretaris') {
            echo '<script language="javascript" type="text/javascript">
            alert("Anda Berhasil Masuk, Selamat Datang '.$_SESSION['jabatan'].'!");</script>';
            echo "<meta http-equiv='refresh' content='0; url=Surat/dashboard'>";
            exit();
        }elseif ($_SESSION['akses'] == 'User') {
            echo '<script language="javascript" type="text/javascript">
            alert("Anda Berhasil Masuk, Selamat Datang '.$_SESSION['jabatan'].'!");</script>';
            echo "<meta http-equiv='refresh' content='0; url=Surat/dashboard'>";
            exit();
        }
    } else {
        // Password is incorrect
        echo "<script>alert('Username atau Password yang Anda masukan salah!'); window.location='index';</script>";
        exit();
    }
} else {
    // User doesn't exist
    echo "<script>alert('Username yang Anda masukan tidak terdaftar!'); window.location='index';</script>";
    exit();
}
?>