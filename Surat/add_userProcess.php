<?php
    include 'koneksi.php';    
    $no_induk       =$_POST['noinduk'];
    $nama_lengkap   =$_POST['nama_lengkap'];
    $jabatan        =$_POST['jabatan'];
    $akses          =$_POST['akses'];
    $password       =$_POST['password'];
    $email          =$_POST['email'];
    $no_telepon     =$_POST['no_telepon'];

    // Masukkan data ke dalam database
    $query = "INSERT INTO tb_pengguna (noinduk, nama_lengkap, jabatan, akses, password, email, no_hp) 
    VALUES ('$no_induk', '$nama_lengkap', '$jabatan', '$akses', '$password', '$email', '$no_telepon')";

    if (mysqli_query($koneksi, $query)) {
        echo '<script language="javascript" type="text/javascript">
          alert("Data pengguna berhasil disimpan.");</script>';
        echo "<meta http-equiv='refresh' content='0; url=pengaturan_akun.php'>";
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }

mysqli_close($koneksi);
?>