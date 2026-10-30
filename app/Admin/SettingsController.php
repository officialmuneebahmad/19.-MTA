<?php
/**
 * Multan Tech Accessories — Admin SettingsController (Part B)
 * Store Configuration, Pricing Engine, Delivery Rules & Integrations
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Cache;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\AuditLogger;
use App\Services\PricingService;

class SettingsController
{
    public function index(): Response
    {
        $db = Database::getInstance();
        $rows = $db->fetchAll("SELECT `key`, `value` FROM `settings`");
        
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['key']] = $r['value'];
        }

        $content = View::render('admin/settings/index', [
            'settings' => $settings,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Store & Business Settings',
            'meta_title' => 'Settings — Multan Tech Accessories',
        ]));
    }

    public function update(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $allowedKeys = [
            'site_name', 'store_address', 'store_phone', 'store_whatsapp',
            'social_facebook', 'social_instagram', 'announcement_marquee',
            'show_wa_on_product_cards',
            'delivery_free_threshold', 'delivery_multan_charge', 'delivery_standard_charge',
            'pricing_threshold', 'pricing_tier1_add', 'pricing_tier1_mode', 'pricing_tier1_ending',
            'pricing_tier2_add', 'pricing_tier2_mode', 'pricing_tier2_ending',
            'advance_payment_required', 'advance_minimum_amount',
            'adsense_client_id', 'google_analytics_id',
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_secure',
        ];

        $updatedCount = 0;
        foreach ($allowedKeys as $key) {
            $val = trim((string)$request->post($key, ''));
            // Check if key exists in settings
            $existing = $db->fetch("SELECT `key` FROM settings WHERE `key` = ? LIMIT 1", [$key]);
            if ($existing) {
                $db->execute(
                    "UPDATE settings SET value = ?, updated_at = CURRENT_TIMESTAMP WHERE `key` = ?",
                    [$val, $key]
                );
            } else {
                $db->execute(
                    "INSERT INTO settings (`key`, value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP)",
                    [$key, $val]
                );
            }
            $updatedCount++;
        }

        // Clear full page cache so changes propagate to storefront
        Cache::flush();

        AuditLogger::log('update_settings', 'settings', null, ['keys_updated' => $updatedCount]);

        Session::flash('success', 'Store settings and pricing engine parameters updated successfully. Full-page cache cleared.');
        return $resp->redirect($adminPath . '/settings');
    }

    public function cacheClear(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        Cache::flush();
        AuditLogger::log('cache_cleared', 'system');

        Session::flash('success', 'Full-page cache flushed successfully! All storefront pages will recompile dynamically on next request.');
        return $resp->redirect($adminPath . '/settings');
    }

    public function reprice(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        $count = PricingService::repriceAllProducts();
        AuditLogger::log('reprice_all_products', 'catalog', null, ['count' => $count]);

        Session::flash('success', "Pricing engine executed! {$count} products re-calculated and verified strictly ending in 99.");
        return $resp->redirect($adminPath . '/settings');
    }
}
