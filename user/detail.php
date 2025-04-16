<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$wisata_id = $_GET['id'];
// Ambil data tempat wisata
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
$data = mysqli_fetch_assoc($wisata);

// Ambil komentar
$komentar = mysqli_query($conn, "
    SELECT komentar.isi_komentar, komentar.created_at, users.username 
    FROM komentar 
    JOIN users ON komentar.user_id = users.id 
    WHERE komentar.wisata_id = $wisata_id 
    ORDER BY komentar.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Detail Tempat Wisata</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="row">
    <div class="col-md-8">
      <h2 class="mb-3"><?= $data['nama']; ?></h2>
      <img src="../uploads/<?= $data['foto']; ?>" class="img-fluid rounded mb-3" alt="<?= $data['nama']; ?>">
      <p><strong>Alamat:</strong> <?= $data['alamat']; ?></p>
      <p><strong>Deskripsi:</strong> <?= $data['deskripsi']; ?></p>
    </div>
    <div class="col-md-4">
      <!-- Form komentar -->
      <h4>Komentar</h4>
      <?php while ($k = mysqli_fetch_assoc($komentar)): ?>
        <div class="mb-3 border-bottom pb-2">
          <strong><?= $k['username']; ?>:</strong><br>
          <p><?= $k['isi_komentar']; ?></p>
          <small class="text-muted"><?= $k['created_at']; ?></small>
        </div>
      <?php endwhile; ?>

      <!-- Form untuk menambahkan komentar -->
      <form action="../proses/komentar_proses.php" method="POST">
        <input type="hidden" name="wisata_id" value="<?= $wisata_id; ?>">
        <div class="mb-3">
          <textarea name="isi_komentar" class="form-control" placeholder="Tulis komentar..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Kirim Komentar</button>
      </form>

      <hr>

      <!-- Tombol Pengaduan -->
      <a href="pengaduan.php?id=<?= $wisata_id; ?>" class="btn btn-warning w-100">Laporkan Pengaduan 🛠️</a>
    </div>
  </div>

  <div class="mt-3">
    <a href="index.php" class="btn btn-secondary">⬅️ Kembali ke Daftar Tempat Wisata</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
