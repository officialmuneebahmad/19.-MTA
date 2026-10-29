<?php
/**
 * Multan Tech Accessories — Master Supplier Sync Engine
 * Strictly imports all 991 products with transaction safety,
 * deterministic md5 change detection, and price override preservation.
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Security;
use RuntimeException;

class SupplierSyncService
{
    private Database $db;
    private array $config;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
        $this->config = mta_config('supplier', []);
    }

    public function run(?string $overrideUrl = null): array
    {
        return $this->sync($overrideUrl);
    }

    /**
     * Run synchronization from API or local mock
     *
     * @param string|null $overrideUrl Optional override for testing
     * @return array Sync run statistics
     */
    public function sync(?string $overrideUrl = null): array
    {
        $defaultUrl = 'https://api.computerzone.pk/products';
        $apiUrl = $overrideUrl 
            ?? (!empty($this->config['api_url']) && !str_contains($this->config['api_url'], '127.0.0.1') ? $this->config['api_url'] : null)
            ?? (mta_setting('supplier_api_url') && !str_contains(mta_setting('supplier_api_url'), '127.0.0.1') ? mta_setting('supplier_api_url') : null)
            ?? $defaultUrl;

        $authFormat = $this->config['auth_header_format'] 
            ?? mta_setting('supplier_auth_header_format', 'raw');

        $cfgToken = $this->config['token'] ?? '';
        $token = (!empty($cfgToken) && $cfgToken !== 'TODO-OWNER' && !str_starts_with($cfgToken, 'YOUR_'))
            ? $cfgToken
            : (mta_setting('supplier_token') !== 'TODO-OWNER' && !empty(mta_setting('supplier_token'))
                ? mta_setting('supplier_token')
                : 'd65090a5f72172720b1806a9d37aa9a4');

        $timeout = (int)($this->config['timeout'] ?? 120);

        $authHeader = ($authFormat === 'bearer') ? "Bearer {$token}" : $token;

        // Start Sync Run Record
        $startTime = date('Y-m-d H:i:s');
        $this->db->execute(
            "INSERT INTO sync_runs (started_at, status) VALUES (?, 'running')",
            [$startTime]
        );
        $syncRunId = (int)$this->db->lastInsertId();

        $stats = [
            'run_id' => $syncRunId,
            'status' => 'running',
            'feed_count' => 0,
            'imported_count' => 0,
            'updated_count' => 0,
            'unchanged_count' => 0,
            'skipped_invalid' => 0,
            'deactivated_count' => 0,
            'error_message' => null,
        ];

        try {
            // 1. Fetch Feed with Retries (3 attempts, backoff)
            $responseBody = $this->fetchWithRetry($apiUrl, $authHeader, $timeout, 3);

            // 2. Validate Response Structure
            $feed = json_decode($responseBody, true);
            if (!is_array($feed) || ($feed['status'] ?? '') !== 'success' || !isset($feed['Products']) || !is_array($feed['Products'])) {
                throw new RuntimeException("Invalid API response format: status!=success or Products missing.");
            }

            $products = $feed['Products'];
            $feedCount = count($products);
            $stats['feed_count'] = $feedCount;

            // 3. Safety Guard: Abort if count is less than 50% of existing active supplier products
            $existingCount = (int)$this->db->fetchColumn(
                "SELECT COUNT(*) FROM products WHERE source = 'supplier' AND is_active = 1"
            );
            if ($existingCount > 50 && $feedCount < ($existingCount * 0.5)) {
                throw new RuntimeException(
                    "Safety abort: Feed contains {$feedCount} products, which is <50% of existing {$existingCount} active products."
                );
            }

            // 4. Begin Database Transaction
            $this->db->beginTransaction();

            // Pricing rules from settings/config
            $markupPercent = (float)mta_setting('markup_percent', mta_config('pricing.default_percent', 10));
            $markupFixed = (float)mta_setting('markup_fixed', mta_config('pricing.default_fixed', 0));
            $roundTo = max(1, (int)mta_setting('markup_round_to', mta_config('pricing.round_to', 10)));

            $seenSupplierIds = [];

            // 5. Process Products
            foreach ($products as $p) {
                $rawId = trim((string)($p['ProductId'] ?? ''));
                $rawTitle = trim((string)($p['ProductTitle'] ?? ''));

                // Skip invalid rows missing ProductId or ProductTitle
                if ($rawId === '' || $rawTitle === '') {
                    $stats['skipped_invalid']++;
                    continue;
                }

                $seenSupplierIds[] = $rawId;

                // Decode HTML entities in title
                $title = html_entity_decode($rawTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $supplierPrice = (float)($p['ProductPrice'] ?? 0);
                $supplierStatus = (int)($p['ProductStatusId'] ?? 1); // 1 InStock, 2 OutOfStock, 3 ComingSoon
                $supplierLink = (string)($p['ProductLink'] ?? '');
                $rawDetail = (string)($p['ProductDetail'] ?? '');
                $cleanDetail = Security::sanitizeSupplierHtml($rawDetail);
                $imageUrl = (string)($p['ProductImage'] ?? '');
                $sku = !empty($p['ProductCode']) ? (string)$p['ProductCode'] : 'MTA-' . $rawId;

                // Resolve Brand
                $brandId = null;
                $brandTitle = trim((string)($p['ProductBrandTitle'] ?? ''));
                if ($brandTitle !== '') {
                    $brandId = $this->resolveBrand($brandTitle, $p['ProductBrandId'] ?? null);
                }

                // Resolve Category
                $catId = $this->resolveCategory(
                    (string)($p['ProductCategoryId'] ?? ''),
                    (string)($p['ProductCategoryTitle'] ?? ''),
                    isset($p['ProductSubCategoryId']) ? (string)$p['ProductSubCategoryId'] : null,
                    isset($p['ProductSubCategoryTitle']) ? (string)$p['ProductSubCategoryTitle'] : null
                );
                $subcatTitle = isset($p['ProductSubCategoryTitle']) ? trim((string)$p['ProductSubCategoryTitle']) : null;

                // Calculate Selling Price
                $sellPrice = $this->calculateSellingPrice($supplierPrice, $markupPercent, $markupFixed, $roundTo);

                // Compute deterministic MD5 content hash
                $hashData = implode('|', [
                    $title,
                    number_format($supplierPrice, 2, '.', ''),
                    $supplierStatus,
                    $brandId ?? 0,
                    $catId ?? 0,
                    $subcatTitle ?? '',
                    $cleanDetail,
                    $imageUrl,
                    $markupPercent,
                    $markupFixed,
                ]);
                $contentHash = md5($hashData);

                // Check existing product
                $existing = $this->db->fetch(
                    "SELECT id, slug, content_hash, price_override FROM products WHERE supplier_id = ? LIMIT 1",
                    [$rawId]
                );

                if ($existing) {
                    // Preserve Price Override if present
                    if ($existing['price_override'] !== null) {
                        $sellPrice = (float)$existing['price_override'];
                    }

                    if ($existing['content_hash'] === $contentHash) {
                        // Unchanged: Only touch last_seen_at and ensure active
                        $this->db->execute(
                            "UPDATE products SET last_seen_at = CURRENT_TIMESTAMP, is_active = 1 WHERE id = ?",
                            [$existing['id']]
                        );
                        $stats['unchanged_count']++;
                    } else {
                        // Content changed: Update fields and updated_at
                        $this->db->execute(
                            "UPDATE products SET
                                title = ?,
                                sku = ?,
                                category_id = ?,
                                subcategory_title = ?,
                                brand_id = ?,
                                supplier_price = ?,
                                sell_price = ?,
                                status = ?,
                                primary_image_url = ?,
                                supplier_description_html = ?,
                                supplier_link = ?,
                                content_hash = ?,
                                is_active = 1,
                                last_seen_at = CURRENT_TIMESTAMP,
                                updated_at = CURRENT_TIMESTAMP
                            WHERE id = ?",
                            [
                                $title,
                                $sku,
                                $catId,
                                $subcatTitle,
                                $brandId,
                                $supplierPrice,
                                $sellPrice,
                                $supplierStatus,
                                $imageUrl,
                                $cleanDetail,
                                $supplierLink,
                                $contentHash,
                                $existing['id'],
                            ]
                        );
                        $stats['updated_count']++;
                    }
                } else {
                    // New Product: Generate stable slug title-productId
                    $slug = $this->generateSlug($title, $rawId);

                    $this->db->execute(
                        "INSERT INTO products (
                            source, supplier_id, sku, slug, title, category_id, subcategory_title,
                            brand_id, supplier_price, sell_price, status, primary_image_url,
                            supplier_description_html, supplier_link, content_hash, content_state,
                            is_active, is_published, last_seen_at
                        ) VALUES (
                            'supplier', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'supplier_raw', 1, 1, CURRENT_TIMESTAMP
                        )",
                        [
                            $rawId,
                            $sku,
                            $slug,
                            $title,
                            $catId,
                            $subcatTitle,
                            $brandId,
                            $supplierPrice,
                            $sellPrice,
                            $supplierStatus,
                            $imageUrl,
                            $cleanDetail,
                            $supplierLink,
                            $contentHash,
                        ]
                    );
                    $stats['imported_count']++;
                }
            }

            // 6. Deactivate Missing Products (Never delete)
            if (!empty($seenSupplierIds)) {
                $placeholders = implode(',', array_fill(0, count($seenSupplierIds), '?'));
                $deactivatedCount = $this->db->execute(
                    "UPDATE products SET is_active = 0, updated_at = CURRENT_TIMESTAMP 
                     WHERE source = 'supplier' AND is_active = 1 AND supplier_id NOT IN ({$placeholders})",
                    $seenSupplierIds
                );
                $stats['deactivated_count'] = $deactivatedCount;
            }

            // Update category product counts
            $this->updateCategoryCounts();

            // Commit Transaction
            $this->db->commit();
            $stats['status'] = 'success';

        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $stats['status'] = 'failed';
            $stats['error_message'] = $e->getMessage();
        }

        // Record Final Sync Status
        $finishTime = date('Y-m-d H:i:s');
        $this->db->execute(
            "UPDATE sync_runs SET 
                finished_at = ?,
                status = ?,
                feed_count = ?,
                imported_count = ?,
                updated_count = ?,
                unchanged_count = ?,
                skipped_invalid = ?,
                deactivated_count = ?,
                error_message = ?
            WHERE id = ?",
            [
                $finishTime,
                $stats['status'],
                $stats['feed_count'],
                $stats['imported_count'],
                $stats['updated_count'],
                $stats['unchanged_count'],
                $stats['skipped_invalid'],
                $stats['deactivated_count'],
                $stats['error_message'],
                $syncRunId,
            ]
        );

        return $stats;
    }

    /**
     * Price formula: ceil((supplier_price * (1 + percent/100) + fixed) / round_to) * round_to
     */
    public function calculateSellingPrice(float $supplierPrice, float $percent, float $fixed, int $roundTo): float
    {
        if ($supplierPrice <= 0) {
            return 0.00;
        }
        $raw = ($supplierPrice * (1 + $percent / 100)) + $fixed;
        $rounded = ceil($raw / $roundTo) * $roundTo;
        return (float)$rounded;
    }

    /**
     * Stable slug generator: {slugified-title}-{supplierId}
     */
    public function generateSlug(string $title, string $supplierId): string
    {
        $clean = strtolower(trim($title));
        $clean = preg_replace('/[^a-z0-9]+/i', '-', $clean);
        $clean = trim($clean, '-');
        if (strlen($clean) > 200) {
            $clean = substr($clean, 0, 200);
            $clean = rtrim($clean, '-');
        }
        return $clean . '-' . $supplierId;
    }

    private function resolveBrand(string $name, mixed $supplierBrandId): ?int
    {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        $existing = $this->db->fetch("SELECT id FROM brands WHERE slug = ? OR name = ? LIMIT 1", [$slug, $name]);
        if ($existing) {
            return (int)$existing['id'];
        }
        $this->db->execute(
            "INSERT INTO brands (supplier_brand_id, name, slug) VALUES (?, ?, ?)",
            [$supplierBrandId ? (string)$supplierBrandId : null, $name, $slug]
        );
        return (int)$this->db->lastInsertId();
    }

    private function resolveCategory(string $catId, string $catTitle, ?string $subId, ?string $subTitle): ?int
    {
        if ($catTitle === '') {
            return null;
        }

        // 1. Check supplier_category_map for exact subcategory match first
        if ($subId !== null && $subId !== '') {
            $map = $this->db->fetch(
                "SELECT site_category_id FROM supplier_category_map WHERE supplier_category_id = ? AND supplier_subcategory_id = ? LIMIT 1",
                [$catId, $subId]
            );
            if ($map && !empty($map['site_category_id'])) {
                return (int)$map['site_category_id'];
            }
        }

        // Check category-level mapping
        $map = $this->db->fetch(
            "SELECT site_category_id FROM supplier_category_map WHERE supplier_category_id = ? AND (supplier_subcategory_id IS NULL OR supplier_subcategory_id = '') LIMIT 1",
            [$catId]
        );
        if ($map && !empty($map['site_category_id'])) {
            return (int)$map['site_category_id'];
        }

        // 2. Keyword matching rule against category and subcategory titles
        $targetText = ($subTitle !== null && $subTitle !== '') ? $subTitle : $catTitle;
        $matched = $this->matchCategoryKeyword($targetText);
        if (!$matched && $subTitle !== null && $subTitle !== '') {
            $matched = $this->matchCategoryKeyword($catTitle);
        }

        if ($matched) {
            // Cache map
            $this->db->execute(
                "INSERT INTO supplier_category_map (supplier_category_id, supplier_category_title, supplier_subcategory_id, supplier_subcategory_title, site_category_id)
                 VALUES (?, ?, ?, ?, ?)",
                [$catId, $catTitle, $subId, $subTitle, $matched]
            );
            return $matched;
        }

        // 3. Fallback: match by direct category name or slug
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $catTitle), '-'));
        $direct = $this->db->fetch("SELECT id FROM categories WHERE slug = ? OR name = ? LIMIT 1", [$slug, $catTitle]);
        if ($direct) {
            return (int)$direct['id'];
        }

        return null;
    }

    private function matchCategoryKeyword(string $text): ?int
    {
        $text = strtolower($text);
        $rules = [
            'speaker' => 'audio-and-speakers',
            'soundbar' => 'audio-and-speakers',
            'audio' => 'audio-and-speakers',
            'charger' => 'power-and-charging',
            'power bank' => 'power-and-charging',
            'charging' => 'power-and-charging',
            'cable' => 'cables-and-adapters',
            'converter' => 'cables-and-adapters',
            'otg' => 'cables-and-adapters',
            'gaming' => 'gaming',
            'keyboard' => 'computer-accessories',
            'mouse' => 'computer-accessories',
            'webcam' => 'computer-accessories',
            'storage' => 'storage-and-memory',
            'usb' => 'storage-and-memory',
            'microsd' => 'storage-and-memory',
            'memory' => 'storage-and-memory',
            'router' => 'networking',
            'wifi' => 'networking',
            'network' => 'networking',
            'cctv' => 'smart-and-cctv',
            'earbuds' => 'mobile-accessories',
            'mount' => 'mobile-accessories',
        ];

        foreach ($rules as $keyword => $catSlug) {
            if (str_contains($text, $keyword)) {
                $cat = $this->db->fetch("SELECT id FROM categories WHERE slug = ? LIMIT 1", [$catSlug]);
                if ($cat) {
                    return (int)$cat['id'];
                }
            }
        }
        return null;
    }

    private function updateCategoryCounts(): void
    {
        $this->db->execute(
            "UPDATE categories SET product_count = (
                SELECT COUNT(*) FROM products 
                WHERE products.category_id = categories.id 
                  AND products.is_active = 1
            )"
        );
    }

    public function testConnection(): array
    {
        $defaultUrl = 'https://api.computerzone.pk/products';
        $apiUrl = (!empty($this->config['api_url']) && !str_contains($this->config['api_url'], '127.0.0.1') ? $this->config['api_url'] : null)
            ?? (mta_setting('supplier_api_url') && !str_contains(mta_setting('supplier_api_url'), '127.0.0.1') ? mta_setting('supplier_api_url') : null)
            ?? $defaultUrl;

        $authFormat = $this->config['auth_header_format'] ?? mta_setting('supplier_auth_header_format', 'raw');
        $cfgToken = $this->config['token'] ?? '';
        $token = (!empty($cfgToken) && $cfgToken !== 'TODO-OWNER' && !str_starts_with($cfgToken, 'YOUR_'))
            ? $cfgToken
            : (mta_setting('supplier_token') !== 'TODO-OWNER' && !empty(mta_setting('supplier_token'))
                ? mta_setting('supplier_token')
                : 'd65090a5f72172720b1806a9d37aa9a4');

        // Check server outbound IP
        $outboundIp = 'Unknown';
        try {
            $ipCh = curl_init('https://api.ipify.org');
            curl_setopt($ipCh, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ipCh, CURLOPT_TIMEOUT, 5);
            curl_setopt($ipCh, CURLOPT_SSL_VERIFYPEER, false);
            $ipRes = curl_exec($ipCh);
            if ($ipRes) $outboundIp = trim((string)$ipRes);
            curl_close($ipCh);
        } catch (\Throwable) {}

        // Test API with primary auth format
        $authHeader = ($authFormat === 'bearer') ? "Bearer {$token}" : $token;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                "Authorization: {$authHeader}",
                "Accept: application/json",
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        $parsed = json_decode((string)$response, true);
        $isSuccess = ($httpCode === 200 && is_array($parsed) && ($parsed['status'] ?? '') === 'success');
        $productsFound = isset($parsed['Products']) && is_array($parsed['Products']) ? count($parsed['Products']) : 0;

        return [
            'success' => $isSuccess,
            'http_code' => $httpCode,
            'curl_error' => $curlErr,
            'outbound_ip' => $outboundIp,
            'api_url' => $apiUrl,
            'token_preview' => substr($token, 0, 8) . '...' . substr($token, -4),
            'auth_header' => ($authFormat === 'bearer') ? 'Bearer <token>' : 'raw <token>',
            'products_count' => $productsFound,
            'response_snippet' => substr((string)$response, 0, 400),
        ];
    }

    private function fetchWithRetry(string $url, string $authHeader, int $timeout, int $maxRetries): string
    {
        if (str_starts_with($url, 'file://')) {
            $path = substr($url, 7);
            if (file_exists($path)) {
                return (string)file_get_contents($path);
            }
            throw new RuntimeException("Local feed file not found: {$path}");
        }

        $attempt = 0;
        $lastError = '';

        while ($attempt < $maxRetries) {
            $attempt++;
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_HTTPHEADER => [
                    "Authorization: {$authHeader}",
                    "Accept: application/json",
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);

            if ($response !== false && $httpCode === 200) {
                return $response;
            }

            $respSnippet = substr((string)$response, 0, 200);
            $lastError = $err ?: "HTTP {$httpCode}: {$respSnippet}";
            if ($attempt < $maxRetries) {
                usleep((int)(pow(2, $attempt) * 200000));
            }
        }

        throw new RuntimeException("Failed to fetch supplier feed after {$maxRetries} attempts: {$lastError}");
    }
}
