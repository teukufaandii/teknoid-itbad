<?php
// Query untuk mendapatkan data disposisi
$query = "SELECT  dispo1, dispo2, dispo3, dispo4, dispo5, dispo6, dispo7, dispo8, dispo9, dispo10, catatan_disposisi, catatan_disposisi2, catatan_disposisi3, catatan_disposisi4, catatan_disposisi5, catatan_disposisi6, catatan_disposisi7, catatan_disposisi8, catatan_disposisi9, catatan_disposisi10,
keputusan_disposisi1, keputusan_disposisi2, keputusan_disposisi3, keputusan_disposisi4, keputusan_disposisi5, keputusan_disposisi6, keputusan_disposisi7, keputusan_disposisi8, keputusan_disposisi9, keputusan_disposisi10, diteruskan_ke
FROM tb_disposisi WHERE id_surat = '$id'";
$result = mysqli_query($koneksi, $query);
?>

<div class="input-disposisi">
    <label for="disposisi">Riwayat Disposisi</label>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <div class="info-disposisi">
            <?php
            for ($i = 1; $i <= 10; $i++) {
                $dispo = 'dispo' . $i;
                $keputusan = 'keputusan_disposisi' . $i;
                if ($i == 1) {
                    $catatan = 'catatan_disposisi'; 
                } else {
                    $catatan = 'catatan_disposisi' . $i;
                }

                if (!empty($row[$dispo]) || !empty($row[$catatan]) || !empty($row[$keputusan])) { ?>
                    <span>Disposisi <?php echo $i; ?> :</span>
                    <input type="text" id="disposisi<?php echo $i; ?>" name="disposisi<?php echo $i; ?>[]" value="<?php echo htmlspecialchars($row[$dispo]); ?>" readonly>
                    <span>Catatan :</span>
                    <input type="text" id="catatan<?php echo $i; ?>" name="catatan<?php echo $i; ?>[]" value="<?php echo htmlspecialchars($row[$catatan]); ?>" readonly>
                    <span>Keputusan :</span>
                    <input type="text" id="keputusan<?php echo $i; ?>" name="keputusan<?php echo $i; ?>[]" value="<?php echo htmlspecialchars($row[$keputusan]); ?>" readonly><br>
            <?php }
            } ?>
            <span>Posisi Surat Saat Ini :</span>
            <?php
            // Array pemetaan nilai database ke UI
            $map = [
                'prodi_keuSyariah' => 'Prodi S2 Keuangan Syariah',
                'prodi_akuntansi' => 'Prodi S1 Akuntansi & D3 Akuntansi',
                'prodi_manajemen' => 'Prodi S1 Manajemen & D3 Keuangan Perbankan',
                'prodi_si' => 'Prodi S1 Sistem Informasi',
                'prodi_ti' => 'Prodi S1 Teknologi Informasi',
                'prodi_dkv' => 'Prodi S1 Desain Komunikasi Visual',
                'prodi_arsitek' => 'Prodi S1 Arsitektur'
            ];

            if (is_string($row['diteruskan_ke']) && is_array(json_decode($row['diteruskan_ke'], true))) {
                $decoded_array = json_decode($row['diteruskan_ke'], true);
                $diteruskan_ke_value = [];
                foreach ($decoded_array as $value) {
                    // Ganti nilai sesuai pemetaan
                    if (isset($map[$value])) {
                        $diteruskan_ke_value[] = $map[$value];
                    } else {
                        $diteruskan_ke_value[] = ucfirst(str_replace("_", " ", $value));
                    }
                }
                $diteruskan_ke_value = implode(", ", $diteruskan_ke_value);
            } elseif (is_array($row['diteruskan_ke'])) {
                $diteruskan_ke_value = [];
                foreach ($row['diteruskan_ke'] as $value) {
                    // Ganti nilai sesuai pemetaan
                    if (isset($map[$value])) {
                        $diteruskan_ke_value[] = $map[$value];
                    } else {
                        $diteruskan_ke_value[] = ucfirst(str_replace("_", " ", $value));
                    }
                }
                $diteruskan_ke_value = implode(", ", $diteruskan_ke_value);
            } else {
                $diteruskan_ke_value = isset($map[$row['diteruskan_ke']]) ? $map[$row['diteruskan_ke']] : $row['diteruskan_ke'];
            }

            ?>
            <input type="text" id="diteruskan_ke" name="diteruskan_ke" style="margin-top: 10px;" value="<?php echo htmlspecialchars($diteruskan_ke_value); ?>" readonly><br>
        </div>
    <?php } ?>
</div>