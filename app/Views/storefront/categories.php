<?php
/**
 * Multan Tech Accessories — Categories Directory
 */
?>
<div class="container" style="padding-top: 1rem;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <div style="margin: 1.5rem 0 2rem;">
    <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0.5rem;">All Tech Categories</h1>
    <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6;">
      Explore our comprehensive catalog of verified computer hardware, mobile accessories, chargers, and gaming peripherals in Pakistan.
    </p>
  </div>

  <div class="category-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
    <?php foreach ($categories as $cat): ?>
      <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.75rem; display: flex; flex-direction: column;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
          <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(255, 98, 35, 0.1); color: var(--accent-strong); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <?= mta_category_icon($cat['slug']) ?>
          </div>
          <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">
            <a href="/categories/<?= e($cat['slug']) ?>" style="color: var(--accent-strong);"><?= e($cat['name']) ?></a>
          </h2>
        </div>
        <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1rem; flex: 1;">
          <?= e(strip_tags($cat['intro_html'] ?? '')) ?>
        </p>
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 1rem; font-size: 0.85rem; font-weight: 600;">
          <span style="color: var(--text-muted);"><?= (int)$cat['product_count'] ?> Products</span>
          <a href="/categories/<?= e($cat['slug']) ?>" style="color: var(--accent-strong);">Shop Now &rarr;</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
