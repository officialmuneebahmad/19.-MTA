<?php
/**
 * Multan Tech Accessories — Admin Orders Listing View
 */
$adminPath = mta_admin_path();
?>
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">Customer Orders (<?= (int)$pagination['total'] ?>)</div>
  </div>

  <!-- Search & Filter Bar -->
  <form action="<?= e($adminPath) ?>/orders" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
    <div style="flex: 2; min-width: 220px;">
      <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search by Order #, Customer Name, or Phone..." class="form-control">
    </div>

    <div style="flex: 1; min-width: 150px;">
      <select name="status" class="form-control">
        <option value="">All Statuses</option>
        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="confirmed" <?= $status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
        <option value="dispatched" <?= $status === 'dispatched' ? 'selected' : '' ?>>Dispatched</option>
        <option value="delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Delivered</option>
        <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="<?= e($adminPath) ?>/orders" class="btn btn-secondary btn-sm">Reset</a>
  </form>

  <table class="admin-table">
    <thead>
      <tr>
        <th>Order #</th>
        <th>Customer</th>
        <th>Phone</th>
        <th>City</th>
        <th>Subtotal</th>
        <th>Delivery</th>
        <th>Total</th>
        <th>Status</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td style="font-weight: 700; font-family: monospace;">
              <a href="<?= e($adminPath) ?>/orders/<?= (int)$o['id'] ?>" style="color: #9B3828;">
                <?= e($o['order_number']) ?>
              </a>
              <?php if (!empty($o['is_second_order'])): ?>
                <span class="badge badge-success" style="font-size: 0.65rem; display: block; margin-top: 0.2rem;">2nd Order (Free Delivery)</span>
              <?php endif; ?>
            </td>
            <td><?= e($o['customer_name']) ?></td>
            <td>
              <a href="tel:<?= e($o['customer_phone']) ?>"><?= e($o['customer_phone']) ?></a>
            </td>
            <td><?= e($o['city']) ?></td>
            <td><?= mta_money($o['subtotal']) ?></td>
            <td><?= (float)$o['delivery_charge'] <= 0 ? 'FREE' : mta_money($o['delivery_charge']) ?></td>
            <td style="font-weight: 700; color: #9B3828;"><?= mta_money($o['total']) ?></td>
            <td>
              <?php
                $stClass = match($o['status']) {
                  'delivered' => 'badge-success',
                  'dispatched' => 'badge-info',
                  'confirmed' => 'badge-info',
                  'cancelled' => 'badge-danger',
                  default => 'badge-warning',
                };
              ?>
              <span class="badge <?= $stClass ?>"><?= e($o['status']) ?></span>
            </td>
            <td style="color: #777; font-size: 0.8rem;">
              <?= date('d M Y, h:i A', strtotime($o['created_at'])) ?>
            </td>
            <td>
              <a href="<?= e($adminPath) ?>/orders/<?= (int)$o['id'] ?>" class="btn btn-secondary btn-sm">
                View &rarr;
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="10" style="text-align: center; padding: 2rem; color: #777;">
            No orders found.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <?php if ($pagination['total_pages'] > 1): ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; font-size: 0.85rem;">
      <div style="color: #666;">
        Page <?= $pagination['page'] ?> of <?= $pagination['total_pages'] ?>
      </div>
      <div style="display: flex; gap: 0.35rem;">
        <?php if ($pagination['page'] > 1): ?>
          <a href="<?= e($adminPath) ?>/orders?page=<?= $pagination['page'] - 1 ?>&q=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="btn btn-secondary btn-sm">&larr; Prev</a>
        <?php endif; ?>
        <?php if ($pagination['page'] < $pagination['total_pages']): ?>
          <a href="<?= e($adminPath) ?>/orders?page=<?= $pagination['page'] + 1 ?>&q=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>" class="btn btn-secondary btn-sm">Next &rarr;</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
