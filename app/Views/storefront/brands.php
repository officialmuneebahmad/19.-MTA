<?php
/**
 * Multan Tech Accessories — Brands Directory
 */
?>
<div class="container" style="padding-top: 1rem;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <div style="margin: 1.5rem 0 2rem;">
    <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0.5rem;">Authentic Tech Brands</h1>
    <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6;">
      Shop verified computer hardware, mobile accessories, chargers, and gaming peripherals by leading brands.
    </p>
  </div>

  <div class="brands-grid" role="list">
    <?php foreach ($brands as $brand): ?>
      <?php 
        $logoCheck = mta_verify_brand_logo($brand['logo_path'] ?? null);
        if ($logoCheck['status'] !== 'ok') {
            continue; // Omit brands without verified logos; never show broken images or text tiles
        }
      ?>
      <a href="/brands/<?= e($brand['slug']) ?>" class="brand-tile" aria-label="<?= e($brand['name']) ?> products" role="listitem">
        <img src="<?= e($brand['logo_path']) ?>" alt="<?= e($brand['logo_alt'] ?: ($brand['name'] . ' logo')) ?>" class="brand-tile-logo" width="120" height="48" loading="lazy">
      </a>
    <?php endforeach; ?>
  </div>
</div>
