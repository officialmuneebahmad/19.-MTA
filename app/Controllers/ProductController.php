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
            "SELECT p.*, b.name AS brand_name, b.slug AS brand_slug, b.logo_path AS brand_logo_path, b.logo_alt AS brand_logo_alt, c.name AS category_name, c.slug AS category_slug 
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

        $canonicalUrl = mta_url('/products/' . $product['slug']);
        $ogTitle = (string)$product['title'];
        $ogImageAlt = (string)$product['title'];

        // Build Clean Plaintext Product OG Description with Price & Store Mention
        $cleanDesc = trim(strip_tags((string)($product['short_description'] ?: ($product['description_html'] ?: ($product['supplier_description_html'] ?: '')))));
        $cleanDesc = preg_replace('/\s+/', ' ', $cleanDesc);
        $sellPriceNum = (float)($product['sell_price'] ?? 0);
        $priceText = $sellPriceNum > 0 ? 'Rs. ' . number_format($sellPriceNum) : '';

        if (!empty($cleanDesc) && mb_strlen($cleanDesc) > 20) {
            if (mb_strlen($cleanDesc) > 120) {
                $cleanDesc = mb_substr($cleanDesc, 0, 117) . '...';
            }
            if ($priceText) {
                $ogDescription = "{$cleanDesc} Price: {$priceText}. Available with Cash on Delivery at The Computer Shop Multan.";
            } else {
                $ogDescription = "{$cleanDesc} Available with Cash on Delivery at The Computer Shop Multan.";
            }
        } else {
            if ($priceText) {
                $ogDescription = "Buy authentic {$product['title']} at {$priceText} in Pakistan. 100% genuine products with Cash on Delivery at The Computer Shop Multan.";
            } else {
                $ogDescription = "Buy authentic {$product['title']} in Pakistan. 100% genuine products with Cash on Delivery at The Computer Shop Multan.";
            }
        }

        // Primary Product Image: Ensure Complete Absolute HTTPS URL
        $productOgImage = '';
        if (!empty($product['primary_image_url'])) {
            $img = trim((string)$product['primary_image_url']);
            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                $productOgImage = $img;
            } elseif (str_starts_with($img, '/')) {
                $productOgImage = mta_url($img);
            }
        }
        if (empty($productOgImage) && !empty($images[0]['url'])) {
            $img = trim((string)$images[0]['url']);
            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                $productOgImage = $img;
            } elseif (str_starts_with($img, '/')) {
                $productOgImage = mta_url($img);
            }
        }
        // Fallback to dedicated raster share image if missing or SVG
        if (empty($productOgImage) || str_ends_with(strtolower(parse_url($productOgImage, PHP_URL_PATH) ?? ''), '.svg')) {
            $productOgImage = mta_url('/assets/images/og-share.jpg');
        }

        $availability = match((int)($product['status'] ?? 1)) {
            1 => 'in stock',
            2 => 'out of stock',
            3 => 'preorder',
            default => 'in stock',
        };

        $html = View::render('storefront/product', [
            'product' => $product,
            'images' => $images,
            'relatedProducts' => $relatedProducts,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => $canonicalUrl,
            'noindex' => !$isIndexable,
            'product_id' => $product['id'],
            'structured_data' => $structuredData,
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_url' => $canonicalUrl,
            'og_image' => $productOgImage,
            'og_image_alt' => $ogImageAlt,
            'og_site_name' => 'The Computer Shop Multan',
            'og_type' => 'product',
            'product_price' => (float)($product['sell_price'] ?? 0),
            'product_availability' => $availability,
            'product_brand' => $product['brand_name'] ?? 'Multan Tech Accessories',
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
