<?php
/**
 * Multan Tech Accessories — CategoryController
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\SeoService;

class CategoryController
{
    public function index(): Response
    {
        $db = Database::getInstance();
        $categories = $db->fetchAll(
            "SELECT id, slug, name, intro_html, product_count 
             FROM categories 
             WHERE parent_id IS NULL AND is_active = 1 
             ORDER BY sort_order ASC, name ASC"
        );

        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Categories', 'url' => null],
        ];

        $html = View::render('storefront/categories', [
            'meta_title' => SeoService::title('Shop Computer & Mobile Accessories by Category'),
            'meta_description' => 'Browse all tech accessory categories at Multan Tech Accessories. Chargers, cables, audio, storage & gaming gear in Pakistan.',
            'canonical_url' => mta_url('/categories'),
            'categories' => $categories,
            'breadcrumbs' => $breadcrumbs,
        ]);

        $resp = new Response();
        return $resp->html($html);
    }

    public function show(string $slug, Request $request): Response
    {
        $db = Database::getInstance();
        $category = $db->fetch("SELECT * FROM categories WHERE slug = ? AND is_active = 1 LIMIT 1", [$slug]);

        $resp = new Response();
        if (!$category) {
            $resp->status(404);
            return $resp->html(View::render('storefront/errors/404', [
                'meta_title' => 'Category Not Found — Multan Tech Accessories',
                'meta_description' => 'The requested category was not found.',
                'noindex' => true,
            ]), 404);
        }

        // Subcategories
        $subcategories = $db->fetchAll(
            "SELECT id, slug, name, product_count FROM categories WHERE parent_id = ? AND is_active = 1 ORDER BY sort_order ASC",
            [$category['id']]
        );

        // Sorting
        $sort = (string)$request->query('sort', 'popular');
        $orderBy = match ($sort) {
            'newest' => 'p.created_at DESC, p.id DESC',
            'price_asc' => 'p.sell_price ASC, p.id DESC',
            'price_desc' => 'p.sell_price DESC, p.id DESC',
            default => 'p.popularity_score DESC, p.id DESC',
        };

        // Paginated Products
        $page = max(1, (int)$request->query('page', 1));
        $catIds = [$category['id']];
        foreach ($subcategories as $sub) {
            $catIds[] = $sub['id'];
        }
        $catPlaceholders = implode(',', array_fill(0, count($catIds), '?'));

        $baseSql = "SELECT p.*, b.name AS brand_name 
                    FROM products p
                    LEFT JOIN brands b ON b.id = p.brand_id
                    WHERE p.category_id IN ({$catPlaceholders}) AND p.is_active = 1 AND p.is_published = 1
                    ORDER BY {$orderBy}";

        $pagination = $db->paginate($baseSql, $catIds, $page, 24);

        // Breadcrumbs
        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Categories', 'url' => '/categories'],
        ];
        if (!empty($category['parent_id'])) {
            $parent = $db->fetch("SELECT name, slug FROM categories WHERE id = ?", [$category['parent_id']]);
            if ($parent) {
                $breadcrumbs[] = ['label' => $parent['name'], 'url' => '/categories/' . $parent['slug']];
            }
        }
        $breadcrumbs[] = ['label' => $category['name'], 'url' => null];

        // Section 5 rule: Category indexable only if it has at least 4 products and unique intro text; otherwise noindex, follow
        $isIndexable = ($pagination['total'] >= 4) && !empty($category['intro_html']) && empty($request->query('sort'));

        // SEO Title (<= 60 chars) & Description
        $metaTitle = SeoService::categoryTitle($category['name']);
        $metaDescription = !empty($category['meta_description']) ? $category['meta_description'] : SeoService::categoryMetaDescription($category['name']);

        $html = View::render('storefront/category', [
            'category' => $category,
            'subcategories' => $subcategories,
            'products' => $pagination['data'],
            'pagination' => $pagination,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => mta_url('/categories/' . $category['slug']), // Sort canonicalizes to clean URL
            'noindex' => !$isIndexable,
        ]);

        return $resp->html($html);
    }
}
