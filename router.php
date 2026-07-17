<?php
// router.php — PHP built-in server router for Railway deployment
// Routes all requests correctly: static files served directly, PHP files executed

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = __DIR__ . $uri;

// Serve existing static files (CSS, JS, images, fonts) directly
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    // If it's a PHP file, we MUST include it (otherwise the built-in server will serve the source code or fail)
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require_once $file;
        return true;
    }
    return false;
}

// Route to index.php for all other requests (like /)
require_once __DIR__ . '/index.php';
