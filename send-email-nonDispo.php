<?php

$email = $_POST["email"];

// Check if the email exists in the database
$koneksi = include 'koneksi.php';
$stmt = $koneksi->prepare("SELECT * FROM tb_surat_dis WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($result->num_rows == 0) {
    echo "<script>alert('Email tidak terdaftar.'); window.location='index.php';</script>";
    exit; // Terminate the script
}

// If email exists, proceed with the password reset process
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);

// Set expiry time to 10 minutes from the current time
$expiry = date("Y-m-d H:i:s", time() + 60 * 10);

$sql = "UPDATE tb_surat_dis
        SET reset_token = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($koneksi->affected_rows) {

    $mail = include 'mailer.php';

    $nama_lengkap = $row["nama_lengkap"];

    $pdf->Output(__DIR__ . '/detail_surat.pdf', 'F');

    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    
    // Mengisi isi email menggunakan sintaks heredoc
    $mail->Body = <<<END
    <html>
    <head>
        <style>
            /* CSS styling for email */
            .header {
                text-align: center;
                padding: 10px;
                background-color: rgba(198, 229, 251);
            }

            body{
                margin: 0;
            }

            /* CSS styling for logo */
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

    $mail->isHTML(true); // Set email format to HTML

    try {
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
}



echo "<script>alert('Pesan telah dikirim, silahkan cek Email anda.'); window.location='index.php';</script>";
?>
