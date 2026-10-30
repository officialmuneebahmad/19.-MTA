<?php
/**
 * Multan Tech Accessories — Admin WhatsApp Orders & Inquiries Canvas (Part B)
 * MultanTech.store
 */
$adminPath = mta_admin_path();
?>

<!-- =========================================================================
     1. Telemetry Metric Cards
     ========================================================================= -->
<div class="kpi-grid" style="margin-bottom: 2rem;">
  
  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-label">WhatsApp Clicks</span>
      <div class="kpi-icon-box bg-teal-light text-teal">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
      </div>
    </div>
    <div class="kpi-value text-teal"><?= number_format($totalClicks) ?></div>
    <div class="kpi-meta">
      <span class="badge badge-teal">Storefront Engagement</span>
      <span class="kpi-subtext">Share & Order Clicks</span>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-label">Recorded Inquiries</span>
      <div class="kpi-icon-box bg-orange-light text-orange">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
      </div>
    </div>
    <div class="kpi-value text-orange"><?= number_format($totalInquiries) ?></div>
    <div class="kpi-meta">
      <span class="badge badge-amber">Direct Leads</span>
      <span class="kpi-subtext">Beacon API Logged</span>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-label">WhatsApp Bookings</span>
      <div class="kpi-icon-box bg-green-light text-green">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
      </div>
    </div>
    <div class="kpi-value text-green"><?= number_format($totalWaOrders) ?></div>
    <div class="kpi-meta">
      <span class="badge badge-success">COD Dispatches</span>
      <span class="kpi-subtext">Manual Chat Bookings</span>
    </div>
  </div>

</div>

<!-- =========================================================================
     2. Main Two-Column Layout (Manual Order Creator & Inquiries Log)
     ========================================================================= -->
<div class="dashboard-two-cols">
  
  <!-- Column 1: Add WhatsApp Order Manual Form -->
  <div class="admin-panel">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">Add WhatsApp Direct Order</h4>
        <span class="panel-badge" style="background:var(--mta-teal-light); color:var(--mta-teal);">Chat Booking</span>
      </div>
    </div>

    <form action="<?= e($adminPath) ?>/whatsapp-orders/create" method="POST" style="padding: 1.25rem;">
      <?= mta_csrf_field() ?>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div class="form-group">
          <label class="form-label">Customer Full Name *</label>
          <input type="text" name="customer_name" required placeholder="e.g. Usama Khan" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">WhatsApp Phone Number *</label>
          <input type="tel" name="customer_phone" required placeholder="e.g. 03001234567" class="form-control">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div class="form-group">
          <label class="form-label">Destination City *</label>
          <select name="city" id="wa-order-city" class="form-control" required>
            <option value="Multan">Multan (Local Delivery - Rs. 100)</option>
            <option value="Lahore">Lahore (Standard - Rs. 250)</option>
            <option value="Karachi">Karachi (Standard - Rs. 250)</option>
            <option value="Islamabad">Islamabad (Standard - Rs. 250)</option>
            <option value="Rawalpindi">Rawalpindi (Standard - Rs. 250)</option>
            <option value="Faisalabad">Faisalabad (Standard - Rs. 250)</option>
            <option value="Peshawar">Peshawar (Standard - Rs. 250)</option>
            <option value="Gujranwala">Gujranwala (Standard - Rs. 250)</option>
            <option value="Sialkot">Sialkot (Standard - Rs. 250)</option>
            <option value="Quetta">Quetta (Standard - Rs. 250)</option>
            <option value="Bahawalpur">Bahawalpur (Standard - Rs. 250)</option>
            <option value="Other">Other City / District (Standard - Rs. 250)</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Select Catalog Product *</label>
          <select name="product_id" id="wa-order-product" class="form-control" required>
            <option value="">-- Choose Product --</option>
            <?php foreach ($products as $p): ?>
              <option value="<?= (int)$p['id'] ?>" data-price="<?= (int)$p['sell_price'] ?>">
                <?= e($p['title']) ?> (Rs. <?= number_format((int)$p['sell_price']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div class="form-group">
          <label class="form-label">Order Quantity</label>
          <input type="number" name="quantity" id="wa-order-qty" value="1" min="1" max="100" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Advance Received (PKR)</label>
          <input type="number" name="advance_amount" id="wa-order-advance" value="0" min="0" step="50" placeholder="e.g. 500" class="form-control">
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 1rem;">
        <label class="form-label">Complete Shipping Address *</label>
        <textarea name="shipping_address" required rows="2" placeholder="House/Shop #, Street, Area, City" class="form-control"></textarea>
      </div>

      <div class="form-group" style="margin-bottom: 1rem;">
        <label class="form-label">Advance Payment Reference / TID</label>
        <input type="text" name="advance_reference" placeholder="e.g. JazzCash TID 08392019482" class="form-control">
      </div>

      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label class="form-label">Order Notes / Chat Context</label>
        <input type="text" name="customer_notes" placeholder="e.g. Customer requested urgent dispatch via Leopard" class="form-control">
      </div>

      <!-- Live Calculation Card -->
      <div style="background:var(--admin-surface-muted); border:1px solid var(--admin-card-border); border-radius:var(--radius-md); padding:1rem; margin-bottom:1.25rem; font-size:0.875rem;">
        <div style="display:flex; justify-content:space-between; margin-bottom:0.35rem;">
          <span style="color:var(--admin-text-muted);">Estimated Delivery:</span>
          <span id="wa-calc-delivery" style="font-weight:600;">Rs. 100 (Free above Rs. 5,000)</span>
        </div>
        <div style="display:flex; justify-content:space-between; font-weight:700; color:var(--mta-orange); font-size:1rem; border-top:1px dashed var(--admin-card-border); padding-top:0.5rem; margin-top:0.35rem;">
          <span>Outstanding COD Collection:</span>
          <span id="wa-calc-cod" style="font-family:var(--font-heading);">Rs. --</span>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; height: 48px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
        <span>Book WhatsApp Order & Generate Invoice</span>
      </button>
    </form>
  </div>

  <!-- Column 2: Recent WhatsApp Inquiries Stream -->
  <div class="admin-panel">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">Recent WhatsApp Inquiries</h4>
        <span class="panel-badge"><?= count($inquiries) ?> Leads</span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Product / Context</th>
            <th>IP & Time</th>
            <th style="text-align:right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($inquiries)): ?>
            <tr>
              <td colspan="3" style="text-align:center; padding:3rem 1rem; color:var(--admin-text-muted);">
                No direct WhatsApp inquiries captured yet. Customer clicks on WhatsApp share and WhatsApp order buttons will appear here in real time.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($inquiries as $inq): ?>
              <tr>
                <td>
                  <div style="font-weight:600; color:var(--admin-text); font-size:0.875rem;">
                    <?= e($inq['product_title'] ?: 'General Catalog Inquiry') ?>
                  </div>
                  <?php if (!empty($inq['product_price'])): ?>
                    <div style="font-size:0.75rem; color:var(--mta-orange); font-weight:700;">
                      <?= mta_money($inq['product_price']) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="font-size:0.8rem; color:var(--admin-text);"><?= date('d M, h:i A', strtotime($inq['created_at'])) ?></div>
                  <div style="font-size:0.72rem; color:var(--admin-text-dim); font-family:monospace;"><?= e($inq['ip_address'] ?? '127.0.0.1') ?></div>
                </td>
                <td style="text-align:right;">
                  <?php if (!empty($inq['product_slug'])): ?>
                    <a href="/products/<?= e($inq['product_slug']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" title="View Product Page">
                      View &rarr;
                    </a>
                  <?php else: ?>
                    <span class="badge badge-neutral">Inquiry</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const productSelect = document.getElementById('wa-order-product');
  const citySelect = document.getElementById('wa-order-city');
  const qtyInput = document.getElementById('wa-order-qty');
  const advanceInput = document.getElementById('wa-order-advance');
  const calcDelivery = document.getElementById('wa-calc-delivery');
  const calcCod = document.getElementById('wa-calc-cod');

  function updateCalc() {
    if (!productSelect) return;
    const opt = productSelect.options[productSelect.selectedIndex];
    const price = opt ? parseInt(opt.getAttribute('data-price') || '0', 10) : 0;
    const qty = Math.max(1, parseInt(qtyInput.value || '1', 10));
    const advance = Math.max(0, parseFloat(advanceInput.value || '0'));
    const isMultan = (citySelect.value || '').toLowerCase() === 'multan';

    const subtotal = price * qty;
    let delivery = 250;
    if (subtotal >= 5000) {
      delivery = 0;
    } else if (isMultan) {
      delivery = 100;
    }

    const total = subtotal + (price > 0 ? delivery : 0);
    const cod = Math.max(0, total - advance);

    calcDelivery.textContent = delivery === 0 ? 'FREE (Orders over Rs. 5,000)' : 'Rs. ' + delivery.toLocaleString();
    calcCod.textContent = price > 0 ? 'Rs. ' + cod.toLocaleString() : 'Select a product';
  }

  if (productSelect) productSelect.addEventListener('change', updateCalc);
  if (citySelect) citySelect.addEventListener('change', updateCalc);
  if (qtyInput) qtyInput.addEventListener('input', updateCalc);
  if (advanceInput) advanceInput.addEventListener('input', updateCalc);
  updateCalc();
});
</script>

