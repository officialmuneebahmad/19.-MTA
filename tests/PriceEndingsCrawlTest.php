<?php
/**
 * Multan Tech Accessories — Site Crawl Price Endings Test (A5)
 * Crawls storefront routes and verifies that all product prices end in 99 or 999.
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Core\Request;
use App\Core\Router;

class PriceEndingsCrawlTest
{
    private Router $router;
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $router = new Router();
        require MTA_APP . '/routes.php';
        $this->router = $router;
    }

    public function run(): void
    {
        echo "\n=== Running Site-Wide Price Endings Crawl Test (A5) ===\n";

        // Routes to test
        $routes = [
            '/',
            '/best-sellers',
            '/categories',
            '/categories/mobile-accessories',
            '/categories/computer-accessories',
            '/categories/audio-and-speakers',
            '/categories/power-and-charging',
            '/categories/gaming',
            '/categories/networking',
            '/search?q=cable',
        ];

        // Sample product pages (first 5 active products)
        $sampleProducts = $this->db->fetchAll(
            "SELECT slug FROM products WHERE is_active = 1 AND is_published = 1 AND sell_price > 0 LIMIT 5"
        );
        foreach ($sampleProducts as $sp) {
            $routes[] = '/products/' . $sp['slug'];
        }

        $totalChecked = 0;
        $failedPages = [];

        foreach ($routes as $route) {
            echo "Crawling {$route}... ";
            $html = $this->fetchRoute($route);

            if (empty($html)) {
                throw new \RuntimeException("Route {$route} returned empty response");
            }

            // Find all product prices in HTML
            // Look for patterns like class="product-price">Rs. ... or mta_money outputs
            $invalidPrices = $this->inspectPricesInHtml($html);

            if (!empty($invalidPrices)) {
                $failedPages[$route] = $invalidPrices;
                echo "FAILED: Found non-99 product prices: " . implode(', ', $invalidPrices) . "\n";
            } else {
                $totalChecked++;
                echo "PASSED\n";
            }
        }

        if (!empty($failedPages)) {
            echo "\nPrice Endings Crawl Test FAILED on " . count($failedPages) . " pages.\n";
            exit(1);
        }

        echo "\nPrice Endings Crawl Test PASSED! All {$totalChecked} pages strictly display product prices ending in 99 or 999.\n";
    }

    private function fetchRoute(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $query = parse_url($uri, PHP_URL_QUERY);
        $queryParams = [];
        if ($query) {
            parse_str($query, $queryParams);
        }

        $request = new Request([], [], $queryParams, [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => $uri,
        ]);

        try {
            $response = $this->router->dispatch($request);
            return $response->getBody();
        } catch (\Throwable $e) {
            throw new \RuntimeException("Error dispatching {$uri}: " . $e->getMessage());
        }
    }

    private function inspectPricesInHtml(string $html): array
    {
        $invalid = [];

        // 1. Matches elements with class="product-price"
        if (preg_match_all('/class="product-price"[^>]*>\s*Rs\.(?:&nbsp;|\s)*([0-9,]+)/i', $html, $matches)) {
            foreach ($matches[1] as $priceStr) {
                $num = (int)str_replace(',', '', $priceStr);
                if ($num <= 0) continue;
                if (($num % 100) !== 99) {
                    $invalid[] = "Card Price: Rs. {$num}";
                }
            }
        }

        // 2. Matches product detail page main price display
        if (preg_match_all('/letter-spacing:-0\.5px;">\s*Rs\.(?:&nbsp;|\s)*([0-9,]+)/i', $html, $matches)) {
            foreach ($matches[1] as $priceStr) {
                $num = (int)str_replace(',', '', $priceStr);
                if ($num <= 0) continue;
                if (($num % 100) !== 99) {
                    $invalid[] = "Detail Price: Rs. {$num}";
                }
            }
        }

        // 3. Matches schema JSON-LD "price": "..."
        if (preg_match_all('/"price"\s*:\s*"([0-9.]+)"/i', $html, $matches)) {
            foreach ($matches[1] as $priceStr) {
                $num = (int)round((float)$priceStr);
                if ($num <= 0) continue;
                if (($num % 100) !== 99) {
                    $invalid[] = "Schema Price: {$num}";
                }
            }
        }

        return $invalid;
    }
}

$crawlTest = new PriceEndingsCrawlTest();
$crawlTest->run();
