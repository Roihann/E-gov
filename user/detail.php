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
  <!-- Animate On Scroll Library -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    /* Font Modern untuk Gen Z */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f0f4f8;
    }

    /* Header */
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

    /* Detail Wisata Card */
    .wisata-card {
      border: 2px solid transparent;
      background-clip: padding-box;
      border-radius: 20px;
      transition: all 0.3s ease;
    }
    .wisata-card:hover {
      border: 2px solid;
      border-image: linear-gradient(45deg, #a855f7, #3b82f6) 1;
      box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
    }

    /* Carousel */
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
      opacity: 0;
      transition: opacity 0.5s ease-in-out;
    }
    .carousel-item.active {
      opacity: 1;
    }
    .carousel-button {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: linear-gradient(90deg, #a855f7, #3b82f6);
      color: white;
      padding: 0.5rem;
      border: none;
      border-radius: 50%;
      cursor: pointer;
      z-index: 10;
      transition: all 0.3s ease;
    }
    .carousel-button:hover {
      transform: translateY(-50%) scale(1.1);
      box-shadow: 0 4px 12px rgba(168, 85, 247, 0.4);
    }
    .carousel-button.prev {
      left: 10px;
    }
    .carousel-button.next {
      right: 10px;
    }
    .carousel-dots {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 8px;
    }
    .carousel-dot {
      width: 10px;
      height: 10px;
      background-color: #d1d5db;
      border-radius: 50%;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .carousel-dot.active {
      background-color: #a855f7;
      transform: scale(1.2);
    }
    .wisata-image {
      max-height: 400px;
      object-fit: cover;
      width: 100%;
    }

    /* Detail Info */
    .detail-info {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #1f2937;
    }
    .detail-info i {
      color: #a855f7;
    }

    /* Komentar Card */
    .komentar-card {
      border: 2px solid transparent;
      background-clip: padding-box;
      border-radius: 20px;
      transition: all 0.3s ease;
    }
    .komentar-card:hover {
      border: 2px solid;
      border-image: linear-gradient(45deg, #a855f7, #3b82f6) 1;
      box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
    }

    /* Comment Card */
    .comment-card {
      transition: all 0.3s ease;
    }
    .comment-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    .comment-card .user-icon {
      background: linear-gradient(90deg, #f97316, #facc15);
      color: #1f2937;
    }
    .comment-card p {
      max-width: 60ch; /* Approx 60 characters */
      overflow-wrap: break-word; /* Ensure long words break */
      word-break: break-word; /* Additional breaking for very long strings */
    }

    /* Comment Form */
    .comment-textarea {
      transition: all 0.3s ease;
    }
    .comment-textarea:focus {
      transform: scale(1.02);
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
    }
    .comment-btn {
      background: linear-gradient(90deg, #a855f7, #3b82f6);
      transition: all 0.3s ease;
    }
    .comment-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(168, 85, 247, 0.4);
    }
    .report-btn {
      background: linear-gradient(90deg, #f97316, #facc15);
      transition: all 0.3s ease;
    }
    .report-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
    }

    /* Back Button */
    .back-btn {
      background: linear-gradient(90deg, #6b7280, #9ca3af);
      transition: all 0.3s ease;
    }
    .back-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
    }

    /* Peta Interaktif */
    #map {
        height: 200px;
        width: 100%;
        border-radius: 8px;
        margin-top: 0.5rem;
        border: 1px solid #d1d5db;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <nav class="text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <div class="flex items-center space-x-4">
        <img src="../assets/images/banjarmasin-logo.png" alt="Logo" class="h-10 w-auto">
        <h1 class="text-xl">Jelajah Banjarmasin</h1>
      </div>
      <div class="flex items-center space-x-4">
        <span class="text-sm">Hi, <?= htmlspecialchars($_SESSION['username']); ?></span>
        <a href="../auth/logout.php" class="logout-btn text-white">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container mx-auto py-8 px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Left Section: Attraction Details -->
      <div class="md:col-span-2">
        <div class="wisata-card bg-white p-6" data-aos="fade-up">
          <h2 class="text-2xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($data['nama']); ?></h2>
          <!-- Carousel -->
          <div class="carousel mb-4">
            <div class="carousel-inner" id="carouselInner">
              <?php foreach ($images as $index => $image): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                  <img src="../Uploads/<?= htmlspecialchars(trim($image)); ?>" class="wisata-image" alt="<?= htmlspecialchars($data['nama']); ?> Image <?= $index + 1; ?>">
                </div>
              <?php endforeach; ?>
            </div>
            <?php if (count($images) > 1): ?>
              <button class="carousel-button prev" onclick="moveSlide(-1)"><i class="fas fa-chevron-left"></i></button>
              <button class="carousel-button next" onclick="moveSlide(1)"><i class="fas fa-chevron-right"></i></button>
              <!-- Carousel Dots -->
              <div class="carousel-dots">
                <?php foreach ($images as $index => $image): ?>
                  <span class="carousel-dot <?= $index === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $index ?>)"></span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="space-y-4">
            <p class="detail-info"><i class="fas fa-map-marker-alt"></i> <strong>Alamat:</strong> <?= htmlspecialchars($data['alamat']); ?></p>
            <p class="detail-info"><i class="fas fa-info-circle"></i> <strong>Deskripsi:</strong> <?= htmlspecialchars($data['deskripsi']); ?></p>
            <p class="detail-info"><i class="fas fa-map"></i> <strong>Lokasi:</strong></p>
            <div id="map" data-lat="<?= isset($data['latitude']) ? htmlspecialchars($data['latitude']) : '0.0'; ?>" data-lng="<?= isset($data['longitude']) ? htmlspecialchars($data['longitude']) : '0.0'; ?>"></div>
          </div>
          <a href="index.php" class="back-btn inline-block mt-6 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Tempat Wisata
          </a>
        </div>
      </div>

      <!-- Right Section: Comments -->
      <div class="md:col-span-1">
        <div class="komentar-card bg-white p-6" data-aos="fade-up" data-aos-delay="200">
          <h3 class="text-xl font-semibold text-gray-800 mb-4">Komentar</h3>
          <div class="space-y-4 max-h-96 overflow-y-auto mb-6">
            <?php if (mysqli_num_rows($komentar) > 0): ?>
              <?php while ($k = mysqli_fetch_assoc($komentar)): ?>
                <div class="comment-card bg-gray-50 p-4 rounded-lg">
                  <div class="flex items-center mb-2">
                    <div class="w-10 h-10 user-icon rounded-full flex items-center justify-center mr-3">
                      <i class="fas fa-user"></i>
                    </div>
                    <div>
                      <strong class="text-gray-800"><?= htmlspecialchars($k['username']); ?></strong>
                      <p class="text-sm text-gray-500"><?= htmlspecialchars($k['created_at']); ?></p>
                    </div>
                  </div>
                  <p class="text-gray-700 break-words"><?= htmlspecialchars($k['isi_komentar']); ?></p>
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
                class="comment-textarea w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
                placeholder="Tulis komentar..." 
                rows="4" 
                required
              ></textarea>
            </div>
            <button 
              type="submit" 
              class="comment-btn w-full text-white p-3 rounded-lg"
            >
              Kirim Komentar
            </button>
          </form>

          <!-- Report Button -->
          <a 
            href="pengaduan.php?id=<?= $wisata_id; ?>" 
            class="report-btn block mt-4 w-full text-white p-3 rounded-lg text-center"
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
    const dots = document.querySelectorAll('.carousel-dot');
    const totalSlides = slides.length;

    function updateCarousel() {
      slides.forEach((slide, index) => {
        slide.classList.remove('active');
        if (index === currentSlide) {
          slide.classList.add('active');
        }
      });
      dots.forEach((dot, index) => {
        dot.classList.remove('active');
        if (index === currentSlide) {
          dot.classList.add('active');
        }
      });
      const carouselInner = document.getElementById('carouselInner');
      carouselInner.style.transform = `translateX(-${currentSlide * 100}%)`;
    }

    function moveSlide(direction) {
      currentSlide += direction;
      if (currentSlide >= totalSlides) {
        currentSlide = 0;
      } else if (currentSlide < 0) {
        currentSlide = totalSlides - 1;
      }
      updateCarousel();
    }

    function goToSlide(index) {
      currentSlide = index;
      updateCarousel();
    }

    // Auto-slide every 5 seconds
    setInterval(() => {
      moveSlide(1);
    }, 5000);

    // Initialize carousel
    updateCarousel();
  </script>

  <!-- AOS Script -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 800,
      once: true,
    });
  </script>

  <!-- Leaflet Map Initialization -->
  <script>
      document.addEventListener('DOMContentLoaded', function () {
          const mapContainer = document.getElementById('map');
          if (mapContainer) {
              const lat = parseFloat(mapContainer.getAttribute('data-lat'));
              const lng = parseFloat(mapContainer.getAttribute('data-lng'));

              // Validasi koordinat
              if (isNaN(lat) || isNaN(lng) || lat === 0.0 || lng === 0.0) {
                  mapContainer.innerHTML = '<p class="text-gray-600">Lokasi tidak tersedia.</p>';
                  return;
              }

              // Inisialisasi peta
              const map = L.map('map').setView([lat, lng], 15);

              // Tambahkan tile layer dari OpenStreetMap
              L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
              }).addTo(map);

              // Tambahkan marker
              L.marker([lat, lng]).addTo(map)
                  .bindPopup('<?= htmlspecialchars($data['nama']); ?>')
                  .openPopup();
          }
      });
  </script>
</body>
</html>