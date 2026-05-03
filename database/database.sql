-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 03, 2026 at 06:49 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cafemenu`
--

-- --------------------------------------------------------

--
-- Table structure for table `cafes`
--

CREATE TABLE `cafes` (
  `id` bigint UNSIGNED NOT NULL,
  `code` char(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `person_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cafe_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slogan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UZS',
  `theme_style` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'theme1',
  `address_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_fee_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `extra_fee_type` enum('fixed','percent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_fee_value` decimal(10,2) DEFAULT NULL,
  `menu_updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive','demo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cafes`
--

INSERT INTO `cafes` (`id`, `code`, `username`, `phone`, `person_name`, `cafe_name`, `slogan`, `password_hash`, `logo_path`, `currency_name`, `theme_style`, `address_text`, `location_url`, `extra_fee_enabled`, `extra_fee_type`, `extra_fee_value`, `menu_updated_at`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'bestcafe', '+998901112233', 'Muslimbek', 'Best Cafe', '', '$2y$12$l4sW6d9F2VGJ5u0A0FsNeOsqCO1nLpHVfdt8TPrXh.6Ht/bQGvv2G', 'uploads/bestcafe/1775393416_fe35f5633cc531f6aaa2.jpg', 'USD', 'theme2', 'Navoi street 12, Urgench', 'https://maps.google.com/?q=41.55,60.63', 0, NULL, NULL, '2026-04-28 19:33:17', 'active', '2026-04-02 21:36:13', '2026-04-28 19:33:17'),
(2, NULL, 'daryo', '+998918500077', 'Rustam', 'Daryo by Bursa', 'Обслуживание / Xizmat  🛎️ 15%', '$2y$12$l4sW6d9F2VGJ5u0A0FsNeOsqCO1nLpHVfdt8TPrXh.6Ht/bQGvv2G', 'uploads/daryo/1776140379_8d3334629ed53d3686b9.png', 'UZS', 'theme1', '', '', 0, NULL, NULL, '2026-04-24 18:05:33', 'active', '2026-04-02 23:34:49', '2026-04-24 18:05:33'),
(3, '197435', 'demo', '+998946875461', 'Ahmed', 'Super cafe', '', '$2y$10$Kns4LkkpUu.19V4X.KOTv.GYIlFVtyed1gzdhvW0GuGbc5D8Yosg.', NULL, 'UZS', 'theme1', '', '', 0, 'percent', 10.00, '2026-05-03 00:17:27', 'active', '2026-04-29 23:12:52', '2026-05-03 00:17:27'),
(4, '313723', 'wewe', '+998946875461', 'wer', 'Super cafe', NULL, '$2y$10$iARbaKPvHadG6kLWoHQqbe8/Uh0EwUlMQ4Vk4TAv2XIlL/JcRVFMK', NULL, 'UZS', 'theme1', NULL, NULL, 0, NULL, NULL, '2026-05-02 23:22:20', 'demo', '2026-05-02 23:22:20', '2026-05-02 23:22:20'),
(5, '060430', 'wewe1', '+998946875461', 'asdasd adsa', 'Super cafe', NULL, '$2y$10$BF1WyjNRpgjMRDl.kU1rxOklCtfXqdBFua77AqnMr0F8m79xRR8Ny', NULL, 'UZS', 'theme1', NULL, NULL, 0, NULL, NULL, '2026-05-02 23:23:19', 'demo', '2026-05-02 23:23:19', '2026-05-02 23:23:19');

-- --------------------------------------------------------

--
-- Table structure for table `cafe_fee_translations`
--

CREATE TABLE `cafe_fee_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `cafe_id` bigint UNSIGNED NOT NULL,
  `language_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cafe_fee_translations`
--

INSERT INTO `cafe_fee_translations` (`id`, `cafe_id`, `language_code`, `label`, `created_at`, `updated_at`) VALUES
(20, 3, 'en', 'Service fee', '2026-05-01 20:26:14', '2026-05-01 20:26:14');

-- --------------------------------------------------------

--
-- Table structure for table `cafe_languages`
--

CREATE TABLE `cafe_languages` (
  `id` bigint UNSIGNED NOT NULL,
  `cafe_id` bigint UNSIGNED NOT NULL,
  `language_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cafe_languages`
--

INSERT INTO `cafe_languages` (`id`, `cafe_id`, `language_code`, `sort_order`, `created_at`, `updated_at`) VALUES
(2, 2, 'ru', 1, '2026-04-28 18:46:42', '2026-04-28 18:46:42'),
(14, 1, 'en', 1, '2026-04-28 19:32:37', '2026-04-28 19:32:37'),
(74, 3, 'en', 1, '2026-05-01 20:26:14', '2026-05-01 20:26:14'),
(75, 3, 'uz', 2, '2026-05-01 20:26:14', '2026-05-01 20:26:14'),
(76, 4, 'en', 1, '2026-05-02 23:22:20', '2026-05-02 23:22:20'),
(77, 5, 'en', 1, '2026-05-02 23:23:19', '2026-05-02 23:23:19');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `cafe_id` bigint UNSIGNED NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `icon_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `cafe_id`, `sort_order`, `is_active`, `icon_path`, `created_at`, `updated_at`) VALUES
(1, 3, 0, 1, 'uploads/demo/1777527588_01848e27ba173e7f792b.svg', '2026-04-29 23:22:02', '2026-04-30 10:39:48'),
(2, 3, 0, 1, NULL, '2026-04-29 23:22:59', '2026-04-29 23:22:59');

-- --------------------------------------------------------

--
-- Table structure for table `category_translations`
--

CREATE TABLE `category_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `language_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_translations`
--

INSERT INTO `category_translations` (`id`, `category_id`, `language_code`, `name`, `created_at`, `updated_at`) VALUES
(9, 2, 'en', 'Burgers', '2026-04-30 21:50:24', '2026-04-30 21:50:24'),
(10, 2, 'uz', 'Burgerlar', '2026-04-30 21:50:24', '2026-04-30 21:50:24'),
(14, 1, 'en', 'Drinks', '2026-04-30 23:15:00', '2026-04-30 23:15:00'),
(15, 1, 'uz', 'ichimlik', '2026-04-30 23:15:00', '2026-04-30 23:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint UNSIGNED NOT NULL,
  `cafe_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `cafe_id`, `category_id`, `price`, `image_path`, `is_available`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 12.00, 'uploads/bestcafe/1777385337_df3c008c2cbeb75da31c.png', 1, 0, '2026-04-28 19:08:57', '2026-04-28 19:08:57'),
(2, 3, NULL, 50000.00, 'uploads/demo/1777660929_a1a634acd5ce7150f63b.jpg', 1, 10, '2026-04-30 21:44:21', '2026-05-01 23:42:10'),
(3, 3, 1, 10000.00, 'uploads/demo/1777652184_c4a6723097a365fc34d9.jpg', 1, 0, '2026-04-30 21:45:34', '2026-05-01 21:16:30'),
(5, 3, NULL, 33.00, 'uploads/demo/1777664940_a7b54823f51d9a8f1605.jpg', 1, 0, '2026-05-02 00:41:42', '2026-05-02 00:49:00');

-- --------------------------------------------------------

--
-- Table structure for table `menu_item_translations`
--

CREATE TABLE `menu_item_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `menu_item_id` bigint UNSIGNED NOT NULL,
  `language_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_item_translations`
--

INSERT INTO `menu_item_translations` (`id`, `menu_item_id`, `language_code`, `name`, `description`, `created_at`, `updated_at`) VALUES
(5, 1, 'en', 'Osh', NULL, '2026-04-28 19:33:17', '2026-04-28 19:33:17'),
(16, 2, 'en', 'Plov', 'Amazing plov with rice', '2026-05-01 23:42:10', '2026-05-01 23:42:10'),
(23, 5, 'en', 'dsds', NULL, '2026-05-02 00:49:00', '2026-05-02 00:49:00'),
(33, 3, 'en', 'Coca cola', 'Black water', '2026-05-03 00:17:27', '2026-05-03 00:17:27'),
(34, 3, 'uz', 'Kola ichimligi', 'Qora suv', '2026-05-03 00:17:27', '2026-05-03 00:17:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cafes`
--
ALTER TABLE `cafes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `cafe_fee_translations`
--
ALTER TABLE `cafe_fee_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_cafe_fee_translation` (`cafe_id`,`language_code`),
  ADD KEY `idx_cafe_fee_translations_cafe_id` (`cafe_id`),
  ADD KEY `idx_cafe_fee_translations_language_code` (`language_code`);

--
-- Indexes for table `cafe_languages`
--
ALTER TABLE `cafe_languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_cafe_language` (`cafe_id`,`language_code`),
  ADD UNIQUE KEY `uniq_cafe_language_order` (`cafe_id`,`sort_order`),
  ADD KEY `idx_cafe_languages_cafe_id` (`cafe_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categories_cafe_id` (`cafe_id`),
  ADD KEY `idx_categories_cafe_sort` (`cafe_id`,`sort_order`);

--
-- Indexes for table `category_translations`
--
ALTER TABLE `category_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_category_translation` (`category_id`,`language_code`),
  ADD KEY `idx_category_translations_category_id` (`category_id`),
  ADD KEY `idx_category_translations_language_code` (`language_code`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_menu_items_cafe_id` (`cafe_id`),
  ADD KEY `idx_menu_items_category_id` (`category_id`),
  ADD KEY `idx_menu_items_cafe_sort` (`cafe_id`,`sort_order`);

--
-- Indexes for table `menu_item_translations`
--
ALTER TABLE `menu_item_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_menu_item_translation` (`menu_item_id`,`language_code`),
  ADD KEY `idx_menu_item_translations_menu_item_id` (`menu_item_id`),
  ADD KEY `idx_menu_item_translations_language_code` (`language_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cafes`
--
ALTER TABLE `cafes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cafe_fee_translations`
--
ALTER TABLE `cafe_fee_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `cafe_languages`
--
ALTER TABLE `cafe_languages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `category_translations`
--
ALTER TABLE `category_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menu_item_translations`
--
ALTER TABLE `menu_item_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cafe_fee_translations`
--
ALTER TABLE `cafe_fee_translations`
  ADD CONSTRAINT `fk_cafe_fee_translations_cafe` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cafe_languages`
--
ALTER TABLE `cafe_languages`
  ADD CONSTRAINT `fk_cafe_languages_cafe` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_cafe` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `category_translations`
--
ALTER TABLE `category_translations`
  ADD CONSTRAINT `fk_category_translations_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `fk_menu_items_cafe` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_menu_items_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_item_translations`
--
ALTER TABLE `menu_item_translations`
  ADD CONSTRAINT `fk_menu_item_translations_menu_item` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
