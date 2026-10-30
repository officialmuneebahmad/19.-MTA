<?php
/**
 * Multan Tech Accessories — Admin WhatsAppOrderController (Part B)
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
use App\Services\PricingService;

class WhatsAppOrderController
{
    public function index(Request $request): Response
    {
        $db = Database::getInstance();
        $adminPath = mta_admin_path();

        // 1. Inquiries from WhatsApp interactions
        $inquiries = $db->fetchAll(
            "SELECT wi.*, p.slug as product_slug, p.sell_price as product_price
             FROM whatsapp_inquiries wi
             LEFT JOIN products p ON p.id = wi.product_id
             ORDER BY wi.id DESC
             LIMIT 50"
        );

        // 2. Active products for manual order creator dropdown
        $products = $db->fetchAll(
            "SELECT id, title, sku, sell_price 
             FROM products 
             WHERE status = 1 
             ORDER BY title ASC"
        );

        // 3. Stats Telemetry
        $waHitsRow = $db->fetch("SELECT COALESCE(SUM(whatsapp_clicks), 0) as clicks FROM daily_hits");
        $waInqCount = $db->fetch("SELECT COUNT(*) as count FROM whatsapp_inquiries");
        $waOrdersCount = $db->fetch("SELECT COUNT(*) as count FROM orders WHERE order_number LIKE 'MTA-WA-%'");

        $content = View::render('admin/orders/whatsapp', [
            'inquiries' => $inquiries,
            'products' => $products,
            'totalClicks' => (int)($waHitsRow['clicks'] ?? 0),
            'totalInquiries' => (int)($waInqCount['count'] ?? 0),
            'totalWaOrders' => (int)($waOrdersCount['count'] ?? 0),
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'WhatsApp Inquiries & Direct Orders',
            'meta_title' => 'WhatsApp Orders — Multan Tech Accessories',
        ]));
    }

    public function create(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $name = trim((string)$request->post('customer_name', ''));
        $phone = trim((string)$request->post('customer_phone', ''));
        $city = trim((string)$request->post('city', 'Multan'));
        $address = trim((string)$request->post('shipping_address', ''));
        $productId = (int)$request->post('product_id', 0);
        $qty = max(1, (int)$request->post('quantity', 1));
        $advance = max(0.0, (float)$request->post('advance_amount', 0));
        $ref = trim((string)$request->post('advance_reference', ''));
        $notes = trim((string)$request->post('customer_notes', ''));

        if (empty($name) || empty($phone) || empty($address) || $productId <= 0) {
            Session::flash('error', 'Please fill in customer name, phone number, delivery address, and select a product.');
            return $resp->redirect($adminPath . '/whatsapp-orders');
        }

        $product = $db->fetch("SELECT * FROM products WHERE id = ? LIMIT 1", [$productId]);
        if (!$product) {
            Session::flash('error', 'Selected product was not found in catalog.');
            return $resp->redirect($adminPath . '/whatsapp-orders');
        }

        // Selling price guarantee (ends in 99)
        $unitPrice = PricingService::snapPrice((int)($product['sell_price'] ?: 999));
        $subtotal = $unitPrice * $qty;

        // Delivery Fee Calculation per City Rules
        $freeThreshold = (int)mta_setting('delivery_free_threshold', 5000);
        $multanCharge = (int)mta_setting('delivery_multan_charge', 100);
        $standardCharge = (int)mta_setting('delivery_standard_charge', 250);

        if ($subtotal >= $freeThreshold) {
            $deliveryFee = 0;
        } elseif (strcasecmp($city, 'Multan') === 0) {
            $deliveryFee = $multanCharge;
        } else {
            $deliveryFee = $standardCharge;
        }

        $total = $subtotal + $deliveryFee;
        $codDue = max(0.0, $total - $advance);

        $orderNumber = 'MTA-WA-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

        if ($ref !== '') {
            $notes = "[WhatsApp Order] [Advance Ref: {$ref}] " . $notes;
        } else {
            $notes = "[WhatsApp Direct Order] " . $notes;
        }

        $advanceVerifiedAt = $advance > 0 ? date('Y-m-d H:i:s') : null;

        $db->execute(
            "INSERT INTO orders (
                order_number, customer_name, customer_phone, city, shipping_address, 
                customer_notes, subtotal, delivery_charge, total, advance_amount, 
                advance_verified_at, cod_due, payment_method, status, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'cod', 'processing', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
            [
                $orderNumber,
                $name,
                $phone,
                $city,
                $address,
                $notes,
                $subtotal,
                $deliveryFee,
                $total,
                $advance,
                $advanceVerifiedAt,
                $codDue,
            ]
        );

        $orderId = (int)$db->lastInsertId();

        $db->execute(
            "INSERT INTO order_items (order_id, product_id, product_title, product_sku, unit_price, quantity, total_price)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $orderId,
                $productId,
                $product['title'],
                $product['sku'],
                $unitPrice,
                $qty,
                $subtotal,
            ]
        );

        AuditLogger::log('create_whatsapp_order', 'order', (string)$orderId, [
            'order_number' => $orderNumber,
            'product' => $product['title'],
            'total' => $total,
            'advance' => $advance,
            'cod_due' => $codDue,
        ]);

        Session::flash('success', "WhatsApp Order #{$orderNumber} successfully booked! Outstanding COD is Rs. " . number_format($codDue) . ".");
        return $resp->redirect($adminPath . '/orders/' . $orderId);
    }
}

