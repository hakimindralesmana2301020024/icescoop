<?php
// CLI script to fill missing/empty slugs in `blogs` table using a unique slug generator.
// Usage (from project root):
// php tools/fill_slugs.php

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

// Load DB config from application/config/database.php
$dbConfigPath = __DIR__ . '/../application/config/database.php';
if (!file_exists($dbConfigPath)) {
    echo "Cannot find database config at $dbConfigPath. Please run this script from the project root.\n";
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

function slugify($text) {
    // basic slugify: transliterate, lowercase, replace non-alnum with -, collapse -, trim
    $text = trim($text);
    if ($text === '') return '';
    // transliterate (requires iconv)
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    $text = trim($text, '-');
    if ($text === '') return 'post';
    // limit length
    return substr($text, 0, 200);
}

// Fetch rows with empty or NULL slug
$sel = $mysqli->prepare("SELECT id, title, slug FROM blogs WHERE slug IS NULL OR slug = ''");
if (!$sel) {
    echo "Prepare failed: " . $mysqli->error . "\n";
    exit(1);
}
$sel->execute();
$res = $sel->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);
$sel->close();

if (empty($rows)) {
    echo "No rows with empty slug found.\n";
    exit(0);
}

$updated = 0;
foreach ($rows as $r) {
    $id = (int)$r['id'];
    $title = isset($r['title']) ? $r['title'] : '';
    $base = slugify($title);
    if ($base === '') $base = 'post';
    $slug = $base;

    // ensure unique
    $i = 0;
    while (true) {
        $chk = $mysqli->prepare("SELECT COUNT(*) as c FROM blogs WHERE slug = ?");
        $chk->bind_param('s', $slug);
        $chk->execute();
        $cr = $chk->get_result()->fetch_assoc();
        $chk->close();
        if ($cr && isset($cr['c']) && (int)$cr['c'] === 0) break;
        $i++;
        $slug = $base . '-' . $i;
    }

    // update
    $upd = $mysqli->prepare("UPDATE blogs SET slug = ? WHERE id = ?");
    $upd->bind_param('si', $slug, $id);
    $ok = $upd->execute();
    if ($ok) {
        echo "Updated id=$id slug=$slug\n";
        $updated++;
    } else {
        echo "Failed update id=$id: " . $upd->error . "\n";
    }
    $upd->close();
}

echo "Done. Updated $updated rows.\n";
$mysqli->close();

?>