<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$wisata_id = $_GET['id'];

// Ambil data tempat wisata
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
$data = mysqli_fetch_assoc($wisata);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Hapus foto tempat wisata
    unlink("../uploads/" . $data['foto']);

    // Hapus data tempat wisata dari database
    $sql = "DELETE FROM wisata WHERE id = $wisata_id";
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Gagal menghapus wisata.";
    }
}
?>


