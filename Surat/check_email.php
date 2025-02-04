<?php
header('Content-Type: application/json');
require 'koneksi.php'; 

session_start();
$user_id = $_SESSION['pengguna_id']; 

$query = "SELECT email FROM tb_pengguna WHERE id_pg = ?";
$stmt = $conn->prepare($query);

$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$response = ["email_exists" => !empty($row['email'])];

echo json_encode($response);
