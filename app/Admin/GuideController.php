<?php
/**
 * Multan Tech Accessories — Admin GuideController
 * Editorial Management of Tech Buying Guides
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;

class GuideController
{
    public function index(): Response
    {
        $db = Database::getInstance();
        $guides = $db->fetchAll(
            "SELECT p.*, a.name AS author_name
             FROM posts p
             LEFT JOIN authors a ON a.id = p.author_id
             ORDER BY p.id DESC"
        );

        $content = View::render('admin/guides/index', [
            'guides' => $guides,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Tech Buying Guides & Editorial Content',
            'meta_title' => 'Guides — Multan Tech Accessories',
        ]));
    }

    public function create(): Response
    {
        $db = Database::getInstance();
        $authors = $db->fetchAll("SELECT id, name FROM authors ORDER BY name ASC");

        $content = View::render('admin/guides/edit', [
            'guide' => null,
            'authors' => $authors,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Create New Buying Guide',
            'meta_title' => 'Create Guide — Multan Tech Accessories',
        ]));
    }

    public function store(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        $title = trim((string)$request->post('title', ''));
        $contentHtml = trim((string)$request->post('content_html', ''));
        $excerpt = trim((string)$request->post('excerpt', ''));
        $status = trim((string)$request->post('status', 'draft'));
        $authorId = (int)$request->post('author_id', 1);
        $metaTitle = trim((string)$request->post('meta_title', ''));
        $metaDesc = trim((string)$request->post('meta_description', ''));

        if (empty($title) || empty($contentHtml)) {
            Session::flash('error', 'Title and content are required.');
            return $resp->redirect($adminPath . '/guides/create');
        }

        $clean = preg_replace('/[^a-z0-9]+/i', '-', strtolower($title));
        $slug = trim($clean, '-');

        $db = Database::getInstance();
        $db->execute(
            "INSERT INTO posts (
                title, slug, excerpt, content_html, status, author_id,
                meta_title, meta_description, published_at, created_at
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $title,
                $slug,
                $excerpt ?: substr(strip_tags($contentHtml), 0, 160),
                $contentHtml,
                $status,
                $authorId ?: 1,
                $metaTitle ?: null,
                $metaDesc ?: null,
                $status === 'published' ? date('Y-m-d H:i:s') : null,
                date('Y-m-d H:i:s'),
            ]
        );

        Session::flash('success', "Guide '{$title}' created successfully.");
        return $resp->redirect($adminPath . '/guides');
    }

    public function edit(int $id): Response
    {
        $db = Database::getInstance();
        $guide = $db->fetch("SELECT * FROM posts WHERE id = ? LIMIT 1", [$id]);

        $resp = new Response();
        if (!$guide) {
            Session::flash('error', 'Guide not found.');
            return $resp->redirect(mta_admin_path('guides'));
        }

        $authors = $db->fetchAll("SELECT id, name FROM authors ORDER BY name ASC");

        $content = View::render('admin/guides/edit', [
            'guide' => $guide,
            'authors' => $authors,
        ]);

        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Edit Guide — ' . $guide['title'],
            'meta_title' => 'Edit Guide — Multan Tech Accessories',
        ]));
    }

    public function update(int $id, Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $guide = $db->fetch("SELECT * FROM posts WHERE id = ? LIMIT 1", [$id]);
        if (!$guide) {
            Session::flash('error', 'Guide not found.');
            return $resp->redirect($adminPath . '/guides');
        }

        $title = trim((string)$request->post('title', $guide['title']));
        $contentHtml = trim((string)$request->post('content_html', $guide['content_html']));
        $excerpt = trim((string)$request->post('excerpt', $guide['excerpt']));
        $status = trim((string)$request->post('status', $guide['status']));
        $authorId = (int)$request->post('author_id', $guide['author_id']);
        $metaTitle = trim((string)$request->post('meta_title', ''));
        $metaDesc = trim((string)$request->post('meta_description', ''));

        $db->execute(
            "UPDATE posts SET 
                title = ?, excerpt = ?, content_html = ?, status = ?, author_id = ?,
                meta_title = ?, meta_description = ?, updated_at = ?
             WHERE id = ?",
            [
                $title,
                $excerpt ?: substr(strip_tags($contentHtml), 0, 160),
                $contentHtml,
                $status,
                $authorId ?: 1,
                $metaTitle ?: null,
                $metaDesc ?: null,
                date('Y-m-d H:i:s'),
                $id,
            ]
        );

        Session::flash('success', "Guide '{$title}' updated successfully.");
        return $resp->redirect($adminPath . '/guides');
    }

    public function delete(int $id): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $db->execute("DELETE FROM posts WHERE id = ?", [$id]);
        Session::flash('success', 'Guide deleted successfully.');
        return $resp->redirect($adminPath . '/guides');
    }
}
