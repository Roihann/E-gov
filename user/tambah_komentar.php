<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $komentar = $_POST['komentar'];
    $wisata_id = $_POST['wisata_id'];
    $user_id = $_SESSION['user_id'];

    // Insert komentar ke database
    $sql = "INSERT INTO komentar (wisata_id, user_id, isi_komentar) VALUES ('$wisata_id', '$user_id', '$komentar')";
    if (mysqli_query($conn, $sql)) {
        header("Location: halaman_detail.php?id=$wisata_id");
        exit;
    } else {
        echo "Gagal menambahkan komentar.";
    }
}
?>
