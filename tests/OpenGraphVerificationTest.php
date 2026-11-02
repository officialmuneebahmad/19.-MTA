<?php
/**
 * Multan Tech Accessories — Open Graph & Social Sharing Verification Test
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Core\Database;
use App\Core\Request;

echo "=== MTA OPEN GRAPH & SOCIAL PREVIEW VERIFICATION ===\n\n";

$db = Database::getInstance();

// 1. Test Homepage OG Metadata
echo "1. Testing Homepage OG Metadata... ";
$homeController = new HomeController();
$homeHtml = $homeController->index()->getBody();

assert(str_contains($homeHtml, '<meta property="og:site_name" content="The Computer Shop Multan">'), "og:site_name must be 'The Computer Shop Multan'");
assert(str_contains($homeHtml, '<meta property="og:title" content="The Computer Shop Multan">'), "og:title must be 'The Computer Shop Multan'");
assert(str_contains($homeHtml, '<meta property="og:description" content="Shop computer accessories, mobile accessories, laptop accessories, networking products and gadgets at The Computer Shop Multan.">'), "og:description must match specification");
assert(str_contains($homeHtml, '<meta property="og:type" content="website">'), "og:type must be 'website'");
assert(str_contains($homeHtml, '<meta property="og:url" content="https://multantech.pk/">'), "og:url must be canonical 'https://multantech.pk/'");
assert(str_contains($homeHtml, '<meta property="og:image" content="https://multantech.pk/assets/images/og-share.jpg">'), "og:image must be store social share image");
assert(str_contains($homeHtml, '<meta property="og:image:alt" content="The Computer Shop Multan">'), "og:image:alt must match");
assert(str_contains($homeHtml, '<meta name="twitter:card" content="summary_large_image">'), "twitter:card must be summary_large_image");
assert(str_contains($homeHtml, '<meta name="twitter:title" content="The Computer Shop Multan">'), "twitter:title must match");
echo "PASSED\n";

// 2. Test 5 Product Pages OG Metadata
echo "2. Testing Product Pages OG Metadata for 5 Sample Products...\n";
$products = $db->fetchAll("SELECT slug, title, primary_image_url FROM products WHERE is_active = 1 AND is_published = 1 LIMIT 5");
$productController = new ProductController();

foreach ($products as $idx => $p) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/products/' . $p['slug'];
    $req = new Request();
    $prodHtml = $productController->show($p['slug'], $req)->getBody();

    assert(str_contains($prodHtml, '<meta property="og:site_name" content="The Computer Shop Multan">'), "Product #{$idx} og:site_name must be 'The Computer Shop Multan'");
    assert(str_contains($prodHtml, '<meta property="og:type" content="product">'), "Product #{$idx} og:type must be 'product'");
    assert(str_contains($prodHtml, '<meta property="og:url" content="https://multantech.pk/products/' . $p['slug'] . '">'), "Product #{$idx} og:url must be canonical");
    assert(str_contains($prodHtml, '<meta name="twitter:card" content="summary_large_image">'), "Product #{$idx} twitter:card must be summary_large_image");
    
    // Check that og:title has the actual title
    $escapedTitle = htmlspecialchars($p['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    assert(str_contains($prodHtml, 'property="og:title" content="' . $escapedTitle . '"') || str_contains($prodHtml, 'property="og:title" content="' . $p['title'] . '"'), "Product #{$idx} og:title must contain actual product name");

    echo "   -> Product #{$idx} [{$p['slug']}]: PASSED\n";
}

echo "\n[ALL OPEN GRAPH TESTS PASSED SUCCESSFULLY!]\n";

