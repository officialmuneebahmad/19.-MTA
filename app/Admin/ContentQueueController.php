<?php
/**
 * Multan Tech Accessories — Admin ContentQueueController
 * AI Content Rewrite Queue & Editorial Approval Gate
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class ContentQueueController
{
    public function index(): Response
    {
        $db = Database::getInstance();

        // 1. Pending Queue Items
        $queueItems = $db->fetchAll(
            "SELECT q.*, p.title AS product_title, p.sku, p.content_state
             FROM ai_queue q
             JOIN products p ON p.id = q.product_id
             WHERE q.status IN ('pending', 'processing')
             ORDER BY q.id ASC
             LIMIT 30"
        );

        // 2. Drafts awaiting human editorial approval
        $drafts = $db->fetchAll(
            "SELECT p.id, p.title, p.slug, p.sku, p.sell_price, p.content_state, p.updated_at
             FROM products p
             WHERE p.content_state = 'ai_draft'
             ORDER BY p.updated_at DESC
             LIMIT 30"
        );

        // 3. Stats
        $stats = $db->fetch(
            "SELECT 
                SUM(CASE WHEN content_state = 'supplier_raw' THEN 1 ELSE 0 END) as raw_count,
                SUM(CASE WHEN content_state = 'ai_draft' THEN 1 ELSE 0 END) as draft_count,
                SUM(CASE WHEN content_state = 'approved' THEN 1 ELSE 0 END) as approved_count
             FROM products"
        );

        $content = View::render('admin/queue/index', [
            'queueItems' => $queueItems,
            'drafts' => $drafts,
            'stats' => $stats ?: [],
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'AI Content Queue & SEO Approval Gate',
            'meta_title' => 'Content Queue — Multan Tech Accessories',
        ]));
    }

    public function approve(int $productId): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $db->execute(
            "UPDATE products SET content_state = 'approved', updated_at = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $productId]
        );

        Session::flash('success', "Product #{$productId} approved and added to live XML sitemaps.");
        return $resp->redirect($adminPath . '/queue');
    }

    public function bulkApprove(): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $affected = $db->execute(
            "UPDATE products SET content_state = 'approved', updated_at = ? WHERE content_state = 'ai_draft'",
            [date('Y-m-d H:i:s')]
        );

        Session::flash('success', "Bulk approved {$affected} product drafts for search engine indexing.");
        return $resp->redirect($adminPath . '/queue');
    }

    public function bulkEnqueue(): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $rawProducts = $db->fetchAll(
            "SELECT id FROM products WHERE content_state = 'supplier_raw' LIMIT 200"
        );

        $enqueued = 0;
        foreach ($rawProducts as $p) {
            try {
                $db->execute(
                    "INSERT INTO ai_queue (product_id, status, created_at) VALUES (?, 'pending', ?)",
                    [$p['id'], date('Y-m-d H:i:s')]
                );
                $enqueued++;
            } catch (\Throwable $e) {
                // Ignore duplicates
            }
        }

        Session::flash('success', "Queued {$enqueued} products for AI content rewrite.");
        return $resp->redirect($adminPath . '/queue');
    }
}
