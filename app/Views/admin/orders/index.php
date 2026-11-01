<?php
/**
 * Multan Tech Accessories — Admin Orders Listing View
 * Selectable rows, bulk delete with double confirmation, refresh, and responsive layout.
 */
$adminPath = mta_admin_path();
?>
<div class="admin-card">
  <div class="admin-card-header">
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
      <div class="admin-card-title">Customer Orders (<?= (int)$pagination['total'] ?>)</div>
      <button type="button" onclick="window.location.reload();" class="btn btn-secondary btn-sm" title="Refresh Orders List">
        🔄 Refresh
      </button>
    </div>
    <div>
      <span style="font-size: 0.85rem; color: var(--admin-text-muted);">
        Showing page <?= (int)$pagination['page'] ?> of <?= (int)$pagination['total_pages'] ?>
      </span>
    </div>
  </div>

  <!-- Search & Filter Bar -->
  <form action="<?= e($adminPath) ?>/orders" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.75rem; align-items: center;">
    <div style="flex: 2; min-width: 240px;">
      <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search by Order #, Customer Name, or Phone..." class="form-control">
    </div>

    <div style="flex: 1; min-width: 160px;">
      <select name="status" class="form-control">
        <option value="">All Statuses</option>
        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="confirmed" <?= $status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
        <option value="dispatched" <?= $status === 'dispatched' ? 'selected' : '' ?>>Dispatched</option>
        <option value="delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Delivered</option>
        <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
      </select>
    </div>

    <div style="display: flex; gap: 0.5rem;">
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      <a href="<?= e($adminPath) ?>/orders" class="btn btn-secondary btn-sm">Reset</a>
    </div>
  </form>

  <!-- Bulk Actions Bar (Appears when >= 1 checkbox is selected) -->
  <div id="bulkActionsBar" style="display: none; align-items: center; justify-content: space-between; background: var(--admin-surface-2, #343432); border: 1px solid var(--admin-card-border); padding: 0.85rem 1.25rem; border-radius: 10px; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <span style="font-size: 1.1rem;">☑️</span>
      <strong id="selectedCountText" style="font-size: 0.95rem; color: var(--admin-text);">0 orders selected</strong>
    </div>
    <div style="display: flex; gap: 0.5rem; align-items: center;">
      <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm">
        🗑️ Delete Selected Orders
      </button>
      <button type="button" id="btnCancelSelection" class="btn btn-secondary btn-sm">
        Deselect All
      </button>
    </div>
  </div>

  <!-- Hidden Form for Bulk Delete -->
  <form id="bulkDeleteForm" action="<?= e($adminPath) ?>/orders/bulk-delete" method="POST" style="display: none;">
    <?= mta_csrf_field() ?>
    <div id="bulkDeleteInputs"></div>
  </form>

  <div class="admin-table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 44px; text-align: center;">
            <input type="checkbox" id="selectAllOrders" title="Select all orders on this page" style="width: 18px; height: 18px; cursor: pointer;">
          </th>
          <th>Order #</th>
          <th>Customer</th>
          <th>Phone</th>
          <th>City</th>
          <th>Subtotal</th>
          <th>Delivery</th>
          <th>Total</th>
          <th>Status</th>
          <th>Date</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($orders)): ?>
          <?php foreach ($orders as $o): ?>
            <tr id="row-order-<?= (int)$o['id'] ?>">
              <td style="text-align: center;">
                <input type="checkbox" name="order_ids[]" value="<?= (int)$o['id'] ?>" class="order-select-cb" data-ordernum="<?= e($o['order_number']) ?>" style="width: 18px; height: 18px; cursor: pointer;">
              </td>
              <td style="font-weight: 700; font-family: monospace;">
                <a href="<?= e($adminPath) ?>/orders/<?= (int)$o['id'] ?>" style="color: var(--mta-orange); text-decoration: underline;">
                  <?= e($o['order_number']) ?>
                </a>
                <?php if (!empty($o['is_second_order'])): ?>
                  <span class="badge badge-success" style="font-size: 0.65rem; display: block; margin-top: 0.2rem;">2nd Order (Free Delivery)</span>
                <?php endif; ?>
              </td>
              <td><?= e($o['customer_name']) ?></td>
              <td>
                <a href="tel:<?= e($o['customer_phone']) ?>" style="color: var(--admin-text);"><?= e($o['customer_phone']) ?></a>
              </td>
              <td><?= e($o['city']) ?></td>
              <td><?= mta_money($o['subtotal']) ?></td>
              <td><?= (float)$o['delivery_charge'] <= 0 ? 'FREE' : mta_money($o['delivery_charge']) ?></td>
              <td style="font-weight: 700; color: var(--mta-orange);"><?= mta_money($o['total']) ?></td>
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
              <td style="color: var(--admin-text-muted); font-size: 0.8rem;">
                <?= date('d M Y, h:i A', strtotime($o['created_at'])) ?>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <a href="<?= e($adminPath) ?>/orders/<?= (int)$o['id'] ?>" class="btn btn-secondary btn-sm" title="View Order Details">
                  View
                </a>
                <a href="<?= e($adminPath) ?>/orders/<?= (int)$o['id'] ?>/invoice" target="_blank" class="btn btn-secondary btn-sm" title="Print Official Invoice">
                  📄
                </a>
                <form action="<?= e($adminPath) ?>/orders/<?= (int)$o['id'] ?>/delete" method="POST" style="display: inline-block; margin-left: 0.25rem;" onsubmit="return mtaConfirmTwice('Are you sure you want to delete Order #<?= e($o['order_number']) ?>?', '⚠️ FINAL CONFIRMATION: Permanently delete Order #<?= e($o['order_number']) ?> from the database?');">
                  <?= mta_csrf_field() ?>
                  <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.35rem 0.6rem;" title="Delete Order">
                    🗑️
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="11" style="text-align: center; padding: 3rem; color: var(--admin-text-muted);">
              No customer orders found matching the criteria.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($pagination['total_pages'] > 1): ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; font-size: 0.9rem; flex-wrap: wrap; gap: 1rem;">
      <div style="color: var(--admin-text-muted);">
        Page <?= (int)$pagination['page'] ?> of <?= (int)$pagination['total_pages'] ?> (Total <?= (int)$pagination['total'] ?> orders)
      </div>
      <div style="display: flex; gap: 0.5rem;">
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

<!-- Bulk Selection Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  var selectAll = document.getElementById('selectAllOrders');
  var checkboxes = document.querySelectorAll('.order-select-cb');
  var bulkBar = document.getElementById('bulkActionsBar');
  var countText = document.getElementById('selectedCountText');
  var btnBulkDelete = document.getElementById('btnBulkDelete');
  var btnCancel = document.getElementById('btnCancelSelection');
  var formInputs = document.getElementById('bulkDeleteInputs');
  var bulkForm = document.getElementById('bulkDeleteForm');

  function updateBulkBar() {
    var checked = document.querySelectorAll('.order-select-cb:checked');
    var count = checked.length;
    if (count > 0) {
      bulkBar.style.display = 'flex';
      countText.innerText = count + ' order' + (count > 1 ? 's' : '') + ' selected';
    } else {
      bulkBar.style.display = 'none';
      if (selectAll) selectAll.checked = false;
    }
  }

  if (selectAll) {
    selectAll.addEventListener('change', function() {
      checkboxes.forEach(function(cb) {
        cb.checked = selectAll.checked;
      });
      updateBulkBar();
    });
  }

  checkboxes.forEach(function(cb) {
    cb.addEventListener('change', updateBulkBar);
  });

  if (btnCancel) {
    btnCancel.addEventListener('click', function() {
      checkboxes.forEach(function(cb) { cb.checked = false; });
      if (selectAll) selectAll.checked = false;
      updateBulkBar();
    });
  }

  if (btnBulkDelete) {
    btnBulkDelete.addEventListener('click', function() {
      var checked = document.querySelectorAll('.order-select-cb:checked');
      var count = checked.length;
      if (count === 0) return;

      var step1 = window.mtaConfirmTwice 
        ? window.mtaConfirmTwice(
            'Are you sure you want to delete ' + count + ' selected order' + (count > 1 ? 's' : '') + '? This action cannot be undone.',
            '⚠️ FINAL CONFIRMATION: Are you ABSOLUTELY sure? This will permanently delete ' + count + ' order' + (count > 1 ? 's' : '') + ' from the database.'
          )
        : (confirm('Delete ' + count + ' orders?') && confirm('FINAL CONFIRMATION: Permanently delete ' + count + ' orders?'));

      if (step1) {
        formInputs.innerHTML = '';
        checked.forEach(function(cb) {
          var input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'order_ids[]';
          input.value = cb.value;
          formInputs.appendChild(input);
        });
        bulkForm.submit();
      }
    });
  }
});
</script>
