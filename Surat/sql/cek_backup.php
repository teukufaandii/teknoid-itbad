<?php
include '../koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_awal = $_POST['tanggal_awal'];
    $tanggal_akhir = $_POST['tanggal_akhir'];

    if (empty($tanggal_awal) || empty($tanggal_akhir)) {
        echo json_encode(['success' => false, 'message' => 'Tanggal awal dan akhir harus diisi.']);
        exit();
    }

    $backupFolder = __DIR__ . '/../backup/'; // Gunakan path absolut
    $outputFile = $backupFolder . 'backup_' . date('dmy_His') . '.zip';

    // Pastikan folder backup dapat ditulis
    if (!is_writable($backupFolder)) {
        echo json_encode(['success' => false, 'message' => 'Folder backup tidak dapat ditulis.']);
        exit();
    }

    $zip = new ZipArchive();

    if ($zip->open($outputFile, ZipArchive::CREATE) === TRUE) {
        $logFile = '../backup/debug_log.txt';
        file_put_contents($logFile, "Proses ZIP dimulai...\n", FILE_APPEND);

        // Proses file dari tb_srt_dosen
        $queryDosen = "SELECT file_berkas_insentif_ppm, file_berkas_ppm, file_berkas_insentif_pi, file_berkas_pi, 
                       file_berkas_insentif_ppdpi, file_berkas_ppdpi, file_berkas_insentif_ppdks, file_berkas_ppdks, 
                       file_berkas_insentif_vl, file_berkas_vl, file_berkas_insentif_hki, file_berkas_hki, 
                       file_berkas_insentif_tg, file_berkas_tg, file_berkas_insentif_buku, file_berkas_buku, 
                       file_berkas_insentif_mpdks, file_berkas_mpdks, file_berkas_insentif_ipbk, file_berkas_ipbk, 
                       file_berkas_srd 
                       FROM tb_srt_dosen WHERE tanggal_surat BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
        $resultDosen = mysqli_query($conn, $queryDosen);

        while ($row = mysqli_fetch_assoc($resultDosen)) {
            foreach ($row as $file) {
                if (!empty($file)) {
                    $filePath = '../uploads/dosen/' . $file;
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, 'dosen/' . $file);
                    } else {
                        file_put_contents($logFile, "File tidak ditemukan: $filePath\n", FILE_APPEND);
                    }
                }
            }
        }

        // Proses file dari tb_srt_honor
        $queryHonor = "SELECT berkas FROM tb_srt_honor WHERE tanggal_surat BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
        $resultHonor = mysqli_query($conn, $queryHonor);

        while ($row = mysqli_fetch_assoc($resultHonor)) {
            $filePath = '../uploads/honorium/' . $row['berkas'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, 'honorium/' . $row['berkas']);
            } else {
                file_put_contents($logFile, "File tidak ditemukan: $filePath\n", FILE_APPEND);
            }
        }

        // Proses file dari tb_surat_dis
        $queryDis = "SELECT id_surat FROM tb_surat_dis WHERE tanggal_surat BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
        $resultDis = mysqli_query($conn, $queryDis);

        while ($row = mysqli_fetch_assoc($resultDis)) {
            $id_surat = $row['id_surat'];

            // Proses file dari file_laporan
            $queryLaporan = "SELECT nama_laporan FROM file_laporan WHERE id_surat = '$id_surat'";
            $resultLaporan = mysqli_query($conn, $queryLaporan);

            while ($laporan = mysqli_fetch_assoc($resultLaporan)) {
                $filePath = '../uploads/laporan/' . $laporan['nama_laporan'];
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, 'laporan/' . $laporan['nama_laporan']);
                } else {
                    file_put_contents($logFile, "File tidak ditemukan: $filePath\n", FILE_APPEND);
                }
            }

            // Proses file dari file_berkas
            $queryBerkas = "SELECT nama_berkas FROM file_berkas WHERE id_surat = '$id_surat'";
            $resultBerkas = mysqli_query($conn, $queryBerkas);

            while ($berkas = mysqli_fetch_assoc($resultBerkas)) {
                $filePath = '../uploads/berkas/' . $berkas['nama_berkas'];
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, 'berkas/' . $berkas['nama_berkas']);
                } else {
                    file_put_contents($logFile, "File tidak ditemukan: $filePath\n", FILE_APPEND);
                }
            }
        }

        if ($zip->close()) {
            file_put_contents($logFile, "Proses ZIP selesai.\n", FILE_APPEND);

            // Cek apakah file ZIP berhasil dibuat
            if (file_exists($outputFile)) {
                echo json_encode(['success' => true, 'backupFilePath' => realpath($outputFile), 'message' => 'Backup berhasil dibuat.']);
                include '../backup/index.php';  // Memanggil upload ke Google Drive
                uploadToGoogleDrive($outputFile);  // Fungsi untuk upload ke Google Drive
            } else {
                echo json_encode(['success' => false, 'message' => 'File backup tidak ditemukan setelah ZIP dibuat.']);
            }
        } else {
            file_put_contents($logFile, "Gagal menutup file ZIP.\n", FILE_APPEND);
            echo json_encode(['success' => false, 'message' => 'Gagal menutup file ZIP.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal membuka file ZIP untuk penulisan.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
