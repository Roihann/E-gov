<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$pengaduan_id = $_GET['id'];

// Hapus pengaduan dari database
$sql = "DELETE FROM pengaduan WHERE id = $pengaduan_id";
if (mysqli_query($conn, $sql)) {
    header("Location: lihat_komentar.php?id=$_GET[wisata_id]");
    exit;
} else {
    echo "Gagal menghapus pengaduan.";
}
?>
