<?php
session_start(); // Start the session before accessing $_SESSION variables
include '../koneksi.php';

$output = '';

if (isset($_POST['query'])) {
    $search = $_POST['query'];
    $search = '%' . $search . '%';
    $stmt = $koneksi->prepare("
        SELECT * FROM tb_srt_dosen 
        WHERE (asal_surat LIKE ?
        OR NIDN LIKE ?   
        OR DATE_FORMAT(tanggal_surat, '%d-%m-%Y') LIKE ? 
        OR perihal_srd LIKE ?)
        AND jenis_surat = 6
    ");
    $stmt->bind_param("ssss", $search, $search, $search, $search);
    $stmt->execute();
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $counter = 1;
    $output .= '<table id="tablesm" class="tablesorter">';
    $output .= '<thead style="position: sticky; top: 0;">
                    <tr>
                        <th style="min-width: 75px;">No <i class="fas fa-sort"></i></th>
                        <th>Asal Surat <i class="fas fa-sort"></i></th>
                        <th>NIDN <i class="fas fa-sort"></i></th>
                        <th>Tanggal Surat <i class="fas fa-sort"></i></th>
                        <th>Perihal <i class="fas fa-sort"></i></th>                        
                        <th>Aksi</th>
                    </tr>
                </thead>';
    $output .= '<tbody>';
    while ($row = $result->fetch_assoc()) {
        $tanggalSuratFormatted = date('d-m-Y', strtotime($row['tanggal_surat']));

        $output .= '<tr>';
        $output .= '<td style="min-width: 75px;">' . htmlspecialchars($counter++) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['asal_surat']) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['NIDN']) . '</td>';
        $output .= '<td>' . htmlspecialchars($tanggalSuratFormatted) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['perihal_srd']) . '</td>';
        $output .= '<td><a href="dispo_dosen.php?id=' . $row['id_srt'] . '"><button style="background-color: white; color: #1b5ebe;"> Proses </button></a></td>';
        $output .= '</tr>';
    }
    $output .= '</tbody>';

    $output .= '</table>';
    echo $output;
} else {
    echo "<table><tr><td colspan='7'>0 results</td></tr></table>";
}
