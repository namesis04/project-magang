-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 15 Jun 2023 pada 13.33
-- Versi server: 10.1.32-MariaDB
-- Versi PHP: 5.6.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
-- Struktur dari tabel `audits`
--

CREATE TABLE IF NOT EXISTS `audits` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `action` varchar(40) NOT NULL,
  `user_id` int(11) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `audits_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `details`
--

CREATE TABLE IF NOT EXISTS `details` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `price_id` int(11) NOT NULL,
  `urut` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `details_price_id_foreign` (`price_id`),
  KEY `details_urut_foreign` (`urut`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `menus`
--

CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE IF NOT EXISTS `pesanan` (
  `urut` int(11) NOT NULL AUTO_INCREMENT,
  `booked_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`urut`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `prices`
--

CREATE TABLE IF NOT EXISTS `prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `harga` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `label` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prices_menu_id_foreign` (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fn` text NOT NULL,
  `username` varchar(11) NOT NULL,
  `password` char(43) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `audits`
--
ALTER TABLE `audits`
  ADD CONSTRAINT `audits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `details`
--
ALTER TABLE `details`
  ADD CONSTRAINT `details_ibfk_1` FOREIGN KEY (`urut`) REFERENCES `pesanan` (`urut`),
  ADD CONSTRAINT `details_ibfk_2` FOREIGN KEY (`price_id`) REFERENCES `prices` (`id`);

--
-- Ketidakleluasaan untuk tabel `prices`
--
ALTER TABLE `prices`
  ADD CONSTRAINT `prices_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
