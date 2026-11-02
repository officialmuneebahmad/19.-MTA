<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\View;

$pass = 0;
$fail = 0;

function check(bool $cond, string $msg): void {
    global $pass, $fail;
    if ($cond) {
        $pass++;
        echo " [PASS] {$msg}\n";
    } else {
        $fail++;
        echo " [FAIL] {$msg}\n";
    }
}

echo "=== STOREFRONT MOBILE RESPONSIVENESS & CONTENT TESTS ===\n\n";

// 1. About Us: 3-Day Checking Warranty
$aboutHtml = View::render('storefront/pages/about', ['breadcrumbs' => []]);
check(str_contains($aboutHtml, '3-Day Checking Warranty'), "About Us: Contains '3-Day Checking Warranty'");
check(!str_contains($aboutHtml, '7-Day Checking Warranty'), "About Us: '7-Day Checking Warranty' eradicated");

// 2. Contact Us: Responsive Grid
$contactHtml = View::render('storefront/pages/contact', ['breadcrumbs' => []]);
check(str_contains($contactHtml, 'class="contact-grid"'), "Contact Us: Uses .contact-grid class");
check(str_contains($contactHtml, 'class="contact-card"'), "Contact Us: Uses .contact-card class");
check(!str_contains($contactHtml, 'grid-template-columns: 1fr 1fr'), "Contact Us: Rigid inline 1fr 1fr removed");

// 3. All Tech Categories: Responsive Grid
$catHtml = View::render('storefront/categories', ['breadcrumbs' => [], 'categories' => [
    ['slug' => 'cables', 'name' => 'Cables', 'intro_html' => 'Cables intro', 'product_count' => 12]
]]);
check(str_contains($catHtml, 'class="categories-directory-grid"'), "Categories: Uses .categories-directory-grid");
check(str_contains($catHtml, 'class="category-directory-card"'), "Categories: Uses .category-directory-card");
check(!str_contains($catHtml, 'style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr))'), "Categories: Inline grid override removed");

// 4. Homepage: Domain Migration Notice below carousel
$homeHtml = View::render('storefront/home', [
    'categories' => [],
    'bestSellers' => [],
    'newArrivals' => [],
    'brands' => [],
    'featuredBrands' => [],
    'latestGuides' => [
        ['slug' => 'sample', 'title' => 'Sample Guide', 'content_html' => '<p>Content</p>', 'excerpt' => 'Excerpt']
    ],
]);
check(str_contains($homeHtml, 'class="domain-migration-banner"'), "Home: Contains .domain-migration-banner");
check(str_contains($homeHtml, 'multantech.store is now multantech.pk'), "Home: Contains highlighted 'multantech.store is now multantech.pk'");
check(str_contains($homeHtml, 'class="home-guides-grid"'), "Home: Guides section uses responsive .home-guides-grid");

// 5. App CSS Rules
$appCss = file_get_contents(MTA_ROOT . '/assets/css/app.css');
check(str_contains($appCss, '.domain-migration-banner'), "CSS: .domain-migration-banner defined");
check(str_contains($appCss, '.categories-directory-grid'), "CSS: .categories-directory-grid defined with mobile 1fr");
check(str_contains($appCss, '.contact-grid'), "CSS: .contact-grid defined with mobile 1fr");
check(str_contains($appCss, '.guides-grid'), "CSS: .guides-grid defined with mobile 1fr");

echo "\nTOTAL: " . ($pass + $fail) . " | PASSED: {$pass} | FAILED: {$fail}\n";

if ($fail > 0) {
    exit(1);
}
