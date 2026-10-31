<?php
/**
 * Multan Tech Accessories — SeoController
 * Dynamic XML Sitemaps with strict content gate filters & real lastmod
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Response;

class SeoController
{
    public function index(): Response
    {
        $baseUrl = rtrim(mta_config('app.url', 'https://multantech.store'), '/');
        $now = date('Y-m-d');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        $subSitemaps = [
            '/sitemap-products-1.xml',
            '/sitemap-categories.xml',
            '/sitemap-brands.xml',
            '/sitemap-guides.xml',
            '/sitemap-pages.xml',
        ];

        foreach ($subSitemaps as $map) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>{$baseUrl}{$map}</loc>\n";
            $xml .= "    <lastmod>{$now}</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';

        $resp = new Response();
        $resp->header('Content-Type', 'application/xml; charset=utf-8');
        $resp->body($xml);
        return $resp;
    }

    public function products(): Response
    {
        $baseUrl = rtrim(mta_config('app.url', 'https://multantech.store'), '/');
        $db = Database::getInstance();

        // Critical SEO Gate: Only active, publicly accessible products
        $products = $db->fetchAll(
            "SELECT slug, updated_at, created_at 
             FROM products 
             WHERE is_active = 1
             ORDER BY id ASC"
        );

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($products as $p) {
            $lastmod = date('Y-m-d', strtotime($p['updated_at'] ?? $p['created_at']));
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/products/" . htmlspecialchars($p['slug'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        $resp = new Response();
        $resp->header('Content-Type', 'application/xml; charset=utf-8');
        $resp->body($xml);
        return $resp;
    }

    public function categories(): Response
    {
        $baseUrl = rtrim(mta_config('app.url', 'https://multantech.store'), '/');
        $db = Database::getInstance();

        $categories = $db->fetchAll(
            "SELECT slug, updated_at, created_at 
             FROM categories 
             WHERE is_active = 1
             ORDER BY id ASC"
        );

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($categories as $c) {
            $lastmod = date('Y-m-d', strtotime($c['updated_at'] ?? $c['created_at']));
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/categories/" . htmlspecialchars($c['slug'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.9</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        $resp = new Response();
        $resp->header('Content-Type', 'application/xml; charset=utf-8');
        $resp->body($xml);
        return $resp;
    }

    public function brands(): Response
    {
        $baseUrl = rtrim(mta_config('app.url', 'https://multantech.store'), '/');
        $db = Database::getInstance();

        $brands = $db->fetchAll(
            "SELECT slug, updated_at, created_at 
             FROM brands 
             WHERE is_active = 1
             ORDER BY id ASC"
        );

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($brands as $b) {
            $lastmod = date('Y-m-d', strtotime($b['updated_at'] ?? $b['created_at']));
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/brands/" . htmlspecialchars($b['slug'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>monthly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        $resp = new Response();
        $resp->header('Content-Type', 'application/xml; charset=utf-8');
        $resp->body($xml);
        return $resp;
    }

    public function guides(): Response
    {
        $baseUrl = rtrim(mta_config('app.url', 'https://multantech.store'), '/');
        $db = Database::getInstance();

        $posts = $db->fetchAll(
            "SELECT slug, updated_at, created_at 
             FROM posts 
             WHERE status = 'published'
             ORDER BY id ASC"
        );

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($posts as $post) {
            $lastmod = date('Y-m-d', strtotime($post['updated_at'] ?? $post['created_at']));
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}/guides/" . htmlspecialchars($post['slug'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>monthly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        $resp = new Response();
        $resp->header('Content-Type', 'application/xml; charset=utf-8');
        $resp->body($xml);
        return $resp;
    }

    public function pages(): Response
    {
        $baseUrl = rtrim(mta_config('app.url', 'https://multantech.store'), '/');
        $today = date('Y-m-d');

        $staticPages = [
            ['path' => '/', 'freq' => 'daily', 'prio' => '1.0'],
            ['path' => '/best-sellers', 'freq' => 'daily', 'prio' => '0.9'],
            ['path' => '/categories', 'freq' => 'weekly', 'prio' => '0.9'],
            ['path' => '/brands', 'freq' => 'weekly', 'prio' => '0.8'],
            ['path' => '/guides', 'freq' => 'weekly', 'prio' => '0.8'],
            ['path' => '/author/muneeb-ahmad', 'freq' => 'monthly', 'prio' => '0.7'],
            ['path' => '/about', 'freq' => 'monthly', 'prio' => '0.6'],
            ['path' => '/contact', 'freq' => 'monthly', 'prio' => '0.7'],
            ['path' => '/shipping-and-delivery', 'freq' => 'monthly', 'prio' => '0.6'],
            ['path' => '/returns-and-refunds', 'freq' => 'monthly', 'prio' => '0.6'],
            ['path' => '/editorial-and-pricing-policy', 'freq' => 'monthly', 'prio' => '0.6'],
            ['path' => '/faq', 'freq' => 'monthly', 'prio' => '0.6'],
            ['path' => '/sitemap', 'freq' => 'weekly', 'prio' => '0.5'],
            ['path' => '/privacy-policy', 'freq' => 'monthly', 'prio' => '0.4'],
            ['path' => '/terms-and-conditions', 'freq' => 'monthly', 'prio' => '0.4'],
            ['path' => '/cookie-policy', 'freq' => 'monthly', 'prio' => '0.4'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($staticPages as $page) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}{$page['path']}</loc>\n";
            $xml .= "    <lastmod>{$today}</lastmod>\n";
            $xml .= "    <changefreq>{$page['freq']}</changefreq>\n";
            $xml .= "    <priority>{$page['prio']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        $resp = new Response();
        $resp->header('Content-Type', 'application/xml; charset=utf-8');
        $resp->body($xml);
        return $resp;
    }
}
