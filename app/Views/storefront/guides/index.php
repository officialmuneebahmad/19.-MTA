<?php
/**
 * Multan Tech Accessories — Guides Listing View
 */
?>
<div class="container" style="padding: 2rem 1.25rem; max-width: 950px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <div style="margin: 1.5rem 0 2.5rem;">
    <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0.5rem;">Tech Buying Guides & Advice</h1>
    <p style="font-size: 1rem; color: var(--text-muted); line-height: 1.6;">
      Practical, honest hardware advice written for Pakistani tech enthusiasts, mobile users, and office professionals.
    </p>
  </div>

  <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;" class="guides-grid">
    <?php foreach ($guides as $guide): ?>
      <article style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; display: flex; flex-direction: column;">
        <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent-strong); text-transform: uppercase; margin-bottom: 0.5rem;">
          Buying Guide
        </span>
        <h2 style="font-size: 1.35rem; font-weight: 700; line-height: 1.35; margin-bottom: 0.75rem;">
          <a href="/guides/<?= e($guide['slug']) ?>"><?= e($guide['title']) ?></a>
        </h2>
        <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.25rem; flex: 1;">
          <?= e($guide['excerpt'] ?? substr(strip_tags($guide['content_html']), 0, 150) . '...') ?>
        </p>
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 1rem; font-size: 0.85rem; color: var(--text-muted);">
          <span>By Muneeb Ahmad</span>
          <a href="/guides/<?= e($guide['slug']) ?>" style="font-weight: 700; color: var(--accent-strong);">Read Full Guide &rarr;</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <!-- AdSlot on Guides -->
  <?php require MTA_APP . '/Views/partials/ad-slot.php'; ?>

</div>
