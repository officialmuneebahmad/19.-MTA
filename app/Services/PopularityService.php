<?php
/**
 * Multan Tech Accessories — Popularity Ranking Engine
 * Formula: popularity_score = (orders*10 + whatsapp_clicks*4 + add_to_cart*3 + views) [30-day window with decay] + (pinned * 500)
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class PopularityService
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    /**
     * Record a hit from the storefront beacon
     */
    public function recordEvent(int $productId, string $event): void
    {
        $validEvents = ['views', 'add_to_cart', 'whatsapp_clicks', 'orders'];
        if (!in_array($event, $validEvents, true)) {
            return;
        }

        $today = date('Y-m-d');
        
        $this->db->execute(
            "INSERT INTO daily_hits (date, product_id, {$event}) 
             VALUES (?, ?, 1)
             ON CONFLICT(date, product_id) DO UPDATE SET {$event} = {$event} + 1",
            [$today, $productId]
        );
    }

    /**
     * Recompute popularity scores for all products (nightly cron)
     */
    public function recalculateAll(): int
    {
        // 30-day window
        $cutoffDate = date('Y-m-d', strtotime('-30 days'));

        // Aggregate hits per product
        $hits = $this->db->fetchAll(
            "SELECT product_id,
                    SUM(views) AS total_views,
                    SUM(add_to_cart) AS total_cart,
                    SUM(whatsapp_clicks) AS total_wa,
                    SUM(orders) AS total_orders
             FROM daily_hits
             WHERE date >= ?
             GROUP BY product_id",
            [$cutoffDate]
        );

        $scores = [];
        foreach ($hits as $h) {
            $score = ((int)$h['total_orders'] * 10)
                   + ((int)$h['total_wa'] * 4)
                   + ((int)$h['total_cart'] * 3)
                   + ((int)$h['total_views']);
            $scores[(int)$h['product_id']] = $score;
        }

        // Get all products to update
        $products = $this->db->fetchAll("SELECT id, pinned FROM products");
        $updated = 0;

        foreach ($products as $p) {
            $id = (int)$p['id'];
            $baseScore = $scores[$id] ?? 0;
            $pinnedBoost = !empty($p['pinned']) ? 500 : 0;
            $finalScore = $baseScore + $pinnedBoost;

            $this->db->execute(
                "UPDATE products SET popularity_score = ? WHERE id = ?",
                [$finalScore, $id]
            );
            $updated++;
        }

        return $updated;
    }
}
