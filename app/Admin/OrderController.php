<?php
/**
 * Multan Tech Accessories — Admin OrderController
 * Order Management, Status Updates & Courier Tracking Link Generator
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class OrderController
{
    public function index(Request $request): Response
    {
        $db = Database::getInstance();
        $page = max(1, (int)$request->get('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $status = trim((string)$request->get('status', ''));
        $search = trim((string)$request->get('q', ''));

        $where = ['1=1'];
        $params = [];

        if ($status !== '') {
            $where[] = 'status = ?';
            $params[] = $status;
        }

        if ($search !== '') {
            $where[] = '(order_number LIKE ? OR customer_name LIKE ? OR customer_phone LIKE ?)';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $whereClause = implode(' AND ', $where);

        $countRow = $db->fetch("SELECT COUNT(*) as total FROM orders WHERE {$whereClause}", $params);
        $total = (int)($countRow['total'] ?? 0);
        $totalPages = max(1, (int)ceil($total / $perPage));

        $fetchParams = $params;
        $fetchParams[] = $perPage;
        $fetchParams[] = $offset;

        $orders = $db->fetchAll(
            "SELECT * FROM orders WHERE {$whereClause} ORDER BY id DESC LIMIT ? OFFSET ?",
            $fetchParams
        );

        $content = View::render('admin/orders/index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ]);

        $resp = new Response();
        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Customer Orders Management',
            'meta_title' => 'Orders — Multan Tech Accessories',
        ]));
    }

    public function show(int $id): Response
    {
        $db = Database::getInstance();
        $order = $db->fetch("SELECT * FROM orders WHERE id = ? LIMIT 1", [$id]);

        $resp = new Response();
        if (!$order) {
            Session::flash('error', 'Order not found.');
            return $resp->redirect(mta_admin_path('orders'));
        }

        $items = $db->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$id]);

        $content = View::render('admin/orders/show', [
            'order' => $order,
            'items' => $items,
        ]);

        return $resp->html(View::render('layouts/admin', [
            'content' => $content,
            'page_title' => 'Order #' . $order['order_number'],
            'meta_title' => 'Order Detail — Multan Tech Accessories',
        ]));
    }

    public function update(int $id, Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $order = $db->fetch("SELECT * FROM orders WHERE id = ? LIMIT 1", [$id]);
        if (!$order) {
            Session::flash('error', 'Order not found.');
            return $resp->redirect($adminPath . '/orders');
        }

        $status = trim((string)$request->post('status', $order['status']));
        $courierName = trim((string)$request->post('courier_name', $order['courier_name'] ?? ''));
        $trackingNumber = trim((string)$request->post('tracking_number', $order['tracking_number'] ?? ''));
        $trackingLink = trim((string)$request->post('tracking_link', $order['tracking_link'] ?? ''));

        // Auto-generate tracking link if known Pakistani courier is selected
        if (!empty($trackingNumber) && empty($trackingLink)) {
            $trackingLink = match(strtolower($courierName)) {
                'trax' => "https://trax.pk/tracking/{$trackingNumber}",
                'leopard', 'leopards' => "https://leopardscourier.com/tracking?track={$trackingNumber}",
                'postex' => "https://postex.pk/tracking?order={$trackingNumber}",
                'tcs' => "https://www.tcsexpress.com/track/{$trackingNumber}",
                'mnp', 'm&p' => "https://mulphilog.com/tracking?consignmentNo={$trackingNumber}",
                default => null,
            };
        }

        $db->execute(
            "UPDATE orders SET 
                status = ?, courier_name = ?, tracking_number = ?, tracking_link = ?, updated_at = ?
             WHERE id = ?",
            [
                $status,
                $courierName ?: null,
                $trackingNumber ?: null,
                $trackingLink ?: null,
                date('Y-m-d H:i:s'),
                $id,
            ]
        );

        Session::flash('success', "Order #{$order['order_number']} status updated to '{$status}'.");
        return $resp->redirect($adminPath . '/orders/' . $id);
    }

    public function verifyAdvance(int $id, Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $db = Database::getInstance();

        $order = $db->fetch("SELECT * FROM orders WHERE id = ? LIMIT 1", [$id]);
        if (!$order) {
            Session::flash('error', 'Order not found.');
            return $resp->redirect($adminPath . '/orders');
        }

        $advance = max(0, (float)$request->post('advance_amount', 0));
        $ref = trim((string)$request->post('advance_reference', ''));
        $total = (float)$order['total'];
        $codDue = max(0.0, $total - $advance);

        $notes = trim((string)($order['customer_notes'] ?? ''));
        if ($ref !== '') {
            $notes .= ($notes ? "\n" : '') . "[Advance Verified: Rs. " . number_format($advance) . " via Ref/TID: " . $ref . " on " . date('Y-m-d H:i') . "]";
        }

        $db->execute(
            "UPDATE orders SET 
                advance_amount = ?, 
                advance_verified_at = CURRENT_TIMESTAMP, 
                cod_due = ?, 
                customer_notes = ?,
                updated_at = CURRENT_TIMESTAMP 
             WHERE id = ?",
            [$advance, $codDue, $notes, $id]
        );

        \App\Services\AuditLogger::log('verify_advance', 'order', (string)$id, [
            'order_number' => $order['order_number'],
            'advance_amount' => $advance,
            'reference' => $ref,
            'cod_due' => $codDue,
        ]);

        Session::flash('success', "Advance of Rs. " . number_format($advance) . " verified. Outstanding COD is now Rs. " . number_format($codDue) . ".");
        return $resp->redirect($adminPath . '/orders/' . $id);
    }
}
