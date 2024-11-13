<?php
// Connect to database
$conn = mysqli_connect("localhost", "teknoid1_admin", "RadKrwY8qt3v", "teknoid1_db_teknoid");

// Check connection
if (!$conn) {
  die("Connection failed: ". mysqli_connect_error());
}

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