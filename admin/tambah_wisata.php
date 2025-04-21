<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    error_log("POST Data: " . print_r($_POST, true));
    error_log("FILES Data: " . print_r($_FILES, true));

    $nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat'] ?? '');
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi'] ?? '');
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan'] ?? 'Banjarmasin'); // Ambil kecamatan dari form
    $fotos = isset($_FILES['fotos']) ? $_FILES['fotos'] : null;

    $foto_names = [];
    $target_dir = "../Uploads/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    if (!is_writable($target_dir)) {
        $error_message = "Folder Uploads tidak dapat ditulis. Periksa izin folder.";
    } else {
        if ($fotos && !empty($fotos['name'][0])) {
            $total_files = count($fotos['name']);
            if ($total_files > 5) {
                $error_message = "Anda hanya dapat mengunggah maksimal 5 gambar.";
            } else {
                foreach ($fotos['name'] as $key => $name) {
                    if ($fotos['error'][$key] == UPLOAD_ERR_OK) {
                        $target_file = $target_dir . basename($name);
                        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                        if (!in_array($imageFileType, $allowed_types)) {
                            $error_message .= "Tipe file $name tidak diizinkan. Gunakan JPG, JPEG, PNG, atau GIF. ";
                            continue;
                        }
                        if ($fotos['size'][$key] > 5 * 1024 * 1024) {
                            $error_message .= "Ukuran file $name terlalu besar. Maksimum 5MB. ";
                            continue;
                        }
                        if (move_uploaded_file($fotos['tmp_name'][$key], $target_file)) {
                            $foto_names[] = basename($name);
                            $success_message .= "Foto $name berhasil diupload. ";
                        } else {
                            $error_message .= "Gagal mengupload foto $name. Periksa izin folder Uploads atau konfigurasi server. ";
                        }
                    } elseif ($fotos['error'][$key] != UPLOAD_ERR_NO_FILE) {
                        $error_message .= "Error upload foto: " . $fotos['error'][$key] . ". ";
                    }
                }
            }
        }

        if (empty($error_message)) {
            $foto_string = implode(',', $foto_names);

            // Insert data into database
            $sql = "INSERT INTO wisata (nama, alamat, deskripsi, longitude, latitude, foto) VALUES ('$nama', '$alamat', '$deskripsi', '$longitude', '$latitude', '$foto_string')";
            if (mysqli_query($conn, $sql)) {
                $success_message .= "Tempat wisata berhasil ditambahkan!";
                header("Location: dashboard.php?success=1");
                exit;
            } else {
                $error_message .= "Gagal menambah wisata: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Tempat Wisata - Sistem Informasi Pariwisata Banjarmasin</title>
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
    <a href="tambah_wisata.php" class="flex items-center active"><i class="fas fa-plus mr-2"></i> Tambah Wisata</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <div class="form-container">
      <h2 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Tambah Tempat Wisata</h2>
      <?php if ($error_message): ?>
        <div class="error-message">
          <?= htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>
      <?php if ($success_message): ?>
        <div class="success-message">
          <?= htmlspecialchars($success_message); ?>
        </div>
      <?php endif; ?>
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-5">
          <label for="nama" class="block text-gray-900 font-medium mb-2">Nama Tempat Wisata</label>
          <div class="relative">
            <input 
              type="text" 
              name="nama" 
              id="nama" 
              class="w-full p-4 pl-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 shadow-sm transition-all" 
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
              <option value="Banjarmasin Selatan">Banjarmasin Selatan</option>
              <option value="Banjarmasin Utara">Banjarmasin Utara</option>
              <option value="Banjarmasin Timur">Banjarmasin Timur</option>
              <option value="Banjarmasin Barat">Banjarmasin Barat</option>
              <option value="Banjarmasin Tengah">Banjarmasin Tengah</option>
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
            ></textarea>
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
                  required
                >
                <i class="fas fa-globe absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="mb-5">
          <label for="fotos" class="block text-gray-900 font-medium mb-2">Foto (Maksimal 5 gambar)</label>
          <div class="relative">
            <input 
              type="file" 
              name="fotos[]" 
              id="fotos" 
              class="w-full p-2 pl-12 border border-gray-200 rounded-lg" 
              multiple 
              accept="image/*"
              required
            >
            <i class="fas fa-camera absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
          <small class="text-gray-600">Format: JPG, JPEG, PNG, GIF. Maksimum 5MB per gambar.</small>
          <div class="photo-preview" id="preview"></div>
        </div>

        <button 
          type="submit" 
          class="w-full bg-green-600 text-white p-4 rounded-lg hover:bg-green-700 shadow-md hover:shadow-lg transition-all flex items-center justify-center"
        >
          <i class="fas fa-plus mr-2"></i> Tambah Wisata
        </button>
      </form>
    </div>
  </div>

  <!-- Toast Notification -->
  <div id="toast" class="toast">
    Tempat wisata berhasil ditambahkan!
  </div>

  <script>
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('open');
    }

    document.getElementById('fotos').addEventListener('change', function(event) {
      const preview = document.getElementById('preview');
      preview.innerHTML = '';
      const files = event.target.files;
      if (files.length > 5) {
        alert('Anda hanya dapat mengunggah maksimal 5 gambar.');
        event.target.value = '';
        return;
      }
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

    window.onload = function() {
      <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
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