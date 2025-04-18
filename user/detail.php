<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$wisata_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data tempat wisata
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
if (!$wisata || mysqli_num_rows($wisata) == 0) {
    die("Tempat wisata tidak ditemukan.");
}
$data = mysqli_fetch_assoc($wisata);

// Ambil komentar
$komentar = mysqli_query($conn, "
    SELECT komentar.isi_komentar, komentar.created_at, users.username 
    FROM komentar 
    JOIN users ON komentar.user_id = users.id 
    WHERE komentar.wisata_id = $wisata_id 
    ORDER BY komentar.created_at DESC
");
if (!$komentar) {
    die("Query komentar gagal: " . mysqli_error($conn));
}

// Parse multiple images from foto column (assuming comma-separated)
$images = !empty($data['foto']) ? explode(',', $data['foto']) : ['default.jpg'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Tempat Wisata - <?= htmlspecialchars($data['nama']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f7f9fc;
    }
    .comment-card {
      transition: transform 0.3s;
    }
    .comment-card:hover {
      transform: translateY(-2px);
    }
    .wisata-image {
      max-height: 400px;
      object-fit: cover;
      width: 100%;
    }
    .carousel {
      position: relative;
      overflow: hidden;
      border-radius: 0.5rem;
    }
    .carousel-inner {
      display: flex;
      transition: transform 0.5s ease-in-out;
    }
    .carousel-item {
      min-width: 100%;
      transition: opacity 0.5s ease-in-out;
    }
    .carousel-button {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0, 0, 0, 0.5);
      color: white;
      padding: 0.5rem;
      border: none;
      cursor: pointer;
      z-index: 10;
    }
    .carousel-button.prev {
      left: 10px;
    }
    .carousel-button.next {
      right: 10px;
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
  <div class="container mx-auto py-8 px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Left Section: Attraction Details -->
      <div class="md:col-span-2">
        <div class="bg-white rounded-xl shadow-md p-6">
          <h2 class="text-2xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($data['nama']); ?></h2>
          <!-- Carousel -->
          <div class="carousel mb-4">
            <div class="carousel-inner" id="carouselInner">
              <?php foreach ($images as $index => $image): ?>
                <div class="carousel-item">
                  <img src="../Uploads/<?= htmlspecialchars(trim($image)); ?>" class="wisata-image" alt="<?= htmlspecialchars($data['nama']); ?> Image <?= $index + 1; ?>">
                </div>
              <?php endforeach; ?>
            </div>
            <?php if (count($images) > 1): ?>
              <button class="carousel-button prev" onclick="moveSlide(-1)"><i class="fas fa-chevron-left"></i></button>
              <button class="carousel-button next" onclick="moveSlide(1)"><i class="fas fa-chevron-right"></i></button>
            <?php endif; ?>
          </div>
          <div class="space-y-4">
            <p><strong class="text-gray-700">Alamat:</strong> <?= htmlspecialchars($data['alamat']); ?></p>
            <p><strong class="text-gray-700">Deskripsi:</strong> <?= htmlspecialchars($data['deskripsi']); ?></p>
          </div>
          <a href="index.php" class="inline-block mt-6 bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Tempat Wisata
          </a>
        </div>
      </div>

      <!-- Right Section: Comments -->
      <div class="md:col-span-1">
        <div class="bg-white rounded-xl shadow-md p-6">
          <h3 class="text-xl font-semibold text-gray-800 mb-4">Komentar</h3>
          <div class="space-y-4 max-h-96 overflow-y-auto mb-6">
            <?php if (mysqli_num_rows($komentar) > 0): ?>
              <?php while ($k = mysqli_fetch_assoc($komentar)): ?>
                <div class="comment-card bg-gray-50 p-4 rounded-lg">
                  <div class="flex items-center mb-2">
                    <div class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center mr-3">
                      <i class="fas fa-user"></i>
                    </div>
                    <div>
                      <strong class="text-gray-800"><?= htmlspecialchars($k['username']); ?></strong>
                      <p class="text-sm text-gray-500"><?= htmlspecialchars($k['created_at']); ?></p>
                    </div>
                  </div>
                  <p class="text-gray-700"><?= htmlspecialchars($k['isi_komentar']); ?></p>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <p class="text-gray-600">Belum ada komentar.</p>
            <?php endif; ?>
          </div>

          <!-- Comment Form -->
          <form action="../proses/komentar_proses.php" method="POST">
            <input type="hidden" name="wisata_id" value="<?= $wisata_id; ?>">
            <div class="mb-4">
              <textarea 
                name="isi_komentar" 
                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
                placeholder="Tulis komentar..." 
                rows="4" 
                required
              ></textarea>
            </div>
            <button 
              type="submit" 
              class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-all"
            >
              Kirim Komentar
            </button>
          </form>

          <!-- Report Button -->
          <a 
            href="pengaduan.php?id=<?= $wisata_id; ?>" 
            class="block mt-4 w-full bg-yellow-500 text-white p-3 rounded-lg hover:bg-yellow-600 transition-all text-center"
          >
            <i class="fas fa-exclamation-triangle mr-2"></i> Laporkan Pengaduan
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript for Carousel -->
  <script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-item');
    const totalSlides = slides.length;

    function moveSlide(direction) {
      currentSlide += direction;
      if (currentSlide >= totalSlides) {
        currentSlide = 0;
      } else if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
      }
      updateCarousel();
    }

    function updateCarousel() {
      const carouselInner = document.getElementById('carouselInner');
      carouselInner.style.transform = `translateX(-${currentSlide * 100}%)`;
    }

    // Auto-slide every 5 seconds (optional)
    setInterval(() => {
      moveSlide(1);
    }, 5000);
  </script>
</body>
</html>