<link rel="icon" href="logo itbad.png">

<?php

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

// Include database connection and get the connection object
$koneksi = include 'koneksi.php';

if ($koneksi === false) {
    die("Failed to include database connection");
}

$sql = "SELECT * FROM tb_pengguna WHERE reset_token = ?";

$stmt = $koneksi->prepare($sql);

if ($stmt === false) {
    die("Failed to prepare statement: " . $koneksi->error);
}

// Bind parameter
$stmt->bind_param("s", $token_hash);

// Execute statement
$result = $stmt->execute();

if ($result === false) {
    die("Failed to execute query: " . $stmt->error);
}

// Get result
$result = $stmt->get_result();

// Fetch user data
$user = $result->fetch_assoc();

if ($user === null) {
    die("Token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired");
}

// Continue with further processing if needed
?>


<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
</head>
<body>
    <div class="login-box">
        <h2>Atur Ulang Kata Sandi</h2>
        <form method="POST" action="process-reset-password.php">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="user-box">
                <input type="password" id="password" name="password" autocomplete="off" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required="">
                <label>Masukkan Kata Sandi Baru</label>
                <i class="fa fa-eye" id="pass-toggle"></i>
            </div>
            <div id="message">
                <p id="letter" class="invalid"> Harus Mengandung minimal <b>1 Huruf Kecil</b></p>
                <p id="capital" class="invalid">Harus Mengandung minimal <b>1 Huruf Besar</b></p>
                <p id="number" class="invalid">Harus Mengandung minimal <b>1 Angka</b></p>
                <p id="length" class="invalid">Harus berisi minimal <b>8 Karakter</b></p>
            </div>
            <div class="user-box">
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="off" required="">
                <label>Ulangi Kata Sandi</label>
                <i class="fa fa-eye" id="pass-toggle-confirmation"></i>
            </div>
            <div class="btn-container">
                <input type="submit" value="Send" class="login-btn">
            </div>
        </form>
    </div>
    <script>
var myInput = document.getElementById("password");
var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var number = document.getElementById("number");
var length = document.getElementById("length");

// When the user clicks on the password field, show the message box
myInput.onfocus = function() {
  document.getElementById("message").style.display = "block";
}

// When the user clicks outside of the password field, hide the message box
myInput.onblur = function() {
  document.getElementById("message").style.display = "none";
}

// When the user starts to type something inside the password field
myInput.onkeyup = function() {
  // Validate lowercase letters
  var lowerCaseLetters = /[a-z]/g;
  if(myInput.value.match(lowerCaseLetters)) {  
    letter.classList.remove("invalid");
    letter.classList.add("valid");
  } else {
    letter.classList.remove("valid");
    letter.classList.add("invalid");
  }
  
  // Validate capital letters
  var upperCaseLetters = /[A-Z]/g;
  if(myInput.value.match(upperCaseLetters)) {  
    capital.classList.remove("invalid");
    capital.classList.add("valid");
  } else {
    capital.classList.remove("valid");
    capital.classList.add("invalid");
  }

  // Validate numbers
  var numbers = /[0-9]/g;
  if(myInput.value.match(numbers)) {  
    number.classList.remove("invalid");
    number.classList.add("valid");
  } else {
    number.classList.remove("valid");
    number.classList.add("invalid");
  }
  // Validate length
  if(myInput.value.length >= 8) {
    length.classList.remove("invalid");
    length.classList.add("valid");
  } else {
    length.classList.remove("valid");
    length.classList.add("invalid");
  }
}
function myFunction() {
  var x = document.getElementById("psw");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>
    <script>
    const togglePassword = document.querySelector('#pass-toggle');
    const togglePasswordConfirmation = document.querySelector('#pass-toggle-confirmation');
    const password = document.querySelector('#password');
    const passwordConfirmation = document.querySelector('#password_confirmation');
  
    togglePassword.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
    });

    togglePasswordConfirmation.addEventListener('click', function (e) {
        // toggle the type attribute
        const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmation.setAttribute('type', type);
        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
    });
    </script>

</body>
</html>