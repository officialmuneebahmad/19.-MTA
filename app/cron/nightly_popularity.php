<?php
/**
 * Multan Tech Accessories — Nightly Popularity Recalculation Cron
 * CLI only, recalculates 30-day popularity scores with decay and pin boost
 */

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Access denied. CLI only.\n");
}

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Services\PopularityService;

$lockFile = MTA_STORAGE . '/nightly_popularity.lock';
$fp = fopen($lockFile, 'c+');

if (!$fp || !flock($fp, LOCK_EX | LOCK_NB)) {
    echo "[" . date('Y-m-d H:i:s') . "] Popularity recalculation already running.\n";
    exit(0);
}

try {
    $service = new PopularityService();
    $count = $service->recalculateAll();
    echo "[" . date('Y-m-d H:i:s') . "] Nightly popularity updated for {$count} products.\n";
} catch (\Throwable $e) {
    echo "Popularity recalculation error: " . $e->getMessage() . "\n";
} finally {
    flock($fp, LOCK_UN);
    fclose($fp);
}
