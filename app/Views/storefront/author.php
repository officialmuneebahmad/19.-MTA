<?php
/**
 * Multan Tech Accessories — Author View for Muneeb Ahmad
 */
?>
<div class="container" style="padding: 2.5rem 1.25rem; max-width: 800px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2.5rem; display: flex; gap: 2rem; align-items: flex-start; margin-bottom: 2.5rem;" class="author-card">
    <div style="width: 100px; height: 100px; border-radius: var(--radius-pill); background: var(--surface-2); border: 2px solid var(--accent-strong); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; color: var(--accent-strong); flex-shrink: 0;">
      MA
    </div>

    <div>
      <span style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; color: var(--accent-strong); letter-spacing: 0.5px;">Founder & Full-Stack Developer</span>
      <h1 style="font-size: 2rem; font-weight: 800; margin: 0.25rem 0 0.75rem;">Muneeb Ahmad</h1>
      <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 1rem;">
        Founder, lead architect, and developer of <strong>Multan Tech Accessories</strong> (MultanTech.store). Based in Multan, Punjab, Pakistan, Muneeb is dedicated to making authentic computer hardware, fast charging gear, and durable mobile accessories accessible and transparent for Pakistani consumers.
      </p>
      <div style="font-size: 0.85rem; color: var(--text); line-height: 1.6;">
        <div><strong>Store Headquarters:</strong> Al-Quresh Phase 3, Sher Shah Road, Multan, Pakistan</div>
        <div><strong>Direct Contact / WhatsApp:</strong> <a href="tel:+923070655275" style="color: var(--accent-strong);">0307-0655275</a></div>
      </div>
    </div>
  </div>

  <section>
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.25rem;">Articles & Buying Guides by Muneeb Ahmad</h2>
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <?php if (!empty($authorGuides)): ?>
        <?php foreach ($authorGuides as $guide): ?>
          <article style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
              <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;">
                <a href="/guides/<?= e($guide['slug']) ?>"><?= e($guide['title']) ?></a>
              </h3>
              <p style="font-size: 0.85rem; color: var(--text-muted);"><?= e($guide['excerpt'] ?? '') ?></p>
            </div>
            <a href="/guides/<?= e($guide['slug']) ?>" class="btn btn-secondary btn-sm" style="flex-shrink: 0; margin-left: 1rem;">Read &rarr;</a>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="color: var(--text-muted);">Check our <a href="/guides" style="color: var(--accent-strong); text-decoration: underline;">Guides Section</a> for practical tech buying advice.</p>
      <?php endif; ?>
    </div>
  </section>

</div>
