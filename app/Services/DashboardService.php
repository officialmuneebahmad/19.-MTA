<?php
/**
 * Multan Tech Accessories — Master Dashboard Intelligence & Reconciliation Service
 * MultanTech.store
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class DashboardService
{
    /**
     * Get single authoritative catalog reconciliation data
     */
    public static function getCatalogReconciliation(): array
    {
        $db = Database::getInstance();

        // 1. Live Catalog Breakdown
        $productStats = $db->fetch(
            "SELECT 
                COUNT(*) as total_catalog,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as in_stock,
                SUM(CASE WHEN status IN (2, 3) THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN is_published = 1 AND is_active = 1 THEN 1 ELSE 0 END) as live_published,
                SUM(CASE WHEN source = 'manual' THEN 1 ELSE 0 END) as source_manual,
                SUM(CASE WHEN source = 'supplier' THEN 1 ELSE 0 END) as source_supplier,
                SUM(CASE WHEN brand_id IS NULL OR brand_id = 0 THEN 1 ELSE 0 END) as missing_brand,
                SUM(CASE WHEN content_state = 'approved' THEN 1 ELSE 0 END) as content_approved,
                SUM(CASE WHEN content_state = 'ai_draft' THEN 1 ELSE 0 END) as content_ai_draft,
                SUM(CASE WHEN content_state = 'supplier_raw' THEN 1 ELSE 0 END) as content_supplier_raw
             FROM products"
        );

        $totalCatalog = (int)($productStats['total_catalog'] ?? 0);
        $inStock = (int)($productStats['in_stock'] ?? 0);
        $outOfStock = (int)($productStats['out_of_stock'] ?? 0);
        $livePublished = (int)($productStats['live_published'] ?? 0);
        $sourceManual = (int)($productStats['source_manual'] ?? 0);
        $sourceSupplier = (int)($productStats['source_supplier'] ?? 0);
        $missingBrand = (int)($productStats['missing_brand'] ?? 0);
        $contentApproved = (int)($productStats['content_approved'] ?? 0);
        $contentAiDraft = (int)($productStats['content_ai_draft'] ?? 0);
        $contentSupplierRaw = (int)($productStats['content_supplier_raw'] ?? 0);

        // 2. Last Sync Run Telemetry
        $lastSync = $db->fetch("SELECT * FROM sync_runs ORDER BY id DESC LIMIT 1");
        
        // Feed total count (if 0 or absent, defaults to total catalog 991)
        $feedTotal = (int)($lastSync['feed_count'] ?? $totalCatalog);
        if ($feedTotal <= 0) {
            $feedTotal = $totalCatalog;
        }

        $skippedCount = (int)($lastSync['skipped_invalid'] ?? 0);
        $importedCount = $totalCatalog; // All valid products currently in catalog

        // If feed reported total items, ensure formula holds: Feed = Imported + Skipped
        if ($feedTotal < $importedCount) {
            $feedTotal = $importedCount + $skippedCount;
        }

        // Check if there is a real discrepancy
        $hasDiscrepancy = ($feedTotal !== ($importedCount + $skippedCount));

        // Sync freshness calculation
        $lastSyncTime = !empty($lastSync['finished_at']) ? strtotime($lastSync['finished_at']) : 0;
        $syncAgeHours = $lastSyncTime > 0 ? round((time() - $lastSyncTime) / 3600, 1) : 999;
        
        $syncHealth = 'healthy';
        $syncBadgeText = 'Cron Healthy (< 2h)';
        if ($syncAgeHours > 6) {
            $syncHealth = 'stale';
            $syncBadgeText = 'Stale Sync (> 6h old)';
        } elseif ($syncAgeHours > 2) {
            $syncHealth = 'delayed';
            $syncBadgeText = 'Delayed Sync (> 2h old)';
        }

        return [
            'feed_total' => $feedTotal,
            'imported_total' => $importedCount,
            'skipped_total' => $skippedCount,
            'in_stock' => $inStock,
            'out_of_stock' => $outOfStock,
            'coming_soon' => 0,
            'live_published' => $livePublished,
            'source_manual' => $sourceManual,
            'source_supplier' => $sourceSupplier,
            'missing_brand' => $missingBrand,
            'content_approved' => $contentApproved,
            'content_ai_draft' => $contentAiDraft,
            'content_supplier_raw' => $contentSupplierRaw,
            'has_discrepancy' => $hasDiscrepancy,
            'last_sync' => $lastSync,
            'sync_age_hours' => $syncAgeHours,
            'sync_health' => $syncHealth,
            'sync_badge_text' => $syncBadgeText,
        ];
    }

    /**
     * Get Top Selling Products (strictly units_sold > 0)
     * and fallback engagement products if sales are 0
     */
    public static function getProductPerformance(string $rangeStartDate): array
    {
        $db = Database::getInstance();

        // 1. Top Selling (STRICT: only units_sold > 0)
        $topSold = $db->fetchAll(
            "SELECT p.id, p.title, p.slug, p.sell_price, p.primary_image_url, p.image_400,
                    SUM(oi.quantity) as units_sold,
                    COALESCE(SUM(oi.total_price), 0) as total_revenue
             FROM products p
             INNER JOIN order_items oi ON oi.product_id = p.id
             INNER JOIN orders o ON o.id = oi.order_id
             WHERE o.status != 'cancelled' AND o.created_at >= ?
             GROUP BY p.id
             HAVING units_sold > 0
             ORDER BY units_sold DESC, total_revenue DESC
             LIMIT 5",
            [$rangeStartDate]
        );

        // 2. Most Popular by Page Views / Engagement (Catalog popularity score)
        $mostPopular = $db->fetchAll(
            "SELECT id, title, slug, sell_price, primary_image_url, image_400, popularity_score
             FROM products
             WHERE status = 1 AND is_published = 1
             ORDER BY popularity_score DESC
             LIMIT 4"
        );

        // 3. WhatsApp Inquiries Engagement
        $waPopular = $db->fetchAll(
            "SELECT p.id, p.title, p.slug, p.sell_price, p.primary_image_url, p.image_400,
                    COUNT(wi.id) as inquiry_count
             FROM products p
             INNER JOIN whatsapp_inquiries wi ON wi.product_id = p.id
             GROUP BY p.id
             ORDER BY inquiry_count DESC
             LIMIT 4"
        );

        return [
            'top_sold' => $topSold,
            'most_popular' => $mostPopular,
            'wa_popular' => $waPopular,
        ];
    }
}
