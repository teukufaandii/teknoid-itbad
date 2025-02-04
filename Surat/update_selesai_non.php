<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

include 'koneksi.php';
include "logout-checker.php";

// Increase memory limit and execution time
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);

// Function to generate PDF
function generatePDF($html)
{
    require '../vendor/autoload.php';
    $uploadDir = __DIR__ . '/uploads/suratMhs/'; // Use an absolute path
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
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
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username   = $_ENV['APP_EMAIL'];
        $mail->Password   = $_ENV['APP_EMAIL_PASS']; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //Recipients
        $mail->setFrom($_ENV['APP_EMAIL'], 'TEKNOID ITBAD');
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
    $kd_surat = mysqli_real_escape_string($koneksi, $_POST['kd_surat']);

    if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Humas') {
        $update_query_surat_dis = "UPDATE tb_surat_dis SET status_selesai = 1, kd_surat = '$kd_surat', status_baca = true WHERE id_surat = '$id'";
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

                    // Email sending and PDF generation logic
                    $email_query = "SELECT email FROM tb_surat_dis WHERE id_surat = '$id'";
                    $email_result = mysqli_query($koneksi, $email_query);
                    $email_row = mysqli_fetch_assoc($email_result);
                    $email_pengirim = $email_row['email'];

                    $subject = "Status Surat Anda Telah Diperbarui";
                    $body = "Surat anda telah selesai diproses. Mohon cek kembali.";

                    $select_query = "SELECT * FROM tb_surat_dis WHERE id_surat = '$id'";
                    $result = mysqli_query($koneksi, $select_query);
                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);


                        //Untuk mengubah bulan menjadi bahasa indonesia 
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


                        //pengkondisian jenis surat 
                        $jenis_surat_array = array(
                            3 => 'Permohonan KKL/Magang',
                            4 => 'Permohonan Riset/Penelitian'
                        );
                        $jenis_surat = $row['jenis_surat'];
                        $surat_type = $jenis_surat_array[$jenis_surat];


                        // Use fetched data to fill in the HTML template
                        $asal_surat = $row['asal_surat'];
                        $perihal = $row['perihal'];
                        $nomor_surat = $row['kd_surat'];
                        $tanggal_surat = $row['tanggal_surat'];
                        $tujuan_surat = $row['tujuan_surat'];
                        $email = $row['email'];
                        $nama_lengkap = $row['nama_lengkap'];
                        $nim = $row['nim'];
                        $nama_lengkap2 = $row['nama_lengkap2'];
                        $nim2 = $row['nim2'];
                        $nama_lengkap3 = $row['nama_lengkap3'];
                        $nim3 = $row['nim3'];
                        $prodi = $row['prodi'];
                        $no_hp = $row['no_hp'];
                        $no_hp2 = $row['no_hp2'];
                        $no_hp3 = $row['no_hp3'];
                        $deskripsi = $row['deskripsi'];
                        $nama_perusahaan = $row['nama_perusahaan'];
                        $alamat_perusahaan = $row['alamat_perusahaan'];
                        $alamat_domisili = $row['alamat_domisili'];
                        $ttl = $row['ttl'];

                        // Konsolidasi data mahasiswa ke dalam array
                        $mahasiswa = [
                            [
                                'nama_lengkap' => $row['nama_lengkap'],
                                'nim' => $row['nim'],
                                'prodi' => $row['prodi'],
                                'no_hp' => $row['no_hp']
                            ],
                            [
                                'nama_lengkap' => $row['nama_lengkap2'],
                                'nim' => $row['nim2'],
                                'prodi' => $row['prodi'],
                                'no_hp' => $row['no_hp2']
                            ],
                            [
                                'nama_lengkap' => $row['nama_lengkap3'],
                                'nim' => $row['nim3'],
                                'prodi' => $row['prodi'],
                                'no_hp' => $row['no_hp3']
                            ],
                            // Tambahkan lebih banyak mahasiswa jika diperlukan
                        ];
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


                    if ($jenis_surat == 3) {
                        // HTML untuk Surat KKL/Magang
                        $html = '<!DOCTYPE html>
                        <html lang="en">
                        <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Detail Surat</title>
                        <style>
                        @page{margin-bottom: 0; padding-bottom: 0;}
                        body { }
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
                        Hal &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; : <b> ' . $surat_type . ' </b> </p>
                                            
                                            
                        <p>Kepada Yth. <br>
                        Bapak/Ibu Pimpinan <b> ' . $nama_perusahaan . ' </b> <br>
                        ' . $alamat_perusahaan . '</p>
                        <p><i> Assalamu’alaikum, Wr, Wb. </i></p>
                        <p style="text-align: justify"> Salam sejahtera kami sampaikan kepada Bapak/Ibu beserta jajaran, semoga selalu dalam lindungan Allah SWT dan sukses menjalankan tugas sehari – hari Aamiin.</p>
                        <p style="text-align: justify"> Salah satu persyaratan untuk memperoleh gelar Diploma/Sarjana maka Mahasiswa/i diwajibkan melaksanakan Kuliah Kerja Lapangan (KKL). Oleh sebab itu, kami mohon kesediaan Bapak/Ibu menerima Mahasiswa/i kami melaksanakan KKL/Magang pada Instansi/Perusahaan yang Bapak/Ibu pimpin, adapun identitas Mahasiswa/i tersebut adalah sebagai berikut:</p>
                        <table border="1">
                        <thead>
                        <tr>
                        <th style="width:2% ">No</th>
                        <th style="width: 30%">Nama</th>
                        <th style="width: 20%">No. Pokok</th>
                        <th style="width: 23%">Program Studi</th>
                        <th style="width: 20%">No. Telpon/HP</th>
                        </tr>
                        </thead>
                        <tbody>';

                        // Loop melalui data mahasiswa untuk menghasilkan baris tabel
                        $no = 1;
                        foreach ($mahasiswa as $mhs) {
                            if (!empty($mhs['nama_lengkap'])) {
                                $html .= '<tr>
                                <td>' . $no . '</td>
                                <td>' . $mhs['nama_lengkap'] . '</td>
                                <td style="text-align: center">' . $mhs['nim'] . '</td>
                                <td style="text-align: center">' . $mhs['prodi'] . '</td>
                                <td style="text-align: center">' . $mhs['no_hp'] . '</td>
                                </tr>';
                                $no++;
                            }
                        }
                        $html .= '</tbody>
                        </table>
                        <p> Demikianlah permohonan ini kami sampaikan, atas bantuan dan kerja samanya, kami ucapkan terima kasih.</p>
                        <p><i> Wassalamu’alaikum, Wr. Wb. </i></p>
                        <div class="signature" class="signature" alt="signature">
                        <p>Hormat kami,<br>Wakil Rektor Bidang Akademik</p>
                        <img src="' . $base642 . '" class="logo" alt="Logo"> 
                        <p><strong>Dr. Eng., Saiful Anwar, S.E., Ak., M.Si., CA.</strong> <br>
                        NIDN/NBM: 0319047704/480.134</p>
                        </div>
                        </div>
                        <footer></footer>
                        </body>
                        </html>';
                    } elseif ($jenis_surat == 4) {
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
                    echo "Gagal memperbarui status pada tabel tb_disposisi: " . mysqli_error($koneksi);
                }
            } else {
                // Insert a new record if no existing records were found
                $insert_query = "INSERT INTO tb_disposisi (id_surat, dispo1, tanggal_disposisi1, status_disposisi1, catatan_selesai, nama_selesai) VALUES ('$id', '$jabatan', '$tanggal_disposisi1', true, '$catatan', '$asal_surat')";
                if (mysqli_query($koneksi, $insert_query)) {
                    mysqli_commit($koneksi);
                    echo "Status berhasil diperbarui dengan penyisipan data baru ke tb_disposisi";

                    // Email sending and PDF generation logic
                    $email_query = "SELECT email FROM tb_surat_dis WHERE id_surat = '$id'";
                    $email_result = mysqli_query($koneksi, $email_query);
                    $email_row = mysqli_fetch_assoc($email_result);
                    $email_pengirim = $email_row['email'];

                    $subject = "Status Surat Anda Telah Diperbarui";
                    $body = "Surat anda telah selesai diproses. Mohon cek kembali.";

                    $select_query = "SELECT * FROM tb_surat_dis WHERE id_surat = '$id'";
                    $result = mysqli_query($koneksi, $select_query);
                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);

                        //Untuk mengubah bulan menjadi bahasa indonesia 
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

                        //pengkondisian jenis surat 
                        $jenis_surat_array = array(
                            3 => 'Permohonan KKL/Magang',
                            4 => 'Permohonan Riset/Penelitian'
                        );

                        $jenis_surat = $row['jenis_surat'];
                        $surat_type = $jenis_surat_array[$jenis_surat];

                        // Use fetched data to fill in the HTML template
                        $asal_surat = $row['asal_surat'];
                        $perihal = $row['perihal'];
                        $nomor_surat = $row['kd_surat'];
                        $tanggal_surat = $row['tanggal_surat'];
                        $tujuan_surat = $row['tujuan_surat'];
                        $email = $row['email'];
                        $nama_lengkap = $row['nama_lengkap'];
                        $nim = $row['nim'];
                        $nama_lengkap2 = $row['nama_lengkap2'];
                        $nim2 = $row['nim2'];
                        $nama_lengkap3 = $row['nama_lengkap3'];
                        $nim3 = $row['nim3'];
                        $prodi = $row['prodi'];
                        $no_hp = $row['no_hp'];
                        $no_hp2 = $row['no_hp2'];
                        $no_hp3 = $row['no_hp3'];
                        $deskripsi = $row['deskripsi'];
                        $nama_perusahaan = $row['nama_perusahaan'];
                        $alamat_perusahaan = $row['alamat_perusahaan'];
                        $alamat_domisili = $row['alamat_domisili'];
                        $ttl = $row['ttl'];
                        
                        // Konsolidasi data mahasiswa ke dalam array
                        $mahasiswa = [
                            [
                                'nama_lengkap' => $row['nama_lengkap'],
                                'nim' => $row['nim'],
                                'prodi' => $row['prodi'],
                                'no_hp' => $row['no_hp']
                            ],
                            [
                                'nama_lengkap' => $row['nama_lengkap2'],
                                'nim' => $row['nim2'],
                                'prodi' => $row['prodi'],
                                'no_hp' => $row['no_hp2']
                            ],
                            [
                                'nama_lengkap' => $row['nama_lengkap3'],
                                'nim' => $row['nim3'],
                                'prodi' => $row['prodi'],
                                'no_hp' => $row['no_hp3']
                            ],
                            // Tambahkan lebih banyak mahasiswa jika diperlukan
                        ];
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


                    if ($jenis_surat == 3) {
                        // HTML untuk Surat KKL/Magang
                        $html = '<!DOCTYPE html>
                        <html lang="en">
                        <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Detail Surat</title>
                        <style>
                        @page{margin-bottom: 0; padding-bottom: 0;}
                        body { }
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
                        Hal &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; : <b> ' . $surat_type . ' </b> </p>
                                            
                                            
                        <p>Kepada Yth. <br>
                        Bapak/Ibu Pimpinan <b> ' . $nama_perusahaan . ' </b> <br>
                        ' . $alamat_perusahaan . '</p>
                        <p><i> Assalamu’alaikum, Wr, Wb. </i></p>
                        <p style="text-align: justify"> Salam sejahtera kami sampaikan kepada Bapak/Ibu beserta jajaran, semoga selalu dalam lindungan Allah SWT dan sukses menjalankan tugas sehari – hari Aamiin.</p>
                        <p style="text-align: justify"> Salah satu persyaratan untuk memperoleh gelar Diploma/Sarjana maka Mahasiswa/i diwajibkan melaksanakan Kuliah Kerja Lapangan (KKL). Oleh sebab itu, kami mohon kesediaan Bapak/Ibu menerima Mahasiswa/i kami melaksanakan KKL/Magang pada Instansi/Perusahaan yang Bapak/Ibu pimpin, adapun identitas Mahasiswa/i tersebut adalah sebagai berikut:</p>
                        <table border="1">
                        <thead>
                        <tr>
                        <th style="width:2% ">No</th>
                        <th style="width: 30%">Nama</th>
                        <th style="width: 20%">No. Pokok</th>
                        <th style="width: 23%">Program Studi</th>
                        <th style="width: 20%">No. Telpon/HP</th>
                        </tr>
                        </thead>
                        <tbody>';

                        // Loop melalui data mahasiswa untuk menghasilkan baris tabel
                        $no = 1;
                        foreach ($mahasiswa as $mhs) {
                            if (!empty($mhs['nama_lengkap'])) {
                                $html .= '<tr>
                                <td>' . $no . '</td>
                                <td>' . $mhs['nama_lengkap'] . '</td>
                                <td style="text-align: center">' . $mhs['nim'] . '</td>
                                <td style="text-align: center">' . $mhs['prodi'] . '</td>
                                <td style="text-align: center">' . $mhs['no_hp'] . '</td>
                                </tr>';
                                $no++;
                            }
                        }
                        $html .= '</tbody>
                        </table>
                        <p> Demikianlah permohonan ini kami sampaikan, atas bantuan dan kerja samanya, kami ucapkan terima kasih.</p>
                        <p><i> Wassalamu’alaikum, Wr. Wb. </i></p>
                        <div class="signature" class="signature" alt="signature">
                        <p>Hormat kami,<br>Wakil Rektor Bidang Akademik</p>
                        <img src="' . $base642 . '" class="logo" alt="Logo"> 
                        <p><strong>Dr. Eng., Saiful Anwar, S.E., Ak., M.Si., CA.</strong> <br>
                        NIDN/NBM: 0319047704/480.134</p>
                        </div>
                        </div>
                        <footer></footer>
                        </body>
                        </html>';
                    } elseif ($jenis_surat == 4) {
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
                    echo "Gagal menyisipkan data baru ke tabel tb_disposisi: " . mysqli_error($koneksi);
                }
            }
        } else {
            mysqli_rollback($koneksi);
            echo "Gagal memperbarui status pada tabel tb_surat_dis: " . mysqli_error($koneksi);
        }

        header("Location: dashboard.php");
        exit();
    }
}
