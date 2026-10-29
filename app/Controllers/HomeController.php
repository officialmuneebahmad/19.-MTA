<?php
/**
 * Multan Tech Accessories — HomeController
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Core\Response;
use App\Services\SeoService;

class HomeController
{
    public function index(): Response
    {
        $db = Database::getInstance();

        // Active Parent Categories
        $categories = $db->fetchAll(
            "SELECT id, slug, name, intro_html, product_count 
             FROM categories 
             WHERE parent_id IS NULL AND is_active = 1 
             ORDER BY sort_order ASC, name ASC"
        );

        // Best Sellers (Ranked by popularity_score, fallback to latest products)
        $bestSellers = $db->fetchAll(
            "SELECT p.*, b.name AS brand_name 
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.is_active = 1
             ORDER BY p.popularity_score DESC, p.id DESC
             LIMIT 12"
        );

        // New Arrivals
        $newArrivals = $db->fetchAll(
            "SELECT p.*, b.name AS brand_name 
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.is_active = 1
             ORDER BY p.id DESC
             LIMIT 12"
        );

        // Featured Brands
        $featuredBrands = $db->fetchAll(
            "SELECT id, slug, name FROM brands WHERE is_active = 1 ORDER BY name ASC LIMIT 16"
        );

        // Latest Guides
        $latestGuides = $db->fetchAll(
            "SELECT id, slug, title, excerpt, content_html FROM posts WHERE status = 'published' ORDER BY id DESC LIMIT 3"
        );

        // Homepage Structured Data (Organization + WebSite with SearchAction)
        $structuredData = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => mta_url('/#organization'),
                    'name' => 'Multan Tech Accessories',
                    'url' => mta_url(),
                    'logo' => mta_url('/assets/images/logo.png'),
                    'telephone' => '+923070655275',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => 'Al-Quresh Phase 3, Sher Shah Road',
                        'addressLocality' => 'Multan',
                        'addressRegion' => 'Punjab',
                        'addressCountry' => 'PK',
                    ],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => mta_url('/#website'),
                    'url' => mta_url(),
                    'name' => 'Multan Tech Accessories',
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => mta_url('/search?q={search_term_string}'),
                        'query-input' => 'required name=search_term_string',
                    ],
                ]
            ]
        ];

        $html = View::render('storefront/home', [
            'meta_title' => SeoService::title('Multan Tech Accessories — Authentic Tech & Accessories in Pakistan'),
            'meta_description' => 'Shop authentic computer and mobile accessories in Pakistan. GaN fast chargers, cables, speakers & gaming gear with Cash on Delivery across Multan & all cities.',
            'canonical_url' => mta_url(),
            'categories' => $categories,
            'bestSellers' => $bestSellers,
            'newArrivals' => $newArrivals,
            'featuredBrands' => $featuredBrands,
            'latestGuides' => $latestGuides,
            'structured_data' => $structuredData,
        ]);

        $resp = new Response();
        return $resp->html($html);
    }

    public function bestSellers(\App\Core\Request $request): Response
    {
        $db = Database::getInstance();
        $page = max(1, (int)$request->get('page', 1));
        $perPage = 24;
        $offset = ($page - 1) * $perPage;

        $countRow = $db->fetch(
            "SELECT COUNT(*) as total FROM products WHERE is_active = 1 AND is_published = 1"
        );
        $total = (int)($countRow['total'] ?? 0);
        $totalPages = max(1, (int)ceil($total / $perPage));

        $products = $db->fetchAll(
            "SELECT p.*, b.name AS brand_name 
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.is_active = 1 AND p.is_published = 1
             ORDER BY p.popularity_score DESC, p.id DESC
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Best Sellers', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/best-sellers', [
            'products' => $products,
            'breadcrumbs' => $breadcrumbs,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
            'meta_title' => SeoService::title('Best Selling Tech Accessories in Pakistan'),
            'meta_description' => 'Browse our top-rated, best-selling computer and smartphone accessories. Ranked by real Pakistani demand and customer satisfaction.',
            'canonical' => mta_url('/best-sellers'),
        ]));
    }
}
