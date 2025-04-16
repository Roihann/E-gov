<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data tempat wisata
$wisata = mysqli_query($conn, "SELECT * FROM wisata");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard Admin</h2>
    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
  </div>

  <div class="row mb-4">
    <div class="col-md-6">
      <h4>Tempat Wisata</h4>
      <a href="tambah_wisata.php" class="btn btn-success mb-3">Tambah Tempat Wisata</a>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama Wisata</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($wisata)): ?>
            <tr>
              <td><?= $row['id']; ?></td>
              <td><?= $row['nama']; ?></td>
              <td>
                <a href="lihat_komentar.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm">Lihat Komentar</a>
                <a href="edit_wisata.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="hapus_wisata.php?id=<?= <?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data tempat wisata
$wisata = mysqli_query($conn, "SELECT * FROM wisata");

if (!$wisata) {
    die("Query failed: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard Admin</h2>
    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
  </div>

  <div class="row mb-4">
    <div class="col-md-6">
      <h4>Tempat Wisata</h4>
      <a href="tambah_wisata.php" class="btn btn-success mb-3">Tambah Tempat Wisata</a>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama Wisata</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($wisata) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($wisata)): ?>
              <tr>
                <td><?= $row['id']; ?></td>
                <td><?= $row['nama']; ?></td>
                <td>
                  <a href="lihat_komentar.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm">Lihat Komentar</a>
                  <a href="edit_wisata.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="hapus_wisata.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus tempat wisata ini?')">Hapus</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="3">Tidak ada data tempat wisata.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>$row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus tempat wisata ini?')">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
