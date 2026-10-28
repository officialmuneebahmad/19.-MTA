<?php
/**
 * Multan Tech Accessories — Order Tracking View
 */
?>
<div class="container" style="padding: 2.5rem 1.25rem; max-width: 680px;">
  
  <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; text-align: center;">Track Your Order</h1>
  <p style="font-size: 0.95rem; color: var(--text-muted); text-align: center; margin-bottom: 2rem;">
    Enter your Order ID (e.g. MTA-241028-7K3Q) and Mobile Number to check real-time courier dispatch status.
  </p>

  <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 2rem;">
    <form action="/track-order" method="GET">
      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label for="order_number" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">Order Number *</label>
        <input type="text" id="order_number" name="order_number" required placeholder="MTA-241028-7K3Q" value="<?= e($_GET['order_number'] ?? '') ?>" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem;">
      </div>

      <div class="form-group" style="margin-bottom: 1.5rem;">
        <label for="phone" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">Mobile Phone *</label>
        <input type="tel" id="phone" name="phone" required placeholder="03XXXXXXXXX" value="<?= e($_GET['phone'] ?? '') ?>" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem;">
      </div>

      <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
        Check Order Status &rarr;
      </button>
    </form>
  </div>

  <?php if (isset($order) && $order !== null): ?>
    <div style="background: var(--surface); border: 2px solid var(--accent-strong); border-radius: var(--radius-lg); padding: 2rem;">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1.25rem;">
        <div>
          <span style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Order Status</span>
          <h2 style="font-size: 1.35rem; font-weight: 800; text-transform: capitalize; color: var(--accent-strong);">
            <?= e($order['status']) ?>
          </h2>
        </div>
        <div style="text-align: right;">
          <span style="font-size: 0.8rem; color: var(--text-muted);">Placed on</span>
          <div style="font-size: 0.9rem; font-weight: 600;"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></div>
        </div>
      </div>

      <div style="font-size: 0.9rem; line-height: 1.8; color: var(--text);">
        <div><strong>Customer:</strong> <?= e($order['customer_name']) ?> (<?= e($order['customer_phone']) ?>)</div>
        <div><strong>City / Destination:</strong> <?= e($order['city']) ?></div>
        <div><strong>Address:</strong> <?= e($order['shipping_address']) ?></div>
        <div><strong>Total Payable (COD):</strong> <?= mta_money($order['total']) ?></div>
        
        <?php if (!empty($order['courier_name'])): ?>
          <div style="margin-top: 1rem; background: var(--surface-2); padding: 1rem; border-radius: 8px;">
            <div><strong>Courier Service:</strong> <?= e($order['courier_name']) ?></div>
            <div><strong>Tracking Number:</strong> <?= e($order['tracking_number'] ?? 'Assigned') ?></div>
            <?php if (!empty($order['tracking_link'])): ?>
              <div style="margin-top: 0.5rem;">
                <a href="<?= e($order['tracking_link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">
                  Open Courier Live Tracking &rarr;
                </a>
              </div>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div style="margin-top: 1rem; color: var(--text-muted); font-size: 0.85rem;">
            * Your order is being packed at our Multan warehouse. Courier tracking details will appear here once dispatched.
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php elseif (isset($_GET['order_number'])): ?>
    <div class="alert alert-danger" style="background: var(--danger-bg); color: var(--danger); padding: 1rem; border-radius: 8px; text-align: center;">
      No order found matching the provided order number and phone number. Please verify and try again.
    </div>
  <?php endif; ?>

</div>
