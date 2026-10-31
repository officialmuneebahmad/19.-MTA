<?php
/**
 * Multan Tech Accessories — SearchController
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\SeoService;

class SearchController
{
    public function index(Request $request): Response
    {
        $query = trim((string)$request->get('q', ''));
        $page = max(1, (int)$request->get('page', 1));
        $perPage = 24;
        $offset = ($page - 1) * $perPage;

        $db = Database::getInstance();
        $products = [];
        $total = 0;

        if ($query !== '') {
            $searchTerm = '%' . $query . '%';
            
            // Count total matching
            $countRow = $db->fetch(
                "SELECT COUNT(p.id) as total
                 FROM products p
                 LEFT JOIN brands b ON b.id = p.brand_id
                 WHERE p.is_active = 1 AND p.is_published = 1
                   AND (p.title LIKE ? OR p.sku LIKE ? OR b.name LIKE ? OR p.description_html LIKE ?)",
                [$searchTerm, $searchTerm, $searchTerm, $searchTerm]
            );
            $total = (int)($countRow['total'] ?? 0);

            // Fetch products
            $products = $db->fetchAll(
                "SELECT p.*, b.name AS brand_name
                 FROM products p
                 LEFT JOIN brands b ON b.id = p.brand_id
                 WHERE p.is_active = 1 AND p.is_published = 1
                   AND (p.title LIKE ? OR p.sku LIKE ? OR b.name LIKE ? OR p.description_html LIKE ?)
                 ORDER BY (CASE WHEN p.status = 1 THEN 1 WHEN p.status = 3 THEN 2 ELSE 3 END) ASC, p.popularity_score DESC, p.id DESC
                 LIMIT " . (int)$perPage . " OFFSET " . (int)$offset,
                [$searchTerm, $searchTerm, $searchTerm, $searchTerm]
            );
        }

        $totalPages = max(1, (int)ceil($total / $perPage));

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Search', 'url' => null],
        ];

        $resp = new Response();
        $seoTitle = $query !== '' ? "Search: {$query}" : 'Search Products';

        return $resp->html(View::render('storefront/search', [
            'query' => $query,
            'products' => $products,
            'breadcrumbs' => $breadcrumbs,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
            'meta_title' => SeoService::title($seoTitle),
            'meta_description' => 'Search tech accessories, chargers, cables, earbuds, and gaming gear at Multan Tech Accessories.',
            'noindex' => true, // Mandatory SEO requirement: search pages must never be indexed
        ]));
    }

    public function suggestions(Request $request): Response
    {
        $query = trim((string)$request->get('q', ''));
        $resp = new Response();

        if (mb_strlen($query) < 2) {
            return $resp->json([]);
        }

        $db = Database::getInstance();
        $searchTerm = '%' . $query . '%';

        $items = $db->fetchAll(
            "SELECT p.id, p.title, p.slug, p.sell_price, p.primary_image_url
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.is_active = 1 AND p.is_published = 1
               AND (p.title LIKE ? OR p.sku LIKE ? OR b.name LIKE ?)
             ORDER BY p.popularity_score DESC, p.id DESC
             LIMIT 6",
            [$searchTerm, $searchTerm, $searchTerm]
        );

        return $resp->json($items);
    }
}
