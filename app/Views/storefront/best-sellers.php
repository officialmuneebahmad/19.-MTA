<?php
/**
 * Multan Tech Accessories — Best Sellers View
 */
?>
<div class="container" style="padding-top: 1rem;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <div style="margin: 1.5rem 0 2rem;">
    <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0.5rem;">Best Selling Tech Accessories</h1>
    <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6;">
      Our most in-demand products ranked by verified orders, customer inquiries, and daily demand across Pakistan.
    </p>
  </div>

  <div class="product-grid">
    <?php foreach ($products as $product): ?>
      <?php require MTA_APP . '/Views/partials/product-card.php'; ?>
    <?php endforeach; ?>
  </div>

  <?php
    $baseUrl = '/best-sellers';
    require MTA_APP . '/Views/partials/pagination.php';
  ?>
</div>
