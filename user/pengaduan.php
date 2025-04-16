<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$wisata_id = $_GET['id'];
// Ambil info wisata untuk ditampilkan
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
$data = mysqli_fetch_assoc($wisata);
?>

<h2>Laporkan Pengaduan</h2>
<p><strong>Tempat Wisata:</strong> <?= $data['nama']; ?></p>

<form action="../proses/pengaduan_proses.php" method="POST">
  <input type="hidden" name="wisata_id" value="<?= $wisata_id; ?>">
  <textarea name="isi_pengaduan" placeholder="Tulis pengaduan Anda..." required></textarea><br>
  <button type="submit">Kirim Pengaduan</button>
</form>

<br>
<a href="detail.php?id=<?= $wisata_id; ?>">⬅️ Kembali ke detail wisata</a>
