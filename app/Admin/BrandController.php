<?php
/**
 * Multan Tech Accessories — Admin BrandController
 * Brand Logos, Featured Brands & Missing Logos Report
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Cache;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class BrandController
{
    public function index(Request $request): Response
    {
        $db = Database::getInstance();

        // All brands with active product counts
        $brands = $db->fetchAll(
            "SELECT b.*, COUNT(p.id) AS product_count
             FROM brands b
             LEFT JOIN products p ON p.brand_id = b.id AND p.is_active = 1
             WHERE b.is_active = 1
             GROUP BY b.id
             ORDER BY b.is_featured DESC, b.featured_sort ASC, product_count DESC, b.name ASC"
        );

        // Missing logo report (brands with at least 1 product but no logo_path)
        $missingLogos = array_filter($brands, function ($b) {
            return empty($b['logo_path']) && (int)($b['product_count'] ?? 0) > 0;
        });

        $content = View::render('admin/brands/index', [
            'brands' => $brands,
            'missingLogos' => $missingLogos,
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Brand Logos & Featured Brands',
            'meta_title' => 'Brands — Multan Tech Accessories Control',
        ]));
    }

    public function update(string $id, Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $brand = $db->fetch("SELECT * FROM brands WHERE id = ? LIMIT 1", [(int)$id]);
        if (!$brand) {
            Session::flash('error', 'Brand not found.');
            return $resp->redirect($adminPath . '/brands');
        }

        $isFeatured = (int)$request->post('is_featured', 0);
        $featuredSort = (int)$request->post('featured_sort', 0);
        $logoAlt = trim((string)$request->post('logo_alt', '')) ?: ($brand['name'] . ' logo');

        $logoPath = $brand['logo_path'];
        $uploadDir = MTA_ROOT . '/assets/images/brands';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        // Process File Upload if provided
        if (!empty($_FILES['logo']['tmp_name']) && is_uploaded_file($_FILES['logo']['tmp_name'])) {
            $file = $_FILES['logo'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $slug = $brand['slug'];

            if (!in_array($ext, ['svg', 'png', 'webp', 'jpg', 'jpeg'], true)) {
                Session::flash('error', 'Invalid file type. Only SVG, PNG, WebP, and JPEG logos are permitted.');
                return $resp->redirect($adminPath . '/brands');
            }

            if ($ext === 'svg') {
                $sanitized = $this->sanitizeSvg((string)file_get_contents($file['tmp_name']));
                $destFile = $uploadDir . '/' . $slug . '.svg';
                file_put_contents($destFile, $sanitized);
                $logoPath = '/assets/images/brands/' . $slug . '.svg';
            } else {
                $destFile = $uploadDir . '/' . $slug . '.webp';
                if ($this->processRasterImage($file['tmp_name'], $destFile)) {
                    $logoPath = '/assets/images/brands/' . $slug . '.webp';
                } else {
                    Session::flash('error', 'Failed to process raster image.');
                    return $resp->redirect($adminPath . '/brands');
                }
            }
        }

        // Optional Remove Logo
        if ($request->post('remove_logo') === '1') {
            $logoPath = null;
        }

        $db->execute(
            "UPDATE brands 
             SET is_featured = ?, featured_sort = ?, logo_alt = ?, logo_path = ?, updated_at = CURRENT_TIMESTAMP
             WHERE id = ?",
            [$isFeatured, $featuredSort, $logoAlt, $logoPath, $brand['id']]
        );

        Cache::flush();
        Session::flash('success', "Brand '{$brand['name']}' updated successfully.");
        return $resp->redirect($adminPath . '/brands');
    }

    public function bulkUpload(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        if (empty($_FILES['zip_file']['tmp_name']) || !is_uploaded_file($_FILES['zip_file']['tmp_name'])) {
            Session::flash('error', 'Please select a valid ZIP archive containing brand logos.');
            return $resp->redirect($adminPath . '/brands');
        }

        if (!class_exists('ZipArchive')) {
            Session::flash('error', 'PHP ZipArchive extension is not enabled on this server. Please upload logos individually or enable php-zip.');
            return $resp->redirect($adminPath . '/brands');
        }

        $uploadDir = MTA_ROOT . '/assets/images/brands';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($_FILES['zip_file']['tmp_name']) !== true) {
            Session::flash('error', 'Failed to open ZIP archive.');
            return $resp->redirect($adminPath . '/brands');
        }

        $db = Database::getInstance();
        $allBrands = $db->fetchAll("SELECT id, slug, name FROM brands");
        $brandMap = [];
        foreach ($allBrands as $b) {
            $brandMap[strtolower($b['slug'])] = $b;
        }

        $updatedCount = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $filename = basename($stat['name']);
            if (empty($filename) || str_starts_with($filename, '.')) continue;

            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $fileSlug = strtolower(pathinfo($filename, PATHINFO_FILENAME));

            if (!isset($brandMap[$fileSlug])) continue;
            if (!in_array($ext, ['svg', 'png', 'webp', 'jpg', 'jpeg'], true)) continue;

            $content = $zip->getFromIndex($i);
            if ($content === false) continue;

            $targetBrand = $brandMap[$fileSlug];
            $savedRelPath = null;

            if ($ext === 'svg') {
                $sanitized = $this->sanitizeSvg($content);
                $dest = $uploadDir . '/' . $fileSlug . '.svg';
                file_put_contents($dest, $sanitized);
                $savedRelPath = '/assets/images/brands/' . $fileSlug . '.svg';
            } else {
                $tmpFile = tempnam(sys_get_temp_dir(), 'mta_br_');
                file_put_contents($tmpFile, $content);
                $dest = $uploadDir . '/' . $fileSlug . '.webp';
                if ($this->processRasterImage($tmpFile, $dest)) {
                    $savedRelPath = '/assets/images/brands/' . $fileSlug . '.webp';
                }
                @unlink($tmpFile);
            }

            if ($savedRelPath) {
                $db->execute(
                    "UPDATE brands SET logo_path = ?, logo_alt = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
                    [$savedRelPath, $targetBrand['name'] . ' logo', $targetBrand['id']]
                );
                $updatedCount++;
            }
        }
        $zip->close();

        Cache::flush();
        Session::flash('success', "Bulk logo import complete: {$updatedCount} brand logos imported.");
        return $resp->redirect($adminPath . '/brands');
    }

    /**
     * Sanitizes SVG XML to ensure zero script injection or malicious entity execution
     */
    private function sanitizeSvg(string $svg): string
    {
        // Strip doctype and entity references
        $svg = preg_replace('/<!DOCTYPE[^>]*>/i', '', $svg) ?? $svg;
        $svg = preg_replace('/<!ENTITY[^>]*>/i', '', $svg) ?? $svg;

        // Strip scripts
        $svg = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $svg) ?? $svg;

        // Strip inline handlers (e.g. onload, onclick)
        $svg = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $svg) ?? $svg;

        // Strip javascript: pseudo-protocols
        $svg = preg_replace('/href\s*=\s*["\']\s*javascript:[^"\']*["\']/i', 'href="#"', $svg) ?? $svg;

        return trim($svg);
    }

    /**
     * Re-encodes raster images to WebP at 2x max 400px wide
     */
    private function processRasterImage(string $srcPath, string $destPath): bool
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        $data = file_get_contents($srcPath);
        if (!$data) return false;

        $im = @imagecreatefromstring($data);
        if (!$im) return false;

        $origW = imagesx($im);
        $origH = imagesy($im);

        $maxW = 400; // 2x max for 140x72 tile display
        if ($origW > $maxW) {
            $newW = $maxW;
            $newH = (int)round(($origH / $origW) * $newW);

            $dst = imagecreatetruecolor($newW, $newH);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);

            imagecopyresampled($dst, $im, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($im);
            $im = $dst;
        } else {
            imagealphablending($im, false);
            imagesavealpha($im, true);
        }

        $ok = imagewebp($im, $destPath, 90);
        imagedestroy($im);
        return $ok;
    }
}

