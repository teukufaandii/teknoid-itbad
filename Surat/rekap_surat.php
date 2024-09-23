<?php
session_start(); // Start the session at the beginning of the script
if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Humas' || isset($_SESSION['akses']) && $_SESSION['akses'] == 'Admin' || isset($_SESSION['akses']) && $_SESSION['akses'] == 'lp3m') { // Check if $_SESSION['akses'] is set and equals 'Humas'
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
        <title>Rekap Surat-Teknoid</title>
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
                        <h3>Rekap Surat</h3>
                        <?php
                        if (isset($_SESSION['akses']) && $_SESSION['akses'] === 'Humas') {
                            echo '<a href="surat_keluar.php"><button class="back">Kembali</button></a>';
                        }
                        ?>
                    </div>
                    <form method="POST" class="form" id="formCari">
                        <div class="inputfield">
                            <label for="jenis_surat">Jenis Surat</label>
                            <div class="custom_select">
                                <?php
                                if ($_SESSION['akses'] === 'Admin') {
                                    echo '<select name="jenis_surat" id="jenis_surat" required>';
                                    echo '<option hidden>Option (Permohonan, laporan, kkl, riset, insentif, riset dosen, honorium)</option>';
                                    $conn = mysqli_connect("localhost", "root", "", "db_teknoid");
                                    if ($conn->connect_error) {
                                        // You should handle the connection error here
                                    }
                                    // Query untuk mendapatkan tipe surat dari tabel tb jenis_surat
                                    $jenis_surat = "SELECT * FROM tb_jenis WHERE kd_jenissurat IN (1, 2, 3, 4, 5, 6, 7)";
                                    $result_jenis_surat = $conn->query($jenis_surat);

                                    // Periksa apakah ada hasil query
                                    if ($result_jenis_surat->num_rows > 0) {
                                        // Loop melalui setiap baris hasil query
                                        while ($row_jenis_surat = $result_jenis_surat->fetch_assoc()) {
                                            // Tampilkan opsi jenis_surat 
                                            echo "<option value='" . $row_jenis_surat['kd_jenissurat'] . "'>" . $row_jenis_surat['nama_jenis'] . "</option>";
                                        }
                                    }
                                    echo '</select>';
                                } else if ($_SESSION['akses'] === 'Humas') {
                                    echo '<select name="jenis_surat" id="jenis_surat" required>';
                                    echo '<option hidden>Option (Permohonan, laporan, kkl, riset, riset dosen, honorium)</option>';
                                    $conn = mysqli_connect("localhost", "root", "", "db_teknoid");
                                    if ($conn->connect_error) {
                                        // You should handle the connection error here
                                    }
                                    // Query untuk mendapatkan tipe surat dari tabel tb jenis_surat
                                    $jenis_surat = "SELECT * FROM tb_jenis WHERE kd_jenissurat IN (1, 2, 3, 4, 6, 7)";
                                    $result_jenis_surat = $conn->query($jenis_surat);

                                    // Periksa apakah ada hasil query
                                    if ($result_jenis_surat->num_rows > 0) {
                                        // Loop melalui setiap baris hasil query
                                        while ($row_jenis_surat = $result_jenis_surat->fetch_assoc()) {
                                            // Tampilkan opsi jenis_surat 
                                            echo "<option value='" . $row_jenis_surat['kd_jenissurat'] . "'>" . $row_jenis_surat['nama_jenis'] . "</option>";
                                        }
                                    }
                                    echo '</select>';
                                } else if ($_SESSION['akses'] === 'lp3m') {
                                    echo '<select name="jenis_surat" id="jenis_surat" required>';
                                    echo '<option hidden disabled>Surat Insentif</option>';
                                    $conn = mysqli_connect("localhost", "root", "", "db_teknoid");
                                    if ($conn->connect_error) {
                                        // You should handle the connection error here
                                    }
                                    // Query untuk mendapatkan tipe surat dari tabel tb jenis_surat
                                    $jenis_surat = "SELECT * FROM tb_jenis WHERE kd_jenissurat IN (5)";
                                    $result_jenis_surat = $conn->query($jenis_surat);

                                    // Periksa apakah ada hasil query
                                    if ($result_jenis_surat->num_rows > 0) {
                                        // Loop melalui setiap baris hasil query
                                        while ($row_jenis_surat = $result_jenis_surat->fetch_assoc()) {
                                            // Tampilkan opsi jenis_surat 
                                            echo "<option value='" . $row_jenis_surat['kd_jenissurat'] . "'>" . $row_jenis_surat['nama_jenis'] . "</option>";
                                        }
                                    }
                                    echo '</select>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="tanggal">
                            <div class="inputfield" style="margin:0; gap:20px;">
                                <label for="tanggal_awal">Tanggal Awal Surat</label>
                                <input type="date" id="tanggal_awal" name="tanggal_awal" style="margin-left:8px;">
                            </div>
                            <div class="inputfield" style="margin:0; gap:20px; margin-left: 10px;">
                                <label for="tanggal_akhir">Tanggal Akhir Surat</label>
                                <input type="date" id="tanggal_akhir" name="tanggal_akhir">
                            </div>
                        </div>
                        <input type="submit" name="search" class="search" value="Cari" id="searchForm">
                    </form>

                    <form method='POST' action='export.php'>
                        <input type='submit' value='Export' class="ekspor" name='Export'>
                        <div class="tableOverflow">
                            <table id="tablerekap">
                                <?php
                                $user_arr = array();
                                if (isset($_POST['search'])) {
                                    $jenis_surat = mysqli_real_escape_string($conn, $_POST['jenis_surat']);
                                    // Tentukan query berdasarkan jenis_surat
                                    if (in_array($jenis_surat, [1])) {
                                        $query = "SELECT s.id_surat, s.kode_surat, s.kd_surat, s.jenis_surat, s.asal_surat, s.perihal, s.tanggal_surat
                                                  FROM tb_surat_dis AS s
                                                  JOIN tb_jenis AS js ON s.jenis_surat = js.kd_jenissurat
                                                  WHERE s.jenis_surat = $jenis_surat";
                                    } elseif (in_array($jenis_surat, [2])) {
                                        $query = "SELECT s.kode_surat, s.kd_surat, s.jenis_surat, s.asal_surat, s.perihal, s.tanggal_surat
                                                  FROM tb_surat_dis AS s
                                                  JOIN tb_jenis AS js ON s.jenis_surat = js.kd_jenissurat
                                                  WHERE s.jenis_surat = $jenis_surat";
                                    } elseif (in_array($jenis_surat, [3])) {
                                        $query = "SELECT s.kode_surat, s.kd_surat, s.jenis_surat, s.asal_surat, s.perihal, s.tanggal_surat
                                                  FROM tb_surat_dis AS s
                                                  JOIN tb_jenis AS js ON s.jenis_surat = js.kd_jenissurat
                                                  WHERE s.jenis_surat = $jenis_surat";
                                    } elseif (in_array($jenis_surat, [4])) {
                                        $query = "SELECT s.kode_surat, s.kd_surat, s.jenis_surat, s.asal_surat, s.perihal, s.tanggal_surat
                                                  FROM tb_surat_dis AS s
                                                  JOIN tb_jenis AS js ON s.jenis_surat = js.kd_jenissurat
                                                  WHERE s.jenis_surat = $jenis_surat";
                                    } elseif (in_array($jenis_surat, [5])) {
                                        $query = "SELECT *
                                                  FROM tb_srt_dosen AS sd
                                                  JOIN tb_jenis AS js ON sd.jenis_surat = js.kd_jenissurat
                                                  WHERE sd.jenis_surat = $jenis_surat";
                                    } elseif (in_array($jenis_surat, [6])) {
                                        $query = "SELECT *
                                                  FROM tb_srt_dosen AS sd
                                                  JOIN tb_jenis AS js ON sd.jenis_surat = js.kd_jenissurat
                                                  WHERE sd.jenis_surat = $jenis_surat";
                                    } elseif (in_array($jenis_surat, [7])) {
                                        $query = "SELECT *
                                                  FROM tb_srt_honor AS sh
                                                  JOIN tb_jenis AS js ON sh.jenis_surat = js.kd_jenissurat
                                                  WHERE sh.jenis_surat = $jenis_surat";
                                    } else {
                                        die("Jenis surat tidak valid.");
                                    }


                                    // Jalankan query
                                    $result = $conn->query($query);

                                    $thead = '';
                                    if ($result && $result->num_rows > 0) {
                                        // Ambil nama jenis surat dari tabel tb_jenis
                                        $row = $result->fetch_assoc();
                                        $jenis_surat_query = "SELECT nama_jenis FROM tb_jenis WHERE kd_jenissurat = " . $row["jenis_surat"];
                                        $jenis_surat_result = $conn->query($jenis_surat_query);
                                        $jenis_surat_row = $jenis_surat_result->fetch_assoc();
                                        $jenis_surat_nama = $jenis_surat_row["nama_jenis"];

                                        // Tentukan header berdasarkan jenis surat
                                        if (in_array($jenis_surat, [1])) {
                                            $thead = "<thead>
                                                        <tr>
                                                            <th>NO</th>
                                                            <th>Kode Surat</th>
                                                            <th>Jenis Surat</th>
                                                            <th>Asal Surat</th>
                                                            <th>Perihal</th>
                                                            <th>Tanggal Surat</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>";
                                        } elseif (in_array($jenis_surat, [2])) {
                                            $thead = "<thead>
                                                        <tr>
                                                            <th>NO</th>
                                                            <th>Kode Surat</th>
                                                            <th>Jenis Surat</th>
                                                            <th>Asal Surat</th>
                                                            <th>Perihal</th>
                                                            <th>Tanggal Surat</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>";
                                        } elseif (in_array($jenis_surat, [3])) {
                                            $thead = "<thead>
                                                        <tr>
                                                            <th>NO</th>
                                                            <th>Kode Surat</th>
                                                            <th>Jenis Surat</th>
                                                            <th>Asal Surat</th>
                                                            <th>Perihal</th>
                                                            <th>Tanggal Surat</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>";
                                        } elseif (in_array($jenis_surat, [4])) {
                                            $thead = "<thead>
                                                        <tr>
                                                            <th>NO</th>
                                                            <th>Kode Surat</th>
                                                            <th>Jenis Surat</th>
                                                            <th>Asal Surat</th>
                                                            <th>Perihal</th>
                                                            <th>Tanggal Surat</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>";
                                        } elseif (in_array($jenis_surat, [5])) {
                                            $thead = "<thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Jenis Surat</th>
                                                            <th>Asal Surat</th>
                                                            <th>Jenis Insentif</th>
                                                            <th>Tanggal Surat</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>";
                                        } elseif (in_array($jenis_surat, [6])) {
                                            $thead = "<thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Jenis Surat</th>
                                                            <th>Asal Surat</th>
                                                            <th>Perihal</th>
                                                            <th>Nama Perusahaan</th>
                                                            <th>Tanggal Surat</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>";
                                        } elseif (in_array($jenis_surat, [7])) {
                                            $thead = "<thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Jenis Surat</th>
                                                            <th>Asal Surat</th>
                                                            <th>Nama Kegiatan</th>
                                                            <th>Tanggal Surat</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>";
                                        }
                                    }
                                ?>
                                    <?php echo $thead; ?>
                                    <tbody>
                                    <?php
                                    $conn = mysqli_connect("localhost", "root", "", "db_teknoid");
                                    if ($conn->connect_error) {
                                    }

                                    $user_arr = array();
                                    // Proses pencarian rekap
                                    if (isset($_POST['search'])) {

                                        $jenis_surat = mysqli_real_escape_string($conn, $_POST['jenis_surat']);
                                        $tanggal_awal = mysqli_real_escape_string($conn, $_POST['tanggal_awal']);
                                        $tanggal_akhir = mysqli_real_escape_string($conn, $_POST['tanggal_akhir']);


                                        // Tentukan query berdasarkan jenis_surat
                                        if (in_array($jenis_surat, [1])) {
                                            // Query untuk jenis surat 1-4
                                            $query = "SELECT s.id_surat, s.kode_surat, s.kd_surat, s.jenis_surat, s.asal_surat, s.perihal, s.tanggal_surat
                                            FROM tb_surat_dis AS s
                                            JOIN tb_jenis AS js ON s.jenis_surat = js.kd_jenissurat
                                            WHERE s.jenis_surat = 1
                                            AND s.tanggal_surat BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
                                        } elseif (in_array($jenis_surat, [2])) {
                                            // Query untuk jenis surat 1-4
                                            $query = "SELECT s.id_surat, s.kode_surat, s.kd_surat, s.jenis_surat, s.asal_surat, s.perihal, s.tanggal_surat
                                             FROM tb_surat_dis AS s
                                             JOIN tb_jenis AS js ON s.jenis_surat = js.kd_jenissurat
                                             WHERE s.jenis_surat = 2
                                             AND s.tanggal_surat BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
                                        } elseif (in_array($jenis_surat, [3])) {
                                            // Query untuk jenis surat 1-4
                                            $query = "SELECT s.kode_surat, s.kd_surat, s.jenis_surat, s.asal_surat, s.perihal, s.tanggal_surat
                                            FROM tb_surat_dis AS s
                                            JOIN tb_jenis AS js ON s.jenis_surat = js.kd_jenissurat
                                            WHERE s.jenis_surat = 3
                                            AND s.tanggal_surat BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
                                        } elseif (in_array($jenis_surat, [4])) {
                                            // Query untuk jenis surat 1-4
                                            $query = "SELECT s.kode_surat, s.kd_surat, s.jenis_surat, s.asal_surat, s.perihal, s.tanggal_surat
                                            FROM tb_surat_dis AS s
                                            JOIN tb_jenis AS js ON s.jenis_surat = js.kd_jenissurat
                                            WHERE s.jenis_surat = 4
                                            AND s.tanggal_surat BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
                                        } elseif (in_array($jenis_surat, [5])) {
                                            // Query untuk jenis surat 5-6
                                            $query = "SELECT sd.id_srt, sd.jenis_surat, sd.asal_surat, sd.jenis_insentif, sd.tanggal_surat
                                            FROM tb_srt_dosen AS sd
                                            JOIN tb_jenis AS js ON sd.jenis_surat = js.kd_jenissurat
                                            WHERE sd.jenis_surat = 5
                                            AND sd.tanggal_surat BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
                                        } elseif (in_array($jenis_surat, [6])) {
                                            // Query untuk jenis surat 5-6
                                            $query = "SELECT *
                                            FROM tb_srt_dosen AS sd
                                            JOIN tb_jenis AS js ON sd.jenis_surat = js.kd_jenissurat
                                            WHERE sd.jenis_surat = 6 
                                            AND sd.tanggal_surat BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
                                        } elseif ($jenis_surat == 7) {
                                            // Query untuk jenis surat 7
                                            $query = "SELECT *
                                            FROM tb_srt_honor AS sh
                                            JOIN tb_jenis AS js ON sh.jenis_surat = js.kd_jenissurat
                                            WHERE sh.jenis_surat = 7
                                            AND sh.tanggal_surat BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
                                        }

                                        // Eksekusi query
                                        $result = mysqli_query($conn, $query);

                                        // Tampilkan hasil pencarian
                                        if ($result->num_rows > 0) {
                                            $nomor = 1;
                                            while ($row = $result->fetch_assoc()) {
                                                // Ambil nama jenis surat berdasarkan ID dari tabel tb_jenissurat
                                                $jenis_surat_query = "SELECT nama_jenis FROM tb_jenis WHERE kd_jenissurat = '" . $row["jenis_surat"] . "'";
                                                $jenis_surat_result = $conn->query($jenis_surat_query);
                                                $jenis_surat_row = $jenis_surat_result->fetch_assoc();
                                                $jenis_surat = $jenis_surat_row["nama_jenis"];

                                                // Format tanggal
                                                $formatted_date = date('d-m-Y', strtotime($row["tanggal_surat"]));

                                                // Kondisi untuk menampilkan data sesuai jenis surat
                                                if (in_array($row['jenis_surat'], [1])) {
                                                    // Hanya untuk jenis surat 1-4
                                                    $id_surat = $row['id_surat'];
                                                    $id = $row['kode_surat'];
                                                    $asal_surat = $row['asal_surat'];
                                                    $perihal = $row['perihal'];
                                                    $tgl_surat = $row['tanggal_surat'];
                                                    $user_arr[] = array($id_surat, $id, $jenis_surat, $asal_surat, $perihal, $tgl_surat);
                                                    echo "<tr>
                                                                <td style='width:10px;'>" . $nomor . "</td>
                                                                <td>" . (!empty($row['kode_surat']) ? $row['kode_surat'] : $row['kd_surat']) . "</td>
                                                                <td>" . $jenis_surat . "</td>
                                                                <td>" . $row["asal_surat"] . "</td>
                                                                <td>" . $row["perihal"] . "</td>
                                                                <td>" . $formatted_date . "</td>
                                                                <td>
                                                                    <a href='lacak.php?id=" . $row['id_surat'] . "' class='btnLihat'>Lihat</a>
                                                                    <button type='button' class='btn_delete' name='delete_btn' data-id3='" . $row['kode_surat'] . "'>Hapus</button>
                                                                </td>
                                                            </tr>";
                                                } elseif (in_array($row['jenis_surat'], [2])) {
                                                    // Hanya untuk jenis surat 1-4
                                                    $id_surat = $row['id_surat'];
                                                    $id = $row['kode_surat'];
                                                    $asal_surat = $row['asal_surat'];
                                                    $perihal = $row['perihal'];
                                                    $tgl_surat = $row['tanggal_surat'];
                                                    $user_arr[] = array($id_surat, $id, $jenis_surat, $asal_surat, $perihal, $tgl_surat);
                                                    echo "<tr>
                                                                            <td style='width:10px;'>" . $nomor . "</td>
                                                                            <td>" . (!empty($row['kode_surat']) ? $row['kode_surat'] : $row['kd_surat']) . "</td>
                                                                            <td>" . $jenis_surat . "</td>
                                                                            <td>" . $row["asal_surat"] . "</td>
                                                                            <td>" . $row["perihal"] . "</td>
                                                                            <td>" . $formatted_date . "</td>
                                                                           <td>
                                                                                <a href='lacak.php?id=" . $row['id_surat'] . "' class='btnLihat'>Lihat</a>
                                                                                <button type='button' class='btn_delete' name='delete_btn' data-id3='" . $row['kode_surat'] . "'>Hapus</button>
                                                                            </td>
                                                                        </tr>";
                                                } elseif (in_array($row['jenis_surat'], [3])) {
                                                    // Hanya untuk jenis surat 1-4
                                                    $id2 = $row['kd_surat'];
                                                    $asal_surat = $row['asal_surat'];
                                                    $perihal = $row['perihal'];
                                                    $tgl_surat = $row['tanggal_surat'];
                                                    $user_arr[] = array($id2, $jenis_surat, $asal_surat, $perihal, $tgl_surat);
                                                    echo "<tr>
                                                                            <td style='width:10px;'>" . $nomor . "</td>
                                                                            <td>" . (!empty($row['kode_surat']) ? $row['kode_surat'] : $row['kd_surat']) . "</td>
                                                                            <td>" . $jenis_surat . "</td>
                                                                            <td>" . $row["asal_surat"] . "</td>
                                                                            <td>" . $row["perihal"] . "</td>
                                                                            <td>" . $formatted_date . "</td>
                                                                            <td><button type='button' class='btn_delete' name='delete_btn' data-id3='" . ($row['kd_surat']) . "'>Hapus</button></td>
                                                                        </tr>";
                                                } elseif (in_array($row['jenis_surat'], [4])) {
                                                    // Hanya untuk jenis surat 1-4
                                                    $id2 = $row['kd_surat'];
                                                    $asal_surat = $row['asal_surat'];
                                                    $perihal = $row['perihal'];
                                                    $tgl_surat = $row['tanggal_surat'];
                                                    $user_arr[] = array($id2, $jenis_surat, $asal_surat, $perihal, $tgl_surat);
                                                    echo "<tr>
                                                                            <td style='width:10px;'>" . $nomor . "</td>
                                                                            <td>" . (!empty($row['kode_surat']) ? $row['kode_surat'] : $row['kd_surat']) . "</td>
                                                                            <td>" . $jenis_surat . "</td>
                                                                            <td>" . $row["asal_surat"] . "</td>
                                                                            <td>" . $row["perihal"] . "</td>
                                                                            <td>" . $formatted_date . "</td>
                                                                            <td><button type='button' class='btn_delete' name='delete_btn' data-id3='" . ($row['kd_surat']) . "'>Hapus</button></td>
                                                                        </tr>";
                                                } elseif (in_array($row['jenis_surat'], [5])) {
                                                    $id_srt = $row['id_srt'];
                                                    $asal_surat = $row['asal_surat'];
                                                    $jenis_insentif = $row['jenis_insentif'];
                                                    $tgl_surat = $row['tanggal_surat'];
                                                    $user_arr[] = array($id_srt, $jenis_surat, $asal_surat, $jenis_insentif, $tgl_surat);
                                                    echo "<tr id='hasilSearch'>
                                                            <td style='width:10px;'>" . $nomor . "</td>
                                                            <td>" . $jenis_surat . "</td>
                                                            <td>" . $asal_surat . "</td>
                                                            <td>" . ucwords($jenis_insentif) . "</td>
                                                            <td>" . $formatted_date . "</td>
                                                            <td><button type='button' class='btn_delete' name='delete_btn' data-id3='" . ($row['id_srt']) . "'>Hapus</button></td>
                                                        </tr>";
                                                } elseif (in_array($row['jenis_surat'], [6])) {
                                                    $id_srt = $row['id_srt'];
                                                    $asal_surat = $row['asal_surat'];
                                                    $tgl_surat = $row['tanggal_surat'];
                                                    $perihalsrd = $row['perihal_srd'];
                                                    $nama_perusahaan = $row['nama_perusahaan_srd'];
                                                    $user_arr[] = array($id_srt, $jenis_surat, $asal_surat, $perihalsrd, $nama_perusahaan, $tgl_surat);
                                                    echo "<tr id='hasilSearch'>
                                                            <td style='width:10px;'>" . $nomor . "</td>
                                                            <td>" . $jenis_surat . "</td>
                                                            <td>" . $asal_surat . "</td>
                                                            <td>" . $perihalsrd . "</td>
                                                            <td>" . $nama_perusahaan . "</td>
                                                            <td>" . $formatted_date . "</td>
                                                            <td><button type='button' class='btn_delete' name='delete_btn' data-id3='" . ($row['id_srt']) . "'>Hapus</button></td>
                                                        </tr>";
                                                } elseif (in_array($row['jenis_surat'], [7])) {
                                                    $id = $row['id'];
                                                    $asal_surat = $row['asal_surat'];
                                                    $nm_kegiatan = $row['nm_kegiatan'];
                                                    $tgl_surat = $row['tanggal_surat'];
                                                    $user_arr[] = array($id, $jenis_surat, $asal_surat, $nm_kegiatan, $tgl_surat);
                                                    echo "<tr id='hasilSearch'>
                                                            <td style='width:10px;'>" . $nomor . "</td>
                                                            <td>" . $jenis_surat . "</td>
                                                            <td>" . $row["asal_surat"] . "</td>
                                                            <td>" . $row["nm_kegiatan"] . "</td>
                                                            <td>" . $formatted_date . "</td>
                                                            <td><button type='button' class='btn_delete' name='delete_btn' data-id3='" . ($row['id']) . "'>Hapus</button></td>
                                                        </tr>";
                                                }
                                                $nomor++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='7'>0 hasil ditemukan</td></tr>";
                                        }
                                    }
                                }
                                $conn->close();
                                    ?>
                                    </tbody>
                            </table>
                            <?php
                            // Serialize the filtered data to pass to export.php
                            $serialize_user_arr = serialize($user_arr);
                            ?>
                            <textarea name='export_data' style='display: none;'><?php echo $serialize_user_arr; ?></textarea>
                            <textarea name='jenis_surat' style='display: none;'><?php echo $jenis_surat; ?></textarea>
                        </div>
                    </form>
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>

        <script>
            // logout
            function redirectToIndex() {
                window.location.href = "/Teknoid-ITBAD/";
            }

            // sidebar
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

            // Function to check if search form is valid
            function isSearchFormValid() {
                var jenis_surat = document.getElementById("jenis_surat").value;
                var tanggal_awal = document.getElementById("tanggal_awal").value;
                var tanggal_akhir = document.getElementById("tanggal_akhir").value;
                var hasilSearch = document.getElementById("hasilSearch");

                // Check if all required fields are filled
                if (jenis_surat && tanggal_awal && tanggal_akhir || hasilSearch) {
                    return true;
                } else {
                    return false;
                }
            }

            // Event listener for search form submission
            document.getElementById("searchForm").addEventListener("submit", function(event) {
                if (!isSearchFormValid()) {
                    alert("Mohon lengkapi semua kolom pencarian terlebih dahulu.");
                    event.preventDefault(); // Prevent form submission
                }
            });

            // Event listener for export form submission
            document.getElementById("exportForm").addEventListener("submit", function(event) {
                if (!isSearchFormValid()) {
                    alert("Mohon lengkapi semua kolom pencarian terlebih dahulu sebelum mengekspor.");
                    event.preventDefault(); // Prevent form submission
                }
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
            // hapus rekap //
            $('.btn_delete').on('click', function() {
                var id = $(this).data("id3");
                console.log(id); // <-- log ID here  
                if (confirm('Yakin untuk menghapus data ini ?')) {
                    $.ajax({
                        url: 'hapus_rekap.php',
                        type: 'POST',
                        data: {
                            delete: 1,
                            id: id
                        },
                        success: function(data) {
                            alert('Data berhasil dihapus'); // Tampilkan alert
                            window.location.reload();
                        }
                    });
                }
            });
        </script>

        <script src="js/dashboard-js.js"></script>
    </body>

    </html>

<?php
} else {
    include "./access-denied.php";
}
?>