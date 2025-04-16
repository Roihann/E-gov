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

<!DOCTYPE html>
<html>
<head>
  <title>Hapus Tempat Wisata</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h2 class="mb-4">Hapus Tempat Wisata</h2>
  <p>Apakah Anda yakin ingin menghapus tempat wisata <strong><?= $data['nama']; ?></strong>?</p>
  <form method="POST">
    <button type="submit" class="btn btn-danger w-100">Hapus</button>
  </form>
  <a href="dashboard.php" class="btn btn-secondary w-100 mt-3">Batal</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
