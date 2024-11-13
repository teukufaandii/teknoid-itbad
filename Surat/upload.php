<?php
$conn = mysqli_connect("localhost", "teknoid1_admin", "RadKrwY8qt3v", "teknoid1_db_teknoid");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['upload'])) {
    $title = $_POST['title'];
    $file = $_FILES['file_form'];

    $fileName = $_FILES['file_form']['name'];
    $fileTmpName = $_FILES['file_form']['tmp_name'];
    $fileSize = $_FILES['file_form']['size'];
    $fileError = $_FILES['file_form']['error'];
    $fileType = $_FILES['file_form']['type'];

    $fileExt = explode('.', $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array('doc', 'docx');

    if (in_array($fileActualExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 10000000) {
                $fileDestination = 'formulir/' . $fileName;
                move_uploaded_file($fileTmpName, $fileDestination);
                $sql = "INSERT INTO files (title, file) VALUES ('$title', '$fileName')";
                mysqli_query($conn, $sql);
                echo "<script>alert('File berhasil ditambahkan');</script>";
                echo "<meta http-equiv='refresh' content='1; url=manajemen_form.php'>";
            } else {
                echo "<script>alert('Your file is too big!');</script>";
            }
        } else {
            echo "<script>alert('There was an error uploading your file!');</script>";
        }
    } else {
        echo "<script>alert('You cannot upload files of this type!');</script>";
    }
}
