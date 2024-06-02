<?php
    // Get file name from query string
    $fileName = $_GET['file'];

    // Set the header to force download
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"".$fileName."\"");

    // Read the file and output the contents
    readfile("formulir/".$fileName);
?>