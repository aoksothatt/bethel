-- =========================================================
-- BETHEL HIGH SCHOOL — TEAM JERSEY TRACKER DATABASE DUMP
-- Compatible with MySQL 8.0, MariaDB, TiDB Cloud Serverless
-- Total Players: 28 | Admin Users: 1
-- Generated: 2026-11-05 04:41:28
-- =========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ---------------------------------------------------------
-- Table structure for table `users`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Team Administrator', 'admin@teamjersey.local', '$2y$12$8d7ayE2LyZbpoVmNUO29tunVQJnCjQQ17IdQJJRNSOJlO3zrlrLCi', NOW(), NOW());

-- ---------------------------------------------------------
-- Table structure for table `personal_access_tokens`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table structure for table `players`
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shirt_number` int NOT NULL,
  `jersey_size` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` enum('paid','unpaid','not_confirmed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `players_payment_status_index` (`payment_status`),
  KEY `players_shirt_number_index` (`shirt_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `players` (`id`, `name`, `shirt_number`, `jersey_size`, `payment_status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'CHHAY', 8, 'L', 'paid', NULL, NOW(), NOW()),
(2, 'HOUR', 3, 'L', 'unpaid', NULL, NOW(), NOW()),
(3, 'JUMING', 7, 'L', 'unpaid', NULL, NOW(), NOW()),
(4, 'MESSING', 10, 'L', 'unpaid', NULL, NOW(), NOW()),
(5, 'SUII NANN', 1, 'L', 'unpaid', 'GK', NOW(), NOW()),
(6, 'Sak', 8, 'L', 'unpaid', NULL, NOW(), NOW()),
(7, 'THEARA', 6, 'L', 'paid', NULL, NOW(), NOW()),
(8, 'Xorng', 9, 'M', 'unpaid', NULL, NOW(), NOW()),
(9, 'PHA LEY', 67, 'M', 'unpaid', NULL, NOW(), NOW()),
(10, 'Pedri', 21, 'XL', 'paid', NULL, NOW(), NOW()),
(11, 'NITH', 19, 'M', 'unpaid', NULL, NOW(), NOW()),
(12, 'Long', 17, 'L', 'unpaid', NULL, NOW(), NOW()),
(13, 'Panha', 27, 'M', 'unpaid', 'អាវគេចូលរួម', NOW(), NOW()),
(14, 'PrimeHeng', 15, 'L', 'unpaid', NULL, NOW(), NOW()),
(15, 'Sothatt', 80, 'L', 'unpaid', NULL, NOW(), NOW()),
(16, 'SOKKUN', 18, 'XL', 'paid', NULL, NOW(), NOW()),
(17, 'Yimchamroeun', 19, 'M', 'unpaid', 'អាវគេចូលរួម', NOW(), NOW()),
(18, 'PiNy', 10, 'L', 'unpaid', 'អាវគេចូលរួម', NOW(), NOW()),
(19, 'SEY', 19, 'L', 'unpaid', 'អាវគេចូលរួម', NOW(), NOW()),
(20, 'CHONG', 11, 'L', 'unpaid', 'អាវគេចូលរួម', NOW(), NOW()),
(21, 'BANOO', 29, 'L', 'unpaid', 'អាវគេចូលរួម', NOW(), NOW()),
(22, 'HENG', 12, 'L', 'unpaid', NULL, NOW(), NOW()),
(23, 'JING ASSASIN', 10, 'XL', 'unpaid', 'អាវគេចូលរួម', NOW(), NOW()),
(24, 'YTT', 19, 'M', 'unpaid', 'អាវគេចូលរួម', NOW(), NOW()),
(25, 'HOR', 0, 'M', 'unpaid', 'អាវគេចូលរួម', NOW(), NOW()),
(26, 'Meng Kruy', 23, 'L', 'unpaid', NULL, NOW(), NOW()),
(27, 'Srey Nith', 19, 'M', 'unpaid', 'អាវគេចូលរួម', NOW(), NOW()),
(28, 'PHOUNG', 23, 'M', 'not_confirmed', NULL, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
