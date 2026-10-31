<?php
/**
 * Multan Tech Accessories — Master Bootstrap & Helpers
 * MultanTech.store
 */

declare(strict_types=1);

if (!defined('MTA_START')) {
    define('MTA_START', microtime(true));
}
if (!defined('MTA_APP')) {
    define('MTA_APP', __DIR__);
}
if (!defined('MTA_ROOT')) {
    define('MTA_ROOT', dirname(MTA_APP));
}
if (!defined('MTA_PUBLIC')) {
    define('MTA_PUBLIC', is_dir(MTA_ROOT . '/public_html') ? (MTA_ROOT . '/public_html') : MTA_ROOT);
}
if (!defined('MTA_STORAGE')) {
    define('MTA_STORAGE', MTA_APP . '/storage');
}
if (!defined('MTA_BUILD_VERSION')) {
    define('MTA_BUILD_VERSION', '2026.10.31-v2.4.0');
}

// ----------------------------------------------------------------------
// PSR-4 Autoloader
// ----------------------------------------------------------------------
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = MTA_APP . '/';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// ----------------------------------------------------------------------
// Global Configuration Loader
// ----------------------------------------------------------------------
function mta_config(?string $key = null, mixed $default = null): mixed {
    static $config = null;
    if ($config === null) {
        $configFile = MTA_APP . '/config.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
        } elseif (file_exists(MTA_APP . '/config.sample.php')) {
            $config = require MTA_APP . '/config.sample.php';
        } else {
            $config = [];
        }
    }

    if ($key === null) {
        return $config;
    }

    $parts = explode('.', $key);
    $current = $config;
    foreach ($parts as $part) {
        if (!is_array($current) || !array_key_exists($part, $current)) {
            return $default;
        }
        $current = $current[$part];
    }
    return $current;
}

// ----------------------------------------------------------------------
// Output Escaping Helper
// ----------------------------------------------------------------------
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ----------------------------------------------------------------------
// Currency Formatter: Rs. 1,400
// ----------------------------------------------------------------------
function mta_money(float|int|string|null $amount): string {
    $val = (float)($amount ?? 0);
    if ($val <= 0) {
        return 'Rs.&nbsp;0';
    }
    return 'Rs.&nbsp;' . number_format($val, 0);
}

// ----------------------------------------------------------------------
// URL Helper
// ----------------------------------------------------------------------
function mta_url(string $path = ''): string {
    $base = rtrim((string)mta_config('app.url', 'https://multantech.store'), '/');
    if ($path === '' || $path === '/') {
        return $base;
    }
    return $base . '/' . ltrim($path, '/');
}

function mta_admin_path(string $path = ''): string {
    $adminBase = (string)mta_config('app.admin_path', mta_setting('admin_path', 'mta-control-9872'));
    if ($path === '' || $path === '/') {
        return '/' . $adminBase;
    }
    return '/' . $adminBase . '/' . ltrim($path, '/');
}

// ----------------------------------------------------------------------
// Settings Helper (Database Key-Value Store)
// ----------------------------------------------------------------------
function mta_setting(string $key, mixed $default = null, bool $refresh = false): mixed {
    static $cache = null;
    if ($cache === null || $refresh) {
        $cache = [];
        try {
            $db = \App\Core\Database::getInstance();
            $rows = $db->fetchAll("SELECT `key`, `value` FROM `settings`");
            foreach ($rows as $row) {
                $cache[$row['key']] = $row['value'];
            }
        } catch (\Throwable) {
            // DB not ready yet (e.g. during installer)
        }
    }
    return $cache[$key] ?? $default;
}

// ----------------------------------------------------------------------
// CSRF Helpers
// ----------------------------------------------------------------------
function mta_csrf_token(): string {
    return \App\Core\Session::csrfToken();
}

function mta_csrf_field(): string {
    $token = mta_csrf_token();
    return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
}

// ----------------------------------------------------------------------
// WhatsApp Order / Chat Link Builder
// ----------------------------------------------------------------------
function mta_whatsapp_url(string $phone = '923070655275', string $message = ''): string {
    $cleanPhone = preg_replace('/\D/', '', $phone);
    if (empty($cleanPhone)) {
        $cleanPhone = '923070655275';
    }
    $url = 'https://wa.me/' . $cleanPhone;
    if ($message !== '') {
        $url .= '?text=' . rawurlencode($message);
    }
    return $url;
}

// ----------------------------------------------------------------------
// WhatsApp Share Link Builder (Server-Side URL-Encoded Message)
// ----------------------------------------------------------------------
function mta_share_whatsapp_url(string $title, int|float $price, string $cleanUrl): string {
    $priceText = ($price > 0) ? ('Rs. ' . number_format((float)$price, 0)) : 'Price on WhatsApp';
    $shareUrl = $cleanUrl;
    $sep = str_contains($shareUrl, '?') ? '&' : '?';
    $shareUrl .= $sep . 'utm_source=whatsapp&utm_medium=share';

    $text = $title . "\n" . $priceText . "\n" . $shareUrl;
    return 'https://wa.me/?text=' . rawurlencode($text);
}

// ----------------------------------------------------------------------
// Description HTML Cleaner (Removes stray &nbsp;, empty tags, duplicate titles)
// ----------------------------------------------------------------------
function mta_clean_description_html(?string $html, string $productTitle = ''): string {
    if (empty($html)) {
        return '';
    }

    // 1. Remove duplicate headings matching product title
    if ($productTitle !== '') {
        $escapedTitle = preg_quote($productTitle, '/');
        $html = preg_replace('/<h[1-6][^>]*>\s*' . $escapedTitle . '\s*<\/h[1-6]>/i', '', $html);
    }

    // 2. Replace excessive &nbsp; runs with standard space
    $html = preg_replace('/(&nbsp;|\x{00A0})+/u', ' ', $html);

    // 3. Remove empty paragraphs or spans
    $html = preg_replace('/<p[^>]*>(\s|&nbsp;|<br\s*\/?>)*<\/p>/i', '', $html);
    $html = preg_replace('/<span[^>]*>(\s|&nbsp;)*<\/span>/i', '', $html);

    // 4. Ensure tables have specs-table class
    $html = str_replace('<table', '<table class="specs-table"', $html);

    // 5. Clean up excessive line breaks
    $html = preg_replace('/(<br\s*\/?>\s*){3,}/i', '<br><br>', $html);

    return trim($html);
}

// ----------------------------------------------------------------------
// Category-Specific SVG Icons Helper
// ----------------------------------------------------------------------
function mta_category_icon(string $slug): string {
    $slug = strtolower(trim($slug));
    return match ($slug) {
        'mobile-accessories' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>',
        'computer-accessories' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>',
        'audio-and-speakers' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>',
        'gaming' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="12" x2="10" y2="12"></line><line x1="8" y1="10" x2="8" y2="14"></line><line x1="15" y1="13" x2="15.01" y2="13"></line><line x1="18" y1="11" x2="18.01" y2="11"></line><rect x="2" y="6" width="20" height="12" rx="6"></rect></svg>',
        'power-and-charging' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>',
        'storage-and-memory' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h12v6H6z"></path><path d="M4 8h16v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8z"></path><line x1="10" y1="12" x2="14" y2="12"></line><line x1="9" y1="5" x2="9" y2="6"></line><line x1="15" y1="5" x2="15" y2="6"></line></svg>',
        'networking' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>',
        'cables-and-adapters' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="2" width="10" height="7" rx="1"></rect><rect x="9" y="9" width="6" height="7" rx="1"></rect><line x1="12" y1="16" x2="12" y2="22"></line><line x1="10" y1="4" x2="10" y2="6"></line><line x1="14" y1="4" x2="14" y2="6"></line></svg>',
        'smart-and-cctv' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>',
        default => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>',
    };
}

// ----------------------------------------------------------------------
// Verified Brand Logo Validator Helper
// ----------------------------------------------------------------------
function mta_verify_brand_logo(?string $relPath): array {
    static $cache = [];
    if (empty($relPath)) {
        return ['status' => 'missing', 'is_dark_needed' => false];
    }
    if (isset($cache[$relPath])) {
        return $cache[$relPath];
    }

    $fullPath = MTA_ROOT . $relPath;
    if (!file_exists($fullPath) || !is_file($fullPath)) {
        return $cache[$relPath] = ['status' => 'missing', 'is_dark_needed' => false];
    }
    $size = @filesize($fullPath);
    if ($size < 50) {
        return $cache[$relPath] = ['status' => 'broken', 'is_dark_needed' => false];
    }

    $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    if ($ext === 'svg') {
        $content = @file_get_contents($fullPath);
        if ($content === false || stripos($content, '<svg') === false || stripos($content, 'Wikimedia Error') !== false || stripos($content, 'xmlns=') === false) {
            return $cache[$relPath] = ['status' => 'broken', 'is_dark_needed' => false];
        }
        $isDark = (stripos($content, 'fill="#fff"') !== false || stripos($content, 'fill="#ffffff"') !== false || stripos($content, 'fill="white"') !== false);
        return $cache[$relPath] = ['status' => 'ok', 'is_dark_needed' => $isDark];
    }

    if (function_exists('imagecreatefromwebp') && $ext === 'webp') {
        $img = @imagecreatefromwebp($fullPath);
    } elseif (function_exists('imagecreatefrompng') && $ext === 'png') {
        $img = @imagecreatefrompng($fullPath);
    } else {
        $img = @imagecreatefromstring(@file_get_contents($fullPath));
    }

    if (!$img) {
        return $cache[$relPath] = ['status' => 'broken', 'is_dark_needed' => false];
    }

    $w = imagesx($img);
    $h = imagesy($img);
    if ($w < 10 || $h < 10) {
        imagedestroy($img);
        return $cache[$relPath] = ['status' => 'broken', 'is_dark_needed' => false];
    }

    $samples = 0;
    $whiteSamples = 0;
    $stepX = max(1, (int)($w / 15));
    $stepY = max(1, (int)($h / 15));
    for ($y = 0; $y < $h; $y += $stepY) {
        for ($x = 0; $x < $w; $x += $stepX) {
            $rgba = imagecolorat($img, $x, $y);
            $alpha = ($rgba >> 24) & 0x7F;
            if ($alpha < 100) {
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;
                $samples++;
                if ($r > 240 && $g > 240 && $b > 240) {
                    $whiteSamples++;
                }
            }
        }
    }
    imagedestroy($img);
    $isDark = ($samples > 0 && ($whiteSamples / $samples) > 0.85);

    return $cache[$relPath] = ['status' => 'ok', 'is_dark_needed' => $isDark];
}
