<?php 
session_start(); 
include __DIR__ . '/../Maintenance/Middleware/index.php';
include "logout-checker.php";
if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Admin') { // Check if $_SESSION['akses'] is set and equals 'Humas'
?>

<?php 
include 'koneksi.php';
// Periksaaaa apakah session username telah diatur
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
        <title>Manajemen Formulir - Teknoid</title>
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
                        <h3>Tambah Formulir</h3>
                        <a style="color: white;" href="manajemen_form.php" > <button>  Kembali </button> </a>
                    </div>
                    <form method="post" action="upload.php" class="form" enctype="multipart/form-data">                    
                        <div class="inputfield">
                            <label for="">Judul</label>
                            <input type="text" class="input"  name="title" placeholder="Masukkan Judul" required autocomplete="off" required>
                        </div> 

                        <div class="inputfield">
                            <label>Input File <br><span style="color: red;"> (DOC/DOCX) </span> </label>
                            <input type="file" style="border: none;"  class="input" name="file_form" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/msword">
                        </div>

                        <div class="btn-kirim">
                            <div class="floatFiller">ff</div>
                            <input class="btn" type="submit" name="upload" value="Kirim">
                        </div>
                    </form>
                </div>
            </div>
            <?php include './footer.php'; ?>
        </div>
        <script src="js/dashboard-js.js"></script>
        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    </body>
</html>
<?php 
} else {
    include "./access-denied.php";
}
?>