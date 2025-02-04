<?php
// Connect to database
include 'koneksi.php';

// Download file
if(isset($_GET['file'])){
    $file = $_GET['file'];
    $filePath = 'formulir/'.$file;
    if(file_exists($filePath)){
        header("Content-Type: application/doc");
        header("Content-Disposition: attachment; filename=".$file);
        readfile($filePath);
    }else{
        echo "The file does not exist!";
    }
}
?>