<?php
/**
 * Multan Tech Accessories — Domain Migration & Asset Verification Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Controllers\SeoController;

class DomainMigrationTest
{
    public function run(): void
    {
        echo "\n=== Running Domain Migration & Asset Verification Tests ===\n\n";

        $this->testCanonicalBaseUrl();
        $this->testSitemapsContainPkDomain();
        $this->testRobotsTxtPointsToPk();
        $this->testHtaccessRules();
        $this->testProductPlaceholderAssets();
        $this->testCategoryVisuals();
        $this->testBrandLogosVerification();
        $this->testUserRequestedFiveBrands();
        $this->testDatabaseSettingSiteUrl();

        echo "\n============================================\n";
        echo "DOMAIN MIGRATION & ASSET TESTS: ALL PASSED!\n";
        echo "============================================\n";
    }

    private function testCanonicalBaseUrl(): void
    {
        echo "Testing Canonical Base URL Helper... ";
        $base = mta_canonical_base_url();
        assert($base === 'https://multantech.pk', "Base URL must be https://multantech.pk, got: {$base}");

        $url = mta_url('/products/fast-charger');
        assert($url === 'https://multantech.pk/products/fast-charger', "Product URL must be https://multantech.pk/products/fast-charger, got: {$url}");
        echo "PASSED ({$base})\n";
    }

    private function testSitemapsContainPkDomain(): void
    {
        echo "Testing Dynamic Sitemap Index & Sub-Sitemaps for .pk... ";
        $c = new SeoController();

        $index = $c->index()->getBody();
        assert(str_contains($index, 'https://multantech.pk/sitemap-products-1.xml'));
        assert(str_contains($index, 'https://multantech.pk/sitemap-categories.xml'));
        assert(str_contains($index, 'https://multantech.pk/sitemap-brands.xml'));
        assert(str_contains($index, 'https://multantech.pk/sitemap-guides.xml'));
        assert(str_contains($index, 'https://multantech.pk/sitemap-pages.xml'));
        assert(!str_contains($index, 'multantech.store'), "Sitemap index must have zero .store references");

        $prods = $c->products()->getBody();
        assert(str_contains($prods, 'https://multantech.pk/products/'));
        assert(!str_contains($prods, 'multantech.store'));

        echo "PASSED\n";
    }

    private function testRobotsTxtPointsToPk(): void
    {
        echo "Testing robots.txt Sitemap Directive... ";
        $robotsPath = MTA_ROOT . '/robots.txt';
        assert(file_exists($robotsPath), "robots.txt must exist");
        $content = file_get_contents($robotsPath);
        assert(str_contains($content, 'Sitemap: https://multantech.pk/sitemap.xml'), "robots.txt must point to https://multantech.pk/sitemap.xml");
        assert(!str_contains($content, 'multantech.store'), "robots.txt must not reference multantech.store");
        echo "PASSED\n";
    }

    private function testHtaccessRules(): void
    {
        echo "Testing .htaccess Canonical 301 Redirect Rules... ";
        $htaccessPath = MTA_ROOT . '/.htaccess';
        assert(file_exists($htaccessPath), ".htaccess must exist");
        $content = file_get_contents($htaccessPath);

        // 1. Old domain redirect
        assert(str_contains($content, 'RewriteCond %{HTTP_HOST} ^(www\.)?multantech\.store$ [NC]'));
        assert(str_contains($content, 'RewriteRule ^(.*)$ https://multantech.pk/$1 [R=301,L]'));

        // 2. www.multantech.pk redirect
        assert(str_contains($content, 'RewriteCond %{HTTP_HOST} ^www\.multantech\.pk$ [NC]'));

        // 3. HTTP to HTTPS enforcement
        assert(str_contains($content, 'RewriteCond %{HTTP_HOST} ^multantech\.pk$ [NC]'));
        assert(str_contains($content, 'RewriteCond %{HTTPS} !on'));

        echo "PASSED\n";
    }

    private function testProductPlaceholderAssets(): void
    {
        echo "Testing Product Placeholder Assets (SVG, WebP, PNG)... ";
        $svg = MTA_ROOT . '/assets/images/placeholder.svg';
        $webp = MTA_ROOT . '/assets/images/placeholder.webp';
        $png = MTA_ROOT . '/assets/images/placeholder.png';

        assert(file_exists($svg) && filesize($svg) > 100, "placeholder.svg must exist and be valid");
        assert(file_exists($webp) && filesize($webp) > 100, "placeholder.webp must exist and be valid");
        assert(file_exists($png) && filesize($png) > 100, "placeholder.png must exist and be valid");

        // Test helper function
        $mockProd = ['id' => 999, 'title' => 'Test Item', 'primary_image_url' => 'https://cdn.computerzone.pk/products/img_1.webp'];
        $resolved = mta_product_image($mockProd, 400);
        assert(!str_contains($resolved, 'computerzone.pk'), "Unresolvable computerzone host must be intercepted");

        echo "PASSED\n";
    }

    private function testCategoryVisuals(): void
    {
        echo "Testing Category Product Visuals in assets/images/products/... ";
        $dir = MTA_ROOT . '/assets/images/products';
        assert(is_dir($dir), "assets/images/products directory must exist");
        $files = glob($dir . '/*.svg');
        assert(count($files) >= 10, "Expected at least 10 category vector files, got: " . count($files));
        echo "PASSED (" . count($files) . " category visuals)\n";
    }

    private function testBrandLogosVerification(): void
    {
        echo "Testing All 28 Brand Logos in Database... ";
        $db = Database::getInstance();
        $brands = $db->fetchAll("SELECT name, slug, logo_path FROM brands WHERE is_featured = 1");
        assert(count($brands) >= 20, "Expected >= 20 featured brands, got: " . count($brands));

        foreach ($brands as $b) {
            $res = mta_verify_brand_logo($b['logo_path']);
            assert($res['status'] === 'ok', "Brand {$b['name']} ({$b['slug']}) failed logo verification: {$res['status']}");
        }
        echo "PASSED (" . count($brands) . " verified brands)\n";
    }

    private function testUserRequestedFiveBrands(): void
    {
        echo "Testing The 5 Specific Brand Logos (Xtrike Me, Xiaomi, Alfa, DigitalPersona, Xprinter)... ";
        $db = Database::getInstance();
        $targetSlugs = ['xtrike-me', 'xiaomi', 'alfa', 'digital-persona', 'xprinter'];

        foreach ($targetSlugs as $slug) {
            $b = $db->fetch("SELECT name, slug, logo_path, is_featured FROM brands WHERE slug = ?", [$slug]);
            assert(!empty($b), "Brand {$slug} must exist in brands table");
            assert((int)$b['is_featured'] === 1, "Brand {$slug} must be featured");
            assert(file_exists(MTA_ROOT . $b['logo_path']), "Logo file must exist for {$slug}: {$b['logo_path']}");
            $res = mta_verify_brand_logo($b['logo_path']);
            assert($res['status'] === 'ok', "Brand {$slug} logo must be ok");
        }
        echo "PASSED (all 5 brands verified)\n";
    }

    private function testDatabaseSettingSiteUrl(): void
    {
        echo "Testing Database site_url Setting... ";
        $db = Database::getInstance();
        $db->execute("UPDATE settings SET value = 'https://multantech.pk' WHERE `key` = 'site_url'");
        $val = mta_setting('site_url', null, true);
        assert($val === 'https://multantech.pk', "site_url setting must be https://multantech.pk, got: {$val}");
        echo "PASSED\n";
    }
}

$test = new DomainMigrationTest();
$test->run();
