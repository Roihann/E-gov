<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}
$wisata = mysqli_query($conn, "SELECT * FROM wisata");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Daftar Tempat Wisata</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Daftar Tempat Wisata</h2>
    <div>
      <span class="me-2">Hi, <?= $_SESSION['username']; ?></span>
      <a href="../auth/logout.php" class="btn btn-sm btn-danger">Logout</a>
    </div>
  </div>

  <div class="row">
    <?php while ($row = mysqli_fetch_assoc($wisata)): ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow">
          <img src="../uploads/<?= $row['foto']; ?>" class="card-img-top" alt="<?= $row['nama']; ?>">
          <div class="card-body">
            <h5 class="card-title"><?= $row['nama']; ?></h5>
            <p class="card-text"><?= substr($row['deskripsi'], 0, 100); ?>...</p>
            <a href="detail.php?id=<?= $row['id']; ?>" class="btn btn-primary">Lihat Detail</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
</body>
</html>
