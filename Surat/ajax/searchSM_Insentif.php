<?php
session_start(); // Start the session before accessing $_SESSION variables
include '../koneksi.php';

$output = '';

if (isset($_POST['query'])) {
    $search = $_POST['query'];
    // Add % placeholders for search term to allow partial matching
    $search = '%' . $search . '%';
    $stmt = $koneksi->prepare("
        SELECT * FROM tb_srt_dosen 
        WHERE tujuan_surat_srd LIKE ? 
        OR asal_surat LIKE ? 
        OR id_sinta LIKE ? 
        OR DATE_FORMAT(tanggal_surat, '%d-%m-%Y') LIKE ? 
        OR judul_penelitian_ppm LIKE ?
        OR judul_publikasi_pi LIKE ?
        OR judul_hki LIKE ?
        OR judul_buku LIKE ?
        OR judul_ipbk LIKE ?
    ");
    $stmt->bind_param("sssssssss", $search, $search, $search, $search, $search, $search, $search, $search, $search);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $counter = 1;
    $output .= '<table id="tablesm" class="tablesorter">';
    $output .= '<thead style="position: sticky; top: 0;">
                    <tr>
                        <th style="min-width: 75px;">No <i class="fas fa-sort"></i></th>
                        <th>Judul <i class="fas fa-sort"></i></th>
                        <th>Asal Surat <i class="fas fa-sort"></i></th>
                        <th>ID Sinta <i class="fas fa-sort"></i></th>
                        <th>Tanggal Surat <i class="fas fa-sort"></i></th>
                        <th>Status <i class="fas fa-sort"></i></th>
                        <th>Aksi</th>
                    </tr>
                </thead>';
    $output .= '<tbody>';
    while ($row = $result->fetch_assoc()) {
        $tanggalSuratFormatted = date('d-m-Y', strtotime($row['tanggal_surat']));

        $output .= '<tr>';
        $output .= '<td style="min-width: 75px;">' . htmlspecialchars($counter++) . '</td>';
        $output .= '<td>' . htmlspecialchars(!empty($row['judul_penelitian_ppm']) ? $row['judul_penelitian_ppm'] : (!empty($row['judul_publikasi_pi']) ? $row['judul_publikasi_pi'] : (!empty($row['judul_hki']) ? $row['judul_hki'] : (!empty($row['judul_buku']) ? $row['judul_buku'] : (!empty($row['judul_ipbk']) ? $row['judul_ipbk'] : 'Data Tidak Tersedia'))))) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['asal_surat']) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['id_sinta']) . '</td>';
        $output .= '<td>' . htmlspecialchars($tanggalSuratFormatted) . '</td>';
        // Verifikasi
        $output .= '<td>';
        if ($row['verifikasi_keuangan'] == 1) {
            $output .= '<i class="fa-solid fa-square-check" style="background-color: white; color: green;"></i> Terverifikasi - Keuangan';
        } elseif ($row['verifikasi'] == 1) {
            $output .= '<i class="fa-solid fa-square-check" style="background-color: white; color: green;"></i> Terverifikasi';
        } else {
            $output .= 'Belum Diverifikasi';
        }
        $output .= '</td>';

        // Tombol verifikasi proses keuangan
        $output .= '<td><div class="aksi-btn">';
        if ($row['verifikasi_keuangan'] == 1) {
            $output .= "<button style='cursor: not-allowed;' class='verify-button' data-id='" . htmlspecialchars($row['id_srt']) . "' disabled><i class='fa-solid fa-check'></i> Sudah Diverifikasi ke keuangan</button>";
        } else {
            $output .= "<button class='verify-button' data-id='" . htmlspecialchars($row['id_srt']) . "'>Proses ke keuangan</button>";
        }
        $output .= '</div></td>';

        $output .= '</tr>';
    }
    $output .= '</tbody>';

    $output .= '</table>';
    echo $output;
} else {
    echo "<table><tr><td colspan='7'>0 results</td></tr></table>";
}
