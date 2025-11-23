<?php
/**
 * Router script for PHP built-in web server
 *
 * This router enables Laminas MVC routing to work with PHP's built-in server.
 * Without this, the built-in server treats MVC routes as file paths and returns 404.
 *
 * Usage:
 *   php -S localhost:8080 router.php
 *
 * This routes all requests through index.php while still serving static files directly.
 */

// Define document root - use absolute path to avoid working directory issues
define('ROUTER_DOCUMENT_ROOT', __DIR__);

// Get the requested URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Don't route the router script itself
if ($uri === '/router.php') {
    return false;
}

// Serve static files directly (CSS, JS, images, etc.)
$file = ROUTER_DOCUMENT_ROOT . $uri;
if ($uri !== '/' && is_file($file)) {
    // Let PHP built-in server handle static files
    return false;
}

// Route all other requests through index.php
$indexFile = ROUTER_DOCUMENT_ROOT . '/index.php';
require $indexFile;
