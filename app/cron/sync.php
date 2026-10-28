<?php
/**
 * Multan Tech Accessories — Hourly Supplier Synchronization Cron
 * CLI only, flock protected against overlapping runs
 */

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Access denied. CLI only.\n");
}

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Services\SupplierSyncService;

$lockFile = MTA_STORAGE . '/sync.lock';
$fp = fopen($lockFile, 'c+');

if (!$fp || !flock($fp, LOCK_EX | LOCK_NB)) {
    echo "[" . date('Y-m-d H:i:s') . "] Sync job already running. Exiting.\n";
    exit(0);
}

echo "[" . date('Y-m-d H:i:s') . "] Starting Supplier Synchronization...\n";

try {
    $service = new SupplierSyncService();
    $stats = $service->sync();

    echo "Sync Completed:\n";
    echo "  Status: " . $stats['status'] . "\n";
    echo "  Feed Count: " . $stats['feed_count'] . "\n";
    echo "  Imported: " . $stats['imported_count'] . "\n";
    echo "  Updated: " . $stats['updated_count'] . "\n";
    echo "  Unchanged: " . $stats['unchanged_count'] . "\n";
    echo "  Skipped Invalid: " . $stats['skipped_invalid'] . "\n";
    echo "  Deactivated: " . $stats['deactivated_count'] . "\n";

    if (!empty($stats['error_message'])) {
        echo "  Error: " . $stats['error_message'] . "\n";
    }

} catch (\Throwable $e) {
    echo "Fatal Sync Exception: " . $e->getMessage() . "\n";
} finally {
    flock($fp, LOCK_UN);
    fclose($fp);
}
