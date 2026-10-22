-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.11.6-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.21.0.7344
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for dsv
CREATE DATABASE IF NOT EXISTS `dsv` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `dsv`;

-- Dumping structure for table dsv.deposit_1
CREATE TABLE IF NOT EXISTS `deposit_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenor` varchar(100) NOT NULL,
  `interest_rate` decimal(25,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.deposit_1: ~2 rows (approximately)
DELETE FROM `deposit_1`;
INSERT INTO `deposit_1` (`id`, `tenor`, `interest_rate`, `min_amount`, `description`) VALUES
	(2, 'daada', 4214.00, 2142412.00, 'fdsfsdfsdf g g  g g g g'),
	(3, '1dsadsad', 111111.00, 11111.00, 'adsadsadsadsada');

-- Dumping structure for table dsv.deposit_2
CREATE TABLE IF NOT EXISTS `deposit_2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenor` varchar(100) NOT NULL,
  `interest_rate` decimal(25,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.deposit_2: ~0 rows (approximately)
DELETE FROM `deposit_2`;

-- Dumping structure for table dsv.deposit_3
CREATE TABLE IF NOT EXISTS `deposit_3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenor` varchar(100) NOT NULL,
  `interest_rate` decimal(25,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.deposit_3: ~0 rows (approximately)
DELETE FROM `deposit_3`;

-- Dumping structure for table dsv.deposit_4
CREATE TABLE IF NOT EXISTS `deposit_4` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenor` varchar(100) NOT NULL,
  `interest_rate` decimal(25,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.deposit_4: ~0 rows (approximately)
DELETE FROM `deposit_4`;

-- Dumping structure for table dsv.deposit_5
CREATE TABLE IF NOT EXISTS `deposit_5` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenor` varchar(100) NOT NULL,
  `interest_rate` decimal(25,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.deposit_5: ~0 rows (approximately)
DELETE FROM `deposit_5`;

-- Dumping structure for table dsv.deposit_6
CREATE TABLE IF NOT EXISTS `deposit_6` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenor` varchar(100) NOT NULL,
  `interest_rate` decimal(25,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.deposit_6: ~0 rows (approximately)
DELETE FROM `deposit_6`;

-- Dumping structure for table dsv.deposit_7
CREATE TABLE IF NOT EXISTS `deposit_7` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenor` varchar(100) NOT NULL,
  `interest_rate` decimal(25,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.deposit_7: ~0 rows (approximately)
DELETE FROM `deposit_7`;

-- Dumping structure for table dsv.deposit_8
CREATE TABLE IF NOT EXISTS `deposit_8` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tenor` varchar(100) NOT NULL,
  `interest_rate` decimal(25,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.deposit_8: ~0 rows (approximately)
DELETE FROM `deposit_8`;

-- Dumping structure for table dsv.identity
CREATE TABLE IF NOT EXISTS `identity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `marquee` text DEFAULT NULL,
  `options` varchar(10) DEFAULT '1',
  `themes` varchar(100) DEFAULT 'show-0.css',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.identity: ~0 rows (approximately)
DELETE FROM `identity`;
INSERT INTO `identity` (`id`, `logo`, `title`, `name`, `marquee`, `options`, `themes`) VALUES
	(1, 'logo_1785403681.png', 'Display Signage', 'Sub Title / Unit Name', 'Selamat Datang di Layanan Informasi Digital', '8', 'show-0.css');

-- Dumping structure for table dsv.titles
CREATE TABLE IF NOT EXISTS `titles` (
  `id_information` int(11) NOT NULL AUTO_INCREMENT,
  `title_1` varchar(255) DEFAULT NULL,
  `title_2` varchar(255) DEFAULT NULL,
  `title_3` varchar(255) DEFAULT NULL,
  `title_4` varchar(255) DEFAULT NULL,
  `title_5` varchar(255) DEFAULT NULL,
  `title_6` varchar(255) DEFAULT NULL,
  `title_7` varchar(255) DEFAULT NULL,
  `title_8` varchar(255) DEFAULT NULL,
  `title_9` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_information`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.titles: ~0 rows (approximately)
DELETE FROM `titles`;
INSERT INTO `titles` (`id_information`, `title_1`, `title_2`, `title_3`, `title_4`, `title_5`, `title_6`, `title_7`, `title_8`, `title_9`) VALUES
	(1, 'Informasi Kurs Valuta Asing', 'title 1', '', '', '', '', '', '', '');

-- Dumping structure for table dsv.valas
CREATE TABLE IF NOT EXISTS `valas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_code` varchar(10) NOT NULL,
  `currency_name` varchar(100) NOT NULL,
  `flag_image` varchar(50) DEFAULT NULL,
  `buy_rate` decimal(15,2) DEFAULT 0.00,
  `sell_rate` decimal(15,2) DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.valas: ~0 rows (approximately)
DELETE FROM `valas`;
INSERT INTO `valas` (`id`, `currency_code`, `currency_name`, `flag_image`, `buy_rate`, `sell_rate`) VALUES
	(1, 'IDR', 'Rupiah', 'id', 111.00, 111.00);

-- Dumping structure for table dsv.videos
CREATE TABLE IF NOT EXISTS `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `create_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table dsv.videos: ~0 rows (approximately)
DELETE FROM `videos`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
