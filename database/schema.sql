-- HUIT Schedule Hub Database Schema
-- Database: huit_db

CREATE DATABASE IF NOT EXISTS `huit_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `huit_db`;

-- 1. Bảng branches (Cơ sở)
DROP TABLE IF EXISTS `schedules`;
DROP TABLE IF EXISTS `teachers`;
DROP TABLE IF EXISTS `branches`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `branches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_name` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng teachers (Giảng viên)
CREATE TABLE `teachers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(20) NOT NULL,
    `expertise` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL DEFAULT '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- default 'password'
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng users (Quản trị viên & Người dùng hệ thống)
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'staff') DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng schedules (Lịch dạy)
CREATE TABLE `schedules` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `teacher_id` INT NOT NULL,
    `branch_id` INT NOT NULL,
    `subject_name` VARCHAR(150) NOT NULL,
    `room` VARCHAR(50) NOT NULL,
    `day_of_week` VARCHAR(20) NOT NULL,
    `shift_or_periods` VARCHAR(50) NOT NULL,
    `semester` VARCHAR(50) DEFAULT 'HK1 (2026-2027)',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_schedule_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_schedule_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
