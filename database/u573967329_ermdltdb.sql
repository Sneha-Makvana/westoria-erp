-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2026 at 10:13 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u573967329_ermdltdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `advance_payments`
--

CREATE TABLE `advance_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,0) DEFAULT NULL,
  `status` enum('pending','cleared') DEFAULT 'pending',
  `date` date NOT NULL,
  `method` varchar(255) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `isDeleted` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advance_payments`
--

INSERT INTO `advance_payments` (`id`, `staff_id`, `amount`, `paid_amount`, `status`, `date`, `method`, `reason`, `branch_id`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 10, 1000.00, NULL, 'pending', '2026-04-16', 'Cash', NULL, 1, 0, '2026-04-16 10:06:12', '2026-04-16 10:06:12'),
(2, 10, 1000.00, NULL, 'pending', '2026-04-29', 'Online', NULL, 1, 0, '2026-05-06 10:56:50', '2026-05-26 13:53:46'),
(3, 10, 10000.00, NULL, 'pending', '2026-05-25', 'Cash', NULL, 1, 0, '2026-05-25 13:21:02', '2026-05-26 13:53:53');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `vehicle_number` varchar(255) DEFAULT NULL,
  `vehicle_type` varchar(255) DEFAULT NULL,
  `vehicle_brand` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_model` bigint(20) UNSIGNED DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `work_hours` decimal(5,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT '-',
  `reason` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `extraday` tinyint(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `branch_id`, `user_id`, `date`, `check_in_time`, `check_out_time`, `work_hours`, `status`, `reason`, `description`, `extraday`, `created_at`, `updated_at`) VALUES
(1, 2, 12, '2026-04-15', '19:05:14', NULL, NULL, 'P', NULL, NULL, 0, '2026-04-15 19:05:14', '2026-04-15 19:05:14'),
(2, 1, 19, '2026-05-26', '14:34:26', '15:47:26', NULL, 'P', NULL, NULL, 0, '2026-05-26 14:34:26', '2026-05-26 15:47:26');

-- --------------------------------------------------------

--
-- Table structure for table `bank_master`
--

CREATE TABLE `bank_master` (
  `id` int(11) NOT NULL,
  `branch_id` bigint(20) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 = active,0-inactive',
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_master`
--

INSERT INTO `bank_master` (`id`, `branch_id`, `bank_name`, `account_number`, `ifsc_code`, `branch_name`, `opening_balance`, `status`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, 'SBI Bank', 'f454354545345454545', '545', 'Adajan', 10000.00, 1, 0, '2026-04-15 17:31:06', '2026-04-15 17:31:06'),
(2, 1, 'The Surat District Co operative Bank Ltd', '1122334455', 'SBIN0005678', 'surat', 1000.00, 1, 1, '2026-04-30 18:50:12', '2026-05-06 10:47:58'),
(3, 2, 'SBI Bank', '1674010000825', 'SBIN0005678', 'surat', 999.99, 1, 0, '2026-05-04 17:16:38', '2026-05-04 17:16:38'),
(4, 1, 'HDFC BANK', '142536', 'ABVC34534', 'Adajan', 15000.00, 1, 0, '2026-05-06 10:40:03', '2026-05-06 10:40:03'),
(5, 1, 'ICICI Bank', '1674010000825', 'IC123452101', 'Vesu', 1000.00, 1, 0, '2026-05-06 15:07:47', '2026-05-06 15:07:47'),
(6, 1, 'YES BANK', '7689', 'SBIN0005678', '123test', 1000.00, 1, 1, '2026-05-06 15:08:56', '2026-05-06 15:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `branch_id`, `name`, `logo`, `status`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, 'Toyota', NULL, 1, 0, '2026-04-03 12:06:41', '2026-04-03 12:06:41'),
(2, 1, 'Honda', NULL, 1, 0, '2026-04-03 12:06:55', '2026-04-03 12:06:55'),
(3, 1, 'Hyundai', NULL, 1, 0, '2026-04-03 12:07:04', '2026-04-03 12:07:04'),
(4, 1, 'Tata', NULL, 1, 0, '2026-04-03 12:07:13', '2026-04-03 12:07:13'),
(5, 1, 'Mahindra', NULL, 1, 0, '2026-04-03 12:07:21', '2026-04-03 12:07:21'),
(6, 1, 'Suzuki', NULL, 1, 0, '2026-04-03 12:07:29', '2026-04-03 12:07:29'),
(7, 1, 'Kia', NULL, 1, 0, '2026-04-03 12:07:36', '2026-04-03 12:07:36'),
(8, 1, 'Ford', NULL, 1, 0, '2026-04-03 12:07:42', '2026-04-03 12:07:42'),
(9, 1, 'BMW', NULL, 1, 0, '2026-04-03 12:07:51', '2026-04-03 12:07:51'),
(10, 1, 'Mercedes-Benz', NULL, 1, 0, '2026-04-03 12:07:59', '2026-04-03 12:07:59'),
(11, 1, 'Castrol', NULL, 1, 0, '2026-04-03 15:38:12', '2026-04-03 15:38:12'),
(12, 1, 'Bosch', NULL, 1, 0, '2026-04-03 15:39:16', '2026-04-03 15:39:16'),
(13, 1, 'Brembo', NULL, 1, 0, '2026-04-03 15:40:54', '2026-04-03 15:40:54'),
(14, 1, 'Pharma', NULL, 1, 0, '2026-04-06 11:34:21', '2026-04-06 11:34:21'),
(15, 2, 'H & M', NULL, 1, 0, '2026-04-27 16:44:24', '2026-04-27 16:44:24'),
(16, 1, 'Adidas', 'img/brand/l1QbtvcpxQMXHeMgMG6iJaPEBIQmm6nujdoyvkD1.png', 1, 0, '2026-05-26 10:18:45', '2026-05-26 10:18:45'),
(17, 1, 'test', NULL, 1, 0, '2026-05-26 11:05:49', '2026-05-26 11:05:49'),
(18, 1, 'ii', NULL, 1, 0, '2026-05-26 17:56:55', '2026-05-26 17:56:55');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `branch_id`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 'Engine Parts', NULL, 1, 0, '2026-04-03 12:08:11', '2026-04-03 12:08:11'),
(2, 'Brake System', NULL, 1, 0, '2026-04-03 12:08:20', '2026-04-03 12:08:20'),
(3, 'Suspension', NULL, 1, 0, '2026-04-03 12:08:27', '2026-04-03 12:08:27'),
(4, 'Electrical Components', NULL, 1, 0, '2026-04-03 12:08:33', '2026-04-03 12:08:33'),
(5, 'Body Parts', NULL, 1, 0, '2026-04-03 12:08:38', '2026-04-03 12:08:38'),
(6, 'Interior Accessories', NULL, 1, 0, '2026-04-03 12:08:44', '2026-04-03 12:08:44'),
(7, 'Exterior Accessories', NULL, 1, 0, '2026-04-03 12:08:51', '2026-04-03 12:08:51'),
(8, 'Cooling System', NULL, 1, 0, '2026-04-03 12:08:59', '2026-04-03 12:08:59'),
(9, 'Fuel System', NULL, 1, 0, '2026-04-03 12:09:06', '2026-04-03 12:09:06'),
(10, 'Transmission', NULL, 1, 0, '2026-04-03 12:09:12', '2026-04-03 12:09:12'),
(11, 'Cosmetic', 'img/category/Y5sH9xNLIZAEw2HTzrlXmN3t8Ny88Lc51jIFaj6B.webp', 1, 0, '2026-04-06 11:33:32', '2026-05-26 17:54:08'),
(12, 'Clothes', NULL, 2, 0, '2026-04-27 16:43:58', '2026-04-27 16:43:58'),
(13, 'Clothes', 'img/category/ZQbrWfh0VEMwBENHeichthqDNoXpxYHTgwdvzrBx.jpg', 1, 0, '2026-05-26 10:15:22', '2026-05-26 10:15:22'),
(14, 'category1', NULL, 1, 0, '2026-05-26 11:05:49', '2026-05-26 11:05:49'),
(15, 'ono', NULL, 1, 1, '2026-05-26 17:54:36', '2026-05-26 17:54:50'),
(16, 'ibib', NULL, 1, 1, '2026-05-26 17:55:11', '2026-05-26 17:56:40'),
(17, 'hg', NULL, 1, 0, '2026-05-26 17:56:12', '2026-05-26 17:56:12');

-- --------------------------------------------------------

--
-- Table structure for table `connected_devices`
--

CREATE TABLE `connected_devices` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `device_code` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `connected_devices`
--

INSERT INTO `connected_devices` (`id`, `session_id`, `device_name`, `device_code`, `status`, `created_at`, `updated_at`) VALUES
(1, 'FJx8ZZMTAFb2v0VD5V4762wfBeZ0URxxk5YBs7l7', NULL, 'DEV_GJW9YVVD40', 0, '2026-04-06 12:46:15', '2026-04-06 12:46:15'),
(2, 'D8GjZqcIyWqiaZS2JeFFNmLvfeYbiDE4llGOQQhe', NULL, 'DEV_J71JMRVOUM', 0, '2026-05-11 11:59:18', '2026-05-11 11:59:18'),
(3, 'DaK0t3ayqpGr9lDb4eWTWTUtRjJaEADeuH8JKk7C', NULL, 'DEV_UDDYO8FCMK', 0, '2026-05-18 12:46:00', '2026-05-18 12:46:00'),
(4, 'L4y2MWtodnITXmEkrwiysrAyohuct0rr9Me0TeWc', NULL, 'DEV_SJRIRRKIO8', 0, '2026-05-26 11:21:48', '2026-05-26 11:21:48');

-- --------------------------------------------------------

--
-- Table structure for table `connected_device_scans`
--

CREATE TABLE `connected_device_scans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device_code` varchar(50) NOT NULL,
  `barcode` varchar(191) NOT NULL,
  `consumed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `credit_notes_type`
--

CREATE TABLE `credit_notes_type` (
  `id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credit_notes_type`
--

INSERT INTO `credit_notes_type` (`id`, `type_name`, `branch_id`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 'Adjust', 1, 0, '2026-04-15 17:48:15', '2026-04-15 17:48:15'),
(2, 'demo test', 1, 0, '2026-05-01 11:01:49', '2026-05-01 11:01:49');

-- --------------------------------------------------------

--
-- Table structure for table `credit_note_items`
--

CREATE TABLE `credit_note_items` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `type_id` varchar(255) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `credite_note_id` varchar(255) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `purchase_id` varchar(255) DEFAULT NULL,
  `product_gst_details` varchar(255) DEFAULT NULL,
  `total_amt` varchar(255) DEFAULT NULL,
  `remaining_amt` varchar(255) DEFAULT NULL,
  `total_paid` varchar(255) DEFAULT NULL,
  `settlement_amount` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `total` varchar(255) DEFAULT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credit_note_items`
--

INSERT INTO `credit_note_items` (`id`, `user_id`, `type_id`, `branch_id`, `credite_note_id`, `order_id`, `purchase_id`, `product_gst_details`, `total_amt`, `remaining_amt`, `total_paid`, `settlement_amount`, `reason`, `total`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, '3', 'receipt', 1, '1', '9', NULL, NULL, '73920.00', '73920', '0', '920', 'adjust', '73000.00', 0, '2026-04-16 10:01:55', '2026-04-16 10:01:55'),
(2, '5', 'payment', 1, '2', NULL, '7', NULL, '1200', '1200', '0', '200', 'tthd', '1000.00', 0, '2026-05-01 11:01:55', '2026-05-01 11:01:55');

-- --------------------------------------------------------

--
-- Table structure for table `custom_invoice`
--

CREATE TABLE `custom_invoice` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`products`)),
  `total_amount` decimal(10,2) NOT NULL,
  `paid` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `shipping` decimal(10,2) DEFAULT NULL,
  `taxes` text DEFAULT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `remaining_amount` int(11) DEFAULT NULL,
  `gst_option` varchar(255) NOT NULL DEFAULT 'without_gst',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `custom_invoice`
--

INSERT INTO `custom_invoice` (`id`, `branch_id`, `invoice_number`, `vendor_id`, `customer_id`, `products`, `total_amount`, `paid`, `discount`, `shipping`, `taxes`, `grand_total`, `remaining_amount`, `gst_option`, `status`, `isDeleted`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'INV-40817319', 5, NULL, '\"[{\\\"product_id\\\":1,\\\"price\\\":350,\\\"quantity\\\":1,\\\"total\\\":350}]\"', 350.00, 450.00, 0.00, 100.00, '[]', 450.00, 0, 'without_gst', 'pending', 1, 1, '2026-04-03 15:48:08', '2026-05-19 18:31:22'),
(2, 1, 'INV-88445847', NULL, 4, '\"[{\\\"product_id\\\":1,\\\"price\\\":350,\\\"quantity\\\":1,\\\"total\\\":350}]\"', 350.00, 315.00, 10.00, 0.00, '[]', 315.00, 0, 'without_gst', 'pending', 1, 1, '2026-04-03 15:48:17', '2026-05-01 18:22:39'),
(3, 1, 'INV-61030411', 7, NULL, '\"[{\\\"product_id\\\":1,\\\"price\\\":350,\\\"quantity\\\":1,\\\"total\\\":350},{\\\"product_id\\\":3,\\\"price\\\":2500,\\\"quantity\\\":2,\\\"total\\\":5000},{\\\"product_id\\\":2,\\\"price\\\":1200,\\\"quantity\\\":1,\\\"total\\\":1200},{\\\"product_id\\\":4,\\\"price\\\":4500,\\\"quantity\\\":1,\\\"total\\\":4500},{\\\"product_id\\\":5,\\\"price\\\":6000,\\\"quantity\\\":1,\\\"total\\\":6000},{\\\"product_id\\\":18,\\\"price\\\":900,\\\"quantity\\\":1,\\\"total\\\":900}]\"', 17950.00, 450.00, 0.00, 100.00, '\"[]\"', 18050.00, 18050, 'without_gst', 'pending', 0, 1, '2026-04-03 15:48:24', '2026-05-26 12:51:11'),
(4, 1, '222', 7, NULL, '\"[{\\\"product_id\\\":3,\\\"price\\\":2500,\\\"quantity\\\":1,\\\"total\\\":2500}]\"', 2500.00, 100.00, 0.00, 0.00, '[]', 2500.00, 2400, 'without_gst', 'partially', 0, 1, '2026-05-04 16:05:10', '2026-05-04 16:05:10'),
(5, 2, '01', NULL, 15, '\"[{\\\"product_id\\\":23,\\\"price\\\":1000,\\\"quantity\\\":1,\\\"total\\\":1000}]\"', 1000.00, 100.00, 0.00, 0.00, '[]', 1000.00, 900, 'without_gst', 'partially', 0, 1, '2026-05-04 16:23:04', '2026-05-04 16:23:04');

-- --------------------------------------------------------

--
-- Table structure for table `custom_invoice_item`
--

CREATE TABLE `custom_invoice_item` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` text DEFAULT NULL,
  `product_gst_details` text DEFAULT NULL,
  `product_gst_total` varchar(255) DEFAULT NULL,
  `invoice_status` varchar(255) DEFAULT NULL,
  `purchase_status` varchar(255) DEFAULT NULL,
  `amount_total` decimal(10,2) NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_id` text DEFAULT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'unpaid',
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `branch_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `custom_invoice_item`
--

INSERT INTO `custom_invoice_item` (`id`, `item`, `quantity`, `price`, `product_gst_details`, `product_gst_total`, `invoice_status`, `purchase_status`, `amount_total`, `vendor_id`, `customer_id`, `invoice_id`, `payment_status`, `isDeleted`, `branch_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '1', 1, '350', '[]', '0', 'pending', 'completed', 350.00, 5, NULL, '1', 'completed', 1, 1, 1, '2026-04-03 15:48:08', '2026-05-19 18:31:22'),
(2, '1', 1, '350', '[]', '0', 'pending', 'completed', 350.00, NULL, 4, '2', 'completed', 1, 1, 1, '2026-04-03 15:48:17', '2026-05-01 18:22:56'),
(4, '3', 1, '2500', '[]', '0', 'partially', 'partially', 2500.00, 7, NULL, '4', 'partially', 0, 1, 1, '2026-05-04 16:05:10', '2026-05-04 16:05:10'),
(5, '23', 1, '1000', '[]', '0', 'partially', 'partially', 1000.00, NULL, 15, '5', 'partially', 0, 2, 1, '2026-05-04 16:23:04', '2026-05-04 16:23:04'),
(6, '1', 1, '350', '\"[]\"', '0', NULL, 'pending', 350.00, 7, NULL, '3', 'pending', 0, NULL, NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(7, '3', 2, '2500', '\"[]\"', '0', NULL, 'pending', 5000.00, 7, NULL, '3', 'pending', 0, NULL, NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(8, '2', 1, '1200', '\"[]\"', '0', NULL, 'pending', 1200.00, 7, NULL, '3', 'pending', 0, NULL, NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(9, '4', 1, '4500', '\"[]\"', '0', NULL, 'pending', 4500.00, 7, NULL, '3', 'pending', 0, NULL, NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(10, '5', 1, '6000', '\"[]\"', '0', NULL, 'pending', 6000.00, 7, NULL, '3', 'pending', 0, NULL, NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(11, '18', 1, '900', '\"[]\"', '0', NULL, 'pending', 900.00, 7, NULL, '3', 'pending', 0, NULL, NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11');

-- --------------------------------------------------------

--
-- Table structure for table `debit_notes_type`
--

CREATE TABLE `debit_notes_type` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `transaction_type` varchar(255) DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_gst_details` varchar(255) DEFAULT NULL,
  `create_note_id` varchar(255) NOT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `grand_total` int(11) NOT NULL,
  `remaning_amount` varchar(255) NOT NULL,
  `total_paid` varchar(255) NOT NULL,
  `settlement_amount` varchar(255) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `total` int(11) NOT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `debit_notes_type`
--

INSERT INTO `debit_notes_type` (`id`, `user_id`, `branch_id`, `transaction_type`, `order_id`, `purchase_id`, `product_gst_details`, `create_note_id`, `invoice_number`, `grand_total`, `remaning_amount`, `total_paid`, `settlement_amount`, `reason`, `total`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, '1', 1, 'receipt', 6, NULL, NULL, '1', NULL, 2600, '2600', '0', '100', 'Adjust Amount', 2500, 0, '2026-04-16 10:03:04', '2026-04-16 10:03:04'),
(2, '1', 1, 'payment', NULL, 7, NULL, '2', '5345', 1200, '1200', '0', '500', 'gyj', 700, 0, '2026-05-01 11:04:38', '2026-05-01 11:04:38');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_value` decimal(10,2) NOT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sr_no` bigint(20) DEFAULT NULL,
  `payment_mode` varchar(255) DEFAULT NULL,
  `expense_name` varchar(255) NOT NULL,
  `expense_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `expense_type_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `sr_no`, `payment_mode`, `expense_name`, `expense_date`, `amount`, `description`, `branch_id`, `isDeleted`, `expense_type_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Cash', 'Chair', '2026-04-03', 1000.00, NULL, 1, 0, 1, 1, '2026-04-03 15:48:50', '2026-05-01 10:13:13'),
(2, 2, 'Cash', 'Door', '2026-04-03', 1000.00, NULL, 1, 1, 1, 1, '2026-04-03 15:48:58', '2026-05-04 11:06:06'),
(3, 3, 'Cash', 'Car Polishing', '2026-04-03', 1000.00, NULL, 1, 0, 1, 1, '2026-04-03 15:49:01', '2026-05-01 10:13:10'),
(4, 4, 'Cash', 'Meals', '2026-04-30', 150.00, 'dsfs', 1, 0, 1, 1, '2026-05-01 10:11:20', '2026-05-01 10:11:20'),
(5, 5, 'Bank', 'afsd', '2026-04-28', 50.00, 'gadgg fg td', 1, 0, 1, 1, '2026-05-01 10:11:41', '2026-05-01 10:50:42'),
(6, 6, 'Bank', 'Tea Break', '2026-04-27', 250.00, 'ga', 1, 0, 3, 1, '2026-05-01 10:12:05', '2026-05-01 10:50:11'),
(7, 1, 'Cash', 'Salary Expense', '2026-05-04', 1000.00, NULL, 2, 0, 4, 1, '2026-05-04 17:45:06', '2026-05-04 17:45:06');

-- --------------------------------------------------------

--
-- Table structure for table `expense_types`
--

CREATE TABLE `expense_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_types`
--

INSERT INTO `expense_types` (`id`, `branch_id`, `type`, `created_by`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, 'Marketing and advertising  Expense', 1, 0, '2026-04-03 15:48:36', '2026-04-03 15:48:36'),
(2, 1, 'Repair/Maintenance', 1, 0, '2026-04-03 15:48:39', '2026-04-03 15:48:39'),
(3, 1, 'TEA', 1, 0, '2026-04-06 17:05:10', '2026-04-06 17:05:10'),
(4, 2, 'salary expense', 1, 0, '2026-05-04 17:44:45', '2026-05-04 17:44:45');

-- --------------------------------------------------------

--
-- Table structure for table `facebook_app_configurations`
--

CREATE TABLE `facebook_app_configurations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `facebook_app_id` varchar(255) DEFAULT NULL,
  `facebook_app_secret` text DEFAULT NULL,
  `phone_number_id` varchar(255) DEFAULT NULL,
  `whatsapp_business_account_id` varchar(255) DEFAULT NULL,
  `access_token` text DEFAULT NULL,
  `webhook_url` text DEFAULT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facebook_app_configurations`
--

INSERT INTO `facebook_app_configurations` (`id`, `branch_id`, `facebook_app_id`, `facebook_app_secret`, `phone_number_id`, `whatsapp_business_account_id`, `access_token`, `webhook_url`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, '757332923380984', '3fd98ac3a4e9e27710398923669df0be', '848317898376074', '4135992726716468', 'EAAKwykZCf9PgBQTweZBtbBIPdblynIxhTApn4ui8d3d74Vc1SvaJckZCVOFZAZC3zJ6PeUQZB82QRYMw7ZAam1wpUFl7cxt4zx3sgrKanKOIRgaZADokfZAWh1EhXeZC3qfLSyZB1CRZB6Yx09gfMJHOSZCNRWo5zqPdJ7R0FKdydZBYXCLzH2UTYcGjYcZAnxLvVLMvQZDZD', 'http://127.0.0.1:8000/setting/facebook-app-configuration', 0, '2025-12-23 09:13:19', '2025-12-25 14:54:42'),
(2, 28, '717461941309133', '55322f3de7d22e7bb3158614955a0c8c', '840826929103118', '25611847155119201', 'EAAKMhyAmzs0BQeEUPDECoCam97i16YJmUsiWXWp9DTEYHnOZBxBvvEdAQyXdbDLoZCZCR286udmPTUFvtuPC9cn7h2uVCnaaS1yWufZBPLZBDgMKMeLRVmGPuKnJZAraeA1U9j5TzDfaNwlUunKdnt9USZC33mdRZCypgEFrCtPn6rw2KoAz2QauPRnV72TXGPmmTQZDZD', 'https://33307b1443b7.ngrok-free.app/api/whatsapp/webhook/verify', 0, '2025-12-23 12:19:17', '2025-12-23 13:05:33');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `follow_ups`
--

CREATE TABLE `follow_ups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `purpose` varchar(255) NOT NULL,
  `comment` text DEFAULT NULL,
  `priority` enum('Low','Medium','High') NOT NULL,
  `status` enum('Pending','Rescheduled','Completed','Cancelled') NOT NULL,
  `follow_up_datetime` datetime NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `follow_ups`
--

INSERT INTO `follow_ups` (`id`, `branch_id`, `customer_id`, `assigned_to`, `purpose`, `comment`, `priority`, `status`, `follow_up_datetime`, `isDeleted`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 18, 19, 'Demo', 'comments dmmyy', 'Medium', 'Rescheduled', '2026-05-30 18:22:00', 0, 1, '2026-05-25 18:22:56', '2026-05-26 18:38:00'),
(2, 1, 3, 19, 'Marketing', 'I want to marketing of my Digital Product - HRM', 'High', 'Completed', '2026-05-26 13:55:00', 0, 1, '2026-05-26 13:56:22', '2026-05-26 15:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `labour_items`
--

CREATE TABLE `labour_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `labour_items`
--

INSERT INTO `labour_items` (`id`, `item_name`, `price`, `created_by`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 'Engine Service', 1500.00, 1, 0, '2026-04-03 12:23:14', '2026-04-03 12:23:14'),
(2, 'Brake Pad Replacement', 800.00, 1, 0, '2026-04-03 12:23:30', '2026-04-03 12:23:30'),
(3, 'Suspension Repair', 1200.00, 1, 0, '2026-04-03 12:23:49', '2026-04-03 12:23:49'),
(4, 'repair', 100.00, 2, 0, '2026-04-27 17:34:19', '2026-04-27 17:34:19'),
(5, 'General Service', 250.00, 2, 0, '2026-04-27 18:05:21', '2026-04-27 18:05:31');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `address` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `sic_code` varchar(255) DEFAULT NULL,
  `lead_source` varchar(255) NOT NULL,
  `lead_status` varchar(255) NOT NULL DEFAULT 'New',
  `comment` text DEFAULT NULL,
  `converted_customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `branch_id`, `assigned_to`, `created_by`, `updated_by`, `name`, `email`, `phone`, `whatsapp`, `address`, `image`, `company_name`, `sic_code`, `lead_source`, `lead_status`, `comment`, `converted_customer_id`, `isDeleted`, `created_at`, `updated_at`) VALUES
(3, 1, 19, 1, 1, 'Garrett Schwartz', 'tejasfablead12@gmail.com', '7896452321', NULL, 'Ea rerum dolor eaque', NULL, 'ADITYA INFOTECH', NULL, 'Ut in doloremque hic', 'Working', 'Quam culpa quis fug', NULL, 0, '2026-05-26 18:53:42', '2026-05-26 18:54:03'),
(4, 1, 19, 1, 1, 'testtt', 'tejasfaeeblead@gmail.com', '9875641200', NULL, 'surat', NULL, 'ADITYA INFOTECH', NULL, 'Lead Source', 'Closed Won', NULL, 21, 1, '2026-05-26 18:55:00', '2026-05-26 18:55:29');

-- --------------------------------------------------------

--
-- Table structure for table `lead_status_histories`
--

CREATE TABLE `lead_status_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `comment` text DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_status_histories`
--

INSERT INTO `lead_status_histories` (`id`, `lead_id`, `branch_id`, `status`, `comment`, `updated_by`, `created_at`, `updated_at`) VALUES
(5, 3, 1, 'Working', 'Quam culpa quis fug', 1, '2026-05-26 18:53:42', '2026-05-26 18:53:42'),
(6, 4, 1, 'Ready to Close', NULL, 1, '2026-05-26 18:55:00', '2026-05-26 18:55:00');

-- --------------------------------------------------------

--
-- Table structure for table `log_attendance`
--

CREATE TABLE `log_attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `check_date` varchar(255) NOT NULL,
  `check_in` varchar(255) DEFAULT NULL,
  `checkout_out` varchar(255) DEFAULT NULL,
  `branch_id` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `log_attendance`
--

INSERT INTO `log_attendance` (`id`, `user_id`, `check_date`, `check_in`, `checkout_out`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, '12', '2026-04-15', '19:05:14', NULL, '2', '2026-04-15 19:05:14', '2026-04-15 19:05:14'),
(2, '19', '2026-05-26', '14:34:26', '15:47:26', '1', '2026-05-26 14:34:26', '2026-05-26 15:47:26');

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `meeting_title` varchar(255) DEFAULT NULL,
  `meeting_type` varchar(255) DEFAULT NULL,
  `agenda` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `scheduled_on` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meetings`
--

INSERT INTO `meetings` (`id`, `branch_id`, `customer_id`, `assigned_to`, `meeting_title`, `meeting_type`, `agenda`, `address`, `scheduled_on`, `status`, `isDeleted`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 18, 19, 'Demo Meeting', 'Meeting Type', 'test', 'Surat', '2026-05-28 18:24:00', 'Scheduled', 0, 1, '2026-05-25 18:24:21', '2026-05-26 15:50:19'),
(2, 1, 6, 19, 'Demo Meeting', 'Meeting Type', 'test', 'Surat', '2026-05-26 15:51:00', 'Scheduled', 0, 1, '2026-05-26 15:51:35', '2026-05-26 15:51:35'),
(3, 1, 4, 1, 'i', 'iiu', 'gggggggg hgggggjjjjjjjjjjjv   vvhjvjvj  vjjv jjyjvjh j jgjvgjg jgj gjvkj g kjjv,j', NULL, '2026-05-26 15:22:00', 'Completed', 0, 1, '2026-05-26 18:39:25', '2026-05-26 18:39:25');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_reminder_logs`
--

CREATE TABLE `meeting_reminder_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reminder_at` datetime NOT NULL,
  `queued_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `queue_status` varchar(255) NOT NULL DEFAULT 'pending',
  `email_status` varchar(255) NOT NULL DEFAULT 'pending',
  `whatsapp_status` varchar(255) NOT NULL DEFAULT 'pending',
  `email_sent_at` datetime DEFAULT NULL,
  `whatsapp_sent_at` datetime DEFAULT NULL,
  `email_recipient` varchar(255) DEFAULT NULL,
  `whatsapp_recipient` varchar(255) DEFAULT NULL,
  `twilio_message_sid` varchar(255) DEFAULT NULL,
  `email_error` text DEFAULT NULL,
  `whatsapp_error` text DEFAULT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2019_08_19_000000_create_failed_jobs_table', 1),
(9, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(10, '2025_04_01_095322_create_user_details_table', 1),
(11, '2025_04_01_101427_create_taxes_table', 1),
(12, '2025_04_01_103048_create_products_table', 1),
(13, '2025_04_01_103511_create_discounts_table', 1),
(14, '2025_04_01_103920_create_orders_table', 1),
(15, '2025_04_01_104026_create_order_items_table', 1),
(16, '2025_04_01_104142_create_purchases_table', 1),
(17, '2025_04_02_061710_create_categories_table', 1),
(18, '2025_04_02_061858_create_brands_table', 1),
(19, '2025_04_03_072609_create_settings_table', 1),
(20, '2025_04_04_064629_create_purchase_invoice_table', 2),
(21, '2025_04_04_064630_create_purchase_invoice_table', 3),
(22, '2025_05_12_124653_create_expenses_table', 4),
(23, '2025_04_01_103921_create_orders_table', 5),
(24, '2025_04_01_103922_create_orders_table', 6),
(25, '2025_04_01_103923_create_orders_table', 7),
(26, '2025_04_01_104027_create_order_items_table', 8),
(27, '2025_04_01_103924_create_orders_table', 9),
(28, '2025_04_01_104028_create_order_items_table', 9),
(29, '2025_08_19_092634_add_gst_number_and_pan_number_to_users_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `module` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `module`, `created_at`, `updated_at`) VALUES
(1, 'Products', NULL, NULL),
(2, 'Sales and Orders', NULL, NULL),
(3, 'Purchases', NULL, NULL),
(4, 'Invoices', NULL, NULL),
(5, 'Expenses', NULL, NULL),
(9, 'Customers', NULL, NULL),
(10, 'Vendors', NULL, NULL),
(26, 'Attendance', '2025-11-04 10:55:19', '2025-11-04 10:55:19'),
(27, 'Transaction', NULL, NULL),
(30, 'Follow Ups', NULL, NULL),
(31, 'Meetings', NULL, NULL),
(32, 'Manage Leads', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `is_sound` int(11) NOT NULL DEFAULT 0,
  `branch_id` int(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `is_read`, `is_sound`, `branch_id`, `created_at`, `updated_at`) VALUES
(2, 3, 'customer', 'New Customer Created', 'VIKAS KANHAIYALAL DESAI has been added successfully.', '/customer-view/3', 1, 0, 1, '2026-04-03 12:19:27', '2026-05-25 18:27:10'),
(3, 4, 'customer', 'New Customer Created', 'BABU SINGH has been added successfully.', '/customer-view/4', 1, 0, 1, '2026-04-03 12:20:20', '2026-05-25 18:27:07'),
(8, 5, 'vendor', 'New Vendor Created', 'SANDEEP KUMAR JAIN has been added as a vendor successfully.', '/vendor-view/5', 1, 0, 1, '2026-04-03 15:46:47', '2026-05-25 18:27:12'),
(9, 6, 'customer', 'New Customer Created', 'SHITALBEN JIGNESHBHAI DESAI has been added successfully.', '/customer-view/6', 1, 0, 1, '2026-04-03 15:47:05', '2026-05-25 18:27:05'),
(10, 7, 'vendor', 'New Vendor Created', 'BABU SINGH has been added as a vendor successfully.', '/vendor-view/7', 1, 0, 1, '2026-04-03 15:47:25', '2026-05-25 18:26:58'),
(11, 10, 'staff', 'New Staff Member Created', 'Default Staff has been added as staff successfully.', '/staff-view/10', 1, 0, 1, '2026-04-03 15:49:52', '2026-05-25 18:26:56'),
(61, 12, 'staff', 'New Staff Member Created', 'Raj Singh has been added as staff successfully.', '/staff-view/12', 0, 0, 2, '2026-04-15 19:04:46', '2026-04-15 19:04:46'),
(62, 12, 'login', 'Staff Login Successful', 'Staff Raj Singh has successfully logged in at 07:05 PM on 15 Apr 2026', '/profile', 0, 0, 2, '2026-04-15 19:05:14', '2026-04-15 19:05:14'),
(93, 13, 'vendor', 'New Vendor Created', 'Default Vendorr has been added as a vendor successfully.', '/vendor-view/13', 1, 0, 1, '2026-04-24 15:30:13', '2026-05-25 18:27:02'),
(115, 14, 'vendor', 'New Vendor Created', 'axay has been added as a vendor successfully.', '/vendor-view/14', 0, 0, 2, '2026-04-27 17:20:30', '2026-04-27 17:20:30'),
(117, 15, 'customer', 'New Customer Created', 'Harsh Patel has been added successfully.', '/customer-view/15', 0, 0, 2, '2026-04-27 17:31:40', '2026-04-27 17:31:40'),
(133, 16, 'vendor', 'New Vendor Created', 'test has been added as a vendor successfully.', '/vendor-view/16', 1, 0, 1, '2026-05-01 12:06:33', '2026-05-25 18:26:53'),
(144, 17, 'vendor', 'New Vendor Created', 'vijay has been added as a vendor successfully.', '/vendor-view/17', 0, 0, 2, '2026-05-04 17:26:24', '2026-05-04 17:26:24'),
(168, 18, 'customer', 'New Customer Created', 'RAMKISHORE GANPATRAM BISHNOI has been added successfully.', '/customer-view/18', 1, 0, 1, '2026-05-06 18:09:53', '2026-05-25 18:26:49'),
(224, 19, 'staff', 'New Staff Member Created', 'Tejas Patel has been added as staff successfully.', '/staff-view/19', 0, 0, 1, '2026-05-26 09:49:10', '2026-05-26 09:49:10'),
(234, 19, 'login', 'Staff Login Successful', 'Staff Tejas Patel has successfully logged in at 02:34 PM on 26 May 2026', '/profile', 0, 0, 1, '2026-05-26 14:34:26', '2026-05-26 14:34:26'),
(236, 19, 'login', 'Staff Login Successful', 'Staff Tejas Patel has successfully logged in at 03:49 PM on 26 May 2026', '/profile', 1, 0, 1, '2026-05-26 15:49:13', '2026-05-26 17:23:26'),
(240, 20, 'vendor', 'New Vendor Created', 'Sneh Chaudhary has been added as a vendor successfully.', '/vendor-view/20', 0, 0, 1, '2026-05-26 18:08:20', '2026-05-26 18:08:20'),
(241, 1, 'purchase_order', 'New Purchase Order Created', 'Dear Sneh Chaudhary, a new purchase order #INV-29190670 has been created for you. Total amount: 90.09', '/print-purchase/14', 0, 0, 1, '2026-05-26 18:09:19', '2026-05-26 18:09:19'),
(242, 1, 'meeting', 'New Meeting Created', 'Meeting \"i\" has been created successfully for BABU SINGH on 26-05-2026 03:22 PM.', '/meeting-view/3', 0, 0, 1, '2026-05-26 18:39:25', '2026-05-26 18:39:25'),
(243, 19, 'login', 'Staff Login Successful', 'Staff Tejas Patel has successfully logged in at 06:42 PM on 26 May 2026', '/profile', 0, 0, 1, '2026-05-26 18:42:52', '2026-05-26 18:42:52'),
(244, 1, 'login', 'Admin Login Successful', 'Admin Admin has successfully logged in at 07:00 PM on 26 May 2026', '/profile', 0, 0, 1, '2026-05-26 19:00:50', '2026-05-26 19:00:50');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('00269b057a8c36709f0f80b7a1d5d77c241b33fa73d01f3651e228cda88e0c1f992ceba112cf072a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 17:38:49', '2026-03-24 17:38:49', '2027-03-24 17:38:49'),
('00475efed1c54426762b52cf089ea26105f2d1f50130b66dc8365036f13a8ea33600691b345f924b', 288, 1, 'LaravelPassportToken', '[]', 1, '2025-11-29 14:29:13', '2025-12-01 11:25:07', '2026-11-29 14:29:13'),
('007cf277ef2262478c32107e029ca95b0a33b165f62370f9b86cd24c14efcb2b822d5f12581fedfd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-25 04:26:56', '2025-08-25 04:26:56', '2026-08-25 04:26:56'),
('00804edd5567889adb2ae253a299008a30bcf459de5ab145b75c77a0787f986e90427440d698921e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-16 16:21:28', '2025-05-16 16:21:28', '2026-05-16 09:21:28'),
('00a0ebf27d7d41ae5c0daa2a6e1491d644aeab23e099e8800f3937751c0fe7626eeffedb1d29a409', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 09:41:29', '2025-09-17 09:41:29', '2026-09-17 09:41:29'),
('00c56b46c45424b4ccad4d236c11bb4078a85daa7e90f79e32cac48e4d5e7e0bba7333f93128c91c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 10:47:29', '2026-03-02 10:47:29', '2027-03-02 10:47:29'),
('00d97b498625df6cca5f77e22c89caad1b4c3b224ce1ec80e68539db702d6b10849d301566a81d3f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 12:51:32', '2025-11-04 12:51:32', '2026-11-04 12:51:32'),
('00ef1556f5e3dcbfd0155723c15fed0e55cfad2dd1753218eeab7d9aafee1b9620e174cfa6505f8b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-13 15:04:28', '2025-06-13 15:04:28', '2026-06-13 08:04:28'),
('01261721c5d6307effd4992aef761ccfd1bd259220dc639b74294a7bb704c631bed750c8f4a1dfb0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 11:46:03', '2026-03-30 11:46:03', '2027-03-30 11:46:03'),
('013d0f909539bd301ce012a39d5717c25f1e8685f6dd854b6ecdc19a4c4ed2ad5e037d1a4bb1f58d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-27 11:51:00', '2026-04-27 11:51:00', '2027-04-27 11:51:00'),
('016d70f8684fe9df2400fe67af113d4177d46e6476fe6da318c597f8edbd4dafeab46d55e9978bd3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:39:18', '2025-09-01 09:39:18', '2026-09-01 09:39:18'),
('01715b26339975d7c95cf9f1dd798af10ef27f3d0e1bdb13e82f856d96db98c73fa4f9d1b36f6c8a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 07:41:59', '2025-09-18 07:41:59', '2026-09-18 07:41:59'),
('019aceec2eddba8bc49b859a55ae2aa0952a14a4c772f90c6459a4518c5611ce9601ccd559ed6938', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-05 11:07:44', '2025-06-05 11:07:44', '2026-06-05 04:07:44'),
('01b8726c56eb71ea40a6e03642d44db1c45e2bdbde5916fd1b8164ef1ff1d9b55172cd1e83f5d8f5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-08 11:37:39', '2025-11-08 11:37:39', '2026-11-08 11:37:39'),
('01d996edb60849c7cb5ff364743be9f44831054c2c7960e2ab3cc6ab929ec2115f7afee3baf21c52', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 04:42:08', '2025-09-25 04:42:08', '2026-09-25 04:42:08'),
('020c091d8a48aa2383767d02c1edc5c073b7fdb28863fe819b219f5abd9ede78c2cfbf60c89d238c', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 14:46:11', '2025-11-11 14:46:11', '2026-11-11 14:46:11'),
('021411eaaeb623202f0eb19a7d3cd6d645910d40cfb83989edd228a7cc3aac177eafc1bf9abdea9f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-15 12:01:53', '2025-12-15 12:01:53', '2026-12-15 12:01:53'),
('02485e7acd7ec255df7ab22e69aea0dcfb11a98a883ad1957c9317533b7c7a2dcf59796bdb3ab9e6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-19 17:13:27', '2026-03-19 17:13:27', '2027-03-19 17:13:27'),
('025cd579345864785961c4c1ff9bbedeb2d90f3268a92cc4881e702a37dbf76d77216c5632633206', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-10 13:10:41', '2025-09-10 13:10:41', '2026-09-10 13:10:41'),
('02aa4009ad8fc9032ac2dcd16df4c4c548d0fc6eff0ae9c897b40f374ce3c2a73bc3101df52dcc12', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:35:33', '2025-09-01 09:35:33', '2026-09-01 09:35:33'),
('02b6123f6c02674697a6d307a8a7089cf7580b16c55531e829a00c0888808190915f1529ed6282de', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-12 17:29:23', '2026-01-12 17:29:23', '2027-01-12 17:29:23'),
('03369f2c38d694445e21d160a0a1c8c96580f3caff76783e94183d504d18bc0887997254f04074aa', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:16:10', '2025-11-03 12:16:10', '2026-11-03 12:16:10'),
('0337840c4dc6072925f9437c0db1ddf8e89405155a9fd400312a471d84fbdd6cdf0c2f1a6c67b4b0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 11:00:00', '2025-09-05 11:00:00', '2026-09-05 11:00:00'),
('0355985e0aca5bcdd133799e5a530001a724954898cd87ebc2d6937b9ffe4b383425ff0de54e884d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 10:09:30', '2025-09-08 10:09:30', '2026-09-08 10:09:30'),
('037088e0595f0288338907c1f8d7fbd01d2cf816b0a8857946c5a6ffed1decb13580196f1866bc06', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-11 10:15:35', '2026-02-11 10:15:35', '2027-02-11 10:15:35'),
('037af2f37f8ea382cb665833ee7f803d4bb7b302646e901564bd76c2af442f6a4c56f3ddf119eeb4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-08 15:58:40', '2026-01-08 15:58:40', '2027-01-08 15:58:40'),
('0399ff4cfa7ae3e62ec596d4695c9a24e25cd2a770c550c7026977cd9dda19e60e84fd07363f78dc', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:06:36', '2025-11-13 12:06:36', '2026-11-13 12:06:36'),
('03b09261f07cc2454f631cc2d72b19e74d8d723dea211e1491a78d3aa9ca7b0c211376bb82144563', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-28 10:04:08', '2026-01-28 10:04:09', '2027-01-28 10:04:08'),
('03e97e63647cc4b02bfb54d908d154ff05a0c5073b8a577b4483ce0136d6b0e55a38711836d423f4', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:23:41', '2025-11-12 13:23:41', '2026-11-12 13:23:41'),
('03f070ce59349db36b0f46d0e525785255b4fc55627c029119989d02482e36d90a3301e3eb44263b', 151, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 12:10:00', '2025-09-18 12:10:00', '2026-09-18 12:10:00'),
('04201a0646e6163f567bede10b1e1503d0c0b6350f4219fe2cf2dc031089c9ccaf06d48b60c3e4be', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-05 10:11:36', '2026-02-05 10:11:36', '2027-02-05 10:11:36'),
('0484b68f7e5216fc7509f2cc258945dbbfbac473e692c6006c25008bec24b8a4644475435698df62', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:58:43', '2025-11-06 10:58:43', '2026-11-06 10:58:43'),
('04a2255a5912c4af45204f35cbc6dd58207f11b217b9fb6801093173e53671d79b7bf9a7214fd9a2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 17:17:32', '2025-11-20 17:17:32', '2026-11-20 17:17:32'),
('04af1411f0aae2e127f3376239ab227d059eaae61b77de81d3c33f3b87eb7823537b86f67580ee45', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-31 14:43:19', '2025-12-31 14:43:19', '2026-12-31 14:43:19'),
('050509bd80fb0790724cd3041a3098ca13d5628094a98c12e11b1301171db12023afdbaa636054f2', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 06:56:16', '2025-09-23 06:56:16', '2026-09-23 06:56:16'),
('05083f3af0aece41e28c1cc0c0d8c24e4099c6651c9b3edef8dc1e784646bacf38218f12aa1c914e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-01 11:21:11', '2025-10-01 11:21:11', '2026-10-01 11:21:11'),
('059b5aff49b206b085be0cccc5e987959eafa5fd9ee55b4bc3e2741e99ecd55d359fada054a9598e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-19 17:20:07', '2026-03-19 17:20:07', '2027-03-19 17:20:07'),
('059d3695d9e7fa4dd4cfd5a602c050cf514a235d2b7efc1f6a1413083c8fcf0e334db4775f7cb904', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 12:22:55', '2025-11-06 12:22:55', '2026-11-06 12:22:55'),
('05a5ef2cc4eac992ccb8ecbe5cda65eb7f6c50059531550c9df30585213a2de727b80dabcc44d1b8', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:00:25', '2025-11-13 11:00:25', '2026-11-13 11:00:25'),
('05c82b909d981d018c3252507e019e96904b52248253273bffedaae1015128b0c84e3a42b2432393', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:50:10', '2025-11-11 13:50:10', '2026-11-11 13:50:10'),
('05ca0d640b487ef153416406a86bdfae2597a8a227dc87631cdf95efe44af70724683a76948805d0', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 13:48:50', '2025-12-01 13:48:50', '2026-12-01 13:48:50'),
('05d561fffa1d51549c44d4c08b55fbd7e54e8f6971b5da6437ca1a23cbfb60173b763c816c02c242', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 11:25:26', '2026-03-02 11:25:26', '2027-03-02 11:25:26'),
('05d96f4c2711bd40736c3b753401c9c81d78bb16df2cb2572ff6403abace3ee7e461e6c118c620d4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-16 17:19:48', '2025-05-16 17:19:48', '2026-05-16 10:19:48'),
('05e2be1d738cce7ec783ffbb660a7c2f56b6dc348b4c9e65b711d0a7b87f01c136c93072382aa5d5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 12:32:31', '2026-03-24 12:32:31', '2027-03-24 12:32:31'),
('0601d77f62b027e6144cd4c4436bb4fc4130feadcb1610d1bde6864788537f29ca92265145d96233', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 14:34:30', '2025-10-31 14:34:30', '2026-10-31 14:34:30'),
('0628f0bc71fd7b05d8e50ced22c4b786372e1cb9d45d236fb5c4cd63090d353eae876544c240a510', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 14:28:59', '2026-04-06 14:28:59', '2027-04-06 14:28:59'),
('06376e7f3fe44a56540fa24240a0dfbdfc81a596841dd2d59ac58e66e65b5a924036cfd747e69228', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:37:25', '2025-09-01 09:37:25', '2026-09-01 09:37:25'),
('064f7dad3743180a4c0fc746adf4bb022602eac2e341242321564216433cac65a336e649f39d6114', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-01 12:26:54', '2025-08-01 12:26:54', '2026-08-01 12:26:54'),
('065f8c5c22a70127be0713e1c98f9013902fc85eb344048ab7b33f8e8b7e95d15f63fed0aa451b14', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 11:27:10', '2025-09-02 11:27:10', '2026-09-02 11:27:10'),
('06b16e88ef10a3c0ad2e804935209f4a3c327cb95f16e0f64ee691bf182461147bbd643a5afa9414', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 09:41:21', '2025-11-07 09:41:21', '2026-11-07 09:41:21'),
('06d263e7a0a682f200f0ea9088ccb25cb2fadca80f9d48dc7c8687ed6b65a9d00b09cb8c97ec7e1f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-24 04:42:46', '2025-09-24 04:42:46', '2026-09-24 04:42:46'),
('06ed8823da5868dd5cb1392f8bda9d574dfe61303aa5a57f19502d7c2b2889a0c415510d1c7608f5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-29 04:22:23', '2025-09-29 04:22:23', '2026-09-29 04:22:23'),
('0734a9dea3010d205916601bf43f7a92660eee0dc2f67085c64f6286d4df878fd86cad03e15074e5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-05 17:44:15', '2026-01-05 17:44:15', '2027-01-05 17:44:15'),
('077fada2edda3dea78b2b7eaa42a1bf03955220e1dbf28971b47bb85fd4eec68507bb04cee53f8a7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-26 06:05:52', '2025-09-26 06:05:52', '2026-09-26 06:05:52'),
('07803f1c9425a702ba6517a850bfb9a4ec4591db436ea6bf593ed078ad20079eb81ab5b3edb892ef', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 06:41:43', '2025-09-09 06:41:43', '2026-09-09 06:41:43'),
('0797fd62717fa8a3166a6c3fa17e6f90a39f2a2fbda5abe26dd58939c0c46220875e3538d3787739', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-02 10:04:35', '2025-07-02 10:04:35', '2026-07-02 10:04:35'),
('079c29b78e12f36d6c974e2f85ae95b74cd897cfe88ec8023b9c794e730b6b179462149b9c96b9e4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-16 12:05:41', '2026-02-16 12:05:41', '2027-02-16 12:05:41'),
('07d0cf270008b66be46587d9eb1bb77494c02399087333ef0ab061c9a464860742ed211b5ea7b0a0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 15:03:19', '2025-11-04 15:03:19', '2026-11-04 15:03:19'),
('07d581ecab5a07f5b799305cb73dc852c208e459efaae0774d9adb1f7332cc64ae5f637df46492d3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-15 10:36:45', '2026-04-15 10:36:45', '2027-04-15 10:36:45'),
('07d5a29a7a80b650706691267b08c168a89826f3780f95b3dc61681ccd983a8eeda7a9fac17772d1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 07:09:19', '2025-08-28 07:09:19', '2026-08-28 07:09:19'),
('07e3ee3af2bfcdc03f96b19121d32cebd5f7ce5822faec13830c4a21a0f44599c061822d59b63907', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-01 05:56:56', '2025-08-01 05:56:56', '2026-08-01 05:56:56'),
('080ba1d19dd6109938291a5b8cff16048cd692e5f1839d775d56b27289c436a84c850b1de3a79bfb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-02 04:22:42', '2025-07-02 04:22:42', '2026-07-02 04:22:42'),
('083cdbed6370b1c152d5e3f502f9e231f98354a0c199e76279dc5005c01c4be85ec91255ac734c4c', 179, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:13:28', '2025-11-11 13:13:28', '2026-11-11 13:13:28'),
('0841b8eb3cb4066a435c1000f283d560deec5e8a1509cb1b3972d83162df00fd6cd80b26f02999a1', 175, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 14:38:20', '2025-12-01 14:38:20', '2026-12-01 14:38:20'),
('08440323fb792cfb9c32d480e548d94dc898046d88ac2f7a45faf7283e34107e408ac9a32dfa0515', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 14:57:56', '2025-11-20 14:57:56', '2026-11-20 14:57:56'),
('08c9ae37819ffb2b99767404ade7b7e5adb4e0a44427a3b3abe01569ae2281b73bffe8c50b907675', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 05:58:06', '2025-09-03 05:58:06', '2026-09-03 05:58:06'),
('08e8850b0348affe1c92147d7c3154168f51364d44a444f738bb3e5fa3c215e43d04fea4e674ac98', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-12 16:45:19', '2026-05-12 16:45:19', '2027-05-12 16:45:19'),
('094b174fb21f8464d8686e4d5889d90a538588344aaaf1d4a8dd52434d5f8b4b42589204fc7ffe45', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 13:16:46', '2025-11-13 13:16:46', '2026-11-13 13:16:46'),
('095c180b07c7d71d30fc6bb4cd34d199b29228569486188f8dd2381d6e71abfc0d234954b83a85c9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-28 13:17:19', '2025-11-28 13:17:19', '2026-11-28 13:17:19'),
('096239151f8fcc300bdd02883fa7e073ae8cc8aa1138c776c26710772977161f5d03213c27608ae6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-02 10:03:28', '2026-04-02 10:03:28', '2027-04-02 10:03:28'),
('096944c77697896c6d4000958e69891fcd82aab7074038f8c023b317185321a2df1e8aa7ad722294', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:00:41', '2025-11-12 12:00:41', '2026-11-12 12:00:41'),
('0969945dcf5bc98259725af4bd7df2c9759602982bcd9fa0796777034d3deb23147f6eeb15504c14', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-27 07:46:11', '2025-09-27 07:46:11', '2026-09-27 07:46:11'),
('09712de7075d7b386d913aa3d2a199774804c87d4525de29f36a0d4323790ca0cd88009e82646ed9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 20:48:07', '2025-06-11 20:48:07', '2026-06-11 13:48:07'),
('0994364fc75f8e7aacafb4d67e425bf7015bac6c06cbad9d6021e9fe29becf67bfd0f08d4db7a16c', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:56:43', '2025-11-06 10:56:43', '2026-11-06 10:56:43'),
('09d1ec9f8d7257ecc50b7bfdef263d34674d317ab3181b00d8694578ec046d6dae29b771b8b7382c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-03 18:49:14', '2026-03-03 18:49:14', '2027-03-03 18:49:14'),
('09d623be7254d5673426d23347d6368f7b63e17cd1e229f9b5ac7e5418b81723b7c2fb187b617108', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-30 16:22:49', '2026-01-30 16:22:49', '2027-01-30 16:22:49'),
('09e7b47a23e15228af3b5a0db55d2860ed384d662c04cb9e590bacb670420a3fd7611cba8f108231', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 15:11:54', '2025-11-26 15:11:54', '2026-11-26 15:11:54'),
('0a15a39d2962c667b6c6c3eea62d05688d2bef3a5fc8e85e312cbd894cd13610e2c1c1bbde84b26e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-04 15:45:03', '2026-02-04 15:45:03', '2027-02-04 15:45:03'),
('0a56b43cb5f46fad8d785884b5b9fed1fe2a2662c40554b44f704ddc8e113b9cd4b6d673372c664a', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 10:56:29', '2025-09-19 10:56:29', '2026-09-19 10:56:29'),
('0a5fc4180988e688f2711331a2f89c8c2e3d602c9f668eae7c5db71d123465777026f9c1ad34772d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-12 16:53:23', '2025-06-12 16:53:23', '2026-06-12 09:53:23'),
('0a711ea460ac560cfc42c90b3e37b85e0bcbc3e17daeaf7abfc2fb77de03b4b011180dd8d138d3ed', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-26 08:08:44', '2025-08-26 08:08:45', '2026-08-26 08:08:44'),
('0a9825c09813c01631b74a378747cbcffc4fc86ce3ad4f84b2378b4c6ff230376b46d494f96fdab2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 06:57:18', '2025-09-18 06:57:18', '2026-09-18 06:57:18'),
('0b0f83f39416d1632d743ce3e1724bb32b90a3882208e165a536ee548757c1a704ef9c70f4ce5fe2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:10:56', '2025-10-31 18:10:56', '2026-10-31 18:10:56'),
('0b2c633c73d8a3d3f188e9ef6cd4042667d08d1b1515b115ed8a326b813bab6c85cb0e197d4777da', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 12:36:47', '2025-12-01 12:36:47', '2026-12-01 12:36:47'),
('0b3739cfbe06f2d50dc9ee9329e69bec8e6a8673b441a73d61103cf84e1050fd645f89eb68549d08', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-20 12:35:10', '2025-05-20 12:35:10', '2026-05-20 05:35:10'),
('0b533195cecff5a299a4fc84a1656189ae7c6ae6b879af66e0a9689a0eb9368c9e99eeee3883d761', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-31 13:56:04', '2025-05-31 13:56:04', '2026-05-31 06:56:04'),
('0b800d01ca38eeb9f1016192e67210c324213c7c5274ecc64a102b7113e9953415d13094d1f5a4ae', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 12:52:47', '2025-10-03 12:52:47', '2026-10-03 12:52:47'),
('0b87729cc64c28f1a972f8b8a9086681e46eb493f1c879837c2fc1a3d8c506ef58860d69f2462e3e', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:42:04', '2025-11-05 13:42:04', '2026-11-05 13:42:04'),
('0b9ea082970b955dd49fab09b3bf272fbe262e73ac1b5e617a9ed5005dfeb167286f90d9ef05899f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-22 12:13:58', '2025-08-22 12:13:58', '2026-08-22 12:13:58'),
('0bb118955bd23581d34cc8657c34f7b3032628b228c2599dc980f59ac2ceef55446f375b25e8ffca', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-20 16:20:16', '2026-03-20 16:20:16', '2027-03-20 16:20:16'),
('0bb9d0d406026387db21fe903a29db74efbafcc82d674be3e2a18e283d0fb28dd1065c56752ff352', 346, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 16:58:47', '2026-03-31 16:58:47', '2027-03-31 16:58:47'),
('0c1eeac77344fe1d8764152e0dec2e3acfc78acb09bd5e67c4112ee6dfaf6a445f8f535c03f40b0e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:28:29', '2025-10-31 18:28:29', '2026-10-31 18:28:29'),
('0c394f3a6d125f684574c5aec6934480c316ca3de000a77f41b62043cf4c9a861d6bd92caa7f8590', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-29 12:43:27', '2025-09-29 12:43:28', '2026-09-29 12:43:27'),
('0c3d2de64a10f541fdcd5b12f4b8aa47e492e7464a3154a718e5f170f3baf8c4dbc79d8897051415', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 10:18:59', '2025-11-12 10:18:59', '2026-11-12 10:18:59'),
('0c424fed7cb735420afa58be937b54c3faccefdf6c37e048e68a335d5d8522022b9415f2992ea1a1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 11:21:37', '2026-04-06 11:21:37', '2027-04-06 11:21:37'),
('0c45db2d75d7d8a9722d994387e2d97abb3d3b0ed91a7ace1cfd3dcfdadcf218d4b804abc4a9352e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-31 13:45:49', '2025-05-31 13:45:49', '2026-05-31 06:45:49'),
('0ce0fe6b7f8579f913a3bd02a3e54840c29c3a5bfc08fd533dc3df89032321bd4817a3bffb49f6fd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 15:42:49', '2026-05-25 15:42:49', '2027-05-25 15:42:49'),
('0ce54ccb624da98e42b5302edc6a47c484080a920fcc201cc7e00f4f447fcffd12a0b09c041db2fb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-14 16:04:42', '2026-04-14 16:04:42', '2027-04-14 16:04:42'),
('0d16e9e2628cb0825ec19ffcb8bca2971a7e81fb7297fe596d8a4bbb4020f26d254c1b4ebdf2541a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 10:53:12', '2025-09-18 10:53:12', '2026-09-18 10:53:12'),
('0d548a369fd22071996e48290640d450d35abd70949a567d099bff299ec6d50ebff29f98e7d400a5', 256, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 16:26:35', '2025-11-20 16:26:35', '2026-11-20 16:26:35'),
('0d5b6bf45bc86b992eef662e97817ed787a8b43009ee5eccf2ed9612073dd1b7e773e291351ad1d5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-06 09:42:14', '2026-05-06 09:42:14', '2027-05-06 09:42:14'),
('0d608c17117aef42f8c5ef7c27c791b358f9d6eaef636c5a1c961e10cd17cf478e5c4e6b45d1fbda', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-10 09:29:44', '2025-11-10 09:29:44', '2026-11-10 09:29:44'),
('0d68117802dc84f738fe16335b79fcd5fec5a167c25a291da8899a8477a880e90a50ddd123889366', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-26 17:53:24', '2026-03-26 17:53:24', '2027-03-26 17:53:24'),
('0d81fbc830593def9959eb736fa51904c308a16a8a4464b756aed099049ba7eddcd703abffb4c4fd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 05:38:10', '2025-09-18 05:38:10', '2026-09-18 05:38:10'),
('0d87bbb280d75c3c3b47dfc4c4f40c77c16725f6323bd6ced439397cddaa6095e528920c40798e8b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-25 06:25:18', '2025-08-25 06:25:18', '2026-08-25 06:25:18'),
('0d969d18931a81b3eaa60bc50a043b4142e89a22e080d704ef3c1e48419f92b2d7f399bc9650491b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-21 11:54:08', '2026-05-21 11:54:08', '2027-05-21 11:54:08'),
('0dbdaf3d76c76eff84187ca2d9f6927aacbe8565b8a65a1584bfed6995a2a0ce4671337d734b06d9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-17 15:19:54', '2026-02-17 15:19:54', '2027-02-17 15:19:54'),
('0dd29a00f7578788f19cde7e9d9400f304465a2883d8b0aba32efe0b41d91735a17c8ce8db5392f1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-15 13:15:37', '2025-10-15 13:15:37', '2026-10-15 13:15:37'),
('0dfc8c50e7f34460a347f0e92537fc5b567dde73c70cc05076a58d29b8ce3f68a085ef00a6e7890f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-18 10:19:02', '2026-05-18 10:19:02', '2027-05-18 10:19:02'),
('0e048828597c7337d3f178f6d32bf5408a084909e0a2acdc7dddb80131f8546152ead8d6e7f5f11f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 04:33:04', '2025-09-02 04:33:04', '2026-09-02 04:33:04'),
('0e3877da94452667c90b3c7ccd3d225f542d0ea481ecf39c59576b5857cf60e002d3b0ddc0c14a90', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 05:10:17', '2025-09-05 05:10:17', '2026-09-05 05:10:17'),
('0e4269f2a2f68d97edec8af562d536be954fbdb55d91d36382bc020fa5e60703b59591e8e854ebbc', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-24 11:02:13', '2025-11-24 11:02:13', '2026-11-24 11:02:13'),
('0e571e5423050e6e967bce7029ab274832416080aa43265dfa2fb732eedf3bad5fb3aae332362582', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-27 16:00:27', '2026-04-27 16:00:27', '2027-04-27 16:00:27'),
('0e613e48cd3b70686a16101a6d5798648b1dca82b253a862b2f908f46a661c58ad5060a04a2da811', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:38:39', '2025-10-31 17:38:39', '2026-10-31 17:38:39'),
('0ec94ac57cd8304c869a1f9934bb83e573a860fbf8316893f9a00c28d258fc603203e128e764f09c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 06:22:36', '2025-07-17 06:22:36', '2026-07-17 06:22:36'),
('0f1223bdda0ebc694f9a13772fd2df4050da1737c56061f7806749134d2676a359cf0f312b7162b8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 15:26:17', '2026-03-31 15:26:17', '2027-03-31 15:26:17'),
('0f467f7b2f45e8577398c825e72049397da02c36ef97bf2def4a6b8f84300164a705a213a7fdf88c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 16:27:19', '2026-03-24 16:27:19', '2027-03-24 16:27:19'),
('0f52fd0db82b8471af12fd4af007d610a80d8e03aceb3f03cfa57a4511c4d66fc292bfe35d70782c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 10:26:38', '2025-11-03 10:26:38', '2026-11-03 10:26:38'),
('0f6bb9a09e95a40497a02d4a402b74611fb62f8e820bc163689b362a9a50e6ba1982d2ed189c7f7c', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:23:25', '2025-09-23 05:23:25', '2026-09-23 05:23:25'),
('0f75fa80dcaa756561ca9a1b6e20bcce88324ad58063afc7e609806d964c0e756027ff6b86041dbe', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-21 12:39:36', '2026-05-21 12:39:36', '2027-05-21 12:39:36'),
('0f81b6fc680417a2b139aced14427bcf5c5a43f726c8fc38de38c8caccc0b2246d6e9ccae2dafbcf', 188, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 05:19:49', '2025-09-22 05:19:49', '2026-09-22 05:19:49'),
('0fa572391f4350dae0b38c9ce3d817a1b58cdef9d91debc923a7753a33b1d47d898553ef0c327460', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 13:47:54', '2025-11-03 13:47:54', '2026-11-03 13:47:54'),
('0fbbeb98033d973ef810c28f4bdc3a67567a59b92a5536e1f3dc435e29c2216beacdf74f02e9aa87', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-17 13:55:11', '2025-12-17 13:55:11', '2026-12-17 13:55:11'),
('0fbe046443558fa316e3a3bedb4d04d92eb84d20a98612a9730ea03509ed8aca3f43a69669658e8e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 12:44:04', '2025-08-28 12:44:04', '2026-08-28 12:44:04'),
('0fc3515106af656cc63eb20995377bd6742ee183bac408496cc77704a7fe8093973ff75c18c219dc', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:37:07', '2025-10-31 17:37:07', '2026-10-31 17:37:07'),
('0fcd48d857acb60fea5d96b9b7a929989a8ec15e2a7b5678cf25bbbc122389b9861e33e9f03dc379', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-10 17:30:00', '2026-03-10 17:30:00', '2027-03-10 17:30:00'),
('1017cdce3936c01d26b27497500bab035855d7747fe4ce751c76a8675e9ebf5b6f363354f387079e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-09 11:28:42', '2026-01-09 11:28:42', '2027-01-09 11:28:42'),
('1029f74eecf802d743a2044492fa7f51a8f2a1c671e1539cc2400d0605f13416cc90e3e5d4960021', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-22 18:03:39', '2026-01-22 18:03:39', '2027-01-22 18:03:39'),
('103df9d71ec41d08c84ca0d6273f5cf67af48bc0cfb4d2bfe2e043f15bc0b08c5a4bf56233e47705', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-04 06:39:34', '2025-08-04 06:39:34', '2026-08-04 06:39:34'),
('104f896947ec076f9db89c6f9ec9998440425916db237bd77386f75ab7572d64e3b624ee2d99a6c6', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 16:46:07', '2025-11-13 16:46:12', '2026-11-13 16:46:07'),
('10600c8dcfd6120da164e03b2b2e694203fcace948abeeba2c692fbe20005337851e24689c92ee55', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-13 15:23:17', '2026-01-13 15:23:17', '2027-01-13 15:23:17'),
('106efdb5b7b4c7af174c8ad622468dd906d5dd0e73b6885b7f257d84909f2e3b426913a6b41a9678', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-03 14:29:38', '2025-06-03 14:29:38', '2026-06-03 07:29:38'),
('106fb05fed4c8d3b04cf34a495bff8d70cb603376c4f08e84b72be186951a4c9e132da4554f9b146', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 13:18:41', '2026-05-25 13:18:41', '2027-05-25 13:18:41'),
('108f427e82fe8aa9ff6241fb954f5e52e37439496865332618d0ab18589f778a0c40fb75749c2e54', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-19 14:50:43', '2026-05-19 14:50:43', '2027-05-19 14:50:43'),
('10e8c33c974caf722d3308644c8791569b0e84c91305fbfff3bc0913f65da04ebbf363b5c0843686', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:26:26', '2025-11-05 13:26:26', '2026-11-05 13:26:26'),
('10f2feda7476d3169354d3be04ee6f6153a00673c169600320dc04fc0e01af9061c4577af3dff70f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-13 10:35:51', '2026-03-13 10:35:51', '2027-03-13 10:35:51'),
('1117ce8f6620f6f8a153acf84efb9a204ce0978bfc14b2ed1199efff8dd603f7bbc2ee543d316038', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:36:48', '2025-09-01 09:36:48', '2026-09-01 09:36:48'),
('11531844bc00d9bd12744f0928c55d2423bc256d0af726fca72d521ea27ae3741c38ee5b371331d9', 22, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:57:23', '2025-07-17 05:57:23', '2026-07-17 05:57:23'),
('11a0744503d62944b9b45872c0cba49a8c5d1bbca1f945eb862a5611536034703d1dc136cb356147', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 12:40:46', '2025-09-22 12:40:46', '2026-09-22 12:40:46'),
('11b530ba8209129df6d305f877bdcbe50302124d735b11dfa2a70c33c9a2e6180d3117d9e13043d3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-30 09:38:55', '2026-01-30 09:38:56', '2027-01-30 09:38:55'),
('11b5aedca433df418fdbc2a6b09caab8253df2dd243e8a35ac3de54458a8c0cf8766ba394962d055', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:38:33', '2025-11-11 13:38:33', '2026-11-11 13:38:33'),
('11c221b47960d5947cf617057819a24e5a810cf288902a62749e5108df63ae1ce55599e79d5c96f1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 18:35:00', '2025-06-11 18:35:00', '2026-06-11 11:35:00'),
('11c5382f8a0220e1a41074812bb3a3cdb680ad205d35326edb79dabad89f59c2c85f11bb4a73536c', 196, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 11:07:33', '2025-09-25 11:07:33', '2026-09-25 11:07:33'),
('11cf5954a5c90aec96fd73f9d7c83e2cdf5762152774db516ca36350eb8dea4b18c9cec691c734d9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-15 04:03:07', '2025-05-15 04:03:07', '2026-05-15 09:33:07'),
('11d7e9a7b59c64570236a0051cd771171272c8654df2fdbc86d98df5230988c7d864b19b80f5c3e3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 11:40:55', '2025-11-05 11:40:55', '2026-11-05 11:40:55'),
('120d13e59c619f0e389cc3a1eb9fa98d5204a705d192ef12608addb48ffbbc005a17635239bee44b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 13:42:08', '2025-05-30 13:42:08', '2026-05-30 06:42:08'),
('123aeddf176e3623ab47dca2c2999b2fd5a81015190926142f7b39ae42f37aa52f9f4c4162cc000a', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:55:31', '2025-11-06 10:55:31', '2026-11-06 10:55:31'),
('1256ec987f3e0612befa9afb4d640c3bcf1fee644c292448de6db8a208ab521623b403953e15739f', 208, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:59:21', '2025-11-06 10:59:21', '2026-11-06 10:59:21'),
('127adaee82356e38d60a40cc8d0a5524cee8fca2533792c5f49e61270d46395c9760566fb9c67350', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:14:10', '2025-11-03 12:14:10', '2026-11-03 12:14:10'),
('12b10b6c3bda416e2cf589d20c8a48998cd86918fe8a3626728baaea88dfffb96daf9d87ac5e52c0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-17 22:48:53', '2026-02-17 22:48:53', '2027-02-17 22:48:53'),
('12b1fd1cfbf37feac20893b58e7aca4024b6b8fc139a278be9ad7596b86d4e91d3a65b93a6557166', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 17:04:35', '2025-11-06 17:04:35', '2026-11-06 17:04:35'),
('12bec0b2efc3cff1d03932ee5a89ca7f0da360bbc609213c174ba6bb46e5f64b57707f84d8607191', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-01 09:36:39', '2026-04-01 09:36:39', '2027-04-01 09:36:39'),
('133af7dbb9feb5f13435905737299bd7ace200ff82143b37d7c86bdd3e333e8af494651901d2b32a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-29 13:35:21', '2025-12-29 13:35:21', '2026-12-29 13:35:21'),
('1340653a92439fbabd374e44e605f94e6df6cc8c91d9e6733f4703a73688821b2c4efb8141355cc9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 10:23:19', '2025-09-18 10:23:19', '2026-09-18 10:23:19'),
('136c542a116f01c50c806f1389f7705289f1227589ea02efeef9c6b75a81aa3dd6c85f79d12bf615', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 06:49:14', '2025-09-17 06:49:14', '2026-09-17 06:49:14'),
('136f4da4aeb526ca0ef688f1743d32f5cb45473b891d30d4d4b85c5a2c1ed374c53d990bb7338e30', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-21 08:26:50', '2026-01-21 08:26:50', '2027-01-21 08:26:50'),
('1390e0ec14d09ef65f97c9e48d80ccefc489f209745ec00686e8bb3c115fd4531b3fcb9aedea774a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 05:34:56', '2025-09-02 05:34:56', '2026-09-02 05:34:56'),
('1392485e898f46e57813833cad6a829de4562869deec28cf56473625f16d78e3624b8980ad5eba8a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 11:31:11', '2025-10-30 11:31:11', '2026-10-30 11:31:11'),
('13d12be4f6d974e48dc4dfd5696a464f22488f85120343ce0e52a0434be0e761b2a3e67eaeca5209', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 09:53:57', '2025-12-01 09:53:57', '2026-12-01 09:53:57'),
('13e0dbc8353c25fad404f61873e19a96ec21a225c1a05b684986d81ec7e06f43b54945bdc9d5e195', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 11:35:16', '2026-03-30 11:35:16', '2027-03-30 11:35:16'),
('144a9488671c734bdf26d5f3133f13cdfde208a7865d817b55f3f7caa657eead87979ef50c789850', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:45:44', '2025-11-11 13:45:44', '2026-11-11 13:45:44'),
('1479519d7303e2cd85e0c52edf74ae109f1d6747bfadf35b6c676013e1d0a272e5811096d88031ae', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 10:18:09', '2026-04-06 10:18:09', '2027-04-06 10:18:09'),
('14b2a451a97bcfc02c2af5d371be1ca602d7878a25395d4bbf0f3f16810682fe8a0431ade030d59e', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:44:32', '2025-11-06 10:44:32', '2026-11-06 10:44:32'),
('14d7d5d492ecb4d7c2a6a1cb4c3081ce0ba8e8ad23bb7740c6b9b925827f14557310fe745aa2401e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-13 06:29:32', '2025-09-13 06:29:32', '2026-09-13 06:29:32'),
('14d869bf7f3d5a85e10d9786a4678531e63970fefb15a06e0f3bf650c28a9d443c2dd8054fe8a981', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 18:09:20', '2026-04-06 18:09:20', '2027-04-06 18:09:20'),
('14e10c01c0248cda965d274f92dbf8c7a98d99476880e7ca99d04b9c3349069ecc061e6f19f8b6ca', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 04:46:44', '2025-10-30 04:46:44', '2026-10-30 04:46:44'),
('14fe99d7601a0ce1e37a85b64eea855d5a3a49a582429a55a120a39a80f752f402631543b974fb95', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 07:35:30', '2025-10-03 07:35:30', '2026-10-03 07:35:30'),
('152c4e926f62b463c4bba7d6de43509b50a7b6e81b67dcc2665bd6c97962e21489c6396b799631ce', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-18 16:09:53', '2026-03-18 16:09:53', '2027-03-18 16:09:53'),
('156d7a9d9060c10f8f7438c5c17b3d23015cc733c2771b025f3143fad67a3275fbe0eb745bf0715f', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:28:56', '2025-11-12 12:28:56', '2026-11-12 12:28:56'),
('157bb12c28ba1436e159abbefaebe5c76df19d76e4c3085c324c20c2bfe8fc9bed464f1a7b133c2f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 12:14:57', '2025-12-01 12:14:57', '2026-12-01 12:14:57'),
('15c22b7ec61703678f2282f4deb1db10ed2ce0bd78999479837260b810566edfacbcb80fd0dadf5f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 07:16:55', '2025-09-01 07:16:55', '2026-09-01 07:16:55'),
('15c73513c413456755851c8cc5de056868855800f54ee401fa7608159eb8e3387a6ac7c95a485476', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-25 11:01:18', '2025-05-25 11:01:18', '2026-05-25 04:01:18'),
('15cec6fc79ddb7bbb66a0578b0a807bffeb4871a4ac2acc7671242cc6941b3cac21008969f924b1e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 18:25:12', '2025-11-07 18:25:12', '2026-11-07 18:25:12'),
('15cec89f4f7e205a3e64010d18fb8aedcc2edfb6d90ad4cab187e38612e39f338a3d370b897f7d45', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 04:57:50', '2025-09-03 04:57:50', '2026-09-03 04:57:50'),
('15d447709fa5ef5f2c01a571e3373dcc6e6f31d6f7a107fa9361067469562d018af0976e887bf3fb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 04:15:32', '2025-09-17 04:15:32', '2026-09-17 04:15:32'),
('15f6099974f1932445428259e1e05254bde786e923e758f1b86989c9c406c9c9b7285a28839e6059', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:02:50', '2025-10-31 18:02:50', '2026-10-31 18:02:50'),
('15f6d053d672fbefa25707b471284716198ca8a176f183edc615105acdfb1e61d168c831a265a677', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 13:47:35', '2025-12-01 13:47:35', '2026-12-01 13:47:35'),
('15f801dcf8eff8bb631856f74db129b12a1819acf9f352baa9c081ead959071f930e3654667a00cc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-01 08:48:45', '2026-01-01 08:48:45', '2027-01-01 08:48:45'),
('1647c98696ffc81207126066347381e4b64f6d4a590a725d91fb01e7cb0bbdca86bcf0cd35a6d406', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 13:41:58', '2025-11-03 13:41:58', '2026-11-03 13:41:58'),
('1660d3e9b242a1fb2d6c182f63b97f7e81eebe6f35a5912b9538225517e43ad11a6a26e97847d2a9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 06:09:14', '2025-09-18 06:09:14', '2026-09-18 06:09:14'),
('166510beaf226bbae38f63fd4b6da300a254c8952d3bc21ddc10f86c16d53eafb5f6fc5dd2d70073', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:18:31', '2025-11-13 11:18:31', '2026-11-13 11:18:31'),
('168aa4ac84ad2c76a6297fe188c313eb1c5f55ba8a1e5923cdc44935ed142e90ac7764a4074e6b8c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-29 11:03:27', '2025-10-29 11:03:27', '2026-10-29 11:03:27'),
('16ca9855bcdd32f688e622a37b79ab7dc130574d19c37e280d5f831dfbd0592dce000aaf37802c49', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-02 10:34:02', '2025-12-02 10:34:02', '2026-12-02 10:34:02'),
('16e43aa9228fb542304faa3981fe5368e37cbc70524d41dd10961ecf679b81117b912630d5b15c43', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:13:38', '2025-11-03 12:13:38', '2026-11-03 12:13:38'),
('16e8b64b26a0f6fc8d059547363f851acfa44896b6f45e7bcbb112fb9cdef4c723ea717aae7f9899', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 14:48:38', '2025-11-13 14:48:38', '2026-11-13 14:48:38'),
('1706f7ed778697d26641a2d5599bfcef16dc660784e799022cc28dcff9a7e1eb890a0839e62c80ec', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-13 23:34:28', '2025-05-13 23:34:28', '2026-05-14 05:04:28'),
('17173834c77ba22bbd7c45ff96eba63a9ba02f773640665af77adc1911652fcca170ae2ff23089af', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 07:13:29', '2025-09-23 07:13:29', '2026-09-23 07:13:29'),
('171a165b515a551f23927b5e6d7c05b97355f4fc89ab85d863c757d64c7bf204b69d69dca70cc417', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 12:06:33', '2025-11-04 12:06:33', '2026-11-04 12:06:33'),
('175bde0783c92c33d615a3eac8ee24fe4c88479429d275b7067ad2b82c431510c88581e8783b3b4e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-10 10:20:50', '2026-01-10 10:20:51', '2027-01-10 10:20:50'),
('175e1cd5f690dfa2c88e81a182b4f611fcd65a9224fd41df01c95a1cf8b9686bd617b50d2c519e69', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 18:20:27', '2025-06-11 18:20:27', '2026-06-11 11:20:27'),
('1767be5d77f94dea2f8eeafed10a6a180de82574bd1af471ca18e2ca6476ee19580eea7da147b39c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 18:28:07', '2025-05-30 18:28:07', '2026-05-30 11:28:07'),
('17901655f0db3a1f7237db49c2e98e5e15bf2593f80da1e6d9620d59b38bfcad24a4de42014eda01', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 14:58:10', '2025-06-11 14:58:10', '2026-06-11 07:58:10'),
('17989132ac54ebafd25a0a7069d6547fd7822ada619b51a58e52f07a8d83f222170d6ba5da3447c3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-08 11:35:12', '2026-01-08 11:35:12', '2027-01-08 11:35:12'),
('17cec6660d33849f48a8ba8129dc4d10ed7124f6163287879cc12b8ca1854105feabe081c6991dfc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-06 14:47:08', '2026-02-06 14:47:08', '2027-02-06 14:47:08'),
('17f4ec9ff6e1fe0afe6b100770d6337f831c1ace712a8e40927ba4fdb014f51b27387e99f3d7726d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-04-06 23:29:27', '2025-04-06 23:29:27', '2026-04-07 04:59:27'),
('1831d3985cf0ae40cb375bc6624c048c116afe16e729194a5875c509cba19973b14db604c37c1ee4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 12:32:48', '2025-08-28 12:32:48', '2026-08-28 12:32:48'),
('18449a45ecfe7401a1cfde5ee825e30f036378cd6511af5cce82c29cd564e2ff8fc19257a8c95465', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-28 15:24:34', '2026-04-28 15:24:34', '2027-04-28 15:24:34'),
('187737d1ad9c87a8e08a8ee531da90e9f0f367fe034e8cd44bc531786e8e084f36e2b1ae37fed9af', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-16 10:08:37', '2026-01-16 10:08:37', '2027-01-16 10:08:37'),
('18a14398f6c63f253f4fb594a03872f337b38cff5ff4d54fe42498513e77903c33ce3e9e7ee0458c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 05:04:24', '2025-10-31 05:04:24', '2026-10-31 05:04:24'),
('18c11bc16a98203c74ee18d56e14eb10635eec5eca0554ee47cf77a68cbb4b87d3be5857b808d3ab', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-28 13:57:42', '2026-04-28 13:57:42', '2027-04-28 13:57:42'),
('18c9e65f650b95e1d7b6dc5d5e9027d95d433b621d2eeec0d17b93501db6ecd5736b4d8f62f4fe4d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 11:27:15', '2025-07-16 11:27:15', '2026-07-16 11:27:15'),
('18d8cee350f4924d8e1b72f82ed69fb6b0d0e23f46974bda32b08d54bfaaa6cf11809fe86ac0a568', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 11:08:40', '2026-03-30 11:08:40', '2027-03-30 11:08:40'),
('18ec6e6010e43cd67c8cd8d510b981eee688718a9a85aea3bbb6e38638a8074a3f1a501aa158efc3', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 15:10:06', '2025-11-26 15:34:38', '2026-11-26 15:10:06'),
('18f8acbab29e025431463a28c30667d49f1e62b634df5c8723e1902871cade97f2d9012f8c73a8b6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 10:16:06', '2025-11-05 10:16:06', '2026-11-05 10:16:06'),
('190af323a4f2366f506d469d41a5fffc40d301a8e04cb6bd11ea1d9234575ee790b1b1800bd6b360', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 15:00:43', '2026-04-06 15:00:43', '2027-04-06 15:00:43'),
('19315ba6f164a3542f6c0b50f6a18a800e88cce92eaabf852b368c56aaf9ba18a635f667546081c4', 21, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 11:59:34', '2025-07-16 11:59:34', '2026-07-16 11:59:34'),
('198e0114963002ed4b0ccd18e40241a646332ffbf63c0244de39956ff6dd59e043acd63aeb6ca5c1', 219, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 13:10:47', '2025-11-03 13:10:47', '2026-11-03 13:10:47'),
('1992eada942db7540eed7a6a7293dd46c384756ce2e2a63a4c09f01289fe3507a2f0b1d6662e3697', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-01 14:48:55', '2026-04-01 14:48:55', '2027-04-01 14:48:55'),
('19b5008b5b4a8a0ebf58d43d1c9117c8a380d0dcdddc8b22033a2827ebd929275877f931c440c2d4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 04:35:43', '2025-09-25 04:35:43', '2026-09-25 04:35:43'),
('19cf6c3115316428d50fd39fb32df2d9fcc6d73fe26688056da4053a46867ecb4315a917428042ce', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-21 04:25:33', '2025-08-21 04:25:33', '2026-08-21 04:25:33'),
('19ed0ffe39145eec9d35b0085efe6c94edf529180462feccb4bd059bcd3320eaa5ab937a38216fba', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 16:43:26', '2026-03-31 16:43:26', '2027-03-31 16:43:26'),
('19fe2d46b64acc6cfcfd07f463699b471819c125654d4b06e9f18d7faf45f77ff16a7da752dbf439', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 06:42:45', '2025-09-18 06:42:45', '2026-09-18 06:42:45'),
('1a08656edbd540e105789559bac099bc6a9d613b1b9d501d10c71d3ebd6f5a8c852702ea1673689c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-26 12:58:15', '2025-12-26 12:58:15', '2026-12-26 12:58:15'),
('1a12f95b85003b055c9709eff5cfc64f0c7a813024ad6fed16085b84437b9ad65493aefb03153aac', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-27 06:51:00', '2025-10-27 06:51:00', '2026-10-27 06:51:00'),
('1a18e18f4703fb1033b81ddde0e5d93dba9d995bf9c78fd54894a001a595ad0e8ee93a4ad61b58da', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-16 18:34:21', '2025-05-16 18:34:21', '2026-05-16 11:34:21'),
('1a3a308cb1a0f7997c3681b262f269c15f0dab55fe63daca86d00ccb30998187896fff88c224be75', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 17:46:51', '2026-01-29 17:46:51', '2027-01-29 17:46:51'),
('1a4cd8c686a0f134d0b56aef4f0473fca26470da4af2cf17cc6532bfbe950aa4e2f7f1396fd699a0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-16 17:48:03', '2026-01-16 17:48:03', '2027-01-16 17:48:03'),
('1a6a017db6a26e848c926780666b0ea40cd50d82bc7973c8ab477837c599cc244e1371ce9f09aa3b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-11 10:12:51', '2026-02-11 10:12:51', '2027-02-11 10:12:51'),
('1ab448b459813ae3c55723d7025308cd71fee42645f08c3410f13e8a01ba742ad6419389ce1393cd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-06 04:51:43', '2025-10-06 04:51:43', '2026-10-06 04:51:43'),
('1ab9884eb5cbf9c74a3b2a4683c0bef1c715da1c9ad63185386b2815f0640787c0a8eccca741bade', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-22 11:59:20', '2026-04-22 11:59:20', '2027-04-22 11:59:20'),
('1ac43201ccda177f76d71706f2be51d046c683d674b6a61e64a5cbd78545bf569de5448f1065227f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 05:51:43', '2025-07-16 05:51:43', '2026-07-16 05:51:43'),
('1ae975861bbb72f2a7d5200c3b6c7b63096846448a50b284a894ef63abc141bbbdb25fc3c27ac28f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-07 16:11:47', '2026-01-07 16:11:47', '2027-01-07 16:11:47'),
('1aeb2249226831357c582b77f7949f14d7e145482830819438e98680a70b8362d3092f2638bf9594', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 05:15:34', '2025-09-03 05:15:34', '2026-09-03 05:15:34'),
('1aebf5d6d8350451a1ebd375aac756603398d9a3540c210b0400c04130b687652804b5ae19a2cdfa', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:46:32', '2025-11-12 12:46:32', '2026-11-12 12:46:32'),
('1af3823c428b3f7a211a681c9f1fefbd85db5a18d140aece43a6913095579faa7a7a4dc7c8e994bd', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:53:26', '2025-11-06 10:53:26', '2026-11-06 10:53:26'),
('1b6fcd2f52cd66112015816003d0880a91aeaeb2c77dd7cf65c65c26e419ccaef0bc7350ade1adcb', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:52:48', '2025-11-06 10:52:48', '2026-11-06 10:52:48'),
('1b96de2ca393bcfde9c064288d1f89afed7b334f13dd07c98854f989acf4511102ed389d28cbb479', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:35:16', '2025-11-12 12:35:16', '2026-11-12 12:35:16'),
('1c520c6f546886a4a560273c89ac40f850a164c5d4f8030fd21f29552d91c06cfa58c4a7ba12529b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 04:46:33', '2025-09-09 04:46:33', '2026-09-09 04:46:33'),
('1c725c4b29a4e8e22b19513d50f69be5274298e96e35f028185296d8ad92e902746538f6a9c42691', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:24:42', '2025-11-12 12:24:42', '2026-11-12 12:24:42'),
('1c7abadc32a670ec43e3cf6c0998229a104dd62e8135e5d4b7823af1df9eabd3a395e0d7bec99618', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-24 06:35:58', '2025-09-24 06:35:58', '2026-09-24 06:35:58'),
('1c83ab25f589a453bbbc9921cd28026584a1dcea1aca78c7fe7168e8ce33d110672541f6dcf840c3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:30:53', '2025-09-23 05:30:53', '2026-09-23 05:30:53'),
('1c86c4edc921192e6c51665ffdbdbb833a4f1e045c475c3850aa7404fafa6a880b501af4b99878bb', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 14:46:10', '2025-11-26 14:51:01', '2026-11-26 14:46:10'),
('1d0771f5405ad1672739a00693ba8bdff1bd6f1047f288411ad4aa49d01da5302cbf9b78b6bd2b2c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 12:25:28', '2025-09-18 12:25:28', '2026-09-18 12:25:28'),
('1d0ef83cf71c0425afc220bb5eb979ecc31fba7b4c4b330e1a5143e66f643a0a9fdb9102bf7cfbad', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 13:04:46', '2025-09-02 13:04:46', '2026-09-02 13:04:46'),
('1d1ecf607c9df2581a9027aea17e135f5fc0e70e654fa3918908bafef0a1b216cd2669be71cfa57d', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:49:53', '2025-11-12 13:49:53', '2026-11-12 13:49:53'),
('1d392d36ab2aeec6ff0eb9cfe2bf34f5094b8624dbe0b1f80d32d9c840d791e9fab3640c11cff8b5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-02 12:54:52', '2026-01-02 12:54:52', '2027-01-02 12:54:52'),
('1d4ac9e71876ec5a9fd664a4384950e65a7f08b71e011548fa6f126bf98abeabe92e8e8c00e2914c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-24 11:15:09', '2025-05-24 11:15:09', '2026-05-24 04:15:09'),
('1d5e5b6e0d48810e198acaf16d03912f8ac8277649c200bc4f3ff610e0b94487b5a6b8bbda901060', 210, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 12:32:05', '2025-10-30 12:32:05', '2026-10-30 12:32:05'),
('1d650d5091706ed4c736d65aecf0f52148aad8a8b4e70dd10e6614ba69e6e2eec8407a737a3afcf2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-27 06:18:45', '2025-08-27 06:18:45', '2026-08-27 06:18:45'),
('1d7e0254923749c52c76d26b1fce38d71e249ad76092871d4500e01e9b8fcb076665b0fc732afb8f', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 15:37:52', '2025-11-26 15:37:52', '2026-11-26 15:37:52'),
('1d9fb36ad04368f37687b16a5521079f21171bb17ce2a752c5f8684b8473f80f53087e5677aa4fa5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-02 12:51:15', '2025-06-02 12:51:15', '2026-06-02 05:51:15'),
('1daed85a28e9f516016c9540cd7982c4f58dffb3a97be62b67d391ca46d9ad01bc2346e7a1840050', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 04:04:40', '2025-09-08 04:04:40', '2026-09-08 04:04:40'),
('1df612b5085a20540ada77a682c8d334eb5456bf00e9b1f65bb5de0af1779582527fe8be36954187', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-01 16:32:06', '2026-05-01 16:32:06', '2027-05-01 16:32:06'),
('1e11ff36e91ec86f560efca2b308393c8c32fe79775c5cdb82d727aa5ad60798b40d26ef6c341ef5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-16 09:11:07', '2025-09-16 09:11:07', '2026-09-16 09:11:07'),
('1e3a1c7c150af5b1337cfdd6c57e5e6827b1477291b803881526a0273e9b7a4be1ca11b6fd72e91c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-13 05:17:04', '2025-10-13 05:17:04', '2026-10-13 05:17:04'),
('1e3cded289acc3b7520c4977191ad2fc26a75494784656fda0e8cf5fe92c505a80b039568208a62f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-06 16:01:58', '2025-06-06 16:01:58', '2026-06-06 09:01:58'),
('1e405d630a965e344e32299c648e1a3c74e4fe4c726b58b82ab747079558e95e215fc547e4ca9cde', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 06:54:38', '2025-09-17 06:54:38', '2026-09-17 06:54:38'),
('1e55b7501ba484004c94b5fa114b824d86d68269fbbd071b4f86f59ac91d34ab620d0b35297d6442', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 12:13:30', '2025-11-11 12:13:30', '2026-11-11 12:13:30'),
('1e5e1949c51fdeccf264a42a4c4176f996695a4e0a1f2001cd6a0476e7663b0586ac165f8b4dcee6', 76, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 07:10:11', '2025-09-18 07:10:11', '2026-09-18 07:10:11'),
('1e682ec6811afee97d1cfd78d29d09f043bbdede2ca9926dcf7df83a4e68eb6fd0bb2a08d102a46e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-27 10:44:31', '2025-12-27 10:44:32', '2026-12-27 10:44:31'),
('1e7ebfde16e0093ffbb6ea9e65c8ac4cfdedaf33316128a6b90a1d0fb14e569970830a2fc88b8718', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-14 08:03:59', '2025-10-14 08:03:59', '2026-10-14 08:03:59'),
('1ef04e1b1e47782ea286c1e075ad5953890d7384d06672b2af54861d5798d8b0accd3ba587571b7e', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 16:46:04', '2025-11-13 16:46:04', '2026-11-13 16:46:04'),
('1f0391be8d8b71ef516c8db9319fb99307af0586b8ed3004ac2d801c4fc572c0b44e1a548e458f86', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-23 12:11:01', '2026-01-23 12:11:01', '2027-01-23 12:11:01'),
('1f45c0f372e9c182ffc59eaa72a5f6b3429ff02b9060f138bbfd267bcb8c5eaf465a372c1bc3cbbb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 13:35:29', '2025-11-04 13:35:29', '2026-11-04 13:35:29'),
('1f523594dfb571a2799fa43ea16312084365d87abc5a363f533a71320f2476a4be8ec751274cbc53', 150, 1, 'LaravelPassportToken', '[]', 0, '2026-01-02 12:51:22', '2026-01-02 12:51:22', '2027-01-02 12:51:22'),
('1f7331a1e5d52f6499f26482356e00d2f64bddd0b4a09f82db09f505e2d3518fe6be04969cc48574', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 14:49:59', '2025-11-11 14:49:59', '2026-11-11 14:49:59'),
('1f7a9c750187ff5470574890f04424f5057b33d8d2b88efb5bd9456717203e45870ff58782943e8b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-23 11:57:26', '2026-04-23 11:57:26', '2027-04-23 11:57:26'),
('1f8a2b857442fe741ab9fc1c7721409a1a8f7d2d7367a55e7e4ca1db274539f2c169dd1704c8af1c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 14:21:24', '2025-06-11 14:21:24', '2026-06-11 07:21:24'),
('1f942b12ce506665c9288b2cbc7d76cc5e0f49dc96b7488e31708c595f5b6fb271734f36ad0ef861', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:13:44', '2025-11-12 12:13:44', '2026-11-12 12:13:44');
INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('1fa558c55539d8d01a0afde4b454339ffad0cd2d54e50d878fc0474d1e5a3d3c9ea2e1dc0225cc81', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-13 11:10:03', '2025-06-13 11:10:03', '2026-06-13 04:10:03'),
('1fa94b81af162c12c10fe98375f00d3b28622446382dff5774326ccf001acc0c19780f5fc525db46', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-23 17:16:34', '2025-12-23 17:16:34', '2026-12-23 17:16:34'),
('1fa98f77c536b73d8885d0f7f812d475b4e688bdca8d1698c94b42bcb78d0a5dbfa8dbb01d89a8e3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 10:40:13', '2026-05-26 10:40:13', '2027-05-26 10:40:13'),
('1fc246c5ae62eb5faf9185ce063309939e2288e8be37667634ea316d6327e5a23c1c39244646eaa2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-08 11:13:40', '2025-11-08 11:13:40', '2026-11-08 11:13:40'),
('1fea079cb7ec21a73c026bc35702e8b6b97b64995602f0ba19b35b1d6d1bbb8312043dc10778ab01', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-17 17:18:53', '2026-03-17 17:18:53', '2027-03-17 17:18:53'),
('20150e2f170e05e6069477b687cfaccfc54f07bbe4cbfd8c2c353d9a997088eb5ddf80b67072f7f7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 13:22:30', '2025-10-30 13:22:30', '2026-10-30 13:22:30'),
('201cbe73532e76e449de2b579822ce5887883043f339d7ff93b56578afeb965b5818c3773c44b2d4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-13 21:43:30', '2026-04-13 21:43:30', '2027-04-13 21:43:30'),
('202a9f30b2028a457054447d6e32a92a0d40b406cd947094b5e4ea1b09d0e136ee75b6efa3ff05e1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-27 16:28:09', '2026-03-27 16:28:09', '2027-03-27 16:28:09'),
('203c2633a9fc064c94ed65f8e2a3665f437520cf943b4b51f5a626dcb7029d7c1a2f5d9295aeff85', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-12 20:46:34', '2026-03-12 20:46:34', '2027-03-12 20:46:34'),
('204e73925613405731fe3546c859f2b22917275d6e58458d92711ba2f731facceeb4b036ab5acb52', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-02 16:14:30', '2026-04-02 16:14:30', '2027-04-02 16:14:30'),
('2053241015ba00e59253a943790336023ee8cffe2eff573ac8fc506797c4747a726bac9b2c95f7d1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-18 17:11:12', '2026-02-18 17:11:12', '2027-02-18 17:11:12'),
('2066e6ec49191c98d348dc33b335e00b771c56e55f93394a14b51959f5bf9ef3f84245c0435695d0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 10:50:02', '2025-09-19 10:50:02', '2026-09-19 10:50:02'),
('207796d4dff391c2f61e5817028ce9d3dd607297e25e8e7176d021c1660589499e4a80c24beef828', 208, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 11:02:44', '2025-11-06 11:02:44', '2026-11-06 11:02:44'),
('208e436072524aea20ef2531f7a29e964084afd0029100e37b471e75ec7d51361a2c6c04d199c3e7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-07 13:46:53', '2026-01-07 13:46:53', '2027-01-07 13:46:53'),
('209d48f55cda6157a8ca8f945183b704a7ce1efa119061bf9015424a304510066bd2410c2649941c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-24 04:07:18', '2025-09-24 04:07:18', '2026-09-24 04:07:18'),
('209e53be62b4277e1b72209a1abe616cd4ff6e0caddaf9381edfe1fadb881987efdc97326aac773f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 10:56:34', '2026-03-02 10:56:34', '2027-03-02 10:56:34'),
('20a5e27a116ec3c1464e356379780a770057b016f5007168f2a22c49923f1846dbcbb76158b5fdd0', 328, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 15:25:38', '2026-03-16 15:25:38', '2027-03-16 15:25:38'),
('20d562564ae0cd1fe1660cb88471328e4449099024116b10d7377dc362bcd950dee2bad4bd93363e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 18:03:27', '2026-03-16 18:03:27', '2027-03-16 18:03:27'),
('20ee04a18f95f7444311d62cb8b96527adbde09032cf5d80f28513b77eacf57c8e61d5769ccc22ba', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:39:34', '2025-11-13 11:39:34', '2026-11-13 11:39:34'),
('20f7d2a76950aaf046de7a9d4b224b26dd8f79a0bb17d88d2825096383e674cd5a72903aa326f5d5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 12:37:26', '2025-09-17 12:37:26', '2026-09-17 12:37:26'),
('20f8000f60bba64487e6b1f45c8e2bdf7f0e320e8f073ac1e2c9e0f678090e0adba4e0569a8ddc00', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-25 17:18:38', '2025-11-25 17:18:38', '2026-11-25 17:18:38'),
('20fdde6fbacb669fc97de81e6e91cbcfa4ce586f2d0627d80fae0e4f5b8eceb4ac61ec261d37eb60', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 14:53:00', '2025-11-20 14:53:00', '2026-11-20 14:53:00'),
('210dc7d7450d5a0b0128f7a8ffea112439b44f0b152cd33f28d8951db28c9de960c32d5a25562ced', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:20:58', '2025-11-05 13:20:58', '2026-11-05 13:20:58'),
('210e1ea37d04c1d76d7a914b3f4549aa993371002db44669577402283f00266326a90c1b7777a227', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-28 15:02:31', '2025-11-28 16:12:51', '2026-11-28 15:02:31'),
('21391364dc427c18c201dc4b88825ced67d696200f074ce82fd6dbc5832f572653f388bc9a9637d7', 21, 1, 'LaravelPassportToken', '[]', 0, '2025-08-01 04:41:43', '2025-08-01 04:41:43', '2026-08-01 04:41:43'),
('2149f8f4eba721d95ab09b75c02242c3a60e36a9062586ebdeaf3ae1870274abaf615283ab1b9678', 336, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 11:34:15', '2026-03-30 11:34:15', '2027-03-30 11:34:15'),
('214b2adfa41018f03e97b8880add804993c857521675c0825a0aa953f1cfe243aa4595a2bc068cdb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 06:24:17', '2025-09-03 06:24:17', '2026-09-03 06:24:17'),
('21860d1005023f8b5a9d53a5ad8cdda32cb22b71c2fca7f16c7bb5f6ae6656298683f18c4ae1c9a0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-23 07:03:29', '2025-08-23 07:03:29', '2026-08-23 07:03:29'),
('21c1732fd75e9ce4185dcffa351bd700e6ac08f4d0503c90f78421c6206d06a753bb2c9128761738', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-14 15:12:47', '2025-11-14 15:12:47', '2026-11-14 15:12:47'),
('21c1b59d304a8a8e8756ae90c8a1f5678b3f0de7dbc6ff87392dffb3e1e5a2933b41059de1b96803', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-30 04:30:12', '2025-08-30 04:30:12', '2026-08-30 04:30:12'),
('21e9a2d16aaf7e895a714d20384d1b1b49af21c1caaff26f7897e8dfac8c3e750dda9d263eb891f7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:39:29', '2025-09-01 09:39:29', '2026-09-01 09:39:29'),
('21f886c1f159c2539ee05361cf92af01e02e32fc4f4dec19d7170d46e7d4174da338f3de15d18f70', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 13:51:13', '2025-11-04 13:51:13', '2026-11-04 13:51:13'),
('2201c2c9710a58c373c40cad46934eae7d3ade770d76958e47273aca29fb98e671df18d70e6533bf', 151, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 11:38:42', '2025-09-18 11:38:42', '2026-09-18 11:38:42'),
('220d4cf16bec3eaf54d54c0599a7a7549c1163477ecaef3f9849c4a53475122ae3cb746f0af8cc3b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-06 15:37:43', '2026-01-06 15:37:43', '2027-01-06 15:37:43'),
('22184e7edad272e252bbfba4f0e5b5d52c8f3cf6cc9b52db253a154fe5dc8e1ac7d8305d0a26d1c8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 11:49:14', '2025-09-09 11:49:14', '2026-09-09 11:49:14'),
('225602be81e53570667a8710a55e96ee94dd9a08888c2a02d582dd59911677ee7026a25ea785e975', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-20 18:07:46', '2026-01-20 18:07:46', '2027-01-20 18:07:46'),
('225b6ad529627029b8837d22878e209be579aa27ee7ebce533a73b543bc8825235f74a1552e7bb88', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-06 19:56:08', '2025-06-06 19:56:08', '2026-06-06 12:56:08'),
('22934486055a21d67605e34432c081aea00a2e1e42e2246ab46e0a648cda4654446faf98e0da5f37', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-12 09:37:15', '2026-03-12 09:37:15', '2027-03-12 09:37:15'),
('22937ba96bc343ab8591450b2d5ba17154797173958f09dcf243067b1ae152046f0c04b98631f9cc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-27 16:48:19', '2026-01-27 16:48:19', '2027-01-27 16:48:19'),
('22944d7d30c914eb9afb303c9d862cdb15a1c300b7624458a34cdf65fa3dc9f64f887fa9ee2cff4d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-01 04:51:46', '2025-08-01 04:51:46', '2026-08-01 04:51:46'),
('229df00e0f6fc5e2cfbe9956c6db8438287d278d6f96cef1e9ca8920d6fdfd9c90222ae3b92e6b39', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 16:08:41', '2026-05-25 16:08:41', '2027-05-25 16:08:41'),
('22c3f000c3408326067e3b68cc98aad30d67700bbd8465f66c0d272a7fde74d065da5e4ef3aea37c', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:07:54', '2025-11-13 11:07:54', '2026-11-13 11:07:54'),
('22dac495d5e5b1ca60800dcb172e6b182786892d353b8dee7daf43113ffc86e8493491e9f57ba08a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-09 18:10:23', '2026-01-09 18:10:23', '2027-01-09 18:10:23'),
('23073d20aae545ae01c545684419e242a4f5c65035c4beed18f0e670d3dd43c913e6dad8ea17d261', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:13:32', '2025-11-12 13:13:32', '2026-11-12 13:13:32'),
('2308566afda2aedabc16ff45f8fce0ff723220e00ff27b7c54d70e29203990b5ce9f2d7e610cd3ac', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-11 04:57:47', '2025-09-11 04:57:47', '2026-09-11 04:57:47'),
('231aaf4bfb0f8656ad7c49cb7fc329b1b8757e397dc8a173b1cd56d4c8e0cf06f1d5f55132b1430a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 04:42:06', '2025-09-03 04:42:06', '2026-09-03 04:42:06'),
('238c9c17dcded4e34355939a4a839ede5aa03e5dfbf4779b231b61c62a440860b8b54e0eaa7e0946', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 11:07:06', '2025-11-11 11:07:06', '2026-11-11 11:07:06'),
('238d1b238231dfb203b5697d069240132a70844d4fc028818dfb7b8a0e29081e2274643867378ac9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 09:46:30', '2025-11-07 09:46:40', '2026-11-07 09:46:30'),
('239813f7d181cea26c2f22a510ace9e218e759e0c808a71528d1f12055f0508d9a65f55c6c651393', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:05:14', '2025-09-23 05:05:14', '2026-09-23 05:05:14'),
('23bbf21ea7ba0cd19c950bfde074759f73478d1bf049acb49244e1d800c6dfee5d0dd3de44ec129c', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 11:03:53', '2025-09-25 11:03:54', '2026-09-25 11:03:53'),
('23beaf977f2b09fe360929dd88ad1c963d377cb98d323ea0c717758e6e9aeb7919252531176a98de', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-21 15:34:19', '2026-01-21 15:34:19', '2027-01-21 15:34:19'),
('23c85c1d25d76eb0e3914d69fe4d70b6525416a13b7a563b18151da9f5afad4424aa4dfebf261d2f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-23 17:07:33', '2026-03-23 17:07:33', '2027-03-23 17:07:33'),
('23e23c569f2e42f37038aa4fef13f3c0afb5f739c9c32a36ecad8e825c1b0743d320e77b7514a035', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 16:44:51', '2025-11-13 16:44:51', '2026-11-13 16:44:51'),
('23fef3776aace3032333336fec9b528f9983d99e62f4b8b73276c8be7a94088017513fff4748ddc8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-02 12:09:50', '2026-01-02 12:09:50', '2027-01-02 12:09:50'),
('244194493d250b3e16c82baa4c1a41f2c70e0c372b0e004451e27cbdbcf4d0312f2edb54a46c6f0e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 12:10:16', '2025-06-16 12:10:16', '2026-06-16 05:10:16'),
('247deb4458ae907fbd65ead4547e0522c6927dd5236536acfd03151c6b350659fa06ca90b3f284f9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 11:26:20', '2025-11-03 11:26:20', '2026-11-03 11:26:20'),
('24b4c30445fd0ddd5a0bc9190f32968bfedf97ab40ffd2d83647037bf1cf97a58e825e8bd6bde1e8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:38:39', '2025-10-31 18:38:39', '2026-10-31 18:38:39'),
('24ff08887014a9aa5b88b1477f1d51161e254a17c5bc204bf7caf2720409e248ef7cacbd64a561cd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-09 13:33:15', '2026-01-09 13:33:15', '2027-01-09 13:33:15'),
('2500cdfc7d06d61bf7583bd5bf7c7805043745d88802d04cc42bcf2f5ff9c112beeec4cfdf4ae498', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-19 17:41:58', '2026-05-19 17:41:58', '2027-05-19 17:41:58'),
('251cbb173d347b8846ec9456d445276b2d9c9b1b4ff52a087d4d2d8c445db35224bcf7760bd6582a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-24 14:41:55', '2025-12-24 14:41:55', '2026-12-24 14:41:55'),
('25357e3c25a59802dd901beb813a8e59583d539d4ef2092d8f40d0409c0ffdf5bb81d43c18140a92', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-06 05:00:57', '2025-10-06 05:00:58', '2026-10-06 05:00:57'),
('2535f6f841b4ec59751e9e32189e745e671d904558b8d70d9229c38018788a6bcb79df17508ca526', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-30 17:20:51', '2026-04-30 17:20:51', '2027-04-30 17:20:51'),
('2562879892b05349364675df3fbf5e88b5ebf5669e05253388824ac217438b7fe43af8065c920c72', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-18 10:09:20', '2026-03-18 10:09:20', '2027-03-18 10:09:20'),
('2563f749359ab10e8a5e4dfd5d132eca7c4e510fab586660494aaa013ad7d0dd7b4ece3ed8c59714', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-30 17:43:39', '2026-01-30 17:43:39', '2027-01-30 17:43:39'),
('256b3e1738be19a709f050081ff3f18690a0caa8d99a91a5fe17a00806c53f3c695d710bd5a3bab2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-22 09:58:36', '2025-11-22 09:58:36', '2026-11-22 09:58:36'),
('25cd0613f18eead94952ca1df54a0cc0f041abe195812df96d2863b394cbd2ced888c225093b09ee', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 16:10:25', '2025-11-13 16:10:25', '2026-11-13 16:10:25'),
('25ebd87f7cf3bdc72d49f597e196b80bd1ad4983696398d06d9197cd2769c672a9f3858c8930a898', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:42:35', '2025-09-01 09:42:35', '2026-09-01 09:42:35'),
('260aade58eab88e608b40953ccaea2884355f8eccd2e4a6245a094fc28b9df79260d24dc42a351e8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 12:48:35', '2025-08-28 12:48:35', '2026-08-28 12:48:35'),
('2612deffd08aa573d38505ed6a8225fa3997fa5b26184426908d671e62f818f4d32dc2be4696a81c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 10:05:50', '2026-03-24 10:05:50', '2027-03-24 10:05:50'),
('262255b94f80be78ceb425106036b4e754112b68b4c3c6c3b1a0d2d2996df83e401e9b78533fea6b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-01 11:34:40', '2025-07-01 11:34:40', '2026-07-01 11:34:40'),
('26289a249d915f7f00f89e48f3ebca6a21d4f0b18ea30ab3f63a3f3cd8d0d7fa7ee094aeba7229f8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-23 10:18:12', '2025-12-23 10:18:12', '2026-12-23 10:18:12'),
('26601b127ea5467a1d0d8f14bb077405823c9da66dfb9248da3fa43bbb580cb3b12b2664c685247e', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 11:26:37', '2025-11-13 12:07:52', '2026-11-13 11:26:37'),
('268cc9478c50fbe1f1556b3cd2e95e60aa632bb1856b0f4e6a7d313a3dc368d82a9e2f49c6b10cbf', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:14:26', '2025-11-03 12:14:26', '2026-11-03 12:14:26'),
('268fb4ab335f9968cbb70bcfb3fd422a22d827b08c7ab9c4a7e87e567e5114e4d55451be2780f31a', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 11:08:42', '2025-09-22 11:08:42', '2026-09-22 11:08:42'),
('26b2e2e852d274453a0aea8bef558409cde14441dfebb4f1a1fdef092a892c59d6c379f84a149dac', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:16:17', '2025-11-12 12:16:17', '2026-11-12 12:16:17'),
('26bc6a598b84953a5a33fca666149147b3b81284ae84d2eb5cae37a345fe551b3efef3b862e5d6d3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-23 16:53:34', '2026-01-23 16:53:35', '2027-01-23 16:53:34'),
('26c93238cd8128cc7b47fe904d4b0061a4f6f5e23e31d64cc7d0f75ea712421dc14eeddb1235afe5', 175, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 13:49:04', '2025-12-01 13:49:04', '2026-12-01 13:49:04'),
('26e7aaf669996e4333a84c029ce68a7252de3369af97e8c48a3cfc2c00be6f63068bf493c2a3d49d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 11:25:40', '2025-11-04 11:25:40', '2026-11-04 11:25:40'),
('26e7f542bb526b12fda2fdfa705639f726015115a43c4ef4e145767ca0819d09cb42dd2b8d54c452', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 05:25:38', '2025-10-30 05:25:38', '2026-10-30 05:25:38'),
('26f04f45b0cb30be1a223b1efc80f8e957bf3c57d8e1c4e6b3c41837f9582e4ade3e7384c0712b55', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-15 15:01:39', '2025-12-15 15:01:39', '2026-12-15 15:01:39'),
('26ffc17e955bd4441552a32d3a30cbfc8e90b03502ac18ddcb71a9af7f3e5f8063f018abbb0ee9f7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-23 13:53:34', '2025-12-23 13:53:34', '2026-12-23 13:53:34'),
('270b81ae18c57a115ab4a0076948ee079b5891fbffb729f34e5ef7c301217090dc0969cfc9b0973c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-26 04:12:26', '2025-09-26 04:12:26', '2026-09-26 04:12:26'),
('27df00c81df9ef2458fe58abcb38c18c4ed544b3fd77c7afa8e1c86d114f2331bf0455660f250ea8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 14:01:25', '2026-03-31 14:01:25', '2027-03-31 14:01:25'),
('27e26d80cff0d208b891a6b29dfad56d4336e1c9fa2705e994b7e5972538cfd611e160aac55a0b4b', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 16:28:25', '2025-10-31 16:28:25', '2026-10-31 16:28:25'),
('27f29d87246d2ae9a6b347e9064013541be6d048c077972a7dd0cd523c235ca03dcad5edd97ef28a', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:10:09', '2025-11-03 12:10:09', '2026-11-03 12:10:09'),
('280b16ce5cbafef7a726a6a0f1017cfd6c73c5d1ee1a1d814616671cac692cd1e930b297d07ff265', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 04:22:20', '2025-10-31 04:22:20', '2026-10-31 04:22:20'),
('28743473f5663705ce672fa64e6295e4fd4112e595a6432d54e3f1da5bfaeb02b7e86017d986f624', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-23 12:51:36', '2025-12-23 12:51:36', '2026-12-23 12:51:36'),
('287e06d93a0bf1e048f6521895ea0a4c02afce5919d99965e2a25edcc718470e38ef7fb1e5551145', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-05 15:53:36', '2026-01-05 15:53:36', '2027-01-05 15:53:36'),
('289646ca3bf65ae035a104a6ef640c5946ff90e0febec15516e60dcf7eaff4e2ecf6008a5df4bfe9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-10 06:04:16', '2025-10-10 06:04:16', '2026-10-10 06:04:16'),
('289a5fbdf11843a29d90ad4cde37c42ce512ac148f43377c79f50333219960cbc87fb37ba0ee1c41', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:26:26', '2025-11-13 11:26:26', '2026-11-13 11:26:26'),
('28a25d4717da5d64b8846f91407cc860499b4c8ff8119c5f376876ce94416c7e6fb187edd6751c37', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 14:48:15', '2025-11-06 14:48:15', '2026-11-06 14:48:15'),
('28b6fd9c5a3e5fa84bafc6680d917f3c2858f2758b781a1b97fc7340c1ded188fcb0ea33b7f401ff', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 04:32:47', '2025-09-17 04:32:47', '2026-09-17 04:32:47'),
('28c059880e81406ae35db0931311902d1a62d58ad5e74f3a437e7af98aa54d1a58b3f5ff21bc6cc7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-30 13:34:24', '2025-12-30 13:34:24', '2026-12-30 13:34:24'),
('28d8e9761d4d6be7be6b38ad5b14b6d1eed9fdf36d27ed8d9ab299d031ac6224825571b49d5c4b8d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-01 16:09:02', '2026-01-01 16:09:02', '2027-01-01 16:09:02'),
('28e09bb92ac27816e2a73cbd66c8157ac617ef81abf9a04bbe7815e441c9f12991f64ee7a3f47210', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-13 18:00:36', '2026-04-13 18:00:36', '2027-04-13 18:00:36'),
('28f686987c29a832f7e3201a52d4c34594f478c5c400f319756b53eac06e86425b63dcd17a576557', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-13 10:57:35', '2026-01-13 10:57:35', '2027-01-13 10:57:35'),
('290c594d923900e2b5f3e735defd0ccd5d38ba06e1537499ee0dbac91ce989d811e8686de8a157ae', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-27 15:08:05', '2026-04-27 15:08:05', '2027-04-27 15:08:05'),
('2924d36294a66cccd4b0ad362a16a3fca5d1fc265ee1638e8de42e9f1289b46d0f1e2368eaf88b9a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 12:31:35', '2025-09-18 12:31:35', '2026-09-18 12:31:35'),
('2928a86c50101bdc935001d722af4c67a44598b880b4fe357fbc276936d3008a2029c82fa8b8d79b', 268, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 14:44:56', '2025-11-26 14:44:56', '2026-11-26 14:44:56'),
('293cbbb2536c6f7dd0c52f1295d929f260d0f29303fe60c4c9c6de2e5dce18f3dfe0de6ca51d2dff', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:56:25', '2025-10-31 17:56:25', '2026-10-31 17:56:25'),
('2975fbc70f214e5c0ea91603ec7b94493e686138f8a3a973f23f0bbfc364af5345f87e6ea091a5f5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-13 12:21:32', '2025-06-13 12:21:32', '2026-06-13 05:21:32'),
('29de13b520c152b6a2cc9621007c4d1cbcc14200c585d7506f90b5b875d79cd87abb9af3f191d746', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-25 10:29:13', '2026-03-25 10:29:13', '2027-03-25 10:29:13'),
('2a3ae675b8348186e9d7f948bf8ba5448d46b79d57fb99ec975c2b9525927801cb93ce87847ba6a5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 18:33:33', '2025-05-30 18:33:33', '2026-05-30 11:33:33'),
('2aa9e4586d957e0d8437095895345dafba5fe0aa48147713b9ddb087d9d6bf81962d71eaeab3e360', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-16 11:11:15', '2026-05-16 11:11:15', '2027-05-16 11:11:15'),
('2ad2382d873520603ec3832e1eff95991c329e8b38efc0f9c36f49215f1e22bdaac0b01b1430de3a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 15:15:30', '2025-11-04 15:15:30', '2026-11-04 15:15:30'),
('2adf6343c5845fe5603cd626194a000cd8d17324e7b4444ba9d1405fedafef701be2478fdbd2e1be', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-16 09:18:06', '2025-09-16 09:18:06', '2026-09-16 09:18:06'),
('2ae5ade2e52abfd1a2bde1e009cd1d96cf7d147e8f73b0e65c2af919b2d79695d396efa30afee27f', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 14:49:25', '2025-11-05 14:49:25', '2026-11-05 14:49:25'),
('2b0429588feedca563dd440c1b03450b98f7f850c3d7b1860ea7c2f0a8419afa81e96fd01405aaba', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-29 13:30:09', '2025-05-29 13:30:09', '2026-05-29 06:30:09'),
('2b3819e2f28481a15d3078914aafc0bb863159f92a953bd1cb0e78e67121e32432359b5cda68f92a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-31 21:00:53', '2025-12-31 21:00:53', '2026-12-31 21:00:53'),
('2b45af310faa3ec01f39fd64e27ac6f60f582e8244a1166fa84f66183d7e861b8787065bb3521d9c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-25 07:19:25', '2025-08-25 07:19:25', '2026-08-25 07:19:25'),
('2b760c68a66871f9a77a02a9713ae817fad19315d0170558fb22f0f90cf2fe382e6ca5e3ef6df914', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-06 14:48:57', '2026-03-06 14:48:57', '2027-03-06 14:48:57'),
('2bca52d0b5a6abd88d12b65401f7f4162c0a208fcbf7c6688163e7756924bde0a4f5cd376e6759a2', 22, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 11:59:12', '2025-07-16 11:59:12', '2026-07-16 11:59:12'),
('2bfe4ca9548b25016bac9aa00c7e803bea21d0baa954270657b19483ecf6e41608fcafbb32a58607', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-13 12:19:36', '2025-06-13 12:19:36', '2026-06-13 05:19:36'),
('2c30acca8ef9e129eeef594bfabe511cdaf72172ed03f705702a8aaa5f28eabfbce2f0c113d0b9b2', 328, 1, 'LaravelPassportToken', '[]', 0, '2026-03-10 18:38:03', '2026-03-10 18:38:03', '2027-03-10 18:38:03'),
('2c314a2f5c0957676086dd6f725a20028e5fe4b399e4b19ed79ad502463c0178ded271f8978182ba', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-20 11:57:28', '2026-01-20 11:57:28', '2027-01-20 11:57:28'),
('2c3d338b2073999a99ec35e60d657bffee6ecba87101535c5914fa0f20b14eefebb98f71a0723f32', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 13:12:12', '2025-09-22 13:12:12', '2026-09-22 13:12:12'),
('2c41faf7f4166864af15148ecc593367bf9f75edebc8f6fb7a191388e5d57b84491c3edde252dbac', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 11:04:59', '2025-11-06 11:04:59', '2026-11-06 11:04:59'),
('2c437ee679bfd2f25a9459fae730566ee1241a5a09d9742c8daa6fe9dbe5921487ae6de111569d02', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 12:25:11', '2025-09-17 12:25:11', '2026-09-17 12:25:11'),
('2c6f7f7c2b08a970d853f501b04554aa07e1e2626885eb23e950e8b7bae917482d4a674bd22f9fc6', 12, 1, 'LaravelPassportToken', '[]', 0, '2026-04-15 19:05:14', '2026-04-15 19:05:14', '2027-04-15 19:05:14'),
('2c881e40c5680883b548158cf0cd2793ce0d3983ae58759a75d02044ec055c9103b02dc49acf118f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-27 09:15:17', '2025-12-27 09:15:17', '2026-12-27 09:15:17'),
('2c8964fa6f3d5a4418b64b3d83bdb60536f477944d8f5ddf6b3be718c44e8e4a456b77e3c21b5d60', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:09:22', '2025-11-12 12:09:22', '2026-11-12 12:09:22'),
('2cc2d3259cf9ebc7418c8362a1a81ede2a95f68a547fdfe08b83a135f80983d9c52fc15437ef4856', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-17 14:01:56', '2026-04-17 14:01:56', '2027-04-17 14:01:56'),
('2ceb5fad236779ec79bb7d5e758b86eb2bc6f2db0638f7b53db330b578196edd1c263867ee2d7a0d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-28 15:41:45', '2025-11-28 15:41:45', '2026-11-28 15:41:45'),
('2cef1a0f73c6316288de69d3f108ccff3178241bb7aaae91b39cf66e53b55872e98fb0acaa44036c', 209, 1, 'LaravelPassportToken', '[]', 0, '2025-10-16 04:23:24', '2025-10-16 04:23:24', '2026-10-16 04:23:24'),
('2cf188be392ab01f18f8d7486829d564417f873342335c6cb8e7f3b7fdb24b07435a002b2fc54772', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 10:20:01', '2025-08-28 10:20:01', '2026-08-28 10:20:01'),
('2d192843e4a27c9050f1e9e077c594cb6a66683bbfbd6259c3a0dc7cd47790b00a84e3863e2e016f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-07 13:39:44', '2026-05-07 13:39:44', '2027-05-07 13:39:44'),
('2d2d51c8262cc85f180301b39845bd69cd4b7269bd77e096b76db402ed0a123c6e7c8c50b486b0e0', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 09:54:04', '2025-09-22 09:54:04', '2026-09-22 09:54:04'),
('2d570dfc0c8cc329bfac351ccb08689a817cdffd5e117b3bbad6ec0e2056c068f8605fed2e516dd8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 15:02:52', '2025-11-20 15:02:52', '2026-11-20 15:02:52'),
('2d5869d1d34ebcfadeaf951997d69664e2368712d441de60795d7d3e0c6a12a843257c988170eace', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-16 10:17:21', '2025-09-16 10:17:21', '2026-09-16 10:17:21'),
('2d616db3edd442d3a9929ea659eecf928e6af27a79c62ff3683baaed695d76abea59b74c4ed14b6e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 16:41:09', '2025-11-20 16:41:09', '2026-11-20 16:41:09'),
('2d8a93bc90b1227d9f34a69c46f62323d0907649f00d43442051e8354504db75f6849a5bc2d6dce5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-10 04:55:46', '2025-09-10 04:55:46', '2026-09-10 04:55:46'),
('2db85d83597b4315054d0f8e156556db3b0b89608c972759ab98fa226893c76fb94233deff80c5c4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 16:26:32', '2026-03-24 16:26:32', '2027-03-24 16:26:32'),
('2dd142d7f09700b5adf385dbe29789e9e8ed90c1c8afe6c60f5e41f774d6b99b0d2bef1214f2eca9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-19 10:09:02', '2026-01-19 10:09:02', '2027-01-19 10:09:02'),
('2dd9c6d45cf10f6c682b954626eb7cae05015043eaf3d36ca60b6a90109d89d86dc170436bda2e92', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-23 15:35:01', '2026-01-23 15:35:01', '2027-01-23 15:35:01'),
('2e0ea12e5059c7e3ba17777c7d039990765f7534413ebbd3ecd4a005be7b267f76dd832b2f8ba0e3', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 18:37:53', '2025-11-26 18:37:53', '2026-11-26 18:37:53'),
('2e10e4eb0fe160111cd15cf4d60195d1be5b733b39b97c395ff5b09ec4404707500d0cde8812f242', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-13 00:12:35', '2025-06-13 00:12:35', '2026-06-12 17:12:35'),
('2e4dec65e1574c7604612b58a3eea57d6b2482fe62c8dbf676a4766d408d1758d92ffad72db6d462', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-15 16:59:48', '2026-01-15 16:59:48', '2027-01-15 16:59:48'),
('2e863dea99747ef153783eedfec8942b4532e827573f30041ab7d180efc67a3ef98728e1c0b08155', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-04 05:01:36', '2025-10-04 05:01:36', '2026-10-04 05:01:36'),
('2e9c17bade89383cdc9787f22fc79f4240b3854ca6c41da04391b9cb13d75cf41675fe8f43ae57cd', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:30:27', '2025-11-05 13:30:27', '2026-11-05 13:30:27'),
('2ea2143d630165693fb955c8e82e19b75c545b08a7cf51e30330cb6f290591ad504e992e064f08b9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-10 15:27:13', '2026-03-10 15:27:13', '2027-03-10 15:27:13'),
('2ed093ee6fb38205a7f3e463916ec07c8d0163904d99e262188d01e16f7789c7be67e9d8a55cad23', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 18:29:49', '2025-06-10 18:29:49', '2026-06-10 11:29:49'),
('2ed7a081df5826891f7e406204ec8962583bd88aa17d66940ae36c525e68dc6ce98c342d0f0f0ee4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 12:24:27', '2025-09-01 12:24:27', '2026-09-01 12:24:27'),
('2ee4d6b798298e2bc7bcdecc538bfdb741a0a40072cfe8ab0cdbf137ca83c6134f1e3a27b86fe5d7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 10:56:03', '2025-09-18 10:56:03', '2026-09-18 10:56:03'),
('2ef31523ccca43932a99971a18619938fbe145644ec69b528950324d325fcd42f56ffcdcd6b75e51', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 12:28:51', '2025-09-09 12:28:51', '2026-09-09 12:28:51'),
('2f4abeef6d2846ddff57e7443e365830e3b47a6b4f0afd88ba64008d65ee3e975bb2b7e24ed0ac37', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 13:36:04', '2025-11-13 13:36:04', '2026-11-13 13:36:04'),
('2f58df24b1137361b1c2f53b7ba1bed22125a86eb318a66bde7dccd85663e81aff6b623972b4f2a5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-26 04:17:17', '2025-08-26 04:17:17', '2026-08-26 04:17:17'),
('2f8cc3336732d1fcfb52419415d6facb70eee0aa1bec35e05d828f7f310b3b0b1eb18f644c8eb938', 346, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 16:57:58', '2026-03-31 16:57:58', '2027-03-31 16:57:58'),
('2fade0587e9a39f1d49e4f07b206c7ea013b2a235b5b97b34f6167bc0b0af6ad5567a949ae63f321', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-09 11:50:12', '2025-06-09 11:50:12', '2026-06-09 04:50:12'),
('2fbee25ab1f7f2206882bc09b015e94a120210e755d0140e9d7e5403fa77f1791664cff363570004', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-16 18:40:18', '2025-05-16 18:40:18', '2026-05-16 11:40:18'),
('2fdf0cb5f196a86b65b5dda0682fb1584884d22828b07a86aafa65bcbfaf12aa93fcbcb389bb0e52', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-20 15:00:37', '2026-01-20 15:00:37', '2027-01-20 15:00:37'),
('2fe95609be8d762915a75a48f271c684c7c13813a708abe4a25381f9caf9a6860ea0df89df1b80f3', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:40:59', '2025-11-05 13:40:59', '2026-11-05 13:40:59'),
('305d26f258ec927a98c82e71ba25fd39d7b411dfb216f8bbb85bfbc0d814d1d4aa27167ee14c621d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-30 15:20:18', '2025-12-30 15:20:18', '2026-12-30 15:20:18'),
('30bf0fe72edc2fd43cfca90d2465f442bfdc43590478c99128beac195714bfb8ac7befe5f94955f1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-30 17:26:26', '2025-12-30 17:26:26', '2026-12-30 17:26:26'),
('30c876dd7ff277610ae4fb0cde7bf3421af534fe0fce7be97de31640670663c3dbe00f4ae80f377f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-28 09:35:28', '2026-04-28 09:35:28', '2027-04-28 09:35:28'),
('30d4cd1d95269316b4b3ce9fa5fcc6aac95fe9db88edb65737362bb87399b0e07fe88e77bc127aa6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 15:04:53', '2025-11-05 15:04:53', '2026-11-05 15:04:53'),
('30da347e7a1fe4a28d35f48f6b65fe1cc5bd6a599c12563ce2be6dda501269fe8face707112016c1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 13:38:46', '2025-06-11 13:38:46', '2026-06-11 06:38:46'),
('30dc2f0d1eedd03e978062eba2b087f23d691518b5e663c6535b1e76bc669dbf650f992ab5117307', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-06 12:56:43', '2025-08-06 12:56:43', '2026-08-06 12:56:43'),
('310e8e99cb2cb85215b3605004f98ceab8a9748658aaf51a2b34e3d642cb0fc62c4b3cbbd0ea2136', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-20 12:20:08', '2026-01-20 12:20:08', '2027-01-20 12:20:08'),
('311b90be11faf9b53fd45412c3123163396eba4c182096f4adba3823d2377b7a16e6c83b35649f99', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 14:43:35', '2025-11-13 14:43:35', '2026-11-13 14:43:35'),
('3130c33e4118b8f3c6c0e159ef6fed1e1c4686e925cfe50d5b307d36107e2ba2417b65d44cc0b426', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 09:15:25', '2025-09-05 09:15:25', '2026-09-05 09:15:25'),
('3151827cbd0eb51664777c727b2374e81f5243940e77e3865edb5fea97b032c08f8712f15e10f60a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-04 18:57:53', '2025-06-04 18:57:53', '2026-06-04 11:57:53'),
('315a272ada5445dc355343b33b2da90d734439bef54aa1d6444906ae3dda2494e0d317db47869bc1', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 14:38:12', '2025-12-01 14:38:12', '2026-12-01 14:38:12'),
('31688d03f7569a172ebd016563cd29102fa68dbbc531ea2a37bc75a097c5380e2225595c4516667f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-12 13:12:28', '2025-12-12 13:12:28', '2026-12-12 13:12:28'),
('317d9d71c9b875d126fa18a0dda803cdfcbffc7e2d6feef6f3b8fcaa7055dace0ee8378a75790255', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 13:35:42', '2025-11-13 13:35:46', '2026-11-13 13:35:42'),
('31a566298ed8f0c6c5f0e54b87cdda9a04df488d9e1436fbe950e56d9a3157654519f41121030926', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 11:01:59', '2025-10-30 11:01:59', '2026-10-30 11:01:59'),
('31abcc63577f2a7ec7d4271f25216a6680d214f42522dbc0440b2d1ad16cdd3ac63554c9631db0c2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 04:25:48', '2025-09-18 04:25:48', '2026-09-18 04:25:48'),
('31c5bd4124b2a58243f254b14915f95a8e58e2a67bfbba2d1868626b506c4c2897060c9e63ebb353', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-16 22:22:59', '2025-05-16 22:22:59', '2026-05-16 15:22:59'),
('3213c42e20126f56760daeb722f67bb351ed2e508b4f6a76294f41e327d06671b15a5f0f5202159f', 268, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 14:39:00', '2025-11-26 14:41:45', '2026-11-26 14:39:00'),
('322ebbe246db48ffaa96d113ead60140cf48ccecd1fa46171db1f569ed768b9119a01c5f6a53adfa', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-06 16:16:32', '2025-06-06 16:16:32', '2026-06-06 09:16:32'),
('3232f789facdae12e3e86da30077ba3abce431aa4c9ff4a161757d9bf90db3ce2b0b41fdf6f3452d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 16:53:24', '2026-05-25 16:53:24', '2027-05-25 16:53:24'),
('3239c8dcb6480abfbd96eb77ec77ec6446adf604f642da1b347d474a3d339a627ac8412196975842', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 05:28:35', '2025-08-28 05:28:35', '2026-08-28 05:28:35'),
('323c8a93750a12c963b1cbc2ed1f2075109f81473fe2661c7999ee66b0313e54402bcb088af48343', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 15:14:44', '2025-11-13 15:14:56', '2026-11-13 15:14:44'),
('323fdabffc3faaee7adadf365f6b8f0553dcf65cf466b972841cdcba47dfcf8727e3ee6f13e0205f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-06 04:45:28', '2025-10-06 04:45:28', '2026-10-06 04:45:28'),
('328412192856eb3335d0fb6fa5e66cf3f7d6ee84ff2750fce138dc415d38fbaaf52c23a9380eb831', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:35:58', '2025-09-01 09:35:58', '2026-09-01 09:35:58'),
('328955ed87ff097819d7ae9fda01bc8e989e32fa13b9fe54ba9d369819bdb57dcf7a3fc2acbffd8b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-16 11:05:34', '2026-01-16 11:05:34', '2027-01-16 11:05:34'),
('32947a8cd661307c5d04064a548e54bca25d52ce363a9832bf887f06c7f0586fb20b13327caf7692', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:06:28', '2025-11-13 12:06:28', '2026-11-13 12:06:28'),
('32aec6bab1b9f899c952cfe18f40fbc8d83f5c604f1dc7d913a2b1ac49a90287ea9c0f4607dfff7d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 04:24:54', '2025-09-01 04:24:54', '2026-09-01 04:24:54'),
('32b5fd24ed73e2212e9a371f2464c2c4026ddb411753606762e29673266d4e440897b1ee275daa98', 19, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 14:34:26', '2026-05-26 14:34:26', '2027-05-26 14:34:26'),
('32c7bf3b1dc846403d7ef7b97cc128c88901e6c0c67c79b924ae387c93b7eddf8ab966d7e24f4830', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 11:07:49', '2025-05-30 11:07:49', '2026-05-30 04:07:49'),
('32db2f910d4105f30b66b7edd04b81faa6602a71964357bae21cee38ec828a4d8172823d68a795ee', 316, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 16:41:28', '2026-01-29 16:41:28', '2027-01-29 16:41:28'),
('32de509bd2d8286d487fea4b2d493fe012431734eb64305566d42dbdb6f44b69faa877f509175a5a', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:18:18', '2025-11-13 11:18:18', '2026-11-13 11:18:18'),
('33427420525e3607ca502a42676fb7c2f9ea48bd752100c894a8c99c9bc8bb1ff78710e2d52b277d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-23 18:46:12', '2026-02-23 18:46:12', '2027-02-23 18:46:12'),
('334bcf2c02274732344f6a0af2535cfe93de189c5098000913826e0ac85d03789b85b61cb2847ad8', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 12:39:49', '2025-12-01 12:39:49', '2026-12-01 12:39:49'),
('3354fed2b61527431b035db15f5933937adff3a365c98755681d8ee3e4adcb2f45fbaf4443ed4f62', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-22 09:58:56', '2026-04-22 09:58:56', '2027-04-22 09:58:56'),
('3386ef187870f6b2e3071e4725d523f49809ece6849444f1d5a8d2f4d6c09e445587105d944d3667', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-15 17:00:01', '2026-01-15 17:00:01', '2027-01-15 17:00:01'),
('3387cc9a27ca1a00da342a119a46b226ec765495646b4bcd0732a30d21ec05c9160227966f42b128', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-24 11:32:35', '2025-05-24 11:32:35', '2026-05-24 04:32:35'),
('338b21d32f6902cba2ee0c5132405caf8332ffdb2e0428f9ed2f98f13fdafb0a44e171146c02932b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 16:30:28', '2026-03-24 16:30:28', '2027-03-24 16:30:28'),
('3391606fba78c3fb15cfa3a5b58a4c734b35367a837c302c32f971c16a059048e8478d575acb406a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-18 11:34:06', '2025-08-18 11:34:06', '2026-08-18 11:34:06'),
('33aeda21b0e32e8a210058b2ecf29dbe8474900e019025859a320c5cca3c322f9c68d70e9cd736b9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-01 04:44:43', '2025-10-01 04:44:43', '2026-10-01 04:44:43'),
('33b7ab7916d00f285e18dcd86f1b751e28211da60013e5a0012bbb10670e1f32ddd669afbbc262cb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-18 11:02:59', '2026-03-18 11:02:59', '2027-03-18 11:02:59'),
('3418bca57e2decbb5408f9c913f32a807c9999a3395e428c6052daaa1046bc3ab4cae2ac96648272', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-05 11:28:04', '2025-06-05 11:28:04', '2026-06-05 04:28:04'),
('345701f93c606eef39be4d13fa6c828f8e111d793193fe4de1c2475443b7172c36d3051e412a742d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-09 11:10:07', '2025-12-09 11:10:07', '2026-12-09 11:10:07'),
('346dce54dc1a1715a017a0eed0f8dc00bd22cb1b5ccc79aee4857d0c4cfdee711b66f57eb655123b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 07:10:32', '2025-09-18 07:10:32', '2026-09-18 07:10:32'),
('3477e135f13c335d9a3e7f08494123585e352b7c412e9687702d1845e50fea830177b589d73e6f93', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-03 11:38:43', '2026-04-03 11:38:43', '2027-04-03 11:38:43'),
('3492d19d9fa0a4aab52464bc89275122afc749a0c25a97639198af6e7b4b9ba32b6daefbe9f87ff2', 328, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 17:18:41', '2026-03-16 17:18:41', '2027-03-16 17:18:41'),
('34e9734940014b600e5b021d351957cb5eb3530ce42c130b7fbd31340a2d425cce23bd41989bb16e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-16 10:28:08', '2025-10-16 10:28:08', '2026-10-16 10:28:08'),
('34f96107cc48a48ef63098d0b91dda79e3b1f81ba36a6076258588918740e6324486543aa25e9fae', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-11-29 14:29:10', '2025-11-29 14:29:10', '2026-11-29 14:29:10'),
('350b2bf573e6d3baad26a6ff5a45a1ea13bbf13712dfb919cc628310911ca3d9c726363703f975a8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-12 04:06:15', '2025-09-12 04:06:15', '2026-09-12 04:06:15'),
('350f2b7324276f3aa2a06e2b91f8f27653dcc25b5278dc1b54c4040b7787822956f49383011d4758', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 04:37:53', '2025-07-16 04:37:53', '2026-07-16 04:37:53'),
('351814f22072135bcce588ede63101d8f0434365eb3ca5f9575f78c719a2ea9f1805f130f94a060d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-27 16:47:33', '2026-03-27 16:47:33', '2027-03-27 16:47:33'),
('354b9687e9f6bddf33513ca82d4c5920392abca0dd06418fcd492f420512cbf93f2cab6b6dc81917', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 17:48:54', '2026-01-29 17:48:54', '2027-01-29 17:48:54'),
('3553ebf25f493ce73f09b0abde9af8fb3d27adab8870c0b29fc1944e57574b08128923f537a8b264', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-01 06:14:58', '2025-07-01 06:14:58', '2026-07-01 06:14:58'),
('355f7e4e7ea17f2153caed5e48ea20949c7767f80ea9d7d84ee0b094afcd5b1fcaa46a2ff941c8d7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-06 17:27:02', '2026-05-06 17:27:02', '2027-05-06 17:27:02'),
('35600aa108e9a3b89ff6c599c1c65aecd24bd46ef1f9d9e1f7a1cb8e30c9a5e0dd0a3f7c7744c53a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-23 10:15:21', '2026-01-23 10:15:21', '2027-01-23 10:15:21'),
('35698cbcb668934a70b3212d0beb72719319a8f907443f7da51e669b2b080823c14ed9f5cc74ef00', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 11:53:51', '2025-09-01 11:53:51', '2026-09-01 11:53:51'),
('35722e0780c2ec0baa7d7d10979acacb33d32c724b13a633e40c6197d2c4f61b3b0e59a52d5b6d72', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-24 10:31:24', '2026-04-24 10:31:24', '2027-04-24 10:31:24'),
('3582bb3068fa2cbf04f15f18c0cd3888194b81db903e65287e264e2fb6cf9a76b2815e747b132c15', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-21 22:16:02', '2026-01-21 22:16:02', '2027-01-21 22:16:02'),
('35b090332fb70fa3dc9e745eb3e2635198bfac596e9615e8ab08d53971f8d2fa743841b645340d64', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 19:27:49', '2025-05-30 19:27:49', '2026-05-30 12:27:49'),
('35bf31090f1180f49e43ecf6fd6a30f5835905ba3fe111576cd2ae5e094570d9af46e0b91b0bfa85', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 18:19:59', '2025-11-06 18:19:59', '2026-11-06 18:19:59'),
('35bfd4e826868d3670acd4bf3200087ef5119cd6a9d0bca5e6a3b33f288fafee0bcb4219cabe784d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-13 15:52:32', '2026-03-13 15:52:33', '2027-03-13 15:52:32'),
('35c9d267704f1beb90b4d333d2a3a7c6406a6745fcb9c1a6b64937219dd5a48fbe170007745675ce', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 08:24:53', '2025-09-01 08:24:53', '2026-09-01 08:24:53'),
('35cd2c113f704ee6f33270408d76ec522c1127c13190dc72a94ec5ea5bc300b493bab4ac11311ebd', 22, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 11:40:18', '2025-07-16 11:40:18', '2026-07-16 11:40:18'),
('35d2141997031ab58e0303b579086b1e6514dfecf40b872a33b4d57b81332e6b77700bb49c69b557', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 11:12:12', '2026-03-02 11:12:12', '2027-03-02 11:12:12'),
('35f75e3f328d6cf264bd4c8e5e1a03dae43d25409f854085a32070508f0257af6cb573274c831349', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 10:21:02', '2026-04-06 10:21:02', '2027-04-06 10:21:02'),
('35f87384226a5eb0499ce02632890c4503407352d4ae74e7b017219b6c05c6cba8c42b1560d4c7eb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 10:04:42', '2025-09-25 10:04:42', '2026-09-25 10:04:42'),
('361b89c6284eeabed70925279e37eccebb1d0f20559c9fa49fef953576c95721326e94f256812388', 23, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 12:13:12', '2025-07-16 12:13:12', '2026-07-16 12:13:12'),
('36349aa95a4d691fd5895bbae14f353d5fee0224f271e8d5957a43260d1d17f03e710992e1e0b9d7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 12:02:51', '2026-03-02 12:02:51', '2027-03-02 12:02:51'),
('365dcc82a6131e3d8012088064db0097bafcc210f51535069fd56897eb01ef57d45cb18137791fe6', 127, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 06:08:02', '2025-09-18 06:08:02', '2026-09-18 06:08:02'),
('367c1b3bdbc5934869776dff31e9b679ff7ac550422c4b77af8929e0afe72a6c1089587a7faddf2f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-15 22:38:36', '2025-05-15 22:38:37', '2026-05-16 04:08:36'),
('367ee8b058e528fbd3031f0c1bc0d77ecdc2fbeb281926e0ef66ca6347a927d6feee0d843b2563da', 196, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:12:57', '2025-10-31 18:12:57', '2026-10-31 18:12:57'),
('36c3400f580e0b7d6bbb8bc33cf73e6dfc2fc1f128eda68a7e43f5369b0ce6baf0477c9b5b2d6cb4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-11 12:35:52', '2025-09-11 12:35:52', '2026-09-11 12:35:52'),
('36c36f8d1f78b1a53de174c90a31a3f99e44c218ea5bc50f076ac66f1d0f9e5f2c1ecf2bc8b232ac', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-03 13:41:30', '2026-02-03 13:41:30', '2027-02-03 13:41:30'),
('36cdc3e4b4bab54a60d5f78f0c03683789d85dfc5f48edb722deb3fa21c697f398c18d5590b746f3', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:16:24', '2025-09-23 05:16:24', '2026-09-23 05:16:24'),
('370191a4682e95516d6fc73ac796015bd90470151df137d4c797110a2bd0237d97d759db2a9ac582', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 16:59:45', '2025-11-13 16:59:45', '2026-11-13 16:59:45'),
('371f2ee9f3811dc5411f4f2b4da8c807fe3ebc06f780d5ff6f1b26a490abcddb03b2f9b752f2ea43', 196, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:12:56', '2025-10-31 18:12:56', '2026-10-31 18:12:56'),
('374b2aad8cfe3c5af389815f876a073964fe5655eb2fdeaea50b200a3c4aa11d02a3d8af22c0d2c5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-12 10:17:55', '2025-09-12 10:17:55', '2026-09-12 10:17:55'),
('374dcde62af990e1661222bfbf8493afda6a70ab108e17dc3caf5a6593c189acb0d2335055fef7a0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-04-09 23:04:16', '2025-04-09 23:04:16', '2026-04-10 04:34:16'),
('378f3e11172c6db5c8e776da72697ad4e8386b642f747a78880cf96ff178f66385e54619d9a539ec', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:27:16', '2025-09-01 10:27:16', '2026-09-01 10:27:16'),
('37b9685c500c4287aa6e5772021c1e295b8bda32e40600e410c63de30f8cdbd557b8a5ab83c46af5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-21 11:53:16', '2025-08-21 11:53:16', '2026-08-21 11:53:16'),
('37d5c8452190351e07ef1263f22c087f848086ab8b8ff84194ad2d994ef3ec38a2c341c5028d7a7b', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 14:43:18', '2025-11-13 14:43:18', '2026-11-13 14:43:18'),
('37e30129f0bc9ec3f6f3c3f9e5e11215f9e1fb9ea4689265f80121e92c79cd0e4ba19776c714c04c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-05 17:37:12', '2026-03-05 17:37:12', '2027-03-05 17:37:12'),
('37f8991372812875a212725b5fd6caddf94438a6769a708a05e37528759725cc0a934971742d46a6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-06 11:22:39', '2025-06-06 11:22:39', '2026-06-06 04:22:39'),
('37fde6f2ca5ddcd4eb171ba4154302171ca977c29c5fef97cad935ba32fc823b280f71d915577483', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-03 09:53:52', '2026-03-03 09:53:52', '2027-03-03 09:53:52'),
('3802ec996f80a3c679ef4020b1d4736f40e17399db452079647d5dcd705ea44f723354f8546bbcaa', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-05 10:10:09', '2026-01-05 10:10:09', '2027-01-05 10:10:09'),
('380e265da2f432a32e3bcf1231c18cd7bc416dfcf6ccf3ccd681ae0dd3c4327d6c255e9011f0ef57', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-01 17:06:44', '2025-07-01 17:06:44', '2026-07-01 17:06:44'),
('38325e3c6e46a13a95de5c85d3a151a9cc975d53ecd74e5ba3f27fdd2a963cd6b1942ed1929979ce', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:47:18', '2025-11-06 10:47:18', '2026-11-06 10:47:18'),
('383958c46b8b817085f366aa5506c75de614f1bf4d9db4ed16b332c1d1bf9d7d74bca27e008d4b2f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-25 23:34:56', '2026-02-25 23:34:56', '2027-02-25 23:34:56'),
('38660fd3dd6399a92cc1b7aa1d6cd7006c848c9ffa2bb465a1785e9cb28c5e49ea81117f42171596', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:09:27', '2025-10-31 18:09:27', '2026-10-31 18:09:27'),
('38735c3843cdf9767078c97925d2049b79ab3d9d92ec8094105ba569989b2f5071aa8787e28373f5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-13 12:43:22', '2026-03-13 12:43:22', '2027-03-13 12:43:22'),
('388943eca16da63a3627bd4eb036a8e139331fe4acca2517c09df56da7acd9733c633d6a6fc3160c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-03 11:05:32', '2025-06-03 11:05:32', '2026-06-03 04:05:32'),
('38a1d526ed7528ca361633ee43b7972904bfe4d0aeb70aecc8673a85bb4ecb4dd29bf37d09a18f83', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 11:47:43', '2025-11-03 11:47:43', '2026-11-03 11:47:43'),
('38a38cb35eec41c200b0f37c6c733f7881cf4b0ae248ea083a94edda5c1ee8eebf2df78af4d81c6d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-26 00:37:32', '2025-12-26 00:37:32', '2026-12-26 00:37:32'),
('38a8dbd690aac57a0c1e410ca411a9ad9bc9e3d534713be5798029fca543d089a49ee9e8e3c2ffc8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-09 22:17:26', '2026-03-09 22:17:26', '2027-03-09 22:17:26'),
('38e3e8d3908071d5253a74ed04eeefc9efbfd218245ed117375169f868caecd7eab725318d2b7549', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-29 06:07:30', '2025-09-29 06:07:30', '2026-09-29 06:07:30'),
('38f98819b714ea63353126c5ff2d9304f7142587cbcae2b4ac74b218c9fed4f619c77d00e0a3a984', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-09 18:39:29', '2026-01-09 18:39:29', '2027-01-09 18:39:29'),
('391398597993ae56542d2f530fa7560638f399057f0a3218ba3c18f75dbec2b363b668bef412731e', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-11-29 14:24:43', '2025-11-29 14:24:43', '2026-11-29 14:24:43'),
('39326c114c4bafce6124bc07bb1ad2c64244f55d231a7dc0700534278ba014fcf3727df6a353da9c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-19 18:13:53', '2026-01-19 18:13:53', '2027-01-19 18:13:53'),
('394674545586616f7300062dbcefcc206189cf24751459e55c7e7bb41f27ec6634b15d3a2772b12e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-12 17:56:10', '2026-01-12 17:56:10', '2027-01-12 17:56:10'),
('394ff3221566ff5ad8cbec0837cd14e20c19f379b02211b9c09b1e20a1df28dd50e21133378c67fc', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 10:22:12', '2025-11-12 10:22:12', '2026-11-12 10:22:12'),
('3963be820646d31a212b8018d6609ecbf69033390ce1c91a6dc5870e701fa7b675a7bd15be52c59c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-21 11:07:38', '2025-05-21 11:07:38', '2026-05-21 04:07:38'),
('39889774618157b236fc964350052bbf352aa30fd1cd0b13c025ef0a058f0b3e2f5c20621442ae0c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 13:36:16', '2025-10-30 13:36:16', '2026-10-30 13:36:16'),
('39d5584ed9c5999e8764d2e7cc9c3fabe6713ab84270b9dc53f39b925fe78e0c2c72acccec18063e', 76, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 07:03:26', '2025-09-18 07:03:26', '2026-09-18 07:03:26'),
('39eccead36cfee52b0829351f778144f4274405e8e7105d41ff4dc19c0c1a9c8741b97cc7a9c79d2', 288, 1, 'LaravelPassportToken', '[]', 1, '2025-12-01 11:26:24', '2025-12-01 11:56:13', '2026-12-01 11:26:24'),
('3a0aed26bc343cb9248f17c6c5220851b9347f3c8cdaffc34a02001f666d4e4163bdb0b976742dce', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 12:00:29', '2026-05-26 12:00:29', '2027-05-26 12:00:29'),
('3a12e9db13a8abfa7b522b842dc24d4fffe6fe24b0cc45af18ca1523b0bd947c6ab23a40d277e31b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-06 14:36:32', '2026-05-06 14:36:32', '2027-05-06 14:36:32'),
('3a1525018366c1c296706c65da7f56f635bf3856d859ec47cee4b75a276c12b2371527ecaf109f7e', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:16:07', '2025-11-03 12:16:07', '2026-11-03 12:16:07'),
('3a371067741b471fcff3cbcdc5d1f481b75e53f51f07d79b0c2879609d0f6ffdad4f7ace321132cb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-29 04:20:22', '2025-08-29 04:20:22', '2026-08-29 04:20:22'),
('3a79e1f5333025166fc8c8943d044475f53c39be16b59e50d9a49f7d3f243ded7bc9bcf646e6b3b0', 179, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 15:18:28', '2025-11-11 15:18:28', '2026-11-11 15:18:28'),
('3abb141ad2b3a06203262fe0933c5af14ab8253f8537c8f4567a8af22f0f4cf2866c12e8ad655493', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-20 16:02:35', '2026-02-20 16:02:35', '2027-02-20 16:02:35');
INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('3ac0816245f4ef2b3688ea5a238998d1089ca7ec1e55aaa3d86a2dc71b1fe7929ed7f92cff57a318', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 13:28:24', '2025-11-06 13:28:25', '2026-11-06 13:28:24'),
('3ae903b73d361a951c60260a066f41de7f869adda37674e264a4915dc239a225379f27284ee4562e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-26 17:51:44', '2026-01-26 17:51:44', '2027-01-26 17:51:44'),
('3ae98d013f1976c0acbac6d9ce4e1f51920e941367f34f1fd21308a7b57eb8b5c3a3e2d0291a4e0e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-15 15:59:15', '2026-04-15 15:59:15', '2027-04-15 15:59:15'),
('3aeeebd1ee1288261fc69f4a5f8bf6c70641ac572e4129e78011c35ebcb1d6c80158721dcc65f97f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:40:57', '2025-11-13 11:40:57', '2026-11-13 11:40:57'),
('3b0d445f8097176e652aa5c21af0a053f2b0695db0e76e1fb81ce03612c45cf1a6f153e6e54d264a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 09:33:45', '2026-03-16 09:33:45', '2027-03-16 09:33:45'),
('3b259dd455c8c52ac83442d59d659b694eed8a6d841ab04ff6d544b70b967ea453a7558f851716b9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 10:37:39', '2026-05-26 10:37:39', '2027-05-26 10:37:39'),
('3b5a4093acb351b7221d345f80e158fa4a4d6affb0c68a340ec2f0f617eb7732ac53939c766a40e8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-24 17:03:59', '2025-11-24 17:03:59', '2026-11-24 17:03:59'),
('3b9e81057e92473362bfcf0c11780a5eaa5faa57597b4a462e6c0a65079458cd47c35d96cfd78036', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-19 11:22:21', '2025-05-19 11:22:21', '2026-05-19 04:22:21'),
('3bb3b917b5dcb40f9fba09ebe2c79dda72744a0ec42478ee96e88c142f2e3b575472693d831959d6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-20 14:37:33', '2026-05-20 14:37:33', '2027-05-20 14:37:33'),
('3be290becbbfa16e0099a16e8f9347e9e4459b990a75a815bc2f16243ce253eda823f7108ecb0c79', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 12:36:24', '2025-11-04 12:36:24', '2026-11-04 12:36:24'),
('3be3862acd53aeb0206652f76b2cbc9bbd55f24741ca20a2f35aede846b92bf6b882c20cc81d288b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 12:41:07', '2025-09-17 12:41:07', '2026-09-17 12:41:07'),
('3bf75d2c3c23268a7ad3aa0ea5fd6c6e4279d2e07f3676452fece11cfe10eb3450be463dcb730212', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-31 12:28:15', '2025-07-31 12:28:15', '2026-07-31 12:28:15'),
('3bfb7c447345f69c854c9406bf205c263ec92f9e96d06f779d3b86b98fa6b255466fcc6e714e39e5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 13:44:01', '2025-11-06 13:44:01', '2026-11-06 13:44:01'),
('3c171f6d3ccafc9da49b0c305ef3ef4c3793fd01607dae4469914eb2806af45cc5c2fe42ccaa2696', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-23 16:08:36', '2026-01-23 16:08:36', '2027-01-23 16:08:36'),
('3c2310a663ee5b227fd884d933482e7805cc3954b752da9d2246fd793d5efaf40a1492cfd5607f2e', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:17:06', '2025-11-05 13:17:06', '2026-11-05 13:17:06'),
('3c395b7301f9613d990b5b55dc764466cf11f314d0ae991815cdbc8ca699e154b888aeb3d35b301d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-14 11:25:08', '2026-04-14 11:25:08', '2027-04-14 11:25:08'),
('3c8769960bb54c8a13db331a7b136fdc04ba0cc5242895788e0a7cc941b8e0653ca673e1098b8770', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-06 11:25:32', '2025-06-06 11:25:32', '2026-06-06 04:25:32'),
('3cf5c472ee0ecb6e0d5b8cfbf6171d83d6445e9fa19462c721434774ad1f9d3067e29cc93c825d72', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-23 17:44:17', '2026-02-23 17:44:17', '2027-02-23 17:44:17'),
('3cf65c6f7cf87f8b24c27e264cf4467cfe1c3ed246c832ea04fc426f3b2e74eb4bf3b914a5c09e03', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-08 10:15:51', '2025-11-08 10:15:51', '2026-11-08 10:15:51'),
('3d12bd3f651668e8a9fea533c009cf652147f5ad1827ab5fd9154d4b96998ae75edf572f7e1f92eb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-02 09:56:25', '2026-01-02 09:56:25', '2027-01-02 09:56:25'),
('3d1790dd719ca3721eb07afde891cb7150f35c35180e8e927c0c4b5e3cf0f7d71d3618bba5b0af60', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:44:42', '2025-11-06 10:44:42', '2026-11-06 10:44:42'),
('3d1da1bdd9f91282be915dc7c336c4efc2e17e127cea2e0789c9ae39d4e7d34cda515ed07235c6d1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-10 09:37:14', '2026-03-10 09:37:14', '2027-03-10 09:37:14'),
('3d250cd53ecfb8d4b3369254a75ab08ae7eaf56b6ac3ec3f3fd30890bf329827fa79bfb9acb64446', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 18:57:13', '2025-11-26 18:57:13', '2026-11-26 18:57:13'),
('3d30086efb059bc123e5fd8b5faf23035735699c43d95f305fea95176c81c4befecf3807c3fbc978', 331, 1, 'LaravelPassportToken', '[]', 0, '2026-03-18 13:39:41', '2026-03-18 13:39:41', '2027-03-18 13:39:41'),
('3d5134bc5e6135c7b3026a57223bdf35122fd3e63dcf961b6e737a92fdd4b95198fd5829690fcb6b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-01 11:27:00', '2025-10-01 11:27:00', '2026-10-01 11:27:00'),
('3d609c1e862e3a5914356712ab858112c5ad18acfd08eccf4773274645e4b6809b65d52936c8aef9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 05:57:58', '2025-09-03 05:57:58', '2026-09-03 05:57:58'),
('3d6799f7e1278351fe97497084971dae0946722ab9f60cb45b05aa02ef16f5a37fdcb61065f37753', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:36:10', '2025-11-12 12:36:10', '2026-11-12 12:36:10'),
('3d84c45802434dcce334e4999cb62badf28370656d806ba8e380f97a7a0f5734bd8de57f87775df6', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 15:23:35', '2025-11-05 15:23:35', '2026-11-05 15:23:35'),
('3da403617a6ab97fb65a4bab3c46daaecb7260b9e293409443ebabe6dcefa63f4ee149ddfa711a0f', 336, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 11:34:48', '2026-03-30 11:34:48', '2027-03-30 11:34:48'),
('3db568f632a57ce0d77c889be2f553d37076fd0a042272c1a74480bed2feb8794342634cd2404c53', 22, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 07:09:10', '2025-08-28 07:09:10', '2026-08-28 07:09:10'),
('3dcc81dc42a6f901be5a09aa7d6de25e8e43b32cd588fc3216aa837ee6a2cc43345c87d5977ffdf2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-27 04:13:50', '2025-09-27 04:13:50', '2026-09-27 04:13:50'),
('3dfc3794c6c72d9ee24b5647ee22eec061e10721fe1c1ddec91c2f9e4f921bec50d5a00f15a75e17', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-23 09:38:46', '2026-03-23 09:38:46', '2027-03-23 09:38:46'),
('3e3aad450e13f931a3510ddabee7d1ae92a9f618ae6356c10bf4b085acd5167236a9f2081fa15f21', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 12:20:10', '2025-05-30 12:20:10', '2026-05-30 05:20:10'),
('3e57ae8b08ab06ff900ac3f8d609234be69a636034b256beded84bbf17ddb968eb65cd57ba0db2e7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-30 04:28:15', '2025-08-30 04:28:15', '2026-08-30 04:28:15'),
('3e645afeb80630a41635a9e4eb20a606ff49aad17d053377ba32181730469404bca9e53a9d89854e', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:15:06', '2025-11-03 12:15:06', '2026-11-03 12:15:06'),
('3e7214fc1ef09061d8f2f8394ef47084a1b3c60b7e73991d3eec23a9ed8ff2c467fa3cff909d27ef', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 17:43:41', '2026-02-10 17:43:41', '2027-02-10 17:43:41'),
('3ed738bf4d7ea1c82ee4cfffffc9105d99e8b5336719fba0df5e9e49456bebd5318a0b5c734fd287', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:29:19', '2025-09-01 10:29:19', '2026-09-01 10:29:19'),
('3ef165ab22da7e0eb72538a80f93e9409af2289717785bb7ed281e6f2f62bdc6d7a218d3301abfef', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-06 04:48:58', '2025-10-06 04:48:58', '2026-10-06 04:48:58'),
('3f1deeb5d64779918a94b81f0cb5dfacda80662494e66faa85212adce1f6e3b1b7f56bc2c5bcf859', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 10:29:09', '2026-03-24 10:29:09', '2027-03-24 10:29:09'),
('3f6e814011ea68d4bc554f323728c9a43e0054f7750c049570cf368606029661656abecb480d922b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-25 18:06:08', '2026-03-25 18:06:08', '2027-03-25 18:06:08'),
('3f932bc90b6e590c9434c075a5255334c4b473fa44d16ea725ce0060aad00f7ccb724a720c9afe13', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 06:36:01', '2025-10-30 06:36:01', '2026-10-30 06:36:01'),
('3f956c2b09faddfc818a7f06631082e49c17103a0207effe7713848f3ce970dcde8edfce0cd1a64b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 19:10:29', '2026-02-10 19:10:29', '2027-02-10 19:10:29'),
('3f9ac3c6e2ef0fac2b3d56c01a5e160ccbd53e73b57ace5790d39344be2cbf27eba3c84a9a906b16', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-23 17:27:32', '2026-02-23 17:27:32', '2027-02-23 17:27:32'),
('3fa6e7054820288139a3a9681ddd3a1992de57fa7e567242f50927a047c1a543733b1082d2aeb25d', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-11-28 16:13:19', '2025-11-28 16:13:19', '2026-11-28 16:13:19'),
('4036454aa909313a93323a1e92d7bd6f077105c8b0882dad0947cdc096972744b7d1829432aa6a01', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-04 07:32:12', '2025-08-04 07:32:12', '2026-08-04 07:32:12'),
('403b5b0902ffe5a6faa0c03b9f77209d3b28bc97210b6b1de0c9215c8b9803f9626e0a2e59a57573', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-06 02:52:46', '2025-09-06 02:52:46', '2026-09-06 02:52:46'),
('40863346fc4b4e79df4b0e66e1b23d1831a19ce6768f5ca93d8eb23ccf265abfdaf8d4ea7da97dbd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-09 18:15:04', '2026-01-09 18:15:04', '2027-01-09 18:15:04'),
('40cdd0b2a5049ab99379c26ff53034d479a6207cb40e5ca4e4d0eabb5ff1e11087aba1e0c4c1e2b1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 10:09:01', '2026-01-29 10:09:01', '2027-01-29 10:09:01'),
('410417b8348449e5de7e961c20fb6a5e284376c1e402e831514f80356d9ebfd1f27ac6f792c093ae', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-18 05:54:26', '2025-08-18 05:54:26', '2026-08-18 05:54:26'),
('4109c0184df4e98519d2bcbde99ff21b5f419aba6301bb568ff0ea2205a2a982dc4f14b443a10944', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:58:29', '2025-10-31 17:58:29', '2026-10-31 17:58:29'),
('411583ef4f5a9b92a065efae26d1b8c040f26c54ee53caa018da059949160c44d9dd7cf60784aeeb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 10:29:50', '2025-11-03 10:29:50', '2026-11-03 10:29:50'),
('412c3ed8db7d5f1b79a9c7d0b946bb34285408bc2c2d731c1b06a1412f6932eae4afc6a80ff01454', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-23 08:02:15', '2025-08-23 08:02:15', '2026-08-23 08:02:15'),
('4142ac24d64387b29478231d28f25b8240df06edd745a2dbe122908fe5c8f78a6e9a281776b5af39', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 11:48:04', '2025-09-22 11:48:04', '2026-09-22 11:48:04'),
('415f29f41edb197de074c6d22c05a43a07e7ff22c33e0c9a80b6ae49b3547afb5d75d1100bc46135', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-27 09:39:29', '2026-04-27 09:39:29', '2027-04-27 09:39:29'),
('416ea6ecca69176f50deeb3b1442d3e448631db1ef588dbe1dd0c1e011377fc0b78521dc94635c61', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:41:05', '2025-11-13 12:41:05', '2026-11-13 12:41:05'),
('419f05a102a49813d63e65d9b7d270c09dd012241b4aab2561ae7970367c6c81c093e7df096d88ed', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 06:18:04', '2025-07-17 06:18:04', '2026-07-17 06:18:04'),
('41a1ff07c635026f26c9d0db221a163a0ccb9652b7242f89bfbfc152aab934c14c5bf9255eaf40e2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-29 05:38:29', '2025-09-29 05:38:29', '2026-09-29 05:38:29'),
('41a8b000e3eb7271df5c6f61164c3d155fdd1934a01c211e6f98988218c33ad39c6071fde5d43b70', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:24:19', '2025-11-12 12:24:19', '2026-11-12 12:24:19'),
('41dab32ef9da710976bdb99d6a26ffa78d314f85651e23a3b6c402d6ea393f328232562241c685d6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-21 15:33:09', '2026-01-21 15:33:09', '2027-01-21 15:33:09'),
('41e863677f11473798670b1188086e3a73663276c2bf64cd24f1d1a5bb44b5f3c81d19cbad59cbb5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 05:14:24', '2025-10-31 05:14:24', '2026-10-31 05:14:24'),
('41ec247092a07d37824001ba229255d883a63cc1689d40ff6d09343eca42b343fee2c4b9f1f9a77a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-27 18:56:25', '2026-04-27 18:56:25', '2027-04-27 18:56:25'),
('41f243d85de2b5e0ee82fd3d496819771418a60f6ab288b8c5688e3d700ebd83c66368315725e168', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 06:30:20', '2025-09-17 06:30:20', '2026-09-17 06:30:20'),
('41f3e4f83e6024afeec378bb4551fe8a2c6b2e9c51ba4d1be6f02c675799d37eae9b62072fd97926', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 12:24:18', '2025-09-01 12:24:18', '2026-09-01 12:24:18'),
('41f773502a22240d52c666171c274e3ca5e111dcf4113e77b05325a42bf32b217a0e8f38d236b994', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-27 15:16:31', '2026-02-27 15:16:31', '2027-02-27 15:16:31'),
('42197c032fa72a33f49c2265e0e79f4dc43c37e2720433a1591fa25f2a04ca0f1b309eb845db2d21', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-31 18:52:55', '2025-12-31 18:52:55', '2026-12-31 18:52:55'),
('428053493dd6f31e1c0de0a4cf2cc434d620c47216b9d175024d9e1595d0b543270a0c27566941f8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 12:13:26', '2025-09-01 12:13:26', '2026-09-01 12:13:26'),
('4283142eb5ba76e41f354502241222157996320c4b1e22bda12ad41a4b30bb43a6a591eb33b3eaf5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-08 11:24:57', '2025-11-08 11:24:57', '2026-11-08 11:24:57'),
('429f4e5fb71ca94f6768ba78789f48145e46fbda5df18e3e973fbac7343e40d5e2bf0a8a15abd8cd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 06:46:36', '2025-09-02 06:46:36', '2026-09-02 06:46:36'),
('430f656b0efab1694e18f509716a83c1d1f36f1c4a8fe346f9dedfdecd1025b958d653cc217c4d14', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-19 12:44:25', '2026-02-19 12:44:25', '2027-02-19 12:44:25'),
('4317d6f29763a6735e8d152aaaa1e0881fbdafa2fdd9a9578cbfde2d51b2de6d74d42a41aff63a0a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-03 15:32:43', '2026-04-03 15:32:43', '2027-04-03 15:32:43'),
('432344ae2b494762258a3dda20c8fe9654e0dc4e543f8504af6bdd18625594410879186ffc54f0f7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-05 22:48:11', '2025-06-05 22:48:11', '2026-06-05 15:48:11'),
('4358c9c719563bac5ef9f3274695df9723e333ca7e418934d2c21e6e7f1e3a796598e6f4796f7fba', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-17 00:52:58', '2026-01-17 00:52:58', '2027-01-17 00:52:58'),
('4378d592fcae8a89533cfd691dfb13d8601c0bc169d44cdb72587620a3e100d1c72854d622fb1eb8', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:57:43', '2025-10-31 17:57:43', '2026-10-31 17:57:43'),
('439ab37fc57662257cf95c8b335d566328cba9ed1230fcdf8009d0cf3d6c27fbf1d9722a5a5d2a60', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-25 11:10:47', '2026-03-25 11:10:47', '2027-03-25 11:10:47'),
('43b31954ec4ac0d6411568b7b77173d6eb595c8afbae5219d673ac70ee9a26b0cb0ea381c0ff76bb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-18 17:51:54', '2025-06-18 17:51:54', '2026-06-18 10:51:54'),
('43b4e84684821fd349ab5b1771b51c532ba79030f467ae54a70cbeabf8e05fec8421f1a08306ec9a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-15 17:08:43', '2025-12-15 17:08:43', '2026-12-15 17:08:43'),
('43c73769b16e0709d958c2a47708728040e28eb2ac7e24894c0751a11f2be209aaa1ca465fc79de1', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:09:48', '2025-09-23 05:09:48', '2026-09-23 05:09:48'),
('43cd2fa801bbee1baed0f04b221a8fb76322c52c5f810d1abf6d375066b15b6a99bf5ebf910879ec', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-11 16:27:44', '2026-03-11 16:27:44', '2027-03-11 16:27:44'),
('43f16237720a881b66567f2dedec22011c1ce30728b30d9cbb332ee9373c0a546f93cb4c76f47576', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-27 05:29:03', '2025-08-27 05:29:03', '2026-08-27 05:29:03'),
('44644f0df1f91cbb3693fa2f596bb0c147ced4c8a3986155eefd50d04cd6dc5dbb77889a4d414e2f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-09 10:09:32', '2026-03-09 10:09:32', '2027-03-09 10:09:32'),
('446e5456d4a9862f5e69a3af299918820e7c4dc77695ff076f3e22c279d996c1d9db233112795f21', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-19 14:01:18', '2026-03-19 14:01:18', '2027-03-19 14:01:18'),
('447b2d8c423f2def9ccfcdd5d3172e8a5681ecb668a39aafc181d5d1d68090258c45ee47a963d148', 328, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 12:46:58', '2026-03-16 12:46:58', '2027-03-16 12:46:58'),
('44805fe56db129cfccc015becb2b39803e947808f4c95beadcd3696cc41e96c2df9e06a6700dfe8e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 18:28:26', '2025-05-30 18:28:26', '2026-05-30 11:28:26'),
('4492fe67a72bd71ad53f2e735b16a702eab9e56d19ccb5e41a77657534f6ad51f1faf7a13adda949', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 08:10:32', '2025-09-03 08:10:32', '2026-09-03 08:10:32'),
('44a04b308357f171367e1ceaf7752da04e39a6171c518fd901aabc560bc39e2f5d43d4195d0eb716', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 04:52:17', '2025-10-31 04:52:17', '2026-10-31 04:52:17'),
('44c3ca870d97793974183eca4aaa64ca86e874985554b2f86f481637e97b2057801ac6df7445b1f9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-09 09:38:04', '2026-03-09 09:38:04', '2027-03-09 09:38:04'),
('44c4c81effc0dc32032ff4920b60e12c69d7288ce55a7700346f9f325b72075a7951a97b8ac9ddac', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 04:46:26', '2025-10-31 04:46:26', '2026-10-31 04:46:26'),
('44cd4b092351dd83cd86e3fd059aeb4c22f6680290a63ddd91622191e19bd9a508842d60dbd41656', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 14:47:10', '2025-11-20 14:47:10', '2026-11-20 14:47:10'),
('44d32ec668f4a4739691587de3f14a7102d641cafb7f981d151bdc13f0f585507acd3812a62db05d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 12:07:58', '2025-09-19 12:07:58', '2026-09-19 12:07:58'),
('44f06ce3f2da2c508c08772352b40a644d3537c6d1ee11659edea136e3ba4cbb44e475b36b7a97d7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-08 16:23:22', '2025-12-08 16:23:22', '2026-12-08 16:23:22'),
('44ff188dd367ec976dbeba4d3ef98464a07e44ae8db2b49d71067e7d24c0c48086b0bfd0596880fc', 328, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 15:14:53', '2026-03-16 15:14:53', '2027-03-16 15:14:53'),
('450a38b90821d59ff705a01a471547e7b8b0549e1675b96f114a25687ed3d8aee27f0f41ffaa07b2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-22 18:08:09', '2025-05-22 18:08:09', '2026-05-22 11:08:09'),
('455a7b4399b53b345959c9209dd3b4ac3f1273588971b2cd9a82d8ceb8389c44c627942790c55ae6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 18:09:19', '2025-11-11 18:09:19', '2026-11-11 18:09:19'),
('455dc602c2148eae75b9ca576f0aeecc12a127a5bae87f74c8384554f39ddbeb6ec7d4379f6c7620', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-19 15:16:41', '2026-01-19 15:16:41', '2027-01-19 15:16:41'),
('45a4cf2189ba00949e6e7e8bffe2282287b16252821cb4a46968e32bea494e7b1b61dafc14534c94', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-03 11:26:32', '2026-04-03 11:26:32', '2027-04-03 11:26:32'),
('45c8acd2d3bc02892ce32675b2a7236461432a6140a231f2a0d3a791fc4c38009ed474fbc4286266', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 07:06:54', '2025-10-30 07:06:54', '2026-10-30 07:06:54'),
('45f675c47894968f36d7d92a46407c2b0a570f819a3b57fdca6d96d3e2953ec1fa26961e92dd272c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 13:09:27', '2025-09-01 13:09:27', '2026-09-01 13:09:27'),
('45fe3186e408615b1a744114c8b7067ba0218a698ddb4bc8afdae1cd28048b94f7c3bb93d291aa0e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-02 04:46:47', '2025-10-02 04:46:47', '2026-10-02 04:46:47'),
('45fe6a83ed6fe3f2fafa6258dc5c2f3bb2c65e747cbb040540514af04ecc0fbdfceab392ea29f6e5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-05 18:25:04', '2026-02-05 18:25:04', '2027-02-05 18:25:04'),
('46171e36e1df7b0d3dd21a03b333703d20d1f0e4bc2429309359aac75d37dab74b7be17eae08c996', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 15:35:04', '2025-11-26 15:35:04', '2026-11-26 15:35:04'),
('46198587c3890b961716ea62c7db94ccb90cacee13b6caa0c40fbd4e2ce8e90b8f8794b04ac419f1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 06:11:11', '2025-09-18 06:11:11', '2026-09-18 06:11:11'),
('465e0af2c7d474cde9d04317a52fe13b46105f52f3b36dfd941aa8ffa1b8fef68948f969cceb40e7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-11 11:56:27', '2026-03-11 11:56:27', '2027-03-11 11:56:27'),
('4666b40b365b2b57965defbc7ccb70a93cc766a1d65a672225cc357af82de9c61a7cd3311cd3e22c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:41:21', '2025-11-12 12:41:21', '2026-11-12 12:41:21'),
('46d4599790baa4961f142d6b182e236b0bde5a9115f1c617a80db8aa290e6bfbee0b6feee00855f8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-14 10:09:34', '2025-11-14 10:09:34', '2026-11-14 10:09:34'),
('46de8de8d6c83ddea7c8126e30b612f331c94c8a6af72d7dbc95a501aa7ea6ff67aa3bd577e505cb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 10:16:44', '2026-04-06 10:16:44', '2027-04-06 10:16:44'),
('46ecc8a26930815b1312d357cd2a06eda668dd519c74fe30330e20d832dadfce5449cbaf6295f2dc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-11 13:50:20', '2026-03-11 13:50:20', '2027-03-11 13:50:20'),
('46fd168999a701da8b497c5b748e3428c1ccbe5171ca17e47516d6d3e65f75916e663b92b79bf0f6', 268, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 14:51:21', '2025-11-26 15:01:48', '2026-11-26 14:51:21'),
('4717b24b807a22f9e77dccc0adf56fd09352bd8346e800f44b9ddc4e278f0cea3345422ae688a4b0', 175, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 14:40:33', '2025-12-01 14:40:33', '2026-12-01 14:40:33'),
('47742a6dabd25378b59c41cbd6e2cd8c6cd4b0c41484618399dfa268fbdd43055ff8ba5199ce3b61', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-18 16:19:57', '2026-03-18 16:19:57', '2027-03-18 16:19:57'),
('47bbb0b0c02074bc4a3051dda71013193422045bee7279e08b6d82bb52c81ed310398147022f2060', 268, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 14:43:35', '2025-11-26 14:43:35', '2026-11-26 14:43:35'),
('48182ac2aa52fdb3147d7949e8a322de52fbce633ad22f11305b77bf480ca0e8cedb214b0df37316', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 16:31:53', '2025-10-31 16:31:53', '2026-10-31 16:31:53'),
('4846021717eaef6b1cc5ad5a5aadb195c3d9d29e48655470f310dd39fbe95d22c39f0d3b2c92be01', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 05:15:54', '2025-10-31 05:15:54', '2026-10-31 05:15:54'),
('484fb12d929f896c6c948d6a750af81b3f3f5fdd5c1d836f8250b675ad4ae76a935266a73957cad6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 16:45:42', '2025-05-30 16:45:42', '2026-05-30 09:45:42'),
('4862d1d2247201dcd265e14b03206d12c4256865711425b77dd2b982d94d5238f90b7f6a7543bfc3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 06:08:28', '2025-09-05 06:08:28', '2026-09-05 06:08:28'),
('48956234082c8bd04e1969688f572536902da0dd794b85903ef8ceb303c706fe0d4cbc5c54a4a84e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-19 12:43:47', '2026-02-19 12:43:47', '2027-02-19 12:43:47'),
('49222759db94e040d039e44cc6ccaca558abb1aa894e8997b0638cc21ec443c8536f465fa3b96748', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-23 13:20:31', '2025-12-23 13:20:31', '2026-12-23 13:20:31'),
('492875ecaca1f6cd0a40777cab5c7fbb4df5839668d48766ac2977aad13bdc365efe50fc06b66198', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 11:50:59', '2025-10-03 11:51:00', '2026-10-03 11:50:59'),
('493963d0cb03fb54ce3fd82ae2153023bdc8c0e5bc1469bb977212d7796bf5c29245f697707c8b13', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-18 11:18:47', '2025-08-18 11:18:47', '2026-08-18 11:18:47'),
('493d7e01d93fbe73c806dcb26782b2040eb6b67f8c9c576d4510b3183a3cf8ac8981c9646987c0a5', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:59:02', '2025-10-31 17:59:02', '2026-10-31 17:59:02'),
('4972d1204cf8f424ca080c298b7668897dc039aad9486be0450331df0dc631103b5549f946367e45', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-02 04:07:13', '2025-07-02 04:07:13', '2026-07-02 04:07:13'),
('497f2879e015f23e17ce0bbf0258f7ecbf9331a976a1c2f673d8db84b7e4aa7f33c894235d6e4dc4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-19 14:36:48', '2026-01-19 14:36:49', '2027-01-19 14:36:48'),
('49c27d12388eecb648158ea07a9539e398b7e370f3f92cba8dbdb01b093b01f1a721edc8b63c4fa0', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:03:24', '2025-11-03 12:03:24', '2026-11-03 12:03:24'),
('49d480d6f947a6b7656eb381489b34ceb8c81c185a610fb698af0349d56073d87bbf1f2e051e2d85', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 09:37:56', '2025-09-22 09:37:56', '2026-09-22 09:37:56'),
('49d5b22b258baab2d815bcf265fde8210c05d387cb928ce34b68a7bc2c9d64e037d51a439a07d16d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:18:45', '2025-10-31 18:18:45', '2026-10-31 18:18:45'),
('49f3d63eb9c33ac0bf83f658529d328e69ce1a2e0267455c530050a14350bcc5b1e9dfe258690254', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-27 17:41:40', '2026-01-27 17:41:40', '2027-01-27 17:41:40'),
('4a07ad6683dc132f977eb82fca500ebe1855a1114383e43519a4d25258c22b854edede627d3450b9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-11 11:48:02', '2026-03-11 11:48:02', '2027-03-11 11:48:02'),
('4a544e3c8bd38d87b51eb5b1fb42c78172741e34f4a0e6c8fd3ce9c40c2489bfc6f815cbf87c6a70', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:37:09', '2025-09-01 09:37:09', '2026-09-01 09:37:09'),
('4a6683e1be65f17978044bd5f0bc2ff68245f1317e030a12af93ee9383f132b29c3060dbd5529ed2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 04:12:24', '2025-07-17 04:12:24', '2026-07-17 04:12:24'),
('4ac0367607409093cacef358193dd9a8416874ac3be97f5f0ea22cf993d8394ac56a9eb9e53f951c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-09 20:11:05', '2025-06-09 20:11:05', '2026-06-09 13:11:05'),
('4ac477612b8cf92e5de638c1410f83fa6fc52c5f875f885a613c670adb707639137fa35e6eb7bc08', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:29:31', '2025-10-31 18:29:31', '2026-10-31 18:29:31'),
('4ac79df521b2af84dc406d0439c96c4c594151997f3c8120e85263823112d30cb7f765df9d65d4a0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-16 11:20:44', '2026-01-16 11:20:44', '2027-01-16 11:20:44'),
('4ac7b56bcfa6c290f8ae71f3e74c57cdf6381729e5e9a26e5e456ab3edaa2114f5b01815b4f882b5', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:09:55', '2025-10-31 17:09:55', '2026-10-31 17:09:55'),
('4b0276da2fbca07912f9e1402fda809d6255aace89d03b94182c0494963d3b95a73f02e7274d0e41', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:04:54', '2025-11-13 12:04:55', '2026-11-13 12:04:54'),
('4b0ca6afcca0c489433f4610ca3292ee633f9bcf95c860678ba9a6928787aaf344711e001ec789e1', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:54:22', '2025-11-06 10:54:22', '2026-11-06 10:54:22'),
('4bac62312e2cf77533d9268eb5bf54d4a73926fb4c27f27583616f5d83c2b1e68fffa2c89e1d4275', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-29 17:48:03', '2025-05-29 17:48:03', '2026-05-29 10:48:03'),
('4bad4573a6275e5b6315fd93456bc0836a01096a70e5bc85d7391ecf39e1f3aca9670d9f206f8fd5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 12:28:55', '2025-11-06 12:28:55', '2026-11-06 12:28:55'),
('4bbb285c088c4eaa78388341a5f0177067155eb273f6d36e934aeb8b78f04722087f63680d496d07', 209, 1, 'LaravelPassportToken', '[]', 0, '2025-10-15 10:41:19', '2025-10-15 10:41:19', '2026-10-15 10:41:19'),
('4bdf0335be13fe439cf254ae9db503beea92dff62c370f79c5bd4926e221680dcaf7a8070e87e493', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-08 10:56:36', '2025-11-08 10:56:36', '2026-11-08 10:56:36'),
('4bede062d69a27982ca104499999ddd29a66e6827fac0d4708e0ad2dfd57298b17f0e50ee47c3082', 208, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:59:41', '2025-11-06 10:59:41', '2026-11-06 10:59:41'),
('4c0dc5ca72afa270a35dcde2db254025b33f8ee3714158d27cf22110888fd2b97ba3597633a03518', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-06 10:09:42', '2026-05-06 10:09:42', '2027-05-06 10:09:42'),
('4c24726f71ce201d23a051b29d2d66089b45b02bbbb76a04efb26f692caa737f213be5c6b495444b', 208, 1, 'LaravelPassportToken', '[]', 0, '2025-10-14 08:06:42', '2025-10-14 08:06:42', '2026-10-14 08:06:42'),
('4c4bd6cb445daf6b2675b54e1fe9eb812662e260e5e3aabbf19f65d71dd60a1370beaa44bc62dbdf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-27 15:45:54', '2026-03-27 15:45:54', '2027-03-27 15:45:54'),
('4c67d86cfeabf56d4c07d2fef424760f759c3c8d88885cb550bd2782370ca8847eeb7853409f27fe', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 15:25:58', '2026-03-16 15:25:58', '2027-03-16 15:25:58'),
('4c88a3d0a8ac7235b3d172f3251c7385c4780014f632b46affdea95bf42d2f9ae4830c1ae27a2fdd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-28 16:55:57', '2026-04-28 16:55:57', '2027-04-28 16:55:57'),
('4ca041805695d80a065fd2ca49d894863c0553418c91472f5c9fac14cf35c9f9bb7cee5a904b5cb6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 11:23:18', '2026-03-24 11:23:18', '2027-03-24 11:23:18'),
('4cabacf25c54e624ee31e2da6193512bec4127bf830379ef9cb423198642100ab5b9fa3eb38a299a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 04:16:22', '2025-10-03 04:16:22', '2026-10-03 04:16:22'),
('4cbd96b20837a5e9308b9e9558e6f6477ccfa5eed3fe16e3f0c9705d3677950dfc7fc7284db6a592', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-19 17:29:41', '2025-12-19 17:29:41', '2026-12-19 17:29:41'),
('4cf53a1698fa24600d2f92516259045286ff4714429bc253a5d5ab71c510383d6d8beb45589c2f44', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:48:54', '2025-11-12 13:48:54', '2026-11-12 13:48:54'),
('4d1c1e57c3e7d98dceba74c9fa8f3d2f7193793fc386a47fc4746d414c391b171d24f7636ae2fceb', 27, 1, 'LaravelPassportToken', '[]', 0, '2025-09-16 13:04:45', '2025-09-16 13:04:45', '2026-09-16 13:04:45'),
('4d2c05226c282268575d308b2bd337a58dbfe00d2ca9359acfdc9b73b1fe6ddd724df861615adb44', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-12 16:47:33', '2026-01-12 16:47:33', '2027-01-12 16:47:33'),
('4d2f7da8f7fc0727e95ab9d7116b1eb3437dcab9a1154928d8d9d6c9549f45b8b6badd421a5d65c5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 13:16:03', '2026-04-06 13:16:03', '2027-04-06 13:16:03'),
('4d315b2c50aff1ca0cdbe060bab884343ed47ffd1c0d6113dfe047b014771f860832f377d24027d1', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 13:16:36', '2025-12-01 13:16:36', '2026-12-01 13:16:36'),
('4d39642f278d35bb83386263c88a6e78d6be462e15911355010cf7c93f148e537d8da3b9559776d8', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:02:48', '2025-11-12 12:02:48', '2026-11-12 12:02:48'),
('4d562436c11ca5550faa541d3cf37d0da0adeeb0374999d397644feaee7078265c181c9eb6b3be14', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:28:50', '2025-09-01 10:28:50', '2026-09-01 10:28:50'),
('4d73201fab73f9e729e2f5306e801d5c8dc886e0a7081be4669e9ca8362d3ab4a8ed48a972f5f4a2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 12:39:41', '2025-09-09 12:39:41', '2026-09-09 12:39:41'),
('4d7fb7e46fa521c0dea08cb09b36abfe2e7bc8a0dda74d0d80a128ef5b7a3a4427b7dff059729e06', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:49:32', '2025-11-11 13:49:32', '2026-11-11 13:49:32'),
('4d93aae9f56762aa65d9ef5cc4cdd8eef31f7c5f79d55682c423b01569a4149b2b44f353eb4e0975', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-15 08:04:15', '2025-09-15 08:04:15', '2026-09-15 08:04:15'),
('4da2fea8f109036154d6ef5aa75de7215f0bf3ad0379a8b0e4bf88fe8faacca75c8d5bd51c2d95ed', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-12 10:04:32', '2026-01-12 10:04:32', '2027-01-12 10:04:32'),
('4e09c707343d57e2b5770980c2a9283f154439249ade13e279f72bffde21c40ffe71e92ddcb14bd6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-27 05:31:09', '2025-08-27 05:31:09', '2026-08-27 05:31:09'),
('4e26063483288fcc67f25edb5edbe43795fa4a8924247df3153d3815ee4a1e25ae174829bf21d74f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 19:43:46', '2025-06-10 19:43:46', '2026-06-10 12:43:46'),
('4e3077bb2635246e24bc628345686b68db90baac3dfd883c9efb9f4ec2cea64e54ab75be695ef7f4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 19:02:10', '2025-11-05 19:02:10', '2026-11-05 19:02:10'),
('4eb77f78e7c5a4b261cdb0961f8c529a2869d13168640d13ffc25a6a8011372ec14f49c5e4854f7d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-12 13:41:59', '2026-02-12 13:41:59', '2027-02-12 13:41:59'),
('4ef592287b1519da963af6e62648fd4270f4139899821e193e7a1a1ee497c91afca3a8dd0a0fac33', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 10:17:48', '2025-11-26 14:37:55', '2026-11-26 10:17:48'),
('4f709700d3661d9e6ef78c45f29053403148e7e469f3ee4285ea61a46841f3c3de4a68fcb6a38b70', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-17 15:29:28', '2025-11-17 15:29:28', '2026-11-17 15:29:28'),
('4fab67b032fcfd89e52c0308493da50ef37fe8c3eb8763a1b94a8e5ddd8c68c2fe50e3115816bc1f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-22 11:06:30', '2025-11-22 11:06:30', '2026-11-22 11:06:30'),
('4fc3d035274f96da56a94acfb4f5efa4e8c8322b7ba168a5434b04b7d2117baa17103d446b9b12a1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 14:50:44', '2025-11-20 14:50:44', '2026-11-20 14:50:44'),
('50494337d415ea51e3f5dd66ba7e142bd7a956c05ab1b05102e36e78596060dda3d74dd0c1ae9874', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-04 11:29:30', '2025-06-04 11:29:30', '2026-06-04 04:29:30'),
('5055427d6c07725a7039999bdecd22e9dc67fb195d4230c94be1ae3d1f0daa4ee0fca46a73dd795d', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:15:03', '2025-11-03 12:15:03', '2026-11-03 12:15:03'),
('5058a186f77d955a382170418cee263d994685cd83ea0bc330aa12a9780f3403a15d4abb39fbe531', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 07:08:01', '2025-10-30 07:08:01', '2026-10-30 07:08:01'),
('506346292cfd1da6f66f0b68959c607eb767c9fe8c5bf32777c6558773bc8041658ca6e020d2e6b1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-29 08:26:57', '2025-08-29 08:26:57', '2026-08-29 08:26:57'),
('507f399d0acde2f62e0ff4ad8f118ed4d625babc1d5c4f7f58d9b99a28b0225c3652426104a51111', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-19 15:59:40', '2026-03-19 15:59:40', '2027-03-19 15:59:40'),
('5081112209d099b053dcf143e37a23eef9deb41433146b00b2e23d28f06cf7b4839bbab9aee0f0c2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-12 17:03:44', '2025-06-12 17:03:44', '2026-06-12 10:03:44'),
('508daa0b53ac0c1b051d4f0a3d7edf5d4369ffeb9420939ab320f87e2cc42160ddb93e4d8e4393d3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-29 05:51:46', '2025-08-29 05:51:46', '2026-08-29 05:51:46'),
('508fb24605ac7c755946e1097964d85fed870bb5178d57df9ba361a8c420d7ad410e6080f875b6ee', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-08 09:44:46', '2026-05-08 09:44:46', '2027-05-08 09:44:46'),
('50dfa7cb8cbb0c5c510770b056ed0420a3ce88471317e18590d70439a01940a54cb52d319dc834e1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-04 07:39:30', '2025-10-04 07:39:30', '2026-10-04 07:39:30'),
('50ee69d7725d957668dc73116aef516400ea99ea07f1152a5291e326222c02c050859a0f4ee5f16c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-23 13:18:29', '2025-12-23 13:18:29', '2026-12-23 13:18:29'),
('5137a37f088b044f4e254b9f5aed7b1d2deaa6a950889bceda8d595563381ed6a371ac02de1e6423', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-12 14:22:36', '2025-11-12 14:22:52', '2026-11-12 14:22:36'),
('51651d97035d4911ba834968c4c680149f3862f5c07f55271ad26ec12eeb6eb263810e637e1754fb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-26 13:10:49', '2026-03-26 13:10:49', '2027-03-26 13:10:49'),
('5169268b54d068d6fefbdb137db3012797b8aeb58b19d486a5884815a7fa00beb9d1857e28e47744', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-08 10:59:11', '2025-07-08 10:59:11', '2026-07-08 10:59:11'),
('51828d5f7ed6a7e0f33883856654629977ae8de50de38e7e150689ebef4a099b807492a4029328c1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-01 09:58:53', '2025-07-01 09:58:53', '2026-07-01 09:58:53'),
('518419af0a4208f2d03813f0395d77bc7a529a4b5d104c8d93c0627f0cbffa75e6bf2836dbdb30d5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-09 16:12:33', '2026-04-09 16:12:33', '2027-04-09 16:12:33'),
('51acd23cd884bbd1c2f315d44c900e34dac0d139698ee6d6376adfbaabc35e2081b6006fcc290c0e', 331, 1, 'LaravelPassportToken', '[]', 0, '2026-03-17 16:55:58', '2026-03-17 16:55:58', '2027-03-17 16:55:58'),
('51bd6c96f8e369129e222e336be599fc86ecd8f396eaa29940ef29d5fe7e58344447a9051296b4d6', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:25:52', '2025-11-13 11:25:52', '2026-11-13 11:25:52'),
('51c781a5c53b581b3bc18992594db43e681320be1ce1176a0217e809f546c9f5c3c2c9dd2d179e7b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:33:57', '2025-11-06 10:33:57', '2026-11-06 10:33:57'),
('51cef34761f71f22b49f6df952bb44f3590c4822a81dd44af2eaed23da3b37dd82d464e5355cec41', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-20 12:35:29', '2025-05-20 12:35:29', '2026-05-20 05:35:29'),
('522fee4fc80678886bbc64129662568c6daf85daec20fa93f3385e0dc01b43077ef0dfe62e9628d2', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 10:29:39', '2025-11-12 10:29:39', '2026-11-12 10:29:39'),
('528ddcd2f4c3b7a9e4cee5a91e96d77e2a976144a760d1b46ac4ecc59eb5aee26ef80d31cf3209a5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-26 10:04:00', '2025-09-26 10:04:00', '2026-09-26 10:04:00'),
('529f0ca416ed2706f687f35aa8be1bb976a24b29fab0b0a11c9c361a86f85f9b9a2d90fdb1a4b4f5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-20 13:59:27', '2026-03-20 13:59:27', '2027-03-20 13:59:27'),
('52d2b99df3f398cb0bc2ea2502658f4a02cfdc37803bad0fb1a8d1a2da152e3b3a15d2a6eb82fa1d', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 15:22:58', '2025-11-13 15:22:58', '2026-11-13 15:22:58'),
('52f6670c34afa83d0de74722ec7414cf9f9db28309cde07b71f98e7fc12320a80c273abab487ad08', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 10:04:56', '2026-05-26 10:04:56', '2027-05-26 10:04:56'),
('534ff58897d2cc926d101cb151c8f89f3740c18248925847b29f077027a4ff37e8f9e1ce5e261680', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-26 16:14:51', '2026-01-26 16:14:51', '2027-01-26 16:14:51'),
('5379f95d7fd73f960529320842e1d44fbbd94c1e88334c2cef4e25fd63b5243ece44e438a9c87c98', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 15:57:34', '2025-11-05 15:57:34', '2026-11-05 15:57:34'),
('53cd49926a0ec5b94d46e9df686022f3bae8b68e5413cb43dd7807374e589b8922bde27056773615', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-15 04:35:05', '2025-09-15 04:35:05', '2026-09-15 04:35:05'),
('53e4f7fad956c3d82396c522fa83f1a4914a0266af5b213ac3d8b7e756879e8fe9511ae8adab6877', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-15 17:26:06', '2026-04-15 17:26:06', '2027-04-15 17:26:06'),
('53f39297c705dd3f3b39f30ad85dc62bef98ef75fc4f1b81885c989ad280aee9d5aba1ddfae14366', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 16:10:00', '2026-05-25 16:10:00', '2027-05-25 16:10:00'),
('53f6b48f423abcffd4a982d51299e8674a73efc5a591d84da191fa0d2cafca578f80ee5820de0be3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-19 18:26:55', '2026-01-19 18:26:55', '2027-01-19 18:26:55'),
('542423879e227c83527de2521d154669e51ff70ecafb5445f9277d4535f3b77308803c289cf02597', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-23 12:47:31', '2026-01-23 12:47:31', '2027-01-23 12:47:31'),
('5460eb38af7db72e4ee35177e3cd827e47a3d4b68ddb622dcc5af1d0bcb02ba8f00726507a36ec9c', 346, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 17:10:47', '2026-03-31 17:10:47', '2027-03-31 17:10:47'),
('5462dc33125fe1490590ac53777f8bbf30b0152c6aa81d1ad8900d2dd31be9c844cc4e6615bca143', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-28 11:06:13', '2026-01-28 11:06:13', '2027-01-28 11:06:13'),
('5467a0fecec288fe3b774851740a527d0ddc835ff34557eccfa485d4f2fd10ad9a9e3581ace6e933', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 11:04:13', '2025-09-25 11:04:13', '2026-09-25 11:04:13'),
('5493288c8b5d7095b7bc0f23d841ba38b601f4e9ce15e5b017be124773cb503557736510192d1a8a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:15:56', '2025-09-01 10:15:56', '2026-09-01 10:15:56'),
('54b7a7e08562a7772a2c6055946c33470fe7d89b3678d523ed2c71213a42c942d387b0001a5de95f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 07:01:07', '2025-09-25 07:01:07', '2026-09-25 07:01:07'),
('54c30b95bb753b2542feef04f3217e13fe984da47bc6967a0f61046e8b98f03d559ad1dfc04240fa', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:28:41', '2025-09-01 10:28:41', '2026-09-01 10:28:41'),
('54c8468697f4aab827d9dbbb0e82feac387d516b01471852140fa6ff9c0efd7d5f3579c7d0b452bd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-03 12:55:28', '2025-06-03 12:55:28', '2026-06-03 05:55:28'),
('54d38f3ecabaaa21e7690fe97dfc0c4ff7b3cdb74d389b24170d3f0b5b0376d2bb1473949fadd457', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-04 10:44:25', '2025-09-04 10:44:25', '2026-09-04 10:44:25'),
('54dd01d0d1b9e60bedba56a27acbbe9c300dc1d15bf18b7a88d4746b1ad6ccf76f54e3c9dbe35918', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 10:20:48', '2025-11-12 10:20:48', '2026-11-12 10:20:48'),
('54e9667e10113e9e5a1aef7b59b32700321298530ec67f51c106f86ae07400a1018400263949fb52', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:28:52', '2025-11-05 13:28:52', '2026-11-05 13:28:52'),
('5535b9df877121e41ec0d1ac8eaeb77b26328bccdf120f4ca8e8d3e4db5b79e510f38a78dfd33cb8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 14:52:01', '2025-11-03 14:52:01', '2026-11-03 14:52:01'),
('5548bc08c678f87e20a93e9d64379fe247b6ed44d9c295d0df8d3e617a2bd0ff2bd6a5f651ca5f6a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 15:38:10', '2026-01-29 15:38:10', '2027-01-29 15:38:10'),
('554a62d5f33cbce7db6e554a696a637cffdfe4f8f9cb6771d70d5ab855355f79ecedca5ec5cf6f70', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-05 19:57:08', '2025-06-05 19:57:08', '2026-06-05 12:57:08'),
('555d0a81ad6251879c63eca4a04ce2608ca29a4b11367982154c45ec89e20df193d495f5fad5c80e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-25 23:30:09', '2026-02-25 23:30:09', '2027-02-25 23:30:09'),
('556d161629f0d0fa538f1032efb2c85613deef1ffebdd27c457ef36ec86cfea79cb34beb143a82e4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-11 10:22:22', '2026-02-11 10:22:22', '2027-02-11 10:22:22'),
('5594368890986532d0203c122fac199388af7123488ccbde9b50ef12523f222d5778524ec101725d', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:34:42', '2025-11-12 12:34:42', '2026-11-12 12:34:42'),
('55960c589c3d549748063e4734b4aee2f8e10809de2102435fcbba0d1bd7c04d9ae5c6dd335e6cc8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:19:36', '2025-11-05 13:19:36', '2026-11-05 13:19:36'),
('55bc1ed4ce2c41de62ac5e64c876cf697d94e316f5bed96b313039a3847bbce006a555a14f31f351', 219, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 13:13:24', '2025-11-03 13:13:24', '2026-11-03 13:13:24'),
('55c3e52165a96effb25d0af57217a991fb4a25f56aeeabb2d105bc7bb35753b74f9157c63f9f86ae', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-21 14:02:29', '2025-06-21 14:02:29', '2026-06-21 07:02:29'),
('55d63eb1b01404331fc9c7849d7405382ba94694b105e0cb88d3721f68d857bd0e4b1347bd89c04c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-25 13:20:30', '2026-03-25 13:20:30', '2027-03-25 13:20:30'),
('55d8c26ad5d2f6a52181d9235bec8376c69ec9781a536c9e9f4365d02e4c5de9209d140fa5fd1c54', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-01 12:38:02', '2026-05-01 12:38:02', '2027-05-01 12:38:02'),
('560e10b43ae5ecaddc2172ec080f9236d8360ce98cd2f374496d48e0cde1ade18e87966b738e0870', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 11:19:34', '2025-09-22 11:19:34', '2026-09-22 11:19:34'),
('566648ba666f8d049abb67c1993a72c491eabf3fd5fc904fc0b25f6675229ffa16321486815174c4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 17:03:03', '2025-11-04 17:03:03', '2026-11-04 17:03:03'),
('56870bf22cce4dfe4a75c9b40114c9284bafffc634df67e1b6c801a56a38136ea35577ae54378660', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-30 10:46:15', '2025-09-30 10:46:15', '2026-09-30 10:46:15'),
('56919749e8605612394c2ea2df7022d376ee15c3044703b468ba2903ce5be4aafab2fa1d24842e03', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:07:40', '2025-11-03 12:07:40', '2026-11-03 12:07:40'),
('569fe988df58e707420cb53bc88a478e765880327aace6c184b31785ca2e9719fecc3204d8de4b89', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-12 14:44:22', '2025-11-12 14:46:11', '2026-11-12 14:44:22'),
('56afa4b9f022fc0e5e402e2d183015355eec6de9f19d5ec00d3cc26af5043e95cc1b8c0859a2330c', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:47:10', '2025-11-12 12:47:10', '2026-11-12 12:47:10'),
('56fe0e9840cabe10c11bb5988ad1b980b373a88613b7420adcc8cf1244a2c1b5ab2ebeacf440aad5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-15 04:33:48', '2025-09-15 04:33:48', '2026-09-15 04:33:48'),
('5732674f5c7e6321caf79875bc73dd58f0180dab7b6d893b47b83b44fbab74158c0518afa83009c1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-19 13:44:59', '2026-01-19 13:44:59', '2027-01-19 13:44:59'),
('575ed9c0332ac01df501b369665ef9d8612f0d968c3e7c7d76ce856eb041ba33f5d85d9075099a39', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 07:14:30', '2025-09-23 07:14:30', '2026-09-23 07:14:30'),
('57748d4573f709cbc47d76d4635f6997abe385879c0d7957b3ef3ed94fb76094493c71901f029656', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 19:10:20', '2025-06-10 19:10:20', '2026-06-10 12:10:20'),
('5815395c585a7b73c3643023efa35c595d3a6ab6a08891ec09607ddc618dce4938e071bb8093608e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 10:28:33', '2025-11-03 10:28:33', '2026-11-03 10:28:33'),
('582d840b767310cb3e8d2b4edb61a9ddad39595737ee61e33688278fb9bdc368746ed8a8bfef345e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-08 10:39:22', '2025-11-08 10:39:22', '2026-11-08 10:39:22'),
('583c40dba491bf045050859993431258eabb1a9f11be460cfa4e15cd2d8ed9e137f6ad09d49ead6f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-27 12:33:10', '2025-10-27 12:33:10', '2026-10-27 12:33:10'),
('58717faff02abb74296edad97d835f0ee97130b43560572f6364b6059b50a77a689dc11e10061623', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 06:18:31', '2025-09-22 06:18:31', '2026-09-22 06:18:31'),
('587a61add77db939bb25db2899180fe052796ee8f8ce57c8888e94748ce041292aa976c15ad664eb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 17:46:30', '2026-03-31 17:46:30', '2027-03-31 17:46:30'),
('58bf6a2e657d711c1ba60b764852ff4a97e785b761bee10767a83669517eddfe59194daef7c94acf', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-14 01:15:34', '2025-05-14 01:15:34', '2026-05-14 06:45:34'),
('58c9f67748ca211674afb349cc54fd5a65b8b960dd227eb446217bf296986a75ca9dcc43b0c6318c', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:21:05', '2025-09-23 05:21:05', '2026-09-23 05:21:05'),
('58cd012cbef56b61cd6124d6c897c913910833f1d438ab35c86ebff8195c3c7b158b7a45b8880006', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 07:00:36', '2025-09-25 07:00:36', '2026-09-25 07:00:36'),
('58d532223aac24bdb058c22c8df24bfaee16b5563fed38ad2c2cdf612ec6d6fd27148cfb28c75d0a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-05 11:07:15', '2026-03-05 11:07:15', '2027-03-05 11:07:15'),
('58d69c9ff7d0f4b269695dcfa488a3a68d11e708f5125934ecb8e95ac02f78435d015017d3331bec', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-26 15:36:24', '2025-12-26 15:36:24', '2026-12-26 15:36:24'),
('5902b4ca225deeb949546a73a0820c411d0ec4e3c9a1eb3d0b26cbd3d58ecc257f7131ae361e6481', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 16:49:01', '2025-11-04 16:49:01', '2026-11-04 16:49:01'),
('5937c164fbd51d0e5696dac850a58897ccc4338e670bc16071ae1c5a10583a7a5be1c645d0036378', 219, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 18:22:20', '2025-11-13 18:23:03', '2026-11-13 18:22:20'),
('594bf2e9668aaa8e843536e0936b374f9a8b4dfa1e42c6fad355ea71efcc2499ac0fdd6b08010e45', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 18:16:19', '2026-04-06 18:16:19', '2027-04-06 18:16:19'),
('59513e970f545a1ec2df2ec61dec8d368af8264a4457a704e9b665a9582c5f037fd0e60d5f53ad14', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:08:30', '2025-11-03 12:08:30', '2026-11-03 12:08:30'),
('59542e6c68337083b9d1883d25ba619d2def090c472a7c8a313105f10d9136c169df2616f38a9705', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 06:15:01', '2025-09-17 06:15:01', '2026-09-17 06:15:01'),
('597db709f9aeb33db1f2451dd815c2adcf8226ceb2881bd6b2316886c8823318cfffeb473f4115d7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:22:28', '2025-09-23 05:22:28', '2026-09-23 05:22:28'),
('59a0b0e4d5f523ffb174b5606d5890fcc31810f139a2c4f43bd565b14ada247d7e6bd3de40b4c599', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-17 09:39:47', '2026-03-17 09:39:47', '2027-03-17 09:39:47'),
('59a2da91188ce222c55d03b22dbe242348ed4452739a7556ec27380335380e2fc8476f5541d11f5c', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:56:24', '2025-10-31 17:56:24', '2026-10-31 17:56:24'),
('59a3043f8119190970a22da5cb1098e65eaa199856f83de14d438035a18ccf20c23c8134a270db02', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-12 11:40:04', '2025-12-12 11:40:04', '2026-12-12 11:40:04'),
('59df10f2ff903c6aa1ebc6c6bba0378473b1fe65d4c4f0123e42fdcd2407fe34ba648439229d58fb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 10:46:30', '2026-03-02 10:46:30', '2027-03-02 10:46:30'),
('59e7a9b81f4dfc63cd3c00d6738717bf3dbbb6257598e2997f3dcb58ab071d51ef9fa8e2743ec9ed', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-02 17:07:04', '2026-04-02 17:07:04', '2027-04-02 17:07:04'),
('5a237be460db45ddf2e8ee153be12d5307d0419dca9e087dc7889b866faa0e758f0478dea08a695e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-05 10:00:21', '2026-03-05 10:00:21', '2027-03-05 10:00:21'),
('5a26b088189798e2568ee46b966df3ed3941c3409dfe8687f431ba469fd1b663c3cfc0da26034e73', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:47:46', '2025-11-11 13:47:46', '2026-11-11 13:47:46'),
('5a3fd59dbc6eb59fa0517fd2bcd6d7b5d1593f382f5ce83677181cd76c4e2b93dcc57eb3dc434c21', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 18:10:06', '2025-11-06 18:10:06', '2026-11-06 18:10:06'),
('5a6ec35123a2df3f7dac9dbab0fdcfc556047a5727689eb8e40e1909f21dd397be3ad52328ee34a3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 08:20:08', '2025-09-03 08:20:08', '2026-09-03 08:20:08'),
('5aa03e9d4c584d7afcc2db3f4a23f514af9d2b91d7e0546addae0815bc0d6145a3aa29472ef334e1', 268, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 14:51:18', '2025-11-26 14:51:18', '2026-11-26 14:51:18'),
('5ab9bea8e2d08b471e827092b8677fe1a100a509158879f7944896d987f5bde59b33bed1fc9e9d5d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:32:51', '2025-10-31 17:32:51', '2026-10-31 17:32:51'),
('5adfbdb24e3429380dff603845d719837be69d62a261d73d916f431456f321b975f4497b9403aa97', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 14:58:00', '2025-11-13 14:58:13', '2026-11-13 14:58:00'),
('5aead2beed4067eb4576f5c4ad320ccfff881b7074768f4fc5f85894d18f211dd0b31e2196e6c399', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-11-29 14:15:30', '2025-11-29 14:15:30', '2026-11-29 14:15:30'),
('5b1ab35a034747a3195fb18d65510b0e8654ebc3a64e4d6e445167ccd3143d541d2d23c20d982630', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-02 17:05:02', '2026-02-02 17:05:02', '2027-02-02 17:05:02'),
('5b58ee361d7767c8a756593409b2595ddff5ce2775cba394229faf3ea7017ca0b9e0acaf00aa7c2f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-05 10:48:57', '2026-05-05 10:48:57', '2027-05-05 10:48:57');
INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('5b67d5a24430a1afeeeca2da542df7410d59982e4597329ef3fc2778b3b49bfcfab52d9e0c7f1c90', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 09:14:10', '2025-09-25 09:14:10', '2026-09-25 09:14:10'),
('5b7958a176069d63b7913b06e984f0df0d144dd7266b1af5d064af2bfc0ca8d0ad824f36c9465638', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-23 10:19:04', '2026-02-23 10:19:04', '2027-02-23 10:19:04'),
('5bb305ac9a73bf93c666b16d043ea421931e3a6d8c45e3050754bdc806495be06592cf4bae08ac3e', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:56:09', '2025-11-06 10:56:09', '2026-11-06 10:56:09'),
('5bcf59e5b9b22aa43b4d1e7d498d50e2db76c52e81d78b3df1477db837110b853f5be18d2fdf5438', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-31 10:05:29', '2025-12-31 10:05:29', '2026-12-31 10:05:29'),
('5bd6d976fa93f4680c9e99e15d4e6aba27fed04f0892d17ef5300637f154d538394e567d0798392a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 06:29:45', '2025-09-02 06:29:45', '2026-09-02 06:29:45'),
('5be9e4488f7e88a0c94a3e129fa0dbddc2a3f9ec6db09f3368e4b78c4876b94934041176f94b0e64', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-29 06:14:11', '2025-08-29 06:14:11', '2026-08-29 06:14:11'),
('5bf56abbc0082a64585d4223bff8d97e81749b6b9ced21b514adf0471a05daca6f92ca50879a07aa', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-10 05:20:43', '2025-09-10 05:20:43', '2026-09-10 05:20:43'),
('5bfd47b2c5f916f52f01a7378df68e14f2df001fb1cf5fda50619c436384527dbc60b70ca2da1945', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:51:20', '2025-10-31 17:51:20', '2026-10-31 17:51:20'),
('5c156974cf7d8fef828bbafcbff4ce3b8228fe4363826cc7c8bec6a896407f407e818044b70d4287', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 12:34:07', '2025-10-30 12:34:07', '2026-10-30 12:34:07'),
('5c1c49def331205c25b3c963df615329385b57d04c0eec93fc7e0bc6257e3b9843359b7bb1ca8ccd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 04:34:02', '2025-09-22 04:34:02', '2026-09-22 04:34:02'),
('5c28dd19a5711edc68e4816b47c036f76d483db8de091e89fb5081fb0199d9da0cd6eafc48cf699d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-13 04:49:24', '2025-09-13 04:49:24', '2026-09-13 04:49:24'),
('5c2acfc9fe5cd0d03c3d7607c1209f16726696f0581e0940a860aa58272d2f69f0c15224297df0e6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 18:40:31', '2025-11-26 18:40:31', '2026-11-26 18:40:31'),
('5c2f780b3935650433d4e2b02b6df9b62c8425848cfe6ccf3d99c0d9dc5afaf999553ab95a0a0c85', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 19:04:24', '2025-06-16 19:04:24', '2026-06-16 12:04:24'),
('5c57bab75922d5599e957bc0a55197708b3ce2490a2313211c16757b7af402aa23c4db28dab967c2', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 11:54:20', '2025-09-22 11:54:20', '2026-09-22 11:54:20'),
('5c5d3e2729208bc0e19e70afc1c0992673d3ba1b17be7cab13ab357b61cbe5a9e5b5378dd720e24a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 12:33:12', '2026-05-25 12:33:12', '2027-05-25 12:33:12'),
('5c68d2afe3d380cfee53ea9f4e8ace83e1e8b4d6d763ebb563f1d10f84c62e1fc6b271188acc4c99', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-30 10:47:43', '2025-09-30 10:47:44', '2026-09-30 10:47:43'),
('5c7332609499c5ca8bdf9c3ddfcabc85e390e4bf146e4f1f2e544542040a511b4a8331d8a5f843ff', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-25 09:49:16', '2025-08-25 09:49:16', '2026-08-25 09:49:16'),
('5c792d553a19c2a30dcf60f1d54a843d232a45f2a15e593b83bc462af0c8150cceaeff70c1e01ad0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-04 13:18:40', '2025-06-04 13:18:40', '2026-06-04 06:18:40'),
('5cb2f147e3627f42016fef6c2efb5c4db767af8f3a9e5ab462173451a8a1b51a6cfb1a7d7f0a2449', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-10 13:24:01', '2026-04-10 13:24:01', '2027-04-10 13:24:01'),
('5cb40bb2e7b9331fc66cb705d0b74bceb5891155eb0f5ef1a7efe0ad04618a8592d1ae5340ab02a8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-03 17:05:42', '2025-06-03 17:05:42', '2026-06-03 10:05:42'),
('5cfa08a0642528123f3fda56eb2fdfaed8e71094dfc5812982cfde844dbc48a4f0adf72d22bf26b6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-06 10:15:03', '2026-01-06 10:15:03', '2027-01-06 10:15:03'),
('5d217c4cf4cd1dab7cfec6fef53d6f456c9a6109ae6e7069f819ce1cbfce93beb7d14237bbc02684', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:18:45', '2025-10-31 18:18:45', '2026-10-31 18:18:45'),
('5d3f94ddd5461f210f996f5957fa0665639562dac9fb7a6932935996fc6f36a99ecea29490a750f3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 15:49:14', '2026-03-31 15:49:14', '2027-03-31 15:49:14'),
('5d7be7df9327bcef7fb2590f0e252d5ae9ef3ea30f1ad48e96e4e92a7ab92ec19845e72516ab2d0c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-09 18:34:43', '2026-03-09 18:34:43', '2027-03-09 18:34:43'),
('5d818f91119f0ec1820d23353d17d961fb1488b1d54c3ed5fb717184be546f1bda84b4608d6114bb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-20 13:30:36', '2026-02-20 13:30:36', '2027-02-20 13:30:36'),
('5d8be225e713bd53f49b1b98086064236ac943466b4ff51bac6a088ca0291f5c1c1e2b534df331a5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-11 07:11:26', '2025-10-11 07:11:26', '2026-10-11 07:11:26'),
('5dacd3796f9a85e1c116f00f984f322647d6209dd3b5b9965559701b3d9791c23c95817753d1e779', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:29:43', '2025-10-31 18:29:43', '2026-10-31 18:29:43'),
('5ddb1f56ca5a27f9f618213f6a0073fe037c58714f82c6b9a89974f2211186a5c183162a89219dbc', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-13 12:24:04', '2025-06-13 12:24:04', '2026-06-13 05:24:04'),
('5e2bcdc265fda41da1f06b0c60fa146d8f3bf9207db3bef69f8e47b1173f6dff1ab21972d4cef3b6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-27 12:36:03', '2025-11-27 12:36:03', '2026-11-27 12:36:03'),
('5e33e5e7d238539688df5cd41b4f8e897e00107f4180c6ec9546950cacd9f663a8012644e9319fc2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-21 09:55:49', '2025-11-21 09:55:49', '2026-11-21 09:55:49'),
('5e52569bc2911babc8916936d95bf1dd6d75c2e017507dbeac089de8871d23c046529c9b065c1209', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:55:13', '2025-11-12 13:55:13', '2026-11-12 13:55:13'),
('5e5592a8aea8823bdfc43733c2436fbb9abc5b97be865bf7dd1d93b2ecc99413724a1ae7f085b2fa', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-27 10:04:14', '2026-01-27 10:04:14', '2027-01-27 10:04:14'),
('5e73fcc5c020ac68b6b2541e487a8acad73530cfae915eb5abb110b478c96412195280d3d3375afb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 09:34:03', '2025-07-16 09:34:03', '2026-07-16 09:34:03'),
('5ed7bcc1c9f54555d995fc15c0172c6f2e0d63a592f42017909071a0f549f3cb8eeddc13bed0b17a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 11:53:37', '2025-09-01 11:53:37', '2026-09-01 11:53:37'),
('5ef67ca764703785bbd84d0aa67fbd0a875b9a6c2c05c00488ddd03a4a5919fc5d4a13d0ac6b9ad2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-26 17:32:25', '2025-05-26 17:32:25', '2026-05-26 10:32:25'),
('5efe94c3f4376c866b3caeaa1dc07896110d447ff30970964619657cb04e27cf01e2e59ecc83b7ff', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 06:49:00', '2025-09-18 06:49:00', '2026-09-18 06:49:00'),
('5f1b79b4a40f9fdd928f87ab4b20950de4272ce57bc738da548186d5cb08561ca6383c6a02c5ee50', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 06:30:18', '2025-09-17 06:30:18', '2026-09-17 06:30:18'),
('5f1e109a7a033946d8525876eae3630588e99ce4f1145a9d130a7fbebe332439f1de9cfa38a5e25b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-02 16:27:57', '2025-06-02 16:27:57', '2026-06-02 09:27:57'),
('5f5cfdebe81ddaa36c0d8972c965caa0d656f2ce0e8060322dd6ad2b408e2e62c915fba475762264', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 13:38:01', '2025-10-30 13:38:01', '2026-10-30 13:38:01'),
('5f82f80f761d326bba2fbe0ef677f93b5111004b02e41492fcbc36cd2f3e50e776e55122f4ff378f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-07 11:17:53', '2026-01-07 11:17:53', '2027-01-07 11:17:53'),
('5f841829edd9b4a115535344588419201abdb18d6766c74af49c750af7aad4adf777389d827b040f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-18 13:10:44', '2026-02-18 13:10:44', '2027-02-18 13:10:44'),
('5fb8030e494dd7b60a88987e0090052399db6ee160d1744a94e0508e785a9d7964db0a2caf3ac295', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:53:28', '2025-11-05 13:53:28', '2026-11-05 13:53:28'),
('5fc1adf86a23889d99d6c9726bf573cfb075e90d227935e8d281fed3d99df9183a10c11f324d7d0a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-16 10:12:41', '2026-01-16 10:12:41', '2027-01-16 10:12:41'),
('5fe7f632e2294b6d85ebcb34cdbc06bf2205a516866efad33e7d574c37c075cc8858ce621af63487', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:42:12', '2025-09-01 09:42:12', '2026-09-01 09:42:12'),
('603ffa552c1e83cdbe729e0ea474b24277300cbbabb96bdd14eb50d1dcad0a778243059a671b7330', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 04:36:36', '2025-09-02 04:36:37', '2026-09-02 04:36:36'),
('6063e0e1ea864d6b0428fa9cc365d6fc13fa61017b4fa48d2558c66535b257d6f9aec61bf39151e6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:58:43', '2025-09-01 09:58:43', '2026-09-01 09:58:43'),
('6068ab6b55ce9cdfe408b80e14fb1cc629aa1e171e088873e146f7f44cdbef2e2111a349686b202d', 19, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 15:49:13', '2026-05-26 15:49:13', '2027-05-26 15:49:13'),
('606c20c69c56f29113a4874a6fa3b28a7c4e6797746135078ab47368af10937e1da464f87b6928bb', 337, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 12:40:04', '2026-03-30 12:40:04', '2027-03-30 12:40:04'),
('6073e1a20ba6155c157f8361f86050a76b3f76a86e6e29fb316ad3d6f4a023643158b9fd0b14ff54', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 13:33:03', '2025-09-01 13:33:03', '2026-09-01 13:33:03'),
('609a72db24fb299c4f4065397cafdc990ab4452297b57e49e591a4add741c5a92ab161c360eceb7f', 151, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 11:18:14', '2025-09-18 11:18:14', '2026-09-18 11:18:14'),
('6103c7ad85da4de82ddea9f3938a1e69efde06c69828fec0ee16128893acb9753a9be6b4f4cd0394', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:39:57', '2025-11-13 12:39:57', '2026-11-13 12:39:57'),
('611a2fb16b3edd6ca24e3c716f0940cbc00a5496de982834e6696c9442cd1b04df715951d4f64af3', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-12 14:23:12', '2025-11-12 14:31:32', '2026-11-12 14:23:12'),
('612703e5b54b864f55b05a98bfc079351fda3f5f3d31959fb05368b9dcf92b0592048a329bd07ad3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-07 10:53:26', '2026-05-07 10:53:26', '2027-05-07 10:53:26'),
('613e94a1d7f922872236707edc243b087722ae6d43b7c47b8b14db8cf210bbf19600014f3d55ede0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-21 07:07:20', '2025-08-21 07:07:20', '2026-08-21 07:07:20'),
('61539ecd56d65c2d16f2812059e80b76c37eef405dd051eaf0b5a64648fba342efd1a32b1eb3df43', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 16:41:58', '2026-03-31 16:41:58', '2027-03-31 16:41:58'),
('6164ec011e0a6c610f2c2b4b8f14bc679c962d25ca3c4b4d99dd35a83a11d1225960441546187f5a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 10:38:56', '2025-11-20 10:38:56', '2026-11-20 10:38:56'),
('6166e0d1f35542b3d76a94948199ad4c2784cbb9f10054ecb13472739cd8cbe1d079622ac0717115', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-13 19:08:30', '2025-06-13 19:08:30', '2026-06-13 12:08:30'),
('616e66579c206193ff9441dcd59dff715c8ba42660237bca21419a5770e7dbc1259ea3aee203da53', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-18 15:13:14', '2026-03-18 15:13:14', '2027-03-18 15:13:14'),
('619c2a3ee042abd792ee3cd0aac43c40448d925f9d6c83309d362198279b2a95cf357b87d3ab40fc', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 17:17:47', '2025-06-11 17:17:47', '2026-06-11 10:17:47'),
('61b391e37b677f6cdc4dd0647cc105027be6a6ca522a7adf3b32b4a1bfee66d4fba8b3f76fc171de', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-06 04:57:27', '2025-10-06 04:57:27', '2026-10-06 04:57:27'),
('61d7a1a7abcd09a954030b17fd544bacb331c8f0e0a9ebb4982b0b731b2f0e5bed5ff2c53f43c8da', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 09:35:29', '2025-11-11 09:35:29', '2026-11-11 09:35:29'),
('61f85fb7af05a9509059451aba5d84519e7fb62ba02d36ee65b0c57ecbf91750e39484744e4ccc2e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-27 15:14:58', '2026-03-27 15:14:58', '2027-03-27 15:14:58'),
('62227a565f7beba6a64c046a1db0f81470806abba92da7778718a74d7d04ccd26a3bfff604503fca', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-18 11:19:41', '2026-05-18 11:19:41', '2027-05-18 11:19:41'),
('62535effe9c3aa36e9e7de03d415d6d0c9ad3c11093995d363a26eb7f172c3945d16071f27807d22', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-12 11:59:23', '2026-03-12 11:59:23', '2027-03-12 11:59:23'),
('6293f0197f29ff9006e12c50281269ee8370d10cf2c4d0a5e8b79d7c700f618662179baf1f1de75e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-04 10:59:02', '2025-09-04 10:59:02', '2026-09-04 10:59:02'),
('629f422aafe3a211eb0ab08364ea7fcbdcac66d1a04bea47bde2a0922d2fd77d035ba96a4d384066', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:26:44', '2025-09-23 05:26:44', '2026-09-23 05:26:44'),
('62c2e20c90964ecf8c0019d10baf98205a893cc7f5e45848954f373b10e437e950efcec724b5cf8b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-04 16:19:14', '2026-02-04 16:19:14', '2027-02-04 16:19:14'),
('62c7d7620a017f064588e045f32de1b1294b0cecd08075b6759f8abb96bcc2525142cd1c73c78112', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-27 20:26:55', '2026-02-27 20:26:55', '2027-02-27 20:26:55'),
('6307affd338ab2b8f1914bfc0215ab0f2858a626dce2970300f75df03170ad0e99660e656646569d', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 14:56:52', '2025-11-13 14:56:52', '2026-11-13 14:56:52'),
('63080e2ca93ddd21e8d7b155b80d462f775343f677909791e69693a1a5ca740326f89246d0f1fa47', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-09 14:06:58', '2026-04-09 14:06:58', '2027-04-09 14:06:58'),
('63134dde363fd4a71322fa9a9f139ca644dcb9d875fdd2aa76989a142395c072ea4900fa809d50d4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-16 16:21:19', '2025-05-16 16:21:19', '2026-05-16 09:21:19'),
('631f1db0e154351047a175e31a83f1606f5c9a5ee2517987d34be5d502e7a3972925160e573b8bf6', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:54:09', '2025-11-06 10:54:09', '2026-11-06 10:54:09'),
('6321d4e4e493461b415f6fffef9d2729d212e81f9b3b0aa42cfeff4798395373485546a64be876ad', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 07:01:03', '2025-09-23 07:01:03', '2026-09-23 07:01:03'),
('632c835ec415eeb327ebd15b1e248ff5de512282fafb8dc6a2e748fb0af6a3334438a3b9fde90404', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 07:40:23', '2025-10-30 07:40:23', '2026-10-30 07:40:23'),
('63461190f1b18b643622efa6a74829c324eb0f38603318151b76d273bc6a220919ef48cb6b71d6f1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:34:37', '2025-09-01 09:34:37', '2026-09-01 09:34:37'),
('634f123765ca5850e93dd62005a84acb79bb0c86e1e0b1f4c58dfd971bf132144c0b60778b1c3011', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:50:32', '2025-11-11 13:50:32', '2026-11-11 13:50:32'),
('63692cebc7aadc61b45becc5dd9e3db96278ecf220c24d24538a8f83b952ecb3954f5d22d825296e', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 12:50:59', '2025-09-22 12:50:59', '2026-09-22 12:50:59'),
('638d9031dbe47249517a0a8fba864c3d36caef6c6266a69942eb898db3b00ffdb6299859510e45ac', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-26 10:50:08', '2026-02-26 10:50:08', '2027-02-26 10:50:08'),
('63907df02f6923500d5dac0eff46a2a23fe29803a1737526ec73b46e5f3a3aecc32dbea9746fa988', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 13:37:05', '2025-09-08 13:37:05', '2026-09-08 13:37:05'),
('63ac183cce2f981175350f55647f3aa9d034c0524fc166c2478ea79bfc9780babd39933adbf12778', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:22:37', '2025-11-12 13:22:37', '2026-11-12 13:22:37'),
('63df2f6b318b1fe30dc1e5a9fc3efce0d370f8cd30bdb81c0d2cdc2c3959d5484742dd2dcf636513', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 12:02:29', '2025-06-10 12:02:29', '2026-06-10 05:02:29'),
('63e6a43de4696172248022713bcf6eb024563bd4c50e48fd5e1514a5e3b35ebc8e1026b0219f431f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 07:53:31', '2025-09-08 07:53:31', '2026-09-08 07:53:31'),
('6420999ef8847e727f006d3a3ec5a0ec77e4f843bfccb3a299603743e59d47acf042789f1110d00f', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-12 14:31:57', '2025-11-12 14:35:04', '2026-11-12 14:31:57'),
('6427e1ffc0c4ff1eab85c1a268376d4da987e2848b3fb20c22a817b779f1cc8266f004ee08621889', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-13 16:52:18', '2025-06-13 16:52:18', '2026-06-13 09:52:18'),
('642d6c3d10817360eec287dabc57387ce5f8610901c74e595d91247793dfbac27b222109e108e719', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:43:20', '2025-10-31 17:43:20', '2026-10-31 17:43:20'),
('645a80cedd7396a5789b03e5223101358e611b1265532d725145dc0d0fe5d0d4b1a91321cd91e2d0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-08 10:00:13', '2026-04-08 10:00:13', '2027-04-08 10:00:13'),
('6485806e6aedafd9b89fe4c3a600cdc03b6990f1cb1e749a7f83b763b4a3242bf090e8ad175b3846', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-27 06:58:17', '2025-10-27 06:58:17', '2026-10-27 06:58:17'),
('6492d783be66ed998f8b3e01bde475ea627e74ba369709f283ce1e05026952255b6b5a20146a85a9', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:09:01', '2025-10-31 17:09:01', '2026-10-31 17:09:01'),
('64aa8beb2ca7968f8f0bccb8aa7e50aaf47ca07030511d5df1e4132c3c5ff86a88d65badf693a9af', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-12-01 14:39:38', '2025-12-01 14:40:06', '2026-12-01 14:39:38'),
('64ba97894fb15b526192034315175aa4254f5c016e5ba1e7c6f1af486fe84ff6f60ea7a0867ee75b', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 14:37:38', '2025-11-11 14:37:38', '2026-11-11 14:37:38'),
('64e387be3b90bf0256e2738595ce608118bda1fde5734c748dbef8a966e814affc126c8080ce0b8a', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 15:37:51', '2025-11-26 15:37:51', '2026-11-26 15:37:51'),
('64f67bb77f1db17957910549e9d37ff226d3a355f8eccf95549ce2e8fd7f7ee20b5308cac62b1f64', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-01 11:02:26', '2025-07-01 11:02:26', '2026-07-01 11:02:26'),
('6508ec4aafee518b6ec521a619d8f1ec863283ec5e98e545a92df1a9351e5f59becb0bac3fa6660b', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 06:52:49', '2025-09-23 06:52:49', '2026-09-23 06:52:49'),
('651b3af572b313e04da5398df3ad5adf14a771891bb1c7e8f19f2fbe997bc1b665cbd52d1392f526', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 11:53:13', '2025-09-22 11:53:13', '2026-09-22 11:53:13'),
('65214f5255373e53485ae319134e013aa02711624b2a30b89e3fd92dfff759708f820ae13e066684', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 10:18:05', '2025-09-19 10:18:05', '2026-09-19 10:18:05'),
('656b01ef6f37f6b11996ddb484756f203a6989aeedb1ce9860ade1d34b132ed8050474a78c1f5129', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-23 05:01:26', '2025-08-23 05:01:26', '2026-08-23 05:01:26'),
('65abbade1366652e1b3dc56d56856afbcd7c84f1b530c32dd114da1f36213316f8370bde87bbaa77', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-09 11:10:27', '2025-06-09 11:10:27', '2026-06-09 04:10:27'),
('65ecd21b8d660fc97fd7352223cf58701bdb02c0dfad95e76f58ed5cbc16dccd053a5622a4e7afcb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-01 19:14:01', '2026-01-01 19:14:01', '2027-01-01 19:14:01'),
('65f19c6adf204e067980ac766acdebf44fddff48eca8de32a35b01f3351282bfe5bdeb18063b4f2c', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:38:31', '2025-11-11 13:38:31', '2026-11-11 13:38:31'),
('65f3e745c47a9d57e891940af40f8527caf9c6380be293809d0b7e4e0f76561a446ed020864a036f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 13:38:54', '2025-06-11 13:38:54', '2026-06-11 06:38:54'),
('660a3ec5965ea914e522c59bba26d7a6b4cb697b9a364dbbb96404f1dbb755818c664bdca70b249d', 76, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 06:37:10', '2025-09-18 06:37:10', '2026-09-18 06:37:10'),
('6619d6b515ffb37c1aa40c55018a75c47079a1f8388df898f133532558aea1e32112f1ab02441641', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-14 09:36:36', '2025-08-14 09:36:36', '2026-08-14 09:36:36'),
('6623e449af423d60b0495a31ccc098a383bcda72079dc749bddbe15b21b1a53af6674c8f2bb5e7e7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 16:52:50', '2026-05-25 16:52:50', '2027-05-25 16:52:50'),
('663f26d52d0976cf4bff08535ce8c061253a09cf66aea26f76a29ad478f289d960d5f52cbb369e59', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 14:40:20', '2025-12-01 14:40:20', '2026-12-01 14:40:20'),
('665218633ea51d5a8f5ef1a8c2f55a4d97147455051689a004650730c6e80ae81411aac7e2aac5c7', 328, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 18:02:09', '2026-03-16 18:02:09', '2027-03-16 18:02:09'),
('66549961f09c90ebb6966bacc42613ef3651bd06d5431839d2639da04f101344ec2cd7aa46304675', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-27 18:57:32', '2026-01-27 18:57:32', '2027-01-27 18:57:32'),
('6661d17e1e7eed7661fef03728f8ab2cd166be95a12e1f0be70ccc4f156ee98c772b7820a0cd4b64', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-23 10:35:19', '2026-03-23 10:35:19', '2027-03-23 10:35:19'),
('66908474b88b646ff2161c2f0a1c59bb82cfc5b428c5f0df9c440acd910fab19145bc9610f0136c4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-14 22:41:24', '2025-05-14 22:41:24', '2026-05-15 04:11:24'),
('66a8d6ef6279dfe67e9bfea0ee4d354f2bc38d94b1e305407b970a58cd9c3105bab3aa41f7cba5a1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-17 12:29:42', '2026-02-17 12:29:42', '2027-02-17 12:29:42'),
('66cc975cd92e7aab97c037192703730c0da91654531358d7de5b130d7c8708f48912b92e160d69c7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-05 15:10:02', '2026-05-05 15:10:02', '2027-05-05 15:10:02'),
('66e68b5dc953b5bd8ad7a5beb206e5c1fb007de4219fdb0c3ef06362e9528c04f0d6f222cdcbacf2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 15:59:44', '2025-11-11 15:59:44', '2026-11-11 15:59:44'),
('670a2a9a666777ea480885cece8f55ba5dbb1c5a56e2443208105f5da61e6da47e395634b016068e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-20 22:26:10', '2025-05-20 22:26:11', '2026-05-20 15:26:10'),
('671f36b3eca5774b166405e53157b7c1cb421b2c093817e61ac0203450a29c9db08c7cd6d96163c8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 18:01:39', '2025-06-10 18:01:39', '2026-06-10 11:01:39'),
('6726a1af875c826d41d3268d18c924e5b5b8869c5b6055e367385fe9787ed176f122c8bd544481f1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 14:03:33', '2025-06-11 14:03:33', '2026-06-11 07:03:33'),
('6730ec80672f3ca709b5adc70bca235157e3358e8f04c5440b7e87dbf4ea73a33148a1c0bd77f139', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-19 12:11:34', '2026-05-19 12:11:34', '2027-05-19 12:11:34'),
('6738945f4b3582605f169561a06b3fc9eb20590ce76099c0122fe6baed1b3b7610e8c227da07368d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-11 15:44:08', '2025-12-11 15:44:08', '2026-12-11 15:44:08'),
('67609f564a50a292729be01aa1f3cc9584df4ac1c12c3ebfc54b7e32b2a302297fb21b3ac6bae5b2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 12:56:46', '2025-09-02 12:56:46', '2026-09-02 12:56:46'),
('67805f74b80f880e7ebc640f36745ca6e17591d0a05ad4bf1951e5f747ee21bdb262329ce6a37985', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 14:37:05', '2025-12-01 14:37:05', '2026-12-01 14:37:05'),
('67bb263d5130212e3cc55e8a68a089d4ff0b654cf756ab7773834946dcda948a5249c6d9ebdbb1f9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 15:02:35', '2026-03-16 15:02:35', '2027-03-16 15:02:35'),
('68001e759347ec38ed4f6f8cb52d7ea43d1e56b8ce799b9e104ca4a9fc84422daa18c982ea56f3ff', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-10 09:06:56', '2025-09-10 09:06:56', '2026-09-10 09:06:56'),
('680b3d2a2ecf0478a2a3c6fa18412dfab15f1f5f2935f9862e41f5e50602ec956738ab203ecf6db4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:07:15', '2025-11-11 13:07:15', '2026-11-11 13:07:15'),
('680f009dc77aa02bc8a711ea927011317436850c8486e2b61013212e341c0abf3d676d6a17399b26', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:34:21', '2025-11-12 12:34:21', '2026-11-12 12:34:21'),
('68851592ba71d47815dbf588b81b823744fd622bdb150fcdf21c87e844b1a0d8096e30e80e736112', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 19:11:26', '2026-03-30 19:11:26', '2027-03-30 19:11:26'),
('68902f4295c4c587f2d62c0cb4c6fa1dcfddebc17583dd4d06f3648899ff925b6d16e09f4df5db1d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 13:07:21', '2026-05-25 13:07:21', '2027-05-25 13:07:21'),
('68951bc0cfd7326dcf9349cb79cc9a1cf35a8f2f206bbfeb5bcbeecc69e72a10031a59b2e3648178', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 04:32:19', '2025-10-31 04:32:19', '2026-10-31 04:32:19'),
('68bcbcacec40a6a395f990a04ced51c65379d093b9d1615218ae465c45c1e4f151472d1fffdb6103', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-26 22:15:25', '2026-03-26 22:15:25', '2027-03-26 22:15:25'),
('68e598a0de56cbdcabade911c6c52c021b12f4fe8c0a3a0df2df2a5a0971545799345100e7937489', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 11:10:26', '2025-11-12 11:10:26', '2026-11-12 11:10:26'),
('68ee2a5ca2932be3892f8a1869c08978a2ccc4b8a14898babbd3c8a94dd663cf9a718d1ce95e4cd0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-23 06:55:18', '2025-08-23 06:55:19', '2026-08-23 06:55:18'),
('690ffded780e3db85d7a725c32fc077729d5108e493b759a2c26c39442cdfcc93668d0286abcdba9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-21 22:47:58', '2025-05-21 22:47:58', '2026-05-21 15:47:58'),
('692c772e84ec3383dfaf1285934a3997730c55918d8f1feb475221dd345e0acf731aa4162a2d9109', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 13:12:24', '2025-11-03 13:12:24', '2026-11-03 13:12:24'),
('692cd5a7cb6bf2b62a1f5af9e32dbe2b0985afb3962eb173589fc527baae3ba0b1d67e187653d963', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-16 11:45:13', '2026-02-16 11:45:13', '2027-02-16 11:45:13'),
('6939ea7070c2eb66c146d5181cdfc5b1e5275f6442cb7f06ab42bfe83d7919465de4479c291196f9', 150, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 07:12:35', '2025-09-22 07:12:35', '2026-09-22 07:12:35'),
('69485fd9e6e0f113d2cb76fa3dc022bb67643eaac4c343e23e53b017a8c1e1f29f35cd89b3fd7251', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-04 11:08:54', '2025-06-04 11:08:54', '2026-06-04 04:08:54'),
('69701b79b9838a3c1dc2d5f060ddd99fdd71ae0cc425528950ddc1cb12a657e1a4846d0fd9011e43', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-12 10:21:45', '2025-12-12 10:21:45', '2026-12-12 10:21:45'),
('69be1e8d5c62f50432ff232fb0d7ff301b331d497be33c0f0b231bd99f45f7c3f7190e92948f72b4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-13 11:58:21', '2026-01-13 11:58:21', '2027-01-13 11:58:21'),
('69f837f5184e7c6e35e6c3c54bb77546a85c56da4f46c7d85c96f044ac78a85f77ce6b5a57561a7e', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 13:47:29', '2025-12-01 13:47:29', '2026-12-01 13:47:29'),
('6a01bc57221baf39fe591a09296abb0463f3f00e0e36bddb888e2ca8720a26667d22981f6bddf0f1', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 15:14:42', '2025-11-13 15:14:42', '2026-11-13 15:14:42'),
('6a48de74b4c8c8686efaee9190f20eb811dfa2f0144f0920b2fc2f0d4dc3bf3309aec19702b07106', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 13:02:29', '2025-09-18 13:02:29', '2026-09-18 13:02:29'),
('6a4c3428ac44cf504834762090ac842d4a39f0e0fa5c7fc140712399bde073d7a41c4d5b916abcdb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 09:58:18', '2025-09-19 09:58:18', '2026-09-19 09:58:18'),
('6a86d74ec55f9818b7246294a4e2805218dd7de81a527c1d3c8c30ac6021df57c63920cbe1e3a351', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-16 10:18:43', '2025-09-16 10:18:43', '2026-09-16 10:18:43'),
('6a95af762c5e5af68f459b304c640eb83021d58e6bfe6f65b512446b55e8169712659665c55aa119', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-06 11:10:58', '2025-06-06 11:10:58', '2026-06-06 04:10:58'),
('6aa84248cedad3d38028379b90156a6913c53af4455632c3fe9f85f43157575543ca7bbe66683db3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 07:03:06', '2025-07-16 07:03:06', '2026-07-16 07:03:06'),
('6aa9b78e69c6b2d9c4cd3ba55c77124f747a272de02b5dc01b0c69ced86d4c83208e44a53b9f643e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 13:22:00', '2026-03-30 13:22:00', '2027-03-30 13:22:00'),
('6acb3a3dc4fdf0998db835ed37f0398155368fabd5e6f3319baaa230dbcba3bd7c826bf51534d519', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:52:21', '2025-11-06 10:52:21', '2026-11-06 10:52:21'),
('6b0f39d468c6d9298e90fe2ec54625c67722520dcb13e09b379eb8577ce84890f4289b05fcc2fbe3', 268, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 14:44:59', '2025-11-26 14:45:29', '2026-11-26 14:44:59'),
('6b17190b85ed0177a55ef105dde6efde56d8a113fa3447e032a37ef7076d7ceba5840bc74fdc03cb', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-12-01 13:20:00', '2025-12-01 13:29:52', '2026-12-01 13:20:00'),
('6b894801fe9b1f0897ec400f2f3ba3258e5c093364d376d4bfe1249a2a30d605490a4f8e5f8862ca', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 18:56:18', '2025-11-05 18:56:18', '2026-11-05 18:56:18'),
('6b9a2228bbadac76e73561a6981af67ca9a511feefcc252ffe6ae09b51396d24848906695ea6ea43', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 10:47:55', '2025-09-08 10:47:55', '2026-09-08 10:47:55'),
('6bb145a12cf38930dc57e0524a1b84489caf012fd543fc2e52d3c4793cce8710f968065a67de7bcb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-10 06:13:57', '2025-09-10 06:13:57', '2026-09-10 06:13:57'),
('6bf388203260d3ea5b5d5ebe189aa582f2d442d9c7db4d7e3d614aa9ac907e33ef252a8f672bcd3f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-26 09:52:54', '2026-03-26 09:52:54', '2027-03-26 09:52:54'),
('6bfaeb8aad9ed596288fc8322c312b89b4ca50c496080a32b1a9012b3104f19da0d4e4c2c47e5769', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 05:57:18', '2025-08-28 05:57:18', '2026-08-28 05:57:18'),
('6c0fa11db4f61236206e448c8c2386ba322dfbe0e17408f9c0286a87733d4d6a5ec5a3feab5670fb', 21, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:55:48', '2025-07-17 05:55:48', '2026-07-17 05:55:48'),
('6c5a6da1d8931bfb26a20de2be359e82ee6e6aac4094133b798366884156982369b082efb1ecebc0', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 11:58:30', '2025-11-12 11:58:30', '2026-11-12 11:58:30'),
('6c65514d7c32b991bea2ca47282523684fb9468e9354724c7c90a2fef0e3f7bec3b1e3945a202250', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-08 09:47:22', '2026-05-08 09:47:22', '2027-05-08 09:47:22'),
('6c788f2b3092932a74b51b1cd3af7e08a8246b4d6254b1601ec806d3d9601e20eb5fffc85f70fb0c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:26:48', '2025-09-01 10:26:48', '2026-09-01 10:26:48'),
('6cc3fde42afa29552d4bf11d8a62dec79677c683550c2a6d2922b64bf30f700ff6c98d314c60d519', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-29 05:50:15', '2025-10-29 05:50:15', '2026-10-29 05:50:15'),
('6cc579e22b834a50ae8db8878191145d430ec4f84f0cd998374f621e4ae629d10e2144ecde0e7612', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-01 04:40:39', '2025-08-01 04:40:39', '2026-08-01 04:40:39'),
('6ccb7bf68883ad949da1549fa2ad46f2912e9feecbe06f0b77ae492f7611d0f83860bc0f5daf4352', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 08:02:42', '2025-09-05 08:02:42', '2026-09-05 08:02:42'),
('6d20b55101cd23c7d827cb327c1b6e2c91c768f292d6a2f5f16a9a90a8ad08ecc6f4ca55872a338d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-11 15:38:39', '2026-03-11 15:38:39', '2027-03-11 15:38:39'),
('6d4552b720bc45a6e97edf6dfc4a034eab40f8e87aa2824b2ae2624e3339d635e6920b6caca3c2bd', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 06:57:03', '2025-09-23 06:57:03', '2026-09-23 06:57:03'),
('6d5e9cadf00eb204530956b7bd499b8620674de591cf5d4cb55f40abc188c2459b49c12d6e735ac6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 11:54:22', '2025-11-05 11:54:22', '2026-11-05 11:54:22'),
('6da81179b468708016eee9b9b3462d0384e497daba6304203f950fd7f497cb09680f3c4662c81c63', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 13:32:36', '2025-09-17 13:32:36', '2026-09-17 13:32:36'),
('6dafb9a3e3dcafa04a1f1b72d11671704da4783ac08a83b9cbfdfb2db743bac41fc93fadb02a392c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-20 16:02:26', '2026-02-20 16:02:26', '2027-02-20 16:02:26'),
('6db5c8b11eb8f876692457f404d2afe3ea9e19e331c2efa464cadfbbf34a8f9f9d9728d609094fff', 268, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 14:43:37', '2025-11-26 14:44:42', '2026-11-26 14:43:37'),
('6dcd89bfc287f7ba10db97bbe3c4b39f41c55e34735fb245969b24a1200b041b8528b0c4113a92dd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:04:32', '2025-11-13 12:04:32', '2026-11-13 12:04:32'),
('6de8f57a064b5430b3628589095d0fc266866469eb028b16b7af1f934441ffc6c93aa9513f0604ef', 23, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:58:16', '2025-07-17 05:58:16', '2026-07-17 05:58:16'),
('6e010a5b3a028f475fa328b01ebbe1870dfa2236dbcd12147ec28a51cf438a186f8eb0e9ffb88c26', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 11:34:26', '2026-03-30 11:34:26', '2027-03-30 11:34:26'),
('6e015ace98b2edf3704272cbcfc326b1e9820a90f7c8dafd9c83867182bc75ef4865ba177cc2bad5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-29 09:59:20', '2025-11-29 09:59:20', '2026-11-29 09:59:20'),
('6e3183da6bf7714f59346d1cffd49a8dd7964512fbdf936db67c347b70b327062c860e426096aad4', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 10:27:47', '2025-11-12 10:27:47', '2026-11-12 10:27:47'),
('6e5a2c5a16fea25a7b9a74da26c7dd4d876a9bde1e71ad4e3554baa1ee4c0a41d8ca96ceba2b9f28', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-31 11:39:00', '2025-05-31 11:39:00', '2026-05-31 04:39:00'),
('6e6041089f50fdc2d2fd8ba01ec7804dc07b92c5c8517ec35adab19a937a9dbdfac526db3ee6b327', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 10:59:35', '2025-11-26 10:59:35', '2026-11-26 10:59:35'),
('6e799595807a47a07f4f09a76d583f1cb5b252557a35b403310cee787391e31c7d123e0aa742b083', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 17:30:53', '2026-02-10 17:30:53', '2027-02-10 17:30:53'),
('6e864d9517db2482d8732109202cd9153b03ccc319c1d68f25f00661525b9eea7b9f18a58c2eb8c7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 10:31:45', '2025-09-17 10:31:45', '2026-09-17 10:31:45'),
('6e8d377d8e6356b46e126bf5df032db2ce5703e0d3a20752e22b3de9da94ad4011bb5bc08fe10252', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 07:16:13', '2025-10-30 07:16:13', '2026-10-30 07:16:13'),
('6e94cb926f560d1fb14fbafa8d8d4f88a6deb6fd7558962e6682094bfc1d5c6a978cef311aced4f9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 13:02:53', '2025-09-01 13:02:53', '2026-09-01 13:02:53'),
('6e9cbebadf8a8259f7b7b290b036300c173f762edcbf4f84c8271093b462bde07b94673267e10cc5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 13:10:06', '2025-06-16 13:10:06', '2026-06-16 06:10:06'),
('6ea009fd7b28ef7664af6eddabc4afb2c79440d970909f02475518183928cdd00154370d43eea6e1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 15:36:47', '2025-11-26 15:36:47', '2026-11-26 15:36:47'),
('6ea6268eac4096ffe774316ce908d989b10f91a47e0d19e5ffc477fb19061c218a82cd5da0e1d228', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-16 10:41:11', '2025-09-16 10:41:11', '2026-09-16 10:41:11'),
('6ebef54619f6342a32279df5bdc1c9a5ef2d260e1fbb29c08ebe89afe355ee69b8f88b77149d3782', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:45:40', '2025-10-31 17:45:40', '2026-10-31 17:45:40'),
('6eec3a329a29555217b3b0809fa1075eeca1e2e122da626c333c3839471f816df64e4d9f1122eeca', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-25 10:21:21', '2025-11-25 10:21:21', '2026-11-25 10:21:21'),
('6effa815233a1bf50f6a25697c83957366f827176141d09778c39f62dae665be18bcf816098ff60e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-12 08:24:31', '2025-09-12 08:24:31', '2026-09-12 08:24:31'),
('6f1ddd4a8d99c46f0f99c8c3a5bcdf2c10afe7a66396e9b2c98504802bb2b724bacf0504591c3bb4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-11 10:17:04', '2026-02-11 10:17:04', '2027-02-11 10:17:04'),
('6f317a6823b48d7fa84cfabb86aebe36bcacedc28d952664d36d43e432c4ce6d086363942bf73ece', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-15 07:53:39', '2025-10-15 07:53:39', '2026-10-15 07:53:39'),
('6f9a11b1f97d69540f0f8f9014865263e236350eb2386c4c4577f2ddf48b25675f9a481cb17628e9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-24 10:23:01', '2026-02-24 10:23:01', '2027-02-24 10:23:01'),
('7022268bc524d0da563d956c9b0956e40e625f4689fa06634f8210d5ae73ce7febbed7a701bd214d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 15:22:55', '2025-11-04 15:22:55', '2026-11-04 15:22:55'),
('70284361175481e758a36e97042a39511edd50febbe5b5b0fb72d5ff26ba09cfd14281123b1566b7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 10:46:15', '2025-09-23 10:46:15', '2026-09-23 10:46:15'),
('7046cd2d384ec421720b86d8b10189b865c718ffd7469ec3675ef69375166d9e68f0284a9921f12a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 11:08:54', '2025-11-06 11:08:55', '2026-11-06 11:08:54'),
('704e7e59c928868fa2e0dd2757a9e0c42804708db6f3baf903aee221959bc6c31979123d0fb3368d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 04:43:38', '2025-09-03 04:43:38', '2026-09-03 04:43:38'),
('70a084fd3a5fe3b85f9925e487e1bacb2821f523d21eb16494062616d56ab786b1c1009018ecd9df', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 04:05:38', '2025-09-01 04:05:38', '2026-09-01 04:05:38'),
('70caf4778b2eb8606cc89ff16991eb1de974d186fe69ccbaacd34e497445c3772b9c58da0e21de9c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 18:33:35', '2025-06-16 18:33:35', '2026-06-16 11:33:35'),
('70d02da5411f0f58e82d34024603ff89642cf1a6f218c610fce9216aa899ba1553717d4d9aab854e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 16:08:44', '2025-11-05 16:08:44', '2026-11-05 16:08:44'),
('70d2cc6a418db845061ed3312df26493f2377cea301db577abbbd21ce5947612763274ee5e9b73dd', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 12:09:10', '2025-09-22 12:09:10', '2026-09-22 12:09:10'),
('70f600cc85dec815551ad3e5bc7949f4fe0c29b1cdf03fd25254a25069e40826782ca71967070124', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:05:14', '2025-11-13 12:05:14', '2026-11-13 12:05:14'),
('70f975e5d7d21fbde7cc9750963fe1e76097bceb0efef5f00faf6b3493ea27847caf4f37c6ce214f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-20 15:04:21', '2026-01-20 15:04:21', '2027-01-20 15:04:21'),
('712acdaf0dfbf4d9568faf22afce2b0c0b48165a3ddf48fbc583c7228e490a7bd8d4631d77d7c8f8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 18:26:15', '2025-11-07 18:26:15', '2026-11-07 18:26:15'),
('713f98222e12235b7af15f68cb5458b1247f2faebb0113decc552ad5841fe67e332f933ddf210abf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-30 13:29:28', '2026-01-30 13:29:28', '2027-01-30 13:29:28'),
('7155dd8b7dc1368763956635fa3926872cd595938c3eef86ff3977b6683eeb0d20beed78850353a2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-02 18:33:23', '2025-06-02 18:33:23', '2026-06-02 11:33:23'),
('718b4ca361a043772946220489f973b5ea536ab1775413ac5c12cbc147382ce6994f2bab33a3e590', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-27 16:24:45', '2026-01-27 16:24:45', '2027-01-27 16:24:45'),
('719e2aaa90171ee5c288c66eb803bce680933af9586a089b53c02782e6723972a6062b7c3ca8c86e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 07:26:51', '2025-09-01 07:26:51', '2026-09-01 07:26:51'),
('71b4e604a0a94dcad7464cb698e08404dea4b62fa1bc3ee3a79a28cc5da218b8d8e50e7c159390c9', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-12-01 13:12:52', '2025-12-01 13:16:25', '2026-12-01 13:12:52'),
('71f46b2ba51b60438435f7c076e561e93502593ab44875ab509b50ef99d45a179052b12388b57f2b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:50:41', '2025-11-05 13:50:41', '2026-11-05 13:50:41'),
('721cffd9f65e4aeda90a21f3ef13c0bd064842cef75e99f7ab37cbca0d8ad0d643ced042f19414f1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 16:51:39', '2025-06-16 16:51:39', '2026-06-16 09:51:39'),
('7247c5785caa1ea479a15b8b4f06bc06400a72facaeb3b602627c70d6d356590db19ad32a517e396', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 23:55:15', '2026-04-06 23:55:15', '2027-04-06 23:55:15'),
('7262ec28014039f8b93fe45a2f4e34bd07abeaca1aa694398901bdeeffd7824f8f33dc36044fb1bd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-20 00:39:16', '2025-05-20 00:39:16', '2026-05-19 17:39:16'),
('72842ecc00c7755c410ca6919183d319cce1da1946382ea9997d34d02f7fb02d279bc326a3446038', 268, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 15:10:50', '2025-11-26 15:10:50', '2026-11-26 15:10:50'),
('72b783560973c830f4d8f276100133bdca0343e448a00454ed152c3e021914b406c1451d8c92c331', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:16:38', '2025-11-03 12:16:38', '2026-11-03 12:16:38'),
('7326db6a6569d2870177df82f4f498bca624641f8198aeb6f1ca3156a2f1da8085aec7aa4b51721a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-02 15:52:49', '2026-04-02 15:52:49', '2027-04-02 15:52:49'),
('73288b4b8273a9fa923bb3919a497a8a2e18b23fb3951484677a806d7c1c91d9f34c9a977e62be1a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-20 14:45:08', '2025-05-20 14:45:08', '2026-05-20 07:45:08'),
('7352d46f27e2e7c7682ec07d68b8a140a42662748205430729e33bb6610a4fa0e2eacdb381d66e08', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 06:10:10', '2025-10-30 06:10:10', '2026-10-30 06:10:10'),
('736e1303666b9b4dd813bd9b39119d813741d802f68fe65a562a04bed2365dfed680331a5f13c8e4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-30 08:22:12', '2025-09-30 08:22:12', '2026-09-30 08:22:12'),
('738a88b5993564b02de0a251a1d338be3ece7bc82026b3f64c8b40a30ec906bcd8c5eb9f60bc2932', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 08:26:53', '2026-01-29 08:26:53', '2027-01-29 08:26:53'),
('73b8b20f70186587faf0caaffc63ccb37fd7e02ab664fef75740b193bb21c4a3315ccd9404311631', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-10 12:21:56', '2026-01-10 12:21:56', '2027-01-10 12:21:56'),
('73b9a4090800c3434b06b0b78af1d483becf2f91325918080c0ac4ae9600b09cd61cf6b9470ad22c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-23 11:54:27', '2026-02-23 11:54:27', '2027-02-23 11:54:27'),
('73e3132521050d9d3b41d00917ce49d4219a66e025705af07c631e0fb5b710d6a811df6946fc3c52', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 09:23:31', '2026-03-31 09:23:31', '2027-03-31 09:23:31'),
('73ec068a887c8d24b64fe57bc0e8bbbf0f300fdf83eb6088d7c6a1e552482f616d9284d6d294f948', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 06:23:16', '2025-09-02 06:23:16', '2026-09-02 06:23:16'),
('73f483539aa26f581b2847208b11dcb1d560f2f7e94faa8721643812adc29e470bde52536d23161a', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:41:02', '2025-11-11 13:41:02', '2026-11-11 13:41:02'),
('73f57e9ce104e30c065f51311d0df98c287194d6618c3ee12c161249524b1742cce28dd50c5e208c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 07:26:37', '2025-09-08 07:26:37', '2026-09-08 07:26:37'),
('7422f498499e26d0dd10a9bf955fc6be691e13f25df7725d67f4dd2d08394dc56b25f36657cfe50a', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 16:32:40', '2025-10-31 16:32:40', '2026-10-31 16:32:40'),
('742caf720d2ffaaf9869b1cd3026a23a1c30eb5025257cc8b63aa11fb520b89d13acb07a0a617f2d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-14 17:36:48', '2025-11-14 17:36:48', '2026-11-14 17:36:48'),
('74476d6269213b82f0b76a842fe012a8ae150e3992490eea66e8e458f86fb9ea30ae259e4a9fb328', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 13:25:17', '2025-09-01 13:25:17', '2026-09-01 13:25:17'),
('74542ee700a31ff1108ef45ba6f411431828a2689e3c85c55575cdb5610ee9e297fb77015d5a9261', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-11 12:55:00', '2026-05-11 12:55:00', '2027-05-11 12:55:00'),
('746e98ddc7c9dafa4008454a6f1129f572d39804e795899e6784beee124f3b4c5b2ae71e40503f95', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-18 10:36:15', '2026-05-18 10:36:15', '2027-05-18 10:36:15'),
('74751be27f871b22f83683b2b5c16118f41f036d9204fc87989543ebf40895346712092f4bb48de4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-26 12:01:16', '2025-05-26 12:01:16', '2026-05-26 05:01:16'),
('7483ecd4fe5c665fbe5c2e459921d432a174397beeac864b1e0cb6de73b7815f36bda5dbb667be39', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-27 09:32:51', '2026-03-27 09:32:51', '2027-03-27 09:32:51'),
('74d9c220d472977398d96a0a245fc540425730956eff5841d9e881388e182c9e676435ec6a58f009', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-21 10:03:01', '2026-01-21 10:03:01', '2027-01-21 10:03:01'),
('7507a694bb773dbfcd9b1ac641edfbd81fc59b9b47969c821a1ca4e207784221c731de8e95d12b56', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-23 16:22:20', '2026-03-23 16:22:20', '2027-03-23 16:22:20'),
('75540b3f6b8e2f5ccea612be5a8747361f2af840a3d1069f60d1c19db7a5bbef50baf38fcedfbff6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 09:56:47', '2025-11-11 09:56:47', '2026-11-11 09:56:47'),
('756a851646a06499d7314947aa24f3c60fdf2283675f8545fff2a5bac34a57f7139cc31b1a28dd59', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-10 09:57:45', '2026-01-10 09:57:45', '2027-01-10 09:57:45'),
('75744954869a38272252412d99684cbfe8ae25c510bfd280a713f828ad8990d3d77d33b9c5b612fb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 07:06:48', '2025-07-16 07:06:48', '2026-07-16 07:06:48'),
('7592bc3908400c04ee6ee2e2ed8f5f1a64ebd82332d87911e451162baea7e04fc6c8f779a499d0a5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 10:35:33', '2025-11-07 10:35:33', '2026-11-07 10:35:33'),
('759a3e4295fbf565582dcfe5b517429f7891a3589d7e9572b3152839efc69d6957b7450d06bc5930', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-22 07:00:37', '2025-08-22 07:00:37', '2026-08-22 07:00:37'),
('759dfcb78d18ae99ab541b84410df6dbaf1a30584b93c9745bd0e298ec15df040e2216239343e882', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-12 21:03:39', '2025-06-12 21:03:39', '2026-06-12 14:03:39'),
('75aeb536cca244968e3672e74b0d0d1a34d20641c658b631fa3b4b8698c2da864844708b36aea612', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-07 12:21:24', '2026-04-07 12:21:24', '2027-04-07 12:21:24'),
('75eae6dbf9a0585d915d39a2a375577b5ef36d63c245b121037a47cfd70962ec3a7c6ad6ae7729fc', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-31 20:51:42', '2025-12-31 20:51:42', '2026-12-31 20:51:42'),
('75fab3921018cf90c581e6765003a5c0375d9c5bfd061e2c1805dd7bcbc9fb725eaecb59815ca351', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-26 04:22:43', '2025-08-26 04:22:43', '2026-08-26 04:22:43'),
('76004b8da2050d7dffa0fc99497f02dc4369d058393c737c12f34468165f9b5df75798921081dc3e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-15 11:26:43', '2025-07-15 11:26:43', '2026-07-15 11:26:43'),
('7610cd4eb5cfff9106aec77c219438cb29545033b3c72609df860aa9b95dbc925a2ae34448170021', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-18 12:25:13', '2025-05-18 12:25:13', '2026-05-18 05:25:13'),
('762d747a927473e4ab6ca6ba0c6d5574dccade07ae1ae88ceaea7cb748471deb79086b8088978f41', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:44:51', '2025-11-12 12:44:51', '2026-11-12 12:44:51'),
('764d2f469c9d1d9dc05decfb101c4ad5cfc035b424bf27f7258a874bd3f82047861bf9e0b7a94843', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-13 14:42:47', '2026-02-13 14:42:47', '2027-02-13 14:42:47'),
('7651f47dee63f90f3ba8407442ecb0d7fa4e433f1d06bc3dc0ffca7b0b26d7de2239940d835ddb5d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-25 15:45:56', '2025-11-25 15:45:56', '2026-11-25 15:45:56'),
('76566efdfc833a931f9134a12d355b0281d8cb0c8bbb0df413e2320900cdb867ccbabf7c2d4ed08d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-11 05:56:43', '2025-10-11 05:56:43', '2026-10-11 05:56:43'),
('7663945a2471692ecaa76eae2e94cbf194c5ba73e3e157987f78a65e812cb2836e34330796502cf2', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:42:01', '2025-11-12 12:42:01', '2026-11-12 12:42:01'),
('769da2d6ff76fd591b48d815abc19a6ac5960e59f22bd1387a28513ee1d49454293ce66f13342d7f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-18 13:05:01', '2025-12-18 13:05:01', '2026-12-18 13:05:01'),
('76d6e6260d42e011b07dd2bdba27a1d3fa0a6d3e12b3d5c77d1812613242df7ff9128771fab517c8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-21 11:55:10', '2026-01-21 11:55:10', '2027-01-21 11:55:10'),
('77520dd428d70ffba3a25e756bcd32600b5335c1dd32894732a46b50f53ef63eb34f347d5c42a9b9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 18:39:58', '2026-03-30 18:39:58', '2027-03-30 18:39:58'),
('7756841ae520ba297a7499eda2a9bb00b6ab047e3c1cae5142cb54e3e204b50c07b9255b7fcac00b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 13:59:05', '2025-11-26 13:59:05', '2026-11-26 13:59:05'),
('77a5a75d562bb2c21c0c478222f00078e5d84da21d035b70d87bc7209e078ef104c6d7b64d0f9e08', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-12-01 11:56:45', '2025-12-01 12:00:20', '2026-12-01 11:56:45'),
('78329525234fc41cb24b745c344530032d13675afefffbc345a6ad571aa208bbf5df60e67182b51a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 20:20:10', '2025-05-30 20:20:10', '2026-05-30 13:20:10'),
('7835238ea1868aef6fd72caffc4061fcabe7ae6de2ad37f081db3899be4465dc056dd9147d171bf1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-03 17:53:29', '2026-04-03 17:53:29', '2027-04-03 17:53:29'),
('783a97d5f996cb918ccaa61952a99dc77d7937baac7c164de83e5a799e1ea9b93790106410911e29', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 07:11:05', '2025-09-03 07:11:05', '2026-09-03 07:11:05'),
('786be82440cff974a41420d192cac2048ec051a4a3be5e6bc498bad4618e85747cff9b47d42ebf67', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-16 18:28:39', '2026-01-16 18:28:39', '2027-01-16 18:28:39'),
('787c2d5984139ac5b4d8c47c7c77f11f4ff70e2c6d16fdff3613c1e5935040ee37e617d986ffccda', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-16 14:59:32', '2026-05-16 14:59:32', '2027-05-16 14:59:32'),
('7881681eb26fb520666493d57f24c37158445a5cd1f3f484fae7a44905680e01b021febaefa719b8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-05 10:02:39', '2026-03-05 10:02:39', '2027-03-05 10:02:39'),
('7881a9a1c78d73e83d1c805e57bc54710669b2ec4bf96fde23e3348542f452c9e2f507276ce38f08', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-14 11:32:01', '2025-06-14 11:32:01', '2026-06-14 04:32:01'),
('78c51b36648d75f4564aee57e2b0abf3837540eaad83520ca39849907292a2e0ff6516f09cdebbc1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 04:41:22', '2025-09-03 04:41:22', '2026-09-03 04:41:22'),
('78ea89f0b947655d6f6b0a601175c91a434b1de4ff0cae2e7b4ea9bd803e6e84af4ca5024a91d8e9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-29 04:58:30', '2025-08-29 04:58:30', '2026-08-29 04:58:30'),
('79042b5af9925ca15d39a4bd0f2669bd5679d3a48eafb3c911f0b5f0a129e8df505056a2fdedf909', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 18:43:33', '2025-11-11 18:43:33', '2026-11-11 18:43:33'),
('793255ce3c16df7079d61ce144866a2a2deb970e8b876bf325266df55f54111e148ce70a281ade59', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-18 11:04:41', '2025-06-18 11:04:41', '2026-06-18 04:04:41'),
('796896d43bdca0d7c3d45d0fa8ba8b62a2cada7a491cf8688d114a833c36a5f81172c139d42ada0e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 15:17:14', '2026-03-16 15:17:14', '2027-03-16 15:17:14');
INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('796b7eabc9691f39d8865c3ad7e4e5e1693ee9728aa02edcd9abaa66adfc60155db5eb1d210891a2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-01 11:22:24', '2025-10-01 11:22:24', '2026-10-01 11:22:24'),
('7976184ed9cb5754471001b97faf99a0c924dfee8af61dae6d97a32079f5d54d666de33627b8d388', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:08:12', '2025-11-13 12:08:12', '2026-11-13 12:08:12'),
('797620b91dd944597239b051bb9f21d94089884672dd5f30f50c380e1bfac57ee42e65851ebe6eae', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:22:26', '2025-11-06 10:22:26', '2026-11-06 10:22:26'),
('798673a1d2361ad49d1226cb8bbf618c61e1172487e99d783aa154689143f5f073f23c25a3c30359', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 15:38:35', '2025-11-04 15:38:35', '2026-11-04 15:38:35'),
('7994f2463c4bcd4bdb5faf92a6f8f106bc001743520564c1a1f4d0c6ef6ddc2b72390877664ea2de', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-06 10:03:45', '2026-03-06 10:03:45', '2027-03-06 10:03:45'),
('799bc116974e205c0ad381bc3070bcf0f8001069cab28299e1a33405a6414b0ef72f720e3937d7ae', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-06 16:12:34', '2025-06-06 16:12:34', '2026-06-06 09:12:34'),
('79b8fcca32ef738e7c7290b8be4e3901b04affbe8833ca5b7686ea83999ff4e56ead533726fc2467', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 16:12:04', '2025-11-13 16:12:04', '2026-11-13 16:12:04'),
('79cb7191b7d8548169eac06bb3d36c4446b470ff922afb31c2253c1710cc7dde650ab20a464fc436', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-17 16:58:59', '2025-06-17 16:58:59', '2026-06-17 09:58:59'),
('79e1bfef190f8cdd288b1ff88755c560f447aa665079d4037dcff4d849c7211faf98e13adf90ea01', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-17 16:30:36', '2026-02-17 16:30:36', '2027-02-17 16:30:36'),
('79ed1bb9baf8f6c9043e50183e753f5c93f75d085c648a48a966cd82040dc7f7353541e40cdbbffa', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-07 14:39:14', '2026-01-07 14:39:14', '2027-01-07 14:39:14'),
('79ef776ce4643145a6a4d1f2e78a2310de1c5a3c13b89b4b47f8bd66ee200258eca0dcce309d55a6', 208, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:59:34', '2025-11-06 10:59:34', '2026-11-06 10:59:34'),
('79f804fa76dfc80c641f811904565950239f1bd1e2fab3c072d2d7f363d5ec6fd9f1692996a51785', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:58:27', '2025-10-31 17:58:27', '2026-10-31 17:58:27'),
('7a126731e15dfbbc2fa459c6116435c4e9f3094c1ea74b11b3143842de20033966447b63440954c5', 321, 1, 'LaravelPassportToken', '[]', 0, '2026-02-03 11:46:23', '2026-02-03 11:46:23', '2027-02-03 11:46:23'),
('7a39c2d841000678a10d66860e9ff4815c7062cffa7433c3b92be7a45fcb77ebc909c7cd49eff862', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-14 00:11:25', '2026-03-14 00:11:25', '2027-03-14 00:11:25'),
('7a47dbf0ff5c842db227cc4a44b853fdea2be7b4ac24345f3bc7d63d0922dc46eaf96cd5916444e5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-27 17:03:40', '2026-02-27 17:03:40', '2027-02-27 17:03:40'),
('7a4c5966a6b65b5b5492d3ce38cb8524e6f1cdd25e0dc8cf889fcbab1f199d7a4b28a6cfb37682e1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:37:39', '2025-09-01 09:37:39', '2026-09-01 09:37:39'),
('7a53da218dfbbff9974e3cdb23145ed8f6795b7fe5668c439eb73ce91b4611002eef7aed5b6eeb4c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 13:21:39', '2025-06-11 13:21:39', '2026-06-11 06:21:39'),
('7a66629abf8611c8c2f5dee6ac3c98d0de942184bd784193c2c192af316c3a8b56cdde8a1697138b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 05:24:46', '2025-10-03 05:24:46', '2026-10-03 05:24:46'),
('7a6e4e7c9c6f7b96152710d57b1f4c494f11d338fb92a3856048aed5f1e114994847bc361db08e8d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-06 15:00:58', '2026-05-06 15:00:58', '2027-05-06 15:00:58'),
('7a88c36ef171c284c03e9a2d2229704abf66661b69242575662f758d7343becf8e4d99b65ffda9eb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-13 03:38:04', '2025-05-13 03:38:05', '2026-05-13 09:08:04'),
('7a9fd313c61faf5fa9a63928a42b0cee833952aab05500676e545e445aad83cfbc33738baa5a4095', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 06:55:25', '2025-09-18 06:55:25', '2026-09-18 06:55:25'),
('7aa695b6fe7962de40fb9a37c78cbdc2cfce89089a11863d52ef312edd5291bdfee8c5b6b2c06b39', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-28 09:06:07', '2025-10-28 09:06:07', '2026-10-28 09:06:07'),
('7aa72bdbe1ca3054965f2130191cc7dd68b211897210e6785dea46f55cd5569db77ab50aacc2649e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 10:48:29', '2025-11-04 10:48:29', '2026-11-04 10:48:29'),
('7b03954b466353e4ef605196c4354f8213998ad3768c316e88425855c80f6811d4a771530df9b235', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-17 15:24:52', '2025-11-17 15:24:52', '2026-11-17 15:24:52'),
('7b053c640edd47e8ab232e34eb4e4243a3fe419d44fa58a9c09b5575ab17cb4075bf0866a61575b4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 15:54:47', '2026-05-26 15:54:47', '2027-05-26 15:54:47'),
('7b44a2b23632220c821aa91695f9a9775e0d159dc95c064e7f9a267d38542a52e7670e57b19376cf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-18 10:20:52', '2026-03-18 10:20:52', '2027-03-18 10:20:52'),
('7b5030d914c451b63eb0e6273125fd782f50aab7ff1dc13eb8b2215dd864686febd8f6ac69200a45', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-03 18:35:01', '2026-04-03 18:35:01', '2027-04-03 18:35:01'),
('7b5fb9080ecda2d294f6d1107d954f9b39d56412e2ab504d5f306c8ec9ac52dd14d56cd9c153dcd9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-04 04:41:28', '2025-09-04 04:41:28', '2026-09-04 04:41:28'),
('7b82f088e6320ac304c705d87db4e1156e073cee257d6dc9a7b82620fc690ccf6bccb5fb08a4f417', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 09:54:48', '2025-11-04 09:54:48', '2026-11-04 09:54:48'),
('7b9433cc2e038a8819723611915f41f9a2881bffefdb53267b03d368073dbc2bcfda534798b58d85', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-06 10:57:32', '2026-02-06 10:57:32', '2027-02-06 10:57:32'),
('7b9b765bccf77b05ecc766f586a1fa5118cb853f9f8af7e63dbd5c3f267b29b083fb8a0e851bb067', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:28:47', '2025-10-31 18:28:47', '2026-10-31 18:28:47'),
('7bc6411c4eae8623ca8ab7dd1f87abbe003e517159056a0cab0941064fb2796c048ac547959e5b68', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 16:08:26', '2025-11-13 16:09:00', '2026-11-13 16:08:26'),
('7bc8a2d2ed037b355310372b340f235a8397fdb78ade714020132f82b378487175aae12308692dea', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 10:35:33', '2025-08-28 10:35:33', '2026-08-28 10:35:33'),
('7be263dc0e8138472d4a84996de065a69fc8aa5f3304e63e235ff071232852c0818f0e37c40de52b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-03 14:51:47', '2026-03-03 14:51:47', '2027-03-03 14:51:47'),
('7c5eb493c8cdf6cb7f7cf9d43173c0126d59e73f918522ec9f6894c497364663c6d0883fec08d465', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-20 15:59:45', '2026-03-20 15:59:45', '2027-03-20 15:59:45'),
('7c61846df2f458f79dd2caa432d482a7231d583c1a82682199b760d6a64317add2eeb4d4edae4626', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-25 10:24:29', '2025-08-25 10:24:29', '2026-08-25 10:24:29'),
('7ce9dea20991bbc463edc81fb4fcaee838f273a5125d20a66c09df2655488e62ee511b8d9098a556', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 05:33:51', '2025-09-19 05:33:51', '2026-09-19 05:33:51'),
('7cf54d6fca66c9b242ee20f1f1c25cb00130ac70855b9a77b1db8c1482fde696a73218e042ef996b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 14:22:44', '2025-06-11 14:22:44', '2026-06-11 07:22:44'),
('7d2109a3bd8241cda0dd85aed21b26d51aa38ed222f62718ee3c06b1d77a435281c47d2e3f22106b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-08 09:10:53', '2025-08-08 09:10:53', '2026-08-08 09:10:53'),
('7d2930c75650e5372f0e9147a46550b9e9cae367abcec9d6d538e2d62aa393a4656d107847be366a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-09 13:56:27', '2026-01-09 13:56:27', '2027-01-09 13:56:27'),
('7d517b9c11826ab7e45ea61f33f84101fa6e1172e97fdff7b677f01cf0a2d7f798e35600defec4fe', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-09 16:51:47', '2026-01-09 16:51:47', '2027-01-09 16:51:47'),
('7d51d5836743b02727522075172a71769f764cd629193090e80b6d458110d122ef4249a27d64cbb0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 10:35:47', '2025-09-19 10:35:47', '2026-09-19 10:35:47'),
('7db718e9077eae36bf6f284642e400270f618eaf805a5cb5e92a0bdb28204ff83eaa55fa4badb60c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 12:46:56', '2025-09-01 12:46:56', '2026-09-01 12:46:56'),
('7dbf9f630b471e1bfed1a1bee21fe5eb53b533be1379683ca33c1718803f6f82ff36d8d205fad42d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-06 18:06:23', '2026-05-06 18:06:23', '2027-05-06 18:06:23'),
('7dd7146d91ac4d81b17e644e594e0a425e2237f44a7b95586461c2a2140ee0f42b916ec257c2763c', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:39:30', '2025-11-13 11:39:30', '2026-11-13 11:39:30'),
('7dd8c5f0ad3f6a9986dc022580ccf0cc8b6b51486f75fd1d8cab9c56da9b49b49ceabf2ccd04f8f0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-09 10:21:33', '2026-02-09 10:21:33', '2027-02-09 10:21:33'),
('7def6c8344d8bb2a33483a422178d2e0fc50aa63f3fbf9d3f9ab4ef33fc0e172b096bc5dc47343a2', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 18:08:50', '2026-02-10 18:08:50', '2027-02-10 18:08:50'),
('7e265e6e01e6953f092fa22c8b7c2b8e3aa877ca9e2a770a87f8ab260efe6b62f93f05c602ec098d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-28 11:49:24', '2026-04-28 11:49:24', '2027-04-28 11:49:24'),
('7e271d36e5abb0dece3958159e9217c1da6c1d77165e4ff4f50d992a8093be58a3466e32cd9dae82', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-26 17:24:37', '2025-12-26 17:24:37', '2026-12-26 17:24:37'),
('7eb3ffa06b5a0972c4475833c63215583c51fff5fd08de43a763ad533e9ea7d40fc21ec12b7dc1ac', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-15 17:01:45', '2026-03-15 17:01:45', '2027-03-15 17:01:45'),
('7ee507432d3a62a2f41dc64aceb045cc607751c517200a249f6f5af9681d5e261e8bc2e1d00389f7', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:16:56', '2025-11-05 13:16:56', '2026-11-05 13:16:56'),
('7f09038fbe97eec2918fa5377765ca10e1e9c37f7ab9ca0d081769fcbbf50f2a72db3025f8f6ac67', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-20 18:20:04', '2025-06-20 18:20:04', '2026-06-20 11:20:04'),
('7f121e63c835e167e3a5bf48ee15aa5090bd9bf63373f674905d973cdf58b923b3cd280789931b03', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-11 11:10:54', '2025-08-11 11:10:54', '2026-08-11 11:10:54'),
('7f992e5e794a5ae77866508af163253b04b33385b5d5866a7e5feb086851c14208d3dad9792f47fc', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-06 06:00:11', '2025-10-06 06:00:11', '2026-10-06 06:00:11'),
('7fa18e630405cc499157f85a78dce34c29f3edab92bacda84bd980e169e813faed91c959b630455a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 10:33:17', '2025-10-30 10:33:17', '2026-10-30 10:33:17'),
('7fb0651d7637daf82afd8515ef56d3112ab7114ecad1a2f7d5e6ad25946d41007823590b8fef8b7f', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:20:57', '2025-11-13 11:20:57', '2026-11-13 11:20:57'),
('7fb49d6ddf1416db8eff8792c1878e8d09050f3d444cc0eb5110ae0222acaa4db1686c94f2443389', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 18:04:18', '2026-02-10 18:04:18', '2027-02-10 18:04:18'),
('7fcf9dde0bfeb50f2c695c69181ab5f9e27e2b89b0aea31939a4ea589ea8d9235336f534706d5813', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 15:12:53', '2025-09-01 15:12:53', '2026-09-01 15:12:53'),
('7fd14165445fcd7075b830108338f3d309cb7a410857075b796299e190e469f796c7a52cc1ed9aae', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-27 10:44:19', '2026-02-27 10:44:19', '2027-02-27 10:44:19'),
('8087dcad01bf790772481ac66ba158e073b4e172d7829d23f18b6a303319ff6eb1fa1101472b60a1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 16:46:46', '2025-11-13 16:46:46', '2026-11-13 16:46:46'),
('808c07c0ddfa7e3215e2a6a101baef2ac5ef51a8f84890e51ede2ee3dc1253b00a4a97336c8ab01a', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 13:14:36', '2025-11-06 13:14:36', '2026-11-06 13:14:36'),
('80a083e5b8924c9e5a187ec110c41246cd36d770fc4a031af67a5ad6a2074eccfee822b5f713e8f6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-13 12:10:42', '2026-03-13 12:10:42', '2027-03-13 12:10:42'),
('80e91026e0c66fc2260efdbfbe5213f5e904bb1c25f24a11c2dc1dce8011af11ac68b7ca0df3379c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-09 11:11:36', '2025-07-09 11:11:36', '2026-07-09 11:11:36'),
('80ef1352c7a38d72fd53260682fbd9baa955d737d2f274d0eacf1a6ba6171064f72828ce268c4363', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 04:48:07', '2025-08-28 04:48:07', '2026-08-28 04:48:07'),
('81062330658afd85d7529a19766e0e54933ef8cce4580bd706e42e7a264f208e50eb53bba5c02e09', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 05:27:34', '2025-10-31 05:27:34', '2026-10-31 05:27:34'),
('810dd2555b7cfab68c6c426895ba04ae23f1d56f5026043431747f0816d91f12e06c2e5be5fbf576', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-15 09:51:22', '2026-01-15 09:51:22', '2027-01-15 09:51:22'),
('8117d51d12650cd4cead04e16daeb9147e6c1eff51761467d0f01d893806927b87d22a06dafd6f19', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 16:16:36', '2025-10-31 16:16:36', '2026-10-31 16:16:36'),
('814425d062fdf5a7742a90917bf462833364c5a4ffdc9f16fc318f0a089ff500b7f1a8dc521c7806', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-12 13:53:37', '2025-11-12 14:20:17', '2026-11-12 13:53:37'),
('81443a131bb0305a5877788249016395c6db238098f6efef1a65c7a2900015ef58f219ff4bd73179', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 15:42:23', '2025-11-05 15:42:23', '2026-11-05 15:42:23'),
('8146b9e7e722bd877b363572d211f107e71bf888856ddef4932199ea76bd638aa22533674c9dbd33', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 15:15:47', '2026-03-16 15:15:47', '2027-03-16 15:15:47'),
('818af4f788f229d767ba3ae6f505d72a6f1658b4d56c71c303eebf9b72d472fc7079f96fd83e144f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-27 05:47:53', '2025-10-27 05:47:53', '2026-10-27 05:47:53'),
('8208bcc7fb209d3098e8488a354fef54fc828aa7b67380355f215b8f72fd96b08fa59d916a84b576', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:43:33', '2025-07-17 05:43:33', '2026-07-17 05:43:33'),
('820e346e85444dd1072615e2ad7d482a2532f799f36cd6fe5a9a0f2b29d44e59b9807fd65521ae67', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-09 17:27:03', '2025-06-09 17:27:03', '2026-06-09 10:27:03'),
('8282a829c7daceca1c518a4743cd296c136658ba5199ac4bca42ade3c0baba3afdb6ac71192a640f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-08 16:54:16', '2025-11-08 16:54:16', '2026-11-08 16:54:16'),
('8295e8ccbf938e8a2e6a3ce2c57cb37993cbc9a7925e0063b41848b1582d088dfc83efcef48650c6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 12:03:15', '2025-11-05 12:03:15', '2026-11-05 12:03:15'),
('829870c9b419ad40f9a29a7b80fd3759cf66c7aca503eb1bd32fd556b1edc16efb948a6d34b1c514', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 12:51:57', '2025-11-06 12:51:57', '2026-11-06 12:51:57'),
('82eaab21d18e35214d36f2eef77ff5bf9f87086f66df6913dd39e9cbfed54cc89db72be6e98e6145', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 04:34:07', '2025-09-19 04:34:07', '2026-09-19 04:34:07'),
('82ee0769308498da03ce014103e17987f501492c74627e4d5800a3fd55fe500e3b9cb5f8a02af7d5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-12 13:18:00', '2026-02-12 13:18:00', '2027-02-12 13:18:00'),
('82efc75712544dbaa752cd34472da1cf50f3fe63a31e259089d678c264ef1f824ebfe5818929f6e0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:37:21', '2025-09-01 09:37:21', '2026-09-01 09:37:21'),
('83011e4a07e1ebead2643792af7614276c5b1ae4acc957da6f186d36727f8cf6777ccbfb688f7dee', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-28 06:22:17', '2025-10-28 06:22:17', '2026-10-28 06:22:17'),
('83344e205fbd983e572a27439a66f9a031e8d7e6901c2c5c7f201aadcc6e036d7889b1311662e2e4', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 12:36:49', '2025-12-01 12:36:49', '2026-12-01 12:36:49'),
('835087122e8b79bd67ffed1c605c6ffda96a32af3e65f14e3eb9e00b7aabd08272767584319ee652', 331, 1, 'LaravelPassportToken', '[]', 0, '2026-03-17 16:55:25', '2026-03-17 16:55:25', '2027-03-17 16:55:25'),
('83584c400073965581a536e13d590c02ec3bbfb53a468b940dc8a7c2dc49472bdc33d4d827b68e16', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 04:47:21', '2025-09-02 04:47:21', '2026-09-02 04:47:21'),
('8382e5b13671100329ad03b95814710b7c2731619e516a010b90d4ad1dc85d640c42d0cad47fdf87', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 18:43:31', '2025-11-11 18:43:31', '2026-11-11 18:43:31'),
('8389d7fa6cb11df4e5c772db351bbdac9f01d48ac7466087bd7ba79de1c3526cbf8b83c89c62c2e6', 256, 1, 'LaravelPassportToken', '[]', 1, '2025-11-20 16:26:41', '2025-11-20 16:27:02', '2026-11-20 16:26:41'),
('838fe06663524ecdaa892b8926395c71e8760f0de4611625e5ca40b94eaec977a2d377971cc10647', 288, 1, 'LaravelPassportToken', '[]', 1, '2025-12-01 12:00:52', '2025-12-01 12:36:36', '2026-12-01 12:00:52'),
('83b822a81c2df8a897ce641fc8fad1dcc24bcbd54e013bb48907ea9d8f8e1f89f5e87454ab9c5a18', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:28:29', '2025-11-05 13:28:29', '2026-11-05 13:28:29'),
('83dfb06e24ee7eec66b470db657349c118333f3ed8b63bee368bb2ee24677c561a3f1a399f6232bc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-25 11:42:46', '2026-03-25 11:42:46', '2027-03-25 11:42:46'),
('843322516dab0671516914785c3b57edef733adad3530b5d337ae0f010b9c1e7b077433db549bd3b', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:15:09', '2025-11-03 12:15:09', '2026-11-03 12:15:09'),
('84466d37e9a0176e5b1ebd338deddd6cedd0b1af6d6ab90eba5d93a207146218455461dcdf038974', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 11:00:37', '2025-11-13 11:08:05', '2026-11-13 11:00:37'),
('8495dccbafbc085f8463457856546c718ded69c414af47200bceb50a36d046094e5090c0eaa5bf17', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 07:29:06', '2025-09-05 07:29:06', '2026-09-05 07:29:06'),
('84bd09cb31809d2284be2a76762a39f91afdf8b6884636f38bc0fa86d8d170e0f1b0d7d35aec997d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-06 10:19:18', '2026-05-06 10:19:18', '2027-05-06 10:19:18'),
('84cee358930e8d30abedf4f18049054947f99ed50ddb8e5b45cf67447035589aacc68d919adc3d04', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 12:54:04', '2025-11-05 12:54:04', '2026-11-05 12:54:04'),
('84e6db5f19ded78f69b3f75bd669ed063adb3ad5c5868ee607e34473c81d812877dea5246ee08e7b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 18:24:26', '2025-11-13 18:24:26', '2026-11-13 18:24:26'),
('84ef405a88b5a531acaddf1e45eb5834ed75ed267751c5761c8c6983dc07e50059df72709819b276', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-14 07:16:27', '2025-10-14 07:16:27', '2026-10-14 07:16:27'),
('84fa9f560c253743f0da394b92f7406f11ebf42a2c6e65cf2b4155fea7d880b2898611f5fcd909a3', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 14:57:57', '2025-11-13 14:57:57', '2026-11-13 14:57:57'),
('850e689fcffcae2830465e343b58410cfd92a88fe3e58ca312bb70bafe5aed67bc2d378e3df73e88', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 13:42:52', '2025-09-18 13:42:52', '2026-09-18 13:42:52'),
('851bc00cc1cf1da0e5722b108a6a8d5073ecb02e71d52ed51dfbd055e012c6deb636916338406a14', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-21 06:04:13', '2025-08-21 06:04:13', '2026-08-21 06:04:13'),
('851e63c01bfa120428b48fce10f2a259cd92bd2724de48a3373f9546a3ac97e4fa2b3fde169349e1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-02 12:12:46', '2026-02-02 12:12:46', '2027-02-02 12:12:46'),
('852dad37eb2075fee7f940a36e1e083df81b419e1413e97085891120c8f1946a915cf26762bdc61a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-05 10:09:55', '2025-08-05 10:09:55', '2026-08-05 10:09:55'),
('854029b65510c2f8770d01b6495ec4c9c507cdcd0d10dede7175dca8e1b1c0943a300b9e50663eeb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 16:31:47', '2026-03-24 16:31:47', '2027-03-24 16:31:47'),
('854af5c1a6ec3080acf4da6e44823221f6ba0f05e5c23aba46ec500e477d8d9c3ef3888586724072', 151, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 11:45:16', '2025-09-18 11:45:16', '2026-09-18 11:45:16'),
('8575d0b673d02df493f0dc2bf76925f3c3b78a970da46cf44b36dcc602cd81fc327875cd02b95dd3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 15:42:42', '2025-11-26 15:42:42', '2026-11-26 15:42:42'),
('8583e261f87fc6e47523e800dcd506303bd3867bcd5a6bb4e198fc35e8bc93aa2e43097c2d6c851a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 17:37:15', '2025-06-10 17:37:15', '2026-06-10 10:37:15'),
('858eb66e331bbdf1303146c94b95c35adb530134d9e8c28643b8688996bef783f9ee0ec84193a1e8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-17 10:16:01', '2026-03-17 10:16:01', '2027-03-17 10:16:01'),
('85bbde85c900e970211b56c37cb2901cee306f4ae086e9e9adb8601ef8ee858bf8b0be8e349cf99c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-04 18:57:26', '2025-06-04 18:57:26', '2026-06-04 11:57:26'),
('85c16228e7fa369791286203981c1613c51ea74f32cd73cb46c4be1b9ac615b88531729ded57f662', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-30 12:07:12', '2025-09-30 12:07:12', '2026-09-30 12:07:12'),
('85f913a843e06103ff605dfc13e7eed35cc6579c4cac181889d18c4f1b96cb15940e64c2b3bfa91f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 10:24:00', '2025-07-16 10:24:00', '2026-07-16 10:24:00'),
('8627eb761a35a44a097387bb0c918c3c02ddcba87634ebde8b85f431b0e5ba246f834c5bb9c27f4f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 15:53:41', '2026-05-26 15:53:41', '2027-05-26 15:53:41'),
('862c01466f643983528bda1c8478b89d0d17b8ff660f533b7d5193e5037fb64546bdb20364d76b1b', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:07:35', '2025-11-12 13:07:35', '2026-11-12 13:07:35'),
('863d1e3c3ac89de440209b54907bc27c24ac2ad6095479e2210eb346105060384b152d9439504564', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 10:48:59', '2025-09-22 10:48:59', '2026-09-22 10:48:59'),
('864c0abca1f6b7a26d4fa574fb2a5200673121d4d5df01127d38146309c09739daa2556c1b3779aa', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-24 12:31:17', '2025-11-24 12:31:17', '2026-11-24 12:31:17'),
('864ed6f542558af541ed9fc48d4982664fbf87f3b1207619cff69dd772b1c3ea9aa85b94e3cd857b', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:29:08', '2025-11-05 13:29:08', '2026-11-05 13:29:08'),
('867f47b8b707889359d4fdbde749b0d26ded4daa3b9a3dbbbf35ad86811dda9c17c7317685eea590', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:29:21', '2025-11-12 12:29:21', '2026-11-12 12:29:21'),
('86ab6c7b8fb8a55c25a5f5ef0ddabdbcaa733f82720d9ea7a0a4dfccc0589c3be81dd59ff5262912', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 06:05:11', '2025-08-28 06:05:11', '2026-08-28 06:05:11'),
('86add788d8b9cfdab36d3c90e9c7cd0112194c03b91f0141ea290b3e57dcc764694be2019b667120', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-08 11:25:54', '2026-05-08 11:25:54', '2027-05-08 11:25:54'),
('86b01d058b6fc4f4d3ca18172dafc1410c2a0e70a9a85da3d847e0273e21eef292355a7a0154f5fb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-29 09:05:12', '2025-10-29 09:05:12', '2026-10-29 09:05:12'),
('86b49ba39dcb4d0842f7e19358f4390dedda80ed6985c25edd854a6e24f79edbd8962841a46907da', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 15:47:39', '2026-05-26 15:47:39', '2027-05-26 15:47:39'),
('86bec49b470ad27e85953d3801769b3ebb38b571b860b6f5a62dcbc0343b1788c2a6f0061f99f5f0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 11:14:42', '2025-09-23 11:14:42', '2026-09-23 11:14:42'),
('86da66758c4373eb0273c728451847daaa797d3bfe27e49d1d869602a2286cd76509a8a2370c5c7d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-29 16:59:14', '2025-05-29 16:59:14', '2026-05-29 09:59:14'),
('87033428db59e086b8ac29200388c011cc63c728c691f3a04988ead068e375d8c79ff0ef5aad0ce1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 04:43:55', '2025-10-31 04:43:55', '2026-10-31 04:43:55'),
('8705da1d177930a9e5d55a6ee1d88eed9b21789739ffd4348eb410b2044a92ff93040d02993470d1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 05:04:26', '2025-10-31 05:04:26', '2026-10-31 05:04:26'),
('871303f66afd65e3066094f39c8ceddbd4c74f0d1cce1014d6d16291ec008de3028cbdf253012462', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:23:51', '2025-09-01 10:23:52', '2026-09-01 10:23:51'),
('87476d3d68eb77f73bdeea5ddd68bbbe48f2a44d04815ad410ee731d6749288bd52544b5e38e98fa', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 15:23:00', '2025-11-13 16:04:23', '2026-11-13 15:23:00'),
('87c5cb660a62bdec065a092779601861245c24c70022ef02d883ae55b6e5b0aa05304b08a656b787', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-19 08:24:30', '2025-11-19 08:24:30', '2026-11-19 08:24:30'),
('87e8a98a29ce6a1a2cae91be680c7d198debd5c7da6ce107472f66b84a9b5b2ec6b2af41ff2e4a78', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:09:04', '2025-11-13 11:09:04', '2026-11-13 11:09:04'),
('87fcf113b1ae9ee7387fb36819fc0b3d1bfda8420da43057142845cc4f6653269dc72843e068fded', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-20 16:56:58', '2026-01-20 16:56:58', '2027-01-20 16:56:58'),
('881614ea4f2b633d307cdc003b91c62fbf6b86a4b6d37f28d67068511f32c705bebe077f16313fc0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-02 04:21:46', '2025-10-02 04:21:46', '2026-10-02 04:21:46'),
('884e47087280be50a3ce867ffbb862c6bb840c348fad51e5f55af4397b13fe068ca314cbe130131e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-20 16:20:16', '2026-03-20 16:20:16', '2027-03-20 16:20:16'),
('8863819a3d5c66df9cf94ab1014c69a09d074ca298c6fc9d9ff60c6152d17780c287bcab82847461', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-08 22:26:08', '2026-01-08 22:26:08', '2027-01-08 22:26:08'),
('8897a85ff1c439e6b25331f3398476f4212bf6dee45c85f756a9086a9dd4d7f1d2d9c490ba392897', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:11:50', '2025-07-17 05:11:50', '2026-07-17 05:11:50'),
('889cb90021a19cdf9e3ee6d9348d691c6aafc74179f282d7570f52ff50e2fe2cab85f650cf1e5217', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 12:54:08', '2025-09-01 12:54:08', '2026-09-01 12:54:08'),
('88a077e1dea070efd007bf74574ba8ef40a0593953daeeaf9ab5bfff23db9095e0c47e619d42ba4a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-19 13:22:14', '2026-01-19 13:22:14', '2027-01-19 13:22:14'),
('88bda353e3f7a75c5e3b423c5b62bd962fc651f9d885ae461dd9b5618527fed676e85476a60c25b3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-07 12:04:53', '2026-04-07 12:04:53', '2027-04-07 12:04:53'),
('88e3dcc3a778802d9032a2c93f905021f1cb065d5ecda0eef486c67040057dca10f671c8dd82ddea', 336, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 11:35:48', '2026-03-30 11:35:48', '2027-03-30 11:35:48'),
('88f80768cb7ca3214349a148f4b60edd305379d4944df7d14c04c670a703c6d857e1c45832cedbed', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:42:33', '2025-11-11 13:42:33', '2026-11-11 13:42:33'),
('890ea3c19f93afefd6aefce3fac572130f010f172b53415987ebc673c04040f4a689ad91bfb755a3', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-20 15:18:05', '2025-11-20 16:25:33', '2026-11-20 15:18:05'),
('894e70dfe8767737c69ecf3c6c5083afa25eceb2d44481ee2fdf2a6273276181cfea7c33e35bef1c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-04 11:26:41', '2026-02-04 11:26:41', '2027-02-04 11:26:41'),
('895ce15e06f6b1135d11c623f05494f2eb8ba1c6b37ba1f0861a915ad24cda27acfeb784bb90919c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-12 11:07:48', '2026-03-12 11:07:48', '2027-03-12 11:07:48'),
('8979fa833cdfd37058cec06bdb9d30af3bff1403f61804ae6d4fae7e76e61947c6cdd18ed8b2b283', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-30 17:04:51', '2026-04-30 17:04:51', '2027-04-30 17:04:51'),
('898cc5ff6265421ab533d1bb456d31719dadc595fe3c5226b8660116218c88a404d557a33bb3d450', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-02 16:08:33', '2025-06-02 16:08:33', '2026-06-02 09:08:33'),
('89a20f23a89c580082bde2d754307e3a523ce830dbc52a07ff710af0fe1b4c6ff10a567cc376c350', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 19:38:37', '2025-06-11 19:38:37', '2026-06-11 12:38:37'),
('89d142f821fada9128a55e851d5d04c3dc2b42477e9a29d19a7807226fcefa256f6827dce500be62', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-05 10:08:16', '2026-02-05 10:08:16', '2027-02-05 10:08:16'),
('89db244ac61d93458fe244e26419f7bbee9f7008379749f0cc0bb440c5317b08ab91c39d5bf8295b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 09:23:43', '2025-09-08 09:23:43', '2026-09-08 09:23:43'),
('8a08e78f91097ea468fdf0d392f9121c5d7a72c329b8cf83b89bb41d8448d77afeff2f45fc70143e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 07:14:24', '2025-09-23 07:14:24', '2026-09-23 07:14:24'),
('8a2911cb1c3cdcd0baec3866bbb300cc924f35d24e6cfd1c16c2e21d9a34884e97d07d9493a3b0df', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 11:10:19', '2025-11-12 11:10:19', '2026-11-12 11:10:19'),
('8a4a9e78ce4733ebd84e980a37658745f377baccf6bcd09bc081b35def2684827c4a3628a519824a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:37:44', '2025-09-01 09:37:44', '2026-09-01 09:37:44'),
('8a605f604cac40104efcc42995d40c24c3c4ea34ddd84e3b932ec640facf7a068b292bf4ce8d6e6d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-26 11:51:08', '2026-01-26 11:51:08', '2027-01-26 11:51:08'),
('8a93f7103cb81e0387a90891ca59d6cb5fd6520562ad0ff4d5d244f94a330f91d876f89a74ebe2a4', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 14:56:55', '2025-11-13 14:57:29', '2026-11-13 14:56:55'),
('8aa9acc83c72a5bbe1d37175316bb678188567f8cd3f142777e4c5c5ee1ea6ecb5d8e5e23820e0ac', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 12:07:12', '2025-11-04 12:07:12', '2026-11-04 12:07:12'),
('8ae114e2a9390a06ade009d676769a418fdc0a5fd9e29b80065a0148f4b60b4eade60e3d77cf3956', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-12 11:14:37', '2025-06-12 11:14:37', '2026-06-12 04:14:37'),
('8b0a30c94b1d537fcf56d42806629626a72d47c57e4c0c8d3f6be9f94e051f99f8c6f49c59e6f22f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-28 17:08:15', '2026-01-28 17:08:15', '2027-01-28 17:08:15'),
('8b0b52a754671d0cdbe3602b72d3a2c9896a5e181297979fcbc710d1629d31f9fd4f313503a8f68c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-09 17:38:52', '2025-06-09 17:38:52', '2026-06-09 10:38:52'),
('8b6850dfe41cc49981c052fbbd40c26eb6feee74d7f1e9b7716eaa976a7428eebca8c6b2985a2f15', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 17:56:55', '2025-11-05 17:56:55', '2026-11-05 17:56:55'),
('8b68a5e59ff18f1c222740d2b931076ca1f415cd9b2efa66e62b3b3bad08904b9450a507040efd66', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-03 11:26:02', '2025-06-03 11:26:02', '2026-06-03 04:26:02'),
('8b69ab8eb6e0c5980539f260a04e8f9153c27db6bbdafcb7eba69316b97fac8da135b48d3a1905d7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-13 09:44:51', '2026-03-13 09:44:51', '2027-03-13 09:44:51'),
('8ba9fc1c30f7d2e28b195a4f388e953d23abef0852f92050f86affc429df621d207f080dfe4aa2ab', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-28 23:06:05', '2026-02-28 23:06:05', '2027-02-28 23:06:05'),
('8bbda2529bb79010d3d907133f942c2bd495364afdda2457fca06edee2e51c4a5a6edb939b0113b1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-04 06:11:18', '2025-08-04 06:11:18', '2026-08-04 06:11:18'),
('8bbed06d986723d3016661da131e302967cc41fa47da48d95f09526208c00b921c57abe1b46e0613', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-08 11:21:47', '2025-11-08 11:21:47', '2026-11-08 11:21:47'),
('8bd67d7872150273e9c4fddb7ad16eeac1dad1232ac4386b1283a5889e1c483c82c15f7195155d76', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-26 10:04:28', '2025-08-26 10:04:28', '2026-08-26 10:04:28'),
('8beffb635b2b044d53d152cae413287e860e9ab5d9a14963f1fe6cf20ab6e0cf23dd897410e2c08a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 07:16:43', '2025-09-22 07:16:43', '2026-09-22 07:16:43'),
('8c041167c093a89831dd8f2c80e5849c8d3e1f985dfed60f2fc8713e3ebc7a2d8bdfab953cc6bde2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-01 07:54:44', '2025-10-01 07:54:44', '2026-10-01 07:54:44'),
('8c4858520786ea1390119a3d38ff7464857b0ba5e2dcc4a19c52b090ec230cf8700e62d41e22b3cd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 14:52:01', '2025-11-03 14:52:01', '2026-11-03 14:52:01'),
('8c4e9e0180bee4820fb3ef5d28cc8bbcdeafd6e38d1d78bbf32fb58e3461e5caafa2740f365a3cf0', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:41:08', '2025-11-11 13:41:08', '2026-11-11 13:41:08'),
('8c66275246532455d9d0f8a39efc0485c8f9e5bb6a99f4587efc1ca75196d0641591b473832f464b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-12 05:22:42', '2025-09-12 05:22:42', '2026-09-12 05:22:42'),
('8c730be96515d73e490e45efe71dbf9125f5d562e1bee760fdcd61281e2eef3ffc7e44d7ef5c1c09', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-07 17:21:34', '2026-01-07 17:21:34', '2027-01-07 17:21:34'),
('8c96e8826379bdfdbe2fb64a5a3ad3fe6d28a7940318fcd0e9273d5b34f6018ed3ede61603f8f70c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 04:55:45', '2025-09-09 04:55:45', '2026-09-09 04:55:45'),
('8c9709cf9017f3cd1e1151c2e0895b91fb334bba924b4276512372b43a52c1964161199bf38fdd33', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-01 10:45:37', '2026-04-01 10:45:37', '2027-04-01 10:45:37'),
('8c98f84b758ad8a131cab3be515d6cfa1eb6c4775f6d5f5ddfcd7630b33369d03c9c00ac846d4268', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-08 12:00:08', '2025-12-08 12:00:08', '2026-12-08 12:00:08'),
('8cb47bc7129838a5231c77798525f6c870bb5bc351a5f78e28417986f6d91ad1bd1422cb513d639e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-02 21:20:10', '2025-06-02 21:20:10', '2026-06-02 14:20:10'),
('8d346c5014c1abdcb59ad09df877c866ceea70170e0f44d20c8f1ac8a47d02a28b8ac64a59fd603f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-24 13:35:02', '2025-11-24 13:35:02', '2026-11-24 13:35:02'),
('8d8690063b1bfffc3eb0ce15aede119cf966b180a65384ed1de5111e634145b07d282c5f171e3194', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-06 04:12:59', '2025-10-06 04:12:59', '2026-10-06 04:12:59'),
('8d9d64f0eebf79e1b4d0ff0f27297885d13962a4cc9c3975a5a9aaa4ad18c318fbcfa1b4c22ae0a8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 09:46:05', '2025-11-07 09:46:05', '2026-11-07 09:46:05'),
('8db42454d2ead4ea0fe173b704530401455154c3b9bb994ad5ffd7b0334bdf0261aeca46d2e2b02f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-24 09:39:28', '2026-04-24 09:39:28', '2027-04-24 09:39:28'),
('8de67f5022bdec03b7dcb144e856d1c37df3ccb1547daf3c6533589775f788964ef901757ac4891b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 07:33:51', '2025-07-17 07:33:51', '2026-07-17 07:33:51'),
('8e1b10b933541e94a1e9610ede96118ba5194da4864c4cc38069e20efa8ac851ee8f7d5a6cd65f6e', 288, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 18:42:56', '2025-11-26 18:52:07', '2026-11-26 18:42:56'),
('8e2d41c8c2e9f26fbd5179a76c4add4b169cdd6a77d1ce978edfb24d7614bb0c38980e19aac6860d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-05 15:25:20', '2026-03-05 15:25:20', '2027-03-05 15:25:20'),
('8e3988220c71d412a9c332bbcec7c0a3c387f5a89b0632e99dc472d79e05c9a4eb495693b290a49b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 18:07:14', '2026-02-10 18:07:14', '2027-02-10 18:07:14'),
('8e5d3062cae62b21c4b9e9adfa7aea60fb69915e8838435e92d1eb41c64526e6f1c59dd326b13870', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 12:10:37', '2025-12-01 12:10:37', '2026-12-01 12:10:37'),
('8ea3e5da60fbb6af8a0805c9f00191a7efd3af6e9f91dfcf76897a02f042dc4271443528ecc31860', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-25 19:51:23', '2025-12-25 19:51:23', '2026-12-25 19:51:23'),
('8ec95d37031fd05daa510408495e61a25b08c4d144e98045a739f9a7dc824de2f777feebb34f97fa', 219, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 13:15:44', '2025-11-03 13:15:44', '2026-11-03 13:15:44'),
('8ef3c89aa12ddb19f8f0fc1dbad1e2f9666f159dd76c0ebd417b136c00f02c1ac003a32ec464ce40', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-05 13:18:25', '2026-01-05 13:18:25', '2027-01-05 13:18:25'),
('8f2c93e98994118c9ef7f153440ea9658056675bdb762cb4dbda79aa7dca5600dec6c179297344dd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-15 11:10:18', '2025-10-15 11:10:18', '2026-10-15 11:10:18'),
('8f2fc836afeae63caf8cf4770fdd87bc5a99867874c471dfabb404afd93451365fc749f5b1357e24', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-16 15:25:42', '2026-01-16 15:25:42', '2027-01-16 15:25:42'),
('8f3cc7b5514d1cdcea1bf3ac25968aa20fbf9c82c88c9515d2b3c2ae32c9be8628c819df10478af8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 11:06:33', '2025-09-22 11:06:33', '2026-09-22 11:06:33'),
('8f6e2a4b5afd5ecb1574ffbaf9cb670b338d2fa0d4ab41649744e1c665397f48e0b622ce7ef52ac5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-18 12:45:28', '2026-03-18 12:45:28', '2027-03-18 12:45:28'),
('8f891420e6e30bc6c1c4d3337e4baca2e9dfdc2b120ca06006ece2e990f4040298f11ac6cbb0468a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-10 12:04:48', '2026-01-10 12:04:48', '2027-01-10 12:04:48'),
('8fb39eed356b22d93426c3b91ce8f29540f7676f88e6e30c6725f6fe91d0b4bfcce8475414b85fb4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-06 19:48:28', '2025-06-06 19:48:28', '2026-06-06 12:48:28'),
('8fb519db7e32c37653082ad9b3a273edac70daf23ab5179cb21e13d4eb1678015d03a39002c24226', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 10:10:40', '2025-08-28 10:10:40', '2026-08-28 10:10:40'),
('8fbeccd97d31ef9f445374f5c6898acf379310d15d8819721e57d86a2c7d079941a84149ac6cefa4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-23 17:42:56', '2026-02-23 17:42:56', '2027-02-23 17:42:56'),
('8fe332f4708e471d156514da99184218a1b03a5877f85363451db23ae64c29f3a656d37291efc903', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:28:50', '2025-11-05 13:28:50', '2026-11-05 13:28:50'),
('8feb6abfce5d3dbf3a733da6a34982755381fae26bbe66e9f81fcb161f2f36216e467d8d3b34be80', 346, 1, 'LaravelPassportToken', '[]', 0, '2026-04-01 10:12:44', '2026-04-01 10:12:44', '2027-04-01 10:12:44'),
('9009d7d2f3451eb7a425ceac9fb52b53d7a6eeec9af299fdc1575f5b542f88df891751eced0a958c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-29 09:22:37', '2025-08-29 09:22:37', '2026-08-29 09:22:37'),
('9017ad6c4021b5343f3b37bdbed08a4813b0b16039b4e87a5010a719689085c5e372c150591ab0e4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-03 20:15:06', '2026-03-03 20:15:06', '2027-03-03 20:15:06'),
('902d5cd46cc684dadafbd95ad3dcea9f79e9459eaeebb7d55ea4fe73e6860c314c1b54b0cc535eca', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-03 11:20:36', '2026-02-03 11:20:36', '2027-02-03 11:20:36'),
('90335a83c5200a905e774992d11244f62feaafbe1f51dcb696b6b5c743bed3bb4d52c498b11689ba', 268, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 15:10:52', '2025-11-26 15:10:52', '2026-11-26 15:10:52'),
('9042e72c12f0cfc4991cd5ab884b42f9f7b557538a2e255eebe5d22a7365339a5856587b24f22ffb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 11:52:07', '2025-09-01 11:52:07', '2026-09-01 11:52:07'),
('90563765b69aa8768dc3345f218d4a53888a2c45b614c964554e230bccd51907fa2b4148fff6dcb9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-29 15:17:01', '2025-05-29 15:17:01', '2026-05-29 08:17:01'),
('907bfe2dd8f1c7998bc3a6aac01ec08c8842ed6388418358a38407b8069d3fc55c0be671825bf916', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 10:51:32', '2025-09-19 10:51:32', '2026-09-19 10:51:32'),
('90ad41aacae6293d92bd90ac5616d86f69f9b0407870ede0dffeb5fd971bf3aef0f913caabb07a6a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 12:27:16', '2025-11-06 12:27:16', '2026-11-06 12:27:16'),
('90af457417d6f29a9ef22e8b45fb66d555e298d21cea59c52537112cfb5a54b2e43afb9e03c8bed2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 07:02:01', '2025-09-01 07:02:01', '2026-09-01 07:02:01'),
('90b2a853501f6fe9ab59d48df666b188167fba43c5fb34418cb8bc163670e41749be1df0968213a8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-22 08:29:18', '2025-08-22 08:29:18', '2026-08-22 08:29:18'),
('90f42c8588b0246a726cb6af5041651545b9dee40907b9417c3c65f76eb2897349284e0163c4181c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 06:11:50', '2025-09-03 06:11:50', '2026-09-03 06:11:50'),
('910aed5d34ca41e2b9ec1697af346d1cadcbf2ec3e84361a87222913b5df76a7edc5641d29bba213', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-20 13:05:46', '2026-01-20 13:05:46', '2027-01-20 13:05:46'),
('911195abde7656d92c2631295e090e6b78b14811bb9adfc599b68233513979d402661ae5a37a2530', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-27 15:14:34', '2026-03-27 15:14:34', '2027-03-27 15:14:34'),
('913979fa2bd5cecf6815cda0cccdddc5f98280f1b0a26556233ad62732f6fd6a563377d144c39490', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 06:41:17', '2025-09-05 06:41:17', '2026-09-05 06:41:17'),
('913bc34b2719aaba01024145566aebb9363ce418d3b6a3646dacad5b94fffe8170dc24f8699dd74a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-01 10:09:10', '2026-05-01 10:09:10', '2027-05-01 10:09:10'),
('9174696b2d7fa891c88714a8c9910d71c0451564f63d3f668f18bbd5aa6b7db47009b58977e50172', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 08:25:06', '2025-09-01 08:25:06', '2026-09-01 08:25:06'),
('91807c97a5455f0e60f0ac0d962194920b0457882a0bc2d3d2b6cd0167f87f44233fa83f2e845f6a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-06 15:35:36', '2026-03-06 15:35:36', '2027-03-06 15:35:36'),
('918e03f05ed678d5a7425e915b013f7cb47ed8e1702b7dfacb09005275e5123fd7bf497e48f5250f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 16:55:27', '2026-03-31 16:55:27', '2027-03-31 16:55:27'),
('9192adb01247b5f520abd9e8473c3d976510cf21bd3057471170c8aadf3a23afb8dd2e385bcc98b6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-20 14:16:36', '2025-05-20 14:16:36', '2026-05-20 07:16:36'),
('91cc28fa69231dc3284f8c051d9646fdb2cd586213bc759aebab4c96adc066fe69d296695f04a9d1', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 14:31:53', '2025-11-12 14:31:53', '2026-11-12 14:31:53'),
('91d88ec69ac474b55bce52797409074e5e95e2204e22631e4d49362695c055b33c69070825f005cd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 04:45:05', '2025-09-22 04:45:05', '2026-09-22 04:45:05'),
('924a035e0b8eb9063c0df9f1605753475d5c3742b81067454939089b33d9e15e5e7b8e88db6bb115', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 14:43:24', '2025-11-05 14:43:24', '2026-11-05 14:43:24'),
('925e821a2e21957074d4559ca15e33db906a546957ec2fa90e16e82b6dcd0f3cca31c4a697c7c7e0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:07:37', '2025-11-05 13:07:37', '2026-11-05 13:07:37'),
('92ad9fef69ec12ef81ff46826a707e563dd7659f8777e1bf55160804bcc03cbcbcf6a36eae1a1c6c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-05 16:00:49', '2026-02-05 16:00:49', '2027-02-05 16:00:49'),
('92b6ceb6b1cea867b5789ec0bf8fe970095f2e5daf011ca27a598fe640eb312860fc8fcbd3429292', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-09 15:29:12', '2025-12-09 15:29:12', '2026-12-09 15:29:12'),
('92b7055dd95e90a3e70fe995568f4e930480f30f7a6603ec43bb6eb0aa2c57ec27327afe873c7ee2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-23 18:27:18', '2025-12-23 18:27:19', '2026-12-23 18:27:18'),
('92e8351a3a82b4f6904fdb92560fd4da6301ca1eeba8957a2473b8ea708bf6643ea915b91c6bf316', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-24 10:00:35', '2025-12-24 10:00:35', '2026-12-24 10:00:35'),
('92f85c240df1c8e910077359dd3d9bc9ed22219fabee43b6568c261d84434bb7b12abd70a1ba32cb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 11:32:18', '2025-11-03 11:32:18', '2026-11-03 11:32:18'),
('92fe4c863ca5a680d3e38337c7843f1db312c71e1f8a47626c827689862b2a7a46dd3ee5c05b9a3c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 09:19:04', '2025-09-09 09:19:04', '2026-09-09 09:19:04'),
('9300e152f2fbf2ecc1db81ab3a7399b26a28fe42aad54d3cd0c9fee9329774fd306763daae602cae', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-29 17:25:44', '2025-05-29 17:25:44', '2026-05-29 10:25:44'),
('930e6fde1a5927f146b84ff6660da244d2f1806acebd4dd61cc8ac080b720f8111a7021b448f135f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-05 10:27:10', '2025-08-05 10:27:10', '2026-08-05 10:27:10'),
('933aea7da4e4f9fae4ad6855df4428b258682d48b770faa6069834ae7fe77820a931120b731794c4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-27 04:24:29', '2025-08-27 04:24:29', '2026-08-27 04:24:29'),
('936358a22e1a7534a15fe81d1755a44a8dcb6b77b205430db050002cd772e2f61c40f70e52ca917d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:28:47', '2025-09-01 10:28:47', '2026-09-01 10:28:47'),
('9363b6ee65653fd5e79e5899574e67452eeafda1439f5afca590aeaaf0ff8a3b6ba8fa3870002862', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 03:58:58', '2025-09-09 03:58:58', '2026-09-09 03:58:58'),
('93805a27e35f84ffe37160b6b48ea50daa19eef53841a7b6e3e028a3a182c6d5d6e036526fb7a179', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:35:54', '2025-09-23 05:35:54', '2026-09-23 05:35:54'),
('93a90ad429718094dc1926c914249f986e47c5202cda779ca69797ef76d5cf718fd025e702b5be87', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-18 19:52:46', '2025-06-18 19:52:46', '2026-06-18 12:52:46'),
('93b5e906371fdfccab5b6ea5b5c91fc19f602e4a2771da74be0a85c5eb4067707123e2986f7a0d97', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-07 12:04:42', '2026-04-07 12:04:42', '2027-04-07 12:04:42'),
('93d7a5f491488638ea6b7d10d0fc5b92d9a08aa6f9ba33f3ce688898c7356b0edde88bb55abfe8bf', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 13:03:20', '2025-11-04 13:03:20', '2026-11-04 13:03:20'),
('93f177ab37a4a565f4187ec263fc78fb585cfdb7f5a7aecb130e1a5fc41c03c9ad85a76f0bfd5741', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:47:37', '2025-11-12 12:47:37', '2026-11-12 12:47:37'),
('93fa993e6daef3ca36ccd91537fc0fe56e79ac5b92bd343b4c65f1ca059f3b054913f0e2c425e722', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-05 17:25:36', '2026-05-05 17:25:36', '2027-05-05 17:25:36'),
('941653fc541a360a209f7e055dcde3b1f0ba72d40090031dba62a56e06e85077fa48b57eff0a95b3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-27 11:43:33', '2026-01-27 11:43:33', '2027-01-27 11:43:33'),
('94347efc3eef8e84407ca7a5095f6609be13a5703ff82d022126328e5715e2d9d67d86869c88aac8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 12:39:38', '2025-12-01 12:39:38', '2026-12-01 12:39:38'),
('94420b8eb235d52991e2c202a28f4bbaa979e09b627b8a42c6efb22e1999aed2dec9eb0211a21630', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-20 15:31:17', '2026-02-20 15:31:17', '2027-02-20 15:31:17'),
('94628f7c5cc67a3b77ebb2daf82dce4548d8c971ea35c0b3a120d3ccd6a6d367d5dad1972c672258', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-13 14:37:29', '2026-01-13 14:37:29', '2027-01-13 14:37:29'),
('94699372e29d3504f330735a38fc7ac99a418f2d3d83986951572a22434b47c5ca680e3378724725', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-30 17:50:38', '2025-12-30 17:50:38', '2026-12-30 17:50:38'),
('94779e5c469300fbebe5441bba9e28a71f5446d3fd7ccdc100b2f7cc8f1ffc6975a6c84424fe3e7b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-21 09:59:52', '2026-04-21 09:59:52', '2027-04-21 09:59:52'),
('949811e241fc1f79c5427809341f42231d12c95a4b9ec267cd1f536372aa4ddd84141ea74a0613ad', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 11:49:39', '2025-09-19 11:49:39', '2026-09-19 11:49:39'),
('94d6244dbd2ae6cba0a7de8ee707a7110eab5d03e9221f1caa2369968e7fd04eb302f4dfd39b22c3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-04 11:05:41', '2026-05-04 11:05:41', '2027-05-04 11:05:41'),
('94efa21ec5d48ea1476219fe4b8dd2282cea632c8b78080286327f9fbdc321db0758cc560d8f8382', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-24 17:01:41', '2026-02-24 17:01:41', '2027-02-24 17:01:41'),
('94f40b643409a7ced5a9e758cca6fe3143bbd4538d4d6a2f56bbbc72dd1820f7a392013b6cd929c7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-09 16:25:29', '2025-06-09 16:25:29', '2026-06-09 09:25:29'),
('9510750f4126ad3801e3b32e722cb099115f6a933838beaae764e92d8f76c37ddf25a38ee4b97232', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 16:04:38', '2025-06-11 16:04:38', '2026-06-11 09:04:38'),
('953b243c9983ba51bdb017ac2a0aed88ca9c3b407b33a1c7c0b981abf6065a4dcc220902ab04a3b9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:44:01', '2025-09-01 10:44:01', '2026-09-01 10:44:01'),
('9546e68261b0c25869d26e24ce46c953366afbebd1f2d9aa0d1756d7a1edb404f0d2c5308d4db18a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-07 18:47:13', '2025-06-07 18:47:13', '2026-06-07 11:47:13'),
('955ffa6edbe063bb60b4ad4f54619dfa1799b63e99a0b474a458bd3ec735f3e0ee2c8eac7be637c7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-11 07:06:59', '2025-10-11 07:06:59', '2026-10-11 07:06:59'),
('95bf824f6b6233f2babb0b6266903bd7abefb18455f23b213e785654699fcc90a45009f426345769', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-29 10:21:14', '2025-12-29 10:21:14', '2026-12-29 10:21:14'),
('95d2d8837dd6a418005c951eebf3a11dcfedc309ca893f8f5a0963fc7e294dd4350905098e10d199', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-21 11:04:13', '2026-01-21 11:04:13', '2027-01-21 11:04:13'),
('95d39a3554f3757441b4ba27d2798c6a7d3fd334108337f814916b233efc7e5cf55c58c5a3a1947f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 12:26:35', '2025-12-01 12:26:35', '2026-12-01 12:26:35'),
('95e82800cb690350e0e0edb46607f2fa5aa8f9bc921bbede7999463df868cef3a6125b015be42a4e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-28 09:53:02', '2025-11-28 09:53:02', '2026-11-28 09:53:02'),
('95f9c06291c1d1c4fc0016bbedb616d97f07ab1e0b5156ba523e5dbe125967191215246785e6f67f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 06:45:34', '2025-10-03 06:45:34', '2026-10-03 06:45:34'),
('960badff9add949b59cbc57f5c31da5687315df9f18b30390518ef6d7f48545908495f4600cfd5e1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 09:24:14', '2025-10-03 09:24:14', '2026-10-03 09:24:14'),
('962148f405be09a2e153cc0aafeaaf712c3ebfe9768ff608e06dfa57824afb9876cde8b09ff1267f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-29 12:09:30', '2025-05-29 12:09:30', '2026-05-29 05:09:30'),
('9626b89cc97c82b1c2d0386d626807b42916435210d93884840667b1216e8e1dfd89eb911978c9b4', 164, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 18:21:20', '2025-11-13 18:21:27', '2026-11-13 18:21:20'),
('962b46f3b9c2a7f95eb30ce16b52382d4d7e98af53c8a446a9b38b6477f2ae85da3268a1b7142cc8', 219, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 13:10:36', '2025-11-03 13:10:36', '2026-11-03 13:10:36'),
('9639647d47c7994411971fb6f77246198ff520d7d9f962166589792260d66a0bbb04bafa7e93811c', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 16:27:32', '2025-10-31 16:27:32', '2026-10-31 16:27:32'),
('969f2d7b37a149eb2f0a75289cca31591aa939b35437c8565e359b8425b00453f383823ec1cb23fe', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-12 13:41:34', '2025-11-12 13:47:25', '2026-11-12 13:41:34'),
('96a89524710256419ddcbc5f90b8213e5c4ebb5cdd0099646a272fd58f21dccd6444cee29be80663', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:59:52', '2025-11-06 10:59:52', '2026-11-06 10:59:52'),
('96aeecfb1c0cc5a436563c087533e3e6c2ee6440b430d3294ab4ce770c3a80ee3c758f029a4e11c9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-03 09:37:05', '2026-03-03 09:37:05', '2027-03-03 09:37:05');
INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('96dc39cc9e49ddf9f0a902e54b92d7c002b33ba9de24c8e09974df214c2e88c29f7c2b0419de2215', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 15:25:54', '2025-11-05 15:25:54', '2026-11-05 15:25:54'),
('96dc5867f4c90e99186d50068aa68658b57ea5bb16e170be7751041f73934f5310fb3b47b2a97ab3', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:21:18', '2025-11-05 13:21:18', '2026-11-05 13:21:18'),
('96e96cee2f6dab343727384e8ba6dd96ee3e087f0aa7888a9abcf175836012d020cb7b63775cdc05', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-04-10 04:31:10', '2025-04-10 04:31:10', '2026-04-10 10:01:10'),
('96eef44dbaf9cd3d68cbd4ac1b52f5ef9c50788e2c239c1052076ec185bf50b87a42016bbdf88bfc', 19, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 18:42:52', '2026-05-26 18:42:52', '2027-05-26 18:42:52'),
('971493160d663a4f916661cf864cc78e673f849b006af41b5ff191050d23115df150ec57ef1b3767', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-13 11:33:29', '2025-06-13 11:33:29', '2026-06-13 04:33:29'),
('973ed22c57be016498baf67f19b52b40006419dd7ef104b0c04ed61abda6e5584004422a8bf46b8c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-06 10:06:13', '2026-01-06 10:06:13', '2027-01-06 10:06:13'),
('97804d97992a93b27208c00b331d4f1188fd2304023e0557c5374583a02d7886bc84c50b0e70d30f', 210, 1, 'LaravelPassportToken', '[]', 0, '2025-10-16 10:56:24', '2025-10-16 10:56:24', '2026-10-16 10:56:24'),
('9798abf5b5a93b4b057696b566a04ab1b4aa3277a0358f3258c3c8d82d6ff0ab6afb032d16fe20c2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-24 15:32:30', '2025-12-24 15:32:30', '2026-12-24 15:32:30'),
('97a86e632454dc88d0b4dc3463514a2420e6b71407512e9aee5fd89cde3e15420c2f8637f6ae016d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-03 12:48:48', '2025-07-03 12:48:49', '2026-07-03 12:48:48'),
('97dea2f56c8f703733dddcd30e987fd9f7b148ebd8c7479501cddfdcfeeaf3d5524ee65e4e4c5613', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-03 10:58:23', '2026-02-03 10:58:24', '2027-02-03 10:58:23'),
('97f36da32bb70d65f501491b4db06ce43b0eb0bd1b92e4d65e0bc50964aa64bc5aedb8f45099621e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 04:34:10', '2025-09-02 04:34:10', '2026-09-02 04:34:10'),
('980b9385be3d0bf21e60fcf37b4756c3bb0a4fe655ab39e4be3daefc8c82a719cd2742d620793ce0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-03 13:29:30', '2026-02-03 13:29:30', '2027-02-03 13:29:30'),
('98605cecc9d4a91bf3c6138cf25d01aca203562216c237813106a3dca5ffa8d2c5e4c6b0d0be3a82', 175, 1, 'LaravelPassportToken', '[]', 1, '2025-12-01 13:30:04', '2025-12-01 13:46:36', '2026-12-01 13:30:04'),
('988157fac75ef4c32a530eaa9d8b1a20a6f215b0548de340bb41c9fd186aee45fa3203332b8e4b96', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-20 06:14:19', '2025-08-20 06:14:19', '2026-08-20 06:14:19'),
('989e03ad4ac6c4619b7cb0b4d6c2263bb4d0e04e996279b805469a052c3dff08feba4bb827941c12', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-16 18:10:56', '2026-02-16 18:10:56', '2027-02-16 18:10:56'),
('98ae72e71d13015f2f5e4d16794ca76c9118710b2c6335be133b6ceb2dd219696858141d07338dfd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 13:55:19', '2026-03-31 13:55:19', '2027-03-31 13:55:19'),
('98d86129aeb724c4261121dfe66790e85223148d79dfe0ec54995f10e951f458d682c7c7e23f2782', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-26 11:38:11', '2025-05-26 11:38:11', '2026-05-26 04:38:11'),
('98e63237754fd4ef6217dcc6c475e6006a44eebbf790c743852dba7dd066e93cdcbd3c0cd4ac494f', 268, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 15:08:43', '2025-11-26 15:08:43', '2026-11-26 15:08:43'),
('98ee1178087b3791e109f82a10edcc22baa4fca635f1f8a2179570c82054106a5d62ee2aea89b06a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 09:24:27', '2025-09-18 09:24:27', '2026-09-18 09:24:27'),
('98f760ef99874d2c11a04f9d9429350c0cb4dfe042bed43410ea4d677089bbff8725bb68e7fd0066', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 12:23:05', '2025-09-22 12:23:05', '2026-09-22 12:23:05'),
('9907f5e4e2eafa506d7c18ae3634d2b82e5d69781fd0b270734d939243ff9d108fe29756fe729669', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-27 04:29:12', '2025-08-27 04:29:12', '2026-08-27 04:29:12'),
('9909292163881cbda659e2831b5be199e6439f724532a6c02dd8b7939a747814521ff8375116676a', 316, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 17:49:29', '2026-01-29 17:49:29', '2027-01-29 17:49:29'),
('990eccf366a89880b2ed7d116227fb6c4e8fe5d7464d50a4b6911de51f9f831d75079a3e87d56b6d', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:20:34', '2025-11-05 13:20:34', '2026-11-05 13:20:34'),
('991adcfb7df6dbc01d4f91924c0669c0f3a59574fe7fe28705494546b2094e98a29e04bb28ed467f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-31 15:50:56', '2025-08-31 15:50:56', '2026-08-31 15:50:56'),
('9929c102defd8d693752132cf49b368d4ef22fe2a92b8f55bde83069df2b62110024c3616ef71a0e', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:29:07', '2025-11-12 12:29:07', '2026-11-12 12:29:07'),
('9933301b729bbe30a3466420ec763b4a785db683133610a1f061447ec47eb850aa6d18de80cd784b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-20 11:12:58', '2026-04-20 11:12:58', '2027-04-20 11:12:58'),
('9953f23ae3711b76311278366507c7f09d25abc1e0143de91e55b12fbab849e68178cf965598e6b5', 76, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 07:24:31', '2025-09-18 07:24:31', '2026-09-18 07:24:31'),
('995a13e814161221f8e8acf2f0171c8ef5b2ff08a4284e58e1bd8ce71a24add08a058617ea87d475', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 14:38:36', '2025-11-12 14:38:36', '2026-11-12 14:38:36'),
('995b92e13a37ce88c13a9fb03ad11e400d8b40c16939fede4f11288d3c43945e18f0b63ce595b9d7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 13:11:28', '2025-09-22 13:11:28', '2026-09-22 13:11:28'),
('996b8d3723607901352aae1440c796771ded1e60d964b6fb011a230515b611a7be7d8ad314e78f5a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 07:05:24', '2025-09-02 07:05:24', '2026-09-02 07:05:24'),
('997714bbd2bd4f06392f5855853116c7361b5d8343b2911dd5e678bda49ef29467ba6e3e204a6324', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 07:33:23', '2025-09-23 07:33:23', '2026-09-23 07:33:23'),
('99992ffea45faf1aee36c34f191b84dcfb625a28a49809f46cf1455e0b85c7c06cb28cd6bbd16ab7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 10:51:15', '2025-09-08 10:51:15', '2026-09-08 10:51:15'),
('99ca044464def0968e36d8c435d3c4ef266bc0c43bd751af6109c20eb93a8a2934a83d015a805978', 22, 1, 'LaravelPassportToken', '[]', 0, '2025-08-27 11:12:36', '2025-08-27 11:12:36', '2026-08-27 11:12:36'),
('99db46505d1f159d016c0f3867a4da54156b3a04c2a630042c478550c67d037295f70462d5f5fab2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 13:02:36', '2025-09-09 13:02:36', '2026-09-09 13:02:36'),
('99e8c925a6501057d403363b4dc29bdb72fdb823a37f283d2e2058e404faa0f6644a1eb57d5e649c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 17:39:37', '2026-03-24 17:39:37', '2027-03-24 17:39:37'),
('9a3ef049b8561a9c7440512704dd8249018bfd668ac1d6c1ef8d962bc1a41d71140338463d1cbf07', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 06:33:00', '2025-09-25 06:33:00', '2026-09-25 06:33:00'),
('9a4a32ba5a905686c23dfb870a66732338e200576059a93032b222611c713fac640e0fcc89718607', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 12:26:36', '2025-09-01 12:26:36', '2026-09-01 12:26:36'),
('9a86058a98dcbac273d07f8877c52047bd7418661d40cbff0368885e310e6f92ea2b5861f1e58430', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-10 06:22:12', '2025-09-10 06:22:12', '2026-09-10 06:22:12'),
('9a90b509b651b3b5dce1803b962fe882cbd7c72b367bd3c079e26f135507345a2347cc9b4aecde9a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-01 12:36:13', '2026-01-01 12:36:13', '2027-01-01 12:36:13'),
('9aa7114b281d940724c709e8e10f16674160346a369bb284bc934e8a8a4d21b1445b16a7a566b8dd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-22 13:32:32', '2026-04-22 13:32:32', '2027-04-22 13:32:32'),
('9aacb9daae2bc055777d1b98551715399ccadf1b84d96c8b8103d5e259c5c9758d66190d198ab569', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-19 11:25:49', '2025-05-19 11:25:49', '2026-05-19 04:25:49'),
('9ad59ffc20cc525b4996ed146039acb783de5fcc8d2c71b471d91b1900c4fc325250d39951406a83', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 10:07:28', '2026-03-24 10:07:28', '2027-03-24 10:07:28'),
('9ae255535d9b6896c7f31b9caabf2371a7a4767bb887b21dfe92110903743dc9d34fcea04e71856f', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 14:37:19', '2025-12-01 14:37:19', '2026-12-01 14:37:19'),
('9b0227a168a5eeda58e3f5d98bdeb98293bc0d6a1d60bedf1622466139611ad3ffee98d3dec207c0', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:11:15', '2025-10-31 17:11:15', '2026-10-31 17:11:15'),
('9b0a1731745318180d969e8af72054de6643849ab0162320780e84601cdac38b366cd52f5cdbc35c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-05 12:02:28', '2026-05-05 12:02:28', '2027-05-05 12:02:28'),
('9b0ea7c0bbf1fc17e6c5fdbed15e6cca833dfb60ce2ebda145946c66005b4ede0e82b2ddb8ae2367', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 11:29:26', '2025-09-01 11:29:26', '2026-09-01 11:29:26'),
('9b29f88dd73d3849bdc71ea92b4a7de0f529f82384496d85f48cb1b3123b64200c652a78811ab422', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 15:01:36', '2025-06-11 15:01:37', '2026-06-11 08:01:36'),
('9b3b098f8ea191c5571d4c39169e88555833091a1f0cee0b214a9bdf24b54835dda372a05fd2952a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 08:33:45', '2025-09-08 08:33:45', '2026-09-08 08:33:45'),
('9b4aad4ca316945365ab00eddf28fb393873df26a70d2d2d59579762158f84b56bfb581495a99da7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-15 20:23:35', '2026-04-15 20:23:35', '2027-04-15 20:23:35'),
('9b5509e7d2ed889837660ebbf1732ef570aa2e97ffc91fd61be5eaf7e4ca3fd385d9029e00f77c77', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:10:03', '2025-11-03 12:10:03', '2026-11-03 12:10:03'),
('9b5c632964771db8a31b7710ff40a5dd874079f4b5f315cdc889165a7eb47afcd3eb18e4d816dd7e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 16:02:34', '2026-03-02 16:02:34', '2027-03-02 16:02:34'),
('9b80eac76486f34a3f71653680827797fceb40bb10cfcf69d3a55b2bed3e36d4dce7da1cc0c16169', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-21 10:39:57', '2025-11-21 10:39:57', '2026-11-21 10:39:57'),
('9b8602569743dc8188f3854871e66494b315c0641f3161560afe822391642d7223f00e868be8b71f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 16:52:23', '2025-06-16 16:52:23', '2026-06-16 09:52:23'),
('9ba073a30d47e60afd54e2cd220fbe9ec7ea20b2ca4429a1fe2110c5f77f9fa79d2826bb8807d1b7', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 16:03:19', '2025-11-13 16:03:19', '2026-11-13 16:03:19'),
('9c09135b42658812357332d8b4661e383aaa22e3e9ef0c052be43b935a78c8e4510385de635b3353', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 11:15:32', '2025-09-01 11:15:32', '2026-09-01 11:15:32'),
('9c393c0e71f9de67deb358524d87331bb9f8ca5aef998b876c1c1f36f840e10175f0d36ee7d98379', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:14:43', '2025-09-23 05:14:43', '2026-09-23 05:14:43'),
('9c3de214362c534f0634f92a2c29093c3747a79eb39972c7068380a7d1f4d43c3e25ee6293292a1d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 04:43:28', '2025-10-31 04:43:28', '2026-10-31 04:43:28'),
('9c40742b9bcc413be044e3b86e6f8b17cf023dad013d54e0476f9fd4f66cf280c1767d35dc94b361', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 12:25:31', '2025-11-04 12:25:31', '2026-11-04 12:25:31'),
('9c4bc16e1623ed96db5760fd366b42631cec366b9317f5a55dc874de9e1fda2248c87c9153e6b9ca', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-29 10:20:55', '2025-12-29 10:20:55', '2026-12-29 10:20:55'),
('9c7be0f1e06fa8bbf7be30335d1158a2b69c630136e3a912ce37fcc0b8721e88b01297f6b58bf67e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-12 06:41:47', '2025-08-12 06:41:47', '2026-08-12 06:41:47'),
('9c90dc6b254d3b3555a42592bf6b4b339f81571a8117792b5a6d6f134cd49bbeaeae87dc7bb194e7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-12 13:53:25', '2025-06-12 13:53:25', '2026-06-12 06:53:25'),
('9c9d385a53d8009c37aa3aac9deb10bbe9f899f9496deb2244d1afc4f6ed0d907a5525d39f8204e6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-26 05:34:52', '2025-08-26 05:34:52', '2026-08-26 05:34:52'),
('9ca65f74bfb9263fb35e458b9fcb9875e99281b6381177b18e72eda0992d5df4981311b6511ac672', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 14:02:20', '2025-09-05 14:02:20', '2026-09-05 14:02:20'),
('9cda54db1c7e826dd7c2c38d821f92c6775709c2f20ce355f52e08034e1a44c1068dc88e317c5f96', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 13:29:53', '2025-11-03 13:29:53', '2026-11-03 13:29:53'),
('9d2be07b7a52ad4aa7f36ea08a8726795891d6705b2f454453bb2b8f358bf3ddc2e738dc50fc509c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-05 09:50:35', '2026-05-05 09:50:35', '2027-05-05 09:50:35'),
('9d58c1711b36d3f9275a6b6f5eab0fc63a89550c59bd5c19371d5d9cd37a1b666d62827333b6ab93', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 13:35:07', '2025-11-13 13:35:07', '2026-11-13 13:35:07'),
('9d72cb5bb71d697fbec873f84682a39f1ef14e756be0866fbd84eac5b9c768e25f18564a9a491a7f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-09 11:03:51', '2026-01-09 11:03:51', '2027-01-09 11:03:51'),
('9da3e346265b2112777f5e796e5e285150ad3ef1754fc421d24779739dadca7b57b291e94121bb83', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-04 19:51:07', '2025-06-04 19:51:07', '2026-06-04 12:51:07'),
('9dba3e0e981eb58b00076539713b92547bccdcf1436cb7c1c01b2d5f96681ed24ef05fd24a7896a8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-06 15:07:06', '2026-03-06 15:07:06', '2027-03-06 15:07:06'),
('9dbf967f685ab7ec0582a9eba162ee1f62141cbe0151053b4d2ec2994d29f934961592fc3f540e98', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:09:48', '2025-10-31 18:09:48', '2026-10-31 18:09:48'),
('9dcc7bdae4ecc0fdb0c6983db7d6dbdbbf45895241f3df88a91d2466c5eaab4124a56a4d5366daeb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-19 09:39:38', '2026-03-19 09:39:38', '2027-03-19 09:39:38'),
('9dcdba23daa8ee552089037ac64ad88478c7044744d0aa11dd8cae004c873c44498eee9a851044f8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-06 16:11:20', '2026-05-06 16:11:20', '2027-05-06 16:11:20'),
('9dcee14665a4f2dc280a76f90e232cc489321b904ac7730754086940055c1009ecbb46970d1c6bfa', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-15 18:35:13', '2026-04-15 18:35:13', '2027-04-15 18:35:13'),
('9dd60e32d5faa2ece03a900a4eaa7cd2fd512ec76d24fccad6c3fd74f3aeb30223b75b219d9ba06c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 19:10:19', '2026-02-10 19:10:19', '2027-02-10 19:10:19'),
('9e7bb20f6b1fdbe2a686109aacbcac604531f0d77d87ce8c3ee235ffd302db57431a180f6cdbdf26', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 14:43:42', '2025-11-11 14:43:42', '2026-11-11 14:43:42'),
('9e90598a207aabcbefa234463cc9c7007023389c708ec1727603661eb7536cb5cbfce8eb2a0a5936', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-21 06:17:10', '2025-08-21 06:17:10', '2026-08-21 06:17:10'),
('9ebe240add7534bff4b59caef86d432561f4710de3838644d5d3c5cade16e3c3c7a316fa59f2be26', 256, 1, 'LaravelPassportToken', '[]', 1, '2025-11-20 16:25:46', '2025-11-20 16:26:13', '2026-11-20 16:25:46'),
('9ed86e0f33b28c63f868d792b79d0d09ff4d3594ac14c93b0af7add7bfb917f8868915f2b18cffa6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 06:20:15', '2025-07-17 06:20:15', '2026-07-17 06:20:15'),
('9ef1fab00d91573a19e38123e8d55d96d7f4a9f1d109fb8f89defa53e31602667e8ab1621e71c2e8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 14:53:15', '2026-03-16 14:53:15', '2027-03-16 14:53:15'),
('9f060455c1c83750ed9ebf3381688c0bc429c44a9afa9c91f62535e6ba32877544859fbad5d0b87b', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:56:39', '2025-11-06 10:56:39', '2026-11-06 10:56:39'),
('9f213b2a692fcdf110ed206f95acc15a7f1aaab5d0a5b8c231d5059a0fbd548d3f43952f380cc526', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-16 10:17:38', '2025-09-16 10:17:38', '2026-09-16 10:17:38'),
('9f2323c21deab2edd6baff3b270ab969c2904de3943748eeebe20ff0d0d5b3e21a612c61a6a47984', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 18:15:15', '2025-11-13 18:21:06', '2026-11-13 18:15:15'),
('9f645f2233cf3d30eade5ad75f7fc63999eb6bfa357fcb7a49b0d0272b5ae8913638dc337fa2dcf2', 214, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 18:37:54', '2025-11-26 18:39:17', '2026-11-26 18:37:54'),
('9f875178c97d0eba653a28518537a7cbaa2e335ebe981755444c4b6b1fceebdc8a2cf5fabd885845', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 07:41:06', '2025-09-08 07:41:06', '2026-09-08 07:41:06'),
('9f8d1eaa4cde6925871d805935f0a851027c83215c4697e49e6910fcbc0b2ba3ec10449f99e5d51c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 15:14:05', '2025-11-04 15:14:05', '2026-11-04 15:14:05'),
('9ff5cd747c7ccdd64b09bfdc7d4298fbceb0825413ba7f8cfb643ea2f4c3099de47ebdcac5c67f29', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-26 18:29:25', '2025-12-26 18:29:25', '2026-12-26 18:29:25'),
('a04fbb0378539ede72566b4d5d1a80b7b0ea174b3159c031c7e8ab1324650e1ef85939ce4b3cf4d2', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 10:11:26', '2026-03-02 10:11:26', '2027-03-02 10:11:26'),
('a076ba9c9095b1bfb65beb87d7895ecdc076f456db0ae1490fa5293cfcf45f46d9c820b20ceb6e02', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-10 13:58:53', '2026-01-10 13:58:53', '2027-01-10 13:58:53'),
('a07dea5dd062b486fbd63b7cb033f97435511616fdce12a66c454f8bc88b226cab7cddeb6a2ff200', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:23:38', '2025-09-01 10:23:38', '2026-09-01 10:23:38'),
('a0c448d4d35ac1f28dcfcafea70631e3903c2ec4bff54a31e9d78b5bcaf6e30a571506b41bae8fc5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 10:26:57', '2025-11-05 10:26:57', '2026-11-05 10:26:57'),
('a0c67b18497a24c1b50e7fa964980f9bb5fd5fdc0561de0a55795d3c859012d67b5f3cba2704de11', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-03 11:47:06', '2026-02-03 11:47:06', '2027-02-03 11:47:06'),
('a146858b244028d71e2bb3a3f144b906160fb72fac3ecf74256a23fb80b96982b52e67cdda58fc95', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 15:36:35', '2026-05-25 15:36:35', '2027-05-25 15:36:35'),
('a16844ad8d7aa3d570e5d3cef1d243e95b403325d8cf0589a4a2ff3ed74de9cce7059023ce7e220c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-19 14:49:05', '2025-05-19 14:49:05', '2026-05-19 07:49:05'),
('a17c8072724e9319f71df0c4c02ca9ae9587d1dc9f3a220870a309b76a21f9427ab53fbc4666ffe4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-09 13:38:07', '2025-12-09 13:38:07', '2026-12-09 13:38:07'),
('a1ab2c2c9ac39404d2cf4c05f31adffb77d9a5ccf06ec5e21e0e683789dd3a0769657d26ca1dc7c4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 19:07:56', '2025-06-10 19:07:57', '2026-06-10 12:07:56'),
('a1b9b5f1b02ca437659a258e67eed1faf1d36aabd67a3dc55a3d4c6c6d624933d36a335fe3ebebf4', 74, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 06:29:27', '2025-09-17 06:29:27', '2026-09-17 06:29:27'),
('a1bca63863b3e11b5a7093f29fa19cdd1b4c04d5c2cb22867654087ba7f15734ffd8df08b1c70b47', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 12:44:10', '2025-09-02 12:44:10', '2026-09-02 12:44:10'),
('a1c1b83d2f60d648f39301da099ece8e519322ad6dc6d250223eef9c40133bafea689f8b6469c01b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 12:03:32', '2025-11-26 12:03:32', '2026-11-26 12:03:32'),
('a1d56a5eb30c7e9208a0e0a1bdd732c9cccfbd0d31fac9945381508d0a2db7d42bdd83dd3f237fc8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 11:16:55', '2025-06-10 11:16:55', '2026-06-10 04:16:55'),
('a21af01176c4da7f987025ff11e26f518f5b3bf84d722ff3dfd1fe3383342156f2a6a302d973a895', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 06:57:53', '2025-09-23 06:57:53', '2026-09-23 06:57:53'),
('a21b50886e665d917bba07cb617bb6d2e0dfeceb6cca780fd1fefbbd8b4378a0c32e315ec44134e9', 150, 1, 'LaravelPassportToken', '[]', 0, '2026-01-02 12:52:50', '2026-01-02 12:52:50', '2027-01-02 12:52:50'),
('a221614e9047be75e9c55fd82ddebac9ab862f1431f8e677bd253b371273413177229a757d629d94', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-12 18:28:15', '2025-06-12 18:28:15', '2026-06-12 11:28:15'),
('a22cfe9c4cbb109e4cb92b626fa9682d8a5d00d4482340a45c08dbc5d2f73a59a61c7360f69400e3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:58:54', '2025-07-17 05:58:54', '2026-07-17 05:58:54'),
('a2456000e5b5022e003da74695b90551bdf22b7ec9306df9ca803d621968394ad9ab75e3072716d3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 11:19:22', '2026-01-29 11:19:22', '2027-01-29 11:19:22'),
('a2486f27ea873991837063537a5fa5b009b5ffcb75c3ad44e8ad575a0b201cf4ff1554ebc882da93', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:49:37', '2025-11-05 13:49:37', '2026-11-05 13:49:37'),
('a25925d09f0420cbb571486f186a3c965f2cf65ae75c0da5c7d072d140e80dc4837ca5f2b3652049', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 11:33:47', '2025-11-03 11:33:47', '2026-11-03 11:33:47'),
('a27761d8ec715d819a5f9e719180c4b08bd450036df9ee1626ef1b109eeed22576c72fa2839bbeea', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:08:07', '2025-11-11 13:08:07', '2026-11-11 13:08:07'),
('a28461238a8202724b4217824e199a2ce650ea317f627998ceacf3b2c6256588123e91b4d013db96', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 04:53:34', '2025-09-23 04:53:34', '2026-09-23 04:53:34'),
('a2bbb0a20b235f99126c2994552f70e499526f6ef94154f3f896a895fafdca0912f2eb442f5d67e2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 05:01:37', '2025-09-19 05:01:37', '2026-09-19 05:01:37'),
('a2c5a7a4cbcbe74fa8e218d04e46bed8bac6b6fafd0e2a058344d2eba987c9cdc6d39dfbc2f64426', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 15:23:57', '2025-11-05 15:23:57', '2026-11-05 15:23:57'),
('a2eee1bb70b317709370d97b8534350299a1fe398d558d282723347adbb45496debbaa0ae29036ed', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 15:06:57', '2026-01-29 15:06:57', '2027-01-29 15:06:57'),
('a2f91e74b83793aaad02c6f3f37e8f9662478b9cd24d6c835cb48fb538877a1e13a9542d0012193d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 08:02:03', '2025-09-01 08:02:03', '2026-09-01 08:02:03'),
('a306db1d21d85deedc349e590fcfbef0bc2c268b9c8ab86dc7a36f919708ad05f2a14c27822262ed', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:25:48', '2025-09-01 10:25:48', '2026-09-01 10:25:48'),
('a3204bf4a74f5a4a17ce3917a7be0368f0a8d06c9c62561ebf1cd02f0ca262c708233a81445e00ec', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-17 12:01:43', '2026-03-17 12:01:43', '2027-03-17 12:01:43'),
('a33437e14fadca4aaf48c55d2e337aa309443003cac24ea671e40572788b5ed538161df11175dd00', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-07 10:04:07', '2026-05-07 10:04:07', '2027-05-07 10:04:07'),
('a36c959e3a510a8d98ad000301a7becc8ec68b90716b79049327c1f06fdf279d9626f8cf2e49349a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-08 15:50:45', '2025-12-08 15:50:45', '2026-12-08 15:50:45'),
('a370b9d275c473a45034be87ae4449092987a93ec6d77f2de8fec02851f925be5965a899d929192b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 15:07:10', '2025-11-20 15:07:10', '2026-11-20 15:07:10'),
('a37407ba43397db6c0a98642645b476d304590394805b342e89b4ae17f677b16bb1ae40aed666c6b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-31 21:02:55', '2025-12-31 21:02:55', '2026-12-31 21:02:55'),
('a3c46be0238df20eb9169b3c88f060da1cba628c7314ceaf90da1d85a33ec18d587c085514193d65', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 08:24:57', '2025-09-05 08:24:57', '2026-09-05 08:24:57'),
('a3cc1628ba7ca1568924b106f9c1b19b89802d5d79cb8a77ff2e183a3e03e3370edf10dc2d35b149', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:31:45', '2025-11-12 12:31:45', '2026-11-12 12:31:45'),
('a3d0a8fdf9133293e988375b0c0e6bb4668bc7d8f7d4ee8a3cbd667c3d0fb92c293ff45722cf1f6d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 14:49:39', '2026-03-16 14:49:39', '2027-03-16 14:49:39'),
('a3e342831ff961802765e5b5034c8fb66ea82def1c15e153096d248e1ba045ea86fb8fa84bc1e8fc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-01 12:33:10', '2026-01-01 12:33:10', '2027-01-01 12:33:10'),
('a3f38b655cea793dbdd4fc77fdec5923900f265458417af7a9de0b58eccb0b9abaf3a4b423cf4e22', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-21 15:24:11', '2025-11-21 15:24:11', '2026-11-21 15:24:11'),
('a466fbf89d24a08641debe86a200a919018fe3ec87d515cb055c0f6edfbb91846bb1eaa7bbe5c06d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 07:12:43', '2025-09-22 07:12:43', '2026-09-22 07:12:43'),
('a47477a58084adc7dd297b7906828f53e7a2e90c713b18443cabe619299c4daa97450e539609be28', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-19 17:20:14', '2025-06-19 17:20:14', '2026-06-19 10:20:14'),
('a4e16281288aae760a03e95350cb8c1f3458ffe41db80f7168f4fc773330eb35322744c3e828d0f3', 210, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 12:15:59', '2025-11-05 12:15:59', '2026-11-05 12:15:59'),
('a4e24cd4b91e8a5c5b5932af203cba18c9317092b79827dc0317b23c534c5e47e6599a0696117e04', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:02:47', '2025-11-12 12:02:47', '2026-11-12 12:02:47'),
('a4ef5e2ded5beb4ac741e32fd09ac2021a3752a3ad9a39c5f73db6b4f88cc0c85b0b9da75a81d468', 175, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 13:49:02', '2025-12-01 13:49:02', '2026-12-01 13:49:02'),
('a539f170e19d7b1204bc0717f7318f74abff5c70529d005806571999301a6fc21b876b16dda0d925', 127, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 06:10:41', '2025-09-18 06:10:41', '2026-09-18 06:10:41'),
('a566567ea853e832e06b9d2aecd6ea6f22c8d74418842ef8a655e0990edeae853a8c76d62c1a9230', 288, 1, 'LaravelPassportToken', '[]', 1, '2025-12-01 13:16:39', '2025-12-01 13:19:36', '2026-12-01 13:16:39'),
('a575ec4fd65c33ab15f9bc80b022ff67150a7300ce32e95583f446e1026d15dc4b696a1b1bae70fc', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 10:09:16', '2025-11-04 10:09:16', '2026-11-04 10:09:16'),
('a581778d380817b6af3e09d099ce01a13ae2dbffd185577b82f31e99a8e3d04e4b148af7e3d25f28', 316, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 17:57:17', '2026-01-29 17:57:17', '2027-01-29 17:57:17'),
('a58d4eab29a3ec4ac0ec1dbefd2dcaa7327c4a08ce4fe756a93b1de6ba93c64f61808fa283f089a5', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 15:15:14', '2025-11-13 15:15:24', '2026-11-13 15:15:14'),
('a5a9a23211227dd07dcaf83528c42f6b16c959a2fd2282f96f85dd2dcf8bf3209df00a257a690d7b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-22 18:39:34', '2026-04-22 18:39:34', '2027-04-22 18:39:34'),
('a5aa603ccdd8d08e547a02def22779ce5ad395f7a008a321d23fedf1d33fe824559fc8e64563d748', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 10:10:13', '2026-03-02 10:10:13', '2027-03-02 10:10:13'),
('a5b18ba9dbdf4723cde45c480fd20a3896c33a91ef585aa67253195683d3ef244bf197b7d12baea9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 10:00:49', '2025-11-04 10:00:49', '2026-11-04 10:00:49'),
('a5dd8c4178fd7592c33db652c6b8a8aa6638e99dc8abcede396ca4b15e5f6426ad0fadfae830f95b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 11:04:35', '2026-05-26 11:04:35', '2027-05-26 11:04:35'),
('a5e13d0601e934695d96456c48dc2e2a37c44eb6f23cfa4bf1bce5469d1e0fe62bbde2d17c857ab0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-15 10:07:35', '2026-01-15 10:07:35', '2027-01-15 10:07:35'),
('a5f2c3ad7179b8f98d1b9b3bce921cd26ea6670640d93d6fefb0fc62c43b215ebf2585775d50f170', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-27 07:32:27', '2025-09-27 07:32:27', '2026-09-27 07:32:27'),
('a61306e66de75e00f566bca1b1c0d59da83d58fe94e8eeda92c5af61ce50e7994d8af756af57b4c3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 14:37:17', '2025-12-01 14:37:17', '2026-12-01 14:37:17'),
('a6149b005867c631b426e3d9f77c24880bc826841d59d5b138cfc2c0010fcdcf64a47eac6d9cf926', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 15:36:21', '2026-05-25 15:36:21', '2027-05-25 15:36:21'),
('a618e467557a8c725b60f4faf4fc0f5dcb297937a5c41d9754d57bfcac5e3eba24c171069bd89493', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 10:23:23', '2025-11-12 10:23:23', '2026-11-12 10:23:23'),
('a628bb98db6407d96b4b866a3fdef027aafe9737c6deb456044f283ac46ff8950f92106f5e2687fc', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:38:55', '2025-09-01 09:38:55', '2026-09-01 09:38:55'),
('a62bff3b4943c0a124279d29490c87fc3cc68057d84ddd34547b8b632f1f1c4d8076a992cecf834f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 05:33:26', '2025-09-25 05:33:26', '2026-09-25 05:33:26'),
('a645bd58ad31235bf85614ee52f85daf4fc0792211ffb8a30a8c221b822ceb9181fa050fd05722bf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-09 11:20:18', '2026-01-09 11:20:18', '2027-01-09 11:20:18'),
('a657fe40e78b2b6d8c0e482921d6a58fe681be4f4c115d1c666ae355ec6a1f36f9823506f10a2432', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-19 17:17:38', '2026-02-19 17:17:38', '2027-02-19 17:17:38'),
('a66e4fb58193cb3ebe4dd3e7d9e34836cce1133d403ae914b90247ef4141facd46349659ea376ccd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 13:52:10', '2025-09-01 13:52:10', '2026-09-01 13:52:10'),
('a6b2b93303d06fd35c7395c05e846c90df577a2eccfd4e33ae8e4f5ad05477461d28748179e0d34b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 09:36:09', '2026-03-30 09:36:09', '2027-03-30 09:36:09'),
('a6fa5bda07a1fdc22403f3588c43abecee451c981b717ea5127efd1d1327726ef23adfdf178b932a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 14:46:45', '2025-11-05 14:46:45', '2026-11-05 14:46:45'),
('a715fcc1f5f46c449709d132920699776aa177338fe906e07c745c4a4df6025f430a9ba45bcba8ef', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-27 16:49:59', '2026-03-27 16:49:59', '2027-03-27 16:49:59'),
('a7163bd117778ca3c2d516a5e3016431f9c63b31516a166cab6545960d36ac39167a7df7866dbd41', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 19:10:25', '2025-11-04 19:10:25', '2026-11-04 19:10:25'),
('a72e71bbd7d783f2df58319db70992e5ef3ada3a43e36b85925effd8af0533e4cbcb69c8a3bf6fe0', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-12 12:35:47', '2025-11-12 12:36:22', '2026-11-12 12:35:47'),
('a74bf04f789cfbb660e574bbacc13160a995291794c5270f0db44993bf0418c02e69d3a51c0656e7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-13 18:22:32', '2026-01-13 18:22:32', '2027-01-13 18:22:32'),
('a76f409114e0ffcc1f5bad775e75a2f820b3b5e97e1d61d1bd647b9c45cc80998fcb73c1735031d7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:50:00', '2025-11-03 12:50:00', '2026-11-03 12:50:00'),
('a783bd0df098360fc461a0852ca0bf9d1c70e695c2a30141c6c659475ff4aae158021fcd17b86dc7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-22 08:11:34', '2025-08-22 08:11:34', '2026-08-22 08:11:34'),
('a7d597419ec611f388c6a56cd299788a275db4e997b6c6bc1036399e400166d3959376e4c7ffb996', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-13 17:25:19', '2026-01-13 17:25:19', '2027-01-13 17:25:19'),
('a7e90d09cfd4753c9f4c0d0879c0dd5be92d9754b5b95fadc76345016f2c3122e77c3b4c78a373ef', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:56:16', '2025-11-06 10:56:16', '2026-11-06 10:56:16'),
('a823780d8ac0125e927c2325241ce9f4c4c949a1e78a3fceafc83f6f24fe282ab6858cfb3626dd10', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-24 16:39:49', '2025-05-24 16:39:49', '2026-05-24 09:39:49'),
('a82589cb0e8d72dcbbf9d95b9c196b9628391bed676669899968ce0c5918d917c36ab76add1f7156', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 09:57:03', '2025-08-28 09:57:03', '2026-08-28 09:57:03'),
('a851bafa052c7513fd954161920acaa0cc4a6f46d01a35535f9bd38cb422ab67104d058cde84018d', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:08:02', '2025-11-13 12:08:02', '2026-11-13 12:08:02'),
('a89b2373a699587e662cc7c07197eb2b020b48e4cfaf231a314ed26d79a12f73c73e3a621838811f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 12:08:02', '2025-11-05 12:08:02', '2026-11-05 12:08:02'),
('a8a97ac463023c942ef6c914d4760188302d1e2a0da8a7edcc8fdf9d3260d3f0ea1b1e9031ddbcf3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-27 12:40:37', '2025-10-27 12:40:37', '2026-10-27 12:40:37'),
('a8b6e89daac6d3cf76aee1f0a85509b1c059d384cf4eac00f75b738dc4548e4aa31dd71583ba7275', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:24:41', '2025-11-13 11:24:41', '2026-11-13 11:24:41'),
('a90a2596ac04f087e578df5a1c81c7dc7c3eb4da11a6cfb2160f1f16e3078e947ed757e1e1c6c639', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-11 10:00:53', '2026-05-11 10:00:53', '2027-05-11 10:00:53'),
('a90fcb766a0309cf783d6f2528fd2f37733f275d7511691b0840393e2361e359d861340f346b165f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 08:36:47', '2025-09-08 08:36:47', '2026-09-08 08:36:47'),
('a91b1d1c60b4c5df1312b7723b0602a6b590c0005c82c433282124d1b87832076a90861b8be78e22', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 14:58:52', '2025-11-13 14:59:09', '2026-11-13 14:58:52'),
('a97562c3639461232b2c118890f09281f7139f278602d7b0860288e67aecc627682586173d66a0a3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-28 14:05:45', '2026-04-28 14:05:45', '2027-04-28 14:05:45'),
('a9956312a06231398eb506129c4e20e7d8887b9cd513fe373578a72f928c3d92e5777c9a4543facf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 18:56:36', '2026-03-16 18:56:36', '2027-03-16 18:56:36'),
('a9a825461a0b669d03d83f6302552e92faa398eee7edcae63eaf5df7f89ba335d9d7d695de25bfc2', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-16 10:00:44', '2026-04-16 10:00:44', '2027-04-16 10:00:44'),
('a9b5998fcbcf726ef2b5ed156bf423198e6c903146f989a35f1cf7ad78fc18e89f3fb49f5ba550a7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-04-04 00:39:18', '2025-04-04 00:39:18', '2026-04-04 06:09:18'),
('a9dc2c999056880dba72009bb6ba5d4bd4ac4994a26c4af5118c8ffb62e866ee518f4bc9d823af39', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 07:47:46', '2025-09-01 07:47:46', '2026-09-01 07:47:46'),
('a9f99551d0e998053f35a009e893605d503268c8b269ba21f242696ecd89fc8df579628ff0141915', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-24 15:19:11', '2026-04-24 15:19:11', '2027-04-24 15:19:11'),
('aa20859277a46f69704bd1d24e954a9524ed32ccef98f2ec150d9c53298040b71611123c22d5c2b6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-08 07:01:28', '2025-10-08 07:01:28', '2026-10-08 07:01:28'),
('aaa4f50301835b05f1fe05db25f705e41eb1833cf44fa732d64217f266ac5bac407674bf6dbb7e56', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 06:06:43', '2025-09-18 06:06:43', '2026-09-18 06:06:43'),
('aab3de9045a833c10e711f84dd0680ecb753bddc44ce3d82c57db8c5cc4dbdfdd3a6267d55050153', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 10:06:19', '2026-02-10 10:06:19', '2027-02-10 10:06:19'),
('aac465ef5642c62bab1f25922d2effafd243afdd7b308ec9257683e402da49cb3355c0d106dfeb5e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-24 12:14:42', '2025-11-24 12:14:42', '2026-11-24 12:14:42'),
('aad2757ebc148ca08833ae4edffcf887e6baf9a25f61f42861df96092fd83f7fade3506e6d28178b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-31 21:02:18', '2025-12-31 21:02:18', '2026-12-31 21:02:18'),
('aad813198ed488dcc2358a0be71bafbf589de7fc21a3e6d5a3cb405cedae18e6202fed8a94f1c1d9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-19 13:39:54', '2026-02-19 13:39:54', '2027-02-19 13:39:54'),
('aada2100674ed6c9d3c38ece7cc3593eb11a1d488d75dee13f7dc925452a30a47b92cdfb097b76ff', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 14:49:52', '2025-11-05 14:49:52', '2026-11-05 14:49:52'),
('aada7dff2ee3e94d2892520cf6e2610b57edf60399fbee0e0efd0bd97418864ee05a0b487826c69b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:04:21', '2025-09-23 05:04:21', '2026-09-23 05:04:21'),
('ab1f3f62ac33eab8fce55a9df6c3ce3134a7ff291bd251bab3003fba027eaa5addf1b5089c217207', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 17:01:12', '2025-06-11 17:01:13', '2026-06-11 10:01:12'),
('ab6244468c5fa13e1e58a021b31a61500e26430831f02a06e42e285605c182437328c57b81d4bd42', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 08:06:32', '2025-10-03 08:06:32', '2026-10-03 08:06:32'),
('ab7c97eff250366c56a93c629c220fa0e936e5c3bae8d9e986c8677ae121d6653cc97f4bc731b66c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-03 11:24:51', '2026-03-03 11:24:51', '2027-03-03 11:24:51'),
('abb2b1a5e9a982ca9964ea40600488198957c81776eb636f85b1fb4fa242e2719c29c77c11cf8118', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 14:59:36', '2025-11-13 14:59:36', '2026-11-13 14:59:36'),
('abcad7b2b9cdc4d90b0808ea08353b8746af12c2671856daa7bd70d1b1816b42b9580afc86c54aa5', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 10:49:42', '2025-11-13 10:50:51', '2026-11-13 10:49:42'),
('abceec2774aac053f642054855ba86530e7c9d22493c299932764b1d9cb4a365edea40b802d3c4bd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-15 04:37:16', '2025-09-15 04:37:16', '2026-09-15 04:37:16'),
('abe413b539446b299c6b164e98109319db5f480e421bb393c78feecf61dd6d19aec79869150e8b47', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 05:27:31', '2025-10-03 05:27:31', '2026-10-03 05:27:31'),
('abe81e6a94c0d13c43792a7345b36f16e78ac73ac945f1b3e4f377a94216468be57f27ec8e0aa4cf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-01 12:40:13', '2026-01-01 12:40:13', '2027-01-01 12:40:13'),
('abe8f4fa83f21668559cde8c4c1f25f4ef18f48df8039a8f1f7d6e77c1c42ce6770b93cd0dd70d3d', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 14:37:23', '2025-12-01 14:37:23', '2026-12-01 14:37:23'),
('abf8a9a663d5af4d66b979d99c428c3cf833bca83b9c4339fc612f4ada2f5d9e4fb2ccdbdbcfdaaf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-07 00:05:27', '2026-02-07 00:05:27', '2027-02-07 00:05:27'),
('abfbbf1a6d94634d298745e4af3a34c438f9fe19a5f67d42bbb400470692570b0541804142eb7983', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 11:53:49', '2025-09-22 11:53:49', '2026-09-22 11:53:49'),
('ac20fd5d469bf3324ff9f61e0f7264bd68664f00461b7ccce7884766b4f073a1e37ace706a354bf7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 13:22:14', '2025-11-06 13:22:14', '2026-11-06 13:22:14'),
('ac67250ec471e93937adf2dc3c0f5303142852e39f21aad5dd9568a72bb8e409de3760f009004871', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-20 10:06:21', '2026-01-20 10:06:21', '2027-01-20 10:06:21'),
('ac6b06214817704b1cead7bf43897ff65cc59a81598bb1e9547b8237a15ef44a378cd215b7807c8a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 07:27:16', '2025-09-02 07:27:16', '2026-09-02 07:27:16'),
('ac8b86061440bba5e7240f82f9d0e29195c6f1891bf882294659e306e18b1b33175012b2f7bdb546', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 09:12:35', '2025-10-03 09:12:36', '2026-10-03 09:12:35'),
('ac9f459eb9ec3ca759c562106cb76f7efed7ffc578101fa7eb0a6d54f3ede97768aadd71a7462a1c', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:14:44', '2025-11-12 13:14:44', '2026-11-12 13:14:44'),
('acc83c93120ededb39c78ec62fa1fcbcd3eec1c64dc2afbe862ba1d439ca10294d43cb98e70976a1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-05 10:04:15', '2026-03-05 10:04:15', '2027-03-05 10:04:15'),
('accaa76f29190c8e9a42531f57c1cd68c6c21d2f0e963c85e65c2c912fef67c4d62d98f9f03cf941', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 16:00:21', '2025-11-04 16:00:21', '2026-11-04 16:00:21'),
('acfd50d0e45528eb4b6e8dc683ad1c1c67264f1498f8d93a0fa1a5aa2d60f848d25eefb438ff83e8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 13:44:50', '2026-03-24 13:44:50', '2027-03-24 13:44:50'),
('ad0ed01792baec1f8ba560d5aad14f374bb9fca1daadbfdb0e12121dfacde8161bd33e3e891d7b5b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-29 09:48:56', '2025-10-29 09:48:57', '2026-10-29 09:48:56'),
('ad23f394ec2e01be06dbf0293a19132d8338826012953d4b64fca928efb57e6e06d53bc15ec408b4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-01 05:15:05', '2025-10-01 05:15:05', '2026-10-01 05:15:05'),
('ad2a32c49c2218309acc5915dc92472218f69d9f7159338c2759b0dd287ad44584be7fd1fd14ca85', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-26 15:01:56', '2026-02-26 15:01:56', '2027-02-26 15:01:56'),
('ad4db9d53f315972cc139715ede9e87d58cb47c0cf7d1113331b5061b90a7b4d1186b19e10078724', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-19 17:17:41', '2026-02-19 17:17:41', '2027-02-19 17:17:41'),
('ad56e86ee65a5e8b35ae656a6e0eef6e54c4c1e40ba867022f75f0a99314d70bcaf3d5f054384b1e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:05:30', '2025-11-12 12:05:30', '2026-11-12 12:05:30'),
('ad6baede697a342ae6af27b9189ef80a00ebdf61286ccc39778f31e5e9f3ce2c4d6d67f5d13e2021', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 13:35:38', '2025-11-13 13:35:38', '2026-11-13 13:35:38'),
('ad863e92a97d503d268f148df211818b22ec9aea186ca5ff4ce9140d20ab720255eaa3f0f9d3a7ef', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-11 11:47:17', '2026-05-11 11:47:17', '2027-05-11 11:47:17'),
('ada47dff5f01d15112f60a938f2834b5dd4c5b58db68eb6f7641eefb131c1fc707fa8f11732bfb02', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 17:42:01', '2025-06-16 17:42:01', '2026-06-16 10:42:01'),
('ada5cd0e662a973a5b59d0d945427dbf98fe7b6ef7246a39d1d525acaf633b109ec86effda3003fd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-23 07:07:01', '2025-08-23 07:07:01', '2026-08-23 07:07:01'),
('adb8fbf938bd9605b3de44fa4279be67c0384c025cbe47641de20c4fed726e68618d8b98b249331c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 15:57:06', '2025-11-13 15:57:06', '2026-11-13 15:57:06'),
('adcf5cf467caf2d6a654f939e3a4260320ce61ab437c1a0d44d2f1a042a56a93cb372056cc0405da', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:07:21', '2025-11-13 11:07:21', '2026-11-13 11:07:21'),
('ae2204f7a88c560b68b3ec950859b9423f9299612de58381cdeb6a63bc817d2f23442e0336adec3d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-07 12:45:12', '2026-04-07 12:45:12', '2027-04-07 12:45:12'),
('ae47f15e23ccebce2cfb9d7789ad06a9090391c5c2a81a19016c8d4dbb6fac7ad0dca987d51e36dd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-13 06:57:53', '2025-09-13 06:57:53', '2026-09-13 06:57:53'),
('ae51746b40c9c89047d8138ac4d713be28f44fb1ab592491ee83da381cd9ee9c55d44fc704681417', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-12 20:07:45', '2026-01-12 20:07:45', '2027-01-12 20:07:45'),
('ae526233c7989e08882b556622936974d787ee16103f07aef717eb0e8cb84c3bc35894854a96e9e3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 12:48:09', '2025-09-01 12:48:09', '2026-09-01 12:48:09'),
('aec548ce657a39599263401e64711667b11f533c462d51fde63930035cfaa25e36544be43f8cdd21', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:44:52', '2025-11-05 13:44:52', '2026-11-05 13:44:52'),
('aecee5039f5b9deb698d2183f82a9b4e47a2133d9afeb3659dc6fcf5ad6f63068636db955b3e8de0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 06:37:42', '2025-09-01 06:37:42', '2026-09-01 06:37:42'),
('aed1f0631c40da50cd4d004db2853e2836d4a38249f047ba48abbfdc84a6f34e86f8e17aa55ffd13', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:53:58', '2025-11-06 10:53:58', '2026-11-06 10:53:58'),
('aed4d184ce7105e6bb4c51b2eee1ec255ea2d9937680539bff07da5367738f3d30d9f8d7f324176a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-17 16:54:50', '2026-03-17 16:54:50', '2027-03-17 16:54:50'),
('af2037f610edecb41e284d06319bd5ef8b8e1b44d8448eb355f09015465be8aeb17df14ca8b4f224', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:34:30', '2025-11-06 10:34:30', '2026-11-06 10:34:30'),
('af40376d123931548187b8811da55f9d62696900e7dd8affaafa9d7b22db47a4780d8ed5e5af8f3f', 22, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:58:33', '2025-07-17 05:58:33', '2026-07-17 05:58:33'),
('af4aac1fa50568b564e5aaf0b23784495d0564e47d5a42616578e48fd6cc2410c621382c3556b20b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-24 18:24:44', '2026-04-24 18:24:44', '2027-04-24 18:24:44'),
('af576beeb79d5715fdf5e4118f65e3415e86da0e84250faa8690a8859b0b00019bd5be34ad6cb92d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:34:44', '2025-09-01 09:34:44', '2026-09-01 09:34:44'),
('af6f86cbdf079f7f4b1603020adf0f5cd5d0034bd6b5bd4f9dfe4cb81352d0e311362cfa91156a36', 21, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 12:22:41', '2025-07-16 12:22:41', '2026-07-16 12:22:41'),
('af9cec8eccfcd151bf797fa88e0ca52d2cb46ccc5a28837a8eeba3db9713ee0a931bae440d4a297e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-04 15:10:29', '2026-05-04 15:10:29', '2027-05-04 15:10:29'),
('af9d452f98c22cfb3c7363e2a5e49c845d5e090a79bc51d1ddac8f460b665907aad7b076e4cc0141', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-17 23:09:25', '2025-10-17 23:09:25', '2026-10-17 23:09:25'),
('af9da88fb99420ce1eebba1690faa5588ca222e74a7b227c66c4580326e5d8334bf403bf37e89f09', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-20 14:50:42', '2026-05-20 14:50:42', '2027-05-20 14:50:42'),
('aff256f5216bce919ad419694e517a6f826f6ce8f0599aed2165646a0212534191803b16ac7dc257', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-25 13:41:30', '2025-11-25 13:41:30', '2026-11-25 13:41:30'),
('aff61f2795d4d8cd0e4de37b6a2c8ab1ad22b364730ae9b7bede6e136f66ebd18127f3cfdc6e4d23', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-10 04:25:45', '2025-09-10 04:25:45', '2026-09-10 04:25:45'),
('b011cdb8f32ef83147d489c8443ffcd28af718ab380a296042bcc2b59c6d9c6c07b6ddb75794fde4', 76, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 06:38:06', '2025-09-18 06:38:06', '2026-09-18 06:38:06'),
('b02f568041df7d03cceead181ac195c3f64d08f7323c94c1526babb09f756285988e383d043e0d5f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 11:11:24', '2026-03-02 11:11:24', '2027-03-02 11:11:24'),
('b03abbce1fcb91ef5406dae93c63611b844b10ea9477ede8a7d4467a788e430754678ee8bd14b18a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-13 14:39:44', '2026-02-13 14:39:44', '2027-02-13 14:39:44'),
('b06064d9aaea37e5005217597cd8e0ee5913271b1c230df50809bea3076e10455610f00dd8ed2880', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-05 17:12:44', '2025-12-05 17:12:44', '2026-12-05 17:12:44'),
('b06d910835399af9b5a098a719dd1961e656df953dde61d05c87ed025839c4d2c6b69ebf705d4e75', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 14:38:03', '2025-12-01 14:38:03', '2026-12-01 14:38:03'),
('b0791f50f36eef836db714609d24fd055f9edbfc46edebc60cdaea0ba2bd4b1d56aaad0a92efcf47', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-20 18:36:09', '2026-05-20 18:36:09', '2027-05-20 18:36:09'),
('b0955039e31884119f984b516bd73b05ed29d28fdd3175a8f3eef2109f4ebf7dff5a5678e4827343', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-16 04:22:28', '2025-10-16 04:22:28', '2026-10-16 04:22:28'),
('b0db74ba3b807a335c77ee165ffdd726a6b8d2b08043d4b788ce886e9781c9e9d435bb2ddaa1ae51', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 11:00:09', '2025-11-26 11:00:09', '2026-11-26 11:00:09'),
('b0ee8c8aabd5bcdabb93aa991f56162f230dbe191dc643a9cc3ad1889ff5431ca5915a012025ff63', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-08 11:33:44', '2026-01-08 11:33:44', '2027-01-08 11:33:44'),
('b0f9a65ed9035d5fd39c6405e9155fa4950c88b5f16239520894fedb1aed7b90daac0bccb12f53a9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-15 15:58:13', '2026-04-15 15:58:13', '2027-04-15 15:58:13'),
('b11d22ee5cb17378b2530aa68948ced8a9602801598d6690bf06b7f341024cd2b793d56d42c92258', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:53:14', '2025-11-06 10:53:14', '2026-11-06 10:53:14'),
('b11f44c3a59d159bb654cae523b52f30f7ebb8ac25f4ca3be8ec9e8125f702f189bb0cda0301e5b2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-02 10:37:00', '2025-12-02 10:37:00', '2026-12-02 10:37:00'),
('b1fe9c13d65171f2820732a5d00828872b1e8f42de79b3b39a650083f11c3ec16458df6075d3a193', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-05 17:25:11', '2025-12-05 17:25:11', '2026-12-05 17:25:11'),
('b21fef509d4965f50422cd0d1c2545d5cfd4f6f0e0ca52b1947b6dbdc19025bf2eac17b6313b0370', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 14:54:40', '2025-11-13 14:56:16', '2026-11-13 14:54:40'),
('b23ff350373a0ded99ba2e62961ab11302db34fc2746692c99408a14759c31d1b52f8d55e3d564d1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 17:01:09', '2025-11-04 17:01:09', '2026-11-04 17:01:09'),
('b243122252872b232c9c29ab9a2227a5959c6677dc17b78e848565b25b2e844586992ce5c55c31ad', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:56:36', '2025-11-06 10:56:36', '2026-11-06 10:56:36'),
('b247eb32fb9c76f934caf04a2f43b8a3829ab835306c56befe925e31d3580c48c4a07712f2081a3a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:15:47', '2025-11-03 12:15:47', '2026-11-03 12:15:47'),
('b2635c5ea5e6b22f7fd601c467f9640923a19bd29b924a2e707f34a742b32ec9d38cf56fc3887e0a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 09:46:56', '2025-08-28 09:46:56', '2026-08-28 09:46:56'),
('b2af8e12068406e44521dd5c21b0d5c6c182ae91af92dda45b0e8fd6c7173ee98385481f667c844d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-02 11:26:10', '2025-06-02 11:26:10', '2026-06-02 04:26:10'),
('b2b7843f1966f05f82923c36e8d60d3762a7a1d8959588530ede037be5cb56652e16b7f7687e5a28', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 10:13:39', '2025-11-07 10:13:39', '2026-11-07 10:13:39'),
('b2d57f85cca33b6864bb6d1136b57e03b8f71c9ac3380c449d7cec4895a3d02041651ae5803b6e68', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 12:27:58', '2025-09-22 12:27:58', '2026-09-22 12:27:58'),
('b2d7adb09708e2c752da02c157a67e211b8d5b930f81a7b9ff859a956ecd2066e6817487633dbbf3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-23 12:57:21', '2026-04-23 12:57:21', '2027-04-23 12:57:21'),
('b319b16ff07611015425045f317e95c4d0cbe96c3b0fc950b1def337f67cc0076cedf83f9184fd35', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 12:21:09', '2025-09-01 12:21:09', '2026-09-01 12:21:09'),
('b3218462095ea335355f0367f392fc6450c7bcb3ee4ab0f20275e7ae8d9327982449a545b978fe0c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 12:12:40', '2025-07-16 12:12:40', '2026-07-16 12:12:40'),
('b355b73b5d25ff6bffbb7102dac5b01caa0060c3cc1ce24b881fdfd3eb3df3832891a711edc3a221', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-16 13:05:10', '2025-09-16 13:05:10', '2026-09-16 13:05:10'),
('b35a6d9af245a6b6651037e14c79d261e8e4233995a0afd9ede00160804204a36225e533a0f88620', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-15 16:52:58', '2026-04-15 16:52:58', '2027-04-15 16:52:58'),
('b36e9127cb3af350c899d63fad599838401c088c5266bbaa88f804d16a595998039fd90bfc64848b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-22 10:01:19', '2026-04-22 10:01:19', '2027-04-22 10:01:19'),
('b38f6f8602aa8bb0e1b9124c9280d3de5dc73507bc9c9e5a121a74e6fa4838709b3abf9c05071b9b', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 13:36:06', '2025-11-13 13:36:06', '2026-11-13 13:36:06'),
('b3a10c70158622274603c991f6122ea4ff94107110d6b0d8a21ee1b073c3fb49733443141c442f35', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 11:49:10', '2025-09-01 11:49:10', '2026-09-01 11:49:10'),
('b3bfe7f0555645bcac0c376bbbfeaf9f34f12fe7134beb47ce1d498e60ca82f40ce37bf0f0268aed', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-07 18:55:01', '2025-12-07 18:55:01', '2026-12-07 18:55:01'),
('b45c902629c48ce2069cfbb3a3ffa80db3df2a66f588a3d34117b2ba7c050248e6eae607093d3bdc', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 17:42:40', '2025-05-30 17:42:40', '2026-05-30 10:42:40');
INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('b493e2289473dc29447745873e94d5552bd0ac7205c6934f27417847e0e5d837847d2e0adbcac4f2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 12:04:14', '2025-09-05 12:04:14', '2026-09-05 12:04:14'),
('b4dbe72492c82b66610768249ab2bff1f08de4528f26e1181dae0452e21930ab875f76a061c27ee5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 17:12:09', '2025-11-04 17:12:09', '2026-11-04 17:12:09'),
('b4e871cece73872d93300d4a8765a47fbde23654a17936f4ea34be14b47acc012c5a70778e1a404d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-11 04:29:48', '2025-09-11 04:29:48', '2026-09-11 04:29:48'),
('b4f49456881006d6b50623b4dad7607570751df749cc9de41a034f28b24d86f08ce5f29d7747fd31', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-30 05:22:22', '2025-09-30 05:22:22', '2026-09-30 05:22:22'),
('b503392831680adfcbcc44891fc7134349b5ca5310c61e9a9386fa6aadd6150ff1a93d489c4cf536', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 16:10:14', '2025-11-05 16:10:14', '2026-11-05 16:10:14'),
('b538f7f607183ff7d9a4f684c13984eebb98cd5a49c59cfa15309ff46e1d79ae631ceafc94eed2b0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-01 05:59:11', '2025-07-01 05:59:11', '2026-07-01 05:59:11'),
('b53c8ec0c45d867666ecedaa86bd27d5cc235a48354a0f65f4d5bc0351c7048b193918bea0f5c058', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 12:09:57', '2025-10-30 12:09:57', '2026-10-30 12:09:57'),
('b55af96bef451e1be73ce05d927f20163db43a10100f04236124163336ada710672e7e8512e83b01', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:33:33', '2025-09-01 09:33:33', '2026-09-01 09:33:33'),
('b55b0c6c11d5f709e068f8ce00cf62f74835cd35c35a60dfb2b7622b9e542c3d89770b937b128ef0', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 13:28:26', '2025-11-13 13:30:25', '2026-11-13 13:28:26'),
('b5ab00187b387b743eae2bd54ed01be23da822ed2b94b0cc8bb4be56e01edc6079d52e9a2556350a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-06 18:23:20', '2025-12-06 18:23:20', '2026-12-06 18:23:20'),
('b5c501196852ab8f92102332d32a434a0b63f28d5acfbe1d483a1ee7750766b7ca5df3baffe16be1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-22 15:56:03', '2026-01-22 15:56:03', '2027-01-22 15:56:03'),
('b5c560ce549920892c82b8787da370f2821050c447c194c04f651df88016245165fc7fdc1adfde65', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-01 13:29:01', '2026-01-01 13:29:01', '2027-01-01 13:29:01'),
('b5e155756f44933197690d423516ab6814dfcdc5b61eed145d1d4d07acd0141af1cb5f1e12548223', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-03 11:17:51', '2026-02-03 11:17:51', '2027-02-03 11:17:51'),
('b5e1e3845b52ad66ed15732fce1f4b06e5c327ad949c0bf10284731c0b8d66d69c11c78f43b0801b', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:41:24', '2025-11-05 13:41:24', '2026-11-05 13:41:24'),
('b5e8ac87b2c13366ca5c1b0fbaa7a62871cece348b64916270da2218c12594389ca70ff26444b547', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 11:59:41', '2025-11-06 11:59:41', '2026-11-06 11:59:41'),
('b5ed64ea22b57915fb317178192fc0e67db0888f4973d716ee6d467f72f6965b1f364b952524024e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-20 09:40:48', '2026-03-20 09:40:48', '2027-03-20 09:40:48'),
('b60e9cb2078be2be4b00c7843fa76fda3f20281634182c08f0468a52108a329a7493f7dd59ee361a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-28 12:21:39', '2025-07-28 12:21:39', '2026-07-28 12:21:39'),
('b60f8a3e1fe7796bff42cbee64ef14310032336130632d395530f39b0124714a67fa3c0d438f8091', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:31:03', '2025-11-05 13:31:03', '2026-11-05 13:31:03'),
('b61914f00338d60ee2a7e7e033cc2e9ea97d8de65288303476dc049777845eb180bbf01ff45261d3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-08 16:46:37', '2026-04-08 16:46:37', '2027-04-08 16:46:37'),
('b62e89df2602fe9ef7bb2b51097e1cc58cdba5ee8de4aa1e4c5124be03dd3467a863a0de25eea2f3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-16 01:48:31', '2025-05-16 01:48:31', '2026-05-16 07:18:31'),
('b64b454fba8e1379ee45563969cb1470dbff5fe05110820aeed64724f27097688251640c5dc6b767', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-31 11:37:29', '2025-05-31 11:37:29', '2026-05-31 04:37:29'),
('b6761579b9991f6f8766d8b42c88a06d4c123d2631058a52ef757f5ebf42e5a03d7cb30c299d0801', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 10:26:04', '2026-03-30 10:26:04', '2027-03-30 10:26:04'),
('b68590dbe4606a369757fb36111f2d166fafb2bb744395007c127707789028484c102218eb4c660a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 07:56:50', '2025-09-03 07:56:50', '2026-09-03 07:56:50'),
('b68c26b2b57d14c70eff1cd4110c1ee9c5d61703cb841a68416f296f82b48de42227e7cf802dc4b7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-29 14:28:47', '2025-11-29 14:28:47', '2026-11-29 14:28:47'),
('b69eea35776b0592a056a8085422418d8b263df338cad9383432dcb97d7b21261a56f91e64b684c2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-29 04:32:34', '2025-08-29 04:32:34', '2026-08-29 04:32:34'),
('b6a994eb1055ac63daccfac95eb16b384900efa0242593a2269d552db7b78ab9037632027c450861', 288, 1, 'LaravelPassportToken', '[]', 1, '2025-12-01 12:39:52', '2025-12-01 13:12:32', '2026-12-01 12:39:52'),
('b6ae29476ff24c98b5e1600b6f81d49d49732a83db3b0e2095281c668741a53d8fa4dcfb3d1e7292', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-12 17:10:12', '2026-05-12 17:10:12', '2027-05-12 17:10:12'),
('b6c68338a2f64776d15907f394e24eef45e7d3efd2d9003fc9916ac3788f5d74bc0c4b621bee1de3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-13 15:05:44', '2026-03-13 15:05:44', '2027-03-13 15:05:44'),
('b6d008f27e651794d444ed1899caa87adc843af86ddd36f6d85d81080e4d5ab31753edc231d3d137', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 20:58:47', '2026-02-10 20:58:47', '2027-02-10 20:58:47'),
('b6d4a76c0fbaf3746d687cee13ee5af912c0e110383827cc1e0ac03004fb74de9388b3c2793b5459', 209, 1, 'LaravelPassportToken', '[]', 0, '2025-10-15 10:49:09', '2025-10-15 10:49:09', '2026-10-15 10:49:09'),
('b724bad815e42a747326b50e45cd3e6a9671085300635a6c05dfa03113374a1462223a9da03d883c', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:08:39', '2025-11-13 11:08:39', '2026-11-13 11:08:39'),
('b744b583f1ac954aa1249d26ba4236ac4239c904636eacd3e78410e2db5f9f11fa01e8d301e15d59', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:38:18', '2025-09-01 09:38:18', '2026-09-01 09:38:18'),
('b76cee73216f31dfda6a4898a96aa5e112725213caccc4b216324bdbb626c91531f369a0bef045b6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 17:04:40', '2026-03-31 17:04:40', '2027-03-31 17:04:40'),
('b79746a0f8ec41ef9336390d68843e93465e748ac589328f0bb144457a64553277ac6768bb152d4c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 19:00:50', '2026-05-26 19:00:50', '2027-05-26 19:00:50'),
('b7aade2b8a3fa1b773062e511dbf4bf290da203b657dad996abe534a0ff7aff67d1d6a32eff2933f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-01 09:59:25', '2026-05-01 09:59:25', '2027-05-01 09:59:25'),
('b7af8596907136020fd38f57303a820ae692037eb9b7025be494229548f56777458c410ddfd3a1da', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-03 14:57:31', '2025-06-03 14:57:31', '2026-06-03 07:57:31'),
('b7ee19eebe559e9a8981a02271bc57459d1580ddec2152121f1cd31fda066675e3252cba04a8c310', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 10:12:57', '2025-11-03 10:12:57', '2026-11-03 10:12:57'),
('b800fb386f11bfc46298bccb177813acbf9baefe0758675638c94901d6235fe0305c334a4390642c', 219, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 13:15:21', '2025-11-03 13:15:21', '2026-11-03 13:15:21'),
('b80cdab0a88fa6d503f04ab7dfb13ff48548f9568a14d85d48c0d01b47664db45e38b670221967a5', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 18:52:27', '2025-11-28 18:11:27', '2026-11-26 18:52:27'),
('b810323d98995bf3d787540bb505ca3e0e9bd86d991c195d50397af2260ea208dd8480b888a9a0ab', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 10:25:17', '2025-11-04 10:25:17', '2026-11-04 10:25:17'),
('b8305958d6727386469b6a7a7da4806e7cc57a120ad72d16e46bc0244ac2c8c04fc5e1ce318a2d96', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 12:26:27', '2025-09-01 12:26:27', '2026-09-01 12:26:27'),
('b8324a6e9c832610fbcaa3902d5d1d12b6d0b0dba12d8f4d83ce217d5a31423b69dda77d4de3f576', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-22 13:02:42', '2026-01-22 13:02:42', '2027-01-22 13:02:42'),
('b84dd07b7e8d703f01ee2da86bede4e5f930e5e93ac79600f945d99649501ed4c15f4146efc4226c', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 10:22:09', '2025-11-12 10:22:09', '2026-11-12 10:22:09'),
('b87236e5fee61de7fd075d453c6dd967ceb9d22d331d420e98fcc719d5f4bd5b18e05269810a882c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-16 18:59:19', '2025-05-16 18:59:19', '2026-05-16 11:59:19'),
('b874d149a639f696f65db89aae45df754ace665fca001f1055f6fe82aefd42c85409f1d6a4ac6888', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-17 16:09:56', '2026-02-17 16:09:56', '2027-02-17 16:09:56'),
('b8add72cf4730271607ed5e716510af60766a0c771a4453d014300d1e58664c73744fda20295da3e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 16:27:51', '2025-10-31 16:27:51', '2026-10-31 16:27:51'),
('b8aedf973d3c396a939dcec9d435685205915db188d6bb923818311da55bcd48f0ce24d6c6d65774', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:07:33', '2025-09-23 05:07:33', '2026-09-23 05:07:33'),
('b8c96bcc0b8a279451c4b8061baf74d2ed653ad9466fa34672c7cfd0abd0ddad5f5cf98a3290143a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 16:58:24', '2025-09-09 16:58:24', '2026-09-09 16:58:24'),
('b8e25a13a6d1968c58d29b82af290bfb91187254b82d87c4cebd993edb77a387c635aa6a02529434', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-12 13:00:33', '2025-09-12 13:00:33', '2026-09-12 13:00:33'),
('b8f179f91cd4b4b4f7907f4b78650717528723aecb763238993f4da9ba588ff763fb52ec81283c36', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 17:03:53', '2025-11-13 17:03:53', '2026-11-13 17:03:53'),
('b8fd6023ec0d3d7573cc9ef07a6b3cbde27d1ae441d93d1b2751643d80574f4accdcabdaf8be9aec', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-03 12:06:18', '2026-04-03 12:06:18', '2027-04-03 12:06:18'),
('b900bcfc579b0032af637da19f58e8d0b7dd3a6953734cf8262bb771916fc1301b0b98633af382af', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:31:16', '2025-10-31 18:31:16', '2026-10-31 18:31:16'),
('b903fae808697ada24cecbb35af223d62bfc4802c6ea60bd75a0df547bfc173ad72f68f7ac2bf8ff', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 11:08:38', '2025-09-08 11:08:38', '2026-09-08 11:08:38'),
('b91267a9f2ff822a86b8e4775a1772b648e32631069d3f0fec53fd842273aa14f4af7cf56689eed1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-28 18:26:19', '2026-01-28 18:26:19', '2027-01-28 18:26:19'),
('b92a3a2d414ec89d7d353c8cb44a414b0366a99557ba6df7834728d9e861baac187380c71ce75f70', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 16:44:55', '2025-11-13 16:45:53', '2026-11-13 16:44:55'),
('b93c410ae8e70fc59ed0117ff38319de3e15e89222818b6cfeb60e70c853053307675e594af686ca', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-19 10:24:40', '2026-03-19 10:24:40', '2027-03-19 10:24:40'),
('b9bc11a246855577142b84aab5877e38b87c4110fe2d8cb63e8367eac721d5a293e35f58a02f611b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-05 12:06:52', '2026-01-05 12:06:52', '2027-01-05 12:06:52'),
('b9d150c629f93c92c5f278b37729314d3d4d78c8f03fd4de7519e748161315cd704b95d78e21b4f0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-27 18:19:34', '2026-02-27 18:19:34', '2027-02-27 18:19:34'),
('b9d511063479f48405f4df5a503a85d2670bd04d35077fc301a4f2d36943ea8de881a7ebbb82052d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-06 10:33:30', '2025-06-06 10:33:30', '2026-06-06 03:33:30'),
('b9f276bfcea5265d09cdb50f224a1458d064ca7d2379075710343b8887300196864457e7598ef2d9', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:16:12', '2025-11-05 13:16:12', '2026-11-05 13:16:12'),
('b9f8a0626d406325b44f95757311463216d20189e223d2788f8a73b1b5d00dcc87727cd57acb81df', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:14:02', '2025-11-05 13:14:02', '2026-11-05 13:14:02'),
('b9faa8440486de3a266579b41ab60fc7f58bcf9bf55baee9a9330c5618c78c5d891d42ddcfb1afba', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-13 12:18:51', '2025-06-13 12:18:51', '2026-06-13 05:18:51'),
('ba0c980e36163f075909789b4429d1a58bbcb04462729a460caacf98751a90da0c033ce118d024e4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-28 10:17:49', '2025-10-28 10:17:49', '2026-10-28 10:17:49'),
('ba1a632e0cabdc8367f59b9d801c3e479a054f39f001a04eb7f984371312a41d77fb575fbbd2bbf4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 10:08:57', '2025-07-17 10:08:57', '2026-07-17 10:08:57'),
('ba26d31459a86987c5b0f2726c2acea70aaec40d68b46de93e2437244502b8843e8393cfee684734', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-11 17:15:49', '2026-02-11 17:15:49', '2027-02-11 17:15:49'),
('ba2c63d3b1307c9a523e5d5978bcb009712a44359a93d7d39108d8e1d06280cf585759166b0db41b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-04 09:49:15', '2026-05-04 09:49:15', '2027-05-04 09:49:15'),
('ba2e48ffa84d19b2cfceefadb9b328d29cbcf90d8b8716337461b78993b157444c23db244bda2aa8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:36:35', '2025-09-01 09:36:35', '2026-09-01 09:36:35'),
('ba5cfd23955c0fe4096b1cc6278ebcefdc503a17bed147838d5cea14e185322c0fc2661ecf94fe81', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-06 09:38:24', '2026-03-06 09:38:24', '2027-03-06 09:38:24'),
('bad83321dfa6f5554f03b0970fc0181e8137063d47969a89fadd7811e36de00f9ec199ee047eb97d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 10:16:16', '2026-04-06 10:16:16', '2027-04-06 10:16:16'),
('bae7f52f9dd642db7c23ff315fe7ecf0d66b2232ded718e78f76a3d00301ea68c4789c95158a8d80', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:22:35', '2025-11-13 11:22:35', '2026-11-13 11:22:35'),
('baff18f87ce20123d8663d4c09f30bb5412b9891c4975bf16b5534ba456901e8643b557a7ecdc736', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-01 04:22:21', '2025-10-01 04:22:21', '2026-10-01 04:22:21'),
('bb0e62a83ecc43c1bd53a54da4d78ec6b78a57ebb2df0ec34420999f5f5b9201e8c99772f7e61afd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-27 22:52:57', '2026-02-27 22:52:57', '2027-02-27 22:52:57'),
('bb20a3012516f5f01ee1caa161ea7e171d4ff91cf5cc6a863d1dae23955efaa85edbab60699817ab', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-29 06:12:46', '2025-08-29 06:12:46', '2026-08-29 06:12:46'),
('bb3a859b4020a4da5fcf9d1ab218ade9cba614e394061e1ee500c1039b62b19b65a3286a1db6f49f', 268, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 14:42:02', '2025-11-26 14:43:07', '2026-11-26 14:42:02'),
('bb57860421c3e50a86b7dee84a6da305f99dfac31ced3cf74266a8fd0305e33a53f196e796136f35', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-06 05:58:13', '2025-10-06 05:58:13', '2026-10-06 05:58:13'),
('bb7ca318db8b1f0fb6bea0dbe72da6bab20b07547a41173abd7f72fc8d45abe8d281c6fef6de9274', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 14:15:43', '2025-06-11 14:15:43', '2026-06-11 07:15:43'),
('bb9fc3076b8756500fae6baffdc4432b34a6e1f505cee0067325403b10d95ae80326f581bafbc815', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-12 12:30:57', '2025-12-12 12:30:57', '2026-12-12 12:30:57'),
('bc0963b174972688a7ca23d9b993a1564c3f1da66f789e8f02f663fc76610cc04d6a56cf99131b5b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-16 00:02:55', '2026-05-16 00:02:55', '2027-05-16 00:02:55'),
('bc0b19a060dac499cf69df18c6c2e746c266a8be806cd9c92e2c8c381453c185a9b5b2482f1dc57c', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:13:49', '2025-11-12 12:13:49', '2026-11-12 12:13:49'),
('bc27d3b70d4d9052e7779daa4c31fc5890f6a9703cdb7ef08186dc7cee0c93ed4c5b0a29deb9223d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-31 11:31:36', '2025-05-31 11:31:36', '2026-05-31 04:31:36'),
('bcc163155e48138474ce1ac635c8376b584e8fc1cccfeb695ff422a3521a7797e6c89f36faff6976', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-26 04:19:09', '2025-08-26 04:19:09', '2026-08-26 04:19:09'),
('bd0dc6f760af5ff6975a17ffb9689e84781a32b196e06bb4472341212de43599df947bd97c145b60', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-05 19:40:24', '2025-06-05 19:40:24', '2026-06-05 12:40:24'),
('bd4b57072e4bc24b501f4738ee08427f155158895b0afce55e1525b85140b099136fd01b40d30cdc', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 14:59:45', '2025-11-20 14:59:45', '2026-11-20 14:59:45'),
('bd5032ddf44e9d01a9bfd87b001b818602201c88b1e98b0d37252c76c2933c086279a4985c4300a3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-22 17:33:17', '2025-12-22 17:33:17', '2026-12-22 17:33:17'),
('bd7cb7c86a2ad86e14582bf43442dbea689dabe8e3d0876932b4660b2aaf4506197315551f26e9f6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-30 13:09:50', '2025-09-30 13:09:50', '2026-09-30 13:09:50'),
('bd7fac57c9a542ee21029361b3f973b9dd40db1a48be089adb3b79671a8c7d33fe3eab87cff92118', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 10:36:03', '2025-09-19 10:36:03', '2026-09-19 10:36:03'),
('bd956ed2731bf757cf67524eae0a166be751592dd4f2b0c4daffc2f5b8fc2b91f68a1e0bcd8b4e49', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-29 07:05:30', '2025-09-29 07:05:31', '2026-09-29 07:05:30'),
('bdc28310c49f68747645c1810a01fc878d0f530018f8bc8cc2d1663d099d4295df7b70c76f1795ce', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-12 17:10:46', '2026-05-12 17:10:46', '2027-05-12 17:10:46'),
('bdd6eb09e13cdc144c74459dce8115f4c53b5fa2afdbe8ac00d7d4495b6cf4f574c6b3c0ebc1f1a5', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 13:14:10', '2025-11-13 13:14:10', '2026-11-13 13:14:10'),
('bdd73f5b7efa5639c7dce7e831999b51a8b33bc52b0b818f993fbc0fa75e1ef41b330f16cc2e5b71', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:49:46', '2025-10-31 17:49:46', '2026-10-31 17:49:46'),
('bde094c3380d464b7fceac959b1b2b3ac4d5991302bbc6e02c976c5be18da83c6bce2dac302bd529', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-11 17:22:09', '2026-02-11 17:22:09', '2027-02-11 17:22:09'),
('bde98534cefa648c6e15dbd65f97590c2b2877c3a8188d732443ffb742fefd208262fe926da27587', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-20 18:20:21', '2026-01-20 18:20:21', '2027-01-20 18:20:21'),
('be2779bd6d452c404a00ff3709fea4fceee281ffd29e14b4b33fd4695be6897f85ff4f9d5e84ab12', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-21 13:52:41', '2025-11-21 13:52:41', '2026-11-21 13:52:41'),
('be4d8ecbd36cd2c7849388e727a1536d0e45ee2c5aa2901d5dd6a79a04d5bb26f6a790f812db1ff7', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:46:29', '2025-11-12 12:46:29', '2026-11-12 12:46:29'),
('be6cc0e207226b8092ad32024071b228bb19add25540769536a8ec3958bd69ecb50ce3d07f47f1aa', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-09 15:36:49', '2026-03-09 15:36:49', '2027-03-09 15:36:49'),
('beea047eea33139cb00ba49fe4d686e7f2070f5290015ce89f6cccfb3dcabe459b1252d1071c0ff9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-19 12:11:21', '2026-05-19 12:11:21', '2027-05-19 12:11:21'),
('bf0c1312d9c052fdde837f21f65df0af763919ee1756318b1c9a476a9ca25ca50adf34fd94cb6332', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-17 00:39:53', '2026-01-17 00:39:53', '2027-01-17 00:39:53'),
('bf176b0bd894fff7074dee0f0e88bbc3ed52279b1e2ac0cf24f9d07b59e1f12b381c198b006885c9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-06 16:37:08', '2026-01-06 16:37:08', '2027-01-06 16:37:08'),
('bf20f4245f03ca0440d39228cf04036f781d2378665f6f39c28e4165ac2bfe2740e0384244c56e78', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-21 18:01:02', '2026-01-21 18:01:02', '2027-01-21 18:01:02'),
('bf28580cffc9bece082aed4c677efe45ad8fc7c190e0d87c101903fb8631ce99e8b8249af4d64e8a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 10:22:40', '2025-09-03 10:22:40', '2026-09-03 10:22:40'),
('bf3c8369685deba16ea8aa3d92ad7c336a443e35fc34ddeafdc6ca924c0c56deef4495ad76d21414', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 18:57:19', '2025-11-26 18:57:19', '2026-11-26 18:57:19'),
('bf737bbc60049620c87f40f67fd6eb919515831e13450d9cf83ccf315313138bc00b3e05c1cf508e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 18:10:30', '2026-02-10 18:10:30', '2027-02-10 18:10:30'),
('bf8dd6aff1b13e9fe425333f91a3c9e2ee25b98f182f9428dcf6524e2a6dba3c270ff7083a38983f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-26 04:31:49', '2025-09-26 04:31:49', '2026-09-26 04:31:49'),
('bfaa5202714cf909fda064cc1de1041dcf014faf8d95d812ef8e3fe534d4e272142f3da9b4179b41', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 13:26:03', '2025-11-06 13:26:03', '2026-11-06 13:26:03'),
('bfac0d7487861af7fb397a4d6ad94361e44883c4f114f1b4e9f9ba194e88dee09bf50d7c3d23c74b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-24 14:40:48', '2026-01-24 14:40:48', '2027-01-24 14:40:48'),
('c021c55030b9841c74c137c46f1a9990dcdef6bdf058b619bc11e925f82cae39b76e24fc839a7068', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 08:23:15', '2025-09-01 08:23:15', '2026-09-01 08:23:15'),
('c03015dcbf9ef2e284aea00a07ff63d5fbc6f954953592bf9465e3a5701633c701ed7a078374f4a5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 11:57:30', '2025-09-01 11:57:30', '2026-09-01 11:57:30'),
('c03fb58d3cea0260475e6e3e1fa79e45da3230d088fb5e7a4c97e3d86bf6b317ad1ed559cc27a9d5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-17 18:00:06', '2026-04-17 18:00:06', '2027-04-17 18:00:06'),
('c044ad65253a2851740b2f8cf050a61aeb3228dd80a620528e9b63313f24c0def05aeb4a0ad40642', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-25 11:03:14', '2025-12-25 11:03:14', '2026-12-25 11:03:14'),
('c0539370bb5a5a0898f6cf8cc084ec38604ec7a3b7d9d7fc9b9307c15872568e0a4fc61a17cb8ade', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-10 04:39:37', '2025-09-10 04:39:37', '2026-09-10 04:39:37'),
('c06bb93253fadf7904b5a2a41aefb7f12a2e4fd12e2d930b0a1d9480b2e8162d7e354edc43b6f847', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-16 12:53:11', '2026-04-16 12:53:11', '2027-04-16 12:53:11'),
('c07c20d4ab518470f34b1bb7764967f2b72b3e2179a0323a339663e2f2f6dc3dd0103ecfc94e9060', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-27 10:16:01', '2025-11-28 11:09:41', '2026-11-27 10:16:01'),
('c0839f1c5c0e25451785ab64f8b150ef77d6bb577b2a80aa6dcedaaa417a0356fd5a90fe38dc5f22', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-02 15:47:25', '2026-02-02 15:47:25', '2027-02-02 15:47:25'),
('c0b6d882cdd72e3bfc9c307d7b8d173fd7a98e3cd0bf0fcec4877c96a16c633723b09651a7a00e8b', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 14:48:40', '2025-11-13 14:48:40', '2026-11-13 14:48:40'),
('c1061d4d17c7c1e8a9b87dfa47573e90b1f88e0f5d04b1dfddd9ec9d15deaad9b20c6ef1790c3796', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-23 15:58:15', '2026-02-23 15:58:15', '2027-02-23 15:58:15'),
('c123341d727700c04792cfac9b988937032ba1e743c77aab3a4a57384e879bbd0f2e1d16d640921e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 10:43:44', '2025-11-13 10:43:44', '2026-11-13 10:43:44'),
('c15f783b58c567628c7ee03a16642741118a0961fa33236c4c1b7ac4b28d8e13c38aeb9bcf455eb0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-29 11:08:54', '2025-12-29 11:08:54', '2026-12-29 11:08:54'),
('c16de243dc8df61a6da0f11a0a98a564904320ac047e7116e22e9d157a1e1d7c207e801d25811e86', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-31 13:53:23', '2025-05-31 13:53:23', '2026-05-31 06:53:23'),
('c1d14ad48e373359c3c9d427d0459e33d5df8b8d025a5283b385236a1dff922d3a681cc43027fe13', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 06:09:25', '2025-10-30 06:09:25', '2026-10-30 06:09:25'),
('c20108c352d1772effde61841c19c80fbdb60e1209e2a2851c07f3522dcf26023efe012b4931ffe7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:16:41', '2025-10-31 18:16:41', '2026-10-31 18:16:41'),
('c21d6704be08a0179bd56cb8fd7f8591f0876a417feb60dbedaffe29d9c1036e809959a59bafb505', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-10 10:33:13', '2026-01-10 10:33:13', '2027-01-10 10:33:13'),
('c227deb400017dbfbbc6001aaa3109325f20fb6d22001350b8635f4736792faa778980d86d99c388', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-18 16:18:32', '2026-03-18 16:18:32', '2027-03-18 16:18:32'),
('c24c393aed2fe628d4f86749fe99ebfbffc86c7ee80a1f41cf111463ec4a93c806a38f9eaeab27e9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:22:20', '2025-09-23 05:22:20', '2026-09-23 05:22:20'),
('c26ffcd6b847094f1c911c5e2353dbf9e4f1543de33111aac983e09baf39951645d8e972b218d2a8', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 12:07:48', '2025-09-19 12:07:48', '2026-09-19 12:07:48'),
('c2727af5890b7c3ae6e6c57ca08e0a3c01f5a1787df69d1196a7f53315822f9e0cedd713cdc71e92', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 10:08:12', '2025-11-04 10:08:12', '2026-11-04 10:08:12'),
('c2a306668fa8b8f16013f9a525b06148f254c7c09b82fef5e1694d2e3782a4f752a914ef071b2f18', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-05 13:18:10', '2025-11-05 13:18:21', '2026-11-05 13:18:10'),
('c2ce6b4737e341eec30b96ce33b10cba5683ecc774a7a390206695db711951785acd1726c28221f6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:21:24', '2025-09-23 05:21:24', '2026-09-23 05:21:24'),
('c2d80a601ae7028f45cf7aad054415505315402b8b8b749c07e7922cbec4817f5749d3cfffe08edc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-01 10:32:13', '2026-01-01 10:32:13', '2027-01-01 10:32:13'),
('c336a7649152390bc4028f2f47b205e80716acdd600e72f4705ce88d47076ab2531f95d8116846ec', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-15 09:12:18', '2025-10-15 09:12:18', '2026-10-15 09:12:18'),
('c33cf6717224a7c52455e4cdd68764c7a1fbc00720a048c4dc30a50bd8da3af3bd7f693909b775fe', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 16:31:26', '2025-10-31 16:31:26', '2026-10-31 16:31:26'),
('c346fe6a3a4f70811e80e2f4ed4c87cb585d67358cce9a6975aa57d62de2c596508e483511c04f61', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-27 10:23:11', '2025-08-27 10:23:11', '2026-08-27 10:23:11'),
('c3da9f421b0242b36c3eef0266cfca3c0448536b1f6f4a9186dd7a8b2f60053260c759281daf83ee', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-05 16:42:36', '2026-03-05 16:42:36', '2027-03-05 16:42:36'),
('c4274feeb200d17b432879760050dfc0a1111d8c298acc5bcdc9a10712577239dff8760afbd8e327', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-22 10:00:24', '2026-04-22 10:00:24', '2027-04-22 10:00:24'),
('c4518554791ddc5f1ba259abe4e6725fde11518b388a9a113a0d5d57e06a0ce744322ebf87b05fff', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-09 11:17:49', '2025-06-09 11:17:49', '2026-06-09 04:17:49'),
('c45f4ee142fdfc024c31f27c33163a33a783f8ac56914e3d12de15b25abd8a8c5d47335a6d4f2230', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 11:23:24', '2026-03-02 11:23:24', '2027-03-02 11:23:24'),
('c4a124f21a126e4004ef17b73bf4a7126b7fa3c1bff68421d36406b6b93cdb8bcc3ef76ae94a3c48', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-18 13:41:38', '2026-03-18 13:41:38', '2027-03-18 13:41:38'),
('c4b896a1f2889ce9c649e7e94c7fd86985a63360cad8624e8629556821a2d770d2bbbc181327f5f3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-31 11:07:54', '2025-05-31 11:07:54', '2026-05-31 04:07:54'),
('c4ede48613cda3ee393a72e2d7cec09fd26ca5a0258b2aec3c4d78edfc2d3191d4b1f0dc45291f1f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 11:28:23', '2025-11-05 11:28:23', '2026-11-05 11:28:23'),
('c4edfa46f73c09b5ecbc4243f16b203e63ed7995748cc22b689ab8fd6763e08537536d8c59a0ba1c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 10:48:03', '2025-11-04 10:48:03', '2026-11-04 10:48:03'),
('c4ef778b3af4be8469935dc09c220e8c7dcb23632a9097886db1594da53560c8d7e737586a892be2', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-26 09:40:56', '2026-03-26 09:40:56', '2027-03-26 09:40:56'),
('c50500b144eacc9c47f8fd7f5289903a9ee135a7a67808512f058cb68edbcf13239d6570e42ad524', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-27 05:06:49', '2025-08-27 05:06:49', '2026-08-27 05:06:49'),
('c50cee48896b22d57f3a14c2f38bebd9b0b4b47c1d020c3d515eede5a3e160a497ee7b5a658eef89', 27, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 06:14:14', '2025-09-17 06:14:14', '2026-09-17 06:14:14'),
('c52b7a28d6324aad80984cf9a522b4f8d3ab56db50a01709fafcb38c0fdf09301429024591e3198c', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 14:44:19', '2025-11-12 14:44:19', '2026-11-12 14:44:19'),
('c538dc8c7d25b348f0063cc2d1dd3afe54bbd195de9b94592837739c496220e1d5797a4d278f1bf3', 297, 1, 'LaravelPassportToken', '[]', 0, '2025-12-12 13:02:10', '2025-12-12 13:02:10', '2026-12-12 13:02:10'),
('c53b9fa2742e62acb3b66842a320b221cc08d20b1a6c3bab06643a23c8f8616c3dc08bf6599d4650', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-12 11:18:58', '2025-11-13 10:48:48', '2026-11-12 11:18:58'),
('c56bcdf7ade5a1e9c1f2456885915e47d4e5d7a6abca10304a55925290e1df32949d8a84f3b19f0b', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 12:00:48', '2025-12-01 12:00:48', '2026-12-01 12:00:48'),
('c577c17387c0da3b932ee09cf976f8a92aa03b5177733741d6de6759108622730a4937df750a8196', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 05:04:23', '2025-10-31 05:04:23', '2026-10-31 05:04:23'),
('c588f966c87d5b3ffa67d6ada61b2f147f46a7e5b147918467eda2d00d4d143d97e23d48152a065b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 10:48:24', '2026-03-02 10:48:24', '2027-03-02 10:48:24'),
('c59ee0b2806c8d0163439a7e4d128ddcfe63c30db74c1a76f4756c2ce63e93ea3b2735e8b2481ec6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-26 13:08:15', '2026-01-26 13:08:15', '2027-01-26 13:08:15'),
('c5afbe878ea7f9ca403bbfc29db76b3a61a25e71e4346d488ceacf11085a205fd1b5431697d8a899', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 09:14:09', '2025-09-19 09:14:09', '2026-09-19 09:14:09'),
('c5cd65bc85527992a4bfb7b146b460248ea47a9e958a22d4c59c240da16cf0abde95916b31e1918f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 13:17:42', '2025-11-03 13:17:42', '2026-11-03 13:17:42'),
('c5d43fb0975a7ade9ced26ffbeb09529776ab43caa2cfe31f45a7abe94729f6fc7ff30f24b08dfb3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-12 05:57:23', '2025-09-12 05:57:23', '2026-09-12 05:57:23'),
('c606966600fb56c490451214da71460c8ea45b28a0e019b1390a375c81a47efadfc2a84dc50fb6fe', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-12 17:09:07', '2026-05-12 17:09:07', '2027-05-12 17:09:07'),
('c616d68c8595d5179d243014e9a805173bbbe1cc677bd9e948dbf2feb85af58b14a07f3e2a9c207e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-12 22:39:53', '2025-05-12 22:39:53', '2026-05-13 04:09:53'),
('c61ceeb4db3012635188e59c9e7f04a40e18e8cfbc6e318b8578151dd651bd4cee53eeaedb46c9fc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-24 16:11:08', '2026-04-24 16:11:08', '2027-04-24 16:11:08'),
('c61f2c0f5140973c8f679ab88f02351d8a6af09fba16781b2dff57f0702f803735f3037c119db448', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-17 14:43:27', '2026-03-17 14:43:27', '2027-03-17 14:43:27'),
('c64198fdcf42907ff77321c0890ab1b9d67ddbd0b9bc65267fdaccbd246f737710a40eaa1824188e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-20 06:21:18', '2025-08-20 06:21:18', '2026-08-20 06:21:18'),
('c6558fb926436cf84cef228cb9d54fd2cfaafacb762a64afdafddd22c0bbf399bb09764b64079eb7', 27, 1, 'LaravelPassportToken', '[]', 0, '2025-09-16 10:19:58', '2025-09-16 10:19:58', '2026-09-16 10:19:58'),
('c6652dd5cefef6d340f2cd4174dad92fc87e20711c4ec9d506cfd21ee6c3834253af283551b68884', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:11:38', '2025-11-03 12:11:38', '2026-11-03 12:11:38'),
('c69703cc0cb259075ee534d2331b161272a30ba10fc65cfe73628f239941de19b0a613a015fc4d80', 268, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 14:41:59', '2025-11-26 14:41:59', '2026-11-26 14:41:59'),
('c6b680da447d5479d1449e3cddf65716f4ac28ec3fd42428ecb1442f00d59d06c074d201b65c44fc', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 11:02:30', '2025-09-25 11:02:30', '2026-09-25 11:02:30'),
('c6bbdc24e0654ccea33f4020d5b07e17a007998a89878adfbf31e9b540767a067256ef9dcfdf9077', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 10:13:53', '2026-01-29 10:13:53', '2027-01-29 10:13:53'),
('c6d10e32b1ccd02645f3716f33be34f8ae4ae403416b512a4de8e11be40516f04bd041b55f73fdf6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-27 05:13:44', '2025-09-27 05:13:44', '2026-09-27 05:13:44'),
('c6f1b9539744d1e17afe52f51a55f0f3e73cfea2a6f7fd52de60ffaf8b79c8017567b7e9ca0e7f51', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-19 11:01:28', '2026-01-19 11:01:28', '2027-01-19 11:01:28'),
('c6f32de55d04c8467fad9f7c339485595448f48fcd3bdad733bc5b3c655b8add861ad6e55012930b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 17:32:56', '2025-09-05 17:32:56', '2026-09-05 17:32:56'),
('c7057359fe47c974e20ed1de67560d5c630ccd6332a05cb32956bfaaae05140a018287c0fc00f2b6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 12:11:03', '2026-03-30 12:11:03', '2027-03-30 12:11:03'),
('c73c143e4f2043459d17e7182c9d4068b2a27b794ec5bc94c5f69f57e95f605a53289d6d795f4e3d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:29:25', '2025-09-01 10:29:25', '2026-09-01 10:29:25'),
('c746de543165b030f6c83654d9a83152e033d536a94fff1634459c00c4f28aecda3dbb7cb62b3cae', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-14 11:13:23', '2025-06-14 11:13:23', '2026-06-14 04:13:23'),
('c747b89e73694f23bce11a8a304697064d65bb09b28bb263c7665290756ad5da3f27041236a2ff19', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-29 09:44:39', '2025-10-29 09:44:39', '2026-10-29 09:44:39'),
('c75130a10c1fdf0b381cb5920d7f5c4920a43356cdd0ca1d80616cabe1e31788dc85dbff96f3223a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-03 14:35:33', '2026-03-03 14:35:33', '2027-03-03 14:35:33'),
('c767fa15d35bd4e9233a4324e8c13b64a34aff72caa6bcfbf37a1129808b39c20271d6d32f0afb2f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:37:18', '2025-09-01 09:37:18', '2026-09-01 09:37:18'),
('c77e1fa7c822d71f8aa7fdcb2ceaa2d03ec61500b5d7f1e7661de366439123dbb00e5d265c435ee0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 10:06:11', '2026-05-26 10:06:11', '2027-05-26 10:06:11'),
('c78d70eccf5c5c0c24815ae62cfca25c895f09dbf77ffe97448855469fc956d73ea748383b016d3c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 10:01:57', '2025-09-23 10:01:57', '2026-09-23 10:01:57'),
('c80b84e77a9e4acaa843da88ba5924e9c919cb0f0805ea8fcceab6d58f7083d2c3a7318f2b72c591', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:55:03', '2025-10-31 17:55:03', '2026-10-31 17:55:03'),
('c82e7f1ad7433566e308742ec56ab8337dbf56ae7eecf093c7db81254c904df29edb5c39f92f113f', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 13:12:35', '2025-11-13 13:12:35', '2026-11-13 13:12:35'),
('c82fcd65b7db1942919b0230138f8ebe90f21237617880f5b09ceb425d67ce60003f181a4af9ff5e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 10:26:00', '2025-11-12 10:26:00', '2026-11-12 10:26:00'),
('c83066e545e77cd3ba0dfca2719c6013344bd10f9f882d921e36e729aefd7e3ca75ddc2c4e830de7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-25 14:47:25', '2026-02-25 14:47:25', '2027-02-25 14:47:25'),
('c83137b2498a6cbaa8975ed080f3ef2a963b3496eb4d0cb0eba18ae92782c87cc84df511b6255915', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:54:11', '2025-11-06 10:54:11', '2026-11-06 10:54:11'),
('c85db4cfc135ddd8c57fa387758a9857feccc3c31be0af2515c5ed6aa960a7f20164f44b3a3d5d73', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-10 07:23:53', '2025-10-10 07:23:53', '2026-10-10 07:23:53'),
('c876ee29b316802d5c3dd9af52913c4bf50d55e20b4f70d0b5162aa808cf6bb1ec359903eee75bc3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-27 16:17:11', '2026-03-27 16:17:11', '2027-03-27 16:17:11'),
('c8f3921a2de4540bad258304cf87c1d9208f008a217567c06cc934eac3dbae3030bf40980618ea9c', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:09:34', '2025-11-12 12:09:34', '2026-11-12 12:09:34'),
('c909440610af87a6b9ffc9902b375d5f217096a75ecbdab9ddf8b6a9fbd7273080a505d727ff0e9a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-10 12:32:40', '2026-03-10 12:32:40', '2027-03-10 12:32:40'),
('c90ced429c95f99eb5b216fccb5c5d56e9ffe50c8720ad8cbdceb377515da34a54f29064830eb55a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 04:32:20', '2025-10-31 04:32:20', '2026-10-31 04:32:20'),
('c92ad1c5e5340656e8682c6ce8dbcc74f9d828ca18e6667e28a8a7b9a1a442a28491a5d960aecc03', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 15:02:12', '2025-11-26 15:08:08', '2026-11-26 15:02:12'),
('c93ea2240637dc9b85432234e2b67972e993d8080a3d929a397f8bcbfcf8a00a3bd75f044ede9a93', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-09 18:12:06', '2026-02-09 18:12:06', '2027-02-09 18:12:06'),
('c9721e305631fa34bc7f4a65d2303d85df2b39698f95e68ae5f5542a8f21462f1d8af5848297359a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-27 13:13:09', '2026-03-27 13:13:09', '2027-03-27 13:13:09'),
('c97cfaf2c5ae0b180209d977edba34ba800000763811e244d4f0572249a7d36f2e4180e243616d90', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 12:33:53', '2025-07-16 12:33:53', '2026-07-16 12:33:53'),
('c980c6c88aab5d1e444e2cce88f007435d58746e79ab9d33e37211d34dc6b1e7145edd6d311fb867', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:07:25', '2025-11-13 12:07:25', '2026-11-13 12:07:25'),
('c990e1a191024b4042ca24ccc091435a8a3cada4f4ffa96e046c19eb14f296ab74c615768f1f879c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 14:50:53', '2025-11-20 14:50:53', '2026-11-20 14:50:53'),
('c9a02486e6857fc52d1e962dd1d4f36ca101504c7061888fb6caebc92ec84e8183389d968b1e9068', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-02 12:52:08', '2026-01-02 12:52:08', '2027-01-02 12:52:08'),
('c9c9ebaae966ead5aaab54170865f8266453fc994a283ef92c385898f46ea484e3f9920bc60d0615', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-17 15:03:57', '2025-10-17 15:03:57', '2026-10-17 15:03:57'),
('c9d3cedd9740bfeca833fb7d828600d8075fad0355fa26db5c97e57028db521bcb677cb1794ca3bd', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 14:42:51', '2025-11-12 14:42:51', '2026-11-12 14:42:51'),
('c9d649428891b843cedaf52a577179d8633a4c1ae970a91c2d38c40366fbadfe9c62a3972c425a8e', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 13:14:51', '2025-11-13 13:14:51', '2026-11-13 13:14:51'),
('ca1565555ff350273f0552317a64e8162ade47ec5b4c81304b3461d5d29dd67bfd902949547e5ff0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 06:27:06', '2025-09-08 06:27:06', '2026-09-08 06:27:06'),
('ca2ffb28da3143a2a7a10c23a32c214f476526d7c6499f8f825ef6b39ad1a1bbabb73d43ac48ca4d', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:34:37', '2025-11-12 12:34:37', '2026-11-12 12:34:37'),
('ca398ca0a609f955af9afd7bdc6bdc5e0a84be736a6cd9acf6d38a3ca25fe5c1dd9a3a273d338099', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-09 00:47:44', '2026-01-09 00:47:44', '2027-01-09 00:47:44'),
('ca40a389c703b2b4ed6a7bb0660b3d69799b4db6f556c59722046582329325d84921be403101fd16', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:58:31', '2025-11-06 10:58:31', '2026-11-06 10:58:31'),
('ca530c20439b5c63cbf57d879fef76251154c4360d247a732bd5baa082bacebc1618587edde6806e', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 16:04:04', '2025-11-13 16:04:04', '2026-11-13 16:04:04'),
('ca64229f38bddab9f3eef8c7d044418cce33f74ea1132ab0fae726443be376c7b09d30da7ca0cf35', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-23 12:26:40', '2026-01-23 12:26:41', '2027-01-23 12:26:40'),
('ca8f87ada0986098eb32628d55addd1c2ce3452816b6681afa8bf5b7c4a359877487a09745f35126', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 10:01:48', '2025-10-03 10:01:48', '2026-10-03 10:01:48'),
('cac77a32ba0b056a117bc6f2ccfaa06a45e538b5c9bab0d706a1c115ef1596c656bae62d2fa33d90', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-13 13:19:58', '2025-12-13 13:19:58', '2026-12-13 13:19:58'),
('caddd43637f4f3aee601747a550916bf3f8ec38e481a3de683ee87f41815a40f46b17da7163bad27', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-02 10:25:23', '2026-01-02 10:25:23', '2027-01-02 10:25:23'),
('cae5cd83be3e71559ffc9ce60306eb262d725f06dc84d00202ebd40e945717c51aeaf9eb510ec44c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 17:43:00', '2025-11-07 17:43:00', '2026-11-07 17:43:00'),
('cb2a2474fae3a55da9047defecfc95b6e9b230fa82e6c487658d4809779fb424c73066a4aa666145', 348, 1, 'LaravelPassportToken', '[]', 0, '2026-04-01 10:14:21', '2026-04-01 10:14:21', '2027-04-01 10:14:21'),
('cb46ad43bddaf771c16dca718b02b30640ba887af520c7318537ca6f3d37d803de79a5074ac3f0e2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 06:17:17', '2025-10-31 06:17:17', '2026-10-31 06:17:17'),
('cb60114bc1952bceba4aa005a7719ba8dfac10cbb76150ff45ec3660fc0414b2cf046bdfcb6bc2cf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 17:56:59', '2026-03-24 17:56:59', '2027-03-24 17:56:59'),
('cbbbc52c6b970885638eac37b8b30e8d78f14990875b4433b6af1f40655bebd184ac30b3b2b1f6a0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-03 11:24:29', '2026-04-03 11:24:29', '2027-04-03 11:24:29'),
('cbd7dc391a818689125f24f1399c5661873b25f9fff8ebb3b8cafc6ffee1b64ba8cdf06cf63d9f2d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 15:32:40', '2026-04-06 15:32:40', '2027-04-06 15:32:40'),
('cc2f36baaf1702ac7a193d58ddcfb9c11b6db50b3b54ea1a70a246e93eb20b97391544ff0029a1e6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 10:44:42', '2025-11-05 10:44:42', '2026-11-05 10:44:42'),
('cc57d7ae37f33c76c83f525ad1ab93e6679647634bd19af30c55fbf7c2829044a6d295367b77b4c7', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-12 14:38:42', '2025-11-12 14:42:36', '2026-11-12 14:38:42'),
('cc597a2531120a86efb67c454efb1aac9d30cc794d24951d1e98eeda8657b98672b7ec3027299fd5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-07 17:40:33', '2026-05-07 17:40:33', '2027-05-07 17:40:33'),
('cc6876d30383accc458b50b678340a962ca6cd54638a1c9ad49d9415d6c288e35e3d9bee45e230fa', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-19 08:20:05', '2025-08-19 08:20:05', '2026-08-19 08:20:05'),
('cc77ffc13c75fbe2afb924e6ff0080f43bec01e513f23f6cd70ba444c6f8f61f153cd2627caa1898', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-21 14:01:39', '2025-05-21 14:01:39', '2026-05-21 07:01:39'),
('ccef76c9d30cb25168990067a3d726c2756dd66187a24d6e8e3d0d8f02c460f8a68cdb87fdd48772', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 06:30:40', '2025-09-17 06:30:40', '2026-09-17 06:30:40'),
('cd0d77f818481e5c8e0586121fc769dedebffa6a2a83bcfe8fa934687f879837cf1d3765a99007d3', 22, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 12:02:41', '2025-07-16 12:02:41', '2026-07-16 12:02:41'),
('cd35e85e670680a24dc207f4952fb92b65c6a916eaec8442a46320094b7ac4075724cbab44b306c7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-01 13:45:44', '2026-01-01 13:45:44', '2027-01-01 13:45:44'),
('cd4dccb196198751e54acb2811bfe3004287db675f7945e73dbb62345dab611bd70ecf90b22e5ad4', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 06:27:12', '2025-09-25 06:27:12', '2026-09-25 06:27:12'),
('cd696db4355bf5768a809c08dce51e95c74772a9746ca383871c296c30b8e36bfe6a7c0dda0354b4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-06 16:16:00', '2026-01-06 16:16:00', '2027-01-06 16:16:00'),
('cd6a327941b35627a35c66799246a9412a6eb06cd12480c077cb1eb96573449248a4e952f9512d0b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 12:14:25', '2025-11-11 12:14:25', '2026-11-11 12:14:25'),
('cd7884b08aa51305bfaefcef770426b7d2cb0aa798e4cbc47a9086a25756e3ebc71674c6d43aa516', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:21:55', '2025-11-13 11:21:55', '2026-11-13 11:21:55'),
('cda94f4f90e706677882525fa7c4bc8587611f3820b4ef379a27710a15888d993490201aa7355c1f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 07:18:21', '2025-09-05 07:18:21', '2026-09-05 07:18:21'),
('cdfb810eea25822cdde8feb0b7235185d1a1bb869423e539615a5067002b0c76a9071f14143d40f3', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:07:39', '2025-11-13 11:07:39', '2026-11-13 11:07:39'),
('ce5122849d34385ec4897c1d9583d387eb6560eca2c1656fa10f3d896ab69b78b11d968fdd3b52cb', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:46:54', '2025-11-11 13:46:54', '2026-11-11 13:46:54'),
('ce622fcbb8844dffb05ae87923af53eda40e27fd57432807502691bc8ad2136c067d573e97ebf9f1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 07:08:29', '2025-09-01 07:08:29', '2026-09-01 07:08:29'),
('ce794249a6e44aff5c483a0ade4d91b4c98398509821a8c9fe803c3ac86925956e590a2e77a505a4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-18 11:42:18', '2025-08-18 11:42:18', '2026-08-18 11:42:18'),
('ce8d7c7a0c131a7cd7b95112c7cffc23aa9cc7f9d5e070f9cca4fe447b1495a10b24c03279c8bbac', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-11 09:42:58', '2026-03-11 09:42:58', '2027-03-11 09:42:58'),
('cea66a9b9e00c0fa85e47c1a9a41ea173f82fb027a962ee28687d0398e0bee40c0652c9f0d1b790b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 10:34:41', '2025-09-09 10:34:41', '2026-09-09 10:34:41'),
('ced0d8db695effcf21e2dd61730e852aa132ec061f71acf5def989ba587f10d51aaa4a52a582c203', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 12:09:48', '2025-06-16 12:09:48', '2026-06-16 05:09:48'),
('ced95db4558b68eda30f51b5cf33130be1a0bfc1e3e96a4c1892ffdc31f2857d8950e1d243098760', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 06:46:31', '2025-09-17 06:46:31', '2026-09-17 06:46:31'),
('cf0ce3a1bc0586c297f73a8037105d2c2f1c05bf80c51753cfb924b3b2d9563d515d42d31a64b087', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-05 15:28:10', '2025-06-05 15:28:10', '2026-06-05 08:28:10'),
('cf4ba23ca54fb25ae64796d506a9b6faac39a44aa472e6b39d66e9a9df6c72a253cefd62a7a9a6df', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:07:19', '2025-11-13 12:07:19', '2026-11-13 12:07:19'),
('cf54b7d3fd271376ce8cb297b1f98c8756f87c7ade5c9c586e0a98848d2f93315127195a913abb7b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-10 10:22:06', '2025-11-10 10:22:06', '2026-11-10 10:22:06'),
('cf5dcb4be0214aaaac55d7b7ba790ef4af2008f521d90240aa48907f9a6d30dc4259ef515b09e4ae', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-03 16:56:46', '2026-04-03 16:56:46', '2027-04-03 16:56:46'),
('cf5f9e10c19165ce9f339aa6f73e08d0c35407dd49f5afce6f70bfe06c3b29320a52c04cb7028643', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:40:25', '2025-11-13 11:40:25', '2026-11-13 11:40:25'),
('cf6c65b123e03791370d0a673797e9428c149e1a8cac528a8aa07cd82b554f56c0747a50abcbd237', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-10 15:26:11', '2026-03-10 15:26:11', '2027-03-10 15:26:11'),
('cf70970180f913c36b36718b370729e5dd87c65196b786d70d8ebc1de883151fc554c6f10aecf6a5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:26:07', '2025-10-31 18:26:07', '2026-10-31 18:26:07'),
('cf772061830ff9090ef4dfc319b11a46456cfeef46a156b9818d6617f43c7dda4351478e1f6af3f3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-25 13:31:06', '2025-11-25 13:31:06', '2026-11-25 13:31:06'),
('cf88e9f2311ee359018379318cbce8b1e1c0f47688c7b7dfcd19a969155fdb6bb250362999ea64e8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 04:41:58', '2025-09-22 04:41:58', '2026-09-22 04:41:58'),
('cf90b415799080ccca12f85d26d45883f0892dee8eeab0e43a3ed118dd634ded655f7735493cc5e8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-17 16:28:21', '2026-02-17 16:28:21', '2027-02-17 16:28:21'),
('cfcc7a8c25bf4a9008f5bc8ea8d5e4ec698ddb6eec22a7109bff4d98a140df7d6c55f7f19f547803', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:09:45', '2025-11-03 12:09:45', '2026-11-03 12:09:45'),
('cfde06e287f41d41fd8c8cfaf1213157bd5a02be12230197b054eebd7e85524e07538fac03811b94', 331, 1, 'LaravelPassportToken', '[]', 0, '2026-03-17 14:50:05', '2026-03-17 14:50:05', '2027-03-17 14:50:05'),
('cfe0b9347807479d40b5b35f61be8f7d4c0214963d1a75a7d36076ab5a23f17b944cc64aedcf7689', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-19 13:44:54', '2026-02-19 13:44:54', '2027-02-19 13:44:54'),
('d003e69a267601ede345569e1650fb9527074dfc95024fcca6a01b9513f45c9e1e931f2b089d2a0f', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:21:20', '2025-11-05 13:21:20', '2026-11-05 13:21:20'),
('d004d16d5353615de75c298712df33a271656acd5f4190b07e0310f855e236f776a9a66bd23c2d63', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-07 12:54:57', '2025-07-07 12:54:57', '2026-07-07 12:54:57'),
('d0120033d553f3fc9ad1ef4ab74f3d63c5cde626ade8eb0b05e2c25a5e2e0a2ba3e0bd57570ecd61', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-13 08:51:35', '2025-09-13 08:51:35', '2026-09-13 08:51:35'),
('d012111fc327f15c1b011d50eb9209906b12aebaddfd115a3f22a62cc4bd893db3f2e027dfe225ed', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-03 18:31:56', '2025-06-03 18:31:56', '2026-06-03 11:31:56'),
('d01559335deec5ee700f525e978e17c68d44164c422561249b9ba6eef5f43b071c63c9f4b84bdb9a', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 11:56:18', '2025-09-22 11:56:18', '2026-09-22 11:56:18'),
('d02d5f371c78f62476d015ac806edc6ec3457a31ad4cd2352d941e19bd5492f8557f78b9ad6b1aef', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 17:02:42', '2026-03-31 17:02:42', '2027-03-31 17:02:42'),
('d02dbbe918202ca9be44e4035ae48912d82339200f24ea8ab8e2106b85dc6295e8a440880ffdf6e1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-03 15:35:11', '2026-04-03 15:35:11', '2027-04-03 15:35:11'),
('d03647e36b99355c546e1b7ee8bab6b3a973b5d4edbf0cc0fd662c2283bbab274fa31913cb08ff91', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-08 17:11:38', '2026-01-08 17:11:38', '2027-01-08 17:11:38'),
('d064095353f0e047072581df5f18b9d0a8ae1948d6d44235965bffea83f493ab25bf25f531f46ce8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:34:41', '2025-09-01 09:34:41', '2026-09-01 09:34:41'),
('d07233583c0fa229a959879c09b095d6cbdba645ce8e3a91b2a7ae768e719a3314f8e67c796d2c27', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-04 10:18:30', '2026-05-04 10:18:30', '2027-05-04 10:18:30'),
('d0e278fbcae4f2437063a373e355638d8f0a549b195ae9d568117ddca5fbf50a8ba38834f79b378b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-14 10:39:43', '2025-07-14 10:39:43', '2026-07-14 10:39:43'),
('d12d044ff010f319689d833244f41bc2c4f749d4308e2b000845e284c640225a745b16edb5568a12', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-19 10:43:10', '2025-08-19 10:43:10', '2026-08-19 10:43:10'),
('d159b3c0113b0e3e7d7fbd44b209d0609e5775fe4bec1324d6b900c6338129e9c73d019d1a3ab755', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-25 14:50:44', '2025-12-25 14:50:44', '2026-12-25 14:50:44'),
('d17fb37e75a579bc14cf4f95bd051916316697bb5a300a8d47e079c72f5419f1d7698d0f81c46a90', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:43:50', '2025-10-31 18:43:50', '2026-10-31 18:43:50'),
('d1827defdfaca0ba80246e97ed6d5669c253525b67050f42dc77518d6a714df4598574013f8b410f', 210, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 14:37:35', '2025-11-07 14:37:35', '2026-11-07 14:37:35'),
('d1929ed3e6bbace0f17a59078a49f7e6e82c31297a3d635d9aeae0e1d9fd0162917577199b627b15', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-28 05:14:32', '2025-10-28 05:14:32', '2026-10-28 05:14:32'),
('d19ea6b2241abab8efd2bd9ca78c7f60011d167d0e3fdd9f6b38e6302ec521759aef5f948cf802d3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-29 12:33:58', '2025-07-29 12:33:58', '2026-07-29 12:33:58');
INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('d1cabcd224cfe83b0137e899cc9677ca6ecdb1d1d23e9555e6b48d67887a280a38de6b17b69a3af3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:56:22', '2025-07-17 05:56:22', '2026-07-17 05:56:22'),
('d1dbb1cbf9cbe0d5a896658ce860c1131476c75e03e4deafbc742f950725d8c151513664cee4de70', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-21 15:47:16', '2026-05-21 15:47:16', '2027-05-21 15:47:16'),
('d1e805d7b0fb2639b7b04b73ef50c383fd568aa8f0cf428384afbd0f9fd2e955850f590753be748a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-05 18:07:32', '2026-05-05 18:07:32', '2027-05-05 18:07:32'),
('d21a8fb829f6b0f2adb067cfc7fed1d769a4c2a7193ebee08d7a72a1982403f8f0c51116d5930ef7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 14:45:53', '2025-11-20 14:45:53', '2026-11-20 14:45:53'),
('d23050fe49afab573defe912b08c9934d0b1c5bb8166018b74a5295975a5c6636dd8b625262f1994', 179, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:13:30', '2025-11-11 13:13:30', '2026-11-11 13:13:30'),
('d25c59f0d43c3211ecc67eaceabbc3bee28867f00251910c9cd0f7fcb4fb3360a4ad6a9d5eb79c58', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:54:27', '2025-11-06 10:54:27', '2026-11-06 10:54:27'),
('d268b339cb6a2ae0bbfd9babeaa52c1c3ab79d16d73d49192499ca85052f87730fcf41bb4327b8fa', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-27 12:39:14', '2026-04-27 12:39:14', '2027-04-27 12:39:14'),
('d2887526e8a6d92d4664df9a617158da43a85d4cfb86ab2044826dd775d95ffaa92ac11083e6174a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 06:55:34', '2025-09-23 06:55:34', '2026-09-23 06:55:34'),
('d28ed9c3c5b83cf8223b6fbfffd500531b0347a124c222d97da270ed26f78048a7231f1fb21fbf98', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 11:15:41', '2025-09-02 11:15:41', '2026-09-02 11:15:41'),
('d2956b79f796a69b3381907b39bdf031341c14cc7f2e2df347338640a0a0613be12495f35b4b568e', 21, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:58:13', '2025-07-17 05:58:14', '2026-07-17 05:58:13'),
('d2ab8e5d4eee5dc20d8b62cf808f476ffb92f8e42636cf5800f622be565ef2c6913f1a38565d3291', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 04:26:15', '2025-10-31 04:26:15', '2026-10-31 04:26:15'),
('d2c5365af093a0c2dda593ebe50d9f6bd68bbc39914c369f9d1afc80c8160cfad876a578f63dffe5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:35:28', '2025-09-01 09:35:28', '2026-09-01 09:35:28'),
('d2eec76b51c2444813622544df0c92ae9c294e336af4ea63656a1d236bafb9b4f4c52dfd3d5d5f7d', 185, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 11:47:50', '2025-09-19 11:47:50', '2026-09-19 11:47:50'),
('d348debb34cd9f026c3a125fdbb66142813ca25d4a686f845650e9a897886fb8234581e0aff0d4fe', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-26 14:47:10', '2026-01-26 14:47:10', '2027-01-26 14:47:10'),
('d35c1fe758bd766da4cba1bd6b93acdd36efa21b0a2330376b9a30c25fb074eac3e30d3502fcb3bf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 11:12:02', '2026-03-16 11:12:02', '2027-03-16 11:12:02'),
('d37de917fdd9ac984713a5e4349884b7069481939b573f26144dde75ce9e0a35d5bdb6a2e3134f89', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 14:47:35', '2025-11-13 14:47:35', '2026-11-13 14:47:35'),
('d37f81463defb6c6ebbc4cdf51e64102ded13bc999add3a692a672e47cbc05a7d008f00d0c02e57d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 18:12:30', '2025-06-16 18:12:30', '2026-06-16 11:12:30'),
('d3984a427f4721bdaa8209b148bb748dd5276d7ec7c9d83e7ff735b1017220bb9ccba607476429bc', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:30:42', '2025-10-31 17:30:42', '2026-10-31 17:30:42'),
('d3c439071c43fd69c3916b492255b596fd2d5358327cae0c83b6bf2b7fa02c45e5f973deaf8a3272', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 12:58:06', '2025-11-07 12:58:06', '2026-11-07 12:58:06'),
('d3cd4f81800b7e37c508cc40c11fbe70b1881e96097bf6cf5ae77069052dbec6123a7af72b498eef', 21, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 11:39:25', '2025-07-16 11:39:25', '2026-07-16 11:39:25'),
('d3ddffb96de87b8177e7f9d01158e034016246a7e4646d0dcda4f51583580b5ec28a851b186b839f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 09:56:05', '2025-11-11 09:56:05', '2026-11-11 09:56:05'),
('d3de761cbb78dda514692f5a362d14cf25cbfa6b5a2f684489107406f0a595aa0cab75e1a3e8f961', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-13 12:23:22', '2026-02-13 12:23:22', '2027-02-13 12:23:22'),
('d42f6bfe64cb410cf52fc0fa572543fa648bfc6d0669c4686f8036b7f26117f8ffeae931e26dd6a3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 05:55:46', '2025-09-03 05:55:46', '2026-09-03 05:55:46'),
('d4307b9a86b4dfa20f5e6c070cd93d37d14c860282c6d50851dde22f4a99df05a05a6ea78f01025b', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:09:17', '2025-11-03 12:09:17', '2026-11-03 12:09:17'),
('d4313d5b99fa12a37935d849a440d72e59d107c28736f6088c9ce2c35315c2560e11f842fa204270', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 07:32:12', '2025-09-17 07:32:12', '2026-09-17 07:32:12'),
('d4546c117b29d57e9299183c7da74828ef79661fe2e5d398700a2100f5331853442b13d18da84e6d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:27:47', '2025-09-01 10:27:47', '2026-09-01 10:27:47'),
('d480c8ecea099ea2dc80e6a36d21be103cc462299c972435b1e93f17087d38300b9a64d53ff458fb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 10:16:56', '2025-11-12 10:16:56', '2026-11-12 10:16:56'),
('d49e5e41fa7f8583823029afb2c964a185f90111863c013ed31909b652cba7e8b7bd48f4beedeebb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-03 10:21:22', '2026-03-03 10:21:22', '2027-03-03 10:21:22'),
('d4ce6b2f4a130935803899bb22538064ac9b31cdae1f335230e8ca3a35b5101e870d7cf1bf86d80a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-05 14:02:00', '2025-06-05 14:02:00', '2026-06-05 07:02:00'),
('d4e65a6f7d7fea6489c9f440f590da79b1e6a6270e85045d53af041c6e857196fc11be9b4b5fb946', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:20:59', '2025-11-13 11:20:59', '2026-11-13 11:20:59'),
('d4f787edb33e074cfb040cfd86ec541819cbd871d8e494c3b067ad68dbd649d9b3b83caec2a1eb2b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-16 14:42:55', '2026-02-16 14:42:55', '2027-02-16 14:42:55'),
('d52e995e0359fb24ad264ec68076f8d1e4d1cd463b14ed360e958671a0f1868d03a5ba5c8eec8791', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-24 18:57:47', '2025-05-24 18:57:47', '2026-05-24 11:57:47'),
('d59b97be6ddb28b0f55ec4ab82d9e3644a1074b370a84e12213f187361726a56a3da0b011719bcd5', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 15:35:43', '2025-11-26 15:35:43', '2026-11-26 15:35:43'),
('d5d0e57df20158628f32e90b75d798d19b347e74aff065aa4d1a432a6acba6f391e83df1b3df91a4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 04:48:16', '2025-09-05 04:48:16', '2026-09-05 04:48:16'),
('d60484f83632fe96a16c7703ed80770cdd903f2fecf8ab611d47a80c4758f0f6c1503e0ecf634e50', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-25 16:09:29', '2025-11-29 11:39:15', '2026-11-25 16:09:29'),
('d61027114d96b7906d4ea1029d2ce319eceab0910224daf04733784949d64915405282e2b35fba06', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 16:10:13', '2025-11-13 16:10:13', '2026-11-13 16:10:13'),
('d6235a68da5e6e6d23707be9a6a85a826dafa4b44a8719f2518b2eb15f7a6bae90ce01b15ed29704', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-10 14:43:39', '2026-01-10 14:43:39', '2027-01-10 14:43:39'),
('d63ba5ba8446f9db82507fd83447e49402eb5b97ac71c4078bab2577707ec646634ee7ba578bdecb', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:51:24', '2025-11-06 10:51:24', '2026-11-06 10:51:24'),
('d668c3e0f381cdde36aaabf9885bd7d35bb02693fbc2c9e1624489032edc3113e77cd5482d4cfdbd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-19 10:13:46', '2026-05-19 10:13:46', '2027-05-19 10:13:46'),
('d6a8ee5c1904945799126193a8fde74fe4139384e694084d2f4983c41265284ec1acd61ccb9120c5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-10 09:59:46', '2025-11-10 09:59:46', '2026-11-10 09:59:46'),
('d6b8ac000f365abf4d4ef11c054b9b2165a9861037c07960298c13a76e9f7a877b301a08e5630b29', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 14:43:35', '2025-11-11 14:43:35', '2026-11-11 14:43:35'),
('d6c66e645b0532a73d62682c28f9b5a776e1eb508941ea19eaa80a2d138c45ab5f2a7a4e50b997a8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-06 10:06:06', '2026-03-06 10:06:06', '2027-03-06 10:06:06'),
('d6e55f3ef605f8aedbc76b023e7bd73742affa2db9524d28bfd4b1eb3a7ea9f90c903a2960bc4107', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 13:32:17', '2025-11-04 13:32:17', '2026-11-04 13:32:17'),
('d6eeb7eb6c2776988536f46bb64bcd194fa20c01b6ff54ff7fb9ee295567be295b4c2634d96dfa3e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-28 00:30:01', '2025-06-28 00:30:02', '2026-06-27 17:30:01'),
('d6f47352d1b8a83df4523952f1d1181414bc3131377bba3fcf3e6f6bb078fd1baaec53972b10775f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-30 08:38:22', '2025-08-30 08:38:22', '2026-08-30 08:38:22'),
('d7044280309a8128e857e0edc9eab36727e59eca08535619ca4ff8839f9137d62e206c094b0f905c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-02 15:24:56', '2026-01-02 15:24:56', '2027-01-02 15:24:56'),
('d71e02eb0f4829756077e80a1dc7de7f86a4e77c2d144681719cc8162fe8125c0fba8d0e42cce814', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-11-29 14:24:45', '2025-11-29 14:24:45', '2026-11-29 14:24:45'),
('d74ed9b31b9285586aef31bd2ae5bb14ce938c17b50a10c150e3f9342bc6222e5cccc554ff258153', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-11-28 16:13:21', '2025-11-28 16:13:21', '2026-11-28 16:13:21'),
('d74fac3f2a6bd8d558e0efeeca4d7a02a6c4dcb317b3830c4c0caf59c8d0f1660cb23af408e25ba3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-04 17:07:16', '2025-06-04 17:07:16', '2026-06-04 10:07:16'),
('d75804ebbb1a5234b8f087816f2aeacfa024363699ef0fa62913b55eadfa115167cf56161768da48', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-23 13:38:52', '2025-05-23 13:38:52', '2026-05-23 06:38:52'),
('d772fb6cdbaf3eb894955720d51c0349c18595fa15bb5ea2856d5c0ad5bfeafc157660bcbd5742a6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-11 10:35:41', '2026-02-11 10:35:41', '2027-02-11 10:35:41'),
('d772fb79a792dcd3376c3fcc3e41fa6ffa0718f13080412dfc6bd80fedae52865e20788463cc25bf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-05 17:23:26', '2026-01-05 17:23:26', '2027-01-05 17:23:26'),
('d79a731c100a4e313a71bfde78012daf198527ae009385e97fd73c883248148c3bb80cd202599ad4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-24 10:54:20', '2026-01-24 10:54:20', '2027-01-24 10:54:20'),
('d79cc0952ae8df519438b37cea015914f6af68a95b6198d04f49a10fccb09a6045799173c171fa51', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-05 06:25:19', '2025-08-05 06:25:19', '2026-08-05 06:25:19'),
('d7a06d464b3d8ad606b774dbeed78a8a064150e26d0b4b7fb3f1223b73f78469834e966b81ece8da', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 11:26:19', '2025-06-10 11:26:20', '2026-06-10 04:26:19'),
('d7aaaaefe2599fd39d4c26ea96112d4ac5ba06d480aad33c6583edc148eb62120c85bedd2c1a72ea', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:10:41', '2025-11-12 13:10:41', '2026-11-12 13:10:41'),
('d7bdd2068137f61ed26378faf67240b5ab759a795ebb0417ac066cb7d192636db659a09ea16db28a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-22 12:09:44', '2026-01-22 12:09:44', '2027-01-22 12:09:44'),
('d7bee1150ec6f1c97d11c03e97df45fc62f6abc7d633c2fa4c413c54779462a04ac3d96e790d2786', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 16:26:07', '2025-11-04 16:26:07', '2026-11-04 16:26:07'),
('d7d459ef9096e0b20f7f05622495819f5b1b048c7382b3062be666a361960c6059cefea3051a1fa8', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:14:20', '2025-11-05 13:14:20', '2026-11-05 13:14:20'),
('d7e8fb7ca6661ec7eb162fadb62c5246c2fb0bc78f17003bae70b7b56ab9ec61618f5165da76cdac', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-16 09:51:39', '2025-09-16 09:51:54', '2026-09-16 09:51:39'),
('d808fdc217dee1dc39489778375b7c7f6921d4fd38e755f4a3d95f46dc09f8882f38a29e31be7740', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:44:32', '2025-09-01 09:44:32', '2026-09-01 09:44:32'),
('d832990e1b00016a6a5996a0d3f3fa05242407a61d692547aa9f2ad87c51d3fbba3476e2eec3a577', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 17:36:10', '2025-09-05 17:36:10', '2026-09-05 17:36:10'),
('d833bfdb5ba4be8fc3058c304a32176da4f6b98d956ffc568ee7990e61e229877d3e922a1affc3a4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-19 18:43:24', '2026-02-19 18:43:24', '2027-02-19 18:43:24'),
('d8545b0a07b585e4c8e425a8de88b2a08f011f96005ad69faf4e24edba2946e437444bd7c1ef0a1a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 15:18:34', '2025-11-11 15:18:34', '2026-11-11 15:18:34'),
('d89777bdad589a7a69ee9691706a407f8078044c056f0ed6c91f2b944a9501f493ffa48157640d9f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-26 15:51:18', '2026-01-26 15:51:18', '2027-01-26 15:51:18'),
('d8f731dfba5d38fad4145911e82a453185bd9d280cd4bc24293f019ee302c627b04b91d375499e42', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-27 14:03:10', '2026-01-27 14:03:10', '2027-01-27 14:03:10'),
('d91933a37bdaf653f29ca5b65ac34600592434ca427ea514d697539f3c382401a3a312feb3d87857', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-27 07:15:59', '2025-10-27 07:15:59', '2026-10-27 07:15:59'),
('d92ba11414f44284c8d5656fd02b73ac638befba486e860895528be4244e21e5d094b64039be0fc5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 09:41:40', '2026-03-24 09:41:40', '2027-03-24 09:41:40'),
('d9b45cbffdebf1edab3c94622fe320fd1279060cf410704622889b7310977928b55b9e486d861b0e', 21, 1, 'LaravelPassportToken', '[]', 0, '2025-08-27 11:12:08', '2025-08-27 11:12:08', '2026-08-27 11:12:08'),
('d9e4e319a975725afc971a2ef811741831a2e6d58a0affdd1025daaeea272350b4c5ed444dd1f2a6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 14:46:39', '2026-04-06 14:46:39', '2027-04-06 14:46:39'),
('d9e5115a08aa25b17182e4a7d47ecaabf3df06f46a5cc4b9da29700ca6f9c6b82c7d9f78dfc2fa8e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 17:43:05', '2025-11-04 17:43:05', '2026-11-04 17:43:05'),
('da1e1058771f8392b4374b1e69617828196f00e606fb1992944cf25f68e45c95bb43aa8eed54d9a9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 15:22:47', '2025-11-05 15:22:47', '2026-11-05 15:22:47'),
('da41e917d618421cd433b4be83fe667c753e5f350ecec97f8bca2636881b960702df2440d9436378', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-18 08:33:18', '2026-04-18 08:33:18', '2027-04-18 08:33:18'),
('da568a6fc83dd71a858f5fe18e6d276b475cdb95c853286a12c5426f174f09caa61407721d1ece15', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-09 11:33:30', '2025-06-09 11:33:30', '2026-06-09 04:33:30'),
('da5ba62ba457949ef3eebbea248ab1bfbb893681b8dc2d0638c3f07d3fa379606839b5ca345d0a75', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-06 15:37:54', '2026-03-06 15:37:54', '2027-03-06 15:37:54'),
('daa6f651a4720fd69a4e2aed420c9ef52c3ce263a4ba4c9efd11cf87680a7b423cb3f27f440fd187', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-26 17:35:36', '2026-02-26 17:35:36', '2027-02-26 17:35:36'),
('daa846c17a1dd48f1d04765ce128207f389d788795a07b05fd9e0d3ea10b35228dcd9e6ee6980263', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:48:42', '2025-11-06 10:48:42', '2026-11-06 10:48:42'),
('daa85b3efa4af68acdaf7f1ef75d28bf4561403f2545365bade7b393fc2e14e5d2ed9b141b33f2d2', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-09-03 05:53:48', '2025-09-03 05:55:21', '2026-09-03 05:53:48'),
('dab04174ea01e5a85dad3bc88f71f00752369091320e325975c6f7ea679fd841ec3f3e7d4136c38a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 19:08:53', '2025-06-10 19:08:53', '2026-06-10 12:08:53'),
('dab28663398d56f11a89e63308a79df85ce37bc4bc10fe6db64fa1821480309390db0742e2012502', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 11:00:51', '2025-11-06 11:00:51', '2026-11-06 11:00:51'),
('dada7352b43b2c98e707d3f50d10962e9c3c1bffe8493f6ef0e769686bece586a456dbd75fdb60b4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-26 13:10:29', '2025-08-26 13:10:29', '2026-08-26 13:10:29'),
('dadd459faf4c962764d9a176135643372254c40b48ca731da4960cdbfe80e3ad47cd7d9a43a62557', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-29 10:32:08', '2025-11-29 10:32:08', '2026-11-29 10:32:08'),
('dae4c0b5282d2131ccdd71af30387f78a1a707276c325a8e04088231c3aea07cdd56611c0038aaa5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 10:40:46', '2025-11-05 10:40:46', '2026-11-05 10:40:46'),
('daea38035b6bf09ce57924aae2d47df9ac03b3161af9d382e03a0a72a629ceadef87f918f09e3eb7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-06 16:11:31', '2025-06-06 16:11:31', '2026-06-06 09:11:31'),
('db0cad9b98dbf929b732d2a4ade6bcefba3b00cd2c03fed02ad6f6b53c42b551d2cedd07670906d2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-30 04:56:57', '2025-09-30 04:56:57', '2026-09-30 04:56:57'),
('db0ffa743781dbe368f5b08eb55b20edeec3e5a0b045ce282896f97e0120d5ca086089527f856e46', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-08 15:26:55', '2026-01-08 15:26:55', '2027-01-08 15:26:55'),
('db3196bbf49100d4ec6ac7892257dc88ac33c6145933e1c177f9bb8fde53386c4317a47a15014a0b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 09:19:20', '2025-09-18 09:19:20', '2026-09-18 09:19:20'),
('db3687cd171aa8edfdc0ce254a65e1e57c8091d2eebe879498b83e26426b0fe3436cd699a4a1f21f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-16 16:18:33', '2026-04-16 16:18:33', '2027-04-16 16:18:33'),
('db4b219f31a530bbb2b16b38f4c4e298010af3d19c4406fd0af6747f88fe5707688b2477cea08a3b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-21 10:42:32', '2026-04-21 10:42:32', '2027-04-21 10:42:32'),
('db6c2238e11ffabe829823ecd3f16267ac7bb0c632807640365e0516b0166f2b22d52114c96c1a4e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-12 14:51:58', '2025-06-12 14:51:58', '2026-06-12 07:51:58'),
('db7f816b16ef7db637c6c3cd7ae6de2d37890df06979430f45f5aae277f48c390bbb80a1afef6de8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:55:02', '2025-11-05 13:55:02', '2026-11-05 13:55:02'),
('db8c87b48b4b86ee36a291fff3238fabe63b2d232fed52f9ce98363a02a0360a7ad2ca7b53b4900b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 14:49:53', '2025-12-01 14:49:53', '2026-12-01 14:49:53'),
('db9ea80f785256315bb28dc11a22ac11909e8a730da9e3f952a8b0012773419963aa22bb752b0fc4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-20 17:29:25', '2026-01-20 17:29:25', '2027-01-20 17:29:25'),
('dbc8ac1a099a990a4f6ea010d75e1930de4ee93281f693384ae24a9ba5a3a0af7473690bec667656', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-18 15:15:02', '2026-03-18 15:15:02', '2027-03-18 15:15:02'),
('dbe74bb9853cce46320caea6b987fe59b88887f0943abc338b4392a1b623e6a99f3bc95bb11116b0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-15 11:09:27', '2025-10-15 11:09:27', '2026-10-15 11:09:27'),
('dbe8c619bab27aa44509ef29165657746f5690f4b39756996cc99f9afbf58401059522a27935521b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-20 09:37:25', '2026-05-20 09:37:25', '2027-05-20 09:37:25'),
('dc0138cf583d3e15dc0a523e630ad18629c1bf0d67e2ac2775a21c26abf7800c886cf9f6d3a217a3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 18:04:58', '2026-02-10 18:04:58', '2027-02-10 18:04:58'),
('dc04b38eb7fb4d0cb8f8f169338be61e946152417f3c21a4e83448b0fbdaef022cb2ae2f182268ed', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-13 05:42:26', '2025-10-13 05:42:26', '2026-10-13 05:42:26'),
('dc06885229a172c89e4563ecb322bc6acb0a05d970ce870f493eda37bd4ffd454eb36643a5cde06d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-04 18:46:50', '2025-06-04 18:46:51', '2026-06-04 11:46:50'),
('dc0930c13dbf82054bfb6ea57d3630410847825d75861e785d84e7c54147fc2dd33b27ca003e1d2e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 04:37:44', '2025-09-19 04:37:44', '2026-09-19 04:37:44'),
('dc4f99a2e425f422925e4bf05bfcd5a4a8c037f9cfe9369091b2cf550f73bc109e047ac6bb4c9af0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-05 12:53:12', '2026-05-05 12:53:12', '2027-05-05 12:53:12'),
('dc7117009bdc97121e927592fcaad186faa890dce9451660659a4090e2ebbcd2638ecfe2f5ff2591', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-01 12:43:30', '2026-04-01 12:43:30', '2027-04-01 12:43:30'),
('dc731bfbab9b4048c3534ef8682ea562b9970b0a429bfd2ebcc7ee448e08aece09371122182f18bf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-06 13:15:25', '2026-05-06 13:15:25', '2027-05-06 13:15:25'),
('dccf47c305412484aab8eb786a6f7c5671d901dfd345dba0231672e0506fbdea1c3507375bd4d372', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 12:21:39', '2025-09-22 12:21:39', '2026-09-22 12:21:39'),
('dce4946823033d5527603754ce56af16a219d0b1a18ffd909323e119730aed1a95f2492f6dfe9b65', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-11 17:11:32', '2026-02-11 17:11:32', '2027-02-11 17:11:32'),
('dcf7f9e86f629b368627548f042b10fbb75e6d60dfa45ea5f8faf030b48cdcf0c889b9370072413c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 16:18:57', '2025-11-04 16:18:57', '2026-11-04 16:18:57'),
('dcff3f223b14931ce557138d7b66e6fe543f2b5e39a7961669aceb301b98e4ee7cc8503292161c8a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-31 18:57:42', '2025-12-31 18:57:42', '2026-12-31 18:57:42'),
('dd009ac7289872d579c5e9b98f21e727e802fe29c58c5a748f861252e6f33a9bb08cf2303e97d490', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:08:25', '2025-11-03 12:08:25', '2026-11-03 12:08:25'),
('dd16a5090841f79ad6e8cd4d2103fa5abf885206bd364fddac7dfdc6075192dd1910ab280c98a177', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 12:42:46', '2025-09-22 12:42:46', '2026-09-22 12:42:46'),
('dd48990503278d7da37010546bbc7d3a21f14aec6e97039628835a9319bdec676d3ccf688d4dd0b5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-04 15:17:24', '2025-06-04 15:17:24', '2026-06-04 08:17:24'),
('dde7a1024a862e668bff882b1876aa208bb9e0395fa7131b7829ae8f5eb618b4bd7ac2eb97f37f22', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-27 15:02:07', '2026-03-27 15:02:07', '2027-03-27 15:02:07'),
('de0e1c15cfb104d4cedcf2d198c759c28af09e8dae20b40744a582e9069889f9211187acf4121ea6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 12:29:03', '2025-09-17 12:29:03', '2026-09-17 12:29:03'),
('de1d6bb5dd548d02f048271548196530dcb76d9ba86788d761800a3d0072f564ed5b96bb66b6ad23', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-14 12:25:31', '2026-02-14 12:25:31', '2027-02-14 12:25:31'),
('de2f05d54b8c06dab7115d72add078ecb63af6c38aa50442690e587453e46d17046cebcba1d7bf84', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 11:23:46', '2026-03-16 11:23:46', '2027-03-16 11:23:46'),
('de4073e1000868388e47c04db93d1bb7548f0cd9de7cea99790b2a962ddf180f4d1e34ddd5e5a89c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 14:58:13', '2025-11-04 14:58:13', '2026-11-04 14:58:13'),
('de47bcb2372e989e04f81354abad097becae917a78d9ec936f9bad02496f41065734d273478f89b9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 15:29:22', '2026-01-29 15:29:22', '2027-01-29 15:29:22'),
('de4c20f168708a785c20e4b9ad52f135147688a4222147fcda5c64a2639af3cd9ffbc5f59412b431', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-31 16:52:48', '2025-12-31 16:52:48', '2026-12-31 16:52:48'),
('de729a24d6515498b06a2ae65683cbe7a74509c23f6c3aa98ad4d30ecaed2f2c2e5288d7f40ab1d4', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:29:45', '2025-10-31 18:29:45', '2026-10-31 18:29:45'),
('de766eefe2be796c3ca6e36ec3530aa8c79b9c2ca22a43f1744d64b8a787721154ad7b8ed6f2c6d0', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 13:28:16', '2025-11-13 13:28:16', '2026-11-13 13:28:16'),
('de7bf75e7f18d5776323d1cdc05566a5884e88b06a3c559e50d24fb9de8ad3bb6053ab7dc7a4898f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 09:53:21', '2025-09-02 09:53:21', '2026-09-02 09:53:21'),
('de87dfb04eaf1a44aef22ad719f86573d030c4660616009b56d779a8c48212d5de33574afc581bc2', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-21 11:55:28', '2026-05-21 11:55:28', '2027-05-21 11:55:28'),
('de8c397de1aad55779c60794c9464c1fe0cc11ee3856bb1886ce6fea64730439c7e641de5f961c0d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-25 09:48:46', '2025-08-25 09:48:46', '2026-08-25 09:48:46'),
('de8cfa7834e90238fc53536b805d896075d163e51efc64183678b5dc1d7f2758238dc7d532f9a95b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-10 10:57:55', '2025-10-10 10:57:55', '2026-10-10 10:57:55'),
('de911a4aa8a89f7ef420b44686c8b47a4a59c55fcebd66fc65bb5f3b5ba7a7955cda6ea60726ddcd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 05:04:25', '2025-10-31 05:04:25', '2026-10-31 05:04:25'),
('de9415ee4657e2336d79f76ca9695a079a9b2c1d994cc5762cb68075f75535a1a3f1765adc05373d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-20 18:23:48', '2026-03-20 18:23:48', '2027-03-20 18:23:48'),
('de949c9af80bf7f9ebfe0eea0f9732fbf71639ff9130ff762069f3f2e8693ac93f93c05f6d6eb99a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 11:45:51', '2025-06-10 11:45:51', '2026-06-10 04:45:51'),
('dea642313e070d6a2e641103e4044d2979b04bdc766305a37655accaa54e311d0899b79bccdb7145', 248, 1, 'LaravelPassportToken', '[]', 1, '2025-11-13 16:10:14', '2025-11-13 16:11:10', '2026-11-13 16:10:14'),
('dedc25bba104663095154e25fce0441e2f42c4be0b0060d34b4e03cdb5297091cef2dc40020957df', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-24 11:54:40', '2025-12-24 11:54:40', '2026-12-24 11:54:40'),
('dee4174bbf5b8a93cb4afccda61265fb4885e64f17a7ffa5dee421df55a332c11c5fb004a903296a', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-16 09:53:20', '2026-01-16 09:53:20', '2027-01-16 09:53:20'),
('def942c284c356ac8f00213fd5db5b35b8179386a4eb1dc332d5bd020d6828c6b0084532b603dce7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-12 13:04:07', '2025-12-12 13:04:07', '2026-12-12 13:04:07'),
('deff9f072eaea43e6b37909490197ddec72ec7f62ed926db45ec4dd99b31a88e2bf3f3664b214ec4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-26 10:39:40', '2026-03-26 10:39:40', '2027-03-26 10:39:40'),
('df2443d8d12ad66a3c9775390dcd7a0b67cd83621bbe7960da96b718b44c0508894c12849544ba70', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 10:26:48', '2026-03-16 10:26:48', '2027-03-16 10:26:48'),
('df6c5c3003be8d5a85fe86370f6357cf1f51a15f3e2ce87fefc787130de2c3b96db8716de46c2ce5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-30 10:38:15', '2026-01-30 10:38:15', '2027-01-30 10:38:15'),
('dfb48b78bef29bc738b943ff09deb1ac1fc9f41808b6471df3390e3ed6a008bba012980abc7eb9cb', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 10:40:55', '2025-11-03 10:40:55', '2026-11-03 10:40:55'),
('dfc62a33a1c7cd03ee4d4517a556951c721e79bc62c01fb23fde6ff38826cb9c352fe3fe097c9403', 179, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:11:30', '2025-11-11 13:11:30', '2026-11-11 13:11:30'),
('dfe2bcbdece9b49e9bd18a2af6fd55a99ea6bd03f152ce897e28e99a95ca44d734df45ceed1ac2d7', 288, 1, 'LaravelPassportToken', '[]', 1, '2025-11-29 11:40:23', '2025-11-29 14:24:10', '2026-11-29 11:40:23'),
('dff477cc3f70f6a2bad3e0aa8b50f49bac76b4709ac50a1595b468b4ea274347f668379fd2eeec2b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-02 12:50:43', '2026-01-02 12:50:43', '2027-01-02 12:50:43'),
('e04a947a2abe0a53c6811ab6bcbb4aa72de945bc20c02df09733f101d5745e1ab7dee4c36a26d5b5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 04:55:14', '2025-10-31 04:55:14', '2026-10-31 04:55:14'),
('e0896811b7595487b00c2826a77ae33b70fa65b1f6a2d32544e3d58665244f79befbc81106b4961d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-04 17:22:17', '2025-06-04 17:22:17', '2026-06-04 10:22:17'),
('e0b8f1c9d8125fd1b0cfcf4abcbc586a1ae8b8f0377be99abb4d69465774063fff566bf43674ee8f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:43:13', '2025-11-13 12:43:13', '2026-11-13 12:43:13'),
('e119fbae0d0b168c454fbafd62bbdcdc13dbf94c02f24d2dd9ddd614f3832798590b7df7e286993a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 09:24:25', '2025-09-09 09:24:25', '2026-09-09 09:24:25'),
('e1310349fd622cc2a224e0e7e0dc5e90c6bae8e7949d9c61fb33b5f7228896474d8901fbd6a5163f', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 14:35:30', '2025-11-12 14:35:30', '2026-11-12 14:35:30'),
('e166bf62ee659a74ebd47fa5f62ca380fa81803b3d8811dcb966b885329a59fe1c00c2166767fe3d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 10:50:14', '2026-04-06 10:50:14', '2027-04-06 10:50:14'),
('e19d53b164d41b293162703db6c541ebc0bc509c6a03a99cef66960298f7b1d9f2204418a6ee5cb5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-24 11:17:09', '2026-02-24 11:17:09', '2027-02-24 11:17:09'),
('e1a9bbb945b06f24ca13c6f244bde7554fdba384c5841602f1f1d8c1434e21a148112fd43a7ebbc3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-24 12:29:41', '2026-04-24 12:29:41', '2027-04-24 12:29:41'),
('e1d857e82225762de0b174b07c1bb2ca68c3ff0db966e87dcf2efa2208aa7b01ec245ec2b5d08521', 151, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 11:36:24', '2025-09-18 11:36:24', '2026-09-18 11:36:24'),
('e21885f103d89528079086372e2e2f05a31b28b8f10b1ff301258ef8135a68694fdc9911c644e7b8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-12 15:06:39', '2026-01-12 15:06:39', '2027-01-12 15:06:39'),
('e21ff8af293ae988c5dabb8b5e1cde5a4d8b141b3dbc166bdee1b434fa7022bdaef1c41d97e737a0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-07 10:14:10', '2026-01-07 10:14:10', '2027-01-07 10:14:10'),
('e22c845d355f346f15e44d99e3287fd39bb8b4dab36c6cf68b4c6a12c5c32f14deaedd5ffdd04fc2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 16:51:06', '2025-06-16 16:51:06', '2026-06-16 09:51:06'),
('e23459fa06cfe1b3a4efce9a87368d47410dbf8ec8b778c87d7845bf02645d94f255607dcfb4b2d9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 14:18:55', '2025-06-11 14:18:55', '2026-06-11 07:18:55'),
('e2534626ba8cc918bad9d35faf055cb992d32bb15dd17a6845967ccc8c427ab454324a1cfe28dbb0', 210, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 10:14:21', '2025-11-07 10:14:21', '2026-11-07 10:14:21'),
('e26c9eff0e2341481abc82e436e74e297682af53003407a7c9d1bf0adcc89c6299d46ce6089be5aa', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:08:37', '2025-11-13 11:08:37', '2026-11-13 11:08:37'),
('e3501b546f9b974660d6525f5fe023f3eac36786f6574ecb9e24a5ac4c29f3caf11ae9a575197fcc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 09:35:23', '2026-03-31 09:35:23', '2027-03-31 09:35:23'),
('e3d584d1bc62f46f19925353f0e43eb4745f3f0a91d4fce89d6a8a5a0cf80f42f9bd6052c61ac8cb', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 14:59:35', '2025-11-13 14:59:35', '2026-11-13 14:59:35'),
('e3eedfb6937fda8494957cd5858ae50e675da7f8c6709a036d960d002b40d98f5392ecf71333facd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-02 09:58:10', '2025-09-02 09:58:10', '2026-09-02 09:58:10'),
('e4018c618b45f1b0d9387bd20b176be1f6bbd1616b621d33826a9ba126fc552131508f310f7da3fc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 11:59:34', '2026-03-02 11:59:35', '2027-03-02 11:59:34'),
('e4176fe24d751963889959d47893fbc42f710aca97894684484c5818f36e6d77214227fdd4814bb6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-16 17:48:59', '2025-05-16 17:48:59', '2026-05-16 10:48:59'),
('e433cc6aa422d0e083c151468c3f4c7ac6efd445985392411e306292a1d172c8dbe2bf00730f0558', 328, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 13:01:39', '2026-03-16 13:01:39', '2027-03-16 13:01:39'),
('e442c8be548b6103f5fb57b7f59296dfff25674f010e221d0b916d2acbfd09d36f6327915315682e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-22 12:12:14', '2025-05-22 12:12:14', '2026-05-22 05:12:14'),
('e451131027ac09a2df8ecf345a86d0c718245146f5d98e3aea0bdfe5e317f931f6961e8f1a3c6c9c', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-12 14:35:34', '2025-11-12 14:38:22', '2026-11-12 14:35:34'),
('e48ca66813302729d322fe5f1b30ab607f17288c7124d8ac2f91171b548e8008fa3ce821c5365741', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:05:33', '2025-11-12 13:05:33', '2026-11-12 13:05:33'),
('e492c51400d3621c286cef826ecef9cc0d9142abf62b8e499562bfa1a92ca525976a8853bfb7b072', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-09 17:23:23', '2025-06-09 17:23:23', '2026-06-09 10:23:23'),
('e4984b03a2ac9ff9a8c49368b1098cdf76fec085c25bae6b6a616bed0d8fdea9eb9ef454424f8577', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-12 10:14:14', '2026-01-12 10:14:14', '2027-01-12 10:14:14'),
('e4a878f7aa2260d7e4ff5459664a20c8d7ab942dc919104c5be846804dce92b5c854611cfb841103', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-10 14:58:05', '2025-11-10 14:58:05', '2026-11-10 14:58:05'),
('e4c36d9b5548ded7bfcba92ea37abc651d06a01c4fa1b5af9d9bc40377804bf167004877f92558ab', 175, 1, 'LaravelPassportToken', '[]', 1, '2025-12-01 14:40:37', '2025-12-01 14:49:25', '2026-12-01 14:40:37'),
('e4c3b34a8bf84801344b18394a35d4e2ebac826165bbcff20d27d90fcb9828b870c2d62e087c016b', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-10 16:37:11', '2026-03-10 16:37:11', '2027-03-10 16:37:11'),
('e4c5bcfc85453c5477fc2caa6e46a6420dfc5b79f7e7e393bd22dd37339815baee7f985917cdaa6d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-06 10:08:33', '2026-05-06 10:08:33', '2027-05-06 10:08:33'),
('e4d399cae25d34b16876351302cc581fddd4f79c9cf1123caaa16889a692944a1ab857fc9beba7ad', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 19:18:52', '2025-06-16 19:18:52', '2026-06-16 12:18:52'),
('e4df1a4721683ace99b08ab43fd4967c21120383075351871e556dbdfd766f9018c001389966206a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 12:52:37', '2025-11-05 12:52:37', '2026-11-05 12:52:37'),
('e502320b69c4d30cebbefe33a9671b4608c6f1ed766df52f97ffeecd90a29cf53458457616676e26', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 11:45:58', '2025-11-07 11:45:58', '2026-11-07 11:45:58'),
('e5285567d1b831beea512e2dba180a88ca1a4c372ec8e6ebc6d8d20f9d8af0de14011ea07b01180e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-13 12:20:58', '2025-06-13 12:20:58', '2026-06-13 05:20:58'),
('e569cf9e2a0e2bcd1fabe7d4c658ccf75b24df625fb74af2d04a08280feb37b8e61751fb28918ff9', 151, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 11:45:42', '2025-09-18 11:45:42', '2026-09-18 11:45:42'),
('e5ad5df2c11aa7d480a49109cec643660216228a28d5bc156ea9cf25e1a216e9aa2083490478581e', 196, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:11:40', '2025-10-31 18:11:40', '2026-10-31 18:11:40'),
('e5bafbf908523e97089e7b5da4e90ecd41a4cc4d114dbb49d211215a9e1efd670757245ad7a3616f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 21:42:32', '2025-06-10 21:42:32', '2026-06-10 14:42:32'),
('e5c5cb17d733a9e67134ef074dad739fed308725886983f4f809e79ae3844670f0ec7f03b0e9ab7d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 12:48:09', '2025-09-05 12:48:09', '2026-09-05 12:48:09'),
('e5d843199be969f202c52056b4db84a62f42ae3867653428e29b7e40c1d2e0673b21f8619f3a01ef', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-10 14:32:19', '2026-03-10 14:32:19', '2027-03-10 14:32:19'),
('e5fd052b83d531867e4ea4714f21468e990f3cd53e65f5d9c1f29b7dd05c476c33569a129dce1025', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-29 09:38:59', '2025-08-29 09:38:59', '2026-08-29 09:38:59'),
('e60307f8ccdc98030c221e76c95e64b542f0b7c081e5002bd0b0c1844b5202be8104ce5f2bda1f2d', 23, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:59:28', '2025-07-17 05:59:28', '2026-07-17 05:59:28'),
('e60975f48fc1cb52dfbb05cb41304259bba84b50bf0e8d0b8150863cf6e04cce146e5f2500385b63', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-08 20:43:46', '2025-12-08 20:43:46', '2026-12-08 20:43:46'),
('e62a9f4ddacc0e79095d074802629cad05fe565142f73f5da89e1e56c38ab79134df0c4d23033f21', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-27 13:36:11', '2026-02-27 13:36:11', '2027-02-27 13:36:11'),
('e632c7f11427ca37b8fc16f3e37acfe9642e5f0e8de4686b76e65e7ca65fb036ecc5aa9654d30128', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-10 16:02:24', '2025-06-10 16:02:24', '2026-06-10 09:02:24'),
('e6404249c76a12a9e801ac92058f93446ca4ac1127af6c82f5bd288d18d13b51fa41030d2ce1620a', 219, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 13:09:48', '2025-11-03 13:09:48', '2026-11-03 13:09:48'),
('e6463c77c4c7f0ddb66184596392d1c19b71c0ac8add74b239f83e755a1f866e31d300ae191e4051', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-19 10:34:30', '2025-09-19 10:34:30', '2026-09-19 10:34:30'),
('e669a0c1b2e5a324bfe199a237f45cbc96d208384d1be9cb0f4d6dd2a266c2fade21f5193b5afae9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-23 13:19:10', '2026-03-23 13:19:10', '2027-03-23 13:19:10'),
('e68961b6123eac2ffac6225c5e120cd4e6f3b19d3404f8540229a28f3695798a841d3ae1b224afa5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-12 10:09:42', '2026-01-12 10:09:42', '2027-01-12 10:09:42'),
('e6d17edebb187aaf51443eff36df54b8d1a01295034bb82ecf8e356b8c08bb4498e434935a74faaa', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 08:02:00', '2025-09-01 08:02:00', '2026-09-01 08:02:00'),
('e707e0d5cb5afa15fc9dc14699cb20006e28b5069b8fd1fb7e5d8592f319f3574fa83c0772cda238', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-25 14:53:51', '2025-11-25 14:53:51', '2026-11-25 14:53:51'),
('e71c7cd67597c033aaec7087903cdc9da12acb1ed75f130a6424fa59e15dce6a8191471492bbe23c', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 14:40:58', '2025-11-11 14:40:58', '2026-11-11 14:40:58'),
('e775a9fd6c19399dcd3fa9ae0bbb2ed42a40712a4a634cbb99f2de16bcf65db5e8cb1e565c45a100', 268, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 15:08:45', '2025-11-26 15:09:40', '2026-11-26 15:08:45'),
('e7983af5622fadc87a0b5d65d136e661d4c263b23fdd5e1cab7efa03e5cf3daad1241afa808e1ef8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 15:35:39', '2025-11-26 15:35:39', '2026-11-26 15:35:39'),
('e7a3e4e72db284372b595748cd4fdb8e18c51873a0523493b63117f8aee9064e5b9b035fff219435', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-08 10:35:00', '2025-09-08 10:35:00', '2026-09-08 10:35:00'),
('e7b306cac476dce3e3970644e5aadeabf9d2b42d61d48f5537b1b4e67ea9dbed197930c967164ed0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-20 17:06:15', '2026-01-20 17:06:15', '2027-01-20 17:06:15'),
('e7ca11e5378de127d69499ccf131dff9ac565ff1109011d70a64d8d870829e67d82597e485e069f4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-25 07:54:59', '2025-10-25 07:54:59', '2026-10-25 07:54:59'),
('e7d0b3064688b033f8819689166bf51b298e8c843a4e89a69fbeb1f79f7b908b445e71fa3eb750d6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 15:31:52', '2026-01-29 15:31:52', '2027-01-29 15:31:52'),
('e7e7b56a797e94c9bf80cf74696d537854d4cd2c2f53e0ca06675e84780d3dcdcd38aca33a566824', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:14:01', '2025-11-12 13:14:01', '2026-11-12 13:14:01'),
('e7e92140d6fe5d383993fc3aabd29d4d38424afba8fb9ca7ff3d4e565165931a6ccfa11d75b560f8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-13 14:45:09', '2026-02-13 14:45:10', '2027-02-13 14:45:09'),
('e80772fcaf2716cea16be3b87d34932cd1d280df781acbddaf52091a6bcab62e33cb97db62914817', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 14:58:50', '2025-11-13 14:58:50', '2026-11-13 14:58:50'),
('e80776364da1a87aaf5c675eb0fdf046b98deb6550150e79144c90d14dc1aa655e5f21e1f277839d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 05:15:02', '2025-09-25 05:15:02', '2026-09-25 05:15:02'),
('e80e4c3b5a96c40222729cd8ecaca297eed33f02ed3ad63cb3b61fd3c923451fa4b21425d2f4d518', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:34:33', '2025-09-23 05:34:33', '2026-09-23 05:34:33'),
('e842545d39724e6242f27e7ffd437a7248158fc7bace0dd185aa5d1c059c38a563be536d8a14692a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:55:35', '2025-07-17 05:55:35', '2026-07-17 05:55:35'),
('e86af6abce8b722fb5e88ad38b26367eb8561b4efc9b3bda35c5b7af380475fc0f60f95ece1af3ee', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-13 05:37:21', '2025-08-13 05:37:21', '2026-08-13 05:37:21'),
('e892e0b414705a71f1e6c0b784e0a572e76b2f3b11f1ea49516a2c4c9609c75dc914007dbaac8ff2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 17:24:57', '2025-11-04 17:24:57', '2026-11-04 17:24:57'),
('e8988c2e71b6de2d5f3ce64348035344b7158a7636592fb672afd96c154c6bca4ad8d110469b0bf9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-16 14:30:41', '2026-02-16 14:30:41', '2027-02-16 14:30:41'),
('e8a5df6dbd5767f5352edb2566fcab612f3c6f69fb48a6a9b13dc89c69023d6e518a1db85d6bbc2f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-22 12:16:23', '2026-01-22 12:16:23', '2027-01-22 12:16:23'),
('e8c0032bc4567499710264196a1f3578aa864797458f4e2e95eafd7b42644b5c7e2d1cd3b7fe84bd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 06:57:30', '2025-09-23 06:57:30', '2026-09-23 06:57:30'),
('e8c1a1ad7451e81db18536606862e8aeff35c53bd94421b902e06c8af46df3d4672ffe9a1517dc53', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-25 11:43:36', '2026-03-25 11:43:36', '2027-03-25 11:43:36'),
('e8cc583eb2ba5b24959a6b63bd2dba0541ddc8eec754f03f3ee81f28e2e6def8b6df962b174ee625', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-28 16:02:00', '2026-01-28 16:02:00', '2027-01-28 16:02:00'),
('e8f53ba42d8a44a739249efcb72077fe965c54aa713abb7aec11cfd4a236b9e29ef89183f43cf1fd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-27 11:38:13', '2026-02-27 11:38:13', '2027-02-27 11:38:13'),
('e90784504037396289c8ecd28283352652381fd22e10b4dd8c858327a60e2164a0b6274a655ac1d0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 09:40:31', '2025-11-11 09:40:31', '2026-11-11 09:40:31'),
('e907ba10cebb8e58c606258c68d1ad09432de1029b500dc8c0bfa19521941e10bf5ce6bd58e1d1a2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 14:35:05', '2025-10-31 14:35:05', '2026-10-31 14:35:05'),
('e90952084eb3cc6a77e1d607c64077f762afd0a98d3d6f46cbca85f22b109571feb8724d28eaca87', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 05:56:15', '2025-09-03 05:56:15', '2026-09-03 05:56:15'),
('e91feea01c6053571f8bca08247515ac9428cac0a0107366d3781bba8060632985baec77338c31cd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-24 11:08:47', '2026-02-24 11:08:47', '2027-02-24 11:08:47'),
('e923a9b3ea63bd588d5293b3fc3d00a78ada6e20607865edb50820181f060be7020c7d7c4ffed2c5', 215, 1, 'LaravelPassportToken', '[]', 1, '2025-11-12 14:42:53', '2025-11-12 14:44:09', '2026-11-12 14:42:53'),
('e92dc135cde1c680745990b9518c52c66a68daa5d963dbb76f5a19c3b9fad749183229c8bc3c09cd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 15:57:56', '2026-05-25 15:57:56', '2027-05-25 15:57:56'),
('e96ac334d590c216ae49a3fe594b6455ec1524e2ad086279d749d989b19bff1784cc476138495e0c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-10 05:57:06', '2025-09-10 05:57:06', '2026-09-10 05:57:06'),
('e972f93a6377eccf883d30f9ab8250b583abcbbb1e223753cead7c373f01e949333d4f775b167ac6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-13 04:40:31', '2025-09-13 04:40:31', '2026-09-13 04:40:31'),
('e986275bb69fb396819fd1d60401b7b6287575ef4efad447db54079cadf6bc4df0d9f4e877f72710', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-09 11:41:34', '2026-01-09 11:41:34', '2027-01-09 11:41:34'),
('e9bcc9d0896a1090a31c6dc59ea2fa3666ca3bcb19ef8e249b5774b3c5f426e8d8a3ad9bbd7c16cf', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:29:13', '2025-11-05 13:29:13', '2026-11-05 13:29:13'),
('e9ee5fee4f2160e00f298f3cc8481272ce405ab4e96b11f574c21249997c873efdedddeb96874e80', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 16:43:52', '2026-03-31 16:43:52', '2027-03-31 16:43:52'),
('e9f049e95d401f2033febdfc156b94c345d1f0eb5653a0199890dcc2526ae8881e262266ceb6b9e9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-12 11:11:32', '2025-06-12 11:11:33', '2026-06-12 04:11:32'),
('ea00c507629d2da657fc1b55bb490b300e60832c53112a19c09659b727eb6b74b369f960ad533f4e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-01 11:25:27', '2025-10-01 11:25:27', '2026-10-01 11:25:27'),
('ea2a87b57480852c34b2254f0a63307edf26c2e1d21960a7a6a7292bb89cef7b2f9461283efd2ff1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-10 21:04:44', '2026-04-10 21:04:44', '2027-04-10 21:04:44'),
('ea2c75c56a6345827fc27f4a4cb93fa8fb2624e1bcf7454ffcc33c8d6ccf709a083eebccdfe42019', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 11:56:53', '2025-11-04 11:56:53', '2026-11-04 11:56:53'),
('ea484c0de57ff47d2c6e9d8f2270f1bc42e0a408bc719f080d4e6a2fbd9a77dbdc1769aef8fbf010', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:57:40', '2025-10-31 17:57:40', '2026-10-31 17:57:40'),
('ea6171cb37be3de911d49eb53efef1c03987ea5f1033a7b2ecc83140723d42bd335c5d843222c13e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-31 21:03:17', '2025-12-31 21:03:17', '2026-12-31 21:03:17'),
('ea6da698e3216f579d73738477c6b46a3a4aec8ed17f1c99dcb46e8ab2f19d3223e4c8dcb671cc75', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 13:51:53', '2026-01-29 13:51:53', '2027-01-29 13:51:53'),
('eaf856b80334c72a017f46686d7e1b5d5a41b5e70898f83c8a1087e1991bd30060e457c06bee2687', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-26 13:00:16', '2025-12-26 13:00:16', '2026-12-26 13:00:16'),
('eafa33362556942f0f906b4e3dc77492f91d556c819b039ff8b0f0d809ab2fd886be59aad4975e8d', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 17:59:04', '2025-10-31 17:59:04', '2026-10-31 17:59:04'),
('eb24969553dd00d212e5fbb4ccce25fe98791bfc905d5189531a6a87c07072468b245693d81120e7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-12 22:29:27', '2026-03-12 22:29:27', '2027-03-12 22:29:27'),
('eb35936d32d629e3c416756cf918282b09c9ad0dc3481d1e4785c5ba9eb9c6da954d29bfb6f5bf54', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-14 13:26:04', '2025-08-14 13:26:04', '2026-08-14 13:26:04'),
('eb3f35c041520d09503f3413ac18bc45404ad23080cd8cc91abd5a67a84413915e08f982e994c5dd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-12 11:36:48', '2026-01-12 11:36:48', '2027-01-12 11:36:48'),
('ebb4bde68610e9fd1181733f691f57db947b6478f21282e59133f58094c7d27880f7e83aa29b5410', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-26 18:43:33', '2026-02-26 18:43:33', '2027-02-26 18:43:33'),
('ebbe7b7f8d94d2506b50268247edb68f37f8e8341f2e22696cf96db35681bcd19f7d122d32b18011', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 16:08:24', '2025-11-13 16:08:24', '2026-11-13 16:08:24'),
('ebe0f16ad42ab626623ddde19ec5c3770c2e2408e79478e36506796dfb3e7eef431d91c03336569d', 347, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 18:36:59', '2026-03-31 18:36:59', '2027-03-31 18:36:59'),
('ec16b5203213ee4b6736c7b8005ad82c1c65fba11fa58a203a40845581f58ae8aa3bd69d54066cc9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-24 10:11:45', '2025-11-24 10:11:45', '2026-11-24 10:11:45'),
('ec19f3fc18aebccec308386094249363ba45fd417c57bd9d72383cb6086d236568f9533f9089cd55', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-19 11:19:49', '2026-03-19 11:19:49', '2027-03-19 11:19:49'),
('ec41e8414d3f6e7f970a51b0313837d92685089459625fc6e2b6bb1838167f33d8d7c7a991afa806', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-10-30 13:37:45', '2025-10-30 13:37:45', '2026-10-30 13:37:45'),
('ec45cacdbd982e6cacd44fe4e40f3ca9d74aa9bd61fdf544ae76c66cf294d484419c4cd499a21d37', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 12:26:09', '2025-07-16 12:26:09', '2026-07-16 12:26:09'),
('ec61b1bf40a060960e96fdc7497acbaf6a8cacfa4b0b881bd8f5d83ef685a54332563f1d93bc3e17', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 10:53:43', '2025-11-06 10:53:43', '2026-11-06 10:53:43'),
('ec674c7ed103778b4f3bd8a79e3db2db819258250536af0f4a9ead3c5913f08417378968732eb968', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-29 15:31:58', '2026-01-29 15:31:58', '2027-01-29 15:31:58'),
('ec74ba3883fb8b59573018c1a33b210f82cffbfc14525d48a87c4029790e1b1b393495e88e16f2d0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-01 09:58:34', '2026-04-01 09:58:34', '2027-04-01 09:58:34'),
('ec8fb01913f9beda419a1de43e9d2e300acb69b35e7ee947c8589d100359317e139df7f59507529e', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:55:12', '2025-11-12 13:55:12', '2026-11-12 13:55:12'),
('ec9e6264980006a1140c40ef77e51d21ca66969526136c3b8f8977c6f3154b12e1ee03d4305ed5a4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 17:48:23', '2025-11-06 17:48:23', '2026-11-06 17:48:23'),
('ecd15debe569d382ddd63c185d20b102e4a3c719ed37225197b33b431d0dbe2c4292b27939e94523', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 15:15:11', '2025-11-13 15:15:11', '2026-11-13 15:15:11'),
('ecde4b7d5a418f6fd1bac6665f11217d1f3e283f6158cb7a4f40b3f15373ddf6be7a7ad5a24b5c7a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-15 06:39:34', '2025-09-15 06:39:35', '2026-09-15 06:39:34'),
('ece47347aa49643170d7a498f3a513f1a2116c4c7bbc0c79825c18aab4419d92ce569997a9a40c69', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 06:37:54', '2025-09-25 06:37:54', '2026-09-25 06:37:54'),
('ed1e9d87284547011c357ad6bf51377d61cf71e5998c9a7a80e0838bf351890d0d43d1c1587a882d', 175, 1, 'LaravelPassportToken', '[]', 1, '2025-12-01 14:38:27', '2025-12-01 14:39:07', '2026-12-01 14:38:27'),
('ed28e656cf47df653e41063e365fbdaafcd74bc99af3507a1f0303836be3d6a2faa3e9cef42672b7', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-10 14:49:01', '2026-03-10 14:49:01', '2027-03-10 14:49:01'),
('ed397d09cdcd59adae5e2f58d7fd0a77ee43ffb9b8ed5866a84b876b50042a1b6aefefbb4069255f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-16 11:08:05', '2025-06-16 11:08:05', '2026-06-16 04:08:05'),
('ed3b8e076b13e1a3cf78143e5a6cb42824e621c8b445a02184cf6cf679aa521525b175b7c1d07a56', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-03 12:25:04', '2026-04-03 12:25:04', '2027-04-03 12:25:04'),
('ed447d0de789357af44645f6b7c89c14fa104f8ca59837085b70bc38d47de2f85beee5a05534d647', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-07 09:39:21', '2026-04-07 09:39:21', '2027-04-07 09:39:21'),
('ed579c78eb4ec8014f6b38b9d4ef0528b21b4281f6138ef83d23cf3d3f2bd898370a6746741bc3c0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:28:20', '2025-11-12 12:28:20', '2026-11-12 12:28:20'),
('ed7be09b96caf86fa9d8f313d24621834c78ec31b7e1922ebdcc58cf288d3cfa214aa5c52e642448', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 08:23:42', '2025-09-01 08:23:42', '2026-09-01 08:23:42'),
('ed99d83b105cb490fa43c88aa7487bda6e01618ac247043821a4597ec50b933167bc9908a776cbea', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:37:30', '2025-09-01 09:37:30', '2026-09-01 09:37:30'),
('edaa02b83c134d0ef5a45991831f42728415b75639429df4a289678d743697a9120765d5b3822a55', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-27 10:23:25', '2026-02-27 10:23:25', '2027-02-27 10:23:25'),
('edc5dd0c7abec306c77599322560501609449ee7dee83b7b063cc94851ed62844e4ae8bcc82632fe', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:24:04', '2025-09-23 05:24:04', '2026-09-23 05:24:04'),
('edf4596108a98fb86f865a08e3a249816e4c06f7f6d14d3454413dd2ee245a5494188c12c22b6cff', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 10:28:42', '2026-05-26 10:28:42', '2027-05-26 10:28:42'),
('ee354ec6edd7385325780eb7993acd0bb298b3860741cda30c3cdc2768061338d43cd95f5fbfd8b5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 17:04:12', '2025-11-20 17:04:12', '2026-11-20 17:04:12'),
('ee50ab9fdd54bd9453540071a4e69e5f36fa9caaa427d63ef2cfdf2ab3ff812d6960f93da756078f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:13:41', '2025-11-05 13:13:41', '2026-11-05 13:13:41');
INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('ee53a3079dc74e7725d53fe8cbe374bd6713db4a46401546b64943b3d49e9b09827525100333d001', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-11 05:32:41', '2025-09-11 05:32:41', '2026-09-11 05:32:41'),
('ee5e5b3ccd954bb33ceb45c735fcc5d32ed58659d2c3292497ca5145ed7a688e82a5be0a2138cd68', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-02 09:46:08', '2026-01-02 09:46:08', '2027-01-02 09:46:08'),
('ee6e491bc6e7c07c57a34262b086ad68fd9bafd2f1fd2ed9f5d60a2e5c03437d7993be3a6c110d95', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-26 09:40:37', '2026-05-26 09:40:37', '2027-05-26 09:40:37'),
('ee763f00f8aecb391e172f38ad311e2f1913bc46571d9bd15fd84538534e3b9562f215547851977c', 21, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 12:08:39', '2025-07-16 12:08:39', '2026-07-16 12:08:39'),
('ee863f5aa095d3e5e8f92b133fc56585768ad7790d45d5006fcdc01dd8be02e4b991481b2552ce56', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 08:13:32', '2025-09-18 08:13:32', '2026-09-18 08:13:32'),
('eeb005b489d7b13ed8b23c11cde01121ef73d8540506d43a38abeaf5e0c3f7ae2a102bcb4a6ca61d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-23 09:46:05', '2026-04-23 09:46:05', '2027-04-23 09:46:05'),
('eec0bcd8c4450f5b24a5bd35e137a728387a11efd87de82da3cb7af7d556e2d21b154a0571ca7bf2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-14 10:10:39', '2025-11-14 10:10:39', '2026-11-14 10:10:39'),
('eec1cb7f46899ddce1b2d10e54732ceda96685a0a4ef90952026d5dcf411dc69b063744dc7717073', 214, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 18:33:41', '2025-11-26 18:36:47', '2026-11-26 18:33:41'),
('eec4429293dc1a61a67e585fbee5e6e882c0859de0d5ed79cb2deb7164a1d78a7911c3306fb7d830', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-31 18:31:41', '2026-03-31 18:31:41', '2027-03-31 18:31:41'),
('eeca46e8d4e0daf4b4828bf2cfa463ecb73f6738218e775281e1c6b7640db81c5816a4249942336d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-19 15:15:29', '2026-01-19 15:15:29', '2027-01-19 15:15:29'),
('eee5651dc4d54631c9ddbea16831fd1930db98d22e33108e9548c83d89f49c3fa85886ddeb9e2de1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 05:04:29', '2025-10-31 05:04:29', '2026-10-31 05:04:29'),
('eeec481f9ab0f8943e549b94f2d1f81d0d23a81d075d1869f1a47eed8f412a8669fe23e47a0681f6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-30 08:53:05', '2025-08-30 08:53:05', '2026-08-30 08:53:05'),
('eeeda9fcedf15bef583ad93e5ce6307522fe06a708ec5666b5154b263c2775e314a8c169f6b7351d', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:41:26', '2025-09-01 09:41:26', '2026-09-01 09:41:26'),
('ef5818160af1de5b8a7b80b6f190190657962459f36b3428d83801ec15fd89eb271a23ac9bef1aa3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:44:52', '2025-09-01 09:44:52', '2026-09-01 09:44:52'),
('ef6b3085c01bf0784fe8c1cf2c13e8f701d9fd1c08efa858c6a90452abd8050b0de2c740243e817a', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:06:06', '2025-11-12 13:06:06', '2026-11-12 13:06:06'),
('ef7eef8e6d9961bf65108a1ef7a4e0f99dec2952bee1f1b77d776f5be36ac515e6567f8489703473', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:17:06', '2025-11-05 13:17:06', '2026-11-05 13:17:06'),
('ef8f191ed001a2d13b343611617f8df7e037fe5c299ea2677b0258a72c6444a61376854520d2c514', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-13 16:58:18', '2026-03-13 16:58:18', '2027-03-13 16:58:18'),
('ef96d518dc41828348d676fa1ee53ce9a6b78325bc1293daab75e5dfe7652dc73348c096430c88d3', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-07 12:03:31', '2026-05-07 12:03:31', '2027-05-07 12:03:31'),
('efb491b3559974b551a432d12b29fe637f9d5adb5b611730851a34ac77227e6079d44defbc67b593', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-08 16:03:48', '2026-01-08 16:03:48', '2027-01-08 16:03:48'),
('efb92faa00e3eaabf7eae3183966b363e4a2dcf3521e0163b2c438720ad697403415b301369fa72b', 328, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 15:22:46', '2026-03-16 15:22:46', '2027-03-16 15:22:46'),
('efe3cbf47660d21a7d9806dbf06d1d440de6ac6cdd85c13a476101cc03f40db73ea9c202fab06841', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:08:58', '2025-11-13 11:08:58', '2026-11-13 11:08:58'),
('f01b0ff3400915ac598f4a26df91ff0bd0232c6a5316e4c822d8e51e228378a989ff53f58c1f8668', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-03 18:15:37', '2026-02-03 18:15:37', '2027-02-03 18:15:37'),
('f02f47967bb5157551ecaa303f69e9dd53e9d2e6d5778e4c0c184d4fcc5b17ae892a6651a11d343e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-14 17:45:16', '2026-04-14 17:45:16', '2027-04-14 17:45:16'),
('f0479a49ea899c2bd39bf91c589239b77e9d2f19e36d8d028f1a7060e6d4e0c8ed2ff0e7bedac15d', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 07:26:00', '2025-09-23 07:26:00', '2026-09-23 07:26:00'),
('f04d4954c0723238a8d4d12bc42286780a8712466af6b33cacea87cbb54273bf5b6f0beaae7ef4c9', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-21 17:54:42', '2026-01-21 17:54:42', '2027-01-21 17:54:42'),
('f05d47c811151fa2f9f624bc8064e81c4ee3243600dd95a39b72bf3a26e17ed3b53924728eb4f013', 214, 1, 'LaravelPassportToken', '[]', 1, '2025-11-26 15:35:46', '2025-11-26 15:36:14', '2026-11-26 15:35:46'),
('f067425b24120deba61729d76f390d2b982365d817f71180bd449a79b24e10d90fb1307f5b065e8d', 22, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 12:10:24', '2025-07-16 12:10:24', '2026-07-16 12:10:24'),
('f09085e3ed25320998fb80c2580910a12c0f9963d373c17762eed4d326905addfed268a214e58de5', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-12-01 12:10:35', '2025-12-01 12:10:35', '2026-12-01 12:10:35'),
('f0ae196006141648648f77f4edef54ae8ef53dca1746e3f9854bb2082e4cefffbaee058269b61364', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 12:51:23', '2025-09-01 12:51:23', '2026-09-01 12:51:23'),
('f105ade7f0faa86a6e739c3b05c78a960cc9d3d1dac0f9819757fbecb08c10faea3df0be01cd7345', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 11:29:55', '2025-11-05 11:29:55', '2026-11-05 11:29:55'),
('f1092fcd875042acc3772bf484d21c54ff7c606db70922fb9bfed03861f534939b30085cab9c59bc', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-04 19:07:57', '2025-06-04 19:07:57', '2026-06-04 12:07:57'),
('f124058520ea3118765955374f0e5e3524b18b877f16db45f8db94f9e65b010e0eeb7c7c906c2bbf', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-16 11:45:26', '2026-02-16 11:45:26', '2027-02-16 11:45:26'),
('f12877b106e7c46587c5afd0a472e528c2c2607aaf79c03d89b239799b839f3974078a682725d2d3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 23:00:48', '2025-05-30 23:00:48', '2026-05-30 16:00:48'),
('f129dc9edf56a9deb88da59252001fbf033e29b3fb2ab7ad57c0d69ac7822b4346e1546a0aedc6a5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-24 17:05:26', '2026-03-24 17:05:26', '2027-03-24 17:05:26'),
('f1a3705caa6259cfc5c1231978caf6379e23cb73b10427ba8073232393ffbe9d95ec0b264038f3ff', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 04:39:20', '2025-09-22 04:39:20', '2026-09-22 04:39:20'),
('f1afdd8f041994e315ef1015a80dac4cfaa8333fba71621f9da0fefe2b019d580c225574f691d36e', 288, 1, 'LaravelPassportToken', '[]', 0, '2025-11-29 14:12:37', '2025-11-29 14:12:37', '2026-11-29 14:12:37'),
('f1d9a0edb0065924151cc20faa2f66e44926cda7652cd423ae2b58f1b4d8c6138dc5541e1c610e57', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:49:51', '2025-09-01 10:49:51', '2026-09-01 10:49:51'),
('f1ebf32dca09dcbea3133f9a27f1c33dabc6ad167880c2aedcc4c84d9581f927469b5464a27e96a6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-11 06:00:55', '2025-10-11 06:00:55', '2026-10-11 06:00:55'),
('f1ed1b20b0add7aaa520b6bd55f19d905339e0a05b3e8343c340cd2863126a06e3a24745a86d8ef0', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-01 09:44:00', '2026-05-01 09:44:00', '2027-05-01 09:44:00'),
('f1f3af9dd88e0e4785c0008348be22e53cf94fe6776ee2062b4f577f7c87087aa6b270ab9bbcaa6c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:57:48', '2025-11-03 12:57:48', '2026-11-03 12:57:48'),
('f1fb84d01e5bb26e69b68df7fd99e6b43b84af6f3cd8b7c053949a75075a498a5d8d04f83f0a7938', 143, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 09:24:09', '2025-09-18 09:24:09', '2026-09-18 09:24:09'),
('f1fca246d2b61577fa0caa8c95b91ce280a2c3eeac873ba448a148238f7417ccf5a0fc5cc97da3c3', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:15:39', '2025-11-05 13:15:39', '2026-11-05 13:15:39'),
('f213772a79553b827ce290aa4c0f5f063e908a161b4e063836f046a10b4eeb2f6ad314b6c4a35834', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-16 12:09:42', '2025-07-16 12:09:42', '2026-07-16 12:09:42'),
('f21a7499a17538cf3594c9b5afdbdd63d485e4c0c62a554cde15d9ea70ec1266bf44c58f6a15c153', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 22:38:44', '2025-06-11 22:38:44', '2026-06-11 15:38:44'),
('f22dd5242c60b05605ba31be5e37c96f25a260515af941cc07a2e9a3f2f11a9ac9ccdf94f31689a9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-09 17:46:08', '2025-06-09 17:46:08', '2026-06-09 10:46:08'),
('f2463a90d9041699ab7de45ed6706ecec55fdf496e92f37ac556b5f02dfcea2117aad28cd284b0d5', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 14:43:04', '2025-11-11 14:43:04', '2026-11-11 14:43:04'),
('f27433e13860db77bafc13df56fbb0d85e014cda0d0a66d9b1d53b17abbadd631888d844ce3a8e63', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-28 23:16:11', '2026-02-28 23:16:11', '2027-02-28 23:16:11'),
('f28a1060d21aca8e5d048451efdbdfdc54e3ae751a0d36206a4f40093edb815ea19715faf200c2f3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 10:25:41', '2025-11-03 10:25:41', '2026-11-03 10:25:41'),
('f2939a9b57993b9cb68a1df2144e6ffb6c8b3b29ae82e0e0e00ce3b52966bc459774fe887fc8560f', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 13:30:44', '2025-11-13 13:30:44', '2026-11-13 13:30:44'),
('f29a42a4cbe0db8eb68a48c7a55dd5df413e9725220d22d81b451c852f4f7fab1c34ded28978ad11', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 04:54:36', '2025-09-03 04:54:36', '2026-09-03 04:54:36'),
('f2b6304b3b6d0e1dd95a771ad989189948849ed47bd3061d428540ed058e5d5d1176e5326d71713d', 150, 1, 'LaravelPassportToken', '[]', 0, '2026-02-25 23:33:53', '2026-02-25 23:33:53', '2027-02-25 23:33:53'),
('f2ba48493d2c7a88e97708672b9b1ae0d31bf856ba1416c4b18f6d077feb9a22abe3eb0c5f7dba71', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:07:07', '2025-11-13 12:07:07', '2026-11-13 12:07:07'),
('f2d90f739b8df8d652d22dd30d36d7da61c144f76543a77110ff46108a611ad5c42122f2c9eff641', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 13:38:16', '2025-11-12 13:38:16', '2026-11-12 13:38:16'),
('f3031fe3c4796cd3b4c750b383077593fbefadb62a1e55a2bd99dc2374569a8b07d48f3b5b61ab86', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-17 12:17:13', '2026-02-17 12:17:13', '2027-02-17 12:17:13'),
('f340ee5c12b18ca8fb1f8ebc2c642f1ee64fd97507732f6e0c52667bf4e7e6150172096ca381be8c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-11 07:32:42', '2025-10-11 07:32:42', '2026-10-11 07:32:42'),
('f341de319987be53868f767bb9988ed19ee46071cae622cf83dfbe466254ae4e4dacba747eb6c65e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 18:45:29', '2025-11-11 18:45:29', '2026-11-11 18:45:29'),
('f394f1b699d92730f86fc89f5357d7d8cf0c2df70b1e3fcfd5d9edd3c0441f12988ae595a2473872', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-19 12:22:58', '2025-06-19 12:22:58', '2026-06-19 05:22:58'),
('f3d9f10f3fb0d9d818a358ffb2e04c5025e0eaa27518ae3b7f9d41e80e180d7aa9c969555c88f561', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-12 06:15:00', '2025-05-12 06:15:00', '2026-05-12 11:45:00'),
('f3ecc40622a1296b633d237ee494aece9723e426ce2b53933d069056bd2f22bb8332ce212082fbd6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 16:02:49', '2026-05-25 16:02:49', '2027-05-25 16:02:49'),
('f3f149b0bb9a853d77088ff1585fde54f1ccc0fc09d6f97ca679ef8fd5090c524ac3af333e542aea', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 18:19:13', '2025-10-31 18:19:13', '2026-10-31 18:19:13'),
('f458a7c75316407c59338caa6341d23a44be3e79add80a52d7ac4369f6ac56e984ac9f8338620b64', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-20 11:30:09', '2025-08-20 11:30:09', '2026-08-20 11:30:09'),
('f45ae45c791e308dac05e2154bcf466dfe2a97536e3adfb89d997b8db8bd66126d806ea19918f376', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-20 14:56:43', '2025-11-20 14:56:43', '2026-11-20 14:56:43'),
('f462f74f00264bfd46586ad8a838b5329d865cc4ef22d00ffb5ec9943883d8db6971bab35ba8749e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-05 13:51:47', '2025-09-05 13:51:47', '2026-09-05 13:51:47'),
('f46964378af1f6569dd3844397d41ce75bbef31533138d4e6af0d8a90fba983c6161da1b125e8a8b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 04:53:20', '2025-09-09 04:53:20', '2026-09-09 04:53:20'),
('f4793c8890efe2594e4ee8f3554910a8e0e0d69ac9db4e681b50f981234f1814232175c34fe3d4f8', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-05 22:16:09', '2026-03-05 22:16:09', '2027-03-05 22:16:09'),
('f480daee30bb0d1609a939796c716dc4a58a76429259b48b8fca1459480949e379256fea36ea3824', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-16 10:08:33', '2026-03-16 10:08:33', '2027-03-16 10:08:33'),
('f4b90433da96df9d0065be330e1ff671a730d4d3b2fee35133a633d3b3a0554f4a9708005d9a1d33', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 12:57:36', '2025-09-17 12:57:36', '2026-09-17 12:57:36'),
('f4efa82fa6804f68f5a08c7fd6182eb965655f279609f2d268ad2d6f1fb11bc9c4c723bfe305e3ad', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 12:12:54', '2025-11-04 12:12:54', '2026-11-04 12:12:54'),
('f4f814fbe946494ceacc5a905e61d7294eed15578da436daa9a607713cda5034cbbdeea901f3f2c8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:56:34', '2025-11-03 12:56:34', '2026-11-03 12:56:34'),
('f529c9d19cc6d59c3c14ff43013c0803c55de36a1577320f23d76f68279d321219dece1017eb9098', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 05:21:31', '2025-10-03 05:21:31', '2026-10-03 05:21:31'),
('f53d9694341820cd7c46651d0725ff4bf457c4276e2fbff48edc3a89258273e3d672dffe454a9b19', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 04:39:21', '2025-09-25 04:39:21', '2026-09-25 04:39:21'),
('f55509b83cb34b20e0e626b81cbca2ff215860d9ac3d474255009aa3e32bd156bf61b17a062bacdd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-16 18:22:11', '2026-01-16 18:22:11', '2027-01-16 18:22:11'),
('f5a7a10b8e6b547a505a1536295cb68d0b7965d114a749268c52b28c7158acb3705e6db853b79114', 288, 1, 'LaravelPassportToken', '[]', 1, '2025-11-28 11:10:09', '2025-11-28 15:02:08', '2026-11-28 11:10:09'),
('f5acf0a3b41599c575a42c6bb69c41a1b6df0de0f9d793163c6de4777d3b3a2d90cba5e6d17707c1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:21:33', '2025-11-05 13:21:33', '2026-11-05 13:21:33'),
('f5c5219668e808e4c0c10adb8b32b83a94db8aa359bc025fcb8c349cb60e5d7f4f388e5752775685', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 06:10:43', '2025-09-09 06:10:43', '2026-09-09 06:10:43'),
('f5ca90b96509557e7560813f99570f19b5a2ddff148bdc04c017feb447a1e67959403f102d1c14d5', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-16 17:07:38', '2026-02-16 17:07:38', '2027-02-16 17:07:38'),
('f5cd4b3a0e7201cfd8ded3635197eea0aa1066b27e593ec3fb0cc3395d25c4918e369c1ea4a55468', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-26 18:33:39', '2025-11-26 18:33:39', '2026-11-26 18:33:39'),
('f5d27cfb2ca4f27f8bb642f520107e8dda4ebf962eb4e52e8425e1f82e35e4df2d91033912e23cdb', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-13 20:47:05', '2026-05-13 20:47:05', '2027-05-13 20:47:05'),
('f5edc8a972fb815a28b27cf21a46c679719c975cf1826269b73e4c1e32880eec33d43f17adad916f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-01 16:19:14', '2026-04-01 16:19:14', '2027-04-01 16:19:14'),
('f5f39e78bd0f13899e5747f8e4fe664d2693fa21409aa72d25b8334d0c34f581526a2d127c42e098', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-11 18:38:57', '2025-06-11 18:38:57', '2026-06-11 11:38:57'),
('f666f84e8a89dd6d4ae5917e019dc8fad0ec3dfb9d3ee8cbce59eafc2731730a64e8dad5d6db56e0', 23, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 06:00:47', '2025-07-17 06:00:47', '2026-07-17 06:00:47'),
('f6709e0e5311c1842c38a2518c5ad8a0975492350fc60055fd3abcba131b47467609b2724ff70c7c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-06 12:21:11', '2026-04-06 12:21:11', '2027-04-06 12:21:11'),
('f67be900e7ae43ba553dbc2a544f9121e71afa12ed132669d929bd4d3c12381ec8646af420aa6041', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:35:53', '2025-09-01 09:35:53', '2026-09-01 09:35:53'),
('f6ae58b022c357b41638fcd019274ca3c6a20c79e14956e0630ff9d2e4254c786bc0aaf049143c09', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 13:53:19', '2025-11-06 13:53:19', '2026-11-06 13:53:19'),
('f6dd79c1136d2013a318e29c1399e3d71cb7eb3d06e1a1f3c14a598617cea61c0e84fd13d8c74969', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 09:50:41', '2025-09-22 09:50:41', '2026-09-22 09:50:41'),
('f6e781e67238d8b3be49882bb2ab6e29a8fe5c66f0a8812cb8f320c3b9d03b144e42b57be7214781', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-27 10:49:16', '2026-03-27 10:49:16', '2027-03-27 10:49:16'),
('f6ff8a785db47562b0ad2bb95086354132e810ccfa75beab2cf203aad068046169a5d6185d623393', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-24 11:15:23', '2025-09-24 11:15:23', '2026-09-24 11:15:23'),
('f7061328a9b77442fa03ffa8c8b74dbcb1f6d30b579ce27512362944779ad0007d839bafada417b4', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-16 16:11:31', '2026-01-16 16:11:31', '2027-01-16 16:11:31'),
('f706a13392a6628339317d3ae747d28dcf2cc7b13687e3797b0bdfd47b60f7e25a1af08838638bb2', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-27 16:16:46', '2026-04-27 16:16:46', '2027-04-27 16:16:46'),
('f709df7ef3273c497fe7e6002e5597d88a2c437dd101be6964e75dd264c82e8188ba8dfcc7a16dc2', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-12 12:16:18', '2025-11-12 12:16:18', '2026-11-12 12:16:18'),
('f729bf18c615435072ed1da62cbcc9890d66a1a2c258ba553b0f68279e8bb9aaf69957bb90e11a07', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-27 15:00:05', '2026-04-27 15:00:05', '2027-04-27 15:00:05'),
('f73aab7194c21cac9a0ce7a1abe0be1984b1e406155ad282ee923a3c758747afd19793c10a5c987a', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-25 14:40:29', '2025-12-25 14:40:29', '2026-12-25 14:40:29'),
('f74b810b474151ac92897978d2612306825ae33b86008c133d7de9763c48229c92eb7841cf00c92f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 09:55:55', '2025-11-07 09:55:55', '2026-11-07 09:55:55'),
('f772da76e029a8db64afcd1880eff122b319410101cf375f54cb10258b3c25ed607f3463548dd5fc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-16 14:51:37', '2026-02-16 14:51:37', '2027-02-16 14:51:37'),
('f77bc94d2216dc8c17f4d8fc2823f21f9cb57122d8d8659706b39c461dbadc0abd5862fbc8f4ccd1', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-25 09:36:02', '2026-03-25 09:36:02', '2027-03-25 09:36:02'),
('f7b748e7a762b5acf6e30cdd4e16aa412401413cab502194dc673d45a9993b9603ab6b0a5c0341e5', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 13:50:09', '2025-11-11 13:50:09', '2026-11-11 13:50:09'),
('f7c2c71beb0ec3577fcd5bbb75afb2be201e18467cd2cc3e250338f0dcf629a99b72f6c3962e4f4b', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-03 11:36:10', '2025-09-03 11:36:10', '2026-09-03 11:36:10'),
('f7c96dbcc3b5b416ac9ece65ede64e8fcb1c6fa24c3828e3fda3ac006926dfd5a88c772332274b34', 217, 1, 'LaravelPassportToken', '[]', 0, '2025-11-03 12:15:58', '2025-11-03 12:15:58', '2026-11-03 12:15:58'),
('f848380f39eccb1834d32c486478cb8f277580a57a102931af4c75ae7d1074a601ca29942c252f20', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 12:04:40', '2025-11-07 12:04:40', '2026-11-07 12:04:40'),
('f8dd04a5a105d5b8a0a165d931f3ca124cc9de7396834d2da6ac0bba9c1e364ce7f98e3eedeb43a9', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-04 05:02:13', '2025-09-04 05:02:13', '2026-09-04 05:02:13'),
('f999179c52fdde26621545a07bb423e8bd011cac090aabc9075d3695869d4f6be895c6b6250110b6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-04 18:09:29', '2025-11-04 18:09:29', '2026-11-04 18:09:29'),
('f9a63c0c6d5a1719bdb9044653ec5d4b585c75dfa67d384a21a2b5724cce7bfc7f87937b350da4a1', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 11:20:44', '2025-11-13 11:20:44', '2026-11-13 11:20:44'),
('f9e31326d78f0bdbe0c2e31cdefd2776f3fe7c73636bc8e02589babef8ccc27cb8263aa46a9c6f3c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-04 14:52:34', '2026-05-04 14:52:34', '2027-05-04 14:52:34'),
('f9e772d6b32499a80fa8b282573f9e9d9a71d7087480f2aad563eeb87dc4df10adc84308d1c8170d', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-18 10:16:50', '2026-02-18 10:16:50', '2027-02-18 10:16:50'),
('fa07a341b221fc68ae73c41dcd0787ef55cf9be3741b4ce2372efa8af2b61f894215fc4cc1d1544e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 12:02:20', '2025-09-22 12:02:20', '2026-09-22 12:02:20'),
('fa16b4af1161c98e452f8f57db106d3f2399290508d02252e3bf2080dc3703568835236a4d94e679', 1, 1, 'LaravelPassportToken', '[]', 1, '2025-11-28 18:13:05', '2025-11-28 18:23:10', '2026-11-28 18:13:05'),
('fa247b365556abed47328434780849440d8899dd99c481208df80aa197a9376933218bf444cc491e', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 12:38:40', '2026-03-30 12:38:40', '2027-03-30 12:38:40'),
('fa3f66a648d1d1ad13bfef3dcac2771f0571c09eb9e47413999260098fc1865ceba26547d8dc6af7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 10:28:36', '2025-09-01 10:28:36', '2026-09-01 10:28:36'),
('fa47c641b087293433ad194860af6813f93abba91cc77eddad2a754c8175931985f761e36f151f55', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-07 14:37:47', '2025-11-07 14:37:47', '2026-11-07 14:37:47'),
('fa49801c2b892b4dfc2fbb69b472bc30347fe82f2b14834c7bafbd7408c499fa5a5cf62462b17d53', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-27 17:39:48', '2026-01-27 17:39:48', '2027-01-27 17:39:48'),
('fa5dcd303d9d81c9deb727c0d93425f2d9630bed469a31742860cc40ad5fcea15de6dfc5c9e68fcd', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-11 13:08:51', '2026-03-11 13:08:51', '2027-03-11 13:08:51'),
('fa80ecff3d66bfc913c610c67d0eab8679a0a7d3f7222b0ff00d8920afcefe3a644639fead326be7', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-31 11:31:36', '2025-05-31 11:31:36', '2026-05-31 04:31:36'),
('fa8274e255c8813548d7a407a7b351f1071824f2fdee8d3ebb02cd14671e48bf5bf3e1e7ccf63208', 224, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 11:35:45', '2025-11-05 11:35:45', '2026-11-05 11:35:45'),
('fa88588d2d37e621d6c4e1cbbf9fed4803b44633e9c2adaaf6cdb8bac5a196ae7beb297315d5f493', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-22 11:54:40', '2025-09-22 11:54:40', '2026-09-22 11:54:40'),
('fa8c020c333d3d79117de891a351249762e30664ebf5f5b599bca2e0fff9eed980562789e3a44460', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-31 00:15:41', '2026-01-31 00:15:41', '2027-01-31 00:15:41'),
('fa94283d34a141c557482900ace50f8a4ce44bda00d2a9a9ff5ca57e0de2705b0d4743e2fa423e6e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-03 12:54:20', '2025-10-03 12:54:20', '2026-10-03 12:54:20'),
('fa94e0d2f4ab040031bb72b7b30202def7209ceaddd6884ebea8a63129f5598a5da230e299754abd', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:37:28', '2025-09-01 09:37:28', '2026-09-01 09:37:28'),
('fa990f4356bb712dce033f65f9e00e2cea2165de45f18e35dde6b30b022245da574a9e5513f444d5', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 10:02:30', '2025-09-18 10:02:30', '2026-09-18 10:02:30'),
('fac799d1f53647bcf0db761fe446379470907d22e9ce93cc262826eae944994d665379ec28fc71bc', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-19 13:33:30', '2026-03-19 13:33:30', '2027-03-19 13:33:30'),
('fb1c3f67750ad4d686cc096bbb0fa08751cd718b56a63ef55516e7dea1be9b9eac9a80b19f9e3358', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-17 06:14:24', '2025-09-17 06:14:24', '2026-09-17 06:14:24'),
('fb1d3f0c338443220c3f42afaa71cf37a40c8366050c1788c90b61726ab1165a429d9f040ae00d12', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-11 11:11:01', '2025-11-11 11:11:01', '2026-11-11 11:11:01'),
('fb1d800a7bcfdb7b4e5db70d5976721b6b41ff69c79322667142fdab6c58f6fd65c39de08bf6a88d', 21, 1, 'LaravelPassportToken', '[]', 0, '2025-07-17 05:14:37', '2025-07-17 05:14:37', '2026-07-17 05:14:37'),
('fb264c5f3f589f99f6e8620a114927bdd9a21b7bcfbac749feac97cadb34f857fdaa2b2c681d6dc4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-18 10:55:34', '2025-09-18 10:55:34', '2026-09-18 10:55:34'),
('fb7bd11f98dfd777c3d8085f3a393efb30ee41fc2542e5468478643a507536867e3c896c546431b6', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-04 07:46:03', '2025-09-04 07:46:03', '2026-09-04 07:46:03'),
('fb918e822a96cf324b5262d18e99b7a618eb74c3ed5e8435154deccbd9b981611a5566ae1ee4ae4c', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-06-02 16:43:47', '2025-06-02 16:43:47', '2026-06-02 09:43:47'),
('fbe16cad2cdb8d235b42c3d6bd9b585697e5e34cedfc731bde3057986ce31f4e7bbcc232457f8da2', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-28 05:23:55', '2025-08-28 05:23:55', '2026-08-28 05:23:55'),
('fbfc9bdb1641897c93f8e6a8996ae96daf45236be686228509a5f56d1310cdc095b44eafb804c69f', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-04-10 13:21:30', '2026-04-10 13:21:30', '2027-04-10 13:21:30'),
('fc23ae6856d507db7379990bff580dded9470e9b63ef8297e92601e406bcbd5084960eea084b8c76', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 13:14:17', '2025-11-13 13:14:17', '2026-11-13 13:14:17'),
('fc39dd8cb3b6599902332e97bbe433a1ca689ba52120451883faf025bca616a6d54956735edede65', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-16 07:26:59', '2025-10-16 07:27:00', '2026-10-16 07:26:59'),
('fc56332843666f58c38b3bed21a199bb5915c1ef5b13904306ece54a046040f5ce3942cac64d7bb3', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-08 09:55:03', '2025-11-08 09:55:03', '2026-11-08 09:55:03'),
('fc59f02cdb773e7fa6d7d82eaa56773d2e05ef40d081cb30447ca9477fe9b011d24b9edbf0adfee1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 05:09:57', '2025-09-01 05:09:57', '2026-09-01 05:09:57'),
('fc7de19e7dde9f156adf97acad243e6dae648dc82618571ec383e1fa73ca029c1ba1f1f078bdc8f4', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-25 15:18:45', '2025-11-25 15:18:45', '2026-11-25 15:18:45'),
('fcede7e33e5f76b2b787caf1947c5a3c1a3927089d349c63f2bcf4c09e5eb553b54ae8cb58dbeeed', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-29 04:31:03', '2025-10-29 04:31:03', '2026-10-29 04:31:03'),
('fd081bace44bdc21812a983eba0f60cc0d1a96dd050d1394c1df8686758ea70c6405502a00339824', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:34:39', '2025-09-01 09:34:39', '2026-09-01 09:34:39'),
('fd275e31965e1d15e74cd84d7cd429f69eab7139e522d285a9cf62f800aac9d6b69190b4f2471832', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-07-01 11:46:31', '2025-07-01 11:46:31', '2026-07-01 11:46:31'),
('fd6658d1dd086e72bf0d476c72511f6cdcf7dbf47a3a67057baadfad6129f4002201ba15689d6932', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-20 10:43:21', '2026-03-20 10:43:21', '2027-03-20 10:43:21'),
('fd6e6ac4cb304997112f4dabc7f0ea08002725bb9fd8fe827ba9537c6986136fe19df35b6294da42', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-01 12:04:02', '2026-01-01 12:04:02', '2027-01-01 12:04:02'),
('fd75646bd2bb6652afac44bc44725a35beb9afc88fa68a7c47364ab584a4001a987f52bdb42f0e5c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-01-06 17:41:58', '2026-01-06 17:41:58', '2027-01-06 17:41:58'),
('fd8f90c8329208fe91df9b976fbefabd01a7fd03184ed8e8e308fcdc2536e0476520d75b8a725cd8', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-11-06 09:38:24', '2025-11-06 09:38:24', '2026-11-06 09:38:24'),
('fd98c079af9f2763e62aac6c7e22b8956ab4273dc9bca76d6fb87a284b2bad503041ffe9d1cacbff', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-24 18:08:42', '2025-12-24 18:08:42', '2026-12-24 18:08:42'),
('fdbe0fdcfad6ffe6ebe6aac94aeb8a55613f9aef73a4e775f1fe188124fb08665a24025b2aed3225', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-08-06 12:57:20', '2025-08-06 12:57:20', '2026-08-06 12:57:20'),
('fdc69855b5b712de9ad4567ef7b7e97f9000fa0b5a363fff0fff81afa1ddb6d5c58ad199c1b258cf', 248, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 12:07:35', '2025-11-13 12:07:35', '2026-11-13 12:07:35'),
('fde45446bdf9be9a508a25223cde42cd49f0f4f58df75e5408d132b41b52024eb9a3cef01d5924da', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-01 09:34:45', '2025-09-01 09:34:45', '2026-09-01 09:34:45'),
('fdf167eb8b3bb0f248b94ef5e712975a0191751459b039ed0e74dab39b3b3e8e290617e07590ca53', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-09 04:47:15', '2025-09-09 04:47:15', '2026-09-09 04:47:15'),
('fe38ff540f5579064998495b6c685ef1ece962328e294c2dbafeec2dd41898b324e53995e69a125d', 214, 1, 'LaravelPassportToken', '[]', 0, '2025-11-05 13:30:37', '2025-11-05 13:30:37', '2026-11-05 13:30:37'),
('fe3bce90344c26b183b71c021181af844f995c8f8dfb784051654a426882cddf42831c01f4ea8b02', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-17 12:16:37', '2026-03-17 12:16:37', '2027-03-17 12:16:37'),
('fe4e0871d5fd6869b55869eeff0e00995fa0ee8c0dff44d4362ffdca2a4c05803fe4ad9cb4d6bda6', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-02 15:42:06', '2026-03-02 15:42:06', '2027-03-02 15:42:06'),
('fe6f296a82af094ff9c710743cec9e1cc9d5a699b79bf5ddb997b5853975104f5c8ab2f380572887', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-05-25 16:53:48', '2026-05-25 16:53:48', '2027-05-25 16:53:48'),
('fe782ff874806fa5002448b4b1e9509993bca295c0e21b4c92ddc2444b94dc800f3defd030d61e51', 27, 1, 'LaravelPassportToken', '[]', 0, '2025-09-16 10:18:16', '2025-09-16 10:18:16', '2026-09-16 10:18:16'),
('feab413c0fef9f049d267ba0db07af06a79fc0c025e5ceec5ca8163a9c5185282474a1594cc12b93', 184, 1, 'LaravelPassportToken', '[]', 0, '2025-09-23 05:05:43', '2025-09-23 05:05:43', '2026-09-23 05:05:43'),
('febd2321ee80779348050d4e7f06dd052c9169fd13712a70a183381a8110ea05e0acdee9209c097e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-05-30 19:31:17', '2025-05-30 19:31:17', '2026-05-30 12:31:17'),
('ff2c0499fce021df22fb67439f48179e890e9bc473e5b09d558f3ef17172d90dac79cc93b250827c', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-02-10 18:19:16', '2026-02-10 18:19:16', '2027-02-10 18:19:16'),
('ff2d8ae712e02a3a51ae1037b5a522f2e01d7b8b618525334c44bae922b1b3437315eb1c05ce8adc', 215, 1, 'LaravelPassportToken', '[]', 0, '2025-11-13 14:37:00', '2025-11-13 14:37:00', '2026-11-13 14:37:00'),
('ff2f43cadd4daf20cce5c18aeb7ad5653bb2e6c822cfa28477c368bbb90ff33776789d1e61cf72c0', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-25 11:52:49', '2025-09-25 11:52:49', '2026-09-25 11:52:49'),
('ff3264e6277a0448a17e4c12176cbf2872afdf2d8b87303d9d954372742c5456f8dba5b01efb4712', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-12-17 16:14:05', '2025-12-17 16:14:05', '2026-12-17 16:14:05'),
('ff5b79c5c317bad7bb233a790d255d9d415083ab43c2dd8692d75d188897cc6a47fa50a2fae1ad0f', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-31 04:23:08', '2025-10-31 04:23:08', '2026-10-31 04:23:08'),
('ff6ccda856d50ccf02e20f45fcac3dd49de0bc1082b427c85843e87822dfd04e7e27a2d2ca125166', 1, 1, 'LaravelPassportToken', '[]', 0, '2026-03-30 10:05:26', '2026-03-30 10:05:26', '2027-03-30 10:05:26'),
('ff804da17246a386dfa79bfbcd072265cd0b86b69f14bb7814680b89b000397ac4b24f9062b791a1', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-10-09 04:38:50', '2025-10-09 04:38:50', '2026-10-09 04:38:50'),
('ffd834e86a2bf395d29a1a542c3eb92d20d3bf8e68e7c63fc5979dadca01a6cad7de5a91e4c9c33e', 1, 1, 'LaravelPassportToken', '[]', 0, '2025-09-13 04:11:13', '2025-09-13 04:11:13', '2026-09-13 04:11:13');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Laravel Personal Access Client', 'szou7u7aRvAhcAQrz1xxyXuGfhAOw6QuDkEW806F', NULL, 'http://localhost', 1, 0, 0, '2025-04-04 00:38:53', '2025-04-04 00:38:53'),
(2, NULL, 'Laravel Password Grant Client', 'eDYX3Fzad0V0WUcjt3NXRHXydnx3GIZoIOuUkyiA', 'users', 'http://localhost', 0, 1, 0, '2025-04-04 00:38:53', '2025-04-04 00:38:53');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-04-04 00:38:53', '2025-04-04 00:38:53');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `discount` bigint(20) UNSIGNED DEFAULT NULL,
  `tax_id` text DEFAULT NULL,
  `shipping` varchar(255) NOT NULL DEFAULT '0',
  `gst_option` varchar(255) NOT NULL DEFAULT 'without_gst',
  `total_amount` decimal(10,2) DEFAULT NULL,
  `tds_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tds_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `delivery_status` enum('pending','partially_delivered','delivered') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `remaining_amount` decimal(11,2) DEFAULT 0.00,
  `order_invoice` varchar(255) DEFAULT NULL,
  `quotation_status` varchar(255) DEFAULT NULL,
  `approved_status` varchar(255) DEFAULT 'pending',
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `remarks` text DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `is_approved` tinyint(4) NOT NULL DEFAULT 0,
  `approved_by` bigint(20) DEFAULT NULL,
  `approved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `discount`, `tax_id`, `shipping`, `gst_option`, `total_amount`, `tds_percentage`, `tds_amount`, `payment_status`, `delivery_status`, `payment_method`, `remaining_amount`, `order_invoice`, `quotation_status`, `approved_status`, `approval_status`, `remarks`, `branch_id`, `created_by`, `isDeleted`, `is_approved`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, '2026042200001', 3, 0, NULL, '100', 'with_gst', 2342.00, 0.00, 0.00, 'pending', 'pending', 'pending', 2342.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 1, 0, NULL, '2026-04-03 06:51:50', '2026-04-03 12:21:50', '2026-05-26 12:59:37'),
(4, '2026040600002', 3, 0, '\"[]\"', '100', 'with_gst', 23836.00, 0.00, 0.00, 'pending', 'pending', 'pending', 23836.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 1, 0, NULL, '2026-04-06 09:17:03', '2026-04-06 14:47:03', '2026-05-26 12:59:47'),
(5, '2026041300005', 3, 0, NULL, '500', 'without_gst', 1950.00, 0.00, 0.00, 'completed', 'pending', 'cash', 0.00, NULL, 'sales', 'pending', 'approved', 'gtgthy6t', 1, 1, 1, 0, NULL, '2026-04-13 12:32:00', '2026-04-13 18:02:00', '2026-05-26 13:00:04'),
(6, '2026041400006', 3, 0, NULL, '500', 'without_gst', 2600.00, 0.00, 0.00, 'pending', 'pending', 'pending', 2600.00, NULL, 'sales', 'pending', 'approved', 'ghht6yh', 1, 1, 1, 0, NULL, '2026-04-13 12:32:29', '2026-04-13 18:02:29', '2026-05-26 13:00:11'),
(7, '2026041400007', 3, 0, NULL, '0', 'with_gst', 1276.00, 0.00, 0.00, 'completed', 'pending', 'Online', 0.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 1, 0, NULL, '2026-04-14 10:37:26', '2026-04-14 16:07:26', '2026-05-26 13:00:23'),
(8, '2026041500008', 3, 0, NULL, '0', 'without_gst', 1350.00, 0.00, 0.00, 'completed', 'pending', 'scan', 0.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 0, 0, NULL, '2026-04-15 12:01:38', '2026-04-15 17:31:38', '2026-04-15 17:31:38'),
(9, '2026041500009', 3, 0, NULL, '0', 'without_gst', 73920.00, 0.00, 0.00, 'completed', 'pending', 'Cash', 0.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 0, 0, NULL, '2026-04-15 14:54:25', '2026-04-15 20:24:25', '2026-05-26 13:32:43'),
(10, '2026042200010', 3, 0, '\"[]\"', '100', 'without_gst', 1525.00, 0.00, 0.00, 'pending', 'pending', 'pending', 1525.00, NULL, 'sales', 'pending', 'approved', 'fghgfhg', 1, 1, 1, 0, NULL, '2026-04-22 12:54:55', '2026-04-22 18:24:55', '2026-04-22 18:40:12'),
(11, '2026042400011', 3, 0, '\"[]\"', '100', 'with_gst', 1129.00, 0.00, 0.00, 'completed', 'pending', 'Cash', 0.00, NULL, 'sales', 'pending', 'approved', 'Remarks (Optional)', 1, 1, 0, 0, NULL, '2026-04-24 09:51:52', '2026-04-24 15:21:52', '2026-05-26 13:32:43'),
(12, 'Q-12', 4, 0, NULL, '0', 'without_gst', 375.00, 0.00, 0.00, 'pending', 'pending', 'pending', 375.00, NULL, 'quotation', 'pending', 'approved', NULL, 1, 1, 0, 0, NULL, '2026-04-24 09:55:13', '2026-04-24 15:25:13', '2026-04-24 15:25:13'),
(13, '2026042400013', 3, 0, NULL, '0', 'without_gst', 700.00, 0.00, 0.00, 'pending', 'pending', 'pending', 700.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 1, 0, NULL, '2026-04-24 10:36:40', '2026-04-24 16:06:40', '2026-04-24 16:08:13'),
(14, '2026042400014', 3, 0, NULL, '0', 'with_gst', 4293.00, 0.00, 0.00, 'pending', 'pending', 'pending', 4293.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 1, 0, NULL, '2026-04-24 10:37:40', '2026-04-24 16:07:40', '2026-04-24 16:08:18'),
(15, '2026042400015', 4, 0, '\"[]\"', '0', 'without_gst', 1600.00, 0.00, 0.00, 'pending', 'pending', 'pending', 1600.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 0, 0, NULL, '2026-04-24 10:37:54', '2026-04-24 16:07:54', '2026-04-24 16:10:37'),
(16, '2026042400016', 4, 0, NULL, '0', 'without_gst', 1900.00, 0.00, 0.00, 'partially', 'pending', 'Cash', 1500.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 1, 0, NULL, '2026-04-24 10:39:28', '2026-04-24 16:09:28', '2026-05-26 12:59:57'),
(17, '2026042400017', 4, 0, NULL, '0', 'without_gst', 1600.00, 0.00, 0.00, 'pending', 'pending', 'Cash', 1600.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 0, 0, NULL, '2026-04-24 10:39:42', '2026-04-24 16:09:42', '2026-05-01 18:20:55'),
(18, '2026042400018', 4, 0, NULL, '0', 'without_gst', 500.00, 0.00, 0.00, 'partially', 'pending', 'Cash', 300.00, NULL, 'sales', 'pending', 'approved', 'remark...', 1, 1, 1, 0, NULL, '2026-04-24 12:27:50', '2026-04-24 17:57:50', '2026-04-24 18:04:12'),
(19, '2026042400019', 4, 0, NULL, '0', 'without_gst', 250.00, 0.00, 0.00, 'partially', 'pending', 'Cash', 200.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 0, 0, NULL, '2026-04-24 12:35:28', '2026-04-24 18:05:28', '2026-05-01 18:27:35'),
(20, '2026042400020', 3, 0, NULL, '100', 'with_gst', 1258.00, 10.00, 139.81, 'completed', 'pending', 'Cash', 0.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 0, 0, NULL, '2026-04-24 12:47:31', '2026-04-24 18:17:31', '2026-05-26 13:32:43'),
(21, '2026042700021', 4, 0, '\"[]\"', '100', 'with_gst', 543.00, 5.00, 28.59, 'partially', 'pending', 'Cash', 500.00, NULL, 'sales', 'pending', 'approved', 'remarksss', 1, 1, 0, 0, NULL, '2026-04-27 11:06:31', '2026-04-25 16:36:31', '2026-05-06 10:48:56'),
(22, '1', 15, 0, '\"[]\"', '100', 'with_gst', 1614.00, 10.00, 179.30, 'partially', 'pending', 'Cash', 1600.00, NULL, 'sales', 'pending', 'approved', NULL, 2, 1, 0, 0, NULL, '2026-04-27 12:05:05', '2026-04-27 17:35:05', '2026-04-27 18:03:40'),
(23, '2', 11, 0, '\"[]\"', '100', 'with_gst', 2229.00, 10.00, 247.63, 'partially', 'pending', 'Cash', 2200.00, NULL, 'sales', 'pending', 'approved', 'remarks', 2, 1, 1, 0, NULL, '2026-04-27 12:37:29', '2026-04-27 18:07:29', '2026-04-27 18:18:31'),
(24, '2026042800024', 3, 0, '\"[]\"', '0', 'without_gst', 526.00, 0.00, 0.00, 'completed', 'pending', 'Cash', 0.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 0, 0, NULL, '2026-04-28 11:32:09', '2026-05-01 17:02:09', '2026-05-26 13:32:43'),
(25, '1', 3, 0, NULL, '0', 'without_gst', 2626.00, 0.00, 0.00, 'pending', 'pending', 'pending', 2626.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 1, 0, NULL, '2026-05-04 06:25:28', '2026-05-04 11:55:28', '2026-05-05 12:13:14'),
(26, '3', 11, 0, NULL, '0', 'without_gst', 1501.00, 0.00, 0.00, 'partially', 'pending', 'online', 1401.02, NULL, 'sales', 'pending', 'approved', NULL, 2, 1, 1, 0, NULL, '2026-05-04 11:46:52', '2026-05-04 17:16:52', '2026-05-04 17:20:49'),
(27, '4', 11, 0, NULL, '0', 'without_gst', 2500.00, 0.00, 0.00, 'partially', 'pending', 'Cash', 800.00, NULL, 'sales', 'pending', 'approved', NULL, 2, 1, 0, 0, NULL, '2026-05-04 11:51:09', '2026-05-04 17:21:09', '2026-05-04 18:39:38'),
(28, '2026050600028', 6, 0, '\"[]\"', '100', 'without_gst', 2324.00, 2.00, 47.42, 'pending', 'pending', 'pending', 2324.00, NULL, 'sales', 'pending', 'approved', 'Remarks (Optional)', 1, 1, 0, 0, NULL, '2026-05-05 06:52:08', '2026-05-05 12:22:08', '2026-05-06 15:05:39'),
(29, '2026050600029', 3, 0, '\"[]\"', '0', 'without_gst', 200.00, 0.00, 0.00, 'completed', 'pending', 'Cash', 0.00, NULL, 'sales', 'pending', 'approved', 'remarks', 1, 1, 0, 0, NULL, '2026-05-06 09:36:14', '2026-05-06 15:06:14', '2026-05-26 13:32:43'),
(30, '2026050600030', 3, 0, NULL, '0', 'without_gst', 200.00, 0.00, 0.00, 'completed', 'pending', 'Cash', 0.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 0, 0, NULL, '2026-05-06 13:09:49', '2026-05-06 18:39:49', '2026-05-06 18:51:37'),
(31, '2026050700031', 4, 0, NULL, '0', 'without_gst', 270.00, 0.00, 0.00, 'partially', 'pending', 'Cash', 200.00, NULL, 'sales', 'pending', 'approved', NULL, 1, 1, 0, 0, NULL, '2026-05-07 06:43:39', '2026-05-07 12:13:39', '2026-05-07 12:15:10'),
(32, '5', 15, 0, '\"[]\"', '100', 'with_gst', 4232.00, 0.00, 0.00, 'partially', 'pending', 'pending', 4000.00, NULL, 'sales', 'pending', 'approved', 'remaks', 2, 1, 0, 0, NULL, '2026-05-08 04:19:15', '2026-05-08 09:49:15', '2026-05-08 10:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_gst_details` text DEFAULT NULL,
  `product_gst_total` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(10,2) UNSIGNED NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `user_id`, `product_id`, `product_gst_details`, `product_gst_total`, `category_id`, `price`, `discount_percentage`, `discount_amount`, `quantity`, `total_amount`, `branch_id`, `created_by`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1, '[{\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\",\"tax_amount\":63},{\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\",\"tax_amount\":63}]', '126', 1, 350.00, 0.00, 0.00, 2.00, 700.00, 1, 1, 1, '2026-04-03 12:21:50', '2026-05-26 12:59:37'),
(2, 1, 3, 2, '[{\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\",\"tax_amount\":108},{\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\",\"tax_amount\":108}]', '216', 2, 1200.00, 0.00, 0.00, 1.00, 1200.00, 1, 1, 1, '2026-04-03 12:21:50', '2026-05-26 12:59:37'),
(5, 4, 3, 6, '\"[{\\\"tax_name\\\":\\\"CGST\\\",\\\"tax_rate\\\":\\\"5.00\\\",\\\"tax_amount\\\":150}]\"', '150', 6, 3000.00, 0.00, 0.00, 1.00, 3000.00, 1, 1, 1, '2026-04-08 16:47:34', '2026-05-26 12:59:47'),
(6, 4, 3, 7, '\"[{\\\"tax_name\\\":\\\"CGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":162},{\\\"tax_name\\\":\\\"SGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":162}]\"', '324', 7, 1800.00, 0.00, 0.00, 1.00, 1800.00, 1, 1, 1, '2026-04-08 16:47:34', '2026-05-26 12:59:47'),
(7, 4, 3, 8, '\"[{\\\"tax_name\\\":\\\"CGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":81},{\\\"tax_name\\\":\\\"SGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":81}]\"', '162', 8, 900.00, 0.00, 0.00, 1.00, 900.00, 1, 1, 1, '2026-04-08 16:47:34', '2026-05-26 12:59:47'),
(8, 4, 3, 9, '\"[{\\\"tax_name\\\":\\\"CGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":630},{\\\"tax_name\\\":\\\"SGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":630}]\"', '1260', 9, 7000.00, 0.00, 0.00, 1.00, 7000.00, 1, 1, 1, '2026-04-08 16:47:34', '2026-05-26 12:59:47'),
(9, 4, 3, 10, '\"[{\\\"tax_name\\\":\\\"CGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":495},{\\\"tax_name\\\":\\\"SGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":495}]\"', '990', 10, 5500.00, 0.00, 0.00, 1.00, 5500.00, 1, 1, 1, '2026-04-08 16:47:34', '2026-05-26 12:59:47'),
(10, 4, 3, 11, NULL, '0', 1, 450.00, 10.00, 45.00, 1.00, 405.00, 1, 1, 1, '2026-04-08 16:47:34', '2026-05-26 12:59:47'),
(11, 4, 3, 12, NULL, '0', 1, 250.00, 2.00, 5.00, 1.00, 245.00, 1, 1, 1, '2026-04-08 16:47:34', '2026-05-26 12:59:47'),
(12, 5, 3, 12, '[]', '0', 1, 250.00, 0.00, 0.00, 1.00, 250.00, 1, 1, 1, '2026-04-13 18:02:00', '2026-05-26 13:00:04'),
(13, 6, 3, 13, '[]', '0', 1, 900.00, 0.00, 0.00, 1.00, 900.00, 1, 1, 1, '2026-04-13 18:02:29', '2026-05-26 13:00:11'),
(14, 7, 3, 1, '[{\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\",\"tax_amount\":63},{\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\",\"tax_amount\":63}]', '126', 1, 350.00, 0.00, 0.00, 2.00, 700.00, 1, 1, 1, '2026-04-14 16:07:26', '2026-05-26 13:00:23'),
(15, 7, 3, 11, '[]', '0', 1, 450.00, 0.00, 0.00, 1.00, 450.00, 1, 1, 1, '2026-04-14 16:07:26', '2026-05-26 13:00:23'),
(16, 8, 3, 22, '[]', '0', 11, 100.00, 0.00, 0.00, 10.00, 1000.00, 1, 1, 0, '2026-04-15 17:31:38', '2026-04-15 17:31:38'),
(17, 8, 3, 21, '[]', '0', 11, 350.00, 0.00, 0.00, 1.00, 350.00, 1, 1, 0, '2026-04-15 17:31:38', '2026-04-15 17:31:38'),
(18, 9, 3, 9, '[]', '0', 9, 7000.00, 12.00, 10080.00, 12.00, 73920.00, 1, 1, 0, '2026-04-15 20:24:25', '2026-04-15 20:24:25'),
(25, 10, 3, 1, NULL, '0', 1, 350.00, 0.00, 0.00, 1.50, 525.00, 1, 1, 1, '2026-04-22 18:38:29', '2026-04-22 18:40:12'),
(26, 10, 3, 13, NULL, '0', 1, 900.00, 0.00, 0.00, 1.00, 900.00, 1, 1, 1, '2026-04-22 18:38:29', '2026-04-22 18:40:12'),
(29, 11, 3, 13, '\"[{\\\"tax_name\\\":\\\"SGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":13.5}]\"', '13.5', 1, 100.00, 10.00, 16.35, 1.50, 133.65, 1, 1, 0, '2026-04-24 15:23:50', '2026-04-24 15:23:50'),
(30, 11, 3, 12, NULL, '0', 1, 200.00, 2.00, 18.00, 4.50, 882.00, 1, 1, 0, '2026-04-24 15:23:50', '2026-04-24 15:23:50'),
(31, 12, 4, 12, '[]', '0', 1, 250.00, 0.00, 0.00, 1.50, 375.00, 1, 1, 0, '2026-04-24 15:25:13', '2026-04-24 15:25:13'),
(32, 13, 3, 11, '[]', '0', 1, 450.00, 0.00, 0.00, 1.00, 450.00, 1, 1, 1, '2026-04-24 16:06:40', '2026-04-24 16:08:13'),
(33, 13, 3, 12, '[]', '0', 1, 250.00, 0.00, 0.00, 1.00, 250.00, 1, 1, 1, '2026-04-24 16:06:40', '2026-04-24 16:08:13'),
(34, 14, 3, 11, '[]', '0', 1, 450.00, 0.00, 0.00, 3.00, 1350.00, 1, 1, 1, '2026-04-24 16:07:40', '2026-04-24 16:08:18'),
(35, 14, 3, 13, '[{\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\",\"tax_amount\":243}]', '243', 1, 900.00, 0.00, 0.00, 3.00, 2700.00, 1, 1, 1, '2026-04-24 16:07:40', '2026-04-24 16:08:18'),
(38, 16, 4, 12, '[]', '0', 1, 250.00, 0.00, 0.00, 4.00, 1000.00, 1, 1, 1, '2026-04-24 16:09:28', '2026-05-26 12:59:57'),
(39, 16, 4, 13, '[]', '0', 1, 900.00, 0.00, 0.00, 1.00, 900.00, 1, 1, 1, '2026-04-24 16:09:28', '2026-05-26 12:59:57'),
(40, 17, 4, 11, '[]', '0', 1, 450.00, 0.00, 0.00, 3.00, 1350.00, 1, 1, 0, '2026-04-24 16:09:42', '2026-04-24 16:09:42'),
(41, 17, 4, 12, '[]', '0', 1, 250.00, 0.00, 0.00, 1.00, 250.00, 1, 1, 0, '2026-04-24 16:09:42', '2026-04-24 16:09:42'),
(42, 15, 4, 11, NULL, '0', 1, 450.00, 0.00, 0.00, 3.00, 1350.00, 1, 1, 0, '2026-04-24 16:10:37', '2026-04-24 16:10:37'),
(43, 15, 4, 12, NULL, '0', 1, 250.00, 0.00, 0.00, 1.00, 250.00, 1, 1, 0, '2026-04-24 16:10:37', '2026-04-24 16:10:37'),
(44, 18, 4, 11, '[]', '0', 1, 500.00, 0.00, 0.00, 1.00, 500.00, 1, 1, 1, '2026-04-24 17:57:50', '2026-04-24 18:04:12'),
(45, 19, 4, 12, '[]', '0', 1, 250.00, 0.00, 0.00, 1.00, 250.00, 1, 1, 0, '2026-04-24 18:05:28', '2026-04-24 18:05:28'),
(46, 20, 3, 13, '[{\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\",\"tax_amount\":9}]', '9', 1, 100.00, 10.00, 10.90, 1.00, 98.10, 1, 1, 0, '2026-04-24 18:17:31', '2026-04-24 18:17:31'),
(48, 21, 4, 1, '\"[{\\\"tax_name\\\":\\\"CGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":31.5},{\\\"tax_name\\\":\\\"SGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":31.5}]\"', '63', 1, 350.00, 10.00, 41.30, 1.00, 371.70, 1, 1, 0, '2026-04-27 16:38:29', '2026-04-27 16:38:29'),
(50, 22, 15, 23, '\"[{\\\"tax_name\\\":\\\"CGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":135},{\\\"tax_name\\\":\\\"SGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":135}]\"', '270', 12, 1000.00, 10.00, 177.00, 1.50, 1593.00, 2, 1, 0, '2026-04-27 18:03:27', '2026-04-27 18:03:27'),
(52, 23, 11, 24, '\"[{\\\"tax_name\\\":\\\"IGST\\\",\\\"tax_rate\\\":\\\"5.00\\\",\\\"tax_amount\\\":112.5}]\"', '112.5', 12, 1500.00, 10.00, 236.25, 1.50, 2126.25, 2, 1, 1, '2026-04-27 18:08:31', '2026-04-27 18:18:31'),
(55, 24, 3, 1, NULL, '0', 1, 350.50, 0.00, 0.00, 1.50, 525.75, 1, 1, 0, '2026-05-04 11:31:22', '2026-05-04 11:31:22'),
(56, 25, 3, 1, '[]', '0', 1, 350.00, 0.00, 0.00, 3.00, 1050.00, 1, 1, 1, '2026-05-04 11:55:28', '2026-05-05 12:13:14'),
(57, 25, 3, 12, '[]', '0', 1, 250.50, 0.00, 0.00, 1.50, 375.75, 1, 1, 1, '2026-05-04 11:55:28', '2026-05-05 12:13:14'),
(58, 26, 11, 23, '[]', '0', 12, 1000.50, 0.00, 0.00, 1.50, 1500.75, 2, 1, 1, '2026-05-04 17:16:52', '2026-05-04 17:20:49'),
(59, 27, 11, 23, '[]', '0', 12, 1000.00, 0.00, 0.00, 2.50, 2500.00, 2, 1, 0, '2026-05-04 17:21:09', '2026-05-04 17:21:09'),
(64, 28, 6, 1, NULL, '0', 1, 350.00, 10.00, 70.00, 2.00, 630.00, 1, 1, 0, '2026-05-05 12:25:34', '2026-05-05 12:25:34'),
(65, 28, 6, 11, NULL, '0', 1, 450.00, 2.00, 9.00, 1.00, 441.00, 1, 1, 0, '2026-05-05 12:25:34', '2026-05-05 12:25:34'),
(69, 29, 3, 20, NULL, '0', 11, 200.00, 0.00, 0.00, 1.00, 200.00, 1, 1, 0, '2026-05-06 15:08:01', '2026-05-06 15:08:01'),
(70, 30, 3, 20, '[]', '0', 11, 200.00, 0.00, 0.00, 1.00, 200.00, 1, 1, 0, '2026-05-06 18:39:49', '2026-05-06 18:39:49'),
(71, 31, 4, 19, '[]', '0', 11, 100.00, 10.00, 30.00, 3.00, 270.00, 1, 1, 0, '2026-05-07 12:13:39', '2026-05-07 12:13:39'),
(79, 32, 15, 23, '\"[{\\\"tax_name\\\":\\\"CGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":315.17324999999994},{\\\"tax_name\\\":\\\"SGST\\\",\\\"tax_rate\\\":\\\"9.00\\\",\\\"tax_amount\\\":315.17324999999994}]\"', '630.3465', 12, 1000.55, 0.00, 0.00, 3.50, 4132.27, 2, 1, 0, '2026-05-08 10:10:53', '2026-05-08 10:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_store`
--

CREATE TABLE `payment_store` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `custom_invoice_id` int(11) DEFAULT NULL,
  `payment_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_date` date DEFAULT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `cash_amount` varchar(255) DEFAULT NULL,
  `upi_amount` varchar(255) DEFAULT NULL,
  `emi_month` varchar(255) DEFAULT NULL,
  `remaining_amount` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `isDeleted` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_store`
--

INSERT INTO `payment_store` (`id`, `user_id`, `order_id`, `purchase_id`, `custom_invoice_id`, `payment_amount`, `payment_date`, `payment_type`, `payment_method`, `cash_amount`, `upi_amount`, `emi_month`, `remaining_amount`, `status`, `remarks`, `bank_id`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 5, NULL, NULL, 1, 450.00, '2026-04-03', 'full', 'Cash', '450', '0', NULL, '0', 'debit', NULL, NULL, 1, '2026-04-03 15:48:08', '2026-05-19 18:31:22'),
(2, 4, NULL, NULL, 2, 315.00, '2026-04-03', 'full', 'Cash', '315', '0', NULL, '0', 'credit', NULL, NULL, 0, '2026-04-03 15:48:17', '2026-04-03 15:48:17'),
(3, 7, NULL, NULL, 3, 450.00, '2026-04-03', 'full', 'Cash', '450', '0', NULL, '0', 'debit', NULL, NULL, 1, '2026-04-03 15:48:24', '2026-05-01 10:53:04'),
(4, 1, NULL, 2, NULL, 181.00, '2026-04-06', 'fully', 'cash', '0', '0', '1', '0', 'debit', NULL, NULL, 0, '2026-04-06 12:37:17', '2026-04-06 12:37:17'),
(5, 3, 5, NULL, NULL, 1950.00, '2026-04-13', 'full', 'cash', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-04-13 18:02:00', '2026-04-13 18:02:00'),
(6, 3, 8, NULL, NULL, 1350.00, '2026-04-15', 'full', 'scan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-04-15 17:31:38', '2026-04-15 17:31:38'),
(7, 1, NULL, 3, NULL, 600.00, '2026-04-15', 'partially', 'cash', '0', '0', '1', '2000', 'debit', NULL, NULL, 0, '2026-04-15 17:33:59', '2026-04-15 17:33:59'),
(8, 1, 7, NULL, 0, 1276.00, '2026-04-15', 'fully', 'online', NULL, NULL, '1', '0', 'credit', NULL, 1, 0, '2026-04-15 17:46:58', '2026-04-15 17:46:58'),
(9, 1, NULL, 1, NULL, 1615.00, '2026-04-15', 'fully', 'online', '0', '0', '1', '0', 'debit', NULL, 1, 0, '2026-04-15 17:47:08', '2026-04-15 17:47:08'),
(10, 5, NULL, 4, NULL, 1000.00, '2026-04-16', 'partial', 'Cash', '1000', '0', NULL, '300', 'debit', NULL, NULL, 0, '2026-04-16 11:37:07', '2026-04-16 11:37:07'),
(11, 1, 9, 0, 0, 2400.00, '2026-04-23', 'partially', 'cash', '2400', '0', '1', '71520', 'credit', NULL, NULL, 0, '2026-04-23 12:58:56', '2026-04-23 12:58:56'),
(12, 5, 0, 4, 0, 100.00, '2026-04-23', 'partially', 'cash', '100', '0', '1', '200', 'debit', NULL, NULL, 0, '2026-04-23 13:38:23', '2026-04-23 13:38:23'),
(13, 1, 11, NULL, 0, 29.00, '2026-04-24', 'partially', 'online', NULL, NULL, '1', '1100', 'credit', NULL, 1, 0, '2026-04-24 15:24:32', '2026-04-24 15:24:32'),
(14, 1, 17, 0, 0, 1600.00, '2026-04-24', 'partially', 'cash', '1600', '0', '1', '0', 'credit', NULL, NULL, 1, '2026-04-24 16:11:32', '2026-05-01 18:20:55'),
(15, 1, 16, 0, 0, 400.00, '2026-04-24', 'partially', 'cash', '400', '0', '1', '1500', 'credit', NULL, NULL, 0, '2026-04-24 16:11:32', '2026-04-24 16:11:32'),
(16, 1, 18, NULL, 0, 100.00, '2026-04-24', 'partially', 'cash', '100', '0', '1', '300', 'credit', NULL, 1, 0, '2026-04-24 18:01:20', '2026-04-24 18:01:20'),
(17, 1, 18, NULL, 0, 100.00, '2026-04-24', 'partially', 'online', '0', '100', '1', '300', 'credit', 'jh', 1, 0, '2026-04-24 18:01:20', '2026-05-01 10:47:47'),
(18, 1, 21, NULL, 0, 43.00, '2026-04-27', 'partially', 'cash', NULL, NULL, '1', '500', 'credit', NULL, NULL, 0, '2026-04-27 16:40:18', '2026-04-27 16:40:18'),
(19, 1, NULL, 9, NULL, 93.00, '2026-04-27', 'partially', 'cash', '0', '0', '1', '1600', 'debit', NULL, NULL, 1, '2026-04-27 17:29:15', '2026-05-04 17:19:36'),
(20, 1, 22, NULL, 0, 14.00, '2026-04-27', 'partially', 'cash', NULL, NULL, '1', '1600', 'credit', NULL, NULL, 1, '2026-04-27 18:03:40', '2026-05-04 17:19:31'),
(21, 1, 23, NULL, 0, 29.00, '2026-04-27', 'partially', 'cash', NULL, NULL, '1', '2200', 'credit', NULL, NULL, 1, '2026-04-27 18:09:15', '2026-05-04 17:19:28'),
(22, 3, 24, NULL, NULL, 50.00, '2026-04-28', 'partially', 'cash', '50', '0', NULL, '250', NULL, NULL, 1, 0, '2026-04-28 17:02:09', '2026-04-28 17:02:09'),
(23, 3, 24, NULL, NULL, 50.00, '2026-04-28', 'partially', 'online', '0', '50', NULL, '250', NULL, NULL, 1, 0, '2026-04-28 17:02:09', '2026-04-28 17:02:09'),
(24, 1, NULL, 8, NULL, 150.00, '2026-04-30', 'partially', 'online', '0', '0', '1', '250', 'debit', 'hu', 2, 1, '2026-04-30 18:50:18', '2026-05-01 18:31:00'),
(25, 1, 24, NULL, 0, 100.00, '2026-05-01', 'partially', 'online', NULL, NULL, '1', '150', 'credit', NULL, 2, 1, '2026-05-01 18:07:22', '2026-05-01 18:28:27'),
(26, 1, 19, NULL, 0, 100.00, '2026-05-01', 'partially', 'cash', NULL, NULL, '1', '200', 'credit', NULL, NULL, 1, '2026-05-01 18:27:35', '2026-05-01 18:29:12'),
(27, 1, 24, NULL, 0, 25.96, '2026-05-04', 'partially', 'online', NULL, NULL, '1', '300.04', 'credit', 'remarks', 1, 0, '2026-05-04 11:54:36', '2026-05-04 11:54:36'),
(28, 7, NULL, NULL, 4, 100.00, '2026-05-04', 'partial', 'Online', '0', '100', NULL, '2400', 'debit', NULL, 1, 0, '2026-05-04 16:05:10', '2026-05-04 16:05:10'),
(29, 15, NULL, NULL, 5, 200.00, '2026-05-04', 'partial', 'Cash', '100', '0', NULL, '900', 'credit', NULL, NULL, 1, '2026-05-04 16:23:04', '2026-05-04 17:19:25'),
(30, 11, 26, NULL, NULL, 99.98, '2026-05-04', 'partially', 'online', '0', '99.98', NULL, '1401.02', NULL, NULL, 3, 0, '2026-05-04 17:16:52', '2026-05-04 17:16:52'),
(31, 1, 27, NULL, 0, 200.00, '2026-05-04', 'partially', 'cash', NULL, NULL, '1', '2400', 'credit', 'remarks', NULL, 0, '2026-05-04 17:21:57', '2026-05-04 17:22:37'),
(32, 17, NULL, 10, NULL, 100.00, '2026-05-04', 'partially', 'online', '0', '0', '1', '1400', 'debit', '', 3, 0, '2026-05-04 17:38:29', '2026-05-04 17:54:45'),
(33, 13, NULL, 12, NULL, 100.00, '2026-05-06', 'partial', 'online', '0', '100', NULL, '1400', 'debit', NULL, 4, 0, '2026-05-06 10:40:21', '2026-05-06 10:40:21'),
(34, 1, 21, 0, 0, 100.00, '2026-05-06', 'partially', 'cash', '100', '0', '1', '400', 'credit', NULL, NULL, 1, '2026-05-06 10:48:33', '2026-05-06 10:48:56'),
(35, 3, 29, NULL, NULL, 10.00, '2026-05-06', 'partially', 'cash', '10', '0', NULL, '190', NULL, 'remarks', NULL, 0, '2026-05-06 15:06:59', '2026-05-06 15:06:59'),
(36, 3, 29, NULL, NULL, 10.00, '2026-05-06', 'partially', 'online', '0', '10', NULL, '180', NULL, 'remarks', 5, 0, '2026-05-06 15:08:01', '2026-05-06 15:08:01'),
(37, 1, 30, NULL, 0, 200.00, '2026-05-06', 'fully', 'cash', NULL, NULL, '1', '0', 'credit', 'fgfgf', NULL, 0, '2026-05-06 18:51:37', '2026-05-06 18:51:37'),
(38, 1, 31, NULL, 0, 70.00, '2026-05-07', 'partially', 'cash', NULL, NULL, '1', '200', 'credit', 'recived', NULL, 0, '2026-05-07 12:15:10', '2026-05-07 12:15:10'),
(39, 15, 32, NULL, NULL, 32.00, '2026-05-08', 'partially', 'cash', '32', '0', NULL, '4200', NULL, 'remaks', NULL, 0, '2026-05-08 09:50:48', '2026-05-08 09:50:48'),
(40, 1, 32, NULL, 0, 100.00, '2026-05-08', 'partially', 'online', NULL, NULL, '1', '4100', 'credit', 'remarks', 3, 0, '2026-05-08 09:51:36', '2026-05-08 09:51:36'),
(41, 15, 32, NULL, NULL, 50.00, '2026-05-08', 'partially', 'cash', '50', '0', NULL, '4000', NULL, 'remaks', 3, 0, '2026-05-08 09:52:11', '2026-05-08 09:52:11'),
(42, 15, 32, NULL, NULL, 50.00, '2026-05-08', 'partially', 'online', '0', '50', NULL, '4000', NULL, 'remaks', 3, 0, '2026-05-08 09:52:11', '2026-05-08 09:52:11'),
(43, 5, NULL, 13, NULL, 4298.00, '2026-05-26', 'full', 'Cash', '4298', '0', NULL, '0', 'debit', NULL, NULL, 0, '2026-05-26 10:45:48', '2026-05-26 10:45:48'),
(44, 1, 29, 0, 0, 180.00, '2026-05-26', 'fully', 'cash', '180', '0', '1', '0', 'credit', NULL, NULL, 0, '2026-05-26 13:32:43', '2026-05-26 13:32:43'),
(45, 1, 24, 0, 0, 300.04, '2026-05-26', 'fully', 'cash', '300.04', '0', '1', '0', 'credit', NULL, NULL, 0, '2026-05-26 13:32:43', '2026-05-26 13:32:43'),
(46, 1, 20, 0, 0, 1258.00, '2026-05-26', 'fully', 'cash', '1258', '0', '1', '0', 'credit', NULL, NULL, 0, '2026-05-26 13:32:43', '2026-05-26 13:32:43'),
(47, 1, 11, 0, 0, 1100.00, '2026-05-26', 'fully', 'cash', '1100', '0', '1', '0', 'credit', NULL, NULL, 0, '2026-05-26 13:32:43', '2026-05-26 13:32:43'),
(48, 1, 9, 0, 0, 9920.00, '2026-05-26', 'fully', 'cash', '9920', '0', '1', '0', 'credit', NULL, NULL, 0, '2026-05-26 13:32:43', '2026-05-26 13:32:43'),
(49, 20, NULL, 14, NULL, 90.00, '2026-05-26', 'full', 'Cash', '90', '0', NULL, '0', 'debit', NULL, NULL, 0, '2026-05-26 18:10:28', '2026-05-26 18:12:25'),
(50, 20, NULL, 14, NULL, 811.00, '2026-05-26', 'full', 'Cash', '811', '0', NULL, '0', 'debit', NULL, NULL, 0, '2026-05-26 18:12:25', '2026-05-26 18:12:25');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `SKU` varchar(255) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` decimal(11,2) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `hsn_code` varchar(255) DEFAULT NULL,
  `gst_option` varchar(255) DEFAULT NULL,
  `product_gst` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `availablility` enum('in_stock','out_stock') DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `vendor_id`, `category_id`, `brand_id`, `branch_id`, `created_by`, `name`, `SKU`, `barcode`, `description`, `price`, `quantity`, `unit_id`, `hsn_code`, `gst_option`, `product_gst`, `images`, `availablility`, `status`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, NULL, 'Engine Oil Filter', '1', 'PRD5986831265', NULL, 350.00, 97.70, 1, '1', 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[\"img\\/product\\/ta2D3aktq5StmuatJWmsrT3JKxqsldG4omY7fJQl.jpg\"]', 'in_stock', 'active', 0, '2026-04-03 12:11:23', '2026-05-26 12:51:11'),
(2, 1, 2, 2, 1, NULL, 'Brake Pad Set', '2', 'PRD8321815548', NULL, 1200.00, 103.00, 1, '2', 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-03 12:11:55', '2026-05-26 12:51:11'),
(3, 1, 3, 3, 1, NULL, 'Shock Absorber', '3', 'PRD6404172666', NULL, 2500.00, 99.00, 1, '3', 'without_gst', NULL, '[]', 'in_stock', 'active', 0, '2026-04-03 12:12:27', '2026-05-26 12:51:11'),
(4, 1, 4, 4, 1, NULL, 'Car Battery', '4', 'PRD4931553154', NULL, 4500.00, 99.00, 2, '4', 'without_gst', NULL, '[]', 'in_stock', 'active', 0, '2026-04-03 12:13:19', '2026-05-26 12:51:11'),
(5, 1, 5, 5, 1, NULL, 'Front Bumper', '5', 'PRD6130547948', NULL, 6000.00, 99.00, 1, '5', 'without_gst', NULL, '[]', 'in_stock', 'active', 0, '2026-04-03 12:13:56', '2026-05-26 12:51:11'),
(6, 1, 6, 6, 1, NULL, 'Seat Cover Set', '6', 'PRD8283366329', NULL, 3000.00, 99.00, 2, '6', 'with_gst', '[{\"tax_id\":\"23\",\"tax_name\":\"CGST\",\"tax_rate\":\"5.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-03 12:14:31', '2026-04-08 16:47:34'),
(7, 1, 7, 7, 1, NULL, 'Side Mirror', '7', 'PRD3167177271', NULL, 1800.00, 99.00, 3, '7', 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-03 12:15:19', '2026-04-08 16:47:34'),
(8, 1, 8, 8, 1, NULL, 'Radiator Coolant', '8', 'PRD1520723800', NULL, 900.00, 99.00, 2, '8', 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-03 12:16:03', '2026-04-08 16:47:34'),
(9, 1, 9, 9, 1, NULL, 'Fuel Pump', '9', 'PRD9893502110', NULL, 7000.00, 97.00, 1, '9', 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-03 12:16:44', '2026-05-06 12:50:03'),
(10, 1, 10, 10, 1, NULL, 'Clutch Plate', '10', 'PRD6316359287', NULL, 5500.00, 99.00, 2, '10', 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-03 12:17:08', '2026-04-08 16:47:34'),
(11, 1, 1, 1, 1, NULL, 'Air Filter', '11', 'PRD5204021311', NULL, 450.00, 86.00, 4, '11', 'without_gst', NULL, '[]', 'in_stock', 'active', 0, '2026-04-03 15:36:32', '2026-05-06 15:05:39'),
(12, 1, 1, 3, 1, NULL, 'Spark Plug', '12', 'PRD8522619851', NULL, 250.00, 95.50, 4, '12', 'without_gst', NULL, '[]', 'in_stock', 'active', 0, '2026-04-03 15:37:41', '2026-05-04 11:55:28'),
(13, 1, 1, 11, 1, NULL, 'Engine Oil', '13', 'PRD7793019679', NULL, 900.00, 91.50, 4, '13', 'with_gst', '[{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-03 15:38:38', '2026-04-24 18:17:31'),
(14, 1, 1, 12, 1, NULL, 'Timing Belt', '14', 'PRD5462587069', NULL, 1800.00, 100.00, 1, '14', 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-03 15:39:28', '2026-04-03 15:39:28'),
(15, 1, 2, 13, 1, NULL, 'Brake Disc Rotor', '15', 'PRD8465832166', NULL, 3500.00, 100.00, 1, '15', 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-03 15:41:15', '2026-04-03 15:41:15'),
(16, 1, 2, 11, 1, NULL, 'Brake Oil', '16', 'PRD9805460185', NULL, 300.00, 100.00, 5, '16', 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-03 15:42:34', '2026-04-03 15:42:34'),
(17, 1, 2, 1, 1, NULL, 'Brake Caliper', '17', 'PRD7339037423', NULL, 4500.00, 100.00, 1, '17', 'with_gst', '[{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-03 15:43:08', '2026-04-03 15:43:08'),
(18, 1, 2, 2, 1, NULL, 'Brake Shoe', '18', 'PRD2192056985', NULL, 900.00, 99.00, 2, '18', 'without_gst', NULL, '[]', 'in_stock', 'active', 0, '2026-04-03 15:44:29', '2026-05-26 12:51:11'),
(19, 1, 11, 14, 1, NULL, 'Hair Oil', '2000', 'PRD3534225918', NULL, 100.00, 197.00, 1, NULL, 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-06 11:35:08', '2026-05-07 12:14:29'),
(20, 1, 11, 14, 1, NULL, 'Shampoo', '2001', 'PRD9585410661', NULL, 200.00, 98.00, 1, NULL, 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-06 11:37:48', '2026-05-06 18:39:49'),
(21, 1, 11, 14, 1, NULL, 'Beauty Cream', '2002', 'PRD2930717412', NULL, 350.50, 109.20, 1, NULL, 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-06 11:38:37', '2026-05-04 11:29:29'),
(22, 1, 11, 14, 1, NULL, 'cream', '2004', 'PRD8894065181', NULL, 100.00, 102.00, 6, NULL, 'with_gst', '[{\"tax_id\":\"1\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"2\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-06 12:33:48', '2026-04-23 12:57:38'),
(23, 1, 12, 15, 2, NULL, 'Shirt', '0', 'PRD5394165035', NULL, 1000.00, 96.00, 11, NULL, 'with_gst', '[{\"tax_id\":\"26\",\"tax_name\":\"CGST\",\"tax_rate\":\"9.00\"},{\"tax_id\":\"27\",\"tax_name\":\"SGST\",\"tax_rate\":\"9.00\"}]', '[\"img\\/product\\/Ejxd1tuD9fnbW9hk45tVRhP1fslsq3Sa7seJ3XgY.webp\"]', 'in_stock', 'active', 0, '2026-04-27 16:48:09', '2026-05-08 09:49:15'),
(24, 1, 12, 15, 2, NULL, 'pent', '01246', 'PRD1372717075', NULL, 1500.00, 98.50, 9, NULL, 'with_gst', '[{\"tax_id\":\"28\",\"tax_name\":\"IGST\",\"tax_rate\":\"5.00\"}]', '[]', 'in_stock', 'active', 0, '2026-04-27 16:48:52', '2026-04-27 18:08:31'),
(25, 1, 1, 4, 1, NULL, 'Engine Oil', NULL, 'PRD3412388492', NULL, 2500.00, 100.00, 3, NULL, 'with_gst', '[{\"tax_id\":\"23\",\"tax_name\":\"CGST\",\"tax_rate\":\"5.00\"}]', '[]', 'in_stock', 'active', 0, '2026-05-05 12:01:31', '2026-05-05 12:06:10'),
(26, 1, 1, 1, 1, NULL, 'New Test product', NULL, 'PRD3962912771', NULL, 150.00, 20.00, 1, NULL, 'without_gst', NULL, '[\"img\\/product\\/QnavqybYDlQQkfMdbVqTYsQlIrXFzHWE7UIf0us1.png\",\"img\\/product\\/dzriEvZ8Wv3PyI2yplCm64XfQp51JwoS493apBSC.png\",\"img\\/product\\/qmXW3cIBq8RmNlTeuEuqel3rvmZlHWqyuN32nZwe.png\",\"img\\/product\\/7U3aioc9s9P5DwurWivG8EbSzdHai84ZEPYGT6IN.png\",\"img\\/product\\/84JrWErI0Pj3KsgYK7TNIZeQ7DWuYgpRQ1lnBVBe.png\",\"img\\/product\\/BdRZ4k6rXlUHnwb5NEdMEi4Tgh5hZVHC4iVdMScl.png\",\"img\\/product\\/cvzJFcZPFEFx3zn2hAf8MVKXjy0bvKu96qUqxxZf.png\",\"img\\/product\\/yZNSirLHWcAGBdjeWsE6VqjWzApcDEKNi6Dkxool.png\"]', 'in_stock', 'active', 0, '2026-05-06 10:39:10', '2026-05-26 17:45:52'),
(27, 1, 13, 16, 1, NULL, 'Logo T-Shirt', '001245001', 'ADI23012', 'A soft and versatile loose-fit adidas tee.\r\nThe name says it all. This Premium Essentials tee from adidas is crafted from soft, single jersey fabric with a loose, relaxed fit. Perfect for laid-back days or nights out, it pairs just as well with joggers as it does with jeans. A small Trefoil on the chest adds a subtle yet unmistakable signoff.', 1999.00, 102.00, 1, NULL, 'with_gst', '[{\"tax_id\":\"23\",\"tax_name\":\"CGST\",\"tax_rate\":\"5.00\"}]', '[\"img\\/product\\/CTVvesmapAYxskxidjJycaeGyjjwY6NTisIDVwgG.jpg\"]', 'in_stock', 'active', 0, '2026-05-26 10:24:13', '2026-05-26 10:45:48'),
(28, 1, 17, 18, 1, NULL, 'hjbhbb', '9089090', 'PRD7882818465', NULL, 9009.00, 9.00, 4, '9999un', 'without_gst', NULL, '[]', 'in_stock', 'active', 0, '2026-05-26 17:55:40', '2026-05-26 18:09:19');

-- --------------------------------------------------------

--
-- Table structure for table `product_inventory`
--

CREATE TABLE `product_inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `initial_stock` varchar(100) NOT NULL DEFAULT '0',
  `current_stock` varchar(100) NOT NULL DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  `branch_id` int(11) NOT NULL,
  `create_by` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_inventory`
--

INSERT INTO `product_inventory` (`id`, `product_id`, `initial_stock`, `current_stock`, `type`, `branch_id`, `create_by`, `date`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 12:11:23', '2026-04-03 12:11:23'),
(2, 2, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 12:11:55', '2026-04-03 12:11:55'),
(3, 3, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 12:12:27', '2026-04-03 12:12:27'),
(4, 4, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 12:13:19', '2026-04-03 12:13:19'),
(5, 5, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 12:13:56', '2026-04-03 12:13:56'),
(6, 6, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 12:14:31', '2026-04-03 12:14:31'),
(7, 7, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 12:15:19', '2026-04-03 12:15:19'),
(8, 8, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 12:16:03', '2026-04-03 12:16:03'),
(9, 9, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 12:16:44', '2026-04-03 12:16:44'),
(10, 10, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 12:17:08', '2026-04-03 12:17:08'),
(11, 11, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 15:36:32', '2026-04-03 15:36:32'),
(12, 12, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 15:37:41', '2026-04-03 15:37:41'),
(13, 13, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 15:38:38', '2026-04-03 15:38:38'),
(14, 14, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 15:39:28', '2026-04-03 15:39:28'),
(15, 15, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 15:41:15', '2026-04-03 15:41:15'),
(16, 16, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 15:42:34', '2026-04-03 15:42:34'),
(17, 17, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 15:43:08', '2026-04-03 15:43:08'),
(18, 18, '100', '100', 'Create', 1, 1, '2026-04-03', NULL, '2026-04-03 15:44:29', '2026-04-03 15:44:29'),
(19, 1, '100', '101', 'Purchase', 1, 1, '2026-04-03', NULL, '2026-04-03 15:48:08', '2026-04-03 15:48:08'),
(20, 1, '100', '102', 'Purchase', 1, 1, '2026-04-03', NULL, '2026-04-03 15:48:17', '2026-04-03 15:48:17'),
(21, 1, '100', '103', 'Purchase', 1, 1, '2026-04-03', NULL, '2026-04-03 15:48:24', '2026-04-03 15:48:24'),
(22, 1, '100', '104', 'Purchase', 1, 1, '2026-04-03', NULL, '2026-04-03 17:53:58', '2026-04-03 17:53:58'),
(23, 2, '100', '101', 'Purchase', 1, 1, '2026-04-03', NULL, '2026-04-03 17:53:58', '2026-04-03 17:53:58'),
(24, 19, '100', '100', 'Create', 1, 1, '2026-04-06', NULL, '2026-04-06 11:35:08', '2026-04-06 11:35:08'),
(25, 20, '100', '100', 'Create', 1, 1, '2026-04-06', NULL, '2026-04-06 11:37:48', '2026-04-06 11:37:48'),
(26, 21, '100', '100', 'Create', 1, 1, '2026-04-06', NULL, '2026-04-06 11:38:37', '2026-04-06 11:38:37'),
(27, 22, '100', '100', 'Create', 1, 1, '2026-04-06', NULL, '2026-04-06 12:33:48', '2026-04-06 12:33:48'),
(28, 22, '100', '101', 'Purchase', 1, 1, '2026-04-06', NULL, '2026-04-06 12:35:31', '2026-04-06 12:35:31'),
(29, 11, '100', '99', 'Sale', 1, 1, '2026-04-06', NULL, '2026-04-06 14:47:03', '2026-04-06 14:47:03'),
(30, 12, '100', '99', 'Sale', 1, 1, '2026-04-06', NULL, '2026-04-06 14:47:03', '2026-04-06 14:47:03'),
(31, 6, '100', '99', 'Update Sale', 1, NULL, '2026-04-08', NULL, '2026-04-08 16:47:34', '2026-04-08 16:47:34'),
(32, 7, '100', '99', 'Update Sale', 1, NULL, '2026-04-08', NULL, '2026-04-08 16:47:34', '2026-04-08 16:47:34'),
(33, 8, '100', '99', 'Update Sale', 1, NULL, '2026-04-08', NULL, '2026-04-08 16:47:34', '2026-04-08 16:47:34'),
(34, 9, '100', '99', 'Update Sale', 1, NULL, '2026-04-08', NULL, '2026-04-08 16:47:34', '2026-04-08 16:47:34'),
(35, 10, '100', '99', 'Update Sale', 1, NULL, '2026-04-08', NULL, '2026-04-08 16:47:34', '2026-04-08 16:47:34'),
(36, 11, '100', '99', 'Update Sale', 1, NULL, '2026-04-08', NULL, '2026-04-08 16:47:34', '2026-04-08 16:47:34'),
(37, 12, '100', '99', 'Update Sale', 1, NULL, '2026-04-08', NULL, '2026-04-08 16:47:34', '2026-04-08 16:47:34'),
(38, 12, '100', '98', 'Sale', 1, 1, '2026-04-13', NULL, '2026-04-13 18:02:00', '2026-04-13 18:02:00'),
(39, 13, '100', '99', 'Convert Quotation to Sale', 1, 1, '2026-04-14', NULL, '2026-04-14 11:39:58', '2026-04-14 11:39:58'),
(40, 1, '100', '102', 'Sale', 1, 1, '2026-04-14', NULL, '2026-04-14 16:07:26', '2026-04-14 16:07:26'),
(41, 11, '100', '98', 'Sale', 1, 1, '2026-04-14', NULL, '2026-04-14 16:07:26', '2026-04-14 16:07:26'),
(42, 22, '100', '91', 'Sale', 1, 1, '2026-04-15', NULL, '2026-04-15 17:31:38', '2026-04-15 17:31:38'),
(43, 21, '100', '99', 'Sale', 1, 1, '2026-04-15', NULL, '2026-04-15 17:31:38', '2026-04-15 17:31:38'),
(44, 12, '100', '108', 'Purchase', 1, 1, '2026-04-15', NULL, '2026-04-15 17:33:42', '2026-04-15 17:33:42'),
(45, 9, '100', '87', 'Sale', 1, 1, '2026-04-15', NULL, '2026-04-15 20:24:25', '2026-04-15 20:24:25'),
(46, 22, '91', '93', 'Sales Return', 1, 1, '2026-04-16', NULL, '2026-04-16 10:05:08', '2026-04-16 10:05:08'),
(47, 22, '93', '92', 'Purchase Return', 1, 1, '2026-04-16', NULL, '2026-04-16 10:05:36', '2026-04-16 10:05:36'),
(48, 11, '100', '100', 'Purchase', 1, 1, '2026-04-16', NULL, '2026-04-16 11:37:07', '2026-04-16 11:37:07'),
(49, 1, '100.00', '101.00', 'Convert Quotation to Sale', 1, 1, '2026-04-22', NULL, '2026-04-22 18:38:29', '2026-04-22 18:38:29'),
(50, 13, '100.00', '98.00', 'Convert Quotation to Sale', 1, 1, '2026-04-22', NULL, '2026-04-22 18:38:29', '2026-04-22 18:38:29'),
(51, 1, '100.00', '99.00', 'Convert Quotation to Sale', 1, 1, '2026-04-22', NULL, '2026-04-22 18:45:12', '2026-04-22 18:45:12'),
(52, 2, '100.00', '100.00', 'Convert Quotation to Sale', 1, 1, '2026-04-22', NULL, '2026-04-22 18:45:12', '2026-04-22 18:45:12'),
(53, 22, '100.00', '102', 'Stock Added', 1, 1, '2026-04-23', 'ftghhf', '2026-04-23 12:57:38', '2026-04-23 12:57:38'),
(54, 2, '100.00', '101.5', 'Purchase', 1, 1, '2026-04-24', NULL, '2026-04-24 14:55:10', '2026-04-24 14:55:10'),
(55, 2, '102', '101.50', 'Purchase Update', 1, 1, '2026-04-24', NULL, '2026-04-24 14:56:37', '2026-04-24 14:56:37'),
(56, 2, '102', '101.50', 'Purchase Update', 1, 1, '2026-04-24', NULL, '2026-04-24 14:57:18', '2026-04-24 14:57:18'),
(57, 2, '102', '102.50', 'Purchase Update', 1, 1, '2026-04-24', NULL, '2026-04-24 14:57:51', '2026-04-24 14:57:51'),
(58, 13, '100.00', '96.5', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 15:21:52', '2026-04-24 15:21:52'),
(59, 12, '100.00', '105.5', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 15:21:52', '2026-04-24 15:21:52'),
(60, 13, '100.00', '96.50', 'Update Sale', 1, NULL, '2026-04-24', NULL, '2026-04-24 15:23:50', '2026-04-24 15:23:50'),
(61, 12, '100.00', '103.50', 'Update Sale', 1, NULL, '2026-04-24', NULL, '2026-04-24 15:23:50', '2026-04-24 15:23:50'),
(62, 1, '100.00', '109.2', 'Purchase', 1, 1, '2026-04-10', NULL, '2026-04-10 15:30:49', '2026-04-24 15:30:49'),
(63, 1, '109.2', '109.20', 'Purchase Update', 1, 1, '2026-04-24', NULL, '2026-04-24 15:34:16', '2026-04-24 15:34:16'),
(64, 2, '102.00', '103.5', 'Purchase', 1, 1, '2026-04-24', NULL, '2026-04-24 15:37:44', '2026-04-24 15:37:44'),
(65, 21, '100.00', '109.2', 'Stock Added', 1, 1, '2026-04-24', 'Remarks....', '2026-04-24 15:57:30', '2026-04-24 15:57:30'),
(66, 11, '100.00', '99', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 16:06:40', '2026-04-24 16:06:40'),
(67, 12, '100.00', '102.5', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 16:06:40', '2026-04-24 16:06:40'),
(68, 11, '100.00', '96', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 16:07:40', '2026-04-24 16:07:40'),
(69, 13, '100.00', '93.5', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 16:07:40', '2026-04-24 16:07:40'),
(70, 11, '100.00', '93', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 16:07:54', '2026-04-24 16:07:54'),
(71, 12, '100.00', '101.5', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 16:07:54', '2026-04-24 16:07:54'),
(72, 12, '100.00', '97.5', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 16:09:28', '2026-04-24 16:09:28'),
(73, 13, '100.00', '92.5', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 16:09:28', '2026-04-24 16:09:28'),
(74, 11, '100.00', '90', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 16:09:42', '2026-04-24 16:09:42'),
(75, 12, '100.00', '96.5', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 16:09:42', '2026-04-24 16:09:42'),
(76, 11, '100.00', '90.00', 'Update Sale', 1, NULL, '2026-04-24', NULL, '2026-04-24 16:10:37', '2026-04-24 16:10:37'),
(77, 12, '100.00', '96.50', 'Update Sale', 1, NULL, '2026-04-24', NULL, '2026-04-24 16:10:37', '2026-04-24 16:10:37'),
(78, 11, '100.00', '89', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 17:57:50', '2026-04-24 17:57:50'),
(79, 12, '100.00', '97.5', 'Purchase', 1, 1, '2026-04-24', NULL, '2026-04-24 18:04:53', '2026-04-24 18:04:53'),
(80, 12, '100.00', '96.5', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 18:05:28', '2026-04-24 18:05:28'),
(81, 13, '100.00', '91.5', 'Sale', 1, 1, '2026-04-24', NULL, '2026-04-24 18:17:31', '2026-04-24 18:17:31'),
(82, 1, '109.20', '108.2', 'Sale', 1, 1, '2026-04-25', NULL, '2026-04-25 16:36:31', '2026-04-25 16:36:31'),
(83, 1, '109.20', '108.20', 'Update Sale', 1, NULL, '2026-04-27', NULL, '2026-04-27 16:38:29', '2026-04-27 16:38:29'),
(84, 23, '100', '100', 'Create', 2, 1, '2026-04-27', NULL, '2026-04-27 16:48:09', '2026-04-27 16:48:09'),
(85, 24, '100', '100', 'Create', 2, 1, '2026-04-27', NULL, '2026-04-27 16:48:52', '2026-04-27 16:48:52'),
(86, 24, '100.00', '100.00', 'Edit', 2, 1, '2026-04-27', NULL, '2026-04-27 16:51:01', '2026-04-27 16:51:01'),
(87, 23, '100.00', '100.00', 'Edit', 2, 1, '2026-04-27', NULL, '2026-04-27 16:51:22', '2026-04-27 16:51:22'),
(88, 23, '100.00', '100.00', 'Edit', 2, 1, '2026-04-27', NULL, '2026-04-27 16:53:38', '2026-04-27 16:53:38'),
(89, 23, '100.00', '101.5', 'Purchase', 2, 1, '2026-04-27', NULL, '2026-04-27 17:21:07', '2026-04-27 17:21:07'),
(90, 23, '101.5', '101.50', 'Purchase Update', 2, 1, '2026-04-27', NULL, '2026-04-27 17:29:50', '2026-04-27 17:29:50'),
(91, 23, '101.50', '100', 'Sale', 2, 1, '2026-04-27', NULL, '2026-04-27 17:35:05', '2026-04-27 17:35:05'),
(92, 23, '101.50', '100.00', 'Update Sale', 2, NULL, '2026-04-27', NULL, '2026-04-27 18:03:27', '2026-04-27 18:03:27'),
(93, 24, '100.00', '98.50', 'Convert Quotation to Sale', 2, 1, '2026-04-27', NULL, '2026-04-27 18:08:31', '2026-04-27 18:08:31'),
(94, 12, '96.5', '96.50', 'Purchase Update', 1, 1, '2026-04-28', NULL, '2026-04-28 16:51:31', '2026-04-28 16:51:31'),
(95, 1, '109.20', '107.2', 'Sale', 1, 1, '2026-04-28', NULL, '2026-04-28 17:02:09', '2026-04-28 17:02:09'),
(96, 1, '109.20', '107.20', 'Update Sale', 1, NULL, '2026-05-01', NULL, '2026-05-01 18:07:47', '2026-05-01 18:07:47'),
(97, 21, '109.20', '109.20', 'Edit', 1, 1, '2026-05-04', NULL, '2026-05-04 11:29:29', '2026-05-04 11:29:29'),
(98, 1, '109.20', '106.70', 'Update Sale', 1, NULL, '2026-05-04', NULL, '2026-05-04 11:31:22', '2026-05-04 11:31:22'),
(99, 12, '96.5', '97.00', 'Purchase Update', 1, 1, '2026-05-04', NULL, '2026-05-04 11:32:50', '2026-05-04 11:32:50'),
(100, 1, '109.20', '103.7', 'Sale', 1, 1, '2026-05-04', NULL, '2026-05-04 11:55:28', '2026-05-04 11:55:28'),
(101, 12, '96.50', '95.5', 'Sale', 1, 1, '2026-05-04', NULL, '2026-05-04 11:55:28', '2026-05-04 11:55:28'),
(102, 3, '100.00', '101', 'Purchase', 1, 1, '2026-05-04', NULL, '2026-05-04 16:05:10', '2026-05-04 16:05:10'),
(103, 23, '101.50', '101', 'Purchase', 2, 1, '2026-05-04', NULL, '2026-05-04 16:23:04', '2026-05-04 16:23:04'),
(104, 23, '101.50', '99.5', 'Sale', 2, 1, '2026-05-04', NULL, '2026-05-04 17:16:52', '2026-05-04 17:16:52'),
(105, 23, '101.50', '97', 'Sale', 2, 1, '2026-05-04', NULL, '2026-05-04 17:21:09', '2026-05-04 17:21:09'),
(106, 23, '101.50', '98.5', 'Purchase', 2, 1, '2026-05-04', NULL, '2026-05-04 17:26:58', '2026-05-04 17:26:58'),
(107, 23, '98.50', '99.50', 'Sales Return', 2, 1, '2026-05-04', NULL, '2026-05-04 17:54:08', '2026-05-04 17:54:08'),
(108, 23, '99.5', '100.50', 'Purchase Update', 2, 1, '2026-05-04', NULL, '2026-05-04 17:54:45', '2026-05-04 17:54:45'),
(109, 23, '100.50', '99.00', 'Purchase Return', 2, 1, '2026-05-04', NULL, '2026-05-04 18:39:16', '2026-05-04 18:39:16'),
(110, 23, '99.00', '99.50', 'Sales Return', 2, 1, '2026-05-04', NULL, '2026-05-04 18:39:38', '2026-05-04 18:39:38'),
(111, 25, '100', '100', 'Create', 1, 1, '2026-05-05', NULL, '2026-05-05 12:01:31', '2026-05-05 12:01:31'),
(112, 25, '100.00', '101', 'Purchase', 1, 1, '2026-05-05', NULL, '2026-05-05 12:02:01', '2026-05-05 12:02:01'),
(113, 25, '101', '103.00', 'Purchase Update', 1, 1, '2026-05-05', NULL, '2026-05-05 12:04:06', '2026-05-05 12:04:06'),
(114, 25, '103.00', '101.50', 'Purchase Return', 1, 1, '2026-05-05', NULL, '2026-05-05 12:04:55', '2026-05-05 12:04:55'),
(115, 25, '101.50', '100.00', 'Purchase Return', 1, 1, '2026-05-05', NULL, '2026-05-05 12:06:10', '2026-05-05 12:06:10'),
(116, 1, '109.20', '101.7', 'Sale', 1, 1, '2026-05-05', NULL, '2026-05-05 12:22:08', '2026-05-05 12:22:08'),
(117, 11, '100.00', '88', 'Sale', 1, 1, '2026-05-05', NULL, '2026-05-05 12:22:08', '2026-05-05 12:22:08'),
(118, 1, '109.20', '99.70', 'Convert Quotation to Sale', 1, 1, '2026-05-05', NULL, '2026-05-05 12:25:24', '2026-05-05 12:25:24'),
(119, 11, '100.00', '87.00', 'Convert Quotation to Sale', 1, 1, '2026-05-05', NULL, '2026-05-05 12:25:24', '2026-05-05 12:25:24'),
(120, 1, '99.70', '99.70', 'Edit', 1, 1, '2026-05-05', NULL, '2026-05-05 12:49:39', '2026-05-05 12:49:39'),
(121, 26, '10', '10', 'Create', 1, 1, '2026-05-06', NULL, '2026-05-06 10:39:10', '2026-05-06 10:39:10'),
(122, 26, '10.00', '20', 'Purchase', 1, 1, '2026-05-06', NULL, '2026-05-06 10:40:21', '2026-05-06 10:40:21'),
(123, 9, '87.00', '97.00', 'Sales Return', 1, 1, '2026-05-06', NULL, '2026-05-06 12:50:03', '2026-05-06 12:50:03'),
(124, 1, '99.70', '97.70', 'Convert Quotation to Sale', 1, 1, '2026-05-06', NULL, '2026-05-06 15:05:39', '2026-05-06 15:05:39'),
(125, 11, '100.00', '86.00', 'Convert Quotation to Sale', 1, 1, '2026-05-06', NULL, '2026-05-06 15:05:39', '2026-05-06 15:05:39'),
(126, 20, '100.00', '99.00', 'Convert Quotation to Sale', 1, 1, '2026-05-06', NULL, '2026-05-06 15:06:32', '2026-05-06 15:06:32'),
(127, 20, '100.00', '99.00', 'Update Sale', 1, NULL, '2026-05-06', NULL, '2026-05-06 15:06:59', '2026-05-06 15:06:59'),
(128, 20, '100.00', '99.00', 'Update Sale', 1, NULL, '2026-05-06', NULL, '2026-05-06 15:08:01', '2026-05-06 15:08:01'),
(129, 20, '100.00', '98', 'Sale', 1, 1, '2026-05-06', NULL, '2026-05-06 18:39:49', '2026-05-06 18:39:49'),
(130, 19, '100.00', '200', 'Stock Added', 1, 1, '2026-05-06', NULL, '2026-05-06 18:40:58', '2026-05-06 18:40:58'),
(131, 19, '100.00', '197.00', 'Convert Quotation to Sale', 1, 1, '2026-05-07', NULL, '2026-05-07 12:14:29', '2026-05-07 12:14:29'),
(132, 23, '99.00', '96', 'Sale', 2, 1, '2026-05-08', NULL, '2026-05-08 09:49:15', '2026-05-08 09:49:15'),
(133, 23, '99.00', '96.00', 'Update Sale', 2, NULL, '2026-05-08', NULL, '2026-05-08 09:50:48', '2026-05-08 09:50:48'),
(134, 23, '99.00', '96.00', 'Update Sale', 2, NULL, '2026-05-08', NULL, '2026-05-08 09:52:11', '2026-05-08 09:52:11'),
(135, 23, '99.00', '96.00', 'Update Sale', 2, NULL, '2026-05-08', NULL, '2026-05-08 09:54:15', '2026-05-08 09:54:15'),
(136, 23, '99.00', '96.00', 'Update Sale', 2, NULL, '2026-05-08', NULL, '2026-05-08 09:54:35', '2026-05-08 09:54:35'),
(137, 23, '99.00', '96.00', 'Update Sale', 2, NULL, '2026-05-08', NULL, '2026-05-08 09:55:24', '2026-05-08 09:55:24'),
(138, 23, '99.00', '96.00', 'Update Sale', 2, NULL, '2026-05-08', NULL, '2026-05-08 09:55:37', '2026-05-08 09:55:37'),
(139, 23, '99.00', '96.00', 'Update Sale', 2, NULL, '2026-05-08', NULL, '2026-05-08 10:10:53', '2026-05-08 10:10:53'),
(140, 27, '100', '100', 'Create', 1, 1, '2026-05-26', NULL, '2026-05-26 10:24:13', '2026-05-26 10:24:13'),
(141, 27, '100.00', '102', 'Purchase', 1, 1, '2026-05-27', NULL, '2026-05-27 10:45:48', '2026-05-26 10:45:48'),
(142, 1, '97.7', '98.70', 'Invoice Update (Reversed)', 1, 1, '2026-05-26', NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(143, 1, '98.7', '97.70', 'Invoice Update', 1, 1, '2026-05-26', NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(144, 3, '101', '99.00', 'Invoice Update', 1, 1, '2026-05-26', NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(145, 2, '104', '103.00', 'Invoice Update', 1, 1, '2026-05-26', NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(146, 4, '100', '99.00', 'Invoice Update', 1, 1, '2026-05-26', NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(147, 5, '100', '99.00', 'Invoice Update', 1, 1, '2026-05-26', NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(148, 18, '100', '99.00', 'Invoice Update', 1, 1, '2026-05-26', NULL, '2026-05-26 12:51:11', '2026-05-26 12:51:11'),
(149, 26, '20.00', '20.00', 'Edit', 1, 1, '2026-05-26', NULL, '2026-05-26 17:45:24', '2026-05-26 17:45:24'),
(150, 26, '20.00', '20.00', 'Edit', 1, 1, '2026-05-26', NULL, '2026-05-26 17:45:52', '2026-05-26 17:45:52'),
(151, 28, '8', '8', 'Create', 1, 1, '2026-05-26', NULL, '2026-05-26 17:55:40', '2026-05-26 17:55:40'),
(152, 28, '8.00', '8.00', 'Edit', 1, 1, '2026-05-26', NULL, '2026-05-26 17:56:20', '2026-05-26 17:56:20'),
(153, 28, '8.00', '8.00', 'Edit', 1, 1, '2026-05-26', NULL, '2026-05-26 17:57:05', '2026-05-26 17:57:05'),
(154, 28, '8.00', '8.00', 'Edit', 1, 1, '2026-05-26', NULL, '2026-05-26 17:57:47', '2026-05-26 17:57:47'),
(155, 28, '8.00', '9', 'Purchase', 1, 1, '2026-05-26', NULL, '2026-05-26 18:09:19', '2026-05-26 18:09:19'),
(156, 28, '9', '9.00', 'Purchase Update', 1, 1, '2026-05-26', NULL, '2026-05-26 18:10:28', '2026-05-26 18:10:28'),
(157, 28, '9', '9.00', 'Purchase Update', 1, 1, '2026-05-26', NULL, '2026-05-26 18:11:04', '2026-05-26 18:11:04'),
(158, 28, '9', '9.00', 'Purchase Update', 1, 1, '2026-05-26', NULL, '2026-05-26 18:12:25', '2026-05-26 18:12:25');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `item` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` text DEFAULT NULL,
  `product_gst_details` text DEFAULT NULL,
  `product_gst_total` varchar(255) DEFAULT NULL,
  `purchase_status` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) DEFAULT NULL,
  `amount_total` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` text DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `created_by`, `item`, `quantity`, `price`, `product_gst_details`, `product_gst_total`, `purchase_status`, `payment_status`, `amount_total`, `discount_amount`, `discount_percent`, `vendor_id`, `invoice_id`, `branch_id`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, '1', 1.00, '350', '[]', '0', 'completed', 'completed', 315.00, 35.00, 10.00, 5, '1', 1, 0, '2026-04-03 17:53:58', '2026-04-15 17:47:08'),
(2, 1, '2', 1.00, '1200', '[]', '0', 'completed', 'completed', 1200.00, 0.00, 0.00, 5, '1', 1, 0, '2026-04-03 17:53:58', '2026-04-15 17:47:08'),
(3, 1, '22', 1.00, '90', '[]', '0', 'completed', 'completed', 81.00, 9.00, 10.00, 7, '2', 1, 0, '2026-04-06 12:35:31', '2026-04-06 12:37:17'),
(4, 1, '12', 10.00, '250', '[]', '0', 'partially', 'partially', 2500.00, 0.00, 0.00, 7, '3', 1, 0, '2026-04-15 17:33:42', '2026-04-15 17:33:59'),
(5, 1, '11', 2.00, '650', '[]', '0', 'pending', 'partially', 1300.00, 0.00, 0.00, 5, '4', 1, 0, '2026-04-16 11:37:07', '2026-04-16 11:37:07'),
(6, 1, '2', 1.50, '1200', '[{\"name\":\"CGST\",\"rate\":9,\"amount\":162},{\"name\":\"SGST\",\"rate\":9,\"amount\":162}]', '324', 'pending', 'pending', 1911.60, 212.40, 10.00, 7, '5', 1, 1, '2026-04-24 14:55:10', '2026-04-24 14:59:46'),
(7, 1, '1', 10.20, '100.2', '[{\"name\":\"CGST\",\"rate\":9,\"amount\":91.98360000000001},{\"name\":\"SGST\",\"rate\":9,\"amount\":91.98360000000001}]', '183.9672', 'pending', 'pending', 1181.89, 24.12, 2.00, 13, '6', 1, 0, '2026-04-10 15:30:49', '2026-04-24 15:34:16'),
(8, 1, '2', 1.00, '1200', '[]', '0', 'pending', 'pending', 1200.00, 0.00, 0.00, 5, '7', 1, 0, '2026-04-24 15:37:44', '2026-04-24 15:37:44'),
(9, 1, '12', 1.50, '250.55', '[]', '0', 'partially', 'pending', 375.83, 0.00, 0.00, 7, '8', 1, 0, '2026-04-24 18:04:53', '2026-05-04 11:32:50'),
(10, 1, '23', 1.50, '1000', '[{\"name\":\"CGST\",\"rate\":9,\"amount\":135},{\"name\":\"SGST\",\"rate\":9,\"amount\":135}]', '270', 'partially', 'partially', 1593.00, 177.00, 10.00, 14, '9', 2, 1, '2026-04-27 17:21:07', '2026-04-27 18:16:03'),
(11, 1, '23', 2.50, '1000', '[]', '0', 'partially', 'partially', 2500.00, 0.00, 0.00, 17, '10', 2, 0, '2026-05-04 17:26:58', '2026-05-04 17:54:45'),
(12, 1, '25', 3.00, '2500.5', '[]', '0', 'pending', 'pending', 7501.50, 0.00, 0.00, 16, '11', 1, 0, '2026-05-05 12:02:01', '2026-05-05 12:04:06'),
(13, 1, '26', 10.00, '150', '[]', '0', 'pending', 'partially', 1500.00, 0.00, 0.00, 13, '12', 1, 0, '2026-05-06 10:40:21', '2026-05-06 10:40:21'),
(14, 1, '27', 2.00, '1999', '[{\"name\":\"CGST\",\"rate\":5,\"amount\":199.9}]', '199.9', 'completed', 'completed', 4197.90, 0.00, 0.00, 5, '13', 1, 0, '2026-05-27 10:45:48', '2026-05-26 10:45:48'),
(15, 1, '28', 1.00, '9009', '[]', '0', 'partially', 'completed', 900.90, 8108.10, 90.00, 20, '14', 1, 0, '2026-05-26 18:09:19', '2026-05-26 18:12:25');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_invoice`
--

CREATE TABLE `purchase_invoice` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `bill_no` varchar(255) DEFAULT NULL,
  `products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`products`)),
  `total_amount` decimal(10,2) NOT NULL,
  `paid` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `shipping` decimal(10,2) DEFAULT NULL,
  `taxes` text DEFAULT NULL,
  `grand_total` int(10) NOT NULL,
  `remaining_amount` int(11) DEFAULT NULL,
  `gst_option` varchar(255) NOT NULL DEFAULT 'without_gst',
  `status` varchar(255) DEFAULT NULL,
  `purchase_date` timestamp NULL DEFAULT NULL,
  `remark` varchar(250) DEFAULT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_invoice`
--

INSERT INTO `purchase_invoice` (`id`, `branch_id`, `created_by`, `invoice_number`, `vendor_id`, `bill_no`, `products`, `total_amount`, `paid`, `discount`, `shipping`, `taxes`, `grand_total`, `remaining_amount`, `gst_option`, `status`, `purchase_date`, `remark`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'INV-73273291', 5, '1', '\"[{\\\"product_id\\\":1,\\\"price\\\":350,\\\"quantity\\\":1,\\\"discount_percent\\\":10,\\\"discount_amount\\\":35,\\\"total\\\":315},{\\\"product_id\\\":2,\\\"price\\\":1200,\\\"quantity\\\":1,\\\"discount_percent\\\":0,\\\"discount_amount\\\":0,\\\"total\\\":1200}]\"', 1515.00, 1615.00, 0.00, 100.00, '[]', 1615, 0, 'without_gst', 'completed', NULL, NULL, 0, '2026-04-03 17:53:58', '2026-04-15 17:47:08'),
(2, 1, 1, 'INV-18298834', 7, '100', '\"[{\\\"product_id\\\":22,\\\"price\\\":90,\\\"quantity\\\":1,\\\"discount_percent\\\":10,\\\"discount_amount\\\":9,\\\"total\\\":81}]\"', 81.00, 181.00, 0.00, 100.00, '[]', 181, 0, 'without_gst', 'completed', NULL, NULL, 0, '2026-04-06 12:35:31', '2026-04-06 12:37:17'),
(3, 1, 1, 'INV-94502890', 7, '2153', '\"[{\\\"product_id\\\":12,\\\"price\\\":250,\\\"quantity\\\":10,\\\"discount_percent\\\":0,\\\"discount_amount\\\":0,\\\"total\\\":2500}]\"', 2500.00, 600.00, 0.00, 100.00, '[]', 2600, 2000, 'with_gst', 'partially', NULL, NULL, 0, '2026-04-15 17:33:42', '2026-04-15 17:33:59'),
(4, 1, 1, 'INV-75895609', 5, '100', '\"[{\\\"product_id\\\":11,\\\"price\\\":650,\\\"quantity\\\":2,\\\"discount_percent\\\":0,\\\"discount_amount\\\":0,\\\"total\\\":1300}]\"', 1300.00, NULL, 0.00, 0.00, '[]', 1300, 200, 'without_gst', 'pending', NULL, NULL, 0, '2026-04-16 11:37:07', '2026-04-23 13:38:23'),
(5, 1, 1, 'INV-16047772', 7, '090980', '\"[{\\\"product_id\\\":\\\"2\\\",\\\"price\\\":1200,\\\"quantity\\\":1.5,\\\"discount_percent\\\":10,\\\"discount_amount\\\":212.4,\\\"total\\\":1911.6}]\"', 1911.60, NULL, 0.00, 100.00, '[{\"name\":\"CGST\",\"id\":\"1\",\"rate\":9,\"amount\":162},{\"name\":\"SGST\",\"id\":\"2\",\"rate\":9,\"amount\":162}]', 2012, 2012, 'with_gst', 'pending', '2026-04-24 14:55:10', 'fghgh', 1, '2026-04-24 14:55:10', '2026-04-24 14:59:46'),
(6, 1, 1, 'INV-27074158', 13, '234', '\"[{\\\"product_id\\\":\\\"1\\\",\\\"price\\\":100.2,\\\"quantity\\\":10.2,\\\"discount_percent\\\":2,\\\"discount_amount\\\":24.120144,\\\"total\\\":1181.887056}]\"', 1181.89, NULL, 0.00, 100.00, '[{\"name\":\"CGST\",\"id\":\"1\",\"rate\":9,\"amount\":91.98360000000001},{\"name\":\"SGST\",\"id\":\"2\",\"rate\":9,\"amount\":91.98360000000001}]', 1282, 1282, 'with_gst', 'pending', '2026-04-10 15:30:49', 'fetfewrwe', 0, '2026-04-10 15:30:49', '2026-04-24 15:34:16'),
(7, 1, 1, 'INV-97507077', 5, '5345', '\"[{\\\"product_id\\\":2,\\\"price\\\":1200,\\\"quantity\\\":1,\\\"discount_percent\\\":0,\\\"discount_amount\\\":0,\\\"total\\\":1200}]\"', 1200.00, NULL, 0.00, 0.00, '[]', 1200, 1200, 'without_gst', NULL, '2026-04-24 15:37:44', NULL, 0, '2026-04-24 15:37:44', '2026-04-24 15:37:44'),
(8, 1, 1, 'INV-15135957', 7, '445566', '\"[{\\\"product_id\\\":\\\"12\\\",\\\"price\\\":250.55,\\\"quantity\\\":1.5,\\\"discount_percent\\\":0,\\\"discount_amount\\\":0,\\\"total\\\":375.82500000000005}]\"', 375.83, 100.00, 0.00, 100.00, '[]', 476, 476, 'without_gst', 'partially', '2026-04-24 18:04:53', 'remarks', 0, '2026-04-24 18:04:53', '2026-05-04 11:32:50'),
(9, 2, 1, 'INV-38978344', 14, '01', '\"[{\\\"product_id\\\":\\\"23\\\",\\\"price\\\":1000,\\\"quantity\\\":1.5,\\\"discount_percent\\\":10,\\\"discount_amount\\\":177,\\\"total\\\":1593}]\"', 1593.00, 93.00, 0.00, 100.00, '[{\"name\":\"CGST\",\"id\":\"26\",\"rate\":9,\"amount\":135},{\"name\":\"SGST\",\"id\":\"27\",\"rate\":9,\"amount\":135}]', 1693, 1600, 'with_gst', 'partially', '2026-04-27 17:21:07', 'remarks', 1, '2026-04-27 17:21:07', '2026-04-27 18:16:03'),
(10, 2, 1, 'INV-31695173', 17, '01', '\"[{\\\"product_id\\\":\\\"23\\\",\\\"price\\\":1000,\\\"quantity\\\":2.5,\\\"discount_percent\\\":0,\\\"discount_amount\\\":0,\\\"total\\\":2500}]\"', 2500.00, 100.00, 0.00, 0.00, '[]', 2500, 900, 'without_gst', 'partially', '2026-05-04 17:26:58', NULL, 0, '2026-05-04 17:26:58', '2026-05-04 18:39:16'),
(11, 1, 1, 'INV-13908059', 16, '02', '\"[{\\\"product_id\\\":\\\"25\\\",\\\"price\\\":2500.5,\\\"quantity\\\":3,\\\"discount_percent\\\":0,\\\"discount_amount\\\":0,\\\"total\\\":7501.5}]\"', 7501.50, 0.00, 0.00, 100.00, '[]', 7602, 0, 'without_gst', 'completed', '2026-05-05 12:02:01', 'remarks', 0, '2026-05-05 12:02:01', '2026-05-05 12:06:10'),
(12, 1, 1, 'INV-23203745', 13, '12', '\"[{\\\"product_id\\\":26,\\\"price\\\":150,\\\"quantity\\\":10,\\\"discount_percent\\\":0,\\\"discount_amount\\\":0,\\\"total\\\":1500}]\"', 1500.00, NULL, 0.00, 0.00, '[]', 1500, 1400, 'without_gst', NULL, '2026-05-06 10:40:21', 'iyuiuyi', 0, '2026-05-06 10:40:21', '2026-05-06 10:40:21'),
(13, 1, 1, 'INV-41604013', 5, '0002121', '\"[{\\\"product_id\\\":27,\\\"price\\\":1999,\\\"quantity\\\":2,\\\"discount_percent\\\":0,\\\"discount_amount\\\":0,\\\"total\\\":4197.9}]\"', 4197.90, NULL, 0.00, 100.10, '[{\"id\":\"23\",\"name\":\"CGST\",\"rate\":\"5.00\",\"amount\":199.9}]', 4298, 0, 'with_gst', NULL, '2026-05-27 10:45:48', NULL, 0, '2026-05-26 10:45:48', '2026-05-26 10:45:48'),
(14, 1, 1, 'INV-29190670', 20, 'BILL-3343', '\"[{\\\"product_id\\\":\\\"28\\\",\\\"price\\\":9009,\\\"quantity\\\":1,\\\"discount_percent\\\":90,\\\"discount_amount\\\":8108.1,\\\"total\\\":900.8999999999996}]\"', 900.90, NULL, 0.00, 0.00, '[]', 901, 0, 'without_gst', 'partially', '2026-05-26 18:09:19', NULL, 0, '2026-05-26 18:09:19', '2026-05-26 18:12:25');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `return_no` varchar(255) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `refund_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_returns`
--

INSERT INTO `purchase_returns` (`id`, `purchase_id`, `return_no`, `subtotal`, `discount`, `discount_amount`, `tax_amount`, `shipping`, `total_amount`, `refund_amount`, `branch_id`, `created_by`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 2, 'PR-69E067184E79B', 90.00, 0.00, 9.00, 0.00, 100.00, 181.00, 0.00, 1, 1, 0, '2026-04-16 10:05:36', '2026-04-16 10:05:36'),
(2, 10, 'PR-69F89A7C4F38B', 1500.00, 0.00, 0.00, 0.00, 0.00, 1500.00, 0.00, 2, 1, 0, '2026-05-04 18:39:16', '2026-05-04 18:39:16'),
(3, 11, 'PR-69F98F8FB7A2D', 3750.75, 0.00, 0.00, 0.00, 0.00, 3751.00, 0.00, 1, 1, 0, '2026-05-05 12:04:55', '2026-05-05 12:04:55'),
(4, 11, 'PR-69F98FDABDA3A', 3750.75, 0.00, 0.00, 0.00, 100.00, 3851.00, 0.00, 1, 1, 0, '2026-05-05 12:06:10', '2026-05-05 12:06:10');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_items`
--

CREATE TABLE `purchase_return_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_return_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_item_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(11,3) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `discount` varchar(250) DEFAULT NULL,
  `discount_amount` varchar(250) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `product_gst_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_gst_details`)),
  `product_gst_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_return_items`
--

INSERT INTO `purchase_return_items` (`id`, `purchase_return_id`, `purchase_item_id`, `product_id`, `quantity`, `price`, `discount`, `discount_amount`, `subtotal`, `product_gst_details`, `product_gst_total`, `branch_id`, `created_by`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 22, 1.000, 90.00, '10', '9', 90.00, NULL, 0.00, 1, 1, 0, '2026-04-16 10:05:36', '2026-04-16 10:05:36'),
(2, 2, 11, 23, 1.500, 1000.00, '0', '0', 1500.00, NULL, 0.00, 2, 1, 0, '2026-05-04 18:39:16', '2026-05-04 18:39:16'),
(3, 3, 12, 25, 1.500, 2500.50, '0', '0', 3750.75, NULL, 0.00, 1, 1, 0, '2026-05-05 12:04:55', '2026-05-05 12:04:55'),
(4, 4, 12, 25, 1.500, 2500.50, '0', '0', 3750.75, NULL, 0.00, 1, 1, 0, '2026-05-05 12:06:10', '2026-05-05 12:06:10');

-- --------------------------------------------------------

--
-- Table structure for table `salaries`
--

CREATE TABLE `salaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `staff_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `month` varchar(255) DEFAULT NULL,
  `year` varchar(255) DEFAULT NULL,
  `present` float NOT NULL,
  `absent` int(11) NOT NULL,
  `extra_present` int(11) NOT NULL,
  `advance_pay` decimal(8,2) NOT NULL,
  `salary` decimal(8,2) NOT NULL,
  `extra_amount` decimal(8,2) NOT NULL,
  `total_salary` decimal(8,2) NOT NULL,
  `old_advance_pay` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_labour_items`
--

CREATE TABLE `sales_labour_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `labour_item_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_labour_items`
--

INSERT INTO `sales_labour_items` (`id`, `order_id`, `user_id`, `labour_item_id`, `qty`, `price`, `created_at`, `updated_at`) VALUES
(3, 4, 3, 3, 1, 1200.00, '2026-04-08 16:47:34', '2026-04-08 16:47:34'),
(4, 4, 3, 2, 1, 800.00, '2026-04-08 16:47:34', '2026-04-08 16:47:34'),
(5, 5, 3, 3, 1, 1200.00, '2026-04-13 18:02:00', '2026-04-13 18:02:00'),
(6, 6, 3, 3, 1, 1200.00, '2026-04-13 18:02:29', '2026-04-13 18:02:29'),
(7, 20, 3, 3, 1, 1200.00, '2026-04-24 18:17:31', '2026-04-24 18:17:31'),
(9, 21, 4, 3, 1, 100.00, '2026-04-27 16:38:29', '2026-04-27 16:38:29'),
(10, 22, 15, 4, 1, 100.00, '2026-04-27 18:03:27', '2026-04-27 18:03:27'),
(12, 23, 11, 5, 1, 250.00, '2026-04-27 18:08:31', '2026-04-27 18:08:31'),
(13, 25, 3, 3, 1, 1200.00, '2026-05-04 11:55:28', '2026-05-04 11:55:28'),
(16, 28, 6, 3, 1, 1200.00, '2026-05-05 12:25:34', '2026-05-05 12:25:34');

-- --------------------------------------------------------

--
-- Table structure for table `sales_returns`
--

CREATE TABLE `sales_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `return_number` varchar(255) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `refund_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_returns`
--

INSERT INTO `sales_returns` (`id`, `order_id`, `return_number`, `subtotal`, `discount`, `discount_amount`, `tax_amount`, `total_amount`, `refund_amount`, `branch_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 8, 'SR-1776314108', 200.00, 0.00, 0.00, 0.00, 200.00, 0.00, 1, 1, '2026-04-16 10:05:08', '2026-04-16 10:05:08'),
(2, 27, 'SR-1777897448', 1000.00, 0.00, 0.00, 0.00, 1000.00, 0.00, 2, 1, '2026-05-04 17:54:08', '2026-05-04 17:54:08'),
(3, 27, 'SR-1777900178', 500.00, 0.00, 0.00, 0.00, 500.00, 0.00, 2, 1, '2026-05-04 18:39:38', '2026-05-04 18:39:38'),
(4, 9, 'SR-1778052003', 70000.00, 0.00, 8400.00, 0.00, 61600.00, 0.00, 1, 1, '2026-05-06 12:50:03', '2026-05-06 12:50:03');

-- --------------------------------------------------------

--
-- Table structure for table `sales_return_items`
--

CREATE TABLE `sales_return_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sales_return_id` bigint(20) UNSIGNED NOT NULL,
  `order_item_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(11,3) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `discount` varchar(250) DEFAULT NULL,
  `discount_amount` varchar(250) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `product_gst_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_gst_details`)),
  `product_gst_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_return_items`
--

INSERT INTO `sales_return_items` (`id`, `sales_return_id`, `order_item_id`, `product_id`, `quantity`, `price`, `discount`, `discount_amount`, `subtotal`, `product_gst_details`, `product_gst_total`, `created_at`, `updated_at`) VALUES
(1, 1, 16, 22, 2.000, 100.00, '0.00', '0', 200.00, NULL, 0.00, '2026-04-16 10:05:08', '2026-04-16 10:05:08'),
(2, 2, 59, 23, 1.000, 1000.00, '0.00', '0', 1000.00, NULL, 0.00, '2026-05-04 17:54:08', '2026-05-04 17:54:08'),
(3, 3, 59, 23, 0.500, 1000.00, '0.00', '0', 500.00, NULL, 0.00, '2026-05-04 18:39:38', '2026-05-04 18:39:38'),
(4, 4, 18, 9, 10.000, 7000.00, '12.00', '8400', 70000.00, NULL, 0.00, '2026-05-06 12:50:03', '2026-05-06 12:50:03');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `gst_num` varchar(255) DEFAULT NULL,
  `low_stock` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `state_code` varchar(255) DEFAULT NULL,
  `currency_symbol` text DEFAULT NULL,
  `currency_position` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `alt_phone` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `ac_no` varchar(255) DEFAULT NULL,
  `ifsc_code` varchar(255) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `working_hours` varchar(255) DEFAULT NULL,
  `sunday_off` varchar(255) DEFAULT NULL,
  `grace_period` varchar(255) DEFAULT NULL,
  `lunch_break` varchar(255) DEFAULT NULL,
  `open_time` varchar(255) DEFAULT NULL,
  `close_time` varchar(255) DEFAULT NULL,
  `yearly_holidays` varchar(255) DEFAULT NULL,
  `invoice_size` varchar(200) DEFAULT NULL,
  `send_mail` tinyint(1) DEFAULT 1,
  `customer_whatsapp_message` tinyint(1) NOT NULL DEFAULT 0,
  `admin_whatsapp_message` tinyint(1) NOT NULL DEFAULT 0,
  `appointment_reminder_hours_before` tinyint(3) NOT NULL DEFAULT 3 COMMENT '	Send meeting/follow-up admin reminders this many hours before scheduled time (IST)',
  `admin_whatsapp_number` varchar(250) DEFAULT NULL,
  `financial_year` tinyint(1) NOT NULL DEFAULT 1,
  `tds_apply` tinyint(1) NOT NULL DEFAULT 1,
  `cin_no` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `branch_id`, `name`, `email`, `phone`, `gst_num`, `low_stock`, `address`, `state_code`, `currency_symbol`, `currency_position`, `logo`, `favicon`, `alt_phone`, `bank_name`, `branch`, `ac_no`, `ifsc_code`, `qr_code`, `working_hours`, `sunday_off`, `grace_period`, `lunch_break`, `open_time`, `close_time`, `yearly_holidays`, `invoice_size`, `send_mail`, `customer_whatsapp_message`, `admin_whatsapp_message`, `appointment_reminder_hours_before`, `admin_whatsapp_number`, `financial_year`, `tds_apply`, `cin_no`, `created_at`, `updated_at`) VALUES
(1, 1, 'Fablead Developer & Technolab', 'fabinfo@gmail.com', '132123121', 'GST1234', 11, '5001, Adajan Gam', '24', '₹', 'left', 'logos/C8sWoajLCBkWZlOJhmSLDke4y0t5aCOSq6D6hJ7t.webp', 'favicons/5YjVF6A7si8v9WjoA93b412GOxToHJK0Enr79dZZ.webp', NULL, 'YES BANK', 'Adajan', '354235344', 'YES123452101', 'qr_codes/6XhLAOR2wzgKdE2x8KbkDQYQ6dJS2P5RmXSGp0cf.webp', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'big', 0, 0, 0, 3, NULL, 0, 0, NULL, '2025-04-04 06:04:17', '2026-05-20 10:12:40'),
(2, 28, 'Surat Shop', 'shop@gmail.com', '1321321321', '1321321', 10, '5001 Ascon Plaza Near, Adajan Gam', NULL, '₹', 'left', 'logos/1Ya4X9auwY3ovTDrWPmeXv2W7h8NqvUhQF72xLD0.png', 'favicons/mwrIZ9oJ8RicO8aaKooXBKXvjvC0agKKASRk1JVM.png', NULL, 'Bank Of Baroda', 'Adajan', '3215215512', 'IFSCS655656', 'qr_codes/JBMuHpDY9xVR6TxHzQuXESAReha3bQfNt5O7ZQKs.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'big', 0, 0, 0, 3, NULL, 1, 1, NULL, '2025-09-02 12:15:03', '2026-04-03 11:45:43'),
(4, 2, 'Fablead Developer & Technolab', 'fabinfo@gmail.com', '9368745859', 'GST1234', 10, 'adajan', '24', '₹', 'left', 'logos/0UhWw3EGcj2VobPe6LXqCLe6ItV1aBi5QPgHYDjm.webp', 'favicons/dXs8OMi0f1ARmMd2g6c9lYlqqTaXd94T8MXMUUHS.webp', NULL, 'YES BANK', 'Adajan', '167312232334', 'YES123452101', 'qr_codes/ZA3bGrhpbW0SDq0JtERw74ujgiNfeo6FEc3HG2sC.webp', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'big', 1, 0, 0, 3, NULL, 1, 1, NULL, '2026-04-27 17:28:13', '2026-04-27 17:28:13');

-- --------------------------------------------------------

--
-- Table structure for table `smtp_settings`
--

CREATE TABLE `smtp_settings` (
  `id` bigint(25) NOT NULL,
  `mailer` varchar(255) NOT NULL DEFAULT 'smtp',
  `host` varchar(255) NOT NULL,
  `port` int(20) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `encryption` varchar(255) DEFAULT NULL,
  `from_address` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `branch_id` int(25) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `smtp_settings`
--

INSERT INTO `smtp_settings` (`id`, `mailer`, `host`, `port`, `username`, `password`, `encryption`, `from_address`, `from_name`, `status`, `branch_id`, `created_at`, `updated_at`) VALUES
(1, 'smtp', 'smtp.gmail.com', 587, 'pnaitik.fablead@gmail.com', 'eyJpdiI6ImhuMVFKODZzVC94ODFEWm1uNmMyV1E9PSIsInZhbHVlIjoidGFVWTFsTU9JSENPSnNtRk44Vkl3Zz09IiwibWFjIjoiNDU2ZGIyN2U4NmQzZWRkMGIxY2M5ZDVjZDZlMDgwM2NhOWJlY2RjZTM4Y2Q5ODZkMGUxYjE4Mjk2YjE1YjUyNiIsInRhZyI6IiJ9', 'tls', 'pnaitik.fablead@gmail.com', '', 1, 1, '2026-03-30 06:29:36', '2026-04-01 12:35:46');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tax_name` varchar(255) NOT NULL,
  `tax_rate` decimal(8,2) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `branch_id` int(11) DEFAULT NULL,
  `isDeleted` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `tax_name`, `tax_rate`, `status`, `branch_id`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 'CGST', 9.00, 'active', 1, 0, '2025-05-29 12:13:20', '2026-01-16 11:24:03'),
(2, 'SGST', 9.00, 'active', 1, 0, '2025-05-30 16:00:49', '2026-01-16 11:24:12'),
(19, 'CGST', 10.00, 'active', 28, 0, '2025-09-19 05:31:23', '2025-09-19 05:31:23'),
(20, 'SGST', 10.00, 'active', 28, 0, '2025-09-19 05:31:32', '2025-09-19 05:31:32'),
(21, 'f', 2.00, 'active', 28, 1, '2025-11-06 13:13:40', '2025-11-06 13:14:11'),
(22, 'sgst', 10.00, 'active', 1, 1, '2025-11-20 16:20:21', '2025-11-20 16:20:33'),
(23, 'CGST', 5.00, 'active', 1, 0, '2026-01-07 11:22:51', '2026-03-27 13:18:50'),
(24, 'IGST', 18.00, 'active', 28, 0, '2026-01-27 13:35:39', '2026-01-27 13:35:39'),
(25, 'sc', 10.00, 'active', 1, 1, '2026-03-13 12:32:34', '2026-03-13 12:32:41'),
(26, 'CGST', 9.00, 'active', 2, 0, '2026-04-27 16:49:22', '2026-04-27 16:49:22'),
(27, 'SGST', 9.00, 'active', 2, 0, '2026-04-27 16:49:34', '2026-04-27 16:49:34'),
(28, 'IGST', 5.00, 'active', 2, 0, '2026-04-27 16:49:43', '2026-04-27 16:49:51');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unit_name` varchar(255) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit_name`, `created_by`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'Pc', 1, 0, '2026-04-03 12:09:24', '2026-04-03 12:09:24'),
(2, 'Set', 1, 0, '2026-04-03 12:10:05', '2026-04-03 12:10:05'),
(3, 'Pair', 1, 0, '2026-04-03 12:10:15', '2026-04-03 12:10:15'),
(4, 'Liter', 1, 0, '2026-04-03 15:35:30', '2026-04-03 15:35:59'),
(5, 'ML', 1, 0, '2026-04-03 15:42:11', '2026-04-03 15:42:11'),
(6, 'kg', 1, 0, '2026-04-06 12:33:39', '2026-04-06 12:33:39'),
(7, 'KGS', 2, 0, '2026-04-27 16:45:08', '2026-04-27 16:45:08'),
(8, 'liters', 2, 0, '2026-04-27 16:45:38', '2026-04-27 16:45:38'),
(9, 'Large', 2, 0, '2026-04-27 16:47:17', '2026-04-27 16:47:17'),
(10, 'Medium', 2, 0, '2026-04-27 16:47:41', '2026-04-27 16:47:41'),
(11, 'Small', 2, 0, '2026-04-27 16:47:50', '2026-04-27 16:47:50'),
(12, '500', 1, 0, '2026-05-26 11:05:49', '2026-05-26 11:05:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `gst_number` varchar(20) DEFAULT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `state_code` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profile_image` text DEFAULT NULL,
  `role` varchar(255) DEFAULT 'user',
  `status` tinyint(1) DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `haspermission` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `branch_id`, `name`, `company_name`, `email`, `gst_number`, `pan_number`, `phone`, `state_code`, `email_verified_at`, `password`, `profile_image`, `role`, `status`, `remember_token`, `haspermission`, `created_by`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Admin', NULL, 'admin@gmail.com', NULL, NULL, '9uh97hy9', '24', NULL, '$2y$10$Tz6HKfbDf.xPw/f7dGjSx.F2jI0AsG.snMBrikO5NAoWVFipHcqD.', NULL, 'admin', 1, NULL, NULL, NULL, 0, '2026-04-03 11:53:52', '2026-05-26 17:04:48'),
(2, NULL, 'Sub Admin', NULL, 'subadmin@gmail.com', NULL, NULL, NULL, '24', NULL, '$2y$10$Tz6HKfbDf.xPw/f7dGjSx.F2jI0AsG.snMBrikO5NAoWVFipHcqD.', NULL, 'sub-admin', 1, NULL, NULL, NULL, 0, '2026-04-03 11:53:52', '2026-04-03 11:53:52'),
(3, 1, 'Default Customer', NULL, 'default_customer@gmail.com', NULL, NULL, '1234567890', '24', NULL, NULL, NULL, 'customer', 1, NULL, NULL, 1, 0, '2026-04-03 12:19:27', '2026-04-06 07:33:46'),
(4, 1, 'BABU SINGH', NULL, 'babu@gmail.com', '24FQSPS8154J1ZX', 'FQSPS8154J', '3216547890', '24', NULL, NULL, NULL, 'customer', 1, NULL, NULL, 1, 0, '2026-04-03 12:20:20', '2026-04-03 12:20:20'),
(5, 1, 'SANDEEP KUMAR JAIN', NULL, NULL, '23ADZPJ0191R1ZS', 'ADZPJ0191R', '5666456546', '23', NULL, NULL, NULL, 'vendor', 1, NULL, NULL, 1, 0, '2026-04-03 15:46:47', '2026-04-03 15:46:47'),
(6, 1, 'SHITALBEN JIGNESHBHAI DESAI', 'A K POLYMERS', NULL, '24BBHPD8316B1Z7', 'BBHPD8316B', '5465645645', '24', NULL, NULL, NULL, 'customer', 1, NULL, NULL, 1, 0, '2026-04-03 15:47:05', '2026-05-06 18:08:33'),
(7, 1, 'BABU SINGH', NULL, NULL, '24FQSPS8154J1ZX', 'FQSPS8154J', '5465465445', '24', NULL, NULL, NULL, 'vendor', 1, NULL, NULL, 1, 0, '2026-04-03 15:47:25', '2026-04-03 15:47:25'),
(10, 1, 'Default Staff', NULL, 'defaultstaff@gmail.com', NULL, NULL, '2313123213', NULL, NULL, '$2y$12$kq/O/S0JCMiouSe7hBhRieccUNsmZMPZ0n9B5FYTSvHKiitvSGjbq', NULL, 'staff', 1, NULL, '1', 1, 0, '2026-04-03 15:49:52', '2026-04-06 07:32:36'),
(11, 2, 'Default Customer', NULL, 'default_customer34@gmail.com', NULL, 'AIVPV8105A', '1234567890', '24', NULL, NULL, NULL, 'customer', 1, NULL, NULL, 2, 0, '2026-04-03 12:19:27', '2026-04-06 07:33:30'),
(12, 2, 'Raj Singh', NULL, 'raj@gmail.com', NULL, NULL, '7898798789', NULL, NULL, '$2y$12$vmpC1w/Tqncl.jI8rXl0Fuh2p8dCJQcfsiCMxkd.C/T5fJafCuZ2.', NULL, 'staff', 1, NULL, '1', NULL, 0, '2026-04-15 19:04:46', '2026-04-15 19:04:46'),
(13, 1, 'Default Vendorr', NULL, NULL, '345345345345', '435345435', '2352345345', '24', NULL, NULL, NULL, 'vendor', 1, NULL, NULL, 1, 0, '2026-04-24 15:30:13', '2026-04-24 15:30:13'),
(14, 2, 'axay', NULL, 'axay@gmail.com', NULL, NULL, '9898676789', '', NULL, NULL, NULL, 'vendor', 1, NULL, NULL, 1, 0, '2026-04-27 17:20:30', '2026-04-27 17:20:30'),
(15, 2, 'Harsh Patel', NULL, 'harsh@gmail.com', NULL, NULL, '7874531005', '', NULL, NULL, NULL, 'customer', 1, NULL, NULL, 1, 0, '2026-04-27 17:31:40', '2026-04-27 17:31:40'),
(16, 1, 'test', NULL, NULL, NULL, NULL, '1234560000', '', NULL, NULL, NULL, 'vendor', 1, NULL, NULL, 1, 0, '2026-05-01 12:06:33', '2026-05-01 12:06:33'),
(17, 2, 'vijay', NULL, 'vijay@gmail.com', NULL, NULL, '9784653120', '', NULL, NULL, NULL, 'vendor', 1, NULL, NULL, 1, 0, '2026-05-04 17:26:24', '2026-05-04 17:26:24'),
(18, 1, 'RAMKISHORE GANPATRAM BISHNOI', 'ADITYA INFOTECH', NULL, '24AJPPB5119B1ZQ', 'AJPPB5119B', '7874531050', '24', NULL, NULL, NULL, 'customer', 1, NULL, NULL, 1, 0, '2026-05-06 18:09:53', '2026-05-06 18:09:53'),
(19, 1, 'Tejas Patel', NULL, 'tejasfablead@gmail.com', NULL, NULL, '7874531055', NULL, NULL, '$2y$12$wl2ByNLDf5FFXFk3W89ydeyRny3gfx60jZkmEfBKOBV3Nh3OeJwza', NULL, 'staff', 1, NULL, '1', NULL, 0, '2026-05-26 09:49:10', '2026-05-26 09:49:10'),
(20, 1, 'Sneh Chaudhary', NULL, 'fablead.sneh@gmail.com', NULL, NULL, '8460335764', '05', NULL, NULL, NULL, 'vendor', 1, NULL, NULL, 1, 0, '2026-05-26 18:08:20', '2026-05-26 18:08:20'),
(21, 1, 'testtt', 'ADITYA INFOTECH', 'tejasfaeeblead@gmail.com', NULL, NULL, '9875641200', NULL, NULL, '$2y$12$Vv5RDLl0f9wHjsqFodFbt.Qnuyun10GTXKHuAC.yA4JQ5O0jTLfUK', NULL, 'customer', 1, NULL, NULL, 1, 1, '2026-05-26 18:55:10', '2026-05-26 18:55:21');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `isDeleted` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `user_id`, `address`, `city`, `country`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, 'Adajan', 'Surat', 'India', 0, '2026-04-03 11:58:45', '2026-04-03 11:58:45'),
(2, 2, 'Adajan', 'Surat', 'India', 0, '2026-04-03 11:58:45', '2026-04-03 11:58:45'),
(3, 3, 'Millenium Textile Market, Ring Road, Surat, Surat, Gujarat - 395002', 'Surat', 'India', 0, '2026-04-03 12:19:27', '2026-04-03 12:19:27'),
(4, 4, 'INSIDE GOODLUCK MKT, UMARWADA, RING ROAD, Surat, Gujarat - 395002', 'Surat', 'India', 0, '2026-04-03 12:20:20', '2026-04-03 12:20:20'),
(5, 5, 'Satna Building, Gole Bazar Road, Super Market, Jabalpur, Madhya Pradesh - 482001', 'Jabalpur', 'India', 0, '2026-04-03 15:46:47', '2026-04-03 15:46:47'),
(6, 6, 'MATRUSHAKTI SOCIETY, PUNA GAM, CHORYASI, SURAT, Surat, Gujarat - 395010', 'Surat', 'India', 0, '2026-04-03 15:47:05', '2026-04-03 15:47:05'),
(7, 7, 'INSIDE GOODLUCK MKT, UMARWADA, RING ROAD, Surat, Gujarat - 395002', 'Surat', 'India', 0, '2026-04-03 15:47:25', '2026-04-03 15:47:25'),
(10, 10, NULL, NULL, NULL, 0, '2026-04-03 15:49:52', '2026-04-03 15:49:52'),
(11, 12, NULL, NULL, NULL, 0, '2026-04-15 19:04:46', '2026-04-15 19:04:46'),
(12, 13, NULL, 'Surat', 'India', 0, '2026-04-24 15:30:13', '2026-04-24 15:30:13'),
(13, 14, 'surat', NULL, NULL, 0, '2026-04-27 17:20:30', '2026-04-27 17:20:30'),
(14, 15, 'surat', NULL, NULL, 0, '2026-04-27 17:31:40', '2026-04-27 17:31:40'),
(15, 16, NULL, NULL, NULL, 0, '2026-05-01 12:06:33', '2026-05-01 12:06:33'),
(16, 17, NULL, NULL, NULL, 0, '2026-05-04 17:26:24', '2026-05-04 17:26:24'),
(17, 18, 'AJANTA SHOPPING CENTER, SURAT TEXTILE MARKET, RINGROAD, Surat, Gujarat - 394150', 'Surat', 'India', 0, '2026-05-06 18:09:53', '2026-05-06 18:09:53'),
(18, 19, NULL, NULL, 'india', 0, '2026-05-26 09:49:10', '2026-05-26 09:49:10'),
(19, 20, 'D-204, Vasupujya Green Apartment, Ichhapore-3, Surat', 'Surat', 'India', 0, '2026-05-26 18:08:20', '2026-05-26 18:08:20'),
(20, 21, 'surat', NULL, NULL, 1, '2026-05-26 18:55:10', '2026-05-26 18:55:21');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `module_id` bigint(20) UNSIGNED NOT NULL,
  `view` tinyint(1) NOT NULL DEFAULT 0,
  `add` tinyint(1) NOT NULL DEFAULT 0,
  `edit` tinyint(1) NOT NULL DEFAULT 0,
  `delete` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `module_id`, `view`, `add`, `edit`, `delete`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, '2025-09-18 15:58:21', '2025-09-18 15:58:21'),
(2, 1, 2, 1, 1, 1, 1, '2025-09-18 15:58:21', '2025-09-18 15:58:21'),
(3, 1, 3, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(4, 1, 4, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(5, 1, 5, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(6, 1, 6, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-19 15:59:10'),
(7, 1, 7, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(8, 1, 15, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(9, 1, 9, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(10, 1, 10, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(11, 1, 11, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(12, 1, 12, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(13, 1, 13, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(14, 1, 14, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(15, 150, 1, 1, 1, 1, 1, '2025-09-18 10:37:28', '2025-09-18 10:37:28'),
(16, 150, 2, 1, 1, 1, 1, '2025-09-18 10:37:28', '2026-01-02 12:52:37'),
(17, 150, 3, 1, 1, 1, 1, '2025-09-18 10:37:28', '2025-09-18 10:37:28'),
(18, 150, 4, 1, 1, 1, 1, '2025-09-18 10:37:28', '2026-01-02 12:52:37'),
(19, 150, 5, 1, 1, 1, 1, '2025-09-18 10:37:28', '2025-09-18 10:37:28'),
(20, 150, 6, 0, 0, 0, 0, '2025-09-18 10:37:28', '2025-09-18 10:37:28'),
(21, 150, 7, 1, 1, 1, 1, '2025-09-18 10:37:28', '2025-09-18 10:37:39'),
(22, 150, 9, 1, 1, 1, 1, '2025-09-18 10:37:28', '2026-01-02 12:52:37'),
(23, 150, 10, 1, 1, 1, 1, '2025-09-18 10:37:28', '2026-01-02 12:52:37'),
(24, 150, 11, 0, 0, 0, 0, '2025-09-18 10:37:28', '2025-09-18 10:37:28'),
(25, 150, 12, 0, 0, 0, 0, '2025-09-18 10:37:28', '2025-09-18 10:37:28'),
(26, 150, 13, 0, 0, 0, 0, '2025-09-18 10:37:28', '2025-09-18 10:37:28'),
(27, 150, 14, 0, 0, 0, 0, '2025-09-18 10:37:28', '2025-09-18 10:37:28'),
(28, 150, 15, 0, 0, 0, 0, '2025-09-18 10:37:28', '2025-09-18 10:37:28'),
(29, 1, 8, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(40, 151, 13, 1, 1, 1, 1, '2025-09-18 10:58:53', '2025-09-18 10:58:53'),
(41, 151, 14, 1, 1, 1, 1, '2025-09-18 10:58:53', '2025-09-18 10:58:53'),
(42, 151, 15, 1, 1, 1, 1, '2025-09-18 10:58:53', '2025-09-18 10:58:53'),
(43, 151, 1, 1, 1, 1, 1, '2025-09-18 11:17:48', '2025-09-18 11:17:48'),
(44, 151, 2, 1, 1, 1, 1, '2025-09-18 11:17:48', '2025-09-18 11:17:48'),
(45, 152, 1, 1, 1, 1, 1, '2025-09-18 12:29:28', '2025-09-18 12:29:28'),
(46, 152, 4, 1, 1, 1, 1, '2025-09-18 12:29:28', '2025-09-18 12:29:28'),
(47, 152, 6, 1, 1, 1, 1, '2025-09-18 12:29:28', '2025-09-18 12:29:28'),
(48, 152, 7, 1, 1, 1, 1, '2025-09-18 12:29:28', '2025-09-18 12:29:28'),
(49, 152, 8, 1, 1, 1, 1, '2025-09-18 12:29:28', '2025-09-18 12:29:28'),
(50, 152, 9, 1, 1, 1, 1, '2025-09-18 12:29:28', '2025-09-18 12:29:28'),
(51, 152, 10, 1, 1, 1, 1, '2025-09-18 12:29:28', '2025-09-18 12:29:28'),
(52, 152, 11, 1, 1, 1, 1, '2025-09-18 12:29:28', '2025-09-18 12:29:28'),
(53, 152, 13, 1, 1, 1, 1, '2025-09-18 12:29:28', '2025-09-18 12:29:28'),
(54, 152, 14, 1, 1, 1, 1, '2025-09-18 12:29:28', '2025-09-18 12:29:28'),
(55, 152, 15, 1, 1, 1, 1, '2025-09-18 12:29:28', '2025-09-18 12:29:28'),
(56, 153, 1, 1, 1, 1, 1, '2025-09-18 12:36:14', '2025-09-18 12:36:14'),
(57, 153, 2, 1, 1, 1, 1, '2025-09-18 12:36:14', '2025-09-18 12:36:14'),
(58, 153, 3, 1, 1, 1, 1, '2025-09-18 12:36:14', '2025-09-18 12:36:14'),
(59, 153, 4, 1, 1, 1, 1, '2025-09-18 12:36:14', '2025-09-18 12:36:14'),
(60, 153, 5, 1, 1, 1, 1, '2025-09-18 12:36:14', '2025-09-18 12:36:14'),
(61, 153, 8, 1, 1, 1, 1, '2025-09-18 12:36:14', '2025-09-18 12:36:14'),
(62, 153, 9, 1, 1, 1, 1, '2025-09-18 12:36:14', '2025-09-18 12:36:14'),
(63, 153, 10, 1, 1, 1, 1, '2025-09-18 12:36:14', '2025-09-18 12:36:14'),
(64, 154, 1, 1, 1, 1, 1, '2025-09-18 12:39:40', '2025-09-18 12:39:40'),
(65, 154, 2, 1, 1, 1, 1, '2025-09-18 12:39:40', '2025-09-18 12:39:40'),
(66, 154, 3, 1, 1, 1, 1, '2025-09-18 12:39:40', '2025-09-18 12:39:40'),
(67, 154, 4, 1, 1, 1, 1, '2025-09-18 12:39:40', '2025-09-18 12:39:40'),
(68, 154, 13, 1, 1, 1, 1, '2025-09-18 12:39:40', '2025-09-18 12:39:40'),
(69, 154, 14, 1, 1, 1, 1, '2025-09-18 12:39:40', '2025-09-18 12:39:40'),
(70, 154, 15, 1, 1, 1, 1, '2025-09-18 12:39:40', '2025-09-18 12:39:40'),
(71, 155, 1, 1, 1, 1, 1, '2025-09-18 12:47:16', '2025-09-18 12:47:16'),
(72, 155, 2, 1, 1, 1, 1, '2025-09-18 12:47:16', '2025-09-18 12:47:16'),
(73, 155, 4, 1, 1, 1, 1, '2025-09-18 12:47:16', '2025-09-18 12:47:16'),
(74, 155, 5, 1, 1, 1, 1, '2025-09-18 12:47:16', '2025-09-18 12:47:16'),
(75, 158, 1, 1, 1, 1, 1, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(76, 158, 2, 1, 1, 1, 1, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(77, 158, 3, 1, 1, 1, 1, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(78, 158, 4, 0, 1, 1, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(79, 158, 5, 0, 1, 0, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(80, 158, 6, 0, 0, 1, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:35'),
(81, 158, 7, 0, 0, 1, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:35'),
(82, 158, 8, 0, 0, 0, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(83, 158, 9, 0, 0, 0, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(84, 158, 10, 0, 0, 0, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(85, 158, 11, 0, 0, 0, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(86, 158, 12, 0, 0, 0, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(87, 158, 13, 0, 0, 0, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(88, 158, 14, 0, 0, 0, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(89, 158, 15, 0, 0, 0, 0, '2025-09-18 13:05:03', '2025-09-18 13:05:03'),
(90, 159, 1, 1, 0, 1, 1, '2025-09-18 13:12:39', '2025-09-18 13:12:39'),
(91, 159, 2, 1, 0, 1, 1, '2025-09-18 13:12:39', '2025-09-18 13:12:39'),
(92, 159, 3, 1, 0, 1, 1, '2025-09-18 13:12:39', '2025-09-18 13:12:39'),
(93, 159, 4, 1, 0, 1, 1, '2025-09-18 13:12:39', '2025-09-18 13:12:39'),
(94, 159, 9, 1, 0, 1, 1, '2025-09-18 13:12:39', '2025-09-18 13:12:39'),
(95, 159, 10, 1, 0, 1, 1, '2025-09-18 13:12:39', '2025-09-18 13:12:39'),
(96, 159, 11, 1, 0, 1, 1, '2025-09-18 13:12:39', '2025-09-18 13:12:39'),
(97, 159, 12, 1, 0, 1, 1, '2025-09-18 13:12:39', '2025-09-18 13:12:39'),
(98, 160, 1, 1, 0, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(99, 160, 2, 1, 1, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(100, 160, 3, 0, 1, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(101, 160, 4, 1, 1, 1, 1, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(102, 160, 5, 1, 1, 1, 1, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(103, 160, 6, 0, 0, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(104, 160, 7, 0, 0, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(105, 160, 8, 0, 0, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(106, 160, 9, 0, 0, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(107, 160, 10, 0, 0, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(108, 160, 11, 0, 0, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(109, 160, 12, 0, 0, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(110, 160, 13, 0, 0, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(111, 160, 14, 0, 0, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(112, 160, 15, 0, 0, 0, 0, '2025-09-18 13:14:27', '2025-09-18 13:14:27'),
(113, 161, 1, 1, 1, 1, 1, '2025-09-18 13:20:23', '2025-09-18 13:20:23'),
(114, 161, 2, 1, 1, 1, 1, '2025-09-18 13:20:23', '2025-09-18 13:20:23'),
(115, 161, 3, 1, 1, 1, 1, '2025-09-18 13:20:23', '2025-09-18 13:20:23'),
(116, 161, 4, 1, 1, 1, 1, '2025-09-18 13:20:23', '2025-09-18 13:20:23'),
(117, 161, 5, 1, 1, 1, 1, '2025-09-18 13:20:23', '2025-09-18 13:20:23'),
(118, 162, 10, 1, 1, 1, 1, '2025-09-18 13:30:53', '2025-09-18 13:30:53'),
(119, 162, 11, 1, 1, 1, 1, '2025-09-18 13:30:53', '2025-09-18 13:30:53'),
(120, 162, 12, 1, 1, 1, 1, '2025-09-18 13:30:53', '2025-09-18 13:30:53'),
(121, 162, 13, 1, 1, 1, 1, '2025-09-18 13:30:53', '2025-09-18 13:30:53'),
(122, 162, 14, 1, 1, 1, 1, '2025-09-18 13:30:53', '2025-09-18 13:30:53'),
(123, 162, 15, 1, 1, 1, 1, '2025-09-18 13:30:53', '2025-09-18 13:30:53'),
(124, 163, 1, 1, 1, 1, 1, '2025-09-18 13:43:36', '2025-09-18 13:43:36'),
(125, 163, 2, 1, 1, 1, 1, '2025-09-18 13:43:36', '2025-09-18 13:43:36'),
(126, 163, 3, 1, 1, 1, 1, '2025-09-18 13:43:36', '2025-09-18 13:43:36'),
(127, 163, 4, 1, 1, 1, 1, '2025-09-18 13:43:36', '2025-09-18 13:43:36'),
(128, 163, 15, 1, 1, 1, 1, '2025-09-18 13:43:36', '2025-09-18 13:43:36'),
(136, 165, 1, 1, 1, 1, 1, '2025-09-19 05:01:29', '2025-09-19 05:01:29'),
(137, 165, 12, 1, 1, 1, 1, '2025-09-19 05:01:29', '2025-09-19 05:01:29'),
(138, 165, 13, 1, 1, 1, 1, '2025-09-19 05:01:29', '2025-09-19 05:01:29'),
(139, 165, 14, 1, 1, 1, 1, '2025-09-19 05:01:29', '2025-09-19 05:01:29'),
(140, 165, 15, 1, 1, 1, 1, '2025-09-19 05:01:29', '2025-09-19 05:01:29'),
(141, 166, 10, 1, 1, 1, 1, '2025-09-19 05:04:38', '2025-09-19 05:04:38'),
(142, 166, 11, 1, 1, 1, 1, '2025-09-19 05:04:38', '2025-09-19 05:04:38'),
(143, 166, 12, 1, 1, 1, 1, '2025-09-19 05:04:38', '2025-09-19 05:04:38'),
(144, 166, 13, 1, 1, 1, 1, '2025-09-19 05:04:38', '2025-09-19 05:04:38'),
(145, 166, 14, 1, 1, 1, 1, '2025-09-19 05:04:38', '2025-09-19 05:04:38'),
(146, 166, 15, 1, 1, 1, 1, '2025-09-19 05:04:38', '2025-09-19 05:04:38'),
(147, 167, 9, 1, 1, 1, 1, '2025-09-19 05:15:41', '2025-09-19 05:15:41'),
(148, 167, 10, 1, 1, 1, 1, '2025-09-19 05:15:41', '2025-09-19 05:15:41'),
(149, 167, 11, 1, 1, 1, 1, '2025-09-19 05:15:41', '2025-09-19 05:15:41'),
(150, 167, 12, 1, 1, 1, 1, '2025-09-19 05:15:41', '2025-09-19 05:15:41'),
(151, 167, 13, 1, 1, 1, 1, '2025-09-19 05:15:41', '2025-09-19 05:15:41'),
(152, 167, 14, 1, 1, 1, 1, '2025-09-19 05:15:41', '2025-09-19 05:15:41'),
(153, 167, 15, 1, 1, 1, 1, '2025-09-19 05:15:41', '2025-09-19 05:15:41'),
(154, 170, 1, 1, 1, 1, 1, '2025-09-19 06:08:52', '2025-09-19 06:08:52'),
(155, 170, 2, 1, 1, 1, 1, '2025-09-19 06:08:52', '2025-09-19 06:08:52'),
(156, 170, 4, 1, 1, 1, 1, '2025-09-19 06:08:52', '2025-09-19 06:08:52'),
(157, 170, 5, 1, 1, 1, 1, '2025-09-19 06:08:52', '2025-09-19 06:08:52'),
(158, 170, 13, 1, 1, 1, 1, '2025-09-19 06:08:52', '2025-09-19 06:08:52'),
(159, 170, 14, 1, 1, 1, 1, '2025-09-19 06:08:52', '2025-09-19 06:08:52'),
(160, 170, 15, 1, 1, 1, 1, '2025-09-19 06:08:52', '2025-09-19 06:08:52'),
(161, 171, 1, 1, 0, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(162, 171, 2, 1, 0, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(163, 171, 3, 0, 0, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(164, 171, 4, 0, 1, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(165, 171, 5, 0, 1, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(166, 171, 6, 0, 0, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(167, 171, 7, 1, 1, 1, 1, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(168, 171, 8, 1, 1, 1, 1, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(169, 171, 9, 0, 0, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(170, 171, 10, 0, 0, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(171, 171, 11, 0, 0, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(172, 171, 12, 0, 0, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(173, 171, 13, 0, 0, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(174, 171, 14, 0, 0, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(175, 171, 15, 0, 0, 0, 0, '2025-09-19 06:13:03', '2025-09-19 06:13:03'),
(182, 174, 4, 1, 1, 1, 1, '2025-09-19 06:47:03', '2025-09-19 06:47:03'),
(183, 174, 5, 1, 1, 1, 1, '2025-09-19 06:47:03', '2025-09-19 06:47:03'),
(184, 174, 6, 1, 1, 1, 1, '2025-09-19 06:47:03', '2025-09-19 06:47:03'),
(185, 174, 7, 1, 1, 1, 1, '2025-09-19 06:47:03', '2025-09-19 06:47:03'),
(186, 174, 8, 1, 1, 1, 1, '2025-09-19 06:47:03', '2025-09-19 06:47:03'),
(187, 174, 9, 1, 1, 1, 1, '2025-09-19 06:47:03', '2025-09-19 06:47:03'),
(188, 174, 10, 1, 1, 1, 1, '2025-09-19 06:47:03', '2025-09-19 06:47:03'),
(189, 175, 1, 1, 1, 1, 1, '2025-09-19 06:54:19', '2025-12-01 13:29:26'),
(190, 175, 2, 1, 1, 1, 1, '2025-09-19 06:54:19', '2025-12-01 13:29:26'),
(191, 175, 3, 1, 1, 1, 1, '2025-09-19 06:54:19', '2025-12-01 13:29:26'),
(192, 175, 4, 1, 1, 1, 1, '2025-09-19 06:54:19', '2025-12-01 13:29:26'),
(193, 175, 5, 1, 1, 1, 1, '2025-09-19 06:54:19', '2025-12-01 13:29:26'),
(194, 175, 6, 1, 1, 0, 0, '2025-09-19 06:54:19', '2025-09-19 06:54:19'),
(195, 175, 7, 1, 0, 0, 0, '2025-09-19 06:54:19', '2025-09-19 06:54:19'),
(196, 175, 8, 0, 1, 0, 0, '2025-09-19 06:54:19', '2025-09-19 06:54:19'),
(197, 175, 9, 1, 1, 1, 1, '2025-09-19 06:54:19', '2025-12-01 13:29:26'),
(232, 177, 1, 1, 0, 0, 0, '2025-09-19 08:29:44', '2025-09-19 08:29:44'),
(233, 177, 2, 1, 0, 0, 0, '2025-09-19 08:29:44', '2025-09-19 08:29:44'),
(234, 177, 3, 1, 0, 0, 0, '2025-09-19 08:29:44', '2025-09-19 08:29:44'),
(235, 177, 5, 1, 0, 0, 0, '2025-09-19 08:29:44', '2025-09-19 08:29:44'),
(236, 177, 6, 1, 0, 0, 0, '2025-09-19 08:29:44', '2025-09-19 08:29:44'),
(237, 177, 14, 1, 0, 0, 1, '2025-09-19 08:29:44', '2025-09-19 09:05:48'),
(238, 177, 15, 1, 0, 0, 0, '2025-09-19 08:29:44', '2025-09-19 08:29:44'),
(239, 176, 1, 1, 0, 0, 0, '2025-09-19 08:30:48', '2025-09-19 11:36:35'),
(240, 176, 2, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(241, 176, 3, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(242, 176, 4, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(243, 176, 5, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(244, 176, 6, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(245, 176, 7, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(246, 176, 8, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(247, 176, 9, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(248, 176, 10, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(249, 176, 11, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(250, 176, 12, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(251, 176, 13, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(252, 176, 14, 1, 1, 1, 1, '2025-09-19 08:30:48', '2025-09-19 08:30:48'),
(253, 177, 11, 1, 1, 1, 1, '2025-09-19 09:05:48', '2025-09-19 09:07:08'),
(254, 177, 12, 1, 1, 1, 1, '2025-09-19 09:05:48', '2025-09-19 09:07:08'),
(255, 177, 13, 1, 1, 1, 1, '2025-09-19 09:05:48', '2025-09-19 09:07:08'),
(256, 178, 1, 1, 1, 1, 1, '2025-09-19 09:12:22', '2025-09-19 09:12:22'),
(257, 178, 2, 1, 1, 1, 1, '2025-09-19 09:12:22', '2025-09-19 09:12:22'),
(258, 178, 5, 1, 0, 0, 0, '2025-09-19 09:12:22', '2025-09-19 09:12:22'),
(259, 178, 6, 1, 0, 0, 0, '2025-09-19 09:12:22', '2025-09-19 09:12:22'),
(260, 178, 7, 1, 0, 0, 0, '2025-09-19 09:12:22', '2025-09-19 09:12:22'),
(261, 178, 8, 1, 0, 0, 0, '2025-09-19 09:12:22', '2025-09-19 09:12:22'),
(262, 179, 1, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(263, 179, 2, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(264, 179, 3, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(265, 179, 4, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(266, 179, 5, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(267, 179, 6, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(268, 179, 7, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(269, 179, 8, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(270, 179, 9, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(271, 179, 10, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(272, 179, 11, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(273, 179, 12, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(274, 179, 13, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(275, 179, 14, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(276, 179, 15, 1, 1, 1, 1, '2025-09-19 09:30:48', '2025-09-19 10:40:15'),
(277, 178, 11, 1, 1, 0, 0, '2025-09-19 09:31:50', '2025-09-19 09:31:50'),
(278, 178, 12, 1, 1, 0, 0, '2025-09-19 09:31:50', '2025-09-19 09:31:50'),
(279, 178, 13, 1, 1, 0, 0, '2025-09-19 09:31:50', '2025-09-19 09:31:50'),
(280, 178, 14, 1, 1, 0, 0, '2025-09-19 09:31:50', '2025-09-19 09:31:50'),
(281, 180, 1, 1, 1, 1, 1, '2025-09-19 09:34:32', '2025-09-19 09:34:32'),
(282, 180, 2, 1, 1, 1, 1, '2025-09-19 09:34:32', '2025-09-19 09:34:32'),
(283, 180, 3, 0, 1, 0, 0, '2025-09-19 09:34:32', '2025-09-19 09:34:32'),
(284, 180, 4, 0, 1, 0, 1, '2025-09-19 09:34:32', '2025-09-19 09:34:32'),
(285, 180, 5, 0, 0, 0, 1, '2025-09-19 09:34:32', '2025-09-19 09:34:32'),
(286, 181, 1, 1, 0, 0, 0, '2025-09-19 09:50:39', '2025-09-19 09:50:39'),
(287, 181, 2, 1, 0, 0, 0, '2025-09-19 09:50:39', '2025-09-19 09:50:39'),
(288, 181, 3, 1, 0, 0, 0, '2025-09-19 09:50:39', '2025-09-19 09:50:39'),
(289, 181, 4, 0, 1, 0, 0, '2025-09-19 09:50:39', '2025-09-19 09:50:39'),
(290, 181, 5, 0, 0, 1, 0, '2025-09-19 09:50:39', '2025-09-19 09:50:39'),
(291, 181, 12, 1, 0, 0, 0, '2025-09-19 09:50:39', '2025-09-19 09:50:39'),
(292, 181, 13, 0, 1, 0, 0, '2025-09-19 09:50:39', '2025-09-19 09:50:39'),
(293, 181, 14, 0, 0, 1, 0, '2025-09-19 09:50:39', '2025-09-19 09:50:39'),
(294, 181, 15, 0, 0, 0, 1, '2025-09-19 09:50:39', '2025-09-19 09:50:39'),
(295, 182, 1, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(296, 182, 2, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(297, 182, 3, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(298, 182, 4, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(299, 182, 5, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(300, 182, 6, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(301, 182, 7, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(302, 182, 8, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(303, 182, 9, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(304, 182, 10, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(305, 182, 11, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(306, 182, 12, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(307, 182, 13, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(308, 182, 14, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(309, 182, 15, 1, 1, 1, 1, '2025-09-19 09:55:29', '2025-09-19 09:55:29'),
(310, 183, 1, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(311, 183, 2, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(312, 183, 3, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(313, 183, 4, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(314, 183, 5, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(315, 183, 6, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(316, 183, 7, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(317, 183, 8, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(318, 183, 9, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(319, 183, 10, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(320, 183, 11, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(321, 183, 12, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(322, 183, 13, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(323, 183, 14, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(324, 183, 15, 1, 1, 1, 1, '2025-09-19 09:58:27', '2025-09-19 10:49:21'),
(325, 184, 1, 0, 0, 0, 0, '2025-09-19 10:11:24', '2025-09-25 11:01:37'),
(326, 184, 2, 0, 0, 0, 0, '2025-09-19 10:11:24', '2025-09-22 11:52:37'),
(342, 184, 8, 0, 0, 0, 0, '2025-09-19 11:10:30', '2025-09-22 09:54:49'),
(343, 184, 9, 1, 0, 1, 0, '2025-09-19 11:10:30', '2025-09-23 05:22:46'),
(346, 185, 1, 1, 0, 0, 0, '2025-09-19 11:16:29', '2025-09-19 11:16:29'),
(347, 185, 2, 1, 0, 0, 0, '2025-09-19 11:16:29', '2025-09-19 11:16:29'),
(359, 185, 14, 1, 1, 1, 1, '2025-09-19 11:16:29', '2025-09-19 11:16:29'),
(360, 185, 15, 1, 1, 1, 1, '2025-09-19 11:16:29', '2025-09-19 11:16:29'),
(361, 186, 1, 1, 1, 1, 1, '2025-09-19 11:20:53', '2025-09-19 11:20:53'),
(362, 186, 2, 1, 0, 0, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:35'),
(363, 186, 3, 0, 0, 0, 1, '2025-09-19 11:20:53', '2025-09-25 09:11:07'),
(364, 186, 4, 0, 0, 1, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:07'),
(365, 186, 5, 0, 1, 0, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:07'),
(366, 186, 6, 1, 0, 0, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:07'),
(367, 186, 7, 0, 1, 0, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:07'),
(368, 186, 8, 0, 0, 1, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:07'),
(369, 186, 9, 0, 0, 0, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:21'),
(370, 186, 10, 0, 0, 0, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:21'),
(371, 186, 11, 0, 0, 1, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:07'),
(372, 186, 12, 0, 1, 0, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:07'),
(373, 186, 13, 1, 0, 0, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:07'),
(374, 186, 14, 0, 1, 0, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:07'),
(375, 186, 15, 0, 0, 1, 0, '2025-09-19 11:20:53', '2025-09-25 09:11:07'),
(376, 184, 13, 0, 0, 0, 0, '2025-09-19 11:22:11', '2025-09-22 11:52:37'),
(377, 184, 14, 1, 0, 0, 0, '2025-09-19 11:22:11', '2025-09-23 06:56:42'),
(378, 187, 1, 0, 0, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:34:51'),
(379, 187, 2, 0, 0, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:34:51'),
(380, 187, 3, 0, 0, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:25:33'),
(381, 187, 4, 0, 0, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:34:28'),
(382, 187, 5, 0, 0, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:34:28'),
(383, 187, 6, 0, 0, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:34:28'),
(384, 187, 7, 1, 1, 1, 1, '2025-09-19 11:25:33', '2025-09-19 11:25:33'),
(385, 187, 8, 1, 1, 1, 1, '2025-09-19 11:25:33', '2025-09-19 11:25:33'),
(386, 187, 9, 0, 0, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:25:33'),
(387, 187, 10, 0, 0, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:25:33'),
(388, 187, 11, 0, 0, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:25:33'),
(389, 187, 12, 0, 0, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:25:33'),
(390, 187, 13, 0, 0, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:38:22'),
(391, 187, 14, 1, 1, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:34:28'),
(392, 187, 15, 1, 1, 0, 0, '2025-09-19 11:25:33', '2025-09-19 11:34:28'),
(393, 188, 1, 1, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-22 05:47:56'),
(394, 188, 2, 0, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-22 09:16:33'),
(395, 188, 3, 0, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-22 09:16:33'),
(396, 188, 4, 0, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-22 09:16:33'),
(397, 188, 5, 0, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-22 09:16:33'),
(398, 188, 6, 0, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-19 12:24:25'),
(399, 188, 7, 1, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-19 12:24:25'),
(400, 188, 8, 1, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-19 12:24:25'),
(401, 188, 9, 0, 1, 0, 0, '2025-09-19 12:24:25', '2025-09-19 12:24:25'),
(402, 188, 10, 0, 1, 0, 0, '2025-09-19 12:24:25', '2025-09-19 12:24:25'),
(403, 188, 11, 0, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-19 12:25:33'),
(404, 188, 12, 0, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-19 12:25:33'),
(405, 188, 13, 0, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-19 12:25:33'),
(406, 188, 14, 0, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-19 12:25:33'),
(407, 188, 15, 0, 0, 0, 0, '2025-09-19 12:24:25', '2025-09-19 12:24:25'),
(408, 184, 3, 0, 0, 0, 0, '2025-09-22 09:54:49', '2025-09-25 11:01:37'),
(409, 184, 4, 0, 0, 0, 0, '2025-09-22 09:54:49', '2025-09-25 11:01:37'),
(410, 184, 5, 1, 0, 0, 1, '2025-09-22 09:54:49', '2025-09-22 11:52:37'),
(411, 184, 6, 1, 0, 1, 0, '2025-09-22 09:54:49', '2025-09-22 11:52:37'),
(412, 184, 7, 1, 1, 1, 1, '2025-09-22 09:54:49', '2025-09-22 12:11:10'),
(413, 184, 10, 1, 0, 0, 0, '2025-09-22 09:54:49', '2025-09-22 12:42:30'),
(414, 184, 11, 0, 0, 0, 0, '2025-09-22 09:54:49', '2025-09-22 09:54:49'),
(415, 184, 12, 0, 0, 0, 0, '2025-09-22 09:54:49', '2025-09-22 09:54:49'),
(416, 184, 15, 0, 1, 0, 1, '2025-09-22 09:54:49', '2025-09-23 06:52:26'),
(417, 184, 16, 0, 0, 0, 0, '2025-09-25 11:01:37', '2025-09-25 11:01:37'),
(418, 184, 17, 0, 0, 0, 0, '2025-09-25 11:01:37', '2025-09-25 11:01:37'),
(419, 196, 1, 1, 0, 1, 0, '2025-09-25 11:07:08', '2025-09-25 11:12:29'),
(420, 196, 2, 0, 1, 0, 0, '2025-09-25 11:07:08', '2025-09-25 11:09:53'),
(421, 196, 3, 1, 0, 1, 0, '2025-09-25 11:07:08', '2025-09-25 11:12:29'),
(422, 196, 4, 1, 0, 1, 0, '2025-09-25 11:07:08', '2025-09-25 11:12:29'),
(423, 196, 5, 1, 0, 0, 0, '2025-09-25 11:07:08', '2025-09-25 11:07:08'),
(424, 196, 9, 1, 0, 0, 0, '2025-09-25 11:07:08', '2025-09-25 11:07:08'),
(425, 196, 10, 1, 0, 0, 0, '2025-09-25 11:07:08', '2025-09-25 11:07:08'),
(426, 196, 14, 1, 0, 0, 0, '2025-09-25 11:07:08', '2025-09-25 11:12:29'),
(427, 196, 15, 1, 0, 1, 0, '2025-09-25 11:07:08', '2025-09-25 11:12:29'),
(428, 196, 16, 1, 1, 1, 1, '2025-09-25 11:07:08', '2025-09-25 11:12:29'),
(429, 196, 17, 1, 1, 1, 1, '2025-09-25 11:07:08', '2025-09-25 11:12:29'),
(430, 196, 8, 0, 0, 0, 0, '2025-09-25 11:09:53', '2025-09-25 11:09:53'),
(431, 150, 16, 0, 0, 0, 0, '2025-10-14 08:05:17', '2025-10-14 08:05:17'),
(432, 150, 17, 0, 0, 0, 0, '2025-10-14 08:05:17', '2025-10-14 08:05:17'),
(433, 208, 1, 0, 0, 0, 0, '2025-10-14 08:06:19', '2025-10-14 08:15:09'),
(434, 208, 2, 1, 1, 1, 1, '2025-10-14 08:06:19', '2025-10-14 08:29:25'),
(435, 208, 3, 0, 0, 0, 0, '2025-10-14 08:06:19', '2025-10-14 08:06:19'),
(436, 208, 4, 0, 0, 0, 0, '2025-10-14 08:06:19', '2025-10-14 08:06:19'),
(437, 208, 5, 0, 0, 0, 0, '2025-10-14 08:06:19', '2025-10-14 08:06:19'),
(438, 208, 9, 0, 0, 0, 0, '2025-10-14 08:06:19', '2025-10-14 08:06:19'),
(439, 208, 10, 0, 0, 0, 0, '2025-10-14 08:06:19', '2025-10-14 08:06:19'),
(440, 208, 14, 0, 0, 0, 0, '2025-10-14 08:06:19', '2025-10-14 08:06:19'),
(441, 208, 15, 0, 0, 0, 0, '2025-10-14 08:06:19', '2025-10-14 08:06:19'),
(442, 208, 16, 0, 0, 0, 0, '2025-10-14 08:06:19', '2025-10-14 08:06:19'),
(443, 208, 17, 0, 0, 0, 0, '2025-10-14 08:06:19', '2025-10-14 08:06:19'),
(444, 209, 1, 1, 1, 1, 1, '2025-10-15 10:40:46', '2025-10-15 10:40:46'),
(445, 209, 2, 1, 1, 1, 1, '2025-10-15 10:40:46', '2025-10-15 10:40:46'),
(446, 209, 3, 0, 0, 0, 0, '2025-10-15 10:40:46', '2025-10-15 10:40:46'),
(447, 209, 4, 0, 0, 0, 0, '2025-10-15 10:40:46', '2025-10-15 10:40:46'),
(448, 209, 5, 0, 0, 0, 0, '2025-10-15 10:40:46', '2025-10-15 10:40:46'),
(449, 209, 9, 0, 0, 0, 0, '2025-10-15 10:40:46', '2025-10-15 10:40:46'),
(450, 209, 10, 0, 0, 0, 0, '2025-10-15 10:40:46', '2025-10-15 10:40:46'),
(451, 209, 14, 0, 0, 0, 0, '2025-10-15 10:40:46', '2025-10-15 10:40:46'),
(452, 209, 15, 0, 0, 0, 0, '2025-10-15 10:40:46', '2025-10-15 10:40:46'),
(453, 209, 16, 0, 0, 0, 0, '2025-10-15 10:40:46', '2025-10-15 10:40:46'),
(454, 209, 17, 0, 0, 0, 0, '2025-10-15 10:40:46', '2025-10-15 10:40:46'),
(455, 210, 1, 1, 1, 1, 1, '2025-10-16 10:55:43', '2025-10-16 10:55:43'),
(456, 210, 2, 1, 1, 1, 1, '2025-10-16 10:55:43', '2025-10-16 10:55:43'),
(457, 210, 3, 1, 1, 1, 1, '2025-10-16 10:55:43', '2025-10-16 10:55:43'),
(458, 210, 4, 1, 1, 1, 1, '2025-10-16 10:55:43', '2025-10-16 10:55:43'),
(459, 210, 5, 1, 1, 1, 1, '2025-10-16 10:55:43', '2025-10-16 10:55:43'),
(460, 210, 9, 1, 1, 1, 1, '2025-10-16 10:55:43', '2025-10-16 10:55:43'),
(461, 210, 10, 1, 1, 1, 1, '2025-10-16 10:55:43', '2025-10-16 10:55:43'),
(462, 214, 1, 1, 1, 1, 1, '2025-10-30 12:33:45', '2025-11-26 18:39:06'),
(463, 214, 2, 1, 1, 1, 1, '2025-10-30 12:33:45', '2025-11-26 18:39:06'),
(464, 214, 3, 1, 1, 1, 1, '2025-10-30 12:33:45', '2025-11-26 18:39:06'),
(465, 214, 4, 1, 1, 1, 1, '2025-10-30 12:33:45', '2025-11-26 18:39:06'),
(466, 214, 5, 1, 1, 1, 1, '2025-10-30 12:33:45', '2025-11-26 18:35:37'),
(467, 214, 9, 1, 1, 1, 1, '2025-10-30 12:33:45', '2025-11-26 18:35:37'),
(468, 214, 10, 1, 1, 1, 1, '2025-10-30 12:33:45', '2025-11-26 18:35:37'),
(469, 215, 1, 1, 1, 1, 1, '2025-10-31 18:28:16', '2025-10-31 18:28:16'),
(470, 215, 2, 1, 1, 1, 1, '2025-10-31 18:28:16', '2025-10-31 18:28:16'),
(471, 215, 3, 1, 1, 1, 1, '2025-10-31 18:28:16', '2025-10-31 18:28:16'),
(472, 215, 4, 1, 1, 1, 1, '2025-10-31 18:28:16', '2025-10-31 18:28:16'),
(473, 215, 5, 1, 1, 1, 1, '2025-10-31 18:28:16', '2025-10-31 18:28:16'),
(474, 215, 9, 1, 1, 1, 1, '2025-10-31 18:28:16', '2025-10-31 18:28:16'),
(475, 215, 10, 1, 1, 1, 1, '2025-10-31 18:28:16', '2025-10-31 18:28:16'),
(484, 215, 8, 0, 0, 0, 0, '2025-11-03 12:17:32', '2025-11-03 12:17:32'),
(493, 219, 1, 1, 1, 1, 1, '2025-11-03 13:13:50', '2025-11-13 18:20:57'),
(494, 219, 2, 1, 1, 1, 1, '2025-11-03 13:13:50', '2025-11-13 18:20:57'),
(495, 219, 3, 1, 1, 1, 1, '2025-11-03 13:13:50', '2025-11-13 18:20:57'),
(496, 219, 4, 1, 1, 1, 1, '2025-11-03 13:13:50', '2025-11-13 18:20:57'),
(497, 219, 5, 1, 1, 1, 1, '2025-11-03 13:13:50', '2025-11-13 18:20:57'),
(498, 219, 8, 0, 0, 0, 0, '2025-11-03 13:13:50', '2025-11-03 13:13:50'),
(499, 219, 9, 1, 1, 1, 1, '2025-11-03 13:13:50', '2025-11-13 18:20:57'),
(500, 219, 10, 1, 1, 1, 1, '2025-11-03 13:13:50', '2025-11-13 18:20:57'),
(510, 208, 26, 0, 0, 0, 0, '2025-11-06 10:59:03', '2025-11-06 10:59:03'),
(511, 248, 1, 1, 1, 1, 1, '2025-11-06 15:20:54', '2025-11-06 15:20:54'),
(512, 248, 2, 1, 1, 1, 1, '2025-11-06 15:20:54', '2025-11-06 15:20:54'),
(513, 248, 3, 1, 1, 1, 1, '2025-11-06 15:20:54', '2025-11-06 15:20:54'),
(514, 248, 4, 1, 1, 1, 1, '2025-11-06 15:20:54', '2025-11-06 15:20:54'),
(515, 248, 5, 1, 1, 1, 1, '2025-11-06 15:20:54', '2025-11-06 15:20:54'),
(516, 248, 9, 1, 1, 1, 1, '2025-11-06 15:20:54', '2025-11-06 15:20:54'),
(517, 248, 10, 1, 1, 1, 1, '2025-11-06 15:20:54', '2025-11-06 15:20:54'),
(518, 248, 26, 1, 1, 1, 1, '2025-11-06 15:20:54', '2025-11-06 15:20:54'),
(519, 250, 1, 1, 1, 1, 1, '2025-11-06 17:33:17', '2025-11-06 17:33:17'),
(520, 250, 2, 1, 1, 1, 1, '2025-11-06 17:33:17', '2025-11-06 17:33:17'),
(521, 250, 3, 1, 1, 1, 1, '2025-11-06 17:33:17', '2025-11-06 17:33:17'),
(522, 250, 4, 1, 1, 1, 1, '2025-11-06 17:33:17', '2025-11-06 17:33:17'),
(523, 250, 5, 1, 1, 1, 1, '2025-11-06 17:33:17', '2025-11-06 17:33:17'),
(524, 250, 9, 1, 1, 1, 1, '2025-11-06 17:33:17', '2025-11-06 17:33:17'),
(525, 250, 10, 1, 1, 1, 1, '2025-11-06 17:33:17', '2025-11-06 17:33:17'),
(526, 250, 26, 1, 1, 1, 1, '2025-11-06 17:33:17', '2025-11-06 17:33:17'),
(527, 210, 26, 1, 1, 1, 1, '2025-11-07 10:14:32', '2025-11-07 10:14:32'),
(528, 179, 26, 0, 0, 0, 0, '2025-11-11 13:11:14', '2025-11-11 13:11:14'),
(529, 219, 26, 1, 1, 1, 1, '2025-11-12 11:46:58', '2025-11-13 18:20:57'),
(530, 1, 26, 1, 1, 1, 1, '2025-09-18 15:59:10', '2025-09-18 15:59:10'),
(531, 256, 1, 1, 1, 1, 1, '2025-11-20 16:04:27', '2025-11-20 16:04:27'),
(532, 256, 2, 0, 0, 0, 0, '2025-11-20 16:04:27', '2025-11-20 16:04:27'),
(533, 256, 3, 0, 0, 0, 0, '2025-11-20 16:04:27', '2025-11-20 16:04:27'),
(534, 256, 4, 1, 1, 1, 1, '2025-11-20 16:04:27', '2025-11-20 16:04:27'),
(535, 256, 5, 0, 0, 0, 0, '2025-11-20 16:04:27', '2025-11-20 16:04:27'),
(536, 256, 9, 0, 0, 0, 0, '2025-11-20 16:04:27', '2025-11-20 16:04:27'),
(537, 256, 10, 0, 0, 0, 0, '2025-11-20 16:04:27', '2025-11-20 16:04:27'),
(538, 256, 26, 0, 0, 0, 0, '2025-11-20 16:04:27', '2025-11-20 16:04:27'),
(539, 266, 1, 1, 0, 0, 0, '2025-11-21 13:53:47', '2025-11-21 13:53:47'),
(540, 266, 2, 1, 0, 0, 0, '2025-11-21 13:53:47', '2025-11-21 13:53:47'),
(541, 266, 3, 1, 0, 0, 0, '2025-11-21 13:53:47', '2025-11-21 13:53:47'),
(542, 266, 4, 0, 0, 0, 0, '2025-11-21 13:53:47', '2025-11-21 13:53:47'),
(543, 266, 5, 0, 0, 0, 0, '2025-11-21 13:53:47', '2025-11-21 13:53:47'),
(544, 266, 9, 0, 0, 0, 0, '2025-11-21 13:53:47', '2025-11-21 13:53:47'),
(545, 266, 10, 0, 0, 0, 0, '2025-11-21 13:53:47', '2025-11-21 13:53:47'),
(546, 266, 26, 0, 0, 0, 0, '2025-11-21 13:53:47', '2025-11-21 13:53:47'),
(547, 267, 1, 1, 0, 0, 0, '2025-11-21 14:40:32', '2025-11-21 14:40:32'),
(548, 267, 2, 1, 0, 0, 0, '2025-11-21 14:40:32', '2025-11-21 14:40:32'),
(549, 267, 3, 1, 0, 0, 0, '2025-11-21 14:40:32', '2025-11-21 14:40:32'),
(550, 267, 4, 1, 0, 0, 0, '2025-11-21 14:40:32', '2025-11-21 14:40:32'),
(551, 267, 5, 1, 0, 0, 0, '2025-11-21 14:40:32', '2025-11-21 14:56:21'),
(552, 267, 9, 0, 0, 0, 0, '2025-11-21 14:40:32', '2025-11-21 14:40:32'),
(553, 267, 10, 0, 0, 0, 0, '2025-11-21 14:40:32', '2025-11-21 14:40:32'),
(554, 267, 26, 0, 0, 0, 0, '2025-11-21 14:40:32', '2025-11-21 14:40:32'),
(555, 268, 1, 1, 0, 0, 0, '2025-11-21 15:00:25', '2025-11-26 14:50:47'),
(556, 268, 2, 1, 0, 0, 0, '2025-11-21 15:00:25', '2025-11-26 15:07:51'),
(557, 268, 3, 1, 0, 0, 0, '2025-11-21 15:00:25', '2025-11-26 15:07:51'),
(558, 268, 4, 1, 0, 0, 0, '2025-11-21 15:00:25', '2025-11-26 15:07:51'),
(559, 268, 5, 1, 0, 0, 0, '2025-11-21 15:00:25', '2025-11-26 15:07:51'),
(560, 268, 9, 1, 0, 0, 0, '2025-11-21 15:00:25', '2025-11-26 15:07:51'),
(561, 268, 10, 1, 0, 0, 0, '2025-11-21 15:00:25', '2025-11-26 15:07:51'),
(562, 268, 26, 1, 0, 0, 0, '2025-11-21 15:00:25', '2025-11-26 15:07:51'),
(563, 270, 1, 1, 1, 1, 1, '2025-11-22 11:34:54', '2025-11-22 11:34:54'),
(564, 270, 2, 1, 1, 1, 1, '2025-11-22 11:34:54', '2025-11-22 11:34:54'),
(565, 270, 3, 0, 0, 0, 0, '2025-11-22 11:34:54', '2025-11-22 11:34:54'),
(566, 270, 4, 0, 0, 0, 0, '2025-11-22 11:34:54', '2025-11-22 11:34:54'),
(567, 270, 5, 0, 0, 0, 0, '2025-11-22 11:34:54', '2025-11-22 11:34:54'),
(568, 270, 9, 0, 0, 0, 0, '2025-11-22 11:34:54', '2025-11-22 11:34:54'),
(569, 270, 10, 0, 0, 0, 0, '2025-11-22 11:34:54', '2025-11-22 11:34:54'),
(570, 270, 26, 0, 0, 0, 0, '2025-11-22 11:34:54', '2025-11-22 11:34:54'),
(571, 272, 1, 1, 1, 1, 1, '2025-11-25 13:36:08', '2025-11-25 13:36:08'),
(572, 272, 2, 1, 1, 1, 1, '2025-11-25 13:36:08', '2025-11-25 13:36:08'),
(573, 272, 3, 1, 1, 1, 1, '2025-11-25 13:36:08', '2025-11-25 13:36:08'),
(574, 272, 4, 1, 1, 1, 1, '2025-11-25 13:36:08', '2025-11-25 13:36:08'),
(575, 272, 5, 1, 1, 1, 1, '2025-11-25 13:36:08', '2025-11-25 13:36:08'),
(576, 272, 9, 1, 1, 1, 1, '2025-11-25 13:36:08', '2025-11-25 13:36:08'),
(577, 272, 10, 1, 1, 1, 1, '2025-11-25 13:36:08', '2025-11-25 13:36:08'),
(578, 272, 26, 1, 1, 1, 1, '2025-11-25 13:36:08', '2025-11-25 13:36:08'),
(579, 287, 1, 0, 0, 0, 0, '2025-11-26 13:10:22', '2025-11-26 13:10:22'),
(580, 287, 2, 0, 0, 0, 0, '2025-11-26 13:10:22', '2025-11-26 13:10:22'),
(581, 287, 3, 0, 0, 0, 0, '2025-11-26 13:10:22', '2025-11-26 13:10:22'),
(582, 287, 4, 0, 0, 0, 0, '2025-11-26 13:10:22', '2025-11-26 13:10:22'),
(583, 287, 5, 0, 0, 0, 0, '2025-11-26 13:10:22', '2025-11-26 13:10:22'),
(584, 287, 9, 0, 0, 0, 0, '2025-11-26 13:10:22', '2025-11-26 13:10:22'),
(585, 287, 10, 0, 0, 0, 0, '2025-11-26 13:10:22', '2025-11-26 13:10:22'),
(586, 287, 26, 0, 0, 0, 0, '2025-11-26 13:10:22', '2025-11-26 13:10:22'),
(587, 214, 26, 1, 0, 0, 0, '2025-11-26 15:14:49', '2025-11-26 15:39:01'),
(588, 288, 1, 1, 1, 1, 1, '2025-11-26 18:42:09', '2025-11-26 18:42:09'),
(589, 288, 2, 1, 1, 1, 1, '2025-11-26 18:42:09', '2025-11-26 18:42:09'),
(590, 288, 3, 1, 1, 1, 1, '2025-11-26 18:42:09', '2025-11-26 18:42:09'),
(591, 288, 4, 1, 1, 1, 1, '2025-11-26 18:42:09', '2025-11-26 18:42:09'),
(592, 288, 5, 1, 1, 1, 1, '2025-11-26 18:42:09', '2025-11-26 18:42:09'),
(593, 288, 9, 1, 1, 1, 1, '2025-11-26 18:42:09', '2025-11-26 18:42:09'),
(594, 288, 10, 1, 1, 1, 1, '2025-11-26 18:42:09', '2025-11-26 18:42:09'),
(595, 288, 26, 1, 1, 1, 1, '2025-11-26 18:42:09', '2025-11-26 18:42:09'),
(596, 175, 10, 1, 1, 1, 1, '2025-12-01 13:29:26', '2025-12-01 13:29:26'),
(597, 175, 26, 1, 1, 1, 1, '2025-12-01 13:29:26', '2025-12-01 13:29:26'),
(598, 297, 1, 1, 1, 1, 1, '2025-12-12 13:01:56', '2025-12-12 13:01:56'),
(599, 297, 2, 1, 1, 1, 1, '2025-12-12 13:01:56', '2025-12-12 13:01:56'),
(600, 297, 3, 1, 1, 1, 1, '2025-12-12 13:01:56', '2025-12-12 13:01:56'),
(601, 297, 4, 0, 0, 0, 0, '2025-12-12 13:01:56', '2025-12-12 13:01:56'),
(602, 297, 5, 0, 0, 0, 0, '2025-12-12 13:01:56', '2025-12-12 13:01:56'),
(603, 297, 9, 1, 1, 1, 1, '2025-12-12 13:01:56', '2025-12-12 13:01:56'),
(604, 297, 10, 0, 0, 0, 0, '2025-12-12 13:01:56', '2025-12-12 13:01:56'),
(605, 297, 26, 0, 0, 0, 0, '2025-12-12 13:01:56', '2025-12-12 13:01:56'),
(606, 150, 26, 1, 1, 1, 1, '2026-01-02 12:51:08', '2026-01-02 12:52:37'),
(607, 314, 1, 1, 1, 1, 1, '2026-01-29 15:49:36', '2026-01-29 15:49:36'),
(608, 314, 2, 1, 1, 1, 1, '2026-01-29 15:49:36', '2026-01-29 15:49:36'),
(609, 314, 3, 1, 1, 1, 1, '2026-01-29 15:49:36', '2026-01-29 15:49:36'),
(610, 314, 4, 1, 1, 1, 1, '2026-01-29 15:49:36', '2026-01-29 15:49:36'),
(611, 314, 5, 1, 1, 1, 1, '2026-01-29 15:49:36', '2026-01-29 15:49:36'),
(612, 314, 9, 0, 0, 0, 0, '2026-01-29 15:49:36', '2026-03-17 14:47:57'),
(613, 314, 10, 0, 0, 0, 0, '2026-01-29 15:49:36', '2026-03-17 14:47:57'),
(614, 314, 26, 0, 0, 0, 0, '2026-01-29 15:49:36', '2026-03-17 14:47:57'),
(615, 314, 27, 0, 0, 0, 0, '2026-01-29 15:49:36', '2026-03-17 14:47:57'),
(616, 315, 1, 1, 1, 1, 1, '2026-01-29 15:50:48', '2026-01-29 15:50:48'),
(617, 315, 2, 1, 1, 1, 1, '2026-01-29 15:50:48', '2026-01-29 15:50:48'),
(618, 315, 3, 1, 1, 1, 1, '2026-01-29 15:50:48', '2026-01-29 15:50:48'),
(619, 315, 4, 1, 1, 1, 1, '2026-01-29 15:50:48', '2026-01-29 15:50:48'),
(620, 315, 5, 1, 1, 1, 1, '2026-01-29 15:50:48', '2026-01-29 15:50:48'),
(621, 315, 9, 1, 1, 1, 1, '2026-01-29 15:50:48', '2026-01-29 15:50:48'),
(622, 315, 10, 1, 1, 1, 1, '2026-01-29 15:50:48', '2026-01-29 15:50:48'),
(623, 315, 26, 1, 1, 1, 1, '2026-01-29 15:50:48', '2026-01-29 15:50:48'),
(624, 315, 27, 1, 1, 1, 1, '2026-01-29 15:50:48', '2026-01-29 15:50:48'),
(625, 316, 1, 1, 1, 1, 1, '2026-01-29 16:41:04', '2026-01-29 16:41:04'),
(626, 316, 2, 1, 1, 1, 1, '2026-01-29 16:41:04', '2026-01-29 16:41:04'),
(627, 316, 3, 1, 1, 1, 1, '2026-01-29 16:41:04', '2026-01-29 16:41:04'),
(628, 316, 4, 1, 1, 1, 1, '2026-01-29 16:41:04', '2026-01-29 16:41:04'),
(629, 316, 5, 1, 1, 1, 1, '2026-01-29 16:41:04', '2026-01-29 16:41:04'),
(630, 316, 9, 1, 1, 1, 1, '2026-01-29 16:41:04', '2026-01-29 16:41:04'),
(631, 316, 10, 1, 1, 1, 1, '2026-01-29 16:41:04', '2026-01-29 16:41:04'),
(632, 316, 26, 1, 1, 1, 1, '2026-01-29 16:41:04', '2026-01-29 16:41:04'),
(633, 316, 27, 1, 1, 1, 1, '2026-01-29 16:41:04', '2026-01-29 16:41:04'),
(634, 321, 1, 0, 0, 0, 0, '2026-02-03 11:46:11', '2026-02-03 11:46:11'),
(635, 321, 2, 1, 1, 1, 1, '2026-02-03 11:46:11', '2026-02-03 11:46:11'),
(636, 321, 3, 0, 0, 0, 0, '2026-02-03 11:46:11', '2026-02-03 11:46:11'),
(637, 321, 4, 0, 0, 0, 0, '2026-02-03 11:46:11', '2026-02-03 11:46:11'),
(638, 321, 5, 0, 0, 0, 0, '2026-02-03 11:46:11', '2026-02-03 11:46:11'),
(639, 321, 9, 0, 0, 0, 0, '2026-02-03 11:46:11', '2026-02-03 11:46:11'),
(640, 321, 10, 0, 0, 0, 0, '2026-02-03 11:46:11', '2026-02-03 11:46:11'),
(641, 321, 26, 0, 0, 0, 0, '2026-02-03 11:46:11', '2026-02-03 11:46:11'),
(642, 321, 27, 0, 0, 0, 0, '2026-02-03 11:46:11', '2026-02-03 11:46:11'),
(670, 328, 1, 1, 1, 1, 1, '2026-03-17 12:18:59', '2026-03-17 12:18:59'),
(671, 328, 2, 1, 1, 1, 1, '2026-03-17 12:18:59', '2026-03-17 12:18:59'),
(672, 328, 3, 1, 1, 1, 1, '2026-03-17 12:19:00', '2026-03-17 12:19:00'),
(673, 328, 4, 1, 1, 1, 1, '2026-03-17 12:19:00', '2026-03-17 12:19:00'),
(674, 328, 5, 1, 1, 1, 1, '2026-03-17 12:19:00', '2026-03-17 12:19:00'),
(675, 328, 9, 1, 1, 1, 1, '2026-03-17 12:19:00', '2026-03-17 12:19:00'),
(676, 328, 10, 1, 1, 1, 1, '2026-03-17 12:19:00', '2026-03-17 12:19:00'),
(677, 328, 26, 1, 1, 1, 1, '2026-03-17 12:19:00', '2026-03-17 12:19:00'),
(678, 328, 27, 1, 1, 1, 1, '2026-03-17 12:19:00', '2026-03-17 12:19:00'),
(679, 331, 1, 1, 1, 1, 1, '2026-03-17 14:49:14', '2026-03-17 14:49:14'),
(680, 331, 2, 1, 1, 1, 1, '2026-03-17 14:49:14', '2026-03-17 14:49:14'),
(681, 331, 3, 1, 1, 1, 1, '2026-03-17 14:49:14', '2026-03-17 14:49:14'),
(682, 331, 4, 1, 1, 1, 1, '2026-03-17 14:49:14', '2026-03-17 14:49:14'),
(683, 331, 5, 0, 0, 0, 0, '2026-03-17 14:49:14', '2026-03-17 14:49:14'),
(684, 331, 9, 0, 0, 0, 0, '2026-03-17 14:49:14', '2026-03-17 14:49:14'),
(685, 331, 10, 0, 0, 0, 0, '2026-03-17 14:49:14', '2026-03-17 14:49:14'),
(686, 331, 26, 0, 0, 0, 0, '2026-03-17 14:49:14', '2026-03-17 14:49:14'),
(687, 331, 27, 0, 0, 0, 0, '2026-03-17 14:49:14', '2026-03-17 14:49:14'),
(696, 336, 1, 1, 1, 1, 1, '2026-03-30 11:34:00', '2026-03-30 12:09:09'),
(697, 336, 2, 1, 1, 0, 0, '2026-03-30 11:34:00', '2026-03-30 12:04:38'),
(698, 336, 3, 1, 1, 1, 1, '2026-03-30 11:34:00', '2026-03-30 12:11:48'),
(699, 336, 4, 0, 0, 0, 0, '2026-03-30 11:34:00', '2026-03-30 11:34:00'),
(700, 336, 5, 0, 0, 0, 0, '2026-03-30 11:34:00', '2026-03-30 11:34:00'),
(701, 336, 9, 0, 0, 0, 0, '2026-03-30 11:34:00', '2026-03-30 11:34:00'),
(702, 336, 10, 0, 0, 0, 0, '2026-03-30 11:34:00', '2026-03-30 11:34:00'),
(703, 336, 26, 0, 0, 0, 0, '2026-03-30 11:34:00', '2026-03-30 11:34:00'),
(704, 337, 1, 1, 1, 1, 1, '2026-03-30 12:39:40', '2026-03-30 12:39:40'),
(705, 337, 2, 1, 1, 1, 1, '2026-03-30 12:39:40', '2026-03-30 12:39:40'),
(706, 337, 3, 0, 0, 0, 0, '2026-03-30 12:39:40', '2026-03-30 12:39:40'),
(707, 337, 4, 0, 0, 0, 0, '2026-03-30 12:39:40', '2026-03-30 12:39:40'),
(708, 337, 5, 0, 0, 0, 0, '2026-03-30 12:39:40', '2026-03-30 12:39:40'),
(709, 337, 9, 0, 0, 0, 0, '2026-03-30 12:39:40', '2026-03-30 12:39:40'),
(710, 337, 10, 0, 0, 0, 0, '2026-03-30 12:39:40', '2026-03-30 12:39:40'),
(711, 337, 26, 0, 0, 0, 0, '2026-03-30 12:39:40', '2026-03-30 12:39:40'),
(712, 341, 1, 0, 0, 0, 0, '2026-03-31 11:32:24', '2026-03-31 11:32:24'),
(713, 341, 2, 0, 0, 0, 0, '2026-03-31 11:32:24', '2026-03-31 11:32:24'),
(714, 341, 3, 0, 0, 0, 0, '2026-03-31 11:32:24', '2026-03-31 11:32:24'),
(715, 341, 4, 0, 0, 0, 0, '2026-03-31 11:32:24', '2026-03-31 11:32:24'),
(716, 341, 5, 0, 0, 0, 0, '2026-03-31 11:32:24', '2026-03-31 11:32:24'),
(717, 341, 9, 0, 0, 0, 0, '2026-03-31 11:32:24', '2026-03-31 11:32:24'),
(718, 341, 10, 0, 0, 0, 0, '2026-03-31 11:32:24', '2026-03-31 11:32:24'),
(719, 341, 26, 0, 0, 0, 0, '2026-03-31 11:32:24', '2026-03-31 11:32:24'),
(720, 344, 1, 1, 1, 1, 1, '2026-03-31 15:21:26', '2026-03-31 15:21:26'),
(721, 344, 2, 1, 1, 1, 1, '2026-03-31 15:21:26', '2026-03-31 15:21:26'),
(722, 344, 3, 1, 1, 1, 1, '2026-03-31 15:21:26', '2026-03-31 15:21:26'),
(723, 344, 4, 0, 0, 0, 0, '2026-03-31 15:21:26', '2026-03-31 15:21:26'),
(724, 344, 5, 0, 0, 0, 0, '2026-03-31 15:21:26', '2026-03-31 15:21:26'),
(725, 344, 9, 0, 0, 0, 0, '2026-03-31 15:21:26', '2026-03-31 15:21:26'),
(726, 344, 10, 0, 0, 0, 0, '2026-03-31 15:21:26', '2026-03-31 15:21:26'),
(727, 344, 26, 0, 0, 0, 0, '2026-03-31 15:21:26', '2026-03-31 15:21:26'),
(728, 345, 1, 1, 1, 1, 1, '2026-03-31 16:28:39', '2026-03-31 16:28:39'),
(729, 345, 2, 1, 1, 1, 1, '2026-03-31 16:28:39', '2026-03-31 16:28:39'),
(730, 345, 3, 1, 1, 1, 1, '2026-03-31 16:28:39', '2026-03-31 16:28:39'),
(731, 345, 4, 0, 0, 0, 0, '2026-03-31 16:28:39', '2026-03-31 16:28:39'),
(732, 345, 5, 0, 0, 0, 0, '2026-03-31 16:28:39', '2026-03-31 16:28:39'),
(733, 345, 9, 0, 0, 0, 0, '2026-03-31 16:28:39', '2026-03-31 16:28:39'),
(734, 345, 10, 0, 0, 0, 0, '2026-03-31 16:28:39', '2026-03-31 16:28:39'),
(735, 345, 26, 0, 0, 0, 0, '2026-03-31 16:28:39', '2026-03-31 16:28:39'),
(736, 346, 1, 1, 1, 1, 1, '2026-03-31 16:57:11', '2026-03-31 16:57:11'),
(737, 346, 2, 1, 1, 1, 1, '2026-03-31 16:57:11', '2026-03-31 16:57:11'),
(738, 346, 3, 0, 0, 0, 0, '2026-03-31 16:57:11', '2026-03-31 17:00:05'),
(739, 346, 4, 0, 0, 0, 0, '2026-03-31 16:57:11', '2026-03-31 17:00:05'),
(740, 346, 5, 0, 0, 0, 0, '2026-03-31 16:57:11', '2026-03-31 17:00:05'),
(741, 346, 9, 0, 0, 0, 0, '2026-03-31 16:57:11', '2026-03-31 17:00:05'),
(742, 346, 10, 0, 0, 0, 0, '2026-03-31 16:57:11', '2026-03-31 17:00:05'),
(743, 346, 26, 0, 0, 0, 0, '2026-03-31 16:57:11', '2026-03-31 17:00:05'),
(744, 347, 1, 0, 0, 0, 0, '2026-03-31 18:36:06', '2026-03-31 18:36:06'),
(745, 347, 2, 0, 0, 0, 0, '2026-03-31 18:36:06', '2026-03-31 18:36:06'),
(746, 347, 3, 0, 0, 0, 0, '2026-03-31 18:36:06', '2026-03-31 18:36:06'),
(747, 347, 4, 0, 0, 0, 0, '2026-03-31 18:36:06', '2026-03-31 18:36:06'),
(748, 347, 5, 0, 0, 0, 0, '2026-03-31 18:36:06', '2026-03-31 18:36:06'),
(749, 347, 9, 0, 0, 0, 0, '2026-03-31 18:36:06', '2026-03-31 18:36:06'),
(750, 347, 10, 0, 0, 0, 0, '2026-03-31 18:36:06', '2026-03-31 18:36:06'),
(751, 347, 26, 0, 0, 0, 0, '2026-03-31 18:36:06', '2026-03-31 18:36:06'),
(752, 348, 1, 1, 1, 0, 0, '2026-04-01 10:13:35', '2026-04-01 10:15:31'),
(753, 348, 2, 0, 0, 0, 0, '2026-04-01 10:13:35', '2026-04-01 10:14:07'),
(754, 348, 3, 0, 0, 0, 0, '2026-04-01 10:13:35', '2026-04-01 10:14:07'),
(755, 348, 4, 0, 0, 0, 0, '2026-04-01 10:13:35', '2026-04-01 10:14:07'),
(756, 348, 5, 0, 0, 0, 0, '2026-04-01 10:13:35', '2026-04-01 10:14:07'),
(757, 348, 9, 0, 0, 0, 0, '2026-04-01 10:13:35', '2026-04-01 10:14:07'),
(758, 348, 10, 0, 0, 0, 0, '2026-04-01 10:13:35', '2026-04-01 10:14:07'),
(759, 348, 26, 0, 0, 0, 0, '2026-04-01 10:13:35', '2026-04-01 10:14:07'),
(760, 10, 1, 1, 1, 1, 1, '2026-04-03 15:49:52', '2026-04-03 15:49:52'),
(761, 10, 2, 1, 1, 1, 1, '2026-04-03 15:49:52', '2026-04-03 15:49:52'),
(762, 10, 3, 1, 1, 1, 1, '2026-04-03 15:49:52', '2026-04-03 15:49:52'),
(763, 10, 4, 1, 1, 1, 1, '2026-04-03 15:49:52', '2026-04-03 15:49:52'),
(764, 10, 5, 1, 1, 1, 1, '2026-04-03 15:49:52', '2026-04-03 15:49:52'),
(765, 10, 9, 1, 1, 1, 1, '2026-04-03 15:49:52', '2026-04-03 15:49:52'),
(766, 10, 10, 1, 1, 1, 1, '2026-04-03 15:49:52', '2026-04-03 15:49:52'),
(767, 10, 26, 1, 1, 1, 1, '2026-04-03 15:49:52', '2026-04-03 15:49:52'),
(768, 12, 1, 1, 1, 1, 1, '2026-04-15 19:04:46', '2026-04-15 19:04:46'),
(769, 12, 2, 1, 1, 1, 1, '2026-04-15 19:04:46', '2026-04-15 19:04:46'),
(770, 12, 3, 0, 0, 0, 0, '2026-04-15 19:04:46', '2026-04-15 19:04:46'),
(771, 12, 4, 0, 0, 0, 0, '2026-04-15 19:04:46', '2026-04-15 19:04:46'),
(772, 12, 5, 0, 0, 0, 0, '2026-04-15 19:04:46', '2026-04-15 19:04:46'),
(773, 12, 9, 0, 0, 0, 0, '2026-04-15 19:04:46', '2026-04-15 19:04:46'),
(774, 12, 10, 0, 0, 0, 0, '2026-04-15 19:04:46', '2026-04-15 19:04:46'),
(775, 12, 26, 0, 0, 0, 0, '2026-04-15 19:04:46', '2026-04-15 19:04:46'),
(776, 19, 1, 1, 1, 1, 1, '2026-05-26 09:49:10', '2026-05-26 09:49:10'),
(777, 19, 2, 1, 1, 1, 1, '2026-05-26 09:49:10', '2026-05-26 09:49:10'),
(778, 19, 3, 1, 1, 1, 1, '2026-05-26 09:49:10', '2026-05-26 09:49:10'),
(779, 19, 4, 1, 1, 1, 1, '2026-05-26 09:49:10', '2026-05-26 09:49:10'),
(780, 19, 5, 1, 1, 1, 1, '2026-05-26 09:49:10', '2026-05-26 09:49:10'),
(781, 19, 9, 1, 1, 1, 1, '2026-05-26 09:49:10', '2026-05-26 09:49:10'),
(782, 19, 10, 1, 1, 1, 1, '2026-05-26 09:49:10', '2026-05-26 09:49:10'),
(783, 19, 26, 1, 1, 1, 1, '2026-05-26 09:49:10', '2026-05-26 09:49:10'),
(784, 19, 30, 1, 1, 1, 1, '2026-05-26 15:49:28', '2026-05-26 15:49:28'),
(785, 19, 31, 1, 1, 1, 1, '2026-05-26 15:49:28', '2026-05-26 15:49:28');

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_message_templates`
--

CREATE TABLE `whatsapp_message_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `facebook_app_configuration_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `template_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `on_off` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `use_for_template` varchar(255) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `sub_category` varchar(255) DEFAULT NULL,
  `components` text DEFAULT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_message_templates`
--

INSERT INTO `whatsapp_message_templates` (`id`, `facebook_app_configuration_id`, `branch_id`, `template_id`, `name`, `status`, `on_off`, `use_for_template`, `language`, `category`, `sub_category`, `components`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2229947840845810', 'order_conform', 'APPROVED', 'active', NULL, 'en_US', 'UTILITY', NULL, '\"[{\\\"type\\\":\\\"HEADER\\\",\\\"format\\\":\\\"TEXT\\\",\\\"text\\\":\\\"Order confirmed\\\"},{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hi {{1}},\\\\n\\\\nThank you for your {{2}}! Your order number is {{3}}.\\\\n\\\\nWe\'ll start getting {{4}} ready to ship.\\\\n\\\\nEstimated delivery: {{5}} \\\\n\\\\nWe will let you know when your order ships.\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"John\\\",\\\"purchase\\\",\\\"#12345\\\",\\\"2 12-pack of Jasper\'s paper towels\\\",\\\"Jan 1, 2024\\\"]]}},{\\\"type\\\":\\\"BUTTONS\\\",\\\"buttons\\\":[{\\\"type\\\":\\\"URL\\\",\\\"text\\\":\\\"View order details\\\",\\\"url\\\":\\\"https:\\\\\\/\\\\\\/www.example.com\\\\\\/\\\"}]}]\"', 0, '2025-12-23 11:20:18', '2025-12-25 04:46:23'),
(2, 1, 1, '1202446365314468', 'order_update_notification', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hello {{1}}, your order {{2}} has been {{3}}.  \\\\nDelivery expected by {{4}} Us.\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"akash\\\",\\\"patel\\\",\\\"11234\\\",\\\"TO LONG\\\"]]}},{\\\"type\\\":\\\"FOOTER\\\",\\\"text\\\":\\\"Do not share this message with anyone.\\\"}]\"', 0, '2025-12-23 11:20:18', '2025-12-25 04:46:23'),
(3, 1, 1, '1610099976656320', 'birthday', 'APPROVED', 'active', 'Birthday', 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Happy birthday {{1}} , good life ahead.\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"saurav\\\"]]}}]\"', 0, '2025-12-23 11:20:19', '2025-12-25 04:46:23'),
(4, 1, 1, '1239591461368430', 'hello_world', 'APPROVED', 'active', NULL, 'en_US', 'UTILITY', NULL, '\"[{\\\"type\\\":\\\"HEADER\\\",\\\"format\\\":\\\"TEXT\\\",\\\"text\\\":\\\"Hello World\\\"},{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Welcome and congratulations!! This message demonstrates your ability to send a WhatsApp message notification from the Cloud API, hosted by Meta. Thank you for taking the time to test with us.\\\"},{\\\"type\\\":\\\"FOOTER\\\",\\\"text\\\":\\\"WhatsApp Business Platform sample message\\\"}]\"', 0, '2025-12-23 11:20:19', '2025-12-25 04:46:34'),
(5, 2, 28, '877159211343732', 'order_pending', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hello {{1}},\\\\n\\\\nWe\\\\u2019ve received your order with {{2}} \\\\u23f3  \\\\nYour order is currently pending confirmation.\\\\n\\\\n\\\\ud83e\\\\uddfe Order ID: {{3}}  \\\\n\\\\ud83d\\\\udcb0 Total Amount: \\\\u20b9{{4}}\\\\n\\\\nWe\\\\u2019ll notify you once your order is confirmed.\\\\nFor any questions, you can use the options below.\\\\n\\\\nThank you for your patience \\\\ud83d\\\\ude0a\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Customer Name\\\",\\\"Business Name\\\",\\\"Order ID\\\",\\\"Total Amount\\\"]]}}]\"', 0, '2025-12-23 13:05:42', '2025-12-25 06:10:16'),
(6, 2, 28, '2714802242201132', 'order_confirmation', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hello {{1}},\\\\n\\\\nThank you for your order with {{2}} \\\\ud83c\\\\udf89  \\\\nYour order has been successfully placed.\\\\n\\\\n\\\\ud83e\\\\uddfe Order ID: {{3}}  \\\\n\\\\ud83d\\\\udcb0 Total Amount: \\\\u20b9{{4}}\\\\n\\\\nYou can track your order or contact support using the buttons below.\\\\n\\\\nThank you for shopping with us! \\\\ud83d\\\\ude0a\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Customer Name\\\",\\\"Business Name\\\",\\\"Order ID\\\",\\\"Total Amount\\\"]]}}]\"', 0, '2025-12-23 13:05:42', '2025-12-25 06:10:17'),
(7, 2, 28, '1209235890689563', 'video_details', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"HEADER\\\",\\\"format\\\":\\\"VIDEO\\\",\\\"example\\\":{\\\"header_handle\\\":[\\\"https:\\\\\\/\\\\\\/scontent.whatsapp.net\\\\\\/v\\\\\\/t61.29466-34\\\\\\/588751564_1209235894022896_4908131316015330713_n.mp4?ccb=1-7&_nc_sid=8b1bef&_nc_ohc=VzQAtAhoZ6QQ7kNvwEd4OuM&_nc_oc=AdlX0wsjjQChe_IysXo2PzTGkARy3UDwXV6awS0psazWUL95h3lP44TcytSN-RiUmak&_nc_zt=28&_nc_ht=scontent.whatsapp.net&edm=AH51TzQEAAAA&_nc_gid=V4wouIpGLXZQu96T-XW8Kg&_nc_tpa=Q5bMBQHNPd_7fBypgfFTX96AqnzI8-XytKqEgsrteECi9ADz3-QrAnyzb8HhsK_8kZoVl0tlJRuiLllB&oh=01_Q5Aa3QG8kO55RpUZ3KuSw14Xus_lRnsVKZsLIc-uzAoIRv0lqQ&oe=69746151\\\"]}},{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Contact Us For More Details\\\\n*Rabeet Luxurious Perfumes*\\\\n_Feel the Luxury, Wear the Charm_\\\"}]\"', 0, '2025-12-23 13:05:42', '2025-12-25 06:10:08'),
(8, 2, 28, '823568323838030', 'grand_tapi_video_info', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"HEADER\\\",\\\"format\\\":\\\"VIDEO\\\",\\\"example\\\":{\\\"header_handle\\\":[\\\"https:\\\\\\/\\\\\\/scontent.whatsapp.net\\\\\\/v\\\\\\/t61.29466-34\\\\\\/604044617_823568327171363_4475342026337029891_n.mp4?ccb=1-7&_nc_sid=8b1bef&_nc_ohc=U9xDNSV0P5QQ7kNvwFr-e88&_nc_oc=AdkMPqfc9goYQ04huCsv5q5NrtDaR3z31rRnCaWrqhFCAlAobpnrNiok5BBnbOLN_aI&_nc_zt=28&_nc_ht=scontent.whatsapp.net&edm=AH51TzQEAAAA&_nc_gid=V4wouIpGLXZQu96T-XW8Kg&_nc_tpa=Q5bMBQG84bWUHEnoPOI6Ry5ClXAD9YP-y74jw2UaAz74EjcZP_eVBy7EEKEbgsRGZW9i1HVz0A1TfUgJ&oh=01_Q5Aa3QGZKClBX2MaCMwxRiLni7zC9PHtwpIDrepKPqCrql8n6Q&oe=69743E2F\\\"]}},{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"\\\\ud83c\\\\udfe8 Welcome to *{{1}}*! \\\\ud83d\\\\ude0d  \\\\n\\\\n\\\\ud83d\\\\udccd {{2}}\\\\n\\\\ud83d\\\\udcde {{3}}  \\\\n\\\\ud83d\\\\udcb0 Affordable Prices | Comfortable Stay | Perfect Location  \\\\n\\\\n\\\\u2728 Book your stay now \\\\u2014 rooms filling fast!\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"The Grand Tapi Restaurant & Rooms\\\",\\\"Kosmadi Patiya, NH 48 Kamrej Kadodra Highway\\\",\\\"97266 49477\\\"]]}}]\"', 0, '2025-12-23 13:05:42', '2025-12-25 06:10:08'),
(9, 2, 28, '1404749124502746', 'hello_world', 'APPROVED', 'active', NULL, 'en_US', 'UTILITY', NULL, '\"[{\\\"type\\\":\\\"HEADER\\\",\\\"format\\\":\\\"TEXT\\\",\\\"text\\\":\\\"Hello World\\\"},{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Welcome and congratulations!! This message demonstrates your ability to send a WhatsApp message notification from the Cloud API, hosted by Meta. Thank you for taking the time to test with us.\\\"},{\\\"type\\\":\\\"FOOTER\\\",\\\"text\\\":\\\"WhatsApp Business Platform sample message\\\"}]\"', 0, '2025-12-23 13:05:42', '2025-12-24 04:53:54'),
(10, 1, 1, '1411021080367051', 'order_complete_notification', 'APPROVED', 'active', 'Complete order', 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hi {{1}} \\\\ud83d\\\\udc4b,\\\\n\\\\nGreat news! \\\\ud83c\\\\udf89  \\\\nYour order {{2}} from {{3}} has been {{4}} \\\\u2705\\\\n\\\\n\\\\ud83d\\\\udce6 Order ID: {{5}}\\\\n\\\\ud83d\\\\udcb0 Total Amount: ${{6}}\\\\n\\\\nWe appreciate your business \\\\ud83d\\\\ude4f\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Customer Name\\\",\\\"Order ID\\\",\\\"Company Name\\\",\\\"Order Status\\\",\\\"Order ID\\\",\\\"Amount\\\"]]}}]\"', 0, '2025-12-23 13:08:31', '2025-12-25 04:46:40'),
(11, 1, 1, '2121242648702623', 'order_panding_notification', 'APPROVED', 'active', 'Pending order', 'en', 'UTILITY', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hello {{1}} \\\\ud83d\\\\udc4b,\\\\n\\\\nYour order {{2}} from {{3}} is currently **pending** \\\\u23f3.\\\\n\\\\n\\\\ud83d\\\\udce6 Order ID: {{4}}\\\\n\\\\ud83d\\\\udcb0 Amount: ${{5}}\\\\n\\\\nWe\\\\u2019ll notify you once the status is updated. \\\\ud83d\\\\ude4f\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Customer Name\\\",\\\"Order ID\\\",\\\"Company Name\\\",\\\"Order ID\\\",\\\"Amount\\\"]]}}]\"', 0, '2025-12-23 13:16:16', '2025-12-25 04:46:23'),
(12, 1, 1, '1416957246717868', 'return_sale_success_customer', 'APPROVED', 'active', 'Return order', 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hello {{1}} \\\\ud83d\\\\udc4b,\\\\n\\\\nYour return request from {{2}} has been **successfully completed** \\\\u2705.\\\\n\\\\n\\\\ud83d\\\\udce6 Order ID: {{3}}\\\\n\\\\ud83d\\\\udcb0 Refunded Amount: \\\\u20b9{{4}}\\\\n\\\\nThank you for shopping with us. \\\\ud83d\\\\ude4f\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Customer Name\\\",\\\"Store \\\\\\/ Company Name\\\",\\\"Order ID\\\",\\\"Refund Amount\\\"]]}}]\"', 0, '2025-12-25 06:09:17', '2025-12-26 17:45:24'),
(13, 2, 28, '4297543503900655', 'order_complete_notification', 'PENDING', 'inactive', 'Complete order', 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hi {{1}} \\\\ud83d\\\\udc4b,\\\\nGreat news! \\\\ud83c\\\\udf89  \\\\nYour order {{2}} from {{3}} has been {{4}} \\\\u2705\\\\n\\\\ud83d\\\\udce6 Order ID: {{5}}\\\\n\\\\ud83d\\\\udcb0 Total Amount: \\\\u20b9{{6}}\\\\nWe appreciate your business \\\\ud83d\\\\ude4f\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"akash`\\\",\\\"123163456\\\",\\\"feb\\\",\\\"padding\\\",\\\"123163456\\\",\\\"999\\\"]]}}]\"', 0, '2025-12-25 06:10:08', '2025-12-25 06:10:21'),
(14, 2, 28, '2136007120272189', 'order_panding_notification', 'PENDING', 'inactive', 'Pending order', 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hello {{1}} \\\\ud83d\\\\udc4b,\\\\nYour order {{2}} from {{3}} is currently *Padding* \\\\u23f3.\\\\n\\\\ud83d\\\\udce6 Order ID: {{4}}\\\\n\\\\ud83d\\\\udcb0 Amount: \\\\u20b9 {{5}}\\\\nWe\\\\u2019ll notify you once the status is updated. \\\\ud83d\\\\ude4f\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"akash\\\",\\\"123456789\\\",\\\"feb\\\",\\\"123456789\\\",\\\"1222\\\"]]}}]\"', 0, '2025-12-25 06:10:08', '2025-12-25 06:10:24'),
(15, 2, 28, '2139035936626431', 'return_sale_success_customer', 'PENDING', 'inactive', 'Return order', 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hello {{1}} \\\\ud83d\\\\udc4b,\\\\n \\\\nYour return request from {{2}} has been **successfully completed** \\\\u2705.\\\\n \\\\n\\\\ud83d\\\\udce6 Order ID: {{3}}\\\\n\\\\ud83d\\\\udcb0 Refunded Amount: \\\\u20b9{{4}}\\\\n \\\\nThank you for shopping with us. \\\\ud83d\\\\ude4f\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"akash\\\",\\\"suarta`\\\",\\\"1312345\\\",\\\"2390\\\"]]}}]\"', 0, '2025-12-25 06:10:08', '2025-12-25 06:10:27'),
(16, 1, 1, '1205347095114535', 'appointment_rescheduled', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hello {{1}},\\\\n\\\\nYour appointment schedule has been *updated*.\\\\n\\\\n\\\\ud83e\\\\ude7a Service: {{2}}  \\\\n\\\\ud83d\\\\udcc5 Previous: {{3}} at {{4}}  \\\\n\\\\ud83d\\\\udcc5 New: {{5}} at {{6}}\\\\n\\\\nThank you for your understanding.\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Rahul Sharma\\\",\\\"Physiotherapy Session\\\",\\\"20 March 2026\\\",\\\"09:00 AM\\\",\\\"25 March 2026\\\",\\\"10:30 AM\\\"]]}},{\\\"type\\\":\\\"BUTTONS\\\",\\\"buttons\\\":[{\\\"type\\\":\\\"URL\\\",\\\"text\\\":\\\"Visit website\\\",\\\"url\\\":\\\"https:\\\\\\/\\\\\\/tanishphysiofitness.in\\\\\\/\\\"}]}]\"', 0, '2026-02-23 12:35:05', '2026-02-23 12:35:05'),
(17, 1, 1, '1928219567825175', 'session_reminder', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hi {{1}} \\\\ud83d\\\\udc4b\\\\n\\\\nThis is a reminder for your upcoming session.\\\\n\\\\n\\\\ud83e\\\\ude7a Service: {{2}}  \\\\n\\\\ud83d\\\\udcc5 Date: {{3}}  \\\\n\\\\u23f0 Time: {{4}}\\\\n\\\\nPlease try to join 5 minutes before the scheduled time.\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Rahul Sharma\\\",\\\"Physiotherapy Session\\\",\\\"25 March 2026\\\",\\\"10:30 AM\\\"]]}},{\\\"type\\\":\\\"BUTTONS\\\",\\\"buttons\\\":[{\\\"type\\\":\\\"URL\\\",\\\"text\\\":\\\"Visit website\\\",\\\"url\\\":\\\"https:\\\\\\/\\\\\\/tanishphysiofitness.in\\\\\\/\\\"}]}]\"', 0, '2026-02-23 12:35:05', '2026-02-23 12:35:05'),
(18, 1, 1, '1262340959086097', 'payment_successful', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hello {{1}} \\\\u2705\\\\n\\\\nYour payment has been *successfully received*.\\\\n\\\\n\\\\ud83d\\\\udcb0 Amount: \\\\u20b9{{2}}  \\\\n\\\\ud83e\\\\ude7a Service: {{3}}  \\\\n\\\\ud83e\\\\uddfe Transaction ID: {{4}}\\\\n\\\\nYour booking is now confirmed. Thank you for trusting us.\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Rahul Sharma\\\",\\\"1,200\\\",\\\"Physiotherapy Session\\\",\\\"TXN983746\\\"]]}},{\\\"type\\\":\\\"BUTTONS\\\",\\\"buttons\\\":[{\\\"type\\\":\\\"URL\\\",\\\"text\\\":\\\"Visit website\\\",\\\"url\\\":\\\"https:\\\\\\/\\\\\\/invalid.invalid\\\\\\/\\\"}]}]\"', 0, '2026-02-23 12:35:05', '2026-02-23 12:35:05'),
(19, 1, 1, '1241032004626424', 'payment_reminder', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hi {{1}},\\\\n\\\\nThis is a gentle reminder to complete your payment.\\\\n\\\\n\\\\ud83e\\\\ude7a Service: {{2}}  \\\\n\\\\ud83d\\\\udcb0 Amount: \\\\u20b9{{3}}\\\\n\\\\nPlease complete the payment to confirm your booking.\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Rahul Sharma\\\",\\\"Physiotherapy Session\\\",\\\"1,200\\\"]]}},{\\\"type\\\":\\\"BUTTONS\\\",\\\"buttons\\\":[{\\\"type\\\":\\\"URL\\\",\\\"text\\\":\\\"Visit website\\\",\\\"url\\\":\\\"https:\\\\\\/\\\\\\/tanishphysiofitness.in\\\\\\/\\\"}]}]\"', 0, '2026-02-23 12:35:05', '2026-02-23 16:32:30'),
(20, 1, 1, '2440865573034805', 'booking_cancelled', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hello {{1}},\\\\n\\\\nWe\\\\u2019d like to inform you that your booking has been *cancelled*.\\\\n\\\\n\\\\ud83e\\\\ude7a Service: {{2}}  \\\\n\\\\ud83d\\\\udccc Reason: {{3}}\\\\n\\\\nYou can reschedule or contact us anytime through our website.\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Rahul Sharma\\\",\\\"Physiotherapy Session\\\",\\\"Therapist unavailable\\\"]]}},{\\\"type\\\":\\\"BUTTONS\\\",\\\"buttons\\\":[{\\\"type\\\":\\\"URL\\\",\\\"text\\\":\\\"Visit website\\\",\\\"url\\\":\\\"https:\\\\\\/\\\\\\/tanishphysiofitness.in\\\\\\/\\\"}]}]\"', 0, '2026-02-23 12:35:05', '2026-02-23 12:35:05'),
(21, 1, 1, '1530788394661110', 'booking_confirmation', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hi {{1}} \\\\ud83d\\\\ude0a\\\\n\\\\nYour appointment has been *confirmed successfully*.\\\\n\\\\n\\\\ud83e\\\\ude7a Service: {{2}}  \\\\n\\\\ud83d\\\\udcc5 Date: {{3}}  \\\\n\\\\u23f0 Time: {{4}}\\\\n\\\\nWe look forward to assisting you in your session.\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Rahul Sharma\\\",\\\"Physiotherapy Session\\\",\\\"25 March 2026\\\",\\\"10:30 AM\\\"]]}},{\\\"type\\\":\\\"BUTTONS\\\",\\\"buttons\\\":[{\\\"type\\\":\\\"URL\\\",\\\"text\\\":\\\"View Booking\\\",\\\"url\\\":\\\"https:\\\\\\/\\\\\\/tanishphysiofitness.in\\\\\\/\\\"}]}]\"', 0, '2026-02-23 12:35:05', '2026-02-23 12:35:05'),
(22, 1, 1, '883627954595335', 'welcome_message', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Hello {{1}} \\\\ud83d\\\\udc4b\\\\n\\\\nWelcome to Tanish Physio!\\\\n\\\\nYour account has been successfully created with us. We\\\\u2019re excited to support you on your recovery and wellness journey.\\\\n\\\\nIf you have any questions, feel free to reply to this message.\\\\n\\\\nTeam Tanish Physio \\\\ud83d\\\\udc99\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Client Name\\\"]]}},{\\\"type\\\":\\\"BUTTONS\\\",\\\"buttons\\\":[{\\\"type\\\":\\\"URL\\\",\\\"text\\\":\\\"Visit website\\\",\\\"url\\\":\\\"https:\\\\\\/\\\\\\/tanishphysiofitness.in\\\\\\/\\\"}]}]\"', 0, '2026-02-23 12:35:05', '2026-02-23 12:35:05'),
(23, 1, 1, '2626606421055821', 'admin_session_reminder', 'APPROVED', 'active', NULL, 'en', 'UTILITY', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"Dear Admin,\\\\n\\\\nAn upcoming session has been scheduled with the following details:\\\\n\\\\n\\\\ud83d\\\\udc64 Patient Name: {{1}}\\\\n\\\\ud83d\\\\udcde Contact Number: {{2}}\\\\n\\\\ud83e\\\\ude7a Service: {{3}}\\\\n\\\\ud83d\\\\udcc5 Scheduled Date: {{4}}\\\\n\\\\u23f0 Scheduled Time: {{5}}\\\\n\\\\nKindly ensure arrangements are completed accordingly.\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Patient\\\",\\\"6351148406\\\",\\\"Service\\\",\\\"12-03-2027\\\",\\\"12:22\\\"]]}},{\\\"type\\\":\\\"BUTTONS\\\",\\\"buttons\\\":[{\\\"type\\\":\\\"URL\\\",\\\"text\\\":\\\"Visit website\\\",\\\"url\\\":\\\"https:\\\\\\/\\\\\\/tanishphysiofitness.in\\\\\\/physio-admin\\\\\\/sessions\\\"}]}]\"', 0, '2026-03-20 15:37:31', '2026-03-20 15:37:31'),
(24, 1, 1, '922396243518578', 'new_booking_request', 'APPROVED', 'active', NULL, 'en', 'MARKETING', 'CUSTOM', '\"[{\\\"type\\\":\\\"BODY\\\",\\\"text\\\":\\\"New appointment booking received.\\\\n\\\\nPatient Name: {{1}}\\\\nPhone: {{2}}\\\\nService: {{3}}\\\\nDate: {{4}}\\\\nTime: {{5}}\\\\n\\\\nPlease review and confirm the appointment from the admin panel.\\\",\\\"example\\\":{\\\"body_text\\\":[[\\\"Patient Name\\\",\\\"Patient Phone\\\",\\\"Service Name\\\",\\\"Date\\\",\\\"Time\\\"]]}},{\\\"type\\\":\\\"BUTTONS\\\",\\\"buttons\\\":[{\\\"type\\\":\\\"URL\\\",\\\"text\\\":\\\"View Booking\\\",\\\"url\\\":\\\"https:\\\\\\/\\\\\\/tanishphysiofitness.in\\\\\\/physio-admin\\\"}]}]\"', 0, '2026-03-20 15:37:31', '2026-03-20 15:37:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advance_payments`
--
ALTER TABLE `advance_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_master`
--
ALTER TABLE `bank_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `connected_devices`
--
ALTER TABLE `connected_devices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `connected_device_scans`
--
ALTER TABLE `connected_device_scans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `connected_device_scans_device_code_index` (`device_code`),
  ADD KEY `connected_device_scans_consumed_at_index` (`consumed_at`);

--
-- Indexes for table `credit_notes_type`
--
ALTER TABLE `credit_notes_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_note_items`
--
ALTER TABLE `credit_note_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_invoice`
--
ALTER TABLE `custom_invoice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `custom_invoice_invoice_number_unique` (`invoice_number`);

--
-- Indexes for table `custom_invoice_item`
--
ALTER TABLE `custom_invoice_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `debit_notes_type`
--
ALTER TABLE `debit_notes_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_types`
--
ALTER TABLE `expense_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facebook_app_configurations`
--
ALTER TABLE `facebook_app_configurations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `facebook_app_configurations_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `follow_ups`
--
ALTER TABLE `follow_ups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `labour_items`
--
ALTER TABLE `labour_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leads_branch_id_isdeleted_lead_status_index` (`branch_id`,`isDeleted`,`lead_status`),
  ADD KEY `leads_assigned_to_created_by_index` (`assigned_to`,`created_by`),
  ADD KEY `leads_converted_customer_id_index` (`converted_customer_id`);

--
-- Indexes for table `lead_status_histories`
--
ALTER TABLE `lead_status_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_status_histories_lead_id_created_at_index` (`lead_id`,`created_at`),
  ADD KEY `lead_status_histories_branch_id_updated_by_index` (`branch_id`,`updated_by`);

--
-- Indexes for table `log_attendance`
--
ALTER TABLE `log_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meetings_branch_id_index` (`branch_id`),
  ADD KEY `meetings_customer_id_index` (`customer_id`),
  ADD KEY `meetings_assigned_to_index` (`assigned_to`),
  ADD KEY `meetings_status_index` (`status`),
  ADD KEY `meetings_isdeleted_index` (`isDeleted`);

--
-- Indexes for table `meeting_reminder_logs`
--
ALTER TABLE `meeting_reminder_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `meeting_reminder_unique` (`meeting_id`,`reminder_at`),
  ADD KEY `meeting_reminder_queue_idx` (`queue_status`,`reminder_at`),
  ADD KEY `meeting_reminder_branch_idx` (`branch_id`,`reminder_at`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `modules_module_unique` (`module`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_store`
--
ALTER TABLE `payment_store`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`,`isDeleted`);

--
-- Indexes for table `product_inventory`
--
ALTER TABLE `product_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `purchase_invoice`
--
ALTER TABLE `purchase_invoice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_invoice_invoice_number_unique` (`invoice_number`);

--
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_returns_return_no_unique` (`return_no`);

--
-- Indexes for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_labour_items`
--
ALTER TABLE `sales_labour_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_returns`
--
ALTER TABLE `sales_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_returns_return_number_unique` (`return_number`);

--
-- Indexes for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `units_unit_name_unique` (`unit_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_details_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `whatsapp_message_templates`
--
ALTER TABLE `whatsapp_message_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `whatsapp_message_templates_template_id_unique` (`template_id`),
  ADD KEY `whatsapp_message_templates_facebook_app_configuration_id_foreign` (`facebook_app_configuration_id`),
  ADD KEY `whatsapp_message_templates_branch_id_index` (`branch_id`),
  ADD KEY `whatsapp_message_templates_template_id_index` (`template_id`),
  ADD KEY `whatsapp_message_templates_status_index` (`status`),
  ADD KEY `whatsapp_message_templates_branch_id_isdeleted_index` (`branch_id`,`isDeleted`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advance_payments`
--
ALTER TABLE `advance_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bank_master`
--
ALTER TABLE `bank_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `connected_devices`
--
ALTER TABLE `connected_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `connected_device_scans`
--
ALTER TABLE `connected_device_scans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `credit_notes_type`
--
ALTER TABLE `credit_notes_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `credit_note_items`
--
ALTER TABLE `credit_note_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `custom_invoice`
--
ALTER TABLE `custom_invoice`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `custom_invoice_item`
--
ALTER TABLE `custom_invoice_item`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `debit_notes_type`
--
ALTER TABLE `debit_notes_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `expense_types`
--
ALTER TABLE `expense_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `facebook_app_configurations`
--
ALTER TABLE `facebook_app_configurations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `follow_ups`
--
ALTER TABLE `follow_ups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `labour_items`
--
ALTER TABLE `labour_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lead_status_histories`
--
ALTER TABLE `lead_status_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `log_attendance`
--
ALTER TABLE `log_attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `meeting_reminder_logs`
--
ALTER TABLE `meeting_reminder_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=245;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `payment_store`
--
ALTER TABLE `payment_store`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `product_inventory`
--
ALTER TABLE `product_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `purchase_invoice`
--
ALTER TABLE `purchase_invoice`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_labour_items`
--
ALTER TABLE `sales_labour_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sales_returns`
--
ALTER TABLE `sales_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  MODIFY `id` bigint(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=786;

--
-- AUTO_INCREMENT for table `whatsapp_message_templates`
--
ALTER TABLE `whatsapp_message_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `facebook_app_configurations`
--
ALTER TABLE `facebook_app_configurations`
  ADD CONSTRAINT `facebook_app_configurations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `whatsapp_message_templates`
--
ALTER TABLE `whatsapp_message_templates`
  ADD CONSTRAINT `whatsapp_message_templates_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `whatsapp_message_templates_facebook_app_configuration_id_foreign` FOREIGN KEY (`facebook_app_configuration_id`) REFERENCES `facebook_app_configurations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
