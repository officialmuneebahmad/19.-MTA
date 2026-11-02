<?php
/**
 * Multan Tech Accessories — Admin Sync View
 */
$adminPath = mta_admin_path();
?>
<!-- Status Card -->
<div class="admin-card">
  <div class="admin-card-header" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
      <div class="admin-card-title">Live Feed Health & Synchronization Control</div>
      <button type="button" onclick="window.location.reload();" class="btn btn-secondary btn-sm" title="Refresh Live Sync Status">
        🔄 Refresh
      </button>
    </div>
    <div class="sync-header-actions">
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
        <button type="submit" class="btn btn-secondary" style="background: #14B8A6; color: #fff; border-color: #0D9488;" onclick="return mtaConfirmTwice('This will clear any old products and import fresh products directly from Computer Zone API. Proceed?', '⚠️ FINAL CONFIRMATION: Are you sure you want to run a complete fresh sync?');" title="Purge dummy products and sync fresh from Computer Zone API">
          🧹 Clean & Fresh Sync (Computer Zone API)
        </button>
      </form>
    </div>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: var(--admin-surface-2, #343432); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--admin-card-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700;">Catalog Total</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: var(--admin-text); margin-top: 0.25rem;"><?= $catalogCount ?></div>
      <div style="font-size: 0.75rem; color: #0E7064; margin-top: 0.25rem;">Target: 991 products</div>
    </div>

    <div style="background: var(--admin-surface-2, #343432); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--admin-card-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700;">Feed Last Received</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #0E7064; margin-top: 0.25rem;"><?= (int)($lastSync['feed_count'] ?? $lastSync['products_received'] ?? 0) ?></div>
      <div style="font-size: 0.75rem; color: var(--admin-text-muted); margin-top: 0.25rem;">All records in single feed</div>
    </div>

    <div style="background: var(--admin-surface-2, #343432); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--admin-card-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700;">Last Run Status</div>
      <div style="font-size: 1.4rem; font-weight: 800; color: <?= ($lastSync['status'] ?? '') === 'success' ? '#0E7064' : '#DC2626' ?>; margin-top: 0.25rem;">
        <?= strtoupper((string)($lastSync['status'] ?? 'NONE')) ?>
      </div>
      <div style="font-size: 0.75rem; color: var(--admin-text-muted); margin-top: 0.25rem;"><?= !empty($lastSync['finished_at']) ? date('d M Y, h:i:s A', strtotime($lastSync['finished_at'])) . ' PKT' : 'Never completed' ?></div>
    </div>

    <div style="background: var(--admin-surface-2, #343432); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--admin-card-border);">
      <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700;">50% Drop Guard</div>
      <div style="font-size: 1.85rem; font-weight: 800; color: #0E7064; margin-top: 0.25rem;">ACTIVE</div>
      <div style="font-size: 0.75rem; color: var(--admin-text-muted); margin-top: 0.25rem;">Safety lock against truncations</div>
    </div>
  </div>

  <?php $errMsg = $lastSync['error_message'] ?? $lastSync['error_log'] ?? null; ?>
  <?php if (!empty($errMsg)): ?>
    <div style="background: var(--status-rose-bg); border: 1px solid var(--status-rose-border); padding: 1rem 1.25rem; border-radius: 8px; font-size: 0.85rem; color: var(--status-rose-text); margin-top: 1rem;">
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

  <div class="admin-table-responsive">
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
              <?= date('d M Y, h:i:s A', strtotime($r['started_at'])) ?> PKT
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
</div>
