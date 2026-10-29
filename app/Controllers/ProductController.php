<?php
/**
 * Multan Tech Accessories — ProductController
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\SeoService;

class ProductController
{
    public function show(string $slug, Request $request): Response
    {
        $db = Database::getInstance();

        // Load Product
        $product = $db->fetch(
            "SELECT p.*, b.name AS brand_name, b.slug AS brand_slug, c.name AS category_name, c.slug AS category_slug 
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.slug = ? LIMIT 1",
            [$slug]
        );

        $resp = new Response();

        // 1. Check if product does not exist
        if (!$product) {
            $resp->status(404);
            return $resp->html(View::render('storefront/errors/404', [
                'meta_title' => 'Product Not Found — Multan Tech Accessories',
                'meta_description' => 'The requested accessory was not found.',
                'noindex' => true,
            ]), 404);
        }

        // 2. Check if product is deactivated (410 Gone with noindex)
        if (empty($product['is_active'])) {
            $resp->status(410);
            return $resp->html(View::render('storefront/product-410', [
                'product' => $product,
                'meta_title' => 'Discontinued Item — ' . $product['title'],
                'meta_description' => 'This product has been removed from our active catalog.',
                'noindex' => true,
            ]), 410);
        }

        // 3. Content Gate (Section 11): Only 'approved' products are indexable!
        $isIndexable = ($product['content_state'] === 'approved');

        // Load Gallery Images
        $images = $db->fetchAll(
            "SELECT url, alt_text FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC",
            [$product['id']]
        );

        // Related Products (Same category, up to 4)
        $relatedProducts = [];
        if (!empty($product['category_id'])) {
            $relatedProducts = $db->fetchAll(
                "SELECT p.*, b.name AS brand_name 
                 FROM products p
                 LEFT JOIN brands b ON b.id = p.brand_id
                 WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 AND p.is_published = 1
                 ORDER BY (CASE WHEN p.status = 1 THEN 1 WHEN p.status = 3 THEN 2 ELSE 3 END) ASC, p.popularity_score DESC, p.id DESC
                 LIMIT 4",
                [$product['category_id'], $product['id']]
            );
        }

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/'],
        ];
        if (!empty($product['category_name'])) {
            $breadcrumbs[] = ['label' => $product['category_name'], 'url' => '/categories/' . $product['category_slug']];
        }
        $breadcrumbs[] = ['label' => $product['title'], 'url' => null];

        // SEO Title (<= 60 chars) & Meta Description
        $metaTitle = SeoService::productTitle($product['title']);
        $metaDescription = SeoService::productMetaDescription($product);
        $structuredData = SeoService::productSchema($product, $images);

        $html = View::render('storefront/product', [
            'product' => $product,
            'images' => $images,
            'relatedProducts' => $relatedProducts,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => mta_url('/products/' . $product['slug']),
            'noindex' => !$isIndexable,
            'product_id' => $product['id'],
            'structured_data' => $structuredData,
        ]);

        return $resp->html($html);
    }

    /**
     * GET /api/search-suggestions?q=...
     * JSON suggestions endpoint for debounced header search
     */
    public function searchSuggestions(Request $request): Response
    {
        $q = trim((string)$request->query('q', ''));
        $resp = new Response();

        if (mb_strlen($q) < 2) {
            return $resp->json([]);
        }

        $db = Database::getInstance();
        $param = '%' . $q . '%';

        $items = $db->fetchAll(
            "SELECT id, slug, title, sell_price, primary_image_url 
             FROM products 
             WHERE is_active = 1 AND is_published = 1 AND (title LIKE ? OR sku LIKE ?)
             ORDER BY popularity_score DESC, id DESC
             LIMIT 6",
            [$param, $param]
        );

        return $resp->json($items);
    }
}
