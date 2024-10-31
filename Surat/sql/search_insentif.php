<?php
// koneksi ke database
$host = 'localhost';
$dbname = 'db_teknoid';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['jenis_insentif']) && isset($_POST['tanggal_awal']) && isset($_POST['tanggal_akhir'])) {
        $jenis_insentif = $_POST['jenis_insentif'];
        $tanggal_awal = $_POST['tanggal_awal'];
        $tanggal_akhir = $_POST['tanggal_akhir'];

        $stmt = $conn->prepare("SELECT * FROM tb_srt_dosen WHERE jenis_insentif = :jenis_insentif AND tanggal_surat BETWEEN :tanggal_awal AND :tanggal_akhir");
        $stmt->bindParam(':jenis_insentif', $jenis_insentif);
        $stmt->bindParam(':tanggal_awal', $tanggal_awal);
        $stmt->bindParam(':tanggal_akhir', $tanggal_akhir);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            echo "<div class='tableOverflow'>";
            echo "<table border='1' cellpadding='5' cellspacing='0' id='tablerekap'>";
            echo "<thead>";
            echo "<tr'>";
            echo "<th style='border: solid lightgrey 1px;'>No</th>";
            echo "<th style='border: solid lightgrey 1px;'>Jenis Surat</th>";
            echo "<th style='border: solid lightgrey 1px;'>Asal Surat</th>";
            echo "<th style='border: solid lightgrey 1px;'>Jenis Insentif</th>";
            echo "<th style='border: solid lightgrey 1px;'>Tanggal Surat</th>";

            if ($jenis_insentif == 'publikasi') {
                echo "<th style='border: solid lightgrey 1px;'>Jenis Publikasi/Jurnal</th>";
                echo "<th style='border: solid lightgrey 1px;'>Judul Publikasi</th>";
                echo "<th style='border: solid lightgrey 1px;'>Nama Jurnal/Koran/Majalah/Penerbit</th>";
                echo "<th style='border: solid lightgrey 1px;'>Vol. No. Tahun. ISSN-Edisi-Halaman</th>";
                echo "<th style='border: solid lightgrey 1px;'>Link Jurnal</th>";
            } elseif ($jenis_insentif == 'pertemuan_ilmiah') {
                echo "<th style='border: solid lightgrey 1px;'>Skala</th>";
                echo "<th style='border: solid lightgrey 1px;'>Nama Pertemuan Ilmiah</th>";
                echo "<th style='border: solid lightgrey 1px;'>Usulan Biaya</th>";
            } elseif ($jenis_insentif == 'keynote_speaker') {
                echo "<th style='border: solid lightgrey 1px;'>Skala</th>";
                echo "<th style='border: solid lightgrey 1px;'>Nama Pertemuan Ilmiah</th>";
            } elseif ($jenis_insentif == 'visiting') {
                echo "<th style='border: solid lightgrey 1px;'>Nama Kegiatan dan Lembaga Tujuan</th>";
                echo "<th style='border: solid lightgrey 1px;'>Waktu Pelaksanaan</th>";
            } elseif ($jenis_insentif == 'hki') {
                echo "<th style='border: solid lightgrey 1px;'>Jenis Kekayaan Intelektual</th>";
                echo "<th style='border: solid lightgrey 1px;'>Judul Kekayaan Intelektual</th>";
            } elseif ($jenis_insentif == 'teknologi') {
                echo "<th style='border: solid lightgrey 1px;'>Teknologi Yang Diusulkan</th>";
                echo "<th style='border: solid lightgrey 1px;'>Deskripsi Teknologi</th>";
            } elseif ($jenis_insentif == 'buku') {
                echo "<th style='border: solid lightgrey 1px;'>Jenis Buku</th>";
                echo "<th style='border: solid lightgrey 1px;'>Judul Buku</th>";
                echo "<th style='border: solid lightgrey 1px;'>Sinopsis</th>";
                echo "<th style='border: solid lightgrey 1px;'>ISBN/Jumlah Halaman/Penerbit</th>";
            } elseif ($jenis_insentif == 'model') {
                echo "<th style='border: solid lightgrey 1px;'>Nama Model, Prototype, Desain, Karya Seni, Rekayasa Sosial, Kebijakan yang Diusulkan</th>";
                echo "<th style='border: solid lightgrey 1px;'>Deskripsikan Model, Prototype, Desain, Karya Seni, Rekayasa Sosial, Kebijakan yang Diusulkan</th>";
            } elseif ($jenis_insentif == 'insentif_publikasi') {
                echo "<th style='border: solid lightgrey 1px;'>Judul Publikasi</th>";
                echo "<th style='border: solid lightgrey 1px;'>Nama Penerbit dan Waktu Terbit</th>";
                echo "<th style='border: solid lightgrey 1px;'>Tautan Publikasi Berita (jika online)</th>";
            } else {
                echo "<th style='border: solid lightgrey 1px;'>Skema</th>";
                echo "<th style='border: solid lightgrey 1px;'>Judul Penelitian/Pengabdian Masyarakat</th>";
            }

            echo "<th style='border: solid lightgrey 1px;'>Aksi</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            $no = 1;
            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . ($row['jenis_surat'] == 5 ? "Surat Insentif" : htmlspecialchars($row['jenis_surat'])) . "</td>";
                echo "<td>" . htmlspecialchars($row['asal_surat']) . "</td>";
                echo "<td>" . htmlspecialchars($row['jenis_insentif']) . "</td>";
                $tanggal_surat = new DateTime($row['tanggal_surat']);
                echo "<td>" . $tanggal_surat->format('d-m-Y') . "</td>";

                // Correctly formatted if-else structure for additional columns
                if ($jenis_insentif == 'publikasi') {
                    echo "<td>" . htmlspecialchars($row['jenis_publikasi_pi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['judul_publikasi_pi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_jurnal_pi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['vol_notahun_pi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['link_jurnal_pi']) . "</td>";
                } elseif ($jenis_insentif == 'pertemuan_ilmiah') {
                    echo "<td>" . htmlspecialchars($row['skala_ppdpi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_pertemuan_ppdpi']) . "</td>";
                    echo "<td>" . "Rp. " . htmlspecialchars($row['usulan_biaya_ppdpi']) . "</td>";
                } elseif ($jenis_insentif == 'keynote_speaker') {
                    echo "<td>" . htmlspecialchars($row['skala_ppdks']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_pertemuan_ppdks']) . "</td>";
                } elseif ($jenis_insentif == 'visiting') {
                    echo "<td>" . htmlspecialchars($row['nm_kegiatan_vl']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['waktu_pelaksanaan_vl']) . "</td>";
                } elseif ($jenis_insentif == 'hki') {
                    echo "<td>" . htmlspecialchars($row['jenis_hki']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['judul_hki']) . "</td>";
                } elseif ($jenis_insentif == 'teknologi') {
                    echo "<td>" . htmlspecialchars($row['teknologi_tg']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['deskripsi_tg']) . "</td>";
                } elseif ($jenis_insentif == 'buku') {
                    echo "<td>" . htmlspecialchars($row['jenis_buku']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['sinopsis_buku']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['isbn_buku']) . "</td>";
                } elseif ($jenis_insentif == 'model') {
                    echo "<td>" . htmlspecialchars($row['nama_model_mpdks']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['deskripsi_mpdks']) . "</td>";
                } elseif ($jenis_insentif == 'insentif_publikasi') {
                    echo "<td>" . htmlspecialchars($row['judul_ipbk']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['namaPenerbit_dan_waktu_ipbk']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['link_publikasi_ipbk']) . "</td>";
                } else {
                    echo "<td>" . htmlspecialchars($row['skema_ppmdpek']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['judul_penelitian_ppm']) . "</td>";
                }

                echo "<td><a href='aksi.php?aksi=delete&id=" . $row['id_srt'] . "'>Delete</a></td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";
        } else {
            echo "Tidak ada data";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>