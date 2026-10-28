<?php
/**
 * Multan Tech Accessories — Admin Sync View
 */
$adminPath = mta_admin_path();
?>
<!-- Status Card -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">Live Feed Health & Synchronization Control</div>
    <form action="<?= e($adminPath) ?>/sync/trigger" method="POST">
      <?= mta_csrf_field() ?>
      <button type="submit" class="btn btn-primary">
        ⚡ Run Synchronous Supplier Sync Now
      </button>
    </form>
  </div>

  <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
    <div style="background: #F9F9F7; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700;">Catalog Total</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #141413;"><?= $catalogCount ?></div>
      <div style="font-size: 0.75rem; color: #0E7064;">Target: 991 products</div>
    </div>

    <div style="background: #F9F9F7; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700;">Feed Last Received</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #0E7064;"><?= (int)($lastSync['products_received'] ?? 0) ?></div>
      <div style="font-size: 0.75rem; color: #777;">All records in single feed</div>
    </div>

    <div style="background: #F9F9F7; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700;">Last Run Duration</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #141413;"><?= number_format((float)($lastSync['duration_seconds'] ?? 0), 2) ?>s</div>
      <div style="font-size: 0.75rem; color: #777;">Upsert & hash verified</div>
    </div>

    <div style="background: #F9F9F7; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700;">50% Drop Guard</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #0E7064;">ACTIVE</div>
      <div style="font-size: 0.75rem; color: #777;">Safety lock against truncations</div>
    </div>
  </div>

  <?php if (!empty($lastSync['error_log'])): ?>
    <div style="background: #FDE8E8; border: 1px solid #F8B4B4; padding: 1rem; border-radius: 6px; font-size: 0.85rem; color: #9B1C1C; margin-top: 1rem;">
      <strong>Sync Notes / Warnings:</strong><br>
      <?= nl2br(e($lastSync['error_log'])) ?>
    </div>
  <?php endif; ?>
</div>

<!-- Historical Runs Table -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">Recent Sync Run History</div>
  </div>

  <table class="admin-table">
    <thead>
      <tr>
        <th>Run ID</th>
        <th>Start Time</th>
        <th>Status</th>
        <th>Received</th>
        <th>Inserted</th>
        <th>Updated</th>
        <th>Unchanged</th>
        <th>Skipped</th>
        <th>Deactivated</th>
        <th>Duration</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($runs)): ?>
        <?php foreach ($runs as $r): ?>
          <tr>
            <td><code>#<?= (int)$r['id'] ?></code></td>
            <td style="font-size: 0.825rem; color: #666;">
              <?= date('d M Y, h:i:s A', strtotime($r['started_at'])) ?>
            </td>
            <td>
              <span class="badge <?= $r['status'] === 'success' ? 'badge-success' : 'badge-danger' ?>">
                <?= e($r['status']) ?>
              </span>
            </td>
            <td><strong><?= (int)$r['products_received'] ?></strong></td>
            <td style="color: #0E7064;">+<?= (int)$r['products_inserted'] ?></td>
            <td style="color: #D97706;">&Delta; <?= (int)$r['products_updated'] ?></td>
            <td style="color: #666;"><?= (int)$r['products_unchanged'] ?></td>
            <td style="color: #999;"><?= (int)$r['products_skipped'] ?></td>
            <td style="color: #DC2626;"><?= (int)$r['products_deactivated'] ?></td>
            <td style="font-size: 0.825rem;"><?= number_format((float)$r['duration_seconds'], 2) ?>s</td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="10" style="text-align: center; padding: 2rem; color: #777;">
            No sync runs recorded yet. Click "Run Synchronous Supplier Sync Now" above to start the initial import.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
