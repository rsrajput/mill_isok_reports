-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 02, 2025 at 04:55 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mill_tests`
--

-- --------------------------------------------------------

--
-- Table structure for table `mill_tests`
--

CREATE TABLE `mill_tests` (
  `id` int NOT NULL,
  `mill` varchar(255) NOT NULL,
  `test_date` date NOT NULL,
  `report_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mill_tests`
--

INSERT INTO `mill_tests` (`id`, `mill`, `test_date`, `report_path`) VALUES
(12, 'Mill 1H', '2025-03-03', 'uploads/1743502668_Mill  1H fineness Report _03-03-2025.pdf'),
(13, 'Mill 1G', '2025-03-03', 'uploads/1743502695_Mill  1G  fineness Report _03-03-2025.pdf'),
(14, 'Mill 1C', '2025-03-22', 'uploads/1743502745_Mill 1C fineness Report  22.03.2025 (2).pdf'),
(15, 'Mill 1C', '2024-12-16', 'uploads/1743502807_Mill  1C  fineness Report _16-12-2024.pdf'),
(16, 'Mill 2A', '2025-02-10', 'uploads/1743502915_Mill 2A   fineness Report _10-02-2025.pdf.pdf'),
(17, 'Mill 4C', '2025-02-18', 'uploads/1743503061_Mill 4C  fineness report_18-02-2025.pdf'),
(18, 'Mill 3J', '2025-02-18', 'uploads/1743503089_Mill 3J Finess report_18-02-2025.pdf'),
(19, 'Mill 3A', '2025-02-18', 'uploads/1743503117_Mill 3A Finess report_18-02-2025.pdf'),
(20, 'Mill 2D', '2024-05-18', NULL),
(21, 'Mill 2H', '2024-05-18', NULL),
(22, 'Mill 2K', '2024-05-06', NULL),
(23, 'Mill 2C', '2024-05-06', NULL),
(24, 'Mill 2A OLD', '2024-05-20', NULL),
(25, 'Mill 2J', '2024-05-20', NULL),
(26, 'Mill 2G', '2024-06-15', NULL),
(27, 'Mill 2E', '2024-06-03', NULL),
(28, 'Mill 2F', '2024-06-03', NULL),
(29, 'Mill 3E', '2024-12-12', NULL),
(30, 'Mill 3H', '2024-12-12', NULL),
(31, 'Mill 3E', '2024-02-08', NULL),
(32, 'Mill 3C', '2024-02-08', NULL),
(33, 'Mill 3A OLD', '2024-01-29', NULL),
(34, 'Mill 3F', '2024-01-29', NULL),
(35, 'Mill 3D', '2024-02-08', NULL),
(36, 'Mill 3G', '2024-02-08', NULL),
(37, 'Mill 3F', '2024-08-03', NULL),
(38, 'Mill 3G', '2024-08-03', NULL),
(39, 'Mill 3H', '2024-01-29', NULL),
(40, 'Mill 3J', '2024-01-29', NULL),
(41, 'Mill 4E', '2024-08-28', NULL),
(42, 'Mill 4f', '2024-08-28', NULL),
(43, 'Mill 4H', '2024-08-28', NULL),
(44, 'Mill 4A', '2024-08-23', NULL),
(45, 'Mill 4B', '2024-08-23', NULL),
(46, 'Mill 4D', '2024-08-23', NULL),
(47, 'Mill 4G', '2024-08-23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `role` enum('viewer','editor','admin') NOT NULL DEFAULT 'viewer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `is_admin`, `role`) VALUES
(4, 'admin_user', '$2y$10$8ntca39w8kmfmaQskk0xSeTDGas9/CvbY/lMXDyNkkWfcOSh7VjQq', 1, 'admin'),
(10, '1234', '$2y$10$.KTSrQrsHVf1ti/99JQIQ.x3jX3AYvHWS5JnBlwWScng2F/6Iziby', 0, 'viewer'),
(17, '007345', '$2y$10$CxvDMsIRQaHsypFT8edo1ezqrsSnjn7obgvwr1n1bgE0Vzx1BonRu', 1, 'admin'),
(21, 'viewer', '$2y$10$mJ8p2eIMN1X15H/6SSg.lu2P/9q5ZJ.Xmv9hF8wjI8L4UbJzny3ki', 0, 'viewer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mill_tests`
--
ALTER TABLE `mill_tests`
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
-- AUTO_INCREMENT for table `mill_tests`
--
ALTER TABLE `mill_tests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
