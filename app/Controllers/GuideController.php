<?php
/**
 * Multan Tech Accessories — GuideController
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\SeoService;

class GuideController
{
    public function index(): Response
    {
        $db = Database::getInstance();
        $guides = $db->fetchAll(
            "SELECT p.*, a.name AS author_name, a.slug AS author_slug 
             FROM posts p
             LEFT JOIN authors a ON a.id = p.author_id
             WHERE p.status = 'published'
             ORDER BY p.id DESC"
        );

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Tech Guides', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/guides/index', [
            'guides' => $guides,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Tech Buying Guides & Advice'),
            'meta_description' => 'Expert tech buying guides, hardware tips, and mobile accessory advice tailored for Pakistani consumers by Muneeb Ahmad.',
            'canonical' => mta_url('/guides'),
        ]));
    }

    public function show(string $slug, Request $request): Response
    {
        $db = Database::getInstance();
        $guide = $db->fetch(
            "SELECT p.*, a.name AS author_name, a.slug AS author_slug, a.bio AS author_bio 
             FROM posts p
             LEFT JOIN authors a ON a.id = p.author_id
             WHERE p.slug = ? AND p.status = 'published' 
             LIMIT 1",
            [$slug]
        );

        $resp = new Response();

        if (!$guide) {
            $resp->status(404);
            return $resp->html(View::render('storefront/errors/404', [
                'meta_title' => 'Guide Not Found — Multan Tech Accessories',
                'meta_description' => 'The requested tech guide was not found.',
                'noindex' => true,
            ]), 404);
        }

        $author = [
            'name' => $guide['author_name'] ?? 'Muneeb Ahmad',
            'slug' => $guide['author_slug'] ?? 'muneeb-ahmad',
            'bio' => $guide['author_bio'] ?? 'Founder and lead developer of Multan Tech Accessories.',
        ];

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Guides', 'url' => '/guides'],
            ['title' => $guide['title'], 'url' => null],
        ];

        // BlogPosting Schema
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $guide['title'],
            'description' => $guide['excerpt'] ?? substr(strip_tags($guide['content_html']), 0, 160),
            'url' => mta_url('/guides/' . $guide['slug']),
            'datePublished' => date('c', strtotime($guide['published_at'] ?? $guide['created_at'])),
            'dateModified' => date('c', strtotime($guide['updated_at'] ?? $guide['created_at'])),
            'author' => [
                '@type' => 'Person',
                'name' => $author['name'],
                'url' => mta_url('/author/' . $author['slug']),
                'jobTitle' => 'Founder & Developer',
                'worksFor' => [
                    '@type' => 'Organization',
                    'name' => 'Multan Tech Accessories',
                    'url' => 'https://multantech.store',
                ],
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Multan Tech Accessories',
                'url' => 'https://multantech.store',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => mta_url('/assets/images/logo.webp'),
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => mta_url('/guides/' . $guide['slug']),
            ],
        ];

        $metaDesc = $guide['excerpt'] ?? substr(strip_tags($guide['content_html']), 0, 155);

        return $resp->html(View::render('storefront/guides/show', [
            'guide' => $guide,
            'author' => $author,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title($guide['title']),
            'meta_description' => SeoService::metaDescription($metaDesc),
            'canonical' => mta_url('/guides/' . $guide['slug']),
            'structured_data' => $structuredData,
        ]));
    }

    public function author(string $slug): Response
    {
        $db = Database::getInstance();
        $author = $db->fetch("SELECT * FROM authors WHERE slug = ? LIMIT 1", [$slug]);

        // Default to Muneeb Ahmad if not found in db table
        if (!$author) {
            $author = [
                'name' => 'Muneeb Ahmad',
                'slug' => 'muneeb-ahmad',
                'bio' => 'Founder, lead architect, and developer of Multan Tech Accessories. Dedicated to providing authentic computer hardware, fast charging gear, and durable mobile accessories across Pakistan.',
            ];
        }

        $authorGuides = $db->fetchAll(
            "SELECT id, slug, title, excerpt, created_at 
             FROM posts 
             WHERE status = 'published' 
             ORDER BY id DESC"
        );

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Authors', 'url' => null],
            ['title' => $author['name'], 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/author', [
            'author' => $author,
            'authorGuides' => $authorGuides,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title($author['name'] . ' — Founder & Developer'),
            'meta_description' => 'Author profile of Muneeb Ahmad, founder and developer of Multan Tech Accessories. Hardware guides and tech insights.',
            'canonical' => mta_url('/author/' . ($author['slug'] ?? 'muneeb-ahmad')),
        ]));
    }
}
