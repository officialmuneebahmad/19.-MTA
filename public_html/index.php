<?php
/**
 * Multan Tech Accessories (https://multantech.store)
 * High-Performance Production Front Controller
 * Founded & Developed by Muneeb Ahmad
 */

declare(strict_types=1);

define('MTA_START', microtime(true));
define('MTA_ROOT', dirname(__DIR__));
define('MTA_APP', MTA_ROOT . '/app');
define('MTA_PUBLIC', __DIR__);
define('MTA_STORAGE', MTA_APP . '/storage');

// -------------------------------------------------------------
// 1. Fast Full-Page Cache Gatekeeper (Zero DB boot for cached pages)
// -------------------------------------------------------------
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$pathOnly = parse_url($requestUri, PHP_URL_PATH) ?: '/';

$isCacheable = ($requestMethod === 'GET')
    && empty($_COOKIE['mta_session'])
    && !str_starts_with($pathOnly, '/cart')
    && !str_starts_with($pathOnly, '/checkout')
    && !str_starts_with($pathOnly, '/order')
    && !str_starts_with($pathOnly, '/search')
    && !str_starts_with($pathOnly, '/track-order')
    && !str_contains($pathOnly, 'control-')
    && !str_starts_with($pathOnly, '/api');

if ($isCacheable) {
    $cacheKey = md5(strtolower(trim($requestUri, '/')));
    $cacheFile = MTA_STORAGE . '/cache/' . $cacheKey . '.html';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 3600)) {
        header('Content-Type: text/html; charset=UTF-8');
        header('X-MTA-Cache: HIT');
        readfile($cacheFile);
        exit;
    }
}

// -------------------------------------------------------------
// 2. Trailing Slash Normalization & Canonical HTTPS Redirect
// -------------------------------------------------------------
if ($pathOnly !== '/' && str_ends_with($pathOnly, '/')) {
    $cleanUrl = rtrim($pathOnly, '/');
    $query = parse_url($requestUri, PHP_URL_QUERY);
    if ($query) {
        $cleanUrl .= '?' . $query;
    }
    http_response_code(301);
    header('Location: ' . $cleanUrl);
    exit;
}

// -------------------------------------------------------------
// 3. Bootstrap Application
// -------------------------------------------------------------
require_once MTA_APP . '/bootstrap.php';

// If not yet installed, route to installer
if (!file_exists(MTA_STORAGE . '/install.lock') && file_exists(MTA_PUBLIC . '/install.php')) {
    if (!str_starts_with($pathOnly, '/install')) {
        header('Location: /install.php');
        exit;
    }
}

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;
use App\Core\Cache;
use App\Core\Middleware\SecurityHeadersMiddleware;
use App\Core\Middleware\CsrfMiddleware;

Session::start();

// Apply Global Security Headers
$secMiddleware = new SecurityHeadersMiddleware();
$request = new Request();
$secMiddleware->handle($request);

// -------------------------------------------------------------
// 4. Load Route Definitions
// -------------------------------------------------------------
$router = new Router();
require MTA_APP . '/routes.php';

// -------------------------------------------------------------
// 5. Dispatch Request & Cache Candidate Pages
// -------------------------------------------------------------
try {
    $response = $router->dispatch($request);

    // Save to Full-Page Cache if eligible
    if ($isCacheable && $response->getStatusCode() === 200) {
        ob_start();
        $response->send();
        $output = ob_get_contents();
        ob_end_flush();

        if (strlen($output) > 500) {
            Cache::setPage($requestUri, $output);
        }
        exit;
    }

    $response->send();
} catch (\Throwable $e) {
    if (mta_config('app.debug', false)) {
        echo '<h1>500 System Exception</h1>';
        echo '<p><strong>Message:</strong> ' . e($e->getMessage()) . '</p>';
        echo '<p><strong>File:</strong> ' . e($e->getFile()) . ':' . $e->getLine() . '</p>';
        echo '<pre>' . e($e->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        require MTA_APP . '/Views/storefront/errors/500.php';
    }
}
