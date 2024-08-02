<?php
session_start();
include '../koneksi.php';

// Function to handle file upload, add random number to file name, and return the new file name
function uploadFile($file, $destination) {
    $randomNumber = rand(1000, 9999); // Generate a random number
    $fileName = $randomNumber . "_" . basename($file["name"]);
    $targetFilePath = $destination . $fileName;
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        return $fileName;
    } else {
        return false;
    }
}

date_default_timezone_set('Asia/Jakarta');

$curdate = date("Y-m-d H:i:s");
$jenis_surat_dsn = $_POST["jenis_surat_dsn"];
$asal_surat = $_POST["asal_surat_dsn"];
$nama_dosen = $_POST["nama_dosen"];
$status_pengusul = $_POST["status_pengusul"];
$nidn = $_POST["nidn"];
$no_telpon = $_POST["no_telpon"];
$id_sinta = $_POST["id_sinta"];
$jumlah_dosen = $_POST["jumlah_dosen"];
$prodi_pengusul = $_POST["prodi_pengusul"];
$jenis_insentif = $_POST["jenis_insentif"];

// files ppm
$file_berkas = $_FILES["file_berkas_insentif_ppm"];
$file_ppm = $_FILES["file_berkas_ppm"];

//files pi
$file_berkas_pi = $_FILES["file_berkas_insentif_pi"];
$file_pi = $_FILES["file_berkas_pi"];

//files ppdpi
$file_berkas_ppdpi = $_FILES["file_berkas_insentif_ppdpi"];
$file_ppdpi = $_FILES["file_berkas_ppdpi"];

//files ppdks
$file_berkas_ppdks = $_FILES["file_berkas_insentif_ppdks"];
$file_ppdks = $_FILES["file_berkas_ppdks"];

//files vl
$file_berkas_vl = $_FILES["file_berkas_insentif_vl"];
$file_vl = $_FILES["file_berkas_vl"];

//files hki
$file_berkas_hki = $_FILES["file_berkas_insentif_hki"];
$file_hki = $_FILES["file_berkas_hki"];

//files tg
$file_berkas_tg = $_FILES["file_berkas_insentif_tg"];
$file_tg = $_FILES["file_berkas_tg"];

//files buku
$file_berkas_buku = $_FILES["file_berkas_insentif_buku"];
$file_buku = $_FILES["file_berkas_buku"];

// files mpdks
$file_berkas_mpdks = $_FILES["file_berkas_insentif_mpdks"];
$file_mpdks = $_FILES["file_berkas_mpdks"];

// files ipbk
$file_berkas_ipbk = $_FILES["file_berkas_insentif_ipbk"];
$file_ipbk = $_FILES["file_berkas_ipbk"];

// files srd
$file_srd = $_FILES["file_berkas_srd"];

// ppm
$skema_ppmdpek = $_POST["skema_ppmdpek"];
$judul_penelitian_ppm = $_POST["judul_penelitian_ppm"];

// pi
$jenis_publikasi_pi = $_POST["jenis_publikasi_pi"];
$judul_publikasi_pi = $_POST["judul_publikasi_pi"];
$nama_jurnal_pi = $_POST["nama_jurnal_pi"];
$vol_notahun_pi = $_POST["vol_notahun_pi"];
$link_jurnal_pi = $_POST["link_jurnal_pi"];

// ppdpi
$skala_ppdpi = $_POST["skala_ppdpi"];
$nama_pertemuan_ppdpi = $_POST["nama_pertemuan_ppdpi"];
$usulan_biaya_ppdpi = $_POST["usulan_biaya_ppdpi"];

// ppdks
$skala_ppdks = $_POST["skala_ppdks"];
$nama_pertemuan_ppdks = $_POST["nama_pertemuan_ppdks"];

// vl
$nm_kegiatan_vl = $_POST["nm_kegiatan_vl"];
$waktu_pelaksanaan_vl = $_POST["waktu_pelaksanaan_vl"];

// hki
$judul_hki = $_POST["judul_hki"];
$jenis_hki = $_POST["jenis_hki"];

// tg
$teknologi_tg = $_POST["teknologi_tg"];
$deskripsi_tg = $_POST["deskripsi_tg"];

// buku
$jenis_buku = $_POST["jenis_buku"];
$judul_buku = $_POST["judul_buku"];
$sinopsis_buku = $_POST["sinopsis_buku"];
$isbn_buku = $_POST["isbn_buku"];

// mpdks
$nama_model_mpdks = $_POST["nama_model_mpdks"];
$deskripsi_mpdks = $_POST["deskripsi_mpdks"];

// ipbk
$judul_ipbk = $_POST["judul_ipbk"];
$namaPenerbit_dan_waktu_ipbk = $_POST["namaPenerbit_dan_waktu_ipbk"];
$link_publikasi_ipbk = $_POST["link_publikasi_ipbk"];

// srd
$ttl_srd = $_POST["ttl_srd"];
$alamat_srd = $_POST["alamat_srd"];
$perihal_srd = $_POST["perihal_srd"];
$email_srd = $_POST["email_srd"];
$deskripsi_srd = $_POST["deskripsi_srd"];
$nama_perusahaan_srd = $_POST["nama_perusahaan_srd"];
$alamat_perusahaan_srd = $_POST["alamat_perusahaan_srd"];
$tujuan_surat = $_POST["tujuan_surat_srd"];
$nomor_surat_srd = $_POST["nomor_surat_srd"];


$tujuan_surat_srd = "lp3m";

$sql = "";

if ($skema_ppmdpek) {
    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, nama_dosen, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, skema_ppmdpek, judul_penelitian_ppm, tujuan_surat_srd, tanggal_surat)
    VALUES ('$jenis_surat_dsn', '$asal_surat', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$skema_ppmdpek', '$judul_penelitian_ppm', '$tujuan_surat_srd', '$curdate')";
} elseif ($jenis_publikasi_pi) {
    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, nama_dosen, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, jenis_publikasi_pi, judul_publikasi_pi, nama_jurnal_pi, vol_notahun_pi, link_jurnal_pi, tujuan_surat_srd, tanggal_surat)
    VALUES ('$jenis_surat_dsn', '$asal_surat', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$jenis_publikasi_pi', '$judul_publikasi_pi', '$nama_jurnal_pi', '$vol_notahun_pi', '$link_jurnal_pi', '$tujuan_surat_srd', '$curdate')";
} elseif ($skala_ppdpi) {
    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, nama_dosen, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, skala_ppdpi, nama_pertemuan_ppdpi, usulan_biaya_ppdpi, tujuan_surat_srd, tanggal_surat)
    VALUES ('$jenis_surat_dsn', '$asal_surat', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$skala_ppdpi', '$nama_pertemuan_ppdpi', '$usulan_biaya_ppdpi', '$tujuan_surat_srd', '$curdate')";
} elseif ($skala_ppdks) {
    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, nama_dosen, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, skala_ppdks, nama_pertemuan_ppdks, tujuan_surat_srd, tanggal_surat)
    VALUES ('$jenis_surat_dsn', '$asal_surat', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$skala_ppdks', '$nama_pertemuan_ppdks', '$tujuan_surat_srd', '$curdate')";
} elseif ($nm_kegiatan_vl){
    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, nama_dosen, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, nm_kegiatan_vl, waktu_pelaksanaan_vl, tujuan_surat_srd, tanggal_surat)
    VALUES ('$jenis_surat_dsn', '$asal_surat', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$nm_kegiatan_vl', '$waktu_pelaksanaan_vl', '$tujuan_surat_srd', '$curdate')";
} elseif($jenis_hki){
    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, nama_dosen, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, jenis_hki, judul_hki, tujuan_surat_srd, tanggal_surat)
    VALUES ('$jenis_surat_dsn', '$asal_surat', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$jenis_hki', '$judul_hki', '$tujuan_surat_srd', '$curdate')";
} elseif($teknologi_tg){
    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, nama_dosen, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, teknologi_tg, deskripsi_tg, tujuan_surat_srd, tanggal_surat)
    VALUES ('$jenis_surat_dsn', '$asal_surat', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$teknologi_tg', '$deskripsi_tg', '$tujuan_surat_srd', '$curdate')";
} elseif ($jenis_buku){
    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, nama_dosen, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, jenis_buku, judul_buku, sinopsis_buku, isbn_buku, tujuan_surat_srd, tanggal_surat)
    VALUES ('$jenis_surat_dsn', '$asal_surat', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$jenis_buku', '$judul_buku', '$sinopsis_buku', '$isbn_buku', '$tujuan_surat_srd', '$curdate')";
} elseif ($nama_model_mpdks){
    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, nama_dosen, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, nama_model_mpdks, deskripsi_mpdks, tujuan_surat_srd, tanggal_surat)
    VALUES ('$jenis_surat_dsn', '$asal_surat', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$nama_model_mpdks', '$deskripsi_mpdks', '$tujuan_surat_srd', '$curdate')";
} elseif ($judul_ipbk){
    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, nama_dosen, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, judul_ipbk, namaPenerbit_dan_waktu_ipbk, link_publikasi_ipbk, tujuan_surat_srd, tanggal_surat)
    VALUES ('$jenis_surat_dsn', '$asal_surat', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$judul_ipbk', '$namaPenerbit_dan_waktu_ipbk', '$link_publikasi_ipbk', '$tujuan_surat_srd', '$curdate')";
} elseif ($ttl_srd){
    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, nama_dosen, status_pengusul, nidn, no_telpon, ttl_srd, alamat_srd, perihal_srd, email_srd, deskripsi_srd, nama_perusahaan_srd, alamat_perusahaan_srd, nomor_surat_srd, tujuan_surat_srd, tanggal_surat)
    VALUES ('$jenis_surat_dsn', '$asal_surat', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$ttl_srd', '$alamat_srd', '$perihal_srd', '$email_srd', '$deskripsi_srd', '$nama_perusahaan_srd', '$alamat_perusahaan_srd', '$nomor_surat_srd', '$tujuan_surat', '$curdate')";
}

if ($conn->query($sql) === TRUE) {
    $last_id = $conn->insert_id; // Get the last inserted id (id_srt)
    
    // Define the destination directory for file uploads
    $uploadDir = '../uploads/insentif/';
    
    // Upload file_berkas
    if ($skema_ppmdpek && $file_berkas_name = uploadFile($file_berkas, $uploadDir)) {
        $sql_file_berkas = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_berkas_name')";
        $conn->query($sql_file_berkas);
    }elseif ($jenis_publikasi_pi && $file_berkas_pi_name = uploadFile($file_berkas_pi, $uploadDir)) {
        $sql_file_berkas_pi = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_berkas_pi_name')";
        $conn->query($sql_file_berkas_pi);
    }elseif ($skala_ppdpi && $file_berkas_ppdpi_name = uploadFile($file_berkas_ppdpi, $uploadDir)) {
        $sql_file_berkas_ppdpi = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_berkas_ppdpi_name')";
        $conn->query($sql_file_berkas_ppdpi);
    }elseif ($skala_ppdks && $file_berkas_ppdks_name = uploadFile($file_berkas_ppdks, $uploadDir)) {
        $sql_file_berkas_ppdks = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_berkas_ppdks_name')";
        $conn->query($sql_file_berkas_ppdks);
    }elseif ($nm_kegiatan_vl && $file_berkas_vl_name = uploadFile($file_berkas_vl, $uploadDir)) {
        $sql_file_berkas_vl = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_berkas_vl_name')";
        $conn->query($sql_file_berkas_vl);
    }elseif ($jenis_hki && $file_berkas_hki_name = uploadFile($file_berkas_hki, $uploadDir)) {
        $sql_file_berkas_hki = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_berkas_hki_name')";
        $conn->query($sql_file_berkas_hki);
    }elseif ($teknologi_tg && $file_berkas_tg_name = uploadFile($file_berkas_tg, $uploadDir)) {
        $sql_file_berkas_tg = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_berkas_tg_name')";
        $conn->query($sql_file_berkas_tg);
    }elseif ($jenis_buku && $file_berkas_buku_name = uploadFile($file_berkas_buku, $uploadDir)) {
        $sql_file_berkas_buku = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_berkas_buku_name')";
        $conn->query($sql_file_berkas_buku);
    }elseif ($nama_model_mpdks && $file_berkas_mpdks_name = uploadFile($file_berkas_mpdks, $uploadDir)) {
        $sql_file_berkas_mpdks = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_berkas_mpdks_name')";
        $conn->query($sql_file_berkas_mpdks);
    }elseif ($judul_ipbk && $file_berkas_ipbk_name = uploadFile($file_berkas_ipbk, $uploadDir)) {
        $sql_file_berkas_ipbk = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_berkas_ipbk_name')";
        $conn->query($sql_file_berkas_ipbk);
    }
    
    // Upload file_ppm based on jenis
    if ($skema_ppmdpek && $file_ppm_name = uploadFile($file_ppm, $uploadDir)) {
        $sql_file_ppm = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_ppm_name')";
        $conn->query($sql_file_ppm);
    } elseif ($jenis_publikasi_pi) {
        if ($file_pi_name = uploadFile($file_pi, $uploadDir)) {
            $sql_file_pi = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_pi_name')";
            $conn->query($sql_file_pi);
        }
    } elseif ($skala_ppdpi) {
        if ($file_ppdpi_name = uploadFile($file_ppdpi, $uploadDir)) {
            $sql_file_ppdpi = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_ppdpi_name')";
            $conn->query($sql_file_ppdpi);
        }
    } elseif ($skala_ppdks) {
        if ($file_ppdks_name = uploadFile($file_ppdks, $uploadDir)) {
            $sql_file_ppdks = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_ppdks_name')";
            $conn->query($sql_file_ppdks);
        }
    } elseif ($nm_kegiatan_vl) {
        if ($file_vl_name = uploadFile($file_vl, $uploadDir)) {
            $sql_file_vl = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_vl_name')";
            $conn->query($sql_file_vl);
        }
    } elseif ($jenis_hki) {
        if ($file_hki_name = uploadFile($file_hki, $uploadDir)) {
            $sql_file_hki = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_hki_name')";
            $conn->query($sql_file_hki);
        }
    } elseif ($teknologi_tg) {
        if ($file_tg_name = uploadFile($file_tg, $uploadDir)) {
            $sql_file_tg = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_tg_name')";
            $conn->query($sql_file_tg);
        }
    } elseif ($jenis_buku) {
        if ($file_buku_name = uploadFile($file_buku, $uploadDir)) {
            $sql_file_buku = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_buku_name')";
            $conn->query($sql_file_buku);
        }
    } elseif ($nama_model_mpdks) {
        if ($file_mpdks_name = uploadFile($file_mpdks, $uploadDir)) {
            $sql_file_mpdks = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_mpdks_name')";
            $conn->query($sql_file_mpdks);
        }
    } elseif ($judul_ipbk) {
        if ($file_ipbk_name = uploadFile($file_ipbk, $uploadDir)) {
            $sql_file_ipbk = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_ipbk_name')";
            $conn->query($sql_file_ipbk);
        }
    } elseif ($ttl_srd){
        if ($file_srd_name = uploadFile($file_srd, $uploadDir)) {
            $sql_file_srd = "INSERT INTO file_berkas (id_srt, nama_berkas) VALUES ('$last_id', '$file_srd_name')";
            $conn->query($sql_file_srd);
        }
    }

    echo "<script>alert('Surat berhasil dikirim'); setTimeout(function() {
        window.location.href = '../surat_keluar_nondis';}, 1000);
        </script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
