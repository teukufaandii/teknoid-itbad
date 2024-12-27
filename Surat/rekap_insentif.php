<?php
session_start(); 
include __DIR__ . '/../Maintenance/Middleware/index.php';
if (isset($_SESSION['akses']) && ($_SESSION['akses'] == 'lp3m' || $_SESSION['akses'] == 'Admin')) {
?>
    <?php
    include 'koneksi.php';
    include "logout-checker.php";

    // Check if session username is set
    if (!isset($_SESSION['pengguna_type'])) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Access Denied",
                text: "Anda Tidak Berhak Masuk Kehalaman Ini!"
            }).then(function() {
                window.location.href = "../index.php";
            });
        </script>';
        exit;
    }
    ?>

    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cari Data Dosen</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="icon" type="image/x-icon" href="../logo itbad.png">
        <link href="css/dashboard-style.css" rel="stylesheet">
        <!-- ajax live search -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script type="text/javascript" src="tablesorter/jquery.tablesorter.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://kit.fontawesome.com/9e9ad697fd.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
                        <h3>Rekap Surat Insentif</h3>
                        <?php
                        if (isset($_SESSION['akses']) && $_SESSION['akses'] === 'lp3m') {
                            echo '<a href="surat_keluar.php"><button class="back">Kembali</button></a>';
                        }
                        ?>
                    </div>

                    <form id="searchForm" class="form">
                        <div class="inputfield">
                            <label for="jenis_insentif">Pilih Jenis Insentif:</label>
                            <div class="custom_select">
                                <select name="jenis_insentif" id="jenis_insentif">
                                    <option hidden>Pilih Jenis Insentif</option>
                                    <option value="penelitian">Penelitian</option>
                                    <option value="publikasi">Publikasi</option>
                                    <option value="pertemuan_ilmiah">Pertemuan Ilmiah</option>
                                    <option value="keynote_speaker">Keynote Speaker</option>
                                    <option value="visiting">Visiting</option>
                                    <option value="hki">HKI</option>
                                    <option value="teknologi">Teknologi</option>
                                    <option value="buku">Buku</option>
                                    <option value="model">Model</option>
                                    <option value="insentif_publikasi">Insentif Publikasi</option>
                                </select>
                            </div>
                        </div>

                        <div class="tanggal">
                            <div class="inputfield" style="margin:0; gap:20px;">
                                <label for="tanggal_awal">Tanggal Awal Surat</label>
                                <input type="date" id="tanggal_awal" name="tanggal_awal" style="margin-left:8px;">
                            </div>
                            <div class="inputfield" style="margin:0; gap:20px; margin-left: 10px;">
                                <label for="tanggal_akhir">Tanggal Akhir Surat</label>
                                <input type="date" id="tanggal_akhir" name="tanggal_akhir">
                            </div>
                        </div>

                        <button type="submit" class="search">Cari</button>
                        <button type="button" class="ekspor" id="exportBtn">Export to Excel</button>
                    </form>

                    <div id="result" class="inputfield"></div>

                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#searchForm').on('submit', function(event) {
                    event.preventDefault();

                    var jenis_insentif = $('#jenis_insentif').val();
                    var tanggal_awal = $('#tanggal_awal').val();
                    var tanggal_akhir = $('#tanggal_akhir').val();

                    $.ajax({
                        url: 'sql/search_insentif.php',
                        type: 'POST',
                        data: {
                            jenis_insentif: jenis_insentif,
                            tanggal_awal: tanggal_awal,
                            tanggal_akhir: tanggal_akhir
                        },
                        success: function(response) {
                            $('#result').html(response);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Terjadi kesalahan saat mencari data.'
                            });
                        }
                    });
                });

                $('#exportBtn').on('click', function() {
                    var jenis_insentif = $('#jenis_insentif').val();
                    var tanggal_awal = $('#tanggal_awal').val();
                    var tanggal_akhir = $('#tanggal_akhir').val();

                    if (jenis_insentif && tanggal_awal && tanggal_akhir) {
                        window.location.href = 'sql/export_insentif.php?jenis_insentif=' + encodeURIComponent(jenis_insentif) +
                            '&tanggal_awal=' + encodeURIComponent(tanggal_awal) +
                            '&tanggal_akhir=' + encodeURIComponent(tanggal_akhir);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'Silakan pilih jenis insentif dan tentukan rentang tanggal terlebih dahulu.'
                        });
                    }
                });
            });
        </script>
    </body>
    </html>
<?php
} else {
    include "./access-denied.php";
}
?>