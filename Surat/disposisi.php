<!DOCTYPE html>
<html lang="eng">

<?php
session_start(); // Start the session at the beginning of the script
if (isset($_SESSION['akses']) && $_SESSION['akses'] !== 'User') {
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

    $id = $_GET['id'] ?? null;

    // Fetch data from the first table based on the provided ID
    $sql1 = "SELECT sd.kode_surat, sd.kd_surat, sd.nama_lengkap, sd.nim, sd.nama_lengkap2, sd.nim2, sd.nama_lengkap3, sd.nim3, sd.tanggal_surat, sd.tujuan_surat, sd.asal_surat, j.nama_jenis,
                sd.perihal, sd.nomor_surat, sd.no_hp, sd.no_hp2, sd.no_hp3, sd.email, sd.deskripsi, sd.nama_perusahaan, sd.alamat_perusahaan, sd.prodi
         FROM tb_surat_dis sd
         INNER JOIN tb_jenis j ON sd.jenis_surat = j.kd_jenissurat
         WHERE sd.id_surat = ?";

    $stmt1 = $koneksi->prepare($sql1);
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $stmt1->bind_result(
        $kode_surat,
        $kode_surat2,
        $nama_lengkap,
        $nim,
        $nama_lengkap2,
        $nim2,
        $nama_lengkap3,
        $nim3,
        $tanggal_surat,
        $tujuan_surat,
        $asal_surat,
        $jenis_surat,
        $perihal,
        $no_surat,
        $no_telepon,
        $no_telepon2,
        $no_telepon3,
        $suratelektrik,
        $deskripsi,
        $nama_perusahaan,
        $Alamat_Perusahaan,
        $prodi,
    );
    $stmt1->fetch();
    $stmt1->close();

    $sql2 = "SELECT dispo1, dispo2, dispo3, catatan_disposisi, catatan_disposisi2 FROM tb_disposisi WHERE id_surat = ?";
    $stmt2 = $koneksi->prepare($sql2);
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->bind_result($disposisi1, $disposisi2, $disposisi3, $catatan_disposisi, $catatan_disposisi2);
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

    $sql5 = "SELECT nama_jenis FROM tb_jenis WHERE kd_jenissurat = ?";
    $stmt5 = $koneksi->prepare($sql5);
    $stmt5->bind_param("i", $kd_jenis);
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

            .loading-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                justify-content: center;
                align-items: center;
            }

            /* Spinner Animation */
            .spinner {
                border: 8px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top: 8px solid #ffffff;
                width: 60px;
                height: 60px;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
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
                        <h3>Disposisi - <?php echo $_SESSION['jabatan'] ?></h3>
                        <a href="surat_masuk.php"><button class="back">Kembali</button></a>
                    </div>
                    <form class="form">
                        <div class="input-field">
                            <label for="">Kode Surat</label>
                            <input type="text" class="input" name="#" placeholder="" value="<?php echo (!empty($kode_surat) ? $kode_surat : $kode_surat2); ?>" readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Jenis Surat</label>
                            <input type="text" class="input" name="#" placeholder="" value="<?php echo $jenis_surat; ?>" readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Asal Surat</label>
                            <input type="text" class="input" name="#" value="<?php echo $asal_surat; ?>" readonly>
                        </div>

                        <?php if ($_SESSION['akses'] == "Humas" && $jenis_surat == 'Surat KKL' || $_SESSION['akses'] == "Humas" && $jenis_surat == 'Surat Riset') { ?>
                            <div class="input-field">
                                <label for="">Nama Siswa</label>
                                <input type="text" class="input" name="#" value="<?php echo $nama_lengkap; ?>, &nbsp <?php echo $nama_lengkap2; ?>, &nbsp <?php echo $nama_lengkap3; ?> " readonly>
                            </div>
                            
                            <div class="input-field">
                                <label for="">NIM Siswa</label>
                                <input type="text" class="input" name="#" value="<?php echo ($nim != 0) ? $nim : ''; ?>, &nbsp <?php echo ($nim2 != 0) ? $nim2 : ''; ?>, &nbsp <?php echo ($nim3 != 0) ? $nim3 : ''; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Program Studi</label>
                                <input type="text" class="input" name="#" value="<?php echo $prodi; ?> " readonly>
                            </div>
                        <?php } ?>

                        <div class="input-field">
                            <label for="">Perihal*</label>
                            <input type="text" class="input" name="perihal" placeholder="" value="<?php echo $perihal; ?>" readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Nomor Surat</label>
                            <input type="text" class="input" name="#" value="<?php echo $no_surat; ?>" readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Tanggal Surat</label>
                            <input type="text" class="input" name="#" value="<?php echo $tanggal_surat; ?>" readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Tujuan Surat</label>
                            <input type="text" class="input" name="#" placeholder="" value="<?php echo $tujuan_surat; ?>" readonly>
                        </div>

                        <div class="input-field">
                            <label for="">No Telepon*</label>
                            <input type="text" class="input" name="#" placeholder="" value="<?php echo $no_telepon; ?>, &nbsp <?php echo $no_telepon2; ?>, &nbsp <?php echo $no_telepon3; ?> " readonly>
                        </div>


                        <?php if ($_SESSION['akses'] == "Humas" && $jenis_surat == 'Surat KKL' || $_SESSION['akses'] == "Humas" && $jenis_surat == 'Surat Riset') { ?>
                            <?php if ($_SESSION['akses'] == "Humas") { ?>
                                <div class="input-field">
                                    <label for="">Email*</label>
                                    <input type="text" class="input" name="#" placeholder="" value="<?php echo $suratelektrik; ?>" readonly>
                                </div>
                            <?php } ?>


                            <?php if ($_SESSION['akses'] == "Humas" || $_SESSION['akses'] == "Sekretaris") { ?>
                                <div class="input-field">
                                    <label for="">Nama Perusahaan</label>
                                    <input type="text" class="input" name="#" value="<?php echo $nama_perusahaan; ?>" readonly>
                                </div>

                                <div class="input-field">
                                    <label for="">Alamat Perusahaan</label>
                                    <input type="text" class="input" name="#" value="<?php echo $Alamat_Perusahaan; ?>  " readonly>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <div class="input-field">
                            <label for="">Deskripsi Singkat*</label>
                            <input type="text" class="input" name="#" placeholder="" value="<?php echo $deskripsi; ?>" readonly>
                        </div>


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
            // Function to check if the user is on mobile
            function isMobile() {
                return /Mobi|Android/i.test(navigator.userAgent);
            }

            // Function to display file modal or open in a new tab for mobile
            function lihatBerkas(filePath) {
                if (isMobile()) {
                    window.open(filePath, '_blank'); // Open in new tab for mobile
                } else {
                    document.getElementById("berkasFrame").src = filePath;
                    document.getElementById("modalBerkas").style.display = "block";
                }
            }

            // Function to close file modal
            function closeModal() {
                document.getElementById("modalBerkas").style.display = "none";
            }

            // Function to display report modal or open in a new tab for mobile
            function lihatLaporan(filePath) {
                if (isMobile()) {
                    window.open(filePath, '_blank'); // Open in new tab for mobile
                } else {
                    // Close file modal if it's open
                    closeModal();
                    // Set iframe source and display report modal
                    document.getElementById("laporanFrame").src = filePath;
                    document.getElementById("modalLaporan").style.display = "block";
                }
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

<?php
} else {
    include "./access-denied.php";
}
?>