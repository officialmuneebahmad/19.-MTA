<?php
/**
 * Multan Tech Accessories — CSRF Verification Middleware
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
                return $resp->status(419)->html('<h1>419 Page Expired</h1><p>Your session or CSRF token has expired. Please refresh the page and try again.</p><p><a href="javascript:history.back()">&larr; Go Back</a></p>', 419);
            }
        }
        return null;
    }
}
