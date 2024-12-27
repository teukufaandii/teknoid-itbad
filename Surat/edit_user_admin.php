<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';

if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Admin') { // Check if $_SESSION['akses'] is set and equals 'Admin'
    include 'koneksi.php';
    include "logout-checker.php";

    // Check if session username has been set
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
        <title>Edit Akun - Teknoid</title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/x-icon" href="../logo itbad.png">
        <link href="css/dashboard-style.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <style>
            .inputfield {
                position: relative;
                margin-bottom: 20px;
            }

            .input {
                width: 100%;
                padding: 10px;
                padding-right: 40px; /* Space for the eye icon */
                box-sizing: border-box;
            }

            .toggle-password {
                position: absolute;
                right: 10px;
                top: 10px;
                cursor: pointer;
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
                        <h3>Edit Akun</h3>
                        <a href="pengaturan_akun.php">
                            <button class="back">Kembali</button>
                        </a>
                    </div>
                    <?php
                    include "koneksi.php";

                    // Prepare statement to prevent SQL injection
                    $stmt = $koneksi->prepare("SELECT * FROM tb_pengguna WHERE id_pg = ?");
                    $stmt->bind_param("s", $_GET['ids']); // 's' indicates the type is string
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($rn = $result->fetch_assoc()) { ?>
                            <form class="form" method="post" action="edit_user_adminProcess.php">

                                <div class="inputfield">
                                    <label>Nomor Induk</label>
                                    <input type="text" class="input" name="noinduk" value="<?php echo htmlspecialchars($rn["noinduk"]); ?>">
                                </div>

                                <div class="inputfield">
                                    <label>Nama Pengguna</label>
                                    <input type="text" class="input" name="nama_lengkap" id="nama_lengkap" value="<?php echo htmlspecialchars($rn['nama_lengkap']); ?>" required />
                                </div>

                                <div class="inputfield">
                                    <label>Jabatan</label>
                                    <div class="custom_select">
                                        <select name="jabatan" id="jabatan" required>
                                            <option><?php echo htmlspecialchars($rn['jabatan']); ?></option>
                                            <option>Admin</option>
                                            <option>Rektor</option>
                                            <option>Warek 1</option>
                                            <option>Warek 2</option>
                                            <option>Warek 3</option>
                                            <option>Sekertaris</option>
                                            <option>Dekan FTD</option>
                                            <option>Dekan FEB</option>
                                            <option>Prodi S1 Sistem Informasi</option>
                                            <option>Prodi S1 Teknologi Informasi</option>
                                            <option>Prodi S1 Desain Komunikasi Visual</option>
                                            <option>Prodi S1 Arsitektur</option>
                                            <option>Prodi S1 Manajemen</option>
                                            <option>Pro ```php
                                            di S1 Akuntansi</option>
                                            <option>Prodi D3 Akuntansi</option>
                                            <option>Prodi D3 Keuangan dan Perbankan Syariah</option>
                                            <option>Pasca Sarjana</option>
                                            <option>Unit Keuangan</option>
                                            <option>Unit Umum</option>
                                            <option>Unit Marketing</option>
                                            <option>Unit Akademik</option>
                                            <option>Unit Perpustakaan</option>
                                            <option>Unit LP3M</option>
                                            <option>Unit KUI dan Kerjasama</option>
                                            <option>Unit PPIK dan Kemahasiswaan</option>
                                            <option>Unit IT & Laboratorium</option>
                                            <option>Dosen</option>
                                            <option>Mahasiswa</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="inputfield">
                                    <label>Hak Akses</label>
                                    <div class="custom_select">
                                        <select name="akses" id="akses" required>
                                            <option><?php echo htmlspecialchars($rn['akses']); ?></option>
                                            <option>Admin</option>
                                            <option>Rektor</option>
                                            <option>Warek 1</option>
                                            <option>Warek 2</option>
                                            <option>Warek 3</option>
                                            <option>Unit</option>
                                            <option>Dosen</option>
                                            <option>Mahasiswa</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="inputfield">
                                    <label for="password">Password</label>
                                    <input type="password" class="input" id="password" name="password" required>
                                    <span class="toggle-password" id="togglePassword">
                                        <i class="fas fa-eye" id="eyeIcon"></i>
                                    </span>
                                </div>

                                <div class="inputfield">
                                    <label>Email</label>
                                    <input type="text" class="input" name="email" value="<?php echo htmlspecialchars($rn['email']); ?>">
                                </div>

                                <div class="inputfield">
                                    <label>Nomor Telepon</label>
                                    <input type="number" class="input" name="no_telepon" id="no_telepon" value="<?php echo htmlspecialchars($rn['no_hp']); ?>">
                                </div>

                                <div>
                                    <div class="floatFiller">ff</div>
                                    <button type="submit" value="simpan" class="btn">Simpan</button>
                                </div>

                            </form>
                        <?php }
                    } else {
                        echo "No records found.";
                    }
                    $stmt->close();
                    ?>
                </div>
            </div>
            <?php include './footer.php'; ?>
        </div>
        <script src="js/dashboard-js.js"></script>
        <script>
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle the eye slash icon
                this.querySelector('i').classList.toggle('fa-eye-slash');
                this.querySelector('i').classList.toggle('fa-eye');
            });
        </script>
    </body>

    </html>

<?php
} else {
    include "./access-denied.php";
}
?>