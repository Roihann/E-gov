<?php
session_start();
include '../config/db.php';

// Periksa apakah user sudah login dan memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Pastikan 'id' ada dalam parameter URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID wisata tidak valid.";
    exit;
}

$wisata_id = $_GET['id'];

// Ambil data tempat wisata berdasarkan ID
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
if (!$wisata || mysqli_num_rows($wisata) == 0) {
    echo "Tempat wisata tidak ditemukan.";
    exit;
}
$data_wisata = mysqli_fetch_assoc($wisata);

// Ambil komentar dan pengaduan
$komentar = mysqli_query($conn, "
    SELECT komentar.isi_komentar, komentar.created_at, users.username, komentar.id as komentar_id 
    FROM komentar 
    JOIN users ON komentar.user_id = users.id 
    WHERE komentar.wisata_id = $wisata_id
    ORDER BY komentar.created_at DESC
");

$pengaduan = mysqli_query($conn, "
    SELECT pengaduan.isi_pengaduan, pengaduan.created_at, users.username, pengaduan.id as pengaduan_id 
    FROM pengaduan 
    JOIN users ON pengaduan.user_id = users.id 
    WHERE pengaduan.wisata_id = $wisata_id
    ORDER BY pengaduan.created_at DESC
");

?>

<!DOCTYPE html>
<html>
<head>
  <title>Komentar & Pengaduan - <?= htmlspecialchars($data_wisata['nama']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h2 class="mb-4">Komentar & Pengaduan untuk <?= htmlspecialchars($data_wisata['nama']); ?></h2>

  <div class="mb-4">
    <h4>Komentar</h4>
    <?php while ($row = mysqli_fetch_assoc($komentar)): ?>
      <div class="mb-3 border-bottom pb-2">
        <strong><?= htmlspecialchars($row['username']); ?>:</strong><br>
        <p><?= htmlspecialchars($row['isi_komentar']); ?></p>
        <small class="text-muted"><?= $row['created_at']; ?></small>
        <a href="hapus_komentar.php?id=<?= $row['komentar_id']; ?>" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">Hapus</a>
      </div>
    <?php endwhile; ?>
  </div>

  <div>
    <h4>Pengaduan</h4>
    <?php while ($row = mysqli_fetch_assoc($pengaduan)): ?>
      <div class="mb-3 border-bottom pb-2">
        <strong><?= htmlspecialchars($row['username']); ?>:</strong><br>
        <p><?= htmlspecialchars($row['isi_pengaduan']); ?></p>
        <small class="text-muted"><?= $row['created_at']; ?></small>
        <a href="hapus_pengaduan.php?id=<?= $row['pengaduan_id']; ?>" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Apakah Anda yakin ingin menghapus pengaduan ini?')">Hapus</a>
      </div>
    <?php endwhile; ?>
  </div>

  <a href="dashboard.php" class="btn btn-secondary mt-4">Kembali ke Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
