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
?>

<!doctype html>
<html lang="en">

<head>
    <title>Surat Masuk - Teknoid</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="../logo itbad.png">
    <link href="css/dashboard-style.css" rel="stylesheet">
    <!-- ajax live search -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script type="text/javascript" src="tablesorter/jquery.tablesorter.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                    <h3>Surat Masuk</h3>
                </div>
                <div class="tombol" style="justify-content: flex-end; margin-bottom: 20px;">
                    <div class="search-box">
                        <form method="GET">
                            <input type="text" placeholder="Search..." name="search" id="search">
                        </form>
                    </div>
                </div>
                <div class="tableOverflow">
                    <table id="tablesm" class="tablesorter">
                        <thead>
                            <tr>
                                <th style="min-width: 75px;">No <i class="fas fa-sort"></i></th>
                                <th>Kode Surat <i class="fas fa-sort"></i></th>
                                <th>Asal Surat <i class="fas fa-sort"></i></th>
                                <th>Perihal <i class="fas fa-sort"></i></th>
                                <th>Tanggal Surat <i class="fas fa-sort"></i></th>
                                <th>Status <i class="fas fa-sort"></i></th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = mysqli_connect("localhost", "root", "", "db_teknoid");
                            if ($conn->connect_error) {
                            }

                            // pengaturan baris
                            $start = 0;
                            $rows_per_page = 20;
                            $akses = $_SESSION['akses'];
                            $jabatan = $_SESSION['jabatan'];

                            // Perhatikan penambahan kurung pada kueri SQL berikut
                            $records = mysqli_query($conn, "SELECT sd.id_surat, sd.kode_surat, sd.kd_surat, sd.asal_surat,
                                                                        sd.perihal, sd.diteruskan_ke, sd.status_baca, sd.status_tolak, sd.status_selesai,
                                                                        d.dispo1, d.dispo2, d.dispo3, d.dispo4, d.dispo5 
                                                                        FROM tb_surat_dis sd
                                                                        LEFT JOIN tb_disposisi d ON sd.id_surat = d.id_surat
                                                                        WHERE JSON_CONTAINS(sd.diteruskan_ke, '\"$akses\"') OR sd.diteruskan_ke = '$akses'
                                                                        OR d.dispo1 = '$jabatan' || d.dispo2 = '$jabatan' || d.dispo3 = '$jabatan' || d.dispo4 = '$jabatan' || d.dispo5 = '$jabatan'
                                                                        ORDER BY sd.id_surat ASC");

                            $nr_of_rows = $records->num_rows;

                            // kalkulasi nomor per halaman
                            $pages = ceil($nr_of_rows / $rows_per_page);

                            // start point
                            if (isset($_GET['page-nr'])) {
                                $page = $_GET['page-nr'] - 1;
                                $start = $page * $rows_per_page;
                            }

                            // tabel db surat
                            // Perhatikan penambahan kurung pada kueri SQL berikut
                            $stmt = $conn->prepare("SELECT sd.id_surat, sd.kode_surat, sd.kd_surat, sd.asal_surat,
                                                                sd.tanggal_surat, sd.perihal, sd.diteruskan_ke, sd.status_baca, sd.status_tolak,
                                                                sd.status_selesai, d.dispo1, d.dispo2, d.dispo3, d.dispo4, d.dispo5
                                                                FROM tb_surat_dis sd
                                                                LEFT JOIN tb_disposisi d ON sd.id_surat = d.id_surat
                                                                WHERE JSON_CONTAINS(sd.diteruskan_ke, '\"$akses\"') OR sd.diteruskan_ke = '$akses'
                                                                OR d.dispo1 = '$jabatan' || d.dispo2 = '$jabatan' || d.dispo3 = '$jabatan' || d.dispo4 = '$jabatan' || d.dispo5 = '$jabatan'
                                                                ORDER BY sd.id_surat DESC
                                                                LIMIT ?, ?");
                            $stmt->bind_param("ii", $start, $rows_per_page);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $counter = $start + 1;
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <tr>
                                        <?php
                                        $akses = $_SESSION['akses'];
                                        $jabatan = $_SESSION['jabatan'];

                                        if ($_SESSION['akses'] == 'Rektor') {
                                            if ($row['diteruskan_ke'] == 'Rektor' || $row['dispo1'] == 'Rektor') {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . (isset($row['tanggal_surat']) ? (new DateTime($row['tanggal_surat']))->format('d-m-Y') : '') . "</td>";
                                                echo "<td>";
                                                if ($row['status_baca']) {
                                                    echo 'Sudah Disposisi';
                                                } else {
                                                    echo 'Belum Disposisi';
                                                }

                                                if ($row['status_tolak'] == 1) {
                                                    echo ' - Ditolak';
                                                } else {
                                                    echo '';
                                                }
                                                echo "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Lihat</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'Warek1') {
                                            if (
                                                $row['diteruskan_ke'] == 'Warek1' || $row['dispo2'] == $jabatan ||
                                                $row['dispo3'] == $jabatan || $row['dispo4'] == $jabatan || $row['dispo5'] == $jabatan
                                            ) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>";
                                                if ($row['dispo2'] == $jabatan || $row['dispo3'] == $jabatan || $row['dispo4'] == $jabatan || $row['dispo5'] == $jabatan) {
                                                    echo 'Sudah Disposisi';
                                                } else {
                                                    echo 'Belum Disposisi';
                                                }
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'Warek2') {
                                            if (
                                                $row['diteruskan_ke'] == $akses || strpos($row['diteruskan_ke'], $akses) !== false || $row['dispo2'] == $jabatan ||
                                                $row['dispo3'] == $jabatan || $row['dispo4'] == $jabatan || $row['dispo5'] == $jabatan
                                            ) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>";
                                                if ($row['dispo2'] == $jabatan || $row['dispo3'] == $jabatan || $row['dispo4'] == $jabatan || $row['dispo5'] == $jabatan) {
                                                    echo 'Sudah Disposisi';
                                                } else {
                                                    echo 'Belum Disposisi';
                                                }
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'Warek3') {
                                            if (
                                                $row['diteruskan_ke'] == $akses || strpos($row['diteruskan_ke'], $akses) !== false || $row['dispo2'] == $jabatan ||
                                                $row['dispo3'] == $jabatan || $row['dispo4'] == $jabatan || $row['dispo5'] == $jabatan
                                            ) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>";
                                                if ($row['dispo2'] == $jabatan || $row['dispo3'] == $jabatan || $row['dispo4'] == $jabatan || $row['dispo5'] == $jabatan) {
                                                    echo 'Sudah Disposisi';
                                                } else {
                                                    echo 'Belum Disposisi';
                                                }
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'Admin') {
                                            echo "<td>" . ($row['status_baca'] ? 'Sudah Disposisi' : 'Belum Disposisi') . "</td>";
                                        } elseif ($_SESSION['akses'] == 'DekanFTD') {
                                            if (
                                                strpos($row['diteruskan_ke'], 'DekanFTD') !== false && strpos($row['diteruskan_ke'], 'DekanFEB') !== false || strpos($row['diteruskan_ke'], 'DekanFTD')
                                                || $row['dispo2'] == 'Dekan FTD' || $row['dispo3'] == 'Dekan FTD' || $row['dispo4'] == 'Dekan FTD'
                                                || $row['dispo5'] == 'Dekan FTD' || strpos($row['diteruskan_ke'], 'DekanFTD') !== false
                                            ) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>";
                                                if ($row['dispo2'] == $jabatan || $row['dispo3'] == $jabatan || $row['dispo4'] == $jabatan || $row['dispo5'] == $jabatan) {
                                                    echo 'Sudah Disposisi';
                                                } else {
                                                    echo 'Belum Disposisi';
                                                }
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'DekanFEB') {
                                            if (
                                                strpos($row['diteruskan_ke'], $akses) !== false && strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses
                                                || $row['dispo2'] == $jabatan || $row['dispo3'] == $jabatan || $row['dispo4'] == $jabatan
                                                || $row['dispo5'] == $jabatan
                                            ) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>";
                                                if ($row['dispo2'] == $jabatan || $row['dispo3'] == $jabatan || $row['dispo4'] == $jabatan || $row['dispo5'] == $jabatan) {
                                                    echo 'Sudah Disposisi';
                                                } else {
                                                    echo 'Belum Disposisi';
                                                }
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'direkPasca') {
                                            if (
                                                $row['diteruskan_ke'] == $akses || strpos($row['diteruskan_ke'], 'direkPasca') ||  $row['dispo2'] == 'direkPasca' ||
                                                $row['dispo3'] == 'direkPasca' || $row['dispo4'] == 'direkPasca' || $row['dispo5'] == 'direkPasca'
                                            ) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>";
                                                if ($row['dispo2'] == $jabatan || $row['dispo3'] == $jabatan || $row['dispo4'] == $jabatan || $row['dispo5'] == $jabatan) {
                                                    echo 'Sudah Disposisi';
                                                } else {
                                                    echo 'Belum Disposisi';
                                                }
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'prodi_ti') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'prodi_si') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'prodi_manajemen') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'prodi_akuntansi') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'prodi_dkv') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'prodi_arsitek') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'prodi_keuSyariah') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                            // section unit
                                        } elseif ($_SESSION['akses'] == 'ppik_kmhs') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'PSDOD') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'CHED') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'PSIPP') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'halal_center') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'PKAD') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'keuangan') {
                                            if (
                                                strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses || $row['dispo3'] == $jabatan ||
                                                $row['dispo4'] == $jabatan
                                            ) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'akademik') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'Humas') {
                                            if (
                                                strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses
                                                || $row['dispo1'] == 'Humas'
                                            ) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . (!empty($row['kode_surat']) ? $row['kode_surat'] : $row['kd_surat']) . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>";
                                                if ($row['status_baca']) {
                                                    echo 'Sudah Disposisi';
                                                } else {
                                                    echo 'Belum Disposisi';
                                                }

                                                if ($row['status_tolak'] == 1) {
                                                    echo ' - Ditolak';
                                                } else {
                                                    echo '';
                                                }
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'> 
                                                <button style='padding: 5px; border-radius: 5px; background-color: #1E2287; color: white;'> Disposisi </button></a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'marketing') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'umum') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'it_lab') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'sdm') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'lp3m') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'bpm') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'kui_k') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'upt_perpus') {
                                            if (strpos($row['diteruskan_ke'], $akses) !== false || $row['diteruskan_ke'] == $akses) {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        } elseif ($_SESSION['akses'] == 'pusat_bisnis') {
                                            if ($row['diteruskan_ke'] == $akses || strpos($row['diteruskan_ke'], $akses) !== false) {

                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['kode_surat'] . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['perihal'] . "</td>";
                                                echo "<td>" . $row['tanggal_surat'] . "</td>";
                                                echo "<td>" . ($row['status_selesai'] == TRUE ? 'Sudah Diselesaikan' : 'Belum Diselesaikan') . "</td>";
                                                echo "<td><a href='disposisi.php?id=" . $row['id_surat'] . "'>Disposisi</a></td>";
                                            }
                                        }
                                        ?>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='7'>0 results</td></tr>";
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                if (isset($_GET['page-nr'])) {
                    $id = $_GET['page-nr'];
                } else {
                    $id = 1;
                }
                ?>
                <div id="kontenhalaman" id="<?php echo $id ?>">
                    <!-- efek -->
                    <div class="tekspage">
                        <?php
                        if (!isset($_GET['page-nr'])) {
                            $page = 1;
                        } else {
                            $page = $_GET['page-nr'];
                        }
                        ?>
                        Showing <?php echo $page ?> of <?php echo $pages ?> pages
                    </div>
                    <div class="pagination">
                        <!-- first page  -->
                        <a href="?page-nr=1"><span class="fas fa-angle-double-left"></span></a>
                        <!-- Previous page -->
                        <?php if ($page > 1) : ?>
                            <a href="?page-nr=<?php echo $page - 1 ?>"><span class="fas fa-angle-left"></span></a>
                        <?php endif; ?>
                        <!-- Page numbers -->
                        <div class="pageNumber">
                            <?php
                            // Calculate start and end page numbers to display
                            $startPage = max(1, $page - 2);
                            $endPage = min($pages, $startPage + 4);

                            // Calculate the total number of pages to show
                            $totalPagesToShow = min(5, $pages);

                            // Adjust the start page if less than the maximum number of pages to show
                            if ($pages - $startPage + 1 < $totalPagesToShow) {
                                $startPage = max(1, $pages - $totalPagesToShow + 1);
                            }

                            // Display page numbers
                            for ($counter = $startPage; $counter <= $endPage; $counter++) {
                                echo '<a ' . ($counter === $page ? 'class="active"' : '') . ' href="?page-nr=' . $counter . '">' . $counter . '</a>';
                            }
                            ?>
                        </div>

                        <!-- Next page -->
                        <?php if ($page < $pages) : ?>
                            <a href="?page-nr=<?php echo $page + 1 ?>"><span class="fas fa-angle-right"></span></a>
                        <?php endif; ?>

                        <!-- Last page -->
                        <a href="?page-nr=<?php echo $pages ?>"><span class="fas fa-angle-double-right"></span></a>
                    </div>
                </div>
            </div>
        </div>
        <?php include './footer.php'; ?>
    </div>

    <script type="text/javascript">
        // pencarian
        $(document).ready(function() {
            $("#search").keyup(function() {
                var search = $(this).val();
                $.ajax({
                    url: 'ajax/searchSM.php',
                    method: 'POST',
                    data: {
                        query: search
                    },
                    success: function(response) {
                        $("#tablesm").html(response);
                    }
                });
            });
        });
    </script>

    <script>
        // efek page number //
        let links = document.querySelectorAll('.pageNumber a');
        let id = parseInt("<?php echo $id ?>");
        let pageNumberContainer = document.querySelector('.pageNumber');
        if (!isNaN(id)) {
            links[id - 1].classList.add("active");
        } else {
            console.error("ID tidak valid:", id);
        }
    </script>

    <script>
        // sorting //
        $(document).ready(function() {
            $("#tablesm").tablesorter();
        });
    </script>

    <script src="js/dashboard-js.js"></script>

</body>

</html>