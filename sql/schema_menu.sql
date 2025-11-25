-- Menu schema: products, categories, product_category pivot

DROP TABLE IF EXISTS `product_category`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `description` text,
  `price` decimal(10,2) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_price` (`price`),
  KEY `idx_featured` (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `product_category` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`category_id`),
  KEY `idx_category` (`category_id`),
  CONSTRAINT `fk_pc_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional: sample data (you can remove if not needed)
INSERT INTO categories (name, slug) VALUES
('Cone/Cup','cone-cup'),
('Frozen Yogurt','frozen-yogurt'),
('Ice Cream Cake','ice-cream-cake'),
('Milkshakes','milkshakes'),
('Popsicles','popsicles'),
('Sundaes','sundaes');

INSERT INTO products (name, slug, description, price, rating, image, featured) VALUES
('Classic Vanilla Ice Cream','classic-vanilla','Creamy vanilla ice cream topped with cherry.',4.99,4.85,'assets/images/placeholder.svg',0),
('Chocolate Brownie Sundae','choc-brownie','Rich chocolate ice cream with chunky brownie.',5.49,4.95,'assets/images/placeholder.svg',1),
('Strawberry Shortcake','straw-shortcake','Strawberry ice cream layered with shortcake.',6.29,4.88,'assets/images/placeholder.svg',0);

INSERT INTO product_category (product_id, category_id) VALUES
(1,1),(2,6),(3,3);
