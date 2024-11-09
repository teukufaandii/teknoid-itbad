<?php
session_start();

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

include 'koneksi.php';
include "logout-checker.php";

// Increase memory limit and execution time
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);

// Function to generate PDF
function generatePDF($html)
{
    $uploadDir = __DIR__ . '/uploads/suratRstDosen/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = 'suratRstDosen_' . time() . '.pdf';
    $filePath = $uploadDir . $fileName;

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->render();
    file_put_contents($filePath, $dompdf->output());

    return $filePath;
}

// Function to send email with PDF attachment using PHPMailer
function sendEmailWithPDF($to, $subject, $body, $attachmentPath)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'itbad.teknoid@gmail.com';
        $mail->Password = 'qupi myjd izaw rcef';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('itbad.teknoid@gmail.com', 'TEKNOID ITB-AD');
        $mail->addAddress($to);
        $mail->addAttachment($attachmentPath);

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

if (isset($_POST['id']) && isset($_POST['catatan_penyelesaian_srd']) && isset($_POST['kd_srt_riset'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan_penyelesaian_srd']);
    $kd_srt_riset = mysqli_real_escape_string($koneksi, $_POST['kd_srt_riset']);

    error_log("ID: " . $_POST['id']);
    error_log("Catatan Penyelesaian: " . $_POST['catatan_penyelesaian_srd']);
    error_log("Kode Surat Riset: " . $_POST['kd_srt_riset']);

    if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Humas') {
        $update_query = "UPDATE tb_srt_dosen SET verifikasi = true, kd_srt_riset = '$kd_srt_riset', catatan_penyelesaian_srd = '$catatan' WHERE id_srt = '$id'";

        if (mysqli_query($koneksi, $update_query)) {
            // Fetch required data for the email content and PDF generation
            $email_query = "SELECT asal_surat, perihal_srd, tanggal_surat, tujuan_surat_srd, email_srd, 
                NIDN, prodi_pengusul, no_telpon, deskripsi_srd, 
                nama_perusahaan_srd, alamat_perusahaan_srd, alamat_srd, ttl_srd 
                FROM tb_srt_dosen WHERE id_srt = '$id'";
            $email_result = mysqli_query($koneksi, $email_query);

            if ($email_row = mysqli_fetch_assoc($email_result)) {
                // Check if essential fields are present
                if (!empty($email_row['asal_surat']) && !empty($email_row['perihal_srd']) && !empty($email_row['tanggal_surat']) && !empty($email_row['email_srd'])) {

                    $asal_surat = $email_row['asal_surat'];
                    $perihal = $email_row['perihal_srd'];
                    $nomor_surat = $email_row['kd_srt_riset'];
                    $tanggal_surat = $email_row['tanggal_surat'];
                    $tujuan_surat = $email_row['tujuan_surat_srd'];
                    $email_pengirim = $email_row['email_srd'];
                    $nidn = $email_row['NIDN'];
                    $prodi = $email_row['prodi_pengusul'];
                    $no_hp = $email_row['no_telpon'];
                    $deskripsi = $email_row['deskripsi_srd'];
                    $nama_perusahaan = $email_row['nama_perusahaan_srd'];
                    $alamat_perusahaan = $email_row['alamat_perusahaan_srd'];
                    $alamat_domisili = $email_row['alamat_srd'];
                    $ttl = $email_row['ttl_srd'];

                    $bahasa = array(
                        'January' => 'Januari',
                        'February' => 'Februari',
                        'March' => 'Maret',
                        'April' => 'April',
                        'May' => 'Mei',
                        'June' => 'Juni',
                        'July' => 'Juli',
                        'August' => 'Agustus',
                        'September' => 'September',
                        'October' => 'Oktober',
                        'November' => 'November',
                        'December' => 'Desember'
                    );
                    $bulan = $bahasa[date('F')];
                    $surat_type = 'Surat Riset'; 

                    // Base64 encode images for the logo and signature
                    $path = 'img/kop.jpg';
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

                    $path2 = 'img/signature.jpg';
                    $type2 = pathinfo($path2, PATHINFO_EXTENSION);
                    $data2 = file_get_contents($path2);
                    $base642 = 'data:image/' . $type2 . ';base64,' . base64_encode($data2);

                    $subject = "Status Surat Anda Telah Diperbarui";
                    $body = "Surat anda telah selesai diproses. Mohon cek kembali.";

                    // HTML untuk Surat Riset
                    $html = '<!DOCTYPE html>
                        <html lang="en">
                        <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Detail Surat</title>
                        <style>
                        @page{margin-bottom: 0; padding-bottom: 0;}
                        body {      }
                        .container { width: 92%; margin: 0 auto; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        font-size: 11pt; font-family: TimesNewRoman, Times New Roman, Times; }
                        h1 { font-size: 36px; margin-bottom: 10px; text-align: center;}
                        table { width: 100%; border-collapse: collapse; }
                        th, td { padding: 10px; text-align: left; }
                        th { background-color: #f5f5f5; }
                        .logo {width: 100%; height: 100%; max-height: 120px;}
                        .signature {text-align:center;}
                        .signature img {display: block; margin: 0 auto; width: 105px; height: 70px; }
                        .signature p { margin-left: 10px;}
                        .red-banner h2 { margin: 0; font-size: 20px; }
                        th { background-color: gainsboro; text-align: center; font-weight: bold; }
                        footer {background-color: #6c0000; height: 50px; width: 120%; bottom: 0; position: fixed; margin-left: -50px; margin-right: auto; left: 0; right: 0;}
                        </style>
                        </head>
                        <body>
                        <div class="container">
                                            
                        <img src="' . $base64 . '" class="logo" alt="Logo"> 
                        
                                            
                        <p style="text-align: right"> Jakarta, '  . date("d") . " " . $bulan . " " . date("Y") . '</p>
                        <p>Nomor &nbsp; &nbsp; &nbsp; &nbsp;  : ' . $nomor_surat . ' <br>
                        Lampiran  &nbsp; &nbsp; : - <br>
                        Hal &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; : <b>' . $surat_type . '</b> </p>
                                            
                                            
                        <p>Kepada Yth. <br>
                        Bapak/Ibu Pimpinan <b> ' . $nama_perusahaan . ' </b> <br>
                        ' . $alamat_perusahaan . '</p>
                        <p><i> Assalamu’alaikum, Wr, Wb. </i></p>
                        <p style="text-align: justify"> Salam sejahtera kami sampaikan kepada Bapak/Ibu beserta jajaran, semoga selalu dalam lindungan Allah SWT dan sukses menjalankan tugas sehari – hari Aamiin.</p>
                        <p style="text-align: justify"> Salah satu persyaratan untuk memperoleh gelar Sarjana, Mahasiswa/i di wajibkan menulis Karya Ilmiah (Skripsi). Bersama ini kami mohon kesediaan Bapak/Ibu menerima Mahasiswa/i kami melaksanakan Riset/Penelitian pada perusahaan yang Bapak/Ibu pimpin,adapun identitas Mahasiswa/i tersebut adalah sebagai berikut:</p>
                        
                        
                        <table border="0" style="margin-top: -12px; margin-bottom: -12px">
                            <tr>
                                <td style="width: 180px; height: 5px"> Nama Dosen </td><td style="height: 5px">: ' . $asal_surat . ' </td>
                            </tr>
                            <tr>
                                <td style="width: 180px; height: 5px"> Tempat, Tanggal Lahir </td><td style="height: 5px">: ' . $ttl . ' </td>
                            </tr>
                            <tr>
                                <td style="width: 180px; height: 5px"> Nomor Pokok </td><td style="height: 5px">: ' . $nidn . ' </td>
                            </tr>
                            <tr>
                                <td style="width: 180px; height: 5px"> Program Studi </td><td style="height: 5px">: ' . $prodi . ' </td>
                            </tr>
                            <tr>
                                <td style="width: 180px; height: 5px"> Alamat </td><td style="height: 5px">: ' . $alamat_domisili . ' </td>
                            </tr>
                            <tr>
                                <td style="width: 180px; height: 5px"> No. Telpon/HP </td><td style="height: 5px">: ' . $no_hp . ' </td>
                            </tr>
                        </table>
                        
                        <p> Demikianlah permohonan ini kami sampaikan, atas bantuan dan kerja samanya, kami ucapkan terima kasih.</p>
                        <p><i> Wassalamu’alaikum, Wr. Wb. </i></p>
                        <div class="signature" class="signature" alt="signature">
                        <p style="margin-top: -5px;">Hormat kami,<br>Wakil Rektor Bidang Akademik</p>
                        <img src="' . $base642 . '" class="logo" alt="Logo"> 
                        <p><strong>Dr. Eng., Saiful Anwar, S.E., Ak., M.Si., CA.</strong> <br>
                        NIDN/NBM: 0319047704/480.134</p>
                        </div>
                        </div>
                        <footer></footer>
                        </body>
                        </html>';

                    // Generate PDF and get the file path
                    $pdfPath = generatePDF($html);

                    // Send email with PDF attachment
                    sendEmailWithPDF($email_pengirim, $subject, $body, $pdfPath);
                } else {
                    echo "Gagal memperbarui status pada tabel tb_srt_dosen: " . mysqli_error($koneksi);
                }
            } else {
                echo "Error fetching data for email content.";
            }

            header("Location: dashboard.php");
            exit();
        }
    }
}
