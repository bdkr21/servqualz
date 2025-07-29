-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2025 at 10:58 AM
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
  `status` enum('aktif','tidak aktif') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_kuesioner`
--

INSERT INTO `data_kuesioner` (`id_data_kuesioner`, `nama_kuesioner`, `dimensi_layanan`, `status`) VALUES
(1, 'Kuesioner Reliability', 'reliabillity', 'aktif'),
(3, 'Kuesioner Tangibels', 'tangibels', 'aktif'),
(4, 'test responsiveness', 'responsiveness', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `data_pernyataan`
--

CREATE TABLE `data_pernyataan` (
  `id_data_pernyataan` int(11) NOT NULL,
  `dimensi_layanan` enum('reliability','assurance','tangibels','empathy','responsiveness') NOT NULL,
  `pernyataan` varchar(255) NOT NULL,
  `rekomendasi_perbaikan` varchar(255) NOT NULL,
  `status` enum('Aktif','Tidak Aktif') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_pernyataan`
--

INSERT INTO `data_pernyataan` (`id_data_pernyataan`, `dimensi_layanan`, `pernyataan`, `rekomendasi_perbaikan`, `status`) VALUES
(1, 'reliability', 'Stylist mampu menggunakan alat dengan baik pada saat melayani', 'Lakukan audit peralatan dan perawatan rutin setiap bulan.', 'Aktif'),
(2, 'reliability', 'Staf mampu menyampaikan informasi dengan jelas', 'Adakan pelatihan teknis bagi stylist setiap 3 bulan.', 'Aktif'),
(4, 'responsiveness', 'aku jelek', 'jadi ganteng', 'Aktif'),
(5, 'assurance', 'xcvxcv', 'xcvxcvxcv', 'Tidak Aktif');

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
(0, 1, 4, '2025-06-29');

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
(2, 'Massage & Stap'),
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
(1, 'xcvxcv', '081322900800', '2023-06-06', 'haircut'),
(2, 'zeva', '081322800799', '2025-06-01', 'Layanan A'),
(3, 'rudi', '082133322111', '2025-06-07', '');

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
(10, 'asep', 2, 'asep', '$2y$10$EK.wVp3JD14w4qLm4G6me.8y1WEOtej40Hc3TbSgrY3sLV87Q.yaG'),
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
(1, 'reliabillity'),
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
  `jenis_layanan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pelanggan`, `tanggal_transaksi`, `pembayaran`, `jenis_layanan`) VALUES
(3, 1, '2025-06-02', 'belum lunas', 'Service B,Service C'),
(4, 1, '2025-07-12', 'lunas', 'Haircut & Styling,Massage & Stap,Hairdo & Blow Styling,Eyelashes & Brow Styling'),
(5, 2, '2025-07-12', 'lunas', 'Massage & Stap,Nail care & Beauty,Hairdo & Blow Styling,Eyelashes & Brow Styling'),
(6, 3, '2025-06-29', 'belum lunas', 'Massage & Stap,Nail care & Beauty,Hairdo & Blow Styling');

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
  MODIFY `id_data_kuesioner` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `data_pernyataan`
--
ALTER TABLE `data_pernyataan`
  MODIFY `id_data_pernyataan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `keluhan`
--
ALTER TABLE `keluhan`
  MODIFY `id_keluhan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id_jenis_layanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
