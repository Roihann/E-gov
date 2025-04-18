<?php
session_start();
include '../config/db.php';

// Initialize error and success messages
$_SESSION['error'] = '';
$_SESSION['success'] = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if required fields are set
    if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['password_confirm'])) {
        $_SESSION['error'] = 'Semua field harus diisi!';
        header("Location: ../auth/register.php");
        exit;
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);

    // Input validation
    if (empty($username) || empty($password) || empty($password_confirm)) {
        $_SESSION['error'] = 'Semua field tidak boleh kosong!';
        header("Location: ../auth/register.php");
        exit;
    }

    if (strlen($username) < 3 || strlen($username) > 50) {
        $_SESSION['error'] = 'Username harus antara 3 hingga 50 karakter!';
        header("Location: ../auth/register.php");
        exit;
    }

    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Kata sandi harus minimal 6 karakter!';
        header("Location: ../auth/register.php");
        exit;
    }

    if ($password !== $password_confirm) {
        $_SESSION['error'] = 'Konfirmasi kata sandi tidak cocok!';
        header("Location: ../auth/register.php");
        exit;
    }

    // Check for valid username format (e.g., alphanumeric and underscores)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $_SESSION['error'] = 'Username hanya boleh berisi huruf, angka, dan garis bawah!';
        header("Location: ../auth/register.php");
        exit;
    }

    // Check if username already exists
    $check_query = "SELECT id FROM users WHERE username = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, 's', $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $_SESSION['error'] = 'Username sudah digunakan!';
        mysqli_stmt_close($check_stmt);
        header("Location: ../auth/register.php");
        exit;
    }
    mysqli_stmt_close($check_stmt);

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $hashed_password);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = 'Pendaftaran berhasil! Silakan masuk.';
        header("Location: ../auth/login.php");
    } else {
        $_SESSION['error'] = 'Gagal mendaftar. Silakan coba lagi.';
        header("Location: ../auth/register.php");
    }

    // Close statement
    mysqli_stmt_close($stmt);
} else {
    // Redirect to register page if accessed directly
    header("Location: ../auth/register.php");
}

// No need to close connection explicitly; PHP will handle it
exit;
?>