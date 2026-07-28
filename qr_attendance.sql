-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2026 at 12:14 PM
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
-- Database: `qr_attendance`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `scanned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `status` enum('present','late','excused') NOT NULL DEFAULT 'present',
  `notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `session_id`, `student_id`, `scanned_at`, `ip_address`, `device_info`, `status`, `notes`) VALUES
(1, 18, 3, '2026-06-30 21:28:20', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_8_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/145.2  Mobile/15E148 Safari/604.1', 'late', NULL),
(2, 19, 3, '2026-06-30 21:33:56', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_8_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/145.2  Mobile/15E148 Safari/604.1', 'present', NULL),
(3, 20, 3, '2026-07-03 20:45:35', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_8_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/145.2  Mobile/15E148 Safari/604.1', 'late', NULL),
(4, 21, 4, '2026-07-04 08:38:19', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_8_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/145.2  Mobile/15E148 Safari/604.1', 'late', NULL),
(5, 22, 3, '2026-07-04 21:41:04', 'manual', 'Manual entry by lecturer', 'present', NULL),
(6, 23, 3, '2026-07-05 17:23:29', 'manual', 'Manual — confirmed by class rep', 'present', NULL),
(7, 24, 4, '2026-07-06 13:51:10', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_8_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/145.2  Mobile/15E148 Safari/604.1', 'late', NULL),
(8, 26, 5, '2026-07-11 06:56:35', '10.79.172.132', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', 'late', NULL),
(9, 26, 3, '2026-07-11 06:59:12', '10.79.172.132', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', 'late', NULL),
(10, 27, 3, '2026-07-11 07:01:57', '10.79.172.132', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', 'late', NULL),
(11, 32, 3, '2026-07-25 11:57:27', '10.84.213.126', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36', 'late', NULL),
(12, 27, 5, '2026-07-25 20:48:09', 'manual', 'Manual — confirmed by class rep', 'present', NULL),
(13, 32, 5, '2026-07-25 20:48:12', 'manual', 'Manual — confirmed by class rep', 'present', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `user_name` varchar(120) DEFAULT NULL,
  `user_role` varchar(20) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `user_name`, `user_role`, `action`, `module`, `description`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, 'System Admin', 'admin', 'auth.logout', 'auth', 'User logged out: System Admin', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 09:28:06'),
(2, 6, 'Wilson Leman', 'lecturer', 'auth.login', 'auth', 'User logged in: wleman@gmail.com', NULL, '{\"email\":\"wleman@gmail.com\",\"role\":\"lecturer\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 09:28:31'),
(3, 6, 'Wilson Leman', 'lecturer', 'auth.logout', 'auth', 'User logged out: Wilson Leman', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 09:28:57'),
(4, 2, 'System Admin', 'admin', 'auth.login', 'auth', 'User logged in: admin@university.edu', NULL, '{\"email\":\"admin@university.edu\",\"role\":\"admin\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 09:29:03'),
(5, 2, 'System Admin', 'admin', 'user.created', 'users', 'Created student account: Shareef Saeed (shareef@gmail.com)', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 09:59:47'),
(6, 2, 'System Admin', 'admin', 'auth.logout', 'auth', 'User logged out: System Admin', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 10:05:46'),
(7, 6, 'Wilson Leman', 'lecturer', 'auth.login', 'auth', 'User logged in: wleman@gmail.com', NULL, '{\"email\":\"wleman@gmail.com\",\"role\":\"lecturer\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 10:06:31'),
(8, 6, 'Wilson Leman', 'lecturer', 'auth.logout', 'auth', 'User logged out: Wilson Leman', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 10:07:53'),
(9, 7, 'Austin Phiri', 'student', 'auth.login', 'auth', 'User logged in: austin@gmail.com', NULL, '{\"email\":\"austin@gmail.com\",\"role\":\"student\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-28 10:09:08');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `lecturer_id` int(10) UNSIGNED DEFAULT NULL,
  `code` varchar(30) NOT NULL,
  `name` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `credit_hours` tinyint(3) UNSIGNED DEFAULT 3,
  `semester` tinyint(3) UNSIGNED DEFAULT NULL,
  `year_of_study` tinyint(3) UNSIGNED DEFAULT NULL,
  `academic_year` varchar(10) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `department_id`, `lecturer_id`, `code`, `name`, `description`, `credit_hours`, `semester`, `year_of_study`, `academic_year`, `is_active`, `created_at`) VALUES
(1, 2, NULL, 'ITIL', 'Introduction in Information Systems', NULL, 2, 2, NULL, '2023/2024', 1, '2026-06-30 07:31:44'),
(2, 1, 2, 'ITST', 'Statistics', NULL, 3, 2, NULL, '2023/2024', 1, '2026-06-30 20:11:02'),
(3, 3, 3, 'IB', 'Introduction in Business', NULL, 3, 1, NULL, '2023/2024', 1, '2026-07-04 08:34:37'),
(4, 2, 2, 'PG', 'Programming', NULL, 2, 2, NULL, '2023/2024', 1, '2026-07-11 06:49:44');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `code` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `code`, `created_at`) VALUES
(1, 'Computer Science', 'CS', '2026-06-29 09:31:08'),
(2, 'Information Technology', 'IT', '2026-06-29 09:31:08'),
(3, 'Business Administration', 'BA', '2026-06-29 09:31:08'),
(4, 'Public healthy', 'PH', '2026-07-11 06:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_class_rep` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `enrolled_at`, `is_class_rep`) VALUES
(3, 3, 1, '2026-06-30 20:08:25', 0),
(4, 3, 2, '2026-06-30 20:11:19', 1),
(5, 4, 3, '2026-07-04 08:37:11', 1),
(6, 5, 1, '2026-07-11 06:46:19', 0),
(7, 3, 4, '2026-07-11 06:55:54', 1),
(8, 5, 4, '2026-07-11 06:55:59', 0),
(9, 6, 4, '2026-07-23 09:03:20', 0);

-- --------------------------------------------------------

--
-- Table structure for table `import_logs`
--

CREATE TABLE `import_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` enum('students','lecturers','courses','enrollments') NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `total_rows` int(11) DEFAULT 0,
  `success` int(11) DEFAULT 0,
  `failed` int(11) DEFAULT 0,
  `errors` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `import_logs`
--

INSERT INTO `import_logs` (`id`, `user_id`, `type`, `filename`, `total_rows`, `success`, `failed`, `errors`, `created_at`) VALUES
(1, 2, 'students', 'students_template.csv', 4, 4, 0, NULL, '2026-07-27 21:22:44'),
(2, 2, 'students', 'students_import_template.csv', 4, 0, 4, 'Row 2: Reg number \'2024/CS/001\' already exists — skipped\nRow 3: Reg number \'2024/CS/002\' already exists — skipped\nRow 4: Reg number \'2024/IT/001\' already exists — skipped\nRow 5: Reg number \'2024/BA/001\' already exists — skipped', '2026-07-27 21:48:53'),
(3, 2, 'students', 'students_import_template.csv', 4, 0, 4, 'Row 2: Reg number \'2024/CS/001\' already exists — skipped\nRow 3: Reg number \'2024/CS/002\' already exists — skipped\nRow 4: Reg number \'2024/IT/001\' already exists — skipped\nRow 5: Reg number \'2024/BA/001\' already exists — skipped', '2026-07-27 21:50:19'),
(4, 2, 'students', 'students_import_template.csv', 4, 0, 4, 'Row 2: Reg number \'2024/CS/001\' already exists — skipped\nRow 3: Reg number \'2024/CS/002\' already exists — skipped\nRow 4: Reg number \'2024/IT/001\' already exists — skipped\nRow 5: Reg number \'2024/BA/001\' already exists — skipped', '2026-07-27 21:51:40'),
(5, 2, 'students', 'students_import_template.csv', 4, 4, 0, 'Row 2: \'Abdul Razack\' added but NO matching courses found for Dept=CS Year=1 Sem=1\nRow 3: \'Asiya Hanif\' added but NO matching courses found for Dept=CS Year=1 Sem=1\nRow 4: \'Ren Chirwa\' added but NO matching courses found for Dept=IT Year=2 Sem=1\nRow 5: \'Jannah saeed\' added but NO matching courses found for Dept=BA Year=1 Sem=2', '2026-07-27 21:52:47');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `staff_number` varchar(50) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`id`, `user_id`, `department_id`, `staff_number`, `phone`, `created_at`) VALUES
(2, 6, 2, 'STF-97264E', NULL, '2026-06-30 20:06:43'),
(3, 9, 3, 'STF-A790AD', NULL, '2026-07-04 08:32:36'),
(4, 12, 2, '4', NULL, '2026-07-23 13:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `manual_attendance`
--

CREATE TABLE `manual_attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `reg_number` varchar(50) NOT NULL,
  `status` enum('pending','confirmed','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manual_attendance`
--

INSERT INTO `manual_attendance` (`id`, `session_id`, `student_id`, `reg_number`, `status`, `created_at`) VALUES
(1, 23, 3, 'STU-978313ED', 'confirmed', '2026-07-04 21:53:40'),
(2, 27, 5, 'ICBM/ICT/BXIBW2', 'confirmed', '2026-07-11 07:02:11'),
(3, 32, 5, 'ICBM/ICT/BXIBW2', 'confirmed', '2026-07-25 20:09:01');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
(1, 10, '9df85b8c37242f90e9f9d9f9a8ebc4d5440686d66a1b110a77ab847b6871612c', '2026-07-22 20:15:34', 1, '2026-07-22 20:15:17'),
(2, 10, '15c976f8770e4100cb7bce644f5ef142576b91cfb68b97a9bdbcf777d712e46d', '2026-07-22 21:15:34', 0, '2026-07-22 20:15:34');

-- --------------------------------------------------------

--
-- Table structure for table `payment_claims`
--

CREATE TABLE `payment_claims` (
  `id` int(10) UNSIGNED NOT NULL,
  `lecturer_id` int(10) UNSIGNED NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `month` varchar(20) NOT NULL,
  `designation` enum('full_time','part_time') DEFAULT 'part_time',
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_branch` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT 0.00,
  `status` enum('draft','submitted','approved','rejected') DEFAULT 'draft',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_claims`
--

INSERT INTO `payment_claims` (`id`, `lecturer_id`, `academic_year`, `month`, `designation`, `bank_name`, `bank_branch`, `account_number`, `telephone`, `hourly_rate`, `status`, `submitted_at`, `notes`, `created_at`) VALUES
(1, 2, '2026/2027', '2026-07', 'part_time', 'Naional Bank', 'Blanyre Branch', '1008726524', '+26599992399', 9000.00, 'approved', '2026-07-05 22:14:20', 'Process it', '2026-07-05 22:02:07'),
(2, 3, '2026/2027', '2026-07', 'part_time', 'First Capital Bank', 'Blanyre Branch', '075448586557', '+26599992387', 7000.00, 'approved', '2026-07-06 13:54:37', 'June enquiry', '2026-07-06 13:54:22');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `lecturer_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(180) DEFAULT NULL,
  `session_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `qr_token` varchar(64) NOT NULL,
  `qr_expires_at` timestamp NOT NULL DEFAULT '1999-12-31 22:00:00',
  `qr_image_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','closed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `course_id`, `lecturer_id`, `title`, `session_date`, `start_time`, `end_time`, `qr_token`, `qr_expires_at`, `qr_image_path`, `status`, `created_at`) VALUES
(9, 2, 2, 'week 2', '2026-06-30', '22:12:00', '22:30:00', 'bd308795e5b0c23555883c99bafb88159fd798d97ddf277238e77a5be2d35599', '2026-06-30 20:12:48', NULL, 'closed', '2026-06-30 20:12:21'),
(10, 2, 2, 'week 2', '2026-06-30', '22:30:00', '23:00:00', 'fd40ec0a476c73bebce208f0dd076ac244e6d7517fd6ca550ab2fc103b2b5af9', '2026-06-30 20:24:28', NULL, 'closed', '2026-06-30 20:13:45'),
(11, 2, 2, 'Week 3', '2026-06-30', '11:25:00', '12:00:00', 'c70c3c1185f10a06f843765e5684ca7c05feb076ee358099d14c286fb62df9ae', '2026-06-30 20:28:59', NULL, 'closed', '2026-06-30 20:25:12'),
(12, 2, 2, 'Week 3', '2026-06-30', '22:37:00', '23:00:00', '9a21978e5078b2bb95ca0c91dd63b1e0eac4b77262fccc492d9af7a9debfea86', '2026-06-30 20:46:50', NULL, 'closed', '2026-06-30 20:36:58'),
(13, 2, 2, 'Week 5', '2026-06-30', '22:47:00', '23:00:00', '3698ed664fa33b3178fd633d79329b746817b3f750c6bfca0aa24da943420396', '2026-06-30 20:52:44', NULL, 'closed', '2026-06-30 20:47:36'),
(14, 2, 2, 'Week 7', '2026-06-30', '22:53:00', '23:00:00', '19c5aa3b5d7e0eb8793bdbb818f9f6d85a73167b4e0621b5b9de1eb4ad3eb7b9', '2026-06-30 20:59:01', NULL, 'closed', '2026-06-30 20:53:38'),
(15, 2, 2, 'week 8', '2026-06-30', '23:00:00', '23:30:00', '0b07f5f0a77cd53383521c27242634151a29e208dfba33c60b54cc46bb232df0', '2026-06-30 21:01:53', NULL, 'closed', '2026-06-30 20:59:29'),
(16, 2, 2, 'wee 12', '2026-06-30', '23:00:00', '23:30:00', '13a0cccdf315fc8bcb94fa18526794e63907ed9f8a3ce5833aa30dfd165af182', '2026-06-30 21:09:01', NULL, 'closed', '2026-06-30 21:02:29'),
(17, 2, 2, 'Week 11', '2026-06-30', '23:09:00', '23:30:00', 'ed6fa98ebb020b2466c77a7cd6edd263a4d2b132539095ef2ce8453eae441233', '2026-06-30 21:21:52', NULL, 'closed', '2026-06-30 21:09:30'),
(18, 2, 2, 'week 43', '2026-06-30', '08:00:00', '10:00:00', '8ce8b8718986f31d385fa2aa2a311ab303e8005c2077cffba069c6995ea573b0', '2026-06-30 23:27:28', NULL, 'closed', '2026-06-30 21:27:28'),
(19, 2, 2, 'week 12', '2026-06-30', '23:33:00', '23:30:00', 'b14068548f8326d05d6d12947d0b279fc6507b3440fdf82c57a17f7ae2999575', '2026-06-30 23:33:33', NULL, 'closed', '2026-06-30 21:33:33'),
(20, 2, 2, 'week 19', '2026-07-03', '08:00:00', '10:00:00', 'd3ca49347e7c1b9bc47d1926e0abfe79741c7be52e47c5475a01afda1c8abf11', '2026-07-03 22:39:53', NULL, 'closed', '2026-07-03 20:39:53'),
(21, 3, 3, 'week 4', '2026-07-04', '08:00:00', '10:00:00', '857226c8e61bfb00de257bb415c51156a1d48cdcdcc84e9dce557c3a7fe49cac', '2026-07-04 10:35:23', NULL, 'closed', '2026-07-04 08:35:23'),
(22, 2, 2, 'Week 20', '2026-07-04', '08:00:00', '10:00:00', '726edf5da5d3d70a1028af6559899f8fa519dc786edd0c1d8fd2f45db837a62a', '2026-07-04 13:38:38', NULL, 'closed', '2026-07-04 11:38:38'),
(23, 2, 2, 'Week 20', '2026-07-04', '08:00:00', '10:00:00', 'dfbe19db489e7c40f752fc1d059ae6d4bc0303125522a924359a5f883c062863', '2026-07-04 23:53:14', NULL, 'closed', '2026-07-04 21:53:14'),
(24, 3, 3, 'week 3', '2026-07-06', '08:00:00', '10:00:00', '1d16eba88be12ac9197a2f6c67006c62c186fd38b2f3e62819dd108cf72b7d87', '2026-07-06 15:50:16', NULL, 'closed', '2026-07-06 13:50:16'),
(25, 4, 2, 'week 1', '2026-07-11', '08:00:00', '10:00:00', '01de01945ad879495354d9f76047da7542baef5b88db4ce885b16172da30d31d', '2026-07-11 08:51:07', NULL, 'pending', '2026-07-11 06:51:07'),
(26, 4, 2, 'week 1', '2026-07-11', '08:00:00', '10:00:00', '56b205aafb6787cc725a123b6807ab8b29ad975af675b22984704a32c6775ef6', '2026-07-11 08:52:18', NULL, 'closed', '2026-07-11 06:52:18'),
(27, 4, 2, 'week 2', '2026-07-11', '08:00:00', '10:00:00', '60ea458780ff47466317eeb6395e33d437bdb680fe627d81feba04162e004198', '2026-07-11 09:01:42', NULL, 'closed', '2026-07-11 07:01:42'),
(28, 4, 2, 'week 2', '2026-07-23', '08:00:00', '10:00:00', '6d64d23583716b17e919a3c50defbbf1e344de44514354fd54fee3e843c4522b', '2026-07-23 10:00:39', NULL, 'closed', '2026-07-23 08:00:39'),
(29, 4, 2, 'week 5', '2026-07-23', '08:00:00', '10:00:00', 'd8fe48b98f6a9027ec42aa1bbb858bb30478c7d773600217eb5e502937d60b73', '2026-07-23 11:05:46', NULL, 'closed', '2026-07-23 09:05:46'),
(30, 4, 2, 'Week 5', '2026-07-23', '08:00:00', '10:00:00', 'a9fe1b6970b9d5719cb6e8bc8a2ecef5e95ae67334440dbbd82612ffce4dfc0e', '2026-07-23 14:36:37', NULL, 'closed', '2026-07-23 12:36:37'),
(31, 4, 2, 'wee57', '2026-07-23', '08:00:00', '10:00:00', '98954b1932314b210c9a944f84b2f09c09dc9eca728c5f86eee7c55a517715a9', '2026-07-23 15:14:13', NULL, 'closed', '2026-07-23 13:14:13'),
(32, 4, 2, 'week 29', '2026-07-25', '09:00:00', '10:00:00', '8d3bc3471905e502a023b6575722e9c9caf41b3db08c5dbd255393307d65a60e', '2026-07-25 13:55:52', NULL, 'active', '2026-07-25 11:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `student_number` varchar(50) NOT NULL,
  `year_of_study` tinyint(3) UNSIGNED DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `department_id`, `student_number`, `year_of_study`, `phone`, `created_at`) VALUES
(3, 7, 2, 'STU-978313ED', NULL, NULL, '2026-06-30 20:07:22'),
(4, 8, 3, 'STU-4773DF6D', NULL, NULL, '2026-07-04 08:31:16'),
(5, 10, 2, 'ICBM/ICT/BXIBW2', NULL, NULL, '2026-07-11 06:45:54'),
(6, 11, 2, 'ICBM/ICT/BXIBW29', NULL, NULL, '2026-07-23 09:01:46'),
(7, 13, 1, '2024/CS/001', 1, '2.65991E+11', '2026-07-27 21:22:44'),
(8, 14, 1, '2024/CS/002', 1, '2.65881E+11', '2026-07-27 21:22:44'),
(9, 15, 2, '2024/IT/001', 2, NULL, '2026-07-27 21:22:44'),
(10, 16, 3, '2024/BA/001', 3, '2.65991E+11', '2026-07-27 21:22:44'),
(11, 17, 1, '2024/CS/001324', 1, '2.66E+11', '2026-07-27 21:52:47'),
(12, 18, 1, '2024/CS/002654', 1, NULL, '2026-07-27 21:52:47'),
(13, 19, 2, '2024/IT/0010987', 2, '2.66E+11', '2026-07-27 21:52:47'),
(14, 20, 3, '2024/BA/001194', 1, NULL, '2026-07-27 21:52:47'),
(15, 21, 3, '2025/32/984/C/456', NULL, NULL, '2026-07-28 09:59:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','lecturer','student') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'System Admin', 'admin@university.edu', '$2y$10$TOXXhEi5e6NwEVs4gFlWBeUzOBm0N.DoKY5zAJ3jYS8tyTn0/XJI6', 'admin', 1, '2026-06-29 15:05:03', '2026-06-30 07:11:31'),
(6, 'Wilson Leman', 'wleman@gmail.com', '$2y$10$I09zmZ/JPrUn588fnfY3OOMu4cSugmH7YVw.g6tuVv640MSe2b4V2', 'lecturer', 1, '2026-06-30 20:06:43', '2026-06-30 20:06:43'),
(7, 'Austin Phiri', 'austin@gmail.com', '$2y$10$aO67zBB8axkjYm0JpkjktOTi4U9TKjpqBQkDt.KRdX9f5SjEEXi.S', 'student', 1, '2026-06-30 20:07:22', '2026-07-28 10:08:53'),
(8, 'Ruqaya Frank', 'ruqaya@gmail.com', '$2y$10$zzdOCa8fJuNc0Y84H7gYz.mSBxtRGjYEerXOYH3FV4qJwWcHGj6Z2', 'student', 1, '2026-07-04 08:31:16', '2026-07-04 22:07:16'),
(9, 'Barack Husen', 'barack@gmail.com', '$2y$10$DrW9vjznRrV/juK9ffH5TeBJxIi5ZzMsAOD/qbyJrUXnSjLb5VyWS', 'lecturer', 1, '2026-07-04 08:32:36', '2026-07-04 08:32:36'),
(10, 'suleman', 'sule@gmail.com', '$2y$10$eXQhaWfsDqPLxDTiEVhft.jWr9JgFtL8D2JtQtvU9MG6UPexohkVa', 'student', 1, '2026-07-11 06:45:54', '2026-07-11 06:45:54'),
(11, 'Osman Belo', 'osman@gmail.com', '$2y$10$aPAvbeJEPPQTalzEPJh7uOnYIysmZXNi7pFR5DnP.LNJXKgt6iL9a', 'student', 1, '2026-07-23 09:01:46', '2026-07-23 09:01:46'),
(12, 'Sulaiman Abubakar', 'sulaiman@gmail.com', '$2y$10$Yzpej.NHcBaHjCiAyCi6hOOthFEpGra6bkTt7/.DJOsq1k5xxjJM2', 'lecturer', 1, '2026-07-23 13:11:58', '2026-07-23 13:11:58'),
(13, 'John Banda', 'john.banda@university.edu', '$2y$10$/3FalNoPriaUY.nDK9TNWedclwJk2xwDw5gDE91OkbwkXT5Vcnjdi', 'student', 1, '2026-07-27 21:22:44', '2026-07-27 21:22:44'),
(14, 'Mary Phiri', 'mary.phiri@university.edu', '$2y$10$zVdK9MdnvI9LmXPVlzgC8elATntC7g4TE78eTeWDLHLv2gGT9Pr0q', 'student', 1, '2026-07-27 21:22:44', '2026-07-27 21:22:44'),
(15, 'James Mwale', 'james.mwale@university.edu', '$2y$10$gcMHyhxBFL5YL9mYqnvIeOYGW5dLpy8IZLQ3ZixmwqDLtLcNVn4gO', 'student', 1, '2026-07-27 21:22:44', '2026-07-27 21:22:44'),
(16, 'Sarah Chirwa', 'sarah.chirwa@university.edu', '$2y$10$lxDInST.QArjJVA9U98a.uwZIEzx3WOz6pFMGH0Nv/D.5aLNQpaxi', 'student', 1, '2026-07-27 21:22:44', '2026-07-27 21:22:44'),
(17, 'Abdul Razack', 'abbdul@uni.edu', '$2y$10$SCVSdQ9MxSFj2jufn358u.LviQ53413C5efi0LPGyzOdm165i15Ba', 'student', 1, '2026-07-27 21:52:47', '2026-07-27 21:52:47'),
(18, 'Asiya Hanif', 'asiya@uni.edu', '$2y$10$drSm.cZUut02WuG9GIZh6uRSRmrMY3SV.t5htLsL9FCClbv2BtGLu', 'student', 1, '2026-07-27 21:52:47', '2026-07-27 21:52:47'),
(19, 'Ren Chirwa', 'renc@uni.edu', '$2y$10$gSdJz8Xy5z9Kmn7xB4anx.ugyhbCTwR7VN78uSQABcgMT2L.smwuu', 'student', 1, '2026-07-27 21:52:47', '2026-07-27 21:52:47'),
(20, 'Jannah saeed', 'janna@uni.edu', '$2y$10$iJKA71Ec7mqpEsAc.HhxbeqPfvIq.iyrWhNrPpzo6FeIBpJ42qg0m', 'student', 1, '2026-07-27 21:52:47', '2026-07-27 21:52:47'),
(21, 'Shareef Saeed', 'shareef@gmail.com', '$2y$10$Nnk76YoJkrpfobmtQPzVYOyoYB42.X.GjWv2ksTwpsT98s0fX4igS', 'student', 1, '2026-07-28 09:59:47', '2026-07-28 09:59:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_attendance` (`session_id`,`student_id`),
  ADD KEY `idx_att_student` (`student_id`),
  ADD KEY `idx_att_session` (`session_id`),
  ADD KEY `idx_att_scanned` (`scanned_at`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_action` (`action`),
  ADD KEY `idx_audit_module` (`module`),
  ADD KEY `idx_audit_created` (`created_at`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_course_dept` (`department_id`),
  ADD KEY `fk_course_lecturer` (`lecturer_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_enrollment` (`student_id`,`course_id`),
  ADD KEY `fk_enroll_course` (`course_id`);

--
-- Indexes for table `import_logs`
--
ALTER TABLE `import_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_import_user` (`user_id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `staff_number` (`staff_number`),
  ADD KEY `fk_lecturer_dept` (`department_id`);

--
-- Indexes for table `manual_attendance`
--
ALTER TABLE `manual_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ma_session` (`session_id`),
  ADD KEY `fk_ma_student` (`student_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `fk_reset_user` (`user_id`);

--
-- Indexes for table `payment_claims`
--
ALTER TABLE `payment_claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_claim_lecturer` (`lecturer_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `qr_token` (`qr_token`),
  ADD KEY `fk_session_course` (`course_id`),
  ADD KEY `fk_session_lecturer` (`lecturer_id`),
  ADD KEY `idx_session_date` (`session_date`),
  ADD KEY `idx_session_status` (`status`),
  ADD KEY `idx_session_token` (`qr_token`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD KEY `fk_student_dept` (`department_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `import_logs`
--
ALTER TABLE `import_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `manual_attendance`
--
ALTER TABLE `manual_attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_claims`
--
ALTER TABLE `payment_claims`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_att_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_att_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `fk_course_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_course_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `fk_enroll_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enroll_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `import_logs`
--
ALTER TABLE `import_logs`
  ADD CONSTRAINT `fk_import_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD CONSTRAINT `fk_lecturer_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lecturer_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `manual_attendance`
--
ALTER TABLE `manual_attendance`
  ADD CONSTRAINT `fk_ma_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ma_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_claims`
--
ALTER TABLE `payment_claims`
  ADD CONSTRAINT `fk_claim_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_session_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_session_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_student_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
