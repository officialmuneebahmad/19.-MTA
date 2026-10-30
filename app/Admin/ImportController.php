<?php
/**
 * Multan Tech Accessories — Admin ImportController
 * Multi-Source Product Import: CSV Batch Ingestion & SSRF-Protected Web Scraping
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;

class ImportController
{
    public function index(): Response
    {
        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT id, name FROM categories ORDER BY name ASC");

        $content = View::render('admin/import/index', [
            'categories' => $categories,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Multi-Source Product Import',
            'meta_title' => 'Multi-Source Import — Multan Tech Accessories',
        ]));
    }

    public function importCsv(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        if (empty($_FILES['csv_file']['tmp_name']) || !is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
            Session::flash('error', 'Please upload a valid CSV file.');
            return $resp->redirect($adminPath . '/import');
        }

        $filePath = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            Session::flash('error', 'Could not open uploaded file.');
            return $resp->redirect($adminPath . '/import');
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            Session::flash('error', 'Uploaded CSV file is empty.');
            return $resp->redirect($adminPath . '/import');
        }

        $header = array_map(fn($col) => strtolower(trim((string)$col)), $header);
        $db = Database::getInstance();
        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) continue;
            $data = array_combine($header, array_pad($row, count($header), ''));

            $title = trim($data['title'] ?? $data['name'] ?? '');
            $price = (float)($data['price'] ?? $data['sell_price'] ?? 0);
            $price = (float)\App\Services\PricingService::snapPrice($price);
            if ($title === '' || $price <= 0) {
                $skipped++;
                continue;
            }

            $sku = trim($data['sku'] ?? 'MTA-CSV-' . substr(bin2hex(random_bytes(3)), 0, 6));
            $cleanTitle = preg_replace('/[^a-z0-9]+/i', '-', strtolower($title));
            $slug = trim($cleanTitle, '-') . '-' . substr(bin2hex(random_bytes(2)), 0, 4);

            $imgUrl = trim($data['image_url'] ?? $data['image'] ?? '/assets/images/placeholder.webp');
            $desc = Security::sanitizeHtml($data['description'] ?? '');

            try {
                $db->execute(
                    "INSERT INTO products (
                        supplier_id, sku, title, slug, supplier_price, sell_price, stock_status,
                        content_state, is_active, is_published, source, primary_image_url, description_html, created_at
                     ) VALUES (?, ?, ?, ?, ?, ?, 'in_stock', 'approved', 1, 1, 'manual', ?, ?, ?)",
                    [
                        'csv-' . bin2hex(random_bytes(4)),
                        $sku,
                        $title,
                        $slug,
                        $price * 0.9,
                        $price,
                        $imgUrl,
                        $desc,
                        date('Y-m-d H:i:s'),
                    ]
                );
                $imported++;
            } catch (\Throwable $e) {
                $skipped++;
            }
        }
        fclose($handle);

        Session::flash('success', "CSV Import Complete: {$imported} products added successfully, {$skipped} skipped.");
        return $resp->redirect($adminPath . '/products');
    }

    public function importUrl(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $targetUrl = trim((string)$request->post('target_url', ''));

        if (empty($targetUrl)) {
            Session::flash('error', 'Please enter a target product webpage URL.');
            return $resp->redirect($adminPath . '/import');
        }

        // SSRF Guard via Core\Security
        if (!Security::isSafeUrl($targetUrl)) {
            Session::flash('error', 'Access denied: Target URL failed SSRF security verification (private/internal networks and invalid schemes are blocked).');
            return $resp->redirect($adminPath . '/import');
        }

        // Fetch remote content with timeout
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $targetUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) MultanTechBot/1.0',
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$html || $httpCode !== 200) {
            Session::flash('error', "Could not scrape page (HTTP Status: {$httpCode}). Verify URL and retry.");
            return $resp->redirect($adminPath . '/import');
        }

        // Basic DOM extraction
        $extractedTitle = '';
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            $extractedTitle = trim(html_entity_decode(strip_tags($matches[1])));
            $extractedTitle = preg_replace('/(\s*[-|]\s*.*)$/', '', $extractedTitle); // Strip brand suffix
        }

        $price = (float)$request->post('fallback_price', 1500);
        $price = (float)\App\Services\PricingService::snapPrice($price);
        $categoryId = (int)$request->post('category_id', 1);

        if ($extractedTitle === '') {
            $extractedTitle = 'Imported Hardware Accessory ' . date('ymd-His');
        }

        $cleanTitle = preg_replace('/[^a-z0-9]+/i', '-', strtolower($extractedTitle));
        $slug = trim($cleanTitle, '-') . '-' . substr(bin2hex(random_bytes(2)), 0, 4);

        $db = Database::getInstance();
        $db->execute(
            "INSERT INTO products (
                supplier_id, sku, title, slug, category_id, supplier_price, sell_price, stock_status,
                content_state, is_active, is_published, source, primary_image_url, description_html, created_at
             ) VALUES (?, ?, ?, ?, ?, ?, ?, 'in_stock', 'supplier_raw', 1, 1, 'scraped', '/assets/images/placeholder.webp', ?, ?)",
            [
                'url-' . bin2hex(random_bytes(4)),
                'MTA-SCR-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5)),
                $extractedTitle,
                $slug,
                $categoryId ?: null,
                $price * 0.9,
                $price,
                '<p>Imported from ' . htmlspecialchars($targetUrl, ENT_QUOTES, 'UTF-8') . '</p>',
                date('Y-m-d H:i:s'),
            ]
        );

        $newId = (int)$db->lastInsertId();
        Session::flash('success', "Imported product draft '{$extractedTitle}' successfully.");
        return $resp->redirect($adminPath . '/products/' . $newId . '/edit');
    }
}
