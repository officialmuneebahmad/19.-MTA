<?php
/**
 * Multan Tech Accessories — Individual Brand View
 */
?>
<div class="container" style="padding-top: 1rem;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <div style="margin: 1.5rem 0 2rem;">
    <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0.5rem;"><?= e($brand['name']) ?> Accessories in Pakistan</h1>
    <?php if (!empty($brand['intro_html'])): ?>
      <div style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; max-width: 800px;">
        <?= $brand['intro_html'] ?>
      </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($products)): ?>
    <div class="product-grid">
      <?php foreach ($products as $product): ?>
        <?php require MTA_APP . '/Views/partials/product-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <?php
      $baseUrl = '/brands/' . $brand['slug'];
      require MTA_APP . '/Views/partials/pagination.php';
    ?>
  <?php else: ?>
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 3rem; text-align: center;">
      <p style="font-size: 1.1rem; color: var(--text-muted); margin-bottom: 1.5rem;">No products currently listed under <?= e($brand['name']) ?>.</p>
      <a href="/brands" class="btn btn-primary">Browse All Brands</a>
    </div>
  <?php endif; ?>
</div>
