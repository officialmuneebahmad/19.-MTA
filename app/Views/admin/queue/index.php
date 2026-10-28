<?php
/**
 * Multan Tech Accessories — Admin AI Content Queue View
 */
$adminPath = mta_admin_path();
?>
<!-- KPI Progress Bar -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">SEO Content Gate Progression</div>
    <div style="display: flex; gap: 0.5rem;">
      <form action="<?= e($adminPath) ?>/queue/bulk-enqueue" method="POST" style="display:inline;">
        <?= mta_csrf_field() ?>
        <button type="submit" class="btn btn-secondary btn-sm">⚡ Queue 200 Raw Products for AI</button>
      </form>
      <form action="<?= e($adminPath) ?>/queue/bulk-approve" method="POST" style="display:inline;">
        <?= mta_csrf_field() ?>
        <button type="submit" class="btn btn-primary btn-sm">✓ Approve All AI Drafts</button>
      </form>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; text-align: center;">
    <div style="background: #FAF9F6; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700;">1. Supplier Raw (Noindex)</div>
      <div style="font-size: 1.75rem; font-weight: 800; color: #666;"><?= (int)($stats['raw_count'] ?? 0) ?></div>
      <div style="font-size: 0.75rem; color: #888;">Supplier feeds pending rewrite</div>
    </div>

    <div style="background: #FEF9C3; padding: 1.25rem; border-radius: 8px; border: 1px solid #FDE047;">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: #854D0E; font-weight: 700;">2. AI Drafts (Pending Review)</div>
      <div style="font-size: 1.75rem; font-weight: 800; color: #D97706;"><?= (int)($stats['draft_count'] ?? 0) ?></div>
      <div style="font-size: 0.75rem; color: #854D0E;">Enhanced descriptions ready</div>
    </div>

    <div style="background: #E8F9EE; padding: 1.25rem; border-radius: 8px; border: 1px solid #B8EAD1;">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: #0E7064; font-weight: 700;">3. Approved (Live in Sitemap)</div>
      <div style="font-size: 1.75rem; font-weight: 800; color: #0E7064;"><?= (int)($stats['approved_count'] ?? 0) ?></div>
      <div style="font-size: 0.75rem; color: #0E7064;">Crawled & indexed by Google</div>
    </div>
  </div>
</div>

<!-- AI Drafts Awaiting Review -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">AI Drafts Awaiting Human Review (<?= count($drafts) ?>)</div>
  </div>

  <table class="admin-table">
    <thead>
      <tr>
        <th>Product Title & SKU</th>
        <th>Price</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($drafts)): ?>
        <?php foreach ($drafts as $d): ?>
          <tr>
            <td>
              <div style="font-weight: 600;">
                <a href="/products/<?= e($d['slug']) ?>" target="_blank"><?= e($d['title']) ?> ↗</a>
              </div>
              <div style="font-size: 0.75rem; color: #777;">SKU: <?= e($d['sku']) ?></div>
            </td>
            <td><?= mta_money($d['sell_price']) ?></td>
            <td><span class="badge badge-warning">AI Draft</span></td>
            <td style="display: flex; gap: 0.5rem;">
              <form action="<?= e($adminPath) ?>/queue/approve/<?= (int)$d['id'] ?>" method="POST">
                <?= mta_csrf_field() ?>
                <button type="submit" class="btn btn-success btn-sm">Approve & Index</button>
              </form>
              <a href="<?= e($adminPath) ?>/products/<?= (int)$d['id'] ?>/edit" class="btn btn-secondary btn-sm">Edit Content</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="4" style="text-align: center; padding: 2rem; color: #777;">
            No AI drafts awaiting review. Queue raw products above or run the background content worker.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
