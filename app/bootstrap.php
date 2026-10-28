<?php
/**
 * Multan Tech Accessories — Master Bootstrap & Helpers
 * MultanTech.store
 */

declare(strict_types=1);

if (!defined('MTA_START')) {
    define('MTA_START', microtime(true));
}
if (!defined('MTA_ROOT')) {
    define('MTA_ROOT', dirname(__DIR__));
}
if (!defined('MTA_APP')) {
    define('MTA_APP', MTA_ROOT . '/app');
}
if (!defined('MTA_PUBLIC')) {
    define('MTA_PUBLIC', MTA_ROOT . '/public_html');
}
if (!defined('MTA_STORAGE')) {
    define('MTA_STORAGE', MTA_APP . '/storage');
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
        return 'Rs. 0';
    }
    return 'Rs. ' . number_format($val, 0);
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
function mta_setting(string $key, mixed $default = null): mixed {
    static $cache = null;
    if ($cache === null) {
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
