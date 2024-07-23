<?php
session_start();
include 'koneksi.php';
include "logout-checker.php";
// Periksa apakah session username telah diatur
if (!isset($_SESSION['pengguna_type'])) {
    echo '<script language="javascript" type="text/javascript">
    alert("Anda Tidak Berhak Masuk Kehalaman Ini!");</script>';
    echo "<meta http-equiv='refresh' content='0; url=../index.php'>";
    exit;
}

$conn = new mysqli($host, $user, $pass, $db);

// Fungsi untuk mengecek apakah jenis surat yang dipilih adalah "Surat KKL"
function isSuratKKL($jenis_surat)
{
    return $jenis_surat === "3";
}

// Fungsi untuk mengecek apakah jenis surat yang dipilih adalah "Surat Cuti"
function isSuratRiset($jenis_surat)
{
    return $jenis_surat === "4";
}

function isSuratInsentif($jenis_surat)
{
    return $jenis_surat === "5";
}

function isSuratRisetDosen($jenis_surat)
{
    return $jenis_surat === "6";
}


// Memeriksa apakah form dikirimkan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis_surat = $_POST["jenis_surat"];
    $asal_surat = $_POST["asal_surat"];
    $perihal = $_POST["perihal"];
    $nomor_surat = $_POST["nomor_surat"];
    $tujuan_surat = $_POST["tujuan_surat"];
    $email = $_POST["email"];
    $prodi = $_POST["prodi"];
    $nama_lengkap = $_POST["nama_lengkap"];
    $nim = $_POST["nim"];
    $nidn = $_POST["nidn"];
    $no_hp = $_POST["no_hp"];
    $nama_lengkap2 = $_POST["nama_lengkap2"];
    $nim2 = $_POST["nim2"];
    $no_hp2 = $_POST["no_hp2"];
    $nama_lengkap3 = $_POST["nama_lengkap3"];
    $nim3 = $_POST["nim3"];
    $no_hp3 = $_POST["no_hp3"];
    $deskripsi = $_POST["deskripsi"];
    $tanggal_surat = date("Y-m-d");
    $nama_perusahaan = $_POST["nama_perusahaan"];
    $alamat_perusahaan = $_POST["alamat_perusahaan"];
    $ttl = $_POST["ttl"];
    $alamat_domisili = $_POST["alamat_domisili"];
    $ke_humas = "Humas";
    $ke_lp3m = "lp3m";

    $sql = "";
    if (isSuratKKL($jenis_surat)) {
        $sql = "INSERT INTO tb_surat_dis (jenis_surat, asal_surat, perihal, nomor_surat, tanggal_surat, tujuan_surat,
                     email, nama_lengkap, nim, no_hp,  nama_lengkap2, nim2, no_hp2, nama_lengkap3, nim3, no_hp3, 
                     prodi, nama_perusahaan, alamat_perusahaan, deskripsi, diteruskan_ke)
            VALUES ('$jenis_surat', '$asal_surat', '$perihal', '$nomor_surat', '$tanggal_surat', '$tujuan_surat', 
                    '$email', '$nama_lengkap', '$nim', '$no_hp', '$nama_lengkap2', '$nim2', '$no_hp2', '$nama_lengkap3', '$nim3', '$no_hp3', 
                    '$prodi', '$nama_perusahaan', '$alamat_perusahaan', '$deskripsi', '$ke_humas')";
    } elseif (isSuratRiset($jenis_surat)) {
        $sql = "INSERT INTO tb_surat_dis (jenis_surat, asal_surat, perihal, nomor_surat, tanggal_surat, 
                            tujuan_surat, email, nama_lengkap, nim, prodi, no_hp, nama_perusahaan, alamat_perusahaan, deskripsi, ttl, alamat_domisili, diteruskan_ke)
                    VALUES ('$jenis_surat', '$asal_surat', '$perihal', '$nomor_surat', '$tanggal_surat', '$tujuan_surat', '$email', 
                            '$nama_lengkap', '$nim', '$prodi', '$no_hp', '$nama_perusahaan', '$alamat_perusahaan', '$deskripsi', '$ttl', '$alamat_domisili', '$ke_humas')";
    } elseif (isSuratRisetDosen($jenis_surat)) {
        $sql = "INSERT INTO tb_surat_dis (jenis_surat, asal_surat, perihal, nomor_surat, tanggal_surat, 
                tujuan_surat, email, nama_lengkap, nidn, no_hp, nama_perusahaan, alamat_perusahaan, deskripsi, ttl, alamat_domisili, diteruskan_ke)
                    VALUES ('$jenis_surat', '$asal_surat', '$perihal', '$nomor_surat', '$tanggal_surat', '$tujuan_surat', '$email', 
                            '$nama_lengkap', '$nidn', '$no_hp', '$nama_perusahaan', '$alamat_perusahaan', '$deskripsi', '$ttl', '$alamat_domisili', '$ke_lp3m')";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Surat berhasil dikirim'); setTimeout(function() {
            window.location.href = 'surat_keluar.php';}, 1000);
            </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Tambah Surat - Teknoid</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="../logo itbad.png">
    <link href="css/dashboard-style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- sidenav -->
    <?php include "sidenav.php" ?>

    <!-- content -->
    <div class="content" id="Content">
        <!-- topnav -->
        <?php include "topnav.php" ?>

        <div class="mainContent" id="mainContent">
            <div class="contentBox">
                <div class="pageInfo">
                    <h3>Tambah Surat Non Disposisi</h3>
                    <button class="back" onclick="goBack()">Kembali</button>
                </div>

                <?php if ($_SESSION['jabatan'] == 'Mahasiswa') { ?>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form" enctype="multipart/form-data">
                        <div class="inputfield">
                            <label for="jenis_surat">Jenis Surat*</label>
                            <div class="custom_select">
                                <select name="jenis_surat" id="jenis_surat" class="select" required>
                                    <option value="" hidden>Pilih Jenis Surat</option>
                                    <option value="3">Surat KKL</option>
                                    <option value="4">Surat Riset</option>
                                </select>
                            </div>
                        </div>

                        <div class="inputfield">
                            <label for="">Asal Surat*</label>
                            <input type="text" class="input" name="asal_surat" placeholder="Masukkan Asal Surat" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" readonly>
                        </div>

                        <div class="inputfield" id="jumlah_mahasiswa_atas">
                            <label for="jumlah_mahasiswa">Jumlah Mahasiswa*</label>
                            <div class="custom_select">
                                <select name="jumlah_mahasiswa" id="jumlah_mahasiswa" class="select">
                                    <option value="" hidden>Pilih Jumlah Mahasiswa</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div>

                        <div id="mahasiswa_fields">
                            <!-- Input untuk Mahasiswa 1 -->
                            <div class="inputfield">
                                <label for="nama_mahasiswa1">Nama Mahasiswa <b>1</b>*</label>
                                <input type="text" class="input" name="nama_lengkap" placeholder="Masukkan Nama Mahasiswa 1">
                            </div>
                            <div class="inputfield">
                                <label for="nim_mahasiswa1">NIM Mahasiswa <b>1</b>*</label>
                                <input type="number" class="input" name="nim" placeholder="Masukkan NIM Mahasiswa 1">
                            </div>
                            <div class="inputfield">
                                <label for="no_hp1">Nomor Telepon <b>1</b>*</label>
                                <input type="number" class="input" name="no_hp" placeholder="Masukkan Nomor Telepon Mahasiswa 1">
                            </div>

                            <!-- Input untuk Mahasiswa 2 -->
                            <div class="inputfield" id="mahasiswa2" style="display: none;">
                                <label for="nama_mahasiswa2">Nama Mahasiswa <b>2</b>*</label>
                                <input type="text" class="input" name="nama_lengkap2" placeholder="Masukkan Nama Mahasiswa 2">
                            </div>
                            <div class="inputfield" id="nim_mahasiswa2" style="display: none;">
                                <label for="nim_mahasiswa2">NIM Mahasiswa <b>2</b>*</label>
                                <input type="number" class="input" name="nim2" placeholder="Masukkan NIM Mahasiswa 2">
                            </div>
                            <div class="inputfield" id="no_hp2" style="display: none;">
                                <label for="no_hp2">Nomor Telepon <b>2</b>*</label>
                                <input type="number" class="input" name="no_hp2" placeholder="Masukkan Nomor Telepon Mahasiswa 2">
                            </div>

                            <!-- Input untuk Mahasiswa 3 -->
                            <div class="inputfield" id="mahasiswa3" style="display: none;">
                                <label for="nama_mahasiswa3">Nama Mahasiswa <b>3</b>*</label>
                                <input type="text" class="input" name="nama_lengkap3" placeholder="Masukkan Nama Mahasiswa 3">
                            </div>
                            <div class="inputfield" id="nim_mahasiswa3" style="display: none;">
                                <label for="nim_mahasiswa3">NIM Mahasiswa <b>3</b>*</label>
                                <input type="number" class="input" name="nim3" placeholder="Masukkan NIM Mahasiswa 3">
                            </div>
                            <div class="inputfield" id="no_hp3" style="display: none; margin-bottom: 15px;">
                                <label for="no_hp3">Nomor Telepon <b>3</b>*</label>
                                <input type="number" class="input" name="no_hp3" placeholder="Masukkan Nomor Telepon Mahasiswa 3">
                            </div>
                        </div>

                        <!-- Input tambahan untuk Surat Riset -->
                        <div class="inputfield" id="surat_riset_fields_1" style="display: none;">
                            <label for="ttl">Tempat, tanggal lahir*</label>
                            <input type="text" class="input" name="ttl" id="ttl" placeholder="Masukkan Tempat, tanggal lahir">
                        </div>

                        <div class="inputfield" id="surat_riset_fields_2" style="display: none;">
                            <label for="alamat_domisili">Alamat Domisili*</label>
                            <input type="text" class="input" name="alamat_domisili" id="alamat_domisili" placeholder="Masukkan Alamat Domisili">
                        </div>

                        <div class="inputfield">
                            <label for="">Perihal*</label>
                            <input type="text" class="input" name="perihal" placeholder="Masukkan Perihal" required>
                        </div>

                        <div class="inputfield">
                            <label for="">Alamat Email*</label>
                            <input type="email" class="input" name="email" placeholder="Masukkan Alamat Email" required>
                        </div>

                        <div class="inputfield">
                            <label for="">Program Studi*</label>
                            <div class="custom_select">
                                <select name="prodi" id="prodi" class="select" required>
                                    <option value="" hidden>Pilih Program Studi</option>
                                    <option>Prodi S2 Keuangan Syariah</option>
                                    <option>Prodi S1 Sistem Informasi</option>
                                    <option>Prodi S1 Teknologi Informasi</option>
                                    <option>Prodi S1 Desain Komunikasi Visual</option>
                                    <option>Prodi S1 Arsitektur</option>
                                    <option>Prodi S1 Manajemen</option>
                                    <option>Prodi S1 Akuntansi</option>
                                    <option>Prodi D3 Akuntansi</option>
                                    <option>Prodi D3 Keuangan dan Perbankan</option>
                                </select>
                            </div>
                        </div>

                        <div class="inputfield">
                            <label for="">Deskripsi Singkat</label>
                            <input type="text" class="input" name="deskripsi" placeholder="Masukkan Deskripsi Singkat" maxlength="200">
                        </div>

                        <!-- Input tambahan untuk Surat KKL -->
                        <div class="inputfield" id="surat_kkl_fields" style="display: none;">
                            <label for="nama_perusahaan">Nama Perusahaan* </label>
                            <input type="text" class="input" name="nama_perusahaan" id="nama_perusahaan" placeholder="Masukkan Nama Perusahaan">

                            <label for="alamat_perusahaan">Alamat Perusahaan*</label>
                            <input type="text" class="input" name="alamat_perusahaan" id="alamat_perusahaan" placeholder="Masukkan Alamat Perusahaan">
                        </div>

                        <!-- Input tambahan untuk Surat Riset -->
                        <div class="inputfield" id="surat_riset_fields_3" style="display: none;">
                            <label for="nama_perusahaan">Nama Perusahaan* </label>
                            <input type="text" class="input" name="nama_perusahaan" id="nama_perusahaan" placeholder="Masukkan Nama Perusahaan">
                        </div>
                        <div class="inputfield" id="surat_riset_fields_4" style="display: none;">
                            <label for="alamat_perusahaan">Alamat Perusahaan*</label>
                            <input type="text" class="input" name="alamat_perusahaan" id="alamat_perusahaan" placeholder="Masukkan Alamat Perusahaan">
                        </div>

                        <!--untuk Tujuan Surat-->
                        <div class="inputfield">
                            <label for=""></label>
                            <input type="text" class="input" name="tujuan_surat" placeholder="Masukkan Tujuan Surat" value="Humas" hidden>
                        </div>

                        <div class="inputfield">
                            <label for=""></label>
                            <input type="text" class="input" name="nomor_surat" placeholder="Masukkan Nomor Surat" hidden>
                        </div>

                        <div class="btn-kirim">
                            <div class="floatFiller">ff</div>
                            <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                            <button class="btn" type="button" onclick="showConfirmationPopup()">Kirim</button>
                        </div>
                    </form>
                <?php } ?>

                <?php if ($_SESSION['jabatan'] == 'Dosen') { ?>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form" enctype="multipart/form-data">
                        <div class="inputfield">
                            <label for="jenis_surat">Jenis Surat*</label>
                            <div class="custom_select">
                                <select name="jenis_surat" id="jenis_surat" class="select" required>
                                    <option value="5">Surat Pengajuan Insentif Dosen</option>
                                    <option value="6">Surat Pengajuan Riset Dosen</option>
                                </select>
                            </div>
                        </div>
                        <div class="inputfield">
                            <label for="">Asal Surat*</label>
                            <input type="text" class="input" name="asal_surat" placeholder="Masukkan Asal Surat" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" readonly>
                        </div>

                        <div class="inputfield" id="jumlah_dosen_atas" style="display: none;">
                            <label for="jumlah_dosen">Jumlah Dosen*</label>
                            <div class="custom_select">
                                <input type="number" name="jumlah_dosen" id="jumlah_dosen" class="select" value="1">
                            </div>
                        </div>

                        <div class="inputfield">
                            <label for="nama_dosen1">Nama Dosen</label>
                            <input type="text" class="input" name="nama_lengkap" placeholder="Masukkan Nama">
                        </div>

                        <div class="inputfield">
                            <label for="nim_dosen1">NIDN</label>
                            <input type="number" class="input" name="nidn" placeholder="Masukkan NIDN">
                        </div>

                        <div class="inputfield">
                            <label for="no_hp1">Nomor Telepon </label>
                            <input type="number" class="input" name="no_hp" placeholder="Masukkan Nomor Telepon">
                        </div>

                        <!-- untuk surat insentif -->
                        <div id="input_insentif">
                            <div class="inputfield">
                                <label for="status_pengusul">Status pengusul</label>
                                <div class="custom_select">
                                    <select name="status" id="status" class="select" required>
                                        <option value="" hidden>Pilih Status Pengusul</option>
                                        <option>Ketua</option>
                                        <option>Anggota</option>
                                    </select>
                                </div>
                            </div>
                            <div class="inputfield">
                                <label for="id_sinta">ID SINTA*</label>
                                <input type="text" class="input" name="id_sinta" id="id_sinta" placeholder="Masukkan ID SINTA">
                            </div>
                            <div class="inputfield">
                                <label for="prodi">Program Studi Pengusul*</label>
                                <div class="custom_select">
                                    <select name="prodi" id="prodi" class="select" required>
                                        <option value="" hidden>Pilih Program Studi</option>
                                        <option>Prodi S2 Keuangan Syariah</option>
                                        <option>Prodi S1 Sistem Informasi</option>
                                        <option>Prodi S1 Teknologi Informasi</option>
                                        <option>Prodi S1 Desain Komunikasi Visual</option>
                                        <option>Prodi S1 Arsitektur</option>
                                        <option>Prodi S1 Manajemen</option>
                                        <option>Prodi S1 Akuntansi</option>
                                        <option>Prodi D3 Akuntansi</option>
                                        <option>Prodi D3 Keuangan dan Perbankan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="inputfield">
                                <label for="prodi">Jenis Insentif*</label>
                                <div class="custom_select">
                                    <select name="jenis_insentif" id="jenis_insentif" class="select" required>
                                        <option value="" hidden>Pilih Jenis Insentif</option>
                                        <option value="penelitian">Penelitian & Pengabdian Masyarakat dengan Pendanaan Eksternal - Kompetitif</option>
                                        <option value="publikasi">Publikasi Ilmiah</option>
                                        <option value="pertemuan_ilmiah">Penyaji Paper Dalam Pertemuan Ilmiah</option>
                                        <option value="keynote_speaker">Pembicara Utama [Keynote Speaker] Dalam Pertemuan ilmiah</option>
                                        <option value="visiting">Visiting Lecturer/Researcher</option>
                                        <option value="hki">Hak Atas Kekayaan Intelektual [HKI]</option>
                                        <option value="teknologi">Teknologi Tepat Guna</option>
                                        <option value="buku">Buku</option>
                                        <option value="model">Model, Prototype, Desain, Karya Seni, Rekayasa Sosial, Kebijakan</option>
                                        <option value="insentif_publikasi">Insentif Publikasi Berita Kegiatan Pengabdian masyarakat di Koran, Majalah, Tabloid, TV dan Media Online</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Penelitian & Pengabdian Masyarakat dengan Pendanaan Eksternal-Kompetitif -->
                            <div id="penelitian" style="display: none;">
                                <div class="inputfield">
                                    <label for="skema">Skema*</label>
                                    <div class="custom_select">
                                        <select name="skema" id="skema" class="select" required>
                                            <option value="" hidden>Pilih Jenis Skema</option>
                                            <option>Penelitian Berbasis Kompetitif (atau skema lain yang lebih tinggi dari skema berbasis kompetensi)</option>
                                            <option>Semua Skema Pengabdian Masyarakat dari Kemenristekdikti RI </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="inputfield">
                                    <label for="id_sinta">Judul Penelitian/Pengabdian Masyarakat*</label>
                                    <input type="text" class="input" name="id_sinta" id="id_sinta" placeholder="Masukkan Judul Penelitian/Pengabdian Masyarakat">
                                </div>
                                <div class="inputfield">
                                    <label for="">Upload Berkas Pendukung</label>
                                    <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                                    <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                                </div>
                            </div>

                            <!-- Publikasi Ilmiah -->
                            <div id="publikasi" style="display: none;">
                                <div class="inputfield">
                                    <label for="prodi">Jenis Publikasi/Jurnal*</label>
                                    <div class="custom_select">
                                        <select name="jenis_publikasi" id="jenis_publikasi" class="select" required>
                                            <option value="" hidden>Pilih Publikasi/Jurnal</option>
                                            <option>Internasional Bereputasi [Terindeks ISI Knowledge, Thomson Reuter, USA dan Scopus, Netherland]</option>
                                            <option>Internasional Bereputasi Q1</option>
                                            <option>Internasional Bereputasi Q2</option>
                                            <option>Internasional Bereputasi Q3</option>
                                            <option>Internasional Bereputasi Q4</option>
                                            <option>Internasional Tidak Bereputasi</option>
                                            <option>Nasional Terakreditasi (SINTA 1)</option>
                                            <option>Nasional Terakreditasi (SINTA 2)</option>
                                            <option>Nasional Terakreditasi (SINTA 3)</option>
                                            <option>Nasional Terakreditasi (SINTA 4)</option>
                                            <option>Nasional Terakreditasi (SINTA 5)</option>
                                            <option>Nasional Terakreditasi (SINTA 6)</option>
                                            <option>Nasional tidak Terakreditasi (ber-ISSN)</option>
                                            <option>Lokal Tidak ber-ISSN</option>
                                            <option>Koran/Tabloid/Majalah Lokal</option>
                                            <option>Koran/Tabloid/Majalah Nasional</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="inputfield">
                                    <label for="judul_publikasi">Judul Publikasi*</label>
                                    <input type="text" class="input" name="judul_publikasi" id="judul_publikasi" placeholder="Masukkan Judul Publikasi">
                                </div>
                                <div class="inputfield">
                                    <label for="nama_jurnal">Nama Jurnal/Koran/Majalah/Penerbit*</label>
                                    <input type="text" class="input" name="nama_jurnal" id="nama_jurnal" placeholder="Masukkan Nama Jurnal/Koran/Majalah/Penerbit">
                                </div>
                                <div class="inputfield">
                                    <label for="">Vol. No. Tahun. ISSN-Edisi-Halaman*</label>
                                    <input type="text" class="input" name="nama_jurnal" id="nama_jurnal" placeholder="Contoh: Vol. 2 No. 1 th. 2022 ISSN: 12345 Hal. 12-30">
                                </div>
                                <div class="inputfield">
                                    <label for="">Tautan/link jurnal atau berkas pendukung</label>
                                    <input type="text" class="input" name="link" id="link" placeholder="">
                                </div>
                                <div class="inputfield">
                                    <label for="">Upload Berkas Pendukung</label>
                                    <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                                    <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                                </div>
                            </div>


                            <!-- Penyaji paper dalam pertemuan ilmiah-->
                            <div id="pertemuan_ilmiah" style="display: none;">
                                <div class="inputfield">
                                    <label for="">Skala*</label>
                                    <div class="custom_select">
                                        <select name="skala" id="skala" class="select" required>
                                            <option value="" hidden>Pilih Skala Paper</option>
                                            <option>International</option>
                                            <option>Nasional</option>
                                            <option>Lokal/Regional</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="inputfield">
                                    <label for="">Nama Pertemuan*</label>
                                    <input type="text" class="input" name="nama_pertemuan" id="nama_pertemuan" placeholder="Sebutkan nama acara, penyelenggara, dan waktu pelaksanaan">
                                </div>

                                <div class="inputfield">
                                    <label for="">Usulan Biaya*</label>
                                    <input type="text" class="input" name="usulan_biaya" id="usulan_biaya" placeholder="Sebutkan biaya yang diusulkan seperti biaya registasi, transportasi, hotel, dll.">
                                </div>

                                <div class="inputfield">
                                    <label for="">Upload Berkas Pendukung</label>
                                    <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                                    <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                                </div>
                            </div>


                            <!-- Penyaji paper dalam keynote speaker-->
                            <div id="keynote_speaker" style="display: none;">
                                <div class="inputfield">
                                    <label for="">Skala*</label>
                                    <div class="custom_select"><select name="skala_keynote" id="skala_keynote" class="select" required>
                                            <option value="" hidden>Pilih Skala Paper l</option>
                                            <option>International</option>
                                            <option>Nasional</option>
                                            <option>Lokal/Regional</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="inputfield">
                                    <label for="">Nama pertemuan Ilmiah*</label>
                                    <input type="text" class="input" name="nama_keynote" id="nama_keynote" placeholder="Sebutkan juga nama penyelenggara, dan waktu pelaksana">
                                </div>
                                <div class="inputfield">
                                    <label for="">Upload Berkas Pendukung</label>
                                    <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                                    <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                                </div>
                            </div>


                            <!-- visiting lecturer-->
                            <div id="visiting" style="display: none;">
                                <div class="inputfield">
                                    <label for="">Sebutkan Nama Kegiatan dan Lembaga Tujuan*</label>
                                    <input type="text" class="input" name="visiting_nama" id="nama_pertemuan" placeholder="" required>
                                </div>

                                <div class="inputfield">
                                    <label for="">Waktu Pelaksanaan*</label>
                                    <input type="datetime-local" class="input" name="visiting_waktu" id="nama_pertemuan" placeholder="" required>
                                </div>

                                <div class="inputfield">
                                    <label for="">Upload Berkas Pendukung</label>
                                    <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                                    <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                                </div>
                            </div>


                            <!-- Hak kekayaan intelektual (HKI) -->
                            <div id="hki" style="display: none;">
                                <div class="inputfield">
                                    <label for="">Jenis Kekayaan Intelektual*</label>
                                    <div class="custom_select"><select name="HKI_jenis" id="HKI_jenis" class="select" required>
                                            <option value="" hidden>Pilih Jenis Kekayaan Intelektual</option>
                                            <option>HKI atas hasil penelitian/buku</option>
                                            <option>Paten, Paten Sederhana</option>
                                            <option>Hak Cipta, Merek Dagang, Rahasia Dagang, Desain Produk Industri, Indikasi Geografis</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="inputfield">
                                    <label for="">Judul Kekayaan Intelektual*</label>
                                    <input type="text" class="input" name="HKI_judul" id="HKI_judul" placeholder="Lengkapi dengan nomor atau identitas lainnya" required>
                                </div>

                                <div class="inputfield">
                                    <label for="">Upload Berkas Pendukung* <br> Lengkapi dengan nomor atau Identitas Lainnya</label>
                                    <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                                    <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                                </div>
                            </div>


                            <!-- teknologi tepat guna -->
                            <div id="teknologi" style="display: none;">
                                <div class="inputfield">
                                    <label for="">Sebutkan Teknologi Tepat Guna Yang Diusulkan*</label>
                                    <textarea type="text" class="input" name="" id="" style="resize: none;"></textarea>
                                </div>
                                <div class="inputfield">
                                    <label for="">Deskripsikan Teknologi Tepat Guna Yang Diusulkan*</label>
                                    <textarea type="text" class="input" name="deskripsi" id="deskripsi" style="resize: none;"></textarea>
                                </div>
                                <div class="inputfield">
                                    <label for="">Upload Berkas Pendukung*</label>
                                    <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                                    <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                                </div>
                            </div>


                            <!-- buku -->
                            <div id="buku" style="display: none;">
                                <div class="inputfield">
                                    <label>Jenis Buku*</label>
                                    <div class="custom_select"><select name="HKI_jenis" id="HKI_jenis" class="select" required>
                                            <option value="" hidden>Pilih Jenis Buku</option>
                                            <option>Buku Teks [ber-ISBN]</option>
                                            <option>Buku Ajar [ber-ISBN]</option>
                                            <option>Chapter book dalam buku bereputasi internasional [Springer, Sage Publication; dll]</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="inputfield">
                                    <label for="">Judul Buku*</label>
                                    <input type="text" class="input" name="judul_buku" id="judul_buku" placeholder="Masukkan Judul Buku" required>
                                </div>
                                <div class="inputfield">
                                    <label for="">Sinopsis*</label>
                                    <textarea type="text" class="input" name="sinopsis" id="sinopsis" style="resize: none;" placeholder="Masukkan Sinopsis" required></textarea>
                                </div>
                                <div class="inputfield">
                                    <label for="">ISBN/Jumlah halaman/Penerbit*</label>
                                    <input type="text" class="input" name="isbn" id="isbn" placeholder="Masukkan ISBN/Jumlah halaman/Penerbit" required>
                                </div>

                                <div class="inputfield">
                                    <label for="">Upload Buku*</label>
                                    <i class="fa-solid fa-circle-info" data-tooltip="additional-info"></i>
                                    <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                                    <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                                    <p class="additional-info" style="color:red; display: none;">
                                        ! Jika buku dalam dicetak/hard copy silahkan upload foto sampulnya, versi cetak silahkan langsung sampaikan kepada LP3M
                                    </p>
                                </div>
                            </div>


                            <!-- Model, prototype, desain, karya seni, rekayasa sosial, kebijakan-->
                            <div id="model" style="display: none;">
                                <div class="inputfield">
                                    <label for="">Sebutkan Nama Model, Prototype, Desain, Karya Seni, Rekayasa Sosial, Kebijakan yang Diusulkan*</label>
                                    <input type="text" class="input" name="prototype_nama" id="prototype_nama" placeholder="" required>
                                </div>

                                <div class="inputfield">
                                    <label for="">Deskripsikan Model, Prototype, Desain, Karya Seni, Rekayasa Sosial, Kebijakan yang Diusulkan*</label>
                                    <input type="text" class="input" name="prototype_deskripsi" id="prototype_deskripsi" placeholder="" required>
                                </div>

                                <div class="inputfield">
                                    <label for="">Upload Berkas Pendukung*</label>
                                    <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                                    <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                                </div>
                            </div>


                            <!-- Insentif publikasi berita kegiatan pengabdian masyarakat di koran, majalah, tabloid, TV dan Media Online-->
                            <div id="insentif_publikasi" style="display: none;">
                                <div class="inputfield">
                                    <label for="">Judul Publikasi*</label>
                                    <input type="text" class="input" name="publikasi_judul" id="publikasi_judul" placeholder="" required>
                                </div>

                                <div class="inputfield">
                                    <label for="">Nama Penerbit dan Waktu Terbit*</label>
                                    <input type="text" class="input" name="publikasi_nama_waktu" id="publikasi_nama_waktu" placeholder="" required>
                                </div>

                                <div class="inputfield">
                                    <label for="">Tautan Publikasi Berita (jika online)*</label>
                                    <input type="text" class="input" name="publikasi_tautan" id="publikasi_tautan" placeholder="" required>
                                </div>
                                <div class="inputfield">
                                    <label for="">Upload Publikasi Berita (jika media cetak)*</label>
                                    <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;">
                                    <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                                </div>

                            </div>

                            <div class="inputfield">
                                <label for="">Upload Form Insentif</label>
                                <input type="file" class="input" name="file_berkas" accept="application/pdf" style="border: none;" required>
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>
                        </div>

                        <!------------------- untuk surat riset dosen ----------------->
                        <div id="input_riset" style="display: none;">
                            <div class="inputfield" id="surat_riset_fields_1">
                                <label for="ttl">Tempat, tanggal lahir*</label>
                                <input type="text" class="input" name="ttl" id="ttl" placeholder="Masukkan Tempat, tanggal lahir">
                            </div>

                            <div class="inputfield" id="surat_riset_fields_2">
                                <label for="alamat_domisili">Alamat Domisili*</label>
                                <input type="text" class="input" name="alamat_domisili" id="alamat_domisili" placeholder="Masukkan Alamat Domisili">
                            </div>

                            <div class="inputfield">
                                <label for="">Perihal*</label>
                                <input type="text" class="input" name="perihal" placeholder="Masukkan Perihal" required>
                            </div>

                            <div class="inputfield">
                                <label for="">Alamat Email*</label>
                                <input type="email" class="input" name="email" placeholder="Masukkan Alamat Email" required>
                            </div>

                            <div class="inputfield">
                                <label for="">Deskripsi Singkat</label>
                                <input type="text" class="input" name="deskripsi" placeholder="Masukkan Deskripsi Singkat" maxlength="200">
                            </div>

                            <div class="inputfield" id="surat_riset_fields_3">
                                <label for="nama_perusahaan">Nama Perusahaan* </label>
                                <input type="text" class="input" name="nama_perusahaan" id="nama_perusahaan" placeholder="Masukkan Nama Perusahaan">
                            </div>

                            <div class="inputfield" id="surat_riset_fields_4">
                                <label for="alamat_perusahaan">Alamat Perusahaan*</label>
                                <input type="text" class="input" name="alamat_perusahaan" id="alamat_perusahaan" placeholder="Masukkan Alamat Perusahaan">
                            </div>

                            <div class="inputfield">
                                <label for=""></label>
                                <input type="text" class="input" name="tujuan_surat" placeholder="Masukkan Tujuan Surat" value="Humas" hidden>
                            </div>

                            <div class="inputfield">
                                <label for=""></label>
                                <input type="text" class="input" name="nomor_surat" placeholder="Masukkan Nomor Surat" hidden>
                            </div>
                        </div>

                        <div class="btn-kirim">
                            <div class="floatFiller">ff</div>
                            <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                            <button class="btn" type="button" onclick="showConfirmationPopup()">Kirim</button>
                        </div>
                    </form>
                <?php } ?>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </div>
    <script src="js/dashboard-js.js"></script>

    <script>
        // Function to show SweetAlert confirmation popup
        function showConfirmationPopup() {
            Swal.fire({
                title: 'Konfirmasi?',
                text: 'Anda yakin ingin mengirim surat?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, kirim!',
                cancelButtonText: 'Tidak, periksa kembali',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, trigger form submission
                    document.getElementById('submitForm').click();
                }
            });
        }

        // Function to handle form submission response
        function handleFormSubmissionResponse(success) {
            if (success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'The letter has been sent successfully.',
                    icon: 'success'
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to send the letter. Please try again later.',
                    icon: 'error'
                });
            }
        }
    </script>

    <script>
        function goBack() {
            window.history.back();
        }

        document.getElementById('jenis_surat').addEventListener('change', function() {
            let suratKKLFields = document.getElementById('surat_kkl_fields');
            let suratRisetFields1 = document.querySelectorAll('#surat_riset_fields_1, #surat_riset_fields_2');
            let suratRisetFields2 = document.querySelectorAll('#surat_riset_fields_3, #surat_riset_fields_4');
            let jumlahMahasiswaField = document.getElementById('jumlah_mahasiswa_atas');
            let mahasiswaField = document.getElementById('mahasiswa2');
            let mahasiswa3 = document.getElementById('mahasiswa3');
            let nim2 = document.getElementById('nim_mahasiswa2');
            let nim3 = document.getElementById('nim_mahasiswa3');
            let no_hp2 = document.getElementById('no_hp2');
            let no_hp3 = document.getElementById('no_hp3');

            // Tampilkan atau sembunyikan input tambahan untuk Surat KKL atau Surat Riset
            if (this.value === '3') {
                suratKKLFields.style.display = 'none';
                // Mengakses elemen-elemen dalam kumpulan suratRisetFields1 dan suratRisetFields2 menggunakan indeks
                for (let i = 0; i < suratRisetFields1.length; i++) {
                    suratRisetFields1[i].style.display = 'none'; // Sembunyikan div pertama
                    suratRisetFields2[i].style.display = 'flex'; // Sembunyikan div kedua
                }
                jumlahMahasiswaField.style.display = 'flex'; // Tampilkan input jumlah mahasiswa
            } else if (this.value === '4') {
                suratKKLFields.style.display = 'none';
                // Mengakses elemen-elemen dalam kumpulan suratRisetFields1 dan suratRisetFields2 menggunakan indeks
                for (let i = 0; i < suratRisetFields1.length; i++) {
                    suratRisetFields1[i].style.display = 'flex'; // Tampilkan div pertama
                    suratRisetFields2[i].style.display = 'flex'; // Tampilkan div kedua
                }
                jumlahMahasiswaField.style.display = 'none'; // Sembunyikan input jumlah mahasiswa
                mahasiswaField.style.display = 'none';
                mahasiswa3.style.display = 'none';
                nim2.style.display = 'none';
                no_hp2.style.display = 'none';
                no_hp3.style.display = 'none';
            } else {
                suratKKLFields.style.display = 'none';
                // Mengakses elemen-elemen dalam kumpulan suratRisetFields1 dan suratRisetFields2 menggunakan indeks
                for (let i = 0; i < suratRisetFields1.length; i++) {
                    suratRisetFields1[i].style.display = 'none'; // Sembunyikan div pertama
                    suratRisetFields2[i].style.display = 'none'; // Sembunyikan div kedua
                }
                jumlahMahasiswaField.style.display = 'none'; // Sembunyikan input jumlah mahasiswa
            }
        });


        document.getElementById('jumlah_mahasiswa').addEventListener('change', function() {
            let mahasiswaFields = document.getElementById('mahasiswa_fields');
            let mahasiswa2 = document.getElementById('mahasiswa2');
            let no_hp2 = document.getElementById('no_hp2');
            let nim_mahasiswa2 = document.getElementById('nim_mahasiswa2');
            let mahasiswa3 = document.getElementById('mahasiswa3');
            let nim_mahasiswa3 = document.getElementById('nim_mahasiswa3');
            let no_hp3 = document.getElementById('no_hp3');


            // Tampilkan atau sembunyikan input untuk Mahasiswa 2 dan Mahasiswa 3 sesuai dengan jumlah yang dipilih
            if (this.value === '1') {
                mahasiswa2.style.display = 'none';
                nim_mahasiswa2.style.display = 'none';
                no_hp2.style.display = 'none';
                mahasiswa3.style.display = 'none';
                nim_mahasiswa3.style.display = 'none';
                no_hp3.style.display = 'none';
            } else if (this.value === '2') {
                mahasiswa2.style.display = 'flex';
                nim_mahasiswa2.style.display = 'flex';
                no_hp2.style.display = 'flex';
                mahasiswa3.style.display = 'none';
                nim_mahasiswa3.style.display = 'none';
                no_hp3.style.display = 'none';
            } else if (this.value === '3') {
                mahasiswa2.style.display = 'flex';
                nim_mahasiswa2.style.display = 'flex';
                no_hp2.style.display = 'flex';
                mahasiswa3.style.display = 'flex';
                nim_mahasiswa3.style.display = 'flex';
                no_hp3.style.display = 'flex';
            }
        });
    </script>

    <script>
        // Menambahkan event listener untuk elemen select dengan id 'jenis_surat'
        document.getElementById('jenis_surat').addEventListener('change', function() {

            // Mendapatkan referensi ke elemen-elemen yang ingin diubah tampilannya
            let inputInsentif = document.getElementById('input_insentif');
            let inputRiset = document.getElementById('input_riset');

            inputInsentif.style.display = 'none';
            inputRiset.style.display = 'none';

            // Tampilkan atau sembunyikan input tambahan untuk Surat KKL atau Surat Riset
            if (this.value === '5') {
                inputInsentif.style.display = 'block';
                inputRiset.style.display = 'none';
            } else if (this.value === '6') {
                inputInsentif.style.display = 'none';
                inputRiset.style.display = 'block';
            }
        });


        // Menambahkan event listener untuk elemen select dengan id 'jenis_insentif'
        document.getElementById('jenis_insentif').addEventListener('change', function() {

            // Mendapatkan referensi ke elemen-elemen yang ingin diubah tampilannya
            let penelitian = document.getElementById('penelitian');
            let publikasi = document.getElementById('publikasi');
            let pertemuanIlmiah = document.getElementById('pertemuan_ilmiah');
            let keynoteSpeaker = document.getElementById('keynote_speaker');
            let visiting = document.getElementById('visiting');
            let hki = document.getElementById('hki');
            let teknologi = document.getElementById('teknologi');
            let buku = document.getElementById('buku');
            let model = document.getElementById('model');
            let insentifPublikasi = document.getElementById('insentif_publikasi');

            penelitian.style.display = 'none';
            publikasi.style.display = 'none';
            pertemuanIlmiah.style.display = 'none';
            keynoteSpeaker.style.display = 'none';
            visiting.style.display = 'none';
            hki.style.display = 'none';
            teknologi.style.display = 'none';
            model.style.display = 'none';
            buku.style.display = 'none';
            insentifPublikasi.style.display = 'none';

            // Menampilkan elemen yang dipilih berdasarkan nilai yang dipilih dari dropdown
            if (this.value === 'penelitian') {
                penelitian.style.display = 'block';
            } else if (this.value === 'publikasi') {
                publikasi.style.display = 'block';
            } else if (this.value === 'pertemuan_ilmiah') {
                pertemuanIlmiah.style.display = 'block';
            } else if (this.value === 'keynote_speaker') {
                keynoteSpeaker.style.display = 'block';
            } else if (this.value === 'visiting') {
                visiting.style.display = 'block';
            } else if (this.value === 'hki') {
                hki.style.display = 'block';
            } else if (this.value === 'teknologi') {
                teknologi.style.display = 'block';
            } else if (this.value === 'model') {
                model.style.display = 'block';
            } else if (this.value === 'buku') {
                buku.style.display = 'block';
            } else if (this.value === 'insentif_publikasi') {
                insentifPublikasi.style.display = 'block';
            }
        });
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>

<?php
// Tutup koneksi database
$conn->close();
?>