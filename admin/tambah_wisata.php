<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $deskripsi = $_POST['deskripsi'];
    $foto = $_FILES['foto']['name'];

    // Upload foto
    $target = "../uploads/" . basename($foto);
    move_uploaded_file($_FILES['foto']['tmp_name'], $target);

    // Masukkan data ke database
    $sql = "INSERT INTO wisata (nama, alamat, deskripsi, foto) VALUES ('$nama', '$alamat', '$deskripsi', '$foto')";
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Gagal menambah wisata.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Tambah Tempat Wisata</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h2 class="mb-4">Tambah Tempat Wisata</h2>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="nama" class="form-label">Nama Tempat Wisata</label>
      <input type="text" name="nama" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="alamat" class="form-label">Alamat</label>
      <input type="text" name="alamat" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="deskripsi" class="form-label">Deskripsi</label>
      <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
    </div>
    <div class="mb-3">
      <label for="foto" class="form-label">Foto</label>
      <input type="file" name="foto" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success w-100">Tambah Wisata</button>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
