<?php 
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
include "logout-checker.php";
include 'koneksi.php';
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
        <title>Download Formulir - Teknoid</title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/x-icon" href="../logo itbad.png">
        <link href="css/dashboard-style.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <style> 

        </style>
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
                        <h3>Download Formulir</h3>
                        <a href="dashboard.php">
                            <button class="back">Kembali</button>
                        </a>
                    </div>

                    <form class="form">

                    <?php
                            // Connect to database
                            $conn = mysqli_connect("localhost", "root", "", "db_teknoid");

                            // Check connection
                            if (!$conn) {
                            die("Connection failed: ". mysqli_connect_error());
                            }

                           // Display uploaded files
                            $files = mysqli_query($conn, "SELECT * FROM files");
                            while($file = mysqli_fetch_assoc($files)){
                                echo "<div class='inputfield' style='margin-bottom:30px;'> <label>" .$file['title']." </label> ";
                                echo "<a  style='color:white; cursor:pointer;' href='download.php?file=".$file['file']."'> <button style='border-radius:5px; padding:10px; background-color: #1E2287;'> Download  </a> </button>  </div>";
                            }

                            // Download file
                            if(isset($_GET['file'])){
                                $file = $_GET['file'];
                                $filePath = 'formulir/'.$file;
                                if(file_exists($filePath)){
                                }else{
                                    echo "<script>alert('The file does not exist!')</script>";
                                }
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
    $conn->close();
?>