<?php
session_start();
include '../koneksi.php';
//need to fix
function uploadFile($file, $destination, $prefix)
{
    $randomNumber = rand(1000, 9999);
    $fileName = $prefix . "_" . $randomNumber . "_" . basename($file["name"]);
    $targetFilePath = $destination . $fileName;

    // Check file size and type
    if ($file["size"] > 10 * 1024 * 1024) {
        return ['error' => 'filesize'];
    }

    if ($file["type"] != 'application/pdf') {
        return ['error' => 'filetype'];
    }

    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        return ['success' => $fileName];
    } else {
        return ['error' => 'uploadfailed'];
    }
}

date_default_timezone_set('Asia/Jakarta');
$curdate = date("Y-m-d H:i:s");

// Collect POST data
$groupedFields = [
    'publikasi' => [ // publikasi
        'jenis_publikasi_pi',
        'judul_publikasi_pi',
        'nama_jurnal_pi',
        'vol_notahun_pi',
        'link_jurnal_pi'
    ],
    'penelitian' => [ // penelitian
        'skema_ppmdpek',
        'judul_penelitian_ppm'
    ],
    'pertemuan_ppdpi' => [ //pertemuan_ilmiah
        'nama_pertemuan_ppdpi',
        'usulan_biaya_ppdpi',
        'skala_ppdpi'
    ],
    'pertemuan_ppdks' => [ // keynote_speaker
        'nama_pertemuan_ppdks',
        'skala_ppdks'
    ],
    'kegiatan' => [ // visiting
        'nm_kegiatan_vl',
        'waktu_pelaksanaan_vl'
    ],
    'hki' => [ // hki
        'judul_hki',
        'jenis_hki'
    ],
    'teknologi' => [ //teknologi
        'teknologi_tg',
        'deskripsi_tg'
    ],
    'buku' => [ // buku
        'jenis_buku',
        'judul_buku',
        'sinopsis_buku',
        'isbn_buku'
    ],
    'model' => [ //model
        'nama_model_mpdks',
        'deskripsi_mpdks'
    ],
    'ipbk' => [ // insentif_publikasi
        'judul_ipbk',
        'namaPenerbit_dan_waktu_ipbk',
        'link_publikasi_ipbk'
    ]
];



$data = [];
foreach ($groupedFields as $group => $fields) {
    foreach ($fields as $field) {
        $data[$field] = mysqli_real_escape_string($conn, $_POST[$field] ?? '');
    }
}

$data['jenis_insentif'] = $_POST['jenis_insentif'] ?? '';
$data['jenis_surat_dsn'] = $_POST['jenis_surat_dsn'] ?? '';
$data['asal_surat_dsn'] = $_POST['asal_surat_dsn'] ?? '';
$data['status_pengusul'] = $_POST['status_pengusul'] ?? '';
$data['nidn'] = $_POST['nidn'] ?? '';
$data['no_telpon'] = $_POST['no_telpon'] ?? '';
$data['id_sinta'] = $_POST['id_sinta'] ?? '';
$data['prodi_pengusul'] = $_POST['prodi_pengusul'] ?? '';

$jenis_insentif_lp3m = [
    'penelitian',
    'publikasi',
    'pertemuan_ilmiah',
    'keynote_speaker',
    'visiting',
    'hki',
    'teknologi',
    'buku',
    'model',
    'insentif_publikasi'
];

if (isset($data['jenis_insentif']) && in_array($data['jenis_insentif'], $jenis_insentif_lp3m)) {
    $data['tujuan_surat_srd'] = 'lp3m';
}

// Prepare SQL statement
$sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, tanggal_surat, tujuan_surat_srd";

if ($data['jenis_insentif'] == 'publikasi') {
    $sql .= ", jenis_publikasi_pi, judul_publikasi_pi, nama_jurnal_pi, vol_notahun_pi, link_jurnal_pi) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssssssssssss', $data['jenis_surat_dsn'], $data['asal_surat_dsn'], $data['status_pengusul'], $data['nidn'], $data['no_telpon'], $data['id_sinta'], $data['prodi_pengusul'], $data['jenis_insentif'], $curdate, $data['tujuan_surat_srd'], $data['jenis_publikasi_pi'], $data['judul_publikasi_pi'], $data['nama_jurnal_pi'], $data['vol_notahun_pi'], $data['link_jurnal_pi']);
} elseif ($data['jenis_insentif'] == 'penelitian') {
    $sql .= ", skema_ppmdpek, judul_penelitian_ppm) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssssssssss', $data['jenis_surat_dsn'], $data['asal_surat_dsn'], $data['status_pengusul'], $data['nidn'], $data['no_telpon'], $data['id_sinta'], $data['prodi_pengusul'], $data['jenis_insentif'], $curdate, $data['tujuan_surat_srd'], $data['skema_ppmdpek'], $data['judul_penelitian_ppm']);
} elseif ($data['jenis_insentif'] == 'pertemuan_ilmiah') {
    $sql .= ", nama_pertemuan_ppdpi, usulan_biaya_ppdpi, skala_ppdpi) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die('Prepare failed: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'sssssssssssss', $data['jenis_surat_dsn'], $data['asal_surat_dsn'], $data['status_pengusul'], $data['nidn'], $data['no_telpon'], $data['id_sinta'], $data['prodi_pengusul'], $data['jenis_insentif'], $curdate, $data['tujuan_surat_srd'], $data['nama_pertemuan_ppdpi'], $data['usulan_biaya_ppdpi'], $data['skala_ppdpi']);
} elseif ($data['jenis_insentif'] == 'keynote_speaker') {
    $sql .= ", nama_pertemuan_ppdks, skala_ppdks) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die('Prepare failed: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'ssssssssssss', $data['jenis_surat_dsn'], $data['asal_surat_dsn'], $data['status_pengusul'], $data['nidn'], $data['no_telpon'], $data['id_sinta'], $data['prodi_pengusul'], $data['jenis_insentif'], $curdate, $data['tujuan_surat_srd'], $data['nama_pertemuan_ppdks'], $data['skala_ppdks']);
} elseif ($data['jenis_insentif'] == 'visiting') {
    $sql .= ", nm_kegiatan_vl, waktu_pelaksanaan_vl) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die('Prepare failed: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'ssssssssssss', $data['jenis_surat_dsn'], $data['asal_surat_dsn'], $data['status_pengusul'], $data['nidn'], $data['no_telpon'], $data['id_sinta'], $data['prodi_pengusul'], $data['jenis_insentif'], $curdate, $data['tujuan_surat_srd'], $data['nm_kegiatan_vl'], $data['waktu_pelaksanaan_vl']);
} elseif ($data['jenis_insentif'] == 'hki') {
    $sql .= ", judul_hki, jenis_hki) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die('Prepare failed: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'ssssssssssss', $data['jenis_surat_dsn'], $data['asal_surat_dsn'], $data['status_pengusul'], $data['nidn'], $data['no_telpon'], $data['id_sinta'], $data['prodi_pengusul'], $data['jenis_insentif'], $curdate, $data['tujuan_surat_srd'], $data['judul_hki'], $data['jenis_hki']);
} elseif ($data['jenis_insentif'] == 'teknologi') {
    $sql .= ", teknologi_tg, deskripsi_tg) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die('Prepare failed: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'ssssssssssss', $data['jenis_surat_dsn'], $data['asal_surat_dsn'], $data['status_pengusul'], $data['nidn'], $data['no_telpon'], $data['id_sinta'], $data['prodi_pengusul'], $data['jenis_insentif'], $curdate, $data['tujuan_surat_srd'], $data['teknologi_tg'], $data['deskripsi_tg']);
} elseif ($data['jenis_insentif'] == 'buku') {
    $sql .= ", jenis_buku, judul_buku, sinopsis_buku, isbn_buku) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die('Prepare failed: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'ssssssssssssss', $data['jenis_surat_dsn'], $data['asal_surat_dsn'], $data['status_pengusul'], $data['nidn'], $data['no_telpon'], $data['id_sinta'], $data['prodi_pengusul'], $data['jenis_insentif'], $curdate, $data['tujuan_surat_srd'], $data['jenis_buku'], $data['judul_buku'], $data['sinopsis_buku'], $data['isbn_buku']);
} elseif ($data['jenis_insentif'] == 'model') {
    $sql .= ", nama_model_mpdks, deskripsi_mpdks) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die('Prepare failed: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'ssssssssssss', $data['jenis_surat_dsn'], $data['asal_surat_dsn'], $data['status_pengusul'], $data['nidn'], $data['no_telpon'], $data['id_sinta'], $data['prodi_pengusul'], $data['jenis_insentif'], $curdate, $data['tujuan_surat_srd'], $data['nama_model_mpdks'], $data['deskripsi_mpdks']);
} elseif ($data['jenis_insentif'] == 'insentif_publikasi') {
    $sql .= ", judul_ipbk, namaPenerbit_dan_waktu_ipbk, link_publikasi_ipbk) ";
    $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die('Prepare failed: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'sssssssssssss', $data['jenis_surat_dsn'], $data['asal_surat_dsn'], $data['status_pengusul'], $data['nidn'], $data['no_telpon'], $data['id_sinta'], $data['prodi_pengusul'], $data['jenis_insentif'], $curdate, $data['tujuan_surat_srd'], $data['judul_ipbk'], $data['namaPenerbit_dan_waktu_ipbk'], $data['link_publikasi_ipbk']);
}

if (mysqli_stmt_execute($stmt)) {
    $last_id = mysqli_insert_id($conn);
    $fileTypes = [
        'file_berkas_insentif_ppm' => 'ppm_insentif',
        'file_berkas_ppm' => 'ppm_pendukung',
        'file_berkas_insentif_pi' => 'pi_insentif',
        'file_berkas_pi' => 'pi_pendukung',
        'file_berkas_insentif_ppdpi' => 'ppdpi_insentif',
        'file_berkas_ppdpi' => 'ppdpi_pendukung',
        'file_berkas_insentif_ppdks' => 'ppdks_insentif',
        'file_berkas_ppdks' => 'ppdks_pendukung',
        'file_berkas_insentif_vl' => 'vl_insentif',
        'file_berkas_vl' => 'vl_pendukung',
        'file_berkas_insentif_hki' => 'hki_insentif',
        'file_berkas_hki' => 'hki_pendukung',
        'file_berkas_insentif_tg' => 'tg_insentif',
        'file_berkas_tg' => 'tg_pendukung',
        'file_berkas_insentif_buku' => 'buku_insentif',
        'file_berkas_buku' => 'buku_pendukung',
        'file_berkas_insentif_mpdks' => 'mpdks_insentif',
        'file_berkas_mpdks' => 'mpdks_pendukung',
        'file_berkas_insentif_ipbk' => 'ipbk_insentif',
        'file_berkas_ipbk' => 'ipbk_pendukung'
    ];

    $uploadErrorOccurred = false; // Flag to track upload errors

    foreach ($fileTypes as $fileKey => $prefix) {
        if (!empty($_FILES[$fileKey]["name"])) {
            $file = $_FILES[$fileKey];
            $uploadResult = uploadFile($file, '../uploads/dosen/', $prefix);
            if (isset($uploadResult['error'])) {
                $_SESSION['error'] = 'Upload failed: ' . $uploadResult['error'];
                $uploadErrorOccurred = true; // Set the flag to true
                break; // Exit the loop on error
            } else {
                $uploadedFileName = $uploadResult['success'];
                $updateSql = "UPDATE tb_srt_dosen SET $fileKey=? WHERE id_srt=?";
                $updateStmt = mysqli_prepare($conn, $updateSql);
                mysqli_stmt_bind_param($updateStmt, 'si', $uploadedFileName, $last_id);
                mysqli_stmt_execute($updateStmt);
            }
        }
    }

    if ($uploadErrorOccurred) {
        // Rollback the insert if any upload failed
        $deleteSql = "DELETE FROM tb_srt_dosen WHERE id_srt=?";
        $deleteStmt = mysqli_prepare($conn, $deleteSql);
        mysqli_stmt_bind_param($deleteStmt, 'i', $last_id);
        mysqli_stmt_execute($deleteStmt);

        header("Location: ../error.php");
        exit();
    }

    $_SESSION['message'] = 'Data Berhasil Disimpan';
    header("Location: ../success.php?id_srt=$last_id");
} else {
    $_SESSION['error'] = 'Data Gagal Disimpan';
    header("Location: ../error.php?error=uploadfailed");
}

mysqli_close($conn);
