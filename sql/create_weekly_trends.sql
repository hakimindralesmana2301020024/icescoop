-- SQL: create table for weekly trend metrics
-- Paste this into phpMyAdmin or run in your MySQL client

CREATE TABLE IF NOT EXISTS `weekly_trends` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `metric` VARCHAR(100) NOT NULL,
  `value` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `metric_idx` (`metric`),
  KEY `created_idx` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example seed rows for the last 7 days (uses CURDATE()).
-- Adjust metric name as needed (e.g. 'orders' or 'visits').
INSERT INTO `weekly_trends` (`metric`,`value`,`created_at`) VALUES
('orders', 5, DATE_SUB(CURDATE(), INTERVAL 6 DAY)),
('orders', 8, DATE_SUB(CURDATE(), INTERVAL 5 DAY)),
('orders', 12, DATE_SUB(CURDATE(), INTERVAL 4 DAY)),
('orders', 7, DATE_SUB(CURDATE(), INTERVAL 3 DAY)),
('orders', 10, DATE_SUB(CURDATE(), INTERVAL 2 DAY)),
('orders', 9, DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
('orders', 14, CURDATE());

-- Example revenue rows for the last 7 days (currency in smallest unit or whole number as you prefer)
INSERT INTO `weekly_trends` (`metric`,`value`,`created_at`) VALUES
('revenue', 500000, DATE_SUB(CURDATE(), INTERVAL 6 DAY)),
('revenue', 650000, DATE_SUB(CURDATE(), INTERVAL 5 DAY)),
('revenue', 430000, DATE_SUB(CURDATE(), INTERVAL 4 DAY)),
('revenue', 720000, DATE_SUB(CURDATE(), INTERVAL 3 DAY)),
('revenue', 610000, DATE_SUB(CURDATE(), INTERVAL 2 DAY)),
('revenue', 580000, DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
('revenue', 910000, CURDATE());
