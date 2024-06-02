<link rel="icon" href="logo itbad.png">
<?php
// Start PHP session (if not already started)
session_start();

// Include database connection file
include 'koneksi.php';

// Check if user is logged in and has appropriate access
if (!isset($_SESSION['pengguna_type']) || $_SESSION['akses'] != 'Humas') {
    echo '<script language="javascript" type="text/javascript">
    alert("Anda tidak memiliki izin untuk mengakses halaman ini!");</script>';
    echo "<meta http-equiv='refresh' content='0; url=../index.php'>";
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


$html = '<p><strong><u>SURAT PERMOHONAN PINDAH KAMPUS</u></strong></p>';
$html .= '<p><strong><u>&nbsp;</u></strong></p>';
$html .= '<p><strong><u>&nbsp;</u></strong></p>';
$html .= '<p>Hal: Permohonan Pindah Kampus</p>';
$html .= '<p>&nbsp;</p>';
$html .= '<p><strong>Kepada Yth,</strong></p>';
$html .= '<p><strong>Wakil Rektor I</strong></p>';
$html .= '<p><strong>Institut Teknologi dan Bisnis Ahmad Dahlan</strong></p>';
$html .= '<p><strong>Di Tempat</strong></p>';
$html .= '<p><strong>&nbsp;</strong></p>';
$html .= '<p>Dengan hormat,</p>';
$html .= '<p>Dengan ini saya yang bertanda tangan di bawah ini:</p>';
$html .= '<table>';
$html .= '<tbody>';
$html .= '<tr><td width="153"><p>Nama</p></td><td width="21"><p>:</p></td><td width="379"><p>' . $asal_surat . '</p></td></tr>';
$html .= '<tr><td width="153"><p>Nim</p></td><td width="21"><p>:</p></td><td width="379"><p>' . $nim . '</p></td></tr>';
$html .= '<tr><td width="153"><p>Program Studi</p></td><td width="21"><p>:</p></td><td width="379"><p>' . $prodi . '</p></td></tr>';
$html .= '<tr><td width="153"><p>Fakultas</p></td><td width="21"><p>:</p></td><td width="379"><p>' . $nomor_surat . '</p></td></tr>';
$html .= '<tr><td width="153"><p>Semester / Kelas</p></td><td width="21"><p>:</p></td><td width="379"><p>' . $no_hp . '</p></td></tr>';
$html .= '<tr><td width="153"><p>Alamat</p></td><td width="21"><p>:</p></td><td width="379"><p>' . $no_hp . '</p></td></tr>';
$html .= '</tbody>';
$html .= '</table>';
$html .= '<p>&nbsp;</p>';
$html .= '<p>Dengan ini saya mengajukan permohonan untuk pindah kampus dari Institut Teknologi dan Bisnis Ahmad Dahlan &lt;&lt;asal&gt;&gt; ke Institut Teknologi dan Bisnis Ahmad Dahlan &lt;&lt;tujuan&gt;&gt; dengan alasan:</p>';
$html .= '<table>';
$html .= '<tbody>';
$html .= '<tr><td width="30"><p>1.</p></td><td width="523"><p>' . $prodi . '</p></td></tr>';
$html .= '<tr><td width="30"><p>2.</p></td><td width="523"><p>' . $prodi . '</p></td></tr>';
$html .= '</tbody>';
$html .= '</table>';
$html .= '<p>&nbsp;</p>';
$html .= '<p>Demikian surat ini saya buat dengan sebenar-benarnya dan penuh tanggung jawab.</p>';
$html .= '<p>&nbsp;</p>';
$html .= '<p>&nbsp;</p>';
$html .= '<table>';
$html .= '<tbody>';
$html .= '<tr>';
$html .= '<td width="294"><p>&nbsp;</p></td>';
$html .= '<td width="307"><p>' . $prodi . ', ' . date('d/m/Y') . '</p>';
$html .= '<p>Yang Menyatakan,</p>';
$html .= '<p>&nbsp;</p>';
$html .= '<p>&nbsp;</p>';
$html .= '<p>&nbsp;</p>';
$html .= '<p>' . $nama_lengkap . '</p>';
$html .= '<p>NIM : ' . $nama_lengkap . '</p></td>';
$html .= '</tr>';
$html .= '</tbody>';
$html .= '</table>';
$html .= '<p>&nbsp;</p>';

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
$pdf->Output(__DIR__ . '/detail_surat.pdf', 'F');
exit;

?>
