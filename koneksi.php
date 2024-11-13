<?php
$host = "localhost";
$username = "teknoid1_admin";
$password = "RadKrwY8qt3v";
$dbname = "teknoid1_db_teknoid";

$koneksi = new mysqli(
    hostname: $host,
    username: $username,
    password: $password,
    database: $dbname
);

if ($koneksi->connect_error) {
    die("Connection error: " . $koneksi->connect_error);
}

return $koneksi;
