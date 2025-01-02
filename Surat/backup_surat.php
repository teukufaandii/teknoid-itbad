<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Humas' || isset($_SESSION['akses']) && $_SESSION['akses'] == 'Admin') {
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
        <title>Backup Surat-Teknoid</title>
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
        <link rel="stylesheet" href="./css/disposisi-style.css">
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
                        <h3>Backup Surat</h3>
                        <?php
                        if (isset($_SESSION['akses']) && $_SESSION['akses'] === 'Humas') {
                            echo '<a href="surat_keluar.php"><button class="back">Kembali</button></a>';
                        }
                        ?>
                    </div>
                    <form method="POST" class="form" action="./sql/cek_backup.php" id="backupForm">
                        <div class="tanggal">
                            <div class="inputfield" style="margin:0; gap:20px;">
                                <label for="tanggal_awal">Tanggal Awal Surat</label>
                                <input type="date" id="tanggal_awal" name="tanggal_awal" style="margin-left:8px;" required>
                            </div>
                            <div class="inputfield" style="margin:0; gap:20px; margin-left: 10px;">
                                <label for="tanggal_akhir">Tanggal Akhir Surat</label>
                                <input type="date" id="tanggal_akhir" name="tanggal_akhir" required>
                            </div>
                        </div>
                        <input type="submit" name="backup" class="search" value="Backup">
                    </form>
                    <div id="notification" style="display:none; margin-top:20px;"></div>
                    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                        <div class="spinner"></div>
                    </div>
                    <div class="tableOverflow">
                        <table id="tablesm" class="tablesorter">
                            <thead>
                                <tr>
                                    <th onclick="sortTable(0, this)" style="min-width: 75px; border-top-left-radius: 8px;">
                                        No<i id="sort-icon-0" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i>
                                    </th>
                                    <th onclick="sortTable(1, this)">Nama Folder<i id="sort-icon-1" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                    <th onclick="sortTable(2, this)">Terakhir Backup<i id="sort-icon-2" class="fas fa-sort sort-icon" style="margin-left: 5px; border-top-right-radius: 8px;"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conn = mysqli_connect("localhost", "root", "", "db_teknoid");
                                if ($conn->connect_error) {
                                    die("Koneksi gagal: " . $conn->connect_error);
                                }

                                // Pagination setup
                                $start = 0;
                                $rows_per_page = 20;

                                // SQL query to get the number of records
                                $stmt = $conn->prepare("SELECT COUNT(*) FROM tb_backup");
                                $stmt->execute();
                                $stmt->bind_result($nr_of_rows);
                                $stmt->fetch();
                                $stmt->close();

                                // Calculate the number of pages
                                $pages = ceil($nr_of_rows / $rows_per_page);

                                // Determine the start point
                                if (isset($_GET['page-nr'])) {
                                    $page = $_GET['page-nr'] - 1;
                                    $start = $page * $rows_per_page;
                                }

                                // Query to fetch backup data
                                $stmt = $conn->prepare("SELECT * FROM tb_backup ORDER BY last_backup DESC LIMIT ?, ?");
                                $stmt->bind_param("ii", $start, $rows_per_page);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    $counter = $start + 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td style='min-width: 75px;'>" . $counter++ . "</td>";
                                        echo "<td>" . htmlspecialchars($row['folder_name'], ENT_QUOTES, 'UTF-8') . "</td>";
                                        echo "<td>" . (new DateTime($row['last_backup']))->format('d-m-Y') . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>Tidak ada hasil.</td></tr>";
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
                            <!-- first page  -->
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
            <?php include 'footer.php'; ?>
        </div>
        k
        <script>
            document.getElementById('backupForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const loadingOverlay = document.getElementById('loadingOverlay');
                loadingOverlay.style.display = 'flex';

                const formData = new FormData(this);

                fetch('./sql/cek_backup.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            return fetch('./backup/index.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    backupFilePath: data.backupFilePath
                                })
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .then(response => response.json())
                    .then(result => {
                        const notification = document.getElementById('notification');
                        notification.style.display = 'block';
                        notification.textContent = result.message;
                        notification.style.color = result.success ? 'green' : 'red';
                    })
                    .catch(error => {
                        const notification = document.getElementById('notification');
                        notification.style.display = 'block';
                        notification.textContent = error.message;
                        notification.style.color = 'red';
                    })
                    .finally(() => {
                        loadingOverlay.style.display = 'none';
                    });
            });

            // Sidebar
            function openNav() {
                var sidenavWidth = document.getElementById("mySidenav").style.width;
                if (sidenavWidth === "200px") {
                    closeNav();
                } else {
                    document.getElementById("mySidenav").style.width = "200px";
                    document.getElementById("Content").style.marginLeft = "200px";
                }
            }

            function closeNav() {
                document.getElementById("mySidenav").style.width = "0";
                document.getElementById("Content").style.marginLeft = "0";
            }
        </script>
    </body>

    </html>

<?php
} else {
    include "./access-denied.php";
}
?>