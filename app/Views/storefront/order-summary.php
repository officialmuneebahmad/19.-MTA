<?php
/**
 * Multan Tech Accessories — Order Thank-You & Summary View (noindex)
 */
$orderNumber = $order['order_number'] ?? '';
$waMsg = "Hello Multan Tech Accessories! I have placed order *{$orderNumber}* on MultanTech.store.\nName: {$order['customer_name']}\nTotal: " . mta_money($order['total']) . " (COD)\nDelivery City: {$order['city']}\nPlease confirm and dispatch.";
$waConfirmUrl = mta_whatsapp_url('923070655275', $waMsg);
?>
<div class="container" style="padding: 3rem 1.25rem; max-width: 720px; text-align: center;">
  
  <div style="width: 64px; height: 64px; border-radius: var(--radius-pill); background: var(--success-bg); color: var(--success); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
  </div>

  <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0.5rem;">Order Received!</h1>
  <p style="font-size: 1rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.75rem;">
    Thank you for shopping with Multan Tech Accessories, <strong><?= e($order['customer_name']) ?></strong>.<br>
    Your order number is <strong style="color: var(--accent-strong); font-family: monospace; font-size: 1.15rem;"><?= e($orderNumber) ?></strong>.
  </p>

  <!-- WhatsApp Confirmation Callout -->
  <div style="background: #E8F9EE; border: 2px dashed var(--whatsapp); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 2rem; text-align: center;">
    <h3 style="font-size: 1.15rem; font-weight: 700; color: #128C7E; margin-bottom: 0.5rem;">
      Fast-Track Your Dispatch on WhatsApp
    </h3>
    <p style="font-size: 0.875rem; color: #141413; line-height: 1.5; margin-bottom: 1.25rem;">
      Click below to send your order details directly to our WhatsApp support team. We prioritize WhatsApp-confirmed orders for same-day packing!
    </p>
    <a href="<?= e($waConfirmUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg" style="width: 100%; max-width: 380px;">
      Confirm on WhatsApp &rarr;
    </a>
  </div>

  <!-- Order Receipt Card -->
  <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; text-align: left; margin-bottom: 2rem;">
    <h3 style="font-size: 1.15rem; font-weight: 700; border-bottom: 1px solid var(--border); padding-bottom: 0.75rem; margin-bottom: 1rem;">
      Order Breakdown
    </h3>

    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
      <?php foreach ($items as $item): ?>
        <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
          <span><?= (int)$item['quantity'] ?> &times; <?= e($item['product_title']) ?></span>
          <span style="font-weight: 600;"><?= mta_money($item['total_price']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>

    <div style="border-top: 1px solid var(--border); padding-top: 0.75rem; display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.9rem;">
      <div style="display: flex; justify-content: space-between;">
        <span style="color: var(--text-muted);">Subtotal:</span>
        <span><?= mta_money($order['subtotal']) ?></span>
      </div>
      <div style="display: flex; justify-content: space-between;">
        <span style="color: var(--text-muted);">Delivery Charge:</span>
        <span><?= (float)$order['delivery_charge'] <= 0 ? 'FREE' : mta_money($order['delivery_charge']) ?></span>
      </div>
      <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 1.15rem; border-top: 1px solid var(--border); padding-top: 0.65rem; margin-top: 0.25rem;">
        <span>Total (COD):</span>
        <span style="color: var(--accent-strong);"><?= mta_money($order['total']) ?></span>
      </div>
    </div>

    <div style="border-top: 1px solid var(--border); padding-top: 1rem; margin-top: 1rem; font-size: 0.85rem; color: var(--text-muted);">
      <div><strong>Shipping Address:</strong> <?= e($order['shipping_address']) ?>, <?= e($order['city']) ?></div>
      <div style="margin-top: 0.25rem;"><strong>Phone:</strong> <?= e($order['customer_phone']) ?></div>
    </div>
  </div>

  <div style="display: flex; justify-content: center; gap: 1rem;">
    <a href="/" class="btn btn-primary">Return to Homepage</a>
    <a href="/track-order?order_number=<?= e($orderNumber) ?>" class="btn btn-secondary">Track This Order</a>
  </div>

</div>
