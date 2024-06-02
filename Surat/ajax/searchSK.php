<?php
session_start();
include '../koneksi.php';

$output = '';
if(isset($_POST['query'])) {
    $search = $_POST['query'];
    $fullname = $_SESSION['nama_lengkap'];
    // Query untuk mencari surat keluar dengan asal surat sesuai dengan session nama_lengkap pengguna
    $query = "SELECT sd.id_surat, sd.kode_surat, sd.asal_surat,
              sd.perihal, sd.tanggal_surat, sd.status_baca, sd.status_tolak, sd.status_selesai,
              d.dispo1, d.dispo2, d.dispo3, d.dispo4, d.dispo5
              FROM tb_surat_dis sd
              LEFT JOIN tb_disposisi d ON sd.id_surat = d.id_surat
              WHERE sd.asal_surat = '$fullname' AND 
              (sd.kode_surat LIKE '%$search%' OR sd.perihal LIKE '%$search%' OR sd.tanggal_surat LIKE '%$search%')";
} else {
    // Jika tidak ada query pencarian, tampilkan semua surat keluar dengan asal surat sesuai session nama_lengkap pengguna
    $fullname = $_SESSION['nama_lengkap'];
    $query = "SELECT sd.id_surat, sd.kode_surat, sd.asal_surat,
              sd.perihal, sd.tanggal_surat, sd.status_baca, sd.status_tolak, sd.status_selesai,
              d.dispo1, d.dispo2, d.dispo3, d.dispo4, d.dispo5
              FROM tb_surat_dis sd
              LEFT JOIN tb_disposisi d ON sd.id_surat = d.id_surat
              WHERE sd.asal_surat = '$fullname'";
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