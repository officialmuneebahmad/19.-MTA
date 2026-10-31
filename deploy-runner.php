<?php
/**
 * Multan Tech Accessories — Web Post-Deploy Runner
 * Triggers database migrations, product repricing, cache purging, and Web OPcache reset.
 * 
 * Protected by secret token: ?token=mta_deploy_2026_secret_9872
 */

declare(strict_types=1);

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$expectedToken = 'mta_deploy_2026_secret_9872';

if (!hash_equals($expectedToken, (string)$token)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "403 Forbidden: Invalid deployment token.";
    exit;
}

// Set execution limits for full catalog re-pricing
@set_time_limit(180);
@ini_set('memory_limit', '256M');

// Execute CLI post-deploy script and capture return
$result = require __DIR__ . '/app/cli/post_deploy.php';

// In web server context, reset OPcache
if (function_exists('opcache_reset')) {
    $webOpcache = @opcache_reset();
    if (is_array($result)) {
        $result['web_opcache_reset'] = $webOpcache;
    }
}

if (isset($_GET['unlink']) && $_GET['unlink'] === '1') {
    @unlink(__FILE__);
}

header('Content-Type: application/json; charset=UTF-8');
header('X-MTA-Build: ' . (defined('MTA_BUILD_VERSION') ? MTA_BUILD_VERSION : '2026.10.31-v2.4.0'));
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

