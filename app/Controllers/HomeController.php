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

        // Self-healing: Zero out popularity for industrial spare parts
        try {
            $db->execute(
                "UPDATE products 
                 SET popularity_score = 0 
                 WHERE LOWER(title) LIKE '%pcb%' 
                    OR LOWER(title) LIKE '%board%' 
                    OR LOWER(title) LIKE '%thermal%' 
                    OR LOWER(title) LIKE '%printer%' 
                    OR LOWER(title) LIKE '%printhead%' 
                    OR LOWER(title) LIKE '%ribbon%' 
                    OR LOWER(title) LIKE '%roller%' 
                    OR LOWER(title) LIKE '%gear%'"
            );
        } catch (\Throwable) {}

        // Filter out boring spare parts and industrial replacement components
        $excludeSparePartsSql = " AND LOWER(p.title) NOT LIKE '%pcb%' 
                                  AND LOWER(p.title) NOT LIKE '%board%' 
                                  AND LOWER(p.title) NOT LIKE '%thermal%' 
                                  AND LOWER(p.title) NOT LIKE '%printer%' 
                                  AND LOWER(p.title) NOT LIKE '%printhead%' 
                                  AND LOWER(p.title) NOT LIKE '%ribbon%' 
                                  AND LOWER(p.title) NOT LIKE '%roller%' 
                                  AND LOWER(p.title) NOT LIKE '%gear%' 
                                  AND LOWER(p.title) NOT LIKE '%spare%' 
                                  AND LOWER(p.title) NOT LIKE '%motherboard%' 
                                  AND LOWER(p.title) NOT LIKE '%mechanism%' ";

        // Best Sellers (Keyboards, Mice, Speakers, Headsets, Gadgets, Chargers, Power Banks)
        $bestSellers = $db->fetchAll(
            "SELECT p.*, b.name AS brand_name 
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.is_active = 1 {$excludeSparePartsSql}
             ORDER BY 
               (CASE WHEN p.status = 1 THEN 1 WHEN p.status = 3 THEN 2 ELSE 3 END) ASC,
               (CASE 
                  WHEN LOWER(p.title) LIKE '%keyboard%' THEN 100
                  WHEN LOWER(p.title) LIKE '%mouse%' THEN 95
                  WHEN LOWER(p.title) LIKE '%speaker%' THEN 90
                  WHEN LOWER(p.title) LIKE '%headset%' OR LOWER(p.title) LIKE '%headphone%' THEN 85
                  WHEN LOWER(p.title) LIKE '%power bank%' THEN 80
                  WHEN LOWER(p.title) LIKE '%charger%' THEN 75
                  WHEN LOWER(p.title) LIKE '%gaming%' OR LOWER(p.title) LIKE '%controller%' THEN 70
                  WHEN LOWER(p.title) LIKE '%cable%' THEN 65
                  WHEN LOWER(p.title) LIKE '%wifi%' OR LOWER(p.title) LIKE '%router%' OR LOWER(p.title) LIKE '%repeater%' THEN 60
                  WHEN LOWER(p.title) LIKE '%microsd%' OR LOWER(p.title) LIKE '%flash drive%' OR LOWER(p.title) LIKE '%card%' THEN 55
                  ELSE 0
                END) DESC,
               p.popularity_score DESC,
               p.id DESC
             LIMIT 12"
        );

        // New Arrivals (Power banks, gaming accessories, cameras, cables, audio, gadgets)
        $newArrivals = $db->fetchAll(
            "SELECT p.*, b.name AS brand_name 
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.is_active = 1 {$excludeSparePartsSql}
             ORDER BY 
               (CASE WHEN p.status = 1 THEN 1 WHEN p.status = 3 THEN 2 ELSE 3 END) ASC,
               (CASE 
                  WHEN LOWER(p.title) LIKE '%power bank%' THEN 100
                  WHEN LOWER(p.title) LIKE '%gaming%' OR LOWER(p.title) LIKE '%controller%' THEN 95
                  WHEN LOWER(p.title) LIKE '%camera%' OR LOWER(p.title) LIKE '%cctv%' THEN 90
                  WHEN LOWER(p.title) LIKE '%cable%' THEN 85
                  WHEN LOWER(p.title) LIKE '%charger%' THEN 80
                  WHEN LOWER(p.title) LIKE '%audio%' OR LOWER(p.title) LIKE '%speaker%' THEN 75
                  WHEN LOWER(p.title) LIKE '%keyboard%' OR LOWER(p.title) LIKE '%mouse%' THEN 70
                  WHEN LOWER(p.title) LIKE '%wifi%' OR LOWER(p.title) LIKE '%router%' THEN 65
                  WHEN LOWER(p.title) LIKE '%microsd%' OR LOWER(p.title) LIKE '%flash drive%' THEN 60
                  ELSE 0
                END) DESC,
               p.id DESC
             LIMIT 12"
        );

        // Featured Brands (Strictly only brands that currently have active products in catalog, ordered by is_featured DESC, featured_sort ASC, product_count DESC)
        $featuredBrands = $db->fetchAll(
            "SELECT b.id, b.slug, b.name, b.logo_path, b.logo_dark_path, b.logo_alt, b.is_featured, b.featured_sort, COUNT(p.id) AS product_count 
             FROM brands b
             INNER JOIN products p ON p.brand_id = b.id AND p.is_active = 1
             WHERE b.is_active = 1
             GROUP BY b.id, b.slug, b.name, b.logo_path, b.logo_dark_path, b.logo_alt, b.is_featured, b.featured_sort
             HAVING COUNT(p.id) > 0
             ORDER BY b.is_featured DESC, b.featured_sort ASC, product_count DESC, b.name ASC
             LIMIT 18"
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

        $excludeSparePartsSql = " AND LOWER(p.title) NOT LIKE '%pcb%' 
                                  AND LOWER(p.title) NOT LIKE '%board%' 
                                  AND LOWER(p.title) NOT LIKE '%thermal%' 
                                  AND LOWER(p.title) NOT LIKE '%printer%' 
                                  AND LOWER(p.title) NOT LIKE '%printhead%' 
                                  AND LOWER(p.title) NOT LIKE '%ribbon%' 
                                  AND LOWER(p.title) NOT LIKE '%roller%' 
                                  AND LOWER(p.title) NOT LIKE '%gear%' 
                                  AND LOWER(p.title) NOT LIKE '%spare%' 
                                  AND LOWER(p.title) NOT LIKE '%motherboard%' 
                                  AND LOWER(p.title) NOT LIKE '%mechanism%' ";

        $countRow = $db->fetch(
            "SELECT COUNT(*) as total FROM products p WHERE p.is_active = 1 AND p.is_published = 1 {$excludeSparePartsSql}"
        );
        $total = (int)($countRow['total'] ?? 0);
        $totalPages = max(1, (int)ceil($total / $perPage));

        $products = $db->fetchAll(
            "SELECT p.*, b.name AS brand_name 
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.is_active = 1 AND p.is_published = 1 {$excludeSparePartsSql}
             ORDER BY 
               (CASE WHEN p.status = 1 THEN 1 WHEN p.status = 3 THEN 2 ELSE 3 END) ASC,
               (CASE 
                  WHEN LOWER(p.title) LIKE '%keyboard%' THEN 100
                  WHEN LOWER(p.title) LIKE '%mouse%' THEN 95
                  WHEN LOWER(p.title) LIKE '%speaker%' THEN 90
                  WHEN LOWER(p.title) LIKE '%headset%' OR LOWER(p.title) LIKE '%headphone%' THEN 85
                  WHEN LOWER(p.title) LIKE '%power bank%' THEN 80
                  WHEN LOWER(p.title) LIKE '%charger%' THEN 75
                  WHEN LOWER(p.title) LIKE '%gaming%' OR LOWER(p.title) LIKE '%controller%' THEN 70
                  WHEN LOWER(p.title) LIKE '%cable%' THEN 65
                  WHEN LOWER(p.title) LIKE '%wifi%' OR LOWER(p.title) LIKE '%router%' OR LOWER(p.title) LIKE '%repeater%' THEN 60
                  WHEN LOWER(p.title) LIKE '%microsd%' OR LOWER(p.title) LIKE '%flash drive%' THEN 55
                  ELSE 0
                END) DESC,
               p.popularity_score DESC,
               p.id DESC
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
