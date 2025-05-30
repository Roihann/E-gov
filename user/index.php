<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Pagination settings
$items_per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

$keyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Count total records for pagination
if (!empty($keyword)) {
    $count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM wisata WHERE nama LIKE '%$keyword%'");
} else {
    $count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM wisata");
}
$count_result = mysqli_fetch_assoc($count_query);
$total_items = $count_result['total'];
$total_pages = ceil($total_items / $items_per_page);

// Fetch data with LIMIT and OFFSET for pagination
if (!empty($keyword)) {
    $wisata = mysqli_query($conn, "SELECT id, nama, foto, kecamatan FROM wisata WHERE nama LIKE '%$keyword%' LIMIT $items_per_page OFFSET $offset");
} else {
    $wisata = mysqli_query($conn, "SELECT id, nama, foto, kecamatan FROM wisata LIMIT $items_per_page OFFSET $offset");
}

if (!$wisata) {
    die("Query failed: " . mysqli_error($conn));
}

// Pagination display settings
$max_visible_pages = 5; // Maximum number of page buttons to show
$start_page = max(1, $page - 2);
$end_page = min($total_pages, $page + 2);

// Adjust start and end to always show 5 pages if possible
if ($end_page - $start_page + 1 < $max_visible_pages) {
    if ($start_page == 1) {
        $end_page = min($total_pages, $start_page + $max_visible_pages - 1);
    } else {
        $start_page = max(1, $end_page - $max_visible_pages + 1);
    }
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
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f0f4f8;
    }

    nav {
      background: linear-gradient(90deg, #4f46e5, #a855f7);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    nav h1 {
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      background: linear-gradient(90deg, #ffffff, #e0e7ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    nav .search-input {
      background-color: #ffffff;
      border-radius: 50px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }
    nav .search-input:focus {
      box-shadow: 0 2px 12px rgba(79, 70, 229, 0.3);
    }
    nav .logout-btn {
      background: linear-gradient(90deg, #ef4444, #f87171);
      border-radius: 50px;
      padding: 8px 20px;
      transition: all 0.3s ease;
    }
    nav .logout-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .hero-section {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/images/menara pandang_index.jpg') no-repeat center center/cover;
      height: 500px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: white;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
      position: relative;
      background-attachment: fixed;
    }
    .hero-section h2 {
      font-size: 3rem;
      font-weight: 700;
      letter-spacing: 2px;
      color: #ffffff;
      animation: fadeIn 1.5s ease-in-out;
    }
    .hero-section img {
      animation: fadeIn 2s ease-in-out;
    }
    .hero-section .cta-btn {
      margin-top: 20px;
      background: linear-gradient(90deg, #f97316, #facc15);
      color: #1f2937;
      font-weight: 600;
      padding: 12px 24px;
      border-radius: 50px;
      transition: all 0.3s ease;
    }
    .hero-section .cta-btn:hover {
      transform: scale(1.1);
      box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .wisata-card {
      position: relative;
      background: white;
      border-radius: 20px;
      overflow: hidden;
      transition: all 0.3s ease;
      border: 2px solid transparent;
      background-clip: padding-box;
    }
    .wisata-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
      border: 2px solid;
      border-image: linear-gradient(45deg, #a855f7, #3b82f6) 1;
    }
    .wisata-card img {
      transition: transform 0.3s ease;
    }
    .wisata-card:hover img {
      transform: scale(1.1);
    }
    .wisata-card .badge {
      position: absolute;
      top: 10px;
      left: 10px;
      background: linear-gradient(90deg, #f97316, #facc15);
      color: #1f2937;
      font-size: 12px;
      font-weight: 600;
      padding: 4px 12px;
      border-radius: 20px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    .wisata-card .content {
      padding: 16px;
      text-align: center;
    }
    .wisata-card h5 {
      font-size: 1.25rem;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 8px;
    }
    .wisata-card .location {
      display: flex;
      align-items: center;
      justify-content: center;
      color: #6b7280;
      font-size: 0.9rem;
    }
    .wisata-card .location i {
      margin-right: 6px;
      color: #a855f7;
    }

    /* Pagination Styles */
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 2rem;
      gap: 0.5rem;
    }
    .pagination a, .pagination span {
      padding: 0.5rem 1rem;
      border-radius: 50px;
      font-size: 1rem;
      transition: all 0.3s ease;
      text-decoration: none;
    }
    .pagination a {
      background: linear-gradient(90deg, #4f46e5, #a855f7);
      color: white;
    }
    .pagination a:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    .pagination span.active {
      background: linear-gradient(90deg, #f97316, #facc15);
      color: #1f2937;
      font-weight: 600;
    }
    .pagination .disabled {
      background: #d1d5db;
      color: #6b7280;
      pointer-events: none;
    }
    .pagination .dots {
      padding: 0.5rem;
      color: #6b7280;
    }
  </style>
</head>
<body class="bg-gray-50">
  <!-- Header -->
  <nav class="text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <div class="flex items-center space-x-4">
        <img src="../assets/images/banjarmasin-logo.png" alt="Logo" class="h-10 w-auto">
        <h1 class="text-xl">Jelajah Banjarmasin</h1>
      </div>
      <div class="flex items-center space-x-6">
        <form action="" method="GET" class="relative w-80">
          <input 
            type="text" 
            name="search" 
            value="<?= htmlspecialchars($keyword) ?>" 
            placeholder="Cari Nama Destinasi" 
            class="search-input w-full px-4 py-2 text-gray-800 focus:outline-none"
          >
          <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2">
            <i class="fas fa-search text-gray-500"></i>
          </button>
        </form>
        <div class="flex items-center space-x-4">
          <span class="text-sm">Hi, <?= htmlspecialchars($_SESSION['username']); ?></span>
          <a href="../auth/logout.php" class="logout-btn text-white">Logout</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <div class="hero-section">
    <h2 class="text-4xl md:text-5xl mb-4">Ayo Jelajahi Objek Wisata di Banjarmasin</h2>
    <img src="../assets/images/banjarmasin-logo.png" alt="Banjarmasin Logo" class="w-40 h-auto drop-shadow-md">
    <a href="#wisata" class="cta-btn">Mulai Jelajah Sekarang</a>
  </div>

  <!-- Grid of Tourist Attractions -->
  <div id="wisata" class="container mx-auto py-12 px-4">
    <?php if (!empty($keyword)): ?>
      <p class="mb-6 text-lg text-gray-700" data-aos="fade-up">Hasil pencarian untuk: <strong><?= htmlspecialchars($keyword) ?></strong></p>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php if (mysqli_num_rows($wisata) > 0): ?>
        <?php $index = 0; ?>
        <?php while ($row = mysqli_fetch_assoc($wisata)): ?>
          <?php
          $fotos = !empty($row['foto']) ? explode(',', $row['foto']) : ['default.jpg'];
          $cover_image = trim($fotos[0]);
          $index++;
          ?>
          <a href="detail.php?id=<?= $row['id']; ?>" class="wisata-card" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">        
            <img src="../Uploads/<?= htmlspecialchars($cover_image); ?>" alt="<?= htmlspecialchars($row['nama']); ?>" class="w-full h-48 object-cover">
            <div class="content">
              <h5><?= htmlspecialchars($row['nama']); ?></h5>
              <p class="location">
                <i class="fas fa-map-marker-alt"></i>
                <?= htmlspecialchars($row['kecamatan']); ?>
              </p>
            </div>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-span-full text-center text-gray-600 bg-white p-6 rounded-lg shadow-md" data-aos="fade-up">
          Tidak ditemukan tempat wisata dengan nama tersebut.
        </div>
      <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (mysqli_num_rows($wisata) > 0 && $total_pages > 1): ?>
      <div class="pagination">
        <!-- First Button -->
        <?php if ($page > 1): ?>
          <a href="?page=1&search=<?= htmlspecialchars($keyword) ?>">First</a>
        <?php else: ?>
          <span class="disabled">First</span>
        <?php endif; ?>

        <!-- Previous Button -->
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($keyword) ?>">Previous</a>
        <?php else: ?>
          <span class="disabled">Previous</span>
        <?php endif; ?>

        <!-- Dots Before -->
        <?php if ($start_page > 1): ?>
          <span class="dots">...</span>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
          <?php if ($i == $page): ?>
            <span class="active"><?= $i ?></span>
          <?php else: ?>
            <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($keyword) ?>"><?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <!-- Dots After -->
        <?php if ($end_page < $total_pages): ?>
          <span class="dots">...</span>
        <?php endif; ?>

        <!-- Next Button -->
        <?php if ($page < $total_pages): ?>
          <a href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($keyword) ?>">Next</a>
        <?php else: ?>
          <span class="disabled">Next</span>
        <?php endif; ?>

        <!-- Last Button -->
        <?php if ($page < $total_pages): ?>
          <a href="?page=<?= $total_pages ?>&search=<?= htmlspecialchars($keyword) ?>">Last</a>
        <?php else: ?>
          <span class="disabled">Last</span>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- AOS Script -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 800,
      once: true,
    });
  </script>
</body>
</html>