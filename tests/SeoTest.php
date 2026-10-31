<?php
/**
 * Multan Tech Accessories — Technical SEO Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Services\SeoService;
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
        echo "Testing Sitemaps: Index XML & sub-sitemaps output valid XML... ";
        $controller = new SeoController();
        
        $indexResp = $controller->index();
        assert($indexResp->getStatusCode() === 200);
        assert(str_contains($indexResp->getBody(), '<sitemapindex'));
        assert(str_contains($indexResp->getBody(), 'sitemap-products-1.xml'));

        $catResp = $controller->categories();
        assert($catResp->getStatusCode() === 200);
        assert(str_contains($catResp->getBody(), '<urlset'));
        assert(str_contains($catResp->getBody(), '<loc>'));

        echo "PASSED\n";
    }

    private function testStrictContentGateFilter(): void
    {
        echo "Testing Content Gate: Product Sitemap includes active catalog products... ";
        $db = Database::getInstance();

        // Count active products in DB
        $approvedCount = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE is_active = 1"
        );

        $controller = new SeoController();
        $prodResp = $controller->products();
        $xml = $prodResp->getBody();

        // Count occurrences of <url>
        $urlCount = substr_count($xml, '<url>');
        assert($urlCount === $approvedCount, "Sitemap URLs ({$urlCount}) must match active products in DB ({$approvedCount})");

        echo "PASSED ({$urlCount} active indexable products)\n";
    }
}

$test = new SeoTest();
$test->run();
