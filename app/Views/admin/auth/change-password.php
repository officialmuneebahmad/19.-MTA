<?php
/**
 * Multan Tech Accessories — Change Initial Password View
 */
$adminPath = mta_admin_path();
?>
<!DOCTYPE html>
<html lang="en-PK">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password — Multan Tech Accessories</title>
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
    body {
      font-family: var(--font-sans);
      background: #F4F3EE;
      color: #141413;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      padding: 1.5rem;
      box-sizing: border-box;
    }
    .change-card {
      background: #FFFFFF;
      border: 1px solid #E6E4DC;
      border-radius: 12px;
      padding: 2.5rem;
      width: 100%;
      max-width: 440px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .form-group { margin-bottom: 1.25rem; }
    .form-group label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; }
    .form-control { width: 100%; padding: 0.65rem 0.85rem; border: 1px solid #D6D5CE; border-radius: 6px; box-sizing: border-box; }
    .btn-submit { width: 100%; padding: 0.75rem; background: #9B3828; color: #fff; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; }
    .alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1.25rem; font-size: 0.85rem; }
    .alert-error { background: #FDE8E8; color: #9B1C1C; }
    .alert-warning { background: #FEF08A; color: #854D0E; }
  </style>
</head>
<body>

  <div class="change-card">
    <h1 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 0.5rem;">Update Master Password</h1>
    <p style="font-size: 0.85rem; color: #666; margin-bottom: 1.5rem; line-height: 1.5;">
      Security policy requires setting a personal, strong password (at least 12 characters) to replace the installation key.
    </p>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
      <div class="alert alert-error"><?= e(\App\Core\Session::getFlash('error')) ?></div>
    <?php endif; ?>
    <?php if (\App\Core\Session::hasFlash('warning')): ?>
      <div class="alert alert-warning"><?= e(\App\Core\Session::getFlash('warning')) ?></div>
    <?php endif; ?>

    <form action="<?= e($adminPath) ?>/change-password" method="POST">
      <?= mta_csrf_field() ?>

      <div class="form-group">
        <label for="new_password">New Strong Password (min 12 chars)</label>
        <input type="password" id="new_password" name="new_password" required minlength="12" class="form-control" placeholder="••••••••••••••••">
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="12" class="form-control" placeholder="••••••••••••••••">
      </div>

      <button type="submit" class="btn-submit">
        Save New Password & Continue &rarr;
      </button>
    </form>
  </div>

</body>
</html>
