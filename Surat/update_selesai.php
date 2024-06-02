<?php

session_start();

require 'vendor/autoload.php'; // Include Composer's autoloader for PHPMailer and TCPDF

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use TCPDF;

include 'koneksi.php';
include "logout-checker.php";

// Function to generate PDF
function generatePDF($content) {
    $uploadDir = __DIR__ . '/uploads/suratMhs/'; // Use an absolute path
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
    }
    $fileName = 'suratMhs_' . time() . '.pdf';
    $filePath = $uploadDir . $fileName;
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    $pdf->writeHTML($content, true, false, true, false, '');
    $pdf->Output($filePath, 'F'); // Save the PDF to a file
    return $filePath;
}


// Function to send email with PDF attachment using PHPMailer
function sendEmailWithPDF($to, $subject, $body, $attachmentPath) {
    require 'vendor/autoload.php'; // Include Composer's autoloader for PHPMailer

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'itbad.teknoid@gmail.com'; // SMTP username
        $mail->Password = 'xnei ylsc qhoc tszz'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('itbad.teknoid@gmail.com', 'TEKNOID ITB-AD');
        $mail->addAddress($to);

        // Attachments
        $mail->addAttachment($attachmentPath);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        echo "Email successfully sent to $to...";
    } catch (Exception $e) {
        echo "Email sending failed. Mailer Error: {$mail->ErrorInfo}";
    }
}

if(isset($_POST['id']) && isset($_POST['catatan_disposisi']) && isset($_POST['action'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan_disposisi']);
    $asal_surat = isset($_SESSION['jabatan']) ? $_SESSION['jabatan'] : 'Unknown'; // Use session's jabatan
    $action = mysqli_real_escape_string($koneksi, $_POST['action']); // Get action parameter
    $kode_surat = mysqli_real_escape_string($koneksi, $_POST['kode_surat']);

    if(isset($_SESSION['akses']) && $_SESSION['akses'] == 'Humas') {
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai = true, kode_surat = '$kode_surat', status_baca = true WHERE id_surat = '$id'";
        $jabatan = $_SESSION['jabatan'];
        $tanggal_disposisi1 = date("Y-m-d");

        if ($action == 'selesai') {
            $update_query_disposisi = "UPDATE tb_disposisi SET dispo1 = '$jabatan', tanggal_disposisi1 = '$tanggal_disposisi1', status_disposisi1 = true, catatan_selesai = '$catatan', nama_selesai = '$asal_surat' WHERE id_surat = '$id'";
        } elseif ($action == 'tolak') {
            $update_query_disposisi = "UPDATE tb_disposisi SET dispo1 = '$jabatan', tanggal_disposisi1 = '$tanggal_disposisi1', status_disposisi1 = true, catatan_selesai = '$catatan', nama_penolak = '$asal_surat' WHERE id_surat = '$id'";
        }

        mysqli_autocommit($koneksi, false);

        if (mysqli_query($koneksi, $update_query_surat_dis)) {
            $check_query = "SELECT COUNT(*) as count FROM tb_disposisi WHERE id_surat = '$id'";
            $result_check = mysqli_query($koneksi, $check_query);
            $row = mysqli_fetch_assoc($result_check);
            $count = $row['count'];

            if ($count > 0) {
                if (mysqli_query($koneksi, $update_query_disposisi)) {
                    mysqli_commit($koneksi);
                    echo "Status berhasil diperbarui";

                    $email_query = "SELECT email FROM tb_surat_dis WHERE id_surat = '$id'";
                    $email_result = mysqli_query($koneksi, $email_query);
                    $email_row = mysqli_fetch_assoc($email_result);
                    $email_pengirim = $email_row['email'];

                    $subject = "Status Surat Anda Telah Diperbarui";
                    $body = "Surat anda telah selesai diproses. Mohon cek kembali.";

                    $content = "<h1>Surat ID: $id</h1><p>Catatan: $catatan</p>";
                    $pdfPath = generatePDF($content);

                    sendEmailWithPDF($email_pengirim, $subject, $body, $pdfPath);
                } else {
                    mysqli_rollback($koneksi);
                    echo "Gagal memperbarui status pada tabel tb_disposisi: " . mysqli_error($koneksi);
                }
            } else {
                $insert_query = "INSERT INTO tb_disposisi (id_surat, " . ($action == 'selesai' ? "catatan_selesai, nama_selesai" : ($action == 'tolak' ? "catatan_selesai, nama_penolak" : "catatan_terima, nama_terima")) . ") VALUES ('$id', '$catatan', '$asal_surat')";
                if (mysqli_query($koneksi, $insert_query)) {
                    mysqli_commit($koneksi);
                    echo "Status berhasil diperbarui";

                    $email_query = "SELECT email FROM tb_surat_dis WHERE id_surat = '$id'";
                    $email_result = mysqli_query($koneksi, $email_query);
                    $email_row = mysqli_fetch_assoc($email_result);
                    $email_pengirim = $email_row['email'];

                    $subject = "Status Surat Anda Telah Diperbarui";
                    $body = "Surat dengan ID $id telah selesai diproses. Catatan: $catatan";

                    $content = "<h1>Surat ID: $id</h1><p>Catatan: $catatan</p>";
                    $pdfPath = generatePDF($content);

                    sendEmailWithPDF($email_pengirim, $subject, $body, $pdfPath);
                } else {
                    mysqli_rollback($koneksi);
                    echo "Gagal memperbarui status pada tabel tb_disposisi: " . mysqli_error($koneksi);
                }
            }
        } else {
            mysqli_rollback($koneksi);
            echo "Gagal memperbarui status pada tabel tb_surat_dis: " . mysqli_error($koneksi);
        }

        header("Location: dashboard.php");
        exit();
    } else {
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai = true WHERE id_surat = '$id'";

        if ($action == 'selesai') {
            $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai = '$catatan', nama_selesai = '$asal_surat' WHERE id_surat = '$id'";
        } elseif ($action == 'tolak') {
            $update_query_disposisi = "UPDATE tb_disposisi SET catatan_selesai = '$catatan', nama_penolak = '$asal_surat' WHERE id_surat = '$id'";
        }

        mysqli_autocommit($koneksi, false);

        if (mysqli_query($koneksi, $update_query_surat_dis)) {
            $check_query = "SELECT COUNT(*) as count FROM tb_disposisi WHERE id_surat = '$id'";
            $result_check = mysqli_query($koneksi, $check_query);
            $row = mysqli_fetch_assoc($result_check);
            $count = $row['count'];

            if ($count > 0) {
                if (mysqli_query($koneksi, $update_query_disposisi)) {
                    mysqli_commit($koneksi);
                    echo "Status berhasil diperbarui";
                } else {
                    mysqli_rollback($koneksi);
                    echo "Gagal memperbarui status pada tabel tb_disposisi: " . mysqli_error($koneksi);
                }
            } else {
                $insert_query = "INSERT INTO tb_disposisi (id_surat, " . ($action == 'selesai' ? "catatan_selesai, nama_selesai" : ($action == 'tolak' ? "catatan_selesai, nama_penolak" : "catatan_terima, nama_terima")) . ") VALUES ('$id', '$catatan', '$asal_surat')";
                if (mysqli_query($koneksi, $insert_query)) {
                    mysqli_commit($koneksi);
                    echo "Status berhasil diperbarui";
                } else {
                    mysqli_rollback($koneksi);
                    echo "Gagal memperbarui status pada tabel tb_disposisi: " . mysqli_error($koneksi);
                }
            }
        } else {
            mysqli_rollback($koneksi);
            echo "Gagal memperbarui status pada tabel tb_surat_dis: " . mysqli_error($koneksi);
        }
    }
} else {
    echo "ID surat, catatan disposisi, atau action tidak diterima.";
}

mysqli_close($koneksi);
?>
