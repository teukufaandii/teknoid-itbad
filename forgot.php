<!DOCTYPE html>
<html>

<head>
    <title>Resetting Password</title>
    <link href="styles.css" rel="stylesheet">
    <link rel="icon" href="logo itbad.png">
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
        <h2>Reset Password</h2>
        <form action="send-password-reset.php" method="post" onsubmit="return validateForm()">
            <div class="user-box">
                <input type="text" name="email" id="email" autocomplete="off" required="">
                <label>Masukkan Email</label>
            </div>
            <div class="btn-container">
                <input type="submit" name="submit" id="submitBtn" style="cursor: pointer;">
            </div>
        </form>
    </div>

    <?php include 'loader.php'; ?>

    <script>
        function validateForm() {
            // Menampilkan loader dan menonaktifkan tombol submit setelah diklik
            document.getElementById("loader").style.display = "flex";
            document.getElementById("submitBtn").disabled = true;
            return true; // Mengembalikan nilai true agar form dapat disubmit
        }
    </script>
</body>

</html>