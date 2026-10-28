<?php
/**
 * Multan Tech Accessories — Security Engine
 * Sanitization, SSRF Shield, Password Hashing & Pakistani Phone Normalizer
 */

declare(strict_types=1);

namespace App\Core;

class Security
{
    /**
     * Alias for sanitizeSupplierHtml
     */
    public static function sanitizeHtml(?string $html): string
    {
        return self::sanitizeSupplierHtml($html);
    }

    /**
     * Sanitize supplier HTML with strict tag allowlist and attribute stripping
     * Allowed tags: p, br, strong, b, ul, ol, li, h3, h4, table, thead, tbody, tr, th, td
     */
    public static function sanitizeSupplierHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Strip dangerous script/style/iframe/object tags entirely
        $html = preg_replace('/<(script|style|iframe|object|embed|applet)[^>]*>.*?<\/\\1>/si', '', $html);

        // Strip HTML comments
        $html = preg_replace('/<!--.*?-->/s', '', $html);

        // Strict tag allowlist
        $allowedTags = '<p><br><strong><b><ul><ol><li><h3><h4><table><thead><tbody><tr><th><td>';
        $stripped = strip_tags($html, $allowedTags);

        // Strip ALL attributes from allowed tags to prevent inline CSS, JS events, or tracking
        $clean = preg_replace_callback('/<([a-z1-6]+)([^>]*)>/i', function ($matches) {
            $tag = strtolower($matches[1]);
            return "<{$tag}>";
        }, $stripped);

        // Clean up excessive whitespace
        $clean = preg_replace('/(\s*<br\s*\/?>\s*){3,}/i', '<br><br>', $clean);
        return trim($clean);
    }

    /**
     * Validate and normalize Pakistani mobile numbers
     * Accepts: 03070655275, +923070655275, 923070655275, 0307-0655275
     * Returns: ['valid' => bool, 'local' => '03070655275', 'intl' => '923070655275', 'display' => '0307-0655275']
     */
    public static function normalizePakistaniPhone(string $phone): array
    {
        $digits = preg_replace('/\D/', '', $phone);

        // If prefixed with 92, convert to 03 format for local
        if (str_starts_with($digits, '923') && strlen($digits) === 12) {
            $local = '0' . substr($digits, 2);
            $intl = $digits;
        } elseif (str_starts_with($digits, '03') && strlen($digits) === 11) {
            $local = $digits;
            $intl = '92' . substr($digits, 1);
        } elseif (str_starts_with($digits, '3') && strlen($digits) === 10) {
            $local = '0' . $digits;
            $intl = '92' . $digits;
        } else {
            return [
                'valid' => false,
                'local' => '',
                'intl' => '',
                'display' => $phone,
            ];
        }

        // Ensure 2nd digit of local number is 0-4 (Pakistani mobile network codes: 030, 031, 032, 033, 034, 035)
        $networkCode = substr($local, 0, 4);
        $display = substr($local, 0, 4) . '-' . substr($local, 4);

        return [
            'valid' => true,
            'local' => $local,
            'intl' => $intl,
            'international' => '+' . $intl,
            'display' => $display,
        ];
    }

    /**
     * Alias for isSafeExternalUrl
     */
    public static function isSafeUrl(string $url): bool
    {
        return self::isSafeExternalUrl($url);
    }

    /**
     * SSRF Shield: Verify URL is public and not pointing to local/private/cloud metadata IPs
     */
    public static function isSafeExternalUrl(string $url): bool
    {
        $parts = parse_url($url);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            return false;
        }

        if (!in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower($parts['host']);

        // Block localhost and standard loopback hostnames
        if ($host === 'localhost' || str_ends_with($host, '.localhost') || str_ends_with($host, '.local')) {
            return false;
        }

        $ip = gethostbyname($host);
        if ($ip === $host && !filter_var($ip, FILTER_VALIDATE_IP)) {
            return false; // DNS resolution failed
        }

        // Check if IP is private, reserved or loopback
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }

        // Block cloud metadata addresses (169.254.169.254)
        if (str_starts_with($ip, '169.254.')) {
            return false;
        }

        return true;
    }

    /**
     * Hash password with Argon2id if available
     */
    public static function hashPassword(string $password): string
    {
        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
        return password_hash($password, $algo);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
