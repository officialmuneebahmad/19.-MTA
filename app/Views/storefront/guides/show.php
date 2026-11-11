<?php
/**
 * Multan Tech Accessories — Editorial Buying Guide Detail View
 */
$topicMeta = $topic ?? mta_guide_topic((string)$guide['slug'], (string)$guide['title']);
$readMins = (int)($readingTime ?? mta_reading_time((string)($guide['content_html'] ?? '')));
$pubTs = strtotime((string)($guide['published_at'] ?? $guide['created_at'] ?? 'now'));
$updTs = strtotime((string)($guide['updated_at'] ?? $guide['published_at'] ?? $guide['created_at'] ?? 'now'));
?>
<div class="container" style="padding: 2rem 1.25rem; max-width: 850px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <!-- Article Header -->
  <header style="margin: 1.5rem 0 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
      <a href="/guides?topic=<?= e($topicMeta['slug']) ?>" style="display: inline-block; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--accent-strong); background: rgba(255, 98, 35, 0.1); padding: 0.25rem 0.65rem; border-radius: 4px; text-decoration: none;">
        <?= e($topicMeta['label']) ?>
      </a>
      <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Editorial Buying Guide &bull; <?= $readMins ?> min read</span>
    </div>

    <h1 style="font-size: 2.15rem; font-weight: 800; line-height: 1.25; margin: 0 0 1.25rem;">
      <?= e($guide['title']) ?>
    </h1>

    <?php if (!empty($guide['excerpt'])): ?>
      <p style="font-size: 1.05rem; color: var(--text-muted); line-height: 1.65; margin: 0 0 1.25rem;">
        <?= e($guide['excerpt']) ?>
      </p>
    <?php endif; ?>

    <!-- Author & Dates Metadata Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <?php if (!empty($guide['author_avatar'])): ?>
          <img src="<?= e($guide['author_avatar']) ?>" alt="<?= e($guide['author_name'] ?? 'Muneeb Ahmad') ?>" width="44" height="44" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-strong);">
        <?php endif; ?>
        <div style="font-size: 0.875rem; line-height: 1.4;">
          <div style="font-weight: 700;">
            Written by <a href="/author/<?= e($guide['author_slug'] ?? 'muneeb-ahmad') ?>" style="color: var(--accent-strong); text-decoration: underline;"><?= e($guide['author_name'] ?? 'Muneeb Ahmad') ?></a>
          </div>
          <div style="color: var(--text-muted); font-size: 0.8rem;">
            Founder &amp; Editor, Multan Tech Accessories
          </div>
        </div>
      </div>

      <div style="font-size: 0.82rem; color: var(--text-muted); text-align: right; line-height: 1.5;">
        <div><strong>Published:</strong> <time datetime="<?= e(date('Y-m-d', $pubTs)) ?>"><?= e(date('F j, Y', $pubTs)) ?></time></div>
        <div><strong>Updated:</strong> <time datetime="<?= e(date('Y-m-d', $updTs)) ?>"><?= e(date('F j, Y', $updTs)) ?></time></div>
      </div>
    </div>
  </header>

  <!-- Rich Long-Form Article Content -->
  <style>
    .guide-content {
      font-size: 1.05rem;
      line-height: 1.85;
      color: var(--text);
    }
    .guide-content p {
      margin-bottom: 1.35rem;
    }
    .guide-content h2 {
      font-size: 1.5rem;
      font-weight: 800;
      margin: 2.75rem 0 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid var(--border);
      color: var(--text);
      line-height: 1.3;
    }
    .guide-content h3 {
      font-size: 1.2rem;
      font-weight: 700;
      margin: 1.9rem 0 0.75rem;
      color: var(--text);
      line-height: 1.35;
    }
    .guide-content h4 {
      font-size: 1.05rem;
      font-weight: 700;
      margin: 1.25rem 0 0.5rem;
      color: var(--text);
    }
    .guide-content ul,
    .guide-content ol {
      margin: 0.75rem 0 1.6rem 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
    }
    .guide-content li {
      line-height: 1.75;
    }
    .guide-content a {
      color: var(--accent-strong);
      font-weight: 600;
      text-decoration: underline;
    }
    .guide-content figure {
      margin: 2.25rem 0;
      text-align: center;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      padding: 1rem;
    }
    .guide-content figure img {
      border-radius: var(--radius-sm);
      max-width: 100%;
      height: auto;
      margin: 0 auto;
    }
    .guide-content figcaption {
      font-size: 0.85rem;
      color: var(--text-muted);
      margin-top: 0.75rem;
      font-style: italic;
    }
    .guide-content .source-tag {
      font-size: 0.78rem;
      font-weight: 600;
      color: var(--text-muted);
      background: var(--surface-2);
      border: 1px solid var(--border);
      padding: 0.1rem 0.45rem;
      border-radius: 4px;
      text-decoration: none !important;
      margin-left: 0.25rem;
      vertical-align: middle;
    }
    .guide-content .source-tag:hover {
      border-color: var(--accent-strong);
      color: var(--accent-strong);
    }
  </style>

  <article class="guide-content">
    <?= $guide['content_html'] ?>
  </article>

  <!-- Store Category Cross-Link CTA (Only if Category Has Active Products) -->
  <?php if (!empty($showSuggestedCategory) && !empty($topicMeta['category_slug'])): ?>
    <aside style="margin-top: 2.5rem; background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.35rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
      <div>
        <h2 style="font-size: 1.15rem; font-weight: 800; margin: 0 0 0.25rem;">Browse <?= e($topicMeta['category_name']) ?> on MultanTech.pk</h2>
        <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0;">Compare current models, specifications, and prices with Cash on Delivery across Pakistan.</p>
      </div>
      <a href="/categories/<?= e($topicMeta['category_slug']) ?>" class="btn btn-primary btn-sm">Shop <?= e($topicMeta['category_name']) ?> &rarr;</a>
    </aside>
  <?php endif; ?>

  <!-- AdSense Slot 4 (Guide Bottom) -->
  <div style="margin: 2.5rem 0;">
    <?php $adPosition = 'guide_bottom'; require MTA_APP . '/Views/partials/ad-slot.php'; ?>
  </div>

  <!-- Editorial & Sourcing Note + Author Box -->
  <section aria-label="About the Author" style="margin-top: 2rem; background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; display: flex; align-items: flex-start; gap: 1.25rem; flex-wrap: wrap;">
    <?php if (!empty($guide['author_avatar'])): ?>
      <img src="<?= e($guide['author_avatar']) ?>" alt="<?= e($guide['author_name'] ?? 'Muneeb Ahmad') ?>" width="68" height="68" style="width: 68px; height: 68px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-strong); flex-shrink: 0;">
    <?php endif; ?>
    <div style="flex: 1; min-width: 240px;">
      <div style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: var(--accent-strong); margin-bottom: 0.25rem;">About the Author</div>
      <h2 style="font-size: 1.15rem; font-weight: 800; margin: 0 0 0.45rem;">
        <a href="/author/<?= e($guide['author_slug'] ?? 'muneeb-ahmad') ?>" style="color: var(--text); text-decoration: none;"><?= e($guide['author_name'] ?? 'Muneeb Ahmad') ?></a>
      </h2>
      <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.65; margin: 0 0 0.65rem;">
        <?= e($guide['author_bio'] ?? 'Founder and developer of Multan Tech Accessories.') ?>
      </p>
      <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0;">
        <em>Editorial Note: Technical comparisons in this guide reference published manufacturer standards and supplier specifications. Read our <a href="/editorial-and-pricing-policy" style="color: var(--accent-strong); text-decoration: underline;">Editorial &amp; Pricing Policy</a>.</em>
      </p>
    </div>
  </section>

  <!-- Related Buying Guides -->
  <?php if (!empty($relatedGuides)): ?>
    <section aria-labelledby="related-guides-heading" style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border);">
      <h2 id="related-guides-heading" style="font-size: 1.35rem; font-weight: 800; margin: 0 0 1.25rem;">Related Buying Guides</h2>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
        <?php foreach ($relatedGuides as $rg): ?>
          <?php $rgTopic = $rg['topic'] ?? mta_guide_topic((string)$rg['slug'], (string)$rg['title']); ?>
          <article style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; gap: 0.85rem;">
            <div>
              <span style="display: inline-block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--accent-strong); margin-bottom: 0.45rem;">
                <?= e($rgTopic['label']) ?>
              </span>
              <h3 style="font-size: 1rem; font-weight: 700; line-height: 1.4; margin: 0 0 0.5rem;">
                <a href="/guides/<?= e($rg['slug']) ?>" style="color: var(--text); text-decoration: none;"><?= e($rg['title']) ?></a>
              </h3>
              <p style="font-size: 0.84rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                <?= e(mb_strlen((string)$rg['excerpt']) > 120 ? mb_substr((string)$rg['excerpt'], 0, 117) . '...' : (string)$rg['excerpt']) ?>
              </p>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8rem; font-weight: 700; border-top: 1px solid var(--border); padding-top: 0.75rem;">
              <span style="color: var(--text-muted);"><?= (int)($rg['reading_time'] ?? 4) ?> min read</span>
              <a href="/guides/<?= e($rg['slug']) ?>" style="color: var(--accent-strong);">Read Guide &rarr;</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <div style="margin-top: 2.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <a href="/guides" class="btn btn-secondary">&larr; All Buying Guides</a>
    <a href="/categories" class="btn btn-primary">Explore Store Categories &rarr;</a>
  </div>
</div>
