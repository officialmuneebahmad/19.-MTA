<?php
/**
 * Multan Tech Accessories — Admin Brand Logos & Featured Brands View
 * @var array $brands
 * @var array $missingLogos
 */
$adminPath = mta_admin_path();
?>
<div class="admin-page-header">
  <div>
    <h2 class="admin-page-title">Brand Logos & Featured Brands</h2>
    <p class="admin-page-subtitle">Upload official brand logos, manage featured homepage brands, and inspect missing logos</p>
  </div>
  <div style="display:flex; gap:0.75rem;">
    <a href="/brands" target="_blank" class="btn btn-secondary" style="gap:6px;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
      <span>View Storefront Brands Directory</span>
    </a>
  </div>
</div>

<!-- Stats Row -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-bottom: 2rem;">
  <div class="admin-card" style="padding: 1.25rem;">
    <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--admin-text-muted);">Active Brands</div>
    <div style="font-size: 1.85rem; font-weight: 800; color: var(--admin-text); margin-top: 0.35rem;"><?= count($brands) ?></div>
  </div>
  <div class="admin-card" style="padding: 1.25rem;">
    <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--admin-text-muted);">Brands with Logos</div>
    <div style="font-size: 1.85rem; font-weight: 800; color: var(--admin-success); margin-top: 0.35rem;"><?= count($brands) - count($missingLogos) ?></div>
  </div>
  <div class="admin-card" style="padding: 1.25rem;">
    <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--admin-text-muted);">Missing Logos (with products)</div>
    <div style="font-size: 1.85rem; font-weight: 800; color: var(--mta-orange); margin-top: 0.35rem;"><?= count($missingLogos) ?></div>
  </div>
</div>

<!-- Bulk ZIP Upload Panel -->
<div class="admin-panel" style="margin-bottom: 2rem;">
  <div class="panel-header">
    <div class="panel-title-wrap">
      <h4 class="panel-title">Bulk Logo Import (ZIP Archive)</h4>
      <span class="panel-badge" style="background:var(--admin-surface-muted); color:var(--admin-text);">Task 3</span>
    </div>
  </div>
  <div style="padding: 1.5rem;">
    <p style="font-size: 0.875rem; color: var(--admin-text-muted); margin-bottom: 1.25rem;">
      Upload a ZIP archive containing SVG, PNG, or WebP logo files named by brand slug (e.g., <code>xiaomi.svg</code>, <code>aspor.png</code>, <code>baseus.webp</code>).
      SVGs are automatically sanitized; raster files are converted to crisp WebP at 2x max 400px.
    </p>
    <form action="<?= e($adminPath) ?>/brands/bulk-upload" method="POST" enctype="multipart/form-data" style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
      <?= mta_csrf_field() ?>
      <input type="file" name="zip_file" accept=".zip" required class="form-control" style="max-width:340px;">
      <button type="submit" class="btn btn-primary" style="gap:6px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
        <span>Import ZIP Logos</span>
      </button>
    </form>
  </div>
</div>

<!-- Missing Logos Priority Report -->
<?php if (!empty($missingLogos)): ?>
<div class="admin-panel" style="margin-bottom: 2rem; border-left: 4px solid var(--mta-orange);">
  <div class="panel-header">
    <div class="panel-title-wrap">
      <h4 class="panel-title">High Priority: Brands Missing Logos</h4>
      <span class="panel-badge" style="background:rgba(255,107,44,0.15); color:var(--mta-orange);"><?= count($missingLogos) ?> brands need logos</span>
    </div>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Brand Name</th>
          <th>Slug</th>
          <th>Active Catalog Items</th>
          <th>Expected File Name</th>
          <th style="text-align:right;">Upload Logo</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (array_slice($missingLogos, 0, 10) as $b): ?>
          <tr>
            <td><strong><?= e($b['name']) ?></strong></td>
            <td><code><?= e($b['slug']) ?></code></td>
            <td><span class="badge" style="background:var(--admin-surface-muted); color:var(--admin-text);"><?= (int)$b['product_count'] ?> products</span></td>
            <td><code><?= e($b['slug']) ?>.svg</code> or <code><?= e($b['slug']) ?>.webp</code></td>
            <td style="text-align:right;">
              <form action="<?= e($adminPath) ?>/brands/<?= (int)$b['id'] ?>" method="POST" enctype="multipart/form-data" style="display:inline-flex; align-items:center; gap:0.5rem;">
                <?= mta_csrf_field() ?>
                <input type="hidden" name="is_featured" value="<?= (int)$b['is_featured'] ?>">
                <input type="hidden" name="featured_sort" value="<?= (int)$b['featured_sort'] ?>">
                <input type="file" name="logo" accept=".svg,.png,.webp,.jpg,.jpeg" required style="font-size:0.8rem; max-width:200px;">
                <button type="submit" class="btn btn-sm btn-primary">Upload</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<!-- All Brands Directory Table -->
<div class="admin-panel">
  <div class="panel-header">
    <div class="panel-title-wrap">
      <h4 class="panel-title">All Brands Management</h4>
    </div>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 140px;">Current Logo</th>
          <th>Brand & Slug</th>
          <th>Products</th>
          <th>Featured</th>
          <th>Sort</th>
          <th>Update Logo / Alt Text</th>
          <th style="text-align:right;">Save</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($brands as $b): ?>
          <tr>
            <td>
              <div style="background:#FFFFFF; border:1px solid var(--admin-card-border); border-radius:6px; padding:6px 10px; width:120px; height:50px; display:flex; align-items:center; justify-content:center;">
                <?php if (!empty($b['logo_path'])): ?>
                  <img src="<?= e($b['logo_path']) ?>" alt="<?= e($b['name']) ?>" style="max-width:100%; max-height:40px; object-fit:contain;">
                <?php else: ?>
                  <span style="font-size:0.75rem; color:#94A3B8; font-weight:700;">No Logo</span>
                <?php endif; ?>
              </div>
            </td>
            <td>
              <div style="font-weight:700; color:var(--admin-text);"><?= e($b['name']) ?></div>
              <div style="font-size:0.8rem; color:var(--admin-text-muted);">/brands/<?= e($b['slug']) ?></div>
            </td>
            <td>
              <span class="badge" style="background:var(--admin-surface-muted); color:var(--admin-text);">
                <?= (int)$b['product_count'] ?> items
              </span>
            </td>
            <form action="<?= e($adminPath) ?>/brands/<?= (int)$b['id'] ?>" method="POST" enctype="multipart/form-data">
              <?= mta_csrf_field() ?>
              <td>
                <input type="checkbox" name="is_featured" value="1" <?= !empty($b['is_featured']) ? 'checked' : '' ?> style="transform:scale(1.2);">
              </td>
              <td>
                <input type="number" name="featured_sort" value="<?= (int)($b['featured_sort'] ?? 0) ?>" style="width:60px; padding:0.35rem; border:1px solid var(--admin-card-border); border-radius:4px; font-size:0.85rem;">
              </td>
              <td>
                <div style="display:flex; flex-direction:column; gap:0.4rem;">
                  <input type="file" name="logo" accept=".svg,.png,.webp,.jpg,.jpeg" style="font-size:0.8rem; max-width:240px;">
                  <input type="text" name="logo_alt" value="<?= e($b['logo_alt'] ?? ($b['name'] . ' logo')) ?>" placeholder="Alt text" style="padding:0.35rem 0.5rem; font-size:0.8rem; border:1px solid var(--admin-card-border); border-radius:4px; max-width:240px;">
                </div>
              </td>
              <td style="text-align:right;">
                <button type="submit" class="btn btn-sm btn-primary">Save</button>
              </td>
            </form>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

