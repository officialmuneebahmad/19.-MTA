<?php
/**
 * Multan Tech Accessories — SearchController
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\SearchService;
use App\Services\SeoService;

class SearchController
{
    public function index(Request $request): Response
    {
        $query = trim((string)$request->get('q', ''));
        $page = max(1, (int)$request->get('page', 1));
        $perPage = 24;
        $offset = ($page - 1) * $perPage;

        $searchResult = SearchService::search($query, $perPage, $offset);
        $products = $searchResult['items'];
        $total = $searchResult['total'];
        $totalPages = max(1, (int)ceil($total / $perPage));

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Search', 'url' => null],
        ];

        $resp = new Response();
        $resp->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
        $resp->header('Pragma', 'no-cache');
        $resp->header('Expires', '0');

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
        $resp->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
        $resp->header('Pragma', 'no-cache');
        $resp->header('Expires', '0');

        if (mb_strlen($query) < 2) {
            return $resp->json([]);
        }

        $items = SearchService::suggestions($query, 8);

        return $resp->json($items);
    }
}
