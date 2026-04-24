-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2026 at 05:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shoestore`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'pending',
  `order_status` varchar(30) DEFAULT 'pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `transaction_id`, `shipping_address`, `phone`, `city`, `postal_code`, `created_at`) VALUES
(1, 5, 4050.00, 'esewa', 'Complete', 'completed', '20260102-175225-5-EFE24A', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-02 12:07:25'),
(2, 5, 4050.00, 'esewa', 'Complete', 'pending', '20260102-175351-5-9F9C54', 'kawasoti-02, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-02 12:08:51'),
(3, 5, 4050.00, 'cod', 'pending', 'pending', NULL, 'kawasoti-02, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-02 12:14:07'),
(4, 5, 7550.00, 'esewa', 'Complete', 'completed', '20260102-181431-5-A92CE5', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-02 12:29:31'),
(5, 5, 3050.00, 'cod', 'Complete', 'completed', NULL, 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-02 13:20:57'),
(6, 5, 2050.00, 'esewa', 'Complete', 'pending', '20260103-091920-5-F1D622', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-03 03:34:20'),
(7, 5, 2050.00, 'esewa', 'Complete', 'pending', '20260103-092025-5-912A30', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-03 03:35:25'),
(8, 5, 2050.00, 'esewa', 'pending', 'pending', '20260103-092422-5-508C', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-03 03:39:22'),
(9, 5, 3550.00, 'esewa', 'Complete', 'pending', '20260103-092935-5-4E142C', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-03 03:44:35'),
(10, 5, 1550.00, 'esewa', 'failed', 'cancelled', NULL, 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-03 03:48:37'),
(11, 5, 1550.00, 'esewa', 'failed', 'cancelled', '20260103-095059-5-B103A1', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-03 04:05:59'),
(12, 5, 1550.00, 'esewa', 'Complete', 'completed', '20260103-095216-5-E4B877', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-03 04:07:16'),
(13, 9, 2050.00, 'cod', 'pending', 'completed', NULL, 'jiraune, jiraune', '98899999999999999', 'jiraune', '33000', '2026-01-03 04:45:58'),
(14, 5, 1610.00, 'esewa', 'Complete', 'completed', '20260103-105232-5-48A3E4', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-03 05:07:32'),
(15, 9, 1610.00, 'esewa', 'Complete', 'completed', '20260103-153340-9-3D8516', 'jiraune, jiraune', '9876543355', 'jiraune', '33000', '2026-01-03 09:48:40'),
(16, 9, 2050.00, 'esewa', 'failed', 'cancelled', NULL, 'Kawasoti-02, jiraune', '9876543355', 'jiraune', '33000', '2026-01-03 09:51:17'),
(17, 9, 1610.00, 'esewa', 'pending', 'completed', '20260105-115013-9-7D593', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-05 06:05:13'),
(18, 5, 4050.00, 'cod', 'pending', 'pending', NULL, 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-05 15:08:06'),
(19, 9, 2050.00, 'esewa', 'pending', 'pending', '20260105-211013-9-6B2C28', 'jiraune, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-05 15:25:13'),
(20, 9, 2050.00, 'cod', 'pending', 'pending', NULL, 'jiraune, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-05 15:32:58'),
(21, 9, 1550.00, 'esewa', 'pending', 'pending', '20260105-211912-9-ADC4E2', 'oki, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-05 15:34:12'),
(22, 5, 1550.00, 'esewa', 'pending', 'pending', '20260105-213228-5-87936', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-05 15:47:28'),
(23, 5, 1050.00, 'esewa', 'pending', 'pending', '20260105-220901-5-210F6A', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-05 16:24:01'),
(24, 9, 1550.00, 'esewa', 'success', 'pending', '20260106-110304-9-8BE16F', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 05:18:04'),
(25, 5, 2050.00, 'esewa', 'pending', 'completed', '20260106-112158-5-5838F1', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 05:36:58'),
(26, 5, 1610.00, 'esewa', 'pending', 'pending', '20260106-112709-5-157867', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 05:42:09'),
(27, 5, 1550.00, 'esewa', 'pending', 'pending', '20260106-113319-5-62AF2F', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 05:48:19'),
(28, 5, 1050.00, 'esewa', 'pending', 'pending', '20260106-114020-5-AD02BA', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 05:55:20'),
(29, 5, 1550.00, 'esewa', 'pending', 'pending', '20260106-114835-5-390019', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:03:35'),
(30, 5, 1550.00, 'esewa', 'pending', 'pending', '20260106-115515-5-15F84D', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:10:15'),
(31, 5, 1550.00, 'cod', 'pending', 'pending', '20260106-115943-5-C2D935', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:14:43'),
(32, 5, 1050.00, 'esewa', 'pending', 'pending', '20260106-120004-5-8C5526', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:15:04'),
(33, 5, 1550.00, 'esewa', 'completed', 'processing', '20260106-072413-5-695caa8db332d', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:24:13'),
(34, 9, 2050.00, 'esewa', 'completed', 'processing', '20260106-073412-9-695cace4312cf', 'oki, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:34:12'),
(35, 5, 1610.00, 'esewa', 'completed', 'processing', '20260106-073758-5-695cadc64fcca', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:37:57'),
(36, 5, 1610.00, 'esewa', 'completed', 'processing', '20260106-074408-5-695caf38b93e7', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:44:08'),
(37, 5, 1050.00, 'esewa', 'completed', 'processing', '20260106-075038-5-695cb0be3efa7', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:50:37'),
(38, 5, 2050.00, 'cod', 'pending', 'pending', '20260106-124020-5-751E23', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:55:20'),
(39, 5, 1550.00, 'esewa', 'failed', 'cancelled', '20260106-124103-5-A4973B', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:56:03'),
(40, 5, 3050.00, 'esewa', 'completed', 'cancelled', '20260106-075647-5-695cb22f86b6a', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-06 06:56:47'),
(41, 10, 2050.00, 'cod', 'completed', 'completed', NULL, 'prithivi chowk, Pokhara', '9811111111', 'Pokhara', '33000', '2026-01-06 15:00:00'),
(42, 10, 1050.00, 'esewa', 'completed', 'processing', '20260106-160205-10-695d23ede8ff7', 'prithivi chowk, Pokhara', '9811111111', 'Pokhara', '33000', '2026-01-06 15:02:05'),
(43, 9, 2050.00, 'cod', 'pending', 'pending', NULL, 'Provience no-4, Kawasoti, Nawalpur, chitwan', '9811111111', 'chitwan', '33000', '2026-01-07 04:54:19'),
(44, 9, 1050.00, 'esewa', 'completed', 'completed', '20260107-055457-9-695de7215abc7', 'Provience no-4, Kawasoti, Nawalpur, chitwan', '9811111111', 'chitwan', '33000', '2026-01-07 04:54:56'),
(45, 5, 1550.00, 'esewa', 'completed', 'processing', '20260107-055738-5-695de7c2c8dcb', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-07 04:57:38'),
(46, 5, 2050.00, 'esewa', 'completed', 'processing', '20260107-060042-5-695de87a15358', 'Provience no-4, Kawasoti, Nawalpur, Kawasoti', '9811488333', 'Kawasoti', '33000', '2026-01-07 05:00:41'),
(47, 9, 2050.00, 'cod', 'pending', 'pending', NULL, 'Provience no-4, Kawasoti, Nawalpur', '9811488333', 'Kawasoti', '33000', '2026-02-05 05:11:12'),
(48, 9, 2050.00, 'esewa', 'pending', 'pending', NULL, 'Provience no-4, Kawasoti, Nawalpur', '9811488333', 'Kawasoti', '33000', '2026-02-05 05:11:28'),
(49, 9, 2050.00, 'cod', 'pending', 'pending', NULL, 'Provience no-4, Kawasoti, Nawalpur', '9811488333', 'Kawasoti', '33000', '2026-02-05 05:12:01'),
(50, 9, 2050.00, 'cod', 'pending', 'pending', NULL, 'Provience no-4, Kawasoti, Nawalpur', '9811488333', 'Kawasoti', '33000', '2026-02-05 05:12:42'),
(51, 9, 1550.00, 'esewa', 'completed', 'processing', '20260205-061408-9-698427204a5dd', 'Provience no-4, Kawasoti, Nawalpur', '9811488333', 'Kawasoti', '33000', '2026-02-05 05:14:08'),
(52, 9, 2050.00, 'esewa', 'completed', 'processing', '20260205-064214-9-69842db65adfa', 'Province no-4, Kawasoti,', '9811488333', 'Kawasoti', '33000', '2026-02-05 05:42:14'),
(53, 9, 2050.00, 'cod', 'pending', 'pending', NULL, 'jiraune', '9811488333', 'Kawasoti', '33000', '2026-02-05 05:43:45'),
(54, 9, 2050.00, 'cod', 'pending', 'pending', NULL, 'oki', '9800000000', 'dharan', '2200', '2026-02-13 04:52:07'),
(55, 9, 1550.00, 'esewa', 'completed', 'processing', '20260213-055256-9-698eae2845c48', 'Provience no-4, Kawasoti, Nawalpur', '9811488333', 'Kawasoti', '33000', '2026-02-13 04:52:55'),
(56, 18, 2050.00, 'cod', 'pending', 'pending', NULL, 'okiii', '9811488333', 'dharan', '33000', '2026-02-13 05:32:00'),
(57, 18, 1610.00, 'esewa', 'completed', 'processing', '20260213-063233-18-698eb771e0cfa', 'Kawasoti-02, Nawalpur', '9811488333', 'dharan', '33000', '2026-02-13 05:32:33'),
(58, 9, 3170.00, 'cod', 'pending', 'completed', NULL, 'Province no-4, Kawasoti,', '9800000000', 'Bardiya', '2200', '2026-02-13 14:09:02'),
(59, 9, 2050.00, 'cod', 'pending', 'pending', NULL, '-1', '0000000000', '-1', '-1', '2026-02-17 06:42:18'),
(60, 19, 1550.00, 'cod', 'pending', 'pending', NULL, 'near ntc', '9866140623', 'Kawasoti', '33000', '2026-03-30 12:29:53'),
(61, 19, 2050.00, 'esewa', 'completed', 'completed', '20260330-143255-19-69ca6d77ad3fc', 'Provience no-4, Kawasoti, Nawalpur', '9866140623', 'Kawasoti', '33000', '2026-03-30 12:32:55'),
(62, 19, 1050.00, 'cod', 'pending', 'pending', NULL, 'Kawasoti-02, Nawalpur', '8899999999', 'Kawasoti', '', '2026-04-01 02:29:20'),
(63, 19, 1050.00, 'esewa', 'completed', 'processing', '20260401-044120-19-69cc85d09f2e3', 'Provience no-4, Kawasoti, Nawalpur', '8989898989', 'Kawasoti', '33000', '2026-04-01 02:41:19'),
(64, 19, 1610.00, 'cod', 'pending', 'pending', NULL, 'Provience no-4, Kawasoti, Nawalpur', '9898989898', 'Kawasoti', '33000', '2026-04-01 02:46:05'),
(65, 19, 2050.00, 'cod', 'pending', 'pending', NULL, 'oki', '9818181828', 'oki', '', '2026-04-01 02:47:49'),
(66, 19, 2050.00, 'cod', 'completed', 'completed', NULL, 'oki', '7767676767', 'oki', '', '2026-04-01 03:01:59'),
(67, 19, 1050.00, 'cod', 'completed', 'completed', NULL, 'oki', '9811488333', 'oki', '', '2026-04-03 03:24:15'),
(68, 19, 1650.00, 'esewa', 'pending', 'completed', NULL, 'oki', '9811488333', 'oki', '', '2026-04-03 03:25:39'),
(69, 19, 1650.00, 'cod', 'pending', 'cancelled', NULL, 'kawasoti', '9822122222', 'kawasoti', '', '2026-04-03 03:28:43'),
(70, 9, 4050.00, 'cod', 'completed', 'completed', NULL, 'Kawasoti-02, Nawalpur', '9801000000', 'Kawasoti', '', '2026-04-03 03:42:51'),
(71, 19, 1650.00, 'esewa', 'completed', 'cancelled', '20260405-122603-19-69d238bbd6fba', 'Kawasoti-02, Nawalpur', '9801000000', 'Kawasoti', '', '2026-04-05 10:26:03'),
(72, 19, 2050.00, 'esewa', 'completed', 'processing', '20260411-050954-19-69d9bb82d2610', 'Kawasoti-02, Nawalpur', '9801000000', 'Kawasoti', '', '2026-04-11 03:09:54'),
(73, 19, 1550.00, 'esewa', 'failed', 'cancelled', NULL, 'Kawasoti-02, Nawalpur', '9801000000', 'Kawasoti', '', '2026-04-11 03:11:07'),
(74, 19, 1650.00, 'esewa', 'completed', 'completed', '20260416-122004-19-69e0b7d4cb121', 'Provience no-4, Kawasoti, Nawalpur', '9811488333', 'Kawasoti', '33000', '2026-04-16 10:20:04'),
(75, 19, 1610.00, 'esewa', 'failed', 'cancelled', NULL, 'Provience no-4, Kawasoti, Nawalpur', '9811488333', 'Kawasoti', '33000', '2026-04-18 09:53:28'),
(76, 19, 2050.00, 'esewa', 'failed', 'cancelled', NULL, 'Provience no-4, Kawasoti, Nawalpur', '9811488333', 'Kawasoti', '33000', '2026-04-19 02:25:14'),
(77, 19, 2050.00, 'cod', 'pending', 'cancelled', NULL, 'Provience no-4, Kawasoti, Nawalpur', '9811488333', 'Kawasoti', '33000', '2026-04-19 02:26:01');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `total`) VALUES
(1, 1, 1, 'Formal Shoe', 2000.00, 2, 4000.00),
(2, 2, 1, 'Formal Shoe', 2000.00, 2, 4000.00),
(3, 3, 1, 'Formal Shoe', 2000.00, 2, 4000.00),
(4, 4, 4, 'Air zoom', 1500.00, 5, 7500.00),
(5, 5, 4, 'Air zoom', 1500.00, 2, 3000.00),
(6, 6, 1, 'Formal Shoe', 2000.00, 1, 2000.00),
(7, 7, 1, 'Formal Shoe', 2000.00, 1, 2000.00),
(8, 8, 1, 'Formal Shoe', 2000.00, 1, 2000.00),
(9, 9, 1, 'Formal Shoe', 2000.00, 1, 2000.00),
(10, 9, 4, 'Air zoom', 1500.00, 1, 1500.00),
(11, 10, 4, 'Air zoom', 1500.00, 1, 1500.00),
(14, 13, 5, 'Black Boot', 2000.00, 1, 2000.00),
(15, 14, 6, 'Baby Shoe', 1560.00, 1, 1560.00),
(16, 15, 6, 'Baby Shoe', 1560.00, 1, 1560.00),
(17, 16, 1, 'Formal Shoe', 2000.00, 1, 2000.00),
(18, 17, 6, 'Baby Shoe', 1560.00, 1, 1560.00),
(19, 18, 1, 'Formal Shoe', 2000.00, 2, 4000.00),
(20, 19, 5, 'Black Boot', 2000.00, 1, 2000.00),
(21, 20, 5, 'Black Boot', 2000.00, 1, 2000.00),
(24, 23, 3, ' Nwe brand Addidas', 1000.00, 1, 1000.00),
(26, 25, 5, 'Black Boot', 2000.00, 1, 2000.00),
(27, 26, 6, 'Baby Shoe', 1560.00, 1, 1560.00),
(29, 28, 3, ' Nwe brand Addidas', 1000.00, 1, 1000.00),
(31, 30, 4, 'Air zoom', 1500.00, 1, 1500.00),
(32, 31, 4, 'Air zoom', 1500.00, 1, 1500.00),
(33, 32, 3, ' Nwe brand Addidas', 1000.00, 1, 1000.00),
(35, 34, 5, 'Black Boot', 2000.00, 1, 2000.00),
(36, 35, 6, 'Baby Shoe', 1560.00, 1, 1560.00),
(37, 36, 6, 'Baby Shoe', 1560.00, 1, 1560.00),
(38, 37, 3, ' Nwe brand Addidas', 1000.00, 1, 1000.00),
(39, 38, 1, 'Formal Shoe', 2000.00, 1, 2000.00),
(40, 39, 4, 'Air zoom', 1500.00, 1, 1500.00),
(41, 40, 4, 'Air zoom', 1500.00, 2, 3000.00),
(42, 41, 5, 'Black Boot', 2000.00, 1, 2000.00),
(43, 42, 3, ' Nwe brand Addidas', 1000.00, 1, 1000.00),
(44, 43, 5, 'Black Boot', 2000.00, 1, 2000.00),
(45, 44, 3, ' Nwe brand Addidas', 1000.00, 1, 1000.00),
(47, 46, 5, 'Black Boot', 2000.00, 1, 2000.00),
(48, 50, 5, 'Black Boot', 2000.00, 1, 2000.00),
(49, 51, 4, 'Air zoom', 1500.00, 1, 1500.00),
(50, 52, 5, 'Black Boot', 2000.00, 1, 2000.00),
(51, 53, 1, 'Formal Shoe', 2000.00, 1, 2000.00),
(52, 54, 5, 'Black Boot', 2000.00, 1, 2000.00),
(53, 55, 4, 'Air zoom', 1500.00, 1, 1500.00),
(54, 56, 5, 'Black Boot', 2000.00, 1, 2000.00),
(55, 57, 6, 'Baby Shoe', 1560.00, 1, 1560.00),
(56, 58, 6, 'Baby Shoe', 1560.00, 2, 3120.00),
(57, 59, 1, 'Formal Shoe', 2000.00, 1, 2000.00),
(58, 60, 4, 'Air zoom', 1500.00, 1, 1500.00),
(59, 61, 5, 'Black Boot', 2000.00, 1, 2000.00),
(60, 62, 3, 'Nwe brand Addidas', 1000.00, 1, 1000.00),
(61, 63, 3, 'Nwe brand Addidas', 1000.00, 1, 1000.00),
(62, 64, 6, 'Baby Shoe', 1560.00, 1, 1560.00),
(63, 65, 5, 'Black Boot', 2000.00, 1, 2000.00),
(64, 66, 7, 'shoe of baby', 2000.00, 1, 2000.00),
(65, 67, 3, 'New brand Addidas', 1000.00, 1, 1000.00),
(66, 68, 9, 'Running Nike', 1600.00, 1, 1600.00),
(67, 69, 9, 'Running Nike', 1600.00, 1, 1600.00),
(68, 70, 5, 'Black Boot', 2000.00, 2, 4000.00),
(69, 71, 9, 'Running Nike', 1600.00, 1, 1600.00),
(70, 72, 1, 'Formal Shoe', 2000.00, 1, 2000.00),
(71, 73, 4, 'Air zoom', 1500.00, 1, 1500.00),
(72, 74, 9, 'Running Nike', 1600.00, 1, 1600.00),
(73, 75, 6, 'Baby Shoe', 1560.00, 1, 1560.00),
(74, 76, 1, 'Formal Shoe', 2000.00, 1, 2000.00),
(75, 77, 1, 'Formal Shoe', 2000.00, 1, 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `brand`, `price`, `color`, `size`, `image`, `created_at`, `category`, `quantity`, `description`) VALUES
(1, 'Formal Shoe', 'Formal', 2000.00, 'Grey', '45', 'formal.jpeg', '2025-12-12 03:24:06', 'Male', 197, NULL),
(3, 'New brand Addidas', 'Addidas', 1000.00, 'light black', '44', 'addidas.jpeg', '2025-12-12 03:36:20', 'Male', 1, ''),
(4, 'Air zoom', 'Air Zoom', 1500.00, 'orange black', '44', 'prod_69842064092873.46709919.jpeg', '2025-12-12 03:37:32', 'Male', 47, ''),
(5, 'Black Boot', 'Addidas', 2000.00, 'Black', '30', 'prod_6984202f16c6f8.05316264.jpeg', '2026-01-03 04:22:35', 'Female', 3, 'This is the new launch black boot for female.'),
(6, 'Baby Shoe', 'Nike', 1560.00, 'Light white', '10', 'prod_6984201fdf6c51.60531907.jpeg', '2026-01-03 04:38:31', 'Kid', 1, 'Here is our newly launch product for the baby that keeps them support to fly.'),
(7, 'shoe of baby', 'Nike', 2000.00, 'pink', '12', 'prod_69816841778a38.30629262.jpeg', '2026-01-06 15:11:08', 'Kid', 5, 'This is unisex baby shoes looks outstanding for your baby'),
(8, 'Lightwhite shoe', 'Nike', 2000.00, 'Light white', '30', 'product_1770270789.jpeg', '2026-02-05 05:53:09', 'Female', 6, 'newly arrived for girls'),
(9, 'Running Nike', 'Nike', 1600.00, 'Black and white', '9', 'product_1774875171.jpeg', '2026-03-30 12:52:51', 'Male', 2, 'This is newly arrival Nike brand shoes, mostly used for running purpose, that makes you comfort while running');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_admin`, `created_at`, `phone`, `address`, `city`, `postal_code`, `country`) VALUES
(5, 'sagar Bhattarai', 's.2413695@gmail.com', '$2y$10$TMwWx3CL/F.zifw7a9m2MevISjIaP17Rr1m6GV1p65iLsl7J4BKAC', 0, '2026-01-02 04:55:02', '9811488333', 'Provience no-4, Kawasoti, Nawalpur', 'Kawasoti', '33000', 'Nepal'),
(8, 'Wauuuadmin', 'admin@gmail.com', '$2y$10$jKfia.zPBojHkDw1WZtlNu3A4RQzFkoODqqMpWqeO.M/lJOf/AFv.', 1, '2026-01-02 11:55:29', NULL, NULL, NULL, NULL, NULL),
(9, 'Rose andrewsonn', 'Rose@gmail.com', '$2y$10$3OJ9U76b6eLjJqzPwTORZOLySACVQfUnMrvLk.mfg1GIn2yn2.doO', 0, '2026-01-03 04:44:57', '9811111111', 'Provience no-4, Kawasoti, Nawalpur', 'chitwan', '330000', 'Nepal'),
(10, 'Sneha Neupane', 'Sneha@gmail.com', '$2y$10$kple41BUno2j2bXXJz7F9OcGzWc8EtW9jIXvtexRxtdxZ.lopY.Ou', 0, '2026-01-06 14:58:27', '9822222222', NULL, 'pokhara', NULL, NULL),
(11, 'dikshyant1', 'dikshyant12@gmail.com', '$2y$10$bztT1dCljSuWDQGAB/8yi.44xGi6OMf7pB335NCxQYkJNBQLiDIVe', 0, '2026-01-07 05:19:51', NULL, NULL, NULL, NULL, NULL),
(16, 'Ram sharma', 'ram12@gmail.com', '4f4f387e3fbd3cc87ff3362fb56c900790fee7c96a573f6d79595e67df97', 0, '2026-02-12 15:50:15', NULL, NULL, NULL, NULL, NULL),
(17, 'Rajesh hamal', 'rajesh12@gmail.com', '5d65bfd24b410e0c0c88b26876c6d8c0d896e3d245a09d070c28a3e0d453', 0, '2026-02-12 16:00:55', NULL, NULL, NULL, NULL, NULL),
(18, 'Shristi Sarkar', 'shristi12@gmail.com', '11c1a74448abee0785888e450007fb64141e49d748d84f817ff2a2c891d3', 0, '2026-02-12 16:03:21', NULL, NULL, NULL, NULL, NULL),
(19, 'Sagar Bhattarai', 'sagar12@gmail.com', '1bec0dad1fc6dd8fd058e167f1bf5c8f8b1ef2e67008e9d3414d99a54dc7', 0, '2026-03-30 12:29:03', NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_date` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_order_items_order` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_products_category` (`category`),
  ADD KEY `idx_products_brand` (`brand`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_admin` (`is_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
