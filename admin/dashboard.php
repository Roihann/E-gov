<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle success or error messages from session
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success']);
unset($_SESSION['error']);

// Ambil data tempat wisata
$limit = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = $search ? "WHERE nama LIKE '%$search%'" : '';
$wisata_query = "SELECT * FROM wisata $where_clause LIMIT $limit OFFSET $offset";
$wisata = mysqli_query($conn, $wisata_query);
if (!$wisata) {
    die("Query wisata failed: " . mysqli_error($conn));
}

// Hitung total data untuk pagination
$total_wisata_query = "SELECT COUNT(*) as total FROM wisata $where_clause";
$total_wisata_result = mysqli_query($conn, $total_wisata_query);
if (!$total_wisata_result) {
    die("Query total wisata failed: " . mysqli_error($conn));
}
$total_wisata = mysqli_fetch_assoc($total_wisata_result)['total'];
$total_pages = ceil($total_wisata / $limit);

// Ambil jumlah komentar baru (dalam 24 jam terakhir)
$komentar_query = "SELECT COUNT(*) as total FROM komentar WHERE created_at >= NOW() - INTERVAL 1 DAY";
$komentar_result = mysqli_query($conn, $komentar_query);
if (!$komentar_result) {
    $komentar_baru = 0; // Default jika query gagal
} else {
    $komentar_baru = mysqli_fetch_assoc($komentar_result)['total'];
}

// Ambil jumlah pengaduan baru
$pengaduan_query = "SELECT COUNT(*) as total FROM pengaduan WHERE created_at >= NOW() - INTERVAL 1 DAY";
$pengaduan_result = mysqli_query($conn, $pengaduan_query);
if (!$pengaduan_result) {
    $pengaduan_baru = 0; // Default jika query gagal
} else {
    $pengaduan_baru = mysqli_fetch_assoc($pengaduan_result)['total'];
}

// Ambil daftar pengguna
$users_query = "SELECT * FROM users LIMIT 5";
$users = mysqli_query($conn, $users_query);
if (!$users) {
    die("Query users failed: " . mysqli_error($conn));
}

// Ambil jumlah total pengguna
$total_users_query = "SELECT COUNT(*) as total FROM users";
$total_users_result = mysqli_query($conn, $total_users_query);
if (!$total_users_result) {
    $total_users = 0; // Default jika query gagal
} else {
    $total_users = mysqli_fetch_assoc($total_users_result)['total'];
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
    }
    .table-container {
      overflow-x: auto;
    }
    .table th, .table td {
      white-space: nowrap;
      padding: 1rem;
      border-bottom: 1px solid #e5e7eb;
    }
    .table thead {
      background-color: #f1f5f9;
    }
    .table tbody tr:hover {
      background-color: #f1f5f9;
    }
    .stats-card {
      transition: transform 0.2s;
    }
    .stats-card:hover {
      transform: translateY(-5px);
    }
    .pagination {
      display: flex;
      justify-content: center;
      gap: 0.5rem;
      margin-top: 1rem;
    }
    .pagination a {
      padding: 0.5rem 1rem;
      border: 1px solid #e5e7eb;
      border-radius: 0.375rem;
      color: #374151;
      transition: background-color 0.2s;
    }
    .pagination a:hover {
      background-color: #f1f5f9;
    }
    .pagination .active {
      background-color: #2563eb;
      color: white;
      border-color: #2563eb;
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
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      max-width: 500px;
      width: 90%;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .btn-danger {
      background-color: #ef4444;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 0.375rem;
      transition: background-color 0.2s;
      display: flex;
      justify-content: center;
      width: 100%;
    }
    .btn-danger:hover {
      background-color: #dc2626;
    }
    .btn-secondary {
      background-color: #6b7280;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 0.375rem;
      transition: background-color 0.2s;
      display: flex;
      justify-content: center;
      width: 100%;
    }
    .btn-secondary:hover {
      background-color: #4b5563;
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
    <a href="dashboard.php" class="flex items-center active"><i class="fas fa-home mr-2"></i> Dashboard</a>
    <a href="tambah_wisata.php" class="flex items-center"><i class="fas fa-plus mr-2"></i> Tambah Wisata</a>
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
        <span class="mr-4 text-gray-600">Hi, <?= htmlspecialchars($_SESSION['username']); ?></span>
        <a href="../auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-all flex items-center">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
      </div>
    </div>

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

    <!-- Stats Card -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <div class="bg-white p-6 rounded-lg shadow-md stats-card flex items-center">
        <i class="fas fa-map-marker-alt text-4xl text-blue-600 mr-4"></i>
        <div>
          <h3 class="text-lg font-medium text-gray-700">Jumlah Tempat Wisata</h3>
          <p class="text-3xl font-bold text-blue-600 mt-2"><?= $total_wisata; ?></p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md stats-card flex items-center">
        <i class="fas fa-comments text-4xl text-green-600 mr-4"></i>
        <div>
          <h3 class="text-lg font-medium text-gray-700">Komentar Baru</h3>
          <p class="text-3xl font-bold text-green-600 mt-2"><?= $komentar_baru; ?></p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md stats-card flex items-center">
        <i class="fas fa-exclamation-circle text-4xl text-red-600 mr-4"></i>
        <div>
          <h3 class="text-lg font-medium text-gray-700">Pengaduan Baru</h3>
          <p class="text-3xl font-bold text-red-600 mt-2"><?= $pengaduan_baru; ?></p>
        </div>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md stats-card flex items-center">
        <i class="fas fa-users text-4xl text-purple-600 mr-4"></i>
        <div>
          <h3 class="text-lg font-medium text-gray-700">Jumlah Pengguna</h3>
          <p class="text-3xl font-bold text-purple-600 mt-2"><?= $total_users; ?></p>
        </div>
      </div>
    </div>

    <!-- Daftar Tempat Wisata -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Tempat Wisata</h2>
        <form method="GET" class="flex items-center">
          <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Cari wisata..." class="border border-gray-300 rounded-lg px-4 py-2 mr-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-all">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>
      <div class="table-container">
        <table class="table w-full">
          <thead>
            <tr>
              <th class="text-left">No</th>
              <th class="text-left">Nama Wisata</th>
              <th class="text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($wisata) > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($wisata)): ?>
                <tr>
                  <td><?= $row['id']; ?></td>
                  <td><?= htmlspecialchars($row['nama']); ?></td>
                  <td>
                    <a href="lihat_komentar.php?id=<?= $row['id']; ?>" class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 transition-all mr-2">
                      <i class="fas fa-comments mr-1"></i> Komentar
                    </a>
                    <a href="edit_wisata.php?id=<?= $row['id']; ?>" class="bg-yellow-500 text-white px-3 py-1 rounded-lg hover:bg-yellow-600 transition-all mr-2">
                      <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <button onclick="openDeleteModal(<?= $row['id']; ?>)" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition-all">
                      <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
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
      <!-- Pagination -->
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1; ?>&search=<?= urlencode($search); ?>">Sebelumnya</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>" class="<?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
          <a href="?page=<?= $page + 1; ?>&search=<?= urlencode($search); ?>">Berikutnya</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Daftar Pengguna -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Daftar Pengguna</h2>
      <div class="table-container">
        <table class="table w-full">
          <thead>
            <tr>
              <th class="text-left">Username</th>
              <th class="text-left">Role</th>
              <th class="text-left">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($users) > 0): ?>
              <?php while ($user = mysqli_fetch_assoc($users)): ?>
                <tr>
                  <td><?= htmlspecialchars($user['username']); ?></td>
                  <td><?= htmlspecialchars($user['role']); ?></td>
                  <td>
                    <a href="edit_user.php?id=<?= $user['id']; ?>" class="bg-yellow-500 text-white px-3 py-1 rounded-lg hover:bg-yellow-600 transition-all mr-2">
                      <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="hapus_user.php?id=<?= $user['id']; ?>" class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition-all" onclick="return confirmDelete(event)">
                      <i class="fas fa-trash mr-1"></i> Hapus
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="p-3 text-center text-gray-600">Tidak ada data pengguna.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Container -->
  <div id="deleteModal" class="modal">
    <div id="modalContent" class="modal-content">
      <!-- Modal content will be loaded here via AJAX -->
    </div>
  </div>

  <!-- Toast Notification -->
  <div id="toast" class="toast">
    Aksi berhasil dilakukan!
  </div>

  <script>
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('open');
    }

    function confirmDelete(event) {
      if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        event.preventDefault();
        return false;
      }
      showToast();
      return true;
    }

    function showToast(message = 'Aksi berhasil dilakukan!') {
      const toast = document.getElementById('toast');
      toast.textContent = message;
      toast.style.display = 'block';
      setTimeout(() => {
        toast.style.display = 'none';
      }, 3000);
    }

    function openDeleteModal(wisataId) {
      const modal = document.getElementById('deleteModal');
      const modalContent = document.getElementById('modalContent');

      // Fetch the confirmation form from hapus_wisata.php
      fetch(`hapus_wisata.php?id=${wisataId}&modal=true`)
        .then(response => response.text())
        .then(data => {
          modalContent.innerHTML = data;
          modal.style.display = 'flex';

          // Attach event listener to the form inside the modal
          const form = modalContent.querySelector('form');
          if (form) {
            form.addEventListener('submit', function(e) {
              e.preventDefault();
              const formData = new FormData(form);

              // Submit the form via AJAX
              fetch(`hapus_wisata.php?id=${wisataId}`, {
                method: 'POST',
                body: formData
              })
                .then(response => response.json())
                .then(result => {
                  if (result.success) {
                    showToast(result.message);
                    modal.style.display = 'none';
                    // Refresh the page to update the wisata list
                    window.location.reload();
                  } else {
                    showToast(result.message);
                    modal.style.display = 'none';
                  }
                })
                .catch(error => {
                  showToast('Terjadi kesalahan saat menghapus.');
                  modal.style.display = 'none';
                });
            });
          }

          // Attach event listener to the "Batal" button
          const cancelButton = modalContent.querySelector('.btn-secondary');
          if (cancelButton) {
            cancelButton.addEventListener('click', (e) => {
              e.preventDefault();
              modal.style.display = 'none';
            });
          }
        })
        .catch(error => {
          showToast('Gagal memuat form penghapusan.');
        });
    }
  </script>
</body>
</html>