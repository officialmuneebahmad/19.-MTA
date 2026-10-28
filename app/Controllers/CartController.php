<?php
/**
 * Multan Tech Accessories — CartController
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\SeoService;

class CartController
{
    public function index(): Response
    {
        $session = Session::getInstance();
        $cart = $session->get('cart', []);
        $cartItems = [];

        if (!empty($cart)) {
            $db = Database::getInstance();
            $productIds = array_keys($cart);
            
            if (!empty($productIds)) {
                $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                $products = $db->fetchAll(
                    "SELECT id, slug, sku, title, sell_price, primary_image_url, is_active, stock_status
                     FROM products 
                     WHERE id IN ($placeholders) AND is_active = 1",
                    $productIds
                );

                $productMap = [];
                foreach ($products as $p) {
                    $productMap[$p['id']] = $p;
                }

                foreach ($cart as $id => $qty) {
                    if (isset($productMap[$id])) {
                        $p = $productMap[$id];
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
            }
        }

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Shopping Cart', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/cart', [
            'cartItems' => $cartItems,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Shopping Cart'),
            'meta_description' => 'Review your shopping cart at Multan Tech Accessories. Fast Cash on Delivery across Pakistan.',
            'noindex' => true,
        ]));
    }

    public function add(Request $request): Response
    {
        $productId = (int)$request->post('product_id', 0);
        $quantity = max(1, (int)$request->post('quantity', 1));

        if ($productId > 0) {
            $db = Database::getInstance();
            $product = $db->fetch("SELECT id FROM products WHERE id = ? AND is_active = 1 LIMIT 1", [$productId]);
            
            if ($product) {
                $session = Session::getInstance();
                $cart = $session->get('cart', []);
                $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
                $session->set('cart', $cart);
                $session->setFlash('success', 'Item added to cart.');
            }
        }

        $resp = new Response();
        return $resp->redirect('/cart');
    }

    public function update(Request $request): Response
    {
        $productId = (int)$request->post('product_id', 0);
        $quantity = (int)$request->post('quantity', 1);

        $session = Session::getInstance();
        $cart = $session->get('cart', []);

        if ($productId > 0) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId] = min(10, $quantity);
            }
            $session->set('cart', $cart);
        }

        $resp = new Response();
        return $resp->redirect('/cart');
    }

    public function remove(Request $request): Response
    {
        $productId = (int)$request->post('product_id', 0);

        $session = Session::getInstance();
        $cart = $session->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $session->set('cart', $cart);
            $session->setFlash('info', 'Item removed from cart.');
        }

        $resp = new Response();
        return $resp->redirect('/cart');
    }
}
