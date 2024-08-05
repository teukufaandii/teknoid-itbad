<?php
    session_start();
    include '../koneksi.php';

    // Function to handle file upload, add random number to file name, and return the new file name
    function uploadFile($file, $destination, $prefix) {
        $randomNumber = rand(1000, 9999); // Generate a random number
        $fileName = $prefix . "_" . $randomNumber . "_" . basename($file["name"]);
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
    $status_pengusul = $_POST["status_pengusul"];
    $nidn = $_POST["nidn"];
    $no_telpon = $_POST["no_telpon"];
    $id_sinta = $_POST["id_sinta"];
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
        $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, skema_ppmdpek, judul_penelitian_ppm, tujuan_surat_srd, tanggal_surat)
        VALUES ('$jenis_surat_dsn', '$asal_surat', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$skema_ppmdpek', '$judul_penelitian_ppm', '$tujuan_surat_srd', '$curdate')";
    } elseif ($jenis_publikasi_pi) {
        $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, jenis_publikasi_pi, judul_publikasi_pi, nama_jurnal_pi, vol_notahun_pi, link_jurnal_pi, tujuan_surat_srd, tanggal_surat)
        VALUES ('$jenis_surat_dsn', '$asal_surat', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$jenis_publikasi_pi', '$judul_publikasi_pi', '$nama_jurnal_pi', '$vol_notahun_pi', '$link_jurnal_pi', '$tujuan_surat_srd', '$curdate')";
    } elseif ($skala_ppdpi) {
        $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, skala_ppdpi, nama_pertemuan_ppdpi, usulan_biaya_ppdpi, tujuan_surat_srd, tanggal_surat)
        VALUES ('$jenis_surat_dsn', '$asal_surat', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$skala_ppdpi', '$nama_pertemuan_ppdpi', '$usulan_biaya_ppdpi', '$tujuan_surat_srd', '$curdate')";
    } elseif ($skala_ppdks) {
        $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, skala_ppdks, nama_pertemuan_ppdks, tujuan_surat_srd, tanggal_surat)
        VALUES ('$jenis_surat_dsn', '$asal_surat', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$skala_ppdks', '$nama_pertemuan_ppdks', '$tujuan_surat_srd', '$curdate')";
    } elseif ($nm_kegiatan_vl) {
        $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, nm_kegiatan_vl, waktu_pelaksanaan_vl, tujuan_surat_srd, tanggal_surat)
        VALUES ('$jenis_surat_dsn', '$asal_surat', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$nm_kegiatan_vl', '$waktu_pelaksanaan_vl', '$tujuan_surat_srd', '$curdate')";
    } elseif ($judul_hki) {
        $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, judul_hki, jenis_hki, tujuan_surat_srd, tanggal_surat)
        VALUES ('$jenis_surat_dsn', '$asal_surat', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$judul_hki', '$jenis_hki', '$tujuan_surat_srd', '$curdate')";
    } elseif ($teknologi_tg) {
        $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, teknologi_tg, deskripsi_tg, tujuan_surat_srd, tanggal_surat)
        VALUES ('$jenis_surat_dsn', '$asal_surat', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$teknologi_tg', '$deskripsi_tg', '$tujuan_surat_srd', '$curdate')";
    } elseif ($judul_buku) {
        $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, jenis_buku, judul_buku, sinopsis_buku, isbn_buku, tujuan_surat_srd, tanggal_surat)
        VALUES ('$jenis_surat_dsn', '$asal_surat', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$jenis_buku', '$judul_buku', '$sinopsis_buku', '$isbn_buku', '$tujuan_surat_srd', '$curdate')";
    } elseif ($nama_model_mpdks) {
        $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, nama_model_mpdks, deskripsi_mpdks, tujuan_surat_srd, tanggal_surat)
        VALUES ('$jenis_surat_dsn', '$asal_surat', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$nama_model_mpdks', '$deskripsi_mpdks', '$tujuan_surat_srd', '$curdate')";
    } elseif ($judul_ipbk) {
        $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, judul_ipbk, namaPenerbit_dan_waktu_ipbk, link_publikasi_ipbk, tujuan_surat_srd, tanggal_surat)
        VALUES ('$jenis_surat_dsn', '$asal_surat', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$judul_ipbk', '$namaPenerbit_dan_waktu_ipbk', '$link_publikasi_ipbk', '$tujuan_surat_srd', '$curdate')";
    } elseif ($ttl_srd) {
        $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, ttl_srd, alamat_srd, perihal_srd, email_srd, deskripsi_srd, nama_perusahaan_srd, alamat_perusahaan_srd, tujuan_surat_srd, tanggal_surat, nomor_surat_srd)
        VALUES ('$jenis_surat_dsn', '$asal_surat', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', '$prodi_pengusul', '$jenis_insentif', '$ttl_srd', '$alamat_srd', '$perihal_srd', '$email_srd', '$deskripsi_srd', '$nama_perusahaan_srd', '$alamat_perusahaan_srd', '$tujuan_surat_srd', '$curdate', '$nomor_surat_srd')";
    }

    if (mysqli_query($conn, $sql)) {
        $last_id = mysqli_insert_id($conn);

        // Upload files for each type and update the database
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
            'file_berkas_ipbk' => 'ipbk_pendukung',
            'file_berkas_srd' => 'srd'
        ];

        foreach ($fileTypes as $fileKey => $prefix) {
            if (!empty($_FILES[$fileKey]["name"])) {
                $file = $_FILES[$fileKey];
                $uploadedFileName = uploadFile($file, '../uploads/dosen/', $prefix);
                if ($uploadedFileName) {
                    $updateSql = "UPDATE tb_srt_dosen SET $fileKey='$uploadedFileName' WHERE id_srt='$last_id'";
                    mysqli_query($conn, $updateSql);
                }
            }
        }

        $_SESSION['message'] = 'Data Berhasil Disimpan';
        header("Location: ../success.php");
    } else {
        $_SESSION['error'] = 'Data Gagal Disimpan';
        header("Location: ../error.php");
    }

    mysqli_close($conn);
?>
