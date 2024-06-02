<?php
session_start(); // Mulai session sebelum mengakses variabel $_SESSION
include '../koneksi.php';
$output='';

if(isset($_POST['query'])){
  $search=$_POST['query'];
  if(isset($_SESSION['akses'])) {
    $diteruskan_ke = $_SESSION['akses']; // Ambil nilai session akses dari pengguna
    // Tambahkan placeholder % di depan dan belakang search untuk pencarian per huruf
    $search = '%' . $search . '%';
    $stmt=$koneksi->prepare("SELECT * FROM tb_surat_dis WHERE (kode_surat LIKE ? OR asal_surat LIKE ? OR perihal LIKE ? OR tanggal_surat LIKE ?) AND diteruskan_ke = ?");
    $stmt->bind_param("sssss", $search, $search, $search, $search, $diteruskan_ke);
  } else {
    // Handle jika session akses tidak terdefinisi
    exit("Session akses tidak terdefinisi.");
  }
}
else{
  if(isset($_SESSION['akses'])) {
    $diteruskan_ke = $_SESSION['akses']; // Ambil nilai session akses dari pengguna
    $stmt=$koneksi->prepare("SELECT * FROM tb_surat_dis WHERE diteruskan_ke = ?");
    $stmt->bind_param("s", $diteruskan_ke);
  } else {
    // Handle jika session akses tidak terdefinisi
    exit("Session akses tidak terdefinisi.");
  }
}
$stmt->execute();
$result=$stmt->get_result();

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
      $output .= '<td><a href="disposisi.php?id='.$row['id_surat'].'">Lihat</a></td>';
      $output .= '</tr>';
      $output .= '</tbody>';
  }
  $output .= '</table>';
  echo $output;
} else {
echo "<tr><td colspan='7'>0 results</td></tr>";
}
?>
