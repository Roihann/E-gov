<?php
session_start();
include '../config/db.php';

$user_id = $_SESSION['user_id'];
$wisata_id = $_POST['wisata_id'];
$isi = mysqli_real_escape_string($conn, $_POST['isi_pengaduan']);

$sql = "INSERT INTO pengaduan (user_id, wisata_id, isi_pengaduan) VALUES ('$user_id', '$wisata_id', '$isi')";
if (mysqli_query($conn, $sql)) {
    header("Location: ../user/detail.php?id=$wisata_id");
} else {
    echo "Gagal mengirim pengaduan: " . mysqli_error($conn);
}
?>
