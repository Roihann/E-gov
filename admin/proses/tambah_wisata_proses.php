<?php
include '../config/db.php';

$nama = mysqli_real_escape_string($conn, $_POST['nama']);
$alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
$deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
$fotos = $_FILES['fotos'];

$target_dir = "../Uploads/";
$foto_names = [];

foreach ($fotos['name'] as $key => $name) {
    if ($fotos['error'][$key] == UPLOAD_ERR_OK) {
        $target_file = $target_dir . basename($name);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Validate file type and size
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types) && $fotos['size'][$key] <= 5 * 1024 * 1024) {
            if (move_uploaded_file($fotos['tmp_name'][$key], $target_file)) {
                $foto_names[] = basename($name);
            }
        }
    }
}

if (!empty($foto_names)) {
    $foto_string = implode(',', $foto_names);
    $sql = "INSERT INTO wisata (nama, alamat, deskripsi, foto) 
            VALUES ('$nama', '$alamat', '$deskripsi', '$foto_string')";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../admin/dashboard.php");
    } else {
        echo "Gagal menyimpan ke database: " . mysqli_error($conn);
    }
} else {
    echo "Upload foto gagal atau tidak ada foto yang valid.";
}
?>