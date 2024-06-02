<?php
include "koneksi.php";
include "logout-checker.php";
// Check if the id_pg parameter is set
if (isset($_REQUEST['id_pg'])) {
    // Sanitize the input to prevent SQL injection
    $id = $koneksi->real_escape_string($_REQUEST['id_pg']);
    
    // Construct the SQL query using prepared statements to prevent SQL injection
    $hapus = "DELETE FROM tb_pengguna WHERE id_pg=?";
    
    // Prepare the statement
    $stmt = $koneksi->prepare($hapus);
    
    // Bind the parameter
    $stmt->bind_param("i", $id);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Successful deletion
        echo '<script language="javascript" type="text/javascript">
            alert("Data pengguna berhasil dihapus.");
            </script>';
        echo "<meta http-equiv='refresh' content='0; url=pengaturan_akun.php'>";
    } else {
        // Error handling
        echo '<script language="javascript" type="text/javascript">
            alert("Gagal menghapus data pengguna.");
            </script>';
        echo "<meta http-equiv='refresh' content='0; url=pengaturan_akun.php'>";
    }

    // Close the statement
    $stmt->close();
} else {
    // If id_pg parameter is not set
    echo '<script language="javascript" type="text/javascript">
        alert("Parameter akun tidak ditemukan.");
        </script>';
    echo "<meta http-equiv='refresh' content='0; url=pengaturan_akun.php'>";
}

// Close the connection
$koneksi->close();
?>
