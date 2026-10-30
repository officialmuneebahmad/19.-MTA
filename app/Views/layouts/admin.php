<?php
/**
 * Multan Tech Accessories — Enterprise Admin Operations Console
 * Modern Cloud Infrastructure & Store Operations Architecture
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
  <title><?= e($meta_title ?? 'Operations Console — Multan Tech Accessories') ?></title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png">
  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      /* Brand Identity */
      --brand-orange: #FF6223;
      --brand-orange-hover: #E54F12;
      --brand-teal: #0EB4A9;
      --brand-teal-hover: #099A90;

      /* Enterprise Slate Console Theme */
      --console-bg: #0B0E14;
      --console-sidebar: #0D1117;
      --console-sidebar-border: #1E232F;
      --console-topbar: #0F131C;
      --console-topbar-border: #1E232F;
      --console-card: #131823;
      --console-card-header: #181F2E;
      --console-card-border: #1E2536;
      --console-hover: #1A2131;
      
      --console-text: #E6EDF3;
      --console-text-muted: #8B949E;
      --console-text-dim: #5B6577;

      --font-console: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      --font-mono: 'JetBrains Mono', Consolas, monospace;

      --radius-sm: 6px;
      --radius-md: 10px;
      --radius-lg: 14px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: var(--font-console);
      background-color: var(--console-bg);
      color: var(--console-text);
      display: flex;
      min-height: 100vh;
      font-size: 0.885rem;
      line-height: 1.5;
      overflow-x: hidden;
    }

    a { color: inherit; text-decoration: none; }

    /* Custom Scrollbars */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #0B0E14; }
    ::-webkit-scrollbar-thumb { background: #262D3D; border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: #3B4459; }

    /* -------------------------------------------------------------------------
     * Sidebar Navigation
     * ------------------------------------------------------------------------- */
    .admin-sidebar {
      width: 250px;
      background: var(--console-sidebar);
      border-right: 1px solid var(--console-sidebar-border);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      position: sticky;
      top: 0;
      height: 100vh;
      z-index: 50;
      user-select: none;
    }

    .admin-brand {
      padding: 1.25rem 1.25rem;
      border-bottom: 1px solid var(--console-sidebar-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .brand-id {
      display: flex;
      align-items: center;
      gap: 0.65rem;
    }

    .brand-icon-box {
      width: 32px;
      height: 32px;
      background: linear-gradient(135deg, var(--brand-orange) 0%, #D84D34 100%);
      color: #fff;
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 0.95rem;
      box-shadow: 0 2px 8px rgba(255, 98, 35, 0.35);
    }

    .brand-meta {
      display: flex;
      flex-direction: column;
    }

    .brand-title {
      font-size: 0.875rem;
      font-weight: 800;
      letter-spacing: -0.2px;
      color: #FFFFFF;
      line-height: 1.2;
    }

    .brand-subtitle {
      font-size: 0.68rem;
      color: var(--brand-teal);
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .brand-badge {
      font-size: 0.65rem;
      padding: 0.15rem 0.45rem;
      border-radius: 4px;
      background: rgba(14, 180, 169, 0.15);
      color: var(--brand-teal);
      border: 1px solid rgba(14, 180, 169, 0.3);
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .admin-nav {
      padding: 1rem 0.75rem;
      flex: 1;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 0.15rem;
    }

    .admin-nav-heading {
      font-size: 0.675rem;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: var(--console-text-dim);
      padding: 0.75rem 0.65rem 0.35rem;
      font-weight: 700;
    }

    .admin-nav-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.6rem 0.75rem;
      border-radius: var(--radius-sm);
      font-weight: 600;
      font-size: 0.84rem;
      color: var(--console-text-muted);
      transition: all 0.15s ease;
      position: relative;
    }

    .admin-nav-item svg {
      width: 17px;
      height: 17px;
      stroke-width: 2;
      flex-shrink: 0;
      transition: stroke 0.15s ease;
    }

    .admin-nav-item:hover {
      background: var(--console-hover);
      color: var(--console-text);
    }

    .admin-nav-item.active {
      background: rgba(255, 98, 35, 0.12);
      color: var(--brand-orange);
      font-weight: 700;
    }

    .admin-nav-item.active svg {
      stroke: var(--brand-orange);
    }

    .admin-nav-item.active::before {
      content: '';
      position: absolute;
      left: -0.75rem;
      top: 15%;
      bottom: 15%;
      width: 3px;
      background: var(--brand-orange);
      border-radius: 0 2px 2px 0;
    }

    .admin-nav-pill {
      margin-left: auto;
      font-size: 0.675rem;
      font-weight: 700;
      padding: 0.1rem 0.45rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      color: var(--console-text-muted);
    }

    /* -------------------------------------------------------------------------
     * Topbar & Content Area
     * ------------------------------------------------------------------------- */
    .admin-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
      background: var(--console-bg);
    }

    .admin-topbar {
      height: 56px;
      background: var(--console-topbar);
      border-bottom: 1px solid var(--console-topbar-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 1.75rem;
      position: sticky;
      top: 0;
      z-index: 40;
    }

    .admin-breadcrumb {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.825rem;
      color: var(--console-text-muted);
    }

    .admin-breadcrumb a:hover {
      color: var(--console-text);
    }

    .admin-breadcrumb .active {
      color: #FFFFFF;
      font-weight: 700;
    }

    .admin-topbar-actions {
      display: flex;
      align-items: center;
      gap: 0.85rem;
    }

    .sys-status-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.72rem;
      font-weight: 700;
      padding: 0.25rem 0.65rem;
      background: rgba(14, 180, 169, 0.1);
      border: 1px solid rgba(14, 180, 169, 0.25);
      color: var(--brand-teal);
      border-radius: 999px;
      letter-spacing: 0.3px;
    }

    .status-dot-pulse {
      width: 6px;
      height: 6px;
      background: var(--brand-teal);
      border-radius: 50%;
      box-shadow: 0 0 6px var(--brand-teal);
    }

    .admin-body {
      padding: 1.75rem 2rem 3rem;
      flex: 1;
      max-width: 1500px;
      width: 100%;
      margin: 0 auto;
    }

    /* -------------------------------------------------------------------------
     * Enterprise Card Component
     * ------------------------------------------------------------------------- */
    .admin-card {
      background: var(--console-card);
      border: 1px solid var(--console-card-border);
      border-radius: var(--radius-md);
      padding: 1.35rem 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    }

    .admin-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.25rem;
      padding-bottom: 0.85rem;
      border-bottom: 1px solid var(--console-card-border);
    }

    .admin-card-title {
      font-size: 0.95rem;
      font-weight: 700;
      letter-spacing: -0.1px;
      color: #FFFFFF;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    /* Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.45rem;
      padding: 0.45rem 0.85rem;
      border-radius: var(--radius-sm);
      font-weight: 600;
      font-size: 0.825rem;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.15s ease;
      white-space: nowrap;
    }

    .btn svg {
      width: 15px;
      height: 15px;
      stroke-width: 2;
    }

    .btn-primary {
      background: var(--brand-orange);
      color: #FFFFFF;
      border-color: var(--brand-orange);
      box-shadow: 0 2px 6px rgba(255, 98, 35, 0.25);
    }

    .btn-primary:hover {
      background: var(--brand-orange-hover);
      border-color: var(--brand-orange-hover);
      transform: translateY(-1px);
    }

    .btn-secondary {
      background: #171E2B;
      color: var(--console-text);
      border-color: var(--console-card-border);
    }

    .btn-secondary:hover {
      background: #20293B;
      border-color: #2D374D;
      color: #FFFFFF;
    }

    .btn-teal {
      background: var(--brand-teal);
      color: #0A1C1A;
      font-weight: 700;
    }

    .btn-teal:hover {
      background: var(--brand-teal-hover);
      color: #0A1C1A;
    }

    .btn-danger {
      background: rgba(220, 38, 38, 0.15);
      color: #F87171;
      border-color: rgba(220, 38, 38, 0.3);
    }

    .btn-danger:hover {
      background: rgba(220, 38, 38, 0.3);
      color: #FFFFFF;
    }

    .btn-sm {
      padding: 0.3rem 0.65rem;
      font-size: 0.775rem;
    }

    /* Alerts */
    .alert {
      padding: 0.85rem 1.15rem;
      border-radius: var(--radius-sm);
      margin-bottom: 1.25rem;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border: 1px solid transparent;
    }

    .alert-success {
      background: rgba(14, 180, 169, 0.12);
      color: #2DD4BF;
      border-color: rgba(14, 180, 169, 0.3);
    }

    .alert-error {
      background: rgba(239, 68, 68, 0.12);
      color: #F87171;
      border-color: rgba(239, 68, 68, 0.3);
    }

    .alert-warning {
      background: rgba(245, 158, 11, 0.12);
      color: #FBBF24;
      border-color: rgba(245, 158, 11, 0.3);
    }

    .alert-info {
      background: rgba(59, 130, 246, 0.12);
      color: #60A5FA;
      border-color: rgba(59, 130, 246, 0.3);
    }

    /* Tables */
    .admin-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.84rem;
    }

    .admin-table th {
      background: var(--console-card-header);
      text-align: left;
      padding: 0.75rem 1rem;
      font-weight: 700;
      border-bottom: 1px solid var(--console-card-border);
      color: var(--console-text-muted);
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .admin-table td {
      padding: 0.85rem 1rem;
      border-bottom: 1px solid var(--console-card-border);
      vertical-align: middle;
      color: var(--console-text);
    }

    .admin-table tr:hover td {
      background: rgba(255, 255, 255, 0.02);
    }

    /* Status Badges */
    .badge {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      padding: 0.2rem 0.55rem;
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.2px;
      text-transform: capitalize;
    }

    .badge-success {
      background: rgba(14, 180, 169, 0.15);
      color: #2DD4BF;
      border: 1px solid rgba(14, 180, 169, 0.3);
    }

    .badge-warning {
      background: rgba(245, 158, 11, 0.15);
      color: #FBBF24;
      border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .badge-danger {
      background: rgba(239, 68, 68, 0.15);
      color: #F87171;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .badge-neutral {
      background: rgba(255, 255, 255, 0.08);
      color: var(--console-text-muted);
      border: 1px solid rgba(255, 255, 255, 0.12);
    }

    .badge-info {
      background: rgba(59, 130, 246, 0.15);
      color: #60A5FA;
      border: 1px solid rgba(59, 130, 246, 0.3);
    }

    /* Form Controls */
    .form-group {
      margin-bottom: 1.25rem;
    }

    .form-group label {
      display: block;
      font-weight: 600;
      font-size: 0.825rem;
      margin-bottom: 0.4rem;
      color: var(--console-text);
    }

    .form-control {
      width: 100%;
      padding: 0.65rem 0.85rem;
      border: 1px solid var(--console-card-border);
      border-radius: var(--radius-sm);
      font-size: 0.885rem;
      background: #0B0E14;
      color: #FFFFFF;
      font-family: inherit;
      transition: border-color 0.15s ease;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--brand-orange);
      box-shadow: 0 0 0 2px rgba(255, 98, 35, 0.2);
    }

    .form-help {
      font-size: 0.75rem;
      color: var(--console-text-muted);
      margin-top: 0.35rem;
    }

    /* Monospace Code Pill */
    .code-pill {
      font-family: var(--font-mono);
      font-size: 0.775rem;
      padding: 0.15rem 0.45rem;
      border-radius: 4px;
      background: rgba(0, 0, 0, 0.3);
      border: 1px solid var(--console-card-border);
      color: #38BDF8;
    }

    @media (max-width: 900px) {
      .admin-sidebar {
        width: 68px;
      }
      .admin-brand .brand-meta,
      .admin-brand .brand-badge,
      .admin-nav-heading,
      .admin-nav-item span:not(:first-child),
      .admin-nav-pill {
        display: none;
      }
      .admin-nav-item {
        justify-content: center;
        padding: 0.75rem;
      }
      .admin-topbar {
        padding: 0 1rem;
      }
      .admin-body {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>

  <!-- Enterprise Sidebar -->
  <aside class="admin-sidebar">
    <div class="admin-brand">
      <div class="brand-id">
        <div class="brand-icon-box">M</div>
        <div class="brand-meta">
          <span class="brand-title">MTA Console</span>
          <span class="brand-subtitle">Operations Portal</span>
        </div>
      </div>
      <span class="brand-badge">PROD</span>
    </div>

    <nav class="admin-nav">
      <div class="admin-nav-heading">Operations</div>
      <a href="<?= e($adminPath) ?>" class="admin-nav-item <?= $currentPath === $adminPath ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>
        <span>Telemetry</span>
      </a>
      <a href="<?= e($adminPath) ?>/sync" class="admin-nav-item <?= str_contains($currentPath, '/sync') ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.3"></path></svg>
        <span>Supplier Feed</span>
        <span class="admin-nav-pill">Auto</span>
      </a>

      <div class="admin-nav-heading">Catalog & Fulfillment</div>
      <a href="<?= e($adminPath) ?>/products" class="admin-nav-item <?= str_contains($currentPath, '/products') ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
        <span>Inventory Items</span>
        <span class="admin-nav-pill">991</span>
      </a>
      <a href="<?= e($adminPath) ?>/import" class="admin-nav-item <?= str_contains($currentPath, '/import') ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        <span>Data Ingestion</span>
      </a>
      <a href="<?= e($adminPath) ?>/categories" class="admin-nav-item <?= $currentPath === $adminPath . '/categories' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
        <span>Taxonomy Nodes</span>
      </a>
      <a href="<?= e($adminPath) ?>/categories/mappings" class="admin-nav-item <?= str_contains($currentPath, '/categories/mappings') ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
        <span>Feed Mappings</span>
      </a>
      <a href="<?= e($adminPath) ?>/orders" class="admin-nav-item <?= str_contains($currentPath, '/orders') ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
        <span>Dispatch Orders</span>
      </a>

      <div class="admin-nav-heading">SEO & Automation</div>
      <a href="<?= e($adminPath) ?>/banners" class="admin-nav-item <?= str_contains($currentPath, '/banners') ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
        <span>Billboard Banners</span>
      </a>
      <a href="<?= e($adminPath) ?>/guides" class="admin-nav-item <?= str_contains($currentPath, '/guides') ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <span>Knowledge Guides</span>
      </a>
      <a href="<?= e($adminPath) ?>/queue" class="admin-nav-item <?= str_contains($currentPath, '/queue') ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
        <span>AI Rewriter Pipeline</span>
      </a>
      <a href="<?= e($adminPath) ?>/seo-audit" class="admin-nav-item <?= str_contains($currentPath, '/seo-audit') ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <span>Technical SEO Audit</span>
      </a>

      <div class="admin-nav-heading">Platform</div>
      <a href="<?= e($adminPath) ?>/settings" class="admin-nav-item <?= str_contains($currentPath, '/settings') ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        <span>Environment Config</span>
      </a>
    </nav>
  </aside>

  <!-- Main Console -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div class="admin-breadcrumb">
        <span>Console</span>
        <span style="opacity: 0.4;">/</span>
        <span class="active"><?= e($page_title ?? 'Operations Overview') ?></span>
      </div>

      <div class="admin-topbar-actions">
        <!-- Live Status Pill -->
        <div class="sys-status-badge">
          <span class="status-dot-pulse"></span>
          <span>ONLINE &bull; SQLITE</span>
        </div>

        <!-- Quick Sync Action -->
        <form action="<?= e($adminPath) ?>/sync/trigger" method="POST" style="display:inline;">
          <?= mta_csrf_field() ?>
          <button type="submit" class="btn btn-secondary btn-sm" title="Schedule immediate background supplier sync">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.3"></path></svg>
            <span>Sync Supplier</span>
          </button>
        </form>

        <!-- View Storefront Link -->
        <a href="/" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" title="Open customer-facing storefront in new tab">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
          <span>Storefront &nearr;</span>
        </a>

        <!-- Admin Profile & Logout -->
        <div style="display: flex; align-items: center; gap: 0.65rem; border-left: 1px solid var(--console-card-border); padding-left: 0.85rem;">
          <div style="display: flex; flex-direction: column; text-align: right;">
            <span style="font-size: 0.8rem; font-weight: 700; color: #FFFFFF; line-height: 1.2;">
              <?= e($currentUser['name'] ?? 'Muneeb Ahmad') ?>
            </span>
            <span style="font-size: 0.68rem; color: var(--brand-orange); font-weight: 700; text-transform: uppercase;">
              <?= e($currentUser['role'] ?? 'Owner') ?>
            </span>
          </div>

          <form action="<?= e($adminPath) ?>/logout" method="POST" style="display:inline;">
            <?= mta_csrf_field() ?>
            <button type="submit" class="btn btn-danger btn-sm" title="Sign out of administration console">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
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