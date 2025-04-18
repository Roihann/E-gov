<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$wisata_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil info wisata untuk ditampilkan
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
if (!$wisata || mysqli_num_rows($wisata) == 0) {
    die("Tempat wisata tidak ditemukan.");
}
$data = mysqli_fetch_assoc($wisata);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporkan Pengaduan - <?= htmlspecialchars($data['nama']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f7f9fc;
    }
    .pengaduan-card {
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .pengaduan-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>
  <!-- Header -->
  <nav class="bg-blue-600 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <div class="flex items-center space-x-4">
        <img src="../assets/images/banjarmasin-logo.png" alt="Logo" class="h-10 w-auto">
        <h1 class="text-xl font-bold">Jelajah Banjarmasin</h1>
      </div>
      <div class="flex items-center space-x-4">
        <span class="text-sm">Hi, <?= htmlspecialchars($_SESSION['username']); ?></span>
        <a href="../auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-all">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container mx-auto py-12 px-4">
    <div class="max-w-2xl mx-auto">
      <div class="pengaduan-card bg-white rounded-xl shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Laporkan Pengaduan</h2>
        <p class="mb-6 text-gray-700">
          <strong>Tempat Wisata:</strong> <?= htmlspecialchars($data['nama']); ?>
        </p>

        <form action="../proses/pengaduan_proses.php" method="POST">
          <input type="hidden" name="wisata_id" value="<?= $wisata_id; ?>">
          <div class="mb-4">
            <textarea 
              name="isi_pengaduan" 
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
              placeholder="Tulis pengaduan Anda (misalnya: fasilitas rusak, kebersihan kurang, dll.)" 
              rows="6" 
              required
            ></textarea>
          </div>
          <button 
            type="submit" 
            class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-all flex items-center justify-center"
          >
            <i class="fas fa-paper-plane mr-2"></i> Kirim Pengaduan
          </button>
        </form>

        <a 
          href="detail.php?id=<?= $wisata_id; ?>" 
          class="inline-block mt-4 w-full bg-gray-500 text-white p-3 rounded-lg hover:bg-gray-600 transition-all text-center"
        >
          <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail Wisata
        </a>
      </div>
    </div>
  </div>
</body>
</html>