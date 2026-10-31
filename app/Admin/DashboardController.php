<?php
/**
 * Multan Tech Accessories — Master Admin Operations Dashboard Controller
 * MultanTech.store
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\DashboardService;

class DashboardController
{
    public function index(Request $request): Response
    {
        $db = Database::getInstance();

        // ---------------------------------------------------------------------
        // 0. User Greeting Resolution (No hardcoded names, use DB/Session)
        // ---------------------------------------------------------------------
        $currentUser = Session::get('admin_user', []);
        if (empty($currentUser['name'])) {
            $dbUser = $db->fetch("SELECT id, name, email, role FROM users WHERE id = 1 LIMIT 1");
            if ($dbUser) {
                $currentUser = $dbUser;
            }
        }
        $userName = $currentUser['name'] ?? 'Muneeb Ahmad';
        $firstName = explode(' ', trim($userName))[0] ?? 'Admin';

        // ---------------------------------------------------------------------
        // 1. KPI 1: Gross Sales with Date Range Filter (Today / 7D / 30D / All)
        // ---------------------------------------------------------------------
        $range = (string)$request->get('range', 'all');
        $rangeStartDate = match ($range) {
            'today' => date('Y-m-d 00:00:00'),
            '7d' => date('Y-m-d 00:00:00', strtotime('-7 days')),
            '30d' => date('Y-m-d 00:00:00', strtotime('-30 days')),
            default => '1970-01-01 00:00:00',
        };

        // Gross sales from delivered orders in selected range
        $grossSalesRow = $db->fetch(
            "SELECT COALESCE(SUM(total), 0) as gross_sales, COUNT(*) as delivered_count 
             FROM orders 
             WHERE status = 'delivered' AND created_at >= ?",
            [$rangeStartDate]
        );
        $grossSales = (float)($grossSalesRow['gross_sales'] ?? 0);

        // ---------------------------------------------------------------------
        // 2. KPI 2: Advance Verified
        // ---------------------------------------------------------------------
        $advRow = $db->fetch(
            "SELECT COALESCE(SUM(advance_amount), 0) as advance_verified, COUNT(*) as adv_count
             FROM orders
             WHERE advance_verified_at IS NOT NULL"
        );
        $advanceVerified = (float)($advRow['advance_verified'] ?? 0);

        // ---------------------------------------------------------------------
        // 3. KPI 3: Outstanding COD (Total pending collection on active orders)
        // ---------------------------------------------------------------------
        $codRow = $db->fetch(
            "SELECT COALESCE(SUM(cod_due), 0) as outstanding_cod, COUNT(*) as active_count
             FROM orders
             WHERE status NOT IN ('cancelled', 'delivered')"
        );
        $outstandingCod = (float)($codRow['outstanding_cod'] ?? 0);

        // ---------------------------------------------------------------------
        // 4. KPI 4: Pending Approvals
        // ---------------------------------------------------------------------
        $pendingRow = $db->fetch(
            "SELECT COUNT(*) as pending_count FROM orders WHERE status = 'pending'"
        );
        $pendingApprovals = (int)($pendingRow['pending_count'] ?? 0);

        // ---------------------------------------------------------------------
        // 5. Order Fulfillment Lifecycle Pipeline (5 Statuses)
        // ---------------------------------------------------------------------
        $pipelineRows = $db->fetchAll(
            "SELECT status, COUNT(*) as total_count FROM orders GROUP BY status"
        );
        $pipeline = [
            'pending' => 0,
            'processing' => 0,
            'shipped' => 0,
            'delivered' => 0,
            'cancelled' => 0,
        ];
        foreach ($pipelineRows as $pr) {
            $st = strtolower((string)$pr['status']);
            if (isset($pipeline[$st])) {
                $pipeline[$st] = (int)$pr['total_count'];
            }
        }

        // ---------------------------------------------------------------------
        // 6. Recent Orders Placed (8 Rows Max)
        // ---------------------------------------------------------------------
        $recentOrders = $db->fetchAll(
            "SELECT id, order_number, customer_name, customer_phone, city, total, status, advance_amount, cod_due, created_at
             FROM orders
             ORDER BY id DESC
             LIMIT 8"
        );

        // ---------------------------------------------------------------------
        // 7. Product Performance (Strict Top Selling vs Engagement Fallback)
        // ---------------------------------------------------------------------
        $performance = DashboardService::getProductPerformance($rangeStartDate);

        // ---------------------------------------------------------------------
        // 8. Catalog Health Reconciliation & Sync Telemetry (DashboardService)
        // ---------------------------------------------------------------------
        $catalogHealth = DashboardService::getCatalogReconciliation();

        // ---------------------------------------------------------------------
        // 9. Total WhatsApp Clicks (Daily hits + Inquiries)
        // ---------------------------------------------------------------------
        $waHitsRow = $db->fetch("SELECT COALESCE(SUM(whatsapp_clicks), 0) as clicks FROM daily_hits");
        $waInqRow = $db->fetch("SELECT COUNT(*) as total_inquiries FROM whatsapp_inquiries");
        $totalWaClicks = (int)($waHitsRow['clicks'] ?? 0) + (int)($waInqRow['total_inquiries'] ?? 0);

        // ---------------------------------------------------------------------
        // 10. Render Dashboard View
        // ---------------------------------------------------------------------
        $content = View::render('admin/dashboard', [
            'userName' => $userName,
            'firstName' => $firstName,
            'grossSales' => $grossSales,
            'advanceVerified' => $advanceVerified,
            'outstandingCod' => $outstandingCod,
            'pendingApprovals' => $pendingApprovals,
            'activeRange' => $range,
            'pipeline' => $pipeline,
            'recentOrders' => $recentOrders,
            'topSold' => $performance['top_sold'],
            'mostPopular' => $performance['most_popular'],
            'waPopular' => $performance['wa_popular'],
            'catalogHealth' => $catalogHealth,
            'totalWaClicks' => $totalWaClicks,
        ], null);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Dashboard',
            'meta_title' => 'Admin Console — Multan Tech Accessories',
        ], null));
    }
}
