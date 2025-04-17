<?php
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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Sistem Informasi Pariwisata Banjarmasin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f7f9fc;
    }
    .sidebar {
      background: linear-gradient(to bottom, #3b82f6, #2563eb);
      color: white;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      padding-top: 1rem;
    }
    .sidebar a {
      color: white;
      padding: 0.75rem 1.5rem;
      display: block;
      transition: background 0.3s;
    }
    .sidebar a:hover {
      background: rgba(255, 255, 255, 0.1);
    }
    .content {
      margin-left: 250px;
      padding: 2rem;
    }
    .table-container {
      overflow-x: auto;
    }
    .table th, .table td {
      white-space: nowrap;
    }
    .table tbody tr:hover {
      background-color: #f1f5f9;
    }
    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
        transform: translateX(-100%);
        transition: transform 0.3s;
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="p-4 text-xl font-bold border-b border-blue-500">
      Admin Panel
    </div>
    <a href="#" class="flex items-center"><i class="fas fa-home mr-2"></i> Dashboard</a>
    <a href="tambah_wisata.php" class="flex items-center"><i class="fas fa-plus mr-2"></i> Tambah Wisata</a>
    <a href="#" class="flex items-center"><i class="fas fa-star mr-2"></i> Komentar</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div class="flex items-center">
        <button class="md:hidden mr-4 text-gray-600" onclick="toggleSidebar()">
          <i class="fas fa-bars text-2xl"></i>
        </button>
        <h1 class="text-2xl font-semibold text-gray-800">Admin Dashboard</h1>
      </div>
      <div class="flex items-center">
        <span class="mr-4 text-gray-600">Hi, <?= $_SESSION['username']; ?></span>
        <a href="../auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-all">Logout</a>
      </div>
    </div>

    <!-- Stats Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-medium text-gray-700">Jumlah Tempat Wisata</h3>
        <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo mysqli_num_rows($wisata); ?></p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-medium text-gray-700">Pengunjung Minggu Ini</h3>
        <p class="text-3xl font-bold text-blue-600 mt-2">3,298</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-medium text-gray-700">Komentar Baru</h3>
        <p class="text-3xl font-bold text-blue-600 mt-2">15</p>
      </div>
    </div>

    <!-- Table of Tourist Attractions -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Tempat Wisata</h2>
        <a href="tambah_wisata.php" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-all">Tambah Tempat Wisata</a>
      </div>
      <div class="table-container">
        <table class="table w-full">
          <thead>
            <tr class="bg-gray-100">
              <th class="p-3 text-left text-gray-700">#</th>
              <th class="p-3 text-left text-gray-700">Nama Wisata</th>
              <th class="p-3 text-left text-gray-700">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($wisata) > 0): ?>
              <?php 
              mysqli_data_seek($wisata, 0); // Reset pointer to the beginning
              while ($row = mysqli_fetch_assoc($wisata)): 
              ?>
                <tr>
                  <td class="p-3 text-gray-600"><?= $row['id']; ?></td>
                  <td class="p-3 text-gray-600"><?= $row['nama']; ?></td>
                  <td class="p-3">
                    <a href="lihat_komentar.php?id=<?= $row['id']; ?>" class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 transition-all mr-2">Lihat Komentar</a>
                    <a href="edit_wisata.php?id=<?= $row['id']; ?>" class="bg-yellow-500 text-white px-3 py-1 rounded-lg hover:bg-yellow-600 transition-all mr-2">Edit</a>
                    <a href="hapus_wisata.php?id=<?= $row['id']; ?>" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition-all" onclick="return confirm('Apakah Anda yakin ingin menghapus tempat wisata ini?')">Hapus</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="p-3 text-center text-gray-600">Tidak ada data tempat wisata.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('open');
    }
  </script>
</body>
</html>