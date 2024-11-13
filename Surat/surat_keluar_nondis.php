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
    <!-- ajax live search -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
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
                    <h3>Surat Keluar Insentif</h3>
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

                <div class="tableOverflow">
                    <table id="tablesk" class="tablesorter">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, this)" style="min-width: 75px; border-top-left-radius: 8px;">No<i id="sort-icon-0" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                <th onclick="sortTable(1, this)">Jenis Surat<i id="sort-icon-1" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                <th onclick="sortTable(2, this)">Asal Surat<i id="sort-icon-2" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                <th onclick="sortTable(3, this)">Jenis Insentif<i id="sort-icon-3" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                <th onclick="sortTable(4, this)">Tanggal Surat<i id="sort-icon-4" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                <th onclick="sortTable(5, this)">Status<i id="sort-icon-5" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                <th>Memo</th>
                                <th style="border-top-right-radius: 8px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $conn = mysqli_connect("localhost", "teknoid1_admin", "RadKrwY8qt3v", "teknoid1_db_teknoid");
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            // pengaturan baris
                            $start = 0;
                            $rows_per_page = 20;
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
                            if (isset($_GET['page-nr'])) {
                                $page = $_GET['page-nr'] - 1;
                                $start = $page * $rows_per_page;
                            }

                            // tabel db surat
                            $stmt = $conn->prepare(
                                "SELECT 
                                srt.id_srt, srt.tanggal_surat,srt.jenis_surat, srt.asal_surat,
                                srt.status_pengusul, srt.NIDN, srt.no_telpon, srt.id_sinta, srt.prodi_pengusul,
                                srt.jenis_insentif, srt.skema_ppmdpek, srt.judul_penelitian_ppm, srt.jenis_publikasi_pi,
                                srt.judul_publikasi_pi, srt.nama_jurnal_pi, srt.vol_notahun_pi, srt.link_jurnal_pi,
                                srt.skala_ppdpi, srt.nama_pertemuan_ppdpi, srt.usulan_biaya_ppdpi, srt.skala_ppdks,
                                srt.nama_pertemuan_ppdks, srt.nm_kegiatan_vl, srt.waktu_pelaksanaan_vl, srt.jenis_hki,
                                srt.judul_hki, srt.teknologi_tg, srt.deskripsi_tg, srt.jenis_buku, srt.judul_buku,
                                srt.sinopsis_buku, srt.isbn_buku, srt.nama_model_mpdks, srt.deskripsi_mpdks,
                                srt.judul_ipbk, srt.namaPenerbit_dan_waktu_ipbk, srt.link_publikasi_ipbk, srt.ttl_srd,
                                srt.alamat_srd, srt.verifikasi, srt.perihal_srd, srt.email_srd, srt.deskripsi_srd, srt.nama_perusahaan_srd,
                                srt.alamat_perusahaan_srd, srt.tujuan_surat_srd, srt.nomor_surat_srd, srt.memo
                            FROM 
                                tb_srt_dosen srt
                            WHERE 
                                srt.asal_surat = ?
                                    LIMIT ?, ?"
                            );
                            $stmt->bind_param("sii", $fullname, $start, $rows_per_page);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $counter = $start + 1;
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <tr>
                                        <?php
                                        echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                        echo "<td>" . (isset($row['jenis_surat']) ? ($row['jenis_surat'] == 5 ? "Surat Insentif" : ($row['jenis_surat'] == 6 ? "Surat Riset Dosen" : "Jenis Surat Tidak Dikenali")) : "Data Tidak Tersedia") . "</td>";
                                        echo "<td>" . (isset($row['asal_surat']) ? $row['asal_surat'] : 'Data Tidak Tersedia') . "</td>";

                                        // Mapping for jenis_surat
                                        switch ($row['jenis_insentif']) {
                                            case 'penelitian':
                                                $jenis_surat_text = 'Penelitian & Pengabdian Masyarakat';
                                                break;
                                            case 'publikasi':
                                                $jenis_surat_text = 'Publikasi Ilmiah';
                                                break;
                                            case 'pertemuan_ilmiah':
                                                $jenis_surat_text = 'Penyajian Paper Dalam Pertemuan Ilmiah';
                                                break;
                                            case 'keynote_speaker':
                                                $jenis_surat_text = 'Keynote Speaker Dalam Pertemuan Ilmiah';
                                                break;
                                            case 'visiting':
                                                $jenis_surat_text = 'Visiting Lecturer/Research';
                                                break;
                                            case 'hki':
                                                $jenis_surat_text = 'Hak Kekayaan Intelektual';
                                                break;
                                            case 'teknologi':
                                                $jenis_surat_text = 'Teknologi tepat Guna';
                                                break;
                                            case 'buku':
                                                $jenis_surat_text = 'Buku';
                                                break;
                                            case 'model':
                                                $jenis_surat_text = 'Model, Prototype, Desain, karya Seni, Rekayasa Sosial, Kebijakan';
                                                break;
                                            case 'insentif_publikasi':
                                                $jenis_surat_text = 'Insentif Publikasi berita Kegiatan pengabdian Masyarakat';
                                                break;
                                            default:
                                                $jenis_surat_text = '-'; // Optional: Handle unexpected values
                                                break;
                                        }
                                        echo "<td>" . $jenis_surat_text . "</td>";
                                        echo "<td>" . (isset($row['tanggal_surat']) ? (new DateTime($row['tanggal_surat']))->format('d-m-Y') : '') . "</td>";
                                        echo "<td>";
                                        if ($row['verifikasi'] == 1) {
                                            echo '<i class="fas fa-check-square" style="background-color: white; color: green;"></i> Terverifikasi';
                                        } else {
                                            echo ' Belum Diverifikasi';
                                        }
                                        echo "</td>";
                                        echo "<td>";
                                        if (isset($row['memo']) && !empty($row['memo'])) {
                                            $memo = htmlspecialchars($row['memo'], ENT_QUOTES, 'UTF-8');
                                            echo "<i class='fa fa-sticky-note' style='color: #ffc107; cursor: pointer;' onclick=\"showMemo('$memo');\"></i>";
                                        } else {
                                            echo '-';
                                        }
                                        echo "</td>";
                                        echo "<td><a href='dispo_dosen.php?id=" . $row['id_srt'] . "' ><i class='fas fa-eye' style='background-color: white; color: #1b5ebe;'></i></a></td>";
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
                    url: 'ajax/searchSkInsentif.php',
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