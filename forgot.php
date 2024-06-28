<!DOCTYPE html>
<html>
<head>
    <title>Resetting Password</title>
    <link href="styles.css" rel="stylesheet">
    <link rel="icon" href="logo itbad.png">
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

    <script>
        function validateForm() {
            // Menonaktifkan tombol submit setelah diklik
            document.getElementById("submitBtn").disabled = true;
            return true; // Mengembalikan nilai true agar form dapat disubmit
        }
    </script>
</body>
</html>
