<?php

$isProdi = in_array($_SESSION['jabatan'], ['S2 Keuangan Syariah', 'S1 SI', 'S1 TI', 'S1 DKV', 'S1 Arsitektur', 'S1 Manajemen', 'S1 Akuntansi']);
$isPimpinan = in_array($_SESSION['akses'], ['Rektor', 'Warek1', 'Warek2', 'Warek3']);

?>

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
        <a href="pengaturan_akun"><i class="fa-solid fa-gear" style="margin-right: 10px;"></i>Pengaturan&nbsp;Akun</a>
        <a href="rekap_surat"><i class='bx bxs-notepad' style="margin-right: 10px;"></i>Rekap&nbsp;Surat</a>
        <a href="manajemen_form"><i class="fa-solid fa-wrench" style="margin-right: 10px;"></i>Pengaturan&nbsp;Formulir</a>
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
        <a href="rekap_surat"><i class='bx bxs-notepad' style="margin-right: 10px;"></i>Rekap&nbsp;Surat</a>
        <!-- <a href="pengaturan_akun"><i class="fa-solid fa-gear" style="margin-right: 10px;"></i>Pengaturan&nbsp;Akun</a> --> 
        <button class="dropdown-btn"><i class="fas fa-envelope" style="margin-right: 10px;"></i>Surat&nbsp;Menyurat
            <i class="fa fa-caret-down" style="margin-left: 5px;"></i>
        </button>
        <div class="dropdown-container">
            <a href="surat_masuk"><i class="fas fa-envelope-open-text" style="margin-right: 10px;"></i>Surat&nbsp;Masuk</a>
            <a href="surat_keluar"><i class="fas fa-envelope-open" style="margin-right: 10px;"></i>Surat&nbsp;Keluar</a>
        </div>
    </div>
<?php } elseif ($isPimpinan) { ?>
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
<?php } elseif ($_SESSION['jabatan'] == 'Dosen') { ?>
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
        <div class="dropdown-container" style="text-align: left;">
            <a href="surat_masuk"><i class="fas fa-envelope-open-text" style="margin-right: 10px;"></i>Surat&nbsp;Masuk</a>
            <button class="dropdown-btn"><i class="fas fa-envelope-open" style="margin-right: 10px;"></i>Surat&nbsp;Keluar
                <i class="fa fa-caret-down" style="margin-left: 5px;"></i>
            </button>
            <div class="dropdown-container" style="text-align: left;">
                <a href="surat_keluar">Disposisi</a>
                <a href="surat_keluar_nondis">Non - Disposisi</a>
            </div>
        </div>
    </div>
<?php } elseif ($_SESSION['jabatan'] == 'LP3M') { ?>
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
        <div class="dropdown-container" style="text-align: left;">
            <button class="dropdown-btn"><i class="fas fa-envelope-open" style="margin-right: 10px;"></i>Surat&nbsp;Masuk
                <i class="fa fa-caret-down" style="margin-left: 5px;"></i>
            </button>
            <div class="dropdown-container" style="text-align: left;">
                <a href="surat_masuk">Disposisi</a>
                <a href="surat_masuk_insentif">Insentif</a>
            </div>
            <a href="surat_keluar"><i class="fas fa-envelope-open-text" style="margin-right: 10px;"></i>Surat&nbsp;Keluar</a>
        </div>
    </div>



<?php } elseif ($isProdi) { ?>
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
        <div class="dropdown-container" style="text-align: left;">
            <a href="surat_masuk"><i class="fas fa-envelope-open-text" style="margin-right: 10px;"></i>Surat&nbsp;Masuk</a>
            <button class="dropdown-btn"><i class="fas fa-envelope-open" style="margin-right: 10px;"></i>Surat&nbsp;Keluar
                <i class="fa fa-caret-down" style="margin-left: 5px;"></i>
            </button>
            <div class="dropdown-container" style="text-align: left;">
                <a href="surat_keluar">Disposisi</a>
                <a href="surat_keluar_honorium">Non - Disposisi</a>
            </div>
        </div>
    </div>

<?php } elseif ($_SESSION['akses'] == 'keuangan') { ?>
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
            <a href="surat_masuk_honorium"><i class="fas fa-envelope-open-text" style="margin-right: 10px;"></i>Surat&nbsp;Honorium</a>
            <a href="surat_masuk"><i class="fas fa-envelope-open-text" style="margin-right: 10px;"></i>Surat&nbsp;Masuk</a>
            <a href="surat_keluar"><i class="fas fa-envelope-open" style="margin-right: 10px;"></i>Surat&nbsp;Keluar</a>
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