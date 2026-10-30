<?php
/**
 * Multan Tech Accessories — 2FA Enrollment View (Part B2)
 * Pure PHP RFC 6238 TOTP Setup & Backup Codes
 */
$adminPath = mta_admin_path();
?>
<!DOCTYPE html>
<html lang="en-PK">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup 2FA — Multan Tech Accessories</title>
  <meta name="robots" content="noindex, nofollow, noarchive">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16.png">
  <link rel="shortcut icon" href="/favicon.ico">
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
    .setup-container {
      width: 100%;
      max-width: 520px;
    }
    .setup-card {
      background: var(--admin-card);
      border: 1px solid var(--admin-border);
      border-radius: 20px;
      padding: 2.25rem 2rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
      position: relative;
      overflow: hidden;
    }
    .setup-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--mta-teal), var(--mta-orange));
    }
    .brand-header {
      text-align: center;
      margin-bottom: 1.75rem;
    }
    .brand-logo-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.6rem;
      background: rgba(20, 184, 166, 0.1);
      border: 1px solid rgba(20, 184, 166, 0.25);
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
      background: #FFE4E6;
      border: 1px solid #FECDD3;
      color: #9F1239;
    }
    .qr-box {
      text-align: center;
      padding: 1rem;
      background: #FFFFFF;
      border: 1px solid var(--admin-border);
      border-radius: 12px;
      margin-bottom: 1.25rem;
    }
    .qr-box img {
      width: 160px;
      height: 160px;
      border-radius: 8px;
    }
    .secret-code-box {
      background: #F5F3ED;
      border: 1px dashed #DCD8D0;
      padding: 0.75rem 1rem;
      border-radius: 10px;
      font-family: monospace;
      font-size: 1.1rem;
      font-weight: 700;
      letter-spacing: 2px;
      color: var(--admin-text);
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.25rem;
    }
    .backup-codes-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.5rem;
      background: #F5F3ED;
      border: 1px solid var(--admin-border);
      border-radius: 12px;
      padding: 0.75rem 1rem;
      margin-bottom: 1.25rem;
      font-family: monospace;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--admin-text);
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
      height: 48px;
      padding: 0 1rem;
      background: #FFFFFF;
      border: 1px solid var(--admin-border);
      border-radius: 12px;
      color: var(--admin-text);
      font-family: inherit;
      font-size: 1.1rem;
      font-weight: 700;
      letter-spacing: 4px;
      text-align: center;
      outline: none;
    }
    .form-control:focus {
      border-color: var(--mta-teal);
      box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
    }
    .btn-submit {
      width: 100%;
      height: 48px;
      background: linear-gradient(135deg, var(--mta-teal) 0%, #0D9488 100%);
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
  </style>
</head>
<body>

  <div class="setup-container">
    <div class="setup-card">
      <div class="brand-header">
        <div class="brand-logo-pill">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--mta-teal)" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
          <span style="font-size:0.75rem; font-weight:700; color:var(--mta-teal); text-transform:uppercase;">Mandatory 2FA Setup</span>
        </div>
        <h1 class="brand-title">Secure Your Store Admin</h1>
        <div class="brand-subtitle">Two-Factor Authentication is required for the store owner.</div>
      </div>

      <?php if (\App\Core\Session::hasFlash('error')): ?>
        <div class="alert">
          <?= e(\App\Core\Session::getFlash('error')) ?>
        </div>
      <?php endif; ?>

      <div class="qr-box">
        <?php 
          $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=" . urlencode($otpAuthUri);
        ?>
        <img src="<?= e($qrUrl) ?>" alt="2FA QR Code" width="160" height="160">
        <div style="font-size: 0.75rem; color: var(--admin-text-muted); margin-top: 0.5rem;">
          Scan with Google Authenticator, Microsoft Authenticator, or 1Password
        </div>
      </div>

      <div style="font-size:0.8rem; font-weight:600; color:var(--admin-text); margin-bottom:0.35rem;">
        Or enter this manual secret key:
      </div>
      <div class="secret-code-box">
        <span><?= chunk_split(e($secret), 4, ' ') ?></span>
        <button type="button" onclick="navigator.clipboard.writeText('<?= e($secret) ?>'); alert('Secret key copied!');" style="background:none; border:none; color:var(--mta-teal); font-size:0.75rem; font-weight:700; cursor:pointer;">
          Copy
        </button>
      </div>

      <div style="font-size:0.8rem; font-weight:600; color:var(--admin-text); margin-bottom:0.35rem;">
        Single-Use Emergency Backup Codes (Save these now):
      </div>
      <div class="backup-codes-grid">
        <?php foreach ($backupCodes['plain'] as $code): ?>
          <div>&bull; <?= e($code) ?></div>
        <?php endforeach; ?>
      </div>

      <form action="<?= e($adminPath) ?>/2fa/setup" method="POST">
        <?= mta_csrf_field() ?>
        <input type="hidden" name="secret" value="<?= e($secret) ?>">
        <div class="form-group">
          <label for="totp_code">Enter 6-Digit Authenticator Code to Confirm</label>
          <input type="text" id="totp_code" name="totp_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus placeholder="000000" class="form-control">
        </div>

        <button type="submit" class="btn-submit">
          <span>Activate 2FA & Enter Console</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </button>
      </form>
    </div>
  </div>

</body>
</html>

