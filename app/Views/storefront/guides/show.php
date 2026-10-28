<?php
/**
 * Multan Tech Accessories — Individual Guide View
 * Valid BlogPosting Schema, Author Byline & Internal Links
 */
$authorName = $author['name'] ?? 'Muneeb Ahmad';
$authorSlug = $author['slug'] ?? 'muneeb-ahmad';
?>
<div class="container" style="padding: 2rem 1.25rem; max-width: 820px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <article>
    <!-- Header -->
    <header style="margin: 1.5rem 0 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
      <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent-strong); text-transform: uppercase; letter-spacing: 0.5px;">
        Hardware Buying Guide
      </span>
      <h1 style="font-size: 2.25rem; font-weight: 800; line-height: 1.25; margin: 0.5rem 0 1rem;"><?= e($guide['title']) ?></h1>
      
      <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.875rem; color: var(--text-muted); flex-wrap: wrap;">
        <div>By <a href="/author/<?= e($authorSlug) ?>" style="color: var(--text); font-weight: 700;"><?= e($authorName) ?></a></div>
        <div>&bull;</div>
        <div>Published: <?= date('d M Y', strtotime($guide['published_at'] ?? $guide['created_at'])) ?></div>
        <div>&bull;</div>
        <div>Updated: <?= date('d M Y', strtotime($guide['updated_at'] ?? $guide['created_at'])) ?></div>
      </div>
    </header>

    <!-- Guide Body -->
    <div style="font-size: 1.05rem; line-height: 1.85; color: var(--text); margin-bottom: 3rem;" class="guide-content">
      <?= $guide['content_html'] ?>
    </div>

    <!-- AdSlot Placement -->
    <?php require MTA_APP . '/Views/partials/ad-slot.php'; ?>

    <!-- Author Box -->
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.75rem 2rem; display: flex; gap: 1.5rem; align-items: center; margin: 3rem 0;">
      <div style="width: 70px; height: 70px; border-radius: var(--radius-pill); background: var(--surface-2); border: 2px solid var(--accent-strong); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; color: var(--accent-strong); flex-shrink: 0;">
        MA
      </div>
      <div>
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;">Written by <?= e($authorName) ?></h3>
        <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 0.5rem;">
          Founder and developer of Multan Tech Accessories. Passionate about authentic computer and mobile accessories in Pakistan.
        </p>
        <a href="/author/<?= e($authorSlug) ?>" style="font-size: 0.825rem; font-weight: 700; color: var(--accent-strong);">View Author Bio & All Articles &rarr;</a>
      </div>
    </div>
  </article>
</div>
