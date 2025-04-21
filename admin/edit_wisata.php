<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$wisata_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data tempat wisata yang akan diedit
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
if (!$wisata || mysqli_num_rows($wisata) == 0) {
    die("Tempat wisata tidak ditemukan.");
}
$data = mysqli_fetch_assoc($wisata);

$error_message = '';
$success_message = '';

// Tangani pesan status dari update_wisata.php
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $success_message = isset($_GET['msg']) ? urldecode($_GET['msg']) : "Tempat wisata berhasil diperbarui!";
    } elseif ($_GET['status'] == 'error') {
        $error_message = isset($_GET['msg']) ? urldecode($_GET['msg']) : "Gagal memperbarui tempat wisata.";
    }
}

// Handle hapus foto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_foto'])) {
    $foto_to_delete = mysqli_real_escape_string($conn, $_POST['delete_foto']);
    $foto_names = !empty($data['foto']) ? explode(',', $data['foto']) : [];
    $file_path = "../Uploads/" . $foto_to_delete;

    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            // Hapus foto dari array
            $foto_names = array_filter($foto_names, function($foto) use ($foto_to_delete) {
                return $foto !== $foto_to_delete;
            });
            $foto_string = implode(',', $foto_names);

            // Update database dengan foto yang tersisa
            $sql = "UPDATE wisata SET foto='$foto_string' WHERE id=$wisata_id";
            if (mysqli_query($conn, $sql)) {
                $success_message = "Foto berhasil dihapus!";
                // Refresh data setelah hapus foto
                $wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
                $data = mysqli_fetch_assoc($wisata);
            } else {
                $error_message = "Gagal mengupdate database: " . mysqli_error($conn);
            }
        } else {
            $error_message = "Gagal menghapus foto $foto_to_delete dari server.";
        }
    } else {
        $error_message = "File foto $foto_to_delete tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Tempat Wisata - Sistem Informasi Pariwisata Banjarmasin</title>
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
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .form-container {
      animation: fadeIn 1s ease-in-out;
      background: rgba(255, 255, 255, 1);
      max-width: 600px;
      width: 100%;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .form-container input, .form-container textarea, .form-container select {
      transition: transform 0.2s;
    }
    .form-container input:focus, .form-container textarea:focus, .form-container select:focus {
      transform: scale(1.02);
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
    }
    .photo-preview {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-top: 1rem;
    }
    .photo-preview img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 0.5rem;
      position: relative;
    }
    .photo-preview .delete-btn {
      position: absolute;
      top: -10px;
      right: -10px;
      background-color: #ef4444;
      color: white;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    .photo-preview .delete-btn:hover {
      background-color: #dc2626;
    }
    .toast {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #22c55e;
      color: white;
      padding: 1rem;
      border-radius: 0.375rem;
      display: none;
      z-index: 1000;
      animation: slideIn 0.3s ease-out;
    }
    .error-message {
      background-color: #f8d7da;
      color: #721c24;
      padding: 1rem;
      border-radius: 0.375rem;
      margin-bottom: 1rem;
    }
    .success-message {
      background-color: #d4edda;
      color: #155724;
      padding: 1rem;
      border-radius: 0.375rem;
      margin-bottom: 1rem;
    }
    @keyframes slideIn {
      from { opacity: 0; transform: translateX(100%); }
      to { opacity: 1; transform: translateX(0); }
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
    <div class="form-container">
      <h2 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Edit Tempat Wisata</h2>
      <?php if ($error_message): ?>
        <div class="error-message">
          <?= htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>
      <?php if ($success_message): ?>
        <div class="success-message">
          <?= htmlspecialchars($success_message); ?>
          <a href="dashboard.php" class="underline text-blue-600 ml-2">Kembali ke Dashboard</a>
        </div>
      <?php endif; ?>
      <form method="POST" action="update_wisata.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $wisata_id; ?>">
        <div class="mb-5">
          <label for="nama" class="block text-gray-900 font-medium mb-2">Nama Tempat Wisata</label>
          <div class="relative">
            <input 
              type="text" 
              name="nama" 
              id="nama" 
              class="w-full p-4 pl-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm transition-all" 
              value="<?= htmlspecialchars($data['nama']); ?>" 
              required
            >
            <i class="fas fa-map-marker-alt absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
        </div>

        <div class="mb-5">
          <label for="alamat" class="block text-gray-900 font-medium mb-2">Alamat</label>
          <div class="relative">
            <input 
              type="text" 
              name="alamat" 
              id="alamat" 
              class="w-full p-4 pl-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm transition-all" 
              value="<?= htmlspecialchars($data['alamat']); ?>" 
              required
            >
            <i class="fas fa-map absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
        </div>

        <!-- Dropdown Kecamatan -->
        <div class="mb-5">
          <label for="kecamatan" class="block text-gray-900 font-medium mb-2">Kecamatan</label>
          <div class="relative">
            <select 
              name="kecamatan" 
              id="kecamatan" 
              class="w-full p-4 pl-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm transition-all" 
              required
            >
              <option value="Banjarmasin Selatan" <?= $data['kecamatan'] == 'Banjarmasin Selatan' ? 'selected' : ''; ?>>Banjarmasin Selatan</option>
              <option value="Banjarmasin Utara" <?= $data['kecamatan'] == 'Banjarmasin Utara' ? 'selected' : ''; ?>>Banjarmasin Utara</option>
              <option value="Banjarmasin Timur" <?= $data['kecamatan'] == 'Banjarmasin Timur' ? 'selected' : ''; ?>>Banjarmasin Timur</option>
              <option value="Banjarmasin Barat" <?= $data['kecamatan'] == 'Banjarmasin Barat' ? 'selected' : ''; ?>>Banjarmasin Barat</option>
              <option value="Banjarmasin Tengah" <?= $data['kecamatan'] == 'Banjarmasin Tengah' ? 'selected' : ''; ?>>Banjarmasin Tengah</option>
            </select>
            <i class="fas fa-map-pin absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
        </div>

        <div class="mb-5">
          <label for="deskripsi" class="block text-gray-900 font-medium mb-2">Deskripsi</label>
          <div class="relative">
            <textarea 
              name="deskripsi" 
              id="deskripsi" 
              class="w-full p-4 pl-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm transition-all" 
              rows="4" 
              required
            ><?= htmlspecialchars($data['deskripsi']); ?></textarea>
            <i class="fas fa-pen absolute left-4 top-4 text-gray-400"></i>
          </div>
        </div>

        <div class="mb-5">
          <div class="flex gap-4">
            <div class="flex-1">
              <label for="longitude" class="block text-gray-900 font-medium mb-2">Longitude</label>
              <div class="relative">
                <input 
                  type="number" 
                  step="any" 
                  name="longitude" 
                  id="longitude" 
                  class="w-full p-4 pl-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm transition-all" 
                  value="<?= htmlspecialchars($data['longitude'] ?? '0'); ?>" 
                  required
                >
                <i class="fas fa-globe absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              </div>
            </div>
            <div class="flex-1">
              <label for="latitude" class="block text-gray-900 font-medium mb-2">Latitude</label>
              <div class="relative">
                <input 
                  type="number" 
                  step="any" 
                  name="latitude" 
                  id="latitude" 
                  class="w-full p-4 pl-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm transition-all" 
                  value="<?= htmlspecialchars($data['latitude'] ?? '0'); ?>" 
                  required
                >
                <i class="fas fa-globe absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-5">
          <label for="fotos" class="block text-gray-900 font-medium mb-2">Foto Saat Ini</label>
          <div class="photo-preview">
            <?php 
            $fotos = !empty($data['foto']) ? explode(',', $data['foto']) : [];
            foreach ($fotos as $foto): ?>
              <div class="relative">
                <img src="../Uploads/<?= htmlspecialchars($foto); ?>" alt="Foto Wisata">
                <form method="POST">
                  <input type="hidden" name="delete_foto" value="<?= htmlspecialchars($foto); ?>">
                  <button type="submit" class="delete-btn">
                    <i class="fas fa-times"></i>
                  </button>
                </form>
              </div>
            <?php endforeach; ?>
          </div>
          <?php if (empty($fotos)): ?>
            <p class="text-gray-600">Belum ada foto untuk tempat wisata ini.</p>
          <?php endif; ?>
        </div>

        <div class="mb-5">
          <label for="fotos" class="block text-gray-900 font-medium mb-2">Tambah Foto Baru (Opsional, maksimal 5 gambar)</label>
          <div class="relative">
            <input 
              type="file" 
              name="fotos[]" 
              id="fotos" 
              class="w-full p-2 pl-12 border border-gray-200 rounded-lg" 
              multiple 
              accept="image/*"
            >
            <i class="fas fa-camera absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
          <small class="text-gray-600">Format: JPG, JPEG, PNG, GIF. Maksimum 5MB per gambar.</small>
          <div class="photo-preview" id="preview"></div>
        </div>

        <button 
          type="submit" 
          class="w-full bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all flex items-center justify-center"
        >
          <i class="fas fa-save mr-2"></i> Update Wisata
        </button>
      </form>
    </div>
  </div>

  <!-- Toast Notification -->
  <div id="toast" class="toast">
    Tempat wisata berhasil diperbarui!
  </div>

  <script>
    // Toggle Sidebar
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('open');
    }

    // Preview Foto Baru
    document.getElementById('fotos').addEventListener('change', function(event) {
      const preview = document.getElementById('preview');
      preview.innerHTML = ''; // Clear previous previews
      const files = event.target.files;
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.appendChild(img);
          };
          reader.readAsDataURL(file);
        }
      }
    });

    // Show Toast if Success
    window.onload = function() {
      <?php if ($success_message): ?>
        const toast = document.getElementById('toast');
        toast.style.display = 'block';
        setTimeout(() => {
          toast.style.display = 'none';
        }, 3000);
      <?php endif; ?>
    };
  </script>
</body>
</html>