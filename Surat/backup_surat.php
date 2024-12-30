<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Humas' || isset($_SESSION['akses']) && $_SESSION['akses'] == 'Admin') {
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
        <title>Backup Surat-Teknoid</title>
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
                        <h3>Backup Surat</h3>
                        <?php
                        if (isset($_SESSION['akses']) && $_SESSION['akses'] === 'Humas') {
                            echo '<a href="surat_keluar.php"><button class="back">Kembali</button></a>';
                        }
                        ?>
                    </div>
                    <form method="POST" class="form" action="./sql/cek_backup.php" id="backupForm">
                        <div class="tanggal">
                            <div class="inputfield" style="margin:0; gap:20px;">
                                <label for="tanggal_awal">Tanggal Awal Surat</label>
                                <input type="date" id="tanggal_awal" name="tanggal_awal" style="margin-left:8px;" required>
                            </div>
                            <div class="inputfield" style="margin:0; gap:20px; margin-left: 10px;">
                                <label for="tanggal_akhir">Tanggal Akhir Surat</label>
                                <input type="date" id="tanggal_akhir" name="tanggal_akhir" required>
                            </div>
                        </div>
                        <input type="submit" name="backup" class="search" value="Backup">
                    </form>
                    <div id="notification" style="display:none; margin-top:20px;"></div>
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>
        k
        <script>
            document.getElementById('backupForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch('./sql/cek_backup.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            return fetch('./backup/index.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    backupFilePath: data.backupFilePath
                                })
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .then(response => response.json())
                    .then(result => {
                        const notification = document.getElementById('notification');
                        notification.style.display = 'block';
                        notification.textContent = result.message;
                        notification.style.color = result.success ? 'green' : 'red';
                    })
                    .catch(error => {
                        const notification = document.getElementById('notification');
                        notification.style.display = 'block';
                        notification.textContent = error.message;
                        notification.style.color = 'red';
                    });
            });

            // Sidebar
            function openNav() {
                var sidenavWidth = document.getElementById("mySidenav").style.width;
                if (sidenavWidth === "200px") {
                    closeNav();
                } else {
                    document.getElementById("mySidenav").style.width = "200px";
                    document.getElementById("Content").style.marginLeft = "200px";
                }
            }

            function closeNav() {
                document.getElementById("mySidenav").style.width = "0";
                document.getElementById("Content").style.marginLeft = "0";
            }
        </script>
    </body>

    </html>

<?php
} else {
    include "./access-denied.php";
}
?>