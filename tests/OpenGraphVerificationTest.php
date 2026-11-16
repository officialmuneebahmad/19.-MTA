<?php
/**
 * Multan Tech Accessories — Open Graph & Social Sharing Comprehensive Verification Test
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CategoryController;
use App\Controllers\BrandController;
use App\Controllers\GuideController;
use App\Controllers\PageController;
use App\Core\Database;
use App\Core\Request;

echo "=== MTA OPEN GRAPH & SOCIAL PREVIEW VERIFICATION ===\n\n";

$db = Database::getInstance();

function checkCommonOgTags(string $html, string $context): void {
    // Exactly one og:image tag
    $count = substr_count($html, 'property="og:image"');
    assert($count === 1, "{$context}: Must contain exactly 1 og:image tag, found {$count}");

    // Required Open Graph tags
    assert(str_contains($html, '<meta property="og:site_name" content="Multan Tech Accessories">'), "{$context}: Missing og:site_name");
    assert(str_contains($html, '<meta property="og:locale" content="en_PK">'), "{$context}: Missing og:locale 'en_PK'");
    assert(str_contains($html, 'property="og:type"'), "{$context}: Missing og:type");
    assert(str_contains($html, 'property="og:title"'), "{$context}: Missing og:title");
    assert(str_contains($html, 'property="og:description"'), "{$context}: Missing og:description");
    assert(str_contains($html, 'property="og:url" content="https://multantech.pk'), "{$context}: Missing absolute HTTPS og:url");
    assert(str_contains($html, 'property="og:image" content="https://multantech.pk'), "{$context}: Missing absolute HTTPS og:image");
    assert(str_contains($html, 'property="og:image:width"'), "{$context}: Missing og:image:width");
    assert(str_contains($html, 'property="og:image:height"'), "{$context}: Missing og:image:height");
    assert(str_contains($html, 'property="og:image:alt"'), "{$context}: Missing og:image:alt");

    // Required Twitter tags
    assert(str_contains($html, '<meta name="twitter:card" content="summary_large_image">'), "{$context}: Missing twitter:card='summary_large_image'");
    assert(str_contains($html, 'name="twitter:title"'), "{$context}: Missing twitter:title");
    assert(str_contains($html, 'name="twitter:description"'), "{$context}: Missing twitter:description");
    assert(str_contains($html, 'name="twitter:image" content="https://multantech.pk'), "{$context}: Missing absolute HTTPS twitter:image");
}

// 1. Test Homepage OG Metadata
echo "1. Testing Homepage OG Metadata... ";
$homeController = new HomeController();
$homeHtml = $homeController->index()->getBody();

checkCommonOgTags($homeHtml, "Homepage");
assert(str_contains($homeHtml, '<meta property="og:type" content="website">'), "Homepage og:type must be 'website'");
assert(str_contains($homeHtml, '<meta property="og:url" content="https://multantech.pk">'), "Homepage og:url must be 'https://multantech.pk'");
assert(str_contains($homeHtml, '<meta property="og:image" content="https://multantech.pk/images/mta-og-image.jpg">'), "Homepage og:image must be /images/mta-og-image.jpg");
assert(str_contains($homeHtml, '<meta property="og:image:width" content="1200">'), "Homepage og:image:width must be 1200");
assert(str_contains($homeHtml, '<meta property="og:image:height" content="630">'), "Homepage og:image:height must be 630");
assert(str_contains($homeHtml, '<meta property="og:image:alt" content="Multan Tech Accessories">'), "Homepage og:image:alt must match");
echo "PASSED\n";

// 2. Test 5 Product Pages OG Metadata
echo "2. Testing Product Pages OG Metadata for 5 Sample Products...\n";
$products = $db->fetchAll("SELECT id, slug, title, primary_image_url FROM products WHERE is_active = 1 AND is_published = 1 LIMIT 5");
$productController = new ProductController();

foreach ($products as $idx => $p) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/products/' . $p['slug'];
    $req = new Request();
    $prodHtml = $productController->show($p['slug'], $req)->getBody();

    checkCommonOgTags($prodHtml, "Product #{$idx} [{$p['slug']}]");
    assert(str_contains($prodHtml, '<meta property="og:type" content="product">'), "Product #{$idx} og:type must be 'product'");
    assert(str_contains($prodHtml, '<meta property="og:url" content="https://multantech.pk/products/' . $p['slug'] . '">'), "Product #{$idx} og:url must be canonical");
    assert(str_contains($prodHtml, '<meta property="og:image" content="https://multantech.pk/images/og/product/' . $p['id'] . '.jpg">'), "Product #{$idx} og:image must be dedicated high-reliability endpoint");
    assert(str_contains($prodHtml, '<meta property="og:image:width" content="800">'), "Product #{$idx} og:image:width must be 800");
    assert(str_contains($prodHtml, '<meta property="og:image:height" content="800">'), "Product #{$idx} og:image:height must be 800");
    assert(str_contains($prodHtml, '<meta property="og:image:type" content="image/jpeg">'), "Product #{$idx} og:image:type must be image/jpeg");
    
    // Check that og:title has the actual title
    $escapedTitle = htmlspecialchars($p['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    assert(str_contains($prodHtml, 'property="og:title" content="' . $escapedTitle . '"') || str_contains($prodHtml, 'property="og:title" content="' . $p['title'] . '"'), "Product #{$idx} og:title must contain actual product name");

    echo "   -> Product #{$idx} [{$p['slug']}]: PASSED\n";
}

// 3. Test Category Page OG Metadata
echo "3. Testing Category Page OG Metadata... ";
$catController = new CategoryController();
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/categories/computer-accessories';
$catReq = new Request();
$catHtml = $catController->show('computer-accessories', $catReq)->getBody();
checkCommonOgTags($catHtml, "Category Page");
assert(str_contains($catHtml, '<meta property="og:type" content="website">'), "Category og:type must be 'website'");
assert(str_contains($catHtml, 'property="og:image" content="https://multantech.pk'), "Category must have absolute HTTPS image");
echo "PASSED\n";

// 4. Test Brand Page OG Metadata
echo "4. Testing Brand Page OG Metadata... ";
$brandController = new BrandController();
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/brands/havit';
$brandReq = new Request();
$brandHtml = $brandController->show('havit', $brandReq)->getBody();
checkCommonOgTags($brandHtml, "Brand Page");
assert(str_contains($brandHtml, '<meta property="og:type" content="website">'), "Brand og:type must be 'website'");
assert(str_contains($brandHtml, 'property="og:image" content="https://multantech.pk'), "Brand must have absolute HTTPS image");
echo "PASSED\n";

// 5. Test Buying Guide Page OG Metadata
echo "5. Testing Buying Guide Page OG Metadata... ";
$guideController = new GuideController();
$sampleGuide = $db->fetch("SELECT slug FROM posts WHERE status = 'published' LIMIT 1");
if ($sampleGuide) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/guides/' . $sampleGuide['slug'];
    $gReq = new Request();
    $gHtml = $guideController->show($sampleGuide['slug'], $gReq)->getBody();
    checkCommonOgTags($gHtml, "Guide Page");
    assert(str_contains($gHtml, '<meta property="og:type" content="article">'), "Guide og:type must be 'article'");
    assert(str_contains($gHtml, 'property="og:image" content="https://multantech.pk'), "Guide must have absolute HTTPS image");
}
echo "PASSED\n";

// 6. Test Static Legal / Informational Page OG Metadata
echo "6. Testing Static Legal Page (Disclaimer & Privacy) OG Metadata... ";
$pageController = new PageController();
$discHtml = $pageController->disclaimer()->getBody();
checkCommonOgTags($discHtml, "Disclaimer Page");
assert(str_contains($discHtml, '<meta property="og:image" content="https://multantech.pk/images/mta-og-image.jpg">'), "Disclaimer must use default MTA image");

$privHtml = $pageController->privacy()->getBody();
checkCommonOgTags($privHtml, "Privacy Page");
assert(str_contains($privHtml, '<meta property="og:image" content="https://multantech.pk/images/mta-og-image.jpg">'), "Privacy must use default MTA image");
echo "PASSED\n";

// 7. Test Physical File Existence & Dimensions
echo "7. Testing /images/mta-og-image.jpg File Integrity... ";
$physicalPath = MTA_ROOT . '/images/mta-og-image.jpg';
assert(file_exists($physicalPath), "Physical file /images/mta-og-image.jpg must exist");
$imgSize = getimagesize($physicalPath);
assert($imgSize[0] === 1200, "Image width must be 1200px, found {$imgSize[0]}");
assert($imgSize[1] === 630, "Image height must be 630px, found {$imgSize[1]}");
assert($imgSize['mime'] === 'image/jpeg', "Image mime must be image/jpeg");
assert(filesize($physicalPath) > 50000 && filesize($physicalPath) < 300000, "Image filesize must be optimal (50KB-300KB)");
echo "PASSED (1200x630 JPEG, " . round(filesize($physicalPath)/1024, 1) . " KB)\n";

echo "\n[ALL OPEN GRAPH & TWITTER CARD VERIFICATION TESTS PASSED SUCCESSFULLY!]\n";
