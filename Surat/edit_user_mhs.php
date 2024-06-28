<?php 
session_start();
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    </div>
                    <?php
                    include "koneksi.php";

                    $sqln = mysqli_query($koneksi,"SELECT * FROM tb_pengguna where noinduk='$_GET[ids]' order by nama_lengkap");
                    $row = mysqli_num_rows($sqln);
                    $no=0;

                    while($rn = mysqli_fetch_array($sqln)) { ?>
                        <form class="form" id="form" method="post" action="edit_user_mhsProcess.php">
                        
                            <div class="inputfield">
                                <label>Password</label>
                                <input type="text" name="password" class="input" id="password" value="<?php echo $rn['password'];?>" 
                                autocomplete="off" required>
                            </div> 

                            <div class="inputfield">
                                <label>Email</label>
                                <input type="email" class="input" name="email" value="<?php echo $rn['email'];?>" maxlength="254" 
                                pattern="^(?![_.-])((?![_.-][_.-])[a-zA-Z\d_.-]){0,63}[a-zA-Z\d]@((?!-)((?!--)[a-zA-Z\d-]){0,63}[a-zA-Z\d]\.){1,2}([a-zA-Z]{2,14}\.)?[a-zA-Z]{2,14}$" 
                                autocomplete="on" spellcheck="false" autocorrect="off" placeholder="contoh@gmail.com" inputmode="email" 
                                required>
                            </div> 

                            <div class="inputfield">
                                <label>Nomor telepon</label>
                                <input type="number" class="input" name="no_hp" id="no_telepon" value="<?php echo $rn['no_hp'];?>" 
                                autocomplete="off" required>
                            </div> 

                            <div>
                                <div class="floatFiller">ff</div>
                                <button type="submit" class="btn" id="SubmitButton">Simpan</button>
                            </div>
                            
                        </form> 
                    <?php } ?>
                </div>	
            </div>
            <?php include './footer.php'; ?>
        </div>

        <script src="js/dashboard-js.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
      <!--  <script src="js/dashboard-js.js"></script>
          <script>
            function confirmSave() {
            Swal.fire({
                title: 'Simpan Data?',
                text: "Anda yakin ingin menyimpan data?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form jika tombol "Ya, Simpan!" ditekan
                    document.getElementById("form").submit();
                }
            });
        }
        </script> -->
    </body>
</html>