<?php
// CLI helper to seed pages table with default About content
// Usage: php tools/seed_pages.php

define('CLI', true);
$base = dirname(__DIR__);
require_once $base . '/application/config/database.php';

// extract DB settings
$cfg = $db['default'];
$host = $cfg['hostname'] ?? '127.0.0.1';
$user = $cfg['username'] ?? 'root';
$pass = $cfg['password'] ?? '';
$name = $cfg['database'] ?? 'icescoop_db';

$mysqli = new mysqli($host, $user, $pass, $name);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "DB connect error: " . $mysqli->connect_error . PHP_EOL);
    exit(1);
}

function esc($s) { global $mysqli; return $mysqli->real_escape_string($s); }

$slug = 'about';
$payload = [
    'hero_title' => 'About Us',
    'journey_title' => 'Our Journey Began With a Simple Dream',
    'journey_lead1' => 'Our goal is to make the best ice cream using only the finest, natural ingredients. From rich, creamy classics to adventurous new creations, every flavor is meticulously crafted in-house to ensure the highest quality and freshness.',
    'journey_lead2' => 'We take pride in offering a diverse range of options, including dairy-free, vegan, and gluten-free choices, so everyone can find their perfect scoop.',
    'mission_title' => 'Our Mission is to\nCreate Moments',
    'mission_lead' => 'We strive to foster a welcoming and joyful environment where customers of all ages can gather, celebrate, and make lasting memories. Our commitment extends beyond serving great ice cream.',
    'team' => [
        ['name'=>'Marvin Joner','role'=>'Bakery Worker','image'=>'assets/images/placeholder.svg'],
        ['name'=>'Patricia Woodrum','role'=>'Staff Worker','image'=>'assets/images/placeholder.svg'],
        ['name'=>'Hannaz Stone','role'=>'Shop Worker','image'=>'assets/images/placeholder.svg'],
    ]
];

$json = json_encode($payload, JSON_UNESCAPED_UNICODE);

// ensure table exists (in case migration not run)
$create_sql = "CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `data` longtext NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$mysqli->query($create_sql);

// upsert
$check = $mysqli->query("SELECT id FROM pages WHERE slug='" . esc($slug) . "' LIMIT 1");
if ($check && $check->num_rows > 0) {
    $row = $check->fetch_assoc();
    $id = (int)$row['id'];
    $sql = "UPDATE pages SET data='" . esc($json) . "', updated_at=NOW() WHERE id=" . $id;
    if ($mysqli->query($sql)) {
        echo "Updated existing about page id=$id\n";
    } else {
        echo "Failed to update about: " . $mysqli->error . PHP_EOL;
    }
} else {
    $sql = "INSERT INTO pages (slug, data, created_at, updated_at) VALUES ('" . esc($slug) . "', '" . esc($json) . "', NOW(), NOW())";
    if ($mysqli->query($sql)) {
        echo "Inserted about page id=" . $mysqli->insert_id . "\n";
    } else {
        echo "Failed to insert about: " . $mysqli->error . PHP_EOL;
    }
}

$mysqli->close();

// Also seed a default `home` row into the structured `home` table
$mysqli = new mysqli($host, $user, $pass, $name);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "DB connect error: " . $mysqli->connect_error . PHP_EOL);
    exit(1);
}

$payload = [
    'hero_title' => 'Welcome to IceScoop',
    'hero_subtitle' => 'Discover Sweet Delights',
    'intro' => 'Relish the timeless taste of handcrafted ice cream, made with passion and the finest ingredients.',
    'hero_image' => 'assets/images/placeholder.svg',
    'featured_items' => [
        ['title'=>'Classic Scoop','desc'=>'Rich and creamy vanilla','price'=>'$3.50','rating'=>'4.8','image'=>'assets/images/placeholder.svg'],
        ['title'=>'Chocolate Fudge','desc'=>'Decadent and bold','price'=>'$4.00','rating'=>'4.9','image'=>'assets/images/placeholder.svg'],
        ['title'=>'Strawberry Joy','desc'=>'Fresh strawberry swirls','price'=>'$3.75','rating'=>'4.7','image'=>'assets/images/placeholder.svg']
    ],
    'categories' => [
        ['name'=>'Classic','image'=>'assets/images/placeholder.svg'],
        ['name'=>'Seasonal','image'=>'assets/images/placeholder.svg'],
        ['name'=>'Vegan','image'=>'assets/images/placeholder.svg']
    ],
    'best_sellers' => [
        ['title'=>'Vanilla','price'=>'$3.50','image'=>'assets/images/placeholder.svg'],
        ['title'=>'Chocolate','price'=>'$4.00','image'=>'assets/images/placeholder.svg']
    ],
    'special' => ['title'=>'Summer Special!','sub'=>'Buy One Sundae, Get One 50% Off!','lead'=>'Use code: SUMMER50','image'=>'assets/images/placeholder.svg'],
    'testimonials' => [
        ['text'=>'The best ice cream ever!','name'=>'Amina','role'=>'Customer'],
        ['text'=>'Loved the seasonal flavor.','name'=>'Rian','role'=>'Customer']
    ]
];

$json = $mysqli->real_escape_string(json_encode($payload, JSON_UNESCAPED_UNICODE));

$create_sql = "CREATE TABLE IF NOT EXISTS `home` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_subtitle` varchar(255) DEFAULT NULL,
  `intro` text,
  `hero_image` varchar(255) DEFAULT NULL,
  `features` longtext DEFAULT NULL,
  `featured_items` longtext DEFAULT NULL,
  `categories` longtext DEFAULT NULL,
  `best_sellers` longtext DEFAULT NULL,
  `special` longtext DEFAULT NULL,
  `testimonials` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$mysqli->query($create_sql);

$check = $mysqli->query("SELECT id FROM home LIMIT 1");
if ($check && $check->num_rows > 0) {
    $row = $check->fetch_assoc();
    $id = (int)$row['id'];
    $sql = "UPDATE home SET hero_title='" . $mysqli->real_escape_string($payload['hero_title']) . "', hero_subtitle='" . $mysqli->real_escape_string($payload['hero_subtitle']) . "', intro='" . $mysqli->real_escape_string($payload['intro']) . "', hero_image='" . $mysqli->real_escape_string($payload['hero_image']) . "', featured_items='" . $json . "', categories='" . $json . "', best_sellers='" . $json . "', special='" . $json . "', testimonials='" . $json . "', updated_at=NOW() WHERE id=" . $id;
    if ($mysqli->query($sql)) {
        echo "Updated existing home row id=$id\n";
    } else {
        echo "Failed to update home table: " . $mysqli->error . PHP_EOL;
    }
} else {
    $sql = "INSERT INTO home (hero_title, hero_subtitle, intro, hero_image, featured_items, categories, best_sellers, special, testimonials, created_at, updated_at) VALUES ('" . $mysqli->real_escape_string($payload['hero_title']) . "', '" . $mysqli->real_escape_string($payload['hero_subtitle']) . "', '" . $mysqli->real_escape_string($payload['intro']) . "', '" . $mysqli->real_escape_string($payload['hero_image']) . "', '" . $json . "', '" . $json . "', '" . $json . "', '" . $json . "', '" . $json . "', NOW(), NOW())";
    if ($mysqli->query($sql)) {
        echo "Inserted home row id=" . $mysqli->insert_id . "\n";
    } else {
        echo "Failed to insert home: " . $mysqli->error . PHP_EOL;
    }
}

$mysqli->close();
