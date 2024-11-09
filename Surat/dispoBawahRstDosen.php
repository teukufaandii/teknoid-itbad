<?php if ($_SESSION['akses'] == 'Humas') { ?>
    <?php
    // Memeriksa apakah tombol "Selesai" diklik
    if (isset($_POST['selesai'])) {

        $koneksi = mysqli_connect($host, $user, $pass, $db);

        // Melakukan update status_selesai menjadi true di tabel tb_surat_dis
        $query_update = "UPDATE tb_srt_dosen SET status_selesai = true WHERE id_srt = '$id'";
        mysqli_query($koneksi, $query_update);
        // Redirect atau tindakan lain setelah berhasil diperbarui
        header("Location: dashboard.php");
        exit(); // Pastikan untuk keluar setelah redirect
    }

    // Mendapatkan jenis surat dari tb_surat_dis
    $koneksi = mysqli_connect($host, $user, $pass, $db);
    $query_jenis_surat = "SELECT jenis_surat FROM tb_srt_dosen WHERE id_srt = '$id'";
    $result = mysqli_query($koneksi, $query_jenis_surat);
    $row = mysqli_fetch_assoc($result);
    $jenis_surat = $row['jenis_surat'];
    ?>

    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>
    
        <?php
                if ($jenis_surat == 6 ) : ?>
                    <div class="input-disposisi">
                        <label for="">Kode Surat*</label>
                        <input type="text" class="input" id="kd_srt_riset" name="kd_srt_riset" placeholder="Masukkan kode surat">
                    </div>

                    <div class="input-disposisi">
                        <label for="">Catatan Penyelesaian <span style="color: red;"></span></label>
                        <input type="text" id="catatan" class="input" name="catatan_penyelesaian_srd" placeholder="Masukkan Penyelesaian / Penolakan" required>
                    </div>

                    <div class="input-disposisi">
                        <label for="">Tanggal Disposisi<br> </label>
                        <div class="tgl">
                            <span id="tanggalwaktu"></span>
                        </div>
                    </div>

                <!-- <span style="color: red; font-size: 14px;">*Apabila ingin menolak surat ini, mohon kosongkan form kode surat</span> -->
                    <br> <br>
                    <input type="text" name="executor" value="<?php echo isset($_SESSION['asal_surat']) ? $_SESSION['asal_surat'] : ''; ?>" style="display: none;">

                    <div class="btn-kirim">
                        <div class="floatFiller">ffff</div>
                        <button type="button" id="btnSelesaiRisetDosen" style="cursor: pointer;">Selesai</button>
                    </div>

    <?php endif; ?>
<?php } ?>
