<?php
// CLI helper to add `featured_image` column to `blogs` table if it doesn't exist.
// Usage: php tools/add_featured_column.php

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

$dbConfigPath = __DIR__ . '/../application/config/database.php';
if (!file_exists($dbConfigPath)) {
    echo "Cannot find database config at $dbConfigPath.\n";
    exit(1);
}

if (!defined('BASEPATH')) define('BASEPATH', __DIR__);
if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development');
require $dbConfigPath;
if (!isset($db) || !isset($db['default'])) {
    echo "Database configuration not found in $dbConfigPath.\n";
    exit(1);
}
$cfg = $db['default'];
$dbHost = isset($cfg['hostname']) ? $cfg['hostname'] : 'localhost';
$dbUser = isset($cfg['username']) ? $cfg['username'] : 'root';
$dbPass = isset($cfg['password']) ? $cfg['password'] : '';
$dbName = isset($cfg['database']) ? $cfg['database'] : '';

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n";
    exit(1);
}

// Check if column exists
$stmt = $mysqli->prepare("SELECT COUNT(*) as c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'blogs' AND COLUMN_NAME = 'featured_image'");
$stmt->bind_param('s', $dbName);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($res && isset($res['c']) && (int)$res['c'] > 0) {
    echo "Column `featured_image` already exists in `blogs`.\n";
    $mysqli->close();
    exit(0);
}

// Add the column
$sql = "ALTER TABLE `blogs` ADD COLUMN `featured_image` VARCHAR(255) NULL AFTER `author_id`";
if ($mysqli->query($sql) === TRUE) {
    echo "Added column `featured_image` to `blogs`.\n";
} else {
    echo "Failed to add column: (" . $mysqli->errno . ") " . $mysqli->error . "\n";
}

$mysqli->close();
?>