-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2026 at 01:27 PM
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
-- Database: `userdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `asset_code` varchar(64) NOT NULL,
  `asset_name` varchar(160) NOT NULL,
  `category` varchar(80) NOT NULL,
  `location_name` varchar(120) NOT NULL,
  `status` enum('In Service','In Storage','Disposed','Repair') NOT NULL DEFAULT 'In Service',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_user` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `assigned_person_to_fix` varchar(100) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `work_order_number` varchar(100) DEFAULT NULL,
  `priority_status` enum('None','Low','Medium','High') DEFAULT NULL,
  `qr_image` varchar(255) DEFAULT NULL,
  `date_finish` date DEFAULT NULL,
  `work_done` text DEFAULT NULL,
  `work_done_status` enum('Not Started','In Progress','Completed','On Hold') DEFAULT 'Not Started',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `asset_code`, `asset_name`, `category`, `location_name`, `status`, `created_at`, `assigned_user`, `notes`, `assigned_person_to_fix`, `due_date`, `work_order_number`, `priority_status`, `qr_image`, `date_finish`, `work_done`, `work_done_status`, `image`) VALUES
(1, 'LAP-0001', 'Legion Laptop', 'Laptop', 'Biclatan', 'In Service', '2026-04-23 02:09:22', 'paul03', 'reformatting and install microsoft', 'paul02', '2026-05-07', 'WO-2026-0002', 'High', NULL, NULL, NULL, NULL, 'uploads/asset_5926d7c39d917175.webp'),
(2, 'LAP-0002', 'Legion Laptop', 'Laptop', 'Biclatan', 'In Service', '2026-04-23 02:09:42', 'robert03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Not Started', 'uploads/asset_65e45571ef540860.webp'),
(3, 'LAP-0003', 'Lenovo V15 Gen 5', 'Laptop', 'Biclatan', 'In Service', '2026-04-23 03:30:00', 'robert03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Not Started', 'uploads/asset_ec92d091dc052091.jpg'),
(4, 'PRINT-0001', 'UV Printer UG-641', 'PRINTER', 'Biclatan', 'In Storage', '2026-04-23 03:33:21', 'nathan03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Not Started', 'uploads/asset_c9f811e1ad38960b.jpg'),
(5, 'PRINT-0002', 'EPSON BluePrint', 'PRINTER', 'Biclatan', 'In Service', '2026-04-23 03:33:45', 'junvic03', NULL, 'junvic02', '2026-04-23', 'WO-2026-0005', 'High', NULL, '2026-04-23', 'Completed', 'In Progress', 'uploads/asset_3be02e30a36c44b3.png'),
(7, 'HPRESS-001', 'Brisense Heat Press Machine', 'HEAT PRESS', 'Biclatan', 'In Service', '2026-04-23 03:35:19', 'paul03', 'Needed to clean', 'arwin02', '2026-05-07', 'WO-2026-0001', 'High', NULL, NULL, NULL, NULL, 'uploads/asset_2c7daee968431312.jpg'),
(11, 'LAP-0004', 'Midea 1.5 HP', 'Laptop', 'Biclatan', 'Repair', '2026-04-23 03:38:56', 'arwin03', 'tests', 'arwin02', '2026-04-25', 'WO-2026-0006', 'High', NULL, '2026-05-07', 'Completed', 'In Progress', 'uploads/asset_6c33019e78368969.webp'),
(12, 'LAP-0005', 'HP', 'Laptop', 'Biclatan', 'Repair', '2026-04-23 03:39:09', 'nathan03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Not Started', 'uploads/asset_81effdd424969be9.webp');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `user_type` varchar(20) DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `picture` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `fullname`, `user_type`, `created_at`, `picture`, `status`, `password_hash`) VALUES
(1, 'robert01', '$2y$10$X86Pq0noighl2l9sdK1aEOuNLstPHyzFMHZodZ5izBl/wPEugCzIK', 'robert@gmail.com', 'Robert Christian D, Cazenas', 'admin', '2026-03-20 20:46:02', 'uploads/userpic_69e7c6a2229e72.92097193.png', 'active', '$2y$10$GAiHMPVwXblGfvek9ChjiOa0sO/yF4oj9C2M2vD8cnIApkscjrYsG'),
(47, 'junvic01', '', 'junvic@gmail.com', 'Junvic Ortinez', 'admin', '2026-04-23 03:50:25', NULL, 'active', '$2y$10$W3nmKS7KyIzB8FUk08imvu34YFiTf2wyQD2AE2tEME99NBUhWssiC'),
(48, 'nathan01', '', 'nathan@gmail.com', 'Nathan Fabor', 'admin', '2026-04-23 03:51:49', NULL, 'active', '$2y$10$Qjn8I2kv7DEpymk9aFTMuupIKiO30CS1.Z3A/H0iYgVNqq43I599m'),
(49, 'arwin02', '', 'arwin02@gmail.com', 'Arwin Nario', 'staff', '2026-04-23 03:53:52', 'uploads/userpic_69eaec124a5c18.38354748.png', 'active', '$2y$10$N03EQTrpplJZ/DS2EccNB.XHUGgu9l6Kpebq/I6Lb8DfekTBtkaIS'),
(50, 'junvic02', '', 'junvic02@gmail.com', 'Junvic Ortinez', 'staff', '2026-04-23 03:54:57', NULL, 'active', '$2y$10$ihQXGtMV5W.9pUz.31XdPurCmhf1A//0GZPRoo9p7lWlRF8M8XPau'),
(51, 'nathan02', '', 'nathan02@gmail.com', 'Nathan Fabor', 'staff', '2026-04-23 03:55:45', NULL, 'active', '$2y$10$AA6t1CEEVPddi2q61KEVYOp3.2jcv5IDX66IPTP4YgAf8Wcrc8tn6'),
(52, 'paul02', '', 'paul02@gmail.com', 'Paul Anjo Coronia', 'staff', '2026-04-23 03:56:32', NULL, 'active', '$2y$10$AxebdvDpm4MwAKr/EmYbJeeA7UngwpeOcAHNHoEb9vMd3RCjKq432'),
(53, 'robert02', '', 'robert02@gmail.com', 'Robert Christian D. Cazenas', 'staff', '2026-04-23 03:57:43', NULL, 'active', '$2y$10$bb3wYfJl5fRRpKz.320L8..FIKkSsaU4fomDdp9FnW7Mhy9xeya52'),
(54, 'arwin03', '', 'arwin03@gmail.com', 'Arwin Nario', 'user', '2026-04-23 03:58:26', 'uploads/userpic_69eaec286d9352.43499798.jpg', 'active', '$2y$10$C9CPIb/HWHP4PmDi1djkWus0ymazReWRvQdEZLesuB9wPICKSp31G'),
(55, 'junvic03', '', 'junvic03@gmail.com', 'Junvic Ortinez', 'user', '2026-04-23 03:59:11', NULL, 'active', '$2y$10$C2ul7lkjepCpakUEHL8RHuT9XkPFIT9Iz0Bxv17XnYA5prd2rDikO'),
(56, 'nathan03', '', 'nathan03@gmail.com', 'Nathan Fabor', 'user', '2026-04-23 04:00:00', NULL, 'active', '$2y$10$yrZrVJ1nhLnVkwzZklTiRussPRxcHlzTM3Q8xuOKzq2wavJ3q/6Aa'),
(57, 'paul03', '', 'paul03@gmail.com', 'Paul Anjo Coronia', 'user', '2026-04-23 04:00:48', NULL, 'active', '$2y$10$kCW0nMXp2Fell6CSqM9oO.saYHlZqcpYJYTbNWmiKUdajOntVxfWS'),
(58, 'robert03', '', 'robert03@gmail.com', 'Robert Christian D. Cazenas', 'user', '2026-04-23 04:02:03', NULL, 'active', '$2y$10$z3xljtcpJnxfqVPwDELKL.VWV1ix.LtuTPUMmgyKLWSsCbuTMhYey'),
(59, 'demz023', '', 'demz023@gmail.com', 'JL jr', 'admin', '2026-04-23 04:03:15', 'uploads/userpic_69e9ca6d5d97d5.04892312.jpg', 'active', '$2y$10$jRcUjgWgRESLBJmElIoGh.MOIvJEonA0kozuYR.pmUPgMh4zMzB1e'),
(60, 'ez', '', 'ez@gmail.com', 'ez ez', 'staff', '2026-04-23 07:08:57', 'uploads/userpic_69ea05516466d9.10818820.png', 'active', '$2y$10$deFm53116e/GjPtwymcUmO6XyoptWH9gBFCHukz3OyQvj3Nuexbpe');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_code` (`asset_code`),
  ADD UNIQUE KEY `unique_work_order_number` (`work_order_number`);

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
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
