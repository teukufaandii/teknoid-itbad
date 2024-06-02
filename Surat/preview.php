<!DOCTYPE html>
<html>
<head>
    <title>Preview PDF</title>
</head>
<body>
    <?php
    // Ambil URL file PDF dari parameter URL
    $pdf_url = isset($_GET['url']) ? urldecode($_GET['url']) : '';

if (!empty($pdf_url)) {
    // Set header agar konten ditampilkan di dalam browser
    header('Content-Disposition: inline; filename="preview.pdf"');

    // Tampilkan PDF menggunakan tag embed
    echo "<embed src='$pdf_url' width='100%' height='800px' />";
} else {
    echo "File PDF tidak tersedia.";
}
    ?>
</body>
</html>
