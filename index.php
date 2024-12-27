<?php
include __DIR__ . '/Maintenance/Middleware/index.php';
?>

<!doctype html>
<html lang="en">

<head>
    <title>Login-Teknoid</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="logo itbad.png">
    <link rel="manifest" href="manifest.json">
    <link href="styles.css" rel="stylesheet">
    <script src="script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/9e9ad697fd.js" crossorigin="anonymous"></script>
    <style>
        .loader-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2>Selamat Datang Di Teknoid</h2>
        <div class="login-logo">
            <img src="logo itbad.png">
        </div>
        <form id="loginForm" method="POST" action="login_process.php" onsubmit="return showLoader()">
            <div class="user-box">
                <input type="text" name="noinduk" id="username" autocomplete="off" required="">
                <label>Masukan Username</label>
            </div>
            <div class="user-box">
                <input type="password" name="password" id="password" autocomplete="off" required="">
                <label>Masukan Password</label>
                <i class="fa fa-eye" id="pass-toggle"></i>
            </div>
            <div class="space" style="display: flex; justify-content: space-between; align-items: center;">
                <p style="margin-top: 0px; margin-bottom: 25px;">
                    <a href="forgot" class="forgot">Lupa Password?</a>
                </p>
                <p style="margin-top: 0px; margin-bottom: 25px; position: relative;">
                    <a href="Buku_PedomanTEKNOID.pdf" download class="hover-logo">
                        <i class="fa-solid fa-book" style="font-size: 20px;"></i>
                        <span class="hover-text">Buku Pedoman</span>
                    </a>
                </p>
            </div>
            <div class="btn-container">
                <input type="submit" value="Masuk" class="login-btn" style="cursor: pointer;">
            </div>
        </form>
    </div>

    <?php include 'loader.php'; ?>

    <script>
        const togglePassword = document.querySelector('#pass-toggle');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });

        function showLoader() {
            // Menampilkan loader
            document.getElementById("loader").style.display = "flex";
            // Menunda pengiriman form selama 3 detik
            setTimeout(function() {
                document.getElementById("loginForm").submit();
            }, 1000);
            // Mencegah pengiriman form langsung
            return false;
        }
    </script>
</body>

</html>