<?php
session_start();
include __DIR__ . '/../Maintenance/Middleware/index.php';
include "koneksi.php";
include "logout-checker.php";

// Periksa apakah pengguna telah login
if (!isset($_SESSION['pengguna'])) {
    header("Location: login.php");
    exit;
}

// Periksa apakah data yang diperlukan telah diterima dari formulir
if (isset($_POST['email']) && isset($_POST['no_hp'])) {
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validate the new password and confirm password only if they are not empty
    if (!empty($new_password) && !empty($confirm_password)) {
        if ($new_password != $confirm_password) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => "Password baru dan konfirmasi password tidak cocok."];
            header("Location: edit_user_mhs.php?ids=" . $_SESSION['pengguna']);
            exit;
        }

        // Get the current password from the database
        $sql = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE noinduk='" . $_SESSION['pengguna'] . "'");
        $row = mysqli_fetch_assoc($sql);

        if ($row) {
            // Verify the current password
            if (hash('sha256', $current_password) == $row['password']) {
                // Hash the new password using SHA-256
                $hashed_password = hash('sha256', $new_password);

                // Prepare the SQL statement to update the data with the new password
                $stmt = $koneksi->prepare("UPDATE tb_pengguna SET password=?, email=?, no_hp=? WHERE noinduk=?");
                $stmt->bind_param("ssss", $hashed_password, $email, $no_hp, $_SESSION['pengguna']);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $_SESSION['flash_message'] = ['type' => 'success', 'message' => "Data pengguna berhasil disimpan."];
                } else {
                    $_SESSION['flash_message'] = ['type' => 'error', 'message' => "Terjadi kesalahan saat menyimpan data pengguna."];
                }
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => "Password saat ini salah."];
            }
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => "Pengguna tidak ditemukan."];
        }
    } else {
        // If no new password was provided, update only email and no_hp
        $stmt = $koneksi->prepare("UPDATE tb_pengguna SET email=?, no_hp=? WHERE noinduk=?");
        $stmt->bind_param("sss", $email, $no_hp, $_SESSION['pengguna']);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => "Data pengguna berhasil disimpan tanpa perubahan password."];
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => "Terjadi kesalahan saat menyimpan data pengguna."];
        }
    }

    header("Location: edit_user_mhs.php?ids=" . $_SESSION['pengguna']);
    exit;
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => "Data tidak lengkap."];
    header("Location: edit_user_mhs.php?ids=" . $_SESSION['pengguna']);
    exit;
}
?>
