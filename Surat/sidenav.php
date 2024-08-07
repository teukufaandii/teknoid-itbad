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
        <a href="manajemen_form.php"><i class="fa-solid fa-gear" style="margin-right: 10px;"></i>Pengaturan&nbsp;Formulir</a>
    </div>
<?php } elseif ($_SESSION['akses'] == 'Humas') { ?>
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
        <a href="dashboard"><i class="fas fa-tachometer-alt" style="margin-right: 10px;"></i>Dashboard</a>
        <a href="rekap_surat">Rekap&nbsp;Surat</a>
        <a href="pengaturan_akun.php"><i class="fa-solid fa-gear" style="margin-right: 10px;"></i>Pengaturan&nbsp;Akun</a>
        <button class="dropdown-btn"><i class="fas fa-envelope" style="margin-right: 10px;"></i>Surat&nbsp;Menyurat
            <i class="fa fa-caret-down" style="margin-left: 5px;"></i>
        </button>
        <div class="dropdown-container">
            <a href="surat_masuk"><i class="fas fa-envelope-open-text" style="margin-right: 10px;"></i>Surat&nbsp;Masuk</a>
            <a href="surat_keluar"><i class="fas fa-envelope-open" style="margin-right: 10px;"></i>Surat&nbsp;Keluar</a>
        </div>
    </div>
<?php } elseif ($_SESSION['akses'] == 'Rektor' || $_SESSION['akses'] == 'Warek1' || $_SESSION['akses'] == 'Warek2' || $_SESSION['akses'] == 'Warek3') { ?>
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
        <a href="dashboard"><i class="fas fa-tachometer-alt" style="margin-right: 10px;"></i>Dashboard</a>
        <button class="dropdown-btn"><i class="fas fa-envelope" style="margin-right: 10px;"></i>Surat&nbsp;Menyurat
            <i class="fa fa-caret-down" style="margin-left: 5px;"></i>
        </button>
        <div class="dropdown-container">
            <a href="surat_masuk"><i class="fas fa-envelope-open-text" style="margin-right: 10px;"></i>Surat&nbsp;Masuk</a>
        </div>
    </div>
<?php } else { ?>
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
        <a href="dashboard"><i class="fas fa-tachometer-alt" style="margin-right: 10px;"></i>Dashboard</a>
        <button class="dropdown-btn"><i class="fas fa-envelope" style="margin-right: 10px;"></i>Surat&nbsp;Menyurat
            <i class="fa fa-caret-down" style="margin-left: 5px;"></i>
        </button>
        <div class="dropdown-container">
            <a href="surat_masuk"><i class="fas fa-envelope-open-text" style="margin-right: 10px;"></i>Surat&nbsp;Masuk</a>
            <a href="surat_keluar"><i class="fas fa-envelope-open" style="margin-right: 10px;"></i>Surat&nbsp;Keluar</a>
        </div>
    </div>
<?php } ?>
<script>
    function backTo() {
        window.location.href = "dashboard";
    }
</script>