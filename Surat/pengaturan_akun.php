<?php 
session_start(); // Start the session at the beginning of the script
if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Admin' || $_SESSION['akses'] == 'Humas') {
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
        <title>Pengaturan Akun - Teknoid</title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/x-icon" href="../logo itbad.png">
        <link href="css/dashboard-style.css" rel="stylesheet">
        <!-- ajax live search -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script type="text/javascript" src="tablesorter/jquery.tablesorter.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://kit.fontawesome.com/9e9ad697fd.js" crossorigin="anonymous"></script>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    </head>

    <body></body>
    <!-- sidenav -->
    <?php include "sidenav.php" ?>

    <!-- content -->
    <div class="content" id="Content">

        <!-- topnav -->
        <?php include "topnav.php" ?>

        <div class="mainContent" id="mainContent">
            <div class="contentBox">
                <div class="pageInfo">
                    <h3>Pengaturan Akun</h3>
                </div>
                <div class="tombol">
                    <div class="tambah">
                        <a href="add_user.php"><button>
                                <i class="fa fa-plus"></i>&nbsp; Tambah Akun
                            </button></a>

                        <a href="formulir/FormatAddUser.xlsx" download>
                            <button class="btn">
                               <i class="fa-solid fa-file-arrow-down"></i>&nbsp; Form Tambah Akun</button>
                        </a>
                    </div>
                    <div class="search-box">
                        <form method="GET">
                            <input type="text" placeholder="Search.." name="search" id="search">
                        </form>
                    </div>
                </div>

                <div class="tableOverflow">
                    <table id="tableakun" class="tablesorter">
                        <thead>
                            <tr>
                                <th style="min-width: 75px;">No <i class="fas fa-sort"></th>
                                <th>NIM/NIDN/NIP <i class="fas fa-sort"></th>
                                <th>Nama Lengkap <i class="fas fa-sort"></th>
                                <th>Jabatan <i class="fas fa-sort"></th>
                                <th>Hak Akses <i class="fas fa-sort"></th>
                                <th>No Telepon <i class="fas fa-sort"></th>
                                <th>Email <i class="fas fa-sort"></th>
                                <th>Aksi <i class="fas fa-sort"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = mysqli_connect("localhost", "root", "", "db_teknoid");
                            if ($conn->connect_error) {
                            }

                            // pengaturan baris
                            $start = 0;
                            $rows_per_page = 20;

                            // total nomor baris
                            $records = mysqli_query($conn, "SELECT * FROM tb_pengguna");
                            $nr_of_rows = $records->num_rows;

                            // kalkulasi nomor per halaman
                            $pages = ceil($nr_of_rows / $rows_per_page);

                            // start point
                            if (isset($_GET['page-nr'])) {
                                $page = $_GET['page-nr'] - 1;
                                $start = $page * $rows_per_page;
                            }


                            // tabel db suratmasuk
                            $stmt = $conn->prepare("SELECT * FROM  tb_pengguna LIMIT $start, $rows_per_page");
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $counter = $start + 1;
                                while ($row = $result->fetch_assoc()) {
                            ?>

                                    <tr>
                                        <td th style="min-width: 75px;"><?php echo $counter++; ?></td>
                                        <td><?php echo $row["noinduk"]; ?></td>
                                        <td><?php echo $row["nama_lengkap"]; ?></td>
                                        <td><?php echo $row["jabatan"]; ?></td>
                                        <td><?php echo $row["akses"]; ?></td>
                                        <td><?php echo $row["no_hp"]; ?></td>
                                        <td><?php echo $row["email"]; ?></td>
                                        <td style="text-align: center;">
                                            <div style="margin-right: 45px;">
                                                <a href="edit_user_admin.php?ids=<?php echo $row['id_pg']; ?>">
                                                    <button class="btnEdit" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </a>
                                            </div>
                                            <div style="margin-top: -35px; margin-left: 45px;">
                                                <!-- Button to open the modal -->
                                                <button class="btnDel" title="Delete" onclick="openConfirmationModal('<?php echo $row['id_pg']; ?>')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Confirmation Modal -->
                                    <div id="confirmationModal" class="modal">
                                        <div class="modal-box">
                                            <span onclick="closeConfirmationModal()" class="close" title="Close Modal">×</span>
                                            <form id="id01" class="modal-content" action="#" method="GET">
                                                <div class="container">
                                                    <h1>Hapus Pengguna</h1>
                                                    <p>Apa kamu yakin ingin menghapus Pengguna?</p>
                                                    <input type="hidden" id="userIdToDelete" name="id_pg" value="<?php echo $row['id_pg']; ?>">
                                                    <div class="clearfix">
                                                        <button type="button" onclick="closeConfirmationModal()" class="cancelbtn" style="cursor: pointer;">Batal</button>
                                                        <button type="button" onclick="submitDelete()" class="deletebtn" style="cursor: pointer;">Hapus</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='9'>0 results</td></tr>";
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                if (isset($_GET['page-nr'])) {
                    $id = $_GET['page-nr'];
                } else {
                    $id = 1;
                }
                ?>
                <div id="kontenhalaman" id="<?php echo $id ?>">
                    <!-- efek -->
                    <div class="tekspage">
                        <?php
                        if (!isset($_GET['page-nr'])) {
                            $page = 1;
                        } else {
                            $page = $_GET['page-nr'];
                        }
                        ?>
                        Showing <?php echo $page ?> of <?php echo $pages ?> pages
                    </div>
                    <div class="pagination">
                        <!-- First page -->
                        <a href="?page-nr=1"><span class="fas fa-angle-double-left"></span></a>

                        <!-- Previous page -->
                        <?php if ($page > 1) : ?>
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
                        <?php if ($page < $pages) : ?>
                            <a href="?page-nr=<?php echo $page + 1 ?>"><span class="fas fa-angle-right"></span></a>
                        <?php endif; ?>

                        <!-- Last page -->
                        <a href="?page-nr=<?php echo $pages ?>"><span class="fas fa-angle-double-right"></span></a>
                    </div>



                </div>
            </div>
        </div>
        <?php include './footer.php'; ?>
    </div>
    <script src="js/dashboard-js.js"></script>
    <script>
        // Document Ready
        $(document).ready(function() {
            // Pencarian
            $("#search").keyup(function() {
                var search = $(this).val();
                $.ajax({
                    url: 'ajax/searchPengguna.php',
                    method: 'POST',
                    data: {
                        query: search
                    },
                    success: function(response) {
                        $("#tableakun tbody").html(response);
                    }
                });
            });

            // Sorting
            $("#tableakun").tablesorter();

            // Efek page number
            let links = document.querySelectorAll('.pageNumber a');
            let id = parseInt("<?php echo $id ?>");
            let pageNumberContainer = document.querySelector('.pageNumber');
            if (!isNaN(id)) {
                let adjustedId = id - 1;
                if (adjustedId >= 0 && adjustedId < links.length) {
                    links[adjustedId].classList.add("active");
                } else {
                    console.error("Invalid page number:", id);
                }
            } else {
                console.error("Invalid ID:", id);
            }
        });

        // Fungsi Modal Konfirmasi
        function openConfirmationModal(userId) {
            document.getElementById('userIdToDelete').value = userId;
            document.getElementById('confirmationModal').style.display = 'block';
        }

        function closeConfirmationModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }

        function submitDelete() {
            var userId = document.getElementById('userIdToDelete').value;
            window.location.href = 'user_deleteProcess.php?id_pg=' + userId;
        }
    </script>
    </body>

    </html>

<?php
} else {
    include "./access-denied.php";
}
?>