<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_surat = $_POST["id_surat"];
    
    $sql = "SELECT dispo1, dispo2, dispo3, dispo4, dispo5 FROM tb_disposisi WHERE id_surat = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id_surat);
    $stmt->execute();
    $stmt->bind_result($dispo1, $dispo2, $dispo3, $dispo4, $dispo5);
    $stmt->fetch();
    $stmt->close();
    
    $jabatan = $_SESSION['jabatan'];
    
    if ($dispo1 == $jabatan || $dispo2 == $jabatan || $dispo3 == $jabatan || $dispo4 == $jabatan || $dispo5 == $jabatan) {
        echo "denied";
    } else {
        echo "allowed";
    }
}
?>
