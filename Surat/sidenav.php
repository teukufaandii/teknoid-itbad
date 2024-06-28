<?php if ($_SESSION['akses'] == 'Admin') { ?>
    <?php include "logout-checker.php" ?>
    <div class="sidenav" id="mySidenav">
        <div class="sidenav-logo">
            <img onclick="backTo()" src="../logo itbad.png" style="cursor: pointer;">
        </div>
        <div class="greet-card">
            <p>Selamat&nbsp;Datang</p>
            <h3><?php echo $_SESSION['nama_lengkap']; ?></h3>
            <p><?php echo $_SESSION['jabatan']; ?></p>
        </div>
        <a href="pengaturan_akun.php">Pengaturan&nbsp;Akun</a>
        <a href="rekap_surat.php">Rekap&nbsp;Surat</a>
        <a href="manajemen_form.php">Manajemen&nbsp;Formulir</a>
    </div>
<?php }elseif ($_SESSION['akses'] == 'Humas') { ?>
    <?php include "logout-checker.php" ?>
    <div class="sidenav" id="mySidenav">
        <div class="sidenav-logo">
            <img src="../logo itbad.png">
        </div>
        <div class="greet-card">
            <p>Selamat&nbsp;Datang</p>
            <h3><?php echo $_SESSION['nama_lengkap']; ?></h3>
            <p><?php echo $_SESSION['jabatan']; ?></p>
        </div>
        <a href="dashboard">Dashboard</a>
        <a href="rekap_surat">Rekap&nbsp;Surat</a>
        <button class="dropdown-btn">Surat&nbsp;Menyurat
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="surat_masuk">Surat&nbsp;Masuk</a>
            <a href="surat_keluar">Surat&nbsp;Keluar</a>
        </div>
        <!-- <a href="">Inventaris</a> -->
    </div>
<?php }else{ ?>
    <?php include "logout-checker.php" ?>
    <div class="sidenav" id="mySidenav">
        <div class="sidenav-logo">
            <img src="../logo itbad.png">
        </div>
        <div class="greet-card">
            <p>Selamat&nbsp;Datang</p>
            <h3><?php echo $_SESSION['nama_lengkap']; ?></h3>
            <p><?php echo $_SESSION['jabatan']; ?></p>
        </div>
        <a href="dashboard">Dashboard</a>
        <button class="dropdown-btn">Surat&nbsp;Menyurat
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="surat_masuk">Surat&nbsp;Masuk</a>
            <a href="surat_keluar">Surat&nbsp;Keluar</a>
        </div>
        <!-- <a href="">Inventaris</a> -->
    </div>
<?php } ?>
<script>
        function backTo() {
            window.location.href = "dashboard";
        }
    </script>