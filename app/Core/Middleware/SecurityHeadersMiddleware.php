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
            $adminBase = trim((string)mta_config('app.admin_path', mta_setting('admin_path', 'mta-control-9872')), '/');
            $isAdminRequest = str_contains($request->getPath(), $adminBase) || str_contains($request->getPath(), 'control-');

            header('X-Content-Type-Options: nosniff');
            header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
            header('X-MTA-Build: ' . (defined('MTA_BUILD_VERSION') ? MTA_BUILD_VERSION : '2026.10.31-v2.4.0'));

            if ($isAdminRequest) {
                header('X-Robots-Tag: noindex, nofollow, noarchive');
                header('X-Frame-Options: DENY');
                header('Referrer-Policy: no-referrer');
                header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                header('Pragma: no-cache');
            } else {
                header('X-Frame-Options: SAMEORIGIN');
                header('Referrer-Policy: strict-origin-when-cross-origin');
                if ($request->getMethod() === 'GET'
                    && !str_starts_with($request->getPath(), '/cart')
                    && !str_starts_with($request->getPath(), '/checkout')
                    && !str_starts_with($request->getPath(), '/order')
                    && !str_starts_with($request->getPath(), '/track-order')
                    && !str_starts_with($request->getPath(), '/api')) {
                    header('Cache-Control: public, max-age=0, s-maxage=60, stale-while-revalidate=300');
                }
            }

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
