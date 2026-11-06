<?php
/**
 * Multan Tech Accessories — Technical SEO Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Services\SeoService;
use App\Services\SitemapService;
use App\Controllers\SeoController;

class SeoTest
{
    public function run(): void
    {
        echo "\n=== Running Technical SEO Tests ===\n";

        $this->testTitleLengthConstraint();
        $this->testMetaDescriptionFormatter();
        $this->testSitemapXmlGeneration();
        $this->testStrictContentGateFilter();

        echo "All Technical SEO tests passed successfully!\n";
    }

    private function testTitleLengthConstraint(): void
    {
        echo "Testing SEO Rule: <title> tag length strictly <= 60 characters... ";
        
        $shortTitle = 'Fast GaN Charger 65W';
        $formattedShort = SeoService::title($shortTitle);
        assert(mb_strlen($formattedShort) <= 60, "Title must be <= 60 chars. Got: " . mb_strlen($formattedShort));

        $veryLongTitle = 'Anker Prime 27650mAh Power Bank 250W 3-Port Ultra-Fast Portable Battery with Smart Digital Display for Laptops and Smartphones';
        $formattedLong = SeoService::title($veryLongTitle);
        assert(mb_strlen($formattedLong) <= 60, "Long title must be truncated to <= 60 chars. Got: " . mb_strlen($formattedLong));
        
        echo "PASSED (Length: " . mb_strlen($formattedLong) . " chars)\n";
    }

    private function testMetaDescriptionFormatter(): void
    {
        echo "Testing SEO Rule: Meta description formatting... ";
        $raw = 'Buy authentic GaN fast chargers in Pakistan at Multan Tech Accessories. 7-day checking warranty and same-day WhatsApp order dispatch across Multan, Lahore, Karachi and all cities.';
        $formatted = SeoService::metaDescription($raw);
        assert(mb_strlen($formatted) <= 160, "Meta description must be <= 160 chars. Got: " . mb_strlen($formatted));
        echo "PASSED\n";
    }

    private function testSitemapXmlGeneration(): void
    {
        echo "Testing Sitemaps: Index XML & sub-sitemaps output valid XML with canonical .pk domain... ";
        $controller = new SeoController();
        
        $indexResp = $controller->index();
        assert($indexResp->getStatusCode() === 200);
        assert(str_contains($indexResp->getBody(), '<sitemapindex'));
        assert(str_contains($indexResp->getBody(), 'https://multantech.pk/sitemap-products-1.xml'));
        assert(str_contains($indexResp->getBody(), 'https://multantech.pk/sitemap-categories.xml'));
        assert(str_contains($indexResp->getBody(), 'https://multantech.pk/sitemap-brands.xml'));
        assert(str_contains($indexResp->getBody(), 'https://multantech.pk/sitemap-guides.xml'));
        assert(str_contains($indexResp->getBody(), 'https://multantech.pk/sitemap-pages.xml'));
        assert(!str_contains($indexResp->getBody(), 'multantech.store'), "Sitemap index must not contain .store references");

        $catResp = $controller->categories();
        assert($catResp->getStatusCode() === 200);
        assert(str_contains($catResp->getBody(), '<urlset'));
        assert(str_contains($catResp->getBody(), 'https://multantech.pk/categories/'));
        assert(!str_contains($catResp->getBody(), 'multantech.store'), "Category sitemap must not contain .store references");

        $pageResp = $controller->pages();
        assert($pageResp->getStatusCode() === 200);
        assert(str_contains($pageResp->getBody(), '<urlset'));
        assert(str_contains($pageResp->getBody(), 'https://multantech.pk/sitemap'));

        echo "PASSED\n";
    }

    private function testStrictContentGateFilter(): void
    {
        echo "Testing Content Gate: Product Sitemaps chunking, image tags, and indexability rules... ";
        $db = Database::getInstance();

        // 1. Approve active in-stock products for testing
        $db->execute("
            UPDATE products 
            SET content_state = 'approved', updated_at = CURRENT_TIMESTAMP 
            WHERE is_active = 1 AND is_published = 1 AND status != 2
        ");

        // 2. Count expected indexable products (approved, active, published, status != 2)
        $expectedCount = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE is_active = 1 AND is_published = 1 AND content_state = 'approved' AND status != 2"
        );

        // 3. Generate sitemaps
        $report = SitemapService::generateAll();
        assert($report['status'] === 'success');

        $controller = new SeoController();
        $prod1Resp = $controller->products1();
        assert($prod1Resp->getStatusCode() === 200);
        $xml1 = $prod1Resp->getBody();

        // Check max 500 URLs per file
        $count1 = substr_count($xml1, '<url>');
        assert($count1 <= SitemapService::CHUNK_SIZE, "Chunk 1 must have <= 500 URLs. Got: {$count1}");
        assert(str_contains($xml1, '<image:image>'), "Product sitemap must contain <image:image> tags");
        assert(!str_contains($xml1, '<priority>'), "Sitemap must NOT contain deprecated <priority>");
        assert(!str_contains($xml1, '<changefreq>'), "Sitemap must NOT contain deprecated <changefreq>");

        $prod2Resp = $controller->products2();
        assert($prod2Resp->getStatusCode() === 200);
        $xml2 = $prod2Resp->getBody();
        $count2 = substr_count($xml2, '<url>');

        $totalProductUrls = $count1 + $count2;
        assert($totalProductUrls === $expectedCount, "Total product sitemap URLs ({$totalProductUrls}) must match approved in-stock products in DB ({$expectedCount})");

        // Out of stock product exclusion check
        $outOfStockInSitemap = false;
        $outOfStockSlugs = $db->fetchAll("SELECT slug FROM products WHERE status = 2 LIMIT 10");
        foreach ($outOfStockSlugs as $oos) {
            if (str_contains($xml1, '/products/' . $oos['slug']) || str_contains($xml2, '/products/' . $oos['slug'])) {
                $outOfStockInSitemap = true;
                break;
            }
        }
        assert(!$outOfStockInSitemap, "Out of stock products (status=2) must be excluded from XML sitemaps");

        echo "PASSED ({$totalProductUrls} approved indexable products across " . count($report['files']) . " files)\n";
    }
}

$test = new SeoTest();
$test->run();
