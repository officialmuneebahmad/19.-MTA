<?php
/**
 * Multan Tech Accessories — Admin CategoryController
 * Category Management & Supplier Category Mapping Interface
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class CategoryController
{
    public function index(): Response
    {
        $db = Database::getInstance();
        $categories = $db->fetchAll(
            "SELECT c.*, p.name AS parent_name
             FROM categories c
             LEFT JOIN categories p ON p.id = c.parent_id
             ORDER BY c.parent_id ASC, c.sort_order ASC, c.name ASC"
        );

        $parents = $db->fetchAll("SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC");

        $content = View::render('admin/categories/index', [
            'categories' => $categories,
            'parents' => $parents,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Categories & Hierarchy',
            'meta_title' => 'Categories — Multan Tech Accessories',
        ]));
    }

    public function store(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        $name = trim((string)$request->post('name', ''));
        $parentId = (int)$request->post('parent_id', 0) ?: null;
        $intro = trim((string)$request->post('intro_html', ''));
        $sortOrder = (int)$request->post('sort_order', 0);

        if (empty($name)) {
            Session::flash('error', 'Category name is required.');
            return $resp->redirect($adminPath . '/categories');
        }

        $clean = preg_replace('/[^a-z0-9]+/i', '-', strtolower($name));
        $slug = trim($clean, '-');

        $db = Database::getInstance();
        try {
            $db->execute(
                "INSERT INTO categories (name, slug, parent_id, intro_html, sort_order, is_active, created_at)
                 VALUES (?, ?, ?, ?, ?, 1, ?)",
                [$name, $slug, $parentId, $intro ?: null, $sortOrder, date('Y-m-d H:i:s')]
            );
            Session::flash('success', "Category '{$name}' created successfully.");
        } catch (\Throwable $e) {
            Session::flash('error', 'Could not create category: ' . $e->getMessage());
        }

        return $resp->redirect($adminPath . '/categories');
    }

    public function mappings(): Response
    {
        $db = Database::getInstance();
        $mappings = $db->fetchAll(
            "SELECT scm.*, c.name AS site_category_name
             FROM supplier_category_map scm
             LEFT JOIN categories c ON c.id = scm.site_category_id
             ORDER BY scm.supplier_category_title ASC, scm.supplier_subcategory_title ASC"
        );

        $categories = $db->fetchAll("SELECT id, name FROM categories ORDER BY name ASC");

        $content = View::render('admin/categories/mappings', [
            'mappings' => $mappings,
            'categories' => $categories,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Supplier Category Mappings',
            'meta_title' => 'Category Mappings — Multan Tech Accessories',
        ]));
    }

    public function updateMapping(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $mappingId = (int)$request->post('mapping_id', 0);
        $siteCategoryId = (int)$request->post('site_category_id', 0) ?: null;

        if ($mappingId > 0) {
            $db = Database::getInstance();
            $db->execute(
                "UPDATE supplier_category_map SET site_category_id = ?, updated_at = ? WHERE id = ?",
                [$siteCategoryId, date('Y-m-d H:i:s'), $mappingId]
            );

            // Also optionally remap already imported products for this supplier mapping
            $mapping = $db->fetch("SELECT * FROM supplier_category_map WHERE id = ?", [$mappingId]);
            if ($mapping && $siteCategoryId !== null) {
                if (!empty($mapping['supplier_subcategory_id'])) {
                    $db->execute(
                        "UPDATE products SET category_id = ? WHERE supplier_category_id = ? AND supplier_subcategory_id = ?",
                        [$siteCategoryId, $mapping['supplier_category_id'], $mapping['supplier_subcategory_id']]
                    );
                } else {
                    $db->execute(
                        "UPDATE products SET category_id = ? WHERE supplier_category_id = ?",
                        [$siteCategoryId, $mapping['supplier_category_id']]
                    );
                }
            }

            Session::flash('success', 'Mapping updated and linked products synchronized.');
        }

        return $resp->redirect($adminPath . '/categories/mappings');
    }
}
