<?php
/**
 * Multan Tech Accessories — Master Admin Layout
 */
$adminPath = mta_admin_path();
$currentUser = \App\Core\Session::get('admin_user', []);
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="en-PK">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($meta_title ?? 'Admin Dashboard — Multan Tech Accessories') ?></title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png">
  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/design-tokens.css">
  <style>
    :root {
      --admin-sidebar-bg: #1A1A18;
      --admin-sidebar-text: #D6D5CE;
      --admin-sidebar-hover: #262624;
      --admin-sidebar-active: #9B3828;
      --admin-topbar-bg: #FFFFFF;
      --admin-body-bg: #F5F5F0;
      --admin-card-bg: #FFFFFF;
      --admin-border: #E6E4DC;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: var(--font-sans);
      background-color: var(--admin-body-bg);
      color: #141413;
      display: flex;
      min-height: 100vh;
      font-size: 0.925rem;
      line-height: 1.5;
    }
    a { color: inherit; text-decoration: none; }
    
    /* Sidebar */
    .admin-sidebar {
      width: 260px;
      background: var(--admin-sidebar-bg);
      color: var(--admin-sidebar-text);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
    }
    .admin-brand {
      padding: 1.25rem 1.5rem;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .admin-brand-icon {
      width: 34px;
      height: 34px;
      background: #D84D34;
      color: #fff;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1.1rem;
    }
    .admin-brand-title {
      font-weight: 800;
      font-size: 1rem;
      color: #fff;
      line-height: 1.2;
    }
    .admin-brand-sub {
      font-size: 0.75rem;
      color: #9C9B93;
    }
    .admin-nav {
      padding: 1rem 0;
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }
    .admin-nav-heading {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.75px;
      color: #7A7973;
      padding: 0.75rem 1.5rem 0.25rem;
      font-weight: 700;
    }
    .admin-nav-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.65rem 1.5rem;
      font-weight: 500;
      color: var(--admin-sidebar-text);
      transition: background 0.15s, color 0.15s;
    }
    .admin-nav-item:hover {
      background: var(--admin-sidebar-hover);
      color: #FFFFFF;
    }
    .admin-nav-item.active {
      background: var(--admin-sidebar-active);
      color: #FFFFFF;
      font-weight: 700;
    }
    .admin-nav-badge {
      margin-left: auto;
      font-size: 0.75rem;
      padding: 0.1rem 0.45rem;
      border-radius: 10px;
      background: rgba(255,255,255,0.15);
      color: #fff;
    }

    /* Main Content Area */
    .admin-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }
    .admin-topbar {
      height: 64px;
      background: var(--admin-topbar-bg);
      border-bottom: 1px solid var(--admin-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 2rem;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .admin-topbar-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .admin-topbar-title {
      font-size: 1.15rem;
      font-weight: 700;
    }
    .admin-topbar-right {
      display: flex;
      align-items: center;
      gap: 1.25rem;
    }
    .admin-body {
      padding: 2rem;
      flex: 1;
    }

    /* Common Components */
    .admin-card {
      background: var(--admin-card-bg);
      border: 1px solid var(--admin-border);
      border-radius: 10px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .admin-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.25rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid var(--admin-border);
    }
    .admin-card-title {
      font-size: 1.15rem;
      font-weight: 700;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      font-weight: 600;
      font-size: 0.875rem;
      cursor: pointer;
      border: 1px solid transparent;
      transition: background 0.15s;
    }
    .btn-primary { background: #9B3828; color: #fff; }
    .btn-primary:hover { background: #7E2A1D; }
    .btn-secondary { background: #EFEFEA; color: #141413; border-color: #D6D5CE; }
    .btn-secondary:hover { background: #E5E5DE; }
    .btn-success { background: #128C7E; color: #fff; }
    .btn-success:hover { background: #0E7064; }
    .btn-danger { background: #DC2626; color: #fff; }
    .btn-danger:hover { background: #B91C1C; }
    .btn-sm { padding: 0.35rem 0.65rem; font-size: 0.8rem; }

    /* Flash Alerts */
    .alert {
      padding: 1rem 1.25rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .alert-success { background: #E8F9EE; color: #0E7064; border: 1px solid #B8EAD1; }
    .alert-error { background: #FDE8E8; color: #9B1C1C; border: 1px solid #F8B4B4; }
    .alert-warning { background: #FEF08A; color: #854D0E; border: 1px solid #FDE047; }
    .alert-info { background: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; }

    /* Table Styles */
    .admin-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.875rem;
    }
    .admin-table th {
      background: #F9F9F7;
      text-align: left;
      padding: 0.75rem 1rem;
      font-weight: 600;
      border-bottom: 2px solid var(--admin-border);
      color: #555;
    }
    .admin-table td {
      padding: 0.85rem 1rem;
      border-bottom: 1px solid var(--admin-border);
      vertical-align: middle;
    }
    .admin-table tr:hover td {
      background: #FAF9F6;
    }
    .badge {
      display: inline-block;
      padding: 0.2rem 0.55rem;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: capitalize;
    }
    .badge-success { background: #E8F9EE; color: #0E7064; }
    .badge-warning { background: #FEF08A; color: #854D0E; }
    .badge-danger { background: #FDE8E8; color: #9B1C1C; }
    .badge-neutral { background: #EFEFEA; color: #555; }
    .badge-info { background: #E0F2FE; color: #0369A1; }

    /* Form Controls */
    .form-group {
      margin-bottom: 1.25rem;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      font-size: 0.85rem;
      margin-bottom: 0.4rem;
      color: #333;
    }
    .form-control {
      width: 100%;
      padding: 0.65rem 0.85rem;
      border: 1px solid var(--admin-border);
      border-radius: 6px;
      font-size: 0.9rem;
      background: #fff;
      font-family: inherit;
    }
    .form-control:focus {
      outline: none;
      border-color: #9B3828;
      box-shadow: 0 0 0 3px rgba(155, 56, 40, 0.1);
    }
    .form-help {
      font-size: 0.775rem;
      color: #777;
      margin-top: 0.3rem;
    }
  </style>
</head>
<body>

  <!-- Admin Sidebar -->
  <aside class="admin-sidebar">
    <div class="admin-brand">
      <img src="/assets/images/logo-night.png" alt="Multan Tech Accessories" style="height: 38px; width: auto; object-fit: contain; max-width: 170px;">
    </div>

    <nav class="admin-nav">
      <div class="admin-nav-heading">Overview</div>
      <a href="<?= e($adminPath) ?>" class="admin-nav-item <?= $currentPath === $adminPath ? 'active' : '' ?>">
        <span>📊</span> Dashboard
      </a>
      <a href="<?= e($adminPath) ?>/sync" class="admin-nav-item <?= str_contains($currentPath, '/sync') ? 'active' : '' ?>">
        <span>🔄</span> Supplier Sync
      </a>

      <div class="admin-nav-heading">E-Commerce</div>
      <a href="<?= e($adminPath) ?>/products" class="admin-nav-item <?= str_contains($currentPath, '/products') ? 'active' : '' ?>">
        <span>📦</span> Products (991)
      </a>
      <a href="<?= e($adminPath) ?>/import" class="admin-nav-item <?= str_contains($currentPath, '/import') ? 'active' : '' ?>">
        <span>📥</span> Multi-Source Import
      </a>
      <a href="<?= e($adminPath) ?>/categories" class="admin-nav-item <?= $currentPath === $adminPath . '/categories' ? 'active' : '' ?>">
        <span>📂</span> Categories
      </a>
      <a href="<?= e($adminPath) ?>/categories/mappings" class="admin-nav-item <?= str_contains($currentPath, '/categories/mappings') ? 'active' : '' ?>">
        <span>🔗</span> Supplier Mappings
      </a>
      <a href="<?= e($adminPath) ?>/orders" class="admin-nav-item <?= str_contains($currentPath, '/orders') ? 'active' : '' ?>">
        <span>🛍️</span> Orders
      </a>

      <div class="admin-nav-heading">Content & SEO</div>
      <a href="<?= e($adminPath) ?>/banners" class="admin-nav-item <?= str_contains($currentPath, '/banners') ? 'active' : '' ?>">
        <span>🖼️</span> Hero Banners
      </a>
      <a href="<?= e($adminPath) ?>/guides" class="admin-nav-item <?= str_contains($currentPath, '/guides') ? 'active' : '' ?>">
        <span>📝</span> Buying Guides
      </a>
      <a href="<?= e($adminPath) ?>/queue" class="admin-nav-item <?= str_contains($currentPath, '/queue') ? 'active' : '' ?>">
        <span>🤖</span> AI Content Queue
      </a>
      <a href="<?= e($adminPath) ?>/seo-audit" class="admin-nav-item <?= str_contains($currentPath, '/seo-audit') ? 'active' : '' ?>">
        <span>🔍</span> SEO Audit & Redirects
      </a>

      <div class="admin-nav-heading">System</div>
      <a href="<?= e($adminPath) ?>/settings" class="admin-nav-item <?= str_contains($currentPath, '/settings') ? 'active' : '' ?>">
        <span>⚙️</span> Store Settings
      </a>
    </nav>
  </aside>

  <!-- Admin Main -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div class="admin-topbar-left">
        <h1 class="admin-topbar-title"><?= e($page_title ?? 'Dashboard') ?></h1>
      </div>

      <div class="admin-topbar-right">
        <!-- Quick Sync Button -->
        <form action="<?= e($adminPath) ?>/sync/trigger" method="POST" style="display:inline;">
          <?= mta_csrf_field() ?>
          <button type="submit" class="btn btn-secondary btn-sm" title="Schedule immediate background supplier sync">
            ⚡ Quick Sync
          </button>
        </form>

        <!-- View Storefront Link -->
        <a href="/" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">
          🌐 View Store &rarr;
        </a>

        <!-- Admin Profile & Logout -->
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <span style="font-size: 0.85rem; font-weight: 600; color: #555;">
            <?= e($currentUser['username'] ?? 'Muneeb Ahmad') ?>
          </span>
          <form action="<?= e($adminPath) ?>/logout" method="POST" style="display:inline;">
            <?= mta_csrf_field() ?>
            <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.775rem;">
              Logout
            </button>
          </form>
        </div>
      </div>
    </header>

    <main class="admin-body">
      <!-- Flash Messages -->
      <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success">
          <span><?= e(\App\Core\Session::getFlash('success')) ?></span>
        </div>
      <?php endif; ?>

      <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-error">
          <span><?= e(\App\Core\Session::getFlash('error')) ?></span>
        </div>
      <?php endif; ?>

      <?php if (\App\Core\Session::hasFlash('warning')): ?>
        <div class="alert alert-warning">
          <span><?= e(\App\Core\Session::getFlash('warning')) ?></span>
        </div>
      <?php endif; ?>

      <?php if (\App\Core\Session::hasFlash('info')): ?>
        <div class="alert alert-info">
          <span><?= e(\App\Core\Session::getFlash('info')) ?></span>
        </div>
      <?php endif; ?>

      <!-- View Content Slot -->
      <?= $content ?? '' ?>
    </main>
  </div>

</body>
</html>
