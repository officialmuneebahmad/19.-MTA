<?php
/**
 * Multan Tech Accessories — Editorial Guides Controller
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
    public function index(Request $request): Response
    {
        $db = Database::getInstance();
        $posts = $db->fetchAll(
            "SELECT p.*, a.name AS author_name, a.slug AS author_slug, a.avatar AS author_avatar
             FROM posts p
             LEFT JOIN authors a ON a.id = p.author_id
             WHERE p.status = 'published'
             ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC"
        );

        // Organize guides into active editorial topics (only topics with >= 1 published guide)
        $topics = [];
        $groupedGuides = [];
        foreach ($posts as &$post) {
            $topic = mta_guide_topic((string)$post['slug'], (string)$post['title']);
            $post['topic'] = $topic;
            $post['reading_time'] = mta_reading_time((string)($post['content_html'] ?? ''));

            $tSlug = $topic['slug'];
            if (!isset($topics[$tSlug])) {
                $topics[$tSlug] = $topic + ['count' => 0];
                $groupedGuides[$tSlug] = [];
            }
            $topics[$tSlug]['count']++;
            $groupedGuides[$tSlug][] = $post;
        }
        unset($post);

        $activeTopicSlug = trim((string)$request->get('topic', ''));
        $isFiltered = ($activeTopicSlug !== '' && isset($topics[$activeTopicSlug]));
        $filteredPosts = $isFiltered ? $groupedGuides[$activeTopicSlug] : $posts;

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Buying Guides', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/guides/index', [
            'guides' => $filteredPosts,
            'allGuidesCount' => count($posts),
            'topics' => array_values($topics),
            'groupedGuides' => $groupedGuides,
            'activeTopicSlug' => $isFiltered ? $activeTopicSlug : '',
            'storeCategories' => mta_nav_categories(),
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Tech Buying Guides and Advice'),
            'meta_description' => 'Read practical buying guides on USB-C chargers, power banks, fast-charging cables, budget gaming gear, and avoiding counterfeit tech accessories in Pakistan.',
            'canonical' => mta_url('/guides'),
            'canonical_url' => mta_url('/guides'),
            'noindex' => $isFiltered,
        ]));
    }

    public function show(string $slug, Request $request): Response
    {
        $db = Database::getInstance();
        $guide = $db->fetch(
            "SELECT p.*, a.name AS author_name, a.slug AS author_slug, a.bio AS author_bio, a.avatar AS author_avatar
             FROM posts p
             LEFT JOIN authors a ON a.id = p.author_id
             WHERE p.slug = ? AND p.status = 'published' LIMIT 1",
            [$slug]
        );

        $resp = new Response();
        if (!$guide) {
            $resp->status(404);
            return $resp->html(View::render('storefront/errors/404', [
                'meta_title' => 'Guide Not Found | Multan Tech Accessories',
                'meta_description' => 'The requested hardware buying guide could not be found.',
                'noindex' => true,
            ]), 404);
        }

        $topic = mta_guide_topic((string)$guide['slug'], (string)$guide['title']);
        $readingTime = mta_reading_time((string)($guide['content_html'] ?? ''));

        // Fetch other published guides cleanly and prioritize same-topic matches for Related Guides
        $otherGuides = $db->fetchAll(
            "SELECT p.id, p.slug, p.title, p.excerpt, p.featured_image, p.content_html, p.published_at, p.updated_at,
                    a.name AS author_name
             FROM posts p
             LEFT JOIN authors a ON a.id = p.author_id
             WHERE p.status = 'published' AND p.id != ?
             ORDER BY COALESCE(p.published_at, p.created_at) DESC, p.id DESC",
            [$guide['id']]
        );

        usort($otherGuides, function (array $a, array $b) use ($topic): int {
            $aTopic = mta_guide_topic((string)$a['slug'], (string)$a['title'])['slug'];
            $bTopic = mta_guide_topic((string)$b['slug'], (string)$b['title'])['slug'];
            $aSame = ($aTopic === $topic['slug']) ? 0 : 1;
            $bSame = ($bTopic === $topic['slug']) ? 0 : 1;
            return $aSame <=> $bSame;
        });

        $relatedGuides = array_slice($otherGuides, 0, 3);
        foreach ($relatedGuides as &$rel) {
            $rel['topic'] = mta_guide_topic((string)$rel['slug'], (string)$rel['title']);
            $rel['reading_time'] = mta_reading_time((string)($rel['content_html'] ?? ''));
        }
        unset($rel);

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Buying Guides', 'url' => '/guides'],
            ['title' => $guide['title'], 'url' => null],
        ];

        $rawMetaTitle = trim((string)($guide['meta_title'] ?? ''));
        $metaTitle = $rawMetaTitle !== ''
            ? (mb_strlen($rawMetaTitle) <= 60 ? $rawMetaTitle : SeoService::title($rawMetaTitle, 'guide'))
            : SeoService::title((string)$guide['title'], 'guide');

        $rawMetaDesc = trim((string)($guide['meta_description'] ?? $guide['excerpt'] ?? ''));
        $metaDesc = $rawMetaDesc !== ''
            ? (mb_strlen($rawMetaDesc) <= 160 ? $rawMetaDesc : mb_substr($rawMetaDesc, 0, 157) . '...')
            : SeoService::metaDescription((string)$guide['title']);

        $articleImage = trim((string)($guide['featured_image'] ?? ''));
        if ($articleImage === '' && preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', (string)($guide['content_html'] ?? ''), $imgMatch)) {
            $articleImage = $imgMatch[1];
        }
        if ($articleImage === '') {
            $articleImage = !empty($guide['author_avatar']) ? (string)$guide['author_avatar'] : '/assets/images/muneeb-ahmad.jpg';
        }
        $absoluteImageUrl = str_starts_with($articleImage, 'http') ? $articleImage : mta_url($articleImage);

        $wordCount = str_word_count(strip_tags((string)($guide['content_html'] ?? '')));

        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => mta_url('/guides/' . $guide['slug']),
            ],
            'headline' => $guide['title'],
            'description' => $metaDesc,
            'articleSection' => $topic['label'],
            'wordCount' => $wordCount,
            'image' => [$absoluteImageUrl],
            'author' => [
                '@type' => 'Person',
                'name' => $guide['author_name'] ?? 'Muneeb Ahmad',
                'url' => mta_url('/author/' . ($guide['author_slug'] ?? 'muneeb-ahmad')),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Multan Tech Accessories',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => mta_url('/assets/images/logo.png'),
                ],
            ],
            'datePublished' => date('c', strtotime((string)($guide['published_at'] ?? $guide['created_at']))),
            'dateModified' => date('c', strtotime((string)($guide['updated_at'] ?? $guide['published_at'] ?? $guide['created_at']))),
        ];

        $suggestedCatSlug = (string)($topic['category_slug'] ?? '');
        $showSuggestedCategory = $suggestedCatSlug !== '' && mta_category_has_products($suggestedCatSlug);

        return $resp->html(View::render('storefront/guides/show', [
            'guide' => $guide,
            'topic' => $topic,
            'readingTime' => $readingTime,
            'relatedGuides' => $relatedGuides,
            'showSuggestedCategory' => $showSuggestedCategory,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
            'canonical' => mta_url('/guides/' . $guide['slug']),
            'canonical_url' => mta_url('/guides/' . $guide['slug']),
            'og_type' => 'article',
            'og_image' => $absoluteImageUrl,
            'structured_data' => $structuredData,
        ]));
    }

    public function author(string $slug): Response
    {
        $db = Database::getInstance();
        $author = $db->fetch("SELECT * FROM authors WHERE slug = ? LIMIT 1", [$slug]);

        $resp = new Response();
        if (!$author) {
            $resp->status(404);
            return $resp->html(View::render('storefront/errors/404', [
                'meta_title' => 'Author Not Found | Multan Tech Accessories',
                'meta_description' => 'The requested author profile does not exist.',
                'noindex' => true,
            ]), 404);
        }

        $guides = $db->fetchAll(
            "SELECT id, slug, title, excerpt, content_html, published_at, updated_at
             FROM posts
             WHERE author_id = ? AND status = 'published'
             ORDER BY COALESCE(published_at, created_at) DESC, id DESC",
            [$author['id']]
        );

        foreach ($guides as &$g) {
            $g['topic'] = mta_guide_topic((string)$g['slug'], (string)$g['title']);
            $g['reading_time'] = mta_reading_time((string)($g['content_html'] ?? ''));
        }
        unset($g);

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Buying Guides', 'url' => '/guides'],
            ['title' => $author['name'], 'url' => null],
        ];

        return $resp->html(View::render('storefront/author', [
            'author' => $author,
            'guides' => $guides,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title($author['name'] . ' Articles and Profile'),
            'meta_description' => "Read hardware buying guides and practical tech accessory advice written by {$author['name']}, Founder of Multan Tech Accessories.",
            'canonical' => mta_url('/author/' . $author['slug']),
            'canonical_url' => mta_url('/author/' . $author['slug']),
        ]));
    }
}
