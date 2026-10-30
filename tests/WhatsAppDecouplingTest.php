<?php
/**
 * Multan Tech Accessories — Automated WhatsApp Decoupling Test (Task 1)
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Cache;
use App\Core\Database;
use App\Core\Request;
use App\Core\Router;

echo "\n=== Running WhatsApp Decoupling & Neutral Share Test (Task 1) ===\n";

$db = Database::getInstance();
$router = new Router();
require MTA_APP . '/routes.php';

$errors = [];

// 1. Verify default setting is 0 (OFF)
$waCardSetting = mta_setting('show_wa_on_product_cards', '0', true);
if ($waCardSetting !== '0') {
    $errors[] = "FAIL: show_wa_on_product_cards default setting is not '0'. Current value: '{$waCardSetting}'";
} else {
    echo "Default Admin Setting (show_wa_on_product_cards = 0): PASSED\n";
}

// 2. Fetch sample in-stock and out-of-stock products
$sampleInStock = $db->fetch("SELECT * FROM products WHERE is_active = 1 AND status = 1 AND sell_price > 0 LIMIT 1");
$sampleOutOfStock = $db->fetch("SELECT * FROM products WHERE is_active = 1 AND status = 2 LIMIT 1");

if (!$sampleInStock) {
    echo "FAIL: No active in-stock products found.\n";
    exit(1);
}

// 3. Test Product Cards with show_wa_on_product_cards = 0
Cache::flush();
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = [];
$req = new Request();
$resp = $router->dispatch($req);
$homeHtml = $resp->getBody();

// Extract each product card and assert zero WhatsApp glyphs
preg_match_all('/<div class="product-card".*?<\/div>\s*<\/div>\s*<\/div>/s', $homeHtml, $matches);
$cards = $matches[0] ?? [];
$cardGlyphFound = false;
foreach ($cards as $card) {
    if (str_contains($card, 'M18.403 5.638')) {
        $cardGlyphFound = true;
        break;
    }
}

if ($cardGlyphFound) {
    $errors[] = "FAIL: WhatsApp glyph found inside product card on homepage when setting is OFF";
} else {
    echo "Homepage Product Cards (" . count($cards) . " cards tested): PASSED (Zero WhatsApp logo glyphs inside product cards)\n";
}

// 4. Test Product Page (/products/{slug})
$_SERVER['REQUEST_URI'] = '/products/' . $sampleInStock['slug'];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = [];
$req = new Request();
$resp = $router->dispatch($req);
$productHtml = $resp->getBody();

// Isolate the product layout grid (excluding floating actions)
$productLayout = '';
if (preg_match('/<div class="product-layout-grid">.*?<\/div>\s*<\/div>\s*<\/section>/s', $productHtml, $mLayout)) {
    $productLayout = $mLayout[0];
} else {
    // Or everything before floating-actions
    $productLayout = explode('<div class="floating-actions"', $productHtml)[0];
}

if (str_contains($productLayout, 'M18.403 5.638')) {
    $errors[] = "FAIL: WhatsApp logo glyph found in product detail page element";
} else {
    echo "Product Detail Page: PASSED (Zero WhatsApp logo glyphs inside product page elements)\n";
}

// Required: Neutral share icon present on product page
if (!str_contains($productHtml, 'title="Share"') || !str_contains($productHtml, '<circle cx="18" cy="5" r="3">')) {
    $errors[] = "FAIL: Neutral share icon not found on product page";
} else {
    echo "Product Share Button: PASSED (Neutral outline share icon present)\n";
}

// Required: ONE clear text button "Order on WhatsApp" with neutral chat bubble
if (!str_contains($productHtml, 'Order on WhatsApp') || !str_contains($productHtml, 'd="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"')) {
    $errors[] = "FAIL: 'Order on WhatsApp' with neutral chat bubble icon not found on product page";
} else {
    echo "Product Order on WhatsApp Button: PASSED (Neutral chat bubble icon present)\n";
}

// 5. Test Admin Toggle (show_wa_on_product_cards = 1)
$db->execute("UPDATE settings SET value = '1' WHERE `key` = 'show_wa_on_product_cards'");
mta_setting('show_wa_on_product_cards', '1', true);
Cache::flush();

$req = new Request();
$_SERVER['REQUEST_URI'] = '/';
$resp = $router->dispatch($req);
$homeHtmlWaOn = $resp->getBody();

if (!str_contains($homeHtmlWaOn, '<span>WhatsApp</span>')) {
    $errors[] = "FAIL: WhatsApp quick order button did not appear on product cards when setting was set to 1";
} else {
    echo "Admin Setting Toggle (show_wa_on_product_cards = 1): PASSED (WhatsApp button restored without code)\n";
}

// Revert setting to default 0
$db->execute("UPDATE settings SET value = '0' WHERE `key` = 'show_wa_on_product_cards'");
mta_setting('show_wa_on_product_cards', '0', true);
Cache::flush();

if (!empty($errors)) {
    echo "\nTest FAILED with " . count($errors) . " errors:\n";
    foreach ($errors as $e) echo " - $e\n";
    exit(1);
}

echo "\nWhatsApp Decoupling Test PASSED 100%! All requirements verified.\n";
exit(0);
