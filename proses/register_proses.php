<?php
include '../config/db.php';

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
?>