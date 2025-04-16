<?php
include '../config/db.php';

$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$deskripsi = $_POST['deskripsi'];

// Upload file
$target_dir = "../uploads/";
$nama_file = basename($_FILES["foto"]["name"]);
$target_file = $target_dir . $nama_file;

if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
    $sql = "INSERT INTO wisata (nama, alamat, deskripsi, foto) 
            VALUES ('$nama', '$alamat', '$deskripsi', '$nama_file')";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../admin/dashboard.php");
    } else {
        echo "Gagal menyimpan ke database: " . mysqli_error($conn);
    }
} else {
    echo "Upload foto gagal.";
}
?>
