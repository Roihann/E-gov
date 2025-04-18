<?php
session_start();
include '../config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Tangkap keyword pencarian jika ada
$keyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query berdasarkan keyword atau semua data
if (!empty($keyword)) {
    $wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE nama LIKE '%$keyword%'");
} else {
    $wisata = mysqli_query($conn, "SELECT * FROM wisata");
}

if (!$wisata) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jelajah Banjarmasin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    .hero-section {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/images/menara pandang_index.jpg') no-repeat center center/cover;
      height: 400px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: white;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }
    .wisata-card {
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .wisata-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }
    .wisata-card img {
      transition: transform 0.3s;
    }
    .wisata-card:hover img {
      transform: scale(1.05);
    }
  </style>
</head>
<body class="bg-gray-50">
  <!-- Header -->
  <nav class="bg-blue-600 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <div class="flex items-center space-x-4">
        <img src="../assets/images/banjarmasin-logo.png" alt="Logo" class="h-10 w-auto">
        <h1 class="text-xl font-bold">Jelajah Banjarmasin</h1>
      </div>
      <div class="flex items-center space-x-6">
        <!-- Search Form -->
        <form action="" method="GET" class="relative w-80">
          <input 
            type="text" 
            name="search" 
            value="<?= htmlspecialchars($keyword) ?>" 
            placeholder="Cari Nama Destinasi" 
            class="w-full px-4 py-2 rounded-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all"
          >
          <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2">
            <i class="fas fa-search text-gray-500"></i>
          </button>
        </form>
        <!-- User Info and Logout -->
        <div class="flex items-center space-x-4">
          <span class="text-sm">Hi, <?= htmlspecialchars($_SESSION['username']); ?></span>
          <a href="../auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-all">Logout</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <div class="hero-section">
    <h2 class="text-4xl md:text-5xl font-bold uppercase mb-4">Ayo Jelajahi Objek Wisata Di Banjarmasin</h2>
    <img src="../assets/images/banjarmasin-logo.png" alt="Banjarmasin Logo" class="w-40 h-auto drop-shadow-md">
  </div>

  <!-- Grid of Tourist Attractions -->
  <div class="container mx-auto py-12 px-4">
    <?php if (!empty($keyword)): ?>
      <p class="mb-6 text-lg text-gray-700">Hasil pencarian untuk: <strong><?= htmlspecialchars($keyword) ?></strong></p>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php if (mysqli_num_rows($wisata) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($wisata)): ?>
          <a href="detail.php?id=<?= $row['id']; ?>" class="wisata-card bg-white rounded-xl shadow-md overflow-hidden">
            <img src="../Uploads/<?= $row['foto']; ?>" alt="<?= $row['nama']; ?>" class="w-full h-48 object-cover">
            <div class="p-4 text-center">
              <h5 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($row['nama']); ?></h5>
            </div>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-span-full text-center text-gray-600 bg-white p-6 rounded-lg shadow-md">
          Tidak ditemukan tempat wisata dengan nama tersebut.
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>