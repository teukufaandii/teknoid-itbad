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
    <title>Surat Masuk - Teknoid</title>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            position: sticky !important;
            top: 0 !important;
            background-color: #fff;
            z-index: 10;
            border-bottom: 2px solid #000;
        }
    </style>
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
                    <h3>Surat Masuk Riset</h3>
                </div>
                <div class="tombol" style="justify-content: flex-end; margin-bottom: 20px;">
                    <div class="search-box">
                        <form method="GET">
                            <input type="text" placeholder="Search..." name="search" id="search">
                        </form>
                    </div>
                </div>
                <div class="tableOverflow">
                    <table id="tablesm" class="tablesorter">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0, this)" style="min-width: 75px; border-top-left-radius: 8px;">No<i id="sort-icon-0" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></i></th>
                                <th onclick="sortTable(1, this)">Asal Surat<i id="sort-icon-1" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                <th onclick="sortTable(2, this)">NIDN<i id="sort-icon-2" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                <th onclick="sortTable(3, this)">Tanggal Surat<i id="sort-icon-3" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                <th onclick="sortTable(4, this)">Perihal<i id="sort-icon-4" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th>
                                <!--<th onclick="sortTable(5, this)">Status<i id="sort-icon-5" class="fas fa-sort sort-icon" style="margin-left: 5px;"></i></th> -->
                                <th>Aksi</th>
                                <!-- <th style="border-top-right-radius: 8px;">Detail</th> -->
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
                            $akses = $_SESSION['akses'];
                            $jabatan = $_SESSION['jabatan'];

                            // SQL query to get the number of records
                            $stmt = $conn->prepare("SELECT COUNT(*) FROM tb_srt_dosen WHERE tujuan_surat_srd = ?");
                            $stmt->bind_param("s", $akses);
                            $stmt->execute();
                            $stmt->bind_result($nr_of_rows);
                            $stmt->fetch();
                            $stmt->close();

                            // Calculate number of pages
                            $pages = ceil($nr_of_rows / $rows_per_page);

                            // Determine the start point
                            if (isset($_GET['page-nr'])) {
                                $page = $_GET['page-nr'] - 1;
                                $start = $page * $rows_per_page;
                            }

                            // tabel db surat
                            // Perhatikan penambahan kurung pada kueri SQL berikut
                            $stmt = $conn->prepare("SELECT * FROM tb_srt_dosen WHERE tujuan_surat_srd = ? ORDER BY tanggal_surat DESC LIMIT ?, ?");
                            $stmt->bind_param("sii", $akses, $start, $rows_per_page);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $counter = $start + 1;
                                while ($row = $result->fetch_assoc()) {
                            ?>
                                    <tr>
                                        <?php
                                        $akses = $_SESSION['akses'];
                                        $jabatan = $_SESSION['jabatan'];
                                        $verified = "Verifikasi";

                                        if ($_SESSION['jabatan'] == 'Humas') {
                                            if ($row['tujuan_surat_srd'] == 'humas') {
                                                echo "<td style=\"min-width: 75px;\">" . $counter++ . "</td>";
                                                echo "<td>" . $row['asal_surat'] . "</td>";
                                                echo "<td>" . $row['NIDN'] . "</td>";
                                                echo "<td>" . (isset($row['tanggal_surat']) ? (new DateTime($row['tanggal_surat']))->format('d-m-Y') : '') . "</td>";
                                                echo "<td>" . $row['perihal_srd'] . "</td>";
                                                echo "</td>";
                                                echo "<td><a href='dispo_dosen.php?id=" . $row['id_srt'] . "' ><button style='background-color: white; color: #1b5ebe;'> Proses </button></a></td>";
                                            }
                                        }
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

    <script type="text/javascript">
        // pencarian
        $(document).ready(function() {
            $("#search").keyup(function() {
                var search = $(this).val();
                $.ajax({
                    url: 'ajax/searchSM_Riset.php',
                    method: 'POST',
                    data: {
                        query: search
                    },
                    success: function(response) {
                        $("#tablesm").html(response);
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.memo-button').forEach(function(button) {
                button.addEventListener('click', function() {
                    var suratId = this.getAttribute('data-id');

                    // Cek apakah memo sudah ada untuk surat ini
                    $.ajax({
                        url: 'sql/cek_memo.php',
                        method: 'POST',
                        data: {
                            id_srt: suratId
                        },
                        success: function(response) {
                            var data = JSON.parse(response);
                            var memoExists = data.exists;
                            var existingMemo = data.memo;

                            if (memoExists) {
                                Swal.fire({
                                    title: 'Edit Memo',
                                    input: 'textarea',
                                    inputLabel: 'Isi Memo',
                                    inputValue: existingMemo,
                                    inputAttributes: {
                                        readonly: true
                                    },
                                    showCancelButton: true,
                                    confirmButtonText: 'Edit Memo',
                                    cancelButtonText: 'Batal',
                                    preConfirm: () => {
                                        return {
                                            memo: existingMemo,
                                            id_srt: suratId
                                        };
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        Swal.fire({
                                            title: 'Edit Memo',
                                            input: 'textarea',
                                            inputLabel: 'Isi Memo',
                                            inputValue: existingMemo,
                                            showCancelButton: true,
                                            confirmButtonText: 'Simpan',
                                            cancelButtonText: 'Batal',
                                            preConfirm: (memo) => {
                                                if (!memo) {
                                                    Swal.showValidationMessage('Memo tidak boleh kosong');
                                                }
                                                return {
                                                    memo: memo,
                                                    id_srt: suratId
                                                };
                                            }
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                // Save the memo
                                                $.ajax({
                                                    url: 'sql/tambah_memo.php',
                                                    method: 'POST',
                                                    data: {
                                                        id_srt: result.value.id_srt,
                                                        memo: result.value.memo
                                                    },
                                                    success: function(response) {
                                                        if (response === 'success') {
                                                            Swal.fire({
                                                                icon: 'success',
                                                                title: 'Berhasil',
                                                                text: 'Memo berhasil disimpan'
                                                            }).then(() => {
                                                                location.reload();
                                                            });
                                                        } else {
                                                            Swal.fire({
                                                                icon: 'error',
                                                                title: 'Gagal',
                                                                text: 'Terjadi kesalahan, coba lagi nanti'
                                                            });
                                                        }
                                                    },
                                                    error: function() {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Gagal',
                                                            text: 'Terjadi kesalahan, coba lagi nanti'
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Tambah Memo',
                                    input: 'textarea',
                                    inputLabel: 'Isi Memo',
                                    showCancelButton: true,
                                    confirmButtonText: 'Tambah',
                                    cancelButtonText: 'Batal',
                                    preConfirm: (memo) => {
                                        if (!memo) {
                                            Swal.showValidationMessage('Memo tidak boleh kosong');
                                        }
                                        return {
                                            memo: memo,
                                            id_srt: suratId
                                        };
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Save the memo
                                        $.ajax({
                                            url: 'sql/tambah_memo.php',
                                            method: 'POST',
                                            data: {
                                                id_srt: result.value.id_srt,
                                                memo: result.value.memo
                                            },
                                            success: function(response) {
                                                if (response === 'success') {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Berhasil',
                                                        text: 'Memo berhasil disimpan'
                                                    }).then(() => {
                                                        location.reload();
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Gagal',
                                                        text: 'Terjadi kesalahan, coba lagi nanti'
                                                    });
                                                }
                                            },
                                            error: function() {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Gagal',
                                                    text: 'Terjadi kesalahan, coba lagi nanti'
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    });
                });
            });
        });

        // Handle verify button click
        document.querySelectorAll('.verify-button').forEach(function(button) {
            button.addEventListener('click', function() {
                var suratId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Verifikasi Surat',
                    text: "Apakah Anda yakin ingin memverifikasi surat ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Verifikasi',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                url: 'sql/verifikasi_keuangan.php',
                                method: 'POST',
                                data: {
                                    id_srt: suratId
                                },
                                success: function(response) {
                                    if (response === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: 'Surat berhasil diverifikasi'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: 'Terjadi kesalahan, coba lagi nanti'
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Terjadi kesalahan, coba lagi nanti'
                                    });
                                }
                            });
                        });
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
    </script>

    <script>
        // sorting //
        $(document).ready(function() {
            $("#tablesm").tablesorter();
        });

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

</body>

</html>