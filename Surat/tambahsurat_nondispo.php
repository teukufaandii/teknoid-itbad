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

if ($_SESSION['jabatan'] == 'Dosen') {
    header("Location: tambah_surat_dosen");
    exit();
}

$conn = new mysqli($host, $user, $pass, $db);

// Fungsi untuk mengecek apakah jenis surat yang dipilih adalah "Surat KKL"
function isSuratKKL($jenis_surat)
{
    return $jenis_surat === "3";
}

// Fungsi untuk mengecek apakah jenis surat yang dipilih adalah "Surat Cuti"
function isSuratRiset($jenis_surat)
{
    return $jenis_surat === "4";
}

// Memeriksa apakah form dikirimkan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis_surat = $_POST["jenis_surat"];
    $asal_surat = $_POST["asal_surat"];
    $perihal = $_POST["perihal"];
    $nomor_surat = $_POST["nomor_surat"];
    $tujuan_surat = $_POST["tujuan_surat"];
    $email = $_POST["email"];
    $prodi = $_POST["prodi"];
    $nama_lengkap = $_POST["nama_lengkap"];
    $nim = $_POST["nim"];
    $no_hp = $_POST["no_hp"];
    $nama_lengkap2 = $_POST["nama_lengkap2"];
    $nim2 = $_POST["nim2"];
    $no_hp2 = $_POST["no_hp2"];
    $nama_lengkap3 = $_POST["nama_lengkap3"];
    $nim3 = $_POST["nim3"];
    $no_hp3 = $_POST["no_hp3"];
    $deskripsi = $_POST["deskripsi"];
    $tanggal_surat = date("Y-m-d");
    $nama_perusahaan = $_POST["nama_perusahaan"];
    $alamat_perusahaan = $_POST["alamat_perusahaan"];
    $ttl = $_POST["ttl"];
    $alamat_domisili = $_POST["alamat_domisili"];
    $ke_humas = "Humas";

    $sql = "";
    if (isSuratKKL($jenis_surat)) {
        $sql = "INSERT INTO tb_surat_dis (jenis_surat, asal_surat, perihal, nomor_surat, tanggal_surat, tujuan_surat,
                     email, nama_lengkap, nim, no_hp,  nama_lengkap2, nim2, no_hp2, nama_lengkap3, nim3, no_hp3, 
                     prodi, nama_perusahaan, alamat_perusahaan, deskripsi, diteruskan_ke)
            VALUES ('$jenis_surat', '$asal_surat', '$perihal', '$nomor_surat', '$tanggal_surat', '$tujuan_surat', 
                    '$email', '$nama_lengkap', '$nim', '$no_hp', '$nama_lengkap2', '$nim2', '$no_hp2', '$nama_lengkap3', '$nim3', '$no_hp3', 
                    '$prodi', '$nama_perusahaan', '$alamat_perusahaan', '$deskripsi', '$ke_humas')";
    } elseif (isSuratRiset($jenis_surat)) {
        $sql = "INSERT INTO tb_surat_dis (jenis_surat, asal_surat, perihal, nomor_surat, tanggal_surat, 
                            tujuan_surat, email, nama_lengkap, nim, prodi, no_hp, nama_perusahaan, alamat_perusahaan, deskripsi, ttl, alamat_domisili, diteruskan_ke)
                    VALUES ('$jenis_surat', '$asal_surat', '$perihal', '$nomor_surat', '$tanggal_surat', '$tujuan_surat', '$email', 
                            '$nama_lengkap', '$nim', '$prodi', '$no_hp', '$nama_perusahaan', '$alamat_perusahaan', '$deskripsi', '$ttl', '$alamat_domisili', '$ke_humas')";
    }

    if ($conn->query($sql) === TRUE) {
        header('Location: success.php');
        header('Refresh: 1; URL=surat_keluar.php');
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Tambah Surat - Teknoid</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="../logo itbad.png">
    <link href="css/dashboard-style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <h3>Tambah Surat Non Disposisi</h3>
                    <button class="back" onclick="goBack()">Kembali</button>
                </div>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form" enctype="multipart/form-data">
                    <div class="inputfield">
                        <label for="jenis_surat">Jenis Surat*</label>
                        <div class="custom_select">
                            <select name="jenis_surat" id="jenis_surat" class="select" required>
                                <option value="" hidden>Pilih Jenis Surat</option>
                                <option value="3">Surat KKL</option>
                                <option value="4">Surat Riset</option>
                            </select>
                        </div>
                    </div>

                    <div class="inputfield">
                        <label for="">Asal Surat*</label>
                        <input type="text" class="input" name="asal_surat" placeholder="Masukkan Asal Surat" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" readonly>
                    </div>

                    <div class="inputfield" id="jumlah_mahasiswa_atas">
                        <label for="jumlah_mahasiswa">Jumlah Mahasiswa*</label>
                        <div class="custom_select">
                            <select name="jumlah_mahasiswa" id="jumlah_mahasiswa" class="select">
                                <option value="" hidden>Pilih Jumlah Mahasiswa</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                    </div>

                    <div id="mahasiswa_fields">
                        <!-- Input untuk Mahasiswa 1 -->
                        <div class="inputfield">
                            <label for="nama_mahasiswa1">Nama Mahasiswa <b>1</b>*</label>
                            <input type="text" class="input" name="nama_lengkap" placeholder="Masukkan Nama Mahasiswa 1">
                        </div>
                        <div class="inputfield">
                            <label for="nim_mahasiswa1">NIM Mahasiswa <b>1</b>*</label>
                            <input type="number" class="input" name="nim" placeholder="Masukkan NIM Mahasiswa 1">
                        </div>
                        <div class="inputfield">
                            <label for="no_hp1">Nomor Telepon <b>1</b>*</label>
                            <input type="number" class="input" name="no_hp" placeholder="Masukkan Nomor Telepon Mahasiswa 1">
                        </div>

                        <!-- Input untuk Mahasiswa 2 -->
                        <div class="inputfield" id="mahasiswa2" style="display: none;">
                            <label for="nama_mahasiswa2">Nama Mahasiswa <b>2</b>*</label>
                            <input type="text" class="input" name="nama_lengkap2" placeholder="Masukkan Nama Mahasiswa 2">
                        </div>
                        <div class="inputfield" id="nim_mahasiswa2" style="display: none;">
                            <label for="nim_mahasiswa2">NIM Mahasiswa <b>2</b>*</label>
                            <input type="number" class="input" name="nim2" placeholder="Masukkan NIM Mahasiswa 2">
                        </div>
                        <div class="inputfield" id="no_hp2" style="display: none;">
                            <label for="no_hp2">Nomor Telepon <b>2</b>*</label>
                            <input type="number" class="input" name="no_hp2" placeholder="Masukkan Nomor Telepon Mahasiswa 2">
                        </div>

                        <!-- Input untuk Mahasiswa 3 -->
                        <div class="inputfield" id="mahasiswa3" style="display: none;">
                            <label for="nama_mahasiswa3">Nama Mahasiswa <b>3</b>*</label>
                            <input type="text" class="input" name="nama_lengkap3" placeholder="Masukkan Nama Mahasiswa 3">
                        </div>
                        <div class="inputfield" id="nim_mahasiswa3" style="display: none;">
                            <label for="nim_mahasiswa3">NIM Mahasiswa <b>3</b>*</label>
                            <input type="number" class="input" name="nim3" placeholder="Masukkan NIM Mahasiswa 3">
                        </div>
                        <div class="inputfield" id="no_hp3" style="display: none; margin-bottom: 15px;">
                            <label for="no_hp3">Nomor Telepon <b>3</b>*</label>
                            <input type="number" class="input" name="no_hp3" placeholder="Masukkan Nomor Telepon Mahasiswa 3">
                        </div>
                    </div>

                    <!-- Input tambahan untuk Surat Riset -->
                    <div class="inputfield" id="surat_riset_fields_1" style="display: none;">
                        <label for="ttl">Tempat, tanggal lahir*</label>
                        <input type="text" class="input" name="ttl" id="ttl" placeholder="Masukkan Tempat, tanggal lahir">
                    </div>

                    <div class="inputfield" id="surat_riset_fields_2" style="display: none;">
                        <label for="alamat_domisili">Alamat Domisili*</label>
                        <input type="text" class="input" name="alamat_domisili" id="alamat_domisili" placeholder="Masukkan Alamat Domisili">
                    </div>

                    <div class="inputfield">
                        <label for="">Perihal*</label>
                        <input type="text" class="input" name="perihal" placeholder="Masukkan Perihal" required>
                    </div>

                    <div class="inputfield">
                        <label for="">Alamat Email*</label>
                        <input type="email" class="input" name="email" placeholder="Masukkan Alamat Email" required>
                    </div>

                    <div class="inputfield">
                        <label for="">Program Studi*</label>
                        <div class="custom_select">
                            <select name="prodi" id="prodi" class="select" required>
                                <option value="" hidden>Pilih Program Studi</option>
                                <option>Prodi S2 Keuangan Syariah</option>
                                <option>Prodi S1 Sistem Informasi</option>
                                <option>Prodi S1 Teknologi Informasi</option>
                                <option>Prodi S1 Desain Komunikasi Visual</option>
                                <option>Prodi S1 Arsitektur</option>
                                <option>Prodi S1 Manajemen</option>
                                <option>Prodi S1 Akuntansi</option>
                                <option>Prodi D3 Akuntansi</option>
                                <option>Prodi D3 Keuangan dan Perbankan</option>
                            </select>
                        </div>
                    </div>


                    <div class="inputfield">
                        <label for="">Deskripsi Singkat</label>
                        <input type="text" class="input" name="deskripsi" placeholder="Masukkan Deskripsi Singkat" maxlength="200">
                    </div>

                    <!-- Input tambahan untuk Surat KKL -->
                    <div class="inputfield" id="surat_kkl_fields" style="display: none;">
                        <label for="nama_perusahaan">Nama Perusahaan* </label>
                        <input type="text" class="input" name="nama_perusahaan" id="nama_perusahaan" placeholder="Masukkan Nama Perusahaan">

                        <label for="alamat_perusahaan">Alamat Perusahaan*</label>
                        <input type="text" class="input" name="alamat_perusahaan" id="alamat_perusahaan" placeholder="Masukkan Alamat Perusahaan">
                    </div>

                    <!-- Input tambahan untuk Surat Riset -->
                    <div class="inputfield" id="surat_riset_fields_3" style="display: none;">
                        <label for="nama_perusahaan">Nama Perusahaan* </label>
                        <input type="text" class="input" name="nama_perusahaan" id="nama_perusahaan" placeholder="Masukkan Nama Perusahaan">
                    </div>
                    <div class="inputfield" id="surat_riset_fields_4" style="display: none;">
                        <label for="alamat_perusahaan">Alamat Perusahaan*</label>
                        <input type="text" class="input" name="alamat_perusahaan" id="alamat_perusahaan" placeholder="Masukkan Alamat Perusahaan">
                    </div>

                    <!--untuk Tujuan Surat-->
                    <div class="inputfield">
                        <label for=""></label>
                        <input type="text" class="input" name="tujuan_surat" placeholder="Masukkan Tujuan Surat" value="Humas" hidden>
                    </div>

                    <!--untuk nomor surat optional-->
                    <div class="inputfield">
                        <label for=""></label>
                        <input type="text" class="input" name="nomor_surat" placeholder="Masukkan Nomor Surat" hidden>
                    </div>

                    <div class="btn-kirim">
                        <div class="floatFiller">ff</div>
                        <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                        <button class="btn" type="button" onclick="showConfirmationPopup()">Kirim</button>
                    </div>
                </form>

            </div>
        </div>
        <?php include 'footer.php'; ?>
    </div>
    <script src="js/dashboard-js.js"></script>

    <script>
        // Function to show SweetAlert confirmation popup
        function showConfirmationPopup() {
            Swal.fire({
                title: 'Konfirmasi?',
                text: 'Anda yakin ingin mengirim surat?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, kirim!',
                cancelButtonText: 'Tidak, periksa kembali',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, trigger form submission
                    document.getElementById('submitForm').click();
                }
            });
        }

        // Function to handle form submission response
        function handleFormSubmissionResponse(success) {
            if (success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'The letter has been sent successfully.',
                    icon: 'success'
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to send the letter. Please try again later.',
                    icon: 'error'
                });
            }
        }
    </script>

    <script>
        function goBack() {
            window.history.back();
        }

        document.getElementById('jenis_surat').addEventListener('change', function() {
            let suratKKLFields = document.getElementById('surat_kkl_fields');
            let suratRisetFields1 = document.querySelectorAll('#surat_riset_fields_1, #surat_riset_fields_2');
            let suratRisetFields2 = document.querySelectorAll('#surat_riset_fields_3, #surat_riset_fields_4');
            let jumlahMahasiswaField = document.getElementById('jumlah_mahasiswa_atas');
            let mahasiswaField = document.getElementById('mahasiswa2');
            let mahasiswa3 = document.getElementById('mahasiswa3');
            let nim2 = document.getElementById('nim_mahasiswa2');
            let nim3 = document.getElementById('nim_mahasiswa3');
            let no_hp2 = document.getElementById('no_hp2');
            let no_hp3 = document.getElementById('no_hp3');


            // Tampilkan atau sembunyikan input tambahan untuk Surat KKL atau Surat Riset
            if (this.value === '3') {
                suratKKLFields.style.display = 'none';
                // Mengakses elemen-elemen dalam kumpulan suratRisetFields1 dan suratRisetFields2 menggunakan indeks
                for (let i = 0; i < suratRisetFields1.length; i++) {
                    suratRisetFields1[i].style.display = 'none'; // Sembunyikan div pertama
                    suratRisetFields2[i].style.display = 'flex'; // Sembunyikan div kedua
                }
                jumlahMahasiswaField.style.display = 'flex'; // Tampilkan input jumlah mahasiswa
            } else if (this.value === '4') {
                suratKKLFields.style.display = 'none';
                // Mengakses elemen-elemen dalam kumpulan suratRisetFields1 dan suratRisetFields2 menggunakan indeks
                for (let i = 0; i < suratRisetFields1.length; i++) {
                    suratRisetFields1[i].style.display = 'flex'; // Tampilkan div pertama
                    suratRisetFields2[i].style.display = 'flex'; // Tampilkan div kedua
                }
                jumlahMahasiswaField.style.display = 'none'; // Sembunyikan input jumlah mahasiswa
                mahasiswaField.style.display = 'none';
                mahasiswa3.style.display = 'none';
                nim2.style.display = 'none';
                no_hp2.style.display = 'none';
                no_hp3.style.display = 'none';
            } else {
                suratKKLFields.style.display = 'none';
                // Mengakses elemen-elemen dalam kumpulan suratRisetFields1 dan suratRisetFields2 menggunakan indeks
                for (let i = 0; i < suratRisetFields1.length; i++) {
                    suratRisetFields1[i].style.display = 'none'; // Sembunyikan div pertama
                    suratRisetFields2[i].style.display = 'none'; // Sembunyikan div kedua
                }
                jumlahMahasiswaField.style.display = 'none'; // Sembunyikan input jumlah mahasiswa
            }
        });


        document.getElementById('jumlah_mahasiswa').addEventListener('change', function() {
            let mahasiswaFields = document.getElementById('mahasiswa_fields');
            let mahasiswa2 = document.getElementById('mahasiswa2');
            let no_hp2 = document.getElementById('no_hp2');
            let nim_mahasiswa2 = document.getElementById('nim_mahasiswa2');
            let mahasiswa3 = document.getElementById('mahasiswa3');
            let nim_mahasiswa3 = document.getElementById('nim_mahasiswa3');
            let no_hp3 = document.getElementById('no_hp3');


            // Tampilkan atau sembunyikan input untuk Mahasiswa 2 dan Mahasiswa 3 sesuai dengan jumlah yang dipilih
            if (this.value === '1') {
                mahasiswa2.style.display = 'none';
                nim_mahasiswa2.style.display = 'none';
                no_hp2.style.display = 'none';
                mahasiswa3.style.display = 'none';
                nim_mahasiswa3.style.display = 'none';
                no_hp3.style.display = 'none';
            } else if (this.value === '2') {
                mahasiswa2.style.display = 'flex';
                nim_mahasiswa2.style.display = 'flex';
                no_hp2.style.display = 'flex';
                mahasiswa3.style.display = 'none';
                nim_mahasiswa3.style.display = 'none';
                no_hp3.style.display = 'none';
            } else if (this.value === '3') {
                mahasiswa2.style.display = 'flex';
                nim_mahasiswa2.style.display = 'flex';
                no_hp2.style.display = 'flex';
                mahasiswa3.style.display = 'flex';
                nim_mahasiswa3.style.display = 'flex';
                no_hp3.style.display = 'flex';
            }
        });
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>

<?php
// Tutup koneksi database
$conn->close();
?>