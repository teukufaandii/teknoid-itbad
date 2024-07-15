<?php 
session_start(); // Start the session at the beginning of the script
if (isset($_SESSION['akses']) && $_SESSION['akses'] == 'Humas' || isset($_SESSION['akses']) && $_SESSION['akses'] == 'Admin') { // Check if $_SESSION['akses'] is set and equals 'Humas'
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
        <!-- ajax live search --><script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script type="text/javascript" src="tablesorter/jquery.tablesorter.js"></script>
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
                        <h3>Rekap Surat</h3>
                        <?php 
                        if(isset($_SESSION['akses']) && $_SESSION['akses'] === 'Humas') {
                            echo '<a href="surat_keluar.php"><button class="back">Kembali</button></a>';
                        }
                    ?>
                    </div> 
                    <form method="POST" class="form" id="formCari">
                            <div class="inputfield">
                                <label for="jenis_surat">Jenis Surat</label>
                                <div class="custom_select">
                                    <select name="jenis_surat" id="jenis_surat" required>
                                        <option hidden>Option (Permohonan, laporan, kkl, riset)</option>
                                        <?php
                                        $conn = mysqli_connect("localhost", "root", "", "db_teknoid");
                                        if ($conn-> connect_error) {
                                        }
                                        // Query untuk mendapatkan tipe surat dari tabel tb jenis_surat
                                        $jenis_surat = "SELECT * FROM tb_jenis";
                                        $result_jenis_surat = $conn->query($jenis_surat);
                                        
                                        // Periksa apakah ada hasil query
                                        if ($result_jenis_surat->num_rows > 0) {
                                            // Loop melalui setiap baris hasil query
                                            while($row_jenis_surat = $result_jenis_surat->fetch_assoc()) {
                                                // Tampilkan opsi jenis_surat 
                                                echo "<option value='" . $row_jenis_surat['kd_jenissurat'] . "'>" . $row_jenis_surat['nama_jenis'] . "</option>";
                                                     "<option value='" . $row_jenis_surat['kd_jenissurat'] . "'>" . $row_jenis_surat['nama_jenis'] . "</option>";
                                                     "<option value='" . $row_jenis_surat['kd_jenissurat'] . "'>" . $row_jenis_surat['nama_jenis'] . "</option>";
                                                     "<option value='" . $row_jenis_surat['kd_jenissurat'] . "'>" . $row_jenis_surat['nama_jenis'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="tanggal">
                                <div class="inputfield">
                                    <label for="tanggal_awal">Tanggal Awal Surat</label>
                                    <input type="date" id="tanggal_awal" name="tanggal_awal">
                                </div>
                                <div class="inputfield">
                                    <label for="tanggal_akhir">Tanggal Akhir Surat</label>
                                    <input type="date" id="tanggal_akhir" name="tanggal_akhir">
                                </div>
                            </div>
                            <input type="submit" name="search" class="search" value="Cari" id="searchForm">
                        </form>
                        
                        <form method='post' action='export.php' id="exportForm"> 
                        <input type='submit' value='Export' class="ekspor" name='Export'>
                            <div class="tableOverflow">
                                <table id="tablerekap">
                                    <thead>
                                        <tr>
                                            <th> NO </th>
                                            <th>Kode Surat</th>
                                            <th>Jenis Surat</th>
                                            <th>Asal Surat</th>
                                            <th>Perihal</th>
                                            <th>Tanggal Surat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $conn = mysqli_connect("localhost", "root", "", "db_teknoid");
                                            if ($conn-> connect_error) {
                                            }
                                            
                                            $user_arr = array();
                                            // Proses pencarian rekap
                                            if(isset($_POST['search'])) {
                                                $jenis_surat = mysqli_real_escape_string($conn, $_POST['jenis_surat']); // Escaping to prevent SQL injection
                                                $tanggal_awal = mysqli_real_escape_string($conn, $_POST['tanggal_awal']);
                                                $tanggal_akhir = mysqli_real_escape_string($conn, $_POST['tanggal_akhir']);
                                            
                                                // Query to fetch data only from tb_surat_dis
                                                $query = "
                                                    SELECT s.kode_surat, s.kd_surat, s.jenis_surat, s.asal_surat, s.perihal, s.tanggal_surat
                                                    FROM tb_surat_dis AS s
                                                    JOIN tb_jenis AS js ON s.jenis_surat = js.kd_jenissurat
                                                    WHERE s.jenis_surat = '$jenis_surat'
                                                    AND s.tanggal_surat BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
                                                ";
                                            
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
                                                
                                                        $id = $row['kode_surat'];
                                                        $id2 = $row['kd_surat'];
                                                        $asal_surat = $row['asal_surat'];
                                                        $perihal = $row['perihal'];
                                                        $tgl_surat = $row['tanggal_surat'];
                                                       

                                                        $user_arr[] = array((!empty($id) ? $id : $id2), $jenis_surat, $asal_surat, $perihal, $tgl_surat);                                              
                                                        echo "<tr id='hasilSearch'>
                                                                <td style='width:10px;'>" . $nomor . "</td>
                                                                <td>". (!empty($row['kode_surat']) ? $row['kode_surat'] : $row['kd_surat'])."</td>
                                                                <td>". $jenis_surat ."</td>
                                                                <td>". $row["asal_surat"]."</td>
                                                                <td>". $row["perihal"]."</td>
                                                                <td>". $row["tanggal_surat"]."</td>
                                                                <td><button type='button' class='btn_delete' name='delete_btn' data-id3='".(!empty($row['kode_surat']) ? $row['kode_surat'] : $row['kd_surat'])."'>Hapus</button></td>
                                                            </tr>";
                                                        $nomor++;
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='7'>0 hasil ditemukan</td></tr>";
                                                }                                
                                            }
                                            $conn-> close();
                                        ?>
                                    </tbody>
                                </table>
                                <?php 
                                $serailze_user_arr = serialize($user_arr); 
                                ?> 
                                <textarea name='export_data' style='display: none;'><?php echo $serailze_user_arr; ?></textarea> 
                            </div>
                        </form>
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>
        
        <script>
            // logout
            function redirectToIndex() {
            window.location.href = "/Teknoid-ITBAD/index.php";
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
    $('.btn_delete').on('click', function(){
        var id = $(this).data("id3");
        console.log(id); // <-- log ID here  
        if (confirm('Yakin untuk menghapus data ini ?')) 
        {
            $.ajax({
                url: 'hapus_rekap.php',
                type: 'POST',
                data: {delete: 1, id: id},
                success: function(data){
                    alert('Data berhasil dihapus'); // Tampilkan alert
                    fetch_data(); // Panggil fungsi fetch_data untuk refresh data
                }
            });
        }
    });

    // Fungsi untuk fetch_data (misalnya dari fungsi yang sudah ada)
    function fetch_data() {
        // Tambahkan implementasi sesuai kebutuhan Anda
        console.log('Fetching data...');
    }
</script>

        <script src="js/dashboard-js.js"></script>
    </body>
</html>

<?php 
} else {
    include "./access-denied.php";
}
?>