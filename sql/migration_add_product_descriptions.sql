-- Migration: add separate short and long description columns to products
ALTER TABLE `products`
  ADD COLUMN `short_description` TEXT DEFAULT NULL AFTER `description`,
  ADD COLUMN `long_description` TEXT DEFAULT NULL AFTER `short_description`;

-- Optional: copy existing description to both new columns for backward compatibility
UPDATE `products` SET `short_description` = `description`, `long_description` = `description` WHERE `description` IS NOT NULL;
