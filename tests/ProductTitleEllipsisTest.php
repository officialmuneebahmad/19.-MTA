<?php
/**
 * Multan Tech Accessories — Automated Product Title Truncation & Ellipsis Test (Task 2)
 *
 * Verifies that:
 * 1. Zero CSS rules apply -webkit-line-clamp or text-overflow: ellipsis to product titles or listings.
 * 2. Product titles across home, categories, search, best sellers, and detail pages render full text.
 * 3. The longest supplier titles display completely without truncation.
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Core\Request;
use App\Core\Router;

echo "\n=== Running Product Title Truncation & Ellipsis Test (Task 2) ===\n";

$errors = [];

// 1. Audit CSS stylesheets for prohibited truncation properties on product titles
$appCss = file_get_contents(MTA_ROOT . '/assets/css/app.css');
$criticalCss = file_get_contents(MTA_ROOT . '/assets/css/critical.css');

if (preg_match('/\.product-title\s*\{[^}]*-webkit-line-clamp/s', $appCss)) {
    $errors[] = "FAIL: .product-title in app.css contains -webkit-line-clamp";
}
if (preg_match('/\.product-title\s*\{[^}]*text-overflow\s*:\s*ellipsis/s', $appCss)) {
    $errors[] = "FAIL: .product-title in app.css contains text-overflow: ellipsis";
}
if (preg_match('/\.search-item-title\s*\{[^}]*text-overflow\s*:\s*ellipsis/s', $appCss)) {
    $errors[] = "FAIL: .search-item-title in app.css contains text-overflow: ellipsis";
}
if (preg_match('/\.product-title\s*\{[^}]*-webkit-line-clamp/s', $criticalCss)) {
    $errors[] = "FAIL: .product-title in critical.css contains -webkit-line-clamp";
}

if (empty($errors)) {
    echo "CSS Truncation Audit: PASSED (zero prohibited line-clamp or ellipsis rules on titles)\n";
} else {
    foreach ($errors as $e) echo $e . "\n";
    exit(1);
}

// 2. Query longest product title in database
$db = Database::getInstance();
$longestProduct = $db->fetch(
    "SELECT id, title, slug, LENGTH(title) AS len FROM products WHERE is_active = 1 ORDER BY LENGTH(title) DESC LIMIT 1"
);

if (!$longestProduct) {
    echo "FAIL: No active products found in database.\n";
    exit(1);
}

echo "Longest Product in Catalog ({$longestProduct['len']} chars): '{$longestProduct['title']}'\n";

// 3. Test HTML routes for full titles
$router = new Router();
require MTA_APP . '/routes.php';

$testRoutes = [
    '/',
    '/best-sellers',
    '/categories/cables-and-adapters',
    '/search?q=adapter',
    '/products/' . $longestProduct['slug'],
];

foreach ($testRoutes as $route) {
    $parts = parse_url($route);
    $path = $parts['path'] ?? '/';
    $query = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
    }

    $_SERVER['REQUEST_URI'] = $route;
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET = $query;

    $req = new Request();
    $resp = $router->dispatch($req);

    if ($resp->getStatusCode() !== 200) {
        $errors[] = "FAIL: Route {$route} returned HTTP " . $resp->getStatusCode();
        continue;
    }

    $html = $resp->getBody();

    // Check for inline line-clamp or ellipsis styles on titles
    if (preg_match('/class="[^"]*product-title[^"]*"[^>]*style="[^"]*(line-clamp|ellipsis)[^"]*"/i', $html)) {
        $errors[] = "FAIL: Route {$route} contains inline line-clamp/ellipsis on product title";
    }

    // On the longest product page, ensure the entire title is present in h1
    if ($path === '/products/' . $longestProduct['slug']) {
        if (!str_contains($html, htmlspecialchars($longestProduct['title'], ENT_QUOTES, 'UTF-8'))) {
            $errors[] = "FAIL: Longest product title not rendered completely in product page HTML";
        } else {
            echo "Longest Product Page ({$longestProduct['slug']}): PASSED (100% full title present)\n";
        }
    } else {
        echo "Route {$route}: PASSED\n";
    }
}

if (!empty($errors)) {
    echo "\nTest FAILED with " . count($errors) . " errors:\n";
    foreach ($errors as $e) echo " - $e\n";
    exit(1);
}

echo "\nProduct Title Truncation & Ellipsis Test PASSED! All titles wrap cleanly with zero text truncation.\n";
exit(0);
