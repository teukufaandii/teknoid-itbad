<?php
session_start();
$isProdi = in_array($_SESSION['jabatan'], ['S2 Keuangan Syariah', 'S1 SI', 'S1 TI', 'S1 DKV', 'S1 Arsitektur', 'S1 Manajemen', 'S1 Akuntansi']);
if ($isProdi) {
?>

    <?php
    include 'koneksi.php';
    include "logout-checker.php";
    // Periksaaaa apakah session username telah diatur
    if (!isset($_SESSION['pengguna_type'])) {
        echo '<script language="javascript" type="text/javascript">
    alert("Anda Tidak Berhak Masuk Kehalaman Ini!");</script>';
        echo "<meta http-equiv='refresh' content='0; url=../index.php'>";
        exit;
    }

    $prodi_alias = array(
        'S1 TI' => 'Prodi S1 Teknologi Informasi',
        'S1 SI' => 'Prodi S1 Sistem Informasi',
        'S1 DKV' => 'Prodi S1 Desain Komunikasi Visual',
        'S1 Arsitektur' => 'Prodi S1 Arsitektur',
        'S1 Manajemen' => 'Prodi S1 Manajemen',
        'S1 Akuntansi' => 'Prodi S1 Akuntansi',
        'S2 Keuangan Syariah' => 'Prodi S2 Keuangan Syariah',
    );

    // Ambil nilai dari session atau default kosong
    $asal_surat = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : '';

    // Menggunakan alias jika ada, jika tidak, tampilkan nilai asli
    $alias = isset($prodi_alias[$asal_surat]) ? $prodi_alias[$asal_surat] : $asal_surat;
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
        <!-- ajax live search -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script type="text/javascript" src="tablesorter/jquery.tablesorter.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            /* Style for the modal */
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.8);
                animation: fadeIn 0.5s ease;
            }

            /* Animation for fade-in effect */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            /* Modal content */
            .modal-content-file {
                background-color: #fefefe;
                margin: 10% auto;
                padding: 20px;
                border-radius: 10px;
                width: 80%;
                max-width: 800px;
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
                animation: slideIn 0.5s ease;
            }

            /* Animation for slide-in effect */
            @keyframes slideIn {
                from {
                    transform: translateY(-50px);
                }

                to {
                    transform: translateY(0);
                }
            }

            .modal-content-file h2 {
                color: #333;
                font-size: 24px;
                text-align: center;
                margin-bottom: 20px;
            }

            .close {
                color: #fff;
                float: right;
                font-size: 30px;
                font-weight: bold;
                margin-top: -10px;
                cursor: pointer;
                transition: color 0.3s ease;
            }

            .close:hover {
                color: #ff6b6b;
            }

            #berkasFrame {
                width: 100%;
                height: 60vh;
                border-radius: 5px;
                border: 2px solid #1b5ebe;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                transition: transform 0.3s ease;
            }

            #berkasFrame:hover {
                transform: scale(1.02);
            }

            /* Responsive styling */
            @media (max-width: 600px) {
                .modal-content-file {
                    width: 90%;
                    height: 70vh;
                }

                #berkasFrame {
                    height: 60vh;
                }

                .close {
                    font-size: 25px;
                }
            }
        </style>
    </head>

    <body>

        <!-- Modal konfirmasi -->
        <div id="confirmationModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeConfirmationModal()">&times;</span>
                <button onclick="downloadForm()">Download Form</button> <br>
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
                        <h3>Surat Keluar Honorium</h3>
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
                                <input type="text" placeholder="Search..." name="search" id="search">
                            </form>
                        </div>
                    </div>

                    <div class="tableOverflow" id="table-container">
                        <table id="tablesk" class="tablesorter">
                            <thead>
                                <tr>
                                    <th onclick="sortTable(0, this)" style="min-width: 75px; border-top-left-radius: 8px;">No<i id="sort-icon-0" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                    <th onclick="sortTable(1, this)">Jenis Surat<i id="sort-icon-1" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                    <th onclick="sortTable(2, this)">Asal Surat<i id="sort-icon-2" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                    <th onclick="sortTable(3, this)">Nama Kegiatan<i id="sort-icon-3" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                    <th>Tanggal Surat</th>
                                    <th>Status</th>
                                    <th style="border-top-right-radius: 8px;">Berkas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conn = mysqli_connect("localhost", "root", "", "db_teknoid");
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                // Pengaturan baris
                                $start = 0;
                                $rows_per_page = 20;
                                $fullname = $_SESSION['nama_lengkap'];

                                // Dapatkan parameter sort dari URL (default adalah 'id' dengan 'asc')
                                $sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'id';
                                $sort_order = isset($_GET['order']) ? $_GET['order'] : 'asc';

                                // Validasi parameter sort
                                $valid_columns = ['id', 'asal_surat', 'jenis_surat', 'nm_kegiatan', 'tanggal_surat', 'diteruskan_ke', 'status', 'berkas'];
                                if (!in_array($sort_column, $valid_columns)) {
                                    $sort_column = 'id';
                                }
                                if ($sort_order != 'asc' && $sort_order != 'desc') {
                                    $sort_order == 'desc';
                                }

                                // Total nomor baris
                                $records = mysqli_query($conn, "SELECT * FROM tb_srt_honor WHERE asal_surat = '$fullname'");
                                $nr_of_rows = $records->num_rows;

                                // Kalkulasi nomor per halaman
                                $pages = ceil($nr_of_rows / $rows_per_page);

                                // Start point
                                if (isset($_GET['page-nr'])) {
                                    $page = $_GET['page-nr'] - 1;
                                    $start = $page * $rows_per_page;
                                }

                                // Query dengan LIMIT, OFFSET, dan ORDER BY
                                $sql = "SELECT * FROM tb_srt_honor WHERE asal_surat = '$fullname' ORDER BY $sort_column $sort_order LIMIT $start, $rows_per_page";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    $counter = $start + 1;
                                    while ($row = $result->fetch_assoc()) {
                                ?>
                                        <tr>
                                            <td style="min-width: 75px;"><?php echo $counter++; ?></td>
                                            <td><?php echo ($row['jenis_surat'] == 7) ? "Surat Honorium" : "Data Tidak Tersedia"; ?></td>
                                            <td><?php echo htmlspecialchars($alias); ?></td>
                                            <td><?php echo !empty($row['nm_kegiatan']) ? ucwords($row['nm_kegiatan']) : '-'; ?></td>
                                            <td><?php echo isset($row['tanggal_surat']) ? (new DateTime($row['tanggal_surat']))->format('d-m-Y') : ''; ?></td>
                                            <td>
                                                <?php
                                                if ($row['status'] == 1) {
                                                    echo '<i class="fas fa-check-square" style="background-color: white; color: green;"></i> Terverifikasi';
                                                } else {
                                                    echo ' Belum Diverifikasi';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="input-field">
                                                    <label></label>
                                                    <div class="input" style="color: black; text-align: center; background-color: rgba(0, 0, 0, 0); border: none">
                                                        <div class="lihat">
                                                            <?php
                                                            if (!empty($row['berkas'])) {
                                                                $filePath = 'uploads/honorium/' . htmlspecialchars($row['berkas']);
                                                                echo '<i class="fas fa-eye" style="background-color: white; color: #1b5ebe; cursor: pointer;" onclick="lihatBerkas(\'' . $filePath . '\')"></i>';
                                                            } else {
                                                                echo 'Tidak ada berkas';
                                                            }
                                                            ?>
                                                            <div id="modalBerkas" class="modal">
                                                                <span class="close" onclick="closeModal()">&times;</span>
                                                                <div class="modal-content-file">
                                                                    <h2>PREVIEW BERKAS HONORIUM</h2>
                                                                    <iframe id="berkasFrame" frameborder="0"></iframe>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
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
            $(document).ready(function() {
                $("#search").keyup(function() {
                    var search = $(this).val();
                    $.ajax({
                        url: 'ajax/searchSkHonor.php',
                        method: 'POST',
                        data: {
                            query: search
                        },
                        success: function(response) {
                            $("#tablesk").html(response);
                        }
                    });
                });
            });

            // Function to show memo content in SweetAlert2 popup
            function showMemo(memoContent) {
                Swal.fire({
                    title: 'Memo',
                    text: memoContent,
                    icon: 'info',
                    confirmButtonText: 'Oke'
                });
            }
        </script>

        <script>
            function lihatBerkas(filePath) {
                document.getElementById("berkasFrame").src = filePath;
                document.getElementById("modalBerkas").style.display = "block";
            }

            function closeModal() {
                document.getElementById("modalBerkas").style.display = "none";
            }
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
            });
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
                let hakAkses = "<?php echo $_SESSION['jabatan']; ?>"; // assume you have stored the user's access level in a session variable

                if (hakAkses === 'Karyawan' || hakAkses === 'Bagian Kepegawaian') {
                    window.location.href = "tambah_surat2.php";

                } else if (hakAkses === 'Dosen') {
                    window.location.href = "tambah_surat_dosen.php";
                } else if (hakAkses === 'S2 Keuangan Syariah' || hakAkses === 'S1 SI' ||
                    hakAkses === 'S1 TI' || hakAkses === 'S1 DKV' || hakAkses === 'S1 Arsitektur' ||
                    hakAkses === 'S1 Manajemen' || hakAkses === 'S1 Akuntansi') {
                    window.location.href = "tambah_surat_honorium";
                } else
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
                            window.location.href = "tambahsurat_nondispo";
                        }
                    });
            }

            function downloadForm() {
                window.location.href = "download_form";
            }

            let sortDirection = {}; // To keep track of the sort direction for each column

            function sortTable(columnIndex, header) {
                const table = document.querySelector('table tbody');
                const rows = Array.from(table.querySelectorAll('tr'));
                const isDescending = sortDirection[columnIndex] || false; // Get the current sort direction for this column

                // Sort rows
                rows.sort((rowA, rowB) => {
                    const cellA = rowA.children[columnIndex].textContent.trim();
                    const cellB = rowB.children[columnIndex].textContent.trim();

                    if (columnIndex === 0) { // Special case for the "No" column
                        return isDescending ? cellA - cellB : cellB - cellA;
                    } else {
                        if (isDescending) {
                            return cellA.localeCompare(cellB);
                        } else {
                            return cellB.localeCompare(cellA);
                        }
                    }
                });

                // Update table
                rows.forEach(row => table.appendChild(row));

                // Toggle sort direction
                sortDirection[columnIndex] = !isDescending;

                // Update sort icons
                document.querySelectorAll('.sort-icon').forEach(icon => {
                    icon.classList.remove('fa-sort', 'fa-sort-up', 'fa-sort-down');
                    icon.classList.add('fa-sort');
                });

                const sortIcon = header.querySelector('.sort-icon');
                if (sortDirection[columnIndex]) {
                    sortIcon.classList.remove('fa-sort', 'fa-sort-up');
                    sortIcon.classList.add('fa-sort-down');
                } else {
                    sortIcon.classList.remove('fa-sort', 'fa-sort-down');
                    sortIcon.classList.add('fa-sort-up');
                }
            }
        </script>
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