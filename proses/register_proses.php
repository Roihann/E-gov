<?php
session_start();
include '../config/db.php';

<<<<<<< HEAD
$username = $_POST['username'];
$password = $_POST['password']; // Tidak di-hash

// Menghindari SQL Injection dengan prepared statements
$sql = "INSERT INTO users (username, password) VALUES (?, ?)";

// Mempersiapkan statement
$stmt = mysqli_prepare($conn, $sql);

// Bind parameter untuk statement
mysqli_stmt_bind_param($stmt, 'ss', $username, $password);

// Menjalankan statement
if (mysqli_stmt_execute($stmt)) {
    // Redirect ke register.php dengan query success=true
    header("Location: ../auth/register.php?success=true");
} else {
    // Menampilkan pesan error jika gagal
    echo "Gagal daftar: " . mysqli_error($conn);
}

// Menutup statement
mysqli_stmt_close($stmt);

// Menutup koneksi database
mysqli_close($conn);
=======
// Initialize error and success messages
$_SESSION['error'] = '';
$_SESSION['success'] = '';

// Pastikan koneksi database ada
if (!$conn) {
    $_SESSION['error'] = 'Koneksi ke database gagal. Silakan coba lagi nanti.';
    header("Location: ../auth/register.php");
    exit;
}

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
    if (!$check_stmt) {
        $_SESSION['error'] = 'Terjadi kesalahan pada server. Silakan coba lagi.';
        header("Location: ../auth/register.php");
        exit;
    }

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

    // Insert user into database (password stored as plaintext)
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        $_SESSION['error'] = 'Terjadi kesalahan pada server. Silakan coba lagi.';
        header("Location: ../auth/register.php");
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'ss', $username, $password);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = 'Pendaftaran berhasil! Silakan masuk.';
        header("Location: ../auth/register.php?success=true");
    } else {
        $_SESSION['error'] = 'Gagal mendaftar. Silakan coba lagi.';
        header("Location: ../auth/register.php");
    }

    // Close statement
    mysqli_stmt_close($stmt);
} else {
    // Redirect to register page if accessed directly
    $_SESSION['error'] = 'Silakan daftar melalui form yang tersedia!';
    header("Location: ../auth/register.php");
    exit;
}

// No need to close connection explicitly; PHP will handle it
exit;
>>>>>>> main
?>