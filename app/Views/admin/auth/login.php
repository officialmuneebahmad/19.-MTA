<?php
/**
 * Multan Tech Accessories — Enterprise Admin Login View
 * Modern Cloud Infrastructure & Store Operations Architecture
 */
$adminPath = mta_admin_path();
?>
<!DOCTYPE html>
<html lang="en-PK">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Operations Console Login — Multan Tech Accessories</title>
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
      --brand-orange: #FF6223;
      --brand-orange-hover: #E54F12;
      --brand-teal: #0EB4A9;
      --console-bg: #0B0E14;
      --console-card: #131823;
      --console-border: #1E2536;
      --console-text: #E6EDF3;
      --console-text-muted: #8B949E;
      --font-console: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: var(--font-console);
      background: var(--console-bg);
      color: var(--console-text);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 1.5rem;
    }
    .login-container {
      width: 100%;
      max-width: 440px;
    }
    .login-card {
      background: var(--console-card);
      border: 1px solid var(--console-border);
      border-radius: 14px;
      padding: 2.25rem 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
      position: relative;
      overflow: hidden;
    }
    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--brand-orange), var(--brand-teal));
    }
    .brand-header {
      text-align: center;
      margin-bottom: 2rem;
    }
    .brand-logo-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background: rgba(255, 98, 35, 0.12);
      border: 1px solid rgba(255, 98, 35, 0.25);
      padding: 0.35rem 0.85rem;
      border-radius: 999px;
      margin-bottom: 1rem;
    }
    .brand-icon {
      width: 22px;
      height: 22px;
      background: var(--brand-orange);
      color: #fff;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 0.8rem;
    }
    .brand-logo-text {
      font-size: 0.8rem;
      font-weight: 700;
      color: var(--brand-orange);
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .brand-title {
      font-size: 1.35rem;
      font-weight: 800;
      color: #FFFFFF;
      margin-bottom: 0.35rem;
      letter-spacing: -0.2px;
    }
    .brand-subtitle {
      font-size: 0.825rem;
      color: var(--console-text-muted);
    }
    .form-group {
      margin-bottom: 1.25rem;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      font-size: 0.825rem;
      margin-bottom: 0.45rem;
      color: var(--console-text);
    }
    .form-control {
      width: 100%;
      padding: 0.75rem 0.95rem;
      border: 1px solid var(--console-border);
      border-radius: 8px;
      font-size: 0.925rem;
      background: #0B0E14;
      color: #FFFFFF;
      font-family: inherit;
      transition: all 0.15s ease;
    }
    .form-control:focus {
      outline: none;
      border-color: var(--brand-orange);
      box-shadow: 0 0 0 2px rgba(255, 98, 35, 0.25);
    }
    .btn-submit {
      width: 100%;
      padding: 0.85rem;
      background: var(--brand-orange);
      color: #FFFFFF;
      border: none;
      border-radius: 8px;
      font-weight: 700;
      font-size: 0.95rem;
      cursor: pointer;
      transition: all 0.2s ease;
      margin-top: 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      box-shadow: 0 4px 12px rgba(255, 98, 35, 0.3);
    }
    .btn-submit:hover {
      background: var(--brand-orange-hover);
      transform: translateY(-1px);
    }
    .alert {
      padding: 0.85rem 1rem;
      border-radius: 8px;
      margin-bottom: 1.25rem;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .alert-error {
      background: rgba(239, 68, 68, 0.15);
      color: #F87171;
      border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .alert-success {
      background: rgba(14, 180, 169, 0.15);
      color: #2DD4BF;
      border: 1px solid rgba(14, 180, 169, 0.3);
    }
    .alert-warning {
      background: rgba(245, 158, 11, 0.15);
      color: #FBBF24;
      border: 1px solid rgba(245, 158, 11, 0.3);
    }
    .credentials-hint {
      margin-top: 1.5rem;
      padding: 0.85rem 1rem;
      border-radius: 8px;
      background: rgba(14, 180, 169, 0.08);
      border: 1px dashed rgba(14, 180, 169, 0.3);
      font-size: 0.775rem;
      color: var(--console-text-muted);
      line-height: 1.5;
    }
    .credentials-hint strong {
      color: var(--brand-teal);
    }
    .back-store {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.825rem;
      color: var(--console-text-muted);
    }
    .back-store a {
      color: var(--brand-orange);
      text-decoration: none;
      font-weight: 600;
    }
    .back-store a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-card">
      <div class="brand-header">
        <div class="brand-logo-pill">
          <div class="brand-icon">M</div>
          <span class="brand-logo-text">Operations Console</span>
        </div>
        <h1 class="brand-title">Multan Tech Accessories</h1>
        <div class="brand-subtitle">Administrator Authentication & Operations Portal</div>
      </div>

      <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert alert-error">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
          <span><?= e(\App\Core\Session::getFlash('error')) ?></span>
        </div>
      <?php endif; ?>

      <?php if (\App\Core\Session::hasFlash('success')): ?>
        <div class="alert alert-success">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
          <span><?= e(\App\Core\Session::getFlash('success')) ?></span>
        </div>
      <?php endif; ?>

      <?php if (\App\Core\Session::hasFlash('warning')): ?>
        <div class="alert alert-warning">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
          <span><?= e(\App\Core\Session::getFlash('warning')) ?></span>
        </div>
      <?php endif; ?>

      <form action="<?= e($adminPath) ?>/login" method="POST">
        <?= mta_csrf_field() ?>

        <div class="form-group">
          <label for="email">Administrator Email</label>
          <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? 'admin@multantech.store') ?>" required autofocus class="form-control" placeholder="admin@multantech.store">
        </div>

        <div class="form-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
            <label for="password" style="margin-bottom: 0;">Password</label>
            <button type="button" onclick="const p=document.getElementById('password'); p.type = p.type==='password'?'text':'password'; this.innerText = p.type==='password'?'Show':'Hide';" style="background:none; border:none; color:var(--brand-teal); font-size:0.75rem; cursor:pointer; font-weight:700;">Show</button>
          </div>
          <input type="password" id="password" name="password" required class="form-control" placeholder="Enter administrator password">
        </div>

        <button type="submit" class="btn-submit">
          <span>Authorize Access</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
      </form>

      <div class="credentials-hint">
        <div><strong>Default Credentials:</strong></div>
        <div>Email: <code>admin@multantech.store</code></div>
        <div>Password: <code>MultanTech@2026!</code></div>
      </div>
    </div>

    <div class="back-store">
      <a href="/" target="_blank">&larr; Return to Customer Storefront</a>
    </div>
  </div>

</body>
</html>