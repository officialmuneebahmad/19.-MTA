<?php
/**
 * Multan Tech Accessories — Enterprise Admin Operations Telemetry Dashboard
 */
$adminPath = mta_admin_path();
?>
<!-- Operational Metric Telemetry Widgets -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">

  <!-- Catalog & Inventory Depth -->
  <div class="admin-card" style="margin-bottom: 0; position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--brand-orange), #D84D34);"></div>
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
      <span style="font-size: 0.72rem; text-transform: uppercase; color: var(--console-text-muted); font-weight: 700; letter-spacing: 0.6px;">Master Inventory</span>
      <span class="badge badge-success"><?= (int)($productStats['in_stock'] ?? 0) ?> Active</span>
    </div>
    <div style="font-size: 2.15rem; font-weight: 800; color: #FFFFFF; font-family: var(--font-mono); line-height: 1.1;">
      <?= number_format((int)($productStats['total_products'] ?? 0)) ?>
    </div>
    <div style="font-size: 0.775rem; color: var(--console-text-muted); margin-top: 0.6rem; display: flex; gap: 0.6rem; align-items: center;">
      <span><strong style="color: #2DD4BF;"><?= (int)($productStats['in_stock'] ?? 0) ?></strong> in-stock</span>
      <span>&bull;</span>
      <span><strong style="color: #F87171;"><?= (int)($productStats['out_of_stock'] ?? 0) ?></strong> sold out</span>
      <span>&bull;</span>
      <span><strong style="color: #FBBF24;"><?= (int)($productStats['coming_soon'] ?? 0) ?></strong> queue</span>
    </div>
  </div>

  <!-- Content & SEO Production Gate -->
  <div class="admin-card" style="margin-bottom: 0; position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--brand-teal), #099A90);"></div>
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
      <span style="font-size: 0.72rem; text-transform: uppercase; color: var(--console-text-muted); font-weight: 700; letter-spacing: 0.6px;">Indexed Catalog</span>
      <span class="badge badge-info">SEO Ready</span>
    </div>
    <div style="font-size: 2.15rem; font-weight: 800; color: #2DD4BF; font-family: var(--font-mono); line-height: 1.1;">
      <?= number_format((int)($productStats['approved_count'] ?? 0)) ?>
    </div>
    <div style="font-size: 0.775rem; color: var(--console-text-muted); margin-top: 0.6rem; display: flex; gap: 0.6rem; align-items: center;">
      <span><strong><?= (int)($productStats['raw_count'] ?? 0) ?></strong> raw feed</span>
      <span>&bull;</span>
      <span><strong><?= (int)($productStats['draft_count'] ?? 0) ?></strong> AI drafts</span>
    </div>
  </div>

  <!-- Realtime Order Pipeline (COD) -->
  <div class="admin-card" style="margin-bottom: 0; position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #F59E0B, var(--brand-orange));"></div>
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
      <span style="font-size: 0.72rem; text-transform: uppercase; color: var(--console-text-muted); font-weight: 700; letter-spacing: 0.6px;">Daily Bookings (COD)</span>
      <span class="badge badge-warning"><?= (int)($orderStats['orders_today'] ?? 0) ?> Today</span>
    </div>
    <div style="font-size: 2.15rem; font-weight: 800; color: #FFFFFF; font-family: var(--font-mono); line-height: 1.1;">
      <?= (int)($orderStats['orders_today'] ?? 0) ?>
    </div>
    <div style="font-size: 0.775rem; color: var(--console-text-muted); margin-top: 0.6rem;">
      Gross Value Today: <strong style="color: var(--brand-orange);"><?= mta_money($orderStats['revenue_today'] ?? 0) ?></strong>
    </div>
  </div>

  <!-- Dispatch & Fulfillment Queue -->
  <div class="admin-card" style="margin-bottom: 0; position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #3B82F6, #8B5CF6);"></div>
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
      <span style="font-size: 0.72rem; text-transform: uppercase; color: var(--console-text-muted); font-weight: 700; letter-spacing: 0.6px;">Fulfillment Queue</span>
      <span class="badge badge-neutral"><?= (int)($orderStats['pending_orders'] ?? 0) ?> Pending</span>
    </div>
    <div style="font-size: 2.15rem; font-weight: 800; color: #FFFFFF; font-family: var(--font-mono); line-height: 1.1;">
      <?= (int)($orderStats['pending_orders'] ?? 0) ?>
    </div>
    <div style="font-size: 0.775rem; color: var(--console-text-muted); margin-top: 0.6rem;">
      All-time orders dispatched: <strong><?= number_format((int)($orderStats['total_orders'] ?? 0)) ?></strong>
    </div>
  </div>

</div>

<!-- Feed Synchronization Health Telemetry -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--brand-teal)" stroke-width="2"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.3"></path></svg>
      <span>Supplier Synchronization Pipeline Health</span>
    </div>
    <div style="display: flex; gap: 0.5rem;">
      <a href="<?= e($adminPath) ?>/sync" class="btn btn-secondary btn-sm">Audit Sync Logs &rarr;</a>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; align-items: center; background: rgba(0, 0, 0, 0.2); padding: 1rem 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--console-card-border);">
    <div>
      <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--console-text-muted); font-weight: 700;">Last Ingestion Run</div>
      <div style="font-weight: 700; font-size: 0.9rem; color: #FFFFFF; margin-top: 0.2rem; font-family: var(--font-mono);">
        <?= !empty($lastSync['finished_at']) ? date('d M Y, h:i:s A', strtotime($lastSync['finished_at'])) : 'Pending initialization' ?>
      </div>
    </div>

    <div>
      <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--console-text-muted); font-weight: 700;">Records Received</div>
      <div style="font-weight: 700; font-size: 0.9rem; color: #2DD4BF; margin-top: 0.2rem; font-family: var(--font-mono);">
        <?= (int)($lastSync['products_received'] ?? 0) ?> items (Target: 991)
      </div>
    </div>

    <div>
      <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--console-text-muted); font-weight: 700;">Sync Execution State</div>
      <div style="margin-top: 0.2rem;">
        <?php if (($lastSync['status'] ?? '') === 'success'): ?>
          <span class="badge badge-success">Ingestion Operational</span>
        <?php elseif (($lastSync['status'] ?? '') === 'running'): ?>
          <span class="badge badge-warning">Stream Processing</span>
        <?php else: ?>
          <span class="badge badge-neutral"><?= e($lastSync['status'] ?? 'Standby') ?></span>
        <?php endif; ?>
      </div>
    </div>

    <div>
      <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--console-text-muted); font-weight: 700;">Duration Latency</div>
      <div style="font-weight: 700; font-size: 0.9rem; color: #FFFFFF; margin-top: 0.2rem; font-family: var(--font-mono);">
        <?= number_format((float)($lastSync['duration_seconds'] ?? 0), 2) ?>s
      </div>
    </div>

    <div style="text-align: right;">
      <form action="<?= e($adminPath) ?>/sync/trigger" method="POST" style="display:inline;">
        <?= mta_csrf_field() ?>
        <button type="submit" class="btn btn-primary btn-sm">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
          <span>Run Immediate Sync</span>
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Realtime Dispatch Orders Feed -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--brand-orange)" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
      <span>Live Customer Order Stream</span>
    </div>
    <a href="<?= e($adminPath) ?>/orders" class="btn btn-secondary btn-sm">All Dispatches &rarr;</a>
  </div>

  <?php if (!empty($recentOrders)): ?>
    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Tracking #</th>
            <th>Recipient</th>
            <th>Phone</th>
            <th>Destination</th>
            <th>Total Amount</th>
            <th>Fulfillment State</th>
            <th>Timestamp</th>
            <th style="text-align: right;">Console</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentOrders as $order): ?>
            <tr>
              <td>
                <a href="<?= e($adminPath) ?>/orders/<?= (int)$order['id'] ?>" class="code-pill">
                  <?= e($order['order_number']) ?>
                </a>
              </td>
              <td style="font-weight: 600; color: #FFFFFF;"><?= e($order['customer_name']) ?></td>
              <td style="font-family: var(--font-mono); font-size: 0.8rem; color: var(--console-text-muted);"><?= e($order['customer_phone']) ?></td>
              <td><span style="display: inline-flex; align-items: center; gap: 0.35rem;"><?= e($order['city']) ?></span></td>
              <td style="font-weight: 700; color: #FFFFFF; font-family: var(--font-mono);"><?= mta_money($order['total']) ?></td>
              <td>
                <?php
                  $statusClass = match($order['status']) {
                    'delivered' => 'badge-success',
                    'dispatched' => 'badge-info',
                    'confirmed' => 'badge-info',
                    'cancelled' => 'badge-danger',
                    default => 'badge-warning',
                  };
                ?>
                <span class="badge <?= $statusClass ?>"><?= e($order['status']) ?></span>
              </td>
              <td style="color: var(--console-text-muted); font-size: 0.775rem; font-family: var(--font-mono);">
                <?= date('d M, h:i A', strtotime($order['created_at'])) ?>
              </td>
              <td style="text-align: right;">
                <a href="<?= e($adminPath) ?>/orders/<?= (int)$order['id'] ?>" class="btn btn-secondary btn-sm">
                  Inspect &rarr;
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div style="color: var(--console-text-muted); padding: 2.5rem 0; text-align: center; font-size: 0.9rem;">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--console-text-dim)" stroke-width="1.5" style="margin: 0 auto 0.75rem;"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
      <div>Zero pending customer orders in queue. System ready for incoming traffic.</div>
    </div>
  <?php endif; ?>
</div>