<?php
/**
 * Multan Tech Accessories — Admin Login View
 */
$adminPath = mta_admin_path();
?>
<!DOCTYPE html>
<html lang="en-PK">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Multan Tech Accessories</title>
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
    .login-card {
      background: #FFFFFF;
      border: 1px solid #E6E4DC;
      border-radius: 12px;
      padding: 2.5rem;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .brand-header {
      text-align: center;
      margin-bottom: 2rem;
    }
    .brand-logo {
      width: 48px;
      height: 48px;
      background: #9B3828;
      color: #fff;
      border-radius: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: 800;
      margin-bottom: 0.75rem;
    }
    .brand-title {
      font-size: 1.35rem;
      font-weight: 800;
      color: #141413;
      margin-bottom: 0.25rem;
    }
    .brand-subtitle {
      font-size: 0.825rem;
      color: #7A7973;
    }
    .form-group {
      margin-bottom: 1.25rem;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      font-size: 0.85rem;
      margin-bottom: 0.35rem;
      color: #333;
    }
    .form-control {
      width: 100%;
      padding: 0.65rem 0.85rem;
      border: 1px solid #D6D5CE;
      border-radius: 6px;
      font-size: 0.925rem;
      box-sizing: border-box;
    }
    .form-control:focus {
      outline: none;
      border-color: #9B3828;
      box-shadow: 0 0 0 3px rgba(155, 56, 40, 0.12);
    }
    .btn-submit {
      width: 100%;
      padding: 0.75rem;
      background: #9B3828;
      color: #FFFFFF;
      border: none;
      border-radius: 6px;
      font-weight: 700;
      font-size: 0.95rem;
      cursor: pointer;
      transition: background 0.15s;
      margin-top: 0.5rem;
    }
    .btn-submit:hover {
      background: #7E2A1D;
    }
    .alert {
      padding: 0.75rem 1rem;
      border-radius: 6px;
      margin-bottom: 1.25rem;
      font-size: 0.85rem;
    }
    .alert-error { background: #FDE8E8; color: #9B1C1C; border: 1px solid #F8B4B4; }
    .alert-success { background: #E8F9EE; color: #0E7064; border: 1px solid #B8EAD1; }
    .alert-warning { background: #FEF08A; color: #854D0E; border: 1px solid #FDE047; }
  </style>
</head>
<body>

  <div class="login-card">
    <div class="brand-header">
      <img src="/assets/images/logo-day.png" alt="Multan Tech Accessories" style="height: 48px; width: auto; object-fit: contain; margin-bottom: 0.85rem; max-width: 220px;">
      <h1 class="brand-title">Multan Tech Accessories</h1>
      <div class="brand-subtitle">Authorized Administrator Control Access</div>
    </div>

    <?php if (\App\Core\Session::hasFlash('error')): ?>
      <div class="alert alert-error"><?= e(\App\Core\Session::getFlash('error')) ?></div>
    <?php endif; ?>
    <?php if (\App\Core\Session::hasFlash('success')): ?>
      <div class="alert alert-success"><?= e(\App\Core\Session::getFlash('success')) ?></div>
    <?php endif; ?>
    <?php if (\App\Core\Session::hasFlash('warning')): ?>
      <div class="alert alert-warning"><?= e(\App\Core\Session::getFlash('warning')) ?></div>
    <?php endif; ?>

    <form action="<?= e($adminPath) ?>/login" method="POST">
      <?= mta_csrf_field() ?>

      <div class="form-group">
        <label for="email">Administrator Email</label>
        <input type="email" id="email" name="email" required autofocus class="form-control" placeholder="admin@multantech.pk">
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required class="form-control" placeholder="••••••••••••••••••••">
      </div>

      <button type="submit" class="btn-submit">
        Secure Sign In &rarr;
      </button>
    </form>
  </div>

</body>
</html>
