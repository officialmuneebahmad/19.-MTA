<?php
/**
 * Multan Tech Accessories — Master Admin Operations Console App Shell
 * MultanTech.store
 */
$adminPath = mta_admin_path();
$currentUser = \App\Core\Session::get('admin_user', []);
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$userName = $currentUser['name'] ?? 'Muneeb Ahmad';
$userEmail = $currentUser['email'] ?? 'admin@multantech.store';

$parts = explode(' ', trim($userName));
$firstName = $parts[0] ?? 'Admin';
$userInitials = strtoupper(substr($parts[0] ?? 'M', 0, 1) . substr($parts[1] ?? 'A', 0, 1));

// Helper to determine active navigation item
$isActive = function(string $route) use ($currentPath, $adminPath): bool {
    $target = rtrim($adminPath . '/' . ltrim($route, '/'), '/');
    if ($route === '' || $route === '/') {
        return $currentPath === $adminPath || $currentPath === $adminPath . '/';
    }
    return str_starts_with($currentPath, $target);
};

$buildVersion = defined('MTA_BUILD_VERSION') ? MTA_BUILD_VERSION : time();
?>
<!DOCTYPE html>
<html lang="en-PK">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
  <title><?= e($meta_title ?? 'Store Management Console — Multan Tech Accessories') ?></title>
  <meta name="robots" content="noindex, nofollow, noarchive">
  <meta name="csrf-token" content="<?= e(\App\Core\Session::csrfToken()) ?>">
  
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png">
  <link rel="shortcut icon" href="/favicon.ico">
  
  <!-- Fonts Preconnect -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

  <!-- Admin Design System & Safety Net -->
  <link rel="stylesheet" href="/assets/css/admin.css?v=<?= e($buildVersion) ?>">

  <script>
    // Immediate early theme application to prevent FOUC
    try {
      var saved = localStorage.getItem('mta_admin_theme');
      var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (saved === 'dark' || (!saved && prefersDark)) {
        document.documentElement.setAttribute('data-theme', 'dark');
      } else {
        document.documentElement.removeAttribute('data-theme');
      }
    } catch (e) {}
  </script>
</head>
<body>

  <!-- Mobile Off-Canvas Backdrop -->
  <div id="sidebar-backdrop" class="sidebar-backdrop" aria-hidden="true"></div>

  <div class="admin-shell">
    
    <!-- -----------------------------------------------------------------------
         1. Left Navigation Sidebar (264px Desktop / Off-Canvas Drawer Mobile)
         ----------------------------------------------------------------------- -->
    <aside id="admin-sidebar" class="admin-sidebar" aria-label="Main Administration Navigation">
      
      <!-- Brand Header -->
      <div class="sidebar-header">
        <a href="<?= e($adminPath) ?>" class="brand-link" title="MTA Admin Dashboard">
          <div class="brand-logo-box">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
              <path d="M12 12v9"></path>
              <path d="m8 17 4 4 4-4"></path>
            </svg>
          </div>
          <div class="brand-text-group">
            <div class="brand-name">Multan Tech</div>
            <div class="brand-console-badge">ADMIN CONSOLE</div>
          </div>
        </a>
        <button type="button" id="sidebar-close-btn" class="sidebar-close-btn" aria-label="Close Sidebar">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>

      <!-- Main Navigation Menu -->
      <nav class="sidebar-nav">
        
        <div class="nav-section-title">Core Management</div>

        <!-- Dashboard -->
        <a href="<?= e($adminPath) ?>" class="nav-item <?= $isActive('') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
          <span>Dashboard</span>
        </a>

        <!-- Customer Orders -->
        <a href="<?= e($adminPath) ?>/orders" class="nav-item <?= $isActive('orders') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          <span>Customer Orders</span>
        </a>

        <!-- Products -->
        <a href="<?= e($adminPath) ?>/products" class="nav-item <?= $isActive('products') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>
          <span>Products Catalog</span>
        </a>

        <!-- Categories -->
        <a href="<?= e($adminPath) ?>/categories" class="nav-item <?= $isActive('categories') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
          <span>Categories & Mapping</span>
        </a>

        <!-- Brands & Logos -->
        <a href="<?= e($adminPath) ?>/brands" class="nav-item <?= $isActive('brands') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polygon points="12 8 8 12 12 16 16 12 12 8"></polygon></svg>
          <span>Brand Logos & Featured</span>
        </a>

        <!-- WhatsApp Orders -->
        <a href="<?= e($adminPath) ?>/whatsapp-orders" class="nav-item <?= $isActive('whatsapp-orders') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
          <span>WhatsApp Orders</span>
        </a>

        <!-- Export Data -->
        <a href="<?= e($adminPath) ?>/export" class="nav-item <?= $isActive('export') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          <span>Export Data</span>
        </a>

        <!-- Store Settings -->
        <a href="<?= e($adminPath) ?>/settings" class="nav-item <?= $isActive('settings') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
          <span>Store Settings</span>
        </a>

        <div class="nav-section-title">Catalog Operations</div>

        <!-- Multi-Source Import -->
        <a href="<?= e($adminPath) ?>/import" class="nav-item <?= $isActive('import') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
          <span>Quick Add & Import</span>
        </a>

        <!-- Promotional Banners -->
        <a href="<?= e($adminPath) ?>/banners" class="nav-item <?= $isActive('banners') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
          <span>Banners & Promos</span>
        </a>

        <!-- Buying Guides / Articles -->
        <a href="<?= e($adminPath) ?>/guides" class="nav-item <?= $isActive('guides') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
          <span>Buying Guides</span>
        </a>

        <!-- AI Content Production Queue -->
        <a href="<?= e($adminPath) ?>/queue" class="nav-item <?= $isActive('queue') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 17 10 11 4 5"></polyline><line x1="12" y1="19" x2="20" y2="19"></line></svg>
          <span>AI Content Queue</span>
        </a>

        <!-- SEO Audit & 404 Redirects -->
        <a href="<?= e($adminPath) ?>/seo-audit" class="nav-item <?= $isActive('seo-audit') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          <span>SEO & 404 Audit</span>
        </a>

        <!-- Supplier Sync Telemetry -->
        <a href="<?= e($adminPath) ?>/sync" class="nav-item <?= $isActive('sync') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.3"></path></svg>
          <span>Supplier Sync (991)</span>
        </a>

      </nav>

      <!-- Sidebar Bottom Area -->
      <div class="sidebar-footer">
        <a href="/" target="_blank" rel="noopener noreferrer" class="sidebar-live-store-link" title="Open Public Storefront in New Tab">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
          <span>View Live Store</span>
        </a>

        <form action="<?= e($adminPath) ?>/logout" method="POST" style="margin-top: 0.5rem;">
          <?= mta_csrf_field() ?>
          <button type="submit" class="sidebar-signout-btn" data-confirm="Are you sure you want to end your administration session?">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            <span>Sign Out</span>
          </button>
        </form>
      </div>

    </aside>

    <!-- -----------------------------------------------------------------------
         2. Main Application Area & Top Bar (64px Sticky Topbar)
         ----------------------------------------------------------------------- -->
    <div class="admin-main">
      
      <!-- Top Navigation Bar -->
      <header class="admin-topbar">
        
        <!-- Left Title & Mobile Menu Trigger -->
        <div class="topbar-left">
          <button type="button" id="sidebar-toggle-btn" class="topbar-menu-btn" aria-label="Open Navigation Menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
          </button>
          
          <div class="topbar-title-wrap">
            <h1 class="topbar-heading"><?= e($page_title ?? 'Store Management Console') ?></h1>
            <div class="topbar-breadcrumbs">
              <span>MTA Control</span>
              <span class="sep">/</span>
              <span class="current"><?= e($page_title ?? 'Dashboard') ?></span>
            </div>
          </div>
        </div>

        <!-- Global Search Bar in Topbar -->
        <div class="topbar-search-wrap">
          <form action="<?= e($adminPath) ?>/products" method="GET" class="topbar-search-form">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="topbar-search-icon"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" name="q" placeholder="Search catalog, orders, SKU..." class="topbar-search-input" aria-label="Search Catalog and Orders">
          </form>
        </div>

        <!-- Right Quick Actions -->
        <div class="topbar-right">
          
          <!-- Quick Action: Export JSON -->
          <button type="button" id="quick-export-btn" class="btn-topbar-action" title="Export Store Data (JSON)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span class="btn-text">Export JSON</span>
          </button>

          <!-- Day / Night Mode Toggle -->
          <button type="button" id="theme-toggle-btn" class="theme-toggle-btn" aria-label="Toggle Dark / Light Theme" title="Toggle Theme">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
          </button>

          <!-- Live Store Quick-Link -->
          <a href="/" target="_blank" rel="noopener noreferrer" class="btn-live-store-top" title="Open Storefront in New Tab">
            <span>Live Store</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
          </a>

          <!-- Admin Profile Pill (No 2FA mention, real name) -->
          <div class="admin-profile-pill" title="<?= e($userEmail) ?>">
            <div class="admin-avatar"><?= e($userInitials) ?></div>
            <div class="admin-profile-info">
              <div class="admin-profile-name"><?= e($userName) ?></div>
              <div class="admin-role-badge">
                <span class="role-dot"></span>
                <span>Owner</span>
              </div>
            </div>
          </div>

          <!-- Logout Form Button -->
          <form action="<?= e($adminPath) ?>/logout" method="POST" style="display:inline;">
            <?= mta_csrf_field() ?>
            <button type="submit" class="btn-topbar-logout" title="Sign Out" data-confirm="Sign out of the administration console?">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </button>
          </form>

        </div>

      </header>

      <!-- Content Canvas (Max 1440px) -->
      <main class="admin-content-canvas">
        
        <!-- Flash Alert Banners -->
        <?php if (\App\Core\Session::hasFlash('success')): ?>
          <div class="admin-alert admin-alert-success" role="alert">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <div><?= e(\App\Core\Session::getFlash('success')) ?></div>
          </div>
        <?php endif; ?>

        <?php if (\App\Core\Session::hasFlash('error')): ?>
          <div class="admin-alert admin-alert-error" role="alert">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            <div><?= e(\App\Core\Session::getFlash('error')) ?></div>
          </div>
        <?php endif; ?>

        <?php if (\App\Core\Session::hasFlash('warning')): ?>
          <div class="admin-alert admin-alert-warning" role="alert">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            <div><?= e(\App\Core\Session::getFlash('warning')) ?></div>
          </div>
        <?php endif; ?>

        <!-- Primary View Slot -->
        <?= $content ?? '' ?>

      </main>

    </div>

  </div>

  <!-- -------------------------------------------------------------------------
       3. Export Data Modal Dialog
       ------------------------------------------------------------------------- -->
  <div id="exportModal" class="admin-modal-overlay" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="admin-modal-box">
      <div class="admin-modal-header">
        <h3 class="admin-modal-title">Export Store Data</h3>
        <button type="button" class="modal-close" aria-label="Close Dialog">&times;</button>
      </div>
      <form action="<?= e($adminPath) ?>/export/download" method="POST">
        <?= mta_csrf_field() ?>
        <div class="admin-modal-body">
          <p style="font-size:0.875rem; color:var(--admin-text-muted); margin-bottom:1.25rem;">
            Select the dataset and export format. All administrative data exports are recorded in the security audit trail.
          </p>

          <div class="form-group" style="margin-bottom:1rem;">
            <label class="form-label">Dataset</label>
            <select name="dataset" class="form-control" required>
              <option value="products">Products & Inventory (All 991 Catalog Items)</option>
              <option value="orders">Customer Orders & COD Invoices</option>
              <option value="customers">Customers Directory & City Distribution</option>
              <option value="inquiries">WhatsApp Inquiries & Clicks Log</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom:1rem;">
            <label class="form-label">Export Format</label>
            <select name="format" class="form-control" required>
              <option value="json">JSON Format (Machine-Readable / API Integration)</option>
              <option value="csv">CSV Spreadsheet (Excel / Google Sheets)</option>
            </select>
          </div>
        </div>

        <div class="admin-modal-footer">
          <button type="button" class="btn btn-secondary modal-close">Cancel</button>
          <button type="submit" class="btn btn-primary">Generate Export File</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Master Admin Controller JS -->
  <script src="/assets/js/admin.js?v=<?= e($buildVersion) ?>" defer></script>

</body>
</html>
