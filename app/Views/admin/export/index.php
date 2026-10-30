<?php
/**
 * Multan Tech Accessories — Data Export Center View (Part B)
 * MultanTech.store
 */
$adminPath = mta_admin_path();
?>

<!-- =========================================================================
     1. Telemetry Overview
     ========================================================================= -->
<div class="kpi-grid" style="margin-bottom: 2rem;">
  
  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-label">Catalog Products</span>
      <div class="kpi-icon-box bg-orange-light text-orange">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
      </div>
    </div>
    <div class="kpi-value text-orange"><?= number_format($prodCount) ?></div>
    <div class="kpi-meta">
      <span class="badge badge-success">Full Inventory</span>
      <span class="kpi-subtext">All 991 Catalog Items</span>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-label">Customer Orders</span>
      <div class="kpi-icon-box bg-teal-light text-teal">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
      </div>
    </div>
    <div class="kpi-value text-teal"><?= number_format($orderCount) ?></div>
    <div class="kpi-meta">
      <span class="badge badge-teal">Invoices & COD</span>
      <span class="kpi-subtext">Order History</span>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-label">Unique Customers</span>
      <div class="kpi-icon-box bg-green-light text-green">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
      </div>
    </div>
    <div class="kpi-value text-green"><?= number_format($custCount) ?></div>
    <div class="kpi-meta">
      <span class="badge badge-success">Verified Buyers</span>
      <span class="kpi-subtext">Across Pakistan</span>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-label">WhatsApp Inquiries</span>
      <div class="kpi-icon-box bg-amber-light text-amber">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
      </div>
    </div>
    <div class="kpi-value"><?= number_format($inqCount) ?></div>
    <div class="kpi-meta">
      <span class="badge badge-amber">Click Logs</span>
      <span class="kpi-subtext">Direct Leads</span>
    </div>
  </div>

</div>

<!-- =========================================================================
     2. Export Configurator & Audit Log Trail
     ========================================================================= -->
<div class="dashboard-two-cols">
  
  <!-- Export Form Panel -->
  <div class="admin-panel">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">Generate Data Export</h4>
        <span class="panel-badge <?= $isReauth ? 'badge-success' : 'badge-amber' ?>">
          <?= $isReauth ? 'Re-Auth Active (15m)' : 'Verification Required' ?>
        </span>
      </div>
    </div>

    <form action="<?= e($adminPath) ?>/export/download" method="POST" style="padding: 1.5rem;">
      <?= mta_csrf_field() ?>

      <div class="form-group" style="margin-bottom: 1.25rem;">
        <label class="form-label">Dataset to Export *</label>
        <select name="dataset" class="form-control" required style="height: 44px; font-weight: 600;">
          <option value="products">Products Catalog (All 991 Items, Prices, Categories & Images)</option>
          <option value="orders">Customer Orders (Invoices, COD Amounts & Tracking Numbers)</option>
          <option value="customers">Customers Directory (Aggregated Spend & Order Counts)</option>
          <option value="inquiries">WhatsApp Inquiries Log (Timestamps, Products & IPs)</option>
        </select>
      </div>

      <div class="form-group" style="margin-bottom: 1.5rem;">
        <label class="form-label">Output File Format *</label>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.35rem;">
          <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.85rem 1rem; border: 1px solid var(--admin-card-border); border-radius: var(--radius-md); cursor: pointer; background: var(--admin-surface-muted);">
            <input type="radio" name="format" value="json" checked style="accent-color: var(--mta-orange);">
            <div>
              <div style="font-weight: 700; color: var(--admin-text);">JSON Format</div>
              <div style="font-size: 0.75rem; color: var(--admin-text-muted);">API & system backups</div>
            </div>
          </label>

          <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.85rem 1rem; border: 1px solid var(--admin-card-border); border-radius: var(--radius-md); cursor: pointer; background: var(--admin-surface-muted);">
            <input type="radio" name="format" value="csv" style="accent-color: var(--mta-orange);">
            <div>
              <div style="font-weight: 700; color: var(--admin-text);">CSV Spreadsheet</div>
              <div style="font-size: 0.75rem; color: var(--admin-text-muted);">Excel / Google Sheets</div>
            </div>
          </label>
        </div>
      </div>

      <div style="background: var(--admin-surface-muted); border: 1px solid var(--admin-card-border); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.5rem; font-size: 0.825rem; color: var(--admin-text-muted); line-height: 1.5;">
        <div style="display:flex; align-items:center; gap:0.5rem; font-weight:700; color:var(--admin-text); margin-bottom:0.25rem;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
          <span>Store Security Policy (Part B2)</span>
        </div>
        All data downloads are time-stamped, watermarked, and recorded in the permanent security audit trail with user identity, client IP, and row counts.
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; height: 48px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        <span>Authorize & Download Export File</span>
      </button>
    </form>
  </div>

  <!-- Audit Trail Panel -->
  <div class="admin-panel">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">Export Audit Trail</h4>
        <span class="panel-badge">Permanent Log</span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Timestamp</th>
            <th>Dataset</th>
            <th>Format & Details</th>
            <th>IP Address</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentLogs)): ?>
            <tr>
              <td colspan="4" style="text-align:center; padding:3rem 1rem; color:var(--admin-text-muted);">
                No previous data export events on record.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($recentLogs as $log): 
              $dt = !empty($log['details']) ? json_decode((string)$log['details'], true) : [];
            ?>
              <tr>
                <td>
                  <div style="font-weight:600; color:var(--admin-text); font-size:0.85rem;">
                    <?= date('d M Y', strtotime($log['created_at'])) ?>
                  </div>
                  <div style="font-size:0.75rem; color:var(--admin-text-muted);">
                    <?= date('h:i:s A', strtotime($log['created_at'])) ?>
                  </div>
                </td>
                <td>
                  <span class="badge badge-neutral"><?= e(strtoupper((string)$log['entity_type'] ?: 'DATASET')) ?></span>
                </td>
                <td>
                  <div style="font-size:0.85rem; color:var(--admin-text); font-weight:600;">
                    <?= strtoupper((string)($dt['format'] ?? 'JSON')) ?> &bull; <?= number_format((int)($dt['row_count'] ?? 0)) ?> rows
                  </div>
                  <div style="font-size:0.75rem; color:var(--admin-text-dim); font-family:monospace;">
                    <?= e($dt['filename'] ?? '') ?>
                  </div>
                </td>
                <td>
                  <code style="font-size:0.75rem; color:var(--admin-text-muted);"><?= e($log['ip_address'] ?? '127.0.0.1') ?></code>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

