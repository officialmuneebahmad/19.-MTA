<?php
/**
 * Multan Tech Accessories — Admin Products Listing View
 */
$adminPath = mta_admin_path();
?>
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">Products (<?= (int)$pagination['total'] ?> items)</div>
    <div style="display: flex; gap: 0.5rem;">
      <a href="<?= e($adminPath) ?>/import" class="btn btn-secondary btn-sm">📥 Multi-Source Import</a>
    </div>
  </div>

  <!-- Search & Filter Form -->
  <form action="<?= e($adminPath) ?>/products" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
    <div style="flex: 2; min-width: 220px;">
      <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search by title, SKU, or supplier ID..." class="form-control">
    </div>

    <div style="flex: 1; min-width: 160px;">
      <select name="category_id" class="form-control">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= (int)$cat['id'] ?>" <?= $categoryId === (int)$cat['id'] ? 'selected' : '' ?>>
            <?= e($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div style="flex: 1; min-width: 140px;">
      <select name="stock_status" class="form-control">
        <option value="">All Stock</option>
        <option value="in_stock" <?= $stockStatus === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
        <option value="out_of_stock" <?= $stockStatus === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
        <option value="coming_soon" <?= $stockStatus === 'coming_soon' ? 'selected' : '' ?>>Coming Soon</option>
      </select>
    </div>

    <div style="flex: 1; min-width: 140px;">
      <select name="content_state" class="form-control">
        <option value="">All Content States</option>
        <option value="approved" <?= $contentState === 'approved' ? 'selected' : '' ?>>Approved (Indexed)</option>
        <option value="ai_draft" <?= $contentState === 'ai_draft' ? 'selected' : '' ?>>AI Draft</option>
        <option value="supplier_raw" <?= $contentState === 'supplier_raw' ? 'selected' : '' ?>>Supplier Raw</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="<?= e($adminPath) ?>/products" class="btn btn-secondary btn-sm">Reset</a>
  </form>

  <!-- Products Table -->
  <table class="admin-table">
    <thead>
      <tr>
        <th style="width: 50px;">Img</th>
        <th>Product Title & SKU</th>
        <th>Category / Brand</th>
        <th>Supplier Cost</th>
        <th>Selling Price</th>
        <th>Stock</th>
        <th>SEO Gate</th>
        <th>Quick Actions</th>
        <th>Edit</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $p): ?>
          <tr>
            <td>
              <img src="<?= e($p['primary_image_url'] ?? '/assets/images/placeholder.webp') ?>" alt="" width="40" height="40" style="object-fit: contain; border-radius: 4px; background: #eee;">
            </td>
            <td>
              <div style="font-weight: 600; line-height: 1.3;">
                <a href="/products/<?= e($p['slug']) ?>" target="_blank" title="View live page">
                  <?= e($p['title']) ?> ↗
                </a>
              </div>
              <div style="font-size: 0.75rem; color: #777; margin-top: 0.2rem;">
                SKU: <?= e($p['sku']) ?> &bull; Supplier ID: <code><?= e($p['supplier_id']) ?></code>
                <?php if (!empty($p['pinned'])): ?>
                  <span class="badge badge-warning" style="margin-left: 0.25rem;">Pinned</span>
                <?php endif; ?>
              </div>
            </td>
            <td style="font-size: 0.825rem;">
              <div><?= e($p['category_name'] ?? 'Uncategorized') ?></div>
              <div style="color: #777;"><?= e($p['brand_name'] ?? 'Generic') ?></div>
            </td>
            <td style="font-size: 0.85rem; color: #666;">
              <?= mta_money($p['supplier_price']) ?>
            </td>
            <td>
              <div style="font-weight: 700; color: #9B3828;">
                <?= mta_money($p['sell_price']) ?>
              </div>
              <?php if (!empty($p['price_override'])): ?>
                <span class="badge badge-danger" style="font-size: 0.65rem;">Manual Override</span>
              <?php endif; ?>
            </td>
            <td>
              <?php
                $stBadge = match($p['stock_status']) {
                  'in_stock' => 'badge-success',
                  'out_of_stock' => 'badge-danger',
                  default => 'badge-warning',
                };
              ?>
              <span class="badge <?= $stBadge ?>">
                <?= str_replace('_', ' ', $p['stock_status']) ?>
              </span>
            </td>
            <td>
              <?php
                $csBadge = match($p['content_state']) {
                  'approved' => 'badge-success',
                  'ai_draft' => 'badge-warning',
                  default => 'badge-neutral',
                };
              ?>
              <span class="badge <?= $csBadge ?>">
                <?= $p['content_state'] === 'approved' ? 'Approved' : ($p['content_state'] === 'ai_draft' ? 'AI Draft' : 'Raw') ?>
              </span>
            </td>
            <td>
              <!-- Quick Inline Override Form -->
              <form action="<?= e($adminPath) ?>/products/quick-override" method="POST" style="display: flex; align-items: center; gap: 0.35rem;">
                <?= mta_csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <input type="number" name="price_override" placeholder="Override" value="<?= $p['price_override'] ? (int)$p['price_override'] : '' ?>" style="width: 75px; padding: 0.25rem 0.35rem; font-size: 0.775rem; border: 1px solid #ccc; border-radius: 4px;">
                <input type="hidden" name="pinned" value="<?= $p['pinned'] ? 0 : 1 ?>">
                <button type="submit" class="btn btn-secondary btn-sm" title="Toggle Pin or Save Override" style="padding: 0.2rem 0.4rem; font-size: 0.75rem;">
                  <?= $p['pinned'] ? 'Unpin' : 'Pin' ?>
                </button>
              </form>
            </td>
            <td>
              <a href="<?= e($adminPath) ?>/products/<?= (int)$p['id'] ?>/edit" class="btn btn-secondary btn-sm">
                Edit
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="9" style="text-align: center; padding: 2rem; color: #777;">
            No products match the selected criteria.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <?php if ($pagination['total_pages'] > 1): ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; font-size: 0.85rem;">
      <div style="color: #666;">
        Showing page <?= $pagination['page'] ?> of <?= $pagination['total_pages'] ?> (<?= $pagination['total'] ?> total products)
      </div>
      <div style="display: flex; gap: 0.35rem;">
        <?php if ($pagination['page'] > 1): ?>
          <a href="<?= e($adminPath) ?>/products?page=<?= $pagination['page'] - 1 ?>&q=<?= urlencode($search) ?>&category_id=<?= $categoryId ?>&stock_status=<?= urlencode($stockStatus) ?>&content_state=<?= urlencode($contentState) ?>" class="btn btn-secondary btn-sm">&larr; Prev</a>
        <?php endif; ?>

        <?php if ($pagination['page'] < $pagination['total_pages']): ?>
          <a href="<?= e($adminPath) ?>/products?page=<?= $pagination['page'] + 1 ?>&q=<?= urlencode($search) ?>&category_id=<?= $categoryId ?>&stock_status=<?= urlencode($stockStatus) ?>&content_state=<?= urlencode($contentState) ?>" class="btn btn-secondary btn-sm">Next &rarr;</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

</div>
