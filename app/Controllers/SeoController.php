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

    public function bingSiteAuth(): Response
    {
        $filePath = MTA_ROOT . '/BingSiteAuth.xml';
        if (file_exists($filePath)) {
            $content = (string)file_get_contents($filePath);
        } else {
            $content = "<?xml version=\"1.0\"?>\n<users>\n\t<user>F4B2E303F2C1F9F0C7B9395E143E6270</user>\n</users>\n";
        }

        $resp = new Response();
        $resp->status(200);
        $resp->header('Content-Type', 'application/xml; charset=UTF-8');
        $resp->header('Cache-Control', 'public, max-age=86400');
        $resp->body($content);
        return $resp;
    }
}
