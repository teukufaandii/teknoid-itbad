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

$conn = new mysqli($host, $user, $pass, $db);

function isSuratInsentif($jenis_surat)
{
    return $jenis_surat === "5";
}

function isSuratRisetDosen($jenis_surat)
{
    return $jenis_surat === "6";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
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
                <form method="post" action="sql/proses_tambahsurat_nondispo.php" class="form" enctype="multipart/form-data">
                    <div class="inputfield">
                        <label for="jenis_surat">Jenis Surat*</label>
                        <div class="custom_select">
                            <select name="jenis_surat_dsn" id="jenis_surat" class="select">
                                <option value="5">Surat Pengajuan Insentif Dosen</option>
                                <option value="6">Surat Pengajuan Riset Dosen</option>
                            </select>
                        </div>
                    </div>

                    <div class="inputfield">
                        <label for="">Nama Dosen*</label>
                        <input type="text" class="input" name="asal_surat_dsn" placeholder="Masukkan Asal Surat" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" readonly>
                    </div>

                    <div class="inputfield">
                        <label for="nim_dosen1">NIDN</label>
                        <input type="number" class="input" name="nidn" placeholder="Masukkan NIDN" value="<?php echo isset($_SESSION['pengguna']) ? $_SESSION['pengguna'] : ''; ?>" readonly>
                    </div>

                    <div class="inputfield">
                        <label for="no_hp1">Nomor Telepon </label>
                        <input type="number" class="input" name="no_telpon" placeholder="Masukkan Nomor Telepon" value="<?php echo isset($_SESSION['phone_number']) ? $_SESSION['phone_number'] : ''; ?>">
                    </div>

                    <!-- untuk surat insentif -->
                    <div id="input_insentif">
                        <div class="inputfield">
                            <label for="status_pengusul">Status pengusul</label>
                            <div class="custom_select">
                                <select name="status_pengusul" id="status" class="select">
                                    <option value="" hidden>Pilih Status Pengusul</option>
                                    <option>Ketua</option>
                                    <option>Anggota</option>
                                </select>
                            </div>
                        </div>
                        <div class="inputfield">
                            <label for="id_sinta">ID SINTA*</label>
                            <input type="text" class="input" name="id_sinta" id="id_sinta" placeholder="Masukkan ID SINTA">
                        </div>
                        <div class="inputfield">
                            <label for="prodi">Program Studi Pengusul*</label>
                            <div class="custom_select">
                                <select name="prodi_pengusul" id="prodi" class="select">
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
                            <label for="prodi">Jenis Insentif*</label>
                            <div class="custom_select">
                                <select name="jenis_insentif" id="jenis_insentif" class="select">
                                    <option value="" hidden>Pilih Jenis Insentif</option>
                                    <option value="penelitian">Penelitian & Pengabdian Masyarakat dengan Pendanaan Eksternal - Kompetitif</option>
                                    <option value="publikasi">Publikasi Ilmiah</option>
                                    <option value="pertemuan_ilmiah">Penyaji Paper Dalam Pertemuan Ilmiah</option>
                                    <option value="keynote_speaker">Pembicara Utama [Keynote Speaker] Dalam Pertemuan ilmiah</option>
                                    <option value="visiting">Visiting Lecturer/Researcher</option>
                                    <option value="hki">Hak Atas Kekayaan Intelektual [HKI]</option>
                                    <option value="teknologi">Teknologi Tepat Guna</option>
                                    <option value="buku">Buku</option>
                                    <option value="model">Model, Prototype, Desain, Karya Seni, Rekayasa Sosial, Kebijakan</option>
                                    <option value="insentif_publikasi">Insentif Publikasi Berita Kegiatan Pengabdian masyarakat di Koran, Majalah, Tabloid, TV dan Media Online</option>
                                </select>
                            </div>
                        </div>

                        <!-- Penelitian & Pengabdian Masyarakat dengan Pendanaan Eksternal-Kompetitif -->
                        <div id="penelitian" style="display: none;">
                            <div class="inputfield">
                                <label for="skema">Skema*</label>
                                <div class="custom_select">
                                    <select name="skema_ppmdpek" id="skema" class="select">
                                        <option value="" hidden>Pilih Jenis Skema</option>
                                        <option>Penelitian Berbasis Kompetitif (atau skema lain yang lebih tinggi dari skema berbasis kompetensi)</option>
                                        <option>Semua Skema Pengabdian Masyarakat dari Kemenristekdikti RI </option>
                                    </select>
                                </div>
                            </div>
                            <div class="inputfield">
                                <label for="id_sinta">Judul Penelitian/Pengabdian Masyarakat*</label>
                                <input type="text" class="input" name="judul_penelitian_ppm" id="id_sinta" placeholder="Masukkan Judul Penelitian/Pengabdian Masyarakat">
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Berkas Pendukung</label>
                                <input type="file" class="input" name="file_berkas_ppm" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Form Insentif</label>
                                <input type="file" class="input" name="file_berkas_insentif_ppm" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="btn-kirim">
                                <div class="floatFiller">ff</div>
                                <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                                <button class="btn" id="submitForm" type="button" onclick="showConfirmationPopup()">Kirim</button>
                            </div>
                        </div>

                        <!-- Publikasi Ilmiah -->
                        <div id="publikasi" style="display: none;">
                            <div class="inputfield">
                                <label for="prodi">Jenis Publikasi/Jurnal*</label>
                                <div class="custom_select">
                                    <select name="jenis_publikasi_pi" id="jenis_publikasi" class="select">
                                        <option value="" hidden>Pilih Publikasi/Jurnal</option>
                                        <option>Internasional Bereputasi [Terindeks ISI Knowledge, Thomson Reuter, USA dan Scopus, Netherland]</option>
                                        <option>Internasional Bereputasi Q1</option>
                                        <option>Internasional Bereputasi Q2</option>
                                        <option>Internasional Bereputasi Q3</option>
                                        <option>Internasional Bereputasi Q4</option>
                                        <option>Internasional Tidak Bereputasi</option>
                                        <option>Nasional Terakreditasi (SINTA 1)</option>
                                        <option>Nasional Terakreditasi (SINTA 2)</option>
                                        <option>Nasional Terakreditasi (SINTA 3)</option>
                                        <option>Nasional Terakreditasi (SINTA 4)</option>
                                        <option>Nasional Terakreditasi (SINTA 5)</option>
                                        <option>Nasional Terakreditasi (SINTA 6)</option>
                                        <option>Nasional tidak Terakreditasi (ber-ISSN)</option>
                                        <option>Lokal Tidak ber-ISSN</option>
                                        <option>Koran/Tabloid/Majalah Lokal</option>
                                        <option>Koran/Tabloid/Majalah Nasional</option>
                                    </select>
                                </div>
                            </div>
                            <div class="inputfield">
                                <label for="judul_publikasi">Judul Publikasi*</label>
                                <input type="text" class="input" name="judul_publikasi_pi" id="judul_publikasi" placeholder="Masukkan Judul Publikasi">
                            </div>
                            <div class="inputfield">
                                <label for="nama_jurnal">Nama Jurnal/Koran/<br>Majalah/Penerbit*</label>
                                <input type="text" class="input" name="nama_jurnal_pi" id="nama_jurnal" placeholder="Masukkan Nama Jurnal/Koran/Majalah/Penerbit">
                            </div>
                            <div class="inputfield">
                                <label for="">Vol. No. Tahun. ISSN-Edisi-Halaman*</label>
                                <input type="text" class="input" name="vol_notahun_pi" id="nama_jurnal" placeholder="Contoh: Vol. 2 No. 1 th. 2022 ISSN: 12345 Hal. 12-30">
                            </div>
                            <div class="inputfield">
                                <label for="">Tautan/link jurnal atau berkas pendukung</label>
                                <input type="text" class="input" name="link_jurnal_pi" id="link" placeholder="">
                            </div>
                            <div class="inputfield">
                                <label for="">Upload Berkas Pendukung</label>
                                <input type="file" class="input" name="file_berkas_pi" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Form Insentif</label>
                                <input type="file" class="input" name="file_berkas_insentif_pi" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="btn-kirim">
                                <div class="floatFiller">ff</div>
                                <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                                <button class="btn" id="submitForm" type="button" onclick="showConfirmationPopup()">Kirim</button>
                            </div>
                        </div>


                        <!-- Penyaji paper dalam pertemuan ilmiah-->
                        <div id="pertemuan_ilmiah" style="display: none;">
                            <div class="inputfield">
                                <label for="">Skala*</label>
                                <div class="custom_select">
                                    <select name="skala_ppdpi" id="skala" class="select">
                                        <option value="" hidden>Pilih Skala Paper</option>
                                        <option>International</option>
                                        <option>Nasional</option>
                                        <option>Lokal/Regional</option>
                                    </select>
                                </div>
                            </div>

                            <div class="inputfield">
                                <label for="">Nama Pertemuan*</label>
                                <input type="text" class="input" name="nama_pertemuan_ppdpi" id="nama_pertemuan" placeholder="Sebutkan nama acara, penyelenggara, dan waktu pelaksanaan">
                            </div>

                            <div class="inputfield">
                                <label for="">Usulan Biaya*</label>
                                <input type="text" class="input" name="usulan_biaya_ppdpi" id="usulan_biaya" placeholder="Sebutkan biaya yang diusulkan seperti biaya registasi, transportasi, hotel, dll.">
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Berkas Pendukung</label>
                                <input type="file" class="input" name="file_berkas_ppdpi" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Form Insentif</label>
                                <input type="file" class="input" name="file_berkas_insentif_ppdpi" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="btn-kirim">
                                <div class="floatFiller">ff</div>
                                <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                                <button class="btn" id="submitForm" type="button" onclick="showConfirmationPopup()">Kirim</button>
                            </div>
                        </div>


                        <!-- Penyaji paper dalam keynote speaker-->
                        <div id="keynote_speaker" style="display: none;">
                            <div class="inputfield">
                                <label for="">Skala*</label>
                                <div class="custom_select"><select name="skala_ppdks" id="skala_keynote" class="select">
                                        <option value="" hidden>Pilih Skala Paper l</option>
                                        <option>International</option>
                                        <option>Nasional</option>
                                        <option>Lokal/Regional</option>
                                    </select>
                                </div>
                            </div>
                            <div class="inputfield">
                                <label for="">Nama pertemuan Ilmiah*</label>
                                <input type="text" class="input" name="nama_pertemuan_ppdks" id="nama_keynote" placeholder="Sebutkan juga nama penyelenggara, dan waktu pelaksana">
                            </div>
                            <div class="inputfield">
                                <label for="">Upload Berkas Pendukung</label>
                                <input type="file" class="input" name="file_berkas_ppdks" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Form Insentif</label>
                                <input type="file" class="input" name="file_berkas_insentif_ppdks" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="btn-kirim">
                                <div class="floatFiller">ff</div>
                                <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                                <button class="btn" id="submitForm" type="button" onclick="showConfirmationPopup()">Kirim</button>
                            </div>
                        </div>


                        <!-- visiting lecturer-->
                        <div id="visiting" style="display: none;">
                            <div class="inputfield">
                                <label for="">Sebutkan Nama Kegiatan dan Lembaga Tujuan*</label>
                                <input type="text" class="input" name="nm_kegiatan_vl" id="nama_pertemuan" placeholder="">
                            </div>

                            <div class="inputfield">
                                <label for="">Waktu Pelaksanaan*</label>
                                <input type="datetime-local" class="input" name="waktu_pelaksanaan_vl" id="nama_pertemuan" placeholder="">
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Berkas Pendukung</label>
                                <input type="file" class="input" name="file_berkas_vl" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Form Insentif</label>
                                <input type="file" class="input" name="file_berkas_insentif_vl" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="btn-kirim">
                                <div class="floatFiller">ff</div>
                                <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                                <button class="btn" id="submitForm" type="button" onclick="showConfirmationPopup()">Kirim</button>
                            </div>
                        </div>


                        <!-- Hak kekayaan intelektual (HKI) -->
                        <div id="hki" style="display: none;">
                            <div class="inputfield">
                                <label for="">Jenis Kekayaan Intelektual*</label>
                                <div class="custom_select"><select name="jenis_hki" id="HKI_jenis" class="select">
                                        <option value="" hidden>Pilih Jenis Kekayaan Intelektual</option>
                                        <option>HKI atas hasil penelitian/buku</option>
                                        <option>Paten, Paten Sederhana</option>
                                        <option>Hak Cipta, Merek Dagang, Rahasia Dagang, Desain Produk Industri, Indikasi Geografis</option>
                                    </select>
                                </div>
                            </div>

                            <div class="inputfield">
                                <label for="">Judul Kekayaan Intelektual*</label>
                                <input type="text" class="input" name="judul_hki" id="HKI_judul" placeholder="Lengkapi dengan nomor atau identitas lainnya">
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Berkas Pendukung* <br> Lengkapi dengan nomor atau Identitas Lainnya</label>
                                <input type="file" class="input" name="file_berkas_hki" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Form Insentif</label>
                                <input type="file" class="input" name="file_berkas_insentif_hki" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="btn-kirim">
                                <div class="floatFiller">ff</div>
                                <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                                <button class="btn" id="submitForm" type="button" onclick="showConfirmationPopup()">Kirim</button>
                            </div>
                        </div>


                        <!-- teknologi tepat guna -->
                        <div id="teknologi" style="display: none;">
                            <div class="inputfield">
                                <label for="">Sebutkan Teknologi Tepat Guna Yang Diusulkan*</label>
                                <textarea type="text" class="input" name="teknologi_tg" id="" style="resize: none;"></textarea>
                            </div>
                            <div class="inputfield">
                                <label for="">Deskripsikan Teknologi Tepat Guna Yang Diusulkan*</label>
                                <textarea type="text" class="input" name="deskripsi_tg" id="deskripsi" style="resize: none;"></textarea>
                            </div>
                            <div class="inputfield">
                                <label for="">Upload Berkas Pendukung*</label>
                                <input type="file" class="input" name="file_berkas_tg" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>
                            <div class="inputfield">
                                <label for="">Upload Form Insentif</label>
                                <input type="file" class="input" name="file_berkas_insentif_tg" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="btn-kirim">
                                <div class="floatFiller">ff</div>
                                <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                                <button class="btn" id="submitForm" type="button" onclick="showConfirmationPopup()">Kirim</button>
                            </div>
                        </div>


                        <!-- buku -->
                        <div id="buku" style="display: none;">
                            <div class="inputfield">
                                <label>Jenis Buku*</label>
                                <div class="custom_select"><select name="jenis_buku" id="HKI_jenis" class="select">
                                        <option value="" hidden>Pilih Jenis Buku</option>
                                        <option>Buku Teks [ber-ISBN]</option>
                                        <option>Buku Ajar [ber-ISBN]</option>
                                        <option>Chapter book dalam buku bereputasi internasional [Springer, Sage Publication; dll]</option>
                                    </select>
                                </div>
                            </div>
                            <div class="inputfield">
                                <label for="">Judul Buku*</label>
                                <input type="text" class="input" name="judul_buku" id="judul_buku" placeholder="Masukkan Judul Buku">
                            </div>
                            <div class="inputfield">
                                <label for="">Sinopsis*</label>
                                <textarea type="text" class="input" name="sinopsis_buku" id="sinopsis" style="resize: none;" placeholder="Masukkan Sinopsis"></textarea>
                            </div>
                            <div class="inputfield">
                                <label for="">ISBN/Jumlah halaman/Penerbit*</label>
                                <input type="text" class="input" name="isbn_buku" id="isbn" placeholder="Masukkan ISBN/Jumlah halaman/Penerbit">
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Buku*</label>
                                <i class="fa-solid fa-circle-info" data-tooltip="additional-info"></i>
                                <input type="file" class="input" name="file_berkas_buku" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                                <p class="additional-info" style="color:red; display: none;">
                                    ! Jika buku dalam dicetak/hard copy silahkan upload foto sampulnya, versi cetak silahkan langsung sampaikan kepada LP3M
                                </p>
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Form Insentif</label>
                                <input type="file" class="input" name="file_berkas_insentif_buku" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="btn-kirim">
                                <div class="floatFiller">ff</div>
                                <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                                <button class="btn" id="submitForm" type="button" onclick="showConfirmationPopup()">Kirim</button>
                            </div>
                        </div>


                        <!-- Model, prototype, desain, karya seni, rekayasa sosial, kebijakan-->
                        <div id="model" style="display: none;">
                            <div class="inputfield">
                                <label for="">Sebutkan Nama Model, Prototype, Desain, Karya Seni, Rekayasa Sosial, Kebijakan yang Diusulkan*</label>
                                <input type="text" class="input" name="nama_model_mpdks" id="prototype_nama" placeholder="">
                            </div>

                            <div class="inputfield">
                                <label for="">Deskripsikan Model, Prototype, Desain, Karya Seni, Rekayasa Sosial, Kebijakan yang Diusulkan*</label>
                                <input type="text" class="input" name="deskripsi_mpdks" id="prototype_deskripsi" placeholder="">
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Berkas Pendukung*</label>
                                <input type="file" class="input" name="file_berkas_mpdks" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Form Insentif</label>
                                <input type="file" class="input" name="file_berkas_insentif_mpdks" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="btn-kirim">
                                <div class="floatFiller">ff</div>
                                <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                                <button class="btn" id="submitForm" type="button" onclick="showConfirmationPopup()">Kirim</button>
                            </div>
                        </div>


                        <!-- Insentif publikasi berita kegiatan pengabdian masyarakat di koran, majalah, tabloid, TV dan Media Online-->
                        <div id="insentif_publikasi" style="display: none;">
                            <div class="inputfield">
                                <label for="">Judul Publikasi*</label>
                                <input type="text" class="input" name="judul_ipbk" id="publikasi_judul" placeholder="">
                            </div>

                            <div class="inputfield">
                                <label for="">Nama Penerbit dan Waktu Terbit*</label>
                                <input type="text" class="input" name="namaPenerbit_dan_waktu_ipbk" id="publikasi_nama_waktu" placeholder="">
                            </div>

                            <div class="inputfield">
                                <label for="">Tautan Publikasi Berita (jika online)*</label>
                                <input type="text" class="input" name="link_publikasi_ipbk" id="publikasi_tautan" placeholder="">
                            </div>
                            <div class="inputfield">
                                <label for="">Upload Publikasi Berita (jika media cetak)*</label>
                                <input type="file" class="input" name="file_berkas_ipbk" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="inputfield">
                                <label for="">Upload Form Insentif</label>
                                <input type="file" class="input" name="file_berkas_insentif_ipbk" accept="application/pdf" style="border: none;">
                                <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                            </div>

                            <div class="btn-kirim">
                                <div class="floatFiller">ff</div>
                                <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                                <button class="btn" id="submitForm" type="button" onclick="showConfirmationPopup()">Kirim</button>
                            </div>
                        </div>
                    </div>

                    <!------------------- untuk surat riset dosen ----------------->
                    <div id="input_riset" style="display: none;">
                        <div class="inputfield" id="surat_riset_fields_1">
                            <label for="ttl">Tempat, tanggal lahir*</label>
                            <input type="text" class="input" name="ttl_srd" id="ttl" placeholder="Masukkan Tempat, tanggal lahir" required>
                        </div>

                        <div class="inputfield" id="surat_riset_fields_2">
                            <label for="alamat_domisili">Alamat Domisili*</label>
                            <input type="text" class="input" name="alamat_srd" id="alamat_domisili" placeholder="Masukkan Alamat Domisili" required>
                        </div>

                        <div class="inputfield">
                            <label for="">Perihal*</label>
                            <input type="text" class="input" name="perihal_srd" placeholder="Masukkan Perihal" required>
                        </div>

                        <div class="inputfield">
                            <label for="">Alamat Email*</label>
                            <input type="email" class="input" name="email_srd" placeholder="Masukkan Alamat Email" required>
                        </div>

                        <div class="inputfield">
                            <label for="">Deskripsi Singkat</label>
                            <input type="text" class="input" name="deskripsi_srd" placeholder="Masukkan Deskripsi Singkat" maxlength="200" required>
                        </div>

                        <div class="inputfield" id="surat_riset_fields_3">
                            <label for="nama_perusahaan">Nama Perusahaan* </label>
                            <input type="text" class="input" name="nama_perusahaan_srd" id="nama_perusahaan" placeholder="Masukkan Nama Perusahaan" required>
                        </div>

                        <div class="inputfield" id="surat_riset_fields_4">
                            <label for="alamat_perusahaan">Alamat Perusahaan*</label>
                            <input type="text" class="input" name="alamat_perusahaan_srd" id="alamat_perusahaan" placeholder="Masukkan Alamat Perusahaan" required>
                        </div>

                        <div class="inputfield">
                            <label for=""></label>
                            <input type="text" class="input" name="tujuan_surat_srd" placeholder="Masukkan Tujuan Surat" value="Humas" hidden>
                        </div>

                        <div class="inputfield">
                            <label for=""></label>
                            <input type="text" class="input" name="nomor_surat_srd" placeholder="Masukkan Nomor Surat" hidden>
                        </div>

                        <div class="inputfield">
                            <label for="">Upload Berkas Pendukung</label>
                            <input type="file" class="input" name="file_berkas_srd" accept="application/pdf" style="border: none;" required>
                            <p style="color: red;"> *Ukuran Max 10Mb (PDF)</p>
                        </div>

                        <div class="btn-kirim">
                            <div class="floatFiller">ff</div>
                            <input id="submitForm" type="submit" name="submit" value="Kirim" style="display: none;">
                            <button class="btn" id="submitForm" type="button" onclick="showConfirmationPopup()">Kirim</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php include 'footer.php'; ?>
    </div>
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

        // Menambahkan event listener untuk elemen select dengan id 'jenis_surat'
        document.getElementById('jenis_surat').addEventListener('change', function() {

            // Mendapatkan referensi ke elemen-elemen yang ingin diubah tampilannya
            let inputInsentif = document.getElementById('input_insentif');
            let inputRiset = document.getElementById('input_riset');

            inputInsentif.style.display = 'none';
            inputRiset.style.display = 'none';

            // Tampilkan atau sembunyikan input tambahan untuk Surat KKL atau Surat Riset
            if (this.value === '5') {
                inputInsentif.style.display = 'block';
                inputRiset.style.display = 'none';
            } else if (this.value === '6') {
                inputInsentif.style.display = 'none';
                inputRiset.style.display = 'block';
            }
        });


        // Menambahkan event listener untuk elemen select dengan id 'jenis_insentif'
        document.getElementById('jenis_insentif').addEventListener('change', function() {

            // Mendapatkan referensi ke elemen-elemen yang ingin diubah tampilannya
            let penelitian = document.getElementById('penelitian');
            let publikasi = document.getElementById('publikasi');
            let pertemuanIlmiah = document.getElementById('pertemuan_ilmiah');
            let keynoteSpeaker = document.getElementById('keynote_speaker');
            let visiting = document.getElementById('visiting');
            let hki = document.getElementById('hki');
            let teknologi = document.getElementById('teknologi');
            let buku = document.getElementById('buku');
            let model = document.getElementById('model');
            let insentifPublikasi = document.getElementById('insentif_publikasi');

            penelitian.style.display = 'none';
            publikasi.style.display = 'none';
            pertemuanIlmiah.style.display = 'none';
            keynoteSpeaker.style.display = 'none';
            visiting.style.display = 'none';
            hki.style.display = 'none';
            teknologi.style.display = 'none';
            model.style.display = 'none';
            buku.style.display = 'none';
            insentifPublikasi.style.display = 'none';

            // Menampilkan elemen yang dipilih berdasarkan nilai yang dipilih dari dropdown
            if (this.value === 'penelitian') {
                penelitian.style.display = 'block';
            } else if (this.value === 'publikasi') {
                publikasi.style.display = 'block';
            } else if (this.value === 'pertemuan_ilmiah') {
                pertemuanIlmiah.style.display = 'block';
            } else if (this.value === 'keynote_speaker') {
                keynoteSpeaker.style.display = 'block';
            } else if (this.value === 'visiting') {
                visiting.style.display = 'block';
            } else if (this.value === 'hki') {
                hki.style.display = 'block';
            } else if (this.value === 'teknologi') {
                teknologi.style.display = 'block';
            } else if (this.value === 'model') {
                model.style.display = 'block';
            } else if (this.value === 'buku') {
                buku.style.display = 'block';
            } else if (this.value === 'insentif_publikasi') {
                insentifPublikasi.style.display = 'block';
            }
        });
    </script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="js/dashboard-js.js"></script>
</body>

</html>

<?php
// Tutup koneksi database
$conn->close();
?>