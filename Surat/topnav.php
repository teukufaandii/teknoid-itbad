<?php
include "logout-checker.php";
// Mengambil data email, password, dan nomor telepon dari tabel tb_pengguna
$userid = $_SESSION['pengguna_id'];
$sql_pengguna = "SELECT email, password, no_hp FROM tb_pengguna WHERE id_pg = '$userid'";
$result_pengguna = mysqli_query($conn, $sql_pengguna);
$row_pengguna = mysqli_fetch_assoc($result_pengguna);
$email = $row_pengguna['email'];
$password = $row_pengguna['password'];
$no_hp = $row_pengguna['no_hp'];
?>

<div class="topnav">
    <span class="Bar" id="Bar" onclick="openNav()"><i class="fa fa-bars"></i></span>
    <div class="btn-accSettings">
        <button id="accModalBtn"><i class="far fa-user"></i></button>
    </div>
</div>
    <div class="text">
        <div class="running-text">
            <p class="running-text2"><i class="fa fa-exclamation dash-icon"></i>Mohon lengkapi data pengguna yang berada dalam menu <span style="color: #871E1E;">pengaturan akun</span> di sudut kanan.<i class="fa fa-exclamation dash-icon"></i></p>
            <p class="running-text2"><i class="fa fa-exclamation dash-icon"></i>Mohon lengkapi data pengguna yang berada dalam menu <span style="color: #871E1E;">pengaturan akun </span>di sudut kanan.<i class="fa fa-exclamation dash-icon"></i></p>
        </div>
    </div>
<div id="accModal" class="accModal">
    <!-- Modal content -->
    <div class="accModal-content">
        <?php
            $some = $_SESSION['pengguna'];
            
            $query = "SELECT * FROM tb_pengguna WHERE noinduk = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $some);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo '<a href="edit_user_mhs.php?ids=' . $row['noinduk'] . '">
                        <button class="accBtn">
                        <i class="fas fa-user-edit"></i>Pengaturan Akun
                        </button>
                     </a>';
            }
        ?>
        <a href="keluar.php">
            <button class="accBtn">
                <i class="fas fa-sign-out-alt"></i>Log Out
            </button>
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const email = "<?php echo $email; ?>";
        const password = "<?php echo $password; ?>";
        const phoneNumber = "<?php echo $no_hp; ?>";

        if (email && password && phoneNumber) {
            document.querySelector('.running-text').style.display = 'none';
        } else {
            document.querySelector('.running-text').style.display = 'flex';
        }
    });
</script>