<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['pengguna_type'])) {
    echo '<script language="javascript" type="text/javascript">
    alert("Anda Tidak Berhak Masuk Kehalaman Ini!");</script>';
    echo "<meta http-equiv='refresh' content='0; url=../index.php'>";
    exit;
}

// Check if ID is provided in the URL
$id = $_GET['id'] ?? null;

// Fetch data from the first table based on the provided ID
$sql1 = "SELECT kode_surat, kd_surat, tujuan_surat, perihal FROM tb_surat_dis WHERE id_surat = ?";
$stmt1 = $koneksi->prepare($sql1);
$stmt1->bind_param("i", $id);
$stmt1->execute();
$stmt1->bind_result($kode_surat, $kode_surat2, $tujuan_surat, $perihal);
$stmt1->fetch();
$stmt1->close();

// Fetch data from the second table based on the provided ID
$sql2 = "SELECT dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10,
        catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
        tanggal_disposisi1, tanggal_disposisi2, tanggal_disposisi3, tanggal_disposisi4, tanggal_disposisi5, tanggal_disposisi6, tanggal_disposisi7, tanggal_disposisi8, tanggal_disposisi9, tanggal_disposisi10
        FROM tb_disposisi WHERE id_surat = ?";
$stmt2 = $koneksi->prepare($sql2);
$stmt2->bind_param("i", $id);
$stmt2->execute();
$stmt2->bind_result(
    $disposisi1,
    $disposisi2,
    $disposisi3,
    $disposisi4,
    $disposisi5,
    $disposisi6,
    $disposisi7,
    $disposisi8,
    $disposisi9,
    $disposisi10,
    $catatan_disposisi1,
    $catatan_disposisi2,
    $catatan_disposisi3,
    $catatan_disposisi4,
    $catatan_disposisi5,
    $catatan_disposisi6,
    $catatan_disposisi7,
    $catatan_disposisi8,
    $catatan_disposisi9,
    $catatan_disposisi10,
    $tanggal_disposisi1,
    $tanggal_disposisi2,
    $tanggal_disposisi3,
    $tanggal_disposisi4,
    $tanggal_disposisi5,
    $tanggal_disposisi6,
    $tanggal_disposisi7,
    $tanggal_disposisi8,
    $tanggal_disposisi9,
    $tanggal_disposisi10
);
$stmt2->fetch();
$stmt2->close();

$sql3 = "SELECT nama_berkas FROM file_berkas WHERE id_surat = ?";
$stmt3 = $koneksi->prepare($sql3);
$stmt3->bind_param("i", $id);
$stmt3->execute();
$stmt3->bind_result($file_berkas_name);
$stmt3->fetch();
$stmt3->close();

$sql4 = "SELECT nama_laporan FROM file_laporan WHERE id_surat = ?";
$stmt4 = $koneksi->prepare($sql4);
$stmt4->bind_param("i", $id);
$stmt4->execute();
$stmt4->bind_result($file_laporan_name);
$stmt4->fetch();
$stmt4->close();

// Check if files exist
$file_berkas_exists = !empty($file_berkas_name);
$file_laporan_exists = !empty($file_laporan_name);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Dashboard-Teknoid</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="../tes/logo itbad.png">
    <link href="css/tracking-surat.css" rel="stylesheet">
    <link href="css/dashboard-style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="icon" href="../logo itbad.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>

<body>
    <!-- sidenav -->
    <?php include "sidenav.php" ?>

    <!-- content -->
    <div class="content" id="Content">

        <?php include "topnav.php" ?>

        <div class="mainContent" id="mainContent">
            <div class="contentBox">
                <div class="pageInfo">
                    <div class="jdl1">
                        <h3>Tracking Surat</h3>
                        <button class="back" onclick="goBack()">Kembali</button>
                    </div>
                    <section class="contact">
                        <div class="">
                            <div class="form">
                                <form action="" class="berkasForm" method="post">
                                    <div class="input-field">
                                        <label>Kode Surat</label>
                                        <input type="text" name="kode-surat" value="<?php echo (!empty($kode_surat) ? $kode_surat : $kode_surat2); ?>" class="input" readonly />
                                    </div>
                                    <div class="input-field">
                                        <label>Tujuan Surat</label>
                                        <input type="text" name="tujuan-surat" value="<?php echo $tujuan_surat; ?>" class="input" readonly />
                                    </div>
                                    <div class="input-field">
                                        <label>Perihal</label>
                                        <input type="text" name="perihal" value="<?php echo $perihal; ?>" class="input" readonly />
                                    </div>
                                </form>
                            </div>
                            <div class="file">
                                <div class="berkas">
                                    <?php if ($file_berkas_exists) : ?>
                                        <?php
                                        // Path untuk berkas
                                        $file_berkas_path = "uploads/berkas/" . $file_berkas_name;
                                        ?>
                                        <button onclick="downloadFile('<?php echo $file_berkas_path; ?>')" style="border-radius: 5px">Lihat Berkas</button>
                                    <?php else : ?>
                                        <p>Tidak ada berkas yang tersedia.</p>
                                    <?php endif; ?>
                                </div>
                                <div class="laporan">
                                    <?php if ($file_laporan_exists) : ?>
                                        <?php
                                        // Path untuk laporan
                                        $file_laporan_path = "uploads/laporan/" . $file_laporan_name;
                                        ?>
                                        <button onclick="downloadFile('<?php echo $file_laporan_path; ?>')" style="border-radius: 5px">Lihat Laporan</button>
                                    <?php else : ?>
                                        <p>Tidak ada laporan yang tersedia.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="timeline">
                            <div id="app" class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="swiper-container">
                                            <div class="swiper-wrapper">
                                                <div class="swiper-slide">
                                                    <div class="status">
                                                        <span>Disposisi 1: <?php echo !empty($disposisi1) ? $disposisi1 : 'Belum Disposisi'; ?></span>
                                                    </div>
                                                    <div class="timestamp">
                                                        <span class="date"><?php echo !empty($tanggal_disposisi1) ? $tanggal_disposisi1 : 'DD/MM/YYYY'; ?></span>
                                                    </div>
                                                    <div class="btn-catatan">
                                                        <button type="button" onclick="cekCatatan()" style="cursor: pointer;">Cek Catatan</button>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide">
                                                    <div class="status">
                                                        <span>Disposisi 2: <?php echo !empty($disposisi2) ? $disposisi2 : 'Belum Disposisi'; ?></span>
                                                    </div>
                                                    <div class="timestamp">
                                                        <span class="date"><?php echo !empty($tanggal_disposisi2) ? $tanggal_disposisi2 : 'DD/MM/YYYY'; ?></span>
                                                    </div>
                                                    <div class="btn-catatan">
                                                        <button type="button" onclick="cekCatatan2()" style="cursor: pointer;">Cek Catatan</button>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide">
                                                    <div class="status">
                                                        <span>Disposisi 3: <?php echo !empty($disposisi3) ? $disposisi3 : 'Belum Disposisi'; ?></span>
                                                    </div>
                                                    <div class="timestamp">
                                                        <span class="date"><?php echo !empty($tanggal_disposisi3) ? $tanggal_disposisi3 : 'DD/MM/YYYY'; ?></span>
                                                    </div>
                                                    <div class="btn-catatan">
                                                        <button type="button" onclick="cekCatatan3()" style="cursor: pointer;">Cek Catatan</button>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide">
                                                    <div class="status">
                                                        <span>Disposisi 4: <?php echo !empty($disposisi4) ? $disposisi4 : 'Belum Disposisi'; ?></span>
                                                    </div>
                                                    <div class="timestamp">
                                                        <span class="date"><?php echo !empty($tanggal_disposisi4) ? $tanggal_disposisi4 : 'DD/MM/YYYY'; ?></span>
                                                    </div>
                                                    <div class="btn-catatan">
                                                        <button type="button" onclick="cekCatatan4()" style="cursor: pointer;">Cek Catatan</button>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide">
                                                    <div class="status">
                                                        <span>Disposisi 5: <?php echo !empty($disposisi5) ? $disposisi5 : 'Belum Disposisi'; ?></span>
                                                    </div>
                                                    <div class="timestamp">
                                                        <span class="date"><?php echo !empty($tanggal_disposisi5) ? $tanggal_disposisi5 : 'DD/MM/YYYY'; ?></span>
                                                    </div>
                                                    <div class="btn-catatan">
                                                        <button type="button" onclick="cekCatatan5()" style="cursor: pointer;">Cek Catatan</button>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide">
                                                    <div class="status">
                                                        <span>Disposisi 6: <?php echo !empty($disposisi6) ? $disposisi6 : 'Belum Disposisi'; ?></span>
                                                    </div>
                                                    <div class="timestamp">
                                                        <span class="date"><?php echo !empty($tanggal_disposisi6) ? $tanggal_disposisi6 : 'DD/MM/YYYY'; ?></span>
                                                    </div>
                                                    <div class="btn-catatan">
                                                        <button type="button" onclick="cekCatatan6()" style="cursor: pointer;">Cek Catatan</button>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide">
                                                    <div class="status">
                                                        <span>Disposisi 7: <?php echo !empty($disposisi7) ? $disposisi7 : 'Belum Disposisi'; ?></span>
                                                    </div>
                                                    <div class="timestamp">
                                                        <span class="date"><?php echo !empty($tanggal_disposisi7) ? $tanggal_disposisi7 : 'DD/MM/YYYY'; ?></span>
                                                    </div>
                                                    <div class="btn-catatan">
                                                        <button type="button" onclick="cekCatatan7()" style="cursor: pointer;">Cek Catatan</button>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide">
                                                    <div class="status">
                                                        <span>Disposisi 8: <?php echo !empty($disposisi8) ? $disposisi8 : 'Belum Disposisi'; ?></span>
                                                    </div>
                                                    <div class="timestamp">
                                                        <span class="date"><?php echo !empty($tanggal_disposisi8) ? $tanggal_disposisi8 : 'DD/MM/YYYY'; ?></span>
                                                    </div>
                                                    <div class="btn-catatan">
                                                        <button type="button" onclick="cekCatatan8()" style="cursor: pointer;">Cek Catatan</button>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide">
                                                    <div class="status">
                                                        <span>Disposisi 9: <?php echo !empty($disposisi9) ? $disposisi9 : 'Belum Disposisi'; ?></span>
                                                    </div>
                                                    <div class="timestamp">
                                                        <span class="date"><?php echo !empty($tanggal_disposisi9) ? $tanggal_disposisi9 : 'DD/MM/YYYY'; ?></span>
                                                    </div>
                                                    <div class="btn-catatan">
                                                        <button type="button" onclick="cekCatatan9()" style="cursor: pointer;">Cek Catatan</button>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide">
                                                    <div class="status">
                                                        <span>Disposisi 10: <?php echo !empty($disposisi10) ? $disposisi10 : 'Belum Disposisi'; ?></span>
                                                    </div>
                                                    <div class="timestamp">
                                                        <span class="date"><?php echo !empty($tanggal_disposisi10) ? $tanggal_disposisi10 : 'DD/MM/YYYY'; ?></span>
                                                    </div>  
                                                    <div class="btn-catatan">
                                                        <button type="button" onclick="cekCatatan10()" style="cursor: pointer;">Cek Catatan</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>


    <div id="previewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePreview()">&times;</span>
            <iframe id="previewViewer" src="" width="100%" height="500px"></iframe>
            <button id="downloadBtn">Unduh</button>
        </div>
    </div>
    <div class="footer">
        &copy;<span id="year"> </span><span> Copyright ©2024 TeknoGenius</span></div>
    </div>
    <script src="js/dashboard-js.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        function adjustFormWidth(sidenavWidth) {
            var formContainer = document.getElementById("formContainer");
            formContainer.style.width = "calc(100% - " + sidenavWidth + ")";
        }

        function goBack() {
            window.history.back();
        }

        function downloadFile(filePath) {
            window.location.href = filePath;
        }

        // CEK CATATAN DISPOSISI 1
        let catatan = '<?php echo !empty($catatan_disposisi1) ? $catatan_disposisi1 : "Tidak ada catatan"; ?>';
        function cekCatatan() {
            swal({
                title: 'Catatan Disposisi 1 : ' + catatan,
                showCancelButton: false,
                confirmButtonText: 'Kembali'
            });
        }

        // CEK CATATAN DISPOSISI 2
        let catatan2 = '<?php echo !empty($catatan_disposisi2) ? $catatan_disposisi2 : "Tidak ada catatan"; ?>';
        function cekCatatan2() {
            swal({
                title: 'Catatan Disposisi 2 : ' + catatan2,
                showCancelButton: false,
                confirmButtonText: 'Kembali'
            });
        }

        // CEK CATATAN DISPOSISI 3
        let catatan3 = '<?php echo !empty($catatan_disposisi3) ? $catatan_disposisi3 : "Tidak ada catatan"; ?>';
        function cekCatatan3() {
            swal({
                title: 'Catatan Disposisi 3 : ' + catatan3,
                showCancelButton: false,
                confirmButtonText: 'Kembali'
            });
        }

        // CEK CATATAN DISPOSISI 4
        let catatan4 = '<?php echo !empty($catatan_disposisi4) ? $catatan_disposisi4 : "Tidak ada catatan"; ?>';
        function cekCatatan4() {
            swal({
                title: 'Catatan Disposisi 4 : ' + catatan4,
                showCancelButton: false,
                confirmButtonText: 'Kembali'
            });
        }

        // CEK CATATAN DISPOSISI 5
        let catatan5 = '<?php echo !empty($catatan_disposisi5) ? $catatan_disposisi5 : "Tidak ada catatan"; ?>';
        function cekCatatan5() {
            swal({
                title: 'Catatan Disposisi 5 : ' + catatan5,
                showCancelButton: false,
                confirmButtonText: 'Kembali'
            });
        }

        // CEK CATATAN DISPOSISI 6
        let catatan6 = '<?php echo !empty($catatan_disposisi6) ? $catatan_disposisi6 : "Tidak ada catatan"; ?>';
        function cekCatatan6() {
            swal({
                title: 'Catatan Disposisi 6 : ' + catatan6,
                showCancelButton: false,
                confirmButtonText: 'Kembali'
            });
        }

        // CEK CATATAN DISPOSISI 7
        let catatan7 = '<?php echo !empty($catatan_disposisi7) ? $catatan_disposisi7 : "Tidak ada catatan"; ?>';
        function cekCatatan7() {
            swal({
                title: 'Catatan Disposisi 7 : ' + catatan7,
                showCancelButton: false,
                confirmButtonText: 'Kembali'
            });
        }

        // CEK CATATAN DISPOSISI 8
        let catatan8 = '<?php echo !empty($catatan_disposisi8) ? $catatan_disposisi8 : "Tidak ada catatan"; ?>';
        function cekCatatan8() {
            swal({
                title: 'Catatan Disposisi 8 : ' + catatan8,
                showCancelButton: false,
                confirmButtonText: 'Kembali'
            });
        }

        // CEK CATATAN DISPOSISI 9
        let catatan9 = '<?php echo !empty($catatan_disposisi9) ? $catatan_disposisi9 : "Tidak ada catatan"; ?>';
        function cekCatatan9() {
            swal({
                title: 'Catatan Disposisi 9 : ' + catatan9,
                showCancelButton: false,
                confirmButtonText: 'Kembali'
            });
        }

        // CEK CATATAN DISPOSISI 10
        let catatan10 = '<?php echo !empty($catatan_disposisi10) ? $catatan_disposisi10 : "Tidak ada catatan"; ?>';
        function cekCatatan10() {
            swal({
                title: 'Catatan Disposisi 10 : ' + catatan10,
                showCancelButton: false,
                confirmButtonText: 'Kembali'
            });
        }

    </script>

</body>

</html>