<?php
// Start PHP session (if not already started)
session_start();

// Include database connection file
include 'koneksi.php';

// Check if user is logged in and has appropriate access
if (!isset($_SESSION['pengguna_type']) || $_SESSION['akses'] != 'Humas') {
    echo '<script language="javascript" type="text/javascript">
    alert("Anda tidak memiliki izin untuk mengakses halaman ini!");</script>';
    echo "<meta http-equiv='refresh' content='0; url=index.php'>";
    exit;
}

// Include TCPDF library
require_once __DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php';

// Initialize TCPDF object
// Initialize TCPDF object with default values
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Detail Surat');
$pdf->SetSubject('Detail Surat');
$pdf->SetKeywords('Surat, Detail, PDF');

// Add a page
$pdf->AddPage();


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
$prodi = $row['prodi'];
$no_hp = $row['no_hp'];
$deskripsi = $row['deskripsi'];

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
$html = '<h1><center> Informasi Surat </center></h1>';
$html .= '<p>Tanggal: ' . date("Y-m-d") . '</p>';
$html .= '<p>Pengirim: [Nama Pengirim]</p>';
$html .= '<p>Alamat: [Alamat Pengirim]</p>';
$html .= '<hr>'; // Garis untuk memisahkan informasi surat dengan detail surat
$html .= '<h1>Detail Surat</h1>';
$html .= '<p>Jenis Surat: ' . $nama_jenis_surat . '</p>';
$html .= '<p>Asal Surat: ' . $asal_surat . '</p>';
$html .= '<p>Perihal: ' . $perihal . '</p>';
$html .= '<p>Nomor Surat: ' . $nomor_surat . '</p>';
$html .= '<p>Tanggal Surat: ' . $tanggal_surat . '</p>';
$html .= '<p>Tujuan Surat: ' . $tujuan_surat . '</p>';
$html .= '<p>Email: ' . $email . '</p>';
$html .= '<p>Nama Lengkap: ' . $nama_lengkap . '</p>';
$html .= '<p>NIM: ' . $nim . '</p>';
$html .= '<p>Prodi: ' . $prodi . '</p>';
$html .= '<p>No. HP: ' . $no_hp . '</p>';
$html .= '<p>Deskripsi: ' . $deskripsi . '</p>';

// Set font
$pdf->SetFont('times', '', 11);

// Write the HTML content to the PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Tanda tangan Rektor
$pdf->SetFont('times', '', 12);
$pdf->SetY(-30); // Set posisi Y di bagian bawah halaman
$pdf->Cell(0, 10, 'Atas nama Rektor,', 0, false, 'R', 0, '', 0, false, 'T', 'M');

// Tambahkan nama Rektor
$pdf->SetY(-20); // Sesuaikan posisi Y agar berada di bawah teks sebelumnya
$pdf->Cell(0, 10, 'Fandi', 0, false, 'R', 0, '', 0, false, 'T', 'M');

// Close and output PDF
$pdf->Output('detail_surat.pdf', 'D');
exit;

?>
