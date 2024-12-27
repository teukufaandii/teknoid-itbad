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
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script type="text/javascript" src="tablesorter/jquery.tablesorter.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://kit.fontawesome.com/9e9ad697fd.js" crossorigin="anonymous"></script>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    </head>
    <body>
        <!-- sidenav -->
        <?php include "sidenav.php" ?>

        <!-- content --> 
        <div class="content" id="Content">
            <!-- topnav -->
            <?php include "topnav.php" ?>
            

            <!-- cotent-->
            <div class="mainContent" id="mainContent">
                <div class="contentBox">
                    <div class="pageInfo">  
                        <h3>Manajemen Formulir</h3>
                    </div>
                    <div class="tombol">
                        <div class="tambah">
                            <a href="form_upload.php" style="color: white;"> <button>
                                <i class="fa fa-plus" style="font-family: arial"> &nbsp; Tambah Formulir</i>  </button>
                            </a>
                        </div>
                    </div>
                    <form class="form">
                    <?php
                        // Connect to database
                        $conn = mysqli_connect("localhost", "root", "", "db_teknoid");

                        // Check connection
                        if (!$conn) {
                        die("Connection failed: ". mysqli_connect_error());
                        }

                            // Delete file
                            if(isset($_GET['delete'])){
                                $id = $_GET['delete'];
                                $sql = "DELETE FROM files WHERE id='$id'";
                                if(mysqli_query($conn, $sql)){
                                    unlink('formulir/'. $_GET['file']);
                                    echo "<script>alert('File berhasil dihapus');</script>";
                                    echo "<meta http-equiv='refresh' content='3; url=manajemen_form.php'>";
                                } else {
                                    echo "<script>alert('Error: ". mysqli_error($conn) ."');</script>";
                                }
                            }

                        // Display uploaded files
                        $files = mysqli_query($conn, "SELECT * FROM files");
                        while($file = mysqli_fetch_assoc($files)){
                            echo "<div class='inputfield'> <label style='font-weight:bold'>" .$file['title']." </label> ";
                            echo "
                            <button style='padding: 10px; border-radius: 5px; background-color:#1E2287;'>
                            <a style='color:white; cursor:pointer;' href='view_file_doc.php?file=".$file['file']."'>Unduh</a>
                            </button>  &nbsp &nbsp
                            <button style='padding: 10px; border-radius: 5px; background-color:#1E2287;'>  <a style='color:white; cursor:pointer;' href='manajemen_form.php?delete=".$file['id']."&file=".$file['file']."'> Delete </a> </button> 
                            </div>";
                        }
                        
                    ?>
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