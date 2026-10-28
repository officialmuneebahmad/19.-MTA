<?php
/**
 * Multan Tech Accessories — BeaconController
 * High-performance hit tracking for PopularityService
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

class BeaconController
{
    public function track(Request $request): Response
    {
        $productId = (int)$request->post('product_id', 0);
        $event = (string)$request->post('event', 'views');

        if ($productId <= 0) {
            $productId = (int)$request->get('product_id', 0);
            $event = (string)$request->get('event', 'views');
        }

        if ($productId > 0) {
            $db = Database::getInstance();
            $today = date('Y-m-d');
            $viewInc = ($event === 'views') ? 1 : 0;
            $waInc = ($event === 'whatsapp_clicks') ? 1 : 0;
            $cartInc = ($event === 'add_to_cart') ? 1 : 0;

            try {
                // Portable update or insert
                $updated = $db->execute(
                    "UPDATE daily_hits 
                     SET views = views + ?, whatsapp_clicks = whatsapp_clicks + ?, add_to_cart = add_to_cart + ?
                     WHERE date = ? AND product_id = ?",
                    [$viewInc, $waInc, $cartInc, $today, $productId]
                );

                if ($updated === 0) {
                    $db->execute(
                        "INSERT INTO daily_hits (date, product_id, views, whatsapp_clicks, add_to_cart, orders)
                         VALUES (?, ?, ?, ?, ?, 0)",
                        [$today, $productId, $viewInc, $waInc, $cartInc]
                    );
                }
            } catch (\Throwable $e) {
                // Ignore tracking failures to never slow the user
            }
        }

        $resp = new Response();
        $resp->status(204);
        return $resp;
    }
}
