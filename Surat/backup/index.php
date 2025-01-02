<?php
require '../vendor/autoload.php';

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $backupFilePath = $data['backupFilePath'] ?? '';

    if (empty($backupFilePath)) {
        echo json_encode(['success' => false, 'message' => 'File backup tidak ditemukan.']);
        exit();
    }

    if (!file_exists($backupFilePath)) {
        echo json_encode(['success' => false, 'message' => 'File backup tidak ditemukan.']);
        exit();
    }

    $uploadResult = uploadToGoogleDrive($backupFilePath);
    echo json_encode($uploadResult);
    exit();
}

function uploadToGoogleDrive($backupFilePath)
{
    $normalizedPath = str_replace('\\', '/', $backupFilePath); // Normalize path
    if (!file_exists($normalizedPath)) {
        return ['success' => false, 'message' => 'File backup tidak ditemukan.'];
    }

    $client = new Google_Client();
    $client->setAuthConfig('./json/uploads-teknoid-backup-37fd3c318c61.json');
    $client->addScope(Google_Service_Drive::DRIVE);

    try {
        $service = new Google_Service_Drive($client);

        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => basename($normalizedPath),
            'parents' => ['1tsUfTxwVwGDRpZZDTYpY9iQVRuY9BwTC']
        ]);

        $content = file_get_contents($normalizedPath);

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
