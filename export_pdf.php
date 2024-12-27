<?php
// Start PHP session (if not already started)
session_start();
include __DIR__ . '/Maintenance/Middleware/index.php';

// Include database connection file
include 'koneksi.php';

// Check if user is logged in and has appropriate access
if (!isset($_SESSION['pengguna_type']) || $_SESSION['akses'] != 'Humas') {
    echo '<script language="javascript" type="text/javascript">
    alert("Anda tidak memiliki izin untuk mengakses halaman ini!");</script>';
    echo "<meta http-equiv='refresh' content='0; url=index.php'>";
    exit;
}

require "vendor/autoload.php";

use Dompdf\Dompdf;


$dompdf = new Dompdf();

// Fetch surat data from the database based on ID
$id_surat = $_GET['id'] ?? null;
$stmt = $koneksi->prepare("SELECT * FROM tb_surat_dis WHERE id_surat = ?");
$stmt->bind_param("i", $id_surat);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Prepare data for PDF content
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

// Membuat peta penamaan jenis surat
$jenis_surat_map = array(
    '1' => 'Surat Permohonan',
    '2' => 'Surat Laporan',
    '3' => 'Surat KKL',
    '4' => 'Surat Riset',
    // Tambahkan jenis surat lainnya sesuai kebutuhan
);

// Mendapatkan nama jenis surat berdasarkan kode jenis surat dari database
$nama_jenis_surat = isset($jenis_surat_map[$jenis_surat]) ? $jenis_surat_map[$jenis_surat] : 'Tidak Diketahui';

// Add content to the PDF
$html = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Surat</title>
<style>
body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; background-color: #f2f2f2  }
.container { width: 80%; margin: 0 auto; padding: 20px; background-color: #fff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
h1 { font-size: 36px; margin-bottom: 20px; text-align: center; }
h2 { font-size: 24px; margin-bottom: 10px; text-align: center; }
h3 { font-size: 18px; margin-bottom: 5px; }
table { width: 100%; margin-top: 20px; border-collapse: collapse; }
th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
th { background-color: #f5f5f5; }
.logo { display: block; margin: 0 auto; width: 150px; }
.signature { display: flex; justify-content: center; margin-top: 30px; }
.signature img { width: 100px; }
.signature p { margin-left: 10px; font-style: italic; }
.red-banner h2 { margin: 0; font-size: 20px; }
th { background-color: grey; text-align: center; font-weight: bold; }
</style>
</head>
<body>
</div>
<div class="container">
<img src="logo itbad.jpg" class="logo">
<h1 style="text-align: center;  color: maroon"">ITB AHMAD DAHLAN <br> <span style="font-size: 12px"> Socio Technopreneur University </span></h1>


<p style="font-size: 10px;>Jl. Ir. H. Juanda No. 77, Ciputat, Tangerang Selatan 15419 &nbsp; &nbsp; &nbsp; Jl. Imam Bonjol No. 69, Karawaci, Kota Tangerang</p>
<p style="font-size: 10px">(021) 743 0930 | WA 0858 9119 5646 | www.itb-ad.ac.id &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  (021) 557 267 45 | WA 0857 7031 0322</p>

<p>' . $nomor_surat . '</p>
<p>Lampiran : -</p>
<p>Hal : Permohonan KKL/Magang</p>
<p>' . date("d-m-Y") . '</p>
<p>Kepada Yth.</p>
<p>Bapak/Ibu Pimpinan ' . $nama_perusahaan . '</p>
<p>' . $alamat_perusahaan . '</p>
<p>Assalamualaikum, Wr, Wb.</p>
<p style="text-align: justify"> &nbsp; &nbsp; Salam sejahtera kami sampaikan kepada Bapak/Ibu beserta jajaran, semoga selalu dalam lindungan Allah SWT dan sukses menjalankan tugas sehari – hari Aamiin.</p>
<p style="text-align: justify"> &nbsp; &nbsp; Salah satu persyaratan untuk memperoleh gelar Diploma/Sarjana maka Mahasiswa/i diwajibkan melaksanakan Kuliah Kerja Lapangan (KKL). Oleh sebab itu, kami mohon kesediaan Bapak/Ibu menerima Mahasiswa/i kami melaksanakan KKL/Magang pada Instansi/Perusahaan yang Bapak/Ibu pimpin, adapun identitas Mahasiswa/i tersebut adalah sebagai berikut:</p>
<table border="1">
<thead>
<tr>
<th>No</th>
<th>Nama</th>
<th>No. Pokok</th>
<th>Program Studi</th>
<th>No. Telpon/Hp</th>
</tr>
</thead>
<tbody>
<tr>
<td>1</td>
<td>' . $nama_lengkap . '</td>
<td>' . $nim . '</td>
<td>' . $prodi . '</td>
<td>' . $no_hp . '</td>
</tr>
<tr>
<td>2</td>
<td>' . $nama_lengkap2 . '</td>
<td>' . $nim2 . '</td>
<td>' . $prodi . '</td>
<td>' . $no_hp . '</td>
</tr>
<tr>
<td>3</td>
<td>' . $nama_lengkap3 . '</td>
<td>' . $nim3 . '</td>
<td>' . $prodi . '</td>
<td>' . $no_hp . '</td>
</tr>
</tbody>
</table>
<p>Demikianlah permohonan ini kami sampaikan, atas bantuan dan kerja samanya, kami <br> ucapkan terima kasih.</p>
<p>Wassalamualaikum, Wr. Wb.</p>
<div class="signature">
<p>Hormat kami,</p>
<p>Wakil Rektor Bidang Akademik</p>
<img src="https://i.imgur.com/4r3p7mI.png" alt="Signature">
<p>Dr. Eng., Saiful Anwar, S.E., Ak., M.Si., CA.</p>
<p>NIDN/NBM: 0319047704/480.134</p>
</div>
</div>
</body>
</html>';


$dompdf->loadHtml($html);

$dompdf->render();

$dompdf->stream("DetailSurat.pdf", array('Attachment' => false));

exit;
