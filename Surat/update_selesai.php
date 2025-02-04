<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
include 'koneksi.php';
include 'logout-checker.php';
require 'vendor/autoload.php';

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Cek apakah data POST ada
if (isset($_POST['id'], $_POST['catatan_disposisi'], $_POST['action'])) {
    // Escape input
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan_disposisi']);
    $asal_surat = isset($_SESSION['jabatan']) ? $_SESSION['jabatan'] : 'Unknown';
    $jabatan = isset($_SESSION['jabatan']) ? $_SESSION['jabatan'] : 'Unknown';

    // Mulai transaksi
    mysqli_autocommit($koneksi, false);

    // Query status surat dan disposisi
    $check_status_query = "SELECT status_selesai, status_selesai2, status_selesai3, status_selesai4, status_selesai5, status_selesai6, status_selesai7, asal_surat FROM tb_surat_dis WHERE id_surat = '$id'";
    $check_disposisi_query = "SELECT tanggal_eksekutor, tanggal_eksekutor2, catatan_selesai, catatan_selesai2, nama_selesai, nama_selesai2 FROM tb_disposisi WHERE id_surat = '$id'";

    $result_check_status = mysqli_query($koneksi, $check_status_query);
    $result_check_disposisi = mysqli_query($koneksi, $check_disposisi_query);

    if (!$result_check_status || !$result_check_disposisi) {
        mysqli_rollback($koneksi);
        echo "Error checking surat or disposisi status: " . mysqli_error($koneksi);
        mysqli_close($koneksi);
        exit;
    }

    $status_row = mysqli_fetch_assoc($result_check_status);
    $disposisi_row = mysqli_fetch_assoc($result_check_disposisi);

    $asal_surat = $status_row['asal_surat'];

    // Daftar kolom untuk status, tanggal, catatan, dan nama
    $status_fields = ['status_selesai', 'status_selesai2', 'status_selesai3', 'status_selesai4', 'status_selesai5', 'status_selesai6', 'status_selesai7'];
    $date_fields = ['tanggal_eksekutor', 'tanggal_eksekutor2', 'tanggal_eksekutor3', 'tanggal_eksekutor4', 'tanggal_eksekutor5', 'tanggal_eksekutor6', 'tanggal_eksekutor7'];
    $catatan_fields = ['catatan_selesai', 'catatan_selesai2', 'catatan_selesai3', 'catatan_selesai4', 'catatan_selesai5', 'catatan_selesai6', 'catatan_selesai7'];
    $nama_fields = ['nama_selesai', 'nama_selesai2', 'nama_selesai3', 'nama_selesai4', 'nama_selesai5', 'nama_selesai6', 'nama_selesai7'];

    // Loop untuk menemukan status yang belum selesai
    $update_done = false;
    foreach ($status_fields as $index => $field) {
        if (!$status_row[$field]) {
            // Query update status dan disposisi
            $update_query = "UPDATE tb_surat_dis SET $field = true WHERE id_surat = '$id';
                             UPDATE tb_disposisi 
                             SET {$date_fields[$index]} = CURDATE(), 
                                 {$catatan_fields[$index]} = '$catatan', 
                                 {$nama_fields[$index]} = '$jabatan' 
                             WHERE id_surat = '$id'";

            if (mysqli_multi_query($koneksi, $update_query)) {
                do {
                    if ($result = mysqli_store_result($koneksi)) {
                        mysqli_free_result($result);
                    }
                } while (mysqli_next_result($koneksi));
                $update_done = true;
                break;
            } else {
                mysqli_rollback($koneksi);
                echo "Error updating status: " . mysqli_error($koneksi);
                mysqli_close($koneksi);
                exit;
            }
        }
    }

    if ($update_done) {
        mysqli_commit($koneksi);
        echo "Status updated successfully.";

        if (isset($_SESSION['akses']) && $_SESSION['akses'] === 'sdm') {
            $asal_surat_query = "SELECT email FROM tb_pengguna WHERE nama_lengkap = '$asal_surat'";
            $asal_surat_result = mysqli_query($koneksi, $asal_surat_query);

            if (!$asal_surat_result) {
                echo "Error fetching email: " . mysqli_error($koneksi);
                mysqli_close($koneksi);
                exit;
            }

            $asal_surat_row = mysqli_fetch_assoc($asal_surat_result);
            $email_pengguna = $asal_surat_row['email'];

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username   = $_ENV['APP_EMAIL'];
                $mail->Password   = $_ENV['APP_EMAIL_PASS'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom($_ENV['APP_EMAIL'], 'TEKNOID ITBAD');
                $mail->addAddress($email_pengguna);

                $mail->isHTML(true);
                $mail->Subject = 'Status Surat Anda Telah Diperbaharui';
                $mail->Body    = 'Status surat atas nama ' . $asal_surat . ' telah diupdate.';

                if (isset($_FILES['file_sdm']) && $_FILES['file_sdm']['error'] === UPLOAD_ERR_OK) {
                    $mail->addAttachment($_FILES['file_sdm']['tmp_name'], $_FILES['file_sdm']['name']);
                }

                $mail->send();
                echo 'Email sent successfully.';
            } catch (Exception $e) {
                echo "Error sending email: {$mail->ErrorInfo}";
            }
        }
    } else {
        echo "All statuses are already set to completed.";
    }

    mysqli_close($koneksi);
    exit;
}
