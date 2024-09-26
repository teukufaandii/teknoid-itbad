<?php
// Lakukan koneksi ke database
include 'koneksi.php';

// Pastikan permintaan adalah metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Baca payload JSON dari permintaan
    $data = json_decode(file_get_contents("php://input"), true);

    // Pastikan payload memiliki kueri SQL
    if (isset($data['sql'])) {
        // Eksekusi kueri SQL
        $sql = $data['sql'];
        $result = $koneksi->query($sql);

        // Inisialisasi array untuk menampung hasil
        $notifications = array();

        // Jika terdapat hasil dari kueri, tambahkan ke array
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
        } else {
            // If there are no results, return a message
            echo json_encode(array("message" => "No notifications found."));
        }

        // Kembalikan hasil dalam format JSON
        header('Content-Type: application/json');
        echo json_encode($notifications);
    } else {
        // Jika payload tidak memiliki kueri SQL
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Kueri SQL tidak diberikan."));
    }
} else {
    // Jika metode permintaan bukan POST
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "Metode permintaan tidak diizinkan."));
}
?>
