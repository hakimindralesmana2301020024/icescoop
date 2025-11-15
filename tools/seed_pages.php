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
