-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 13, 2025 at 02:59 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gym_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `trainer_id` int DEFAULT NULL,
  `class_name` varchar(100) DEFAULT NULL,
  `class_day` varchar(50) NOT NULL,
  `class_time` varchar(50) NOT NULL,
  `instructor` varchar(100) NOT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_time` time DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `status` enum('confirmed','pending','cancelled','completed') DEFAULT 'confirmed',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `trainer_id`, `class_name`, `class_day`, `class_time`, `instructor`, `booking_date`, `booking_time`, `duration`, `status`, `notes`, `created_at`) VALUES
(1, 8, NULL, 'HIIT Training', 'Monday', '6:00 AM', 'Sarah', NULL, NULL, NULL, 'confirmed', NULL, '2025-10-13 03:38:03'),
(2, 8, NULL, 'Yoga', 'Monday', '5:30 PM', 'Michael', NULL, NULL, NULL, 'confirmed', NULL, '2025-10-13 03:39:18'),
(3, 8, NULL, 'Cardio Blast', 'Wednesday', '6:30 AM', 'Sarah', NULL, NULL, NULL, 'confirmed', NULL, '2025-10-13 03:41:45'),
(4, 8, NULL, 'Pilates', 'Wednesday', '5:00 PM', 'Emma', NULL, NULL, NULL, 'confirmed', NULL, '2025-10-13 03:42:28'),
(5, 8, NULL, 'HIIT Training', 'Friday', '6:00 AM', 'Sarah', NULL, NULL, NULL, 'confirmed', NULL, '2025-10-13 03:44:11'),
(6, 8, NULL, 'Boxing', 'Friday', '5:00 PM', 'John', NULL, NULL, NULL, 'confirmed', NULL, '2025-10-13 03:45:11'),
(7, 8, NULL, 'Strength Training', 'Tuesday', '7:00 AM', 'David', NULL, NULL, NULL, 'confirmed', NULL, '2025-10-13 03:46:54');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `trainer_id` int DEFAULT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `max_capacity` int DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `class_date` date NOT NULL,
  `class_time` time NOT NULL,
  `duration` int NOT NULL,
  `class_type` varchar(50) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_classes_trainer` (`trainer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `description`, `trainer_id`, `day_of_week`, `start_time`, `end_time`, `max_capacity`, `status`, `created_at`, `updated_at`, `class_date`, `class_time`, `duration`, `class_type`, `class_name`) VALUES
(1, '', 'sssss', 4, NULL, NULL, NULL, 20, 'active', '2025-10-13 01:53:30', '2025-10-13 01:53:30', '2025-10-13', '12:46:00', 60, 'Yoga', 'hiit'),
(2, '', 'intense', 4, NULL, NULL, NULL, 20, 'active', '2025-10-13 01:53:49', '2025-10-13 01:53:49', '2025-10-13', '12:54:00', 60, 'HIIT', 'HIIT'),
(3, '', 'a', 4, NULL, NULL, NULL, 20, 'active', '2025-10-13 06:33:38', '2025-10-13 06:33:38', '2025-10-13', '08:36:00', 60, 'Yoga', 'HIIT'),
(5, '', 'ssss', 4, NULL, NULL, NULL, 20, 'active', '2025-10-13 06:57:27', '2025-10-13 06:57:27', '2025-10-13', '17:00:00', 60, 'Yoga', 'HIIT'),
(6, '', 'sss', 4, NULL, NULL, NULL, 20, 'active', '2025-10-13 07:06:49', '2025-10-13 07:06:49', '2025-10-13', '18:07:00', 60, 'Yoga', 'HIIT');

-- --------------------------------------------------------

--
-- Table structure for table `class_bookings`
--

DROP TABLE IF EXISTS `class_bookings`;
CREATE TABLE IF NOT EXISTS `class_bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `class_day` varchar(20) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `class_time` varchar(20) NOT NULL,
  `instructor` varchar(50) NOT NULL,
  `booked_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`class_day`,`class_name`,`class_time`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `class_bookings`
--

INSERT INTO `class_bookings` (`id`, `user_id`, `class_day`, `class_name`, `class_time`, `instructor`, `booked_at`) VALUES
(11, 24, 'Monday', 'HIIT Training', '6:00 AM', 'Sarah', '2025-10-13 07:25:31'),
(9, 13, 'monday', 'HIIT', '7:00', 'sarah', '2025-10-13 05:50:57'),
(13, 24, 'Saturday', 'Full Body Workout', '8:00 AM', 'David', '2025-10-13 07:37:07'),
(14, 24, 'Saturday', 'Yoga', '10:00 AM', 'Emma', '2025-10-13 07:37:13');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `status`) VALUES
(1, 'Mohan', 'mohan@yahoo.com', 'new member', 'I want to join the GYM.', '2025-10-03 09:45:25', 'pending'),
(2, 'Amish Gurung', 'amish@gmail.com', 'test', 'hello!!!', '2025-10-04 04:15:22', 'read'),
(3, 'Amish Gurung', 'amish@gmail.com', 'test', 'testng testing', '2025-10-08 00:53:45', 'read'),
(4, 'Amish Gurung', 'amish@gmail.com', 'test', 'vvvv', '2025-10-11 00:30:50', 'pending'),
(5, 'Amish Gurung', 'amish@gmail.com', 'test', 'sss', '2025-10-11 00:32:09', 'pending'),
(6, 'amish gurung', 'amish_gurung@yahoo.com', 'aa', 'aaaa', '2025-10-13 06:24:57', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `food_logs`
--

DROP TABLE IF EXISTS `food_logs`;
CREATE TABLE IF NOT EXISTS `food_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `calories` int NOT NULL,
  `protein` int NOT NULL,
  `carbs` int NOT NULL,
  `fat` int NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meal_plans`
--

DROP TABLE IF EXISTS `meal_plans`;
CREATE TABLE IF NOT EXISTS `meal_plans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `meal_name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `calories` int NOT NULL,
  `meal_time` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE IF NOT EXISTS `members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `membership_plan` enum('basic','standard','premium','family','student') DEFAULT NULL,
  `fitness_goals` text,
  `password_hash` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membership_plans`
--

DROP TABLE IF EXISTS `membership_plans`;
CREATE TABLE IF NOT EXISTS `membership_plans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(8,2) NOT NULL,
  `duration_days` int NOT NULL,
  `features` text,
  `max_classes_per_week` int DEFAULT NULL,
  `personal_training_sessions` int DEFAULT '0',
  `gym_access` tinyint(1) DEFAULT '1',
  `group_classes` tinyint(1) DEFAULT '0',
  `pool_access` tinyint(1) DEFAULT '0',
  `sauna_access` tinyint(1) DEFAULT '0',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

DROP TABLE IF EXISTS `trainers`;
CREATE TABLE IF NOT EXISTS `trainers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `specialty` varchar(100) NOT NULL,
  `experience` int NOT NULL,
  `schedule` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`id`, `fullname`, `email`, `phone`, `specialty`, `experience`, `schedule`, `password`, `role`) VALUES
(1, 'Mohan Gaha', 'abc@yahoo.com', '+61410885345', 'Strength', 5, 'Sunday', '$2y$10$wmAo/QW3aJ9jLjhHzpaEfOwzSFnBf.I8IvH0tWwLrz9w3emR4vEYW', 'trainer'),
(2, 'amish gurung', 'amish@yahoo.com', '0416434850', 'strength', 2, 'monday, Wednesday', '$2y$10$zRWOz6VTGdYC2pNkPD6IceIQX5gkzBzpsb9bNs9vrOZIwhFfXNwZq', 'trainer'),
(3, 'amish', 'amish@gmail.com', '0416434850', 'strength', 5, 'monday, Wednesday', '$2y$10$6YeMYXGcnW.NpfTvAVxAUuK.Ib/.M2cGzqDMwVZVV0TQOAi91e31.', 'trainer'),
(4, 'amishg', 'amishgurung@hotmail.com', '041643485', 'strength', 10, 'monday, Wednesday', '$2y$10$MQKFw3Cajacbx.Wnqx3H0Osk6OyBW6MUxrT6NlgUjBdjyBq747Tv6', 'trainer'),
(15, '', '', '', 'Certified Nutrition and Fitness Coach', 3, 'Monday to Wednesday- 9-3', '', ''),
(16, 'a', 'xyz@gmail.com', '04165852563', 'nutrition', 10, 'monday, Wednesday', '$2y$10$G8oUAl/7ZcNftXcDVXYW7eBPzpECxQE0.MUC1IH0U4EGe/dDGMGyu', 'trainer'),
(17, 'y', 'y@yahoo.com', '1023456899', 't', 20, 'monday, Wednesday', '$2y$10$vYqIxD7GBpul2Uh0RpXD4eP7l2cJVKisZ0Tu1TMTBAK93S8q1K0rm', 'trainer'),
(18, 'y', 'y@hotmail.com', '12345678956', 'aaa', 2, 'friday', '$2y$10$bEgNROS19POop/XJHuWPzeeJlqRhQoPL1Hqrk/rX0ld.mJ4y.8RHG', 'trainer');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_clients`
--

DROP TABLE IF EXISTS `trainer_clients`;
CREATE TABLE IF NOT EXISTS `trainer_clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `trainer_id` int NOT NULL,
  `client_id` int NOT NULL,
  `join_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `plan` varchar(100) NOT NULL,
  `goals` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(50) DEFAULT 'member',
  `trainer_id` int DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `bio` text,
  `experience` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `plan`, `goals`, `password`, `created_at`, `role`, `trainer_id`, `specialization`, `bio`, `experience`) VALUES
(1, 'Mohan Gaha', 'mohan@yahoo.com', '+61410885345', 'basic', 'I want to gain muscle.', '$2y$10$sBYaMjk6EUkRB7EiLo.u/u8udnZt1CdHdC6ZnQ2bpY3EUp1o1jqj.', '2025-10-03 08:32:54', 'member', NULL, NULL, NULL, NULL),
(19, 'amish gurung', 'amish@hotmail.com', '0416434850', 'elite', 'aaaa', '$2y$10$HJQHSRUJHDTiTzQmiul/0.OlhuWE6XB/97yk8GaNOg3eW0PfTDhDu', '2025-10-13 06:17:08', 'member', NULL, NULL, NULL, NULL),
(20, 'mohan gaha', 'mohan@gmail.com', '1235646966', 'premium', 'mmm', '$2y$10$01lrbdj9E0CdS04M/ymYzuhyfSciBWH1UXdKn96W42Hn1wWkXRCPq', '2025-10-13 06:20:42', 'member', NULL, NULL, NULL, NULL),
(21, 'a', 'a@yahoo.com', '0412522366', 'elite', 'aa', '$2y$10$dnfRawYhJuvll4oHu8s.JeWiUQjklZQf0gvLIROASfwMyrXkThONm', '2025-10-13 06:21:09', 'member', NULL, NULL, NULL, NULL),
(11, 'Administrator', 'admin@strengthhousegym.com', NULL, '', '', '$2y$10$Bnu/cSgyjnzR5Y124Em7G.BiKXDTD9Ky7HzOX39fj7qgBU3rmLq7W', '2025-10-04 04:08:47', 'admin', NULL, NULL, NULL, NULL),
(17, 'Mike Johnson', 'mikejohnson@yahoo.com', '0412333444', '', '', '$2y$10$CgvEZJNxzZqbI6v6Pa/U7eHlaiqS7ItgGOq6FaTwBmsfI8rOE4u4q', '2025-10-10 01:51:06', 'trainer', NULL, NULL, NULL, NULL),
(16, 'John SMith', 'johnsmith@yahoo.com', '0411222333', '', '', '$2y$10$QbHCMO8uIZV.9UBi.Ml0.uHN1nHUG7gn4KHzyJjaqQXan62xGx6yu', '2025-10-10 01:49:58', 'trainer', NULL, NULL, NULL, NULL),
(23, 'am', 'am@yahoo.com', '1022555555', '', '', '$2y$10$PPPpWttB73x6cu/S8rnk9.QO8xFHn/5JdBonvibWPleRjnXR.nYQi', '2025-10-13 06:23:07', 'trainer', NULL, NULL, NULL, NULL),
(24, 'a', 'a@hi5.com', '14566698556', 'standard', 'aaa', '$2y$10$CB5HGOZ98/gE/U2CUU1Ok.iKG4C5gvnOj16dWIgGAcUpxDSxg38Dm', '2025-10-13 06:27:49', 'member', NULL, NULL, NULL, NULL),
(25, 'jane', 'jane@yahoo.com', '1234567890', 'premium', 'i want become big', '$2y$10$mBh56gJz1S2E1V5LzoNwsOLiSZJcAF7jLbekdaGMDs5HCjaQ6TISm', '2025-10-13 08:44:21', 'member', NULL, NULL, NULL, NULL),
(14, 'aaaa', 'amishgurung@hotmail.com', '+6141643485', 'basic', 'aaa', '$2y$10$vaJZqFW5684keMmatLEPeuppXCcB9pPmbavdwP1/utqJhRhHpq9BK', '2025-10-08 02:05:26', 'member', NULL, NULL, NULL, NULL),
(15, 'Sarah Lee', 'sarahlee@yahoo.com', '0412111222', '', '', '$2y$10$AiUrbM3UAOiDInVeuljOeOB.tmfKfeV2pW/X4kbW/7bzNp0MaLhZW', '2025-10-10 01:48:54', 'trainer', NULL, NULL, NULL, NULL),
(18, 'John Trainer', 'trainer@strengthhouse.com', '041624851', '', '', 'trainer123', '2025-10-10 03:43:10', 'trainer', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

DROP TABLE IF EXISTS `user_progress`;
CREATE TABLE IF NOT EXISTS `user_progress` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `month` varchar(20) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `bodyfat` decimal(5,2) NOT NULL,
  `muscle` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_progress`
--

INSERT INTO `user_progress` (`id`, `user_id`, `month`, `weight`, `bodyfat`, `muscle`, `created_at`) VALUES
(1, 24, 'january', 75.00, 20.00, 56.00, '2025-10-13 08:00:35'),
(2, 24, 'february', 70.00, 15.00, 50.00, '2025-10-13 08:00:54');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
