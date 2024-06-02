<?php 
$host = "localhost";
$username = "root";
$password = "";
$dbname = "db_teknoid";

$koneksi = new mysqli(hostname: $host,
                     username: $username,
                     password: $password,
                     database: $dbname);
                     
if ($koneksi->connect_error) {
    die("Connection error: " . $koneksi->connect_error);
}

return $koneksi;

