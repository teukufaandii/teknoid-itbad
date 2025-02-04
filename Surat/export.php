<?php

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

if (isset($_POST['export_data'])) {
    include 'koneksi.php';
    
    // Unserialize the filtered data
    $user_arr = unserialize($_POST['export_data']);

    // Get jenis_surat from the post data
    $jenis_surat = $_POST['jenis_surat'];

    // If there is no data, show an error
    if (empty($user_arr)) {
        echo "No data available for export.";
        exit();
    }

    // Prepare a list of kode_surat to be used in the query
    $kode_surat_list = array_column($user_arr, 0); // Assuming 0 index contains `kode_surat` or `id`
    $kode_surat_str = implode("','", $kode_surat_list);

    if ($jenis_surat == "Surat Permohonan" || $jenis_surat == 1) {
        $sql = "SELECT kode_surat, jenis_surat, asal_surat, perihal, tanggal_surat 
                FROM tb_surat_dis 
                WHERE jenis_surat = 1";
        $judul = [
            'No',
            'Kode Surat',
            'Jenis Surat',
            'Asal Surat',
            'Perihal',
            'Tanggal Surat'
        ];
    } elseif ($jenis_surat == "Surat Laporan" || $jenis_surat == 2) {
        $sql = "SELECT kode_surat, jenis_surat, asal_surat, perihal, tanggal_surat 
                FROM tb_surat_dis 
                WHERE jenis_surat = 2";
        $judul = [
            'No',
            'Kode Surat',
            'Jenis Surat',
            'Asal Surat',
            'Perihal',
            'Tanggal Surat'
        ];
    } elseif ($jenis_surat == "Surat KKL" || $jenis_surat == 3) {
        $sql = "SELECT kode_surat, jenis_surat, asal_surat, perihal, tanggal_surat 
                FROM tb_surat_dis 
                WHERE kode_surat IN ('$kode_surat_str') AND jenis_surat = 3";
        $judul = [
            'No',
            'Kode Surat',
            'Jenis Surat',
            'Asal Surat',
            'Perihal',
            'Tanggal Surat'
        ];
    } elseif ($jenis_surat == "Surat Riset" || $jenis_surat == 4) {
        $sql = "SELECT kode_surat, jenis_surat, asal_surat, perihal, tanggal_surat 
                FROM tb_surat_dis 
                WHERE kode_surat IN ('$kode_surat_str') AND jenis_surat = 4";
        $judul = [
            'No',
            'Kode Surat',
            'Jenis Surat',
            'Asal Surat',
            'Perihal',
            'Tanggal Surat'
        ];
    } elseif ($jenis_surat == "Surat Insentif" || $jenis_surat == 5) {
        $sql = "SELECT id_srt, jenis_surat, asal_surat, jenis_insentif, tanggal_surat 
                FROM tb_srt_dosen 
                WHERE jenis_surat = 5";
        $judul = [
            'No',
            'Jenis Surat',
            'Asal Surat',
            'Jenis Insentif',
            'Tanggal Surat'
        ];
    } elseif ($jenis_surat == "Surat Honorium" || $jenis_surat == 7) {
        $sql = "SELECT id, jenis_surat, asal_surat, nm_kegiatan, tanggal_surat 
                FROM tb_srt_honor 
                WHERE id IN ('$kode_surat_str') AND jenis_surat = 7";
        $judul = [
            'No',
            'Jenis Surat',
            'Asal Surat',
            'Nama Kegiatan',
            'Tanggal Surat'
        ];
    } else {
        echo "Jenis surat tidak dikenal.";
        exit();
    }

    // Execute query
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menulis judul ke file Excel
        $sheet->fromArray($judul, NULL, 'A1');

        // Define border style
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        // Define colors for even and odd rows
        $evenRowStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'E3E3E3'], // Gray for even rows
            ],
        ];

        $oddRowStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFFF'], // Light Gray for odd rows
            ],
        ];

        // Add row data based on jenis_surat
        $no = 1; // Start numbering
        $rowNumber = 2; // Starting row for data
        while ($row = $result->fetch_assoc()) {
            // Convert 'jenis_surat' values
            if ($row['jenis_surat'] == 1) {
                $row['jenis_surat'] = "Surat Permohonan";
                $dataRow = [
                    $no,
                    $row['kode_surat'],
                    $row['jenis_surat'],
                    $row['asal_surat'],
                    $row['perihal'],
                    date('d-m-Y', strtotime($row['tanggal_surat']))
                ];
            } elseif ($row['jenis_surat'] == 2) {
                $row['jenis_surat'] = "Surat Laporan";
                $dataRow = [
                    $no,
                    $row['kode_surat'],
                    $row['jenis_surat'],
                    $row['asal_surat'],
                    $row['perihal'],
                    date('d-m-Y', strtotime($row['tanggal_surat']))
                ];
            } elseif ($row['jenis_surat'] == 3) {
                $row['jenis_surat'] = "Surat KKL";
                $dataRow = [
                    $no,
                    $row['kode_surat'],
                    $row['jenis_surat'],
                    $row['asal_surat'],
                    $row['perihal'],
                    date('d-m-Y', strtotime($row['tanggal_surat']))
                ];
            } elseif ($row['jenis_surat'] == 4) {
                $row['jenis_surat'] = "Surat Riset";
                $dataRow = [
                    $no,
                    $row['kode_surat'],
                    $row['jenis_surat'],
                    $row['asal_surat'],
                    $row['perihal'],
                    date('d-m-Y', strtotime($row['tanggal_surat']))
                ];
            } elseif ($row['jenis_surat'] == 5) {
                $row['jenis_surat'] = "Surat Insentif";
                $dataRow = [
                    $no,
                    $row['jenis_surat'],
                    $row['asal_surat'],
                    $row['jenis_insentif'],
                    date('d-m-Y', strtotime($row['tanggal_surat']))
                ];
            }  elseif ($row['jenis_surat'] == 7) {
                $row['jenis_surat'] = "Surat Honorium";
                $dataRow = [
                    $no,
                    $row['jenis_surat'],
                    $row['asal_surat'],
                    $row['nm_kegiatan'],
                    date('d-m-Y', strtotime($row['tanggal_surat']))
                ];
            }

            // Add the data row to the spreadsheet
            $sheet->fromArray($dataRow, NULL, 'A' . $rowNumber);

            // Apply styling: borders and background color (even/odd)
            $style = ($rowNumber % 2 == 0) ? $evenRowStyle : $oddRowStyle;
            $sheet->getStyle("A$rowNumber:F$rowNumber")->applyFromArray($style);
            $sheet->getStyle("A$rowNumber:F$rowNumber")->applyFromArray($borderStyle);

            $rowNumber++;
            $no++;
        }

        // Apply border and styling to header row
        $sheet->getStyle('A1:F1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4F81BD'], // Blue header background
            ],
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // White font for header
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Set auto column width for better readability
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create writer to save as Excel or CSV
        $writerType = $_POST['export_type'] ?? 'xlsx'; // Example: 'xlsx' for Excel or 'csv' for CSV
        if ($writerType === 'xlsx') {
            $writer = new Xlsx($spreadsheet);
            $filename = 'RekapSurat.xlsx';
        } else {
            $writer = new Csv($spreadsheet);
            $filename = 'RekapSurat.csv';
        }

        // Output file for download
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=$filename");
        $writer->save('php://output');
        exit();
    } else {
        echo "No data available for export.";
    }

    // Close connection
    $conn->close();
} else {
    echo "Data for export is not available.";
}
