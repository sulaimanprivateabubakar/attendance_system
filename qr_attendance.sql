-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2026 at 06:24 PM
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
(4, 21, 4, '2026-07-04 08:38:19', '172.20.10.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_8_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/145.2  Mobile/15E148 Safari/604.1', 'late', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `academic_year` varchar(10) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `department_id`, `lecturer_id`, `code`, `name`, `description`, `credit_hours`, `semester`, `academic_year`, `is_active`, `created_at`) VALUES
(1, 2, NULL, 'ITIL', 'Introduction in Information Systems', NULL, 2, 2, '2023/2024', 1, '2026-06-30 07:31:44'),
(2, 1, 2, 'ITST', 'Statistics', NULL, 3, 2, '2023/2024', 1, '2026-06-30 20:11:02'),
(3, 3, 3, 'IB', 'Introduction in Business', NULL, 3, 1, '2023/2024', 1, '2026-07-04 08:34:37');

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
(3, 'Business Administration', 'BA', '2026-06-29 09:31:08');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `course_id` int(10) UNSIGNED NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `enrolled_at`) VALUES
(3, 3, 1, '2026-06-30 20:08:25'),
(4, 3, 2, '2026-06-30 20:11:19'),
(5, 4, 3, '2026-07-04 08:37:11');

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
(3, 9, 3, 'STF-A790AD', NULL, '2026-07-04 08:32:36');

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
(19, 2, 2, 'week 12', '2026-06-30', '23:33:00', '23:30:00', 'b14068548f8326d05d6d12947d0b279fc6507b3440fdf82c57a17f7ae2999575', '2026-06-30 23:33:33', NULL, 'active', '2026-06-30 21:33:33'),
(20, 2, 2, 'week 19', '2026-07-03', '08:00:00', '10:00:00', 'd3ca49347e7c1b9bc47d1926e0abfe79741c7be52e47c5475a01afda1c8abf11', '2026-07-03 22:39:53', NULL, 'active', '2026-07-03 20:39:53'),
(21, 3, 3, 'week 4', '2026-07-04', '08:00:00', '10:00:00', '857226c8e61bfb00de257bb415c51156a1d48cdcdcc84e9dce557c3a7fe49cac', '2026-07-04 10:35:23', NULL, 'active', '2026-07-04 08:35:23'),
(22, 2, 2, 'Week 20', '2026-07-04', '08:00:00', '10:00:00', '726edf5da5d3d70a1028af6559899f8fa519dc786edd0c1d8fd2f45db837a62a', '2026-07-04 13:38:38', NULL, 'active', '2026-07-04 11:38:38');

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
(4, 8, 3, 'STU-4773DF6D', NULL, NULL, '2026-07-04 08:31:16');

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
(7, 'Austin Phiri', 'austin@gmai.com', '$2y$10$aO67zBB8axkjYm0JpkjktOTi4U9TKjpqBQkDt.KRdX9f5SjEEXi.S', 'student', 1, '2026-06-30 20:07:22', '2026-06-30 20:07:22'),
(8, 'Ruqaya Frank', 'ruqaya@gmail.com', '$2y$10$zzdOCa8fJuNc0Y84H7gYz.mSBxtRGjYEerXOYH3FV4qJwWcHGj6Z2', 'student', 1, '2026-07-04 08:31:16', '2026-07-04 16:17:46'),
(9, 'Barack Husen', 'barack@gmail.com', '$2y$10$DrW9vjznRrV/juK9ffH5TeBJxIi5ZzMsAOD/qbyJrUXnSjLb5VyWS', 'lecturer', 1, '2026-07-04 08:32:36', '2026-07-04 08:32:36');

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
  ADD KEY `idx_audit_action` (`action`);

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
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `staff_number` (`staff_number`),
  ADD KEY `fk_lecturer_dept` (`department_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `fk_reset_user` (`user_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- Constraints for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD CONSTRAINT `fk_lecturer_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lecturer_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
