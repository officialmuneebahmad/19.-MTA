<?php
/**
 * Multan Tech Accessories — Search Results View (noindex)
 */
$query = trim($_GET['q'] ?? '');
?>
<div class="container" style="padding-top: 1rem;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <div style="margin: 1.5rem 0 2rem;">
    <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;">
      Search Results for &ldquo;<?= e($query) ?>&rdquo;
    </h1>
    <p style="font-size: 0.95rem; color: var(--text-muted);">
      Found <strong><?= (int)($pagination['total'] ?? count($products)) ?></strong> accessories matching your search
    </p>
  </div>

  <?php if (!empty($products)): ?>
    <div class="product-grid">
      <?php foreach ($products as $product): ?>
        <?php require MTA_APP . '/Views/partials/product-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <?php
      $baseUrl = '/search?q=' . urlencode($query);
      require MTA_APP . '/Views/partials/pagination.php';
    ?>
  <?php else: ?>
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 3.5rem 2rem; text-align: center; max-width: 600px; margin: 0 auto;">
      <h2 style="font-size: 1.35rem; margin-bottom: 0.75rem;">No accessories found</h2>
      <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.75rem;">
        We could not find any accessories matching &ldquo;<?= e($query) ?>&rdquo;. Try searching for general keywords like &ldquo;charger&rdquo;, &ldquo;cable&rdquo;, &ldquo;speaker&rdquo;, or &ldquo;gaming&rdquo;.
      </p>
      <a href="/categories" class="btn btn-primary">Browse All Categories</a>
    </div>
  <?php endif; ?>
</div>
