<?php
/**
 * Multan Tech Accessories — Admin SEO Audit Dashboard View
 */
$adminPath = mta_admin_path();
?>
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem;">

  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      Uncategorized Items
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: <?= $uncategorizedCount > 0 ? '#DC2626' : '#0E7064' ?>;">
      <?= $uncategorizedCount ?>
    </div>
    <div style="font-size: 0.75rem; color: #777;">Products missing site category</div>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      Missing Descriptions
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: <?= $missingDescCount > 0 ? '#D97706' : '#0E7064' ?>;">
      <?= $missingDescCount ?>
    </div>
    <div style="font-size: 0.75rem; color: #777;">Thin content (&lt; 40 chars)</div>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      301 Redirect Rules
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: #0369A1;">
      <?= $redirectsCount ?>
    </div>
    <div style="font-size: 0.75rem; color: #777;">
      <a href="<?= e($adminPath) ?>/seo-audit/redirects" style="text-decoration: underline;">Manage 301 rules &rarr;</a>
    </div>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      Logged 404 Errors
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: <?= $top404Count > 0 ? '#DC2626' : '#0E7064' ?>;">
      <?= $top404Count ?>
    </div>
    <div style="font-size: 0.75rem; color: #777;">
      <a href="<?= e($adminPath) ?>/seo-audit/log-404" style="text-decoration: underline;">Inspect 404 log &rarr;</a>
    </div>
  </div>

</div>

<!-- Uncategorized Products Table -->
<?php if (!empty($uncategorizedProducts)): ?>
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Products Needing Category Assignment</div>
      <a href="<?= e($adminPath) ?>/categories/mappings" class="btn btn-secondary btn-sm">Auto-Map via Supplier Categories &rarr;</a>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>SKU</th>
          <th>Price</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($uncategorizedProducts as $p): ?>
          <tr>
            <td style="font-weight: 600;"><?= e($p['title']) ?></td>
            <td><code><?= e($p['sku']) ?></code></td>
            <td><?= mta_money($p['sell_price']) ?></td>
            <td>
              <a href="<?= e($adminPath) ?>/products/<?= (int)$p['id'] ?>/edit" class="btn btn-secondary btn-sm">
                Assign Category &rarr;
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
