<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$wisata_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data tempat wisata yang akan diedit
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
if (!$wisata || mysqli_num_rows($wisata) == 0) {
    die("Tempat wisata tidak ditemukan.");
}
$data = mysqli_fetch_assoc($wisata);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $fotos = $_FILES['fotos'];
    $delete_old = isset($_POST['delete_old']) ? true : false;

    $foto_names = $delete_old ? [] : (!empty($data['foto']) ? explode(',', $data['foto']) : []);
    $target_dir = "../Uploads/";

    // Handle new file uploads
    if (!empty($fotos['name'][0])) {
        foreach ($fotos['name'] as $key => $name) {
            if ($fotos['error'][$key] == UPLOAD_ERR_OK) {
                $target_file = $target_dir . basename($name);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Validate file type and size
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($imageFileType, $allowed_types) && $fotos['size'][$key] <= 5 * 1024 * 1024) {
                    if (move_uploaded_file($fotos['tmp_name'][$key], $target_file)) {
                        $foto_names[] = basename($name);
                    }
                }
            }
        }
    }

    // Delete old images if requested
    if ($delete_old && !empty($data['foto'])) {
        foreach (explode(',', $data['foto']) as $old_foto) {
            $file_path = "../Uploads/" . $old_foto;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    // Join filenames into a comma-separated string
    $foto_string = implode(',', $foto_names);

    // Update data tempat wisata
    $sql = "UPDATE wisata SET nama='$nama', alamat='$alamat', deskripsi='$deskripsi', foto='$foto_string' WHERE id=$wisata_id";
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Gagal mengupdate wisata: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Tempat Wisata</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h2 class="mb-4">Edit Tempat Wisata</h2>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="nama" class="form-label">Nama Tempat Wisata</label>
      <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" required>
    </div>
    <div class="mb-3">
      <label for="alamat" class="form-label">Alamat</label>
      <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($data['alamat']); ?>" required>
    </div>
    <div class="mb-3">
      <label for="deskripsi" class="form-label">Deskripsi</label>
      <textarea name="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($data['deskripsi']); ?></textarea>
    </div>
    <div class="mb-3">
      <label for="fotos" class="form-label">Foto Baru (Opsional, pilih hingga 5 gambar)</label>
      <input type="file" name="fotos[]" class="form-control" multiple accept="image/*">
      <small class="form-text text-muted">Format: JPG, JPEG, PNG, GIF. Maksimum 5MB per gambar.</small>
    </div>
    <div class="mb-3">
      <label class="form-label">Foto Saat Ini:</label>
      <div class="d-flex flex-wrap">
        <?php 
        $fotos = !empty($data['foto']) ? explode(',', $data['foto']) : [];
        foreach ($fotos as $foto): ?>
          <img src="../Uploads/<?= htmlspecialchars($foto); ?>" class="img-thumbnail me-2 mb-2" width="150">
        <?php endforeach; ?>
      </div>
      <?php if (!empty($fotos)): ?>
        <div class="form-check">
          <input type="checkbox" name="delete_old" class="form-check-input" id="delete_old">
          <label class="form-check-label" for="delete_old">Hapus foto lama</label>
        </div>
      <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-warning w-100">Update Wisata</button>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>