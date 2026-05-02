-- Cafe Menu SaaS schema
-- Source of truth for manual SQL-based database setup.

CREATE TABLE `cafes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `extra_fee_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `extra_fee_type` enum('fixed','percent') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_fee_value` decimal(10,2) DEFAULT NULL,
  `menu_updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive','demo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cafe_id` bigint UNSIGNED NOT NULL,
  `sort_order` int NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `icon_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_categories_cafe_id` (`cafe_id`),
  KEY `idx_categories_cafe_sort` (`cafe_id`, `sort_order`),
  CONSTRAINT `fk_categories_cafe` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `menu_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cafe_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_menu_items_cafe_id` (`cafe_id`),
  KEY `idx_menu_items_category_id` (`category_id`),
  KEY `idx_menu_items_cafe_sort` (`cafe_id`, `sort_order`),
  CONSTRAINT `fk_menu_items_cafe` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_menu_items_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cafe_languages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cafe_id` bigint UNSIGNED NOT NULL,
  `language_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` tinyint UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cafe_language` (`cafe_id`, `language_code`),
  UNIQUE KEY `uniq_cafe_language_order` (`cafe_id`, `sort_order`),
  KEY `idx_cafe_languages_cafe_id` (`cafe_id`),
  KEY `idx_cafe_languages_language_code` (`language_code`),
  CONSTRAINT `fk_cafe_languages_cafe` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `category_translations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint UNSIGNED NOT NULL,
  `language_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_category_translation` (`category_id`, `language_code`),
  KEY `idx_category_translations_category_id` (`category_id`),
  KEY `idx_category_translations_language_code` (`language_code`),
  CONSTRAINT `fk_category_translations_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `menu_item_translations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_item_id` bigint UNSIGNED NOT NULL,
  `language_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_menu_item_translation` (`menu_item_id`, `language_code`),
  KEY `idx_menu_item_translations_menu_item_id` (`menu_item_id`),
  KEY `idx_menu_item_translations_language_code` (`language_code`),
  CONSTRAINT `fk_menu_item_translations_menu_item` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cafe_fee_translations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cafe_id` bigint UNSIGNED NOT NULL,
  `language_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cafe_fee_translation` (`cafe_id`, `language_code`),
  KEY `idx_cafe_fee_translations_cafe_id` (`cafe_id`),
  KEY `idx_cafe_fee_translations_language_code` (`language_code`),
  CONSTRAINT `fk_cafe_fee_translations_cafe` FOREIGN KEY (`cafe_id`) REFERENCES `cafes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
