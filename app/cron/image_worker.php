<?php
/**
 * Multan Tech Accessories — Image Worker Cron
 * CLI only, processes 40 images per run
 */

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Access denied. CLI only.\n");
}

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Services\ImageProcessor;

$lockFile = MTA_STORAGE . '/image_worker.lock';
$fp = fopen($lockFile, 'c+');

if (!$fp || !flock($fp, LOCK_EX | LOCK_NB)) {
    echo "[" . date('Y-m-d H:i:s') . "] Image worker already running.\n";
    exit(0);
}

try {
    $processor = new ImageProcessor();
    $count = $processor->processBatch(40);
    echo "[" . date('Y-m-d H:i:s') . "] Image worker processed {$count} product images into WebP (400, 800, 1200 px).\n";
} catch (\Throwable $e) {
    echo "Image worker error: " . $e->getMessage() . "\n";
} finally {
    flock($fp, LOCK_UN);
    fclose($fp);
}
