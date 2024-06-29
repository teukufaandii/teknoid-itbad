<?php
include 'koneksi.php'; // Sertakan koneksi ke database

// Fungsi untuk menghasilkan UUID
function generateUUID() {
    // Membuat format UUID versi 4 (berdasarkan random)
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),
        // 16 bits for "time_hi_and_version", 4 most significant bits hold version number 4
        mt_rand(0, 0x0fff) | 0x4000,
        // 16 bits, 8 bits for "clk_seq_hi_res", 8 bits for "clk_seq_low", 2 most significant bits hold zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,
        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Data yang diterima dari form HTML
$id_pg          = generateUUID(); // Menghasilkan UUID baru untuk primary key
$no_induk       = $_POST['noinduk'];
$nama_lengkap   = $_POST['nama_lengkap'];
$jabatan        = $_POST['jabatan'];
$akses          = $_POST['akses'];
$password       = $_POST['password'];
$email          = $_POST['email'];
$no_telepon     = $_POST['no_telepon'];

// Query untuk memasukkan data ke dalam database
$query = "INSERT INTO tb_pengguna (id_pg, noinduk, nama_lengkap, jabatan, akses, password, email, no_hp) 
          VALUES ('$id_pg', '$no_induk', '$nama_lengkap', '$jabatan', '$akses', '$password', '$email', '$no_telepon')";

// Eksekusi query dan cek hasilnya
if (mysqli_query($koneksi, $query)) {
    echo '<script language="javascript" type="text/javascript">
          alert("Data pengguna berhasil disimpan.");</script>';
    echo "<meta http-equiv='refresh' content='0; url=pengaturan_akun.php'>";
} else {
    // Jika terjadi error, tampilkan pesan error
    echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
}

// Tutup koneksi database
mysqli_close($koneksi);
?>
