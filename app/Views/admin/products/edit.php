<?php
/**
 * Multan Tech Accessories — Admin Product Edit View
 */
$adminPath = mta_admin_path();
?>
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">Edit Product #<?= (int)$product['id'] ?></div>
    <div style="display: flex; gap: 0.5rem;">
      <a href="/products/<?= e($product['slug']) ?>" target="_blank" class="btn btn-secondary btn-sm">View Live Page ↗</a>
      <a href="<?= e($adminPath) ?>/products" class="btn btn-secondary btn-sm">&larr; Back to Catalog</a>
    </div>
  </div>

  <form action="<?= e($adminPath) ?>/products/<?= (int)$product['id'] ?>" method="POST">
    <?= mta_csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
      
      <!-- Left Column: Core Fields -->
      <div>
        <div class="form-group">
          <label for="title">Product Title *</label>
          <input type="text" id="title" name="title" required value="<?= e($product['title']) ?>" class="form-control">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group">
            <label for="category_id">Site Category</label>
            <select id="category_id" name="category_id" class="form-control">
              <option value="">-- None --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= (int)$product['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                  <?= e($cat['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="brand_id">Brand</label>
            <select id="brand_id" name="brand_id" class="form-control">
              <option value="">-- Generic / None --</option>
              <?php foreach ($brands as $brand): ?>
                <option value="<?= (int)$brand['id'] ?>" <?= (int)$product['brand_id'] === (int)$brand['id'] ? 'selected' : '' ?>>
                  <?= e($brand['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="description_html">Description (Sanitized HTML)</label>
          <textarea id="description_html" name="description_html" rows="10" class="form-control" style="font-family: monospace; font-size: 0.85rem;"><?= e($product['description_html']) ?></textarea>
        </div>

        <h3 style="font-size: 1rem; font-weight: 700; margin: 1.5rem 0 0.75rem; border-top: 1px solid var(--admin-border); padding-top: 1rem;">
          SEO Metadata Override
        </h3>

        <div class="form-group">
          <label for="meta_title">Custom Meta Title (&le; 60 chars)</label>
          <input type="text" id="meta_title" name="meta_title" maxlength="60" value="<?= e($product['meta_title'] ?? '') ?>" class="form-control" placeholder="Leave empty for auto-generated title">
        </div>

        <div class="form-group">
          <label for="meta_description">Custom Meta Description (140-160 chars)</label>
          <textarea id="meta_description" name="meta_description" rows="3" maxlength="255" class="form-control" placeholder="Leave empty for auto-generated description"><?= e($product['meta_description'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- Right Column: Pricing & Controls -->
      <div>
        <div style="background: #F9F9F7; border: 1px solid var(--admin-border); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem;">Pricing Controls</h3>
          
          <div class="form-group">
            <label>Supplier Cost Price</label>
            <input type="text" disabled value="<?= mta_money($product['supplier_price']) ?>" class="form-control" style="background: #eee;">
            <div class="form-help">Read-only from supplier feed. Never publicly exposed.</div>
          </div>

          <div class="form-group">
            <label for="sell_price">Calculated Selling Price (PKR)</label>
            <input type="number" id="sell_price" name="sell_price" step="1" value="<?= (int)$product['sell_price'] ?>" class="form-control">
          </div>

          <div class="form-group">
            <label for="price_override" style="color: #9B3828;">Manual Price Override (PKR)</label>
            <input type="number" id="price_override" name="price_override" step="1" value="<?= $product['price_override'] ? (int)$product['price_override'] : '' ?>" placeholder="e.g. 4999" class="form-control" style="border-color: #9B3828;">
            <div class="form-help">Sync will preserve this price until cleared.</div>
          </div>
        </div>

        <div style="background: #F9F9F7; border: 1px solid var(--admin-border); border-radius: 8px; padding: 1.25rem;">
          <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem;">Status & Publishing</h3>

          <div class="form-group">
            <label for="stock_status">Stock Availability</label>
            <select id="stock_status" name="stock_status" class="form-control">
              <option value="in_stock" <?= $product['stock_status'] === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
              <option value="out_of_stock" <?= $product['stock_status'] === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
              <option value="coming_soon" <?= $product['stock_status'] === 'coming_soon' ? 'selected' : '' ?>>Coming Soon</option>
            </select>
          </div>

          <div class="form-group">
            <label for="content_state">SEO Content Gate</label>
            <select id="content_state" name="content_state" class="form-control">
              <option value="approved" <?= $product['content_state'] === 'approved' ? 'selected' : '' ?>>Approved (Indexable & in Sitemap)</option>
              <option value="ai_draft" <?= $product['content_state'] === 'ai_draft' ? 'selected' : '' ?>>AI Draft (Needs Review)</option>
              <option value="supplier_raw" <?= $product['content_state'] === 'supplier_raw' ? 'selected' : '' ?>>Supplier Raw (Noindex)</option>
            </select>
          </div>

          <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
              <input type="checkbox" name="is_active" value="1" <?= !empty($product['is_active']) ? 'checked' : '' ?>>
              <span>Active in Store (unchecked = 410 Gone)</span>
            </label>
          </div>

          <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
              <input type="checkbox" name="pinned" value="1" <?= !empty($product['pinned']) ? 'checked' : '' ?>>
              <span>Pin to Homepage / Category Top</span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
            Save All Changes
          </button>
        </div>

      </div>

    </div>
  </form>
</div>
