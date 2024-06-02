<?php
// Include PHPWord
require_once 'vendor/autoload.php';

// Start PHP session (if not already started)
session_start();

// Include database connection file
include 'koneksi.php';

// Fetch surat data from the database based on ID
$id_surat = $_GET['id'] ?? null;
$stmt = $koneksi->prepare("SELECT * FROM tb_surat_dis WHERE id_surat = ?");
$stmt->bind_param("i", $id_surat);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Prepare data for Word document
$jenis_surat = $row['jenis_surat'];
$nama_lengkap = $row['nama_lengkap'];
$nim = $row['nim'];
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

// Create new PHPWord object
$phpWord = new \PhpOffice\PhpWord\PhpWord();

// Add a section to the document
$section = $phpWord->addSection();

$jenis_surat_map = array(
    '1' => 'Surat Permohonan',
    '2' => 'Surat Laporan',
    '3' => 'Surat KKL',
    '4' => 'Surat Riset',
    // Tambahkan jenis surat lainnya sesuai kebutuhan
);

// Mendapatkan nama jenis surat berdasarkan kode jenis surat dari database
$nama_jenis_surat = isset($jenis_surat_map[$jenis_surat]) ? $jenis_surat_map[$jenis_surat] : 'Tidak Diketahui';

// Add content to the section
$section->addText('Detail Surat', array('size' => 16, 'bold' => true));
$section->addText('Jenis Surat: ' . $nama_jenis_surat);
$section->addText('Nama Lengkap: ' . $nama_lengkap);
$section->addText('NIM: ' . $nim);
$section->addText('Perihal: ' . $perihal);
$section->addText('Nomor Surat: ' . $nomor_surat);
$section->addText('Tanggal Surat: ' . $tanggal_surat);
$section->addText('Tujuan Surat: ' . $tujuan_surat);
$section->addText('Email: ' . $email);
$section->addText('Nama Lengkap: ' . $nama_lengkap);
$section->addText('NIM: ' . $nim);
$section->addText('Prodi: ' . $prodi);
$section->addText('No. HP: ' . $no_hp);
$section->addText('Deskripsi: ' . $deskripsi);

// Save the document
$filename = 'detail_surat.docx';
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($filename);

// Download the document
header("Content-Disposition: attachment; filename=$filename");
readfile($filename);
unlink($filename); // Delete the file after download
exit;
?>
