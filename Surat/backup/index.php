<?php
// backup.php
require '../vendor/autoload.php';

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;

$folders = [
    '../uploads/berkas',
    '../uploads/dosen',
    '../uploads/honorium',
    '../uploads/laporan',
    '../uploads/suratMhs',
    '../uploads/suratRstDosen'
];

date_default_timezone_set('Asia/Jakarta');

$outputFile = 'backup_' . date('dmy_His') . '.zip';
$zip = new ZipArchive();

if ($zip->open($outputFile, ZipArchive::CREATE) !== TRUE) {
    die('Tidak dapat membuka file untuk membuat ZIP');
}

foreach ($folders as $folder) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if ($file->isFile()) {
            $zip->addFile($file->getRealPath(), $file->getFilename());
        }
    }
}

$zip->close();

uploadToGoogleDrive($outputFile);

function uploadToGoogleDrive($filePath)
{
    $client = new Google_Client();
    $client->setAuthConfig('./uploads-teknoid-backup-37fd3c318c61.json');
    $client->addScope(Google_Service_Drive::DRIVE);

    $service = new Google_Service_Drive($client);

    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => basename($filePath),
        'parents' => ['1tsUfTxwVwGDRpZZDTYpY9iQVRuY9BwTC']
    ]);

    $content = file_get_contents($filePath);
    $file = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => 'application/zip',
        'uploadType' => 'multipart',
        'fields' => 'id'
    ]);

    if ($file->id) {
        header('Location: ./success.php');
        exit();
    } else {
        die('Upload ke Google Drive gagal.');
    }
}
