<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
include 'koneksi.php';
include "logout-checker.php";
// Periksa apakah session username telah diatur
if (!isset($_SESSION['pengguna_type'])) {
    echo '<script language="javascript" type="text/javascript">
    alert("Anda Tidak Berhak Masuk Kehalaman Ini!");</script>';
    echo "<meta http-equiv='refresh' content='0; url=../index.php'>";
    exit;
}

$id = $_GET['id'] ?? null;

// Fetch data from the first table based on the provided ID
$sql1 = "SELECT sd.jenis_surat, sd.verifikasi, sd.memo, sd.asal_surat, sd.status_pengusul, sd.NIDN, sd.no_telpon, sd.id_sinta, sd.prodi_pengusul, 
            sd.jenis_insentif, sd.skema_ppmdpek, sd.judul_penelitian_ppm, sd.jenis_publikasi_pi, sd.nama_jurnal_pi, sd.vol_notahun_pi, 
            sd.link_jurnal_pi, sd.skala_ppdpi, sd.nama_pertemuan_ppdpi, sd.usulan_biaya_ppdpi, sd.skala_ppdks, sd.nama_pertemuan_ppdks, sd.nm_kegiatan_vl, sd.waktu_pelaksanaan_vl,
            sd.jenis_hki, sd.judul_hki, sd.teknologi_tg, sd.deskripsi_tg, sd.jenis_buku, sd.judul_buku, sd.sinopsis_buku, sd.isbn_buku, sd.judul_publikasi_pi,
            sd.nama_model_mpdks, sd.deskripsi_mpdks, sd.judul_ipbk, sd.namaPenerbit_dan_waktu_ipbk, sd.link_publikasi_ipbk, j.nama_jenis
         FROM tb_srt_dosen sd
         INNER JOIN tb_jenis j ON sd.jenis_surat = j.kd_jenissurat
         WHERE sd.id_srt = ?";

$stmt1 = $koneksi->prepare($sql1);
$stmt1->bind_param("i", $id);
$stmt1->execute();
$stmt1->bind_result(
    $jenis_surat,
    $verifikasi,
    $memo,
    $asal_surat,
    $status_pengusul,
    $NIDN,
    $no_telpon,
    $id_sinta,
    $prodi_pengusul,
    $jenis_insentif,
    $skema_ppmdpek,
    $judul_penelitian_ppm,
    $jenis_publikasi_pi,
    $nama_jurnal_pi,
    $vol_notahun_pi,
    $link_jurnal_pi,
    $skala_ppdpi,
    $nama_pertemuan_ppdpi,
    $usulan_biaya_ppdpi,
    $skala_ppdks,
    $nama_pertemuan_ppdks,
    $nm_kegiatan_vl,
    $waktu_pelaksanaan_vl,
    $jenis_hki,
    $judul_hki,
    $teknologi_tg,
    $deskripsi_tg,
    $jenis_buku,
    $judul_buku,
    $sinopsis_buku,
    $isbn_buku,
    $judul_publikasi_pi,
    $nama_model_mpdks,
    $deskripsi_mpdks,
    $judul_ipbk,
    $namaPenerbit_dan_waktu_ipbk,
    $link_publikasi_ipbk,
    $nama_jenis,
);

$stmt1->fetch();
$stmt1->close();


$sql3 = "SELECT 
  file_berkas_insentif_ppm, 
  file_berkas_insentif_pi, 
  file_berkas_insentif_ppdpi,  
  file_berkas_insentif_ppdks, 
  file_berkas_insentif_vl, 
  file_berkas_insentif_hki, 
  file_berkas_insentif_tg, 
  file_berkas_insentif_buku, 
  file_berkas_insentif_mpdks, 
  file_berkas_insentif_ipbk
FROM tb_srt_dosen
WHERE id_srt = ?";

// Menyiapkan statement
$stmt3 = $koneksi->prepare($sql3);

// Mengikat parameter untuk WHERE clause
$stmt3->bind_param("i", $id);

// Menjalankan query
$stmt3->execute();

// Mengambil hasil sebagai array
$stmt3->store_result();
$stmt3->bind_result(
    $file_berkas_insentif_ppm,
    $file_berkas_insentif_pi,
    $file_berkas_insentif_ppdpi,
    $file_berkas_insentif_ppdks,
    $file_berkas_insentif_vl,
    $file_berkas_insentif_hki,
    $file_berkas_insentif_tg,
    $file_berkas_insentif_buku,
    $file_berkas_insentif_mpdks,
    $file_berkas_insentif_ipbk
);

// Memasukkan semua hasil ke dalam array
$row = [];
if ($stmt3->fetch()) {
    $row = [
        'file_berkas_insentif_ppm' => $file_berkas_insentif_ppm,
        'file_berkas_insentif_pi' => $file_berkas_insentif_pi,
        'file_berkas_insentif_ppdpi' => $file_berkas_insentif_ppdpi,
        'file_berkas_insentif_ppdks' => $file_berkas_insentif_ppdks,
        'file_berkas_insentif_vl' => $file_berkas_insentif_vl,
        'file_berkas_insentif_hki' => $file_berkas_insentif_hki,
        'file_berkas_insentif_tg' => $file_berkas_insentif_tg,
        'file_berkas_insentif_buku' => $file_berkas_insentif_buku,
        'file_berkas_insentif_mpdks' => $file_berkas_insentif_mpdks,
        'file_berkas_insentif_ipbk' => $file_berkas_insentif_ipbk
    ];
}

$stmt3->close();
$file_berkas_combined = implode(", ", array_filter($row));
$file_berkas_exists = !empty($file_berkas_combined);

$sql4 = "SELECT 
  file_berkas_ppm, 
  file_berkas_pi, 
  file_berkas_ppdpi, 
  file_berkas_ppdks, 
  file_berkas_vl, 
  file_berkas_hki, 
  file_berkas_tg, 
  file_berkas_buku,
  file_berkas_mpdks, 
  file_berkas_ipbk
FROM tb_srt_dosen 
WHERE id_srt = ?";

// Menyiapkan statement
$stmt4 = $koneksi->prepare($sql4);

// Mengikat parameter untuk WHERE clause
$stmt4->bind_param("i", $id);

// Menjalankan query
$stmt4->execute();

// Mengambil hasil dan menyimpannya dalam array
$stmt4->store_result();
$stmt4->bind_result(
    $file_berkas_ppm,
    $file_berkas_pi,
    $file_berkas_ppdpi,
    $file_berkas_ppdks,
    $file_berkas_vl,
    $file_berkas_hki,
    $file_berkas_tg,
    $file_berkas_buku,
    $file_berkas_mpdks,
    $file_berkas_ipbk
);

// Memasukkan hasil binding ke dalam array asosiatif
$row = [];
if ($stmt4->fetch()) {
    $row = [
        'file_berkas_ppm' => $file_berkas_ppm,
        'file_berkas_pi' => $file_berkas_pi,
        'file_berkas_ppdpi' => $file_berkas_ppdpi,
        'file_berkas_ppdks' => $file_berkas_ppdks,
        'file_berkas_vl' => $file_berkas_vl,
        'file_berkas_hki' => $file_berkas_hki,
        'file_berkas_tg' => $file_berkas_tg,
        'file_berkas_buku' => $file_berkas_buku,
        'file_berkas_mpdks' => $file_berkas_mpdks,
        'file_berkas_ipbk' => $file_berkas_ipbk,
    ];
}



$stmt4->close();
$file_berkas_combined_pendukung = implode(", ", array_filter($row));

$sql5 = "SELECT jenis_surat FROM tb_srt_dosen WHERE id_srt = ?";
$stmt5 = $koneksi->prepare($sql5);
$stmt5->bind_param("i", $id);
$stmt5->execute();
$stmt5->bind_result($jenis_surat);
$stmt5->fetch();
$stmt5->close();

$file_berkas_exists = !empty($file_berkas_combined);
$file_berkas_pendukung = !empty($file_berkas_combined_pendukung);
?>

<!DOCTYPE html>
<html lang="eng">

<head>
    <title>Disposisi - Teknoid</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="../logo itbad.png">
    <link href="css/dashboard-style.css" rel="stylesheet">
    <link href="css/disposisi-style.css" rel="stylesheet">
    <link href="css/dispo-dosen.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Style untuk modal-content */
        .modal-content-file {
            background-color: whitesmoke;
            margin: auto;
            height: 85vh;
            width: 80%;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(5px);
            /* Apply blur effect */
        }

        .modal .modal-content-file h2 {
            color: black;
            padding-top: 20px;
        }

        /* Style untuk tombol close */
        .close {
            color: #aaa;
            float: right;
            padding-right: 5vw;
            margin-right: 0px;
            font-size: 35px;
            font-weight: bold;
            color: red;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Style untuk iframe di dalam modal tampilan laporan */
        iframe {
            border: 1px solid black;
        }

        #berkasFrame,
        #laporanFrame {
            width: 90%;
            height: 60vh;
            margin: auto;
        }

        @media (max-width: 600px) {
            .modal-content-file {
                width: 90%;
                height: 70vh;
            }

            #berkasFrame,
            #laporanFrame {
                width: 90%;
                height: 60vh;
                margin: auto;
            }

            .close {
                font-size: 25px;
                margin-right: 10px;
            }
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
                    <script>
                        // Mendapatkan nilai dari PHP
                        let jenisInsentif = "<?php echo $jenis_insentif; ?>";

                        // Mengganti underscore dengan spasi
                        jenisInsentif = jenisInsentif.replace(/_/g, ' ');

                        // Mengubah huruf depan per kata menjadi kapital
                        jenisInsentif = jenisInsentif.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');

                        // Menampilkan hasil berdasarkan jenis surat
                        const jenisSurat = <?php echo $jenis_surat; ?>;
                        let header;

                        if (jenisSurat === 5) {
                            header = `<h3>Disposisi Insentif - ${jenisInsentif}</h3>`;
                        }

                        // Menampilkan header di DOM
                        document.write(header);
                    </script>
                    <button onclick="goBack()" class="back">Kembali</button>
                </div>

                <div class="loading-overlay" id="loadingOverlay">
                    <div class="spinner"></div>
                </div>

                <form class="form">
                    <div class="input-field">
                        <label for="">Nama Pengusul</label>
                        <input type="text" class="input" name="#" value="<?php echo $asal_surat; ?> " readonly>
                    </div>

                    <div class="input-field">
                        <label for="">NIDN</label>
                        <input type="text" class="input" name="#" value="<?php echo $NIDN; ?> " readonly>
                    </div>

                    <div class="input-field">
                        <label for="">NO Telpon/HP</label>
                        <input type="text" class="input" name="#" value="<?php echo $no_telpon; ?> " readonly>
                    </div>

                    <?php if ($jenis_surat == 5) { ?>

                        <div class="input-field">
                            <label for="">Status Pengusul</label>
                            <input type="text" class="input" name="#" value="<?php echo $status_pengusul; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">ID Sinta</label>
                            <input type="text" class="input" name="#" value="<?php echo $id_sinta; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Program Studi Pengusul</label>
                            <input type="text" class="input" name="#" value="<?php echo $prodi_pengusul; ?> " readonly>
                        </div>

                        <div class="input-field">
                            <label for="">Jenis Insentif</label>
                            <input type="text" class="input" name="#" value="<?php
                                                                                $jenis_surat_text = 'Unknown';
                                                                                if ($_SESSION['jabatan'] == 'LP3M' || $_SESSION['jabatan'] == 'Dosen') {
                                                                                    switch ($jenis_insentif) {
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
                                                                                            $jenis_surat_text = 'Teknologi Tepat Guna';
                                                                                            break;
                                                                                        case 'buku':
                                                                                            $jenis_surat_text = 'Buku';
                                                                                            break;
                                                                                        case 'model':
                                                                                            $jenis_surat_text = 'Model, Prototype, Desain, Karya Seni, Rekayasa Sosial, Kebijakan';
                                                                                            break;
                                                                                        case 'insentif_publikasi':
                                                                                            $jenis_surat_text = 'Insentif Publikasi Berita Kegiatan Pengabdian Masyarakat';
                                                                                            break;
                                                                                        default:
                                                                                            $jenis_surat_text = 'Unknown';
                                                                                            break;
                                                                                    }
                                                                                }
                                                                                echo htmlspecialchars($jenis_surat_text);
                                                                                ?>" readonly>
                        </div>



                        <?php if ($jenis_insentif == 'penelitian') { ?>
                            <div class="input-field">
                                <label for="">Judul Penelitian/Pengabdian Masyarakat</label>
                                <input type="text" class="input" name="#" value="<?php echo $judul_penelitian_ppm; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Skema</label>
                                <input type="text" class="input" name="#" value="<?php echo $skema_ppmdpek; ?>" readonly>
                            </div>

                        <?php } elseif ($jenis_insentif == 'publikasi') { ?>
                            <div class="input-field">
                                <label for="">Jenis Publikasi/Jurnal</label>
                                <input type="text" class="input" name="#" value="<?php echo $jenis_publikasi_pi; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Nama Jurnal/Koran/Majalah/Penerbit</label>
                                <input type="text" class="input" name="#" value="<?php echo $nama_jurnal_pi; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Vol. No. Tahun. ISSN-Edisi-Halaman</label>
                                <input type="text" class="input" name="#" value="<?php echo $vol_notahun_pi; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Tautan/Link jurnal atau berkas pendukung (wajib untuk artikel pada jurnal ilmiah)</label>
                                <input type="text" class="input" name="#" value="<?php echo $link_jurnal_pi; ?>" readonly>
                            </div>

                        <?php } elseif ($jenis_insentif == 'pertemuan_ilmiah') { ?>
                            <div class="input-field">
                                <label for="">Skala</label>
                                <input type="text" class="input" name="#" value="<?php echo $skala_ppdpi; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Nama Pertemuan</label>
                                <input type="text" class="input" name="#" value="<?php echo $nama_pertemuan_ppdpi; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Usulan Biaya</label>
                                <input type="text" class="input" name="#" value="<?php echo $usulan_biaya_ppdpi; ?>" readonly>
                            </div>

                        <?php } elseif ($jenis_insentif == 'keynote_speaker') { ?>
                            <div class="input-field">
                                <label for="">Skala</label>
                                <input type="text" class="input" name="#" value="<?php echo $skala_ppdks; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Nama Pertemuan Ilmiah</label>
                                <input type="text" class="input" name="#" value="<?php echo $nama_pertemuan_ppdks; ?>" readonly>
                            </div>

                        <?php } elseif ($jenis_insentif == 'visiting') { ?>
                            <div class="input-field">
                                <label for="">Nama Kegiatan dan Lembaga tujuan</label>
                                <input type="text" class="input" name="#" value="<?php echo $nm_kegiatan_vl; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Waktu Pelaksanaan</label>
                                <input type="text" class="input" name="#" value="<?php echo $waktu_pelaksanaan_vl; ?>" readonly>
                            </div>

                        <?php } elseif ($jenis_insentif == 'hki') { ?>
                            <div class="input-field">
                                <label for="">Jenis Kekayaan Intelektual</label>
                                <input type="text" class="input" name="#" value="<?php echo $jenis_hki; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Judul Kekayaan Intelektual</label>
                                <input type="text" class="input" name="#" value="<?php echo $judul_hki; ?>" readonly>
                            </div>

                        <?php } elseif ($jenis_insentif == 'teknologi') { ?>
                            <div class="input-field">
                                <label for="">Tekonologi tepat guna yang diusulkan</label>
                                <input type="text" class="input" name="#" value="<?php echo $teknologi_tg; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Deskripsi tekonologi tepat guna yang diusulkan</label>
                                <input type="text" class="input" name="#" value="<?php echo $deskripsi_tg; ?>" readonly>
                            </div>

                        <?php } elseif ($jenis_insentif == 'model') { ?>
                            <div class="input-field">
                                <label for="">Nama Model</label>
                                <input type="text" class="input" name="#" value="<?php echo $nama_model_mpdks; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Deskripsi Model</label>
                                <input type="text" class="input" name="#" value="<?php echo $deskripsi_mpdks; ?>" readonly>
                            </div>

                        <?php } elseif ($jenis_insentif == 'buku') { ?>
                            <div class="input-field">
                                <label for="">Jenis Buku</label>
                                <input type="text" class="input" name="#" value="<?php echo $jenis_buku; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Judul Buku</label>
                                <input type="text" class="input" name="#" value="<?php echo $judul_buku; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Sinopsis</label>
                                <input type="text" class="input" name="#" value="<?php echo $sinopsis_buku; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">ISBN/Jumlah halaman/Penerbit</label>
                                <input type="text" class="input" name="#" value="<?php echo $isbn_buku; ?>" readonly>
                            </div>

                        <?php } elseif ($jenis_insentif == 'insentif_publikasi') { ?>
                            <div class="input-field">
                                <label for="">Judul Publikasi</label>
                                <input type="text" class="input" name="#" value="<?php echo $judul_ipbk; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Nama Penerbit dan Waktu terbit</label>
                                <input type="text" class="input" name="#" value="<?php echo $namaPenerbit_dan_waktu_ipbk; ?>" readonly>
                            </div>

                            <div class="input-field">
                                <label for="">Tautan Publikasi</label>
                                <input type="text" class="input" name="#" value="<?php echo $link_publikasi_ipbk; ?>" readonly>
                            </div>

                        <?php } ?>

                        <?php
                        if ($jenis_surat == 5) : ?>
                            <div class="input-field">
                                <label></label>
                                <div class="input" style="padding: 0 !important; color: black; background-color: rgba(0, 0, 0, 0); border: none">
                                    <div class="lihat">
                                        <?php if ($file_berkas_exists) : ?>
                                            <?php
                                            // Path untuk insentif
                                            $file_insentif_path = "uploads/dosen/" .  $file_berkas_combined;
                                            ?>
                                            <button type="button" onclick="lihatBerkas('<?php echo $file_insentif_path; ?>')">
                                                Lihat Berkas Insentif
                                            </button>
                                        <?php else : ?>
                                            <p>Tidak ada berkas insentif yang tersedia.</p>
                                        <?php endif; ?>

                                        <?php if ($file_berkas_pendukung) : ?>
                                            <?php
                                            // Path untuk insentif
                                            $file_pendukung_path = "uploads/dosen/" . $file_berkas_combined_pendukung;
                                            ?>
                                            <button type="button" onclick="lihatInsentif('<?php echo $file_pendukung_path; ?>')">
                                                Lihat Berkas Pendukung
                                            </button>
                                        <?php else : ?>
                                            <p>Tidak ada berkas insentif pendukung yang tersedia.</p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Modal untuk tampilan berkas -->
                                    <div id="modalBerkas" class="modal">
                                        <span class="close" onclick="closeModal()">&times;</span>
                                        <div class="modal-content-file">
                                            <h2>PREVIEW BERKAS INSENTIF</h2>
                                            <iframe id="berkasFrame" frameborder="0"></iframe>
                                        </div>
                                    </div>

                                    <!-- Modal untuk tampilan laporan -->
                                    <div id="modalLaporan" class="modal">
                                        <span class="close" onclick="closeModalInsentif()">&times;</span>
                                        <div class="modal-content-file">
                                            <h2>PREVIEW BERKAS PENDUKUNG</h2>
                                            <iframe id="laporanFrame" frameborder="0"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php } ?>

                    <?php if ($_SESSION['jabatan'] == 'LP3M') : ?>
                        <div class="txt-disposisi">
                            <h3> Memo </h3>
                        </div>

                        <div class="input-disposisi">
                            <label for="">Memo</label>
                            <input type="text" class="input" placeholder="Masukkan Catatan" name="memo" id="memo" value="<?php echo $memo; ?>" <?php echo (!empty($memo)) ? 'readonly style="background-color: #ccc;"' : ''; ?> required>
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                        </div>

                        <div class="btn-kirim-dsn" style="display: flex; justify-content: end; gap: 15px; position: relative; top: 20px;">
                            <button id="btnKirim" type="button" style="cursor: pointer; text-align: center; height: 40px; width: 160px;">Kirim Memo</button>
                            <button id="btnVerifikasi" type="button" style="border-radius: 8px; cursor: pointer; text-align: center; color: #fff; background-color: #31763d; height: 40px; width: 100px; padding: 0 10px;">Verifikasi</button>
                        </div>
                    <?php endif; ?>

                </form>
            </div>
        </div>
        <?php include './footer.php'; ?>
    </div>


    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        document.getElementById('btnKirim').addEventListener('click', function() {
            const memo = document.getElementById('memo').value;

            if (memo.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Memo kosong',
                    text: 'Silakan isi memo terlebih dahulu.'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Pengiriman',
                text: "Apakah Anda yakin ingin mengirim memo ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, kirim!'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendRequest('kirim');
                }
            });
        });

        document.getElementById('btnVerifikasi').addEventListener('click', function() {
            Swal.fire({
                title: 'Konfirmasi Verifikasi',
                text: "Apakah Anda yakin ingin melakukan verifikasi?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, verifikasi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendRequest('verifikasi');
                }
            });
        });

        function sendRequest(action) {
            const memo = document.getElementById('memo').value;
            const id_srt = document.querySelector('input[name="id"]').value;

            fetch('update_memo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'action': action,
                        'memo': memo,
                        'id_srt': id_srt
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        if (action === 'kirim') {
                            Swal.fire('Berhasil', 'Catatan berhasil ditambahkan.', 'success');
                            setTimeout(() => {
                                window.location.href = 'surat_masuk_insentif.php';
                            }, 3000);
                        } else if (action === 'verifikasi') {
                            Swal.fire({
                                title: 'Verifikasi Berhasil',
                                text: 'Verifikasi berhasil dilakukan.',
                                icon: 'success'
                            });
                            setTimeout(() => {
                                window.location.href = 'surat_masuk_insentif.php';
                            }, 3000);
                        }
                    } else {
                        Swal.fire('Gagal', 'Update gagal: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan dalam permintaan.', 'error');
                });
        }
    </script>

    <!-- Modal script -->
    <script>
        // Function to display file modal and set iframe source for files
        function lihatBerkas(filePath) {
            document.getElementById("berkasFrame").src = filePath;
            document.getElementById("modalBerkas").style.display = "block";
        }

        function closeModal() {
            document.getElementById("modalBerkas").style.display = "none";
        }

        function lihatInsentif(filePath) {
            closeModal();
            document.getElementById("laporanFrame").src = filePath;
            document.getElementById("modalLaporan").style.display = "block";
        }

        // Function to close report modal
        function closeModalInsentif() {
            document.getElementById("modalLaporan").style.display = "none";
        }
    </script>

    <!-- Close modal script -->
    <script>
        // Function to close modals when clicking outside
        window.onclick = function(event) {
            var modalBerkas = document.getElementById("modalBerkas");
            var modalLaporan = document.getElementById("modalLaporan");
            if (event.target == modalBerkas) {
                modalBerkas.style.display = "none";
            }
            if (event.target == modalLaporan) {
                modalLaporan.style.display = "none";
            }
        }
    </script>

    <script>
        var dt = new Date();
        document.getElementById("tanggalwaktu").innerHTML = (("0" + dt.getDate()).slice(-2)) + "/" + (("0" + (dt.getMonth() + 1)).slice(-2)) + "/" + (dt.getFullYear());
    </script>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
    <script src="js/dashboard-js.js"></script>

</body>

</html>