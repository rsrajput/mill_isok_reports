-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 30, 2025 at 04:30 PM
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
(9, 'Mill 1A', '2025-03-01', 'uploads/1743352123_Form (1).pdf');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `is_admin`) VALUES
(1, '007345', '$2y$10$hL8iotOLmF/uTOnKRV93QOOYOeTM.mP3jTLaA3.9C5D3.Fp6hO1Nu', 1),
(4, 'admin_user', '$2y$10$8ntca39w8kmfmaQskk0xSeTDGas9/CvbY/lMXDyNkkWfcOSh7VjQq', 1),
(10, '1234', '$2y$10$.KTSrQrsHVf1ti/99JQIQ.x3jX3AYvHWS5JnBlwWScng2F/6Iziby', 0);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
