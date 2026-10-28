<?php
/**
 * Multan Tech Accessories — Accessible Pagination Component
 * @var array{current_page: int, last_page: int, total: int} $pagination
 * @var string $baseUrl
 */
$currentPage = (int)($pagination['current_page'] ?? 1);
$lastPage = (int)($pagination['last_page'] ?? 1);
if ($lastPage <= 1) return;

$baseUrl = $baseUrl ?? '';
$separator = str_contains($baseUrl, '?') ? '&' : '?';
?>
<nav aria-label="Pagination" style="margin: 3rem 0; display: flex; justify-content: center; gap: 0.5rem; align-items: center;">
  <?php if ($currentPage > 1): ?>
    <a href="<?= e($baseUrl . $separator . 'page=' . ($currentPage - 1)) ?>" class="btn btn-secondary btn-sm" aria-label="Previous Page">
      &larr; Previous
    </a>
  <?php endif; ?>

  <div style="font-size: 0.875rem; font-weight: 600; padding: 0 0.75rem; color: var(--text-muted);">
    Page <?= $currentPage ?> of <?= $lastPage ?>
  </div>

  <?php if ($currentPage < $lastPage): ?>
    <a href="<?= e($baseUrl . $separator . 'page=' . ($currentPage + 1)) ?>" class="btn btn-secondary btn-sm" aria-label="Next Page">
      Next &rarr;
    </a>
  <?php endif; ?>
</nav>
