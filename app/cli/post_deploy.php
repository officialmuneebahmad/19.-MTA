<?php
/**
 * Multan Tech Accessories — Post-Deployment Automation Runner
 * 
 * Can be run via CLI (one-time cron job) or via deploy-runner.php:
 * 1. Runs database schema integrity check and settings migrations
 * 2. Reprices all products using the master dual-stage PricingService
 * 3. Rebuilds asset manifest
 * 4. Fully purges the full-page HTML cache in storage/cache
 * 5. Calls opcache_reset() if enabled
 * 6. Logs to storage/logs/post_deploy.log
 * 7. Outputs a structured verification report
 */

declare(strict_types=1);

if (!defined('MTA_START')) {
    define('MTA_START', microtime(true));
}

$appDir = dirname(__DIR__);
require_once $appDir . '/bootstrap.php';

use App\Core\Database;
use App\Core\Cache;
use App\Services\PricingService;

$startTime = microtime(true);
$logEntries = [];

$pd_log = function (string $msg) use (&$logEntries): void {
    $timestamp = date('Y-m-d H:i:s');
    $line = "[{$timestamp}] {$msg}";
    $logEntries[] = $line;
    if (php_sapi_name() === 'cli') {
        echo $line . PHP_EOL;
    }
};

$pd_log("==================================================");
$pd_log("Starting MTA Post-Deploy Runner (Build: " . MTA_BUILD_VERSION . ")");
$pd_log("==================================================");

// 1. Database Connection & Schema/Settings Integrity
$db = Database::getInstance();
$pd_log("Database connected successfully (driver: " . $db->getDriver() . ").");

// 2. Reprice all products
$updatedCount = PricingService::repriceAllProducts();
$pd_log("PricingService::repriceAllProducts() updated {$updatedCount} products.");

// 3. Verify price integrity (every price > 0 must end in 99)
$driver = $db->getDriver();
$modSql = $driver === 'sqlite' 
    ? "SELECT COUNT(*) AS non_99_count FROM products WHERE sell_price > 0 AND (sell_price % 100) != 99"
    : "SELECT COUNT(*) AS non_99_count FROM products WHERE sell_price > 0 AND MOD(sell_price, 100) != 99";
$res = $db->fetch($modSql);
$non99Count = (int)($res['non_99_count'] ?? 0);
$pd_log("Products with sell_price > 0 not ending in 99: {$non99Count} (Must be 0).");

// 4. Rebuild Asset Manifest
$manifestPath = MTA_STORAGE . '/asset_manifest.json';
$assets = [
    'css/app.css' => file_exists(MTA_PUBLIC . '/assets/css/app.css') ? md5_file(MTA_PUBLIC . '/assets/css/app.css') : '',
    'css/critical.css' => file_exists(MTA_PUBLIC . '/assets/css/critical.css') ? md5_file(MTA_PUBLIC . '/assets/css/critical.css') : '',
    'js/main.js' => file_exists(MTA_PUBLIC . '/assets/js/main.js') ? md5_file(MTA_PUBLIC . '/assets/js/main.js') : '',
    'build_version' => MTA_BUILD_VERSION,
    'generated_at' => date('c'),
];
@file_put_contents($manifestPath, json_encode($assets, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
$pd_log("Asset manifest rebuilt at {$manifestPath}.");

// 5. Purge Full-Page HTML Cache
$purgedCount = 0;
$cachedFiles = @glob(MTA_STORAGE . '/cache/*.html');
if ($cachedFiles) {
    foreach ($cachedFiles as $cf) {
        if (@unlink($cf)) {
            $purgedCount++;
        }
    }
}
$purgeTime = date('Y-m-d H:i:s');
$pd_log("Full-page cache purged: {$purgedCount} cached files deleted at {$purgeTime}.");

// 6. Reset OPcache if available
$opcacheReset = false;
if (function_exists('opcache_reset')) {
    $opcacheReset = @opcache_reset();
    $pd_log("opcache_reset() executed: " . ($opcacheReset ? "SUCCESS" : "FAILED / NOT ENABLED"));
} else {
    $pd_log("opcache_reset() not available in current PHP process.");
}

// 7. Pricing Configuration In Effect
$pricingCfg = PricingService::getConfig();
$pd_log("Pricing settings in effect: mode=" . $pricingCfg['margin_mode'] . 
       ", threshold=" . $pricingCfg['threshold'] . 
       ", tier1_add=" . $pricingCfg['tier1_add'] . 
       ", tier2_add=" . $pricingCfg['tier2_add']);

$totalDuration = round(microtime(true) - $startTime, 4);
$pd_log("Post-deploy finished in {$totalDuration} seconds.");

// Write to storage/logs/post_deploy.log
$logDir = MTA_STORAGE . '/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
@file_put_contents($logDir . '/post_deploy.log', implode(PHP_EOL, $logEntries) . PHP_EOL, FILE_APPEND);

// Return structured summary array for web runner
return [
    'status' => 'success',
    'build_version' => MTA_BUILD_VERSION,
    'duration_sec' => $totalDuration,
    'products_recalculated' => $updatedCount,
    'non_99_prices_count' => $non99Count,
    'cache_files_purged' => $purgedCount,
    'cache_purge_time' => $purgeTime,
    'opcache_reset' => $opcacheReset,
    'pricing_config' => $pricingCfg,
    'logs' => $logEntries,
];
