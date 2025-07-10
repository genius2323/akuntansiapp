-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2025 at 10:14 AM
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
-- Database: `db_akuntansi`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `kode_cat` varchar(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `kode_cat`, `name`, `description`, `otoritas`, `kode_ky`, `batas_tanggal_sistem`, `mode_batas_tanggal`, `created_at`, `updated_at`, `deleted_at`, `recovered_at`) VALUES
(2, '', 'Mouse', '', NULL, NULL, NULL, 'automatic', '2025-07-07 06:44:40', '2025-07-07 07:08:19', '2025-07-07 07:08:19', NULL),
(3, 'LAPT', 'LAPTOP', '', NULL, NULL, NULL, NULL, '2025-07-07 07:11:24', '2025-07-10 03:19:44', NULL, NULL),
(4, 'SPEA', 'Speaker', '', NULL, NULL, NULL, NULL, '2025-07-07 07:33:19', '2025-07-10 03:14:29', NULL, NULL),
(5, 'LACI', 'LACI', '', NULL, NULL, NULL, NULL, '2025-07-08 03:21:09', '2025-07-10 03:22:53', NULL, NULL),
(6, '', 'LEMARI', '', NULL, NULL, NULL, 'automatic', '2025-07-08 03:21:33', '2025-07-08 03:21:33', NULL, NULL),
(7, 'bat', 'baterai', '', NULL, NULL, NULL, 'automatic', '2025-07-09 07:24:10', '2025-07-09 07:24:10', NULL, NULL),
(8, 'MEJA', 'MEJA', '', NULL, NULL, NULL, 'automatic', '2025-07-10 03:22:39', '2025-07-10 03:22:39', NULL, NULL),
(9, 'GELA', 'GELAS', '', NULL, NULL, NULL, 'automatic', '2025-07-10 03:27:02', '2025-07-10 03:27:02', NULL, NULL),
(10, 'PRIN', 'PRINTER', '', NULL, NULL, NULL, 'automatic', '2025-07-10 03:31:30', '2025-07-10 03:31:30', NULL, NULL),
(11, 'PINT', 'PINTU', '', NULL, 'geni', NULL, 'automatic', '2025-07-10 03:36:22', '2025-07-10 03:36:22', NULL, NULL),
(12, 'LUKI', 'LUKISAN', '', NULL, 'geni', NULL, 'automatic', '2025-07-10 03:58:45', '2025-07-10 03:58:45', NULL, NULL),
(13, 'GALO', 'GALON', '', NULL, 'geni', NULL, 'automatic', '2025-07-10 03:59:38', '2025-07-10 03:59:38', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `telepon` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daya`
--

CREATE TABLE `daya` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daya`
--

INSERT INTO `daya` (`id`, `name`, `description`, `otoritas`, `kode_ky`, `created_at`, `updated_at`, `deleted_at`, `recovered_at`, `batas_tanggal_sistem`, `mode_batas_tanggal`) VALUES
(1, '100W', '', NULL, NULL, '2025-07-10 01:04:20', '2025-07-10 01:04:20', NULL, NULL, NULL, 'automatic');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `deleted_at`, `recovered_at`, `created_at`) VALUES
(1, 'POS', 'Point of Sales - Kasir', NULL, NULL, '2025-07-06'),
(2, 'Back Office', 'Akunting dan Administrasi', NULL, NULL, '2025-07-06'),
(3, 'General', 'Owner dan Management', NULL, NULL, '2025-07-06');

-- --------------------------------------------------------

--
-- Table structure for table `dimensi`
--

CREATE TABLE `dimensi` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fiting`
--

CREATE TABLE `fiting` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gondola`
--

CREATE TABLE `gondola` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jenis`
--

CREATE TABLE `jenis` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jumlah_mata`
--

CREATE TABLE `jumlah_mata` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jumlah_mata`
--

INSERT INTO `jumlah_mata` (`id`, `name`, `description`, `otoritas`, `kode_ky`, `created_at`, `updated_at`, `deleted_at`, `recovered_at`, `batas_tanggal_sistem`, `mode_batas_tanggal`) VALUES
(1, 'TIGA MATA', '', NULL, NULL, '2025-07-10 01:33:09', '2025-07-10 01:33:09', NULL, NULL, NULL, 'automatic');

-- --------------------------------------------------------

--
-- Table structure for table `kaki`
--

CREATE TABLE `kaki` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merk`
--

CREATE TABLE `merk` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model`
--

CREATE TABLE `model` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelengkap`
--

CREATE TABLE `pelengkap` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelengkap`
--

INSERT INTO `pelengkap` (`id`, `name`, `description`, `otoritas`, `kode_ky`, `created_at`, `updated_at`, `deleted_at`, `recovered_at`, `batas_tanggal_sistem`, `mode_batas_tanggal`) VALUES
(1, 'TUTUP', '', NULL, NULL, '2025-07-10 01:34:56', '2025-07-10 01:34:56', NULL, NULL, NULL, 'automatic');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `satuan_id` int(11) DEFAULT NULL,
  `jenis_id` int(11) DEFAULT NULL,
  `pelengkap_id` int(11) DEFAULT NULL,
  `gondola_id` int(11) DEFAULT NULL,
  `merk_id` int(11) DEFAULT NULL,
  `warna_sinar_id` int(11) DEFAULT NULL,
  `ukuran_barang_id` int(11) DEFAULT NULL,
  `voltase_id` int(11) DEFAULT NULL,
  `dimensi_id` int(11) DEFAULT NULL,
  `warna_body_id` int(11) DEFAULT NULL,
  `warna_bibir_id` int(11) DEFAULT NULL,
  `kaki_id` int(11) DEFAULT NULL,
  `model_id` int(11) DEFAULT NULL,
  `fiting_id` int(11) DEFAULT NULL,
  `daya_id` int(11) DEFAULT NULL,
  `jumlah_mata_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic',
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `satuan_id`, `jenis_id`, `pelengkap_id`, `gondola_id`, `merk_id`, `warna_sinar_id`, `ukuran_barang_id`, `voltase_id`, `dimensi_id`, `warna_body_id`, `warna_bibir_id`, `kaki_id`, `model_id`, `fiting_id`, `daya_id`, `jumlah_mata_id`, `name`, `price`, `stock`, `created_at`, `updated_at`, `deleted_at`, `recovered_at`, `batas_tanggal_sistem`, `mode_batas_tanggal`, `otoritas`, `kode_ky`) VALUES
(2, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Laptop HP', 32535435.00, 12, '2025-07-09 07:34:43', '2025-07-09 07:34:43', NULL, NULL, NULL, 'automatic', NULL, NULL),
(3, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'LAPTOP Asus', 1242123.00, 12, '2025-07-10 05:42:42', '2025-07-10 05:42:42', NULL, NULL, NULL, 'automatic', NULL, 'geni');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `nomor_nota` varchar(32) NOT NULL,
  `tanggal_nota` date NOT NULL,
  `customer` varchar(100) DEFAULT NULL,
  `sales` varchar(100) DEFAULT NULL,
  `total` decimal(18,2) NOT NULL,
  `discount` decimal(18,2) DEFAULT 0.00,
  `tax` decimal(18,2) DEFAULT 0.00,
  `grand_total` decimal(18,2) NOT NULL,
  `payment_a` decimal(18,2) DEFAULT 0.00,
  `payment_b` decimal(18,2) DEFAULT 0.00,
  `account_receivable` decimal(18,2) DEFAULT 0.00,
  `payment_system` varchar(50) DEFAULT NULL,
  `otoritas` char(1) DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'manual',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_items`
--

CREATE TABLE `sales_items` (
  `id` int(11) NOT NULL,
  `sales_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `qty` decimal(10,2) NOT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `price` decimal(18,2) NOT NULL,
  `discount` decimal(18,2) DEFAULT 0.00,
  `total` decimal(18,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`id`, `name`, `description`, `otoritas`, `kode_ky`, `batas_tanggal_sistem`, `mode_batas_tanggal`, `created_at`, `updated_at`, `deleted_at`, `recovered_at`) VALUES
(1, 'cms', '', NULL, NULL, NULL, 'automatic', '2025-07-08 01:42:42', '2025-07-08 04:02:02', '2025-07-08 04:02:02', NULL),
(2, 'cm', '', NULL, NULL, NULL, 'automatic', '2025-07-08 04:02:25', '2025-07-08 04:02:25', NULL, NULL),
(3, 'kg', '', NULL, NULL, NULL, 'automatic', '2025-07-08 04:02:33', '2025-07-08 04:02:33', NULL, NULL),
(4, 'pcs', '', NULL, 'geni', NULL, 'automatic', '2025-07-10 04:54:01', '2025-07-10 04:54:01', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_date_limits`
--

CREATE TABLE `system_date_limits` (
  `id` int(11) NOT NULL,
  `menu` varchar(50) NOT NULL,
  `batas_tanggal` date NOT NULL,
  `mode_batas_tanggal` varchar(20) NOT NULL DEFAULT 'manual',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_date_limits`
--

INSERT INTO `system_date_limits` (`id`, `menu`, `batas_tanggal`, `mode_batas_tanggal`, `created_at`, `updated_at`) VALUES
(10, 'penjualan', '2025-06-06', 'manual', '2025-07-08 08:32:28', '2025-07-08 08:32:28');

-- --------------------------------------------------------

--
-- Table structure for table `ukuran_barang`
--

CREATE TABLE `ukuran_barang` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department_id` int(11) NOT NULL,
  `kode_ky` varchar(10) NOT NULL,
  `alamat` text DEFAULT NULL,
  `noktp` varchar(40) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `otoritas` char(1) DEFAULT 'T',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `department_id`, `kode_ky`, `alamat`, `noktp`, `deleted_at`, `recovered_at`, `otoritas`, `created_at`, `updated_at`) VALUES
(234, 'delby', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'delby', NULL, '', '2025-07-07 05:46:54', '2025-07-07 05:46:46', 'T', '2025-07-05 14:20:16', '2025-07-07 05:46:54'),
(909, 'geni1819', '$2y$10$yCVdmEq9i2kd62zm98/6X.I/ichcTL/s/ePbIL4zNAHrUJ9Sm6tOK', 3, 'geni', 'canggu', '', NULL, NULL, NULL, '2025-07-05 14:20:16', '2025-07-09 03:46:14'),
(910, 'test1819', 'Genius1819', 3, 'test', 'Canggu', '9727312873218312', '2025-07-07 04:29:52', NULL, 'T', '2025-07-05 14:34:12', '2025-07-07 04:29:52'),
(911, 'yono1819', '$2y$10$gRiHXmoawoyrbXpCeWcR.ObcuEAkH/TgbSy3IdxEYKl5xIIYzNrwu', 1, 'yono', 'buduk', '', NULL, NULL, NULL, '2025-07-09 03:34:37', '2025-07-09 03:46:19'),
(912, 'budi123', '$2y$10$kXkZA9UcGmuATZjoX5RpZuh6PO3xz/zyg3YuFJ/A4HaWfnzwUCKUG', 1, 'budi', 'canggu', '', NULL, '2025-07-09 03:47:36', NULL, '2025-07-09 03:37:25', '2025-07-09 03:47:36'),
(913, 'budis123', '$2y$10$y7HuCFphwIK.9mgwzLqSNOy0.CJCy2PzmEiPT9TvVVR/ny7s/Ja4y', 2, 'budis', 'canggu', '', NULL, '2025-07-09 03:47:34', NULL, '2025-07-09 03:40:16', '2025-07-09 03:47:34'),
(914, 'tono123', '$2y$10$JNP1mOPI1U.ZlA.HRXHOQOYfpHla3/n2OEWRlDHczcnbt47ouzruG', 1, 'tono', 'sfewfwe', '', NULL, NULL, NULL, '2025-07-09 03:42:13', '2025-07-09 07:33:25');

-- --------------------------------------------------------

--
-- Table structure for table `voltase`
--

CREATE TABLE `voltase` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warna_bibir`
--

CREATE TABLE `warna_bibir` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warna_body`
--

CREATE TABLE `warna_body` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warna_sinar`
--

CREATE TABLE `warna_sinar` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `otoritas` varchar(100) DEFAULT NULL,
  `kode_ky` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `recovered_at` datetime DEFAULT NULL,
  `batas_tanggal_sistem` date DEFAULT NULL,
  `mode_batas_tanggal` varchar(20) DEFAULT 'automatic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daya`
--
ALTER TABLE `daya`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dimensi`
--
ALTER TABLE `dimensi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fiting`
--
ALTER TABLE `fiting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gondola`
--
ALTER TABLE `gondola`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jenis`
--
ALTER TABLE `jenis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jumlah_mata`
--
ALTER TABLE `jumlah_mata`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kaki`
--
ALTER TABLE `kaki`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `merk`
--
ALTER TABLE `merk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model`
--
ALTER TABLE `model`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pelengkap`
--
ALTER TABLE `pelengkap`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_id` (`sales_id`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_date_limits`
--
ALTER TABLE `system_date_limits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_menu` (`menu`);

--
-- Indexes for table `ukuran_barang`
--
ALTER TABLE `ukuran_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `voltase`
--
ALTER TABLE `voltase`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warna_bibir`
--
ALTER TABLE `warna_bibir`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warna_body`
--
ALTER TABLE `warna_body`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warna_sinar`
--
ALTER TABLE `warna_sinar`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daya`
--
ALTER TABLE `daya`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dimensi`
--
ALTER TABLE `dimensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fiting`
--
ALTER TABLE `fiting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gondola`
--
ALTER TABLE `gondola`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenis`
--
ALTER TABLE `jenis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jumlah_mata`
--
ALTER TABLE `jumlah_mata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kaki`
--
ALTER TABLE `kaki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `merk`
--
ALTER TABLE `merk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `model`
--
ALTER TABLE `model`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pelengkap`
--
ALTER TABLE `pelengkap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_items`
--
ALTER TABLE `sales_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_date_limits`
--
ALTER TABLE `system_date_limits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ukuran_barang`
--
ALTER TABLE `ukuran_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=915;

--
-- AUTO_INCREMENT for table `voltase`
--
ALTER TABLE `voltase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warna_bibir`
--
ALTER TABLE `warna_bibir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warna_body`
--
ALTER TABLE `warna_body`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warna_sinar`
--
ALTER TABLE `warna_sinar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD CONSTRAINT `sales_items_ibfk_1` FOREIGN KEY (`sales_id`) REFERENCES `sales` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
