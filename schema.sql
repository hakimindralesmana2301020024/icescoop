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

-- Pages table: stores simple site pages as JSON payload per slug
DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `data` longtext NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Example admin seed (recommended to create via the provided CLI script so password is hashed securely)
-- Admin email set to icesooptpl@gmail.com per request. To create this admin with password
-- `adminicescoop1234` (hashed) run the CLI helper included in `tools/create_admin.php`:
-- php tools/create_admin.php admin icesooptpl@gmail.com adminicescoop1234
-- If you prefer to insert via SQL, generate a hash in PHP and replace <PASTE_HASH_HERE> below.
-- Example (generate hash): php -r "echo password_hash('adminicescoop1234', PASSWORD_DEFAULT).PHP_EOL;"
-- Then run this INSERT with the generated hash:
-- INSERT INTO `users` (`username`,`email`,`password`,`role`) VALUES
-- ('admin','icesooptpl@gmail.com','<PASTE_HASH_HERE>','admin');
-- Note: the tools script will insert the admin directly into the configured database when run from project root.

-- End of schema
