<?php
// router.php — PHP built-in server router for Railway deployment
// Routes all requests correctly: static files served directly, PHP files executed

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = __DIR__ . $uri;

// Serve existing static files (CSS, JS, images, fonts) directly
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

// Route to index.php for all other requests
require_once __DIR__ . '/index.php';
