<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$user = null;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
    $user = mysqli_fetch_assoc($result);
}

if (isset($_POST['submit'])) {
    $id = (int)$_POST['id'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $query = "UPDATE users SET role = '$role' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        header("Location: dashboard.php");
    } else {
        die("Query failed: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Pengguna</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-semibold mb-4">Edit Pengguna</h2>
    <?php if ($user): ?>
      <form method="POST">
        <input type="hidden" name="id" value="<?= $user['id']; ?>">
        <div class="mb-4">
          <label class="block text-gray-700">Username</label>
          <input type="text" value="<?= htmlspecialchars($user['username']); ?>" class="w-full p-2 border rounded-lg" disabled>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Role</label>
          <select name="role" class="w-full p-2 border rounded-lg">
            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
            <option value="user" <?= $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
          </select>
        </div>
        <button type="submit" name="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Simpan</button>
      </form>
    <?php else: ?>
      <p class="text-red-500">Pengguna tidak ditemukan.</p>
    <?php endif; ?>
  </div>
</body>
</html>