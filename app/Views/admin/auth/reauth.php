<?php
/**
 * Multan Tech Accessories — Administrative Re-Authentication View (Part B2)
 * Re-authenticates sensitive operations (Export, Backups, Settings, Deletions)
 */
$adminPath = mta_admin_path();
?>
<!DOCTYPE html>
<html lang="en-PK">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Security Re-Authentication — Multan Tech Accessories</title>
  <meta name="robots" content="noindex, nofollow, noarchive">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16.png">
  <link rel="shortcut icon" href="/favicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --mta-orange: #FF6B2C;
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
    .reauth-container {
      width: 100%;
      max-width: 440px;
    }
    .reauth-card {
      background: var(--admin-card);
      border: 1px solid var(--admin-border);
      border-radius: 20px;
      padding: 2.25rem 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
      position: relative;
      overflow: hidden;
    }
    .reauth-card::before {
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
      margin-bottom: 1.75rem;
    }
    .brand-logo-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background: rgba(255, 107, 44, 0.1);
      border: 1px solid rgba(255, 107, 44, 0.25);
      padding: 0.35rem 0.85rem;
      border-radius: 9999px;
      margin-bottom: 0.75rem;
    }
    .brand-title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--admin-text);
      line-height: 1.2;
    }
    .brand-subtitle {
      font-size: 0.85rem;
      color: var(--admin-text-muted);
      margin-top: 0.35rem;
    }
    .alert {
      padding: 0.75rem 1rem;
      border-radius: 12px;
      font-size: 0.85rem;
      margin-bottom: 1.25rem;
      background: #FEF3C7;
      border: 1px solid #FCD34D;
      color: #92400E;
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
      font-size: 0.95rem;
      outline: none;
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
      margin-top: 1.25rem;
    }
    .cancel-link {
      text-align: center;
      margin-top: 1.25rem;
      font-size: 0.85rem;
    }
    .cancel-link a {
      color: var(--admin-text-muted);
      text-decoration: none;
    }
  </style>
</head>
<body>

  <div class="reauth-container">
    <div class="reauth-card">
      <div class="brand-header">
        <div class="brand-logo-pill">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--mta-orange)" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
          <span style="font-size:0.75rem; font-weight:700; color:var(--mta-orange); text-transform:uppercase;">Security Check</span>
        </div>
        <h1 class="brand-title">Confirm Identity</h1>
        <div class="brand-subtitle">Re-authenticate with your administrator password to proceed.</div>
      </div>

      <?php if (\App\Core\Session::hasFlash('warning')): ?>
        <div class="alert">
          <?= e(\App\Core\Session::getFlash('warning')) ?>
        </div>
      <?php endif; ?>

      <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert" style="background:#FFE4E6; border-color:#FECDD3; color:#9F1239;">
          <?= e(\App\Core\Session::getFlash('error')) ?>
        </div>
      <?php endif; ?>

      <form action="<?= e($adminPath) ?>/reauth" method="POST">
        <?= mta_csrf_field() ?>
        <input type="hidden" name="return_to" value="<?= e($returnTo) ?>">

        <div class="form-group">
          <label for="password">Administrator Password</label>
          <input type="password" id="password" name="password" autofocus required placeholder="Enter your password" class="form-control">
        </div>

        <button type="submit" class="btn-submit">
          <span>Authorize & Proceed</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </button>
      </form>

      <div class="cancel-link">
        <a href="<?= e($adminPath) ?>">&larr; Return to Dashboard</a>
      </div>
    </div>
  </div>

</body>
</html>

