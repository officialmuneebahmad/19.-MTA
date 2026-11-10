<?php
/**
 * Multan Tech Accessories — Accessible Numbered Pagination Component
 * @var array{current_page: int, last_page: int, total: int} $pagination
 * @var string $baseUrl
 */
$currentPage = (int)($pagination['current_page'] ?? 1);
$lastPage = (int)($pagination['last_page'] ?? 1);
if ($lastPage <= 1) return;

$baseUrl = $baseUrl ?? '';
$separator = str_contains($baseUrl, '?') ? '&' : '?';

// Compute page numbers to display: always show 1, last, and window around current
$pages = [];
if ($lastPage <= 7) {
    for ($i = 1; $i <= $lastPage; $i++) {
        $pages[] = $i;
    }
} else {
    $pages[] = 1;
    $start = max(2, $currentPage - 1);
    $end = min($lastPage - 1, $currentPage + 1);
    if ($start > 2) {
        $pages[] = '...';
    }
    for ($i = $start; $i <= $end; $i++) {
        $pages[] = $i;
    }
    if ($end < $lastPage - 1) {
        $pages[] = '...';
    }
    $pages[] = $lastPage;
}
?>
<nav aria-label="Pagination" style="margin: 3rem 0; display: flex; justify-content: center; gap: 0.4rem; align-items: center; flex-wrap: wrap;">
  <?php if ($currentPage > 1): ?>
    <a href="<?= e($baseUrl . ($currentPage - 1 === 1 ? '' : $separator . 'page=' . ($currentPage - 1))) ?>" class="btn btn-secondary btn-sm" aria-label="Previous Page">
      &larr; Prev
    </a>
  <?php endif; ?>

  <?php foreach ($pages as $p): ?>
    <?php if ($p === '...'): ?>
      <span style="padding: 0.35rem 0.5rem; color: var(--text-muted);">&hellip;</span>
    <?php elseif ($p === $currentPage): ?>
      <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 0.5rem; border-radius: var(--radius-sm, 6px); background: var(--accent-strong, #2e7d32); color: #fff; font-weight: 700; font-size: 0.875rem;" aria-current="page">
        <?= $p ?>
      </span>
    <?php else: ?>
      <a href="<?= e($baseUrl . ($p === 1 ? '' : $separator . 'page=' . $p)) ?>" style="display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 0.5rem; border-radius: var(--radius-sm, 6px); border: 1px solid var(--border); color: var(--text); font-weight: 600; font-size: 0.875rem; text-decoration: none;" aria-label="Page <?= $p ?>">
        <?= $p ?>
      </a>
    <?php endif; ?>
  <?php endforeach; ?>

  <?php if ($currentPage < $lastPage): ?>
    <a href="<?= e($baseUrl . $separator . 'page=' . ($currentPage + 1)) ?>" class="btn btn-secondary btn-sm" aria-label="Next Page">
      Next &rarr;
    </a>
  <?php endif; ?>
</nav>
