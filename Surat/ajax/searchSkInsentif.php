<?php
session_start();
include '../koneksi.php';

$output = '';
if (isset($_POST['query'])) {
    $search = $_POST['query'];
    $fullname = $_SESSION['nama_lengkap'];

    // Check if the search input is a date and convert it to the correct format
    $date_search = DateTime::createFromFormat('d-m-Y', $search);
    if ($date_search) {
        $formatted_search = $date_search->format('Y-m-d');
    } else {
        $formatted_search = $search;
    }

    // Query for matching surat keluar based on asal_surat and the search query
    $query = "SELECT *
              FROM tb_srt_dosen
              WHERE asal_surat = '$fullname' 
              AND (jenis_insentif LIKE '%$search%' 
              OR DATE_FORMAT(tanggal_surat, '%d-%m-%Y') LIKE '%$formatted_search%'
              OR DATE_FORMAT(tanggal_surat, '%d-%m') LIKE '%$formatted_search%'
              OR DATE_FORMAT(tanggal_surat, '%m-%Y') LIKE '%$formatted_search%'
              OR DATE_FORMAT(tanggal_surat, '%Y') LIKE '%$formatted_search%')";
}

$result = mysqli_query($koneksi, $query);
if (mysqli_num_rows($result) > 0) {
    $counter = 1;
    $output .= '<table id="tablesk" class="tablesorter">';
    $output .= '<thead>
                    <tr>
                        <th style="min-width: 75px;">No <i class="fas fa-sort"></i></th>
                        <th>Jenis Surat <i class="fas fa-sort"></i></th>
                        <th>Asal Surat <i class="fas fa-sort"></i></th>
                        <th>Jenis Insentif <i class="fas fa-sort"></i></th>
                        <th>Tanggal Surat <i class="fas fa-sort"></i></th>
                        <th>Status <i class="fas fa-sort"></i></th>
                        <th>Memo</th>
                        <th>Aksi</th>
                    </tr>
                </thead>';
    while ($row = mysqli_fetch_array($result)) {
        $output .= '<tbody>';
        $output .= '<tr>';
        $output .= '<td style="min-width: 75px;">' . $counter++ . '</td>';
        $output .= '<td>' . (isset($row['jenis_surat']) ? ($row['jenis_surat'] == 5 ? "Surat Insentif" : ($row['jenis_surat'] == 6 ? "Surat Riset Dosen" : "Jenis Surat Tidak Dikenali")) : "Data Tidak Tersedia") . '</td>';
        $output .= '<td>' . (isset($row['asal_surat']) ? $row['asal_surat'] : 'Data Tidak Tersedia') . '</td>';
        $output .= '<td>' . (!empty($row['jenis_insentif']) ? ucwords($row['jenis_insentif']) : '-') . '</td>';
        $output .= '<td>' . (isset($row['tanggal_surat']) ? (new DateTime($row['tanggal_surat']))->format('d-m-Y') : '') . '</td>';
        $output .= '<td>';
        if ($row['verifikasi'] == 1) {
            $output .= '<i class="fas fa-check-square" style="background-color: white; color: green;"></i> Terverifikasi';
        } else {
            $output .= ' Belum Diverifikasi';
        }
        $output .= '</td>';
        $output .= '<td>';
        if (isset($row['memo']) && !empty($row['memo'])) {
            $memo = htmlspecialchars($row['memo'], ENT_QUOTES, 'UTF-8');
            $output .= "<i class='fa fa-sticky-note' style='color: #ffc107; cursor: pointer;' onclick=\"showMemo('$memo');\"></i>";
        } else {
            $output .= '-';
        }
        $output .= '</td>';
        $output .= '<td><a href="dispo_dosen.php?id=' . $row['id_srt'] . '"><i class="fas fa-eye" style="background-color: white; color: #1b5ebe;"></i></a></td>';
        $output .= '</tr>';
        $output .= '</tbody>';
    }
    $output .= '</table>';
    echo $output;
} else {
    echo "<tr><td colspan='8'>0 results</td></tr>";
}
?>
