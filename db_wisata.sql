-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2025 at 09:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_wisata`
--

-- --------------------------------------------------------

--
-- Table structure for table `komentar`
--

CREATE TABLE `komentar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `wisata_id` int(11) DEFAULT NULL,
  `isi_komentar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengaduan`
--

CREATE TABLE `pengaduan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `wisata_id` int(11) DEFAULT NULL,
  `isi_pengaduan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'khalid', 'khalid321', 'admin', '0000-00-00 00:00:00'),
(2, 'rehan', 'rehan123', 'user', '0000-00-00 00:00:00'),
(7, 'roihan', 'roihan123', 'user', '2025-04-18 16:25:29'),
(11, 'tes', 'tes321', 'admin', '2025-04-18 16:33:28'),
(22, 'bambang', '123123', 'user', '2025-04-19 16:28:01');

-- --------------------------------------------------------

--
-- Table structure for table `wisata`
--

CREATE TABLE `wisata` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `deskripsi` text NOT NULL,
  `latitude` float NOT NULL DEFAULT 0,
  `longitude` float NOT NULL DEFAULT 0,
  `foto` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wisata`
--

INSERT INTO `wisata` (`id`, `nama`, `alamat`, `deskripsi`, `latitude`, `longitude`, `foto`, `created_at`) VALUES
(5, 'Kuin Cerucuk', 'Jalan Kuin Cerucuk, Banjarmasdsin Selatan, Banjarmasin', 'Kawasan wisata sungai yang menawarkan pemandangan khas Banjarmasin dengan aktivitas warga di tepi sungai dan pasar terapung kecil.', -3.3325, 114.573, '', '2025-04-19 16:09:07'),
(6, 'Pelelangan Ikan RI Ilir', 'Kuin Selatan, Banjarmasin Selatan, Banjarmasin', 'Tempat pelelangan ikan tradisional di tepi sungai, tempat wisatawan dapat melihat aktivitas jual-beli ikan segar oleh nelayan lokal.asa', -3.335, 114.57, '', '2025-04-19 16:09:07'),
(7, 'Rumah Makan Wan Resto', 'Jalan Kuin Selatan, Banjarmasin Selatan, Banjarmasin', 'Rumah makan yang menyajikan hidangan khas Banjar seperti soto Banjar dan ikan bakar, cocok untuk wisata kuliner.', -3.33, 114.575, 'wan_resto.jpg', '2025-04-19 16:09:07'),
(8, 'Museum Kayu Baimbai', 'Banjarmasin Selatan, Banjarmasin', 'Museum kecil yang menyimpan koleksi benda-benda tradisional Banjar, termasuk kerajinan kayu.', -3.328, 114.58, '', '2025-04-19 16:09:07'),
(9, 'One Resto', 'Jalan Pangeran Antasari, Banjarmasin Selatan, Banjarmasin', 'Restoran modern yang menyajikan berbagai menu lokal dan internasional, cocok untuk bersantai.', -3.325, 114.585, '', '2025-04-19 16:09:07'),
(10, 'Tamban Kelayan', 'Kelayan Selatan, Banjarmasin Selatan, Banjarmasin', 'Dermaga kecil di Kelayan yang sering digunakan untuk aktivitas wisata air dan melihat pemandangan sungai.', -3.34, 114.59, 'tamban_kelayan.jpg', '2025-04-19 16:09:07'),
(11, 'Jembatan Bromo', 'Jalan Bromo, Banjarmasin Selatan, Banjarmasin', 'Jembatan bersejarah yang menjadi salah satu ikon Banjarmasin, menawarkan pemandangan sungai yang indah.', -3.32, 114.595, 'jembatan_bromo.jpg', '2025-04-19 16:09:07'),
(12, 'Makam Habib Battilantang', 'Banjarmasin Selatan, Banjarmasin', 'Makam tokoh agama yang sering dikunjungi untuk ziarah, menyimpan nilai sejarah dan religi.', -3.315, 114.59, 'makam_habib_battilantang.jpg', '2025-04-19 16:09:07'),
(13, 'Makam Datu Anggih Amin', 'Banjarmasin Selatan, Banjarmasin', 'Makam tokoh bersejarah yang menjadi tempat ziarah bagi masyarakat Banjarmasin.', -3.31, 114.585, 'makam_datu_anggih_amin.jpg', '2025-04-19 16:09:07'),
(14, 'RM. Soto Bawah Jembatan', 'Jalan Pasar Lama, Banjarmasin Timur, Banjarmasin', 'Rumah makan terkenal dengan soto Banjar yang lezat, terletak di bawah jembatan dengan suasana khas tepi sungai.', -3.305, 114.6, 'soto_bawah_jembatan.jpg', '2025-04-19 16:09:07'),
(15, 'Tugu 9 November 1945', 'Jalan Banua Anyar, Banjarmasin Timur, Banjarmasin', 'Monumen bersejarah yang memperingati perjuangan rakyat Banjarmasin pada 9 November 1945, cocok untuk wisata sejarah.', -3.3, 114.61, 'tugu_9_november_1945.jpg', '2025-04-19 16:09:07'),
(16, 'Keramba Ikan Banua Anyar', 'Sungai Banua Anyar, Banjarmasin Timur, Banjarmasin', 'Kawasan keramba ikan di Sungai Banua Anyar, tempat wisatawan dapat melihat budidaya ikan dan aktivitas nelayan.', -3.295, 114.615, 'keramba_ikan_banua_anyar.jpg', '2025-04-19 16:09:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `komentar`
--
ALTER TABLE `komentar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `wisata_id` (`wisata_id`);

--
-- Indexes for table `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `wisata_id` (`wisata_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wisata`
--
ALTER TABLE `wisata`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `komentar`
--
ALTER TABLE `komentar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pengaduan`
--
ALTER TABLE `pengaduan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `wisata`
--
ALTER TABLE `wisata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `komentar`
--
ALTER TABLE `komentar`
  ADD CONSTRAINT `komentar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `komentar_ibfk_2` FOREIGN KEY (`wisata_id`) REFERENCES `wisata` (`id`);

--
-- Constraints for table `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD CONSTRAINT `pengaduan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pengaduan_ibfk_2` FOREIGN KEY (`wisata_id`) REFERENCES `wisata` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
