<?php
session_start(); // Start the session at the beginning of the script
if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Admin') { // Check if $_SESSION['akses'] is set and equals 'Humas'
?>


    <?php
    include 'koneksi.php';
    include "logout-checker.php";
    // Periksa apakah session username telah diatur
    if (!isset($_SESSION['pengguna_type'])) {
        echo '<script language="javascript" type="text/javascript">
    alert("Anda Tidak Berhak Masuk Kehalaman Ini!");</script>';
        echo "<meta http-equiv='refresh' content='0; url=../index.php'>";
        exit;
    }
    ?>

    <!doctype html>
    <html lang="en">

    <head>
        <title>Tambah Akun - Teknoid</title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/x-icon" href="../logo itbad.png">
        <link href="css/dashboard-style.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
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
                        <h3>Tambah Akun</h3>
                        <a href="pengaturan_akun"><button class="back">Kembali</button></a>
                    </div>
                    <form class="form" method="post" action="add_userProcess.php">

                        <div class="inputfield">
                            <label>NIM/NIDN/NIP</label>
                            <input type="text" class="input" name="noinduk" required />
                        </div>

                        <div class="inputfield">
                            <label>Nama Pengguna</label>
                            <input type="text" class="input" name="nama_lengkap" id="nama_lengkap" required />
                        </div>

                        <div class="inputfield">
                            <label>Jabatan</label>
                            <div class="custom_select">
                                <select name="jabatan" id="jabatan" required>
                                    <option hidden disabled selected value="">Pilih Jabatan</option>
                                    <option>Super Admin</option>
                                    <option>Rektor</option>
                                    <option>Warek 1</option>
                                    <option>Warek 2</option>
                                    <option>Warek 3</option>
                                    <option>Sekertaris</option>
                                    <option>Direktur Pasca Sarjana</option>
                                    <option>Dekan FTD</option>
                                    <option>Dekan FEB</option>
                                    <option>Prodi S2 Keuangan Syariah</option>
                                    <option>Prodi S1 Sistem Informasi</option>
                                    <option>Prodi S1 Teknologi Informasi</option>
                                    <option>Prodi S1 Desain Komunikasi Visual</option>
                                    <option>Prodi S1 Arsitektur</option>
                                    <option>Prodi S1 Manajemen</option>
                                    <option>Prodi S1 Akuntansi</option>
                                    <option>Prodi D3 Akuntansi</option>
                                    <option>Prodi D3 Keuangan dan Perbankan</option>
                                    <option>Unit Keuangan</option>
                                    <option>Unit Umum</option>
                                    <option>Unit Marketing</option>
                                    <option>Unit Akademik</option>
                                    <option>Unit Perpustakaan</option>
                                    <option>Unit LP3M</option>
                                    <option>Unit BPM</option>
                                    <option>Unit Humas</option>
                                    <option>Unit KUI dan Kerjasama</option>
                                    <option>Unit PPIK dan Kemahasiswaan</option>
                                    <option>Unit IT & Laboratorium</option>
                                    <option>Unit Pusat Bisnis</option>
                                    <option>Unit Pusat Studi</option>
                                    <option>Unit Kegiatan mahasiswa</option>
                                    <option>Dosen</option>
                                    <option>Karyawan</option>
                                    <option>Mahasiswa</option>
                                </select>
                            </div>
                        </div>

                        <div class="inputfield">
                            <label>Hak Akses</label>
                            <div class="custom_select">
                                <select name="akses" id="akses" required>
                                    <option hidden disabled selected value="">Pilih Akses</option>
                                    <option>Admin</option>
                                    <option>Rektor</option>
                                    <option>Warek1</option>
                                    <option>Warek2</option>
                                    <option>Warek3</option>
                                    <option value="sekretaris">Sekretaris</option>
                                    <option>Direktur</option>
                                    <option value="DekanFTD">Dekan FTD</option>
                                    <option value="DekanFEB">Dekan FEB</option>
                                    <!---------unit----------->
                                    <option value="keuangan">Unit Keuangan</option>
                                    <option value="umum">Unit Umum</option>
                                    <option value="marketing">Unit Marketing</option>
                                    <option value="akademik">Unit Akademik</option>
                                    <option value="upt_perpus">Unit Perpustakaan</option>
                                    <option value="sdm">Unit Kepegawaian</option>
                                    <option value="lp3m">Unit LP3M</option>
                                    <option value="bpm">Unit BPM</option>
                                    <option value="Humas">Unit Humas</option>
                                    <option value="kui_k">Unit KUI dan Kerjasama</option>
                                    <option value="kmhs">Unit PPIK dan Kemahasiswaan</option>
                                    <option value="it_lab">Unit IT & Laboratorium</option>
                                    <option value="pusat_bisnis">Pusat Bisnis</option>
                                    <!----------prodi----------->
                                    <option value="prodi_ti">Prodi TI</option>
                                    <option value="prodi_si">Prodi SI</option>
                                    <option value="prodi_dkv">Prodi DKV</option>
                                    <option value="prodi_arsitek">Prodi Arsitek</option>
                                    <option value="prodi_manajemen">Prodi Manajemen</option>
                                    <option value="prodi_akuntansi">Prodi Akuntansi</option>
                                    <option value="prodi_keuSyariah">Prodi Keuangan Syariah</option>
                                    <!----------Mahasiswa & Dosen----------->
                                    <option value="User">User</option>
                                </select>
                            </div>
                        </div>

                        <div class="inputfield">
                            <label>Password</label>
                            <input type="text" name="password" class="input" id="password" required>
                        </div>

                        <div class="inputfield">
                            <label>Email</label>
                            <input type="email" class="input" name="email">
                        </div>

                        <div class="inputfield">
                            <label>Nomor telepon</label>
                            <input type="number" class="input" name="no_telepon" id="no_telepon">
                        </div>

                        <div>
                            <div class="floatFiller">ff</div>
                            <button type="submit" value="simpan" class="btn">Simpan</button>
                        </div>

                    </form>
                    <form class="form" method="post" action="upload_excel.php" enctype="multipart/form-data">
                        <div class="inputfield">
                            <label>Upload Excel File</label>
                            <input type="file" name="file" accept=".xlsx, .xls" required />
                        </div>
                        <button type="submit" value="upload" class="btn">Upload & Add Users</button>
                    </form>

                </div>
            </div>
            <?php include './footer.php'; ?>
        </div>
        <script src="js/dashboard-js.js"></script>
    </body>

    </html>

<?php
} else {
    include "./access-denied.php";
}
?>