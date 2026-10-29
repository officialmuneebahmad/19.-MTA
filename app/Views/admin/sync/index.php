<?php
/**
 * Multan Tech Accessories — Admin Sync View
 */
$adminPath = mta_admin_path();
?>
<!-- Status Card -->
<div class="admin-card">
  <div class="admin-card-header" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
    <div class="admin-card-title">Live Feed Health & Synchronization Control</div>
    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
      <form action="<?= e($adminPath) ?>/sync/test" method="POST" style="margin: 0;">
        <?= mta_csrf_field() ?>
        <button type="submit" class="btn btn-secondary" title="Test Computer Zone API handshake and outbound IP">
          🔍 Test API Connection
        </button>
      </form>
      <form action="<?= e($adminPath) ?>/sync/trigger" method="POST" style="margin: 0;">
        <?= mta_csrf_field() ?>
        <button type="submit" class="btn btn-primary" title="Fetch live product updates from Computer Zone API">
          ⚡ Run Live Supplier Sync Now
        </button>
      </form>
      <form action="<?= e($adminPath) ?>/sync/seed-backup" method="POST" style="margin: 0;">
        <?= mta_csrf_field() ?>
        <button type="submit" class="btn btn-secondary" style="background: #14B8A6; color: #fff; border-color: #0D9488;" title="Import all 991 verified products instantly into your store">
          📥 Populate Store (991 Products Feed)
        </button>
      </form>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
    <div style="background: #F9F9F7; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700;">Catalog Total</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #141413;"><?= $catalogCount ?></div>
      <div style="font-size: 0.75rem; color: #0E7064;">Target: 991 products</div>
    </div>

    <div style="background: #F9F9F7; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700;">Feed Last Received</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #0E7064;"><?= (int)($lastSync['feed_count'] ?? $lastSync['products_received'] ?? 0) ?></div>
      <div style="font-size: 0.75rem; color: #777;">All records in single feed</div>
    </div>

    <div style="background: #F9F9F7; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700;">Last Run Status</div>
      <div style="font-size: 1.4rem; font-weight: 800; color: <?= ($lastSync['status'] ?? '') === 'success' ? '#0E7064' : '#DC2626' ?>;">
        <?= strtoupper((string)($lastSync['status'] ?? 'NONE')) ?>
      </div>
      <div style="font-size: 0.75rem; color: #777;"><?= !empty($lastSync['finished_at']) ? e($lastSync['finished_at']) : 'Never completed' ?></div>
    </div>

    <div style="background: #F9F9F7; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700;">50% Drop Guard</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #0E7064;">ACTIVE</div>
      <div style="font-size: 0.75rem; color: #777;">Safety lock against truncations</div>
    </div>
  </div>

  <?php $errMsg = $lastSync['error_message'] ?? $lastSync['error_log'] ?? null; ?>
  <?php if (!empty($errMsg)): ?>
    <div style="background: #FDE8E8; border: 1px solid #F8B4B4; padding: 1rem; border-radius: 6px; font-size: 0.85rem; color: #9B1C1C; margin-top: 1rem;">
      <strong>Last Sync Error / Warning:</strong><br>
      <?= nl2br(e($errMsg)) ?>
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
          <?php 
            $dur = (!empty($r['finished_at']) && !empty($r['started_at'])) 
              ? max(0, strtotime($r['finished_at']) - strtotime($r['started_at'])) 
              : 0;
          ?>
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
            <td><strong><?= (int)($r['feed_count'] ?? $r['products_received'] ?? 0) ?></strong></td>
            <td style="color: #0E7064;">+<?= (int)($r['imported_count'] ?? $r['products_inserted'] ?? 0) ?></td>
            <td style="color: #D97706;">&Delta; <?= (int)($r['updated_count'] ?? $r['products_updated'] ?? 0) ?></td>
            <td style="color: #666;"><?= (int)($r['unchanged_count'] ?? $r['products_unchanged'] ?? 0) ?></td>
            <td style="color: #999;"><?= (int)($r['skipped_invalid'] ?? $r['products_skipped'] ?? 0) ?></td>
            <td style="color: #DC2626;"><?= (int)($r['deactivated_count'] ?? $r['products_deactivated'] ?? 0) ?></td>
            <td style="font-size: 0.825rem;"><?= $dur ?>s</td>
          </tr>
          <?php if (!empty($r['error_message'])): ?>
            <tr>
              <td colspan="10" style="background: #FFF5F5; color: #C53030; font-size: 0.8rem; padding: 0.5rem 1rem;">
                <strong>Error:</strong> <?= e($r['error_message']) ?>
              </td>
            </tr>
          <?php endif; ?>
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
