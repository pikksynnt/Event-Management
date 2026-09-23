-- ========================================================
-- Database Schema & Seed Data for Event Management (Progress 1)
-- Target: MySQL 8.x / MariaDB / Hostinger Shared Hosting
-- ========================================================

CREATE DATABASE IF NOT EXISTS `event_management` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `event_management`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'event_manager',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `events`
-- --------------------------------------------------------
CREATE TABLE `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `estimated_guests` INT NOT NULL,
  `status` ENUM('submitted', 'approved', 'rejected') NOT NULL DEFAULT 'submitted',
  `rejection_reason` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_events_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Seed Data for Event Manager User
-- Password: password123 (bcrypt hash)
-- --------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`) VALUES
(1, 'Budi Santoso', 'manager@eo.com', '$2y$10$7HmYf9VlXD4rilAO2.sDGuqCMMINOJGncLV/7RUguUrw7V5JC2ri.', 'event_manager');

-- --------------------------------------------------------
-- Seed Data for Progress 1 Events
-- --------------------------------------------------------
INSERT INTO `events` (`id`, `title`, `event_type`, `description`, `start_date`, `end_date`, `estimated_guests`, `status`, `rejection_reason`, `created_at`) VALUES
(1, 'Annual Corporate Gala', 'Corporate', 'Malam penghargaan dan apresiasi tahunan seluruh jajaran direksi dan staf korporasi dengan gala dinner serta pertunjukan seni.', '2026-11-15 18:00:00', '2026-11-15 23:00:00', 350, 'submitted', NULL, NOW()),
(2, 'Wedding Celebration', 'Pernikahan', 'Resepsi pernikahan eksklusif bertema modern botanical garden dengan jamuan prasmanan lengkap dan hiburan akustik.', '2026-10-20 10:00:00', '2026-10-20 15:00:00', 500, 'approved', NULL, NOW()),
(3, 'Product Launch', 'Pameran & Peluncuran', 'Peluncuran produk smartphone flagship terbaru dengan sesi live demo, booth interaktif, dan konferensi pers bersama media nasional.', '2026-12-05 13:00:00', '2026-12-05 17:00:00', 200, 'rejected', 'Kapasitas venue yang diajukan tidak sesuai dengan standar keselamatan dan kelengkapan dokumen teknis belum memenuhi regulasi.', NOW());
