<?php
/**
 * Multan Tech Accessories — OrderController
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\View;
use App\Services\SeoService;

class OrderController
{
    public function summary(string $orderNumber): Response
    {
        $db = Database::getInstance();
        $order = $db->fetch(
            "SELECT * FROM orders WHERE order_number = ? LIMIT 1",
            [$orderNumber]
        );

        $resp = new Response();

        if (!$order) {
            $resp->status(404);
            return $resp->html(View::render('storefront/errors/404', [
                'meta_title' => 'Order Not Found',
                'meta_description' => 'The requested order could not be located.',
                'noindex' => true,
            ]), 404);
        }

        $items = $db->fetchAll(
            "SELECT * FROM order_items WHERE order_id = ?",
            [(int)$order['id']]
        );

        return $resp->html(View::render('storefront/order-summary', [
            'order' => $order,
            'items' => $items,
            'meta_title' => SeoService::title("Order #{$orderNumber} Confirmed"),
            'meta_description' => 'Your order has been received by Multan Tech Accessories.',
            'noindex' => true, // Confirmation pages must NEVER be indexed
        ]));
    }

    public function track(Request $request): Response
    {
        $orderNumber = trim((string)$request->get('order_number', ''));
        $phone = trim((string)$request->get('phone', ''));

        $order = null;
        $db = Database::getInstance();

        if (!empty($orderNumber) && !empty($phone)) {
            $phoneData = Security::normalizePakistaniPhone($phone);
            $normalizedPhone = $phoneData['valid'] ? $phoneData['local'] : $phone;

            $order = $db->fetch(
                "SELECT * FROM orders WHERE order_number = ? AND (customer_phone = ? OR customer_phone = ?) LIMIT 1",
                [$orderNumber, $normalizedPhone, $phone]
            );
        }

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Track Order', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/track-order', [
            'order' => $order,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Track Your Order'),
            'meta_description' => 'Track your tech accessories shipment status across Pakistan using your Order ID and phone number.',
            'noindex' => true,
        ]));
    }
}
