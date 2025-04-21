<?php
session_start();
include '../config/db.php';

// Initialize error and success messages
$_SESSION['error'] = '';
$_SESSION['success'] = '';

// Pastikan koneksi database ada
if (!$conn) {
    $_SESSION['error'] = 'Koneksi ke database gagal. Silakan coba lagi nanti.';
    header("Location: ../auth/forgot_password.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if required fields are set
    if (!isset($_POST['username']) || !isset($_POST['new_password']) || !isset($_POST['new_password_confirm'])) {
        $_SESSION['error'] = 'Semua field harus diisi!';
        header("Location: ../auth/forgot_password.php");
        exit;
    }

    $username = trim($_POST['username']);
    $new_password = trim($_POST['new_password']);
    $new_password_confirm = trim($_POST['new_password_confirm']);

    // Input validation
    if (empty($username) || empty($new_password) || empty($new_password_confirm)) {
        $_SESSION['error'] = 'Semua field tidak boleh kosong!';
        header("Location: ../auth/forgot_password.php");
        exit;
    }

    if (strlen($new_password) < 6) {
        $_SESSION['error'] = 'Kata sandi baru harus minimal 6 karakter!';
        header("Location: ../auth/forgot_password.php");
        exit;
    }

    if ($new_password !== $new_password_confirm) {
        $_SESSION['error'] = 'Konfirmasi kata sandi baru tidak cocok!';
        header("Location: ../auth/forgot_password.php");
        exit;
    }

    // Check if username exists
    $check_query = "SELECT id FROM users WHERE username = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    if (!$check_stmt) {
        $_SESSION['error'] = 'Terjadi kesalahan pada server. Silakan coba lagi.';
        header("Location: ../auth/forgot_password.php");
        exit;
    }

    mysqli_stmt_bind_param($check_stmt, 's', $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) == 0) {
        $_SESSION['error'] = 'Username tidak ditemukan!';
        mysqli_stmt_close($check_stmt);
        header("Location: ../auth/forgot_password.php");
        exit;
    }
    mysqli_stmt_close($check_stmt);

    // Update password in database (stored as plaintext)
    $sql = "UPDATE users SET password = ? WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        $_SESSION['error'] = 'Terjadi kesalahan pada server. Silakan coba lagi.';
        header("Location: ../auth/forgot_password.php");
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'ss', $new_password, $username);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = 'Kata sandi berhasil diubah! Silakan masuk.';
        header("Location: ../auth/forgot_password.php?success=true");
    } else {
        $_SESSION['error'] = 'Gagal mengubah kata sandi. Silakan coba lagi.';
        header("Location: ../auth/forgot_password.php");
    }

    // Close statement
    mysqli_stmt_close($stmt);
} else {
    // Redirect to forgot password page if accessed directly
    $_SESSION['error'] = 'Silakan gunakan form yang tersedia!';
    header("Location: ../auth/forgot_password.php");
    exit;
}

// No need to close connection explicitly; PHP will handle it
exit;
?>