<?php
/**
 * Multan Tech Accessories — Author Profile View
 */
?>
<div class="container" style="padding: 2rem 1.25rem; max-width: 900px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <!-- Author Bio Card -->
  <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; margin: 1.5rem 0 2.5rem; display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
    <?php if (!empty($author['avatar'])): ?>
      <img src="<?= e($author['avatar']) ?>" alt="<?= e($author['name']) ?>" width="110" height="110" style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent-strong);">
    <?php endif; ?>

    <div style="flex: 1; min-width: 250px;">
      <span style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--accent-strong);">Founder &amp; Author</span>
      <h1 class="page-title" style="font-size: 2rem; font-weight: 800; margin: 0.25rem 0 0.75rem;"><?= e($author['name']) ?></h1>
      <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.65; margin: 0;">
        <?= e($author['bio']) ?>
      </p>
    </div>
  </div>

  <!-- Articles by Author -->
  <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 1.25rem;">Published Articles by <?= e($author['name']) ?> (<?= count($guides) ?>)</h2>
  <?php if (empty($guides)): ?>
    <p style="color: var(--text-muted);">No published articles yet.</p>
  <?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
      <?php foreach ($guides as $g): ?>
        <?php $gTopic = $g['topic'] ?? mta_guide_topic((string)$g['slug'], (string)$g['title']); ?>
        <article style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.25rem;">
          <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem;">
            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--accent-strong);"><?= e($gTopic['label']) ?></span>
            <span style="font-size: 0.8rem; color: var(--text-muted);">
              <?= !empty($g['published_at']) ? e(date('M j, Y', strtotime((string)$g['published_at']))) : '' ?>
              &bull; <?= (int)($g['reading_time'] ?? 4) ?> min read
            </span>
          </div>
          <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 0.5rem;">
            <a href="/guides/<?= e($g['slug']) ?>" style="color: var(--text); text-decoration: none;"><?= e($g['title']) ?></a>
          </h3>
          <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0 0 0.85rem; line-height: 1.6;">
            <?= e($g['excerpt']) ?>
          </p>
          <a href="/guides/<?= e($g['slug']) ?>" style="font-size: 0.85rem; font-weight: 700; color: var(--accent-strong);">Read Full Article &rarr;</a>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
