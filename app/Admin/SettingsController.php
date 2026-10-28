<?php
/**
 * Multan Tech Accessories — Admin SettingsController
 * Store Configuration, Delivery Rules, Markups & Tracking Integrations
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

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
            'announcement_marquee', 'delivery_free_threshold', 'delivery_multan_charge',
            'delivery_standard_charge', 'pricing_default_markup_percent',
            'adsense_client_id', 'google_analytics_id',
        ];

        foreach ($allowedKeys as $key) {
            $val = trim((string)$request->post($key, ''));
            if ($val !== '') {
                $db->execute(
                    "UPDATE settings SET value = ?, updated_at = ? WHERE `key` = ?",
                    [$val, date('Y-m-d H:i:s'), $key]
                );
            }
        }

        Session::flash('success', 'Store settings updated successfully.');
        return $resp->redirect($adminPath . '/settings');
    }
}
