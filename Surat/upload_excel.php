<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
include 'koneksi.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

if (isset($_FILES['file']['name'])) {
    $fileName = $_FILES['file']['tmp_name'];

    // Check if the file is Excel
    $fileType = IOFactory::identify($fileName);
    $reader = IOFactory::createReader($fileType);
    $spreadsheet = $reader->load($fileName);
    $sheetData = $spreadsheet->getActiveSheet()->toArray();

    $successCount = 0; // Counter for successful inserts
    $errorCount = 0;   // Counter for errors

    // Skip the header row
    for ($i = 1; $i < count($sheetData); $i++) {
        // Read data from each row
        $noinduk = isset($sheetData[$i][0]) ? trim($sheetData[$i][0]) : null;
        $nama_lengkap = isset($sheetData[$i][1]) ? trim($sheetData[$i][1]) : null;
        $jabatan = isset($sheetData[$i][2]) ? trim($sheetData[$i][2]) : null;
        $akses = isset($sheetData[$i][3]) ? trim($sheetData[$i][3]) : null;
        $password = isset($sheetData[$i][4]) ? trim($sheetData[$i][4]) : null;
        $email = isset($sheetData[$i][5]) ? trim($sheetData[$i][5]) : null;
        $no_telepon = isset($sheetData[$i][6]) ? trim($sheetData[$i][6]) : null;

        // Check if required fields are empty, skip the row if they are
        if (empty($noinduk) || empty($nama_lengkap) || empty($jabatan) || empty($akses) || empty($password)) {
            continue;  // Skip this row if any required field is empty
        }

        // Generate a new UUID for each row
        $id_pg = generateUUID();
        $hashedPassword = hash('sha256', $password);

        // Insert into the database
        $sql = "INSERT INTO tb_pengguna (id_pg, noinduk, nama_lengkap, jabatan, akses, password, email, no_hp)
                VALUES ('$id_pg', '$noinduk', '$nama_lengkap', '$jabatan', '$akses', '$hashedPassword', '$email', '$no_telepon')";

        if ($conn->query($sql) === TRUE) {
            $successCount++; // Increment successful inserts counter
        } else {
            $errorCount++; // Increment errors counter
        }
    }

    // Close the database connection
    $conn->close();

    // Display success message with Bootstrap and redirect back to add_user.php after 3 seconds
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <title>Upload Result</title>
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="alert alert-success text-center" role="alert">
                <h4 class="alert-heading">Upload Successful!</h4>
                <p>' . $successCount . ' user(s) added successfully.</p>';
    if ($errorCount > 0) {
        echo '<p class="text-danger">' . $errorCount . ' user(s) failed to upload.</p>';
    }
    echo '
                <hr>
                <p class="mb-0">You will be redirected back in a moment...</p>
            </div>
        </div>

        <script>
            // Redirect to add_user.php after 3 seconds
            setTimeout(function() {
                window.location.href = "add_user.php";
            }, 3000);
        </script>
    </body>
    </html>';
} else {
    echo "No file uploaded!";
}
