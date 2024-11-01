<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

header('Content-Type: application/json');

try {
    $host = 'localhost';
    $dbname = 'db_teknoid';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_GET['jenis_insentif']) || !isset($_GET['tanggal_awal']) || !isset($_GET['tanggal_akhir'])) {
        echo json_encode(["error" => "Jenis insentif, tanggal awal, atau tanggal akhir tidak ditentukan."]);
        exit;
    }

    $jenis_insentif = $_GET['jenis_insentif'];
    $tanggal_awal = $_GET['tanggal_awal'];
    $tanggal_akhir = $_GET['tanggal_akhir'];

    $stmt = $conn->prepare("SELECT * FROM tb_srt_dosen WHERE jenis_insentif = :jenis_insentif AND tanggal_surat BETWEEN :tanggal_awal AND :tanggal_akhir");
    $stmt->bindParam(':jenis_insentif', $jenis_insentif);
    $stmt->bindParam(':tanggal_awal', $tanggal_awal);
    $stmt->bindParam(':tanggal_akhir', $tanggal_akhir);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        // Spreadsheet creation logic
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Asal Surat', 'Tanggal Surat', 'Jenis Insentif'];

        if ($jenis_insentif == 'publikasi') {
            $headers = array_merge($headers, ['Jenis Publikasi/Jurnal', 'Judul Publikasi', 'Nama Jurnal/Koran/Majalah/Penerbit', 'Vol. No. Tahun. ISSN-Edisi-Halaman', 'Link Jurnal']);
        } elseif ($jenis_insentif == 'pertemuan_ilmiah') {
            $headers = array_merge($headers, ['Skala', 'Nama Pertemuan Ilmiah', 'Usulan Biaya']);
        } elseif ($jenis_insentif == 'keynote_speaker') {
            $headers = array_merge($headers, ['Skala', 'Nama Pertemuan Ilmiah']);
        } elseif ($jenis_insentif == 'visiting') {
            $headers = array_merge($headers, ['Nama Kegiatan dan Lembaga Tujuan', 'Waktu Pelaksanaan']);
        } elseif ($jenis_insentif == 'hki') {
            $headers = array_merge($headers, ['Jenis Kekayaan Intelektual', 'Judul Kekayaan Intelektual']);
        } elseif ($jenis_insentif == 'teknologi') {
            $headers = array_merge($headers, ['Teknologi Yang Diusulkan', 'Deskripsi Teknologi']);
        } elseif ($jenis_insentif == 'buku') {
            $headers = array_merge($headers, ['Jenis Buku', 'Judul Buku', 'Sinopsis', 'ISBN/Jumlah Halaman/Penerbit']);
        } elseif ($jenis_insentif == 'model') {
            $headers = array_merge($headers, ['Nama Model, Prototype, Desain, Karya Seni, Rekayasa Sosial, Kebijakan yang Diusulkan', 'Deskripsi']);
        } elseif ($jenis_insentif == 'insentif_publikasi') {
            $headers = array_merge($headers, ['Judul Publikasi', 'Nama Penerbit dan Waktu Terbit', 'Tautan Publikasi Berita']);
        } else {
            $headers = array_merge($headers, ['Skema', 'Judul Penelitian/Pengabdian Masyarakat']);
        }

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $sheet->getStyle($column . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
            $sheet->getStyle($column . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }

        $sheet->getStyle('A1:' . $column . '1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $row = 2;
        foreach ($results as $rowData) {
            $column = 'A';
            $sheet->setCellValue($column++ . $row, $rowData['asal_surat']);
            $sheet->setCellValue($column++ . $row, (new DateTime($rowData['tanggal_surat']))->format('d-m-Y'));
            $sheet->setCellValue($column++ . $row, $rowData['jenis_insentif']);

            if ($jenis_insentif == 'publikasi') {
                $sheet->setCellValue($column++ . $row, $rowData['jenis_publikasi_pi']);
                $sheet->setCellValue($column++ . $row, $rowData['judul_publikasi_pi']);
                $sheet->setCellValue($column++ . $row, $rowData['nama_jurnal_pi']);
                $sheet->setCellValue($column++ . $row, $rowData['vol_notahun_pi']);
                $sheet->setCellValue($column++ . $row, $rowData['link_jurnal_pi']);
            } elseif ($jenis_insentif == 'pertemuan_ilmiah') {
                $sheet->setCellValue($column++ . $row, $rowData['skala_ppdpi']);
                $sheet->setCellValue($column++ . $row, $rowData['nama_pertemuan_ppdpi']);
                $sheet->setCellValue($column++ . $row, "Rp. " . $rowData['usulan_biaya_ppdpi']);
            } elseif ($jenis_insentif == 'keynote_speaker') {
                $sheet->setCellValue($column++ . $row, $rowData['skala_ppdks']);
                $sheet->setCellValue($column++ . $row, $rowData['nama_pertemuan_ppdks']);
            } elseif ($jenis_insentif == 'visiting') {
                $sheet->setCellValue($column++ . $row, $rowData['nm_kegiatan_vl']);
                $sheet->setCellValue($column++ . $row, $rowData['waktu_pelaksanaan_vl']);
            } elseif ($jenis_insentif == 'hki') {
                $sheet->setCellValue($column++ . $row, $rowData['jenis_hki']);
                $sheet->setCellValue($column++ . $row, $rowData['judul_hki']);
            } elseif ($jenis_insentif == 'teknologi') {
                $sheet->setCellValue($column++ . $row, $rowData['teknologi_tg']);
                $sheet->setCellValue($column++ . $row, $rowData['deskripsi_tg']);
            } elseif ($jenis_insentif == 'buku') {
                $sheet->setCellValue($column++ . $row, $rowData['jenis_buku']);
                $sheet->setCellValue($column++ . $row, $rowData['judul_buku']);
                $sheet->setCellValue($column++ . $row, $rowData['sinopsis_buku']);
                $sheet->setCellValue($column++ . $row, $rowData['isbn_buku']);
            } elseif($jenis_insentif == 'penelitian') {
                $sheet->setCellValue($column++ . $row, $rowData['skema_ppmdpek']);
                $sheet->setCellValue($column++ . $row, $rowData['judul_penelitian_ppm']);
            } elseif($jenis_insentif == 'model') {
                $sheet->setCellValue($column++ . $row, $rowData['nama_model_mpdks']);
                $sheet->setCellValue($column++ . $row, $rowData['deskripsi_mpdks']);
            } elseif($jenis_insentif == 'insentif_publikasi') {
                $sheet->setCellValue($column++ . $row, $rowData['judul_ipbk']);
                $sheet->setCellValue($column++ . $row, $rowData['namaPenerbit_dan_waktu_ipbk']);
                $sheet->setCellValue($column++ . $row, $rowData['link_publikasi_ipbk']);
            } else{
                echo "<p>Skema insentif tidak ditentukan.</p>";
            }

            $sheet->getStyle('A' . $row . ':' . $column . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'Data Dosen - ' . date('Y-m-d') . '.xlsx';
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    } else {
        echo json_encode(["error" => "Tidak ada data ditemukan."]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Kesalahan pada server: " . $e->getMessage()]);
}
?>
