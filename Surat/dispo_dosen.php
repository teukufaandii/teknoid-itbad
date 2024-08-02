<!DOCTYPE html>
<html lang="eng">
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

$id = $_GET['id'] ?? null;

// Fetch data from the first table based on the provided ID
$sql1 = "SELECT sd.jenis_surat, sd.asal_surat, sd.nama_dosen, sd.status_pengusul, sd.NIDN, sd.no_telpon, sd.id_sinta, sd.prodi_pengusul, 
            sd.jenis_insentif, sd.skema_ppmdpek, sd.judul_penelitian_ppm, sd.jenis_publikasi_pi, sd.nama_jurnal_pi, sd.vol_notahun_pi, 
            sd.link_jurnal_pi, sd.skala_ppdpi, sd.usulan_biaya_ppdpi, sd.skala_ppdks, sd.nama_pertemuan_ppdks, sd.nm_kegiatan_vl, 
            sd.jenis_hki, sd.judul_hki, sd.teknologi_tg, sd.deskripsi_tg, sd.jenis_buku, sd.judul_buku, sd.sinopsis_buku, sd.isbn_buku, 
            sd.nama_model_mpdks, sd.deskripsi_mpdks, sd.judul_ipbk, sd.namaPenerbit_dan_waktu_ipbk, sd.link_publikasi_ipbk, sd.ttl_srd, 
            sd.alamat_srd, sd.perihal_srd, sd.email_srd, sd.deskripsi_srd, sd.nama_perusahaan_srd, sd.alamat_perusahaan_srd, 
            sd.tujuan_surat_srd, j.nama_jenis
         FROM tb_srt_dosen sd
         INNER JOIN tb_jenis j ON sd.jenis_surat = j.kd_jenissurat
         WHERE sd.id_srt = ?";


$stmt1 = $koneksi->prepare($sql1);
$stmt1->bind_param("i", $id);
$stmt1->execute();
$stmt1->bind_result(
    $jenis_surat,
    $asal_surat,
    $nama_dosen,
    $status_pengusul,
    $NIDN,
    $no_telpon,
    $id_sinta,
    $prodi_pengusul,
    $jenis_insentif,
    $skema_ppmdpek,
    $judul_penelitian_ppm,
    $jenis_publikasi_pi,
    $nama_jurnal_pi,
    $vol_notahun_pi,
    $link_jurnal_pi,
    $skala_ppdpi,
    $usulan_biaya_ppdpi,
    $skala_ppdks,
    $nama_pertemuan_ppdks,
    $nm_kegiatan_vl,
    $jenis_hki,
    $judul_hki,
    $teknologi_tg,
    $deskripsi_tg,
    $jenis_buku,
    $judul_buku,
    $sinopsis_buku,
    $isbn_buku,
    $nama_model_mpdks,
    $deskripsi_mpdks,
    $judul_ipbk,
    $namaPenerbit_dan_waktu_ipbk,
    $link_publikasi_ipbk,
    $ttl_srd,
    $alamat_srd,
    $perihal_srd,
    $email_srd,
    $deskripsi_srd,
    $nama_perusahaan_srd,
    $alamat_perusahaan_srd,
    $tujuan_surat_srd,
    $nama_jenis,
);

$stmt1->fetch();
$stmt1->close();

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

$sql5 = "SELECT nama_jenis FROM tb_jenis WHERE kd_jenissurat = ?";
$stmt5 = $koneksi->prepare($sql5);
$stmt5->bind_param("i", $id);
$stmt5->execute();
$stmt5->bind_result($jenis_surat);
$stmt5->fetch();
$stmt5->close();

// Check if files exist
$file_berkas_exists = !empty($file_berkas_name);
$file_laporan_exists = !empty($file_laporan_name);
?>

<head>
    <title>Disposisi - Teknoid</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="../logo itbad.png">
    <link href="css/dashboard-style.css" rel="stylesheet">
    <link href="css/disposisi-style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>



    <style>
        /* Style untuk modal-content */
        .modal-content-file {
            background-color: whitesmoke;
            margin: auto;
            height: 85vh;
            width: 80%;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(5px);
            /* Apply blur effect */
        }

        .modal .modal-content-file h2 {
            color: black;
            padding-top: 20px;
        }

        /* Style untuk tombol close */
        .close {
            color: #aaa;
            float: right;
            padding-right: 5vw;
            margin-right: 0px;
            font-size: 35px;
            font-weight: bold;
            color: red;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Style untuk iframe di dalam modal tampilan laporan */
        iframe {
            border: 1px solid black;
        }

        #berkasFrame,
        #laporanFrame {
            width: 90%;
            height: 60vh;
            margin: auto;
        }

        @media (max-width: 600px) {
            .modal-content-file {
                width: 90%;
                height: 70vh;
            }

            #berkasFrame,
            #laporanFrame {
                width: 90%;
                height: 60vh;
                margin: auto;
            }

            .close {
                font-size: 25px;
                margin-right: 10px;
            }
        }
    </style>

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
                    <h3>Disposisi Insentif - <?php echo $jenis_insentif; ?> <?php echo $_SESSION['jabatan'] ?></h3>
                    <a href="surat_keluar_nondis.php"><button class="back">Kembali</button></a>
                </div>
                <form class="form">
                    <div class="input-field">
                        <label for="">Nama Pengusul</label></label>
                        <input type="text" class="input" name="#" value="<?php echo $nama_dosen; ?> " readonly>
                    </div>

                    <div class="input-field">
                        <label for="">Status Pengusul</label></label>
                        <input type="text" class="input" name="#" value="<?php echo $status_pengusul; ?> " readonly>
                    </div>

                    <div class="input-field">
                        <label for="">NIDN</label></label>
                        <input type="text" class="input" name="#" value="<?php echo $NIDN; ?> " readonly>
                    </div>

                    <div class="input-field">
                        <label for="">ID Sinta</label></label>
                        <input type="text" class="input" name="#" value="<?php echo $id_sinta; ?> " readonly>
                    </div>

                    <div class="input-field">
                        <label for="">NO Telpon/HP</label></label>
                        <input type="text" class="input" name="#" value="<?php echo $no_telpon; ?> " readonly>
                    </div>

                    <div class="input-field">
                        <label for="">Program Studi Pengusul</label></label>
                        <input type="text" class="input" name="#" value="<?php echo $prodi_pengusul; ?> " readonly>
                    </div>

                    <div class="input-field">
                        <label for="">Jenis Insentif</label></label>
                        <input type="text" class="input" name="#" value="<?php echo $jenis_insentif; ?> " readonly>
                    </div>

                    <?php if ($jenis_insentif= 'penelitian') { ?>
                        <div class="input-field">
                            <label for="">Judul Penelitian/Pengabdian Masyarakat</label>
                            <input type="text" class="input" name="#" value="<?php echo $judul_penelitian_ppm; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Skema</label>
                            <input type="text" class="input" name="#" value="<?php echo $skema_ppmdpek; ?> " readonly>
                        </div>

                    <?php } elseif ($jenis_insentif= 'publikasi') { ?>
                        <div class="input-field">
                            <label for="">Jenis Publikasi/Jurnal</label>
                            <input type="text" class="input" name="#" value="<?php echo $jenis_publikasi_pi; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Judul Publikasi/Jurnal</label>
                            <input type="text" class="input" name="#" value="<?php echo $judul_publikasi_pi; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Nama Jurnal/Koran/Majalah/Penerbit</label>
                            <input type="text" class="input" name="#" value="<?php echo $nama_jurnal_pi; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Vol. No. Tahun. ISSN-Edisi-Halaman</label>
                            <input type="text" class="input" name="#" value="<?php echo $vol_notahun_pi; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Tautan/Link jurnal atau berkas pendukung (wajib untuk artikel pada jurnal ilmiah) </label>
                            <input type="text" class="input" name="#" value="<?php echo $link_jurnal_pi; ?> " readonly>
                        </div>

                    <?php } elseif ($jenis_insentif= 'pertemuan_ilmiah') { ?>

                        <div class="input-field">
                            <label for="">Skala</label>
                            <input type="text" class="input" name="#" value="<?php echo $skala_ppdpi; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Nama Pertemuan</label>
                            <input type="text" class="input" name="#" value="<?php echo $nama_pertemuan_ppdpi; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Usulan Biaya</label>
                            <input type="text" class="input" name="#" value="<?php echo $usulan_biaya_ppdpi; ?> " readonly>
                        </div>


                    <?php } elseif ($jenis_insentif= 'keynote_speaker') { ?>

                        <div class="input-field">
                            <label for="">Skala</label>
                            <input type="text" class="input" name="#" value="<?php echo $skala_ppdks; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Nama Pertemuan Ilmiah</label>
                            <input type="text" class="input" name="#" value="<?php echo $nama_pertemuan_ppdks; ?> " readonly>
                        </div>

                    <?php } elseif ($jenis_insentif='visiting_lecturer') { ?>

                        <div class="input-field">
                            <label for="">Nama Kegiatan dan Lembaga tujuan </label>
                            <input type="text" class="input" name="#" value="<?php echo $nam; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Waktu Pelaksanaan</label>
                            <input type="text" class="input" name="#" value="<?php echo $waktu_pelaksanaan_vl; ?> " readonly>
                        </div>


                    <?php } elseif ($jenis_insentif= 'hki') { ?>
                        <div class="input-field">
                            <label for="">Jenis Kekayaan Intelektual</label>
                            <input type="text" class="input" name="#" value="<?php echo $jenis_hki; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Judul Kekayaan Intelektual</label>
                            <input type="text" class="input" name="#" value="<?php echo $judul_hki; ?> " readonly>
                        </div>

                    <?php } elseif ($jenis_insentif= 'teknologi') { ?>

                        <div class="input-field">
                            <label for="">Tekonologi tepat guna yang diusulkan</label>
                            <input type="text" class="input" name="#" value="<?php echo $teknologi_tg; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Deskripsi tekonologi tepat guna yang diusulkan</label>
                            <input type="text" class="input" name="#" value="<?php echo $deskripsi_tg; ?> " readonly>
                        </div>

                    <?php } elseif ($jenis_insentif= 'model') { ?>

                        <div class="input-field">
                            <label for="">Nama Model</label>
                            <input type="text" class="input" name="#" value="<?php echo $nama_model_mpdks; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Deskripsi Model</label>
                            <input type="text" class="input" name="#" value="<?php echo $deskripsi_mpdks; ?> " readonly>
                        </div>

                    <?php } elseif ($jenis_insentif= 'buku') { ?>

                        <div class="input-field">
                            <label for="">Jenis Buku</label>
                            <input type="text" class="input" name="#" value="<?php echo $jenis_buku; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Judul Buku</label>
                            <input type="text" class="input" name="#" value="<?php echo $judul_buku; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Sinopsis</label>
                            <input type="text" class="input" name="#" value="<?php echo $sinopsis_buku; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">ISBN/Jumlah halaman/Penerbit</label>
                            <input type="text" class="input" name="#" value="<?php echo $isbn_buku; ?> " readonly>
                        </div>

                    <?php } elseif ($jenis_insentif= 'insentif_publikasi') { ?>

                        <div class="input-field">
                            <label for="">Judul Publikasi</label>
                            <input type="text" class="input" name="#" value="<?php echo $judul_publikasi_pi; ?> " readonly>
                        </div>  

                        <div class="input-field">
                            <label for="">Nama Penerbit dan Waktu terbit</label>
                            <input type="text" class="input" name="#" value="<?php echo $namaPenerbit_dan_waktu_ipbk; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for=""> Tautan Publikasi</label></label>
                            <input type="text" class="input" name="#" value="<?php echo $link_publikasi_ipbk; ?> " readonly>
                        </div>

                    <?php } ?>

                    <div class="input-field">
                        <label> </label>
                        <div class="input" style="color: black; text-align: center; background-color: rgba(0, 0, 0, 0); border: none">
                            <div class="lihat">
                                <?php if ($file_berkas_exists) : ?>
                                    <?php
                                    // Path untuk berkas
                                    $file_berkas_path = "uploads/berkas/" . $file_berkas_name;
                                    ?>
                                    <button type="button" onclick="lihatBerkas('<?php echo $file_berkas_path; ?>')">Lihat Berkas</button>
                                <?php else : ?>
                                    <p>Tidak ada berkas yang tersedia.</p>
                                <?php endif; ?>


                                <!--button laporan-->
                                <?php if ($file_laporan_exists) : ?>
                                    <?php
                                    // Path untuk laporan
                                    $file_laporan_path = "uploads/laporan/" . $file_laporan_name;
                                    ?>
                                    <button type="button" onclick="lihatLaporan('<?php echo $file_laporan_path; ?>')">Lihat Laporan</button>
                                <?php else : ?>
                                    <p>Tidak ada laporan yang tersedia.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Modal untuk tampilan berkas -->
                            <div id="modalBerkas" class="modal">
                                <span class="close" onclick="closeModal()">&times;</span>
                                <div class="modal-content-file">
                                    <h2> PREVIEW BERKAS</h2>
                                    <iframe id="berkasFrame" frameborder="0"></iframe>
                                </div>
                            </div>

                            <!-- Modal untuk tampilan laporan -->
                            <div id="modalLaporan" class="modal" <?php if (!$file_laporan_exists) echo 'style="display: none;"'; ?>>
                                <span class="close" onclick="closeModalLaporan()">&times;</span>
                                <div class="modal-content-file">
                                    <h2> PREVIEW LAPORAN </h2>
                                    <iframe id="laporanFrame" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- form disposisi bawah -->
                    <?php include "disposisiBawah.php" ?>
                </form>
            </div>
        </div>
        <?php include './footer.php'; ?>
    </div>
    <script>
        // Get the modal
        var accMdl = document.getElementById("accModal");

        // Get the button that opens the modal
        var accBtn = document.getElementById("accModalBtn");

        // When the user clicks the button, toggle the modal
        accBtn.onclick = function() {
            accMdl.style.display === "block" ? closeModalAcc() : openModalAcc();
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == accMdl) {
                closeModalAcc();
            }
        }

        // Function to open modal
        function openModalAcc() {
            accMdl.style.display = "block";
        }

        // Function to close modal
        function closeModalAcc() {
            accMdl.style.display = "none";
        }
    </script>
    <script src="js/dashboard-js.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <!-- Modal script -->

    <script>
        // Function to display file modal and set iframe source for files
        function lihatBerkas(filePath) {
            document.getElementById("berkasFrame").src = filePath;
            document.getElementById("modalBerkas").style.display = "block";
        }

        // Function to close file modal
        function closeModal() {
            document.getElementById("modalBerkas").style.display = "none";
        }

        // Function to display report modal and set iframe source for reports
        function lihatLaporan(filePath) {
            // Close file modal if it's open
            closeModal();
            // Set iframe source and display report modal
            document.getElementById("laporanFrame").src = filePath;
            document.getElementById("modalLaporan").style.display = "block";
        }

        // Function to close report modal
        function closeModalLaporan() {
            document.getElementById("modalLaporan").style.display = "none";
        }
    </script>

    <!-- Close modal script -->
    <script>
        // Function to close modals when clicking outside
        window.onclick = function(event) {
            var modalBerkas = document.getElementById("modalBerkas");
            var modalLaporan = document.getElementById("modalLaporan");
            if (event.target == modalBerkas) {
                modalBerkas.style.display = "none";
            }
            if (event.target == modalLaporan) {
                modalLaporan.style.display = "none";
            }
        }
    </script>

    <script>
        var dt = new Date();
        document.getElementById("tanggalwaktu").innerHTML = (("0" + dt.getDate()).slice(-2)) + "/" + (("0" + (dt.getMonth() + 1)).slice(-2)) + "/" + (dt.getFullYear());
    </script>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>

</body>

</html>