<?php
session_start();
include '../config/db.php';

$wisata_id = $_GET['id'];

// Ambil data tempat wisata
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
$data_wisata = mysqli_fetch_assoc($wisata);

// Ambil komentar dan pengaduan
$komentar = mysqli_query($conn, "
    SELECT komentar.isi_komentar, komentar.created_at, users.username 
    FROM komentar 
    JOIN users ON komentar.user_id = users.id 
    WHERE komentar.wisata_id = $wisata_id
    ORDER BY komentar.created_at DESC
");

$pengaduan = mysqli_query($conn, "
    SELECT pengaduan.isi_pengaduan, pengaduan.created_at, users.username 
    FROM pengaduan 
    JOIN users ON pengaduan.user_id = users.id 
    WHERE pengaduan.wisata_id = $wisata_id
    ORDER BY pengaduan.created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Tempat Wisata</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h2 class="mb-4"><?= $data_wisata['nama']; ?></h2>
  
  <!-- Foto Tempat Wisata -->
  <div class="mb-4">
    <img src="../uploads/<?= $data_wisata['foto']; ?>" class="img-fluid rounded mb-3" alt="<?= $data_wisata['nama']; ?>">
    <p><strong>Alamat:</strong> <?= $data_wisata['alamat']; ?></p>
    <p><strong>Deskripsi:</strong> <?= $data_wisata['deskripsi']; ?></p>
  </div>

  <!-- Komentar Section -->
  <div class="mb-4">
    <h4>Komentar</h4>
    <?php while ($row = mysqli_fetch_assoc($komentar)): ?>
      <div class="mb-3 border-bottom pb-2">
        <strong><?= $row['username']; ?>:</strong><br>
        <p><?= $row['isi_komentar']; ?></p>
        <small class="text-muted"><?= $row['created_at']; ?></small>
      </div>
    <?php endwhile; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
      <h5>Tinggalkan Komentar</h5>
      <form action="tambah_komentar.php" method="POST">
        <div class="mb-3">
          <textarea name="komentar" class="form-control" rows="3" required></textarea>
        </div>
        <input type="hidden" name="wisata_id" value="<?= $wisata_id; ?>">
        <button type="submit" class="btn btn-primary">Kirim Komentar</button>
      </form>
    <?php else: ?>
      <p>Silakan <a href="login.php">login</a> untuk memberikan komentar.</p>
    <?php endif; ?>
  </div>

  <!-- Pengaduan Section -->
  <div>
    <h4>Pengaduan</h4>
    <?php while ($row = mysqli_fetch_assoc($pengaduan)): ?>
      <div class="mb-3 border-bottom pb-2">
        <strong><?= $row['username']; ?>:</strong><br>
        <p><?= $row['isi_pengaduan']; ?></p>
        <small class="text-muted"><?= $row['created_at']; ?></small>
      </div>
    <?php endwhile; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
      <h5>Buat Pengaduan</h5>
      <form action="tambah_pengaduan.php" method="POST">
        <div class="mb-3">
          <textarea name="pengaduan" class="form-control" rows="3" required></textarea>
        </div>
        <input type="hidden" name="wisata_id" value="<?= $wisata_id; ?>">
        <button type="submit" class="btn btn-danger">Kirim Pengaduan</button>
      </form>
    <?php else: ?>
      <p>Silakan <a href="login.php">login</a> untuk mengajukan pengaduan.</p>
    <?php endif; ?>
  </div>

  <a href="index.php" class="btn btn-secondary mt-4">Kembali ke Daftar Tempat Wisata</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
