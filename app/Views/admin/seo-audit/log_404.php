<?php
/**
 * Multan Tech Accessories — Admin 404 Crawl Error Log View
 */
$adminPath = mta_admin_path();
?>
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">404 Error Log (<?= count($logs) ?> entries)</div>
    <div style="display: flex; gap: 0.5rem;">
      <form action="<?= e($adminPath) ?>/seo-audit/log-404/clear" method="POST" onsubmit="return confirm('Clear 404 error log?');">
        <?= mta_csrf_field() ?>
        <button type="submit" class="btn btn-secondary btn-sm">Clear Log</button>
      </form>
      <a href="<?= e($adminPath) ?>/seo-audit" class="btn btn-secondary btn-sm">&larr; SEO Audit</a>
    </div>
  </div>

  <table class="admin-table">
    <thead>
      <tr>
        <th>Missing URL</th>
        <th>Referrer</th>
        <th>Hits</th>
        <th>Last Seen</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($logs)): ?>
        <?php foreach ($logs as $l): ?>
          <tr>
            <td><code><?= e($l['url']) ?></code></td>
            <td style="font-size: 0.8rem; color: #777; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
              <?= e($l['referrer'] ?? 'Direct / Bot') ?>
            </td>
            <td><strong><?= (int)$l['hits'] ?></strong></td>
            <td style="color: #777; font-size: 0.8rem;">
              <?= date('d M Y, h:i A', strtotime($l['last_seen_at'])) ?>
            </td>
            <td>
              <a href="<?= e($adminPath) ?>/seo-audit/redirects?source=<?= urlencode($l['url']) ?>" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                Create 301 Redirect &rarr;
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" style="text-align: center; padding: 2rem; color: #777;">
            No 404 errors recorded. All site URLs are resolving cleanly!
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
