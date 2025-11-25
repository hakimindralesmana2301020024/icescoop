<?php
// CLI script: extract first local image from content_html and save as featured_image
// Usage (from project root): php tools/extract_cover_from_content.php

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

$dbConfigPath = __DIR__ . '/../application/config/database.php';
if (!file_exists($dbConfigPath)) {
    echo "Cannot find database config at $dbConfigPath. Please run from project root.\n";
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

// Select posts where featured_image is null/empty and status is published (optional)
$sql = "SELECT id, content_html FROM blogs WHERE (featured_image IS NULL OR featured_image = '')";
$res = $mysqli->query($sql);
if (!$res) {
    echo "Query failed: " . $mysqli->error . "\n";
    $mysqli->close();
    exit(1);
}

$updated = 0;
while ($row = $res->fetch_assoc()) {
    $id = (int)$row['id'];
    $html = isset($row['content_html']) ? $row['content_html'] : '';
    if (!$html) continue;

    // Find first img src
    if (preg_match('/<img[^>]+src=["\']?([^"\' >]+)["\']?[^>]*>/i', $html, $m)) {
        $src = $m[1];
        // If src refers to assets/images, extract basename
        $pos = strpos($src, '/assets/images/');
        if ($pos !== false) {
            $basename = basename($src);
            if ($basename) {
                // Update DB
                $upd = $mysqli->prepare("UPDATE blogs SET featured_image = ? WHERE id = ?");
                $upd->bind_param('si', $basename, $id);
                if ($upd->execute()) {
                    echo "Updated id={$id} featured_image={$basename}\n";
                    $updated++;
                } else {
                    echo "Failed update id={$id}: " . $upd->error . "\n";
                }
                $upd->close();
                continue;
            }
        }
        // If src is data: URI or external or not in assets/images, skip
    }
}

echo "Done. Updated $updated rows.\n";
$mysqli->close();
?>