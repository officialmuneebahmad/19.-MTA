<?php
/**
 * Multan Tech Accessories — CheckoutController
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;
use App\Services\DeliveryCalculator;
use App\Services\SeoService;

class CheckoutController
{
    public function index(Request $request): Response
    {
        $session = Session::getInstance();
        $cart = $session->get('cart', []);
        $resp = new Response();

        if (empty($cart)) {
            $session->setFlash('info', 'Your cart is empty. Add products before proceeding to checkout.');
            return $resp->redirect('/cart');
        }

        $db = Database::getInstance();
        $productIds = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        
        $products = $db->fetchAll(
            "SELECT id, slug, sku, title, sell_price, primary_image_url
             FROM products 
             WHERE id IN ($placeholders) AND is_active = 1",
            $productIds
        );

        if (empty($products)) {
            $session->set('cart', []);
            $session->setFlash('warning', 'The items in your cart are no longer available.');
            return $resp->redirect('/cart');
        }

        $productMap = [];
        foreach ($products as $p) {
            $productMap[$p['id']] = $p;
        }

        $cartItems = [];
        $subtotal = 0;
        foreach ($cart as $id => $qty) {
            if (isset($productMap[$id])) {
                $p = $productMap[$id];
                $itemTotal = (float)$p['sell_price'] * (int)$qty;
                $subtotal += $itemTotal;
                $cartItems[] = [
                    'id' => (int)$p['id'],
                    'slug' => $p['slug'],
                    'sku' => $p['sku'],
                    'title' => $p['title'],
                    'sell_price' => (float)$p['sell_price'],
                    'quantity' => (int)$qty,
                    'primary_image_url' => $p['primary_image_url'],
                ];
            }
        }

        $calculator = new DeliveryCalculator($db);
        $deliveryEstimate = $calculator->calculate($subtotal, 'Multan', '');

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Cart', 'url' => '/cart'],
            ['title' => 'Checkout', 'url' => null],
        ];

        return $resp->html(View::render('storefront/checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'deliveryEstimate' => $deliveryEstimate,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Fast Checkout'),
            'meta_description' => 'Fast Cash on Delivery checkout across Pakistan. Free delivery on orders above Rs. 5,000.',
            'noindex' => true,
        ]));
    }

    public function placeOrder(Request $request): Response
    {
        $resp = new Response();
        $session = Session::getInstance();

        // 1. Anti-bot honeypot check
        $honeypot = (string)$request->post('_website_url_hp', '');
        if (!empty($honeypot)) {
            // Fake success for bots
            return $resp->redirect('/');
        }

        // 2. IP Rate limit: max 6 orders per 15 minutes
        $clientIp = $request->ip();
        if (!RateLimiter::check("checkout_{$clientIp}", 6, 900)) {
            $session->setFlash('error', 'Too many order requests. Please wait a few minutes before trying again.');
            return $resp->redirect('/checkout');
        }

        $cart = $session->get('cart', []);
        if (empty($cart)) {
            $session->setFlash('warning', 'Your cart is empty.');
            return $resp->redirect('/cart');
        }

        // 3. Form fields
        $customerName = trim((string)$request->post('customer_name', ''));
        $rawPhone = trim((string)$request->post('customer_phone', ''));
        $city = trim((string)$request->post('city', 'Multan'));
        $address = trim((string)$request->post('shipping_address', ''));
        $notes = trim((string)$request->post('order_notes', ''));

        if (empty($customerName) || empty($rawPhone) || empty($address)) {
            $session->setFlash('error', 'Please fill in all required delivery fields.');
            return $resp->redirect('/checkout');
        }

        // 4. Validate Pakistani phone
        $phoneData = Security::normalizePakistaniPhone($rawPhone);
        if (!$phoneData['valid']) {
            $session->setFlash('error', 'Please enter a valid 11-digit Pakistani mobile number (e.g., 03001234567).');
            return $resp->redirect('/checkout');
        }

        $db = Database::getInstance();
        $productIds = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        
        $products = $db->fetchAll(
            "SELECT id, slug, sku, title, sell_price, is_active 
             FROM products 
             WHERE id IN ($placeholders) AND is_active = 1",
            $productIds
        );

        if (empty($products)) {
            $session->set('cart', []);
            $session->setFlash('warning', 'Items are no longer available.');
            return $resp->redirect('/cart');
        }

        $productMap = [];
        foreach ($products as $p) {
            $productMap[$p['id']] = $p;
        }

        $subtotal = 0.0;
        $orderItemsData = [];
        foreach ($cart as $id => $qty) {
            if (isset($productMap[$id])) {
                $p = $productMap[$id];
                $quantity = max(1, (int)$qty);
                $unitPrice = (float)$p['sell_price'];
                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $orderItemsData[] = [
                    'product_id' => (int)$p['id'],
                    'product_title' => $p['title'],
                    'product_sku' => $p['sku'],
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'total_price' => $lineTotal,
                ];
            }
        }

        // 5. Calculate delivery
        $calculator = new DeliveryCalculator($db);
        $delivery = $calculator->calculate($subtotal, $city, $phoneData['local']);
        $deliveryCharge = (float)$delivery['charge'];
        $grandTotal = $subtotal + $deliveryCharge;

        // 6. Generate unique order number (e.g. MTA-261028-4K9P)
        $datePrefix = date('ymd');
        $randomSuffix = strtoupper(bin2hex(random_bytes(2)));
        $orderNumber = "MTA-{$datePrefix}-{$randomSuffix}";

        // 7. Insert Order in a transaction
        $db->beginTransaction();
        try {
            $db->execute(
                "INSERT INTO orders (
                    order_number, customer_name, customer_phone, city, shipping_address, customer_notes,
                    subtotal, delivery_charge, discount, total, payment_method, status,
                    is_second_order, ip_address, user_agent, created_at
                 ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0.00, ?, 'cod', 'pending', ?, ?, ?, ?)",
                [
                    $orderNumber,
                    $customerName,
                    $phoneData['local'],
                    $city,
                    $address,
                    $notes ?: null,
                    $subtotal,
                    $deliveryCharge,
                    $grandTotal,
                    $delivery['is_second_order'] ? 1 : 0,
                    $clientIp,
                    substr($request->header('User-Agent') ?? '', 0, 500),
                    date('Y-m-d H:i:s'),
                ]
            );

            $orderId = (int)$db->lastInsertId();

            foreach ($orderItemsData as $item) {
                $db->execute(
                    "INSERT INTO order_items (
                        order_id, product_id, product_title, product_sku, unit_price, quantity, total_price
                     ) VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [
                        $orderId,
                        $item['product_id'],
                        $item['product_title'],
                        $item['product_sku'],
                        $item['unit_price'],
                        $item['quantity'],
                        $item['total_price'],
                    ]
                );
            }

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            $session->setFlash('error', 'We encountered an issue processing your order. Please try again.');
            return $resp->redirect('/checkout');
        }

        // 8. Clear Cart & redirect to summary
        $session->set('cart', []);
        return $resp->redirect('/order/summary/' . urlencode($orderNumber));
    }
}
