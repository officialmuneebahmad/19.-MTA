<?php
/**
 * Multan Tech Accessories — Category Products View
 */
$sort = $_GET['sort'] ?? 'popular';
$currentCatId = (int)($category['id'] ?? 0);
?>
<div class="container" style="padding-top: 1rem;">
  
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <!-- Category Title & Intro -->
  <div style="margin: 1.5rem 0 2rem;">
    <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0.75rem;"><?= e($category['name']) ?></h1>
    <?php if (!empty($category['intro_html'])): ?>
      <div style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; max-width: 800px;">
        <?= $category['intro_html'] ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Subcategory Chips -->
  <?php if (!empty($subcategories)): ?>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2rem;">
      <a href="/categories/<?= e($category['slug']) ?>" class="btn btn-sm <?= empty($_GET['sub']) ? 'btn-primary' : 'btn-secondary' ?>">
        All in <?= e($category['name']) ?>
      </a>
      <?php foreach ($subcategories as $sub): ?>
        <a href="/categories/<?= e($sub['slug']) ?>" class="btn btn-sm btn-secondary">
          <?= e($sub['name']) ?> (<?= (int)$sub['product_count'] ?>)
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Toolbar: Product Count & Sort Selector -->
  <div style="display: flex; align-items: center; justify-content: space-between; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 0.75rem 1.25rem; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div style="font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">
      Showing <strong><?= count($products) ?></strong> of <strong><?= (int)($pagination['total'] ?? count($products)) ?></strong> accessories
    </div>

    <form method="GET" action="" style="display: flex; align-items: center; gap: 0.5rem;">
      <label for="sort-select" style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">Sort by:</label>
      <select id="sort-select" name="sort" onchange="this.form.submit()" style="padding: 0.4rem 0.75rem; border-radius: 6px; border: 1px solid var(--border); font-size: 0.85rem; background: var(--surface-2); color: var(--text);">
        <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Most Popular</option>
        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
      </select>
    </form>
  </div>

  <!-- Product Grid -->
  <?php if (!empty($products)): ?>
    <div class="product-grid">
      <?php foreach ($products as $product): ?>
        <?php require MTA_APP . '/Views/partials/product-card.php'; ?>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php
      $baseUrl = '/categories/' . $category['slug'] . ($sort !== 'popular' ? '?sort=' . urlencode($sort) : '');
      require MTA_APP . '/Views/partials/pagination.php';
    ?>
  <?php else: ?>
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 3rem; text-align: center;">
      <p style="font-size: 1.1rem; color: var(--text-muted); margin-bottom: 1.5rem;">No accessories currently found in this category.</p>
      <a href="/categories" class="btn btn-primary">Browse All Categories</a>
    </div>
  <?php endif; ?>

  <!-- AdSense AdSlot (Controlled by Admin Settings) -->
  <?php require MTA_APP . '/Views/partials/ad-slot.php'; ?>

</div>
