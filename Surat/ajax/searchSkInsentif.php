<?php
session_start();
include '../koneksi.php';

$output = '';
if(isset($_POST['query'])) {
    $search = $_POST['query'];
    $fullname = $_SESSION['nama_lengkap'];
    // Query untuk mencari surat keluar dengan asal surat sesuai dengan session nama_lengkap pengguna
    $query = "SELECT *
              FROM tb_srt_dosen
              WHERE asal_surat = '$fullname' AND 
              (jenis_insentif LIKE '%$search%')";
} else {
}

$result = mysqli_query($koneksi, $query);
if(mysqli_num_rows($result) > 0) {
    $counter = 1;
    $output .= '<table id="tablesk" class="tablesorter">';
    $output .= '<thead>
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
    while($row = mysqli_fetch_array($result)) {
        $output .= '<tbody>';
        $output .= '<tr>';
        $output .= '<td style="min-width: 75px;">'. $counter++ .'</td>';
        $output .= '<td>'.$row['kode_surat'].'</td>';
        $output .= '<td>'.$row['asal_surat'].'</td>';
        $output .= '<td>'.$row['perihal'].'</td>';
        $output .= '<td>'.$row['tanggal_surat'].'</td>';
        $output .= '<td>';
        if ($row['status_baca']) {
            $output .= 'Sudah Disposisi';
        } else {
            $output .= 'Belum Disposisi';
        }
        if ($row['status_tolak'] == 1) {
            $output .= ' - Ditolak';
        } else {
            $output .= '';
        }
        if ($row['status_selesai'] == 1) {
            $output .= ' - Selesai';
        } else {
            $output .= '';
        }
        $output .= '</td>';
        $output .= '<td><a href="lacak.php?id='.$row['id_surat'].'">Lihat</a></td>';
        $output .= '</tr>';
        $output .= '</tbody>';
    }
    $output .= '</table>';
    echo $output;
} else {
  echo "<tr><td colspan='7'>0 results</td></tr>";
}
?>