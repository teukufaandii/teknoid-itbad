<?php
session_start(); // Start the session before accessing $_SESSION variables
include '../koneksi.php';

$output = '';

if (isset($_POST['query'])) {
    $search = $_POST['query'];
    // Add % placeholders for search term to allow partial matching
    $search = '%' . $search . '%';
    $stmt = $koneksi->prepare("SELECT * FROM tb_srt_dosen WHERE tujuan_surat_srd LIKE ? OR asal_surat LIKE ? OR id_sinta LIKE ? OR tanggal_surat LIKE ?");
    $stmt->bind_param("ssss", $search, $search, $search, $search);
} else {
    // In case there's no search query, retrieve all records
    $stmt = $koneksi->prepare("SELECT * FROM tb_srt_dosen");
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
        // Here, assign some value to $status based on your criteria
        $status = "Pending"; // Example status; replace this with real logic

        $output .= '<tr>';
        $output .= '<td style="min-width: 75px;">' . htmlspecialchars($counter++) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['kode_surat']) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['asal_surat']) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['id_sinta']) . '</td>';
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
