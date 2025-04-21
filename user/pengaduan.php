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
  <!-- Animate On Scroll Library -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    /* Font Modern untuk Gen Z */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f0f4f8;
    }

    /* Header */
    nav {
      background: linear-gradient(90deg, #4f46e5, #a855f7); /* Gradient Indigo ke Purple */
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
      background: linear-gradient(90deg, #ef4444, #f87171); /* Gradient Merah */
      border-radius: 50px;
      padding: 8px 20px;
      transition: all 0.3s ease;
    }
    nav .logout-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    /* Pengaduan Card */
    .pengaduan-card {
      border: 2px solid transparent;
      background-clip: padding-box;
      border-radius: 20px;
      transition: all 0.3s ease;
    }
    .pengaduan-card:hover {
      border: 2px solid;
      border-image: linear-gradient(45deg, #a855f7, #3b82f6) 1;
      box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
      transform: translateY(-5px);
    }

    /* Form Elements */
    .pengaduan-textarea {
      transition: all 0.3s ease;
    }
    .pengaduan-textarea:focus {
      transform: scale(1.02);
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
    }
    .submit-btn {
      background: linear-gradient(90deg, #a855f7, #3b82f6); /* Gradient Purple ke Blue */
      transition: all 0.3s ease;
    }
    .submit-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(168, 85, 247, 0.4);
    }
    .back-btn {
      background: linear-gradient(90deg, #6b7280, #9ca3af); /* Gradient Abu-abu */
      transition: all 0.3s ease;
    }
    .back-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(107, 114, 128, 0.4);
    }

    /* Info Section */
    .info-section {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #1f2937;
    }
    .info-section i {
      color: #a855f7; /* Purple untuk ikon */
    }

    /* Pop-up Validation */
    .popup-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6); /* Overlay gelap */
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .popup-overlay.active {
      display: flex;
      opacity: 1;
    }
    .popup-content {
      background: white;
      padding: 2rem;
      border-radius: 20px;
      text-align: center;
      position: relative;
      transform: scale(0.8);
      opacity: 0;
      transition: transform 0.5s ease, opacity 0.5s ease;
    }
    .popup-overlay.active .popup-content {
      transform: scale(1);
      opacity: 1;
    }
    .checkmark {
      font-size: 4rem;
      color: #a855f7; /* Purple */
      margin-bottom: 1rem;
      display: inline-block;
      animation: scaleCheck 0.5s ease forwards;
    }
    @keyframes scaleCheck {
      0% { transform: scale(0); }
      70% { transform: scale(1.2); }
      100% { transform: scale(1); }
    }
    .popup-content p {
      font-size: 1.25rem;
      font-weight: 600;
      color: #1f2937;
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
  <div class="container mx-auto py-12 px-4">
    <div class="max-w-2xl mx-auto">
      <div class="pengaduan-card bg-white p-6" data-aos="fade-up">
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
          <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i> Laporkan Pengaduan
        </h2>
        <p class="info-section mb-6">
          <i class="fas fa-map-marker-alt"></i>
          <strong>Tempat Wisata:</strong> <?= htmlspecialchars($data['nama']); ?>
        </p>

        <form id="pengaduanForm" action="../proses/pengaduan_proses.php" method="POST">
          <input type="hidden" name="wisata_id" value="<?= $wisata_id; ?>">
          <div class="mb-4">
            <textarea 
              name="isi_pengaduan" 
              class="pengaduan-textarea w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
              placeholder="Tulis pengaduan Anda (misalnya: fasilitas rusak, kebersihan kurang, dll.)" 
              rows="6" 
              required
            ></textarea>
          </div>
          <button 
            type="submit" 
            class="submit-btn w-full text-white p-3 rounded-lg flex items-center justify-center"
          >
            <i class="fas fa-paper-plane mr-2"></i> Kirim Pengaduan
          </button>
        </form>

        <a 
          href="detail.php?id=<?= $wisata_id; ?>" 
          class="back-btn inline-block mt-4 w-full text-white p-3 rounded-lg text-center"
        >
          <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail Wisata
        </a>
      </div>
    </div>
  </div>

  <!-- Pop-up Validation -->
  <div class="popup-overlay" id="popupOverlay">
    <div class="popup-content">
      <i class="fas fa-check-circle checkmark"></i>
      <p>Pengaduan Berhasil Dikirim!</p>
    </div>
  </div>

  <!-- JavaScript for Form Submission and Pop-up -->
  <script>
    const form = document.getElementById('pengaduanForm');
    const popupOverlay = document.getElementById('popupOverlay');

    form.addEventListener('submit', async (e) => {
      e.preventDefault(); // Mencegah form submit langsung

      const formData = new FormData(form);
      try {
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData,
        });
        const result = await response.json();

        if (result.status === 'success') {
          // Tampilkan pop-up
          popupOverlay.classList.add('active');

          // Redirect setelah 2 detik
          setTimeout(() => {
            window.location.href = `detail.php?id=<?= $wisata_id; ?>`;
          }, 2000);
        } else {
          alert(result.message || 'Gagal mengirim pengaduan');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim pengaduan');
      }
    });
  </script>

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