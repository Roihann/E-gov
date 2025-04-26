<?php
    $conn = new mysqli("localhost", "root", "", "db_wisata");
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $kecamatan = ['Banjarmasin Selatan', 'Banjarmasin Utara', 'Banjarmasin Timur', 'Banjarmasin Barat', 'Banjarmasin Tengah'];

    for ($i = 1; $i <= 1000; $i++) {
        $nama = "Wisata_$i";
        $alamat = "Jl. Wisata No.$i";
        $kec = $kecamatan[array_rand($kecamatan)];
        $deskripsi = "Deskripsi tempat wisata ke-$i di Banjarmasin";
        $longitude = 114.5900 + (rand(-100, 100) / 10000);
        $latitude = -3.3244 + (rand(-100, 100) / 10000);
        $foto = "foto_$i.jpg";

        $stmt = $conn->prepare("INSERT INTO wisata (nama, alamat, kecamatan, deskripsi, longitude, latitude, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssdds", $nama, $alamat, $kec, $deskripsi, $longitude, $latitude, $foto);
        $stmt->execute();
        $stmt->close();
    }

    echo "1000 data wisata berhasil dibuat di db_wisata!";
    $conn->close();
    ?>