<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['wisata_id']) || !is_numeric($_GET['wisata_id'])) {
    $_SESSION['error'] = "ID pengaduan atau wisata tidak valid.";
    header("Location: dashboard.php");
    exit;
}

$pengaduan_id = (int)$_GET['id'];
$wisata_id = (int)$_GET['wisata_id'];

// Verify pengaduan exists and belongs to the specified wisata_id
$stmt = $conn->prepare("SELECT wisata_id FROM pengaduan WHERE id = ?");
$stmt->bind_param("i", $pengaduan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Pengaduan tidak ditemukan.";
    header("Location: dashboard.php");
    exit;
}

$row = $result->fetch_assoc();
if ($row['wisata_id'] !== $wisata_id) {
    $_SESSION['error'] = "Pengaduan tidak sesuai dengan wisata yang dipilih.";
    header("Location: dashboard.php");
    exit;
}
$stmt->close();

// Delete the pengaduan
$stmt = $conn->prepare("DELETE FROM pengaduan WHERE id = ?");
$stmt->bind_param("i", $pengaduan_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Pengaduan berhasil dihapus.";
    header("Location: lihat_komentar.php?id=$wisata_id");
} else {
    $_SESSION['error'] = "Gagal menghapus pengaduan: " . $conn->error;
    header("Location: lihat_komentar.php?id=$wisata_id");
}

$stmt->close();
$conn->close();
?>