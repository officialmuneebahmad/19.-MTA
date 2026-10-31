<?php
/**
 * Multan Tech Accessories — Master Admin Operations Dashboard View
 * MultanTech.store
 */
$adminPath = mta_admin_path();
?>

<!-- =========================================================================
     1. Hero Gradient Command Card (Single greeting with DB first name)
     ========================================================================= -->
<div class="hero-command-card">
  <div class="hero-badge">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
    <span>MTA Operations & Command Center</span>
  </div>
  
  <h2 class="hero-title">Welcome back, <?= e($firstName ?? 'Muneeb') ?>!</h2>
  
  <p class="hero-desc">
    Catalog inventory, Pakistan COD logistics pipeline, and high-speed automated supplier synchronization overview.
  </p>

  <div class="hero-actions">
    <a href="<?= e($adminPath) ?>/orders" class="btn-hero-white">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
      <span>Manage Orders (<?= (int)$pendingApprovals ?> Pending)</span>
    </a>

    <a href="<?= e($adminPath) ?>/import" class="btn-hero-glass">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add Product</span>
    </a>

    <a href="<?= e($adminPath) ?>/export" class="btn-hero-glass">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
      <span>Backup</span>
    </a>
  </div>
</div>

<!-- =========================================================================
     2. 4 Primary KPI Cards with Date Range Selector
     ========================================================================= -->
<div class="kpi-grid">
  
  <!-- KPI 1: Gross Sales with Date Range Selector -->
  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-label">Gross Sales</span>
      <div class="kpi-range-pills" role="tablist" aria-label="Sales Range">
        <button type="button" class="kpi-range-btn <?= $activeRange === 'today' ? 'active' : '' ?>" data-range="today">Today</button>
        <button type="button" class="kpi-range-btn <?= $activeRange === '7d' ? 'active' : '' ?>" data-range="7d">7D</button>
        <button type="button" class="kpi-range-btn <?= $activeRange === '30d' ? 'active' : '' ?>" data-range="30d">30D</button>
        <button type="button" class="kpi-range-btn <?= $activeRange === 'all' ? 'active' : '' ?>" data-range="all">All</button>
      </div>
    </div>
    <div class="kpi-value text-orange"><?= mta_money($grossSales) ?></div>
    <div class="kpi-meta">
      <span class="badge badge-success">Delivered Invoices</span>
      <span class="kpi-subtext"><?= $grossSales > 0 ? 'Verified Revenue' : 'Awaiting Deliveries' ?></span>
    </div>
  </div>

  <!-- KPI 2: Advance Verified -->
  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-label">Advance Verified</span>
      <div class="kpi-icon-box bg-teal-light text-teal">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
      </div>
    </div>
    <div class="kpi-value text-teal"><?= mta_money($advanceVerified) ?></div>
    <div class="kpi-meta">
      <span class="badge badge-teal">Bank / JazzCash / EasyPaisa</span>
      <span class="kpi-subtext">Prepaid Security Deposit</span>
    </div>
  </div>

  <!-- KPI 3: Outstanding COD -->
  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-label">Outstanding COD</span>
      <div class="kpi-icon-box bg-amber-light text-amber">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
      </div>
    </div>
    <div class="kpi-value"><?= mta_money($outstandingCod) ?></div>
    <div class="kpi-meta">
      <span class="badge badge-amber">Courier Collection</span>
      <span class="kpi-subtext">Active Orders in Pipeline</span>
    </div>
  </div>

  <!-- KPI 4: Pending Approvals -->
  <div class="kpi-card">
    <div class="kpi-card-header">
      <span class="kpi-label">Pending Approvals</span>
      <div class="kpi-icon-box bg-rose-light text-rose">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
      </div>
    </div>
    <div class="kpi-value <?= $pendingApprovals > 0 ? 'text-rose' : '' ?>"><?= number_format($pendingApprovals) ?></div>
    <div class="kpi-meta">
      <span class="badge <?= $pendingApprovals > 0 ? 'badge-rose' : 'badge-neutral' ?>"><?= $pendingApprovals > 0 ? 'Action Required' : 'All Clear' ?></span>
      <span class="kpi-subtext">Awaiting Phone Confirmation</span>
    </div>
  </div>

</div>

<!-- =========================================================================
     3. Order Fulfillment Lifecycle Pipeline (5 Statuses with links)
     ========================================================================= -->
<div class="pipeline-section">
  <div class="pipeline-header">
    <div class="section-title-wrap">
      <h3 class="section-title">Order Fulfillment Lifecycle</h3>
      <p class="section-subtitle">Real-time status breakdown across all Pakistan customer shipments</p>
    </div>
    <a href="<?= e($adminPath) ?>/orders" class="pipeline-view-all">
      <span>View All Orders</span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
    </a>
  </div>

  <div class="pipeline-tiles-grid">
    
    <!-- 1. Pending Approval -->
    <a href="<?= e($adminPath) ?>/orders?status=pending" class="pipeline-tile status-amber">
      <div class="tile-top">
        <span class="tile-status-dot dot-amber"></span>
        <span class="tile-label">Pending Approval</span>
      </div>
      <div class="tile-count"><?= (int)($pipeline['pending'] ?? 0) ?></div>
      <div class="tile-badge badge-amber">Awaiting Review</div>
    </a>

    <!-- 2. Processing -->
    <a href="<?= e($adminPath) ?>/orders?status=processing" class="pipeline-tile status-sky">
      <div class="tile-top">
        <span class="tile-status-dot dot-sky"></span>
        <span class="tile-label">Processing</span>
      </div>
      <div class="tile-count"><?= (int)($pipeline['processing'] ?? 0) ?></div>
      <div class="tile-badge badge-sky">Packing & QA</div>
    </a>

    <!-- 3. Shipped -->
    <a href="<?= e($adminPath) ?>/orders?status=shipped" class="pipeline-tile status-violet">
      <div class="tile-top">
        <span class="tile-status-dot dot-violet"></span>
        <span class="tile-label">Shipped</span>
      </div>
      <div class="tile-count"><?= (int)($pipeline['shipped'] ?? 0) ?></div>
      <div class="tile-badge badge-violet">In Courier Transit</div>
    </a>

    <!-- 4. Delivered -->
    <a href="<?= e($adminPath) ?>/orders?status=delivered" class="pipeline-tile status-green">
      <div class="tile-top">
        <span class="tile-status-dot dot-green"></span>
        <span class="tile-label">Delivered</span>
      </div>
      <div class="tile-count"><?= (int)($pipeline['delivered'] ?? 0) ?></div>
      <div class="tile-badge badge-green">Completed & Paid</div>
    </a>

    <!-- 5. Cancelled -->
    <a href="<?= e($adminPath) ?>/orders?status=cancelled" class="pipeline-tile status-rose">
      <div class="tile-top">
        <span class="tile-status-dot dot-rose"></span>
        <span class="tile-label">Cancelled</span>
      </div>
      <div class="tile-count"><?= (int)($pipeline['cancelled'] ?? 0) ?></div>
      <div class="tile-badge badge-rose">Voided / Returned</div>
    </a>

  </div>
</div>

<!-- =========================================================================
     4. Two-Column Main Section (Recent Orders & Product Intelligence)
     ========================================================================= -->
<div class="dashboard-two-cols">
  
  <!-- Column 1: Recent Orders Placed -->
  <div class="admin-panel">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">Recent Orders Placed</h4>
        <span class="panel-badge"><?= count($recentOrders) ?> Recent</span>
      </div>
      <a href="<?= e($adminPath) ?>/orders" class="panel-link">Manage All &rarr;</a>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>City</th>
            <th>Total</th>
            <th>Status</th>
            <th style="text-align:right;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentOrders)): ?>
            <tr>
              <td colspan="6" style="text-align:center; padding:3rem 1rem; color:var(--admin-text-muted);">
                <div style="font-weight:600; font-size:1rem; margin-bottom:0.35rem; color:var(--admin-text);">No Customer Orders Yet</div>
                <div style="font-size:0.85rem;">As orders arrive via website checkout or WhatsApp, they will appear here in real time.</div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($recentOrders as $ord): 
              $st = strtolower((string)$ord['status']);
              $badgeClass = match ($st) {
                'pending' => 'badge-amber',
                'processing' => 'badge-sky',
                'shipped' => 'badge-violet',
                'delivered' => 'badge-green',
                'cancelled' => 'badge-rose',
                default => 'badge-neutral',
              };
            ?>
              <tr>
                <td>
                  <a href="<?= e($adminPath) ?>/orders/<?= (int)$ord['id'] ?>" class="table-order-num">
                    <?= e($ord['order_number']) ?>
                  </a>
                </td>
                <td>
                  <div style="font-weight:600; color:var(--admin-text);"><?= e($ord['customer_name']) ?></div>
                  <div style="font-size:0.75rem; color:var(--admin-text-muted);"><?= e($ord['customer_phone']) ?></div>
                </td>
                <td>
                  <span class="badge badge-neutral"><?= e($ord['city'] ?: 'Multan') ?></span>
                </td>
                <td>
                  <strong style="color:var(--admin-text); font-family:var(--font-heading);"><?= mta_money($ord['total']) ?></strong>
                </td>
                <td>
                  <span class="badge <?= $badgeClass ?>"><?= ucfirst($st) ?></span>
                </td>
                <td style="text-align:right;">
                  <a href="<?= e($adminPath) ?>/orders/<?= (int)$ord['id'] ?>" class="btn-action-icon" title="View Order Details">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Column 2: Top Selling Products (Strict: only units_sold > 0) OR Engagement Fallback -->
  <div class="admin-panel">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">Product Performance</h4>
        <span class="panel-badge"><?= !empty($topSold) ? 'Top Selling (Delivered)' : 'Catalog Engagement' ?></span>
      </div>
      <a href="<?= e($adminPath) ?>/products" class="panel-link">Catalog &rarr;</a>
    </div>

    <div class="top-products-list">
      <?php if (!empty($topSold)): ?>
        <?php foreach ($topSold as $idx => $prod): ?>
          <div class="top-product-item">
            <div class="top-product-rank"><?= $idx + 1 ?></div>
            <img src="<?= e($prod['image_400'] ?: ($prod['primary_image_url'] ?: '/assets/images/placeholder.svg')) ?>" alt="" class="top-product-img" loading="lazy" width="48" height="48">
            <div class="top-product-info">
              <a href="/products/<?= e($prod['slug']) ?>" target="_blank" rel="noopener noreferrer" class="top-product-name" title="<?= e($prod['title']) ?>">
                <?= e($prod['title']) ?>
              </a>
              <div class="top-product-meta">
                <span class="top-product-price"><?= mta_money($prod['sell_price']) ?></span>
                <span class="sep">&bull;</span>
                <span class="top-product-sold"><strong><?= (int)$prod['units_sold'] ?></strong> sold (<?= mta_money($prod['total_revenue']) ?>)</span>
              </div>
            </div>
            <a href="<?= e($adminPath) ?>/products/<?= (int)$prod['id'] ?>/edit" class="btn-action-icon" title="Edit in Admin">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Clean Empty State for Sales (Never label unsold items as top selling) -->
        <div class="empty-sales-state">
          <div class="empty-sales-title">No verified sales yet</div>
          <p class="empty-sales-desc">Products will appear here ranked by revenue and units once customer orders are completed.</p>
        </div>

        <!-- Fallback List: Most Popular by Engagement & Page Views -->
        <div class="engagement-section-title">
          <span>Most Viewed Products (Catalog Engagement)</span>
        </div>
        <?php foreach ($mostPopular as $idx => $prod): ?>
          <div class="top-product-item">
            <div class="top-product-rank"><?= $idx + 1 ?></div>
            <img src="<?= e($prod['image_400'] ?: ($prod['primary_image_url'] ?: '/assets/images/placeholder.svg')) ?>" alt="" class="top-product-img" loading="lazy" width="48" height="48">
            <div class="top-product-info">
              <a href="/products/<?= e($prod['slug']) ?>" target="_blank" rel="noopener noreferrer" class="top-product-name" title="<?= e($prod['title']) ?>">
                <?= e($prod['title']) ?>
              </a>
              <div class="top-product-meta">
                <span class="top-product-price"><?= mta_money($prod['sell_price']) ?></span>
                <span class="sep">&bull;</span>
                <span class="badge badge-sky" style="font-size:0.65rem;">Score: <?= (int)$prod['popularity_score'] ?> views</span>
              </div>
            </div>
            <a href="<?= e($adminPath) ?>/products/<?= (int)$prod['id'] ?>/edit" class="btn-action-icon" title="Edit in Admin">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            </a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- =========================================================================
     5. Bottom Row: Single Catalog Reconciliation & Supplier Sync Telemetry
     ========================================================================= -->
<div class="bottom-stats-grid">
  
  <!-- Reconciliation Card: Single Definitive Source of Truth -->
  <div class="stat-card catalog-reconciliation-card">
    <div class="stat-card-header">
      <div style="display:flex; align-items:center; gap:0.5rem;">
        <span class="stat-card-label">Catalog Health & Reconciliation</span>
        <button type="button" class="btn-glossary-info" id="btn-catalog-glossary" title="Click to view definition glossary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        </button>
      </div>
      <span class="badge <?= $catalogHealth['has_discrepancy'] ? 'badge-rose' : 'badge-success' ?>">
        <?= $catalogHealth['has_discrepancy'] ? 'Discrepancy Detected' : '100% Reconciled' ?>
      </span>
    </div>

    <!-- Exact formula breakdown -->
    <div class="reconciliation-breakdown">
      <div class="recon-row">
        <span class="recon-label">Supplier Feed Total:</span>
        <strong class="recon-val"><?= number_format($catalogHealth['feed_total']) ?> items</strong>
      </div>
      <div class="recon-row">
        <span class="recon-label">MTA Active Catalog:</span>
        <strong class="recon-val"><?= number_format($catalogHealth['imported_total']) ?> products</strong>
      </div>
      <div class="recon-pill-row">
        <span class="recon-pill pill-stock">In Stock: <?= number_format($catalogHealth['in_stock']) ?></span>
        <span class="recon-pill pill-out">Out of Stock: <?= number_format($catalogHealth['out_of_stock']) ?></span>
        <span class="recon-pill pill-live">Live Published: <?= number_format($catalogHealth['live_published']) ?></span>
      </div>
      <div class="recon-formula">
        Formula: <?= $catalogHealth['imported_total'] ?> Imported (<?= $catalogHealth['in_stock'] ?> in stock + <?= $catalogHealth['out_of_stock'] ?> out) + <?= $catalogHealth['skipped_total'] ?> Skipped = <?= $catalogHealth['feed_total'] ?> Total Feed
      </div>
    </div>
  </div>

  <!-- Supplier Sync Telemetry & Health Heartbeat -->
  <div class="stat-card sync-status-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Supplier Sync Heartbeat</span>
      <span class="badge <?= match($catalogHealth['sync_health']) {
        'stale' => 'badge-rose',
        'delayed' => 'badge-amber',
        default => 'badge-green',
      } ?>">
        <?= e($catalogHealth['sync_badge_text']) ?>
      </span>
    </div>
    
    <div class="sync-telemetry-body">
      <div class="sync-info-row">
        <span>Last Sync:</span>
        <strong><?= !empty($catalogHealth['last_sync']['finished_at']) ? date('d M Y, h:i A', strtotime($catalogHealth['last_sync']['finished_at'])) : 'Never' ?></strong>
      </div>
      <div class="sync-info-row">
        <span>Result:</span>
        <strong class="text-teal"><?= number_format($catalogHealth['imported_total']) ?> of <?= number_format($catalogHealth['feed_total']) ?> synced (Ends in 99/999)</strong>
      </div>
      <?php if ($catalogHealth['sync_health'] === 'stale'): ?>
        <div class="sync-warning-alert">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
          <span>Sync delayed (> 6 hrs). Please verify the cron job in your hosting control panel.</span>
        </div>
      <?php endif; ?>
    </div>

    <form action="<?= e($adminPath) ?>/sync/trigger" method="POST" style="margin-top:0.75rem;">
      <?= mta_csrf_field() ?>
      <button type="submit" class="btn btn-primary btn-sm" style="width:100%; justify-content:center;" data-confirm="Run automated supplier catalog synchronization now?">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.3"></path></svg>
        <span>Run Sync Now</span>
      </button>
    </form>
  </div>

  <!-- Stat Card: WhatsApp Clicks -->
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">WhatsApp Interactions</span>
      <span class="badge badge-success">Clicks</span>
    </div>
    <div class="stat-card-value text-teal">
      <?= number_format($totalWaClicks) ?>
    </div>
    <div class="stat-card-meta">
      <span>Order & Product Share Inquiries</span>
    </div>
  </div>

  <!-- Stat Card: Out of Stock Alert -->
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Out of Stock Items</span>
      <span class="badge <?= $catalogHealth['out_of_stock'] > 0 ? 'badge-amber' : 'badge-neutral' ?>">Inventory Alert</span>
    </div>
    <div class="stat-card-value <?= $catalogHealth['out_of_stock'] > 0 ? 'text-amber' : '' ?>">
      <?= number_format($catalogHealth['out_of_stock']) ?>
    </div>
    <div class="stat-card-meta">
      <a href="<?= e($adminPath) ?>/products?stock=out_of_stock" class="stat-card-link">Review Inventory &rarr;</a>
    </div>
  </div>

</div>

<!-- ---------------------------------------------------------------------------
     Catalog Reconciliation Definition Glossary Dialog
     --------------------------------------------------------------------------- -->
<div id="glossaryModal" class="admin-modal-overlay" aria-hidden="true">
  <div class="modal-backdrop"></div>
  <div class="admin-modal-box">
    <div class="admin-modal-header">
      <h3 class="admin-modal-title">Catalog Numbers Glossary</h3>
      <button type="button" class="modal-close" aria-label="Close Dialog">&times;</button>
    </div>
    <div class="admin-modal-body" style="font-size:0.875rem; line-height:1.6; color:var(--admin-text);">
      <p style="margin-bottom:1rem; color:var(--admin-text-muted);">
        Every number in the MTA admin console is grounded in this exact relational specification:
      </p>
      <ul style="list-style-type:none; padding-left:0; display:flex; flex-direction:column; gap:0.75rem;">
        <li>
          <strong>Supplier Feed Total (991):</strong> Raw items received from the automated supplier XML/JSON feed.
        </li>
        <li>
          <strong>MTA Active Catalog (991):</strong> Total verified product records present in the MTA SQLite/MySQL database.
        </li>
        <li>
          <strong>In Stock (<?= (int)$catalogHealth['in_stock'] ?>):</strong> Active items available for checkout with live supplier inventory.
        </li>
        <li>
          <strong>Out of Stock (<?= (int)$catalogHealth['out_of_stock'] ?>):</strong> Products temporarily out of stock at supplier; prices still tracked.
        </li>
        <li>
          <strong>Live Published (<?= (int)$catalogHealth['live_published'] ?>):</strong> Active catalog items accessible on the storefront.
        </li>
        <li>
          <strong>Skipped (0):</strong> Items rejected during feed import due to missing SKU, corrupt title, or negative price.
        </li>
      </ul>
    </div>
    <div class="admin-modal-footer">
      <button type="button" class="btn btn-secondary modal-close">Close Glossary</button>
    </div>
  </div>
</div>
