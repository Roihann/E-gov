<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $fotos = $_FILES['fotos'];

    $foto_names = [];
    $target_dir = "../Uploads/";
    
    // Handle multiple file uploads
    foreach ($fotos['name'] as $key => $name) {
        if ($fotos['error'][$key] == UPLOAD_ERR_OK) {
            $target_file = $target_dir . basename($name);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Validate file type and size (e.g., allow only images, max 5MB)
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed_types) && $fotos['size'][$key] <= 5 * 1024 * 1024) {
                if (move_uploaded_file($fotos['tmp_name'][$key], $target_file)) {
                    $foto_names[] = basename($name);
                }
            }
        }
    }

    // Join filenames into a comma-separated string
    $foto_string = implode(',', $foto_names);

    // Insert data into database
    $sql = "INSERT INTO wisata (nama, alamat, deskripsi, foto) VALUES ('$nama', '$alamat', '$deskripsi', '$foto_string')";
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Gagal menambah wisata: " . mysqli_error($conn);
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
      <label for="fotos" class="form-label">Foto (Pilih hingga 5 gambar)</label>
      <input type="file" name="fotos[]" class="form-control" multiple accept="image/*" required>
      <small class="form-text text-muted">Format: JPG, JPEG, PNG, GIF. Maksimum 5MB per gambar.</small>
    </div>
    <button type="submit" class="btn btn-success w-100">Tambah Wisata</button>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>