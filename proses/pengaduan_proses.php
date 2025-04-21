<?php
session_start();
include '../config/db.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Gagal mengirim pengaduan'];

try {
    // Pastikan user sudah login
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
        $response['message'] = 'Anda harus login terlebih dahulu';
        echo json_encode($response);
        exit;
    }

    // Ambil data dari form
    $wisata_id = isset($_POST['wisata_id']) ? (int)$_POST['wisata_id'] : 0;
    $isi_pengaduan = isset($_POST['isi_pengaduan']) ? trim($_POST['isi_pengaduan']) : '';
    $user_id = $_SESSION['user_id'];

    // Validasi input
    if ($wisata_id <= 0 || empty($isi_pengaduan)) {
        $response['message'] = 'Data tidak lengkap';
        echo json_encode($response);
        exit;
    }

    // Simpan ke database
    $query = "INSERT INTO pengaduan (wisata_id, user_id, isi_pengaduan, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'iis', $wisata_id, $user_id, $isi_pengaduan);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute statement failed: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);

    $response['status'] = 'success';
    $response['message'] = 'Pengaduan berhasil dikirim';
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>