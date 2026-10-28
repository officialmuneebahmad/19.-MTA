<?php
/**
 * Multan Tech Accessories — Admin SeoAuditController
 * On-page SEO Health Auditing, 301 Redirect Manager & 404 Crawl Error Tracker
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class SeoAuditController
{
    public function index(): Response
    {
        $db = Database::getInstance();

        // 1. Audit Checks
        $missingDescCount = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE is_active = 1 AND (description_html IS NULL OR length(description_html) < 40)"
        );

        $uncategorizedCount = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE is_active = 1 AND category_id IS NULL"
        );

        $missingImagesCount = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE is_active = 1 AND (primary_image_url IS NULL OR primary_image_url LIKE '%placeholder%')"
        );

        $shortTitlesCount = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM products WHERE is_active = 1 AND length(title) < 15"
        );

        $top404Count = (int)$db->fetchColumn("SELECT COUNT(*) FROM log_404");
        $redirectsCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM redirects");

        // Top sample issues
        $uncategorizedProducts = $db->fetchAll(
            "SELECT id, title, sku, sell_price FROM products WHERE is_active = 1 AND category_id IS NULL LIMIT 8"
        );

        $content = View::render('admin/seo-audit/index', [
            'missingDescCount' => $missingDescCount,
            'uncategorizedCount' => $uncategorizedCount,
            'missingImagesCount' => $missingImagesCount,
            'shortTitlesCount' => $shortTitlesCount,
            'top404Count' => $top404Count,
            'redirectsCount' => $redirectsCount,
            'uncategorizedProducts' => $uncategorizedProducts,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Technical SEO Health & Integrity Audit',
            'meta_title' => 'SEO Audit — Multan Tech Accessories',
        ]));
    }

    public function redirects(): Response
    {
        $db = Database::getInstance();
        $redirects = $db->fetchAll("SELECT * FROM redirects ORDER BY id DESC");

        $content = View::render('admin/seo-audit/redirects', [
            'redirects' => $redirects,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => '301 Permanent Redirects Manager',
            'meta_title' => 'Redirects — Multan Tech Accessories',
        ]));
    }

    public function storeRedirect(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        $source = '/' . ltrim(trim((string)$request->post('source_url', '')), '/');
        $target = '/' . ltrim(trim((string)$request->post('target_url', '')), '/');
        $statusCode = (int)$request->post('status_code', 301);

        if (empty($source) || empty($target) || $source === $target) {
            Session::flash('error', 'Please enter valid, distinct source and target paths.');
            return $resp->redirect($adminPath . '/seo-audit/redirects');
        }

        $db = Database::getInstance();
        try {
            $db->execute(
                "INSERT INTO redirects (source_url, target_url, status_code, hits, created_at)
                 VALUES (?, ?, ?, 0, ?)",
                [$source, $target, $statusCode, date('Y-m-d H:i:s')]
            );
            Session::flash('success', "Redirect rule from '{$source}' to '{$target}' added.");
        } catch (\Throwable $e) {
            Session::flash('error', 'Redirect rule already exists for this source path.');
        }

        return $resp->redirect($adminPath . '/seo-audit/redirects');
    }

    public function deleteRedirect(int $id): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $db->execute("DELETE FROM redirects WHERE id = ?", [$id]);
        Session::flash('success', 'Redirect rule removed.');
        return $resp->redirect($adminPath . '/seo-audit/redirects');
    }

    public function log404(): Response
    {
        $db = Database::getInstance();
        $logs = $db->fetchAll("SELECT * FROM log_404 ORDER BY hits DESC, last_seen_at DESC LIMIT 100");

        $content = View::render('admin/seo-audit/log_404', [
            'logs' => $logs,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => '404 Crawl Error Log',
            'meta_title' => '404 Log — Multan Tech Accessories',
        ]));
    }

    public function clear404(): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $db->execute("DELETE FROM log_404");
        Session::flash('success', '404 log cleared.');
        return $resp->redirect($adminPath . '/seo-audit/log-404');
    }
}
