<?php
include '../config/db.php';

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
if (mysqli_query($conn, $sql)) {
    header("Location: ../auth/login.php");
} else {
    echo "Gagal daftar: " . mysqli_error($conn);
}
?>
