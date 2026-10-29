<?php
/**
 * Multan Tech Accessories — Admin SyncController
 * Live Supplier API Synchronization & Audit Diagnostics
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\SupplierSyncService;

class SyncController
{
    public function index(): Response
    {
        $db = Database::getInstance();

        // 1. Last Sync
        $lastSync = $db->fetch("SELECT * FROM sync_runs ORDER BY id DESC LIMIT 1");

        // 2. Recent 15 Runs
        $runs = $db->fetchAll("SELECT * FROM sync_runs ORDER BY id DESC LIMIT 15");

        // 3. Current Catalog count
        $catalogCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM products");

        $content = View::render('admin/sync/index', [
            'lastSync' => $lastSync,
            'runs' => $runs,
            'catalogCount' => $catalogCount,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Supplier API Synchronization',
            'meta_title' => 'Supplier Sync — Multan Tech Accessories',
        ]));
    }

    public function test(): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        try {
            $syncService = new SupplierSyncService();
            $diag = $syncService->testConnection();

            if ($diag['success']) {
                Session::flash(
                    'success',
                    "API Connection Successful! Outbound Server IP: {$diag['outbound_ip']}. HTTP Code: 200. Feed contains {$diag['products_count']} products. You can now run the full sync below."
                );
            } else {
                $err = $diag['curl_error'] ? "cURL Error: {$diag['curl_error']}" : "HTTP {$diag['http_code']}";
                Session::flash(
                    'error',
                    "Supplier API Connection Failed ({$err}). Server Outbound IP: {$diag['outbound_ip']}. API Response: {$diag['response_snippet']}"
                );
            }
        } catch (\Throwable $e) {
            Session::flash('error', 'Supplier test exception: ' . $e->getMessage());
        }

        return $resp->redirect($adminPath . '/sync');
    }

    public function seedFallback(): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        @set_time_limit(300);

        try {
            $backupFile = MTA_ROOT . '/tools/mock_supplier/products.json';
            if (!file_exists($backupFile)) {
                throw new \RuntimeException('Verified catalog feed file not found at: ' . $backupFile);
            }

            $syncService = new SupplierSyncService();
            $result = $syncService->run('file://' . $backupFile);

            if ($result['status'] === 'success') {
                Session::flash(
                    'success',
                    "Success! {$result['feed_count']} verified products imported into store catalog! All categories, brands, and stock counts have been populated."
                );
            } else {
                Session::flash('error', "Import error: " . ($result['error_message'] ?? 'Unknown error'));
            }
        } catch (\Throwable $e) {
            Session::flash('error', 'Seed exception: ' . $e->getMessage());
        }

        return $resp->redirect($adminPath . '/sync');
    }

    public function trigger(): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        // Extend execution time for synchronous import pass
        @set_time_limit(180);

        try {
            $syncService = new SupplierSyncService();
            $result = $syncService->run();

            if ($result['status'] === 'success') {
                $feedCount = $result['feed_count'] ?? $result['products_received'] ?? 0;
                $imported = $result['imported_count'] ?? $result['products_inserted'] ?? 0;
                $updated = $result['updated_count'] ?? $result['products_updated'] ?? 0;
                $unchanged = $result['unchanged_count'] ?? $result['products_unchanged'] ?? 0;
                Session::flash(
                    'success',
                    "Supplier Sync Completed: {$feedCount} products processed ({$imported} imported, {$updated} updated, {$unchanged} unchanged)."
                );
            } else {
                $err = $result['error_message'] ?? $result['error'] ?? 'Unknown error';
                Session::flash('error', "Sync aborted or encountered an error: " . $err);
            }
        } catch (\Throwable $e) {
            Session::flash('error', 'Supplier sync exception: ' . $e->getMessage());
        }

        return $resp->redirect($adminPath . '/sync');
    }
}
