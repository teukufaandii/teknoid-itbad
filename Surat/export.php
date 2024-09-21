<?php
if (isset($_POST['export_data'])) {
    // Database connection (adjust as needed)
    $conn = mysqli_connect("localhost", "root", "", "db_teknoid");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Unserialize the filtered data
    $user_arr = unserialize($_POST['export_data']);

    // Get jenis_surat from the post data
    $jenis_surat = $_POST['jenis_surat'];

    // If there is no data, show an error
    if (empty($user_arr)) {
        echo "No data available for export.";
        exit();
    }

    // Prepare a list of kode_surat to be used in the query
    $kode_surat_list = array_column($user_arr, 0); // Assuming 0 index contains `kode_surat` or `id`
    $kode_surat_str = implode("','", $kode_surat_list);

    // Adjust the SQL query to fetch data based on the filtered `kode_surat`
    if ($jenis_surat == "Surat Permohonan") {
        $sql = "SELECT kode_surat, jenis_surat, asal_surat, perihal, tanggal_surat 
                FROM tb_surat_dis 
                WHERE kode_surat IN ('$kode_surat_str')";
        $judul = ['No', 'Kode Surat', 'Jenis Surat',
            'Asal Surat',
            'Perihal',
            'Tanggal Surat'
        ];
    } elseif ($jenis_surat == "Surat Laporan") {
        $sql = "SELECT kode_surat, jenis_surat, asal_surat, perihal, tanggal_surat 
                FROM tb_surat_dis 
                WHERE kode_surat IN ('$kode_surat_str')";
        $judul = ['No', 'Kode Surat', 'Jenis Surat',
            'Asal Surat',
            'Perihal',
            'Tanggal Surat'
        ];
    } elseif ($jenis_surat == "Surat KKL") {
        $sql = "SELECT kode_surat, jenis_surat, asal_surat, perihal, tanggal_surat 
                FROM tb_surat_dis 
                WHERE kode_surat IN ('$kode_surat_str')";
        $judul = ['No', 'Kode Surat', 'Jenis Surat',
            'Asal Surat',
            'Perihal',
            'Tanggal Surat'
        ];
    } elseif ($jenis_surat == "Surat Riset") {
        $sql = "SELECT kode_surat, jenis_surat, asal_surat, perihal, tanggal_surat 
                FROM tb_surat_dis 
                WHERE kode_surat IN ('$kode_surat_str')";
        $judul = ['No', 'Kode Surat', 'Jenis Surat',
            'Asal Surat',
            'Perihal',
            'Tanggal Surat'
        ];
    } elseif ($jenis_surat == "Surat Insentif") {
        $sql = "SELECT id_srt, jenis_surat, asal_surat, jenis_insentif, tanggal_surat 
                FROM tb_srt_dosen 
                WHERE id_srt IN ('$kode_surat_str')";
        $judul = ['No', 'Jenis Surat', 'Asal Surat',
            'Jenis Insentif',
            'Tanggal Surat'
        ];
    } elseif ($jenis_surat == "Surat Riset Dosen") {
        $sql = "SELECT id_srt, jenis_surat, asal_surat, perihal_srd, nama_perusahaan_srd, tanggal_surat 
                FROM tb_srt_dosen 
                WHERE id_srt IN ('$kode_surat_str')";
        $judul = ['No', 'Jenis Surat', 'Asal Surat',
            'Perihal',
            'Nama Perusahaan',
            'Tanggal Surat'
        ];
    } elseif ($jenis_surat == "Surat Honorium") {
        $sql = "SELECT id, jenis_surat, asal_surat, nm_kegiatan, tanggal_surat 
                FROM tb_srt_honor 
                WHERE id IN ('$kode_surat_str')";
        $judul = ['No', 'Jenis Surat', 'Asal Surat',
            'Nama Kegiatan',
            'Tanggal Surat'
        ];
    } else {
        echo "Jenis surat tidak dikenal.";
        exit();
    }

    // Execute query
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Create CSV
        $filename = 'RekapSurat.csv';
        $file = fopen($filename, "w");

        // Menulis judul ke file CSV
        fputcsv($file, $judul);

        // Add row data
        $no = 1; // Start numbering
        while ($row = $result->fetch_assoc()) {
            // Convert 'jenis_surat' values
            if ($row['jenis_surat'] == 1) {
                $row['jenis_surat'] = "Surat Permohonan";
            } elseif ($row['jenis_surat'] == 2) {
                $row['jenis_surat'] = "Surat Laporan";
            } elseif ($row['jenis_surat'] == 3) {
                $row['jenis_surat'] = "Surat KKL";
            } elseif ($row['jenis_surat'] == 4) {
                $row['jenis_surat'] = "Surat Riset";
            } elseif ($row['jenis_surat'] == 5) {
                $row['jenis_surat'] = "Surat Insentif";
            } elseif ($row['jenis_surat'] == 6) {
                $row['jenis_surat'] = "Surat Riset Dosen";
            } elseif ($row['jenis_surat'] == 7) {
                $row['jenis_surat'] = "Surat Honorium";
            }

            // Format date
            if (isset($row['tanggal_surat'])) {
                $row['tanggal_surat'] = date('d-m-Y', strtotime($row['tanggal_surat']));
            }

            array_unshift($row, $no); // Add numbering to each row
            fputcsv($file, $row);
            $no++;
        }

        fclose($file);

        // Download the file
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv;");
        readfile($filename);

        // Remove the file after download
        unlink($filename);
        exit();
    } else {
        echo "No data available for export.";
    }

    // Close connection
    $conn->close();
} else {
    echo "Data for export is not available.";
}
