-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2025 at 07:01 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sukhniwas`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `email`, `full_name`, `created_at`, `last_login`, `is_active`) VALUES
(1, 'admin', '$2y$10$gVuT3AxIZhZTNguTi/Zsyeqa07Bt0AWVKoybiPiXza.kdlxyVrspC', 'admin@sukhniwas.com', 'Administrator', '2025-11-04 17:02:05', '2025-11-04 17:43:34', 1);

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `message` text DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `status` enum('new','contacted','confirmed','rejected') DEFAULT 'new',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `room_type` enum('pg','guest_house') DEFAULT 'guest_house',
  `description` text DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `pricing_type` enum('per_month','per_night') DEFAULT 'per_night',
  `max_occupancy` int(11) DEFAULT 2,
  `area_sqft` int(11) DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `is_visible` tinyint(1) DEFAULT 1,
  `featured_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `title`, `slug`, `room_type`, `description`, `price_per_night`, `pricing_type`, `max_occupancy`, `area_sqft`, `amenities`, `is_available`, `is_visible`, `featured_image`, `created_at`, `updated_at`) VALUES
(1, 'PG - 6 Sharing (AC)', 'pg-6-sharing-ac', 'pg', 'Comfortable 6 sharing PG room with AC. Spacious 450 sq ft area perfect for students and working professionals.', 8000.00, 'per_month', 6, 450, 'WiFi,AC,Fan,Attached Bathroom,Hot Water,24/7 Security,Power Backup,Study Table', 1, 1, NULL, '2025-11-05 04:32:16', '2025-11-05 04:32:16'),
(2, 'PG - 4 Sharing (AC)', 'pg-4-sharing-ac', 'pg', 'Well-maintained 4 sharing PG room with AC. 310-350 sq ft space with modern amenities.', 9000.00, 'per_month', 4, 350, 'WiFi,AC,Fan,Attached Bathroom,Hot Water,24/7 Security,Power Backup,Study Table', 1, 1, NULL, '2025-11-05 04:32:16', '2025-11-05 04:32:16'),
(3, 'PG - 3 Sharing (AC + TV)', 'pg-3-sharing-ac-tv', 'pg', 'Premium 3 sharing PG room with AC and in-room TV. 300 sq ft space for comfortable living.', 10000.00, 'per_month', 3, 300, 'WiFi,AC,Fan,TV,Attached Bathroom,Hot Water,24/7 Security,Power Backup,Study Table', 1, 1, NULL, '2025-11-05 04:32:16', '2025-11-05 04:32:16'),
(4, 'PG - 2 Sharing (AC + Refrigerator/TV)', 'pg-2-sharing-ac-refrigerator-tv', 'pg', 'Luxury 2 sharing PG room with AC, in-room Refrigerator and TV. 275 sq ft premium accommodation.', 11000.00, 'per_month', 2, 275, 'WiFi,AC,Fan,TV,Refrigerator,Attached Bathroom,Hot Water,24/7 Security,Power Backup,Study Table', 1, 1, NULL, '2025-11-05 04:32:16', '2025-11-05 04:32:16');

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `media_type` enum('image','video') DEFAULT 'image',
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `image_order` int(11) DEFAULT 0,
  `alt_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'Sukh Niwas PG &amp; Guest House', '2025-11-05 05:37:15'),
(2, 'site_email', 'olivspace1@gmail.com', '2025-11-05 04:50:53'),
(3, 'site_phone', '+91-9711 813 813', '2025-11-05 04:50:53'),
(4, 'whatsapp_number', '919711 813 813', '2025-11-05 04:50:53'),
(5, 'whatsapp_message', 'Hi, I would like to know more about your rooms.', '2025-11-04 17:02:05'),
(6, 'address', 'House no 1741, Sector 55, Faridabad', '2025-11-05 04:50:53'),
(7, 'facebook_url', '', '2025-11-04 17:02:05'),
(8, 'instagram_url', '', '2025-11-04 17:02:05'),
(9, 'google_map_embed', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3511.4452296259915!2d77.302082!3d28.345385999999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjjCsDIwJzQzLjQiTiA3N8KwMTgnMDcuNSJF!5e0!3m2!1sen!2sin!4v1762318223663!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '2025-11-05 04:50:53'),
(10, 'seo_title', 'Sukh Niwas PG &amp; Guest House - Comfortable Stay in City', '2025-11-05 05:37:15'),
(11, 'seo_description', 'Book your stay at Sukhniwas Guest House. Affordable rooms with modern amenities. Contact us for bookings.', '2025-11-04 17:02:05'),
(12, 'seo_keywords', 'guest house, PG, affordable rooms, stay, accommodation', '2025-11-04 17:02:05'),
(16, 'site_phone_2', '', '2025-11-05 04:50:53'),
(26, 'food_available', 'yes', '2025-11-05 04:50:53'),
(27, 'food_type', 'vegetarian', '2025-11-05 04:50:53'),
(28, 'food_description', 'Breakfast Parantha/Curd AlloPoori Maggi/Pasta\r\n Lunch Rice Meals\r\n Dinner Vegetable, Dal, Roti, Salad meal', '2025-11-05 05:37:15'),
(29, 'food_timings', 'Breakfast', '2025-11-05 04:50:53'),
(30, 'food_pricing', '', '2025-11-05 04:50:53'),
(31, 'additional_services', '', '2025-11-05 04:50:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_visible` (`is_visible`,`is_available`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_room_type` (`room_type`,`is_visible`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_room_order` (`room_id`,`image_order`),
  ADD KEY `idx_media_type` (`media_type`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD CONSTRAINT `enquiries_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
