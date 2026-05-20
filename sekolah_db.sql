-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2026 at 05:51 AM
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
-- Database: `sekolah_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `isi` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `judul`, `isi`, `tanggal`) VALUES
(1, 'Pendaftaran Gelombang 1 Ditutup', 'Terima kasih atas antusiasmenya. Pendaftaran PMB Gelombang 1 Tahun Akademik 2026/2027 telah resmi ditutup. Silakan cek jadwal ujian masuk Anda.', '2026-05-20 02:06:02'),
(2, 'Persyaratan Ujian Masuk', 'Setiap calon mahasiswa diwajibkan membawa Kartu Tanda Peserta Ujian dan Identitas Diri (KTP/Kartu Pelajar) asli pada saat pelaksanaan ujian. Berpakaian kemeja putih dan celana/rok kain hitam.', '2026-05-20 02:06:02');

-- --------------------------------------------------------

--
-- Table structure for table `ujian`
--

CREATE TABLE `ujian` (
  `id` int(11) NOT NULL,
  `nama_ujian` varchar(100) NOT NULL,
  `tanggal_ujian` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ujian`
--

INSERT INTO `ujian` (`id`, `nama_ujian`, `tanggal_ujian`) VALUES
(1, 'Tes Potensi Akademik (TPA)', '2026-06-20'),
(2, 'Tes Kemampuan Dasar (Matematika & Bahasa)', '2026-06-21'),
(3, 'Wawancara (Khusus Jalur Prestasi)', '2026-06-25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') DEFAULT 'student',
  `dokumen` varchar(255) DEFAULT NULL,
  `status_penerimaan` enum('pending','diterima','ditolak') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `dokumen`, `status_penerimaan`) VALUES
(1, 'min', '$2y$10$7MeZjZt7FTrmpXk6uiFlHeHQ8lCTZYdI2kMY3Xjh0GQxHlRxcM9qu', 'student', NULL, 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ujian`
--
ALTER TABLE `ujian`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ujian`
--
ALTER TABLE `ujian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
