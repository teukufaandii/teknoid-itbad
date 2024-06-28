<?php 
session_start();
include 'koneksi.php';
include "logout-checker.php";
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
        <title>Surat Keluar - Teknoid</title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/x-icon" href="../logo itbad.png">
        <link href="css/dashboard-style.css" rel="stylesheet">
        <!-- ajax live search --><script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script type="text/javascript" src="tablesorter/jquery.tablesorter.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>

<!-- Modal konfirmasi -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeConfirmationModal()">&times;</span>
            <button onclick="downloadForm()">Download Form </button> <br> 
            <button onclick="buatSuratDisposisi()">Tambah Surat Disposisi</button> <br>
            <button onclick="buatSuratNonDisposisi()">Tambah Surat Non Disposisi</button><br>
    </div>
</div>

<!-- sidenav -->
<?php include "sidenav.php" ?>

    <!-- content --> 
    <div class="content" id="Content">

            <!-- topnav -->
        <?php include "topnav.php" ?>
            
            <div class="mainContent" id="mainContent">
                <div class="contentBox">
                    <div class="pageInfo">  
                        <h3>Surat Keluar</h3>
                    </div>
                    <div class="tombol">
                        <div class="tambah">
                            <button onclick="confirmAddSurat()">
                                <i class="fa fa-plus"> &nbsp; Tambah Surat</i>
                            </button>
                            <button onclick="downloadForm()">
                                <i class="fas fa-download"> &nbsp; Download Form</i>
                            </button>
                        </div>
                        
                        <div class="search-box">
                            <form method="GET">
                                <input type="text" placeholder="Search.." name="search" id="search">
                            </form>
                        </div>
                    </div>
                    
                    <div class="tableOverflow">
                        <table id="tablesk" class="tablesorter">
                            <thead>
                                <tr>
                                    <th style="min-width: 75px;">No <i class="fas fa-sort"></i></th>
                                    <th>Kode Surat <i class="fas fa-sort"></i></th>
                                    <th>Asal Surat <i class="fas fa-sort"></i></th>
                                    <th>Perihal <i class="fas fa-sort"></i></th>
                                    <th>Tanggal Surat <i class="fas fa-sort"></i></th>
                                    <th>Status <i class="fas fa-sort"></i></th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $conn = mysqli_connect("localhost", "root", "", "db_teknoid");
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }
                                    
                                    // pengaturan baris
                                    $start = 0;
                                    $rows_per_page = 10;
                                    $fullname = $_SESSION['nama_lengkap'];
                                    
                                    // total nomor baris
                                    $records = mysqli_query($conn, "SELECT sd.id_surat, sd.kode_surat, sd.kd_surat, sd.asal_surat,
                                    sd.perihal, sd.diteruskan_ke, sd.status_baca, sd.status_tolak, sd.status_selesai,
                                    d.dispo1, d.dispo2, d.dispo3, d.dispo4, d.dispo5
                                    FROM tb_surat_dis sd
                                    LEFT JOIN tb_disposisi d ON sd.id_surat = d.id_surat
                                    WHERE sd.asal_surat = '$fullname' ");
                                    
                                    $nr_of_rows = $records->num_rows;
                                    
                                    // kalkulasi nomor per halaman
                                    $pages = ceil($nr_of_rows / $rows_per_page);
                                    
                                    // start point
                                    if(isset($_GET['page-nr'])){
                                        $page = $_GET['page-nr'] - 1;
                                        $start = $page * $rows_per_page;
                                    }
                                    
                                    // tabel db surat
                                    $stmt=$conn->prepare("SELECT sd.id_surat, sd.kode_surat, sd.kd_surat, sd.asal_surat,
                                    sd.tanggal_surat, sd.perihal, sd.diteruskan_ke, sd.status_baca, sd.status_tolak,
                                    sd.status_selesai, d.dispo1, d.dispo2, d.dispo3, d.dispo4, d.dispo5,
                                    d.catatan_selesai, d.catatan_tolak, d.nama_penolak, d.nama_selesai
                                    FROM tb_surat_dis sd
                                    LEFT JOIN tb_disposisi d ON sd.id_surat = d.id_surat
                                    WHERE sd.asal_surat = '$fullname' 
                                    LIMIT ?, ?");
                                    $stmt->bind_param("ii", $start, $rows_per_page);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    
                                    if ($result->num_rows > 0) {
                                        $counter = $start + 1;
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                    <?php
                                        echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                        echo "<td>" . (!empty($row['kode_surat']) ? $row['kode_surat'] : $row['kd_surat']) . "</td>";
                                        echo "<td>" . $row['asal_surat'] . "</td>";
                                        echo "<td>" . $row['perihal'] . "</td>";
                                        echo "<td>" . $row['tanggal_surat'] . "</td>";
                                        echo "<td>";
                                    
                                        if ($row['status_selesai']) {
                                            $tooltipText = addslashes($row['catatan_selesai'] . "<br><span style='text-align: center'>Diterima oleh : </span><strong>" . $row['nama_selesai'] . "</strong>");
                                            echo '<span class="status-notification"><span class="status" onclick="showStatusModal(\'' . $tooltipText . '\', \'Selesai\')"><span style="background-color: green; color: white; padding: 5px; border-radius: 5px; display: block; width: 130px; margin: auto; cursor: pointer;">Selesai</span></span><span class="notification-dot"></span></span>';
                                        } elseif ($row['status_tolak']) {
                                            $tooltipText = addslashes($row['catatan_tolak'] . "<br><span style='text-align: center'>Ditolak oleh : </span><strong>" . $row['nama_penolak'] . "</strong>");
                                            echo '<span class="status-notification"><span class="status" onclick="showStatusModal(\'' . $tooltipText . '\', \'Ditolak\')"><span style="background-color: red; color: white; padding: 5px; border-radius: 5px; display: block; width: 130px; margin: auto; cursor: pointer;">Ditolak</span></span><span class="notification-dot"></span></span>';
                                        } elseif ($row['status_baca'] == 1) {
                                            echo '<span style="background-color: #ff7f00; color: white; padding: 5px; border-radius: 5px; display: block; width: 130px; margin: auto;">Diproses</span>';
                                        } else {
                                            echo '<span style="background-color: #a9a9a9; color: white; padding: 5px; border-radius: 5px; display: block; width: 130px; margin: auto;">Belum Disposisi</span>';
                                        }
                                        echo "</td>";
                                        echo "<td><a href='lacak.php?id=" . $row['id_surat'] . "' class='btnLihat'>Lihat</a></td>";
                                    ?>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>0 results</td></tr>";
                                    }
                                $conn->close();
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                        if(isset($_GET['page-nr'])){
                            $id = $_GET['page-nr'];
                        }else{
                            $id = 1;
                        }
                    ?>
                    <div id="kontenhalaman" id="<?php echo $id ?>">
                            <!-- efek -->
                        <div class="tekspage">
                            <?php
                                if(!isset($_GET['page-nr'])){
                                    $page = 1;
                                }else{
                                    $page = $_GET['page-nr'];
                                }
                            ?>
                                    Showing <?php echo $page ?> of <?php echo $pages ?> pages
                        </div>
                        <div class="pagination">
                            <!-- first page  -->
                            <a href="?page-nr=1"><span class="fas fa-angle-double-left"></span></a>
                            <!-- Previous page -->
                            <?php if($page > 1): ?>
                                    <a href="?page-nr=<?php echo $page - 1 ?>"><span class="fas fa-angle-left"></span></a>
                            <?php endif; ?>          
                            <!-- Page numbers -->
                            <div class="pageNumber">
                                <?php
                                // Calculate start and end page numbers to display
                                $startPage = max(1, $page - 2);
                                $endPage = min($pages, $startPage + 4);

                                // Calculate the total number of pages to show
                                $totalPagesToShow = min(5, $pages);

                                // Adjust the start page if less than the maximum number of pages to show
                                if ($pages - $startPage + 1 < $totalPagesToShow) {
                                    $startPage = max(1, $pages - $totalPagesToShow + 1);
                                }

                                // Display page numbers
                                for ($counter = $startPage; $counter <= $endPage; $counter++) {
                                    echo '<a ' . ($counter === $page ? 'class="active"' : '') . ' href="?page-nr=' . $counter . '">' . $counter . '</a>';
                                }
                                ?>
                            </div>
                            <!-- Next page -->
                            <?php if($page < $pages): ?>
                                <a class="paging" href="?page-nr=<?php echo $page + 1 ?>"><span class="fas fa-angle-right"></span></a>
                            <?php endif; ?>

                            <!-- Last page -->
                            <a href="?page-nr=<?php echo $pages ?>"><span class="fas fa-angle-double-right"></span></a>
                        </div>
                    </div>
                </div>
            </div>

            <?php include "./footer.php" ?>
        </div>

        <script type="text/javascript">
            // pencarian
            $(document).ready(function(){
                $("#search").keyup(function(){
                    var search = $(this).val();
                    $.ajax({
                        url:'ajax/searchSk.php',
                        method:'POST',
                        data:{query:search},
                        success:function(response){
                            $("#tablesk").html(response);
                        }
                    });
                });
            });
        </script>
        <script>
            // efek page number //
            let links = document.querySelectorAll('.pageNumber a');
            let id = parseInt("<?php echo $id ?>");
            let pageNumberContainer = document.querySelector('.pageNumber');
            if (!isNaN(id)) {
                links[id - 1].classList.add("active");
            } else {
                console.error("ID tidak valid:", id);
            }

            function showStatusModal(message, status) {
                Swal.fire({
                    title: status,
                    html: message,
                    icon: status === 'Selesai' ? 'success' : 'error',
                    focusConfirm: false,
                    confirmButtonText: 'Oke'
                });
            }
        </script>

        <script>
            // sorting //
            $(document).ready(function() {
                $("#tablesk").tablesorter();
                }
            );
        </script>

        <script>
            // Add an event listener to the closest static parent element of the tooltips
            document.querySelector('.tableOverflow').addEventListener('mouseover', function(event) {
                // Check if the mouseover event was triggered on a tooltip
                if (event.target.classList.contains('tooltip')) {
                    var tooltip = event.target;
                    var tooltipText = tooltip.querySelector('.tooltiptext');
                    var tooltipRect = tooltipText.getBoundingClientRect();
                    var tableRect = tooltip.closest('.tableOverflow').getBoundingClientRect();

                    if (tooltipRect.top < tableRect.top) {
                        tooltipText.style.top = '100%';
                        tooltipText.style.bottom = 'auto';
                    } else if (tooltipRect.bottom > tableRect.bottom) {
                        tooltipText.style.top = 'auto';
                        tooltipText.style.bottom = '100%';
                    } else {
                        tooltipText.style.top = '-190%';
                        tooltipText.style.bottom = 'auto';
                    }
                }
            });



            function confirmAddSurat() {
                Swal.fire({
                    title: 'Tambah Surat',
                    text: 'Pilih jenis surat yang ingin Anda tambahkan',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Surat Disposisi',
                    cancelButtonText: 'Surat Non-Disposisi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to tambah surat disposisi page
                        window.location.href = "tambah_surat2.php";
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Redirect to tambah surat non-disposisi page
                        window.location.href = "tambahsurat_nondispo.php";
                    }
                });
            }

            function downloadForm(){
                window.location.href = "download_form.php";
            }
        </script>
    <script src="js/dashboard-js.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    </body>
</html>