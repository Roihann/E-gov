<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar - Sistem Informasi Pariwisata Banjarmasin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      overflow: hidden;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: url('../assets/images/Budaya-Menara-Pandang-Banjarmasin.jpg') no-repeat center center/cover;
      position: relative;
    }
    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
    }
    .form-container {
      animation: fadeIn 1s ease-in-out;
      background: rgba(255, 255, 255, 0.95);
      max-width: 400px;
      width: 100%;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    input:focus {
      transform: scale(1.02);
      transition: transform 0.2s;
    }
    .password-toggle {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6b7280;
      font-size: 18px;
      transition: color 0.2s;
    }
    .password-toggle:hover {
      color: #2563eb;
    }
    .logo {
      filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
    }
    /* Pop-up Konfirmasi */
    .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #ffffff;
      padding: 24px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      text-align: center;
      z-index: 1000;
      animation: popIn 0.3s ease-out;
      min-width: 280px;
    }
    .popup-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 16px;
    }
    .popup i.checkmark {
      color: #22c55e;
      font-size: 48px;
    }
    .popup p {
      font-size: 18px;
      color: #374151;
      margin: 0;
    }
    .popup button {
      background-color: #2563eb;
      color: #ffffff;
      padding: 10px 24px;
      border-radius: 8px;
      border: none;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    .popup button:hover {
      background-color: #1d4ed8;
    }
    @keyframes popIn {
      0% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
      100% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    }
  </style>
</head>
<body>
  <div class="overlay"></div>
  <div class="flex items-center justify-center h-screen relative z-10">
    <div class="form-container p-8 rounded-2xl shadow-xl">
      <!-- Logo Banjarmasin di atas form -->
      <div class="flex justify-center mb-6">
        <img src="../assets/images/banjarmasin-logo.png" alt="Banjarmasin Logo" class="w-32 logo">
      </div>
      <h3 class="text-3xl font-bold text-gray-800 mb-6 text-center">Daftar Akun Baru</h3>

      <?php if (isset($_SESSION['error']) && $_SESSION['error']): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 text-center animate-pulse">
          <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['success']) && $_SESSION['success']): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6 text-center animate-pulse">
          <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <form method="POST" action="../proses/register_proses.php" id="registerForm">
        <div class="mb-5">
          <div class="relative">
            <input 
              type="text" 
              name="username" 
              id="username" 
              class="w-full p-4 pl-12 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-all" 
              placeholder="Username" 
              required 
              autofocus
            >
            <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
        </div>

        <div class="mb-5">
          <div class="relative">
            <input 
              type="password" 
              name="password" 
              id="password" 
              class="w-full p-4 pr-12 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-all" 
              placeholder="Kata Sandi" 
              required
            >
            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
          </div>
        </div>

        <div class="mb-5">
          <div class="relative">
            <input 
              type="password" 
              name="password_confirm" 
              id="password_confirm" 
              class="w-full p-4 pr-12 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-all" 
              placeholder="Konfirmasi Kata Sandi" 
              required
            >
            <i class="fas fa-eye password-toggle" id="togglePasswordConfirm"></i>
          </div>
        </div>

        <button 
          type="submit" 
          class="w-full bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all flex items-center justify-center"
        >
          <i class="fas fa-user-plus mr-2"></i> Daftar
        </button>
      </form>

      <p class="mt-4 text-center text-gray-600">
        Sudah punya akun? <a href="login.php" class="text-blue-500 hover:underline">Masuk di sini</a>
      </p>

      <!-- Teks Sistem Informasi Pariwisata di bawah form -->
      <p class="mt-4 text-center text-gray-600 font-semibold uppercase">
        Sistem Informasi Pariwisata Banjarmasin
      </p>
    </div>
  </div>

  <!-- Pop-up and Overlay -->
  <div id="popup" class="popup">
    <div class="popup-content">
      <i class="fas fa-check-circle checkmark"></i>
      <p>Akun berhasil dibuat!</p>
      <button onclick="window.location.href='login.php'">OK</button>
    </div>
  </div>

  <div id="overlay" class="overlay"></div>

  <script>
    // Menampilkan pop-up setelah berhasil registrasi
    window.onload = function() {
      <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
        document.getElementById("popup").style.display = "block";
        document.getElementById("overlay").style.display = "block";
      <?php endif; ?>
    };

    // Toggle password visibility for password field
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });

    // Toggle password visibility for password_confirm field
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirmInput = document.getElementById('password_confirm');
    togglePasswordConfirm.addEventListener('click', function() {
      const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordConfirmInput.setAttribute('type', type);
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });
  </script>
</body>
</html>