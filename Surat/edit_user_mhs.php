<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
include 'koneksi.php';
include "logout-checker.php";

// Periksa apakah session username telah diatur
if (!isset($_SESSION['pengguna_type'])) {
    echo '<script language="javascript" type="text/javascript">
    alert("Anda Tidak Berhak Masuk Kehalaman Ini!");</script>';
    echo "<meta http-equiv='refresh' content='0; url=../index.php'>";
    exit;
}

// Get the current user's ID from the session
$current_user_id = $_SESSION['pengguna']; // Make sure 'user_id' is set in your session

// Get the ID from the URL
$edit_id = $_GET['ids'];

// Check if the ID from the URL matches the current user's ID
if ($edit_id != $current_user_id) {
    header("Location: access-denied.php");
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .password-container {
            position: relative;
            width: 100%;
        }

        .password-container input {
            width: 100%;
            padding-right: 30px;
            /* Add space for the eye icon */
        }

        .password-container .field-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            /* Adjust this value if necessary */
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 10;
            /* Make sure the icon is on top of the input */
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
                </div>
                <?php
                // Check if there's a flash message in the session
                if (isset($_SESSION['flash_message'])) {
                    $flash = $_SESSION['flash_message'];
                    unset($_SESSION['flash_message']); // Hapus flash message sebelum output
                
                    echo '<script>
                            Swal.fire({
                                icon: "' . $flash['type'] . '",
                                title: "' . $flash['message'] . '",
                                showConfirmButton: false,
                                timer: 3000
                            }).then(() => {
                                window.location.href = "keluar.php";
                            });
                          </script>';
                    exit;
                }
                

                // Ambil data pengguna untuk di-edit
                $edit_id = $_GET['ids'];
                $sqln = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE noinduk='$edit_id' ORDER BY nama_lengkap");
                $row = mysqli_num_rows($sqln);
                $no = 0;

                while ($rn = mysqli_fetch_array($sqln)) { ?>
                    <form class="form" id="form" method="post" action="edit_user_mhsProcess.php">

                        <div class="inputfield">
                            <label>Email</label>
                            <input type="email" class="input" name="email" value="<?php echo $rn['email']; ?>" maxlength="254"
                                pattern="^(?![_.-])((?![_.-][_.-])[a-zA-Z\d_.-]){0,63}[a-zA-Z\d]@((?!-)((?!--)[a-zA-Z\d-]){0,63}[a-zA-Z\d]\.){1,2}([a-zA-Z]{2,14}\.)?[a-zA-Z]{2,14}$"
                                autocomplete="on" spellcheck="false" autocorrect="off" placeholder="contoh@gmail.com" inputmode="email"
                                required>
                        </div>

                        <div class="inputfield">
                            <label>Nomor telepon</label>
                            <input type="number" class="input" name="no_hp" id="no_telepon" value="<?php echo $rn['no_hp']; ?>"
                                autocomplete="off" required>
                        </div>

                        <!-- Password fields will remain, but will be optional -->
                        <div class="inputfield">
                            <label>Password Sekarang (optional)</label>
                            <div class="password-container">
                                <input type="password" class="input" id="current_password" name="current_password" autocomplete="off">
                                <span toggle="#current_password" class="fa fa-eye-slash field-icon toggle-password"></span>
                            </div>
                        </div>

                        <div class="inputfield">
                            <label>Password Baru (optional)</label>
                            <div class="password-container">
                                <input type="password" class="input" id="new_password" name="new_password" autocomplete="off">
                                <span toggle="#new_password" class="fa fa-eye-slash field-icon toggle-password"></span>
                            </div>
                        </div>

                        <div class="inputfield">
                            <label>Konfirmasi Password Baru (optional)</label>
                            <div class="password-container">
                                <input type="password" class="input" id="confirm_password" name="confirm_password" autocomplete="off">
                                <span toggle="#confirm_password" class="fa fa-eye-slash field-icon toggle-password"></span>
                            </div>
                        </div>

                        <div>
                            <div class="floatFiller">ff</div>
                            <button type="submit" class="btn" id="SubmitButton">Simpan</button>
                        </div>
                    </form>

                <?php } ?>
            </div>
        </div>
        <?php include './footer.php'; ?>
    </div>

    <script src="js/dashboard-js.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
        // Toggle visibility of passwords
        document.querySelectorAll('.toggle-password').forEach(item => {
            item.addEventListener('click', function() {
                let input = document.querySelector(item.getAttribute('toggle'));
                if (input.type === "password") {
                    input.type = "text";
                    item.classList.replace('fa-eye-slash', 'fa-eye');
                } else {
                    input.type = "password";
                    item.classList.replace('fa-eye', 'fa-eye-slash');
                }
            });
        });
    </script>
</body>

</html>