<?php
/**
 * Multan Tech Accessories — Master Pricing Engine (Single Source of Truth)
 * MultanTech.store
 * 
 * Rules:
 * - If supplierPrice <= 0: return 0 (shows "Ask price on WhatsApp"; never add 99 to 0)
 * - Tier threshold: default 2000 (editable in settings)
 * - If supplierPrice < threshold:
 *     x = supplierPrice + add_tier1 (default 100)
 *     round x to NEAREST hundred (half rounds up)
 *     add ending (default 99)
 * - If supplierPrice >= threshold:
 *     x = supplierPrice + add_tier2 (default 300)
 *     round x UP to next hundred (a value already on a hundred stays)
 *     add ending (default 99)
 * - Result always ends with 99 (or 999 when hundreds digit is 9)
 * - Manual price overrides auto-snap to next value ending in 99
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Cache;

class PricingService
{
    /**
     * Compute selling price from supplier price.
     * Single source of truth across the entire platform.
     */
    public static function sellPrice(int|float $supplierPrice): int
    {
        $price = (int)round($supplierPrice);
        if ($price <= 0) {
            return 0;
        }

        // Load pricing configuration (cached static in memory, fallback to settings / defaults)
        $config = self::getConfig();
        $threshold = (int)($config['threshold'] ?? 2000);

        if ($price < $threshold) {
            $add = (int)($config['tier1_add'] ?? 100);
            $mode = $config['tier1_mode'] ?? 'nearest';
            $x = $price + $add;
            $hundreds = self::applyRounding($x, $mode);
            $ending = (int)($config['tier1_ending'] ?? 99);
        } else {
            $add = (int)($config['tier2_add'] ?? 300);
            $mode = $config['tier2_mode'] ?? 'up';
            $x = $price + $add;
            $hundreds = self::applyRounding($x, $mode);
            $ending = (int)($config['tier2_ending'] ?? 99);
        }

        $result = $hundreds + $ending;

        // Guarantee output ends in 99 or 999
        if ($result > 0 && ($result % 100 !== 99)) {
            $result = ((int)floor($result / 100) * 100) + 99;
        }

        return $result;
    }

    /**
     * Snap a manual price or custom input to the next value ending in 99.
     */
    public static function snapPrice(int|float $rawPrice): int
    {
        $price = (int)round($rawPrice);
        if ($price <= 0) {
            return 0;
        }

        if ($price % 100 === 99) {
            return $price;
        }

        $hundreds = (int)floor($price / 100);
        $candidate = ($hundreds * 100) + 99;
        if ($candidate < $price) {
            $candidate += 100;
        }

        return $candidate;
    }

    /**
     * Apply rounding mode to a number and return the rounded hundreds value.
     */
    private static function applyRounding(int $val, string $mode): int
    {
        return match ($mode) {
            'up' => (int)ceil($val / 100) * 100,
            'down' => (int)floor($val / 100) * 100,
            default => (int)round($val / 100, 0, PHP_ROUND_HALF_UP) * 100,
        };
    }

    /**
     * Retrieve pricing configuration with runtime caching.
     */
    public static function getConfig(): array
    {
        static $cfg = null;
        if ($cfg !== null) {
            return $cfg;
        }

        $threshold = (int)mta_setting('pricing_tier_threshold', 2000);
        $tier1Add = (int)mta_setting('pricing_tier1_add', 100);
        $tier1Mode = (string)mta_setting('pricing_tier1_mode', 'nearest');
        $tier1Ending = (int)mta_setting('pricing_tier1_ending', 99);

        $tier2Add = (int)mta_setting('pricing_tier2_add', 300);
        $tier2Mode = (string)mta_setting('pricing_tier2_mode', 'up');
        $tier2Ending = (int)mta_setting('pricing_tier2_ending', 99);

        $cfg = [
            'threshold' => $threshold > 0 ? $threshold : 2000,
            'tier1_add' => $tier1Add,
            'tier1_mode' => $tier1Mode,
            'tier1_ending' => $tier1Ending,
            'tier2_add' => $tier2Add,
            'tier2_mode' => $tier2Mode,
            'tier2_ending' => $tier2Ending,
        ];

        return $cfg;
    }

    /**
     * Compute a deterministic pricing settings signature for the sync content hash.
     */
    public static function getSettingsHash(): string
    {
        $cfg = self::getConfig();
        return md5(json_encode($cfg));
    }

    /**
     * Re-price all active products in the database using the master pricing engine.
     * Respects existing manual price overrides.
     * Returns count of updated products.
     */
    public static function repriceAllProducts(): int
    {
        $db = Database::getInstance();
        $products = $db->fetchAll("SELECT id, supplier_price, price_override FROM products");
        $count = 0;

        $db->beginTransaction();
        try {
            $stmt = $db->getPdo()->prepare("UPDATE products SET sell_price = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");

            foreach ($products as $p) {
                if ($p['price_override'] !== null && (float)$p['price_override'] > 0) {
                    $newPrice = self::snapPrice((float)$p['price_override']);
                } else {
                    $newPrice = self::sellPrice((int)round((float)$p['supplier_price']));
                }

                $stmt->execute([$newPrice, $p['id']]);
                $count++;
            }

            $db->getPdo()->commit();
        } catch (\Throwable $e) {
            $db->getPdo()->rollBack();
            throw $e;
        }

        // Flush full page cache
        Cache::flush();

        return $count;
    }
}

