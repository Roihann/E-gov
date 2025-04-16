<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$wisata_id = $_GET['id'];

// Ambil data tempat wisata yang akan diedit
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
$data = mysqli_fetch_assoc($wisata);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $deskripsi = $_POST['deskripsi'];
    $foto = $_FILES['foto']['name'];

    if ($foto) {
        // Jika ada foto baru, upload foto
        $target = "../uploads/" . basename($foto);
        move_uploaded_file($_FILES['foto']['tmp_name'], $target);
        // Hapus foto lama jika ada
        unlink("../uploads/" . $data['foto']);
    } else {
        // Jika tidak ada foto baru, gunakan foto lama
        $foto = $data['foto'];
    }

    // Update data tempat wisata
    $sql = "UPDATE wisata SET nama='$nama', alamat='$alamat', deskripsi='$deskripsi', foto='$foto' WHERE id=$wisata_id";
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Gagal mengupdate wisata.";
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
      <input type="text" name="nama" class="form-control" value="<?= $data['nama']; ?>" required>
    </div>
    <div class="mb-3">
      <label for="alamat" class="form-label">Alamat</label>
      <input type="text" name="alamat" class="form-control" value="<?= $data['alamat']; ?>" required>
    </div>
    <div class="mb-3">
      <label for="deskripsi" class="form-label">Deskripsi</label>
      <textarea name="deskripsi" class="form-control" rows="4" required><?= $data['deskripsi']; ?></textarea>
    </div>
    <div class="mb-3">
      <label for="foto" class="form-label">Foto (Opsional)</label>
      <input type="file" name="foto" class="form-control">
      <p><em>Foto saat ini: <img src="../uploads/<?= $data['foto']; ?>" class="img-thumbnail" width="150"></em></p>
    </div>
    <button type="submit" class="btn btn-warning w-100">Update Wisata</button>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
