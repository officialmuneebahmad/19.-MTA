<?php
/**
 * Multan Tech Accessories — Admin 301 Redirects View
 */
$adminPath = mta_admin_path();
?>
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">

  <!-- Redirects Table -->
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Active 301 Redirect Rules (<?= count($redirects) ?>)</div>
      <a href="<?= e($adminPath) ?>/seo-audit" class="btn btn-secondary btn-sm">&larr; SEO Audit</a>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Source Path</th>
          <th>Destination Target</th>
          <th>Hits</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($redirects)): ?>
          <?php foreach ($redirects as $r): ?>
            <tr>
              <td><code><?= e($r['source_url']) ?></code></td>
              <td><code><?= e($r['target_url']) ?></code></td>
              <td><strong><?= (int)$r['hits'] ?></strong></td>
              <td>
                <form action="<?= e($adminPath) ?>/seo-audit/redirects/<?= (int)$r['id'] ?>/delete" method="POST" onsubmit="return confirm('Delete redirect?');">
                  <?= mta_csrf_field() ?>
                  <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" style="text-align: center; padding: 2rem; color: #777;">
              No custom redirect rules configured yet.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Create Redirect Form -->
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Add 301 Redirect</div>
    </div>

    <form action="<?= e($adminPath) ?>/seo-audit/redirects" method="POST">
      <?= mta_csrf_field() ?>

      <div class="form-group">
        <label for="source_url">Old / Source Path *</label>
        <input type="text" id="source_url" name="source_url" required placeholder="/old-category/charger" class="form-control">
      </div>

      <div class="form-group">
        <label for="target_url">New / Target Destination *</label>
        <input type="text" id="target_url" name="target_url" required placeholder="/products/anker-65w-gan-charger" class="form-control">
      </div>

      <div class="form-group">
        <label for="status_code">HTTP Status Code</label>
        <select id="status_code" name="status_code" class="form-control">
          <option value="301" selected>301 (Moved Permanently - Recommended for SEO)</option>
          <option value="302">302 (Found / Temporary)</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%;">
        Save 301 Redirect Rule
      </button>
    </form>
  </div>

</div>
