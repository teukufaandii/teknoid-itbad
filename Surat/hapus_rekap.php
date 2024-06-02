<?php
    var_dump($_POST);

    if (isset($_POST['delete'])) {
    
        include("koneksi.php");
    
        $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    
        $sql = "DELETE FROM tb_surat_dis WHERE kode_surat = '".$id."'";
    
        if (mysqli_query($koneksi, $sql))
        {
            echo "Deleted";
        }
    } 
