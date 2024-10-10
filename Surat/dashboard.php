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
    <title>Dashboard - Teknoid</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="../logo itbad.png">
    <link href="css/dashboard-style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://kit.fontawesome.com/9e9ad697fd.js" crossorigin="anonymous"></script>
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
                    <h3>Dashboard</h3>
                </div>
                <div class="dashboard-btn">
                    <?php
                    // Periksa apakah session akses adalah selain user
                    if ($_SESSION['akses'] != 'User') {
                        // Jika bukan user, tampilkan tombol "Belum Ditanggapi"
                        echo '<button class="btn1" onclick="toggleNotifications()">Belum Ditanggapi 
                                <i class="fa fa-exclamation dash-icon"></i>
                                <div class="xs"></div>
                                <span class="badge" id="notificationBadge" style="color: grey; padding: 2px; border-radius: 15px;"></span>
                              </button>';
                    }
                    ?>

                    <!-- BATAS SUCI -->
                    <?php
                    $akses = $_SESSION['akses'];
                    $jabatan = $_SESSION['jabatan'];
                    $tanggal_hari_ini = date("Y-m-d");

                    // Menghitung jumlah surat masuk yang diterima atau diteruskan pada hari ini
                    $records_masuk = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_surat_dis sd
                                 LEFT JOIN tb_disposisi d ON sd.id_surat = d.id_surat
                                 WHERE 
                                 ((JSON_CONTAINS(sd.diteruskan_ke, '\"$akses\"') OR sd.diteruskan_ke = '$akses')
                                 OR 
                                 ((sd.diteruskan_ke = '$akses' OR JSON_CONTAINS(sd.diteruskan_ke, '\"$akses\"')) 
                                 AND (d.dispo1 = '$jabatan' OR d.dispo2 = '$jabatan' OR d.dispo3 = '$jabatan' OR d.dispo4 = '$jabatan' OR d.dispo5 = '$jabatan')))
                                 AND DATE(sd.tanggal_surat) = '$tanggal_hari_ini'");

                    $total_sm_row = mysqli_fetch_assoc($records_masuk);
                    $total_sm_masuk = $total_sm_row['total'];

                    // Menghitung jumlah surat keluar yang dibuat pada hari ini
                    $fullname = $_SESSION['nama_lengkap'];
                    $records_keluar = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_surat_dis sd
                                                                 WHERE sd.asal_surat = '$fullname' 
                                                                 AND DATE(sd.tanggal_surat) = '$tanggal_hari_ini'");
                    $total_sk_row = mysqli_fetch_assoc($records_keluar);
                    $total_sk_keluar = $total_sk_row['total'];

                    // Menghitung total surat keseluruhan pada hari ini
                    $total_keseluruhan = $total_sm_masuk + $total_sk_keluar;
                    ?>
                    <button class="btn2">Hari Ini
                        <i class="fa fa-clock dash-icon"></i><br>
                        <span class="badge" id="" style="color: grey; padding: 2px; border-radius: 15px;"><?php echo $total_keseluruhan; ?></span>
                    </button>

                    <!-- BATAS SUCI -->
                    <?php
                    $akses = $_SESSION['akses'];
                    $jabatan = $_SESSION['jabatan'];

                    $records = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_surat_dis sd
                            LEFT JOIN tb_disposisi d ON sd.id_surat = d.id_surat
                            WHERE 
                            (JSON_CONTAINS(sd.diteruskan_ke, '\"$akses\"') OR sd.diteruskan_ke = '$akses')
                            OR 
                             d.dispo1 = '$jabatan' OR d.dispo2 = '$jabatan' OR d.dispo3 = '$jabatan' OR d.dispo4 = '$jabatan' OR d.dispo5 = '$jabatan'");

                    $total_sm_row = mysqli_fetch_assoc($records);
                    $total_sm = $total_sm_row['total'];

                    $records3 = mysqli_query($conn, "SELECT COUNT(*) AS total  FROM tb_srt_dosen WHERE tujuan_surat_srd = '$akses'");
                    $total_sm_insentif_row = mysqli_fetch_assoc($records3);
                    $total_sm_insentif = $total_sm_insentif_row['total'];

                    $records4 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_srt_dosen WHERE verifikasi = 0 AND tujuan_surat_srd = '$akses'");
                    $total_sm_verif_row = mysqli_fetch_assoc($records4);
                    $total_sm_verif = $total_sm_verif_row['total'];
                    ?>


                    <button onclick="window.location.href='surat_masuk.php'" class="btn3">Surat Masuk
                        <i class="fas fa-envelope dash-icon"></i><br>
                        <span class="badge" id="" style="color: grey; padding: 2px; border-radius: 15px;"><?php echo $total_sm; ?></span>
                    </button>

                    <?php if ($_SESSION['jabatan'] == 'LP3M') : ?>
                        <button onclick="window.location.href='surat_masuk_insentif'" class="btn3">Surat Masuk Insentif.
                            <span class="warning" id="warning">Ada <?php echo $total_sm_verif; ?> surat yang belum ditanggapi</span>
                            <i class="fas fa-envelope dash-icon"></i><br>
                            <span class="badge" id="badge1" style="color: grey; padding: 2px; border-radius: 15px; position: relative; top: -10px;"><?php echo $total_sm_insentif; ?></span>
                        </button>
                    <?php endif; ?>

                    <!-- Dispo Pimpinan -->
                    <?php
                    $fullname = $_SESSION['nama_lengkap'];
                    $records = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_surat_dis
                                WHERE tb_surat_dis.asal_surat = '$fullname'");
                    $total_sk_row = mysqli_fetch_assoc($records);
                    $total_sk = $total_sk_row['total'];

                    $records2 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_srt_dosen
                                WHERE tb_srt_dosen.asal_surat = '$fullname'");
                    $total_sk_row_dos = mysqli_fetch_assoc($records2);
                    $total_sk_dos = $total_sk_row_dos['total'];

                    $records3 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_srt_honor
                                WHERE tb_srt_honor.asal_surat = '$fullname'");
                    $total_sk_row_hon = mysqli_fetch_assoc($records3);
                    $total_sk_hon = $total_sk_row_hon['total'];

                    $records4 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_srt_honor
                                WHERE tb_srt_honor.diteruskan_ke = '$akses'");
                    $total_sm_hon_ttl = mysqli_fetch_assoc($records4);
                    $total_sm_hon = $total_sm_hon_ttl['total'];
                    ?>

                    <?php if ($_SESSION['akses'] != 'Rektor' && $_SESSION['akses'] != 'Warek1' && $_SESSION['akses'] != 'Warek2' && $_SESSION['akses'] != 'Warek3') : ?>
                        <button onclick="window.location.href='surat_keluar'" class="btn4">Surat Keluar
                            <i class="fa fa-envelope-open dash-icon"></i><br>
                            <span class="badge" id="" style="color: grey; padding: 2px; border-radius: 15px;"><?php echo $total_sk; ?></span>
                        </button>
                    <?php endif; ?>

                    <!-- NON DISPO DOSEN -->
                    <?php if ($_SESSION['jabatan'] == 'Dosen') : ?>
                        <button onclick="window.location.href='surat_keluar_nondis'" class="btn4">Surat Keluar Non Disposisi
                            <i class="fa fa-envelope-open dash-icon"></i><br>
                            <span class="badge" id="" style="color: grey; padding: 2px; border-radius: 15px;"><?php echo $total_sk_dos; ?></span>
                        </button>
                    <?php endif; ?>

                    <!-- NON DISPO PRODI -->
                    <?php if (
                        $_SESSION['jabatan'] == 'S2 Keuangan Syariah' || $_SESSION['jabatan'] == 'S1 SI'
                        || $_SESSION['jabatan'] == 'S1 TI' || $_SESSION['jabatan'] == 'S1 DKV'
                        || $_SESSION['jabatan'] == 'S1 Arsitektur' || $_SESSION['jabatan'] == 'S1 Manajemen'
                        || $_SESSION['jabatan'] == 'S1 Akuntansi'
                    ) : ?>
                        <button onclick="window.location.href='surat_keluar_honorium'" class="btn3">Surat Keluar Non Disposisi
                            <span class="warning" id="warning">Ada <?php echo $total_sk_hon; ?> Surat yang belum ditanggapi</span>
                            <i class="fas fa-envelope dash-icon"></i><br>
                            <span class="badge" id="badge1" style="color: grey; padding: 2px; border-radius: 15px; position: relative; top: -10px;"><?php echo $total_sk_hon; ?></span>
                        </button>
                    <?php endif; ?>

                    <!-- NON DISPO BAK -->
                    <?php if (
                        $_SESSION['jabatan'] == 'Bagian Administrasi Keuangan'
                    ) : ?>
                        <button onclick="window.location.href='surat_masuk_honorium'" class="btn3">Surat Masuk Honorium
                            <span class="warning" id="warning">Ada <?php echo $total_sm_hon; ?> Surat yang belum ditanggapi</span>
                            <i class="fas fa-envelope dash-icon"></i><br>
                            <span class="badge" id="badge1" style="color: grey; padding: 2px; border-radius: 15px; position: relative; top: -10px;"><?php echo $total_sm_hon; ?></span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="notificationModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <div class="modal-header">
                    <h2>Notifikasi</h2>
                </div>
                <div class="modal-body">
                    <ul class="notifications"></ul>
                </div>
            </div>
        </div>

        <div id="modalBackground"></div>

        <!-- Modifikasi button untuk memunculkan modal -->


        <?php include './footer.php'; ?>
    </div>
    <script src="js/dashboard-js.js"></script>
    <script>
        // Panggil fetchNotifications saat halaman dimuat untuk pertama kalinya
        document.addEventListener('DOMContentLoaded', function() {
            const notificationsContainer = document.querySelector('.notifications');
            const modal = document.getElementById('notificationModal');
            fetchNotifications(notificationsContainer, modal);
        });

        let notificationVisible = false;

        // <!--untuk notifikasi dashboard-->
        function toggleNotifications() {
            const modal = document.getElementById('notificationModal');
            const modalBackground = document.getElementById('modalBackground');
            const notificationsContainer = document.querySelector('.notifications');

            if (!notificationVisible) {
                fetchNotifications(notificationsContainer, modal);
                modal.classList.add('active');
                modalBackground.style.display = 'block';
                notificationVisible = true;
            } else {
                closeModal();
            }
        }

        function closeModal() {
            const modal = document.getElementById('notificationModal');
            const modalBackground = document.getElementById('modalBackground');
            modal.classList.remove('active');
            modalBackground.style.display = 'none';
            notificationVisible = false;
        }


        function fetchNotifications(container, modal) {
            const diteruskan_ke = '<?php echo json_encode($_SESSION['akses']); ?>'; // Menggunakan json_encode untuk mengubah menjadi JSON format
            const fullname = '<?php echo $_SESSION['jabatan']; ?>';
            let sql = '';

            // Parsing diteruskan_ke dari string JSON
            let diteruskanKeParsed = JSON.parse(diteruskan_ke);

            // Jika diteruskan_ke adalah array (JSON format)
            if (Array.isArray(diteruskanKeParsed)) {
                sql = `
            SELECT * FROM tb_disposisi 
            WHERE 
                (nama_selesai != '${fullname}' AND 
                nama_selesai2 != '${fullname}' AND 
                nama_selesai3 != '${fullname}' AND 
                nama_selesai4 != '${fullname}' AND 
                nama_selesai5 != '${fullname}' AND 
                nama_selesai6 != '${fullname}' AND 
                nama_selesai7 != '${fullname}')
            AND 
                JSON_CONTAINS(diteruskan_ke, '["${diteruskanKeParsed.join('","')}"]')
            `;
                } else {
                    // Jika diteruskan_ke adalah string
                    sql = `
            SELECT * FROM tb_surat_dis 
            WHERE 
                (diteruskan_ke = '${diteruskanKeParsed}' OR JSON_CONTAINS(diteruskan_ke, '"${diteruskanKeParsed}"'))
            AND (status_selesai = FALSE AND status_tolak = FALSE) 
            `;
                }

            fetch('fetch_notification.php', {
                    method: 'POST',
                    body: JSON.stringify({
                        sql: sql
                    }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const notificationsList = modal.querySelector('.notifications');
                    const notificationBadge = document.getElementById('notificationBadge');

                    notificationsList.innerHTML = '';
                    if (data.length > 0) {
                        notificationBadge.innerText = data.length;
                        notificationBadge.style.display = 'inline-block';
                    } else {
                        notificationBadge.style.display = 'none';
                    }

                    data.forEach(surat => {
                        const listItem = document.createElement('li');
                        listItem.innerHTML = `<a href="disposisi.php?id=${surat.id_surat}">Ada Surat Masuk - ${surat.perihal}</a>`;
                        notificationsList.appendChild(listItem);
                    });
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }
    </script>

</body>

</html>