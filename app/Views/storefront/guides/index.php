<?php
/**
 * Multan Tech Accessories — Editorial Buying Guides Hub
 */
?>
<div class="container" style="padding: 2rem 1.25rem; max-width: 1120px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <!-- Editorial Header -->
  <header style="margin: 1.5rem 0 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1.75rem;">
    <span style="display: inline-block; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--accent-strong); margin-bottom: 0.5rem;">
      Multan Tech Accessories &bull; Editorial Resource Center
    </span>
    <h1 class="page-title" style="font-size: 2.25rem; font-weight: 800; margin: 0 0 0.75rem; line-height: 1.2;">Tech Buying Guides &amp; Consumer Advice</h1>
    <p style="font-size: 1rem; color: var(--text-muted); max-width: 780px; line-height: 1.7; margin: 0 0 1.25rem;">
      Practical buying guides, compatibility checklists, charging-standard explainers, and consumer protection advice written for shoppers in Pakistan. Every article is prepared by founder <a href="/author/muneeb-ahmad" style="color: var(--accent-strong); font-weight: 600; text-decoration: underline;">Muneeb Ahmad</a> using published manufacturer standards and local market guidance.
    </p>

    <!-- Topic Filter Bar (Only Active Topics with Published Guides) -->
    <?php if (!empty($topics)): ?>
      <nav aria-label="Buying Guide Topics" style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1rem;">
        <a href="/guides" class="btn btn-sm <?= empty($activeTopicSlug) ? 'btn-primary' : 'btn-secondary' ?>">
          All Guides (<?= (int)($allGuidesCount ?? count($guides)) ?>)
        </a>
        <?php foreach ($topics as $t): ?>
          <a href="/guides?topic=<?= e($t['slug']) ?>" class="btn btn-sm <?= $activeTopicSlug === $t['slug'] ? 'btn-primary' : 'btn-secondary' ?>">
            <?= e($t['label']) ?> (<?= (int)$t['count'] ?>)
          </a>
        <?php endforeach; ?>
      </nav>
    <?php endif; ?>
  </header>

  <!-- Topic Overview Summary (Shown on Main Editorial Hub View) -->
  <?php if (empty($activeTopicSlug) && !empty($topics)): ?>
    <section aria-labelledby="topics-overview-heading" style="margin-bottom: 2.5rem;">
      <h2 id="topics-overview-heading" style="font-size: 1.35rem; font-weight: 800; margin-bottom: 1rem;">Browse Guides by Topic</h2>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1rem;">
        <?php foreach ($topics as $t): ?>
          <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.15rem; display: flex; flex-direction: column; justify-content: space-between; gap: 0.75rem;">
            <div>
              <h3 style="font-size: 1.02rem; font-weight: 700; margin: 0 0 0.35rem; color: var(--text);">
                <a href="/guides?topic=<?= e($t['slug']) ?>" style="color: inherit; text-decoration: none;"><?= e($t['label']) ?></a>
              </h3>
              <p style="font-size: 0.86rem; color: var(--text-muted); line-height: 1.55; margin: 0;">
                <?= e($t['description']) ?>
              </p>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.82rem; font-weight: 700; border-top: 1px solid var(--border); padding-top: 0.65rem;">
              <span style="color: var(--text-muted);"><?= (int)$t['count'] ?> <?= (int)$t['count'] === 1 ? 'Guide' : 'Guides' ?></span>
              <a href="/guides?topic=<?= e($t['slug']) ?>" style="color: var(--accent-strong);">Filter Topic &rarr;</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- Published Buying Guides Grid -->
  <section aria-labelledby="published-guides-heading">
    <div style="display: flex; align-items: baseline; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.25rem;">
      <h2 id="published-guides-heading" style="font-size: 1.35rem; font-weight: 800; margin: 0;">
        <?= !empty($activeTopicSlug) ? e($topics[array_search($activeTopicSlug, array_column($topics, 'slug'))]['label'] ?? 'Selected Topic') . ' Guides' : 'All Published Buying Guides' ?>
      </h2>
      <?php if (!empty($activeTopicSlug)): ?>
        <a href="/guides" style="font-size: 0.88rem; font-weight: 700; color: var(--accent-strong);">&larr; Show All Topics</a>
      <?php endif; ?>
    </div>

    <?php if (empty($guides)): ?>
      <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 3rem 1.5rem; text-align: center;">
        <p style="color: var(--text-muted); font-size: 1rem;">No buying guides are published for this topic yet. Check back soon.</p>
      </div>
    <?php else: ?>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 1.5rem;">
        <?php foreach ($guides as $g): ?>
          <?php
            $gTopic = $g['topic'] ?? mta_guide_topic((string)$g['slug'], (string)$g['title']);
            $gReadMins = (int)($g['reading_time'] ?? mta_reading_time((string)($g['content_html'] ?? '')));
            $gPubDate = !empty($g['published_at']) ? date('M j, Y', strtotime((string)$g['published_at'])) : 'Published';
          ?>
          <article style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; transition: border-color 0.2s, transform 0.2s;">
            <div>
              <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.85rem;">
                <a href="/guides?topic=<?= e($gTopic['slug']) ?>" style="display: inline-block; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--accent-strong); background: rgba(255, 98, 35, 0.1); padding: 0.28rem 0.65rem; border-radius: 4px; text-decoration: none;">
                  <?= e($gTopic['label']) ?>
                </a>
                <span style="font-size: 0.78rem; color: var(--text-muted);"><?= $gReadMins ?> min read</span>
              </div>

              <h3 style="font-size: 1.2rem; font-weight: 800; line-height: 1.38; margin: 0 0 0.85rem;">
                <a href="/guides/<?= e($g['slug']) ?>" style="color: var(--text); text-decoration: none;">
                  <?= e($g['title']) ?>
                </a>
              </h3>

              <p style="font-size: 0.92rem; color: var(--text-muted); line-height: 1.65; margin: 0 0 1.25rem;">
                <?= e($g['excerpt']) ?>
              </p>
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 1rem; margin-top: auto; display: flex; flex-direction: column; gap: 0.9rem;">
              <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; font-size: 0.82rem; color: var(--text-muted);">
                <a href="/author/<?= e($g['author_slug'] ?? 'muneeb-ahmad') ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--text); font-weight: 600; text-decoration: none;">
                  <?php if (!empty($g['author_avatar'])): ?>
                    <img src="<?= e($g['author_avatar']) ?>" alt="<?= e($g['author_name'] ?? 'Muneeb Ahmad') ?>" width="24" height="24" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                  <?php endif; ?>
                  <span><?= e($g['author_name'] ?? 'Muneeb Ahmad') ?></span>
                </a>
                <time datetime="<?= e(date('Y-m-d', strtotime((string)($g['published_at'] ?? 'now')))) ?>"><?= e($gPubDate) ?></time>
              </div>
              <a href="/guides/<?= e($g['slug']) ?>" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">
                Read Full Article &rarr;
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- Internal Linking Hub to Active Non-Empty Store Categories -->
  <?php if (!empty($storeCategories)): ?>
    <section aria-labelledby="shop-by-category-guides-heading" style="margin-top: 3.25rem; background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.75rem;">
      <h2 id="shop-by-category-guides-heading" style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.5rem;">Explore Related Hardware Categories</h2>
      <p style="font-size: 0.92rem; color: var(--text-muted); margin: 0 0 1.25rem; line-height: 1.6;">
        Compare specifications and browse available accessories in our store categories:
      </p>
      <div style="display: flex; flex-wrap: wrap; gap: 0.65rem;">
        <?php foreach ($storeCategories as $sc): ?>
          <a href="/categories/<?= e($sc['slug']) ?>" class="btn btn-secondary btn-sm">
            <?= e($sc['name']) ?> (<?= (int)$sc['product_count'] ?>)
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</div>
