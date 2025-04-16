<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$komentar_id = $_GET['id'];

// Hapus komentar dari database
$sql = "DELETE FROM komentar WHERE id = $komentar_id";
if (mysqli_query($conn, $sql)) {
    header("Location: lihat_komentar.php?id=$_GET[wisata_id]");
    exit;
} else {
    echo "Gagal menghapus komentar.";
}
?>
