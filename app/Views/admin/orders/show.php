<?php
/**
 * Multan Tech Accessories — Admin Order Detail View
 * Clean, modern, responsive, and 100% dark-mode safe.
 */
$adminPath = mta_admin_path();
$orderNumber = $order['order_number'] ?? '';
$waMsg = "Hello {$order['customer_name']}, this is Muneeb from Multan Tech Accessories regarding your order #{$orderNumber}. Total: " . mta_money($order['total']) . " (COD). Please reply to confirm delivery to {$order['city']}.";
$waCustomerUrl = mta_whatsapp_url($order['customer_phone'], $waMsg);
?>
<div class="admin-card">
  <div class="admin-card-header">
    <div class="order-header-info">
      <div class="admin-card-title">Order #<?= e($orderNumber) ?></div>
      <div class="order-header-meta" style="font-size: 0.85rem; color: var(--admin-text-muted); margin-top: 0.35rem;">
        Placed on <?= date('d M Y, h:i:s A', strtotime($order['created_at'])) ?> &bull; IP: <code><?= e($order['ip_address'] ?? 'N/A') ?></code>
      </div>
    </div>
    <div class="order-header-actions">
      <a href="<?= e($adminPath) ?>/orders" class="btn btn-secondary btn-sm">&larr; Back to Orders</a>
      <button type="button" onclick="window.location.reload();" class="btn btn-secondary btn-sm" title="Refresh Order Details">🔄 Refresh</button>
      <a href="<?= e($adminPath) ?>/orders/<?= (int)$order['id'] ?>/invoice" target="_blank" class="btn btn-secondary btn-sm btn-order-invoice" title="View & Print Official Customer Invoice">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        <span>Get Invoice</span>
      </a>
      <form action="<?= e($adminPath) ?>/orders/<?= (int)$order['id'] ?>/delete" method="POST" class="order-delete-form" onsubmit="return mtaConfirmTwice('Are you sure you want to delete Order #<?= e($orderNumber) ?>? This action cannot be undone.', '⚠️ FINAL CONFIRMATION: This will permanently delete Order #<?= e($orderNumber) ?> and all its items. Proceed?');">
        <?= mta_csrf_field() ?>
        <button type="submit" class="btn btn-danger btn-sm" title="Delete this order permanently">
          🗑️ Delete
        </button>
      </form>
    </div>
  </div>

  <div class="admin-order-grid">
    
    <!-- Left: Customer Details & Ordered Products -->
    <div>
      <!-- Customer Information Card (Dark-Mode Safe & Responsive) -->
      <div class="customer-info-box">
        <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1.15rem; color: var(--admin-text); display: flex; align-items: center; gap: 0.5rem;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
          Customer Details
        </h3>
        
        <div class="customer-info-grid">
          <div class="customer-info-item">
            <span class="info-label">Customer Name</span>
            <strong class="info-val" style="font-size: 1rem;"><?= e($order['customer_name']) ?></strong>
          </div>
          <div class="customer-info-item">
            <span class="info-label">Mobile Phone</span>
            <a href="tel:<?= e($order['customer_phone']) ?>" class="info-val" style="color: var(--mta-orange); text-decoration: underline;">
              <?= e($order['customer_phone']) ?>
            </a>
          </div>
          <div class="customer-info-item">
            <span class="info-label">Destination City</span>
            <strong class="info-val"><?= e($order['city']) ?></strong>
          </div>
          <?php if (!empty($order['customer_email'])): ?>
            <div class="customer-info-item">
              <span class="info-label">Email Address</span>
              <span class="info-val"><?= e($order['customer_email']) ?></span>
            </div>
          <?php endif; ?>
        </div>

        <div class="customer-address-box">
          <span class="info-label">Delivery Address</span>
          <div style="color: var(--admin-text); margin-top: 0.25rem; font-size: 0.95rem; line-height: 1.5; word-break: break-word;"><?= nl2br(e($order['shipping_address'])) ?></div>
        </div>

        <?php if (!empty($order['customer_notes'])): ?>
          <div class="customer-notes-box">
            <strong style="color: var(--admin-text-muted); font-size: 0.75rem; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Customer Order Notes:</strong>
            <?= nl2br(e($order['customer_notes'])) ?>
          </div>
        <?php endif; ?>

        <div style="margin-top: 1.25rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
          <a href="<?= e($waCustomerUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm btn-customer-wa" style="background: #128C7E; border-color: #128C7E; color: #fff;">
            💬 Open Customer Chat on WhatsApp &rarr;
          </a>
        </div>
      </div>

      <!-- Items Table with Mobile X-Axis Scroll -->
      <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--admin-text);">Ordered Products</h3>
      <div class="table-scroll-hint mobile-only">
        <span>&larr; Swipe horizontally to view product prices & line totals &rarr;</span>
      </div>
      <div class="admin-table-responsive">
        <table class="admin-table order-items-table">
          <thead>
            <tr>
              <th>Product Title & SKU</th>
              <th style="white-space: nowrap;">Unit Price</th>
              <th style="text-align: center; white-space: nowrap;">Qty</th>
              <th style="text-align: right; white-space: nowrap;">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td>
                  <div style="font-weight: 600; color: var(--admin-text); line-height: 1.35;"><?= e($item['product_title']) ?></div>
                  <div style="font-size: 0.75rem; color: var(--admin-text-muted); font-family: monospace; margin-top: 0.25rem;">SKU: <?= e($item['product_sku'] ?? 'N/A') ?></div>
                </td>
                <td style="color: var(--admin-text); white-space: nowrap;"><?= mta_money($item['unit_price']) ?></td>
                <td style="text-align: center; color: var(--admin-text); white-space: nowrap;"><?= (int)$item['quantity'] ?></td>
                <td style="text-align: right; font-weight: 700; color: var(--admin-text); white-space: nowrap;"><?= mta_money($item['total_price']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Order Summary Totals -->
      <div class="order-totals-card">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--admin-text-muted);">Subtotal:</span>
          <span style="color: var(--admin-text); font-weight: 600;"><?= mta_money($order['subtotal']) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--admin-text-muted);">Delivery Charge:</span>
          <span style="color: var(--admin-text); font-weight: 600;"><?= (float)$order['delivery_charge'] <= 0 ? 'FREE' : mta_money($order['delivery_charge']) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.05rem; color: var(--admin-text); border-top: 1px solid var(--admin-card-border); padding-top: 0.65rem; margin-top: 0.35rem;">
          <span>Order Grand Total:</span>
          <span style="color: var(--admin-text);"><?= mta_money($order['total']) ?></span>
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
        <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 1.2rem; color: var(--mta-orange); border-top: 2px dashed var(--mta-orange); padding-top: 0.65rem; margin-top: 0.35rem;">
          <span>Outstanding COD:</span>
          <span><?= mta_money($order['cod_due'] ?? max(0, $order['total'] - ($order['advance_amount'] ?? 0))) ?></span>
        </div>
      </div>
    </div>

    <!-- Right: Status & Courier Controls + Advance Verification -->
    <div>
      <!-- Advance Verification Form Card -->
      <div class="admin-panel-card order-advance-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; gap: 0.5rem; flex-wrap: wrap;">
          <h3 style="font-size: 1rem; font-weight: 700; color: var(--admin-text); margin: 0;">Advance Payment</h3>
          <?php if (!empty($order['advance_verified_at'])): ?>
            <span class="badge badge-success">Verified</span>
          <?php else: ?>
            <span class="badge badge-amber">Unverified</span>
          <?php endif; ?>
        </div>

        <form action="<?= e($adminPath) ?>/orders/<?= (int)$order['id'] ?>/verify-advance" method="POST">
          <?= mta_csrf_field() ?>
          <div class="form-group" style="margin-bottom: 1rem;">
            <label class="form-label">Advance Amount (PKR)</label>
            <input type="number" name="advance_amount" value="<?= (float)($order['advance_amount'] ?? 0) > 0 ? (int)$order['advance_amount'] : 500 ?>" min="0" step="1" required class="form-control">
          </div>
          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label">Reference / TID (JazzCash / EasyPaisa / Bank)</label>
            <input type="text" name="advance_reference" placeholder="e.g. TID 04928174928" class="form-control">
          </div>
          <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            <span>Verify Advance Payment</span>
          </button>
        </form>
      </div>

      <!-- Dispatch & Courier Controls Card -->
      <div class="admin-panel-card order-dispatch-card">
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--admin-text);">Dispatch & Courier Controls</h3>

        <form action="<?= e($adminPath) ?>/orders/<?= (int)$order['id'] ?>" method="POST">
          <?= mta_csrf_field() ?>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="status" class="form-label">Order Status</label>
            <select id="status" name="status" class="form-control">
              <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending Confirmation</option>
              <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
              <option value="dispatched" <?= $order['status'] === 'dispatched' ? 'selected' : '' ?>>Dispatched to Courier</option>
              <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
              <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
              <option value="returned" <?= $order['status'] === 'returned' ? 'selected' : '' ?>>Returned to Store</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="courier_name" class="form-label">Courier Partner</label>
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

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="tracking_number" class="form-label">Consignment / Tracking #</label>
            <input type="text" id="tracking_number" name="tracking_number" value="<?= e($order['tracking_number'] ?? '') ?>" placeholder="e.g. 58392019" class="form-control">
          </div>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="tracking_link" class="form-label">Direct Tracking URL (Auto-Generated)</label>
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
