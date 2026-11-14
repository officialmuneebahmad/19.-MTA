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

        // 3. All active and published catalog products are indexable in Google Search
        $isIndexable = !empty($product['is_active']) && !empty($product['is_published']);

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
        $rawProductDesc = (string)($product['short_description'] ?: ($product['description_html'] ?: ($product['supplier_description_html'] ?: '')));
        $cleanDesc = trim(strip_tags(mta_clean_description_html($rawProductDesc, (string)($product['title'] ?? ''))));
        $cleanDesc = preg_replace('/\s+/', ' ', $cleanDesc);
        $sellPriceNum = (float)($product['sell_price'] ?? 0);
        $priceText = $sellPriceNum > 0 ? 'Rs. ' . number_format($sellPriceNum) : '';

        if (!empty($cleanDesc) && mb_strlen($cleanDesc) > 20) {
            if (mb_strlen($cleanDesc) > 120) {
                $cleanDesc = mb_substr($cleanDesc, 0, 117) . '...';
            }
            if ($priceText) {
                $ogDescription = "{$cleanDesc} Price: {$priceText}. Available with Cash on Delivery at Multan Tech Accessories.";
            } else {
                $ogDescription = "{$cleanDesc} Available with Cash on Delivery at Multan Tech Accessories.";
            }
        } else {
            if ($priceText) {
                $ogDescription = "Buy authentic {$product['title']} at {$priceText} in Pakistan. 100% genuine products with Cash on Delivery at Multan Tech Accessories.";
            } else {
                $ogDescription = "Buy authentic {$product['title']} in Pakistan. 100% genuine products with Cash on Delivery at Multan Tech Accessories.";
            }
        }

        // Dedicated High-Reliability Product Open Graph Image Endpoint on multantech.pk
        // Guaranteed 800x800 JPEG (<100KB), avoiding cross-domain blocking, timeouts and WebP incompatibility on WhatsApp
        $productOgImage = mta_url('/api/og-image/product/' . (int)$product['id'] . '.jpg');

        $availability = match((int)($product['status'] ?? 1)) {
            1 => 'in stock',
            2 => 'out of stock',
            3 => 'preorder',
            default => 'in stock',
        };

        $faqs = SeoService::productFaqs($product);

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
            'faqs' => $faqs,
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_url' => $canonicalUrl,
            'og_image' => $productOgImage,
            'og_image_alt' => $ogImageAlt,
            'og_image_width' => 800,
            'og_image_height' => 800,
            'twitter_card' => 'summary',
            'og_site_name' => 'Multan Tech Accessories',
            'og_type' => 'product',
            'product_price' => (float)($product['sell_price'] ?? 0),
            'product_availability' => $availability,
            'product_brand' => $product['brand_name'] ?? 'Multan Tech Accessories',
        ]);

        return $resp->html($html);
    }

    /**
     * GET /api/og-image/product/{id}
     * High-reliability Open Graph JPEG generator & cache for WhatsApp / Facebook / Twitter
     */
    public function ogImage(string|int $id = '', ?Request $request = null): Response
    {
        $productId = (int)preg_replace('/[^0-9]/', '', (string)$id);
        $resp = new Response();

        $cacheDir = MTA_ROOT . '/assets/cache/og';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $cacheFile = $cacheDir . '/product-' . $productId . '.jpg';

        // 1. Serve cached file if valid and not empty
        if (file_exists($cacheFile) && filesize($cacheFile) > 1000) {
            $this->streamImage($cacheFile);
        }

        // 2. Fetch product info
        $db = Database::getInstance();
        $product = $productId > 0 ? $db->fetch(
            "SELECT p.id, p.title, p.primary_image_url, p.image_800, p.image_400, c.slug AS category_slug 
             FROM products p 
             LEFT JOIN categories c ON c.id = p.category_id 
             WHERE p.id = ? LIMIT 1",
            [$productId]
        ) : null;

        $fallbackBanner = MTA_ROOT . '/assets/images/og-banner-wide.jpg';

        if (!$product) {
            $this->streamImage($fallbackBanner);
        }

        // 3. Resolve source image URL
        $rawUrl = trim((string)($product['primary_image_url'] ?? ''));
        if (str_contains($rawUrl, 'cdn.computerzone.pk')) {
            $rawUrl = '';
        }
        if ($rawUrl === '' && !empty($product['image_800'])) {
            $rawUrl = trim((string)$product['image_800']);
        }
        if ($rawUrl === '' && !empty($product['image_400'])) {
            $rawUrl = trim((string)$product['image_400']);
        }

        // If local relative path
        if ($rawUrl !== '' && str_starts_with($rawUrl, '/')) {
            $localPath = MTA_ROOT . $rawUrl;
            if (file_exists($localPath)) {
                $rawUrl = 'file://' . $localPath;
            } else {
                $rawUrl = mta_url($rawUrl);
            }
        }

        // 4. Download and process image
        $srcImage = null;
        if ($rawUrl !== '' && (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://'))) {
            $ctx = stream_context_create([
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
                'http' => [
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'timeout' => 4,
                ]
            ]);
            $imageData = @file_get_contents($rawUrl, false, $ctx);
            if ($imageData && strlen($imageData) > 500) {
                $srcImage = @imagecreatefromstring($imageData);
            }
        } elseif ($rawUrl !== '' && str_starts_with($rawUrl, 'file://')) {
            $filePath = substr($rawUrl, 7);
            if (file_exists($filePath)) {
                $imageData = @file_get_contents($filePath);
                if ($imageData) {
                    $srcImage = @imagecreatefromstring($imageData);
                }
            }
        }

        // 5. Create 800x800 square truecolor canvas
        if ($srcImage) {
            $srcW = imagesx($srcImage);
            $srcH = imagesy($srcImage);
            if ($srcW > 10 && $srcH > 10) {
                $targetW = 800;
                $targetH = 800;
                $dst = imagecreatetruecolor($targetW, $targetH);
                $white = imagecolorallocate($dst, 255, 255, 255);
                imagefill($dst, 0, 0, $white);

                // Preserve aspect ratio with 40px safe margin (720x720 max inner)
                $scale = min(720 / $srcW, 720 / $srcH);
                $newW = (int)round($srcW * $scale);
                $newH = (int)round($srcH * $scale);
                $posX = (int)round(($targetW - $newW) / 2);
                $posY = (int)round(($targetH - $newH) / 2);

                imagecopyresampled($dst, $srcImage, $posX, $posY, 0, 0, $newW, $newH, $srcW, $srcH);
                imagejpeg($dst, $cacheFile, 88);
                imagedestroy($dst);
                imagedestroy($srcImage);

                if (file_exists($cacheFile) && filesize($cacheFile) > 1000) {
                    $this->streamImage($cacheFile);
                }
            }
            imagedestroy($srcImage);
        }

        // 6. Fallback if source image fetch or render failed
        $this->streamImage($fallbackBanner);
        return $resp;
    }

    /**
     * Direct streaming helper with optimal caching and CORS headers
     */
    private function streamImage(string $filePath): void
    {
        if (!file_exists($filePath)) {
            $filePath = MTA_ROOT . '/assets/images/og-banner-wide.jpg';
        }
        if (file_exists($filePath)) {
            header('Content-Type: image/jpeg');
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: public, max-age=604800, immutable');
            header('Access-Control-Allow-Origin: *');
            header('X-Content-Type-Options: nosniff');
            readfile($filePath);
            exit;
        }
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
