<?php
session_start();
include '../koneksi.php';

$output = '';
if (isset($_POST['query'])) {
    $search = $_POST['query'];
    $fullname = $_SESSION['nama_lengkap'];
    // Query untuk mencari surat keluar dengan asal surat sesuai dengan session nama_lengkap pengguna
    $query = "SELECT *
              FROM tb_srt_honor
              WHERE asal_surat = '$fullname' AND 
              (nm_kegiatan LIKE '%$search%')";
}

$result = mysqli_query($koneksi, $query);
if (mysqli_num_rows($result) > 0) {
    $counter = 1;
    $output .= '<table id="tablesk" class="tablesorter">';
    $output .= '<thead>
                    <tr>
                        <th style="min-width: 75px;">No<i class="fas fa-sort"></i></th>
                        <th>Jenis Surat <i class="fas fa-sort"></i></th>
                        <th>Asal Surat <i class="fas fa-sort"></i></th>
                        <th>Nama Kegiatan<i class="fas fa-sort"></i></th>
                        <th>Tanggal Surat</th>
                        <th>Status</th>
                        <th>Berkas</th>
                    </tr>
                </thead>';
    while ($row = mysqli_fetch_array($result)) {
        $alias = htmlspecialchars($row['asal_surat']); // Replace this with your alias logic if needed
        
        $output .= '<tbody>';
        $output .= '<tr>';
        $output .= '<td style="min-width: 75px;">' . $counter++ . '</td>';
        $output .= '<td>' . ($row['jenis_surat'] == 7 ? "Surat Honorium" : "Data Tidak Tersedia") . '</td>';
        $output .= '<td>' . $alias . '</td>';
        $output .= '<td>' . (!empty($row['nm_kegiatan']) ? ucwords($row['nm_kegiatan']) : '-') . '</td>';
        $output .= '<td>' . (isset($row['tanggal_surat']) ? (new DateTime($row['tanggal_surat']))->format('d-m-Y') : '') . '</td>';
        $output .= '<td>';
        if ($row['status'] == 1) {
            $output .= '<i class="fas fa-check-square" style="background-color: white; color: green;"></i> Terverifikasi';
        } else {
            $output .= ' Belum Diverifikasi';
        }
        $output .= '</td>';
        $output .= '<td><a href="dispo_dosen.php?id=' . $row['id'] . '"><i class="fas fa-eye" style="background-color: white; color: #1b5ebe;"></i></a></td>';
        $output .= '</tr>';
        $output .= '</tbody>';
    }
    $output .= '</table>';
    echo $output;
} else {
    echo "<tr><td colspan='7'>0 results</td></tr>";
}
?>
