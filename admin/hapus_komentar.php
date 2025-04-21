<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID komentar tidak valid.";
    header("Location: dashboard.php");
    exit;
}

$komentar_id = (int)$_GET['id'];

// Fetch wisata_id associated with the comment
$stmt = $conn->prepare("SELECT wisata_id FROM komentar WHERE id = ?");
$stmt->bind_param("i", $komentar_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Komentar tidak ditemukan.";
    header("Location: dashboard.php");
    exit;
}

$row = $result->fetch_assoc();
$wisata_id = $row['wisata_id'];
$stmt->close();

// Delete the comment
$stmt = $conn->prepare("DELETE FROM komentar WHERE id = ?");
$stmt->bind_param("i", $komentar_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Komentar berhasil dihapus.";
    header("Location: lihat_komentar.php?id=$wisata_id");
} else {
    $_SESSION['error'] = "Gagal menghapus komentar: " . $conn->error;
    header("Location: lihat_komentar.php?id=$wisata_id");
}

$stmt->close();
$conn->close();
?>