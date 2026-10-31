<?php
/**
 * Multan Tech Accessories — Admin ExportController (Part B)
 * MultanTech.store
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\AuditLogger;

class ExportController
{
    public function index(): Response
    {
        $db = Database::getInstance();
        $adminPath = mta_admin_path();

        // Count totals
        $prodCount = $db->fetch("SELECT COUNT(*) as c FROM products")['c'] ?? 0;
        $orderCount = $db->fetch("SELECT COUNT(*) as c FROM orders")['c'] ?? 0;
        $custCount = $db->fetch("SELECT COUNT(DISTINCT customer_phone) as c FROM orders")['c'] ?? 0;
        $inqCount = $db->fetch("SELECT COUNT(*) as c FROM whatsapp_inquiries")['c'] ?? 0;

        // Recent export audit logs
        $recentLogs = $db->fetchAll(
            "SELECT * FROM audit_log WHERE action = 'export_data' ORDER BY id DESC LIMIT 10"
        );

        $content = View::render('admin/export/index', [
            'prodCount' => (int)$prodCount,
            'orderCount' => (int)$orderCount,
            'custCount' => (int)$custCount,
            'inqCount' => (int)$inqCount,
            'recentLogs' => $recentLogs,
            'isReauth' => Session::isReauthenticated(),
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Data Export & Backup Center',
            'meta_title' => 'Export Data — Multan Tech Accessories',
        ]));
    }

    public function download(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        // 1. Re-Authentication Gate Check (Required within 15 minutes)
        if (!Session::isReauthenticated()) {
            Session::flash('warning', 'Security policy requires password verification before exporting store data.');
            return $resp->redirect($adminPath . '/reauth?return_to=' . urlencode($adminPath . '/export'));
        }

        $dataset = trim((string)$request->post('dataset', 'products'));
        $format = strtolower(trim((string)$request->post('format', 'json')));
        $db = Database::getInstance();

        $rows = [];
        $filenamePrefix = 'mta-' . $dataset;

        switch ($dataset) {
            case 'orders':
                $rows = $db->fetchAll(
                    "SELECT id, order_number, customer_name, customer_phone, city, shipping_address,
                            subtotal, delivery_charge, total, advance_amount, advance_verified_at,
                            cod_due, payment_method, status, courier_name, tracking_number, created_at
                     FROM orders ORDER BY id DESC"
                );
                break;

            case 'customers':
                $rows = $db->fetchAll(
                    "SELECT customer_name, customer_phone, city, shipping_address, 
                            COUNT(id) as total_orders, SUM(total) as lifetime_spend, 
                            MAX(created_at) as last_order_date
                     FROM orders
                     GROUP BY customer_phone
                     ORDER BY lifetime_spend DESC"
                );
                break;

            case 'inquiries':
                $rows = $db->fetchAll(
                    "SELECT id, product_id, product_title, page_url, ip_address, user_agent, created_at
                     FROM whatsapp_inquiries ORDER BY id DESC"
                );
                break;

            case 'products':
            default:
                $rows = $db->fetchAll(
                    "SELECT id, sku, slug, title, category_id, brand_id, supplier_price, sell_price,
                            price_override, status, primary_image_url, stock_status, created_at, updated_at
                     FROM products ORDER BY id ASC"
                );
                $dataset = 'products';
                break;
        }

        $dateStr = date('Y-m-d_His');
        $filename = "{$filenamePrefix}_{$dateStr}.{$format}";

        // Audit log the export
        AuditLogger::log('export_data', $dataset, null, [
            'format' => $format,
            'row_count' => count($rows),
            'filename' => $filename,
        ]);

        if ($format === 'csv') {
            $output = fopen('php://temp', 'r+');
            if (!empty($rows)) {
                // Header row
                fputcsv($output, array_keys($rows[0]));
                // Data rows
                foreach ($rows as $row) {
                    fputcsv($output, array_values($row));
                }
            }
            rewind($output);
            $csvContent = stream_get_contents($output);
            fclose($output);

            $resp->header('Content-Type', 'text/csv; charset=UTF-8');
            $resp->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $resp->header('Pragma', 'no-cache');
            $resp->header('Expires', '0');
            $resp->body($csvContent);
            return $resp;
        }

        // Default JSON
        $resp->header('Content-Type', 'application/json; charset=UTF-8');
        $resp->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $resp->header('Pragma', 'no-cache');
        $resp->header('Expires', '0');
        $resp->body((string)json_encode([
            'store' => 'Multan Tech Accessories',
            'dataset' => $dataset,
            'exported_at' => date('c'),
            'total_records' => count($rows),
            'data' => $rows,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $resp;
    }
}

