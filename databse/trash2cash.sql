-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2025 at 02:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `trash2cash`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategori_sampah`
--

CREATE TABLE `kategori_sampah` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `poin_per_kg` int(11) NOT NULL DEFAULT 0,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_sampah`
--

INSERT INTO `kategori_sampah` (`id`, `nama_kategori`, `deskripsi`, `poin_per_kg`, `status`, `created_at`) VALUES
(2, 'kertas', 'Sampah kertas seperti koran, kardus, dan buku', 3, 'aktif', '2025-04-24 18:32:42'),
(3, 'logam', 'Sampah logam seperti kaleng, besi, dan aluminium', 10, 'aktif', '2025-04-24 18:32:42'),
(4, 'kaca', 'Sampah kaca seperti botol dan pecahan kaca', 4, 'aktif', '2025-04-24 18:32:42'),
(5, 'organik', 'Sampah organik seperti sisa makanan dan daun', 2, 'aktif', '2025-04-24 18:32:42'),
(6, 'elektronik', 'Sampah elektronik seperti baterai dan komponen', 16, 'aktif', '2025-04-24 18:32:42'),
(13, 'plastik', 'Sampah plastik seperti botol bekas, kantong plastik, dan wadah plastik lainnya', 5, 'aktif', '2025-04-24 18:58:51'),
(15, 'Mantan', 'Hanya untuk orang-orang porfesional', 100, 'aktif', '2025-04-25 22:39:06');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_sampah`
--

CREATE TABLE `laporan_sampah` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `jumlah_kg` float NOT NULL,
  `tanggal_pengumpulan` date NOT NULL,
  `drop_point` enum('Drop Point A','Drop Point B','Drop Point C') NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_point` int(11) DEFAULT 0,
  `status_verifikasi` enum('menunggu','diterima','ditolak') DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_sampah`
--

INSERT INTO `laporan_sampah` (`id`, `user_id`, `kategori_id`, `jumlah_kg`, `tanggal_pengumpulan`, `drop_point`, `foto`, `catatan`, `created_at`, `total_point`, `status_verifikasi`) VALUES
(2, 2, 2, 12, '2025-04-24', 'Drop Point B', '1745381498_stego_image (3).png', 'ini sampah anjink\r\n', '2025-04-23 04:11:38', 36, 'menunggu'),
(3, 2, 5, 100, '2025-04-26', 'Drop Point B', '1745381976_gambar_steganografi (3).png', 'anjinkkkk', '2025-04-23 04:19:36', 1500, 'ditolak'),
(4, 2, 2, 21, '2025-05-03', 'Drop Point A', '1745495803_580b57fcd9996e24bc43c4e7.png', 'emyu', '2025-04-24 11:56:43', 63, 'ditolak'),
(5, 2, 4, 12, '2025-04-25', 'Drop Point B', '1745496448_580b57fcd9996e24bc43c4e7.png', 'qwerty\r\n', '2025-04-24 12:07:28', 48, 'menunggu'),
(7, 2, 4, 200, '2025-05-01', 'Drop Point B', '1745511292_NICO ROBIN.jpg', 'bulan mei', '2025-04-24 16:14:52', 800, 'diterima'),
(8, 2, 3, 120, '2025-04-30', 'Drop Point C', '1745514352_WhatsApp Image 2025-01-27 at 21.35.09_b1a1f88e.jpg', 'catatan', '2025-04-24 17:05:52', 1200, 'menunggu'),
(10, 2, 6, 100, '2025-04-26', 'Drop Point B', '1745381976_gambar_steganografi (3).png', 'anjinkkkk', '2025-04-22 21:19:36', 0, 'ditolak'),
(11, 2, 2, 21, '2025-05-03', 'Drop Point A', '1745495803_580b57fcd9996e24bc43c4e7.png', 'emyu', '2025-04-24 04:56:43', 0, 'diterima'),
(12, 2, 4, 12, '2025-04-25', 'Drop Point B', '1745496448_580b57fcd9996e24bc43c4e7.png', 'qwerty', '2025-04-24 05:07:28', 0, 'menunggu'),
(13, 2, 4, 200, '2025-05-01', 'Drop Point B', '1745511292_NICO ROBIN.jpg', 'bulan mei', '2025-04-24 09:14:52', 0, 'ditolak'),
(14, 2, 3, 120, '2025-04-30', 'Drop Point C', '1745514352_WhatsApp Image 2025-01-27 at 21.35.09_b1a1f88e.jpg', 'catatan', '2025-04-24 10:05:52', 0, 'menunggu'),
(28, 2, 2, 2, '0000-00-00', 'Drop Point C', '1745770502_photo-strip-1745644495825.png', '26 april', '2025-04-27 16:15:02', 6, 'menunggu');

-- --------------------------------------------------------

--
-- Table structure for table `penukaran_poin`
--

CREATE TABLE `penukaran_poin` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `poin` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `waktu_penukaran` datetime DEFAULT current_timestamp(),
  `status` enum('menunggu','dicetak','diterima') DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penukaran_poin`
--

INSERT INTO `penukaran_poin` (`id`, `user_id`, `poin`, `nominal`, `waktu_penukaran`, `status`) VALUES
(1, 2, 150, 15000, '2025-04-24 21:17:09', 'dicetak'),
(2, 2, 150, 15000, '2025-04-24 21:19:33', 'dicetak'),
(3, 2, 190, 20000, '2025-04-24 21:32:17', 'dicetak'),
(4, 2, 50, 5000, '2025-04-24 21:33:23', 'dicetak'),
(5, 2, 100, 10000, '2025-04-24 21:37:38', 'dicetak'),
(6, 2, 150, 15000, '2025-04-24 21:39:57', 'dicetak'),
(7, 2, 380, 40000, '2025-04-24 22:50:02', 'dicetak'),
(8, 2, 380, 40000, '2025-04-27 18:53:32', 'dicetak'),
(9, 2, 380, 40000, '2025-04-27 18:53:47', 'dicetak'),
(10, 12, 100, 10000, '2025-04-27 19:01:39', 'dicetak'),
(11, 2, 380, 40000, '2025-04-27 23:20:22', 'dicetak');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_time` datetime DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `no_telepon`, `alamat`, `email_verified`, `verification_time`, `password`, `role`, `created_at`) VALUES
(2, 'khilqi', 'khilqi@gmail.com', '0988887878787', 'depok', 0, NULL, '$2y$10$j3lSpFaXrgjujbE3LthxmuOmyUHlaSv7uzqFdodJuyCACPFR1DWbG', 'user', '2025-04-23 00:00:36'),
(10, 'admin', 'admin@gmail.com', '0928789686', 'tasik anjay', 0, NULL, '$2y$10$Xz7OQmNgX/rXrPmFxZcx2eHNnTNIOP5CkdLjH3LGDPlRrucD5dcl.', 'admin', '2025-04-24 18:11:11'),
(11, 'sasa', '237006036@student.unsil.ac.id', '0881-0221-51118', 'tasik', 0, NULL, '$2y$10$LCo15rUP.bEPbwsiTIZz5.G0nxtHQuLY0.eZtL.E6RJKDPjkAPCyS', 'user', '2025-04-26 05:25:10'),
(12, 'desti', 'desti@gmail.com', '08566565625256', 'panjalu', 0, NULL, '$2y$10$VxbxDw6Mwh5T61nzgR.I3uUxAJZQdqj4CDhcno9wT/qiXPZ2XCvxW', 'user', '2025-04-27 12:00:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori_sampah`
--
ALTER TABLE `kategori_sampah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `laporan_sampah`
--
ALTER TABLE `laporan_sampah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `laporan_sampah_fk_kategori` (`kategori_id`);

--
-- Indexes for table `penukaran_poin`
--
ALTER TABLE `penukaran_poin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori_sampah`
--
ALTER TABLE `kategori_sampah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `laporan_sampah`
--
ALTER TABLE `laporan_sampah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `penukaran_poin`
--
ALTER TABLE `penukaran_poin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `laporan_sampah`
--
ALTER TABLE `laporan_sampah`
  ADD CONSTRAINT `laporan_sampah_fk_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_sampah` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_sampah_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penukaran_poin`
--
ALTER TABLE `penukaran_poin`
  ADD CONSTRAINT `penukaran_poin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
