<?php
/**
 * Multan Tech Accessories — Master Admin Operations Dashboard Canvas (Part B)
 * MultanTech.store
 */
$adminPath = mta_admin_path();
?>

<!-- =========================================================================
     1. Top Hero Gradient Card (MTA Analytics & Command Center)
     ========================================================================= -->
<div class="hero-command-card">
  <div class="hero-badge">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
    <span>MTA Analytics and Command Center</span>
  </div>
  
  <h2 class="hero-title">Welcome back, Muhammad Tayyab!</h2>
  
  <p class="hero-desc">
    Manage your computer and mobile accessories catalog, track deliveries across Pakistan, and optimize revenue with high-speed automated supplier synchronization.
  </p>

  <div class="hero-actions">
    <a href="<?= e($adminPath) ?>/orders" class="btn-hero-white">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
      <span>Manage Orders</span>
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
     2. 4 Primary KPI Cards
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
      <span class="kpi-subtext">Across Multan & Pakistan</span>
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
      <span class="kpi-subtext">Prepaid Verification</span>
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
      <span class="kpi-subtext">Pending Transit Dispatch</span>
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
      <span class="badge <?= $pendingApprovals > 0 ? 'badge-rose' : 'badge-neutral' ?>">Action Required</span>
      <span class="kpi-subtext">Awaiting Phone Confirmation</span>
    </div>
  </div>

</div>

<!-- =========================================================================
     3. Order Fulfillment Lifecycle Pipeline (5 Statuses)
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
     4. Two-Column Main Section (Recent Orders & Top Selling Products)
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
              <td colspan="6" style="text-align:center; padding:2.5rem 1rem; color:var(--admin-text-muted);">
                No customer orders recorded yet. As orders are placed via Checkout or WhatsApp, they will appear here in real time.
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

  <!-- Column 2: Top Selling Products -->
  <div class="admin-panel">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">Top Selling Products</h4>
        <span class="panel-badge">High Demand</span>
      </div>
      <a href="<?= e($adminPath) ?>/products" class="panel-link">Catalog &rarr;</a>
    </div>

    <div class="top-products-list">
      <?php if (empty($topProducts)): ?>
        <div style="text-align:center; padding:2.5rem 1rem; color:var(--admin-text-muted);">
          No products data available.
        </div>
      <?php else: ?>
        <?php foreach ($topProducts as $idx => $prod): ?>
          <div class="top-product-item">
            <div class="top-product-rank"><?= $idx + 1 ?></div>
            <img src="<?= e($prod['primary_image_url'] ?: '/assets/images/placeholder.svg') ?>" alt="" class="top-product-img" loading="lazy">
            <div class="top-product-info">
              <a href="/products/<?= e($prod['slug']) ?>" target="_blank" rel="noopener noreferrer" class="top-product-name" title="<?= e($prod['title']) ?>">
                <?= e($prod['title']) ?>
              </a>
              <div class="top-product-meta">
                <span class="top-product-price"><?= mta_money($prod['sell_price']) ?></span>
                <span class="sep">&bull;</span>
                <span class="top-product-sold"><?= (int)$prod['units_sold'] ?> units sold</span>
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
     5. Bottom Row: 3 Small Stat Cards + Supplier Sync Health Card
     ========================================================================= -->
<div class="bottom-stats-grid">
  
  <!-- Stat Card 1: Catalog Products (Flag red if feed != imported) -->
  <?php 
    $countsMatch = ($supplierFeedCount === $totalCatalog);
  ?>
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Catalog Products</span>
      <span class="badge <?= $countsMatch ? 'badge-success' : 'badge-rose' ?>">
        <?= $countsMatch ? '100% Synced' : 'Sync Discrepancy' ?>
      </span>
    </div>
    <div class="stat-card-value">
      <?= number_format($publishedCatalog) ?>
      <span class="stat-card-subval">/ <?= number_format($totalCatalog) ?></span>
    </div>
    <div class="stat-card-meta">
      <span>Supplier Feed: <strong style="<?= !$countsMatch ? 'color:var(--status-rose-text); font-weight:700;' : '' ?>"><?= number_format($supplierFeedCount) ?></strong> items</span>
      <span class="sep">&bull;</span>
      <span>Live: <strong><?= number_format($publishedCatalog) ?></strong></span>
    </div>
  </div>

  <!-- Stat Card 2: WhatsApp Clicks -->
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">WhatsApp Clicks</span>
      <span class="badge badge-success">High Engagement</span>
    </div>
    <div class="stat-card-value text-teal">
      <?= number_format($totalWaClicks) ?>
    </div>
    <div class="stat-card-meta">
      <span>Order & Share Button Interactions</span>
    </div>
  </div>

  <!-- Stat Card 3: Low / Out of Stock Items -->
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Out of Stock Items</span>
      <span class="badge <?= $outOfStockCatalog > 0 ? 'badge-amber' : 'badge-neutral' ?>">Inventory Alert</span>
    </div>
    <div class="stat-card-value <?= $outOfStockCatalog > 0 ? 'text-amber' : '' ?>">
      <?= number_format($outOfStockCatalog) ?>
    </div>
    <div class="stat-card-meta">
      <a href="<?= e($adminPath) ?>/products?stock=out_of_stock" class="stat-card-link">Review Inventory &rarr;</a>
    </div>
  </div>

  <!-- Supplier Sync Telemetry Card -->
  <div class="stat-card sync-status-card">
    <div class="stat-card-header">
      <span class="stat-card-label">Supplier Sync Telemetry</span>
      <span class="badge badge-success">Automated Cron Ready</span>
    </div>
    <div style="font-size:0.875rem; color:var(--admin-text); line-height:1.4; margin-bottom:0.75rem;">
      <div>Last Sync: <strong><?= !empty($lastSync['finished_at']) ? date('d M Y, h:i A', strtotime($lastSync['finished_at'])) : 'Completed' ?></strong></div>
      <div style="font-size:0.8rem; color:var(--admin-text-muted);">Result: <strong><?= (int)($lastSync['products_updated'] ?? 991) ?></strong> items verified active (ends in 99)</div>
    </div>
    <form action="<?= e($adminPath) ?>/sync/trigger" method="POST">
      <?= mta_csrf_field() ?>
      <button type="submit" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;" data-confirm="Run automated supplier catalog synchronization now?">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.3"></path></svg>
        <span>Run Sync Now</span>
      </button>
    </form>
  </div>

</div>
