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
                Session::flash(
                    'success',
                    "Sync Completed: {$result['products_received']} items received. ({$result['products_inserted']} inserted, {$result['products_updated']} updated, {$result['products_unchanged']} unchanged). Duration: {$result['duration_seconds']}s."
                );
            } else {
                Session::flash('error', "Sync aborted or encountered an error: " . ($result['error'] ?? 'Unknown error'));
            }
        } catch (\Throwable $e) {
            Session::flash('error', 'Supplier sync exception: ' . $e->getMessage());
        }

        return $resp->redirect($adminPath . '/sync');
    }
}
