<?php
require '../vendor/autoload.php';

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil path file yang dikirimkan dari form atau request
    $backupFilePath = $_POST['backupFilePath'] ?? '';

    // Debugging: log path file yang diterima ke dalam debug_log.txt
    logToFile("Path file yang diterima: " . $backupFilePath);

    // Validasi apakah file path tidak kosong dan file tersebut ada
    if (empty($backupFilePath) || !file_exists($backupFilePath)) {
        echo json_encode(['success' => false, 'message' => 'File backup tidak ditemukan.']);
        exit();
    }

    // Panggil fungsi untuk meng-upload file ke Google Drive
    $uploadResult = uploadToGoogleDrive($backupFilePath);
    echo json_encode($uploadResult);
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

function uploadToGoogleDrive($filePath)
{
    // Setup Google API Client
    $client = new Google_Client();
    $client->setAuthConfig('./json/uploads-teknoid-backup-37fd3c318c61.json'); // Path ke file kredensial
    $client->addScope(Google_Service_Drive::DRIVE);

    try {
        $service = new Google_Service_Drive($client);

        // Setup metadata file
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => basename($filePath), // Nama file di Google Drive
            'parents' => ['1tsUfTxwVwGDRpZZDTYpY9iQVRuY9BwTC'] // ID folder di Google Drive
        ]);

        // Ambil konten file
        $content = file_get_contents($filePath);

        // Upload file ke Google Drive
        $file = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => 'application/zip',
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);

        if ($file->id) {
            return ['success' => true, 'message' => 'File berhasil diunggah ke Google Drive.', 'fileId' => $file->id];
        } else {
            return ['success' => false, 'message' => 'Upload gagal, file ID tidak ditemukan.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
    }
}

// Fungsi untuk menulis log ke file
function logToFile($message)
{
    // Tentukan file log
    $logFile = 'debug_log.txt';

    // Format pesan log dengan waktu
    $logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL;

    // Tulis pesan ke file log
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
