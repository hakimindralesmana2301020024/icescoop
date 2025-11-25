<?php
// CLI script: extract first image from content_html and set featured_image.
// - If src is a data URI (base64), decode and save to assets/images, then update featured_image.
// - If src points to /assets/images/, extract basename and update featured_image.
// Usage: php tools/extract_and_save_cover.php

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

$destDir = rtrim(__DIR__ . '/../assets/images', '\\/');
if (!is_dir($destDir)) {
    if (!@mkdir($destDir, 0755, true)) {
        echo "Failed to create directory: $destDir\n";
        exit(1);
    }
}

// Select posts where featured_image is null/empty and content_html contains img
$sql = "SELECT id, content_html FROM blogs WHERE (featured_image IS NULL OR featured_image = '') AND content_html IS NOT NULL AND content_html <> ''";
$res = $mysqli->query($sql);
if (!$res) {
    echo "Query failed: " . $mysqli->error . "\n";
    $mysqli->close();
    exit(1);
}

$updated = 0;
while ($row = $res->fetch_assoc()) {
    $id = (int)$row['id'];
    $html = $row['content_html'];
    if (!$html) continue;

    // Find first img src (supports single or double quotes)
    if (preg_match('/<img[^>]+src=["\']?([^"\' >]+)["\']?[^>]*>/i', $html, $m)) {
        $src = $m[1];
        // data URI
        if (strpos($src, 'data:') === 0) {
            // format: data:[<mediatype>][;base64],<data>
            if (preg_match('/^data:([^;]+);base64,(.+)$/', $src, $d)) {
                $mime = $d[1];
                $b64 = $d[2];
                // determine extension
                $ext = '';
                switch (strtolower($mime)) {
                    case 'image/jpeg': $ext = 'jpg'; break;
                    case 'image/jpg': $ext = 'jpg'; break;
                    case 'image/png': $ext = 'png'; break;
                    case 'image/gif': $ext = 'gif'; break;
                    case 'image/webp': $ext = 'webp'; break;
                    default:
                        // try to extract subtype
                        $parts = explode('/', $mime);
                        $ext = isset($parts[1]) ? preg_replace('/[^a-z0-9]/', '', $parts[1]) : 'bin';
                }
                $basename = 'cover_' . $id . '_' . time() . '.' . $ext;
                $dest = $destDir . DIRECTORY_SEPARATOR . $basename;
                $decoded = base64_decode($b64);
                if ($decoded === false) {
                    echo "Failed to decode base64 for id={$id}\n";
                    continue;
                }
                if (@file_put_contents($dest, $decoded) !== false) {
                    // update DB
                    $upd = $mysqli->prepare("UPDATE blogs SET featured_image = ? WHERE id = ?");
                    $upd->bind_param('si', $basename, $id);
                    if ($upd->execute()) {
                        echo "Saved data-URI -> id={$id} file={$basename}\n";
                        $updated++;
                    } else {
                        echo "DB update failed for id={$id}: " . $upd->error . "\n";
                        @unlink($dest);
                    }
                    $upd->close();
                } else {
                    echo "Failed to write file for id={$id} to {$dest}\n";
                }
                continue;
            } else {
                echo "Unrecognized data URI for id={$id}\n";
                continue;
            }
        }

        // If src points to /assets/images/ - use basename
        if (strpos($src, '/assets/images/') !== false) {
            $basename = basename($src);
            if ($basename) {
                $upd = $mysqli->prepare("UPDATE blogs SET featured_image = ? WHERE id = ?");
                $upd->bind_param('si', $basename, $id);
                if ($upd->execute()) {
                    echo "Set existing image -> id={$id} file={$basename}\n";
                    $updated++;
                } else {
                    echo "DB update failed for id={$id}: " . $upd->error . "\n";
                }
                $upd->close();
                continue;
            }
        }

        // External URL (http/https) - skip by default
        if (preg_match('#^https?://#i', $src)) {
            echo "Skipping external image for id={$id}: {$src}\n";
            continue;
        }

        // Other relative paths: attempt to take basename
        $basename = basename($src);
        if ($basename) {
            $upd = $mysqli->prepare("UPDATE blogs SET featured_image = ? WHERE id = ?");
            $upd->bind_param('si', $basename, $id);
            if ($upd->execute()) {
                echo "Set relative image -> id={$id} file={$basename}\n";
                $updated++;
            } else {
                echo "DB update failed for id={$id}: " . $upd->error . "\n";
            }
            $upd->close();
            continue;
        }
    }
}

echo "Done. Updated $updated rows.\n";
$mysqli->close();
?>