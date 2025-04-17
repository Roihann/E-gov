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
        $error = 'Username or password is incorrect!';
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
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      overflow: hidden;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
      background: rgba(0, 0, 0, 0.6);
    }
  </style>
</head>
<body>
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
            <input 
              type="text" 
              name="username" 
              id="username" 
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
              placeholder="Email" 
              required 
              autofocus
            >
          </div>

          <div class="mb-4">
            <input 
              type="password" 
              name="password" 
              id="password" 
              class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
              placeholder="Password" 
              required
            >
          </div>

          <button 
            type="submit" 
            class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-all"
          >
            Sign In
          </button>

          <a href="#" class="block mt-3 text-blue-500 text-sm text-right hover:underline">Forgot password?</a>
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
</body>
</html>