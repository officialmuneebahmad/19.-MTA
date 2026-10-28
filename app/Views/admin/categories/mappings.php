<?php
/**
 * Multan Tech Accessories — Admin Supplier Mappings View
 */
$adminPath = mta_admin_path();
?>
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">Supplier Category &rarr; Site Category Mappings</div>
    <a href="<?= e($adminPath) ?>/categories" class="btn btn-secondary btn-sm">&larr; Back to Categories</a>
  </div>

  <p style="font-size: 0.875rem; color: #666; margin-bottom: 1.5rem; line-height: 1.5;">
    Whenever the supplier API synchronizes, incoming raw categories and subcategories are automatically mapped according to the rules below. You can override and remap any supplier category to any site category.
  </p>

  <table class="admin-table">
    <thead>
      <tr>
        <th>Raw Supplier Category</th>
        <th>Raw Supplier Subcategory</th>
        <th>Mapped Site Category</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($mappings)): ?>
        <?php foreach ($mappings as $map): ?>
          <tr>
            <td>
              <strong><?= e($map['supplier_category_title']) ?></strong>
              <div style="font-size: 0.75rem; color: #777;">ID: <code><?= e($map['supplier_category_id']) ?></code></div>
            </td>
            <td>
              <?php if (!empty($map['supplier_subcategory_title'])): ?>
                <?= e($map['supplier_subcategory_title']) ?>
                <div style="font-size: 0.75rem; color: #777;">ID: <code><?= e($map['supplier_subcategory_id']) ?></code></div>
              <?php else: ?>
                <span style="color: #999;">-- Category Level --</span>
              <?php endif; ?>
            </td>
            <td>
              <form action="<?= e($adminPath) ?>/categories/mappings" method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                <?= mta_csrf_field() ?>
                <input type="hidden" name="mapping_id" value="<?= (int)$map['id'] ?>">
                
                <select name="site_category_id" class="form-control" style="font-size: 0.85rem; padding: 0.35rem 0.5rem;">
                  <option value="">-- Unmapped --</option>
                  <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>" <?= (int)$map['site_category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                      <?= e($cat['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm">
                  Save
                </button>
              </form>
            </td>
            <td>
              <?php if (!empty($map['site_category_name'])): ?>
                <span class="badge badge-success"><?= e($map['site_category_name']) ?></span>
              <?php else: ?>
                <span class="badge badge-warning">Unmapped</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="4" style="text-align: center; padding: 2rem; color: #777;">
            No supplier mappings found yet. Run a supplier sync to discover categories automatically.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
