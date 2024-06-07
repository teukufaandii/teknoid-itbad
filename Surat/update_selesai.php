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

                    $select_query = "SELECT * FROM tb_surat_dis WHERE id_surat = '$id'";
                    $result = mysqli_query($koneksi, $select_query);
                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);

                        // Selanjutnya, gunakan data yang sudah diambil untuk mengisi variabel dalam $html
                        $jenis_surat = $row['jenis_surat'];
                        $asal_surat = $row['asal_surat'];
                        $perihal = $row['perihal'];
                        $nomor_surat = $row['kode_surat'];
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
                        $deskripsi = $row['deskripsi'];
                        $nama_perusahaan = $row['nama_perusahaan'];
                        $alamat_perusahaan = $row['alamat_perusahaan'];
                    } else {
                        // Jika tidak ada baris data yang sesuai dengan $id, berikan pesan kesalahan atau tindakan lain sesuai kebutuhan.
                        echo "Data tidak ditemukan";
                    }

                    $path = 'img/kop.jpg';
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:img/' . $type . ';base64,' . base64_encode($data);

                    $html = '<!DOCTYPE html>
                            <html lang="en">
                            <head></head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Detail Surat</title>
                            <style>
                            body { }
                            .container { width: 92%; margin: 0 auto; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                            font-size: 11pt; font-family: TimesNewRoman, Times New Roman, Times; }
                            h1 { font-size: 36px; margin-bottom: 10px; text-align: center;}
                            table { width: 100%; border-collapse: collapse; }
                            th, td { padding: 10px; text-align: left; }
                            th { background-color: #f5f5f5; }
                            .logo {width: 100%; height: 100%; max-height: 120px;}
                            .signature {text-align:center;  margin-top: 30px;}
                            .signature img {display: block; margin: 0 auto; width: 100px; }
                            .signature p { margin-left: 10px;}
                            .red-banner h2 { margin: 0; font-size: 20px; }
                            th { background-color: gainsboro; text-align: center; font-weight: bold; }
                            footer {background-color: #6c0000; height: 50px; width: 120%; margin-left: -50px;}
                            </style>
                            </head>
                            <body>
                            </div>
                            <div class="container">

                            <img src="' . $base64 . '" class="logo" alt="Logo">

                            <p style="text-align: right"> Jakarta, '  . date("d F Y") . '</p>
                            <p>Nomor &nbsp; &nbsp; &nbsp; &nbsp;  : ' . $nomor_surat . ' <br>
                            Lampiran  &nbsp; &nbsp; : - <br>
                            Hal &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; : <b> Permohonan KKL/Magang </b> </p>

                            <p>Kepada Yth. <br>
                            Bapak/Ibu Pimpinan <b> ' . $nama_perusahaan . ' </b> <br>
                            ' . $alamat_perusahaan . '</p>
                            <p><i> Assalamualaikum, Wr, Wb. </i></p>
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
                            <tbody>
                            <tr>
                            <td>1</td>
                            <td> ' . $nama_lengkap . '</td>
                            <td style="text-align: center">' . $nim . '</td>
                            <td style="text-align: center">' . $prodi . '</td>
                            <td style="text-align: center">' . $no_hp . '</td>
                            </tr>
                            <tr>
                            <td>2</td>
                            <td>' . $nama_lengkap . '</td>
                            <td style="text-align: center">' . $nim . '</td>
                            <td style="text-align: center">' . $prodi . '</td>
                            <td style="text-align: center">' . $no_hp . '</td>
                            </tr>
                            <tr>
                            <td>3</td>
                            <td>' . $nama_lengkap . '</td>
                            <td style="text-align: center">' . $nim . '</td>
                            <td style="text-align: center">' . $prodi . '</td>
                            <td style="text-align: center">' . $no_hp . '</td>
                            </tr>
                            </tbody>
                            </table>
                            <p> Demikianlah permohonan ini kami sampaikan, atas bantuan dan kerja samanya, kami ucapkan terima kasih.</p>
                            <p><i> Wassalamualaikum, Wr. Wb. </i></p>
                            <div class="signature" class="signature" alt="signature">
                            <p>Hormat kami,<br>Wakil Rektor Bidang Akademik</p>
                            <img src="#" alt="Signature">
                            <p><strong>Dr. Eng., Saiful Anwar, S.E., Ak., M.Si., CA.</strong> <br>
                            NIDN/NBM: 0319047704/480.134</p>
                            </div>
                            </div>
                            </body>
                            </html>';



                    $pdfPath = generatePDF($html);

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
