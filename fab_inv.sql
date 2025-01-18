-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 18, 2025 at 11:28 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fabtransport_invoice`
--

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `invoice` int(11) NOT NULL,
  `company` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(15) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `abn` varchar(20) NOT NULL,
  `runsheet` varchar(255) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `sub_total` decimal(20,2) NOT NULL,
  `tax_rate` decimal(10,2) DEFAULT 0.00,
  `other_cost` decimal(20,2) DEFAULT 0.00,
  `total_cost` decimal(20,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `date`, `invoice`, `company`, `address`, `phone`, `postal_code`, `abn`, `runsheet`, `amount`, `sub_total`, `tax_rate`, `other_cost`, `total_cost`, `created_at`, `updated_at`) VALUES
(51, '2025-01-10', 10001, 'fab', 'adfasdf', '42342', '132', '24323', '423234', '0.00', '150.00', '12.00', '0.00', '162.00', '2025-01-17 19:39:56', '2025-01-17 19:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `customer_invoice_no` varchar(255) DEFAULT NULL,
  `item_row_id` varchar(255) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `item_value` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `customer_invoice_no`, `item_row_id`, `item_name`, `item_value`) VALUES
(63, 44, '11', '44~1', 'DELIV', '1.00'),
(57, 43, '10001-1', '43~1', 'DELIV', '400.00'),
(58, 43, '10001-1', '43~1', 'ASSEM', '200.00'),
(59, 43, '10001-1', '43~1', 'RUB', '600.00'),
(60, 43, '10001-2', '43~2', 'DELIV', '30.00'),
(61, 43, '10001-2', '43~2', 'ASSEM', '50.00'),
(62, 43, '10001-2', '43~2', 'RUB', '70.00'),
(64, 44, '11', '44~1', 'DISAS', '1.00'),
(65, 44, '11', '44~1', 'ASSEM', '1.00'),
(66, 44, '11', '44~1', 'RUB', '1.00'),
(67, 44, '11', '44~1', 'UPST', '1.00'),
(68, 44, '11', '44~1', 'DOWNST', '1.00'),
(69, 44, '11', '44~1', 'PREM', '1.00'),
(70, 44, '11', '44~1', 'BRTrans', '1.00'),
(71, 44, '11', '44~1', 'Ins', '1.00'),
(72, 44, '11', '44~1', 'H/Dliv', '1.00'),
(73, 44, '11', '44~1', 'Vol', '1.00'),
(74, 44, '11', '44~1', 'WaterCon', '1.00'),
(75, 44, '11', '44~1', 'Door/R', '1.00'),
(76, 44, '', '44~2', 'DELIV', '1.00'),
(77, 44, '', '44~2', 'DISAS', '1.00'),
(78, 44, '', '44~2', 'ASSEM', '1.00'),
(79, 44, '', '44~2', 'RUB', '1.00'),
(80, 44, '', '44~2', 'UPST', '1.00'),
(81, 44, '', '44~2', 'DOWNST', '1.00'),
(82, 44, '', '44~2', 'PREM', '1.00'),
(83, 44, '', '44~2', 'BRTrans', '1.00'),
(84, 44, '', '44~2', 'Ins', '1.00'),
(85, 44, '', '44~2', 'H/Dliv', '1.00'),
(86, 44, '', '44~2', 'Vol', '1.00'),
(87, 44, '', '44~2', 'WaterCon', '11.00'),
(88, 44, '', '44~2', 'Door/R', '11.00'),
(89, 45, '', '45~1', 'DELIV', '11.00'),
(90, 45, '', '45~1', 'DISAS', '11.00'),
(91, 45, '', '45~1', 'ASSEM', '11.00'),
(92, 45, '', '45~1', 'RUB', '1.00'),
(93, 45, '', '45~1', 'UPST', '11.00'),
(94, 45, '', '45~1', 'WaterCon', '11.00'),
(95, 45, '', '45~1', 'Door/R', '11.00'),
(96, 46, '1', '46~1', 'DELIV', '22.00'),
(97, 46, '1', '46~1', 'DISAS', '22.00'),
(98, 46, '1', '46~1', 'ASSEM', '22.00'),
(99, 46, '1', '46~1', 'RUB', '22.00'),
(100, 46, '1', '46~1', 'UPST', '22.00'),
(101, 46, '1', '46~1', 'DOWNST', '22.00'),
(102, 46, '1', '46~1', 'PREM', '22.00'),
(103, 46, '1', '46~1', 'BRTrans', '22.00'),
(104, 46, '1', '46~1', 'Ins', '22.00'),
(105, 46, '1', '46~1', 'H/Dliv', '22.00'),
(106, 46, '1', '46~1', 'Vol', '22.00'),
(107, 46, '1', '46~1', 'WaterCon', '22.00'),
(108, 46, '1', '46~1', 'Door/R', '22.00'),
(109, 46, '2', '46~2', 'DELIV', '222.00'),
(110, 46, '2', '46~2', 'DISAS', '0.00'),
(111, 46, '2', '46~2', 'ASSEM', '55.00'),
(112, 46, '2', '46~2', 'RUB', '77.00'),
(113, 46, '2', '46~2', 'UPST', '77.00'),
(114, 46, '2', '46~2', 'DOWNST', '99.00'),
(115, 46, '2', '46~2', 'PREM', '8.00'),
(116, 46, '2', '46~2', 'BRTrans', '77.00'),
(117, 46, '2', '46~2', 'Ins', '77.00'),
(118, 46, '2', '46~2', 'H/Dliv', '77.00'),
(119, 46, '2', '46~2', 'Vol', '77.00'),
(120, 46, '2', '46~2', 'WaterCon', '8.00'),
(121, 46, '2', '46~2', 'Door/R', '8.00'),
(122, 48, '2232', '48~1', 'DELIV', '23232.00'),
(123, 48, '2232', '48~1', 'DISAS', '2323.00'),
(124, 48, '2232', '48~1', 'ASSEM', '2323.00'),
(125, 48, '2232', '48~1', 'RUB', '232.00'),
(126, 48, '2232', '48~1', 'BRTrans', '2323.00'),
(127, 48, '2232', '48~1', 'Ins', '2323232.00'),
(128, 48, '22', '48~2', 'DELIV', '222.00'),
(129, 48, '22', '48~2', 'DISAS', '222.00'),
(130, 48, '22', '48~2', 'Ins', '22.00'),
(131, 48, '22', '48~2', 'Vol', '22.00'),
(132, 51, '231231', '51~1', 'DELIV', '100.00'),
(133, 51, '231231', '51~1', 'WaterCon', '50.00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'Invoice.FabTransport.aU', '$2y$10$ClzlshwDF7htWBdIXxHTeOzBIXrT15Wy/jpG/9ZVwP8NM6Fl9tPNO', '2024-12-06 16:19:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`) USING BTREE,
  ADD UNIQUE KEY `invoice` (`invoice`) USING BTREE;

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `invoice_id` (`invoice_id`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
