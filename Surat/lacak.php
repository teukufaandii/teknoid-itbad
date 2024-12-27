<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
include "logout-checker.php";
include 'koneksi.php';
if (!isset($_SESSION['pengguna_type'])) {
    echo '<script language="javascript" type="text/javascript">
    alert("Anda Tidak Berhak Masuk Kehalaman Ini!");</script>';
    echo "<meta http-equiv='refresh' content='0; url=../index.php'>";
    exit;
}

// Check if ID is provided in the URL
$id = $_GET['id'] ?? null;

// Fetch data from the first table based on the provided ID
$sql1 = "SELECT kode_surat, kd_surat, tujuan_surat, perihal FROM tb_surat_dis WHERE id_surat = ?";
$stmt1 = $koneksi->prepare($sql1);
$stmt1->bind_param("i", $id);
$stmt1->execute();
$stmt1->bind_result($kode_surat, $kode_surat2, $tujuan_surat, $perihal);
$stmt1->fetch();
$stmt1->close();

// Fetch data from the second table based on the provided ID
$sql2 = "SELECT 
            d.dispo1, d.dispo2, d.dispo3, d.dispo4, d.dispo5, d.dispo6, d.dispo7, d.dispo8, d.dispo9, d.dispo10, 
            d.catatan_disposisi, d.catatan_disposisi2, d.catatan_disposisi3, d.catatan_disposisi4, d.catatan_disposisi5, d.catatan_disposisi6, d.catatan_disposisi7, d.catatan_disposisi8, d.catatan_disposisi9, d.catatan_disposisi10,
            d.tanggal_disposisi1, d.tanggal_disposisi2, d.tanggal_disposisi3, d.tanggal_disposisi4, d.tanggal_disposisi5, d.tanggal_disposisi6, d.tanggal_disposisi7, d.tanggal_disposisi8, d.tanggal_disposisi9, d.tanggal_disposisi10,
            d.tanggal_eksekutor, d.diteruskan_ke, d.nama_selesai, d.nama_selesai2, d.nama_selesai3, d.nama_selesai4, d.nama_selesai5, d.nama_selesai6, d.nama_selesai7, d.nama_penolak, 
            d.catatan_selesai, d.catatan_selesai2, d.catatan_selesai3, d.catatan_selesai4, d.catatan_selesai5, d.catatan_selesai6, d.catatan_selesai7, d.catatan_tolak,
            s.jenis_surat
        FROM tb_disposisi d
        JOIN tb_surat_dis s ON s.id_surat = d.id_surat
        WHERE d.id_surat = ?";
$stmt2 = $koneksi->prepare($sql2);
$stmt2->bind_param("i", $id);
$stmt2->execute();
$stmt2->bind_result(
    $disposisi1,
    $disposisi2,
    $disposisi3,
    $disposisi4,
    $disposisi5,
    $disposisi6,
    $disposisi7,
    $disposisi8,
    $disposisi9,
    $disposisi10,
    $catatan_disposisi1,
    $catatan_disposisi2,
    $catatan_disposisi3,
    $catatan_disposisi4,
    $catatan_disposisi5,
    $catatan_disposisi6,
    $catatan_disposisi7,
    $catatan_disposisi8,
    $catatan_disposisi9,
    $catatan_disposisi10,
    $tanggal_disposisi1,
    $tanggal_disposisi2,
    $tanggal_disposisi3,
    $tanggal_disposisi4,
    $tanggal_disposisi5,
    $tanggal_disposisi6,
    $tanggal_disposisi7,
    $tanggal_disposisi8,
    $tanggal_disposisi9,
    $tanggal_disposisi10,
    $tanggal_eksekutor,
    $diteruskan_ke,
    $nama_selesai,
    $nama_selesai2,
    $nama_selesai3,
    $nama_selesai4,
    $nama_selesai5,
    $nama_selesai6,
    $nama_selesai7,
    $nama_tolak,
    $catatan_selesai,
    $catatan_selesai2,
    $catatan_selesai3,
    $catatan_selesai4,
    $catatan_selesai5,
    $catatan_selesai6,
    $catatan_selesai7,
    $catatan_tolak,
    $jenis_surat
);
$stmt2->fetch();
$stmt2->close();

$sql3 = "SELECT nama_berkas FROM file_berkas WHERE id_surat = ?";
$stmt3 = $koneksi->prepare($sql3);
$stmt3->bind_param("i", $id);
$stmt3->execute();
$stmt3->bind_result($file_berkas_name);
$stmt3->fetch();
$stmt3->close();

$sql4 = "SELECT nama_laporan FROM file_laporan WHERE id_surat = ?";
$stmt4 = $koneksi->prepare($sql4);
$stmt4->bind_param("i", $id);
$stmt4->execute();
$stmt4->bind_result($file_laporan_name);
$stmt4->fetch();
$stmt4->close();


// Check if files exist
$file_berkas_exists = !empty($file_berkas_name);
$file_laporan_exists = !empty($file_laporan_name);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Dashboard-Teknoid</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="../tes/logo itbad.png">
    <link href="css/tracking-surat.css" rel="stylesheet">
    <link href="css/dashboard-style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="icon" href="../logo itbad.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>

<body>
    <!-- sidenav -->
    <?php include "sidenav.php" ?>

    <!-- content -->
    <div class="content" id="Content">

        <?php include "topnav.php" ?>

        <div class="mainContent" id="mainContent">
            <div class="contentBox">
                <div class="pageInfo">
                    <div class="jdl1">
                        <h3>Tracking Surat</h3>
                        <button class="back" onclick="goBack()">Kembali</button>
                    </div>
                    <section class="contact">
                        <div class="">
                            <div class="form">
                                <form action="" class="berkasForm" method="post">
                                    <div class="input-field">
                                        <label>Kode Surat</label>
                                        <input type="text" name="kode-surat" value="<?php echo (!empty($kode_surat) ? $kode_surat : $kode_surat2); ?>" class="input" readonly />
                                    </div>
                                    <div class="input-field">
                                        <label>Tujuan Surat</label>
                                        <input type="text" name="tujuan-surat" value="<?php echo $tujuan_surat; ?>" class="input" readonly />
                                    </div>
                                    <div class="input-field">
                                        <label>Perihal</label>
                                        <input type="text" name="perihal" value="<?php echo $perihal; ?>" class="input" readonly />
                                    </div>
                                  <?php if ($jenis_surat == 1 || $jenis_surat == 2): ?>
                                        <div class="input-field" class="hiden">
                                            <label>Posisi Surat Saat Ini</label>
                                                <?php
                                                // Define the rules as an array
                                                $rules = array(
                                                    'Warek2' => 'Warek 2',
                                                    'Warek3' => 'Warek 3',
                                                    'DekanFEB' => 'Dekan FEB',
                                                    'DekanFTD' => 'Dekan FTD',
                                                    'direkPasca' => 'Direktur Pasca',
                                                    'prodi_keuSyariah' => 'Prodi S2 Keuangan Syariah',
                                                    'prodi_akuntansi' => 'Prodi S1 Akuntansi & D3 Akuntansi',
                                                    'prodi_manajemen' => 'Prodi S1 manajemen & D3 Keuangan Perbankan',
                                                    'prodi_si' => 'Prodi S1 Sistem Informasi',
                                                    'prodi_ti' => 'Prodi S1 Teknologi Informasi',
                                                    'prodi_dkv' => 'Prodi S1 Desain Komunikasi Visual',
                                                    'prodi_arsitek' => 'Prodi S1 Arsitektur',
                                                    'keuangan' => 'Bag. Keuangan',
                                                    'sdm' => 'SDM',
                                                    'umum' => 'Bag. Umum',
                                                    'it_lab' => 'IT dan Lab',
                                                    'bpm' => 'BPM',
                                                    'Humas' => 'Humas',
                                                    'marketing' => 'Bag. Marketing',
                                                    'lp3m' => 'LP3M',
                                                    'kui_k' => 'KUI',
                                                    'akademik' => 'Bag. Akademik',
                                                    'ppik_kmhs' => 'PPIK dan Kemahasiswaan',
                                                    'upt_perpus' => 'UPT Perpus',
                                                    'pusat_bisnis' => 'Pusat Bisnis',
                                                    'PSDOD' => 'PSDOD',
                                                    'PKAD' => 'PKAD',
                                                    'CHED' => 'CHED',
                                                    'PSIPP' => 'PSIPP',
                                                    'halal_center' => 'Halal Center',
                                                );

                                                // Check if $diteruskan_ke is a string JSON that can be decoded
                                                if (is_string($diteruskan_ke) && is_array(json_decode($diteruskan_ke, true))) {
                                                    $decoded_array = json_decode($diteruskan_ke, true);
                                                    // Apply the rules to the decoded array
                                                    $diteruskan_ke_value = array_map(function($value) use ($rules) {
                                                        return isset($rules[$value]) ? $rules[$value] : $value;
                                                    }, $decoded_array);
                                                    // Convert the array to a string
                                                    $diteruskan_ke_value = implode(", ", $diteruskan_ke_value);
                                                } elseif (is_array($diteruskan_ke)) {
                                                    // If already an array, apply the rules
                                                    $diteruskan_ke_value = array_map(function($value) use ($rules) {
                                                        return isset($rules[$value]) ? $rules[$value] : $value;
                                                    }, $diteruskan_ke);
                                                    // Convert the array to a string
                                                    $diteruskan_ke_value = implode(", ", $diteruskan_ke_value);
                                                } else {
                                                    // If not an array, apply the rules to the single value
                                                    $diteruskan_ke_value = isset($rules[$diteruskan_ke]) ? $rules[$diteruskan_ke] : $diteruskan_ke;
                                                }

                                                // Replace underscores with spaces
                                                $diteruskan_ke_value = str_replace("_", " ", $diteruskan_ke_value);

                                                // Capitalize the first letter of each word
                                                $diteruskan_ke_value = ucwords($diteruskan_ke_value);

                                                // If empty, set to "Surat belum di disposisi"
                                                if (empty($diteruskan_ke_value)) {
                                                    $diteruskan_ke_value = "Surat belum di disposisi";
                                                }

                                            //untuk menampilkan status tolak dan selesai
                                            function getStatusSelesai($nama_tolak, $nama_selesai)
                                            {
                                                //untuk coloring jika diperlukan
                                                // if (!empty($nama_tolak)) {
                                                //   $status_selesai = "- Ditolak";
                                                //   $style = "background-color: red; color: white;"; // add red color for ditolak
                                                // } elseif (!empty($nama_selesai)) {
                                                //   $status_selesai = "- Disetujui";
                                                //   $style = "background-color: green;color: white;"; // add green color for selesai
                                                // } else {
                                                //   $status_selesai = "";
                                                //   $style = ""; // no color for empty status
                                                // }
                                                // return array($status_selesai, $style);
                                            }

                                            list($status_selesai, $style) = getStatusSelesai($nama_tolak, $nama_selesai);
                                            ?>
                                            <input type="text" id="diteruskan_ke" style="<?php echo $style; ?>" name="diteruskan_ke" value="<?php echo htmlspecialchars($diteruskan_ke_value . ' ' . $status_selesai); ?>" class="input" readonly><br>
                                        </div>
                                    <?php else: ?>
                                        <style>
                                            .hiden {
                                                display: none;
                                            }
                                        </style>
                                    <?php endif; ?>
                                </form>
                            </div>

                            <div class="file">
                                <div class="berkas">
                                    <?php if ($file_berkas_exists) : ?>
                                        <?php
                                        // Path untuk berkas
                                        $file_berkas_path = "uploads/berkas/" . $file_berkas_name;
                                        ?>
                                        <button onclick="downloadFile('<?php echo $file_berkas_path; ?>')" style="border-radius: 5px">Lihat Berkas</button>
                                    <?php else : ?>
                                        <p>Tidak ada berkas yang tersedia.</p>
                                    <?php endif; ?>
                                </div>
                                <div class="laporan">
                                    <?php if ($file_laporan_exists) : ?>
                                        <?php
                                        // Path untuk laporan
                                        $file_laporan_path = "uploads/laporan/" . $file_laporan_name;
                                        ?>
                                        <button onclick="downloadFile('<?php echo $file_laporan_path; ?>')" style="border-radius: 5px">Lihat Laporan</button>
                                    <?php else : ?>
                                        <p>Tidak ada laporan yang tersedia.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="timeline">
                            <div id="app" class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="swiper-container">
                                            <div class="swiper-wrapper">
                                                <!-- untuk lacak disposisi 1 -->
                                                <?php if (!empty($disposisi1) || !empty($tanggal_disposisi1) && !empty($tanggal_eksekutor)) : ?>
                                                    <div class="swiper-slide">
                                                        <div class="status">
                                                            <span>Disposisi 1: <?php echo !empty($disposisi1) ? $disposisi1 : 'Belum Disposisi'; ?></span>
                                                        </div>
                                                        <div class="timestamp">
                                                            <span class="date"><?php //ok
                                                                                if (!empty($tanggal_disposisi1)) {
                                                                                    echo $tanggal_disposisi1;
                                                                                } elseif (!empty($tanggal_eksekutor)) {
                                                                                    echo $tanggal_eksekutor;
                                                                                } else {
                                                                                    echo 'DD/MM/YYYY';
                                                                                }
                                                                                ?></span>
                                                        </div>
                                                        <div class="btn-catatan">
                                                            <button type="button" onclick="cekCatatan('<?php
                                                                                                        if (!empty($catatan_disposisi1)) {
                                                                                                            echo $catatan_disposisi1;
                                                                                                        } else {
                                                                                                            if (!empty($catatan_selesai)) {
                                                                                                                echo $catatan_selesai;
                                                                                                            } else {
                                                                                                                if (!empty($catatan_tolak)) {
                                                                                                                    echo $catatan_tolak;
                                                                                                                } else {
                                                                                                                    echo "Tidak ada catatan";
                                                                                                                }
                                                                                                            }
                                                                                                        }; ?>', 
                                                        '<?php echo !empty($disposisi2) ? $disposisi2 : "Belum Diteruskan"; ?>')" style="cursor: pointer;">Cek Catatan</button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- untuk lacak disposisi 2 -->
                                                <?php if (!empty($disposisi2) || !empty($tanggal_disposisi1) && !empty($tanggal_eksekutor)) : ?>
                                                    <div class="swiper-slide">
                                                        <div class="status">
                                                            <span>Disposisi 2:
                                                                <?php //ok 
                                                                if (empty($disposisi2)) {
                                                                    if (!empty($disposisi1)) {
                                                                        $output = print_r($diteruskan_ke, true);
                                                                        $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                        $output = str_replace('_', ' ', $output);
                                                                        echo ucwords($output);
                                                                    } else {
                                                                        echo 'Belum didisposisi';
                                                                    }
                                                                } else {
                                                                    $output = print_r($disposisi2, true);
                                                                    $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                    $output = str_replace('_', ' ', $output);
                                                                    echo ucwords($output);
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="timestamp">
                                                            <span class="date">
                                                                <?php //ok
                                                                if (!empty($tanggal_disposisi2)) {
                                                                    echo $tanggal_disposisi2;
                                                                } elseif (!empty($tanggal_disposisi1)) {
                                                                    echo $tanggal_eksekutor;
                                                                } else {
                                                                    echo 'DD/MM/YYYY';
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="btn-catatan">
                                                            <button type="button" onclick="cekCatatan('<?php
                                                                                                        if (!empty($catatan_disposisi2)) {
                                                                                                            echo $catatan_disposisi2;
                                                                                                        } elseif (!empty($catatan_disposisi1)) {
                                                                                                            if (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6) && !empty($catatan_selesai7)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai7 . ' : ' . $catatan_selesai7;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2;
                                                                                                            } elseif (!empty($catatan_selesai)) {
                                                                                                                echo $catatan_selesai;
                                                                                                            } elseif (!empty($catatan_tolak)) {
                                                                                                                echo $catatan_tolak;
                                                                                                            } else {
                                                                                                                echo "Tidak ada catatan";
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo "Tidak ada catatan";
                                                                                                        }
                                                                                                        ?>',
                                                        
                                                        
                                                                                                    '<?php
                                                                                                        if (empty($disposisi3)) {
                                                                                                            if (!empty($disposisi2)) {
                                                                                                                $diteruskan_ke_array = json_decode($diteruskan_ke, true); // Decode JSON menjadi array
                                                                                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                                                                                    // Iterasi setiap elemen dalam array untuk melakukan replace dan kapitalisasi
                                                                                                                    foreach ($diteruskan_ke_array as &$value) {
                                                                                                                        $value = str_replace("_", " ", $value);
                                                                                                                        $value = ucwords($value);
                                                                                                                    }
                                                                                                                    echo implode(', ', $diteruskan_ke_array); // Gabungkan elemen array menjadi string
                                                                                                                } else {
                                                                                                                    // Jika bukan JSON yang valid, langsung tampilkan setelah replace dan kapitalisasi
                                                                                                                    $diteruskan_ke = str_replace("_", " ", $diteruskan_ke);
                                                                                                                    echo ucwords($diteruskan_ke);
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Belum didisposisi';
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo $disposisi3;
                                                                                                        }
                                                                                                        ?>')" style="cursor: pointer;">Cek Catatan</button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- untuk lacak disposisi 3 -->
                                                <?php if (!empty($disposisi3) || !empty($tanggal_disposisi2) && !empty($tanggal_eksekutor)) : ?>
                                                    <div class="swiper-slide">
                                                        <div class="status">
                                                            <span>Disposisi 3:
                                                                <?php //ok 
                                                                if (empty($disposisi3)) {
                                                                    if (!empty($disposisi2)) {
                                                                        $output = print_r($diteruskan_ke, true);
                                                                        $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                        $output = str_replace('_', ' ', $output);
                                                                        echo ucwords($output);
                                                                    } else {
                                                                        echo 'Belum didisposisi';
                                                                    }
                                                                } else {
                                                                    $output = print_r($disposisi3, true);
                                                                    $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                    $output = str_replace('_', ' ', $output);
                                                                    echo ucwords($output);
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="timestamp">
                                                            <span class="date">
                                                                <?php //ok
                                                                if (!empty($tanggal_disposisi3)) {
                                                                    echo $tanggal_disposisi3;
                                                                } elseif (!empty($tanggal_disposisi2)) {
                                                                    echo $tanggal_eksekutor;
                                                                } else {
                                                                    echo 'DD/MM/YYYY';
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="btn-catatan">
                                                            <button type="button" onclick="cekCatatan('<?php
                                                                                                        if (!empty($catatan_disposisi3)) {
                                                                                                            echo $catatan_disposisi3;
                                                                                                        } elseif (!empty($catatan_disposisi2)) {
                                                                                                            if (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6) && !empty($catatan_selesai7)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai7 . ' : ' . $catatan_selesai7;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2;
                                                                                                            } elseif (!empty($catatan_selesai)) {
                                                                                                                echo $catatan_selesai;
                                                                                                            } elseif (!empty($catatan_tolak)) {
                                                                                                                echo $catatan_tolak;
                                                                                                            } else {
                                                                                                                echo "Tidak ada catatan";
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo "Tidak ada catatan";
                                                                                                        }
                                                                                                        ?>',
                                                                                                    '<?php
                                                                                                        if (empty($disposisi4)) {
                                                                                                            if (!empty($disposisi3)) {
                                                                                                                $diteruskan_ke_array = json_decode($diteruskan_ke, true); // Decode JSON menjadi array
                                                                                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                                                                                    // Iterasi setiap elemen dalam array untuk melakukan replace dan kapitalisasi
                                                                                                                    foreach ($diteruskan_ke_array as &$value) {
                                                                                                                        $value = str_replace("_", " ", $value);
                                                                                                                        $value = ucwords($value);
                                                                                                                    }
                                                                                                                    echo implode(', ', $diteruskan_ke_array); // Gabungkan elemen array menjadi string
                                                                                                                } else {
                                                                                                                    // Jika bukan JSON yang valid, langsung tampilkan setelah replace dan kapitalisasi
                                                                                                                    $diteruskan_ke = str_replace("_", " ", $diteruskan_ke);
                                                                                                                    echo ucwords($diteruskan_ke);
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Belum didisposisi';
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo $disposisi4;
                                                                                                        }
                                                                                                        ?>')" style="cursor: pointer;">Cek Catatan</button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- untuk lacak disposisi 4 -->
                                                <?php if (!empty($disposisi4) || !empty($tanggal_disposisi3) && !empty($tanggal_eksekutor)) : ?>
                                                    <div class="swiper-slide">
                                                        <div class="status">
                                                            <span>Disposisi 4:
                                                                <?php //ok 
                                                                if (empty($disposisi4)) {
                                                                    if (!empty($disposisi3)) {
                                                                        $output = print_r($diteruskan_ke, true);
                                                                        $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                        $output = str_replace('_', ' ', $output);
                                                                        echo ucwords($output);
                                                                    } else {
                                                                        echo 'Belum didisposisi';
                                                                    }
                                                                } else {
                                                                    $output = print_r($disposisi4, true);
                                                                    $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                    $output = str_replace('_', ' ', $output);
                                                                    echo ucwords($output);
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="timestamp">
                                                            <span class="date">
                                                                <?php //ok
                                                                if (!empty($tanggal_disposisi4)) {
                                                                    echo $tanggal_disposisi4;
                                                                } elseif (!empty($tanggal_disposisi3)) {
                                                                    echo $tanggal_eksekutor;
                                                                } else {
                                                                    echo 'DD/MM/YYYY';
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="btn-catatan">
                                                            <button type="button" onclick="cekCatatan('<?php
                                                                                                        if (!empty($catatan_disposisi4)) {
                                                                                                            echo $catatan_disposisi4;
                                                                                                        } elseif (!empty($catatan_disposisi3)) {
                                                                                                            if (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6) && !empty($catatan_selesai7)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai7 . ' : ' . $catatan_selesai7;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2;
                                                                                                            } elseif (!empty($catatan_selesai)) {
                                                                                                                echo $catatan_selesai;
                                                                                                            } elseif (!empty($catatan_tolak)) {
                                                                                                                echo $catatan_tolak;
                                                                                                            } else {
                                                                                                                echo "Tidak ada catatan";
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo "Tidak ada catatan";
                                                                                                        }
                                                                                                        ?>',
                
                                                                                                    '<?php
                                                                                                        if (empty($disposisi5)) {
                                                                                                            if (!empty($disposisi4)) {
                                                                                                                $diteruskan_ke_array = json_decode($diteruskan_ke, true); // Decode JSON menjadi array
                                                                                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                                                                                    // Iterasi setiap elemen dalam array untuk melakukan replace dan kapitalisasi
                                                                                                                    foreach ($diteruskan_ke_array as &$value) {
                                                                                                                        $value = str_replace("_", " ", $value);
                                                                                                                        $value = ucwords($value);
                                                                                                                    }
                                                                                                                    echo implode(', ', $diteruskan_ke_array); // Gabungkan elemen array menjadi string
                                                                                                                } else {
                                                                                                                    // Jika bukan JSON yang valid, langsung tampilkan setelah replace dan kapitalisasi
                                                                                                                    $diteruskan_ke = str_replace("_", " ", $diteruskan_ke);
                                                                                                                    echo ucwords($diteruskan_ke);
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Belum didisposisi';
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo $disposisi5;
                                                                                                        }
                                                                                                        ?>')" style="cursor: pointer;">Cek Catatan</button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- untuk lacak disposisi 5 -->
                                                <?php if (!empty($disposisi5) || !empty($tanggal_disposisi4) && !empty($tanggal_eksekutor)) : ?>
                                                    <div class="swiper-slide">
                                                        <div class="status">
                                                            <span> Disposisi 5 :
                                                                <?php //ok 
                                                                if (empty($disposisi5)) {
                                                                    if (!empty($disposisi4)) {
                                                                        $output = print_r($diteruskan_ke, true);
                                                                        $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                        $output = str_replace('_', ' ', $output);
                                                                        echo ucwords($output);
                                                                    } else {
                                                                        echo 'Belum didisposisi';
                                                                    }
                                                                } else {
                                                                    $output = print_r($disposisi5, true);
                                                                    $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                    $output = str_replace('_', ' ', $output);
                                                                    echo ucwords($output);
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="timestamp">
                                                            <span class="date">
                                                                <?php //ok
                                                                if (!empty($tanggal_disposisi5)) {
                                                                    echo $tanggal_disposisi5;
                                                                } elseif (!empty($tanggal_disposisi4)) {
                                                                    echo $tanggal_eksekutor;
                                                                } else {
                                                                    echo 'DD/MM/YYYY';
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="btn-catatan">
                                                            <button type="button" onclick="cekCatatan('<?php
                                                                                                        if (!empty($catatan_disposisi5)) {
                                                                                                            echo $catatan_disposisi5;
                                                                                                        } elseif (!empty($catatan_disposisi4)) {
                                                                                                            if (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6) && !empty($catatan_selesai7)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai7 . ' : ' . $catatan_selesai7;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2;
                                                                                                            } elseif (!empty($catatan_selesai)) {
                                                                                                                echo $catatan_selesai;
                                                                                                            } elseif (!empty($catatan_tolak)) {
                                                                                                                echo $catatan_tolak;
                                                                                                            } else {
                                                                                                                echo "Tidak ada catatan";
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo "Tidak ada catatan";
                                                                                                        }
                                                                                                        ?>',
                                                        
                                                        
                                                                                                    '<?php
                                                                                                        if (empty($disposisi6)) {
                                                                                                            if (!empty($disposisi5)) {
                                                                                                                $diteruskan_ke_array = json_decode($diteruskan_ke, true); // Decode JSON menjadi array
                                                                                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                                                                                    // Iterasi setiap elemen dalam array untuk melakukan replace dan kapitalisasi
                                                                                                                    foreach ($diteruskan_ke_array as &$value) {
                                                                                                                        $value = str_replace("_", " ", $value);
                                                                                                                        $value = ucwords($value);
                                                                                                                    }
                                                                                                                    echo implode(', ', $diteruskan_ke_array); // Gabungkan elemen array menjadi string
                                                                                                                } else {
                                                                                                                    // Jika bukan JSON yang valid, langsung tampilkan setelah replace dan kapitalisasi
                                                                                                                    $diteruskan_ke = str_replace("_", " ", $diteruskan_ke);
                                                                                                                    echo ucwords($diteruskan_ke);
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Belum didisposisi';
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo $disposisi6;
                                                                                                        }
                                                                                                        ?>')" style="cursor: pointer;">Cek Catatan</button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- untuk lacak disposisi 6 -->
                                                <?php if (!empty($disposisi6) || !empty($tanggal_disposisi5) && !empty($tanggal_eksekutor)) : ?>
                                                    <div class="swiper-slide">
                                                        <div class="status">
                                                            <span>Disposisi 6:
                                                                <?php //ok 
                                                                if (empty($disposisi6)) {
                                                                    if (!empty($disposisi5)) {
                                                                        $output = print_r($diteruskan_ke, true);
                                                                        $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                        $output = str_replace('_', ' ', $output);
                                                                        echo ucwords($output);
                                                                    } else {
                                                                        echo 'Belum didisposisi';
                                                                    }
                                                                } else {
                                                                    $output = print_r($disposisi6, true);
                                                                    $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                    $output = str_replace('_', ' ', $output);
                                                                    echo ucwords($output);
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="timestamp">
                                                            <span class="date">
                                                                <?php
                                                                if (!empty($tanggal_disposisi6)) {
                                                                    echo $tanggal_disposisi6;
                                                                } elseif (!empty($tanggal_disposisi5)) {
                                                                    echo $tanggal_eksekutor;
                                                                } else {
                                                                    echo 'DD/MM/YYYY';
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="btn-catatan">
                                                            <button type="button" onclick="cekCatatan('<?php
                                                                                                        if (!empty($catatan_disposisi6)) {
                                                                                                            echo $catatan_disposisi6;
                                                                                                        } elseif (!empty($catatan_disposisi5)) {
                                                                                                            if (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6) && !empty($catatan_selesai7)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai7 . ' : ' . $catatan_selesai7;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2;
                                                                                                            } elseif (!empty($catatan_selesai)) {
                                                                                                                echo $catatan_selesai;
                                                                                                            } elseif (!empty($catatan_tolak)) {
                                                                                                                echo $catatan_tolak;
                                                                                                            } else {
                                                                                                                echo "Tidak ada catatan";
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo "Tidak ada catatan";
                                                                                                        }
                                                                                                        ?>',
                                                        
                                                        
                                                                                                    '<?php
                                                                                                        if (empty($disposisi7)) {
                                                                                                            if (!empty($disposisi6)) {
                                                                                                                $diteruskan_ke_array = json_decode($diteruskan_ke, true); // Decode JSON menjadi array
                                                                                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                                                                                    // Iterasi setiap elemen dalam array untuk melakukan replace dan kapitalisasi
                                                                                                                    foreach ($diteruskan_ke_array as &$value) {
                                                                                                                        $value = str_replace("_", " ", $value);
                                                                                                                        $value = ucwords($value);
                                                                                                                    }
                                                                                                                    echo implode(', ', $diteruskan_ke_array); // Gabungkan elemen array menjadi string
                                                                                                                } else {
                                                                                                                    // Jika bukan JSON yang valid, langsung tampilkan setelah replace dan kapitalisasi
                                                                                                                    $diteruskan_ke = str_replace("_", " ", $diteruskan_ke);
                                                                                                                    echo ucwords($diteruskan_ke);
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Belum didisposisi';
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo $disposisi7;
                                                                                                        }
                                                                                                        ?>')" style="cursor: pointer;">Cek Catatan</button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- untuk lacak disposisi 7 -->
                                                <?php if (!empty($disposisi7) || !empty($tanggal_disposisi6) && !empty($tanggal_eksekutor)) : ?>
                                                    <div class="swiper-slide">
                                                        <div class="status">
                                                            <span>Disposisi 7:
                                                                <?php //ok 
                                                                if (empty($disposisi7)) {
                                                                    if (!empty($disposisi6)) {
                                                                        $output = print_r($diteruskan_ke, true);
                                                                        $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                        $output = str_replace('_', ' ', $output);
                                                                        echo ucwords($output);
                                                                    } else {
                                                                        echo 'Belum didisposisi';
                                                                    }
                                                                } else {
                                                                    $output = print_r($disposisi7, true);
                                                                    $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                    $output = str_replace('_', ' ', $output);
                                                                    echo ucwords($output);
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="timestamp">
                                                            <span class="date">
                                                                <?php //ok
                                                                if (!empty($tanggal_disposisi7)) {
                                                                    echo $tanggal_disposisi7;
                                                                } elseif (!empty($tanggal_disposisi6)) {
                                                                    echo $tanggal_eksekutor;
                                                                } else {
                                                                    echo 'DD/MM/YYYY';
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="btn-catatan">
                                                            <button type="button" onclick="cekCatatan('<?php
                                                                                                        if (!empty($catatan_disposisi7)) {
                                                                                                            echo $catatan_disposisi7;
                                                                                                        } elseif (!empty($catatan_disposisi6)) {
                                                                                                            if (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6) && !empty($catatan_selesai7)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai7 . ' : ' . $catatan_selesai7;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2;
                                                                                                            } elseif (!empty($catatan_selesai)) {
                                                                                                                echo $catatan_selesai;
                                                                                                            } elseif (!empty($catatan_tolak)) {
                                                                                                                echo $catatan_tolak;
                                                                                                            } else {
                                                                                                                echo "Tidak ada catatan";
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo "Tidak ada catatan";
                                                                                                        }
                                                                                                        ?>',
                                                        
                                                        
                                                                                                    '<?php
                                                                                                        if (empty($disposisi8)) {
                                                                                                            if (!empty($disposisi7)) {
                                                                                                                $diteruskan_ke_array = json_decode($diteruskan_ke, true); // Decode JSON menjadi array
                                                                                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                                                                                    // Iterasi setiap elemen dalam array untuk melakukan replace dan kapitalisasi
                                                                                                                    foreach ($diteruskan_ke_array as &$value) {
                                                                                                                        $value = str_replace("_", " ", $value);
                                                                                                                        $value = ucwords($value);
                                                                                                                    }
                                                                                                                    echo implode(', ', $diteruskan_ke_array); // Gabungkan elemen array menjadi string
                                                                                                                } else {
                                                                                                                    // Jika bukan JSON yang valid, langsung tampilkan setelah replace dan kapitalisasi
                                                                                                                    $diteruskan_ke = str_replace("_", " ", $diteruskan_ke);
                                                                                                                    echo ucwords($diteruskan_ke);
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Belum didisposisi';
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo $disposisi8;
                                                                                                        }
                                                                                                        ?>')" style="cursor: pointer;">Cek Catatan</button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- untuk lacak disposisi 8 -->
                                                <?php if (!empty($disposisi8) || !empty($tanggal_disposisi7) && !empty($tanggal_eksekutor)) : ?>
                                                    <div class="swiper-slide">
                                                        <div class="status">
                                                            <span>Disposisi 8:
                                                                <?php //ok 
                                                                if (empty($disposisi8)) {
                                                                    if (!empty($disposisi7)) {
                                                                        $output = print_r($diteruskan_ke, true);
                                                                        $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                        $output = str_replace('_', ' ', $output);
                                                                        echo ucwords($output);
                                                                    } else {
                                                                        echo 'Belum didisposisi';
                                                                    }
                                                                } else {
                                                                    $output = print_r($disposisi8, true);
                                                                    $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                    $output = str_replace('_', ' ', $output);
                                                                    echo ucwords($output);
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="timestamp">
                                                            <span class="date">
                                                                <?php //ok
                                                                if (!empty($tanggal_disposisi8)) {
                                                                    echo $tanggal_disposisi8;
                                                                } elseif (!empty($tanggal_disposisi7)) {
                                                                    echo $tanggal_eksekutor;
                                                                } else {
                                                                    echo 'DD/MM/YYYY';
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="btn-catatan">
                                                            <button type="button" onclick="cekCatatan('<?php
                                                                                                        if (!empty($catatan_disposisi8)) {
                                                                                                            echo $catatan_disposisi8;
                                                                                                        } elseif (!empty($catatan_disposisi7)) {
                                                                                                            if (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6) && !empty($catatan_selesai7)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai7 . ' : ' . $catatan_selesai7;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2;
                                                                                                            } elseif (!empty($catatan_selesai)) {
                                                                                                                echo $catatan_selesai;
                                                                                                            } elseif (!empty($catatan_tolak)) {
                                                                                                                echo $catatan_tolak;
                                                                                                            } else {
                                                                                                                echo "Tidak ada catatan";
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo "Tidak ada catatan";
                                                                                                        }
                                                                                                        ?>',
                                                        
                                                        
                                                                                                    '<?php
                                                                                                        if (empty($disposisi9)) {
                                                                                                            if (!empty($disposisi8)) {
                                                                                                                $diteruskan_ke_array = json_decode($diteruskan_ke, true); // Decode JSON menjadi array
                                                                                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                                                                                    // Iterasi setiap elemen dalam array untuk melakukan replace dan kapitalisasi
                                                                                                                    foreach ($diteruskan_ke_array as &$value) {
                                                                                                                        $value = str_replace("_", " ", $value);
                                                                                                                        $value = ucwords($value);
                                                                                                                    }
                                                                                                                    echo implode(', ', $diteruskan_ke_array); // Gabungkan elemen array menjadi string
                                                                                                                } else {
                                                                                                                    // Jika bukan JSON yang valid, langsung tampilkan setelah replace dan kapitalisasi
                                                                                                                    $diteruskan_ke = str_replace("_", " ", $diteruskan_ke);
                                                                                                                    echo ucwords($diteruskan_ke);
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Belum didisposisi';
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo $disposisi9;
                                                                                                        }
                                                                                                        ?>')" style="cursor: pointer;">Cek Catatan</button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- untuk lacak disposisi 9-->
                                                <?php if (!empty($disposisi9) || !empty($tanggal_disposisi8) && !empty($tanggal_eksekutor)) : ?>
                                                    <div class="swiper-slide">
                                                        <div class="status">
                                                            <span>Disposisi 9:
                                                                <?php //ok 
                                                                if (empty($disposisi9)) {
                                                                    if (!empty($disposisi8)) {
                                                                        $output = print_r($diteruskan_ke, true);
                                                                        $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                        $output = str_replace('_', ' ', $output);
                                                                        echo ucwords($output);
                                                                    } else {
                                                                        echo 'Belum didisposisi';
                                                                    }
                                                                } else {
                                                                    $output = print_r($disposisi9, true);
                                                                    $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                    $output = str_replace('_', ' ', $output);
                                                                    echo ucwords($output);
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="timestamp">
                                                            <span class="date"><?php //ok
                                                                                if (!empty($tanggal_disposisi9)) {
                                                                                    echo $tanggal_disposisi9;
                                                                                } elseif (!empty($tanggal_disposisi8)) {
                                                                                    echo $tanggal_eksekutor;
                                                                                } else {
                                                                                    echo 'DD/MM/YYYY';
                                                                                }
                                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="btn-catatan">
                                                            <button type="button" onclick="cekCatatan('<?php
                                                                                                        if (!empty($catatan_disposisi9)) {
                                                                                                            echo $catatan_disposisi9;
                                                                                                        } elseif (!empty($catatan_disposisi8)) {
                                                                                                            if (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6) && !empty($catatan_selesai7)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai7 . ' : ' . $catatan_selesai7;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5) && !empty($catatan_selesai6)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai6 . ' : ' . $catatan_selesai6;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4) && !empty($catatan_selesai5)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai5 . ' : ' . $catatan_selesai5;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3) && !empty($catatan_selesai4)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai4 . ' : ' . $catatan_selesai4;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2) && !empty($catatan_selesai3)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2 . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai3 . ' : ' . $catatan_selesai3;
                                                                                                            } elseif (!empty($catatan_selesai) && !empty($catatan_selesai2)) {
                                                                                                                echo 'Catatan Selesai Oleh ' . $nama_selesai . ' : ' . $catatan_selesai . '&nbsp;||&nbsp;' .
                                                                                                                    'Catatan Selesai Oleh ' . $nama_selesai2 . ' : ' . $catatan_selesai2;
                                                                                                            } elseif (!empty($catatan_selesai)) {
                                                                                                                echo $catatan_selesai;
                                                                                                            } elseif (!empty($catatan_tolak)) {
                                                                                                                echo $catatan_tolak;
                                                                                                            } else {
                                                                                                                echo "Tidak ada catatan";
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo "Tidak ada catatan";
                                                                                                        }
                                                                                                        ?>',
                                                        
                                                        
                                                                                                    '<?php
                                                                                                        if (empty($disposisi10)) {
                                                                                                            if (!empty($disposisi9)) {
                                                                                                                $diteruskan_ke_array = json_decode($diteruskan_ke, true); // Decode JSON menjadi array
                                                                                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                                                                                    // Iterasi setiap elemen dalam array untuk melakukan replace dan kapitalisasi
                                                                                                                    foreach ($diteruskan_ke_array as &$value) {
                                                                                                                        $value = str_replace("_", " ", $value);
                                                                                                                        $value = ucwords($value);
                                                                                                                    }
                                                                                                                    echo implode(', ', $diteruskan_ke_array); // Gabungkan elemen array menjadi string
                                                                                                                } else {
                                                                                                                    // Jika bukan JSON yang valid, langsung tampilkan setelah replace dan kapitalisasi
                                                                                                                    $diteruskan_ke = str_replace("_", " ", $diteruskan_ke);
                                                                                                                    echo ucwords($diteruskan_ke);
                                                                                                                }
                                                                                                            } else {
                                                                                                                echo 'Belum didisposisi';
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo $disposisi10;
                                                                                                        }
                                                                                                        ?>')" style="cursor: pointer;">Cek Catatan</button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- untuk lacak disposisi 10-->
                                                <?php if (!empty($disposisi10) || !empty($tanggal_disposisi9) && !empty($tanggal_eksekutor)) : ?>
                                                    <div class="swiper-slide">
                                                        <div class="status">
                                                            <span>Disposisi 10:
                                                                <?php //ok 
                                                                if (empty($disposisi10)) {
                                                                    if (!empty($disposisi9)) {
                                                                        $output = print_r($diteruskan_ke, true);
                                                                        $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                        $output = str_replace('_', ' ', $output);
                                                                        echo ucwords($output);
                                                                    } else {
                                                                        echo 'Belum didisposisi';
                                                                    }
                                                                } else {
                                                                    $output = print_r($disposisi10, true);
                                                                    $output = str_replace(array('["', '"]', '', '"'), '', str_replace(',', ', ', $output));
                                                                    $output = str_replace('_', ' ', $output);
                                                                    echo ucwords($output);
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <div class="timestamp">
                                                            <span class="date"><?php //ok
                                                                                if (!empty($tanggal_disposisi10)) {
                                                                                    echo $tanggal_disposisi10;
                                                                                } elseif (!empty($tanggal_disposisi9)) {
                                                                                    echo $tanggal_eksekutor;
                                                                                } else {
                                                                                    echo 'DD/MM/YYYY';
                                                                                }
                                                                                ?></span>
                                                        </div>
                                                        <div class="btn-catatan">
                                                            <button type="button" onclick="cekCatatan('<?php echo !empty($catatan_disposisi10) ? $catatan_disposisi10 : "Tidak ada catatan"; ?>')" style="cursor: pointer;">Cek Catatan</button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>


    <div id="previewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePreview()">&times;</span>
            <iframe id="previewViewer" src="" width="100%" height="500px"></iframe>
            <button id="downloadBtn">Unduh</button>
        </div>
    </div>
    <div class="footer">
        &copy;<span id="year"> </span><span> Copyright ©2024 TeknoGenius</span></div>
    </div>
    <script src="js/dashboard-js.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        function adjustFormWidth(sidenavWidth) {
            var formContainer = document.getElementById("formContainer");
            formContainer.style.width = "calc(100% - " + sidenavWidth + ")";
        }

        function goBack() {
            const previousPage = document.referrer;

            // Mengecek apakah halaman sebelumnya adalah rekap_surat.php
            if (previousPage.includes('rekap_surat.php')) {
                // Jika iya, arahkan ke rekap_surat.php
                window.location.href = 'rekap_surat.php';
            } else {
                // Jika tidak, kembali ke halaman sebelumnya
                window.history.back();
            }
        }

        function downloadFile(filePath) {
            window.location.href = filePath;
        }

        function cekCatatan(catatan, diteruskan_ke) {
            swal({
                title: `Catatan: ${catatan}`,
                text: `Diteruskan ke: ${diteruskan_ke}`,
                showCancelButton: false,
                confirmButtonText: 'Kembali'
            });
        }

        // CEK CATATAN DISPOSISI 1 - 10
        let catatan1 = '<?php echo !empty($catatan_disposisi1) ? $catatan_disposisi1 : "Tidak ada catatan"; ?>';
        let diteruskan_ke1 = '<?php echo !empty($disposisi2) ? $disposisi2 : "Belum Diteruskan"; ?>';

        let catatan2 = '<?php echo !empty($catatan_disposisi2) ? $catatan_disposisi2 : "Tidak ada catatan"; ?>';
        let diteruskan_ke2 = '<?php echo !empty($disposisi3) ? $disposisi3 : "Belum Diteruskan"; ?>';

        let catatan3 = '<?php echo !empty($catatan_disposisi3) ? $catatan_disposisi3 : "Tidak ada catatan"; ?>';
        let diteruskan_ke3 = '<?php echo !empty($disposisi4) ? $disposisi4 : "Belum Diteruskan"; ?>';

        let catatan4 = '<?php echo !empty($catatan_disposisi4) ? $catatan_disposisi4 : "Tidak ada catatan"; ?>';
        let diteruskan_ke4 = '<?php echo !empty($disposisi5) ? $disposisi5 : "Belum Diteruskan"; ?>';

        let catatan5 = '<?php echo !empty($catatan_disposisi5) ? $catatan_disposisi5 : "Tidak ada catatan"; ?>';
        let diteruskan_ke5 = '<?php echo !empty($disposisi6) ? $disposisi6 : "Belum Diteruskan"; ?>';

        let catatan6 = '<?php echo !empty($catatan_disposisi6) ? $catatan_disposisi6 : "Tidak ada catatan"; ?>';
        let diteruskan_ke6 = '<?php echo !empty($disposisi7) ? $disposisi7 : "Belum Diteruskan"; ?>';

        let catatan7 = '<?php echo !empty($catatan_disposisi7) ? $catatan_disposisi7 : "Tidak ada catatan"; ?>';
        let diteruskan_ke7 = '<?php echo !empty($disposisi8) ? $disposisi8 : "Belum Diteruskan"; ?>';

        let catatan8 = '<?php echo !empty($catatan_disposisi8) ? $catatan_disposisi8 : "Tidak ada catatan"; ?>';
        let diteruskan_ke8 = '<?php echo !empty($disposisi9) ? $disposisi9 : "Belum Diteruskan"; ?>';

        let catatan9 = '<?php echo !empty($catatan_disposisi9) ? $catatan_disposisi9 : "Tidak ada catatan"; ?>';
        let diteruskan_ke9 = '<?php echo !empty($disposisi10) ? $disposisi10 : "Belum Diteruskan"; ?>';

        let catatan10 = '<?php echo !empty($catatan_disposisi10) ? $catatan_disposisi10 : "Tidak ada catatan"; ?>';
    </script>

</body>

</html>