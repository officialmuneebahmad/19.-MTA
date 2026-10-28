<?php
/**
 * Multan Tech Accessories — Admin Import View
 */
$adminPath = mta_admin_path();
?>
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">

  <!-- 1. CSV Batch Import -->
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">1. Bulk CSV Ingestion</div>
      <span class="badge badge-info">Standard Format</span>
    </div>

    <p style="font-size: 0.875rem; color: #666; margin-bottom: 1.25rem; line-height: 1.5;">
      Upload a CSV file with columns: <code>title</code>, <code>price</code>, <code>sku</code> (optional), <code>image_url</code> (optional), <code>description</code> (optional).
    </p>

    <form action="<?= e($adminPath) ?>/import/csv" method="POST" enctype="multipart/form-data">
      <?= mta_csrf_field() ?>

      <div class="form-group">
        <label for="csv_file">Select CSV Spreadsheet *</label>
        <input type="file" id="csv_file" name="csv_file" accept=".csv" required class="form-control">
      </div>

      <div style="background: #F9F9F7; border: 1px dashed var(--admin-border); padding: 1rem; border-radius: 6px; margin-bottom: 1.25rem; font-size: 0.8rem; color: #555;">
        <strong>Sample Header Line:</strong><br>
        <code>title,price,sku,image_url,description</code>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%;">
        Upload & Process CSV Products &rarr;
      </button>
    </form>
  </div>

  <!-- 2. Webpage URL Importer with SSRF Shield -->
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">2. Web Page Importer</div>
      <span class="badge badge-success">SSRF Protected</span>
    </div>

    <p style="font-size: 0.875rem; color: #666; margin-bottom: 1.25rem; line-height: 1.5;">
      Import product specifications directly from an external supplier or retailer webpage. The built-in SSRF security filter blocks all RFC1918 private IP attacks.
    </p>

    <form action="<?= e($adminPath) ?>/import/url" method="POST">
      <?= mta_csrf_field() ?>

      <div class="form-group">
        <label for="target_url">Product Webpage URL *</label>
        <input type="url" id="target_url" name="target_url" required placeholder="https://example.com/product/..." class="form-control">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label for="fallback_price">Selling Price (PKR)</label>
          <input type="number" id="fallback_price" name="fallback_price" value="1500" class="form-control">
        </div>

        <div class="form-group">
          <label for="category_id">Target Category</label>
          <select id="category_id" name="category_id" class="form-control">
            <?php foreach ($categories as $cat): ?>
              <option value="<?= (int)$cat['id'] ?>"><?= e($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <button type="submit" class="btn btn-secondary" style="width: 100%; margin-top: 0.5rem;">
        Fetch & Create Product Draft &rarr;
      </button>
    </form>
  </div>

</div>
