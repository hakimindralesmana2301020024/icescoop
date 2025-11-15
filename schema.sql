-- Schema for IceScoop user authentication
-- Usage: import into MySQL / MariaDB (phpMyAdmin or `mysql -u root -p < schema.sql`)

-- Create a dedicated database (optional)
CREATE DATABASE IF NOT EXISTS `icescoop_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `icescoop_db`;

-- Users table: stores registered users for login/registration
-- Passwords must be stored as secure hashes (e.g. PHP's password_hash()).
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'user',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_users_email` (`email`),
  UNIQUE KEY `ux_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example seed user (password is 'password' hashed using PHP password_hash())
-- To generate a hash in PHP: `echo password_hash('password', PASSWORD_DEFAULT);`
-- Replace the hash below with a real one before using in production.
INSERT INTO `users` (`username`,`email`,`password`,`role`) VALUES
('admin','admin@example.com','$2y$10$abcdefghijklmnopqrstuv0123456789ABCDEFGHijklmnopqrs','admin');

-- End of schema
