<?php
/**
 * Multan Tech Accessories — BrandController
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\SeoService;

class BrandController
{
    public function index(): Response
    {
        $db = Database::getInstance();
        $brands = $db->fetchAll("SELECT * FROM brands WHERE is_active = 1 ORDER BY name ASC");

        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Brands', 'url' => null],
        ];

        $html = View::render('storefront/brands', [
            'meta_title' => SeoService::title('Featured Hardware & Accessory Brands in Pakistan'),
            'meta_description' => 'Explore tech accessory brands available at Multan Tech Accessories. Anker, Baseus, Ugreen, A4Tech, Kingston & more with Cash on Delivery.',
            'canonical_url' => mta_url('/brands'),
            'brands' => $brands,
            'breadcrumbs' => $breadcrumbs,
        ]);

        $resp = new Response();
        return $resp->html($html);
    }

    public function show(string $slug, Request $request): Response
    {
        $db = Database::getInstance();
        $brand = $db->fetch("SELECT * FROM brands WHERE slug = ? AND is_active = 1 LIMIT 1", [$slug]);

        $resp = new Response();
        if (!$brand) {
            $resp->status(404);
            return $resp->html(View::render('storefront/errors/404', [
                'meta_title' => 'Brand Not Found — Multan Tech Accessories',
                'meta_description' => 'The requested brand was not found.',
                'noindex' => true,
            ]), 404);
        }

        $page = max(1, (int)$request->query('page', 1));
        $baseSql = "SELECT p.*, b.name AS brand_name 
                    FROM products p
                    JOIN brands b ON b.id = p.brand_id
                    WHERE p.brand_id = ? AND p.is_active = 1 AND p.is_published = 1
                    ORDER BY p.popularity_score DESC, p.id DESC";

        $pagination = $db->paginate($baseSql, [$brand['id']], $page, 24);

        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Brands', 'url' => '/brands'],
            ['label' => $brand['name'], 'url' => null],
        ];

        // Section 5 rule: Brand indexable only if at least 4 products and unique intro text
        $isIndexable = ($pagination['total'] >= 4) && !empty($brand['intro_html']);

        $metaTitle = SeoService::title("{$brand['name']} Accessories in Pakistan Price & Buy Online");
        $metaDescription = "Buy original {$brand['name']} accessories in Pakistan. Authentic chargers, cables, audio and computer gear with Cash on Delivery across Multan & nationwide.";

        $html = View::render('storefront/brand', [
            'brand' => $brand,
            'products' => $pagination['data'],
            'pagination' => $pagination,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => mta_url('/brands/' . $brand['slug']),
            'noindex' => !$isIndexable,
        ]);

        return $resp->html($html);
    }
}
