<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    if (isset($_GET['modal']) && $_GET['modal'] === 'true') {
        echo '<p class="text-red-600">Anda harus login sebagai admin untuk melakukan tindakan ini.</p>';
        exit;
    }
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    if (isset($_GET['modal']) && $_GET['modal'] === 'true') {
        echo '<p class="text-red-600">ID wisata tidak valid.</p>';
        exit;
    }
    $_SESSION['error'] = "ID wisata tidak valid.";
    header("Location: dashboard.php");
    exit;
}

$wisata_id = (int)$_GET['id'];

// Fetch wisata data
$stmt = $conn->prepare("SELECT nama, foto FROM wisata WHERE id = ?");
$stmt->bind_param("i", $wisata_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    if (isset($_GET['modal']) && $_GET['modal'] === 'true') {
        echo '<p class="text-red-600">Tempat wisata tidak ditemukan.</p>';
        exit;
    }
    $_SESSION['error'] = "Tempat wisata tidak ditemukan.";
    header("Location: dashboard.php");
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle multiple images (assuming foto is a comma-separated string)
    $images = !empty($data['foto']) ? explode(',', $data['foto']) : [];
    foreach ($images as $image) {
        $file_path = "../Uploads/" . trim($image);
        if (file_exists($file_path) && is_file($file_path)) {
            unlink($file_path);
        }
    }

    // Delete associated komentar
    $stmt = $conn->prepare("DELETE FROM komentar WHERE wisata_id = ?");
    $stmt->bind_param("i", $wisata_id);
    $stmt->execute();
    $stmt->close();

    // Delete associated pengaduan
    $stmt = $conn->prepare("DELETE FROM pengaduan WHERE wisata_id = ?");
    $stmt->bind_param("i", $wisata_id);
    $stmt->execute();
    $stmt->close();

    // Delete wisata
    $stmt = $conn->prepare("DELETE FROM wisata WHERE id = ?");
    $stmt->bind_param("i", $wisata_id);

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Tempat wisata berhasil dihapus.'];
    } else {
        $response = ['success' => false, 'message' => 'Gagal menghapus wisata: ' . $conn->error];
    }

    $stmt->close();
    $conn->close();

    // Return JSON response for AJAX requests
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If modal mode is requested, return only the form content
if (isset($_GET['modal']) && $_GET['modal'] === 'true') {
    ?>
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Hapus Tempat Wisata</h2>
    <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus tempat wisata <strong><?= htmlspecialchars($data['nama']); ?></strong>? Tindakan ini akan menghapus semua komentar dan pengaduan terkait.</p>
    <form method="POST">
      <button type="submit" class="btn-danger mb-3">
        <i class="fas fa-trash mr-2"></i> Hapus
      </button>
    </form>
    <a href="#" class="btn-secondary">
      <i class="fas fa-arrow-left mr-2"></i> Batal
    </a>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hapus Tempat Wisata - Sistem Informasi Pariwisata Banjarmasin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f7f9fc;
      margin: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .container {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      width: 100%;
    }
    .btn-danger {
      background-color: #ef4444;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 0.375rem;
      transition: background-color 0.2s;
      display: flex;
      justify-content: center;
    }
    .btn-danger:hover {
      background-color: #dc2626;
    }
    .btn-secondary {
      background-color: #6b7280;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 0.375rem;
      transition: background-color 0.2s;
      display: flex;
      justify-content: center;
    }
    .btn-secondary:hover {
      background-color: #4b5563;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Hapus Tempat Wisata</h2>
    <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus tempat wisata <strong><?= htmlspecialchars($data['nama']); ?></strong>? Tindakan ini akan menghapus semua komentar dan pengaduan terkait.</p>
    <form method="POST">
      <button type="submit" class="btn-danger w-full mb-3">
        <i class="fas fa-trash mr-2"></i> Hapus
      </button>
    </form>
    <a href="dashboard.php" class="btn-secondary w-full">
      <i class="fas fa-arrow-left mr-2"></i> Batal
    </a>
  </div>
</body>
</html>