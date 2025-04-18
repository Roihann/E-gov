<?php
session_start();
include '../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sanitize input to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $username);
    
    // Query to fetch user data based on username
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $user = mysqli_fetch_assoc($result);

    // Check if user exists and password matches (directly compared without hash)
    if ($user && $password == $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on user role
        if ($user['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../user/index.php");
        }
        exit;
    } else {
        $error = 'Username atau kata sandi salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistem Informasi Pariwisata Banjarmasin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      overflow: hidden;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f4f8;
    }
    .image-section {
      position: relative;
      background: url('../assets/images/collage-image.png') no-repeat center center/cover;
    }
    .image-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.4);
    }
    .form-container {
      animation: fadeIn 1s ease-in-out;
    }
    .right-content {
      animation: fadeIn 1s ease-in-out 0.3s;
      animation-fill-mode: backwards;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    input:focus {
      transform: scale(1.02);
      transition: transform 0.2s;
    }
  </style>
</head>
<body>
  <div class="flex h-screen">
    <!-- Left Section: Login Form -->
    <div class="w-full md:w-1/2 flex items-center justify-center bg-gradient-to-br from-blue-50 to-gray-100">
      <div class="form-container w-full max-w-md p-8 bg-white rounded-2xl shadow-xl">
        <h3 class="text-3xl font-bold text-gray-800 mb-6 text-center">Masuk ke Akun Anda</h3>

        <?php if ($error): ?>
          <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 text-center animate-pulse">
            <?= htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <form method="POST">
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
                class="w-full p-4 pl-12 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-all" 
                placeholder="Kata Sandi" 
                required
              >
              <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
          </div>

          <button 
            type="submit" 
            class="w-full bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition-all flex items-center justify-center"
          >
            <i class="fas fa-sign-in-alt mr-2"></i> Masuk
          </button>
        </form>

        <div class="flex justify-between mt-4 text-sm text-gray-600">
          <a href="#" class="text-blue-500 hover:underline">Lupa kata sandi?</a>
          <span>
            Belum punya akun? <a href="register.php" class="text-blue-500 hover:underline">Daftar</a>
          </span>
        </div>
      </div>
    </div>

    <!-- Right Section: Image and Text -->
    <div class="hidden md:block w-1/2 image-section">
      <div class="image-overlay"></div>
      <div class="right-content relative z-10 flex flex-col items-center justify-center h-full text-white">
        <img src="../assets/images/banjarmasin-logo.png" alt="Banjarmasin Logo" class="w-56 mb-6 drop-shadow-md">
        <h2 class="text-4xl font-bold uppercase text-center leading-tight">Sistem Informasi Pariwisata Banjarmasin</h2>
      </div>
    </div>
  </div>
</body>
</html>