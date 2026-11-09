<?php
/**
 * Multan Tech Accessories — Guides Listing View
 * Modern, card-based tech buying guides directory with prominent Read Article buttons
 */
?>
<div class="container" style="padding: 2.5rem 1.25rem 4rem; max-width: 1050px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <div style="margin: 1.5rem 0 2.5rem; text-align: left;">
    <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent-strong); text-transform: uppercase; letter-spacing: 0.5px;">
      Editorial &amp; Research
    </span>
    <h1 class="page-title" style="font-size: 2.25rem; font-weight: 800; margin: 0.35rem 0 0.5rem; line-height: 1.25;">
      Tech Buying Guides &amp; Advice
    </h1>
    <p style="font-size: 1rem; color: var(--text-muted); line-height: 1.6; max-width: 750px;">
      Field-tested hardware advice, local pricing benchmarks, and buyer guides written specifically for Pakistani consumers by founder and lead developer Muneeb Ahmad.
    </p>
  </div>

  <div class="guides-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
    <?php foreach ($guides as $guide): ?>
      <?php
        $featImg = !empty($guide['featured_image']) ? $guide['featured_image'] : '/assets/images/og-banner.jpg';
        $authorImg = '/assets/images/muneeb-ahmad.jpg';
      ?>
      <article class="guide-directory-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; display: flex; flex-direction: column; box-shadow: var(--shadow-sm); transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;">
        
        <!-- Featured Image Header -->
        <a href="/guides/<?= e($guide['slug']) ?>" style="display: block; overflow: hidden; position: relative; background: var(--surface-2); aspect-ratio: 16/9;" aria-label="Read <?= e($guide['title']) ?>">
          <img src="<?= e($featImg) ?>" alt="<?= e($guide['title']) ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
          <span style="position: absolute; top: 0.75rem; left: 0.75rem; background: var(--primary); color: #FFFFFF; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: var(--radius-sm); text-transform: uppercase; letter-spacing: 0.5px;">
            Buying Guide
          </span>
        </a>

        <!-- Card Body -->
        <div style="padding: 1.5rem; display: flex; flex-direction: column; flex: 1;">
          
          <!-- Author Strip -->
          <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.85rem; font-size: 0.825rem; color: var(--text-muted);">
            <img src="<?= e($authorImg) ?>" alt="Muneeb Ahmad" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1px solid var(--primary); flex-shrink: 0;">
            <span style="font-weight: 600; color: var(--text);">Muneeb Ahmad</span>
            <span>&bull;</span>
            <span><?= date('M d, Y', strtotime($guide['published_at'] ?? $guide['created_at'])) ?></span>
          </div>

          <!-- Guide Title -->
          <h2 style="font-size: 1.25rem; font-weight: 700; line-height: 1.35; margin: 0 0 0.75rem;">
            <a href="/guides/<?= e($guide['slug']) ?>" style="color: var(--text); text-decoration: none;">
              <?= e($guide['title']) ?>
            </a>
          </h2>

          <!-- Excerpt -->
          <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem; flex: 1;">
            <?= e($guide['excerpt'] ?? (substr(strip_tags($guide['content_html']), 0, 140) . '...')) ?>
          </p>

          <!-- Read Article Button (Explicit & Highly Visible) -->
          <div style="border-top: 1px solid var(--border); padding-top: 1.25rem; margin-top: auto;">
            <a href="/guides/<?= e($guide['slug']) ?>" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-weight: 700; padding: 0.75rem 1rem; border-radius: var(--radius-sm); text-decoration: none; box-shadow: var(--shadow-sm);">
              <span>Read Full Article</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </a>
          </div>

        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <!-- AdSlot on Guides -->
  <?php require MTA_APP . '/Views/partials/ad-slot.php'; ?>

</div>
