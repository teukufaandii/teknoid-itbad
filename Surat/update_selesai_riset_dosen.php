<?php
session_start();

require 'vendor/autoload.php';

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
    require '../vendor/autoload.php';
    $uploadDir = __DIR__ . '/uploads/suratMhs/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); 
    }
    $fileName = 'suratMhs_' . time() . '.pdf';
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
    require 'vendor/autoload.php'; // Include Composer's autoloader for PHPMailer

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'itbad.teknoid@gmail.com'; // SMTP username
        $mail->Password = 'igfk bhrb wzty dbaz'; // SMTP password
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
    $asal_surat = isset($_SESSION['jabatan']) ? $_SESSION['jabatan'] : 'Unknown';
    $action = mysqli_real_escape_string($koneksi, $_POST['action']);
    $kd_surat = mysqli_real_escape_string($koneksi, $_POST['kd_surat']);

    if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Humas') {
        $update_query_surat_dis = "UPDATE tb_srt_dosen SET verifikasi = true, kode_surat = '$kd_surat' WHERE id_srt = '$id'";
        $jabatan = $_SESSION['jabatan'];
        $tanggal_disposisi1 = date("Y-m-d");

        mysqli_autocommit($koneksi, false);

        if (mysqli_query($koneksi, $update_query_surat_dis)) {
            $check_query = "SELECT COUNT(*) as count FROM tb_srt_dosen WHERE id_srt = '$id'";
            $result_check = mysqli_query($koneksi, $check_query);
            $row = mysqli_fetch_assoc($result_check);
            $count = $row['count'];

            if ($count > 0) {
            } else {
                // Insert a new record if no existing records were found
                $insert_query = "";
                if (mysqli_query($koneksi, $insert_query)) {
                    mysqli_commit($koneksi);
                    echo "Status berhasil diperbarui dengan penyisipan data baru ke tb_disposisi";

                    // Email sending and PDF generation logic
                    $email_query = "SELECT email_srd FROM tb_srt_dosen WHERE id_srt = '$id'";
                    $email_result = mysqli_query($koneksi, $email_query);
                    $email_row = mysqli_fetch_assoc($email_result);
                    $email_pengirim = $email_row['email_srd'];

                    $subject = "Status Surat Anda Telah Diperbarui";
                    $body = "Surat anda telah selesai diproses. Mohon cek kembali.";

                    $select_query = "SELECT * FROM tb_srt_dosen WHERE id_srt = '$id'";
                    $result = mysqli_query($koneksi, $select_query);
                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);

                        // Untuk mengubah bulan menjadi bahasa indonesia 
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

                        // Pengkondisian jenis surat 
                        $jenis_surat_array = array(
                            3 => 'Permohonan KKL/Magang',
                            4 => 'Permohonan Riset/Penelitian'
                        );

                        $jenis_surat = $row['jenis_surat'];
                        $surat_type = $jenis_surat_array[$jenis_surat];

                        // Use fetched data to fill in the HTML template
                        $asal_surat = $row['asal_surat'];
                        $perihal = $row['perihal_srd'];
                        $tanggal_surat = $row['tanggal_surat'];
                        $tujuan_surat = $row['tujuan_surat_srd'];
                        $email = $row['email_srd'];
                        $nama_lengkap = $row['asal_surat'];
                        $nim = $row['NIDN'];
                        $prodi = $row['prodi_pengusul'];
                        $no_hp = $row['no_telepon'];
                        $deskripsi = $row['deskripsi_srd'];
                        $nama_perusahaan = $row['nama_perusahaan_srd'];
                        $alamat_perusahaan = $row['alamat_perusahaan_srd'];
                        $alamat_domisili = $row['alamat_srd'];
                        $ttl = $row['ttl_srd']; //belum ada nih 
                        
                    } else {
                        echo "Data tidak ditemukan";
                    }

                    //untuk kop image 
                    $path = 'img/kop.jpg';
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:img/' . $type . ';base64,' . base64_encode($data);

                    //untuk tanda tangan 
                    $path2 = 'img/signature.jpg';
                    $type2 = pathinfo($path2, PATHINFO_EXTENSION);
                    $data2 = file_get_contents($path2);
                    $base642 = 'data:img/' . $type2 . ';base64,' . base64_encode($data2);

                    if ($jenis_surat == 6) {
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
                                <td style="width: 180px; height: 5px"> Nama Mahasiswa </td><td style="height: 5px">: ' . $nama_lengkap . ' </td>
                            </tr>
                            <tr>
                                <td style="width: 180px; height: 5px"> Tempat, Tanggal Lahir </td><td style="height: 5px">: ' . $ttl . ' </td>
                            </tr>
                            <tr>
                                <td style="width: 180px; height: 5px"> Nomor Pokok </td><td style="height: 5px">: ' . $nim . ' </td>
                            </tr>
                            < ```php
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
                    }

                    // Generate PDF and get the file path
                    $pdfPath = generatePDF($html);

                    // Send email with PDF attachment
                    sendEmailWithPDF($email_pengirim, $subject, $body, $pdfPath);
                } else {
                    mysqli_rollback($koneksi);
                    echo "Gagal menyisipkan data baru ke tabel tb_srt_dosen: " . mysqli_error($koneksi);
                }
            }
        } else {
            mysqli_rollback($koneksi);
            echo "Gagal memperbarui status pada tabel tb_srt_dosen: " . mysqli_error($koneksi);
        }

        header("Location: dashboard.php");
        exit();
    }
}