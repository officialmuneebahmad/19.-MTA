<?php
/**
 * Multan Tech Accessories — Admin BannerController
 * Hero Carousel Slide Management
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class BannerController
{
    public function index(): Response
    {
        $db = Database::getInstance();
        $banners = $db->fetchAll("SELECT * FROM banners ORDER BY sort_order ASC, id DESC");

        $content = View::render('admin/banners/index', [
            'banners' => $banners,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Hero Carousel Banners',
            'meta_title' => 'Banners — Multan Tech Accessories',
        ]));
    }

    public function store(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        $title = trim((string)$request->post('title', ''));
        $subtitle = trim((string)$request->post('subtitle', ''));
        $link = trim((string)$request->post('link', '/categories'));
        $badge = trim((string)$request->post('badge', 'Featured'));
        $imageUrl = trim((string)$request->post('image_url', '/assets/images/banner1.webp'));
        $sortOrder = (int)$request->post('sort_order', 0);
        $isActive = (int)$request->post('is_active', 1);

        if (empty($title)) {
            Session::flash('error', 'Banner title is required.');
            return $resp->redirect($adminPath . '/banners');
        }

        $db = Database::getInstance();
        $db->execute(
            "INSERT INTO banners (title, subtitle, link, badge, image_url, sort_order, is_active, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$title, $subtitle ?: null, $link, $badge ?: null, $imageUrl, $sortOrder, $isActive, date('Y-m-d H:i:s')]
        );

        Session::flash('success', 'New hero banner slide created.');
        return $resp->redirect($adminPath . '/banners');
    }

    public function delete(int $id): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $db->execute("DELETE FROM banners WHERE id = ?", [$id]);
        Session::flash('success', 'Banner deleted successfully.');
        return $resp->redirect($adminPath . '/banners');
    }
}
