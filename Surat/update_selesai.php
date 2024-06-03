<?php

session_start();

require 'vendor/autoload.php'; // Include Composer's autoloader for PHPMailer and TCPDF

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use TCPDF;

include 'koneksi.php';
include "logout-checker.php";

// Increase memory limit and execution time
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);

// Function to generate PDF
function generatePDF($content)
{
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
function sendEmailWithPDF($to, $subject, $body, $attachmentPath)
{
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

if (isset($_POST['id']) && isset($_POST['catatan_disposisi']) && isset($_POST['action'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan_disposisi']);
    $asal_surat = isset($_SESSION['jabatan']) ? $_SESSION['jabatan'] : 'Unknown'; // Use session's jabatan
    $action = mysqli_real_escape_string($koneksi, $_POST['action']); // Get action parameter
    $kode_surat = mysqli_real_escape_string($koneksi, $_POST['kode_surat']);

    if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Humas') {
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

                    $content = '<!DOCTYPE html>
                                <html lang="id">
                                <head>
                                <meta charset="UTF-8" />
                                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                                <title>Permohonan KKL/Magang</title>
                                <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    line-height: 1.6;
                                    margin: 0;
                                    padding: 0;
                                }
                                .margin {
                                    margin: 100px 200px 200px 200px;
                                    position: relative;
                                    z-index: 1;
                                }
                                .margin .logo {
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    z-index: -1;
                                    opacity: 1;
                                }
                                .header {
                                    margin-bottom: 40px;
                                }
                                .margin .address {
                                    margin-bottom: 40px;
                                }
                                .margin .content {
                                    text-align: justify;
                                    text-justify: inter-word;
                                    z-index: 1;
                                }
                                .margin .content p {
                                    margin-bottom: 40px;
                                }
                                .margin .content .data {
                                    display: grid;
                                    grid-template-columns: 1fr 2fr;
                                    gap: 20px;
                                    max-width: 50%;
                                }
                                .data .isi, .data .isi2 {
                                    flex: 1;
                                }
                                .signature {
                                    margin-top: 40px;
                                    text-align: center;
                                }
                                .margin .signature img {
                                    width: 30%;
                                    height: 30%;
                                }
                                footer {
                                    background-color: #6c0000;
                                    text-align: center;
                                    padding: 20px;
                                }
                                </style>
                                </head>
                                <body>
                                <h1>Surat ID: ' . $id . '</h1><p>Catatan: ' . $catatan . '</p>
                                <div class="margin">
                                <div class="logo">
                                <img src="img/bgLogo.png" alt="" />
                                </div>
                                <div class="img">
                                <img src="img/kop.jpg" alt="" style="width: 100%; height: 100%; max-height: 200px" />
                                </div>
                                <p style="text-align: end">Jakarta, 24 Mei 2024</p>
                                <div class="header">
                                <p>Nomor: 361/Rek.1/V/2024</p>
                                <p>Lampiran: -</p>
                                <p>Hal: <strong>Permohonan KKL/Magang</strong></p>
                                </div>
                                <div class="address">
                                <p>Kepada Yth.</p>
                                <p>Bapak/Ibu Pimpinan <strong>PT. Samudra Katulistiwa Nusantara</strong></p>
                                <p>Jl. Kenari 9 No.2 Jl. Nasional 1 RT.1/RW.21 Kec. Pamulang</p>
                                <p>Kota Tangerang Selatan Banten 15417</p>
                                </div>
                                <div class="content">
                                <p style="font-style: italic">Assalamu’alaikum Wr Wb.</p>
                                <p>
                                Salam sejahtera kami sampaikan kepada Bapak/Ibu beserta jajaran semoga
                                selalu dalam lindungan Allah SWT dan sukses menjalankan tugas sehari – 
                                hari. Aamiin.
                                </p>
                                <p>
                                Salah satu persyaratan untuk memperoleh gelar Diploma/Sarjana maka
                                Mahasiswa/i diwajibkan melaksanakan Kuliah Kerja Lapangan (KKL).
                                Bersama ini kami mohon kesediaan Bapak/Ibu menerima Mahasiswa/i kami melaksanakan KKL/Magang pada Instansi/Perusahaan yang Bapak/Ibu pimpin, adapun identitas Mahasiswa/i tersebut adalah sebagai berikut:
                                <div class="data">
                                <div class="pertanyaan">
                                <strong>
                                <div class="isi">
                                <p>Nama</p>
                                <p>No. Pokok</p>
                                <p>Program Studi</p>
                                <p>No. Telpon/Hp</p>
                                </div>
                                </strong>
                                </div>
                                <div class="jawaban">
                                <strong>
                                <div class="isi2">
                                <p>:&nbsp;&nbsp;&nbsp;Hendrik</p>
                                <p>:&nbsp;&nbsp;&nbsp;2190241007</p>
                                <p>:&nbsp;&nbsp;&nbsp;S1 Desain Komunikasi Visual</p>
                                <p>:&nbsp;&nbsp;&nbsp;085156509159</p>
                                </div>
                                </strong>
                                </div>
                                </div>
                                <p>
                                Demikianlah permohonan ini kami sampaikan atas perhatian dan kerjasama
                                Bapak/Ibu kami ucapkan terima kasih.
                                </p>
                                <p style="font-style: italic">Wassalamu’alaikum Wr. Wb.</p>
                                </div>
                                <div class="signature">
                                <img src="img/ttdRektor.png" alt="" />
                                <p style="margin: 0">NIDN/NBM: 0319047704/480.134</p>
                                </div>
                                </div>
                                <footer></footer>
                                </body>
                                </html>';
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

                    $content = '<!DOCTYPE html>
                                <html lang="id">
                                <head>
                                <meta charset="UTF-8" />
                                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                                <title>Permohonan KKL/Magang</title>
                                <style>
                                body {
                                    font-family: Arial, sans-serif;
                                    line-height: 1.6;
                                    margin: 0;
                                    padding: 0;
                                }
                                .margin {
                                    margin: 100px 200px 200px 200px;
                                    position: relative;
                                    z-index: 1;
                                }
                                .margin .logo {
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    z-index: -1;
                                    opacity: 1;
                                }
                                .header {
                                    margin-bottom: 40px;
                                }
                                .margin .address {
                                    margin-bottom: 40px;
                                }
                                .margin .content {
                                    text-align: justify;
                                    text-justify: inter-word;
                                    z-index: 1;
                                }
                                .margin .content p {
                                    margin-bottom: 40px;
                                }
                                .margin .content .data {
                                    display: grid;
                                    grid-template-columns: 1fr 2fr;
                                    gap: 20px;
                                    max-width: 50%;
                                }
                                .data .isi, .data .isi2 {
                                    flex: 1;
                                }
                                .signature {
                                    margin-top: 40px;
                                    text-align: center;
                                }
                                .margin .signature img {
                                    width: 30%;
                                    height: 30%;
                                }
                                footer {
                                    background-color: #6c0000;
                                    text-align: center;
                                    padding: 20px;
                                }
                                </style>
                                </head>
                                <body>
                                <h1>Surat ID: ' . $id . '</h1><p>Catatan: ' . $catatan . '</p>
                                <div class="margin">
                                <div class="logo">
                                <img src="img/bgLogo.png" alt="" />
                                </div>
                                <div class="img">
                                <img src="img/kop.jpg" alt="" style="width: 100%; height: 100%; max-height: 200px" />
                                </div>
                                <p style="text-align: end">Jakarta, 24 Mei 2024</p>
                                <div class="header">
                                <p>Nomor: 361/Rek.1/V/2024</p>
                                <p>Lampiran: -</p>
                                <p>Hal: <strong>Permohonan KKL/Magang</strong></p>
                                </div>
                                <div class="address">
                                <p>Kepada Yth.</p>
                                <p>Bapak/Ibu Pimpinan <strong>PT. Samudra Katulistiwa Nusantara</strong></p>
                                <p>Jl. Kenari 9 No.2 Jl. Nasional 1 RT.1/RW.21 Kec. Pamulang</p>
                                <p>Kota Tangerang Selatan Banten 15417</p>
                                </div>
                                <div class="content">
                                <p style="font-style: italic">Assalamu’alaikum Wr Wb.</p>
                                <p>
                                Salam sejahtera kami sampaikan kepada Bapak/Ibu beserta jajaran semoga
                                selalu dalam lindungan Allah SWT dan sukses menjalankan tugas sehari – 
                                hari. Aamiin.
                                </p>
                                <p>
                                Salah satu persyaratan untuk memperoleh gelar Diploma/Sarjana maka
                                Mahasiswa/i diwajibkan melaksanakan Kuliah Kerja Lapangan (KKL).
                                Bersama ini kami mohon kesediaan Bapak/Ibu menerima Mahasiswa/i kami melaksanakan KKL/Magang pada Instansi/Perusahaan yang Bapak/Ibu pimpin, adapun identitas Mahasiswa/i tersebut adalah sebagai berikut:
                                <div class="data">
                                <div class="pertanyaan">
                                <strong>
                                <div class="isi">
                                <p>Nama</p>
                                <p>No. Pokok</p>
                                <p>Program Studi</p>
                                <p>No. Telpon/Hp</p>
                                </div>
                                </strong>
                                </div>
                                <div class="jawaban">
                                <strong>
                                <div class="isi2">
                                <p>:&nbsp;&nbsp;&nbsp;Hendrik</p>
                                <p>:&nbsp;&nbsp;&nbsp;2190241007</p>
                                <p>:&nbsp;&nbsp;&nbsp;S1 Desain Komunikasi Visual</p>
                                <p>:&nbsp;&nbsp;&nbsp;085156509159</p>
                                </div>
                                </strong>
                                </div>
                                </div>
                                <p>
                                Demikianlah permohonan ini kami sampaikan atas perhatian dan kerjasama
                                Bapak/Ibu kami ucapkan terima kasih.
                                </p>
                                <p style="font-style: italic">Wassalamu’alaikum Wr. Wb.</p>
                                </div>
                                <div class="signature">
                                <img src="img/ttdRektor.png" alt="" />
                                <p style="margin: 0">NIDN/NBM: 0319047704/480.134</p>
                                </div>
                                </div>
                                <footer></footer>
                                </body>
                                </html>';
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
