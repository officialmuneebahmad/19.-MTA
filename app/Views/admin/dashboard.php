<?php
/**
 * Multan Tech Accessories — Admin Dashboard View
 */
$adminPath = mta_admin_path();
?>
<!-- KPI Metric Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem;">

  <!-- Total Catalog Items -->
  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      Imported Catalog
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: #141413;">
      <?= (int)($productStats['total_products'] ?? 0) ?>
    </div>
    <div style="font-size: 0.8rem; color: #0E7064; margin-top: 0.35rem;">
      <strong><?= (int)($productStats['in_stock'] ?? 0) ?></strong> In Stock &bull; <strong><?= (int)($productStats['out_of_stock'] ?? 0) ?></strong> Out
    </div>
  </div>

  <!-- Content Gate (Approved vs Draft) -->
  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      SEO Indexable Products
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: #0E7064;">
      <?= (int)($productStats['approved_count'] ?? 0) ?>
    </div>
    <div style="font-size: 0.8rem; color: #777; margin-top: 0.35rem;">
      <strong><?= (int)($productStats['raw_count'] ?? 0) ?></strong> Raw &bull; <strong><?= (int)($productStats['draft_count'] ?? 0) ?></strong> AI Drafts
    </div>
  </div>

  <!-- Today's Orders -->
  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      Orders Today (COD)
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: #9B3828;">
      <?= (int)($orderStats['orders_today'] ?? 0) ?>
    </div>
    <div style="font-size: 0.8rem; color: #777; margin-top: 0.35rem;">
      <?= mta_money($orderStats['revenue_today'] ?? 0) ?> gross value
    </div>
  </div>

  <!-- Pending Dispatches -->
  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      Pending Confirmation
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: #D97706;">
      <?= (int)($orderStats['pending_orders'] ?? 0) ?>
    </div>
    <div style="font-size: 0.8rem; color: #777; margin-top: 0.35rem;">
      Total historical orders: <strong><?= (int)($orderStats['total_orders'] ?? 0) ?></strong>
    </div>
  </div>

</div>

<!-- Supplier Feed Status & Quick Sync -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">Supplier Synchronization Status</div>
    <div>
      <a href="<?= e($adminPath) ?>/sync" class="btn btn-secondary btn-sm">Full Sync Logs &rarr;</a>
    </div>
  </div>

  <div style="display: flex; gap: 2.5rem; align-items: center; flex-wrap: wrap;">
    <div>
      <div style="font-size: 0.8rem; color: #777;">Last Executed:</div>
      <div style="font-weight: 700; font-size: 0.95rem;">
        <?= !empty($lastSync['finished_at']) ? date('d M Y, h:i:s A', strtotime($lastSync['finished_at'])) : 'Never run' ?>
      </div>
    </div>

    <div>
      <div style="font-size: 0.8rem; color: #777;">Feed Products Received:</div>
      <div style="font-weight: 700; font-size: 0.95rem; color: #0E7064;">
        <?= (int)($lastSync['products_received'] ?? 0) ?> items (Expected: 991)
      </div>
    </div>

    <div>
      <div style="font-size: 0.8rem; color: #777;">Sync Result:</div>
      <div>
        <?php if (($lastSync['status'] ?? '') === 'success'): ?>
          <span class="badge badge-success">Success</span>
        <?php elseif (($lastSync['status'] ?? '') === 'running'): ?>
          <span class="badge badge-warning">Running</span>
        <?php else: ?>
          <span class="badge badge-neutral"><?= e($lastSync['status'] ?? 'Idle') ?></span>
        <?php endif; ?>
      </div>
    </div>

    <div>
      <div style="font-size: 0.8rem; color: #777;">Execution Time:</div>
      <div style="font-weight: 600;">
        <?= number_format((float)($lastSync['duration_seconds'] ?? 0), 2) ?>s
      </div>
    </div>

    <div style="margin-left: auto;">
      <form action="<?= e($adminPath) ?>/sync/trigger" method="POST">
        <?= mta_csrf_field() ?>
        <button type="submit" class="btn btn-primary btn-sm">
          🔄 Sync Supplier Now
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Recent Orders Table -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">Recent Customer Orders</div>
    <a href="<?= e($adminPath) ?>/orders" class="btn btn-secondary btn-sm">View All Orders &rarr;</a>
  </div>

  <?php if (!empty($recentOrders)): ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Order #</th>
          <th>Customer</th>
          <th>Phone</th>
          <th>City</th>
          <th>Total</th>
          <th>Status</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentOrders as $order): ?>
          <tr>
            <td style="font-weight: 700; font-family: monospace;">
              <a href="<?= e($adminPath) ?>/orders/<?= (int)$order['id'] ?>" style="color: #9B3828;">
                <?= e($order['order_number']) ?>
              </a>
            </td>
            <td><?= e($order['customer_name']) ?></td>
            <td><?= e($order['customer_phone']) ?></td>
            <td><?= e($order['city']) ?></td>
            <td style="font-weight: 700;"><?= mta_money($order['total']) ?></td>
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
            <td style="color: #777; font-size: 0.8rem;">
              <?= date('d M, h:i A', strtotime($order['created_at'])) ?>
            </td>
            <td>
              <a href="<?= e($adminPath) ?>/orders/<?= (int)$order['id'] ?>" class="btn btn-secondary btn-sm">
                Details &rarr;
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p style="color: #777; padding: 1.5rem 0; text-align: center;">No orders recorded yet.</p>
  <?php endif; ?>
</div>
