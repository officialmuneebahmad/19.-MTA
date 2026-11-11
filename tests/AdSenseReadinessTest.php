<?php
/**
 * Multan Tech Accessories — AdSense Readiness, Privacy, Editorial SEO & Authenticity Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Core\Request;
use App\Controllers\HomeController;
use App\Controllers\CategoryController;
use App\Controllers\GuideController;
use App\Controllers\PageController;
use App\Services\SitemapService;

$pass = 0;
$fail = 0;

function t_assert(bool $cond, string $label): void {
    global $pass, $fail;
    if ($cond) {
        $pass++;
        echo " [PASS] {$label}\n";
    } else {
        $fail++;
        echo " [FAIL] {$label}\n";
    }
}

echo "=== ADSENSE READINESS, PRIVACY, EDITORIAL & AUTHENTICITY TESTS ===\n\n";

$db = Database::getInstance();

// 1. Authenticity Filter Tests
t_assert(
    mta_is_authentic_product_text('HP wireless keyboard mouse combo CS700 (High Copy)') === false,
    'Authenticity filter blocks HP High Copy combo'
);
t_assert(
    mta_is_authentic_product_text('Dell Wireless Mouse WM428 HIGH COPY') === false,
    'Authenticity filter blocks Dell High Copy mouse'
);
t_assert(
    mta_is_authentic_product_text('Apple Airpods Pro 2 Hengxuan(High Copy With Popup Msg) Black') === false,
    'Authenticity filter blocks AirPods High Copy'
);
t_assert(
    mta_is_authentic_product_text('Baseus 30W Master Copy Charger') === false,
    'Authenticity filter blocks Master Copy'
);
t_assert(
    mta_is_authentic_product_text('Wireless Earbuds', 'The product is Original. Quality is Best and Generic.') === false,
    'Authenticity filter blocks Quality is Best and Generic boilerplate'
);
t_assert(
    mta_is_authentic_product_text('SPEEDX SP-620 SIDE DIGITAL DISPLAY ADVANCED CASH COUNTER MACHINE WITH FAKE NOTE DEDUCTION', '<p>Automatic UV and MG counterfeit note detection and fake bill alert.</p>') === true,
    'Authenticity filter preserves legitimate cash counter with counterfeit note detection'
);
t_assert(
    mta_is_authentic_product_text('Ugreen Nexode 65W GaN Fast Charger') === true,
    'Authenticity filter allows genuine branded charger'
);
t_assert(
    mta_is_authentic_product_text('XTRIKE ME GP-903 RACING WHEEL WITH DUAL VIBRATION MOTORS REALISTIC GAMING EXPERIENCE', '<p>Ergonomic grip with imitation finish, vibration feedback and responsive pedals.</p>') === true,
    'Authenticity filter preserves XTRIKE ME racing wheel even if description mentions imitation finish'
);
t_assert(
    mta_is_authentic_product_text('Apple Magsafe Wireless Power Bank for Iphone 5000mAh 20W Fast Charging (Without Packing)', '<p>Master Copy Grade quality power bank.</p>') === false,
    'Authenticity filter blocks Master Copy in description'
);
t_assert(
    mta_is_authentic_product_text('Apple Airpods Pro 2 Hengxuan TC CHARGING ( LIKE ORIGINAL PACKING ) WHITE') === false &&
    mta_is_authentic_product_text('APPLE AIRPODS PRO 3 (3RD GENERATION) BT 5.3 WITH USB-C CHARGING') === false,
    'Authenticity filter blocks clone AirPods / like original packing listings'
);
t_assert(
    mta_is_authentic_product_text('3-in-1 15W Fast Wireless Magnetic Charging Pad for iPhone AirPods Smartwatch') === true,
    'Authenticity filter preserves genuine 3-in-1 wireless charger made for AirPods'
);

// 2. Consent Mode v2 Layout Fix & Privacy / Cookie Policy
$mainPhp = file_get_contents(MTA_APP . '/Views/layouts/main.php');
t_assert(
    str_contains($mainPhp, "localStorage.getItem('mta_cookie_consent_v2')"),
    'Layout main.php reads mta_cookie_consent_v2 matching cookie-consent.php'
);
$adsenseSnippetPos = strpos($mainPhp, 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6011306066788565');
$consentInitPos = strpos($mainPhp, "gtag('consent', 'default'");
t_assert(
    $adsenseSnippetPos !== false && $consentInitPos !== false && $consentInitPos < $adsenseSnippetPos && str_contains($mainPhp, 'crossorigin="anonymous"'),
    'Layout main.php embeds Google AdSense script ca-pub-6011306066788565 after Consent Mode v2 initialization'
);
t_assert(
    str_contains($mainPhp, '<meta name="referrer" content="strict-origin-when-cross-origin">'),
    'Layout main.php uses strict-origin-when-cross-origin referrer meta for AdSense & security compatibility'
);
$adsTxt = @file_get_contents(MTA_ROOT . '/ads.txt') ?: '';
t_assert(
    str_contains($adsTxt, 'google.com, pub-6011306066788565, DIRECT, f08c47fec0942fa0'),
    'Root ads.txt declares authorized Google AdSense seller pub-6011306066788565'
);

$pageCtrl = new PageController();
$privacyHtml = $pageCtrl->privacy()->getBody();
$cookieHtml = $pageCtrl->cookie()->getBody();
$aboutHtml = $pageCtrl->about()->getBody();
$faqHtml = $pageCtrl->faq()->getBody();
$shippingHtml = $pageCtrl->shipping()->getBody();
$termsHtml = $pageCtrl->terms()->getBody();
$editorialHtml = $pageCtrl->editorial()->getBody();

t_assert(str_contains($privacyHtml, 'Google AdSense') && str_contains($privacyHtml, 'ca-pub-6011306066788565') && !str_contains($privacyHtml, 'not currently active'), 'Privacy Policy accurately discloses Google AdSense ca-pub-6011306066788565 integration');
t_assert(str_contains($privacyHtml, 'https://policies.google.com/technologies/partner-sites'), 'Privacy Policy includes Google Partner Sites URL');
t_assert(str_contains($privacyHtml, 'https://adssettings.google.com/'), 'Privacy Policy includes Google Ads Settings URL');
t_assert(str_contains($privacyHtml, "Children") && str_contains($privacyHtml, 'Updates to This Policy'), 'Privacy Policy includes Childrens Privacy and Updates sections');
t_assert(str_contains($privacyHtml, 'Automatically Collected Information') && str_contains($privacyHtml, 'web beacons'), 'Privacy Policy covers automatically collected info and web beacons');
t_assert(str_contains($cookieHtml, 'mta_cookie_consent_v2') && !str_contains($cookieHtml, 'mta_cookie_consent_v1:'), 'Cookie Policy references mta_cookie_consent_v2');

// 3. No Contradictory Generic / Unverified Testing Copy
t_assert(!str_contains($faqHtml, 'Best and Generic'), 'FAQ removes contradictory Best and Generic claim');
t_assert(!str_contains($aboutHtml, 'thoroughly inspected') && !str_contains($aboutHtml, 'verified for real-world compatibility'), 'About Us removes unverified testing claim');
t_assert(!str_contains($editorialHtml, 'quality inspection margins'), 'Editorial Policy removes unverified inspection margins wording');

// 4. Rs. 100/- Advance Security Payment Consistency
foreach ([
    'About' => $aboutHtml,
    'FAQ' => $faqHtml,
    'Shipping' => $shippingHtml,
    'Terms' => $termsHtml,
] as $pageName => $html) {
    t_assert(
        str_contains($html, 'Rs. 100/- Advance Security Payment'),
        "{$pageName} states Rs. 100/- Advance Security Payment"
    );
    t_assert(
        !preg_match('/\b(no advance|without advance|refundable)\b/i', $html),
        "{$pageName} contains no contradictory advance/refundable claim"
    );
}

// 5. Empty Smart & CCTV Category Hiding Test (using temporary transaction/restore in SQLite)
$smartCat = $db->fetch("SELECT id FROM categories WHERE slug = 'smart-and-cctv' LIMIT 1");
if ($smartCat) {
    $smartId = (int)$smartCat['id'];
    $origProds = $db->fetchAll("SELECT id, is_active FROM products WHERE category_id = ?", [$smartId]);
    try {
        $db->execute("UPDATE products SET is_active = 0 WHERE category_id = ?", [$smartId]);
        t_assert(mta_category_has_products('smart-and-cctv') === false, 'mta_category_has_products returns false when smart-and-cctv has 0 active products');

        $homeCtrl = new HomeController();
        $homeHtml = $homeCtrl->index(new Request())->getBody();
        t_assert(!str_contains($homeHtml, '/categories/smart-and-cctv'), 'Homepage and header/drawer hide empty /categories/smart-and-cctv');
        t_assert(!str_contains($homeHtml, 'strict quality inspection'), 'Homepage removes unverified strict quality inspection claim');

        $catCtrl = new CategoryController();
        $smartResp = $catCtrl->show('smart-and-cctv', new Request());
        t_assert($smartResp->getStatusCode() === 200, 'Empty /categories/smart-and-cctv URL still resolves cleanly with HTTP 200');
        t_assert(str_contains($smartResp->getBody(), 'content="noindex, follow"'), 'Empty /categories/smart-and-cctv carries noindex, follow');

        SitemapService::generateAll();
        $seoCtrl = new \App\Controllers\SeoController();
        $catSitemap = $seoCtrl->categories()->getBody();
        t_assert(!str_contains($catSitemap, '/categories/smart-and-cctv'), 'sitemap-categories.xml excludes empty smart-and-cctv');
    } finally {
        foreach ($origProds as $op) {
            $db->execute("UPDATE products SET is_active = ? WHERE id = ?", [$op['is_active'], $op['id']]);
        }
        SitemapService::generateAll();
    }
}

// 6. Buying Guides Editorial Hub, Deduplication & Schema
$_GET = [];
$guideCtrl = new GuideController();
$guidesHtml = $guideCtrl->index(new Request())->getBody();
t_assert(!str_contains($guidesHtml, 'Field-tested hardware advice'), 'Guides index removes Field-tested claim');
t_assert(str_contains($guidesHtml, 'Browse Guides by Topic') && str_contains($guidesHtml, 'Explore Related Hardware Categories'), 'Guides index includes topic overview and internal category links');

$_GET = ['topic' => 'power-charging'];
$guidesFilteredHtml = $guideCtrl->index(new Request())->getBody();
$_GET = [];
t_assert(str_contains($guidesFilteredHtml, 'content="noindex') && str_contains($guidesFilteredHtml, 'href="https://multantech.pk/guides"'), 'Filtered /guides?topic=... carries noindex and canonical to /guides');

$guideShowHtml = $guideCtrl->show('fast-chargers-buying-guide-pakistan', new Request())->getBody();
t_assert(!str_contains($guideShowHtml, 'Verified Research'), 'Guide detail removes Verified Research label');
t_assert(str_contains($guideShowHtml, 'Related Buying Guides'), 'Guide detail includes Related Buying Guides');
t_assert(
    preg_match('/<script type="application\/ld\+json">(.*?"@type":\s*"BlogPosting".*?)<\/script>/s', $guideShowHtml, $ldMatch) === 1,
    'Guide detail renders BlogPosting JSON-LD'
);
if (!empty($ldMatch[1])) {
    $ld = json_decode($ldMatch[1], true);
    t_assert(!empty($ld['image']), 'BlogPosting JSON-LD includes image');
    t_assert(!empty($ld['articleSection']), 'BlogPosting JSON-LD includes articleSection');
    t_assert(!empty($ld['datePublished']) && !empty($ld['dateModified']), 'BlogPosting JSON-LD includes publish and modified dates');
}

// Check Published Guides count and no duplicate titles
$pubGuides = $db->fetchAll("SELECT id, slug, title FROM posts WHERE status = 'published'");
$titles = array_map(fn($r) => strtolower(trim((string)$r['title'])), $pubGuides);
t_assert(count($pubGuides) === 5 && count(array_unique($titles)) === 5, 'Database contains exactly 5 unique published buying guides (found ' . count($pubGuides) . ')');

echo "\n============================================\n";
echo "TOTAL: " . ($pass + $fail) . " | PASSED: {$pass} | FAILED: {$fail}\n";
echo "============================================\n";
exit($fail > 0 ? 1 : 0);
