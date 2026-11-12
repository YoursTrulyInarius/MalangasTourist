CREATE DATABASE IF NOT EXISTS `malangas_tourism`;
USE `malangas_tourism`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `phone` VARCHAR(50),
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('lgu', 'business', 'tourist') NOT NULL DEFAULT 'tourist',
  `status` ENUM('active', 'disabled') NOT NULL DEFAULT 'active',
  `applicant_type` ENUM('private','public') NOT NULL DEFAULT 'private',
  `last_monitoring_read_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `token` VARCHAR(64) NOT NULL UNIQUE,
  `expires_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS `spots` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `owner_user_id` INT NULL,
  `name` VARCHAR(255) NOT NULL,
  `category_id` INT NOT NULL,
  `description` TEXT,
  `barangay` VARCHAR(255),
  `latitude` DECIMAL(10, 8) NOT NULL,
  `longitude` DECIMAL(11, 8) NOT NULL,
  `contact_number` VARCHAR(100),
  `fb_link` VARCHAR(255),
  `hours` VARCHAR(255),
  `price_range` VARCHAR(100),
  `bir_number` VARCHAR(100),
  `business_permit` VARCHAR(255),
  `operational_status` ENUM('open', 'closed', 'temp_closed', 'maintenance') NOT NULL DEFAULT 'open',
  `status` ENUM('pending', 'approved', 'rejected', 'needs_revision') NOT NULL DEFAULT 'pending',
  `monitor_status` ENUM('normal', 'warning', 'under_renovation', 'improved') NOT NULL DEFAULT 'normal',
  `source` ENUM('lgu', 'business') NOT NULL,
  `rejection_reason` TEXT,
  `monitor_note` TEXT,
  `warning_sent_at` DATETIME NULL,
  `improvement_deadline` DATETIME NULL,
  `monitor_last_triggered_at` DATETIME NULL,
  `renovation_note` TEXT,
  `renovation_proof_path` TEXT NULL,
  `monitor_submission_status` ENUM('pending','approved','declined','none') NOT NULL DEFAULT 'none',
  `monitor_submission_reason` TEXT NULL,
  `monitor_decline_reason` TEXT NULL,
  `monitor_reviewed_at` DATETIME NULL,
  `monitor_submitted_at` DATETIME NULL,
  `reviewed_by` INT NULL,
  `reviewed_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`owner_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS `spot_photos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `spot_id` INT NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `is_cover` BOOLEAN DEFAULT FALSE,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`spot_id`) REFERENCES `spots`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `spot_photo_updates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `spot_id` INT NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` DATETIME NULL,
  `reviewed_by` INT NULL,
  `rejection_reason` TEXT NULL,
  FOREIGN KEY (`spot_id`) REFERENCES `spots`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX (`spot_id`, `status`)
);

CREATE TABLE IF NOT EXISTS `spot_photo_update_files` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `update_id` INT NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `is_cover` BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (`update_id`) REFERENCES `spot_photo_updates`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `application_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `spot_id` INT NOT NULL,
  `action` ENUM('submitted', 'approved', 'rejected', 'revision_requested', 'edited') NOT NULL,
  `performed_by` INT NULL,
  `comment` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`spot_id`) REFERENCES `spots`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`performed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS `spot_monitoring` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `spot_id` INT NOT NULL,
  `record_type` ENUM('warning', 'update') NOT NULL,
  `status` ENUM('active', 'acknowledged', 'in_progress', 'completed') NOT NULL DEFAULT 'active',
  `message` TEXT NULL,
  `proof_path` TEXT NULL,
  `deadline_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`spot_id`) REFERENCES `spots`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `spot_id` INT NOT NULL,
  `user_id` INT NULL,
  `rating` INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `comment` TEXT,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` INT NULL,
  `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
  `moderation_note` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`spot_id`) REFERENCES `spots`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  UNIQUE(`spot_id`, `user_id`)
);

CREATE TABLE IF NOT EXISTS `review_photos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `review_id` INT NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`review_id`) REFERENCES `reviews`(`id`) ON DELETE CASCADE
);

-- Insert Default Categories
INSERT IGNORE INTO `categories` (`name`, `icon`) VALUES
('Beach/Island', 'fa-umbrella-beach'),
('Waterfall', 'fa-water'),
('Resort', 'fa-hotel'),
('Restaurant', 'fa-utensils'),
('Boat/Tour Operator', 'fa-ship'),
('Historical/Cultural', 'fa-landmark'),
('Accommodation', 'fa-bed'),
('Other', 'fa-map-marker-alt');

-- Insert Default LGU Admin (Password is 'password')
INSERT IGNORE INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES
('LGU Admin', 'admin@malangas.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lgu');

