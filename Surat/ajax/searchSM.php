<?php
session_start(); // Start the session before accessing $_SESSION variables
include '../koneksi.php';

$output = '';

if (isset($_POST['query'])) {
    $search = $_POST['query'];
    // Add % placeholders for search term to allow partial matching
    $search = '%' . $search . '%';
    $stmt = $koneksi->prepare("SELECT * FROM tb_surat_dis WHERE kode_surat LIKE ? OR asal_surat LIKE ? OR perihal LIKE ? OR tanggal_surat LIKE ?");
    $stmt->bind_param("ssss", $search, $search, $search, $search);
} else {
    $stmt = $koneksi->prepare("SELECT * FROM tb_surat_dis");
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $counter = 1;
    $output .= '<table id="tablesm" class="tablesorter">';
    $output .= '<thead style="position: sticky; top: 0;">
                    <tr>
                        <th style="min-width: 75px;">No <i class="fas fa-sort"></i></th>
                        <th>Kode Surat <i class="fas fa-sort"></i></th>
                        <th>Asal Surat <i class="fas fa-sort"></i></th>
                        <th>Perihal <i class="fas fa-sort"></i></th>
                        <th>Tanggal Surat <i class="fas fa-sort"></i></th>
                        <th>Status <i class="fas fa-sort"></i></th>
                        <th>Aksi</th>
                    </tr>
                </thead>';
    $output .= '<tbody>';
    while ($row = $result->fetch_assoc()) {
        $status = 'Belum Disposisi';
        if ($row['status_baca']) {
            $status = 'Sudah Disposisi';
        }
        if ($row['status_tolak'] == 1) {
            $status .= ' - Ditolak';
        }
        if ($row['status_selesai'] == 1) {
            $status .= ' - Selesai';
        }

        $output .= '<tr>';
        $output .= '<td style="min-width: 75px;">' . htmlspecialchars($counter++) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['kode_surat']) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['asal_surat']) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['perihal']) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['tanggal_surat']) . '</td>';
        $output .= '<td>' . htmlspecialchars($status) . '</td>';
        $output .= '<td><a href="disposisi.php?id=' . urlencode($row['id_surat']) . '">
            <button style="padding: 5px; border-radius: 5px; background-color: #1E2287; color: white;">Disposisi</button></a>
        </td>';
        $output .= '</tr>';
    }
    $output .= '</tbody>';
    $output .= '</table>';
    echo $output;
} else {
    echo "<table><tr><td colspan='7'>0 results</td></tr></table>";
}
?>
