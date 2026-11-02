<?php
/**
 * Multan Tech Accessories — CSRF Verification Middleware
 * Gracefully handles token expirations, prevents dead-end 419 screens,
 * auto-redirects admin routes to login with pre-filled email and helpful alerts.
 */

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class CsrfMiddleware
{
    public function handle(Request $request): ?Response
    {
        if ($request->isPost()) {
            $token = $request->post('_csrf_token') ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!Session::validateCsrf($token)) {
                $resp = new Response();
                $uri = $request->uri();
                $adminBase = trim((string)mta_config('app.admin_path', mta_setting('admin_path', 'mta-control-9872')), '/');
                $isAdminRequest = str_contains($uri, "/{$adminBase}") || (defined('MTA_ADMIN_DIR') && str_contains($uri, '/admin'));

                // 1. Admin Login or Re-authentication expiration -> smooth redirect to login
                if (str_contains($uri, "/{$adminBase}/login") || str_contains($uri, "/{$adminBase}/reauth")) {
                    $email = trim((string)$request->post('email', ''));
                    Session::regenerate();
                    if (!empty($email)) {
                        Session::flash('old_email', $email);
                    }
                    Session::flash('error', 'Your session timed out. Please enter your password and sign in again.');
                    return $resp->redirect(mta_admin_path('/login'));
                }

                // 2. Any other Admin Route post with expired token -> redirect to login with warning
                if ($isAdminRequest) {
                    Session::regenerate();
                    Session::flash('warning', 'Your administration session expired due to inactivity. Please sign in to continue.');
                    return $resp->redirect(mta_admin_path('/login'));
                }

                // 3. AJAX or JSON request -> structured JSON 419
                if ($request->isAjax() || str_contains((string)($request->header('Accept') ?? ''), 'application/json')) {
                    return $resp->status(419)->json([
                        'status' => 'error',
                        'message' => 'Your session or CSRF token has expired. Please refresh the page and try again.',
                        'csrf_expired' => true,
                    ]);
                }

                // 4. Storefront POST (Cart, Checkout, Contact) -> Branded, responsive recovery page
                Session::regenerate();
                return $resp->status(419)->html(self::renderBrandedExpiredPage(), 419);
            }
        }
        return null;
    }

    private static function renderBrandedExpiredPage(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Session Expired — Multan Tech Accessories</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #FAF9F5; color: #262624; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1.5rem; text-align: center; }
    .card { background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 16px; padding: 2.5rem 2rem; max-width: 460px; width: 100%; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .badge { display: inline-block; background: #FEE2E2; color: #DC2626; font-size: 0.8rem; font-weight: 700; padding: 0.35rem 0.75rem; border-radius: 999px; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.5px; }
    h1 { font-size: 1.5rem; font-weight: 800; color: #111827; margin-bottom: 0.75rem; }
    p { font-size: 0.95rem; color: #4B5563; line-height: 1.5; margin-bottom: 1.75rem; }
    .btn-group { display: flex; flex-direction: column; gap: 0.75rem; }
    .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.85rem 1.5rem; border-radius: 10px; font-weight: 700; font-size: 0.95rem; text-decoration: none; cursor: pointer; border: none; transition: transform 0.15s, opacity 0.15s; }
    .btn-primary { background: #FF6B2C; color: #FFFFFF; }
    .btn-primary:hover { background: #E8501A; transform: translateY(-1px); }
    .btn-secondary { background: #F3F4F6; color: #374151; }
    .btn-secondary:hover { background: #E5E7EB; }
  </style>
</head>
<body>
  <div class="card">
    <div class="badge">Session Expired</div>
    <h1>Security Token Expired</h1>
    <p>Your session timed out to protect your account and data security. A fresh session has been created for you.</p>
    <div class="btn-group">
      <button class="btn btn-primary" onclick="window.location.reload(true);">Refresh & Try Again</button>
      <a class="btn btn-secondary" href="/">Return to Storefront</a>
    </div>
  </div>
</body>
</html>';
    }
}
