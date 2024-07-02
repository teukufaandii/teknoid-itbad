<?php
include 'koneksi.php';
require 'vendor/autoload.php'; // Ensure Composer's autoloader is included

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Generate a secure token
    $token = bin2hex(random_bytes(16));
    $token_hash = hash('sha256', $token);
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

    // Update user record with reset token and expiry
    $sql = "UPDATE tb_pengguna
            SET reset_token = ?,
                reset_token_expires_at = ?
            WHERE email = ?";

    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sss", $token_hash, $expiry, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Fetch the user's name for the email
        $stmt = $koneksi->prepare("SELECT nama_lengkap FROM tb_pengguna WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $nama_lengkap = $row['nama_lengkap'] ?? 'User';

        // Initialize PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'itbad.teknoid@gmail.com';
            $mail->Password   = 'scuf qqwz eeea ercg';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('itbad.teknoid@gmail.com', 'TEKNOID ITBAD');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset';
            $mail->Body    = <<<END
            <html>
            <head>
                <style>
                    .header {
                        text-align: center;
                        padding: 10px;
                        background-color: rgba(198, 229, 251);
                    }

                    body{
                        margin: 0;
                    }

                    .logo {
                        margin-top: 10px;
                        width: 150px; /* Adjust the width as needed */
                        height: auto;
                    }

                    footer{
                        color: black;
                        text-align: center;
                        padding: 10px;
                        background-color: rgba(198, 229, 251);
                    }

                    .container {
                        color: black;
                        padding: 20px;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <img src="https://www.itb-ad.ac.id/wp-content/uploads/2019/11/LOGO-ITBAD-PNG-1024x1024.png" alt="TEKNOID ITBAD Logo" class="logo">
                    <h1 style="text-align: center; font-size: 24px; color: black;">TEKNOID ITBAD</h1>
                </div>

                <div class="container">
                    <p>Halo $nama_lengkap,</p><br>
                    <p>Kami telah menerima permintaan untuk reset password akun Anda.</p>
                    <p>Klik <a href="http://teknoid.itb-ad.ac.id/reset-password.php?token=$token">di sini</a> untuk mereset password Anda.</p>
                    <p>Jika Anda tidak merasa melakukan permintaan ini, Anda dapat mengabaikan email ini.</p><br>
                    <p>Terima kasih,<br>TEKNOID ITBAD</p>
                </div>

                <footer>
                    <p>© 2024 TeknoGenius. All rights reserved.</p>
                </footer>
            </body>
            </html>
            END;

            $mail->send();
            echo "<script>alert('Pesan telah dikirim, silahkan cek Email anda.'); window.location='index.php';</script>";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "<script>alert('Email Tidak terdaftar!'); window.location='/forgot';</script>";
    }
} else {
    echo "Invalid request.";
}
