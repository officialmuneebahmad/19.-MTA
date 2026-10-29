<?php
/**
 * Multan Tech Accessories — Breadcrumb Component
 * @var array<int, array{label: string, url: ?string}> $breadcrumbs
 */
if (empty($breadcrumbs)) return;
?>
<nav aria-label="Breadcrumb" style="margin: 1.25rem 0; font-size: 0.85rem; color: var(--text-muted);">
  <ol style="list-style: none; display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem;" itemscope itemtype="https://schema.org/BreadcrumbList">
    <?php foreach ($breadcrumbs as $index => $crumb): ?>
      <?php $label = $crumb['label'] ?? $crumb['title'] ?? 'Page'; ?>
      <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" style="display: inline-flex; align-items: center; gap: 0.5rem;">
        <?php if (!empty($crumb['url'])): ?>
          <a href="<?= e($crumb['url']) ?>" itemprop="item" style="color: var(--text-muted);">
            <span itemprop="name"><?= e($label) ?></span>
          </a>
        <?php else: ?>
          <span itemprop="name" style="color: var(--text); font-weight: 600;"><?= e($label) ?></span>
        <?php endif; ?>
        <meta itemprop="position" content="<?= $index + 1 ?>">
        <?php if ($index < count($breadcrumbs) - 1): ?>
          <span aria-hidden="true" style="color: var(--border-strong);">&rsaquo;</span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ol>
</nav>
