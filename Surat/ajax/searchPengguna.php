<?php
include '../koneksi.php';
$output='';

if(isset($_POST['query'])){
    $search = $_POST['query'];
    $stmt = $koneksi->prepare("SELECT * FROM tb_pengguna WHERE noinduk LIKE ? OR nama_lengkap LIKE ? OR jabatan LIKE ? OR akses LIKE ? OR password LIKE ? OR no_hp LIKE ?");
    $search = "%$search%"; // Tambahkan tanda persen untuk pencarian wildcard
    $stmt->bind_param("ssssss", $search, $search, $search, $search, $search, $search);
}
else{
    $stmt = $koneksi->prepare("SELECT * FROM tb_pengguna");
}
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $counter = 1;
    $output .= '<table>';

    while($row = $result->fetch_assoc()){
        $output .= '<tr>
                        <td style="min-width: 75px;">' . $counter++ . '</td>
                        <td>' . $row["noinduk"] . '</td>
                        <td>' . $row["nama_lengkap"] . '</td>
                        <td>' . $row["jabatan"] . '</td>
                        <td>' . $row["akses"] . '</td>
                        <td>' . $row["no_hp"] . '</td>
                        <td>' . $row["email"] . '</td>
                        <td style="text-align: center;">
                            <div>
                                <a href="edit_user_admin.php?ids=' . $row["id_pg"] . '">
                                    <button class="btnEdit" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </a>
                            </div>
                            <div>                            
                                <button class="btnDel" title="Delete" onclick="openConfirmationModal(\'' . $row["id_pg"] . '\')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>';

    }
    $output .= '</table>';
    echo $output;
}
else{
    echo "<tr><td colspan='9'>Tidak ada hasil yang ditemukan</td></tr>";
}
?>
