-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2023 at 01:52 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `esca`
--

-- --------------------------------------------------------

--
-- Table structure for table `details`
--

CREATE TABLE `details` (
  `id` bigint(20) NOT NULL,
  `price_id` int(11) NOT NULL,
  `urut` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `details`
--

INSERT INTO `details` (`id`, `price_id`, `urut`) VALUES
(6, 1, 3),
(7, 9, 4),
(8, 9, 5),
(9, 5, 6),
(10, 4, 7);

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(11) NOT NULL,
  `nama` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `nama`) VALUES
(1, 'Kopi Susu Salted Caramel'),
(8, 'Americano'),
(9, 'Kopi Susu Hazelnut'),
(10, 'Kopi Susu Popcorn'),
(11, 'Kopi Susu Vanilla'),
(12, 'Kopi Susu Praline'),
(13, 'Kopi Susu Roasted Almond'),
(14, 'Kopi Susu Esca'),
(15, 'Mocca Latte'),
(16, 'Kopi Susu Cinnamon'),
(17, 'Caramel Macchiato'),
(18, 'Kopi Susu Pandan'),
(19, 'Sanger'),
(20, 'Red Velvet'),
(21, 'Matcha Latte'),
(22, 'Taro Latte'),
(23, 'Choco Mint'),
(24, 'Chocolate Signature'),
(25, 'Lemon Tea'),
(26, 'Rosella Tea'),
(27, 'Nasi Goreng'),
(28, 'Mie Goreng');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `urut` int(11) NOT NULL,
  `booked_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`urut`, `booked_at`, `delivered_at`, `paid_at`) VALUES
(3, '2023-06-11 21:32:16', '2023-06-11 21:36:59', '2023-06-11 22:12:04'),
(4, '2023-06-11 21:32:51', '2023-06-11 21:36:56', '2023-06-11 22:12:03'),
(5, '2023-06-11 21:33:01', '2023-06-11 21:37:01', '2023-06-11 22:12:05'),
(6, '2023-06-11 21:36:15', '2023-06-11 21:37:03', '2023-06-11 22:12:02'),
(7, '2023-06-12 07:12:33', '2023-06-12 07:12:53', '2023-06-12 07:13:06');

-- --------------------------------------------------------

--
-- Table structure for table `prices`
--

CREATE TABLE `prices` (
  `id` int(11) NOT NULL,
  `harga` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `label` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `prices`
--

INSERT INTO `prices` (`id`, `harga`, `menu_id`, `label`) VALUES
(1, 18000, 1, 'S'),
(2, 20000, 1, 'L'),
(4, 10000, 8, NULL),
(5, 18000, 9, 'S'),
(6, 20000, 9, 'L'),
(7, 18000, 10, 'S'),
(8, 20000, 10, 'L'),
(9, 18000, 11, 'S'),
(10, 20000, 11, 'L'),
(11, 18000, 12, 'S'),
(12, 20000, 12, 'L'),
(13, 18000, 13, 'S'),
(14, 20000, 13, 'L'),
(15, 18000, 14, 'S'),
(16, 20000, 14, 'L'),
(17, 20000, 15, 'S'),
(18, 24000, 15, 'L'),
(19, 18000, 16, 'S'),
(20, 20000, 16, 'L'),
(21, 21000, 17, 'S'),
(22, 26000, 17, 'L'),
(23, 18000, 18, 'S'),
(24, 20000, 18, 'L'),
(25, 19000, 19, 'S'),
(26, 25000, 19, 'L'),
(27, 18000, 20, 'S'),
(28, 20000, 20, 'L'),
(29, 20000, 21, 'S'),
(30, 22000, 21, 'L'),
(31, 18000, 22, 'S'),
(32, 20000, 22, 'L'),
(33, 20000, 23, 'S'),
(34, 22000, 23, 'L'),
(35, 18000, 24, 'S'),
(36, 20000, 24, 'L'),
(37, 13000, 25, 'S'),
(38, 15000, 25, 'L'),
(39, 14000, 26, 'S'),
(40, 16000, 26, 'L'),
(41, 20000, 27, NULL),
(42, 15000, 28, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `details`
--
ALTER TABLE `details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `details_price_id_foreign` (`price_id`),
  ADD KEY `details_urut_foreign` (`urut`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`urut`);

--
-- Indexes for table `prices`
--
ALTER TABLE `prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prices_menu_id_foreign` (`menu_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `details`
--
ALTER TABLE `details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `urut` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `prices`
--
ALTER TABLE `prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `details`
--
ALTER TABLE `details`
  ADD CONSTRAINT `details_ibfk_1` FOREIGN KEY (`urut`) REFERENCES `pesanan` (`urut`),
  ADD CONSTRAINT `details_ibfk_2` FOREIGN KEY (`price_id`) REFERENCES `prices` (`id`);

--
-- Constraints for table `prices`
--
ALTER TABLE `prices`
  ADD CONSTRAINT `prices_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
