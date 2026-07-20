-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for spx_attendance
CREATE DATABASE IF NOT EXISTS `spx_attendance` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `spx_attendance`;

-- Dumping structure for table spx_attendance.attendances
CREATE TABLE IF NOT EXISTS `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned DEFAULT NULL,
  `rider_id` bigint unsigned NOT NULL,
  `spx_account_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','rest_day','half_day') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_rider_id_date_unique` (`rider_id`,`date`),
  KEY `attendances_spx_account_id_foreign` (`spx_account_id`),
  KEY `attendances_admin_id_foreign` (`admin_id`),
  CONSTRAINT `attendances_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_rider_id_foreign` FOREIGN KEY (`rider_id`) REFERENCES `riders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_spx_account_id_foreign` FOREIGN KEY (`spx_account_id`) REFERENCES `spx_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.attendances: ~62 rows (approximately)
REPLACE INTO `attendances` (`id`, `admin_id`, `rider_id`, `spx_account_id`, `date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
	(1, 1, 3, 3, '2026-06-29', 'present', NULL, '2026-07-09 16:57:27', '2026-07-09 16:57:27'),
	(2, 1, 4, 3, '2026-06-29', 'present', NULL, '2026-07-09 16:57:27', '2026-07-09 16:57:27'),
	(3, 1, 6, NULL, '2026-06-29', 'rest_day', NULL, '2026-07-09 16:57:27', '2026-07-09 16:57:27'),
	(4, 1, 5, NULL, '2026-06-29', 'rest_day', NULL, '2026-07-09 16:57:27', '2026-07-09 16:57:27'),
	(5, 1, 3, 3, '2026-06-30', 'present', NULL, '2026-07-09 16:57:57', '2026-07-09 16:57:57'),
	(6, 1, 4, 3, '2026-06-30', 'present', NULL, '2026-07-09 16:57:57', '2026-07-09 16:57:57'),
	(7, 1, 6, NULL, '2026-06-30', 'rest_day', NULL, '2026-07-09 16:57:57', '2026-07-09 16:57:57'),
	(8, 1, 5, NULL, '2026-06-30', 'rest_day', NULL, '2026-07-09 16:57:57', '2026-07-09 16:57:57'),
	(9, 1, 3, 3, '2026-07-01', 'present', NULL, '2026-07-09 16:58:06', '2026-07-09 16:58:06'),
	(10, 1, 4, NULL, '2026-07-01', 'rest_day', NULL, '2026-07-09 16:58:07', '2026-07-09 16:58:07'),
	(11, 1, 6, NULL, '2026-07-01', 'rest_day', NULL, '2026-07-09 16:58:07', '2026-07-09 16:58:07'),
	(12, 1, 5, NULL, '2026-07-01', 'rest_day', NULL, '2026-07-09 16:58:07', '2026-07-09 16:58:07'),
	(13, 1, 3, 4, '2026-07-04', 'present', NULL, '2026-07-09 16:58:16', '2026-07-09 16:58:16'),
	(14, 1, 4, 4, '2026-07-04', 'present', NULL, '2026-07-09 16:58:16', '2026-07-09 16:58:16'),
	(15, 1, 6, NULL, '2026-07-04', 'rest_day', NULL, '2026-07-09 16:58:16', '2026-07-09 16:58:16'),
	(16, 1, 5, NULL, '2026-07-04', 'rest_day', NULL, '2026-07-09 16:58:16', '2026-07-09 16:58:16'),
	(17, 1, 3, 4, '2026-07-06', 'present', NULL, '2026-07-12 18:17:30', '2026-07-12 18:17:30'),
	(18, 1, 4, 4, '2026-07-06', 'present', NULL, '2026-07-12 18:17:31', '2026-07-12 18:17:31'),
	(19, 1, 6, NULL, '2026-07-06', 'rest_day', NULL, '2026-07-12 18:17:31', '2026-07-12 18:17:31'),
	(20, 1, 5, NULL, '2026-07-06', 'rest_day', NULL, '2026-07-12 18:17:31', '2026-07-12 18:17:31'),
	(21, 1, 3, 4, '2026-07-07', 'present', NULL, '2026-07-12 18:17:40', '2026-07-12 18:17:40'),
	(22, 1, 4, NULL, '2026-07-07', 'rest_day', NULL, '2026-07-12 18:17:40', '2026-07-12 18:17:40'),
	(23, 1, 6, NULL, '2026-07-07', 'rest_day', NULL, '2026-07-12 18:17:40', '2026-07-12 18:17:40'),
	(24, 1, 5, NULL, '2026-07-07', 'rest_day', NULL, '2026-07-12 18:17:40', '2026-07-12 18:17:40'),
	(25, 1, 3, 4, '2026-07-08', 'present', NULL, '2026-07-12 18:17:49', '2026-07-12 18:17:49'),
	(26, 1, 4, 4, '2026-07-08', 'present', NULL, '2026-07-12 18:17:49', '2026-07-12 18:17:49'),
	(27, 1, 6, NULL, '2026-07-08', 'rest_day', NULL, '2026-07-12 18:17:49', '2026-07-12 18:17:49'),
	(28, 1, 5, NULL, '2026-07-08', 'rest_day', NULL, '2026-07-12 18:17:49', '2026-07-12 18:17:49'),
	(29, 1, 3, 4, '2026-07-09', 'present', NULL, '2026-07-12 18:18:06', '2026-07-12 18:18:06'),
	(30, 1, 4, 4, '2026-07-09', 'present', NULL, '2026-07-12 18:18:06', '2026-07-12 18:18:06'),
	(31, 1, 6, NULL, '2026-07-09', 'rest_day', NULL, '2026-07-12 18:18:06', '2026-07-12 18:18:06'),
	(32, 1, 5, NULL, '2026-07-09', 'rest_day', NULL, '2026-07-12 18:18:06', '2026-07-12 18:18:06'),
	(33, 1, 3, 4, '2026-07-10', 'present', NULL, '2026-07-12 18:18:14', '2026-07-12 18:18:14'),
	(34, 1, 4, 4, '2026-07-10', 'present', NULL, '2026-07-12 18:18:14', '2026-07-12 18:18:14'),
	(35, 1, 6, NULL, '2026-07-10', 'rest_day', NULL, '2026-07-12 18:18:14', '2026-07-12 18:18:14'),
	(36, 1, 5, NULL, '2026-07-10', 'rest_day', NULL, '2026-07-12 18:18:14', '2026-07-12 18:18:14'),
	(37, 1, 3, 3, '2026-07-13', 'present', NULL, '2026-07-12 21:41:32', '2026-07-12 21:41:32'),
	(38, 1, 4, NULL, '2026-07-13', 'rest_day', NULL, '2026-07-12 21:41:32', '2026-07-12 21:41:32'),
	(39, 1, 6, NULL, '2026-07-13', 'rest_day', NULL, '2026-07-12 21:41:32', '2026-07-12 21:41:32'),
	(40, 1, 8, 3, '2026-07-13', 'present', NULL, '2026-07-12 21:41:32', '2026-07-12 21:41:32'),
	(41, 1, 5, NULL, '2026-07-13', 'rest_day', NULL, '2026-07-12 21:41:32', '2026-07-12 21:41:32'),
	(42, 1, 3, 3, '2026-07-14', 'present', NULL, '2026-07-13 16:47:08', '2026-07-13 16:47:08'),
	(43, 1, 4, NULL, '2026-07-14', 'rest_day', NULL, '2026-07-13 16:47:08', '2026-07-13 16:47:08'),
	(44, 1, 6, NULL, '2026-07-14', 'rest_day', NULL, '2026-07-13 16:47:08', '2026-07-13 16:47:08'),
	(45, 1, 8, NULL, '2026-07-14', 'rest_day', NULL, '2026-07-13 16:47:08', '2026-07-13 16:47:08'),
	(46, 1, 5, NULL, '2026-07-14', 'rest_day', NULL, '2026-07-13 16:47:08', '2026-07-13 16:47:08'),
	(47, 1, 3, NULL, '2026-07-15', 'rest_day', 'no parcel', '2026-07-14 17:38:10', '2026-07-14 17:38:10'),
	(48, 1, 4, NULL, '2026-07-15', 'rest_day', 'no parcel', '2026-07-14 17:38:10', '2026-07-14 17:38:10'),
	(49, 1, 6, NULL, '2026-07-15', 'rest_day', 'no parcel', '2026-07-14 17:38:10', '2026-07-14 17:38:10'),
	(50, 1, 8, NULL, '2026-07-15', 'rest_day', 'no parcel', '2026-07-14 17:38:10', '2026-07-14 17:38:10'),
	(51, 1, 5, NULL, '2026-07-15', 'rest_day', 'no parcel', '2026-07-14 17:38:10', '2026-07-14 17:38:10'),
	(52, 1, 3, 4, '2026-07-17', 'present', NULL, '2026-07-16 16:41:03', '2026-07-16 16:41:03'),
	(53, 1, 4, NULL, '2026-07-17', 'rest_day', NULL, '2026-07-16 16:41:03', '2026-07-16 16:41:03'),
	(54, 1, 6, NULL, '2026-07-17', 'rest_day', NULL, '2026-07-16 16:41:03', '2026-07-16 16:41:03'),
	(55, 1, 8, NULL, '2026-07-17', 'rest_day', NULL, '2026-07-16 16:41:03', '2026-07-16 16:41:03'),
	(56, 1, 5, NULL, '2026-07-17', 'rest_day', NULL, '2026-07-16 16:41:03', '2026-07-16 16:41:03'),
	(57, 1, 3, 3, '2026-07-16', 'present', NULL, '2026-07-16 16:41:44', '2026-07-16 16:41:44'),
	(58, 1, 4, 3, '2026-07-16', 'present', NULL, '2026-07-16 16:41:44', '2026-07-16 16:41:44'),
	(59, 1, 6, NULL, '2026-07-16', 'rest_day', NULL, '2026-07-16 16:41:44', '2026-07-16 16:41:44'),
	(60, 1, 8, NULL, '2026-07-16', 'rest_day', NULL, '2026-07-16 16:41:44', '2026-07-16 16:41:44'),
	(61, 1, 5, NULL, '2026-07-16', 'rest_day', NULL, '2026-07-16 16:41:44', '2026-07-16 16:41:44'),
	(62, 1, 3, 3, '2026-07-18', 'present', NULL, '2026-07-19 16:10:01', '2026-07-19 16:10:01'),
	(63, 1, 4, NULL, '2026-07-18', 'rest_day', NULL, '2026-07-19 16:10:01', '2026-07-19 16:10:01'),
	(64, 1, 6, NULL, '2026-07-18', 'rest_day', NULL, '2026-07-19 16:10:01', '2026-07-19 16:10:01'),
	(65, 1, 8, 3, '2026-07-18', 'present', NULL, '2026-07-19 16:10:01', '2026-07-19 16:10:01'),
	(66, 1, 5, NULL, '2026-07-18', 'rest_day', NULL, '2026-07-19 16:10:01', '2026-07-19 16:10:01');

-- Dumping structure for table spx_attendance.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.cache: ~0 rows (approximately)

-- Dumping structure for table spx_attendance.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.cache_locks: ~0 rows (approximately)

-- Dumping structure for table spx_attendance.cash_advances
CREATE TABLE IF NOT EXISTS `cash_advances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned DEFAULT NULL,
  `rider_id` bigint unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deducted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_advances_rider_id_foreign` (`rider_id`),
  KEY `cash_advances_admin_id_foreign` (`admin_id`),
  CONSTRAINT `cash_advances_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_advances_rider_id_foreign` FOREIGN KEY (`rider_id`) REFERENCES `riders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.cash_advances: ~2 rows (approximately)
REPLACE INTO `cash_advances` (`id`, `admin_id`, `rider_id`, `amount`, `date`, `notes`, `is_deducted`, `created_at`, `updated_at`) VALUES
	(5, 1, 8, 100.00, '2026-07-18', 'food', 1, '2026-07-19 16:10:20', '2026-07-19 16:35:50');

-- Dumping structure for table spx_attendance.expenses
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_admin_id_foreign` (`admin_id`),
  CONSTRAINT `expenses_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.expenses: ~6 rows (approximately)
REPLACE INTO `expenses` (`id`, `admin_id`, `amount`, `date`, `description`, `created_at`, `updated_at`) VALUES
	(1, 1, 1800.00, '2026-07-09', 'full tank gas', '2026-07-09 16:59:04', '2026-07-09 16:59:04'),
	(2, 1, 3000.00, '2026-07-12', 'paid 3k cash partial payment sa car monthly due', '2026-07-12 16:14:30', '2026-07-12 16:14:30'),
	(3, 1, 600.00, '2026-07-12', 'food', '2026-07-12 16:14:51', '2026-07-12 16:14:51'),
	(4, 1, 2300.00, '2026-07-18', 'remaining balance for JUNE (paid)', '2026-07-19 16:14:57', '2026-07-19 16:14:57'),
	(5, 1, 930.00, '2026-07-18', 'personal expense (laag2)', '2026-07-19 16:17:46', '2026-07-19 16:17:46'),
	(6, 1, 5000.00, '2026-07-18', 'cash loan', '2026-07-19 16:18:09', '2026-07-19 16:18:09');

-- Dumping structure for table spx_attendance.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table spx_attendance.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.jobs: ~0 rows (approximately)

-- Dumping structure for table spx_attendance.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.job_batches: ~0 rows (approximately)

-- Dumping structure for table spx_attendance.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.migrations: ~0 rows (approximately)
REPLACE INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_06_04_000001_add_role_to_users_table', 1),
	(5, '2026_06_04_000002_create_riders_table', 1),
	(6, '2026_06_04_000003_create_spx_accounts_table', 1),
	(7, '2026_06_04_000004_create_attendances_table', 1),
	(8, '2026_06_04_000005_create_cash_advances_table', 1),
	(9, '2026_06_04_000006_create_payslips_table', 1),
	(10, '2026_06_04_000007_create_payslip_cash_advances_table', 1),
	(11, '2026_06_04_000008_add_spx_account_id_to_riders_table', 1),
	(12, '2026_06_08_000001_create_payslip_deductions_table', 1),
	(13, '2026_06_08_000002_add_manual_deduction_to_payslips_table', 1),
	(14, '2026_06_30_000001_create_expenses_table', 1),
	(15, '2026_06_30_000002_create_weekly_incomes_table', 1),
	(16, '2026_07_02_000000_add_admin_id_to_tenant_tables', 1),
	(17, '2026_07_08_000001_add_carried_balance_to_riders_table', 2),
	(18, '2026_07_08_000002_add_prior_balance_to_payslips_table', 2);

-- Dumping structure for table spx_attendance.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table spx_attendance.payslips
CREATE TABLE IF NOT EXISTS `payslips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned DEFAULT NULL,
  `rider_id` bigint unsigned NOT NULL,
  `week_start` date NOT NULL,
  `week_end` date NOT NULL,
  `days_worked` int NOT NULL DEFAULT '0',
  `half_days` int NOT NULL DEFAULT '0',
  `daily_rate` decimal(10,2) NOT NULL,
  `gross_pay` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cash_advance_deduction` decimal(10,2) NOT NULL DEFAULT '0.00',
  `manual_deduction` decimal(10,2) NOT NULL DEFAULT '0.00',
  `prior_balance_deduction` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Amount of prior carried balance applied/deducted on this payslip',
  `net_pay` decimal(10,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','final') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payslips_rider_id_foreign` (`rider_id`),
  KEY `payslips_admin_id_foreign` (`admin_id`),
  CONSTRAINT `payslips_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payslips_rider_id_foreign` FOREIGN KEY (`rider_id`) REFERENCES `riders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.payslips: ~7 rows (approximately)
REPLACE INTO `payslips` (`id`, `admin_id`, `rider_id`, `week_start`, `week_end`, `days_worked`, `half_days`, `daily_rate`, `gross_pay`, `cash_advance_deduction`, `manual_deduction`, `prior_balance_deduction`, `net_pay`, `notes`, `status`, `created_at`, `updated_at`) VALUES
	(20, 1, 3, '2026-06-29', '2026-07-05', 4, 0, 600.00, 2800.00, 0.00, 0.00, 0.00, 2800.00, NULL, 'final', '2026-07-09 16:59:36', '2026-07-09 16:59:36'),
	(21, 1, 4, '2026-06-29', '2026-07-05', 3, 0, 600.00, 1800.00, 0.00, 0.00, 0.00, 1800.00, NULL, 'final', '2026-07-09 16:59:36', '2026-07-09 16:59:36'),
	(23, 1, 4, '2026-07-06', '2026-07-12', 4, 0, 600.00, 2400.00, 0.00, 0.00, 0.00, 2400.00, NULL, 'final', '2026-07-12 21:10:36', '2026-07-12 21:10:36'),
	(24, 1, 3, '2026-07-06', '2026-07-12', 5, 0, 600.00, 3400.00, 0.00, 500.00, 0.00, 2900.00, NULL, 'final', '2026-07-16 16:43:48', '2026-07-16 16:43:48'),
	(25, 1, 3, '2026-07-13', '2026-07-19', 5, 0, 600.00, 3800.00, 0.00, 0.00, 0.00, 3800.00, NULL, 'final', '2026-07-19 16:35:50', '2026-07-19 16:35:50'),
	(26, 1, 4, '2026-07-13', '2026-07-19', 1, 0, 600.00, 600.00, 0.00, 0.00, 0.00, 600.00, NULL, 'final', '2026-07-19 16:35:50', '2026-07-19 16:35:50'),
	(27, 1, 8, '2026-07-13', '2026-07-19', 2, 0, 500.00, 1000.00, 100.00, 0.00, 0.00, 900.00, NULL, 'final', '2026-07-19 16:35:50', '2026-07-19 16:35:50');

-- Dumping structure for table spx_attendance.payslip_cash_advances
CREATE TABLE IF NOT EXISTS `payslip_cash_advances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payslip_id` bigint unsigned NOT NULL,
  `cash_advance_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payslip_cash_advances_payslip_id_cash_advance_id_unique` (`payslip_id`,`cash_advance_id`),
  KEY `payslip_cash_advances_cash_advance_id_foreign` (`cash_advance_id`),
  CONSTRAINT `payslip_cash_advances_cash_advance_id_foreign` FOREIGN KEY (`cash_advance_id`) REFERENCES `cash_advances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payslip_cash_advances_payslip_id_foreign` FOREIGN KEY (`payslip_id`) REFERENCES `payslips` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.payslip_cash_advances: ~2 rows (approximately)
REPLACE INTO `payslip_cash_advances` (`id`, `payslip_id`, `cash_advance_id`, `created_at`, `updated_at`) VALUES
	(4, 27, 5, NULL, NULL);

-- Dumping structure for table spx_attendance.payslip_deductions
CREATE TABLE IF NOT EXISTS `payslip_deductions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payslip_id` bigint unsigned NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payslip_deductions_payslip_id_foreign` (`payslip_id`),
  CONSTRAINT `payslip_deductions_payslip_id_foreign` FOREIGN KEY (`payslip_id`) REFERENCES `payslips` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.payslip_deductions: ~0 rows (approximately)
REPLACE INTO `payslip_deductions` (`id`, `payslip_id`, `label`, `amount`, `created_at`, `updated_at`) VALUES
	(1, 24, 'cellphone loan', 500.00, '2026-07-16 16:43:48', '2026-07-16 16:43:48');

-- Dumping structure for table spx_attendance.riders
CREATE TABLE IF NOT EXISTS `riders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `daily_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `carried_balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Outstanding debt carried forward from previous payslips where deductions exceeded gross pay',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` bigint unsigned DEFAULT NULL,
  `spx_account_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `riders_employee_id_unique` (`employee_id`),
  KEY `riders_user_id_foreign` (`user_id`),
  KEY `riders_spx_account_id_foreign` (`spx_account_id`),
  KEY `riders_admin_id_foreign` (`admin_id`),
  CONSTRAINT `riders_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `riders_spx_account_id_foreign` FOREIGN KEY (`spx_account_id`) REFERENCES `spx_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `riders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.riders: ~4 rows (approximately)
REPLACE INTO `riders` (`id`, `admin_id`, `name`, `employee_id`, `contact_number`, `daily_rate`, `carried_balance`, `is_active`, `user_id`, `spx_account_id`, `created_at`, `updated_at`) VALUES
	(3, 1, 'Jeff Clifford Aniñon', '1', '09657591242', 600.00, 0.00, 1, NULL, NULL, '2026-07-06 16:51:20', '2026-07-07 19:12:02'),
	(4, 1, 'Michael Adrian Romulo', '2', '09267926034', 600.00, 0.00, 1, NULL, NULL, '2026-07-06 16:51:52', '2026-07-07 17:58:15'),
	(5, 1, 'RJ Caayupan', '3', '09171622120', 600.00, 0.00, 1, NULL, NULL, '2026-07-06 16:54:57', '2026-07-07 17:58:15'),
	(6, 1, 'Mike Jun Zaballero', '4', '09750488036', 500.00, 0.00, 1, NULL, NULL, '2026-07-06 16:55:16', '2026-07-07 17:58:15'),
	(8, 1, 'Rap2', '5', '0912345666', 500.00, 0.00, 1, NULL, NULL, '2026-07-12 21:41:12', '2026-07-12 21:41:12');

-- Dumping structure for table spx_attendance.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.sessions: ~2 rows (approximately)
REPLACE INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('1i0oBIOdpP9elJIsJycTrlihMKkKvDHnykjip1Gq', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibFR3V015V0VHTWZhb2RNQUdYbWtxQ1hPWGZLOVpEU0pWU0xBNkxvSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6OTc6Imh0dHA6Ly9zcHhhdHRlbmRhbmNlLnRlc3QvcGF5c2xpcHMvcmVwb3J0P2N1c3RvbV93ZWVrX3N0YXJ0PSZ3ZWVrX3N0YXJ0PTIwMjYtMDctMTMlMjAwMCUzQTAwJTNBMDAiO3M6NToicm91dGUiO3M6MTU6InBheXNsaXBzLnJlcG9ydCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1784507772),
	('e1Obf15MW231c7sVQcVgxszoPifjpGBzUe7QFHDn', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiM2l1S3FtVWZOU1VOMHhnYWUwOW5jZ0x5aG1NY0tvR2s5bENJQWlxSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9zcHhhdHRlbmRhbmNlLnRlc3QvcGF5c2xpcHMiO3M6NToicm91dGUiO3M6MTQ6InBheXNsaXBzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1784256331);

-- Dumping structure for table spx_attendance.spx_accounts
CREATE TABLE IF NOT EXISTS `spx_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned DEFAULT NULL,
  `account_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spx_accounts_account_code_unique` (`account_code`),
  KEY `spx_accounts_admin_id_foreign` (`admin_id`),
  CONSTRAINT `spx_accounts_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.spx_accounts: ~2 rows (approximately)
REPLACE INTO `spx_accounts` (`id`, `admin_id`, `account_code`, `account_name`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
	(3, 1, '379848', 'Patrick Abrogar Jemino', 'regular account', 1, '2026-07-06 16:52:17', '2026-07-06 16:52:17'),
	(4, 1, '123456', 'Mike Jun Zaballero', 'flexi account', 1, '2026-07-06 16:52:31', '2026-07-06 16:52:31');

-- Dumping structure for table spx_attendance.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','rider') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.users: ~1 rows (approximately)
REPLACE INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Admin', 'admin@spx.com', 'admin', NULL, '$2y$12$Yjlu6rdGwZ9FtRcdq1dTFOnrNyuG6n8ldF9R9d1DPZB6uGPww3bzO', NULL, '2026-07-06 16:30:31', '2026-07-06 16:30:31');

-- Dumping structure for table spx_attendance.weekly_incomes
CREATE TABLE IF NOT EXISTS `weekly_incomes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `week_start` date NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `weekly_incomes_admin_id_foreign` (`admin_id`),
  CONSTRAINT `weekly_incomes_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table spx_attendance.weekly_incomes: ~0 rows (approximately)
REPLACE INTO `weekly_incomes` (`id`, `admin_id`, `amount`, `week_start`, `notes`, `created_at`, `updated_at`) VALUES
	(1, 1, 10000.00, '2026-07-06', NULL, '2026-07-09 16:59:17', '2026-07-09 16:59:17'),
	(2, 1, 9130.00, '2026-07-13', NULL, '2026-07-15 23:01:28', '2026-07-15 23:01:28');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
