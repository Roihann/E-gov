<?php
session_start();
include '../config/db.php';

// Debugging: Pastikan koneksi database berhasil
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: dashboard.php");
    exit;
}

$wisata_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($wisata_id <= 0) {
    header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("ID tempat wisata tidak valid."));
    exit;
}

// Ambil data tempat wisata yang ada
$wisata = mysqli_query($conn, "SELECT * FROM wisata WHERE id = $wisata_id");
if (!$wisata || mysqli_num_rows($wisata) == 0) {
    header("Location: dashboard.php?status=error&msg=" . urlencode("Tempat wisata tidak ditemukan."));
    exit;
}
$data = mysqli_fetch_assoc($wisata);

// Debugging: Log data yang diterima dari form
error_log("POST Data: " . print_r($_POST, true));
error_log("FILES Data: " . print_r($_FILES, true));

// Ambil data dari form
$nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
$alamat = mysqli_real_escape_string($conn, $_POST['alamat'] ?? '');
$deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi'] ?? '');
$kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan'] ?? 'Banjarmasin');

// Handle longitude dan latitude dengan lebih hati-hati
$longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? (float)$_POST['longitude'] : 0.0;
$latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? (float)$_POST['latitude'] : 0.0;

// Validasi longitude dan latitude
if ($longitude < -180 || $longitude > 180) {
    header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("Nilai longitude tidak valid (harus antara -180 dan 180)."));
    exit;
}
if ($latitude < -90 || $latitude > 90) {
    header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("Nilai latitude tidak valid (harus antara -90 dan 90)."));
    exit;
}

$fotos = isset($_FILES['fotos']) ? $_FILES['fotos'] : null;

// Validasi field wajib
if (empty($nama) || empty($alamat) || empty($deskripsi) || empty($kecamatan)) {
    header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("Semua field wajib diisi!"));
    exit;
}

// Ambil foto yang ada
$foto_names = !empty($data['foto']) ? explode(',', $data['foto']) : [];
$target_dir = "../Uploads/";

// Pastikan folder Uploads ada dan dapat ditulis
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}
if (!is_writable($target_dir)) {
    header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("Folder Uploads tidak dapat ditulis. Periksa izin folder."));
    exit;
}

// Handle new file uploads
if ($fotos && !empty($fotos['name'][0])) {
    $total_files = count($foto_names) + count($fotos['name']);
    if ($total_files > 5) {
        header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("Total foto tidak boleh lebih dari 5. Saat ini ada " . count($foto_names) . " foto."));
        exit;
    }

    foreach ($fotos['name'] as $key => $name) {
        if ($fotos['error'][$key] == UPLOAD_ERR_OK) {
            $target_file = $target_dir . basename($name);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validasi tipe dan ukuran file
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowed_types)) {
                header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("Tipe file $name tidak diizinkan. Gunakan JPG, JPEG, PNG, atau GIF."));
                exit;
            }
            if ($fotos['size'][$key] > 5 * 1024 * 1024) {
                header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("Ukuran file $name terlalu besar. Maksimum 5MB."));
                exit;
            }
            if (!move_uploaded_file($fotos['tmp_name'][$key], $target_file)) {
                header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("Gagal mengupload foto $name. Periksa izin folder Uploads atau konfigurasi server."));
                exit;
            }
            $foto_names[] = basename($name);
        } elseif ($fotos['error'][$key] != UPLOAD_ERR_NO_FILE) {
            header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("Error upload foto: " . $fotos['error'][$key] . "."));
            exit;
        }
    }
}

// Join filenames into a comma-separated string
$foto_string = implode(',', $foto_names);

// Gunakan prepared statement untuk keamanan lebih baik
$stmt = $conn->prepare("UPDATE wisata SET nama=?, alamat=?, deskripsi=?, longitude=?, latitude=?, kecamatan=?, foto=? WHERE id=?");
if (!$stmt) {
    header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("Gagal menyiapkan query: " . $conn->error));
    exit;
}

$stmt->bind_param("ssssdssi", $nama, $alamat, $deskripsi, $longitude, $latitude, $kecamatan, $foto_string, $wisata_id);

// Debugging: Log query yang akan dijalankan
error_log("Query: UPDATE wisata SET nama='$nama', alamat='$alamat', deskripsi='$deskripsi', longitude=$longitude, latitude=$latitude, kecamatan='$kecamatan', foto='$foto_string' WHERE id=$wisata_id");

if ($stmt->execute()) {
    header("Location: edit_wisata.php?id=$wisata_id&status=success&msg=" . urlencode("Tempat wisata berhasil diperbarui!"));
    exit;
} else {
    header("Location: edit_wisata.php?id=$wisata_id&status=error&msg=" . urlencode("Gagal mengupdate wisata: " . $stmt->error));
    exit;
}

$stmt->close();
?>