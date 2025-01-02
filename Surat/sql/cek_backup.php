<?php
include '../koneksi.php';
require '../vendor/autoload.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_awal = $_POST['tanggal_awal'] ?? null;
    $tanggal_akhir = $_POST['tanggal_akhir'] ?? null;

    if (empty($tanggal_awal) || empty($tanggal_akhir)) {
        echo json_encode(['success' => false, 'message' => 'Tanggal awal dan akhir harus diisi.']);
        exit();
    }

    $backupFolder = realpath(__DIR__ . '/../backup');
    if (!$backupFolder) {
        $backupFolder = __DIR__ . '/../backup';
        mkdir($backupFolder, 0755, true);
    }

    $outputFile = $backupFolder . '/backup_' . date('dmy_His') . '.zip';

    $zip = new ZipArchive();

    if ($zip->open($outputFile, ZipArchive::CREATE) !== TRUE) {
        echo json_encode(['success' => false, 'message' => 'Gagal membuka file ZIP untuk penulisan.']);
        exit();
    }

    // Function to add files to ZIP
    function addFilesToZip($conn, $query, $basePath, $folderName, $zip, $tanggal_awal, $tanggal_akhir)
    {
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ss", $tanggal_awal, $tanggal_akhir);
        $stmt->execute();
        $result = $stmt->get_result();

        $allFilesExist = true;

        while ($row = $result->fetch_assoc()) {
            foreach ($row as $file) {
                if (!empty($file)) {
                    $filePath = $basePath . $file;
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, $folderName . '/' . $file);
                    } else {
                        $allFilesExist = false;
                    }
                }
            }
        }
        return $allFilesExist;
    }

    // Proses file dari tb_srt_dosen
    $queryDosen = "SELECT file_berkas_insentif_ppm, file_berkas_ppm, file_berkas_insentif_pi, file_berkas_pi, 
                       file_berkas_insentif_ppdpi, file_berkas_ppdpi, file_berkas_insentif_ppdks, file_berkas_ppdks, 
                       file_berkas_insentif_vl, file_berkas_vl, file_berkas_insentif_hki, file_berkas_hki, 
                       file_berkas_insentif_tg, file_berkas_tg, file_berkas_insentif_buku, file_berkas_buku, 
                       file_berkas_insentif_mpdks, file_berkas_mpdks, file_berkas_insentif_ipbk, file_berkas_ipbk
                       FROM tb_srt_dosen WHERE tanggal_surat BETWEEN ? AND ?";
    if (!addFilesToZip($conn, $queryDosen, '../uploads/dosen/', 'dosen', $zip, $tanggal_awal, $tanggal_akhir)) {
        $zip->close();
        echo json_encode(['success' => false, 'message' => 'Gagal memproses file dosen dari folder dosen karena ada file yang tidak ditemukan.']);
        exit();
    }

    // Proses file dari tb_srt_honor
    $queryHonor = "SELECT berkas FROM tb_srt_honor WHERE tanggal_surat BETWEEN ? AND ?";
    if (!addFilesToZip($conn, $queryHonor, '../uploads/honorium/', 'honorium', $zip, $tanggal_awal, $tanggal_akhir)) {
        $zip->close();
        echo json_encode(['success' => false, 'message' => 'Gagal memproses file honorium.']);
        exit();
    }

    // Proses file dari tb_surat_dis
    $queryDis = "SELECT id_surat FROM tb_surat_dis WHERE tanggal_surat BETWEEN ? AND ?";
    $stmtDis = $conn->prepare($queryDis);
    $stmtDis->bind_param("ss", $tanggal_awal, $tanggal_akhir);
    $stmtDis->execute();
    $resultDis = $stmtDis->get_result();

    while ($row = $resultDis->fetch_assoc()) {
        $id_surat = $row['id_surat'];

        // Proses file dari file_laporan
        $queryLaporan = "SELECT nama_laporan FROM file_laporan WHERE id_surat = ?";
        $stmtLaporan = $conn->prepare($queryLaporan);
        $stmtLaporan->bind_param("s", $id_surat);
        $stmtLaporan->execute();
        $resultLaporan = $stmtLaporan->get_result();

        while ($laporan = $resultLaporan->fetch_assoc()) {
            $filePath = '../uploads/laporan/' . $laporan['nama_laporan'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, 'laporan/' . $laporan['nama_laporan']);
            } else {
                $zip->close();
                echo json_encode(['success' => false, 'message' => 'Gagal memproses file laporan karena ada file yang tidak ditemukan.']);
                exit();
            }
        }

        // Proses file dari file_berkas
        $queryBerkas = "SELECT nama_berkas FROM file_berkas WHERE id_surat = ?";
        $stmtBerkas = $conn->prepare($queryBerkas);
        $stmtBerkas->bind_param("s", $id_surat);
        $stmtBerkas->execute();
        $resultBerkas = $stmtBerkas->get_result();

        while ($berkas = $resultBerkas->fetch_assoc()) {
            $filePath = '../uploads/berkas/' . $berkas['nama_berkas'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, 'berkas/' . $berkas['nama_berkas']);
            }
        }
    }

    if ($zip->close()) {
        if (file_exists($outputFile)) {
            echo json_encode([
                'success' => true,
                'backupFilePath' => str_replace('\\', '/', realpath($outputFile)),
                'message' => 'Backup berhasil dibuat.'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'File backup tidak ditemukan setelah ZIP dibuat.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menutup file ZIP.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
