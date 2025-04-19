<?php
session_start();
include '../config/db.php';

// Initialize error message
$_SESSION['error'] = '';

// Check if form data is set
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    $_SESSION['error'] = 'Username dan kata sandi harus diisi!';
    header("Location: ../auth/login.php");
    exit;
}

$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Input validation
if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'Username dan kata sandi tidak boleh kosong!';
    header("Location: ../auth/login.php");
    exit;
}

// Query to find user
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    $_SESSION['error'] = 'Terjadi kesalahan pada server. Silakan coba lagi.';
    header("Location: ../auth/login.php");
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && $password === $user['password']) {
    // Login successful, set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Redirect based on role
    if ($user['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/index.php");
    }
} else {
    // Login failed
    $_SESSION['error'] = 'Username atau kata sandi salah!';
    header("Location: ../auth/login.php");
}

mysqli_stmt_close($stmt);
// No need to close connection explicitly; PHP will handle it
exit;
?>