<?php
include 'koneksi.php'; // Sertakan koneksi ke database

// Fungsi untuk menghasilkan UUID
function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

// Data yang diterima dari form HTML
$id_pg          = generateUUID(); // Menghasilkan UUID baru untuk primary key
$no_induk       = $_POST['noinduk'];
$nama_lengkap   = $_POST['nama_lengkap'];
$jabatan        = $_POST['jabatan'];
$akses          = $_POST['akses'];
$password       = hash('sha256', $_POST['password']); // Hash password dengan SHA-256
$email          = $_POST['email'];
$no_telepon     = $_POST['no_telepon'];

// Menggunakan prepared statement untuk mencegah SQL Injection
$stmt = $koneksi->prepare("INSERT INTO tb_pengguna (id_pg, noinduk, nama_lengkap, jabatan, akses, password, email, no_hp) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $id_pg, $no_induk, $nama_lengkap, $jabatan, $akses, $password, $email, $no_telepon);

// Eksekusi query dan cek hasilnya
if ($stmt->execute()) {
    echo '<script language="javascript" type="text/javascript">
          alert("Data pengguna berhasil disimpan.");</script>';
    echo "<meta http-equiv='refresh' content='0; url=pengaturan_akun.php'>";
} else {
    // Jika terjadi error, tampilkan pesan error
    echo "Error: " . $stmt->error;
}

// Tutup statement dan koneksi database
$stmt->close();
mysqli_close($koneksi);