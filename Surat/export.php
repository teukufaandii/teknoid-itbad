<?php 
if(isset($_POST['export_data'])) {
    $filename = 'RekapSurat.csv'; 
    $file = fopen($filename, "w"); // Buka file dengan mode penulisan

    // Menambahkan baris judul
    $judul = array("Kode Surat", "Jenis Surat", "Asal Surat", "Perihal", "Tanggal Surat");
    fputcsv($file, $judul);

    $export_data = unserialize($_POST['export_data']); 
    foreach ($export_data as $line){ 
        // Memformat tanggal jika perlu
        $line[4] = date('d-m-Y', strtotime($line[4])); // Misalnya format tanggal YYYY-MM-DD
        
        fputcsv($file, $line); 
    } 
    fclose($file); 

    // Header untuk mengatur file sebagai attachment untuk diunduh
    header("Content-Description: File Transfer"); 
    header("Content-Disposition: attachment; filename=$filename"); 
    header("Content-Type: application/csv; "); 
    readfile($filename); 

    // Menghapus file setelah diunduh
    unlink($filename); 
    exit();
} else {
    echo "Data untuk diekspor tidak tersedia.";
}
