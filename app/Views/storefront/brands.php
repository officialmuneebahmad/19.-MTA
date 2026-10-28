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

  <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem;">
    <?php foreach ($brands as $brand): ?>
      <a href="/brands/<?= e($brand['slug']) ?>" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s ease;">
        <span style="font-size: 1.1rem; font-weight: 700; color: var(--text);"><?= e($brand['name']) ?></span>
        <span style="color: var(--accent-strong); font-weight: 600;">&rarr;</span>
      </a>
    <?php endforeach; ?>
  </div>
</div>
