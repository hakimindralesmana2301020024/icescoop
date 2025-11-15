<?php
// CLI script to create an admin user in the icescoop DB.
// Usage (from project root):
// php tools/create_admin.php admin_username admin_email admin_password

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

if ($argc < 4) {
    echo "Usage: php tools/create_admin.php <username> <email> <password>\n";
    exit(1);
}

$username = $argv[1];
$email = $argv[2];
$password = $argv[3];

$hash = password_hash($password, PASSWORD_DEFAULT);

// Load DB config from application/config/database.php if available
$dbConfigPath = __DIR__ . '/../application/config/database.php';
if (!file_exists($dbConfigPath)) {
    echo "Cannot find database config at $dbConfigPath. Please run this script from the project root.\n";
    exit(1);
}

// Attempt to parse database config by including it (it sets $db array)
// The CI config file checks for BASEPATH; define it so include works from CLI.
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

if (empty($dbName)) {
    echo "Database name not set in config.\n";
    exit(1);
}

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n";
    exit(1);
}

// Check if email already exists
$check = $mysqli->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$check->bind_param('s', $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    // email exists -> update password and role to admin
    $check->bind_result($existing_id);
    $check->fetch();
    $upd = $mysqli->prepare("UPDATE users SET username = ?, password = ?, role = 'admin', is_active = 1 WHERE id = ?");
    $upd->bind_param('ssi', $username, $hash, $existing_id);
    $ok = $upd->execute();
    if ($ok) {
        echo "Updated existing user id={$existing_id} to admin: $username <$email>\n";
    } else {
        echo "Failed to update existing user: " . $upd->error . "\n";
    }
    $upd->close();
} else {
    // Insert admin user
    $stmt = $mysqli->prepare("INSERT INTO users (username, email, password, role, is_active, created_at) VALUES (?, ?, ?, 'admin', 1, NOW())");
    $stmt->bind_param('sss', $username, $email, $hash);
    $ok = $stmt->execute();
    if ($ok) {
        echo "Admin user created: $username <$email>\n";
    } else {
        echo "Failed to create admin user: " . $stmt->error . "\n";
    }
    $stmt->close();
}
$check->close();
$mysqli->close();

?>
