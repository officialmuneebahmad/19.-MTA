<?php
/**
 * Multan Tech Accessories — SeoController
 * Dynamic XML Sitemaps with strict content gate filters & real lastmod
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\SitemapService;

class SeoController
{
    private function xmlResponse(string $filename): Response
    {
        $xml = SitemapService::getSitemapXml($filename);
        if ($xml === null) {
            $resp = new Response();
            $resp->status(404);
            $resp->header('Content-Type', 'text/plain; charset=UTF-8');
            $resp->body("404 Not Found: Sitemap '{$filename}' does not exist.");
            return $resp;
        }

        $etag = '"' . md5($xml) . '"';
        $lastModified = gmdate('D, d M Y H:i:s') . ' GMT';

        $resp = new Response();
        $resp->status(200);
        $resp->header('Content-Type', 'application/xml; charset=UTF-8');
        $resp->header('Cache-Control', 'public, max-age=3600');
        $resp->header('ETag', $etag);
        $resp->header('Last-Modified', $lastModified);
        $resp->body($xml);
        return $resp;
    }

    public function index(): Response
    {
        return $this->xmlResponse('sitemap.xml');
    }

    public function pages(): Response
    {
        return $this->xmlResponse('sitemap-pages.xml');
    }

    public function categories(): Response
    {
        return $this->xmlResponse('sitemap-categories.xml');
    }

    public function brands(): Response
    {
        return $this->xmlResponse('sitemap-brands.xml');
    }

    public function guides(): Response
    {
        return $this->xmlResponse('sitemap-guides.xml');
    }

    public function products(): Response
    {
        return $this->xmlResponse('sitemap-products-1.xml');
    }

    public function products1(): Response
    {
        return $this->xmlResponse('sitemap-products-1.xml');
    }

    public function products2(): Response
    {
        return $this->xmlResponse('sitemap-products-2.xml');
    }

    public function products3(): Response
    {
        return $this->xmlResponse('sitemap-products-3.xml');
    }

    public function submap(Request $request, string $name): Response
    {
        $cleanName = basename($name);
        return $this->xmlResponse($cleanName);
    }
}
