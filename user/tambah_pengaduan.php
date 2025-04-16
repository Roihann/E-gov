<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pengaduan = $_POST['pengaduan'];
    $wisata_id = $_POST['wisata_id'];
    $user_id = $_SESSION['user_id'];

    // Insert pengaduan ke database
    $sql = "INSERT INTO pengaduan (wisata_id, user_id, isi_pengaduan) VALUES ('$wisata_id', '$user_id', '$pengaduan')";
    if (mysqli_query($conn, $sql)) {
        header("Location: halaman_detail.php?id=$wisata_id");
        exit;
    } else {
        echo "Gagal mengajukan pengaduan.";
    }
}
?>
