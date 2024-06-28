<?php 
session_start(); // Start the session at the beginning of the script
if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Admin') { // Check if $_SESSION['akses'] is set and equals 'Humas'
?>

<?php 
include 'koneksi.php';
include "logout-checker.php";
// Periksa apakah session username telah diatur
if (!isset($_SESSION['pengguna_type'])) {
    echo '<script language="javascript" type="text/javascript">
    alert("Anda Tidak Berhak Masuk Kehalaman Ini!");</script>';
    echo "<meta http-equiv='refresh' content='0; url=../index.php'>";
    exit;
}
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Edit Akun - Teknoid</title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/x-icon" href="../logo itbad.png">
        <link href="css/dashboard-style.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    </head>
    <body>
        <!-- sidenav -->
        <?php include "sidenav.php" ?>

        <!-- content --> 
        <div class="content" id="Content">
            <!-- topnav -->
            <?php include "topnav.php" ?>
            
            <div class="mainContent" id="mainContent">
                <div class="contentBox">
                    <div class="pageInfo">  
                        <h3>Edit Akun</h3>
                        <a href="pengaturan_akun.php">
                            <button class="back">Kembali</button>
                        </a>
                    </div>
                    <?php
                    include "koneksi.php";
                    
                    $sqln = mysqli_query($koneksi,"SELECT * FROM tb_pengguna where id_pg='$_GET[ids]' order by nama_lengkap");
                    $row = mysqli_num_rows($sqln);
                    $no=0;

                    while($rn = mysqli_fetch_array($sqln)) { ?>
                        <form class="form" method="post" action="edit_user_adminProcess.php">
                        
                            <div class="inputfield">
                                <label>Nomor Induk</label>
                                <input type="text" class="input" name="noinduk" value="<?php echo $rn["noinduk"] ;?>">
                            </div>  

                            <div class="inputfield">
                                <label>Nama Pengguna</label>
                                <input type="text" class="input" name="nama_lengkap" id="nama_lengkap" value="<?php echo $rn['nama_lengkap'];?>" required/>
                            </div>

                            <div class="inputfield">
                                <label>Jabatan</label>
                                <div class="custom_select">
                                    <select name="jabatan" id="jabatan" Required>
                                        <option><?php echo $rn['jabatan'];?></option>
                                        <option>Admin</option>
                                        <option>Rektor</option>
                                        <option>Warek 1</option>
                                        <option>Warek 2</option>
                                        <option>Warek 3</option>
                                        <option>Sekertaris</option>
                                        <option>Dekan FTD</option>
                                        <option>Dekan FEB</option>
                                        <option>Prodi S1 Sistem Informasi</option>
                                        <option>Prodi S1 Teknologi Informasi</option>
                                        <option>Prodi S1 Desain Komunikasi Visual</option>
                                        <option>Prodi S1 Arsitektur</option>
                                        <option>Prodi S1 Manajemen</option>
                                        <option>Prodi S1 Akuntansi</option>
                                        <option>Prodi D3 Akuntansi</option>
                                        <option>Prodi D3 keuangan dan perbankan Syariah</option>
                                        <option>Pasca Sarjana</option>
                                        <option>Unit Keuangan</option>
                                        <option>Unit Umum</option>
                                        <option>Unit Marketing</option>
                                        <option>Unit Akademik</option>
                                        <option>Unit Perpustakaan</option>
                                        <option>Unit LP3M</option>
                                        <option>Unit KUI dan Kerjasama</option>
                                        <option>Unit PPIK dan Kemahasiswaan</option>
                                        <option>Unit IT & Laboratorium</option>
                                        <option>Dosen</option>
                                        <option>Mahasiswa</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="inputfield">
                                <label>Hak Akses</label>
                                <div class="custom_select">
                                    <select name="akses" id="akses" required>
                                        <option><?php echo $rn['akses'];?></option>
                                        <option>Admin</option>
                                        <option>Rektor</option>
                                        <option>Warek 1</option>
                                        <option>Warek 2</option>
                                        <option>Warek 3</option>
                                        <option>Unit</option>
                                        <option>Dosen</option>
                                        <option>Unit</option>
                                        <option>Mahasiswa</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="inputfield">
                                <label>Password</label>
                                <input type="text" name="password" class="input" id="password" value="<?php echo $rn['password'];?>" required>
                            </div> 

                            <div class="inputfield">
                                <label>Email</label>
                                <input type="text" class="input" name="email" value="<?php echo $rn['email'];?>">
                            </div> 

                            <div class="inputfield">
                                <label>Nomor telepon</label>
                                <input type="number" class="input" name="no_telepon" id="no_telepon" value="<?php echo $rn['no_hp'];?>">
                            </div> 

                            <div>
                                <div class="floatFiller">ff</div>
                                <button type="submit" value="simpan" class="btn" >Simpan</button>
                            </div>
                            
                        </form> 
                    <?php } ?>
                </div>	
            </div>
            <?php include './footer.php'; ?>
        </div>
        <script src="js/dashboard-js.js"></script>
    </body>
</html>

<?php 
} else {
    include "./access-denied.php";
}
?>