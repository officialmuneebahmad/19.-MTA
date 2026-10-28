<?php
/**
 * Multan Tech Accessories — Development Server Router
 * Used with PHP built-in web server:
 * php -S 127.0.0.1:8000 -t public_html router.php
 */

declare(strict_types=1);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$publicDir = __DIR__;
$filePath = realpath($publicDir . $uri);

// If the requested URI is an actual file inside public_html, serve it directly
if ($uri !== '/' && $filePath && str_starts_with($filePath, realpath($publicDir)) && is_file($filePath)) {
    return false;
}

// Otherwise pass to front controller
require_once $publicDir . '/index.php';

