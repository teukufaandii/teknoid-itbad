<?php
session_start(); // Mulai session sebelum mengakses variabel $_SESSION
include '../koneksi.php';
$output = '';

if (isset($_POST['query'])) {
  $search = $_POST['query'];
  // Tambahkan placeholder % di depan dan belakang search untuk pencarian per huruf
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
  $output .= '<tbody>';
  while ($row = $result->fetch_assoc()) {
    $output .= '<tr>';
    $output .= '<td style="min-width: 75px;">' . $counter++ . '</td>';
    $output .= '<td>' . $row['kode_surat'] . '</td>';
    $output .= '<td>' . $row['asal_surat'] . '</td>';
    $output .= '<td>' . $row['perihal'] . '</td>';
    $output .= '<td>' . $row['tanggal_surat'] . '</td>';
    $output .= '<td>';
    if ($row['status_baca']) {
      $output .= 'Sudah Disposisi';
    } else {
      $output .= 'Belum Disposisi';
    }
    if ($row['status_tolak'] == 1) {
      $output .= ' - Ditolak';
    }
    if ($row['status_selesai'] == 1) {
      $output .= ' - Selesai';
    }
    $output .= '</td>';
    $output .= '<td><a href="disposisi.php?id=' . $row['id_surat'] . '">Lihat</a></td>';
    $output .= '</tr>';
  }
  $output .= '</tbody>';
  $output .= '</table>';
  echo $output;
} else {
  echo "<table><tr><td colspan='7'>0 results</td></tr></table>";
}
