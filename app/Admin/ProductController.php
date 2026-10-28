<?php
/**
 * Multan Tech Accessories — Admin ProductController
 * Search, filter, manual price override, pinning, content state control
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class ProductController
{
    public function index(Request $request): Response
    {
        $db = Database::getInstance();
        $page = max(1, (int)$request->get('page', 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;

        $search = trim((string)$request->get('q', ''));
        $categoryId = (int)$request->get('category_id', 0);
        $stockStatus = trim((string)$request->get('stock_status', ''));
        $contentState = trim((string)$request->get('content_state', ''));

        $where = ['1=1'];
        $params = [];

        if ($search !== '') {
            $where[] = '(p.title LIKE ? OR p.sku LIKE ? OR p.supplier_id LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($categoryId > 0) {
            $where[] = 'p.category_id = ?';
            $params[] = $categoryId;
        }

        if ($stockStatus !== '') {
            $where[] = 'p.stock_status = ?';
            $params[] = $stockStatus;
        }

        if ($contentState !== '') {
            $where[] = 'p.content_state = ?';
            $params[] = $contentState;
        }

        $whereClause = implode(' AND ', $where);

        // Count total
        $countRow = $db->fetch(
            "SELECT COUNT(p.id) as total FROM products p WHERE {$whereClause}",
            $params
        );
        $total = (int)($countRow['total'] ?? 0);
        $totalPages = max(1, (int)ceil($total / $perPage));

        // Fetch products
        $fetchParams = $params;
        $fetchParams[] = $perPage;
        $fetchParams[] = $offset;

        $products = $db->fetchAll(
            "SELECT p.*, b.name AS brand_name, c.name AS category_name
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE {$whereClause}
             ORDER BY p.id DESC
             LIMIT ? OFFSET ?",
            $fetchParams
        );

        $categories = $db->fetchAll("SELECT id, name FROM categories ORDER BY name ASC");

        $content = View::render('admin/products/index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId,
            'stockStatus' => $stockStatus,
            'contentState' => $contentState,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Product Catalog Management',
            'meta_title' => 'Products — Admin Multan Tech Accessories',
        ]));
    }

    public function edit(int $id): Response
    {
        $db = Database::getInstance();
        $product = $db->fetch("SELECT * FROM products WHERE id = ? LIMIT 1", [$id]);

        $resp = new Response();
        if (!$product) {
            Session::flash('error', 'Product not found.');
            return $resp->redirect(mta_admin_path('products'));
        }

        $categories = $db->fetchAll("SELECT id, name FROM categories ORDER BY name ASC");
        $brands = $db->fetchAll("SELECT id, name FROM brands ORDER BY name ASC");

        $content = View::render('admin/products/edit', [
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
        ]);

        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Edit Product #' . $id . ' — ' . $product['title'],
            'meta_title' => 'Edit Product — Multan Tech Accessories',
        ]));
    }

    public function update(int $id, Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $product = $db->fetch("SELECT * FROM products WHERE id = ? LIMIT 1", [$id]);
        if (!$product) {
            Session::flash('error', 'Product not found.');
            return $resp->redirect($adminPath . '/products');
        }

        $title = trim((string)$request->post('title', $product['title']));
        $categoryId = (int)$request->post('category_id', $product['category_id']);
        $brandId = (int)$request->post('brand_id', $product['brand_id']);
        $sellPrice = (float)$request->post('sell_price', $product['sell_price']);
        
        $priceOverrideRaw = trim((string)$request->post('price_override', ''));
        $priceOverride = ($priceOverrideRaw !== '') ? (float)$priceOverrideRaw : null;

        $stockStatus = trim((string)$request->post('stock_status', $product['stock_status']));
        $contentState = trim((string)$request->post('content_state', $product['content_state']));
        $isActive = (int)$request->post('is_active', 0);
        $pinned = (int)$request->post('pinned', 0);
        $metaTitle = trim((string)$request->post('meta_title', ''));
        $metaDesc = trim((string)$request->post('meta_description', ''));
        $descHtml = trim((string)$request->post('description_html', ''));

        // If override is set, sell_price reflects override
        if ($priceOverride !== null && $priceOverride > 0) {
            $effectiveSellPrice = $priceOverride;
        } else {
            $effectiveSellPrice = $sellPrice;
        }

        $db->execute(
            "UPDATE products SET 
                title = ?, category_id = ?, brand_id = ?, sell_price = ?, price_override = ?,
                stock_status = ?, content_state = ?, is_active = ?, pinned = ?,
                meta_title = ?, meta_description = ?, description_html = ?, updated_at = ?
             WHERE id = ?",
            [
                $title,
                $categoryId ?: null,
                $brandId ?: null,
                $effectiveSellPrice,
                $priceOverride,
                $stockStatus,
                $contentState,
                $isActive,
                $pinned,
                $metaTitle ?: null,
                $metaDesc ?: null,
                $descHtml ?: null,
                date('Y-m-d H:i:s'),
                $id,
            ]
        );

        Session::flash('success', "Product #{$id} updated successfully.");
        return $resp->redirect($adminPath . '/products');
    }

    public function quickOverride(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $id = (int)$request->post('product_id', 0);

        if ($id <= 0) {
            Session::flash('error', 'Invalid product ID.');
            return $resp->redirect($adminPath . '/products');
        }

        $db = Database::getInstance();
        $product = $db->fetch("SELECT * FROM products WHERE id = ? LIMIT 1", [$id]);
        if (!$product) {
            Session::flash('error', 'Product not found.');
            return $resp->redirect($adminPath . '/products');
        }

        $overrideRaw = trim((string)$request->post('price_override', ''));
        $pinned = (int)$request->post('pinned', 0);
        $isActive = (int)$request->post('is_active', 1);

        $priceOverride = ($overrideRaw !== '') ? (float)$overrideRaw : null;
        $sellPrice = ($priceOverride !== null && $priceOverride > 0) 
            ? $priceOverride 
            : (float)$product['supplier_price'] * 1.10; // default 10% markup fallback

        $db->execute(
            "UPDATE products SET price_override = ?, sell_price = ?, pinned = ?, is_active = ?, updated_at = ? WHERE id = ?",
            [$priceOverride, $sellPrice, $pinned, $isActive, date('Y-m-d H:i:s'), $id]
        );

        Session::flash('success', "Quick updates saved for product #{$id}.");
        return $resp->redirect($adminPath . '/products');
    }
}
