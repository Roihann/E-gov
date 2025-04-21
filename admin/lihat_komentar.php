<?php
session_start();
include '../config/db.php';

// Periksa apakah user sudah login dan memiliki role 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Pastikan 'id' ada dalam parameter URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID wisata tidak valid.";
    exit;
}

$wisata_id = (int)$_GET['id'];

// Ambil data tempat wisata berdasarkan ID
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
if (!$wisata || mysqli_num_rows($wisata) == 0) {
    echo "Tempat wisata tidak ditemukan.";
    exit;
}
$data_wisata = mysqli_fetch_assoc($wisata);

// Ambil komentar dan pengaduan
$komentar = mysqli_query($conn, "
    SELECT komentar.isi_komentar, komentar.created_at, users.username, komentar.id as komentar_id 
    FROM komentar 
    JOIN users ON komentar.user_id = users.id 
    WHERE komentar.wisata_id = $wisata_id
    ORDER BY komentar.created_at DESC
");

$pengaduan = mysqli_query($conn, "
    SELECT pengaduan.isi_pengaduan, pengaduan.created_at, users.username, pengaduan.id as pengaduan_id 
    FROM pengaduan 
    JOIN users ON pengaduan.user_id = users.id 
    WHERE pengaduan.wisata_id = $wisata_id
    ORDER BY pengaduan.created_at DESC
");

// Handle success or error messages from session
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';

// Clear session messages to prevent persistence on refresh
unset($_SESSION['success']);
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Komentar & Pengaduan - <?= htmlspecialchars($data_wisata['nama']); ?> - Sistem Informasi Pariwisata Banjarmasin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: url('../assets/images/Budaya-Menara-Pandang-Banjarmasin.jpg') no-repeat center center/cover;
      position: relative;
      min-height: 100vh;
      margin: 0;
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
      transition: transform 0.3s;
    }
    .sidebar a {
      color: white;
      padding: 0.75rem 1.5rem;
      display: flex;
      align-items: center;
      transition: background 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
      background: rgba(255, 255, 255, 0.2);
    }
    .content {
      margin-left: 250px;
      padding: 2rem;
      min-height: 100vh;
    }
    .card-container {
      animation: fadeIn 1s ease-in-out;
      background: rgba(255, 255, 255, 1);
      max-width: 800px;
      width: 100%;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      margin: 0 auto;
    }
    .comment-card, .complaint-card {
      background: #f9fafb;
      padding: 1rem;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
      transition: transform 0.2s;
    }
    .comment-card:hover, .complaint-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    .comment-card p, .complaint-card p {
      max-width: 60ch; /* Approx 60 characters */
      overflow-wrap: break-word; /* Ensure long words break */
      word-break: break-word; /* Additional breaking for very long strings */
    }
    .delete-btn {
      background-color: #ef4444;
      color: white;
      border-radius: 0.375rem;
      padding: 0.5rem 1rem;
      transition: background-color 0.2s;
    }
    .delete-btn:hover {
      background-color: #dc2626;
    }
    .success-message {
      background-color: #d4edda;
      color: #155724;
      padding: 1rem;
      border-radius: 0.375rem;
      margin-bottom: 1rem;
    }
    .error-message {
      background-color: #f8d7da;
      color: #721c24;
      padding: 1rem;
      border-radius: 0.375rem;
      margin-bottom: 1rem;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
      .sidebar {
        width: 200px;
        transform: translateX(-100%);
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
    <a href="dashboard.php" class="flex items-center"><i class="fas fa-home mr-2"></i> Dashboard</a>
    <a href="tambah_wisata.php" class="flex items-center"><i class="fas fa-plus mr-2"></i> Tambah Wisata</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="card-container">
      <h2 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Komentar & Pengaduan untuk <?= htmlspecialchars($data_wisata['nama']); ?></h2>

      <!-- Display Success or Error Messages -->
      <?php if ($success_message): ?>
        <div class="success-message">
          <?= htmlspecialchars($success_message); ?>
        </div>
      <?php endif; ?>
      <?php if ($error_message): ?>
        <div class="error-message">
          <?= htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>

      <!-- Komentar -->
      <div class="mb-6">
        <h4 class="text-lg font-semibold text-gray-900 mb-4">Komentar</h4>
        <?php if (mysqli_num_rows($komentar) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($komentar)): ?>
            <div class="comment-card">
              <div class="flex justify-between items-start">
                <div>
                  <strong class="text-gray-800"><?= htmlspecialchars($row['username']); ?>:</strong>
                  <p class="text-gray-700 mt-1 break-words"><?= htmlspecialchars($row['isi_komentar']); ?></p>
                  <small class="text-gray-600"><?= $row['created_at']; ?></small>
                </div>
                <a href="hapus_komentar.php?id=<?= $row['komentar_id']; ?>&wisata_id=<?= $wisata_id; ?>" class="delete-btn flex items-center" onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                  <i class="fas fa-trash mr-1"></i> Hapus
                </a>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-gray-600 text-center">Belum ada komentar untuk tempat wisata ini.</p>
        <?php endif; ?>
      </div>

      <!-- Pengaduan -->
      <div>
        <h4 class="text-lg font-semibold text-gray-900 mb-4">Pengaduan</h4>
        <?php if (mysqli_num_rows($pengaduan) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($pengaduan)): ?>
            <div class="complaint-card">
              <div class="flex justify-between items-start">
                <div>
                  <strong class="text-gray-800"><?= htmlspecialchars($row['username']); ?>:</strong>
                  <p class="text-gray-700 mt-1 break-words"><?= htmlspecialchars($row['isi_pengaduan']); ?></p>
                  <small class="text-gray-600"><?= $row['created_at']; ?></small>
                </div>
                <a href="hapus_pengaduan.php?id=<?= $row['pengaduan_id']; ?>" class="delete-btn flex items-center" onclick="return confirm('Apakah Anda yakin ingin menghapus pengaduan ini?')">
                  <i class="fas fa-trash mr-1"></i> Hapus
                </a>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-gray-600 text-center">Belum ada pengaduan untuk tempat wisata ini.</p>
        <?php endif; ?>
      </div>

      <a href="dashboard.php" class="mt-6 inline-block bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
      </a>
    </div>
  </div>

  <script>
    // Toggle Sidebar
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('open');
    }
  </script>
</body>
</html>