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
                    $catatan = 'catatan_disposisi'; // Catatan khusus untuk yang pertama
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
            if (is_string($row['diteruskan_ke']) && is_array(json_decode($row['diteruskan_ke'], true))) {
                $decoded_array = json_decode($row['diteruskan_ke'], true);
                $diteruskan_ke_value = implode(", ", $decoded_array);
            } elseif (is_array($row['diteruskan_ke'])) {
                $diteruskan_ke_value = implode(", ", $row['diteruskan_ke']);
            } else {
                $diteruskan_ke_value = $row['diteruskan_ke'];
            }

            $diteruskan_ke_value = str_replace("_", " ", $diteruskan_ke_value);
            $diteruskan_ke_value = ucwords($diteruskan_ke_value);
            ?>
            <input type="text" id="diteruskan_ke" name="diteruskan_ke" style="margin-top: 10px;" value="<?php echo htmlspecialchars($diteruskan_ke_value); ?>" readonly><br>
        </div>
    <?php } ?>
</div>