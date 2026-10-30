<?php
/**
 * Multan Tech Accessories — Admin Order Detail View
 */
$adminPath = mta_admin_path();
$waMsg = "Hello {$order['customer_name']}, this is Muneeb from Multan Tech Accessories regarding your order #{$order['order_number']}. Total: " . mta_money($order['total']) . " (COD). Please reply to confirm delivery to {$order['city']}.";
$waCustomerUrl = mta_whatsapp_url($order['customer_phone'], $waMsg);
?>
<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <div class="admin-card-title">Order #<?= e($order['order_number']) ?></div>
      <div style="font-size: 0.8rem; color: #777; margin-top: 0.2rem;">
        Placed on <?= date('d M Y, h:i:s A', strtotime($order['created_at'])) ?> &bull; IP: <code><?= e($order['ip_address'] ?? 'N/A') ?></code>
      </div>
    </div>
    <a href="<?= e($adminPath) ?>/orders" class="btn btn-secondary btn-sm">&larr; Back to Orders</a>
  </div>

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    
    <!-- Left: Order Breakdown & Customer Info -->
    <div>
      <div style="background: #F9F9F7; border: 1px solid var(--admin-border); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
        <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.75rem;">Customer Details</h3>
        <div style="font-size: 0.9rem; line-height: 1.7;">
          <div><strong>Name:</strong> <?= e($order['customer_name']) ?></div>
          <div><strong>Phone:</strong> <a href="tel:<?= e($order['customer_phone']) ?>" style="color: #9B3828; font-weight: 700;"><?= e($order['customer_phone']) ?></a></div>
          <div><strong>Destination City:</strong> <?= e($order['city']) ?></div>
          <div><strong>Shipping Address:</strong> <?= e($order['shipping_address']) ?></div>
          <?php if (!empty($order['customer_notes'])): ?>
            <div style="margin-top: 0.5rem; background: #fff; padding: 0.5rem 0.75rem; border-radius: 4px; border: 1px solid #ddd;">
              <strong>Customer Note:</strong> <?= e($order['customer_notes']) ?>
            </div>
          <?php endif; ?>
        </div>

        <div style="margin-top: 1rem;">
          <a href="<?= e($waCustomerUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm">
            💬 Open Customer Chat on WhatsApp &rarr;
          </a>
        </div>
      </div>

      <!-- Items Table -->
      <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.75rem;">Ordered Products</h3>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Product Title & SKU</th>
            <th>Unit Price</th>
            <th>Qty</th>
            <th style="text-align: right;">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td>
                <div style="font-weight: 600;"><?= e($item['product_title']) ?></div>
                <div style="font-size: 0.75rem; color: #777;">SKU: <?= e($item['product_sku'] ?? 'N/A') ?></div>
              </td>
              <td><?= mta_money($item['unit_price']) ?></td>
              <td><?= (int)$item['quantity'] ?></td>
              <td style="text-align: right; font-weight: 700;"><?= mta_money($item['total_price']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Order Summary Totals -->
      <div style="margin-top: 1.5rem; border-top: 1px solid var(--admin-border); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.4rem; max-width: 320px; margin-left: auto; font-size: 0.9rem;">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: #666;">Subtotal:</span>
          <span><?= mta_money($order['subtotal']) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: #666;">Delivery Charge:</span>
          <span><?= (float)$order['delivery_charge'] <= 0 ? 'FREE' : mta_money($order['delivery_charge']) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1rem; color: var(--admin-text); border-top: 1px solid var(--admin-border); padding-top: 0.5rem; margin-top: 0.25rem;">
          <span>Order Grand Total:</span>
          <span><?= mta_money($order['total']) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; font-weight: 600; font-size: 0.95rem; color: var(--mta-teal);">
          <span>Advance Paid:</span>
          <span>- <?= mta_money($order['advance_amount'] ?? 0) ?></span>
        </div>
        <?php if (!empty($order['advance_verified_at'])): ?>
          <div style="font-size: 0.75rem; color: var(--mta-teal); text-align: right; margin-top: -0.2rem;">
            &check; Verified on <?= date('d M Y, h:i A', strtotime($order['advance_verified_at'])) ?>
          </div>
        <?php endif; ?>
        <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 1.15rem; color: #FF6B2C; border-top: 2px dashed #FF6B2C; padding-top: 0.5rem; margin-top: 0.25rem;">
          <span>Outstanding COD Due:</span>
          <span><?= mta_money($order['cod_due'] ?? max(0, $order['total'] - ($order['advance_amount'] ?? 0))) ?></span>
        </div>
      </div>
    </div>

    <!-- Right: Status & Courier Controls + Advance Verification -->
    <div>
      <!-- Advance Verification Form Card -->
      <div class="admin-card" style="margin-bottom: 1.5rem; border-left: 4px solid var(--mta-teal);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--admin-text); margin: 0;">Advance Payment</h3>
          <?php if (!empty($order['advance_verified_at'])): ?>
            <span class="badge badge-success">Verified</span>
          <?php else: ?>
            <span class="badge badge-amber">Unverified</span>
          <?php endif; ?>
        </div>

        <form action="<?= e($adminPath) ?>/orders/<?= (int)$order['id'] ?>/verify-advance" method="POST">
          <?= mta_csrf_field() ?>
          <div class="form-group" style="margin-bottom: 0.65rem;">
            <label style="font-size: 0.8rem; font-weight: 600;">Advance Amount (PKR)</label>
            <input type="number" name="advance_amount" value="<?= (float)($order['advance_amount'] ?? 0) > 0 ? (int)$order['advance_amount'] : 500 ?>" min="0" step="1" required class="form-control" style="font-size: 0.9rem;">
          </div>
          <div class="form-group" style="margin-bottom: 0.75rem;">
            <label style="font-size: 0.8rem; font-weight: 600;">Reference / TID (JazzCash / EasyPaisa / Bank)</label>
            <input type="text" name="advance_reference" placeholder="e.g. TID 04928174928" class="form-control" style="font-size: 0.85rem;">
          </div>
          <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            <span>Verify Advance Payment</span>
          </button>
        </form>
      </div>

      <div class="admin-card" style="margin-bottom: 0;">
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem;">Dispatch & Courier Controls</h3>

        <form action="<?= e($adminPath) ?>/orders/<?= (int)$order['id'] ?>" method="POST">
          <?= mta_csrf_field() ?>

          <div class="form-group">
            <label for="status">Order Status</label>
            <select id="status" name="status" class="form-control">
              <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending Confirmation</option>
              <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
              <option value="dispatched" <?= $order['status'] === 'dispatched' ? 'selected' : '' ?>>Dispatched to Courier</option>
              <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
              <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
              <option value="returned" <?= $order['status'] === 'returned' ? 'selected' : '' ?>>Returned to Store</option>
            </select>
          </div>

          <div class="form-group">
            <label for="courier_name">Courier Partner</label>
            <select id="courier_name" name="courier_name" class="form-control">
              <option value="">-- Select Courier --</option>
              <option value="Trax" <?= ($order['courier_name'] ?? '') === 'Trax' ? 'selected' : '' ?>>Trax Logistics</option>
              <option value="Leopards" <?= ($order['courier_name'] ?? '') === 'Leopards' ? 'selected' : '' ?>>Leopards Courier</option>
              <option value="PostEx" <?= ($order['courier_name'] ?? '') === 'PostEx' ? 'selected' : '' ?>>PostEx</option>
              <option value="TCS" <?= ($order['courier_name'] ?? '') === 'TCS' ? 'selected' : '' ?>>TCS Express</option>
              <option value="M&P" <?= ($order['courier_name'] ?? '') === 'M&P' ? 'selected' : '' ?>>M&P Logistics</option>
              <option value="Local Rider" <?= ($order['courier_name'] ?? '') === 'Local Rider' ? 'selected' : '' ?>>Local Multan Rider</option>
            </select>
          </div>

          <div class="form-group">
            <label for="tracking_number">Consignment / Tracking #</label>
            <input type="text" id="tracking_number" name="tracking_number" value="<?= e($order['tracking_number'] ?? '') ?>" placeholder="e.g. 58392019" class="form-control">
          </div>

          <div class="form-group">
            <label for="tracking_link">Direct Tracking URL (Auto-Generated)</label>
            <input type="url" id="tracking_link" name="tracking_link" value="<?= e($order['tracking_link'] ?? '') ?>" placeholder="Leave blank to auto-generate from Courier" class="form-control">
          </div>

          <?php if (!empty($order['tracking_link'])): ?>
            <div style="margin-bottom: 1.25rem;">
              <a href="<?= e($order['tracking_link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="width: 100%;">
                Test Courier Link ↗
              </a>
            </div>
          <?php endif; ?>

          <button type="submit" class="btn btn-primary" style="width: 100%;">
            Update Order Status & Tracking
          </button>
        </form>
      </div>
    </div>

  </div>
</div>
