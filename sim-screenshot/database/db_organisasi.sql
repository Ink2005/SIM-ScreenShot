-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2026 at 09:51 AM
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
-- Database: `db_organisasi`
--
CREATE DATABASE IF NOT EXISTS `db_organisasi` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_organisasi`;

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `program_studi` varchar(100) DEFAULT NULL,
  `angkatan` varchar(4) DEFAULT NULL,
  `divisi` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'aktif',
  `tanggal_bergabung` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `nama`, `nim`, `program_studi`, `angkatan`, `divisi`, `status`, `tanggal_bergabung`) VALUES
(1, 'Hafizh Rizky Gunawan', '2404140053', NULL, '2024', 'Pengurus Harian', 'aktif', NULL),
(2, 'Kinanthi Eka Putri', '2404130113', NULL, '2024', 'Pengurus Harian', 'aktif', NULL),
(3, 'Anggita Rifqi Fauzan', '2404140095', NULL, '2024', 'Pengurus Harian', 'aktif', NULL),
(4, 'Zenzaena Atame Risan', '2404140088', NULL, '2024', 'Pengurus Harian', 'aktif', NULL),
(5, 'Qoulan Sadida', '2404140057', NULL, '2024', 'Pengurus Harian', 'aktif', NULL),
(6, 'Atha Nada Hafizhah', '2504140029', NULL, '2025', 'Pengurus Harian', 'aktif', NULL),
(7, 'Eugenie Friskyla', '2404140018', NULL, '2024', 'Jurnalistik', 'aktif', NULL),
(8, 'Shofi Alisha', '2404140067', NULL, '2024', 'Jurnalistik', 'aktif', NULL),
(9, 'Muhamad Aghis Najib', '2404140075', NULL, '2024', 'Jurnalistik', 'aktif', NULL),
(10, 'Neha Arsa Pratami', '2504140033', NULL, '2025', 'Jurnalistik', 'aktif', NULL),
(11, 'Revalia Marisa Siahaya', '2404130033', NULL, '2024', 'Jurnalistik', 'aktif', NULL),
(12, 'Atiya Maulaya Rusyda', '2404140072', NULL, '2024', 'Jurnalistik', 'aktif', NULL),
(13, 'Muhammad Khalid Misy\'al', '2404140014', NULL, '2024', 'Jurnalistik', 'aktif', NULL),
(14, 'Huwaida Nuuri Ainnov', '2504130153', NULL, '2025', 'Jurnalistik', 'aktif', NULL),
(15, 'Sherel Felicia Sugianto', '2404140031', NULL, '2024', 'DKV', 'aktif', NULL),
(16, 'Najwa Aqila', '2404130085', NULL, '2024', 'DKV', 'aktif', NULL),
(17, 'Farrel Fauzta Widyatama', '2504140025', NULL, '2025', 'DKV', 'aktif', NULL),
(18, 'Itsnan Nur Khanifah', '2404140025', NULL, '2024', 'DKV', 'aktif', NULL),
(19, 'Lanang Arif Prabowo', '2404130106', NULL, '2024', 'DKV', 'aktif', NULL),
(20, 'Muhammad Haq Usmanuhaka', '2404130145', NULL, '2024', 'DKV', 'aktif', NULL),
(21, 'Najwa Zakia', '2404130040', NULL, '2024', 'DKV', 'aktif', NULL),
(22, 'Salsa Agustina Widiyono', '2504130131', NULL, '2025', 'DKV', 'aktif', NULL),
(23, 'Muhammad Farrel Aryatama', '2404140092', NULL, '2024', 'Fotosin', 'aktif', NULL),
(24, 'Ibnu Aryska Putra', '2404130101', NULL, '2024', 'Fotosin', 'aktif', NULL),
(25, 'Kevin Reza Bahtiar', '2404130157', NULL, '2024', 'Fotosin', 'aktif', NULL),
(26, 'Muhammad Ariq Fachruddin', '2504140044', NULL, '2025', 'Fotosin', 'aktif', NULL),
(27, 'Muhammad Raiska Fahrezi', '2504130166', NULL, '2025', 'Fotosin', 'aktif', NULL),
(28, 'Muhammad Rifqi Rahman', '2404130149', NULL, '2024', 'Fotosin', 'aktif', NULL),
(29, 'Saffana Qolbi Atma Widi', '2504130006', NULL, '2025', 'Fotosin', 'aktif', NULL),
(30, 'Selma Prakusya', '2404130028', NULL, '2024', 'Fotosin', 'aktif', NULL),
(31, 'Yakobus Dimas Setiawan', '2404140044', NULL, '2024', 'Fotosin', 'aktif', NULL),
(32, 'Muhammad Daniar Firdaus', '2504130010', NULL, '2025', 'Fotosin', 'aktif', NULL),
(33, 'Fajri Asri', '2504140042', NULL, '2025', 'Fotosin', 'aktif', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `peran` varchar(30) NOT NULL,
  `id_anggota` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama`, `username`, `kata_sandi`, `peran`, `id_anggota`) VALUES
(1, 'Hafizh Rizky Gunawan', 'ketua', '21f97d78461cad00411af4c8ce197774712ce22fd58869cd963fdb57eca078d8', 'Ketua', 1),
(2, 'Anggita Rifqi Fauzan', 'sekretaris', 'b266dd6db331e165a8be1a57d46b4f49572108e710d52ff9e92f608891e1d97b', 'Sekretaris', 3),
(3, 'Qoulan Sadida', 'bendahara', 'dff9cd137cab8b96431ccd81f8bb433fc71f0329eefb54bd4380b3fc3a9fc5d1', 'Bendahara', 5);

-- --------------------------------------------------------

--
-- Table structure for table `surat`
--

CREATE TABLE `surat` (
  `id_surat` int(11) NOT NULL,
  `nomor_surat` varchar(100) NOT NULL,
  `jenis` varchar(30) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `pengirim_tujuan` varchar(150) DEFAULT NULL,
  `perihal` varchar(255) DEFAULT NULL,
  `berkas` varchar(255) DEFAULT NULL,
  `id_pengguna` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_kas`
--

CREATE TABLE `transaksi_kas` (
  `id_transaksi` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` varchar(20) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `id_pengguna` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`),
  ADD UNIQUE KEY `nim` (`nim`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `id_anggota` (`id_anggota`);

--
-- Indexes for table `surat`
--
ALTER TABLE `surat`
  ADD PRIMARY KEY (`id_surat`),
  ADD UNIQUE KEY `nomor_surat` (`nomor_surat`),
  ADD KEY `fk_surat_pengguna` (`id_pengguna`);

--
-- Indexes for table `transaksi_kas`
--
ALTER TABLE `transaksi_kas`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_kas_pengguna` (`id_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `surat`
--
ALTER TABLE `surat`
  MODIFY `id_surat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transaksi_kas`
--
ALTER TABLE `transaksi_kas`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `fk_pengguna_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `surat`
--
ALTER TABLE `surat`
  ADD CONSTRAINT `fk_surat_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON UPDATE CASCADE;

--
-- Constraints for table `transaksi_kas`
--
ALTER TABLE `transaksi_kas`
  ADD CONSTRAINT `fk_kas_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
