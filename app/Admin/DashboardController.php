<?php
/**
 * Multan Tech Accessories — Admin DashboardController
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\View;

class DashboardController
{
    public function index(): Response
    {
        $db = Database::getInstance();

        // 1. Product Stats
        $productStats = $db->fetch(
            "SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as in_stock,
                SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as coming_soon,
                SUM(CASE WHEN content_state = 'approved' THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN content_state = 'ai_draft' THEN 1 ELSE 0 END) as draft_count,
                SUM(CASE WHEN content_state = 'supplier_raw' THEN 1 ELSE 0 END) as raw_count
             FROM products"
        );

        // 2. Order Stats
        $today = date('Y-m-d');
        $orderStats = $db->fetch(
            "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN created_at LIKE ? THEN 1 ELSE 0 END) as orders_today,
                SUM(CASE WHEN created_at LIKE ? AND status != 'cancelled' THEN total ELSE 0 END) as revenue_today
             FROM orders",
            [$today . '%', $today . '%']
        );

        // 3. Last Sync Run
        $lastSync = $db->fetch(
            "SELECT * FROM sync_runs ORDER BY id DESC LIMIT 1"
        );

        // 4. Recent Orders
        $recentOrders = $db->fetchAll(
            "SELECT id, order_number, customer_name, customer_phone, city, total, status, created_at
             FROM orders
             ORDER BY id DESC
             LIMIT 8"
        );

        // Render dashboard content inside admin layout
        $content = View::render('admin/dashboard', [
            'productStats' => $productStats ?: [],
            'orderStats' => $orderStats ?: [],
            'lastSync' => $lastSync,
            'recentOrders' => $recentOrders,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Store Operations & Catalog Dashboard',
            'meta_title' => 'Admin Dashboard — Multan Tech Accessories',
        ]));
    }
}
