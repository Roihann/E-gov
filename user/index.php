<?php
session_start();
include '../config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch tourist attractions
$wisata = mysqli_query($conn, "SELECT * FROM wisata");
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
      background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../assets/images/collage-image.png') no-repeat center center/cover;
      height: 400px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: white;
    }
    .wisata-card img {
      transition: transform 0.3s;
    }
    .wisata-card:hover img {
      transform: scale(1.05);
    }
  </style>
</head>
<body class="bg-gray-100">
  <!-- Header -->
  <nav class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-2xl font-bold">Jelajah Banjarmasin</h1>
      <div class="flex items-center space-x-4">
        <!-- Category Dropdown -->
        <div class="relative">
          <button class="bg-blue-500 px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
            Kategori <i class="fas fa-chevron-down ml-2"></i>
          </button>
          <div class="absolute hidden mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-lg">
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Wisata Air Terjun</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Wisata Alam dan Buatan</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Wisata Budaya</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Wisata Gunung</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Wisata Pantai</a>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">Wisata Religi</a>
          </div>
        </div>
        <!-- Search Bar -->
        <div class="relative">
          <input 
            type="text" 
            placeholder="Nama Destinasi" 
            class="px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
          <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
        </div>
        <!-- Logout -->
        <div class="flex items-center space-x-2">
          <span>Hi, <?= $_SESSION['username']; ?></span>
          <a href="../auth/logout.php" class="bg-red-500 px-4 py-2 rounded-lg hover:bg-red-600">Logout</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <div class="hero-section">
    <h2 class="text-4xl font-bold uppercase mb-4">Ayo Jelajahi Objek Wisata Di Banjarmasin</h2>
    <img src="../assets/images/banjarmasin-logo.png" alt="Banjarmasin Logo" class="w-32">
  </div>

  <!-- Grid of Tourist Attractions -->
  <div class="container mx-auto py-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php if (mysqli_num_rows($wisata) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($wisata)): ?>
          <a href="detail.php?id=<?= $row['id']; ?>" class="wisata-card bg-white rounded-lg shadow-md overflow-hidden">
            <img src="../uploads/<?= $row['foto']; ?>" alt="<?= $row['nama']; ?>" class="w-full h-48 object-cover rounded-t-lg">
            <div class="p-4 text-center">
              <h5 class="text-lg font-semibold text-gray-800"><?= $row['nama']; ?></h5>
            </div>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-span-full text-center text-gray-600">
          Tidak ada data tempat wisata.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Toggle dropdown visibility
    const dropdownButton = document.querySelector('nav .relative button');
    const dropdownMenu = document.querySelector('nav .relative .absolute');
    dropdownButton.addEventListener('click', () => {
      dropdownMenu.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownMenu.classList.add('hidden');
      }
    });
  </script>
</body>
</html>