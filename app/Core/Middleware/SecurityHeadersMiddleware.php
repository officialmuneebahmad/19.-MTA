<?php
/**
 * Multan Tech Accessories — Security Headers Middleware
 */

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Request;
use App\Core\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request): ?Response
    {
        if (!headers_sent()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('Referrer-Policy: strict-origin-when-cross-origin');
            header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');

            $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

            if ($isHttps) {
                header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
            }

            // CSP (Allow self, inlined SVGs/CSS, and Google services when configured)
            $csp = [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' https://www.googletagmanager.com https://pagead2.googlesyndication.com",
                "style-src 'self' 'unsafe-inline'",
                "img-src 'self' data: https: blob:",
                "font-src 'self' data:",
                "connect-src 'self' https://www.google-analytics.com",
                "frame-src 'self' https://googleads.g.doubleclick.net",
                "base-uri 'self'",
                "form-action 'self' https://wa.me",
            ];
            header('Content-Security-Policy: ' . implode('; ', $csp));
        }
        return null;
    }
}
