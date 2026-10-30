<?php
/**
 * Multan Tech Accessories — Master Admin Login View (Part B)
 * MTA Palette: Primary Orange #FF6B2C, Teal #14B8A6, Cream #FAF9F5, Dark Charcoal #262624
 */
$adminPath = mta_admin_path();
?>
<!DOCTYPE html>
<html lang="en-PK">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Operations Console Login — Multan Tech Accessories</title>
  <meta name="robots" content="noindex, nofollow, noarchive">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16.png">
  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --mta-orange: #FF6B2C;
      --mta-orange-hover: #E8501A;
      --mta-teal: #14B8A6;
      --admin-bg: #FAF9F5;
      --admin-card: #FFFFFF;
      --admin-border: #EFECE6;
      --admin-text: #262624;
      --admin-text-muted: #6B7280;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background: var(--admin-bg);
      color: var(--admin-text);
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
      background: var(--admin-card);
      border: 1px solid var(--admin-border);
      border-radius: 20px;
      padding: 2.5rem 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
      position: relative;
      overflow: hidden;
    }
    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--mta-orange), var(--mta-teal));
    }
    .brand-header {
      text-align: center;
      margin-bottom: 2rem;
    }
    .brand-logo-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background: rgba(255, 107, 44, 0.1);
      border: 1px solid rgba(255, 107, 44, 0.2);
      padding: 0.4rem 0.9rem;
      border-radius: 9999px;
      margin-bottom: 1rem;
    }
    .brand-icon {
      width: 24px;
      height: 24px;
      background: linear-gradient(135deg, var(--mta-orange) 0%, var(--mta-orange-hover) 100%);
      color: #fff;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 0.85rem;
    }
    .brand-logo-text {
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--mta-orange);
      letter-spacing: 0.6px;
      text-transform: uppercase;
    }
    .brand-title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.5rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      color: var(--admin-text);
      line-height: 1.2;
    }
    .brand-subtitle {
      font-size: 0.85rem;
      color: var(--admin-text-muted);
      margin-top: 0.4rem;
    }
    .alert {
      padding: 0.85rem 1rem;
      border-radius: 12px;
      font-size: 0.85rem;
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      line-height: 1.4;
    }
    .alert-error {
      background: #FFE4E6;
      border: 1px solid #FECDD3;
      color: #9F1239;
    }
    .alert-success {
      background: #D1FAE5;
      border: 1px solid #A7F3D0;
      color: #065F46;
    }
    .alert-warning {
      background: #FEF3C7;
      border: 1px solid #FCD34D;
      color: #92400E;
    }
    .form-group {
      margin-bottom: 1.25rem;
    }
    .form-group label {
      display: block;
      font-size: 0.8rem;
      font-weight: 600;
      margin-bottom: 0.45rem;
      color: var(--admin-text);
    }
    .form-control {
      width: 100%;
      height: 46px;
      padding: 0 1rem;
      background: #FFFFFF;
      border: 1px solid var(--admin-border);
      border-radius: 12px;
      color: var(--admin-text);
      font-family: inherit;
      font-size: 0.9rem;
      outline: none;
      transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-control:focus {
      border-color: var(--mta-orange);
      box-shadow: 0 0 0 3px rgba(255, 107, 44, 0.15);
    }
    .btn-submit {
      width: 100%;
      height: 48px;
      background: linear-gradient(135deg, var(--mta-orange) 0%, var(--mta-orange-hover) 100%);
      color: #FFFFFF;
      border: none;
      border-radius: 12px;
      font-family: inherit;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      margin-top: 1.5rem;
      box-shadow: 0 4px 12px rgba(255, 107, 44, 0.25);
      transition: opacity 0.15s, transform 0.15s;
    }
    .btn-submit:hover {
      opacity: 0.95;
      transform: translateY(-1px);
    }
    .credentials-hint {
      margin-top: 1.5rem;
      padding: 1rem;
      background: #F5F3ED;
      border-radius: 12px;
      border: 1px dashed #DCD8D0;
      font-size: 0.8rem;
      color: var(--admin-text-muted);
      line-height: 1.6;
    }
    .credentials-hint code {
      background: #FFFFFF;
      padding: 0.15rem 0.45rem;
      border-radius: 6px;
      border: 1px solid var(--admin-border);
      font-weight: 600;
      color: var(--admin-text);
    }
    .back-store {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.85rem;
      color: var(--admin-text-muted);
    }
    .back-store a {
      color: var(--mta-orange);
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
            <button type="button" onclick="const p=document.getElementById('password'); p.type = p.type==='password'?'text':'password'; this.innerText = p.type==='password'?'Show':'Hide';" style="background:none; border:none; color:var(--mta-teal); font-size:0.75rem; cursor:pointer; font-weight:700;">Show</button>
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
      <a href="/" target="_blank" rel="noopener noreferrer">&larr; Return to Customer Storefront</a>
    </div>
  </div>

</body>
</html>
