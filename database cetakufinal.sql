-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 13, 2026 at 12:23 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cetakufinal`
--

-- --------------------------------------------------------

--
-- Table structure for table `alamats`
--

CREATE TABLE `alamats` (
  `id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `alamat_lengkap` text,
  `kelurahan` varchar(255) DEFAULT NULL,
  `kecamatan` varchar(255) DEFAULT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `provinsi` varchar(255) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `nomor_hp` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alamats`
--

INSERT INTO `alamats` (`id`, `user_id`, `label`, `alamat_lengkap`, `kelurahan`, `kecamatan`, `kota`, `provinsi`, `kode_pos`, `nomor_hp`, `created_at`) VALUES
(1, 1, 'Utama', 'Jl. Soedarto', 'Asemrowo', 'Tembalang', 'Semarang', 'Jawa Tengah', '43323223', '089526861571', '2025-05-10 13:40:34'),
(3, 27, 'Utama', 'temcy', 'srondol wetan', 'banyumanik', 'smg', 'jateng', '503244', '08123456', '2025-06-30 03:35:36'),
(4, 27, 'Kantor', 'sumurboto', 'tembalang', 'srondol', 'smg', 'jateng', '50344', '0812333', '2025-06-30 03:36:31');

-- --------------------------------------------------------

--
-- Table structure for table `bahans`
--

CREATE TABLE `bahans` (
  `id` bigint NOT NULL,
  `nama_bahan` varchar(255) DEFAULT NULL,
  `biaya_tambahan` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bahans`
--

INSERT INTO `bahans` (`id`, `nama_bahan`, `biaya_tambahan`) VALUES
(1, 'Cotton Combed 24s', 0.00),
(2, 'Cotton Combed 30s', 20000.00),
(3, 'Fleece', 25000.00),
(4, 'Drill', 30000.00),
(5, 'Plastik', 2000.00),
(9, 'Cutton', 0.00),
(10, 'Cotton Combed 24s', 15000.00),
(11, 'Cotton Combed 30s', 20000.00),
(12, 'Fleece', 25000.00),
(16, 'Uy', 200000.00),
(18, 'cek', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `biaya_desains`
--

CREATE TABLE `biaya_desains` (
  `id` bigint NOT NULL,
  `biaya` decimal(10,2) NOT NULL DEFAULT '60000.00',
  `deskripsi` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `biaya_desains`
--

INSERT INTO `biaya_desains` (`id`, `biaya`, `deskripsi`) VALUES
(5, 100000.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customs`
--

CREATE TABLE `customs` (
  `id` bigint NOT NULL,
  `item_id` bigint DEFAULT NULL,
  `ukuran_id` bigint DEFAULT NULL,
  `bahan_id` bigint DEFAULT NULL,
  `jenis_id` bigint DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customs`
--

INSERT INTO `customs` (`id`, `item_id`, `ukuran_id`, `bahan_id`, `jenis_id`, `harga`) VALUES
(29, 1, 1, 1, 1, 10000.00),
(30, 1, 5, 2, 9, 50000.00),
(31, 1, 1, 1, 1, 100000.00),
(32, 1, 5, 1, 1, 100000.00),
(33, 1, 1, 1, 9, 120000.00),
(34, 1, 1, 1, 1, 100000.00),
(35, 1, 1, 1, 1, 100000.00),
(36, 1, 1, 1, 1, 100000.00),
(37, 1, 1, 1, 1, 100000.00),
(38, 1, 1, 1, 1, 100000.00),
(39, 1, 1, 1, 1, 100000.00),
(40, 1, 1, 1, 1, 100000.00),
(41, 1, 5, 1, 1, 100000.00),
(42, 1, 1, 1, 1, 100000.00),
(43, 1, 1, 1, 1, 100000.00),
(44, 1, 1, 1, 1, 100000.00),
(45, 1, 1, 2, 9, 140000.00),
(46, 1, 1, 1, 1, 100000.00),
(47, 1, 1, 1, 1, 100000.00),
(48, 1, 5, 2, 1, 120000.00),
(50, 1, 1, 2, 9, 140000.00),
(51, 1, 1, 2, 1, 120000.00),
(56, 1, 5, 1, 1, 100000.00),
(57, 1, 5, 2, 9, 140000.00),
(58, 1, 1, 1, 1, 100000.00),
(59, 1, 5, 2, 9, 140000.00),
(60, 1, 1, 1, 1, 100000.00),
(61, 1, 1, 1, 9, 120000.00),
(62, 1, 1, 2, 9, 140000.00),
(63, 1, 1, 1, 1, 100000.00),
(64, 1, 1, 1, 9, 120000.00),
(65, 1, 1, 1, 9, 120000.00),
(66, 1, 1, 2, 9, 140000.00),
(67, 1, 1, 1, 1, 100000.00),
(68, 1, 5, 2, 9, 140000.00),
(69, 1, 1, 1, 1, 100000.00),
(70, 1, 1, 1, 1, 100000.00),
(71, 1, 1, 2, 9, 140000.00),
(72, 1, 1, 1, 9, 120000.00),
(73, 1, 1, 2, 9, 150000.00),
(74, 1, 1, 1, 1, 110000.00),
(75, 1, 5, 2, 9, 140000.00),
(76, 1, 5, 1, 1, 100000.00),
(77, 1, 5, 1, 1, 100000.00),
(78, 1, 5, 1, 1, 100000.00),
(79, 1, 1, 2, 9, 150000.00),
(80, 3, 2, 2, 9, 150000.00),
(81, 1, 1, 3, 9, 100000.00),
(82, 3, 2, 2, 1, 120000.00),
(83, 4, 2, 1, 2, 100000.00),
(84, 2, 5, 2, 1, 120000.00),
(85, 3, 5, 2, 1, 100000.00),
(86, 3, 1, 3, 1, 150000.00),
(87, 1, 5, 2, 2, 140000.00),
(88, 2, 5, 5, 9, 100000.00),
(89, 4, 2, 3, 9, 120000.00),
(90, 5, 5, 1, 2, 120000.00),
(91, 3, 1, 5, 2, 150000.00),
(92, 3, 5, 2, 1, 140000.00),
(93, 1, 1, 5, 2, 100000.00),
(94, 3, 5, 1, 9, 100000.00),
(95, 3, 1, 3, 9, 100000.00),
(96, 5, 2, 1, 9, 140000.00),
(97, 1, 5, 3, 9, 120000.00),
(98, 5, 5, 5, 9, 150000.00),
(99, 5, 1, 3, 2, 150000.00),
(100, 5, 1, 3, 2, 140000.00),
(101, 2, 2, 5, 1, 120000.00),
(102, 3, 5, 2, 1, 120000.00),
(103, 3, 5, 3, 2, 120000.00),
(104, 3, 5, 3, 2, 140000.00),
(105, 3, 1, 1, 9, 100000.00),
(106, 5, 5, 3, 1, 140000.00),
(107, 3, 1, 2, 1, 120000.00),
(108, 5, 1, 3, 2, 150000.00),
(109, 5, 2, 5, 9, 100000.00),
(110, 4, 5, 1, 1, 120000.00),
(111, 3, 5, 3, 2, 120000.00),
(112, 3, 5, 5, 2, 140000.00),
(113, 5, 2, 1, 1, 150000.00),
(114, 2, 5, 2, 9, 120000.00),
(115, 5, 2, 3, 1, 120000.00),
(116, 1, 1, 1, 2, 100000.00),
(117, 3, 1, 1, 2, 120000.00),
(118, 4, 5, 5, 1, 150000.00),
(119, 4, 1, 5, 2, 150000.00),
(120, 1, 5, 3, 9, 140000.00),
(121, 3, 1, 3, 2, 120000.00),
(122, 3, 2, 2, 9, 140000.00),
(123, 4, 5, 3, 1, 150000.00),
(124, 4, 2, 1, 2, 140000.00),
(125, 2, 2, 5, 1, 100000.00),
(126, 1, 2, 1, 9, 150000.00),
(127, 2, 1, 5, 9, 140000.00),
(128, 3, 1, 1, 1, 150000.00),
(129, 3, 5, 5, 2, 120000.00),
(130, 3, 2, 2, 9, 150000.00),
(131, 1, 5, 2, 9, 140000.00),
(132, 5, 2, 2, 2, 140000.00),
(133, 2, 2, 2, 1, 100000.00),
(134, 5, 1, 1, 2, 100000.00),
(135, 4, 5, 2, 9, 120000.00),
(136, 1, 2, 5, 9, 100000.00),
(137, 1, 5, 5, 2, 140000.00),
(138, 4, 1, 5, 2, 100000.00),
(139, 5, 2, 2, 2, 140000.00),
(140, 3, 1, 1, 9, 150000.00),
(141, 5, 1, 3, 9, 150000.00),
(142, 1, 2, 1, 9, 150000.00),
(143, 2, 2, 1, 1, 120000.00),
(144, 2, 5, 1, 1, 100000.00),
(145, 5, 5, 2, 9, 100000.00),
(146, 4, 2, 1, 9, 100000.00),
(147, 3, 1, 3, 9, 150000.00),
(148, 4, 5, 5, 1, 150000.00),
(149, 1, 1, 1, 9, 120000.00),
(150, 2, 1, 2, 2, 140000.00),
(151, 4, 2, 2, 1, 150000.00),
(152, 5, 2, 3, 9, 100000.00),
(153, 3, 1, 2, 1, 100000.00),
(154, 3, 1, 5, 1, 120000.00),
(155, 5, 2, 3, 2, 150000.00),
(156, 5, 5, 5, 2, 120000.00),
(157, 3, 2, 1, 9, 140000.00),
(158, 4, 5, 2, 1, 140000.00),
(159, 1, 5, 2, 9, 150000.00),
(160, 5, 2, 2, 1, 150000.00),
(161, 4, 5, 1, 1, 100000.00),
(162, 1, 1, 1, 2, 140000.00),
(163, 3, 1, 3, 1, 100000.00),
(164, 5, 1, 5, 1, 140000.00),
(165, 5, 1, 1, 2, 120000.00),
(166, 2, 1, 5, 1, 100000.00),
(167, 2, 5, 1, 9, 150000.00),
(168, 3, 2, 2, 1, 150000.00),
(169, 2, 5, 1, 9, 140000.00),
(170, 3, 2, 1, 2, 150000.00),
(171, 2, 1, 5, 9, 150000.00),
(172, 1, 1, 3, 2, 100000.00),
(173, 1, 1, 3, 2, 120000.00),
(174, 1, 1, 1, 9, 140000.00),
(175, 4, 1, 2, 9, 100000.00),
(176, 3, 5, 3, 2, 100000.00),
(177, 2, 1, 2, 2, 100000.00),
(178, 1, 5, 3, 1, 140000.00),
(179, 3, 2, 3, 2, 120000.00),
(184, 4, 2, 3, 9, 150000.00),
(185, 4, 1, 3, 1, 150000.00),
(186, 1, 5, 2, 1, 320000.00),
(187, 1, 5, 1, 1, 300000.00),
(188, 1, 5, 1, 1, 300000.00),
(189, 1, 5, 1, 1, 300000.00),
(190, 1, 5, 1, 1, 300000.00),
(191, 1, 5, 1, 1, 300000.00);

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanans`
--

CREATE TABLE `detail_pesanans` (
  `id` bigint NOT NULL,
  `pesanan_id` bigint DEFAULT NULL,
  `custom_id` bigint DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `upload_desain` varchar(255) DEFAULT NULL,
  `total_harga` decimal(10,2) DEFAULT NULL,
  `tipe_desain` enum('sendiri','dibuatkan') DEFAULT NULL,
  `biaya_jasa` decimal(10,2) DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `komentar` text,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `desain_revisi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_pesanans`
--

INSERT INTO `detail_pesanans` (`id`, `pesanan_id`, `custom_id`, `jumlah`, `upload_desain`, `total_harga`, `tipe_desain`, `biaya_jasa`, `rating`, `komentar`, `reviewed_at`, `desain_revisi`) VALUES
(125, 115, 71, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(126, 116, 72, 1, 'desain/1751242565_Untitled.jpeg', 120000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(127, 117, 73, 1, 'keranjang-designs/1_1751242648_2DbdH6eYDy.jpeg', 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(128, 118, 74, 1, 'desain/1751247144_polines.png', 110000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, 'desain/1751247163_Untitled.jpeg'),
(129, 119, 75, 1, 'keranjang-designs/1_1751243171_BEKKYIPZik.jpeg', 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(130, 120, 76, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(131, 121, 77, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(132, 122, 78, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(133, 123, 79, 1, 'desain/1751257005_stickerr.jpg', 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, 'desain/1751257052_pray.jpg'),
(134, 124, 80, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(135, 125, 81, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(136, 126, 82, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(137, 127, 83, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(138, 128, 84, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(139, 129, 85, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(140, 130, 86, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(141, 131, 87, 1, NULL, 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(142, 132, 88, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(143, 133, 89, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(144, 134, 90, 1, NULL, 120000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(145, 135, 91, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(146, 136, 92, 1, NULL, 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(147, 137, 93, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(148, 138, 94, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(149, 139, 95, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(150, 140, 96, 1, NULL, 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(151, 141, 97, 1, NULL, 120000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(152, 142, 98, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(153, 143, 99, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(154, 144, 100, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(155, 145, 101, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(156, 146, 102, 1, NULL, 120000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(157, 147, 103, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(158, 148, 104, 1, NULL, 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(159, 149, 105, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(160, 150, 106, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(161, 151, 107, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(162, 152, 108, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(163, 153, 109, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(164, 154, 110, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(165, 155, 111, 1, NULL, 120000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(166, 156, 112, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(167, 157, 113, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(168, 158, 114, 1, NULL, 120000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(169, 159, 115, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(170, 160, 116, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(171, 161, 117, 1, NULL, 120000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(172, 162, 118, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(173, 163, 119, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(174, 164, 120, 1, NULL, 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(175, 165, 121, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(176, 166, 122, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(177, 167, 123, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(178, 168, 124, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(179, 169, 125, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(180, 170, 126, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(181, 171, 127, 1, NULL, 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(182, 172, 128, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(183, 173, 129, 1, NULL, 120000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(184, 174, 130, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(185, 175, 131, 1, NULL, 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(186, 176, 132, 1, NULL, 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(187, 177, 133, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(188, 178, 134, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(189, 179, 135, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(190, 180, 136, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(191, 181, 137, 1, NULL, 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(192, 182, 138, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(193, 183, 139, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(194, 184, 140, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(195, 185, 141, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(196, 186, 142, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(197, 187, 143, 1, NULL, 120000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(198, 188, 144, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(199, 189, 145, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(200, 190, 146, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(201, 191, 147, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(202, 192, 148, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(203, 193, 149, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(204, 194, 150, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(205, 195, 151, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(206, 196, 152, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(207, 197, 153, 1, NULL, 100000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(208, 198, 154, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(209, 199, 155, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(210, 200, 156, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(211, 201, 157, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(212, 202, 158, 1, NULL, 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(213, 203, 159, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(214, 204, 160, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(215, 205, 161, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(216, 206, 162, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(217, 207, 163, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(218, 208, 164, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(219, 209, 165, 1, NULL, 120000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(220, 210, 166, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(221, 211, 167, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(222, 212, 168, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(223, 213, 169, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(224, 214, 170, 1, NULL, 150000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(225, 215, 171, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(226, 216, 172, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(227, 217, 173, 1, NULL, 120000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(228, 218, 174, 1, NULL, 140000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(229, 219, 175, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(230, 220, 176, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(231, 221, 177, 1, NULL, 100000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(232, 222, 178, 1, NULL, 140000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(233, 223, 179, 1, NULL, 120000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(238, 229, 184, 1, NULL, 150000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(239, 230, 185, 2, 'keranjang-designs/1_test30.jpeg', 300000.00, 'sendiri', 0.00, NULL, NULL, NULL, NULL),
(240, 231, 186, 1, NULL, 320000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(241, 232, 187, 1, NULL, 300000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(242, 233, 188, 1, 'desain/1753446401_66687618bb34e.jpeg', 300000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(243, 234, 189, 1, 'desain/1753446804_66687618bb34e.jpeg', 300000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(244, 235, 190, 1, 'desain/1753447234_66687618bb34e.jpeg', 300000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL),
(245, 236, 191, 1, 'desain/1753447532_66687618bb34e.jpeg', 300000.00, 'dibuatkan', 100000.00, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ekspedisis`
--

CREATE TABLE `ekspedisis` (
  `id` bigint NOT NULL,
  `pesanan_id` bigint DEFAULT NULL,
  `nama_ekspedisi` varchar(255) DEFAULT NULL,
  `layanan` varchar(255) DEFAULT NULL,
  `estimasi` varchar(255) DEFAULT NULL,
  `ongkos_kirim` decimal(10,2) DEFAULT NULL,
  `berat` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ekspedisis`
--

INSERT INTO `ekspedisis` (`id`, `pesanan_id`, `nama_ekspedisi`, `layanan`, `estimasi`, `ongkos_kirim`, `berat`) VALUES
(20, 115, 'J&T Express', NULL, NULL, 16000.00, 1000),
(21, 122, 'J&T Express', NULL, NULL, 16000.00, 1000),
(22, 123, 'J&T Express', NULL, NULL, 16000.00, 1000),
(27, 229, 'J&T Express', NULL, NULL, 18000.00, 1000),
(28, 230, 'J&T Express', NULL, NULL, 18000.00, 1000),
(29, 231, 'J&T Express', NULL, NULL, 18000.00, 1000),
(30, 232, 'J&T Express', NULL, NULL, 18000.00, 1000),
(31, 233, 'J&T Express', NULL, NULL, 18000.00, 1000),
(32, 234, 'J&T Express', NULL, NULL, 18000.00, 1000),
(33, 235, 'J&T Express', NULL, NULL, 18000.00, 1000),
(34, 236, 'J&T Express', NULL, NULL, 18000.00, 1000);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint NOT NULL,
  `nama_item` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `gambar` varchar(255) DEFAULT NULL,
  `harga_dasar` decimal(10,2) DEFAULT '0.00',
  `jenis_id` bigint DEFAULT NULL,
  `berat` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `nama_item`, `deskripsi`, `gambar`, `harga_dasar`, `jenis_id`, `berat`) VALUES
(1, 'Kaos', NULL, 'product-images/jnUBPgeBxbbdAOsZ7ndW.jpg', 100000.00, NULL, 190),
(2, 'Jaket', NULL, 'product-images/nUkptkK7ULAEiHk2okqS.jpeg', 150000.00, NULL, 123),
(3, 'Topi', NULL, 'product-images/hfw5fBNoPZpwXgR86ipr.jpeg', 35000.00, NULL, 0),
(4, 'Banner', NULL, 'product-images/sFOppcIaph3suFIJXq7J.jpg', 80000.00, NULL, 0),
(5, 'Stiker', NULL, 'product-images/fg1GmEfv8SQQKMMufqIM.jpg', 15000.00, NULL, 0),
(29, 'Jilid Makalah', NULL, 'product-images/D4qlRxFuJIt552Zq5xrh.jpeg', 15000.00, NULL, 0),
(30, 'polaroid', NULL, 'product-images/49Hsxus0ajtuqziajGqN.jpeg', 2500.00, NULL, 0),
(40, 'shafa', NULL, 'product-images/I3ulSQclnwO9MwxEE4ne.jpg', 50000.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `item_bahans`
--

CREATE TABLE `item_bahans` (
  `id` bigint NOT NULL,
  `item_id` bigint DEFAULT NULL,
  `bahan_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `item_bahans`
--

INSERT INTO `item_bahans` (`id`, `item_id`, `bahan_id`) VALUES
(16, 1, 1),
(18, 4, 16),
(20, 1, 2),
(21, 3, 3),
(22, 1, 18);

-- --------------------------------------------------------

--
-- Table structure for table `item_jenis`
--

CREATE TABLE `item_jenis` (
  `id` bigint NOT NULL,
  `item_id` bigint DEFAULT NULL,
  `jenis_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `item_jenis`
--

INSERT INTO `item_jenis` (`id`, `item_id`, `jenis_id`) VALUES
(6, 1, 9),
(9, 1, 1),
(10, 3, 2),
(11, 1, 10),
(12, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `item_ukurans`
--

CREATE TABLE `item_ukurans` (
  `id` bigint NOT NULL,
  `item_id` bigint DEFAULT NULL,
  `ukuran_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `item_ukurans`
--

INSERT INTO `item_ukurans` (`id`, `item_id`, `ukuran_id`) VALUES
(17, 1, 5),
(23, 3, 2),
(24, 3, 19),
(26, 4, 1),
(27, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `jenis`
--

CREATE TABLE `jenis` (
  `id` bigint NOT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `biaya_tambahan` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jenis`
--

INSERT INTO `jenis` (`id`, `kategori`, `biaya_tambahan`) VALUES
(1, 'Lengan Pendek', 200000.00),
(2, 'Lengan Panjang', 0.00),
(3, 'Hoodie', 0.00),
(4, 'Lengan Pendek', 0.00),
(5, 'Lengan Panjang', 10000.00),
(6, 'Hoodie', 25000.00),
(9, 'lengan bolong', 20000.00),
(10, 's', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategoris`
--

CREATE TABLE `kategoris` (
  `id` bigint NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `deskripsi` text,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategoris`
--

INSERT INTO `kategoris` (`id`, `nama_kategori`, `deskripsi`, `gambar`, `created_at`, `updated_at`) VALUES
(2, 'pakaian', NULL, 'kategori-images/4aKYy3j9QjAELtxCLoVR.jpeg', '2025-05-17 02:06:06', '2025-07-04 08:59:43'),
(4, 'banner/MMT', NULL, 'kategori-images/JSNJZ536fG9U5eTUJRlr.jpg', '2025-05-18 18:35:41', '2025-07-04 09:00:07'),
(5, 'Sticker', NULL, 'kategori-images/wJw1DDL1pWXklYKnjKMX.jpg', '2025-05-18 18:35:48', '2025-07-04 09:00:23'),
(6, 'Fotografi', NULL, 'kategori-images/zkgvHoTkqOZ2TKmCLYgw.jpg', '2025-05-18 18:35:54', '2025-07-04 09:00:50'),
(7, 'Print On Paper', NULL, 'kategori-images/aLlFEKsobz15RmXUk54L.jpg', '2025-05-18 18:36:01', '2025-07-04 09:01:07');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_items`
--

CREATE TABLE `kategori_items` (
  `id` bigint NOT NULL,
  `kategori_id` bigint NOT NULL,
  `item_id` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori_items`
--

INSERT INTO `kategori_items` (`id`, `kategori_id`, `item_id`) VALUES
(6, 2, 2),
(7, 2, 1),
(8, 2, 3),
(9, 4, 4),
(10, 5, 5),
(11, 6, 30),
(12, 7, 29);

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `item_id` bigint NOT NULL,
  `ukuran_id` bigint NOT NULL,
  `bahan_id` bigint NOT NULL,
  `jenis_id` bigint NOT NULL,
  `tipe_desain` enum('sendiri','dibuatkan') DEFAULT 'sendiri',
  `jumlah` int NOT NULL DEFAULT '1',
  `upload_desain` varchar(255) DEFAULT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `keranjang`
--

INSERT INTO `keranjang` (`id`, `user_id`, `item_id`, `ukuran_id`, `bahan_id`, `jenis_id`, `tipe_desain`, `jumlah`, `upload_desain`, `harga_satuan`, `total_harga`, `created_at`, `updated_at`) VALUES
(130, 5, 1, 5, 2, 9, 'dibuatkan', 2, NULL, 120000.00, 240000.00, '2025-06-30 22:59:32', NULL),
(131, 5, 5, 1, 3, 2, 'dibuatkan', 3, NULL, 50000.00, 150000.00, '2025-06-23 22:59:32', NULL),
(132, 13, 2, 5, 1, 1, 'dibuatkan', 3, NULL, 150000.00, 450000.00, '2025-06-28 22:59:32', NULL),
(133, 24, 1, 1, 3, 1, 'dibuatkan', 3, NULL, 120000.00, 360000.00, '2025-07-02 22:59:32', NULL),
(134, 15, 5, 1, 2, 1, 'dibuatkan', 3, NULL, 120000.00, 360000.00, '2025-06-24 22:59:32', NULL),
(135, 10, 5, 1, 1, 2, 'sendiri', 1, 'keranjang-designs/10_test5.jpeg', 150000.00, 150000.00, '2025-06-26 22:59:32', NULL),
(136, 5, 2, 1, 3, 1, 'sendiri', 2, 'keranjang-designs/5_test6.jpeg', 100000.00, 200000.00, '2025-06-28 22:59:32', NULL),
(137, 24, 5, 2, 3, 1, 'sendiri', 3, 'keranjang-designs/24_test7.jpeg', 100000.00, 300000.00, '2025-06-28 22:59:32', NULL),
(138, 15, 5, 2, 3, 1, 'dibuatkan', 2, NULL, 50000.00, 100000.00, '2025-06-25 22:59:32', NULL),
(139, 5, 3, 1, 3, 1, 'sendiri', 2, 'keranjang-designs/5_test9.jpeg', 120000.00, 240000.00, '2025-07-01 22:59:32', NULL),
(140, 13, 4, 1, 3, 9, 'sendiri', 3, 'keranjang-designs/13_test10.jpeg', 80000.00, 240000.00, '2025-06-27 22:59:32', NULL),
(141, 10, 4, 2, 1, 2, 'sendiri', 2, 'keranjang-designs/10_test11.jpeg', 80000.00, 160000.00, '2025-06-27 22:59:32', NULL),
(142, 15, 2, 1, 1, 1, 'sendiri', 2, 'keranjang-designs/15_test12.jpeg', 120000.00, 240000.00, '2025-06-27 22:59:32', NULL),
(143, 18, 2, 2, 2, 1, 'sendiri', 1, 'keranjang-designs/18_test13.jpeg', 80000.00, 80000.00, '2025-06-26 22:59:32', NULL),
(144, 10, 3, 2, 5, 1, 'sendiri', 2, 'keranjang-designs/10_test14.jpeg', 50000.00, 100000.00, '2025-06-26 22:59:32', NULL),
(145, 22, 1, 5, 5, 9, 'sendiri', 2, 'keranjang-designs/22_test15.jpeg', 150000.00, 300000.00, '2025-06-30 22:59:32', NULL),
(146, 10, 1, 1, 3, 1, 'dibuatkan', 2, NULL, 50000.00, 100000.00, '2025-06-28 22:59:32', NULL),
(147, 5, 2, 2, 5, 1, 'sendiri', 3, 'keranjang-designs/5_test17.jpeg', 80000.00, 240000.00, '2025-07-02 22:59:32', NULL),
(148, 24, 2, 2, 2, 2, 'sendiri', 3, 'keranjang-designs/24_test18.jpeg', 50000.00, 150000.00, '2025-07-03 22:59:32', NULL),
(149, 22, 4, 5, 5, 2, 'sendiri', 3, 'keranjang-designs/22_test19.jpeg', 150000.00, 450000.00, '2025-06-23 22:59:32', NULL),
(150, 10, 4, 1, 2, 1, 'sendiri', 2, 'keranjang-designs/10_test20.jpeg', 150000.00, 300000.00, '2025-06-28 22:59:32', NULL),
(151, 5, 2, 5, 5, 1, 'sendiri', 3, 'keranjang-designs/5_test21.jpeg', 80000.00, 240000.00, '2025-06-25 22:59:32', NULL),
(152, 22, 3, 2, 3, 9, 'sendiri', 1, 'keranjang-designs/22_test22.jpeg', 80000.00, 80000.00, '2025-06-30 22:59:32', NULL),
(153, 25, 3, 5, 2, 2, 'dibuatkan', 3, NULL, 150000.00, 450000.00, '2025-07-03 22:59:32', NULL),
(154, 24, 5, 1, 1, 2, 'sendiri', 1, 'keranjang-designs/24_test24.jpeg', 120000.00, 120000.00, '2025-06-28 22:59:32', NULL),
(155, 2, 5, 2, 2, 2, 'sendiri', 2, 'keranjang-designs/2_test25.jpeg', 120000.00, 240000.00, '2025-06-28 22:59:32', NULL),
(156, 2, 3, 1, 2, 1, 'sendiri', 3, 'keranjang-designs/2_test26.jpeg', 80000.00, 240000.00, '2025-06-28 22:59:32', NULL),
(157, 2, 3, 2, 5, 2, 'dibuatkan', 3, NULL, 100000.00, 300000.00, '2025-06-29 22:59:32', NULL),
(158, 15, 2, 1, 1, 2, 'sendiri', 3, 'keranjang-designs/15_test28.jpeg', 100000.00, 300000.00, '2025-06-23 22:59:32', NULL),
(159, 13, 5, 2, 2, 1, 'sendiri', 2, 'keranjang-designs/13_test29.jpeg', 50000.00, 100000.00, '2025-06-29 22:59:32', NULL),
(161, 5, 4, 2, 5, 2, 'sendiri', 3, 'keranjang-designs/5_test31.jpeg', 50000.00, 150000.00, '2025-07-03 22:59:32', NULL),
(162, 13, 4, 5, 3, 1, 'dibuatkan', 2, NULL, 80000.00, 160000.00, '2025-06-24 22:59:32', NULL),
(163, 13, 4, 5, 5, 1, 'sendiri', 1, 'keranjang-designs/13_test33.jpeg', 150000.00, 150000.00, '2025-06-24 22:59:32', NULL),
(164, 1, 1, 2, 5, 1, 'sendiri', 2, 'keranjang-designs/1_test34.jpeg', 120000.00, 240000.00, '2025-06-30 22:59:32', NULL),
(165, 24, 2, 5, 5, 9, 'sendiri', 3, 'keranjang-designs/24_test35.jpeg', 150000.00, 450000.00, '2025-07-02 22:59:32', NULL),
(166, 25, 1, 5, 2, 1, 'dibuatkan', 2, NULL, 50000.00, 100000.00, '2025-07-01 22:59:32', NULL),
(167, 18, 3, 5, 5, 9, 'sendiri', 1, 'keranjang-designs/18_test37.jpeg', 120000.00, 120000.00, '2025-06-25 22:59:32', NULL),
(168, 2, 5, 2, 3, 2, 'dibuatkan', 1, NULL, 80000.00, 80000.00, '2025-07-02 22:59:32', NULL),
(169, 25, 3, 1, 1, 9, 'sendiri', 3, 'keranjang-designs/25_test39.jpeg', 150000.00, 450000.00, '2025-06-28 22:59:32', NULL),
(170, 2, 1, 5, 1, 2, 'dibuatkan', 2, NULL, 150000.00, 300000.00, '2025-06-30 22:59:32', NULL),
(171, 22, 2, 2, 3, 9, 'sendiri', 2, 'keranjang-designs/22_test41.jpeg', 120000.00, 240000.00, '2025-07-03 22:59:32', NULL),
(172, 15, 2, 2, 1, 2, 'dibuatkan', 1, NULL, 100000.00, 100000.00, '2025-06-23 22:59:32', NULL),
(173, 15, 2, 5, 3, 2, 'sendiri', 1, 'keranjang-designs/15_test43.jpeg', 100000.00, 100000.00, '2025-07-02 22:59:32', NULL),
(174, 22, 2, 2, 1, 2, 'sendiri', 3, 'keranjang-designs/22_test44.jpeg', 100000.00, 300000.00, '2025-06-24 22:59:32', NULL),
(175, 5, 5, 1, 3, 2, 'sendiri', 1, 'keranjang-designs/5_test45.jpeg', 80000.00, 80000.00, '2025-06-26 22:59:32', NULL),
(176, 13, 1, 2, 3, 1, 'dibuatkan', 1, NULL, 50000.00, 50000.00, '2025-06-29 22:59:32', NULL),
(177, 10, 5, 5, 1, 1, 'dibuatkan', 3, NULL, 100000.00, 300000.00, '2025-06-27 22:59:32', NULL),
(178, 15, 3, 2, 2, 2, 'dibuatkan', 2, NULL, 100000.00, 200000.00, '2025-06-30 22:59:32', NULL),
(179, 22, 2, 2, 5, 2, 'dibuatkan', 3, NULL, 50000.00, 150000.00, '2025-06-29 22:59:32', NULL),
(180, 10, 1, 5, 1, 9, 'sendiri', 2, 'keranjang-designs/10_test50.jpeg', 50000.00, 100000.00, '2025-07-02 22:59:32', NULL),
(181, 5, 2, 1, 3, 9, 'sendiri', 1, 'keranjang-designs/5_test51.jpeg', 120000.00, 120000.00, '2025-07-02 22:59:32', NULL),
(182, 2, 3, 5, 2, 9, 'sendiri', 3, 'keranjang-designs/2_test52.jpeg', 50000.00, 150000.00, '2025-07-03 22:59:32', NULL),
(183, 5, 3, 2, 1, 2, 'sendiri', 2, 'keranjang-designs/5_test53.jpeg', 50000.00, 100000.00, '2025-06-30 22:59:32', NULL),
(184, 23, 5, 1, 3, 2, 'dibuatkan', 2, NULL, 50000.00, 100000.00, '2025-06-25 22:59:32', NULL),
(185, 1, 3, 5, 2, 2, 'dibuatkan', 3, NULL, 100000.00, 300000.00, '2025-06-23 22:59:32', NULL),
(186, 15, 5, 2, 1, 1, 'dibuatkan', 1, NULL, 100000.00, 100000.00, '2025-06-29 22:59:32', NULL),
(187, 2, 2, 5, 2, 9, 'sendiri', 3, 'keranjang-designs/2_test57.jpeg', 100000.00, 300000.00, '2025-06-29 22:59:32', NULL),
(188, 2, 5, 5, 2, 2, 'sendiri', 1, 'keranjang-designs/2_test58.jpeg', 100000.00, 100000.00, '2025-07-03 22:59:32', NULL),
(189, 5, 2, 1, 5, 1, 'sendiri', 2, 'keranjang-designs/5_test59.jpeg', 100000.00, 200000.00, '2025-06-28 22:59:32', NULL),
(190, 2, 1, 5, 2, 9, 'sendiri', 1, 'keranjang-designs/2_test60.jpeg', 50000.00, 50000.00, '2025-06-26 22:59:32', NULL),
(191, 25, 5, 2, 3, 9, 'sendiri', 2, 'keranjang-designs/25_test61.jpeg', 100000.00, 200000.00, '2025-06-30 22:59:32', NULL),
(192, 22, 5, 5, 2, 2, 'dibuatkan', 2, NULL, 120000.00, 240000.00, '2025-06-28 22:59:32', NULL),
(193, 10, 3, 2, 5, 1, 'dibuatkan', 2, NULL, 150000.00, 300000.00, '2025-06-29 22:59:32', NULL),
(194, 10, 3, 1, 2, 2, 'dibuatkan', 1, NULL, 150000.00, 150000.00, '2025-06-23 22:59:32', NULL),
(195, 24, 4, 2, 1, 9, 'dibuatkan', 2, NULL, 80000.00, 160000.00, '2025-06-28 22:59:32', NULL),
(196, 18, 4, 2, 3, 1, 'sendiri', 1, 'keranjang-designs/18_test66.jpeg', 100000.00, 100000.00, '2025-07-03 22:59:32', NULL),
(197, 22, 3, 1, 1, 1, 'dibuatkan', 1, NULL, 120000.00, 120000.00, '2025-07-03 22:59:32', NULL),
(198, 22, 4, 1, 1, 1, 'sendiri', 3, 'keranjang-designs/22_test68.jpeg', 50000.00, 150000.00, '2025-07-03 22:59:32', NULL),
(199, 5, 3, 5, 3, 9, 'dibuatkan', 3, NULL, 80000.00, 240000.00, '2025-06-28 22:59:32', NULL),
(200, 15, 5, 2, 3, 1, 'dibuatkan', 1, NULL, 100000.00, 100000.00, '2025-07-01 22:59:32', NULL),
(201, 2, 1, 1, 5, 1, 'sendiri', 1, 'keranjang-designs/2_test71.jpeg', 50000.00, 50000.00, '2025-07-03 22:59:32', NULL),
(202, 15, 5, 5, 5, 9, 'dibuatkan', 2, NULL, 50000.00, 100000.00, '2025-07-01 22:59:32', NULL),
(203, 15, 3, 2, 3, 1, 'dibuatkan', 3, NULL, 50000.00, 150000.00, '2025-06-23 22:59:32', NULL),
(204, 18, 2, 2, 3, 1, 'sendiri', 3, 'keranjang-designs/18_test74.jpeg', 150000.00, 450000.00, '2025-07-02 22:59:32', NULL),
(205, 2, 5, 5, 3, 2, 'sendiri', 2, 'keranjang-designs/2_test75.jpeg', 50000.00, 100000.00, '2025-06-30 22:59:32', NULL),
(206, 23, 1, 5, 2, 1, 'sendiri', 3, 'keranjang-designs/23_test76.jpeg', 80000.00, 240000.00, '2025-07-02 22:59:32', NULL),
(207, 18, 3, 1, 2, 2, 'sendiri', 1, 'keranjang-designs/18_test77.jpeg', 50000.00, 50000.00, '2025-06-24 22:59:32', NULL),
(208, 15, 5, 5, 5, 1, 'dibuatkan', 3, NULL, 80000.00, 240000.00, '2025-06-24 22:59:32', NULL),
(210, 25, 1, 1, 2, 9, 'dibuatkan', 2, NULL, 100000.00, 200000.00, '2025-06-24 22:59:32', NULL),
(211, 1, 3, 1, 2, 9, 'dibuatkan', 2, NULL, 50000.00, 100000.00, '2025-06-27 22:59:32', NULL),
(212, 24, 3, 1, 2, 9, 'sendiri', 1, 'keranjang-designs/24_test82.jpeg', 80000.00, 80000.00, '2025-06-26 22:59:32', NULL),
(213, 10, 1, 1, 5, 2, 'dibuatkan', 3, NULL, 120000.00, 360000.00, '2025-06-26 22:59:32', NULL),
(214, 25, 2, 5, 3, 2, 'sendiri', 1, 'keranjang-designs/25_test84.jpeg', 150000.00, 150000.00, '2025-06-27 22:59:32', NULL),
(215, 2, 1, 2, 2, 1, 'sendiri', 1, 'keranjang-designs/2_test85.jpeg', 50000.00, 50000.00, '2025-07-02 22:59:32', NULL),
(216, 15, 4, 1, 1, 2, 'sendiri', 3, 'keranjang-designs/15_test86.jpeg', 50000.00, 150000.00, '2025-06-28 22:59:32', NULL),
(217, 15, 1, 1, 2, 1, 'sendiri', 3, 'keranjang-designs/15_test87.jpeg', 100000.00, 300000.00, '2025-06-28 22:59:32', NULL),
(218, 23, 4, 5, 5, 1, 'sendiri', 3, 'keranjang-designs/23_test88.jpeg', 50000.00, 150000.00, '2025-07-01 22:59:32', NULL),
(219, 22, 3, 1, 1, 1, 'sendiri', 1, 'keranjang-designs/22_test89.jpeg', 80000.00, 80000.00, '2025-07-03 22:59:32', NULL),
(220, 13, 1, 1, 1, 1, 'dibuatkan', 2, NULL, 50000.00, 100000.00, '2025-06-27 22:59:32', NULL),
(221, 24, 5, 2, 5, 1, 'sendiri', 1, 'keranjang-designs/24_test91.jpeg', 120000.00, 120000.00, '2025-06-25 22:59:32', NULL),
(222, 23, 2, 1, 2, 9, 'dibuatkan', 2, NULL, 150000.00, 300000.00, '2025-07-01 22:59:32', NULL),
(223, 10, 5, 5, 1, 2, 'sendiri', 1, 'keranjang-designs/10_test93.jpeg', 120000.00, 120000.00, '2025-06-25 22:59:32', NULL),
(224, 15, 5, 1, 3, 9, 'sendiri', 2, 'keranjang-designs/15_test94.jpeg', 150000.00, 300000.00, '2025-06-30 22:59:32', NULL),
(225, 13, 1, 1, 3, 1, 'sendiri', 3, 'keranjang-designs/13_test95.jpeg', 120000.00, 360000.00, '2025-07-02 22:59:32', NULL),
(226, 13, 2, 5, 3, 1, 'dibuatkan', 1, NULL, 80000.00, 80000.00, '2025-06-28 22:59:32', NULL),
(227, 18, 1, 1, 1, 9, 'sendiri', 3, 'keranjang-designs/18_test97.jpeg', 100000.00, 300000.00, '2025-07-03 22:59:32', NULL),
(228, 10, 2, 5, 1, 9, 'sendiri', 1, 'keranjang-designs/10_test98.jpeg', 80000.00, 80000.00, '2025-07-01 22:59:32', NULL),
(229, 2, 3, 2, 3, 9, 'sendiri', 3, 'keranjang-designs/2_test99.jpeg', 120000.00, 360000.00, '2025-06-27 22:59:32', NULL),
(230, 33, 3, 2, 3, 2, 'dibuatkan', 1, NULL, 60000.00, 60000.00, '2025-07-13 13:53:06', '2025-07-13 13:53:06');

-- --------------------------------------------------------

--
-- Table structure for table `mesins`
--

CREATE TABLE `mesins` (
  `id` bigint NOT NULL,
  `nama_mesin` varchar(255) DEFAULT NULL,
  `tipe_mesin` varchar(255) DEFAULT NULL,
  `status` enum('aktif','digunakan','maintenance') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mesins`
--

INSERT INTO `mesins` (`id`, `nama_mesin`, `tipe_mesin`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mesin Cetak A', 'Digital Printing', 'aktif', '2025-05-10 03:21:43', '2025-07-25 12:45:55'),
(2, 'Mesin Cetak B', 'Offset Printing', 'aktif', '2025-05-10 03:21:43', '2025-06-28 05:35:20'),
(3, 'Mesin Cutting', 'Cutting', 'aktif', '2025-05-10 03:21:43', '2025-05-17 23:00:22'),
(4, 'Mesin Press', 'Heat Press', 'aktif', '2025-05-10 03:21:43', NULL),
(5, 'Mesin Finishing', 'Finishing', 'aktif', '2025-05-10 03:21:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_04_29_145833_remove_nama_tingkat_from_biaya_desains_table', 1),
(2, '2025_04_29_151615_add_gambar_to_items_table', 2),
(3, '2025_04_29_152424_add_gambar_to_items_table', 3),
(4, '2025_05_03_123557_create_personal_access_tokens_table', 4),
(5, '0001_01_01_000001_create_cache_table', 5),
(6, '0001_01_01_000002_create_jobs_table', 5),
(7, '2025_05_10_131339_add_updated_at_to_pesanans_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `operators`
--

CREATE TABLE `operators` (
  `id` bigint NOT NULL,
  `nama` varchar(255) NOT NULL,
  `posisi` varchar(100) DEFAULT NULL,
  `kontak` varchar(50) DEFAULT NULL,
  `status` enum('aktif','tidak_aktif') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `operators`
--

INSERT INTO `operators` (`id`, `nama`, `posisi`, `kontak`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Ahmad Rizky', 'Operator Cetak', '081234567890', 'tidak_aktif', '2025-05-10 03:21:43', '2025-07-25 12:46:41'),
(2, 'Budi Santoso', 'Operator Desain', '081234567891', 'tidak_aktif', '2025-05-10 03:21:43', '2025-07-25 12:34:44'),
(3, 'Citra Dewi', 'Operator Finishing', '081234567892', 'tidak_aktif', '2025-05-10 03:21:43', '2025-07-25 12:34:44'),
(4, 'Dodi Prasetyo', 'Operator Cetak', '081234567893', 'tidak_aktif', '2025-05-10 03:21:43', '2025-05-11 10:10:39'),
(5, 'Eko Widodo', 'Operator Desain', '081234567894', 'tidak_aktif', '2025-05-10 03:21:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pembayarans`
--

CREATE TABLE `pembayarans` (
  `id` bigint NOT NULL,
  `pesanan_id` bigint DEFAULT NULL,
  `midtrans_order_id` varchar(255) DEFAULT NULL,
  `snap_token` varchar(255) DEFAULT NULL,
  `metode` enum('COD','QRIS') NOT NULL,
  `status` enum('Pending','Lunas','Dibatalkan') NOT NULL DEFAULT 'Pending',
  `midtrans_response` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pembayarans`
--

INSERT INTO `pembayarans` (`id`, `pesanan_id`, `midtrans_order_id`, `snap_token`, `metode`, `status`, `midtrans_response`, `created_at`, `updated_at`) VALUES
(65, 115, '115', '6ea3147b-f254-4b46-be08-0fb52ca47755', 'QRIS', 'Lunas', '{\"transaction_details\":{\"order_id\":115,\"gross_amount\":256000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-06-29 07:02:00', '2025-06-29 07:02:00'),
(66, 116, NULL, NULL, 'COD', 'Lunas', NULL, '2025-06-29 07:20:30', '2025-06-29 07:20:30'),
(67, 117, NULL, NULL, 'COD', 'Lunas', NULL, '2025-06-29 17:17:51', '2025-06-29 17:17:51'),
(68, 118, NULL, NULL, 'COD', 'Lunas', NULL, '2025-06-29 18:18:16', '2025-06-29 18:18:16'),
(69, 119, '119', '1289c260-ec24-48b0-85c6-10f9ca5f476c', 'QRIS', 'Pending', '{\"transaction_details\":{\"order_id\":119,\"gross_amount\":140000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-06-29 18:19:54', '2025-06-29 18:19:54'),
(70, 120, '120', '22632abb-65ec-4d40-af6e-cd4b0c988a67', 'QRIS', 'Pending', '{\"transaction_details\":{\"order_id\":120,\"gross_amount\":200000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-06-29 18:21:31', '2025-06-29 18:21:31'),
(71, 121, '121', '3db50c0e-0e9a-4a95-8424-1d9d3711c888', 'QRIS', 'Pending', '{\"transaction_details\":{\"order_id\":121,\"gross_amount\":200000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-06-29 18:30:30', '2025-06-29 18:30:30'),
(72, 122, '122', 'b661d68b-4146-41e0-9afd-ef841cb3f7ac', 'QRIS', 'Pending', '{\"transaction_details\":{\"order_id\":122,\"gross_amount\":216000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-06-29 18:37:04', '2025-06-29 18:37:04'),
(73, 123, '123', '29f4b5e1-6751-4e08-a04f-7c8a46c5a780', 'QRIS', 'Lunas', '{\"transaction_details\":{\"order_id\":123,\"gross_amount\":266000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-06-30 04:10:57', '2025-06-30 04:10:57'),
(74, 124, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(75, 125, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(76, 126, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(77, 127, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(78, 128, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(79, 129, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(80, 130, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(81, 131, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(82, 132, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(83, 133, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(84, 134, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(85, 135, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(86, 136, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(87, 137, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(88, 138, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(89, 139, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(90, 140, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(91, 141, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(92, 142, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(93, 143, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(94, 144, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(95, 145, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(96, 146, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(97, 147, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(98, 148, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(99, 149, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(100, 150, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(101, 151, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(102, 152, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(103, 153, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(104, 154, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(105, 155, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(106, 156, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(107, 157, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(108, 158, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(109, 159, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(110, 160, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(111, 161, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(112, 162, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(113, 163, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(114, 164, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(115, 165, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(116, 166, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(117, 167, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(118, 168, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(119, 169, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(120, 170, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(121, 171, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(122, 172, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(123, 173, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(124, 174, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(125, 175, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(126, 176, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(127, 177, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(128, 178, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(129, 179, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(130, 180, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(131, 181, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(132, 182, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(133, 183, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(134, 184, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(135, 185, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(136, 186, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(137, 187, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(138, 188, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(139, 189, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(140, 190, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(141, 191, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(142, 192, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(143, 193, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(144, 194, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(145, 195, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(146, 196, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(147, 197, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(148, 198, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(149, 199, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(150, 200, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(151, 201, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(152, 202, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(153, 203, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(154, 204, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(155, 205, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(156, 206, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(157, 207, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(158, 208, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(159, 209, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(160, 210, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(161, 211, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(162, 212, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(163, 213, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(164, 214, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(165, 215, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(166, 216, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(167, 217, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(168, 218, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(169, 219, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(170, 220, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(171, 221, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(172, 222, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(173, 223, NULL, NULL, 'COD', 'Lunas', NULL, '2025-07-04 06:02:56', '2025-07-04 06:02:56'),
(174, 229, '229', '0cdba408-6c55-4dd1-b402-931c879f44bc', 'QRIS', 'Lunas', '{\"transaction_details\":{\"order_id\":229,\"gross_amount\":268000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-07-04 09:21:42', '2025-07-04 09:21:42'),
(175, 230, '230', '69d53fe8-2572-4dd9-9e4f-14ff4f8ed442', 'QRIS', 'Pending', '{\"transaction_details\":{\"order_id\":230,\"gross_amount\":318000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-07-17 08:52:24', '2025-07-17 08:52:24'),
(176, 231, '231', '70b4d906-ef80-47f1-9591-bf1f73259c23', 'QRIS', 'Pending', '{\"transaction_details\":{\"order_id\":231,\"gross_amount\":438000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-07-25 12:13:18', '2025-07-25 12:13:18'),
(177, 232, '232', '424e77e5-4596-4ead-8929-2f564929981f', 'QRIS', 'Pending', '{\"transaction_details\":{\"order_id\":232,\"gross_amount\":418000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-07-25 12:15:55', '2025-07-25 12:15:55'),
(178, 233, '233', '3e558995-392e-496e-af2b-c7b08a1d0f92', 'QRIS', 'Lunas', '{\"transaction_details\":{\"order_id\":233,\"gross_amount\":418000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-07-25 12:20:29', '2025-07-25 12:20:29'),
(179, 234, '234', 'ee713172-7d1d-41f2-8585-2bd84819d941', 'QRIS', 'Lunas', '{\"transaction_details\":{\"order_id\":234,\"gross_amount\":418000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-07-25 12:30:53', '2025-07-25 12:30:53'),
(180, 235, '235', '51e64fe3-56c4-4241-841b-d87d8b129ab6', 'QRIS', 'Lunas', '{\"transaction_details\":{\"order_id\":235,\"gross_amount\":418000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-07-25 12:38:25', '2025-07-25 12:38:25'),
(181, 236, '236', 'fcf2baa4-7cea-48e8-80d4-75a137b1b0dc', 'QRIS', 'Lunas', '{\"transaction_details\":{\"order_id\":236,\"gross_amount\":418000},\"customer_details\":{\"first_name\":\"shafa\",\"email\":\"azshafa95@gmail.com\",\"phone\":\"081234567890\"},\"payment_type\":\"qris\",\"qris\":{\"acquirer\":\"gopay\"}}', '2025-07-25 12:43:23', '2025-07-25 12:43:23');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(23, 'App\\Models\\User', 16, 'auth_token', 'd1509b3ef66f9ec628263232be747d86207f70361e482235a44e1cb7b68c347f', '[\"*\"]', NULL, NULL, '2025-05-09 11:16:37', '2025-05-09 11:16:37'),
(35, 'App\\Models\\User', 1, 'auth_token', '9c7402e3fd9f5881a92773dabb91bd4c2569563071e6bc283440a6ac425b1b51', '[\"*\"]', NULL, NULL, '2025-05-09 18:04:41', '2025-05-09 18:04:41');

-- --------------------------------------------------------

--
-- Table structure for table `pesanans`
--

CREATE TABLE `pesanans` (
  `id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `admin_id` bigint DEFAULT NULL,
  `ekspedisi_id` bigint DEFAULT NULL,
  `status` enum('Pemesanan','Dikonfirmasi','Sedang Diproses','Menunggu Pengambilan','Sedang Dikirim','Selesai','Dibatalkan') DEFAULT 'Pemesanan',
  `alamat_pengiriman` varchar(255) DEFAULT NULL,
  `metode_pengambilan` enum('antar','ambil') DEFAULT NULL,
  `waktu_pengambilan` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `estimasi_waktu` int DEFAULT NULL,
  `tanggal_dipesan` datetime NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `resi_pesanan` varchar(255) DEFAULT NULL,
  `bukti_pengiriman` varchar(255) DEFAULT NULL,
  `total` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanans`
--

INSERT INTO `pesanans` (`id`, `user_id`, `admin_id`, `ekspedisi_id`, `status`, `alamat_pengiriman`, `metode_pengambilan`, `waktu_pengambilan`, `created_at`, `estimasi_waktu`, `tanggal_dipesan`, `updated_at`, `resi_pesanan`, `bukti_pengiriman`, `total`) VALUES
(115, 1, NULL, NULL, 'Selesai', 'Jl. Soedarto, Asemrowo, Tembalang, Semarang, Jawa Tengah 43323223', 'antar', NULL, '2025-06-29 07:02:00', 24, '2025-06-29 14:02:00', '2025-06-29 17:39:57', 'jhjkyt76', 'bukti_pengiriman/xVSeLnoMhneoqhsbcD7AUujUxrAcPn14SVcK9mkk.jpg', 256000),
(116, 1, NULL, NULL, 'Menunggu Pengambilan', NULL, 'ambil', NULL, '2025-06-29 07:20:30', 24, '2025-06-29 14:20:30', '2025-06-29 17:16:36', NULL, NULL, 220000),
(117, 1, NULL, NULL, 'Menunggu Pengambilan', NULL, 'ambil', NULL, '2025-06-29 17:17:51', 24, '2025-06-30 00:17:51', '2025-06-29 17:38:57', NULL, NULL, 150000),
(118, 1, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-06-29 18:18:16', 24, '2025-06-30 01:18:16', '2025-06-29 18:34:00', NULL, NULL, 210000),
(119, 1, NULL, NULL, 'Pemesanan', NULL, 'ambil', NULL, '2025-06-29 18:19:53', 24, '2025-06-30 01:19:53', '2025-06-29 18:19:53', NULL, NULL, 140000),
(120, 1, NULL, NULL, 'Pemesanan', NULL, 'ambil', NULL, '2025-06-29 18:21:30', 24, '2025-06-30 01:21:30', '2025-06-29 18:21:30', NULL, NULL, 200000),
(121, 1, NULL, NULL, 'Pemesanan', NULL, 'ambil', NULL, '2025-06-29 18:30:29', 24, '2025-06-30 01:30:29', '2025-06-29 18:30:29', NULL, NULL, 200000),
(122, 1, NULL, NULL, 'Pemesanan', 'Jl. Soedarto, Asemrowo, Tembalang, Semarang, Jawa Tengah 43323223', 'antar', NULL, '2025-06-29 18:37:03', 24, '2025-06-30 01:37:03', '2025-06-29 18:37:03', NULL, NULL, 216000),
(123, 1, NULL, NULL, 'Selesai', 'Jl. Soedarto, Asemrowo, Tembalang, Semarang, Jawa Tengah 43323223', 'antar', NULL, '2025-06-30 04:10:56', 24, '2025-06-30 11:10:56', '2025-06-30 04:23:39', '123456', 'bukti_pengiriman/OWSmcyHgwZY6OzHXtVWdQaB5hEfwlVtY6pfXtL7Q.jpg', 266000),
(124, 2, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 150000),
(125, 15, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 216000),
(126, 13, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 136000),
(127, 2, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 200000),
(128, 22, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 136000),
(129, 13, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 100000),
(130, 25, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 150000),
(131, 24, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 140000),
(132, 5, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 200000),
(133, 10, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 120000),
(134, 18, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 220000),
(135, 10, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 150000),
(136, 15, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 140000),
(137, 18, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 116000),
(138, 24, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 216000),
(139, 25, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 100000),
(140, 5, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 140000),
(141, 5, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 236000),
(142, 2, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 266000),
(143, 10, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 166000),
(144, 25, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 240000),
(145, 10, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 120000),
(146, 5, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 220000),
(147, 24, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 136000),
(148, 23, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 140000),
(149, 10, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 216000),
(150, 24, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 240000),
(151, 22, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 120000),
(152, 24, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 166000),
(153, 18, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 116000),
(154, 15, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 136000),
(155, 24, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 236000),
(156, 22, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 256000),
(157, 13, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 166000),
(158, 10, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 220000),
(159, 13, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 136000),
(160, 15, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 100000),
(161, 15, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 236000),
(162, 15, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 166000),
(163, 1, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 266000),
(164, 2, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 156000),
(165, 24, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 136000),
(166, 23, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 240000),
(167, 2, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 250000),
(168, 22, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 256000),
(169, 25, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 100000),
(170, 15, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 150000),
(171, 13, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 140000),
(172, 1, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 266000),
(173, 23, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 220000),
(174, 23, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 166000),
(175, 18, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 140000),
(176, 13, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 156000),
(177, 15, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 216000),
(178, 22, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 200000),
(179, 24, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 136000),
(180, 1, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 100000),
(181, 2, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 156000),
(182, 1, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 200000),
(183, 10, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 256000),
(184, 25, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 166000),
(185, 25, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 266000),
(186, 18, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 250000),
(187, 15, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 236000),
(188, 10, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 100000),
(189, 13, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 216000),
(190, 10, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 216000),
(191, 25, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 166000),
(192, 18, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 250000),
(193, 2, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 120000),
(194, 23, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 240000),
(195, 5, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 250000),
(196, 1, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 200000),
(197, 25, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 200000),
(198, 5, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 120000),
(199, 10, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 266000),
(200, 1, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 136000),
(201, 1, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 240000),
(202, 2, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 156000),
(203, 1, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 250000),
(204, 13, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 150000),
(205, 10, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 116000),
(206, 15, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 256000),
(207, 5, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 100000),
(208, 5, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 240000),
(209, 24, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 120000),
(210, 10, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 116000),
(211, 2, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 266000),
(212, 22, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 150000),
(213, 2, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 240000),
(214, 2, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 150000),
(215, 18, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 266000),
(216, 13, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 100000),
(217, 24, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 236000),
(218, 18, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 156000),
(219, 22, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 100000),
(220, 25, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 116000),
(221, 22, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 100000),
(222, 23, NULL, NULL, 'Selesai', NULL, 'ambil', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 240000),
(223, 24, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 236000),
(227, 24, NULL, NULL, 'Selesai', 'Jl. Simulasi No.123, Semarang', 'antar', NULL, '2025-07-04 06:02:56', 24, '2025-07-04 13:02:56', '2025-07-04 06:02:56', NULL, NULL, 236000),
(229, 1, NULL, NULL, 'Sedang Dikirim', 'Jl. Soedarto, Asemrowo, Tembalang, Semarang, Jawa Tengah 43323223', 'antar', NULL, '2025-07-04 09:21:41', 24, '2025-07-04 16:21:41', '2025-07-04 09:56:21', '127236', 'bukti_pengiriman/viok3OWiPGLuwGghK0hltwZV0Dx8kn9uWgOcsRb0.jpg', 268000),
(230, 1, NULL, NULL, 'Pemesanan', 'Jl. Soedarto, Asemrowo, Tembalang, Semarang, Jawa Tengah 43323223', 'antar', NULL, '2025-07-17 08:52:24', 24, '2025-07-17 15:52:24', '2025-07-17 08:52:24', NULL, NULL, 318000),
(231, 1, NULL, NULL, 'Pemesanan', 'Jl. Soedarto, Asemrowo, Tembalang, Semarang, Jawa Tengah 43323223', 'antar', NULL, '2025-07-25 12:13:17', 24, '2025-07-25 19:13:17', '2025-07-25 12:13:17', NULL, NULL, 438000),
(232, 1, NULL, NULL, 'Pemesanan', 'Jl. Soedarto, Asemrowo, Tembalang, Semarang, Jawa Tengah 43323223', 'antar', NULL, '2025-07-25 12:15:54', 24, '2025-07-25 19:15:54', '2025-07-25 12:15:54', NULL, NULL, 418000),
(233, 1, NULL, NULL, 'Sedang Dikirim', 'Jl. Soedarto, Asemrowo, Tembalang, Semarang, Jawa Tengah 43323223', 'antar', NULL, '2025-07-25 12:20:28', 24, '2025-07-25 19:20:28', '2025-07-25 12:27:28', NULL, NULL, 418000),
(234, 1, NULL, NULL, 'Sedang Dikirim', 'Jl. Soedarto, Asemrowo, Tembalang, Semarang, Jawa Tengah 43323223', 'antar', NULL, '2025-07-25 12:30:52', 24, '2025-07-25 19:30:52', '2025-07-25 12:34:13', 'JKY12980232', NULL, 418000),
(235, 1, NULL, NULL, 'Sedang Dikirim', 'Jl. Soedarto, Asemrowo, Tembalang, Semarang, Jawa Tengah 43323223', 'antar', NULL, '2025-07-25 12:38:24', 24, '2025-07-25 19:38:24', '2025-07-25 12:41:35', 'JKYH8981231', NULL, 418000),
(236, 1, NULL, NULL, 'Sedang Dikirim', 'Jl. Soedarto, Asemrowo, Tembalang, Semarang, Jawa Tengah 43323223', 'antar', NULL, '2025-07-25 12:43:22', 24, '2025-07-25 19:43:22', '2025-07-25 12:46:16', 'POLINES1828', NULL, 418000);

-- --------------------------------------------------------

--
-- Table structure for table `proses_pesanans`
--

CREATE TABLE `proses_pesanans` (
  `id` bigint NOT NULL,
  `detail_pesanan_id` bigint DEFAULT NULL,
  `mesin_id` bigint DEFAULT NULL,
  `operator_id` bigint DEFAULT NULL,
  `waktu_mulai` timestamp NULL DEFAULT NULL,
  `waktu_selesai` timestamp NULL DEFAULT NULL,
  `status_proses` varchar(100) DEFAULT NULL,
  `catatan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `proses_pesanans`
--

INSERT INTO `proses_pesanans` (`id`, `detail_pesanan_id`, `mesin_id`, `operator_id`, `waktu_mulai`, `waktu_selesai`, `status_proses`, `catatan`, `created_at`, `updated_at`) VALUES
(35, 125, 1, 1, '2025-06-29 17:15:10', '2025-06-29 17:15:34', 'Selesai', NULL, '2025-06-29 17:15:10', '2025-06-29 17:15:34'),
(36, 126, 1, 2, '2025-06-29 17:16:24', '2025-06-29 17:16:36', 'Selesai', NULL, '2025-06-29 17:16:24', '2025-06-29 17:16:36'),
(37, 127, 1, 3, '2025-06-29 17:38:48', '2025-06-29 17:38:57', 'Selesai', NULL, '2025-06-29 17:38:48', '2025-06-29 17:38:57'),
(38, 128, 1, 2, '2025-06-29 18:32:56', '2025-06-29 18:33:28', 'Selesai', NULL, '2025-06-29 18:32:56', '2025-06-29 18:33:28'),
(39, 133, 1, 1, '2025-06-30 04:17:53', '2025-06-30 04:18:21', 'Selesai', NULL, '2025-06-30 04:17:53', '2025-06-30 04:18:21'),
(40, 238, 1, 1, '2025-07-04 09:23:22', '2025-07-04 09:24:02', 'Selesai', NULL, '2025-07-04 09:23:22', '2025-07-04 09:24:02'),
(41, 242, 1, 2, '2025-07-25 12:27:02', '2025-07-25 12:27:28', 'Selesai', NULL, '2025-07-25 12:27:02', '2025-07-25 12:27:28'),
(42, 243, 1, 3, '2025-07-25 12:33:42', '2025-07-25 12:33:56', 'Selesai', NULL, '2025-07-25 12:33:42', '2025-07-25 12:33:56'),
(43, 244, 1, 1, '2025-07-25 12:40:47', '2025-07-25 12:41:01', 'Selesai', NULL, '2025-07-25 12:40:47', '2025-07-25 12:41:01'),
(44, 245, 1, 1, '2025-07-25 12:45:44', '2025-07-25 12:45:55', 'Selesai', NULL, '2025-07-25 12:45:44', '2025-07-25 12:45:55');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint NOT NULL,
  `nama_role` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nama_role`) VALUES
(1, 'user'),
(2, 'admin'),
(3, 'super_admin');

-- --------------------------------------------------------

--
-- Table structure for table `toko_info`
--

CREATE TABLE `toko_info` (
  `id` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `alamat_lengkap` text NOT NULL,
  `kelurahan` varchar(255) DEFAULT NULL,
  `kecamatan` varchar(255) NOT NULL,
  `kota` varchar(255) NOT NULL,
  `provinsi` varchar(255) NOT NULL,
  `kode_pos` varchar(10) NOT NULL,
  `nomor_telepon` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `toko_info`
--

INSERT INTO `toko_info` (`id`, `nama`, `alamat_lengkap`, `kelurahan`, `kecamatan`, `kota`, `provinsi`, `kode_pos`, `nomor_telepon`, `email`, `logo`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'CETAKU', 'Jl. Polines Jaya', 'Gambir', 'Margadana', 'Tegal', 'Jawa Tengah', '52143', '08952675671', 'cetaku@gmail.com', 'logos/bvwALBMnIs7IDrLbBIV1U4ctVQVdBosmOelVzobk.png', 1, '2025-06-27 06:46:53', '2025-07-25 13:31:01');

-- --------------------------------------------------------

--
-- Table structure for table `ukurans`
--

CREATE TABLE `ukurans` (
  `id` bigint NOT NULL,
  `size` varchar(100) DEFAULT NULL,
  `biaya_tambahan` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ukurans`
--

INSERT INTO `ukurans` (`id`, `size`, `biaya_tambahan`) VALUES
(1, 'S', 20000.00),
(2, 'M', 0.00),
(3, 'S', 0.00),
(4, 'XL', 0.00),
(5, 'XXL', 0.00),
(9, 'Xl', 0.00),
(10, 'S', 0.00),
(11, 'M', 0.00),
(19, 's', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `api_token` varchar(80) DEFAULT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `avatar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `reset_code` varchar(6) DEFAULT NULL,
  `reset_code_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `password`, `email`, `api_token`, `token_expires_at`, `role_id`, `created_at`, `updated_at`, `last_login_at`, `last_login_ip`, `google_id`, `avatar`, `reset_code`, `reset_code_expires_at`) VALUES
(1, 'shafa', '$2y$12$Wt1tDizpOtavMqgp2Ti8.OlhWeoIJRBO5UnQ/GTrMvPWeNEZZvd.O', 'azshafa95@gmail.com', NULL, NULL, 1, '2025-04-26 08:47:33', '2025-09-04 01:12:16', '2025-11-25 11:57:52', '127.0.0.1', '102858595328853359577', 'https://lh3.googleusercontent.com/a/ACg8ocIdOActcRB_Kn6tJ6wU6Oh8Pa3WYIX9EeAyTzEnHY6WmPeW5A=s96-c', NULL, NULL),
(2, 'Wanto', '$2y$12$vIlOBTow2ShNW0G9k0Bbr.0ESN82h88jcKX1VMnqlRDy7KoKRNzlG', 'wanto@gmail.com', 'JhSvNquVspZSTU7JyTnbagXIQcQwvMCx2kdn5ifhjOr1WyISpEpg2cYoPWGL', '2025-05-26 11:17:26', 1, '2025-04-26 11:17:26', '2025-04-26 11:17:26', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Dewa', '$2y$12$SYjNkK.fyu/mt6MgiH3POOHhvlyC7wbmHaW/.iLzjJ0G7.6fWKuOm', 'Dewa@example.com', 'hisjsbTkGvkiiEK1HlfBUUaj5UbcSt8k9uVXkQY8z5pbGvXOF081Vq8oifyT', '2025-05-31 04:28:37', 1, '2025-05-01 04:28:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'Dewa', '$2y$12$bIACECXbjXyIwsybHoL1qOcCeqU6uQOlij2usAxQkBjnz2o3ZOKzS', 'shafa@example.com', NULL, '2025-05-31 07:23:35', 1, '2025-05-01 07:23:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Administrator Jawa', '$2y$12$6ADS.q9uvtYNOwrhoEF.u.MxS16ekZ7EmMEBtwL.qhdVwZL9fVT9q', 'admin@example.com', NULL, NULL, 2, '2025-05-01 07:27:16', '2025-05-14 06:18:31', '2026-06-04 01:27:34', '127.0.0.1', NULL, NULL, NULL, NULL),
(9, 'Super Administrator', '$2y$12$btZpgfvkHjeVDRMAzfe7vusrA7asVHIcW63PUrtbc6pZ2UqNc6BUm', 'superadmin@example.com', 'ifdQnjmlVOdyranoRYYkkuk3E7SwNBIxPqB79KZLVe3ocBksrnYfTu48Zlvp', '2025-10-29 02:09:05', 3, '2025-05-01 07:27:17', '2025-05-01 07:27:17', '2025-09-29 02:09:05', '127.0.0.1', NULL, NULL, NULL, NULL),
(10, 'Test User', '$2y$12$75QTntFPed1qCV0EjpEuiuV5ZUtV0zVImqH4cBgyCdJO2pjjSY2eK', 'test@example.com', '3LznqMkXNkYnolPI7UZeMrtz52JGw7ycGWRu5zLsBAgNVKn47enc0i1WyHma', '2025-05-31 07:27:17', 1, '2025-05-01 07:27:17', '2025-05-01 07:27:17', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'shafaz', '$2y$12$jDu7soQo/cK6gLJKJOA2Gee5p9vBkxWgQj08v364cv5WIxhigHm8q', 'shafa@gmail.com', NULL, NULL, 1, '2025-05-02 15:31:34', '2025-05-02 15:31:34', '2026-06-04 14:20:11', '127.0.0.1', NULL, NULL, NULL, NULL),
(12, 'Lamine Yamal', '$2y$12$8MTJDtsn1VvcQ3DpCsH.r.3i8OkjoB/eHrV7lok9XaA3wAbZDvO3a', 'yamal@gmail.com', 'rmVwS6LwECDVpyEo5y1QV6OUoo3bGW2EcX3hGdJyJ9Do8rPEBgSNfHejm3xQ', '2025-06-02 21:07:34', 1, '2025-05-03 21:07:15', NULL, '2025-05-03 21:07:34', '127.0.0.1', NULL, NULL, NULL, NULL),
(13, 'John Doe', '$2y$12$C4L4weG3zRyu5AK0JF8aBOgBBZ3h//qbw.zcWPxG1AaDtYe0OcHVm', 'john@example.com', 'H1S8Clm0X6QSzNGny1oQxPbKJjrxkhXUsUzwqNFv3ZZ42GKlFuJrBumDXBmc', '2025-06-07 23:15:35', 1, '2025-05-08 23:15:35', '2025-05-08 23:15:35', NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'asasas', '$2y$12$VYWP9a21BxmdOaqorGlJwuh8JacrlX.hQWwikZy8uwrmg9RWVyg7u', 'ca@example.com', 'ADrewjRtpkoeHkMS6SULGT38cW0zBPOxkLF5pZKh4Mtw7L65Jc5w9hJ28keN', '2025-06-07 23:56:25', 1, '2025-05-08 23:56:25', '2025-05-08 23:56:25', NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Nama Pengguna', '$2y$12$4xr4HvftdAslE9FF795XFOjKRcpx9qKRFh6aZzq6RuI7u1l51iq9q', 'user12@example.com', '5hNGkMzmhRa4khN9LyvCg8IoKutOYepkdHvep0Xypp62qHT36eZtPoZKXQug', '2025-06-08 05:05:01', 1, '2025-05-09 05:05:01', '2025-05-09 05:05:01', NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'shafa', '$2y$12$4x7xV2NNPpS.p/YFburswODquOvKDZzusBFsXzGj/OVDXeqPK5pE2', 'hutaopapaj@gmail.com', 'eo7vaVTiqYcDhcVHfaaDT5gUzUpY7nNhX9Age9o9GdYRPJFeF8GAktwBtvPk', '2025-08-11 13:58:12', 1, '2025-05-09 11:16:37', '2025-07-12 13:58:12', '2025-07-12 13:58:12', '127.0.0.1', '102549939011122887803', 'https://lh3.googleusercontent.com/a/ACg8ocKbb_ldFb6VUWnPiEJEWUebOIFEKGY61bxtgj6a6grStg7fUw=s96-c', NULL, NULL),
(17, 'shafaaz', '$2y$12$qMhS79qF5ieXix51SMjto.BbyhH.OrL8BR8Xqpq6tDTAfSLs4Gn/2', 'shafaz@gmail.com', NULL, NULL, 1, '2025-05-09 11:30:35', '2025-05-09 11:30:35', NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'yusnan', '$2y$12$5Rg3Jyf.X0NKv5qPdVQ3luED0PMzx61hN8NvLOdNDj5tZt.qCnXA.', 'yusnan2@gmai.com', 'qox62UezRWC7gQQFJlDfxqLqxq6eePCS11D3sc2tdO5MTM2XoW5C2oybF2sW', '2025-06-08 18:34:21', 1, '2025-05-09 18:34:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'FERRAN TORRES', '$2y$12$PDQBd8.uApNqbaTNCTZ2pugcacnxWN.dCo1TLSXC74YYQ20CFV4fm', 'Ferran@gmail.com', NULL, NULL, 1, '2025-05-11 19:35:20', '2025-05-14 06:35:07', NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'ammar', '$2y$12$/GZ.3nmltVOwwjd3EzMXjexa9VbaL1l503cG2FuF8rZN5gnCJm09S', 'ammar@gmail.com', NULL, NULL, 1, '2025-05-16 19:24:45', NULL, '2025-06-12 23:19:46', '127.0.0.1', NULL, NULL, NULL, NULL),
(22, 'User Baru', '$2y$12$cNyXXGjogbIAg58yxfYo/O5p6X1nE6eEG8yF6wvWC79e2wyLDNOte', 'userbaru@example.com', 'xy4PEBWv0H8jTDsDYM0PjJlYnVZJpzOknglQm19xgKeyVt9aVy35j90XrH4w', '2025-07-08 02:34:31', 1, '2025-06-08 02:34:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'User Baru', '$2y$12$9SavueDQiHHWbDGpQQNAwudMQLeS7fj1QeNILqmSYI0NgeySyiHlC', 'userbaru1@example.com', 'awxCeR6OovpWpwdnpZgfwIy72Y9EdhPKUROJ5PhsTe5PDo2IIhNzZnxGLnxM', '2025-07-12 23:18:16', 1, '2025-06-12 22:58:42', NULL, '2025-06-12 23:18:16', '127.0.0.1', NULL, NULL, NULL, NULL),
(24, 'User Baru', '$2y$12$eq5TknIe4mHZIp8gGWh64.pZtUdy/YOxdypU8G120QCgm8/q9MiR2', 'userbaru2@example.com', 'QRz5tdzR9LujVPBf0tjKq3uICvDNBOupFq1hYlIW5xM7hMCAFaFYCbE2cL8h', '2025-07-13 00:07:31', 1, '2025-06-12 23:38:33', NULL, '2025-06-13 00:07:31', '127.0.0.1', NULL, NULL, NULL, NULL),
(25, 'User Baru', '$2y$12$R1PkXB97D/LS96XlA.tol.U8mlrVaw72GSMV56w/mRNTdX8FiMPRO', 'userbaru3@example.com', 'DFzppHEvDymS0x5wkAgxEGYlslSyLAHf6rdxG1cSO4czaKLLNwda6PNku8VD', '2025-07-13 00:07:14', 1, '2025-06-13 00:07:14', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'nico', '$2y$12$IkvrIaPw.WzoeyGMsRcAY.qjGrsgGklyEG/Y2yk8Zs2FC5RU9QR4O', 'nico@gmail.com', 'Aqxej4PuJtGn8rofLo8D6l55FU59QWJPlghjxJgynBQEky0r6RCVf1jegcGO', '2025-07-15 20:39:07', 1, '2025-06-15 20:39:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'dirga', '$2y$12$H.QlPCB9/vbEifzKSayXaerPK8Xg2X32j2UuLgkzS7aEcZdJqbiHi', 'dirga@gmail.com', NULL, NULL, 1, '2025-06-30 03:32:46', '2025-06-30 03:41:43', NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'Azka Budianto', '$2y$12$/Kt/eEN2yRneIM6EVJtsE.jmWMAf3iQ.mO4e0JzZY9wtRlPVlkefa', 'azkabudianto@gmail.com', '6WJqzWN1URI5BWjTx7LrARrV0XVB0h9gAIuOwzMMAbGdLN73J0bXuIEDLtJ9', '2025-08-03 10:04:04', 3, '2025-07-04 09:59:58', '2025-07-04 09:59:58', '2025-07-04 10:04:04', '127.0.0.1', NULL, NULL, NULL, NULL),
(29, 'Shafa Azz', NULL, 'azzshafa11@gmail.com', NULL, NULL, 1, '2025-07-12 13:03:42', '2025-07-13 08:06:24', '2025-07-13 08:06:24', '127.0.0.1', '111899989374897167676', 'https://lh3.googleusercontent.com/a/ACg8ocKQ4pcGkG57aYKmm_wDFMALDbKE0V_Vnu-MZxpgAkvH2I8sakY=s96-c', NULL, NULL),
(30, 'sabil aksana saki', NULL, 'mbulgembul225@gmail.com', 'VC8RxR48NGHRb6SrI0HflIQ17nZ6ieN2I1O1nnnj40I8b9VZHi0PsMWkwvaE', '2025-08-11 13:08:46', 1, '2025-07-12 13:08:46', '2025-07-12 13:08:46', '2025-07-12 13:08:46', '127.0.0.1', '100062623910237193031', 'https://lh3.googleusercontent.com/a/ACg8ocJ-gVmupLNHjrub4ydXoOAHVgnC8EKTbSDEOi8sO7MDaJVEoA=s96-c', NULL, NULL),
(31, 'azari', NULL, 'shafasmkn3tgl@gmail.com', NULL, NULL, 1, '2025-07-12 14:01:23', '2025-07-13 08:30:20', '2025-07-13 08:30:20', '127.0.0.1', '108321555522255524861', 'https://lh3.googleusercontent.com/a/ACg8ocLxSn7CN5XB3tmzsiy_VHCjNrZ9rzDs7BrMDgWegAFx-IazO90=s96-c', NULL, NULL),
(32, 'azka', '$2y$12$XY5ORv4wr/fmXVTkjOeHMepGOpMq.qfKhAFA7qUg8.TjXtv5gnLVm', 'azkanurfadel@gmail.com', NULL, NULL, 1, '2025-07-13 13:35:39', '2025-07-13 13:53:37', '2025-07-13 13:53:37', '127.0.0.1', '110139859279052356698', 'https://lh3.googleusercontent.com/a/ACg8ocKFvOIcN5EWVvuxTi4AV4PxlgK9-FIZQH9CVf79WhkKFyk2R3PT0w=s96-c', NULL, NULL),
(33, 'Yuhu Zari', NULL, 'yuhuzari@gmail.com', 'ZUlKZmzJiDcUMcCgV2iONB6HEU6DGxFxMhFbqG7ppf13o2sUG3fKMzp66f7p', '2025-08-12 13:51:33', 1, '2025-07-13 13:51:33', '2025-07-13 13:51:33', '2025-07-13 13:51:33', '127.0.0.1', '116723394686673205270', 'https://lh3.googleusercontent.com/a/ACg8ocJce8ySmu1EkHO4HKwfEpYiffnEdtEYEkVFNgCp3f5RjB1_qA=s96-c', NULL, NULL),
(34, 'shafa', '$2y$12$NwKqQrdijax1hKGjy0Ds2u4fzSsYZ0KvwQP8vS/k0BhFpF3qhPG3G', 'shafa2@gmail.com', 'ohFQRBlslpWBlbmNDBIbAf3Eojiew9aYTTkfRP2xs95mGcnDbupz4tk9nI5Z', '2026-07-04 01:54:43', 1, '2026-06-04 01:19:29', NULL, '2026-06-04 01:54:43', '127.0.0.1', NULL, NULL, NULL, NULL),
(35, 'Test User TC004 2026-06-04', '$2y$12$lF0vqvu5wHfb.ofdM8hn6Ox8jSFWC1KTdZ97ammWmbyBpFtjNcJrq', 'tc004_20260604_000001@example.com', 'dXTMT6gPYeWUOvMeG7sF6lJBsrEwPdvQyyv5sunD0TR3oVPRHy8MgKP4VxzK', '2026-07-04 01:41:25', 1, '2026-06-04 01:41:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'User Test Playwright', '$2y$12$ffYwWUCjr3yPuc1qGJgnAOqve64NFsZhcUudNxlVnKpB3ndYmlzB.', 'testuser_1780537880295@mailtest.com', 'TGHDSUQ4biDRnI07D9dgs6qOp3cK1oubo6kKbNOnD4uwqiTcnX3ohdPJBl6E', '2026-07-04 01:51:21', 1, '2026-06-04 01:51:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'User Test Playwright', '$2y$12$mLjbz641/J7XJeMb7El.genBbKCS2XL3th5ro2yryR7HFOxhs/BRC', 'testuser_1780538003788@mailtest.com', 'tsvbWt21zyg7BGGV7wkesm7EVGq3LJHKJz91IClF3xQcKiGhdpOxs5G7LCPY', '2026-07-04 01:53:24', 1, '2026-06-04 01:53:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alamats`
--
ALTER TABLE `alamats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bahans`
--
ALTER TABLE `bahans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `biaya_desains`
--
ALTER TABLE `biaya_desains`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `customs`
--
ALTER TABLE `customs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `ukuran_id` (`ukuran_id`),
  ADD KEY `bahan_id` (`bahan_id`),
  ADD KEY `jenis_id` (`jenis_id`);

--
-- Indexes for table `detail_pesanans`
--
ALTER TABLE `detail_pesanans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_id` (`pesanan_id`),
  ADD KEY `custom_id` (`custom_id`);

--
-- Indexes for table `ekspedisis`
--
ALTER TABLE `ekspedisis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ekspedisis_pesanan_id` (`pesanan_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jenis_id` (`jenis_id`);

--
-- Indexes for table `item_bahans`
--
ALTER TABLE `item_bahans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `bahan_id` (`bahan_id`);

--
-- Indexes for table `item_jenis`
--
ALTER TABLE `item_jenis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `jenis_id` (`jenis_id`);

--
-- Indexes for table `item_ukurans`
--
ALTER TABLE `item_ukurans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `ukuran_id` (`ukuran_id`);

--
-- Indexes for table `jenis`
--
ALTER TABLE `jenis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategoris`
--
ALTER TABLE `kategoris`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_items`
--
ALTER TABLE `kategori_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `ukuran_id` (`ukuran_id`),
  ADD KEY `bahan_id` (`bahan_id`),
  ADD KEY `jenis_id` (`jenis_id`);

--
-- Indexes for table `mesins`
--
ALTER TABLE `mesins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `operators`
--
ALTER TABLE `operators`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayarans`
--
ALTER TABLE `pembayarans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_id` (`pesanan_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pesanans`
--
ALTER TABLE `pesanans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `ekspedisi_id` (`ekspedisi_id`);

--
-- Indexes for table `proses_pesanans`
--
ALTER TABLE `proses_pesanans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_pesanan_id` (`detail_pesanan_id`),
  ADD KEY `mesin_id` (`mesin_id`),
  ADD KEY `operator_id` (`operator_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `toko_info`
--
ALTER TABLE `toko_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ukurans`
--
ALTER TABLE `ukurans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_api_token_unique` (`api_token`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alamats`
--
ALTER TABLE `alamats`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bahans`
--
ALTER TABLE `bahans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `biaya_desains`
--
ALTER TABLE `biaya_desains`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customs`
--
ALTER TABLE `customs`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT for table `detail_pesanans`
--
ALTER TABLE `detail_pesanans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT for table `ekspedisis`
--
ALTER TABLE `ekspedisis`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `item_bahans`
--
ALTER TABLE `item_bahans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `item_jenis`
--
ALTER TABLE `item_jenis`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `item_ukurans`
--
ALTER TABLE `item_ukurans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `jenis`
--
ALTER TABLE `jenis`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategoris`
--
ALTER TABLE `kategoris`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kategori_items`
--
ALTER TABLE `kategori_items`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT for table `mesins`
--
ALTER TABLE `mesins`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `operators`
--
ALTER TABLE `operators`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pembayarans`
--
ALTER TABLE `pembayarans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `pesanans`
--
ALTER TABLE `pesanans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT for table `proses_pesanans`
--
ALTER TABLE `proses_pesanans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `toko_info`
--
ALTER TABLE `toko_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ukurans`
--
ALTER TABLE `ukurans`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alamats`
--
ALTER TABLE `alamats`
  ADD CONSTRAINT `alamats_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `customs`
--
ALTER TABLE `customs`
  ADD CONSTRAINT `customs_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `customs_ibfk_2` FOREIGN KEY (`ukuran_id`) REFERENCES `ukurans` (`id`),
  ADD CONSTRAINT `customs_ibfk_3` FOREIGN KEY (`bahan_id`) REFERENCES `bahans` (`id`),
  ADD CONSTRAINT `customs_ibfk_4` FOREIGN KEY (`jenis_id`) REFERENCES `jenis` (`id`);

--
-- Constraints for table `detail_pesanans`
--
ALTER TABLE `detail_pesanans`
  ADD CONSTRAINT `detail_pesanans_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanans` (`id`),
  ADD CONSTRAINT `detail_pesanans_ibfk_2` FOREIGN KEY (`custom_id`) REFERENCES `customs` (`id`);

--
-- Constraints for table `ekspedisis`
--
ALTER TABLE `ekspedisis`
  ADD CONSTRAINT `fk_ekspedisis_pesanan_id` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanans` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`jenis_id`) REFERENCES `jenis` (`id`);

--
-- Constraints for table `item_bahans`
--
ALTER TABLE `item_bahans`
  ADD CONSTRAINT `item_bahans_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `item_bahans_ibfk_2` FOREIGN KEY (`bahan_id`) REFERENCES `bahans` (`id`);

--
-- Constraints for table `item_jenis`
--
ALTER TABLE `item_jenis`
  ADD CONSTRAINT `item_jenis_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `item_jenis_ibfk_2` FOREIGN KEY (`jenis_id`) REFERENCES `jenis` (`id`);

--
-- Constraints for table `item_ukurans`
--
ALTER TABLE `item_ukurans`
  ADD CONSTRAINT `item_ukurans_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `item_ukurans_ibfk_2` FOREIGN KEY (`ukuran_id`) REFERENCES `ukurans` (`id`);

--
-- Constraints for table `kategori_items`
--
ALTER TABLE `kategori_items`
  ADD CONSTRAINT `kategori_items_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategoris` (`id`),
  ADD CONSTRAINT `kategori_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `keranjang_ibfk_3` FOREIGN KEY (`ukuran_id`) REFERENCES `ukurans` (`id`),
  ADD CONSTRAINT `keranjang_ibfk_4` FOREIGN KEY (`bahan_id`) REFERENCES `bahans` (`id`),
  ADD CONSTRAINT `keranjang_ibfk_5` FOREIGN KEY (`jenis_id`) REFERENCES `jenis` (`id`);

--
-- Constraints for table `pesanans`
--
ALTER TABLE `pesanans`
  ADD CONSTRAINT `pesanans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pesanans_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pesanans_ibfk_3` FOREIGN KEY (`ekspedisi_id`) REFERENCES `ekspedisis` (`id`);

--
-- Constraints for table `proses_pesanans`
--
ALTER TABLE `proses_pesanans`
  ADD CONSTRAINT `proses_pesanans_ibfk_1` FOREIGN KEY (`detail_pesanan_id`) REFERENCES `detail_pesanans` (`id`),
  ADD CONSTRAINT `proses_pesanans_ibfk_2` FOREIGN KEY (`mesin_id`) REFERENCES `mesins` (`id`),
  ADD CONSTRAINT `proses_pesanans_operator_fk` FOREIGN KEY (`operator_id`) REFERENCES `operators` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
