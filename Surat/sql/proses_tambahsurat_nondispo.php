<?php
session_start();
include '../koneksi.php';

$jenis_surat_dsn = $_POST["jenis_surat_dsn"];
    $asal_surat = $_POST["asal_surat_dsn"];
    $nama_dosen = $_POST["nama_dosen"];
    $status_pengusul = $_POST["status_pengusul"];
    $nidn = $_POST["nidn"];
    $no_telpon = $_POST["no_telpon"];
    $id_sinta = $_POST["id_sinta"];
    $prodi_pengusul = $_POST["prodi_pengusul"];
    $jenis_insentif = $_POST["jenis_insentif"];
    $skema_ppmdpek = $_POST["skema_ppmdpek"];
    $judul_penelitian_ppm = $_POST["judul_penelitian_ppm"];
    $jenis_publikasi_ppm = $_POST["jenis_publikasi_ppm"];
    $judul_publikasi_ppm = $_POST["judul_publikasi_ppm"];
    $nama_jurnal_ppm = $_POST["nama_jurnal_ppm"];
    $vol_notahun_ppm = $_POST["vol_notahun_ppm"];
    $link_jurnal_ppm = $_POST["link_jurnal_ppm"];
    $skala_ppm = $_POST["skala_ppm"];
    $nama_pertemuan_ppm = $_POST["nama_pertemuan_ppm"];
    $ususlan_biaya_ppm = $_POST["ususlan_biaya_ppm"];
    $skala_ppdks = $_POST["skala_ppdks"];
    $nama_pertemuan_ppdks = $_POST["nama_pertemuan_ppdks"];
    $nm_kegiatan_vl = $_POST["nm_kegiatan_vl"];
    $waktu_pelaksanaan_vl = $_POST["waktu_pelaksanaan_vl"];
    $jenis_hki = $_POST["jenis_hki"];
    $teknologi_tg = $_POST["teknologi_tg"];
    $deskripsi_tg = $_POST["deskripsi_tg"];
    $jenis_buku = $_POST["jenis_buku"];
    $judul_buku = $_POST["judul_buku"];
    $sinopsis_buku = $_POST["sinopsis_buku"];
    $isbn_buku = $_POST["isbn_buku"];
    $nama_model_mpdks = $_POST["nama_model_mpdks"];
    $deskripsi_mpdks = $_POST["deskripsi_mpdks"];
    $judul_ipbk = $_POST["judul_ipbk"];
    $namaPenerbit_dan_waktu_ipbk = $_POST["namaPenerbit_dan_waktu_ipbk"];
    $link_publikasi_ipbk = $_POST["link_publikasi_ipbk"];
    $ttl_srd = $_POST["ttl_srd"];
    $alamat_srd = $_POST["alamat_srd"];
    $perihal_srd = $_POST["perihal_srd"];
    $email_srd = $_POST["email_srd"];
    $deskripsi_srd = $_POST["deskripsi_srd"];
    $nama_perusahaan_srd = $_POST["nama_perusahaan_srd"];
    $alamat_perusahaan_srd = $_POST["alamat_perusahaan_srd"];
    $tujuan_surat_srd = $_POST["tujuan_surat_srd"];
    $nomor_surat_srd = $_POST["nomor_surat_srd"];
    $tujuan_surat_srd = "lp3m";

    $sql = "INSERT INTO tb_srt_dosen (jenis_surat, asal_surat, jumlah_dosen, nama_dosen, status_pengusul, nidn, no_telpon, id_sinta, prodi_pengusul, jenis_insentif, skema_ppmdpek,
                judul_penelitian_ppm, jenis_publikasi_ppm, judul_publikasi_ppm, nama_jurnal_ppm, vol_notahun_ppm, link_jurnal_ppm, skala_ppm, nama_pertemuan_ppm,
                usulan_biaya_ppm, skala_ppdks, nama_pertemuan_ppdks, nm_kegiatan_vl, waktu_pelaksanaan_vl, jenis_hki, judul_hki, teknologi_tg, deskripsi_tg,
                jenis_buku, judul_buku, sinopsis_buku, isbn_buku, nama_model_mpdks, deskripsi_mpdks, judul_ipbk, namaPenerbit_dan_waktu_ipbk, link_publikasi_ipbk,
                ttl_srd, alamat_srd, perihal_srd, email_srd, deskripsi_srd, nama_perusahaan_srd, alamat_perusahaan_srd, tujuan_surat_srd, nomor_surat_srd)
                VALUES ('$jenis_surat_dsn', '$asal_surat', '$jumlah_dosen', '$nama_dosen', '$status_pengusul', '$nidn', '$no_telpon', '$id_sinta', 
                '$prodi_pengusul', '$jenis_insentif', '$skema_ppmdpek', '$judul_penelitian_ppm', '$jenis_publikasi_ppm', '$judul_publikasi_ppm', '$nama_jurnal_ppm',
                '$vol_notahun_ppm', '$link_jurnal_ppm', '$skala_ppm', '$nama_pertemuan_ppm', '$usulan_biaya_ppm', '$skala_ppdks', '$nama_pertemuan_ppdks', '$nm_kegiatan_vl',
                '$waktu_pelaksanaan_vl', '$jenis_hki', '$judul_hki', '$teknologi_tg', '$deskripsi_tg', '$jenis_buku', '$judul_buku', '$sinopsis_buku', '$isbn_buku', 
                '$nama_model_mpdks', '$deskripsi_mpdks', '$judul_ipbk', '$namaPenerbit_dan_waktu_ipbk', '$link_publikasi_ipbk', '$ttl_srd', '$alamat_srd', '$perihal_srd', 
                '$email_srd', '$deskripsi_srd', '$nama_perusahaan_srd', '$alamat_perusahaan_srd', '$tujuan_surat_srd', '$nomor_surat_srd')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Surat berhasil dikirim'); setTimeout(function() {
            window.location.href = '../surat_keluar.php';}, 1000);
            </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();

?>


