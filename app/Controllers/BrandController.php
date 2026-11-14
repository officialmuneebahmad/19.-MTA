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
        $brands = $db->fetchAll(
            "SELECT b.*, COUNT(p.id) AS product_count 
             FROM brands b
             LEFT JOIN products p ON p.brand_id = b.id AND p.is_active = 1
             WHERE b.is_active = 1 AND p.id IS NOT NULL
             GROUP BY b.id
             ORDER BY b.featured_sort ASC, product_count DESC, b.name ASC"
        );

        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Brands', 'url' => null],
        ];

        $html = View::render('storefront/brands', [
            'meta_title' => SeoService::title('Featured Tech and Hardware Brands in Pakistan'),
            'meta_description' => 'Explore authentic tech accessory brands at Multan Tech Accessories. Original Anker, Baseus, Ugreen, A4Tech, Kingston and more with Cash on Delivery.',
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
                    ORDER BY (CASE WHEN p.status = 1 THEN 1 WHEN p.status = 3 THEN 2 ELSE 3 END) ASC, p.popularity_score DESC, p.id DESC";

        $pagination = $db->paginate($baseSql, [$brand['id']], $page, 24);

        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Brands', 'url' => '/brands'],
            ['label' => $brand['name'], 'url' => null],
        ];

        // Brand indexability: Brands with active products are indexable.
        $isIndexable = ($pagination['total'] > 0);

        // Ensure brand intro text exists so content is never thin
        if (empty(trim((string)($brand['intro_html'] ?? '')))) {
            $brand['intro_html'] = '<p>Shop authentic ' . e($brand['name']) . ' accessories at Multan Tech Accessories with nationwide Cash on Delivery in Pakistan.</p>';
        }

        // Self-referencing canonical URL including pagination (?page=N)
        $canonicalPage = $page > 1 ? '?page=' . $page : '';
        $canonicalUrl = mta_url('/brands/' . $brand['slug'] . $canonicalPage);

        $metaTitle = SeoService::title("{$brand['name']} Accessories in Pakistan");
        $metaDescription = SeoService::metaDescription("Buy original {$brand['name']} accessories in Pakistan. Authentic computer peripherals and mobile gear with Cash on Delivery nationwide.");

        $brandOgImage = null;
        if (!empty($brand['logo_path'])) {
            $brandOgImage = mta_url($brand['logo_path']);
        } elseif (!empty($pagination['data'][0])) {
            $brandOgImage = mta_product_image($pagination['data'][0], 800);
            if (str_starts_with($brandOgImage, '/')) {
                $brandOgImage = mta_url($brandOgImage);
            }
        }
        if ($brandOgImage && str_ends_with(strtolower(parse_url($brandOgImage, PHP_URL_PATH) ?? ''), '.svg')) {
            $brandOgImage = null;
        }

        $html = View::render('storefront/brand', [
            'brand' => $brand,
            'products' => $pagination['data'],
            'pagination' => $pagination,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => $canonicalUrl,
            'noindex' => !$isIndexable,
            'og_image' => $brandOgImage ?? mta_url('/assets/images/og-banner-wide.jpg'),
            'og_image_width' => 1200,
            'og_image_height' => 630,
            'og_site_name' => 'Multan Tech Accessories',
            'twitter_card' => 'summary_large_image',
        ]);

        return $resp->html($html);
    }
}
