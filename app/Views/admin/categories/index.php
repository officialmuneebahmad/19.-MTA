<?php
/**
 * Multan Tech Accessories — Admin Categories View
 */
$adminPath = mta_admin_path();
?>
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">

  <!-- Categories Table -->
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Site Categories</div>
      <a href="<?= e($adminPath) ?>/categories/mappings" class="btn btn-secondary btn-sm">🔗 View Supplier Mappings</a>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Category Name</th>
          <th>Slug</th>
          <th>Parent</th>
          <th>Products</th>
          <th>Order</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
          <tr>
            <td style="font-weight: 600;">
              <?php if (!empty($cat['parent_id'])): ?>&nbsp;&nbsp;&mdash; <?php endif; ?>
              <a href="/categories/<?= e($cat['slug']) ?>" target="_blank">
                <?= e($cat['name']) ?> ↗
              </a>
            </td>
            <td><code><?= e($cat['slug']) ?></code></td>
            <td style="color: #666;"><?= e($cat['parent_name'] ?? 'Root') ?></td>
            <td><strong><?= (int)$cat['product_count'] ?></strong></td>
            <td><?= (int)$cat['sort_order'] ?></td>
            <td>
              <span class="badge <?= $cat['is_active'] ? 'badge-success' : 'badge-danger' ?>">
                <?= $cat['is_active'] ? 'Active' : 'Hidden' ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Create Category Form -->
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Add New Category</div>
    </div>

    <form action="<?= e($adminPath) ?>/categories" method="POST">
      <?= mta_csrf_field() ?>

      <div class="form-group">
        <label for="name">Category Name *</label>
        <input type="text" id="name" name="name" required class="form-control" placeholder="e.g. Wireless Microphones">
      </div>

      <div class="form-group">
        <label for="parent_id">Parent Category</label>
        <select id="parent_id" name="parent_id" class="form-control">
          <option value="">-- None (Top Level) --</option>
          <?php foreach ($parents as $p): ?>
            <option value="<?= (int)$p['id'] ?>"><?= e($p['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="sort_order">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" value="0" class="form-control">
      </div>

      <div class="form-group">
        <label for="intro_html">Introductory SEO Content</label>
        <textarea id="intro_html" name="intro_html" rows="4" class="form-control" placeholder="Brief guide or description displayed above product grid..."></textarea>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%;">
        Save New Category
      </button>
    </form>
  </div>

</div>
