-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 03, 2025 at 07:48 PM
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
-- Database: `servqual`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_kuesioner`
--

CREATE TABLE `data_kuesioner` (
  `id_data_kuesioner` int(11) NOT NULL,
  `nama_kuesioner` varchar(255) NOT NULL,
  `dimensi_layanan` varchar(255) NOT NULL,
  `status` enum('publish','tidak publish','selesai') NOT NULL,
  `jenis_layanan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_kuesioner`
--

INSERT INTO `data_kuesioner` (`id_data_kuesioner`, `nama_kuesioner`, `dimensi_layanan`, `status`, `jenis_layanan`) VALUES
(13, 'Test Kuesioner1', 'tangibels', 'publish', 'Hair Coloring'),
(14, 'Test Kuesioner2', 'assurance', 'publish', 'Haircut & Styling'),
(15, 'Test Kuesioner3', 'tangibels', 'publish', 'Hair Treament'),
(16, 'aku ganteng', 'assurance', 'tidak publish', 'Massage & Spa'),
(17, 'Kuesioner Nail', 'empathy', 'publish', 'Nail care & Beauty');

-- --------------------------------------------------------

--
-- Table structure for table `data_pernyataan`
--

CREATE TABLE `data_pernyataan` (
  `id_data_pernyataan` int(11) NOT NULL,
  `dimensi_layanan` enum('reliability','assurance','tangibels','empathy','responsiveness') NOT NULL,
  `pernyataan` varchar(255) NOT NULL,
  `rekomendasi_perbaikan` varchar(255) NOT NULL,
  `status` enum('Aktif','Tidak Aktif') NOT NULL,
  `jenis_layanan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_pernyataan`
--

INSERT INTO `data_pernyataan` (`id_data_pernyataan`, `dimensi_layanan`, `pernyataan`, `rekomendasi_perbaikan`, `status`, `jenis_layanan`) VALUES
(9, 'tangibels', 'haircut a', 'haircut b', 'Aktif', 'Haircut & Styling'),
(12, 'empathy', 'kau cantik hari ini', 'dan aku suka', 'Aktif', 'Haircut & Styling,Massage & Spa'),
(24, 'responsiveness', 'sdfsdf', 'sdfsdf', 'Aktif', 'Hairdo & Blow Styling'),
(29, 'responsiveness', 'sdfsdf', 'sdfsdf', 'Aktif', 'Hair Treament'),
(35, 'assurance', 'a', 'a', 'Tidak Aktif', 'Haircut & Styling'),
(36, 'reliability', 'b', 'b', 'Tidak Aktif', 'Haircut & Styling'),
(39, 'assurance', 'nail a', 'nail a', 'Aktif', 'Nail care & Beauty'),
(40, 'tangibels', 'hair color a', 'hair color a', 'Aktif', 'Hair Coloring');

-- --------------------------------------------------------

--
-- Table structure for table `jawaban_kuesioner`
--

CREATE TABLE `jawaban_kuesioner` (
  `id_jawaban_kuesioner` int(11) NOT NULL,
  `id_kuesioner` int(11) NOT NULL,
  `id_pernyataan` int(11) NOT NULL,
  `jawaban_harapan` enum('STS','TS','CS','S','SS') NOT NULL,
  `jawaban_kenyataan` enum('STS','TS','CS','S','SS') NOT NULL,
  `gap` double NOT NULL,
  `rekomendasi_perbaikan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keluhan`
--

CREATE TABLE `keluhan` (
  `id_keluhan` int(11) NOT NULL,
  `keluhan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kuesioner`
--

CREATE TABLE `kuesioner` (
  `id_kuesioner` int(11) NOT NULL,
  `id_data_kuesioner` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `tgl_pengisian` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kuesioner`
--

INSERT INTO `kuesioner` (`id_kuesioner`, `id_data_kuesioner`, `id_pelanggan`, `tgl_pengisian`) VALUES
(5, 0, 0, '2025-08-03'),
(6, 0, 0, '2025-08-03'),
(7, 0, 0, '2025-08-03');

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id_jenis_layanan` int(11) NOT NULL,
  `jenis_layanan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id_jenis_layanan`, `jenis_layanan`) VALUES
(1, 'Haircut & Styling'),
(2, 'Massage & Spa'),
(3, 'Hair Coloring'),
(4, 'Nail care & Beauty'),
(5, 'Hairdo & Blow Styling'),
(6, 'Eyelashes & Brow Styling'),
(7, 'Hair Treament');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `no_telp` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_layanan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `no_telp`, `tanggal_lahir`, `jenis_layanan`) VALUES
(13, 'fazrin', '081322900800', '2001-07-03', 'Haircut & Styling,Massage & Stap,Hair Coloring,Nail care & Beauty'),
(14, 'sdfsdfsdf', '345345345', '2025-07-08', 'Haircut & Styling,Nail care & Beauty,Hair Treament'),
(15, 'fghfghf', '5345345345', '2025-07-01', 'Nail care & Beauty'),
(16, 'cvbcvb', '534534535', '2025-07-07', 'Hair Coloring,Eyelashes & Brow Styling,Hair Treament'),
(17, 'mnghjghj', '54363546346', '2025-07-10', 'Haircut & Styling,Massage & Stap,Hair Coloring,Nail care & Beauty'),
(18, 'fgfgfghfgretrt', '35345123324', '2025-07-21', 'Massage & Stap,Hair Coloring,Nail care & Beauty,Hairdo & Blow Styling,Eyelashes & Brow Styling,Hair Treament'),
(19, '3acvbcvbsdfsd', '34534534234234', '2023-07-06', 'Haircut & Styling'),
(20, 'a', '0323232323232', '2025-01-28', 'Massage & Stap'),
(21, 'nhnhnhnhnhn', '543534534543', '2025-04-23', 'Hairdo & Blow Styling'),
(22, 'ccvbcvb', '356456456456', '2025-06-25', 'Hair Treament'),
(23, 'dfgdfgdfg', '23424522221', '2025-05-31', 'Eyelashes & Brow Styling'),
(24, 'lulu', '0851-626-82920', '2021-07-01', 'Haircut & Styling'),
(25, 'zxc', '0851-626-82920', '2025-07-01', 'Nail care & Beauty'),
(26, 'bubu', '0851-626-82920', '2021-07-01', 'Nail care & Beauty'),
(27, 'lutfi', '0851-626-82920', '2003-07-04', 'Haircut & Styling'),
(28, 'Yang', '0812-144-44884', '2019-08-08', 'Haircut & Styling,Massage & Spa,Hair Coloring,Nail care & Beauty,Hairdo & Blow Styling,Eyelashes & Brow Styling,Hair Treament'),
(29, 'Sab', '5345-345-345', '2025-08-01', 'Haircut & Styling,Nail care & Beauty');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `roles_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `name`, `roles_id`, `username`, `password`) VALUES
(8, 'caca', 1, 'caca', '$2y$10$kKYPtwD0KpQv9xXwGiXkPeNJP15J96MEsU1YHwLKJ5juUF5y35pz2'),
(9, 'Maharani', 1, 'admin', '$2y$10$JXiedyoKsvwUdd4XzjeHMe0it9Un3B/flsAHhzChAE1R.EySsvzXK'),
(11, 'caca', 2, 'caca', '$2y$10$ZE.aic0ndtY4i15XAGdsu.WrG6annzm/9fJaDnKovyS6AVQQ6jvG.');

-- --------------------------------------------------------

--
-- Table structure for table `servqual`
--

CREATE TABLE `servqual` (
  `id_servqual` int(11) NOT NULL,
  `dimensi_layanan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `servqual`
--

INSERT INTO `servqual` (`id_servqual`, `dimensi_layanan`) VALUES
(2, 'assurance'),
(4, 'empathy'),
(1, 'reliability'),
(5, 'responsiveness'),
(3, 'tangibels');

-- --------------------------------------------------------

--
-- Table structure for table `servqual_roles`
--

CREATE TABLE `servqual_roles` (
  `id_jabatan` int(11) NOT NULL,
  `jabatan` enum('Owner','Administrasi') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `servqual_roles`
--

INSERT INTO `servqual_roles` (`id_jabatan`, `jabatan`) VALUES
(1, 'Owner'),
(2, 'Administrasi');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `tanggal_transaksi` date NOT NULL,
  `pembayaran` enum('lunas','belum lunas') NOT NULL,
  `jenis_layanan` varchar(255) NOT NULL,
  `kode_transaksi` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pelanggan`, `tanggal_transaksi`, `pembayaran`, `jenis_layanan`, `kode_transaksi`) VALUES
(16, 13, '2025-07-29', 'lunas', 'Haircut & Styling', 'TR001'),
(17, 14, '2025-07-29', 'lunas', 'Haircut & Styling,Nail care & Beauty,Hair Treament', 'TR002'),
(18, 15, '2025-07-29', 'lunas', 'Nail care & Beauty', 'TR003'),
(19, 16, '2025-07-29', 'lunas', 'Hair Coloring,Eyelashes & Brow Styling,Hair Treament', 'TR004'),
(20, 17, '2025-07-29', 'lunas', 'Haircut & Styling,Massage & Stap,Hair Coloring,Nail care & Beauty', 'TR005'),
(21, 18, '2025-07-29', 'belum lunas', 'Massage & Stap,Hair Coloring,Nail care & Beauty,Hairdo & Blow Styling,Eyelashes & Brow Styling,Hair Treament', 'TR006'),
(22, 19, '2025-07-29', 'lunas', 'Haircut & Styling', 'TR007'),
(23, 20, '2025-07-29', 'lunas', 'Massage & Stap', 'TR008'),
(24, 21, '2025-07-29', 'lunas', 'Hairdo & Blow Styling', 'TR009'),
(25, 22, '2025-07-29', 'lunas', 'Hair Treament', 'TR010'),
(26, 23, '2025-07-29', 'lunas', 'Eyelashes & Brow Styling', 'TR011'),
(27, 24, '2025-07-30', 'lunas', 'Haircut & Styling', 'TR012'),
(28, 25, '2025-07-30', 'lunas', 'Nail care & Beauty', 'TR013'),
(29, 26, '2025-07-30', 'lunas', 'Nail care & Beauty', 'TR014'),
(30, 27, '2025-07-30', 'lunas', 'Haircut & Styling', 'TR015'),
(31, 28, '2025-08-03', 'lunas', 'Haircut & Styling,Massage & Spa,Hair Coloring,Nail care & Beauty,Hairdo & Blow Styling,Eyelashes & Brow Styling,Hair Treament', 'TR016'),
(32, 29, '2025-08-03', 'lunas', 'Haircut & Styling,Nail care & Beauty', 'TR017');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_kuesioner`
--
ALTER TABLE `data_kuesioner`
  ADD PRIMARY KEY (`id_data_kuesioner`);

--
-- Indexes for table `data_pernyataan`
--
ALTER TABLE `data_pernyataan`
  ADD PRIMARY KEY (`id_data_pernyataan`);

--
-- Indexes for table `keluhan`
--
ALTER TABLE `keluhan`
  ADD PRIMARY KEY (`id_keluhan`);

--
-- Indexes for table `kuesioner`
--
ALTER TABLE `kuesioner`
  ADD PRIMARY KEY (`id_kuesioner`);

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id_jenis_layanan`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- Indexes for table `servqual`
--
ALTER TABLE `servqual`
  ADD PRIMARY KEY (`id_servqual`),
  ADD UNIQUE KEY `dimensi_layanan` (`dimensi_layanan`);

--
-- Indexes for table `servqual_roles`
--
ALTER TABLE `servqual_roles`
  ADD PRIMARY KEY (`id_jabatan`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_kuesioner`
--
ALTER TABLE `data_kuesioner`
  MODIFY `id_data_kuesioner` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `data_pernyataan`
--
ALTER TABLE `data_pernyataan`
  MODIFY `id_data_pernyataan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `keluhan`
--
ALTER TABLE `keluhan`
  MODIFY `id_keluhan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kuesioner`
--
ALTER TABLE `kuesioner`
  MODIFY `id_kuesioner` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id_jenis_layanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `servqual`
--
ALTER TABLE `servqual`
  MODIFY `id_servqual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `servqual_roles`
--
ALTER TABLE `servqual_roles`
  MODIFY `id_jabatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
