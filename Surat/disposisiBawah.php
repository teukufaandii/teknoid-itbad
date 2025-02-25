<!--disposisi untuk rektor-->
<?php if ($_SESSION['akses'] == 'Rektor') { ?>
    <?php
    // Query untuk mendapatkan data disposisi
    $query = "SELECT  dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10, catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query);
    ?>
    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>

    <?php include 'riwayat_dispo.php'; ?>

    <div class="input-disposisi">
        <label for="">Diteruskan kepada</label>
        <div class="radio">
            <div>
                <input type="checkbox" name="diteruskan[]" value="Warek1">
                <label for="">Warek 1</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="Warek2">
                <label for="">Warek 2</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="Warek3">
                <label for="">Warek 3</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="sekretaris">
                <label for="">Sekretaris</label>
            </div>
        </div>
    </div>
    <div class="input-disposisi">
        <label for="">Keputusan Rektor*</label>
        <div class="radio">
            <div>
                <input type="radio" name="keputusan" value="Tindak Lanjuti">
                <label for="">Tindak Lanjuti</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Dibicarakan dengan Rektor">
                <label for="">Dibicarakan dengan rektor</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Pendapat dan masukkan">
                <label for="">Pendapat dan masukkan</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Dicek dan Diteliti">
                <label for="">Dicek dan diteliti</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Dirapimkan">
                <label for="">Dirapimkan</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Dikoordinasikan">
                <label for="">Dikoordinasikan</label>
            </div>
        </div>
    </div>

    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="input-disposisi">
        <label for="">Catatan Disposisi*</label>
        <input type="text" class="input" name="catatan_disposisi" placeholder="Masukkan Catatan Disposisi" required>
    </div>
    <div class="input-disposisi">
        <label for="">Tanggal Disposisi</label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>
    <?php
    if (isset($_SESSION['akses'])) {
        $sql = "SELECT diteruskan_ke FROM tb_surat_dis WHERE id_surat = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Ambil baris (row) hasil query
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $diteruskan_ke_tb_surat_dis = $row['diteruskan_ke'];

            // Periksa jika diteruskan_ke yang berada di tb_surat_dis tidak sama dengan session akses
            if ($diteruskan_ke_tb_surat_dis == $_SESSION['akses']) {
                // Tampilkan button
    ?>

                <div class="btn-kirim">
                    <div class="floatFiller">ffff</div>
                    <button type="button" onclick="kirimDisposisi()" style="cursor: pointer;">Kirim</button>
                    <!-- <button type="button" onclick="batalDisposisi()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
                </div>

    <?php
            }
        }
    }
    ?>

    <script>
        function kirimDisposisi() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "check_disposisi.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === "allowed") {
                        // Lanjutkan dengan proses disposisi
                        proceedDisposisi();
                    } else {
                        swal("Gagal Disposisi!", "Anda sudah mendisposisi surat ini.", "error");
                    }
                }
            };
            xhr.send("id_surat=<?php echo $id; ?>");
        }

        function proceedDisposisi() {
            var diteruskan_checkboxes = document.querySelectorAll('input[name="diteruskan[]"]:checked'); // Ambil semua checkbox yang dicentang

            var tujuanMapping = {
                'Warek1': 'Warek 1',
                'Warek2': 'Warek 2',
                'Warek3': 'Warek 3',
                'sekretaris': 'Sekretaris'
            };

            var tujuan = [];
            var diteruskan = []; // Array untuk menyimpan semua nilai tujuan

            diteruskan_checkboxes.forEach(function(checkbox) {
                tujuan.push(tujuanMapping[checkbox.value]);
                diteruskan.push(checkbox.value); // Simpan nilai dari setiap checkbox yang dicentang
            });

            swal({
                title: "Anda yakin ingin mengirim disposisi ke " + tujuan.join(', ') + "?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willProceed) => {
                if (willProceed) {
                    var keputusan = document.querySelector('input[name="keputusan"]:checked').value;
                    var perihal = document.querySelector('input[name="perihal"]').value;
                    var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;

                    // Buat objek XMLHttpRequest
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "update_disposisi.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            swal("Disposisi Berhasil!", {
                                    icon: "success",
                                    buttons: "OK"
                                })
                                .then(() => {
                                    window.location.href = "dashboard.php";
                                });
                        }
                    };

                    // Set tanggal disposisi
                    var today = new Date();
                    var year = today.getFullYear();
                    var month = today.getMonth() + 1; // January is 0
                    var day = today.getDate();
                    var tanggal_disposisi = year + '-' + month + '-' + day;

                    // Kirim data ke server, gabungkan tujuan checkbox menjadi satu string dipisah koma
                    xhr.send("id_surat=<?php echo $id; ?>&keputusan=" + keputusan + "&tanggal_disposisi=" + tanggal_disposisi + "&perihal=" + perihal + "&catatan_disposisi=" + catatan_disposisi + "&diteruskan=" + diteruskan.join(','));
                } else {
                    swal("Disposisi dibatalkan!", {
                        icon: "error",
                    });
                }
            });
        }

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>


    <!--disposisi untuk warek 1-->
<?php } elseif ($_SESSION['akses'] == 'Warek1') { ?>
    <?php
    $query = "SELECT  dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10, catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query);
    ?>
    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>

    <?php include 'riwayat_dispo.php'; ?>

    <div class="input-disposisi">
        <label for="">Keputusan Warek 1*</label>
        <div class="radio">
            <div>
                <input type="radio" name="keputusan" value="Tindak Lanjuti">
                <label for="">Tindak Lanjuti</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Dibicarakan dengan rektor">
                <label for="">Dibicarakan dengan rektor</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Pendapat dan masukkan">
                <label for="">Pendapat dan masukkan</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Dicek dan diteliti">
                <label for="">Dicek dan diteliti</label>
            </div>
        </div>
    </div>

    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="input-disposisi">
        <label for="">Catatan Disposisi*</label>
        <input type="text" class="input" name="catatan_disposisi" placeholder="Masukkan Catatan Disposisi" required>
    </div>
    <div class="input-disposisi">
        <label for="">Diteruskan kepada<br></label>
        <div class="radio">
            <div>
                <input type="checkbox" name="diteruskan[]" value="Warek2">
                <label for="">Warek 2</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="Warek3">
                <label for="">Warek 3</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan" value="sekretaris">
                <label for="">Sekretaris</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="DekanFEB">
                <label for="">Dekan FEB</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="DekanFTD">
                <label for="">Dekan FTD</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="direkPasca">
                <label for="">Direktur Pasca</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_keuSyariah">
                <label for="">Prodi S2 Keuangan Syariah</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_manajemen">
                <label for="">Prodi S1 manajemen & D3 Keuangan Perbankan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_akuntansi">
                <label> Prodi S1 Akuntansi & D3 Akuntansi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_si">
                <label for="">Prodi S1 Sistem Informasi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_ti">
                <label for="">Prodi S1 Teknologi Informasi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_dkv">
                <label for="">Prodi S1 DKV</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_arsitek">
                <label for="">Prodi S1 Arsitektur</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="keuangan">
                <label for="">Bag. Keuangan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="sdm">
                <label for="">SDM</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="umum">
                <label for="">Bag. Umum</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="it_lab">
                <label for="">IT dan Lab</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="bpm">
                <label for="">BPM</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="Humas">
                <label for="">Humas</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="marketing">
                <label for="">Bag. Marketing</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="lp3m">
                <label for="">LP3M</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="kui_k">
                <label for="">KUI</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="akademik">
                <label for="">Bag. Akademik</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="ppik_kmhs">
                <label for="">PPIK dan Kemahasiswaan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="upt_perpus">
                <label for="">UPT Perpus</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="pusat_bisnis">
                <label for="">Pusat Bisnis</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="PSDOD">
                <label for="">PSDOD</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="PKAD">
                <label for="">PKAD</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="CHED">
                <label for="">CHED</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="PSIPP">
                <label for="">PSIPP</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="halal_center">
                <label for="">Halal Center</label>
            </div>
        </div>
    </div>

    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="input-disposisi">
        <label for="">Tanggal Disposisi<br> </label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>
    <div class="btn-kirim">
        <div class="floatFiller">ffff</div>
        <button type="button" onclick="kirimDisposisi()" style="cursor: pointer;">Kirim</button>
        <!--  <button type="button" onclick="batalDisposisi()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
    </div>


    <script>
        function kirimDisposisi() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "check_disposisi.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === "allowed") {
                        // Lanjutkan dengan proses disposisi
                        proceedDisposisi();
                    } else {
                        swal("Gagal Disposisi!", "Anda sudah mendisposisi surat ini.", "error");
                    }
                }
            };
            xhr.send("id_surat=<?php echo $id; ?>");
        }

        function proceedDisposisi() {
            var diteruskan_checkboxes = document.querySelectorAll('input[name="diteruskan[]"]:checked');
            var tujuanMapping = {
                'Warek2': 'Warek 2',
                'Warek3': 'Warek 3',
                'DekanFEB': 'Dekan FEB',
                'DekanFTD': 'Dekan FTD',
                'direkPasca': 'Direktur Pasca',
                'prodi_keuSyariah': 'Prodi S2 Keuangan Syariah',
                'prodi_akuntansi': 'Prodi S1 Akuntansi & D3 Akuntansi',
                'prodi_manajemen': 'Prodi S1 manajemen & D3 Keuangan Perbankan',
                'prodi_si': 'Prodi S1 Sistem Informasi',
                'prodi_ti': 'Prodi S1 Teknologi Informasi',
                'prodi_dkv': 'Prodi S1 Desain Komunikasi Visual',
                'prodi_arsitek': 'Prodi S1 Arsitektur',
                'keuangan': 'Bag. Keuangan',
                'sdm': 'SDM',
                'umum': 'Bag. Umum',
                'it_lab': 'IT dan Lab',
                'bpm': 'BPM',
                'Humas': 'Humas',
                'marketing': 'Bag. Marketing',
                'lp3m': 'LP3M',
                'kui_k': 'KUI',
                'akademik': 'Bag. Akademik',
                'ppik_kmhs': 'PPIK dan Kemahasiswaan',
                'upt_perpus': 'UPT Perpus',
                'pusat_bisnis': 'Pusat Bisnis',
                'PSDOD': 'PSDOD',
                'PKAD': 'PKAD',
                'CHED': 'CHED',
                'PSIPP': 'PSIPP',
                'halal_center': 'Halal Center',
            };

            var tujuan = [];

            diteruskan_checkboxes.forEach(function(checkbox) {
                tujuan.push(tujuanMapping[checkbox.value]);
            });

            swal({
                title: "Anda yakin ingin mengirim disposisi ke " + tujuan.join(', ') + "?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willProceed) => {
                if (willProceed) {
                    if (willProceed) {
                        var keputusan = document.querySelector('input[name="keputusan"]:checked').value;

                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value; // Sesuaikan dengan name pada input

                        // Mendapatkan semua checkbox yang dipilih
                        var diteruskan_checkboxes = document.querySelectorAll('input[name="diteruskan[]"]:checked');
                        var diteruskan_values = [];

                        // Iterasi melalui setiap checkbox yang dipilih dan menambahkannya ke dalam array diteruskan_values
                        diteruskan_checkboxes.forEach(function(checkbox) {
                            diteruskan_values.push(checkbox.value);
                        });

                        // Buat objek XMLHttpRequest
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "update_disposisi.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                swal("Disposisi Berhasil!", {
                                        icon: "success",
                                        buttons: "OK"
                                    })
                                    .then(() => {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };

                        // Set tanggal disposisi
                        var today = new Date();
                        var year = today.getFullYear();
                        var month = today.getMonth() + 1; // January is 0
                        var day = today.getDate();
                        var tanggal_disposisi = year + '-' + month + '-' + day;

                        // Kirim data ke server
                        xhr.send("id_surat=<?php echo $id; ?>&keputusan=" + keputusan + "&tanggal_disposisi=" + tanggal_disposisi + "&catatan_disposisi=" + catatan_disposisi + "&diteruskan=" + JSON.stringify(diteruskan_values));
                    } else {
                        swal("Disposisi dibatalkan!", {
                            icon: "error",
                        });
                    }
                } else {
                    swal("Disposisi dibatalkan!", {
                        icon: "error"
                    });
                }
            });
        }

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>

    <!--disposisi untuk warek 2-->
<?php } elseif ($_SESSION['akses'] == 'Warek2') { ?>
    <?php
    $query = "SELECT  dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10, catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query);
    ?>
    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>

    <?php include 'riwayat_dispo.php'; ?>

    <div class="input-disposisi">
        <label for="">Keputusan Warek 2*</label>
        <div class="radio">
            <div>
                <input type="radio" name="keputusan" value="Tindak Lanjuti">
                <label for="">Tindak Lanjuti</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Dibicarakan dengan rektor">
                <label for="">Dibicarakan dengan rektor</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Pendapat dan masukkan">
                <label for="">Pendapat dan masukkan</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Dicek dan diteliti">
                <label for="">Dicek dan diteliti</label>
            </div>
        </div>
    </div>

    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="input-disposisi">
        <label for="">Catatan Disposisi*</label>
        <input type="text" class="input" name="catatan_disposisi" placeholder="Masukkan Catatan Disposisi" required>
    </div>
    <div class="input-disposisi">
        <label for="">Diteruskan kepada<br></label>
        <div class="radio">
            <div>
                <input type="checkbox" name="diteruskan[]" value="Warek1">
                <label for="">Warek 1</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="Warek3">
                <label for="">Warek 3</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="sekretaris">
                <label for="">Sekretaris</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="DekanFEB">
                <label for="">Dekan FEB</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="DekanFTD">
                <label for="">Dekan FTD</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="direkPasca">
                <label for="">Direktur Pasca</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_keuSyariah">
                <label for="">Prodi S2 Keuangan Syariah</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_manajemen">
                <label for="">Prodi S1 manajemen & D3 Keuangan Perbankan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_akuntansi">
                <label> Prodi S1 Akuntansi & D3 Akuntansi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_si">
                <label for="">Prodi S1 Sistem Informasi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_ti">
                <label for="">Prodi S1 Teknologi Informasi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_dkv">
                <label for="">Prodi S1 Desain Komunikasi Visual</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_arsitek">
                <label for="">Prodi S1 Arsitektur</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="keuangan">
                <label for="">Bag. Keuangan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="sdm">
                <label for="">SDM</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="umum">
                <label for="">Bag. Umum</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="it_lab">
                <label for="">IT dan Lab</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="bpm">
                <label for="">BPM</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="Humas">
                <label for="">Humas</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="marketing">
                <label for="">Bag. Marketing</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="lp3m">
                <label for="">LP3M</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="kui_k">
                <label for="">KUI</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="akademik">
                <label for="">Bag. Akademik</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="ppik_kmhs">
                <label for="">PPIK dan Kemahasiswaan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="upt_perpus">
                <label for="">UPT Perpus</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="pusat_bisnis">
                <label for="">Pusat Bisnis</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="PSDOD">
                <label for="">PSDOD</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="PKAD">
                <label for="">PKAD</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="CHED">
                <label for="">CHED</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="PSIPP">
                <label for="">PSIPP</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="halal_center">
                <label for="">Halal Center</label>
            </div>
        </div>
    </div>
    <div class="input-disposisi">
        <label for="">Tanggal Disposisi<br> </label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>
    <div class="btn-kirim">
        <div class="floatFiller">ffff</div>
        <button type="button" onclick="kirimDisposisi()" style="cursor: pointer;">Kirim</button>
        <!-- <button type="button" onclick="batalDisposisi()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
    </div>

    <script>
        function kirimDisposisi() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "check_disposisi.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === "allowed") {
                        // Lanjutkan dengan proses disposisi
                        proceedDisposisi();
                    } else {
                        swal("Gagal Disposisi!", "Anda sudah mendisposisi surat ini.", "error");
                    }
                }
            };
            xhr.send("id_surat=<?php echo $id; ?>");
        }

        function proceedDisposisi() {
            var diteruskan_checkboxes = document.querySelectorAll('input[name="diteruskan[]"]:checked');
            var tujuanMapping = {
                'Warek1': 'Warek 1',
                'Warek3': 'Warek 3',
                'sekretaris': 'Sekretaris',
                'DekanFEB': 'Dekan FEB',
                'DekanFTD': 'Dekan FTD',
                'direkPasca': 'Direktur Pasca',
                'prodi_keuSyariah': 'Prodi S2 Keuangan Syariah',
                'prodi_akuntansi': 'Prodi S1 Akuntansi & D3 Akuntansi',
                'prodi_manajemen': 'Prodi S1 manajemen & D3 Keuangan Perbankan',
                'prodi_si': 'Prodi S1 Sistem Informasi',
                'prodi_ti': 'Prodi S1 Teknologi Informasi',
                'prodi_dkv': 'Prodi S1 Desain Komunikasi Visual',
                'prodi_arsitek': 'Prodi S1 Arsitektur',
                'keuangan': 'Bag. Keuangan',
                'sdm': 'SDM',
                'umum': 'Bag. Umum',
                'it_lab': 'IT dan Lab',
                'bpm': 'BPM',
                'Humas': 'Humas',
                'marketing': 'Bag. Marketing',
                'lp3m': 'LP3M',
                'kui_k': 'KUI',
                'akademik': 'Bag. Akademik',
                'ppik_kmhs': 'PPIK dan Kemahasiswaan',
                'upt_perpus': 'UPT Perpus',
                'pusat_bisnis': 'Pusat Bisnis',
                'PSDOD': 'PSDOD',
                'PKAD': 'PKAD',
                'CHED': 'CHED',
                'PSIPP': 'PSIPP',
                'halal_center': 'Halal Center',
            };
            var tujuan = [];

            diteruskan_checkboxes.forEach(function(checkbox) {
                tujuan.push(tujuanMapping[checkbox.value]);
            });
            swal({
                title: "Anda yakin ingin mengirim disposisi ke " + tujuan.join(', ') + "?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willProceed) => {
                if (willProceed) {
                    if (willProceed) {
                        var keputusan = document.querySelector('input[name="keputusan"]:checked').value;

                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value; // Sesuaikan dengan name pada input

                        var diteruskan_checkboxes = document.querySelectorAll('input[name="diteruskan[]"]:checked');
                        var diteruskan_values = [];

                        diteruskan_checkboxes.forEach(function(checkbox) {
                            diteruskan_values.push(checkbox.value);
                        });
                        // Buat objek XMLHttpRequest
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "update_disposisi.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                swal("Disposisi Berhasil!", {
                                        icon: "success",
                                        buttons: "OK"
                                    })
                                    .then(() => {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };

                        // Set tanggal disposisi
                        var today = new Date();
                        var year = today.getFullYear();
                        var month = today.getMonth() + 1; // January is 0
                        var day = today.getDate();
                        var tanggal_disposisi = year + '-' + month + '-' + day;

                        // Kirim data ke server
                        xhr.send("id_surat=<?php echo $id; ?>&keputusan=" + keputusan + "&tanggal_disposisi=" + tanggal_disposisi + "&catatan_disposisi=" + catatan_disposisi + "&diteruskan=" + JSON.stringify(diteruskan_values));
                    } else {
                        swal("Disposisi dibatalkan!", {
                            icon: "error",
                        });
                    }
                } else {
                    swal("Disposisi dibatalkan!", {
                        icon: "error"
                    });
                }
            });
        }

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>


    <!-- disposisi untuk warek 3-->
<?php } elseif ($_SESSION['akses'] == 'Warek3') { ?>
    <?php
    $query = "SELECT  dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10, catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query);
    ?>
    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>

    <?php include 'riwayat_dispo.php'; ?>

    <div class="input-disposisi">
        <label for="">Keputusan Warek 3*</label>
        <div class="radio">
            <div>
                <input type="radio" name="keputusan" value="Tindak Lanjuti">
                <label for="">Tindak Lanjuti</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Dibicarakan dengan rektor">
                <label for="">Dibicarakan dengan rektor</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Pendapat dan masukkan">
                <label for="">Pendapat dan masukkan</label>
            </div>
            <div>
                <input type="radio" name="keputusan" value="Dicek dan diteliti">
                <label for="">Dicek dan diteliti</label>
            </div>
        </div>
    </div>

    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="input-disposisi">
        <label for="">Catatan Disposisi*</label>
        <input type="text" class="input" name="catatan_disposisi" placeholder="Masukkan Catatan Disposisi" required>
    </div>
    <div class="input-disposisi">
        <label for="">Diteruskan kepada<br></label>
        <div class="radio">
            <div>
                <input type="checkbox" name="diteruskan[]" value="Warek1">
                <label for="">Warek 1</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="Warek2">
                <label for="">Warek 2</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="sekretaris">
                <label for="">Sekretaris</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="DekanFEB">
                <label for="">Dekan FEB</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="DekanFTD">
                <label for="">Dekan FTD</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="direkPasca">
                <label for="">Direktur Pasca</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_keuSyariah">
                <label for="">Prodi S2 Keuangan Syariah</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_manajemen">
                <label for="">Prodi S1 manajemen & D3 Keuangan Perbankan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_akuntansi">
                <label> Prodi S1 Akuntansi & D3 Akuntansi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_si">
                <label for="">Prodi S1 Sistem Informasi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_ti">
                <label for="">Prodi S1 Teknologi Informasi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_dkv">
                <label for="">Prodi S1 Desain Komunikasi Visual</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_arsitek">
                <label for="">Prodi S1 Arsitektur</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="keuangan">
                <label for="">Bag. Keuangan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="sdm">
                <label for="">SDM</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="umum">
                <label for="">Bag. Umum</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="it_lab">
                <label for="">IT dan Lab</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="bpm">
                <label for="">BPM</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="Humas">
                <label for="">Humas</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="marketing">
                <label for="">Bag. Marketing</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="lp3m">
                <label for="">LP3M</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="kui_k">
                <label for="">KUI</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="akademik">
                <label for="">Bag. Akademik</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="ppik_kmhs">
                <label for="">PPIK dan Kemahasiswaan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="upt_perpus">
                <label for="">UPT Perpus</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="pusat_bisnis">
                <label for="">Pusat Bisnis</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="PSDOD">
                <label for="">PSDOD</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="PKAD">
                <label for="">PKAD</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="CHED">
                <label for="">CHED</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="PSIPP">
                <label for="">PSIPP</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="halal_center">
                <label for="">Halal Center</label>
            </div>
        </div>
    </div>
    <div class="input-disposisi">
        <label for="">Tanggal Disposisi<br> </label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>
    <div class="btn-kirim">
        <div class="floatFiller">ffff</div>
        <button type="button" onclick="kirimDisposisi()" style="cursor: pointer;">Kirim</button>
        <!-- <button type="button" onclick="batalDisposisi()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
    </div>

    <script>
        function kirimDisposisi() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "check_disposisi.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === "allowed") {
                        // Lanjutkan dengan proses disposisi
                        proceedDisposisi();
                    } else {
                        swal("Gagal Disposisi!", "Anda sudah mendisposisi surat ini.", "error");
                    }
                }
            };
            xhr.send("id_surat=<?php echo $id; ?>");
        }

        function proceedDisposisi() {
            var diteruskan_checkboxes = document.querySelectorAll('input[name="diteruskan[]"]:checked');
            var tujuanMapping = {
                'Warek1': 'Warek 1',
                'Warek2': 'Warek 2',
                'sekretaris': 'Sekretaris',
                'DekanFEB': 'Dekan FEB',
                'DekanFTD': 'Dekan FTD',
                'direkPasca': 'Direktur Pasca',
                'prodi_keuSyariah': 'Prodi S2 Keuangan Syariah',
                'prodi_akuntansi': 'Prodi S1 Akuntansi & D3 Akuntansi',
                'prodi_manajemen': 'Prodi S1 manajemen & D3 Keuangan Perbankan',
                'prodi_si': 'Prodi S1 Sistem Informasi',
                'prodi_ti': 'Prodi S1 Teknologi Informasi',
                'prodi_dkv': 'Prodi S1 Desain Komunikasi Visual',
                'prodi_arsitek': 'Prodi S1 Arsitektur',
                'keuangan': 'Bag. Keuangan',
                'sdm': 'SDM',
                'umum': 'Bag. Umum',
                'it_lab': 'IT dan Lab',
                'bpm': 'BPM',
                'Humas': 'Humas',
                'marketing': 'Bag. Marketing',
                'lp3m': 'LP3M',
                'kui_k': 'KUI',
                'akademik': 'Bag. Akademik',
                'ppik_kmhs': 'PPIK dan Kemahasiswaan',
                'upt_perpus': 'UPT Perpus',
                'pusat_bisnis': 'Pusat Bisnis',
                'PSDOD': 'PSDOD',
                'PKAD': 'PKAD',
                'CHED': 'CHED',
                'PSIPP': 'PSIPP',
                'halal_center': 'Halal Center',
            };
            var tujuan = [];

            diteruskan_checkboxes.forEach(function(checkbox) {
                tujuan.push(tujuanMapping[checkbox.value]);
            });

            swal({
                title: "Anda yakin ingin mengirim disposisi ke " + tujuan.join(', ') + "?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willProceed) => {
                if (willProceed) {
                    if (willProceed) {
                        var keputusan = document.querySelector('input[name="keputusan"]:checked').value;

                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value; // Sesuaikan dengan name pada input
                        // Mendapatkan semua checkbox yang dipilih
                        var diteruskan_checkboxes = document.querySelectorAll('input[name="diteruskan[]"]:checked');
                        var diteruskan_values = [];

                        // Iterasi melalui setiap checkbox yang dipilih dan menambahkannya ke dalam array diteruskan_values
                        diteruskan_checkboxes.forEach(function(checkbox) {
                            diteruskan_values.push(checkbox.value);
                        });

                        // Buat objek XMLHttpRequest
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "update_disposisi.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                swal("Disposisi Berhasil!", {
                                        icon: "success",
                                        buttons: "OK"
                                    })
                                    .then(() => {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };

                        // Set tanggal disposisi
                        var today = new Date();
                        var year = today.getFullYear();
                        var month = today.getMonth() + 1; // January is 0
                        var day = today.getDate();
                        var tanggal_disposisi = year + '-' + month + '-' + day;

                        // Kirim data ke server
                        xhr.send("id_surat=<?php echo $id; ?>&keputusan=" + keputusan + "&tanggal_disposisi=" + tanggal_disposisi + "&catatan_disposisi=" + catatan_disposisi + "&diteruskan=" + JSON.stringify(diteruskan_values));
                    } else {
                        swal("Disposisi dibatalkan!", {
                            icon: "error",
                        });
                    }
                } else {
                    swal("Disposisi dibatalkan!", {
                        icon: "error"
                    });
                }
            });
        }

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>


    <!-- disposisi untuk dekan FTD-->
<?php } elseif ($_SESSION['akses'] == 'DekanFTD') { ?>
    <?php
    $query = "SELECT  dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10, catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query);
    ?>
    <?php
    // Memeriksa apakah tombol "Selesai" diklik
    if (isset($_POST['selesai'])) {

        include 'koneksi.php';

        // Melakukan update status_selesai menjadi true di tabel tb_surat_dis
        $query_update = "UPDATE tb_surat_dis SET status_selesai = true WHERE id_surat = '$id'";
        mysqli_query($koneksi, $query_update);
        // Redirect atau tindakan lain setelah berhasil diperbarui
        header("Location: dashboard.php");
        exit(); // Pastikan untuk keluar setelah redirect
    }
    ?>

    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>

    <?php include 'riwayat_dispo.php'; ?>

    <div class="input-disposisi" style="display: none;">
        <label for="">Keputusan Dekan FTD*</label>
        <div class="radio" style="display: none;">
            <div>
                <input type="radio" name="keputusan" value="Tindak Lanjuti" checked>
                <label for=""></label>Tindak Lanjuti</label>
            </div>
        </div>
    </div>

    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="input-disposisi">
        <label for="">Catatan Disposisi*</label>
        <input type="text" class="input" name="catatan_disposisi" placeholder="Masukkan Catatan Disposisi" required>
    </div>
    <div class="input-disposisi">
        <label for="">Diteruskan kepada</label>
        <div class="radio">
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_si">
                <label for="">Prodi S1 Sistem Informasi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_ti">
                <label for="">Prodi S1 Teknologi Informasi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_dkv">
                <label for="">Prodi S1 Desain Komunikasi Visual</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_arsitek">
                <label for="">Prodi S1 Arsitektur</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="keuangan">
                <label for="">Bag. Keuangan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="akademik">
                <label for="">Bag. Akademik</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="umum">
                <label for="">Bag. Umum</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="kui_k">
                <label for="">KUI dan Kerjasama</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="marketing">
                <label for="">Bag. Marketing</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="upt_perpus">
                <label for="">UPT Perpus</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="sdm">
                <label for="">SDM</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="bpm">
                <label for="">BPM</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="lp3m">
                <label for="">LP3M</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="it_lab">
                <label for="">IT dan Lab</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="keuangan">
                <label for="">Keuangan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="ppik_kmhs">
                <label for="">PPIK dan Kemahasiswaan</label>
            </div>
        </div>
    </div>
    <div class="input-disposisi">
        <label for="">Tanggal Disposisi<br> </label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>
    <div class="btn-kirim">
        <div class="floatFiller">ffff</div>
        <button type="button" onclick="kirimDisposisi()" style="cursor: pointer;">Disposisi</button>
        <button type="button" id="btnSelesai" style="cursor: pointer;">Selesai</button>
        <!-- <button type="button" onclick="batalDisposisi()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
    </div>

    <script>
        document.getElementById('btnSelesai').addEventListener('click', function() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menyelesaikan surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_selesai.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Dikonfirmasi Selesai!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=selesai");
                    } else {
                        swal("Dibatalkan", "Surat tidak diselesaikan", "info");
                    }
                });
        });

        function kirimDisposisi() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "check_disposisi.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === "allowed") {
                        // Lanjutkan dengan proses disposisi
                        proceedDisposisi();
                    } else {
                        swal("Gagal Disposisi!", "Anda sudah mendisposisi surat ini.", "error");
                    }
                }
            };
            xhr.send("id_surat=<?php echo $id; ?>");
        }

        function proceedDisposisi() {
            var diteruskan_checkboxes = document.querySelectorAll('input[name="diteruskan[]"]:checked');
            var tujuanMapping = {
                'prodi_si': 'Prodi S1 Sistem Informasi',
                'prodi_ti': 'Prodi S1 Teknologi Informasi',
                'prodi_dkv': 'Prodi S1 Desain Komunikasi Visual',
                'prodi_arsitek': 'Prodi S1 Arsitektur',
                'keuangan': 'Bag. Keuangan',
                'akademik': 'Bag. Akademik',
                'umum': 'Bag. Umum',
                'kui_k': 'KUI dan Kerjasama',
                'marketing': 'Bag. Marketing',
                'upt_perpus': 'UPT Perpus',
                'sdm': 'SDM',
                'bpm': 'BPM',
                'lp3m': 'LP3M',
                'it_lab': 'IT dan Lab',
                'keuangan': 'Keuangan',
                'ppik_kmhs': 'PPIK dan Kemahasiswaan',
            };
            var tujuan = [];

            diteruskan_checkboxes.forEach(function(checkbox) {
                tujuan.push(tujuanMapping[checkbox.value]);
            });

            swal({
                title: "Anda yakin ingin mengirim disposisi ke " + tujuan.join(', ') + "?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willProceed) => {
                if (willProceed) {
                    if (willProceed) {
                        var keputusan = document.querySelector('input[name="keputusan"]:checked').value;

                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value; // Sesuaikan dengan name pada input
                        // Mendapatkan semua checkbox yang dipilih
                        var diteruskan_checkboxes = document.querySelectorAll('input[name="diteruskan[]"]:checked');
                        var diteruskan_values = [];

                        // Iterasi melalui setiap checkbox yang dipilih dan menambahkannya ke dalam array diteruskan_values
                        diteruskan_checkboxes.forEach(function(checkbox) {
                            diteruskan_values.push(checkbox.value);
                        });

                        // Buat objek XMLHttpRequest
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "update_disposisi.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                swal("Disposisi Berhasil!", {
                                        icon: "success",
                                        buttons: "OK"
                                    })
                                    .then(() => {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };

                        // Set tanggal disposisi
                        var today = new Date();
                        var year = today.getFullYear();
                        var month = today.getMonth() + 1; // January is 0
                        var day = today.getDate();
                        var tanggal_disposisi = year + '-' + month + '-' + day;

                        // Kirim data ke server
                        xhr.send("id_surat=<?php echo $id; ?>&keputusan=" + keputusan + "&tanggal_disposisi=" + tanggal_disposisi + "&catatan_disposisi=" + catatan_disposisi + "&diteruskan=" + JSON.stringify(diteruskan_values));
                    } else {
                        swal("Disposisi dibatalkan!", {
                            icon: "error",
                        });
                    }
                } else {
                    swal("Disposisi dibatalkan!", {
                        icon: "error"
                    });
                }
            });
        }

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>


    <!--disposisi untuk dekan FEB-->
<?php } elseif ($_SESSION['akses'] == 'DekanFEB') { ?>
    <?php
    $query = "SELECT  dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10, catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query);
    ?>
    <?php
    // Memeriksa apakah tombol "Selesai" diklik
    if (isset($_POST['selesai'])) {

        include 'koneksi.php';

        // Melakukan update status_selesai menjadi true di tabel tb_surat_dis
        $query_update = "UPDATE tb_surat_dis SET status_selesai = true WHERE id_surat = '$id'";
        mysqli_query($koneksi, $query_update);
        // Redirect atau tindakan lain setelah berhasil diperbarui
        header("Location: dashboard.php");
        exit(); // Pastikan untuk keluar setelah redirect
    }
    ?>
    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>

    <?php include 'riwayat_dispo.php'; ?>

    <div style="display: none;">
        <input type="radio" name="keputusan" value="Tindak Lanjuti" checked>
    </div>

    <div class="input-disposisi">
        <label for="">Catatan Disposisi*</label>
        <input type="text" id="catatan" class="input" name="catatan_disposisi" placeholder="Masukkan Catatan Disposisi" required>
    </div>
    <div class="input-disposisi">
        <label for="">Diteruskan kepada</label>
        <div class="radio">
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_akuntansi">
                <label for="">Prodi S1 Akuntansi & D3 Akuntansi</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_manajemen">
                <label for="">Prodi S1 manajemen & D3 Keuangan Perbankan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="ppik_kmhs">
                <label for="">PPIK dan Kemahasiswaan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="keuangan">
                <label for="">Bag. Keuangan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="akademik">
                <label for="">Bag. Akademik</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="umum">
                <label for="">Bag. Umum</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="kui_k">
                <label for="">KUI dan Kerjasama</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="marketing">
                <label for="">Bag. Marketing</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="upt_perpus">
                <label for="">UPT Perpus</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="sdm">
                <label for="">SDM</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="bpm">
                <label for="">BPM</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="lp3m">
                <label for="">LP3M</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="it_lab">
                <label for="">IT dan Lab</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="keuangan">
                <label for="">Keuangan</label>
            </div>
            <div>
                <input type="checkbox" name="diteruskan[]" value="prodi_keuangan_d3">
                <label for="">Prodi D3 Keuangan dan Perbankan</label>
            </div>
        </div>
    </div>
    <div class="input-disposisi">
        <label for="">Tanggal Disposisi<br> </label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>
    <div class="btn-kirim">
        <div class="floatFiller">ffff</div>
        <button type="button" onclick="kirimDisposisi()" style="cursor: pointer;">Disposisi</button>
        <button type="button" id="btnSelesai" style="cursor: pointer;">Selesai</button>
        <!-- <button type="button" onclick="batalDisposisi()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
    </div>

    <script>
        document.getElementById('btnSelesai').addEventListener('click', function() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menyelesaikan surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_selesai.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Dikonfirmasi Selesai!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&action=selesai");
                    } else {
                        swal("Dibatalkan", "Surat tidak diselesaikan", "info");
                    }
                });
        });

        function kirimDisposisi() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "check_disposisi.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === "allowed") {
                        // Lanjutkan dengan proses disposisi
                        proceedDisposisi();
                    } else {
                        swal("Gagal Disposisi!", "Anda sudah mendisposisi surat ini.", "error");
                    }
                }
            };
            xhr.send("id_surat=<?php echo $id; ?>");
        }

        function proceedDisposisi() {
            var diteruskan_checkboxes = document.querySelectorAll('input[name="diteruskan[]"]:checked');
            var tujuanMapping = {
                'prodi_akuntansi': 'Prodi S1 Akuntansi & D3 Akuntansi',
                'prodi_manajemen': 'Prodi S1 manajemen & D3 Keuangan Perbankan',
                'prodi_akuntansi_d3': 'Prodi D3 Akuntansi',
                'ppik_kmhs': 'PPIK dan Kemahasiswaan',
                'keuangan': 'Bag. Keuangan',
                'akademik': 'Bag. Akademik',
                'umum': 'Bag. Umum',
                'kui_k': 'KUI dan Kerjasama',
                'marketing': 'Bag. Marketing',
                'upt_perpus': 'UPT Perpus',
                'sdm': 'SDM',
                'bpm': 'BPM',
                'lp3m': 'LP3M',
                'it_lab': 'IT dan Lab',
                'keuangan': 'Keuangan',
                'prodi_keuangan_d3': 'Prodi D3 Keuangan dan Perbankan',
            };
            var tujuan = [];

            diteruskan_checkboxes.forEach(function(checkbox) {
                tujuan.push(tujuanMapping[checkbox.value]);
            });

            swal({
                title: "Anda yakin ingin mengirim disposisi ke " + tujuan.join(', ') + "?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willProceed) => {
                if (willProceed) {
                    if (willProceed) {
                        var keputusan = document.querySelector('input[name="keputusan"]:checked').value;

                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value; // Sesuaikan dengan name pada input

                        // Mendapatkan semua checkbox yang dipilih
                        var diteruskan_checkboxes = document.querySelectorAll('input[name="diteruskan[]"]:checked');
                        var diteruskan_values = [];

                        // Iterasi melalui setiap checkbox yang dipilih dan menambahkannya ke dalam array diteruskan_values
                        diteruskan_checkboxes.forEach(function(checkbox) {
                            diteruskan_values.push(checkbox.value);
                        });

                        // Buat objek XMLHttpRequest
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "update_disposisi.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                swal("Disposisi Berhasil!", {
                                        icon: "success",
                                        buttons: "OK"
                                    })
                                    .then(() => {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };

                        // Set tanggal disposisi
                        var today = new Date();
                        var year = today.getFullYear();
                        var month = today.getMonth() + 1; // January is 0
                        var day = today.getDate();
                        var tanggal_disposisi = year + '-' + month + '-' + day;

                        // Kirim data ke server
                        xhr.send("id_surat=<?php echo $id; ?>&keputusan=" + keputusan + "&tanggal_disposisi=" + tanggal_disposisi + "&catatan_disposisi=" + catatan_disposisi + "&diteruskan=" + JSON.stringify(diteruskan_values));
                    } else {
                        swal("Disposisi dibatalkan!", {
                            icon: "error",
                        });
                    }
                } else {
                    swal("Disposisi dibatalkan!", {
                        icon: "error"
                    });
                }
            });
        }

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>


    <!-- disposisi untuk direk Pasca Sarjana-->
<?php } elseif ($_SESSION['akses'] == 'direkPasca') { ?>
    <?php
    $query = "SELECT  dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10, catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query);
    ?>
    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>

    <?php include 'riwayat_dispo.php'; ?>

    <div class="input-disposisi">
        <div class="radio" style="display: none;">
            <div>
                <input type="radio" name="keputusan" value="Tindak Lanjuti" checked>
                <label for="">Tindak Lanjuti</label>
            </div>
        </div>
    </div>

    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="input-disposisi">
        <label for="">Catatan Disposisi<span style="color: red;">*</span></label>
        <input type="text" id="catatan" class="input" name="catatan_disposisi" placeholder="Masukkan Catatan Disposisi" required>
    </div>
    <div class="input-disposisi">
        <label for="">Diteruskan kepada</label>
        <div class="radio">
            <div>
                <input type="radio" name="diteruskan" value="prodi_keuSyariah">
                <label for="">Prodi S2 Keuangan Syariah</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="keuangan">
                <label for="">Bag. Keuangan</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="akademik">
                <label for="">Bag. Akademik</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="umum">
                <label for="">Bag. Umum</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="kui_k">
                <label for="">KUI dan Kerjasama</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="marketing">
                <label for="">Bag. Marketing</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="upt_perpus">
                <label for="">UPT Perpus</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="sdm">
                <label for="">SDM</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="bpm">
                <label for="">BPM</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="lp3m">
                <label for="">LP3M</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="it_lab">
                <label for="">IT dan Lab</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="ppik_kmhs">
                <label for="">PPIK dan Kemahasiswaan</label>
            </div>
        </div>
    </div>
    <div class="input-disposisi">
        <label for="">Tanggal Disposisi<br> </label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>
    <div class="btn-kirim">
        <div class="floatFiller">ffff</div>
        <button type="button" onclick="kirimDisposisi()" style="cursor: pointer;">Kirim</button>
    </div>

    <script>
        function kirimDisposisi() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "check_disposisi.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === "allowed") {
                        // Lanjutkan dengan proses disposisi
                        proceedDisposisi();
                    } else {
                        swal("Gagal Disposisi!", "Anda sudah mendisposisi surat ini.", "error");
                    }
                }
            };
            xhr.send("id_surat=<?php echo $id; ?>");
        }

        function proceedDisposisi() {
            var diteruskan = document.querySelector('input[name="diteruskan"]:checked').value;
            var tujuanMapping = {
                'prodi_keuSyariah': 'Prodi S2 Keuangan Syariah',
                'keuangan': 'Bag. Keuangan',
                'akademik': 'Bag. Akademik',
                'umum': 'Bag. Umum',
                'kui_k': 'KUI',
                'marketing': 'Bag. Marketing',
                'upt_perpus': 'UPT Perpus',
                'it_lab': 'IT dan Lab',
                'sdm': 'SDM',
                'bpm': 'BPM',
                'lp3m': 'LP3M',
                'it_lab': 'IT dan Lab',
                'keuangan': 'Keuangan',
                'ppik_kmhs': 'PPIK dan Kemahasiswaan',
            };
            var tujuan = tujuanMapping[diteruskan];
            swal({
                title: "Anda yakin ingin mengirim disposisi ke " + tujuan + "?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willProceed) => {
                if (willProceed) {
                    if (willProceed) {
                        var keputusan = document.querySelector('input[name="keputusan"]:checked').value;

                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value; // Sesuaikan dengan name pada input

                        var diteruskan = document.querySelector('input[name="diteruskan"]:checked').value;

                        // Buat objek XMLHttpRequest
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "update_disposisi.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                swal("Disposisi Berhasil!", {
                                        icon: "success",
                                        buttons: "OK"
                                    })
                                    .then(() => {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };

                        // Set tanggal disposisi
                        var today = new Date();
                        var year = today.getFullYear();
                        var month = today.getMonth() + 1; // January is 0
                        var day = today.getDate();
                        var tanggal_disposisi = year + '-' + month + '-' + day;

                        // Kirim data ke server
                        xhr.send("id_surat=<?php echo $id; ?>&keputusan=" + keputusan + "&tanggal_disposisi=" + tanggal_disposisi + "&catatan_disposisi=" + catatan_disposisi + "&diteruskan=" + diteruskan);
                    } else {
                        swal("Disposisi dibatalkan!", {
                            icon: "error",
                        });
                    }
                } else {
                    swal("Disposisi dibatalkan!", {
                        icon: "error"
                    });
                }
            });
        }

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>


    <!-- disposisi untuk unit-->
<?php } elseif (
    $_SESSION['akses'] == 'bpm' || $_SESSION['akses'] == 'umum' || $_SESSION['akses'] == 'it_lab' ||
    $_SESSION['akses'] == 'marketing' || $_SESSION['akses'] == 'kui_k' || $_SESSION['akses'] == 'akademik' ||
    $_SESSION['akses'] == 'ppik_kmhs' || $_SESSION['akses'] == 'lp3m' || $_SESSION['akses'] == 'pusat_bisnis' ||
    $_SESSION['akses'] == 'PKAD' || $_SESSION['akses'] == 'PSDOD'  || $_SESSION['akses'] == 'CHED' ||
    $_SESSION['akses'] == 'PSIPP' || $_SESSION['akses'] == 'halal_center'
) { ?>
    <?php
    $query = "SELECT dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10,
                    catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query);
    ?>
    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>

    <?php include 'riwayat_dispo.php'; ?>

    <div cl>
        <labeass="input-disposisi"l for="">Keputusan Unit*</label>
            <div class="radio">
                <div>
                    <input type="radio" name="option">
                    <label for="">Tindak Lanjuti</label>
                </div>
                <div>
                    <input type="radio" name="option">
                    <label for="">Dibicarakan dengan rektor</label>
                </div>
                <div>
                    <input type="radio" name="option">
                    <label for="">Pendapat dan masukkan</label>
                </div>
                <div>
                    <input type="radio" name="option">
                    <label for="">Dicek dan diteliti</label>
                </div>
            </div>
    </div>
    <div class="input-disposisi">
        <label for="">Catatan Penyelesaian <br>/ Penolakan <span style="color: red;"></span></label>
        <input type="text" id="catatan" class="input" name="catatan_disposisi" placeholder="Masukkan Penyelesaian / Penolakan">
    </div>
    <div class="input-disposisi">
        <label for="">Tanggal Disposisi<br> </label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>

    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="btn-kirim">
        <div class="floatFiller">ff</div>
        <button type="button" id="btnSelesai" style="cursor: pointer;">Selesai</button>
        <!-- <button type="button" onclick="batalDisposisi()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
    </div>


    <script>
        document.getElementById('btnSelesai').addEventListener('click', function() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menyelesaikan surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_selesai.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Dikonfirmasi Selesai!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=selesai");
                    } else {
                        swal("Dibatalkan", "Surat tidak diselesaikan", "info");
                    }
                });
        });

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>


    <!-- disposisi untuk prodi-->
<?php } elseif (
    $_SESSION['akses'] == 'prodi_ti' || $_SESSION['akses'] == 'prodi_si' || $_SESSION['akses'] == 'prodi_dkv'
    || $_SESSION['akses'] == 'prodi_arsitek' || $_SESSION['akses'] == 'prodi_manajemen' || $_SESSION['akses'] == 'prodi_akuntansi'
    || $_SESSION['akses'] == 'prodi_keuSyariah' || $_SESSION['akses'] == 'upt_perpus'
) { ?>

    <?php
    $query = "SELECT dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10,
                    catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query);
    ?>
    <?php
    // Memeriksa apakah tombol "Selesai" diklik
    if (isset($_POST['selesai'])) {

        include 'koneksi.php';

        // Melakukan update status_selesai menjadi true di tabel tb_surat_dis
        $query_update = "UPDATE tb_surat_dis SET status_selesai = true WHERE id_surat = '$id'";
        mysqli_query($koneksi, $query_update);
        // Redirect atau tindakan lain setelah berhasil diperbarui
        header("Location: dashboard.php");
        exit(); // Pastikan untuk keluar setelah redirect
    }
    ?>
    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>

    <?php include 'riwayat_dispo.php'; ?>

    <div class="input-disposisi" style="display: none;">
        <label for="">Keputusan Prodi*</label>
        <div class="radio" style="display: none;">
            <div>
                <input type="radio" name="keputusan" value="Tindak Lanjuti" checked>
                <label for=""></label>Tindak Lanjuti</label>
            </div>
        </div>
    </div>

    <div class="input-disposisi">
        <label for="">Catatan Penyelesaian / Disposisi *</label>
        <input type="text" id="catatan" class="input" name="catatan_disposisi" placeholder="Masukkan Penyelesaian / Penolakan">
    </div>

    <div class="input-disposisi">
        <label for="">Tanggal Disposisi<br> </label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>

    <div class="input-disposisi">
        <label for="">Diteruskan kepada</label>
        <div class="radio">
            <div>
                <input type="radio" name="diteruskan" value="keuangan">
                <label for="">Bag. Keuangan</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="akademik">
                <label for="">Bag. Akademik</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="umum">
                <label for="">Bag. Umum</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="kui_k">
                <label for="">KUI dan Kerjasama</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="marketing">
                <label for="">Bag. Marketing</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="upt_perpus">
                <label for="">UPT Perpus</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="sdm">
                <label for="">SDM</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="bpm">
                <label for="">BPM</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="lp3m">
                <label for="">LP3M</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="it_lab">
                <label for="">IT dan Lab</label>
            </div>
            <div>
                <input type="radio" name="diteruskan" value="ppik_kmhs">
                <label for="">PPIK dan Kemahasiswaan</label>
            </div>
        </div>
    </div>


    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="btn-kirim">
        <div class="floatFiller">ff</div>
        <button type="button" onclick="kirimDisposisi()" style="cursor: pointer;">Disposisi</button>
        <button type="button" id="btnSelesai" style="cursor: pointer;">Selesai</button>
        <!--  <button type="button" onclick="batalDisposisi()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
    </div>


    <script>
        document.getElementById('btnSelesai').addEventListener('click', function() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menyelesaikan surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_selesai.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Dikonfirmasi Selesai!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=selesai");
                    } else {
                        swal("Dibatalkan", "Surat tidak diselesaikan", "info");
                    }
                });
        });


        function kirimDisposisi() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "check_disposisi.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === "allowed") {
                        // Lanjutkan dengan proses disposisi
                        proceedDisposisi();
                    } else {
                        swal("Gagal Disposisi!", "Anda sudah mendisposisi surat ini.", "error");
                    }
                }
            };
            xhr.send("id_surat=<?php echo $id; ?>");
        }

        function proceedDisposisi() {
            var diteruskan = document.querySelector('input[name="diteruskan"]:checked').value;
            var keputusan = document.querySelector('input[name="keputusan"]:checked').value;
            var tujuanMapping = {
                'keuangan': 'Bag. Keuangan',
                'akademik': 'Bag. Akademik',
                'umum': 'Bag. Umum',
                'kui_k': 'KUI dan Kerjasama',
                'marketing': 'Bag. Marketing',
                'upt_perpus': 'UPT Perpus',
                'sdm': 'SDM',
                'bpm': 'BPM',
                'lp3m': 'LP3M',
                'it_lab': 'IT dan Lab',
                'ppik_kmhs': 'PPIK dan Kemahasiswaan',
            };
            var tujuan = tujuanMapping[diteruskan];

            swal({
                title: "Anda yakin ingin mengirim disposisi ke " + tujuan + "?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willProceed) => {
                if (willProceed) {
                    if (willProceed) {
                        var keputusan = document.querySelector('input[name="keputusan"]:checked').value;
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var diteruskan = document.querySelector('input[name="diteruskan"]:checked').value;
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "update_disposisi.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                swal("Disposisi Berhasil!", {
                                        icon: "success",
                                        buttons: "OK"
                                    })
                                    .then(() => {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };

                        // Set tanggal disposisi
                        var today = new Date();
                        var year = today.getFullYear();
                        var month = today.getMonth() + 1; // January is 0
                        var day = today.getDate();
                        var tanggal_disposisi = year + '-' + month + '-' + day;

                        // Kirim data ke server
                        xhr.send("id_surat=<?php echo $id; ?>&keputusan=" + keputusan + "&tanggal_disposisi=" + tanggal_disposisi + "&catatan_disposisi=" + catatan_disposisi + "&diteruskan=" + diteruskan);
                    } else {
                        swal("Disposisi dibatalkan!", {
                            icon: "error",
                        });
                    }
                } else {
                    swal("Disposisi dibatalkan!", {
                        icon: "error"
                    });
                }
            });
        }

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>

    <!-- disposisi untuk SDM-->
<?php } elseif ($_SESSION['akses'] == 'sdm') { ?>
    <?php
    $query = "SELECT dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10,
                    catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query);
    ?>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>

    <?php include 'riwayat_dispo.php'; ?>

    <div class="input-disposisi">
        <label for="">Catatan Penyelesaian <br>/ Penolakan <span style="color: red;"></span></label>
        <input type="text" id="catatan" class="input" name="catatan_disposisi" placeholder="Masukkan Penyelesaian / Penolakan">
    </div>

    <div class="input-disposisi">
        <label for="">Upload File</label>
        <input type="file" name="file_sdm" accept=".pdf">
    </div>

    <div class="input-disposisi">
        <label for="">Tanggal Disposisi<br> </label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>

    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="btn-kirim">
        <div class="floatFiller">ff</div>
        <button type="button" id="btnSelesai" style="cursor: pointer;">Selesai</button>
    </div>


    <script>
        document.getElementById('btnSelesai').addEventListener('click', function() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menyelesaikan surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        // Show loading overlay
                        document.getElementById('loadingOverlay').style.display = 'flex';

                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var file_sdm = document.querySelector('input[name="file_sdm"]').files[0];
                        var id = "<?php echo $id; ?>";

                        // Create FormData object and append data
                        var formData = new FormData();
                        formData.append("id", id);
                        formData.append("catatan_disposisi", catatan_disposisi);
                        formData.append("asalsurat", asalsurat);
                        formData.append("action", "selesai");
                        formData.append("file_sdm", file_sdm);

                        // Create an XMLHttpRequest to send the FormData
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', 'update_selesai.php', true);
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4) {
                                // Hide loading overlay
                                document.getElementById('loadingOverlay').style.display = 'none';

                                if (xhr.status == 200) {
                                    console.log(xhr.responseText);
                                    swal("Berhasil!", "Surat Telah Dikonfirmasi Selesai!", "success")
                                        .then(function() {
                                            window.location.href = "dashboard.php";
                                        });
                                } else {
                                    swal("Error", "Terjadi kesalahan saat memproses surat.", "error");
                                }
                            }
                        };
                        xhr.send(formData); // Send the FormData object
                    } else {
                        swal("Dibatalkan", "Surat tidak diselesaikan", "info");
                    }
                });
        });

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>

    <!-- Disposisi untuk keuangan -->
<?php } elseif ($_SESSION['akses'] == 'keuangan') { ?>
    <?php
    $query = "SELECT  dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10, catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query);
    ?>
    <?php
    // Memeriksa apakah tombol "Selesai" diklik
    if (isset($_POST['selesai'])) {

        include 'koneksi.php';

        // Melakukan update status_selesai menjadi true di tabel tb_surat_dis
        $query_update = "UPDATE tb_surat_dis SET status_selesai = true WHERE id_surat = '$id'";
        mysqli_query($koneksi, $query_update);
        // Redirect atau tindakan lain setelah berhasil diperbarui
        header("Location: dashboard.php");
        exit(); // Pastikan untuk keluar setelah redirect
    }
    ?>

    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>

    <?php include 'riwayat_dispo.php'; ?>

    <div class="input-disposisi">
        <label for="">Catatan Surat*</label>
        <input type="text" id="catatan" class="input" name="catatan_disposisi" placeholder="Masukkan catatan Penyelesaian / Penolakan">
    </div>

    <div class="input-disposisi">
        <label for="">Tanggal Disposisi<br> </label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>

    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="btn-kirim">
        <div class="floatFiller">ff</div>
        <button type="button" id="btnSelesai" style="cursor: pointer;">Selesai</button>
        <!-- <button type="button" onclick="batalDisposisi()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
    </div>


    <script>
        document.getElementById('btnSelesai').addEventListener('click', function() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menyelesaikan surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_selesai.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Dikonfirmasi Selesai!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=selesai");
                    } else {
                        swal("Dibatalkan", "Surat tidak diselesaikan", "info");
                    }
                });
        });

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>


    <!--disposisi untuk humas-->
<?php } elseif ($_SESSION['akses'] == 'Humas') { ?>
    <?php
    // Memeriksa apakah tombol "Selesai" diklik
    if (isset($_POST['selesai'])) {

        include 'koneksi.php';

        // Melakukan update status_selesai menjadi true di tabel tb_surat_dis
        $query_update = "UPDATE tb_surat_dis SET status_selesai = true WHERE id_surat = '$id'";
        mysqli_query($koneksi, $query_update);
        // Redirect atau tindakan lain setelah berhasil diperbarui
        header("Location: dashboard.php");
        exit(); // Pastikan untuk keluar setelah redirect
    }

    // Mendapatkan jenis surat dari tb_surat_dis
    include 'koneksi.php';
    $query_jenis_surat = "SELECT jenis_surat FROM tb_surat_dis WHERE id_surat = '$id'";
    $result = mysqli_query($koneksi, $query_jenis_surat);
    $row = mysqli_fetch_assoc($result);
    $jenis_surat = $row['jenis_surat'];
    ?>

    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
    <!-- surat non dispo -->
    <?php if ($jenis_surat == 3 || $jenis_surat == 4) : ?>
        <div class="input-disposisi">
            <label for="">Kode Surat*</label>
            <input type="text" class="input" id="kd_surat" name="kd_surat" placeholder="Masukkan kode surat">
        </div>

        <div class="input-disposisi">
            <label for="">Catatan Penyelesaian <br>/ Penolakan <span style="color: red;"></span></label>
            <input type="text" id="catatan" class="input" name="catatan_disposisi" placeholder="Masukkan Penyelesaian / Penolakan" required>
        </div>

        <div class="input-disposisi">
            <label for="">Tanggal Disposisi<br> </label>
            <div class="tgl">
                <span id="tanggalwaktu"></span>
            </div>
        </div>

        <!-- <span style="color: red; font-size: 14px;">*Apabila ingin menolak surat ini, mohon kosongkan form kode surat</span> -->
        <br> <br>
        <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

        <div class="btn-kirim">
            <div class="floatFiller">ffff</div>
            <button type="button" id="btnSelesai" style="cursor: pointer;">Selesai</button>
            <!-- <button type="button" onclick="batalDisposisi()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
        </div>
    <?php elseif ($jenis_surat == 1 || $jenis_surat == 2) : ?>

        <!-- surat dispo -->
        <?php
        $query = "SELECT dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10,
                    catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
                    keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
                    FROM tb_disposisi WHERE id_surat = '$id'";
        $result = mysqli_query($koneksi, $query);
        ?>

        <?php include 'riwayat_dispo.php'; ?>

        <div class="input-disposisi">
            <label for="">Catatan Penyelesaian / Penolakan *</label>
            <input type="text" id="catatan" class="input" name="catatan_disposisi" placeholder="Masukkan Penyelesaian / Penolakan">
        </div>

        <div class="input-disposisi">
            <label for="">Tanggal Disposisi<br> </label>
            <div class="tgl">
                <span id="tanggalwaktu"></span>
            </div>
        </div>

        <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">
        <!-- tombol belum berfungsi-->
        <div class="btn-kirim">
            <div class="floatFiller">ff</div>
            <button type="button" id="btnSelesaidispo" style="cursor: pointer;">Selesai</button>
            <!-- <button type="button" onclick="batalDisposisidispo()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
        </div>

    <?php endif; ?>


    <script>
        //untuk surat non disposisi humas 
        document.getElementById('btnSelesai').addEventListener('click', function() {
            // Tampilkan konfirmasi sebelum menampilkan Sweet Alert
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menyelesaikan surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        document.getElementById('loadingOverlay').style.display = 'flex';

                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var kd_surat = document.querySelector('input[name="kd_surat"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>"; // Mendapatkan nilai $id dari PHP
                        xhr.open('POST', 'update_selesai_non.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Dikonfirmasi Selesai!", "success")
                                    .then(function() {
                                        // Redirect ke halaman dashboard setelah menutup notifikasi
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&kd_surat=" + encodeURIComponent(kd_surat) + "&action=selesai");
                    } else {
                        swal("Dibatalkan", "Surat tidak diselesaikan", "info");
                    }
                });
        });

        function batalDisposisi() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_tolak.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>
    <script>
        //untuk surat disposisi humas
        document.getElementById('btnSelesaidispo').addEventListener('click', function() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menyelesaikan surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_selesai.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Dikonfirmasi Selesai!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=selesai");
                    } else {
                        swal("Dibatalkan", "Surat tidak diselesaikan", "info");
                    }
                });
        });

        function batalDisposisidispo() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menolak surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_selesai.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Ditolak!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=tolak");
                    } else {
                        swal("Dibatalkan", "Surat tidak ditolak", "info");
                    }
                });
        }
    </script>

    <!-- disposisi untuk sekertaris -->
<?php } elseif ($_SESSION['akses'] == 'sekretaris') { ?>
    <div class="txt-disposisi">
        <h3>Disposisi</h3>
    </div>
    <?php include 'riwayat_dispo.php'; ?>

    <div class="input-disposisi">
        <label for="">Catatan Penyelesaian <br>/ Penolakan <span style="color: red;"></span></label>
        <input type="text" id="catatan" class="input" name="catatan_disposisi" placeholder="Masukkan Penyelesaian / Penolakan">
    </div>
    <div class="input-disposisi">
        <label for="">Tanggal Disposisi<br> </label>
        <div class="tgl">
            <span id="tanggalwaktu"></span>
        </div>
    </div>

    <input type="text" name="executor" value="<?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : ''; ?>" style="display: none;">

    <div class="btn-kirim">
        <div class="floatFiller">ff</div>
        <button type="button" id="btnSelesai" style="cursor: pointer;">Selesai</button>
        <!-- <button type="button" onclick="batalDisposisi()" style="cursor: pointer; background-color: #871F1E; margin-right: 120px; ">Tolak</button> -->
    </div>


    <script>
        document.getElementById('btnSelesai').addEventListener('click', function() {
            swal({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menyelesaikan surat ini?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willProceed) => {
                    if (willProceed) {
                        var catatan_disposisi = document.querySelector('input[name="catatan_disposisi"]').value;
                        var asalsurat = document.querySelector('input[name="executor"]').value;
                        var xhr = new XMLHttpRequest();
                        var id = "<?php echo $id; ?>";
                        xhr.open('POST', 'update_selesai.php', true);
                        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                console.log(xhr.responseText);
                                swal("Berhasil!", "Surat Telah Dikonfirmasi Selesai!", "success")
                                    .then(function() {
                                        window.location.href = "dashboard.php";
                                    });
                            }
                        };
                        xhr.send("id=" + id + "&catatan_disposisi=" + encodeURIComponent(catatan_disposisi) + "&asalsurat=" + asalsurat + "&action=selesai");
                    } else {
                        swal("Dibatalkan", "Surat tidak diselesaikan", "info");
                    }
                });
        });
    </script>
<?php }
