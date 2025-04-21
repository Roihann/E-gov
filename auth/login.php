<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<<<<<<< HEAD
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Pariwisata Banjarmasin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
    body {
        margin: 0;
        padding: 0;
        height: 100vh;
        overflow: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
=======
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk - Sistem Informasi Pariwisata Banjarmasin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      overflow: hidden;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
<<<<<<< HEAD
      background-color: #f0f4f8;
>>>>>>> main
    }
    .image-section {
        position: relative;
        background: url('../assets/images/collage-image.png') no-repeat center center/cover;
    }
    .image-overlay {
<<<<<<< HEAD
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
=======
=======
      background: url('../assets/images/Budaya-Menara-Pandang-Banjarmasin.jpg') no-repeat center center/cover;
      position: relative;
    }
    .overlay {
>>>>>>> main
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5); /* Overlay untuk membuat teks lebih terbaca */
    }
    .form-container {
      animation: fadeIn 1s ease-in-out;
      background: rgba(255, 255, 255, 0.95); /* Transparansi ringan pada form */
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
>>>>>>> main
    }
<<<<<<< HEAD
    </style>
=======
    /* Show Password Icon */
    .password-toggle {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6b7280; /* Tailwind gray-500 */
      font-size: 18px;
      transition: color 0.2s;
    }
    .password-toggle:hover {
      color: #2563eb; /* Tailwind blue-600 */
    }
    .logo {
      filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
    }
  </style>
>>>>>>> main
</head>

<body>
<<<<<<< HEAD
    <div class="flex h-screen">
        <!-- Left Section: Login Form -->
        <div class="w-full md:w-1/2 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
            <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg">
                <h3 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Login to Account</h3>

                <?php if ($error): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-6 text-center">
                    <?= $error; ?>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <input type="text" name="username" id="username"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            placeholder="Email" required autofocus>
                    </div>

                    <div class="mb-4">
                        <input type="password" name="password" id="password"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            placeholder="Password" required>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-all">
                        Sign In
                    </button>
                    <div class="flex justify-between mt-3 text-sm text-gray-600">
                        <a href="#" class="text-blue-500 hover:underline">Forgot password?</a>
                        <span>
                            Belum punya akun?
                            <a href="register.php" class="text-blue-600 hover:underline">Daftar</a>
                        </span>
                    </div>


                </form>
            </div>
        </div>

        <!-- Right Section: Image and Text -->
        <div class="hidden md:block w-1/2 image-section">
            <div class="image-overlay"></div>
            <div class="relative z-10 flex flex-col items-center justify-center h-full text-white">
                <img src="../assets/images/banjarmasin-logo.png" alt="Banjarmasin Logo" class="w-48 mb-4">
                <h2 class="text-3xl font-bold uppercase text-center">Sistem Informasi Pariwisata Banjarmasin</h2>
            </div>
        </div>
    </div>
  <div class="flex h-screen">
    <!-- Left Section: Login Form -->
    <div class="w-full md:w-1/2 flex items-center justify-center bg-gradient-to-br from-blue-50 to-gray-100">
      <div class="form-container w-full max-w-md p-8 bg-white rounded-2xl shadow-xl">
        <h3 class="text-3xl font-bold text-gray-800 mb-6 text-center">Masuk ke Akun Anda</h3>
=======
  <div class="overlay"></div>
  <div class="flex items-center justify-center h-screen relative z-10">
    <div class="form-container p-8 rounded-2xl shadow-xl">
      <!-- Logo Banjarmasin di atas form -->
      <div class="flex justify-center mb-6">
        <img src="../assets/images/banjarmasin-logo.png" alt="Banjarmasin Logo" class="w-32 logo">
      </div>
      <h3 class="text-3xl font-bold text-gray-800 mb-6 text-center">Masuk ke Akun Anda</h3>
>>>>>>> main

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

      <form method="POST" action="../proses/login_proses.php" id="loginForm">
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

        <button 
          type="submit" 
          class="w-full bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all flex items-center justify-center"
        >
          <i class="fas fa-sign-in-alt mr-2"></i> Masuk
        </button>
      </form>

      <p class="mt-4 text-center text-gray-600">
        <a href="forgot_password.php" class="text-blue-500 hover:underline">Lupa kata sandi?</a>
        <span class="mx-2">|</span>
        Belum punya akun? <a href="register.php" class="text-blue-500 hover:underline">Daftar</a>
      </p>

      <!-- Teks Sistem Informasi Pariwisata di bawah form -->
      <p class="mt-4 text-center text-gray-600 font-semibold uppercase">
        Sistem Informasi Pariwisata Banjarmasin
      </p>
    </div>
  </div>

  <script>
    // Toggle password visibility for password field
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });
  </script>
</body>

</html>