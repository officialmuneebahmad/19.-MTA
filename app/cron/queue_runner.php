<?php
/**
 * Multan Tech Accessories — 1-Minute Queue Runner Cron
 * Checks for admin "Sync now" trigger rows and queued background jobs
 */

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Access denied. CLI only.\n");
}

require_once dirname(__DIR__) . '/bootstrap.php';

use App\Core\Database;
use App\Services\SupplierSyncService;

$lockFile = MTA_STORAGE . '/queue_runner.lock';
$fp = fopen($lockFile, 'c+');

if (!$fp || !flock($fp, LOCK_EX | LOCK_NB)) {
    exit(0);
}

try {
    $db = Database::getInstance();
    
    // Check if an admin requested a sync now
    $pendingSync = $db->fetch("SELECT value FROM settings WHERE key = 'request_sync_now' AND value = '1' LIMIT 1");
    if ($pendingSync) {
        $db->execute("UPDATE settings SET value = '0' WHERE key = 'request_sync_now'");
        echo "[" . date('Y-m-d H:i:s') . "] Admin requested manual sync. Starting...\n";
        $syncService = new SupplierSyncService($db);
        $res = $syncService->sync();
        echo "Manual sync finished with status: {$res['status']}\n";
    }
} catch (\Throwable $e) {
    echo "Queue runner error: " . $e->getMessage() . "\n";
} finally {
    flock($fp, LOCK_UN);
    fclose($fp);
}
