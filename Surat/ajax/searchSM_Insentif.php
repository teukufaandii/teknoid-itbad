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
        WHERE (tujuan_surat_srd LIKE ? 
        OR asal_surat LIKE ? 
        OR NIDN LIKE ? 
        OR DATE_FORMAT(tanggal_surat, '%d-%m-%Y') LIKE ? 
        OR judul_penelitian_ppm LIKE ?
        OR judul_publikasi_pi LIKE ?
        OR judul_hki LIKE ?
        OR judul_buku LIKE ?
        OR judul_ipbk LIKE ?)
        AND jenis_surat = 5
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
                        <th>Jenis Insentif <i class="fas fa-sort"></i></th>
                        <th>Asal Surat <i class="fas fa-sort"></i></th>
                        <th>NIDN <i class="fas fa-sort"></i></th>
                        <th>Tanggal Surat <i class="fas fa-sort"></i></th>
                        <th>Status <i class="fas fa-sort"></i></th>
                        <th>Aksi</th>
                        <th>Detail <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>';
    $output .= '<tbody>';
    while ($row = $result->fetch_assoc()) {
        $tanggalSuratFormatted = date('d-m-Y', strtotime($row['tanggal_surat']));

        $output .= '<tr>';
        $output .= '<td style="min-width: 75px;">' . htmlspecialchars($counter++) . '</td>';
        // Mapping for jenis_insentif
            switch ($row['jenis_insentif']) {
                case 'penelitian':
                    $jenis_surat_text = 'Penelitian & Pengabdian Masyarakat';
                    break;
                case 'publikasi':
                    $jenis_surat_text = 'Publikasi Ilmiah';
                    break;
                case 'pertemuan_ilmiah':
                    $jenis_surat_text = 'Penyajian Paper Dalam Pertemuan Ilmiah';
                    break;
                case 'keynote_speaker':
                    $jenis_surat_text = 'Keynote Speaker Dalam Pertemuan Ilmiah';
                    break;
                case 'visiting':
                    $jenis_surat_text = 'Visiting Lecturer/Research';
                    break;
                case 'hki':
                    $jenis_surat_text = 'Hak Kekayaan Intelektual';
                    break;
                case 'teknologi':
                    $jenis_surat_text = 'Teknologi tepat Guna';
                    break;
                case 'buku':
                    $jenis_surat_text = 'Buku';
                    break;
                case 'model':
                    $jenis_surat_text = 'Model, Prototype, Desain, karya Seni, Rekayasa Sosial, Kebijakan';
                    break;
                case 'insentif_publikasi':
                    $jenis_surat_text = 'Insentif Publikasi berita Kegiatan pengabdian Masyarakat';
                    break;
                default:
                    $jenis_surat_text = 'Unknown'; // Optional: Handle unexpected values
                    break;
            }

            // Menambahkan ke output dengan htmlspecialchars
        $output .= '<td>' . htmlspecialchars($jenis_surat_text) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['asal_surat']) . '</td>';
        $output .= '<td>' . htmlspecialchars($row['NIDN']) . '</td>';
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
        $output .= '<td> <div class="aksi-btn">';
        if ($row['verifikasi_keuangan'] == 1) {
            $output .= "<button style='cursor: not-allowed;' class='verify-button' data-id='" . htmlspecialchars($row['id_srt']) . "' disabled><i class='fa-solid fa-check'></i> Sudah Diverifikasi ke keuangan</button>";
        } else {
            $output .= "<button class='verify-button' data-id='" . htmlspecialchars($row['id_srt']) . "'>Proses ke keuangan</button>";
        }
        $output .= '</div></td>';

        $output .= '<td>' . 
            '<a href="dispo_dosen.php?id=' . htmlspecialchars($row['id_srt']) . '">' . 
            '<i class="fas fa-eye" style="background-color: white; color: #1b5ebe;"></i>' . 
            '</a>' . 
        '</td>';

        $output .= '</tr>';
    }
    $output .= '</tbody>';

    $output .= '</table>';
    echo $output;
} else {
    echo "<table><tr><td colspan='7'>0 results</td></tr></table>";
}

?>
<script> document.querySelectorAll('.verify-button').forEach(function(button) {
    button.addEventListener('click', function() {
        var suratId = this.getAttribute('data-id');

        Swal.fire({
            title: 'Verifikasi Surat',
            text: "Apakah Anda yakin ingin memverifikasi surat ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Verifikasi',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: '../sql/verifikasi_keuangan.php',
                        method: 'POST',
                        data: {
                            id_srt: suratId
                        },
                        success: function(response) {
                            if (response === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Surat berhasil diverifikasi'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan, coba lagi nanti'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan, coba lagi nanti'
                            });
                        }
                    });
                });
            }
        });
    });
});
</script> 


