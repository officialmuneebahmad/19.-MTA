<?php
/**
 * Multan Tech Accessories — Individual Guide View
 * Valid BlogPosting Schema, Author Byline with Real Photo & Responsive Typography Spacing
 */
$authorName = $author['name'] ?? 'Muneeb Ahmad';
$authorSlug = $author['slug'] ?? 'muneeb-ahmad';
$authorImg = '/assets/images/muneeb-ahmad.jpg';
?>
<div class="container" style="padding: 2rem 1.25rem 4rem; max-width: 860px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <article>
    <!-- Header -->
    <header style="margin: 1.5rem 0 2.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1.75rem;">
      <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent-strong); text-transform: uppercase; letter-spacing: 0.5px;">
        Hardware Buying Guide &bull; Verified Research
      </span>
      <h1 style="font-size: 2.25rem; font-weight: 800; line-height: 1.25; margin: 0.65rem 0 1.25rem; color: var(--text);">
        <?= e($guide['title']) ?>
      </h1>
      
      <!-- Author Byline with Real Profile Photo -->
      <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.9rem; color: var(--text-muted); flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
          <img src="<?= e($authorImg) ?>" alt="<?= e($authorName) ?>" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); box-shadow: var(--shadow-sm); flex-shrink: 0;">
          <div>
            <div style="font-weight: 700; color: var(--text);">
              <a href="/author/<?= e($authorSlug) ?>" style="color: inherit; text-decoration: none;"><?= e($authorName) ?></a>
            </div>
            <div style="font-size: 0.775rem; color: var(--text-muted);">Founder &amp; Lead Hardware Developer</div>
          </div>
        </div>
        <div>&bull;</div>
        <div>Published: <?= date('d M Y', strtotime($guide['published_at'] ?? $guide['created_at'])) ?></div>
        <div>&bull;</div>
        <div>Updated: <?= date('d M Y', strtotime($guide['updated_at'] ?? $guide['created_at'])) ?></div>
      </div>
    </header>

    <!-- Custom Typography & Topic Spacing Stylesheet -->
    <style>
      .guide-content {
        font-size: 1.05rem;
        line-height: 1.9;
        color: var(--text);
      }
      .guide-content h2 {
        font-size: 1.65rem;
        font-weight: 800;
        line-height: 1.35;
        margin-top: 3.25rem;
        margin-bottom: 1.25rem;
        color: var(--text);
        border-bottom: 2px solid var(--border);
        padding-bottom: 0.65rem;
      }
      .guide-content h3 {
        font-size: 1.3rem;
        font-weight: 700;
        line-height: 1.4;
        margin-top: 2.35rem;
        margin-bottom: 0.85rem;
        color: var(--text);
      }
      .guide-content p {
        margin-bottom: 1.5rem;
        color: var(--text);
      }
      .guide-content ul, .guide-content ol {
        margin-bottom: 1.75rem;
        padding-left: 1.75rem;
      }
      .guide-content li {
        margin-bottom: 0.65rem;
        line-height: 1.8;
      }
      .guide-content figure {
        margin: 2.75rem 0;
        text-align: center;
      }
      .guide-content figure img {
        width: 100%;
        max-height: 480px;
        object-fit: cover;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        display: block;
      }
      .guide-content figcaption {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-top: 0.75rem;
        line-height: 1.5;
        font-style: italic;
      }
      .guide-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 2.25rem 0;
        font-size: 0.95rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
      }
      .guide-content th {
        background: var(--surface-2);
        color: var(--text);
        font-weight: 700;
        text-align: left;
        padding: 0.85rem 1.15rem;
        border-bottom: 2px solid var(--border);
      }
      .guide-content td {
        padding: 0.85rem 1.15rem;
        border-bottom: 1px solid var(--border);
        color: var(--text);
        line-height: 1.6;
      }
      .guide-content tr:last-child td {
        border-bottom: none;
      }
      .guide-content tr:hover td {
        background: var(--surface-2);
      }
      .guide-content a {
        color: var(--primary);
        text-decoration: underline;
        text-underline-offset: 3px;
        transition: color 0.15s ease;
      }
      .guide-content a:hover {
        color: var(--primary-hover, var(--accent-strong));
      }
      .guide-content .source-tag {
        font-size: 0.775rem;
        font-weight: 600;
        padding: 0.15rem 0.45rem;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 4px;
        text-decoration: none !important;
        vertical-align: baseline;
        position: relative;
        top: -1px;
        margin-left: 0.25rem;
        color: var(--text-muted) !important;
      }
      .guide-content .source-tag:hover {
        border-color: var(--primary);
        color: var(--primary) !important;
      }
      @media (max-width: 640px) {
        .guide-content h2 {
          font-size: 1.4rem;
          margin-top: 2.5rem;
        }
        .guide-content h3 {
          font-size: 1.15rem;
          margin-top: 1.85rem;
        }
      }
    </style>

    <!-- Guide Body -->
    <div class="guide-content" style="margin-bottom: 3.5rem;">
      <?= $guide['content_html'] ?>
    </div>

    <!-- AdSlot Placement -->
    <?php require MTA_APP . '/Views/partials/ad-slot.php'; ?>

    <!-- Author Box with Uploaded Photo -->
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; display: flex; gap: 1.75rem; align-items: center; margin: 3.5rem 0; box-shadow: var(--shadow-sm); flex-wrap: wrap;">
      <img src="<?= e($authorImg) ?>" alt="<?= e($authorName) ?>" style="width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 2.5px solid var(--primary); box-shadow: var(--shadow-sm); flex-shrink: 0;">
      <div style="flex: 1; min-width: 250px;">
        <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--accent-strong); letter-spacing: 0.5px;">About The Author</span>
        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0.2rem 0 0.4rem; color: var(--text);">Written by <?= e($authorName) ?></h3>
        <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 0.75rem;">
          Founder, lead architect, and developer of Multan Tech Accessories. Passionate about authentic computer hardware, fast charging technologies, and empowering Pakistani consumers with transparent technical knowledge.
        </p>
        <a href="/author/<?= e($authorSlug) ?>" style="font-size: 0.85rem; font-weight: 700; color: var(--accent-strong); text-decoration: none;">View Author Profile &amp; All Articles &rarr;</a>
      </div>
    </div>
  </article>
</div>
