<?php
/**
 * Multan Tech Accessories — Background Content Worker
 * Consumes items from ai_queue and generates rich Pakistan-tailored copy
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use App\Core\Database;
use App\Services\AiRewriteService;

$lockFile = MTA_STORAGE . '/content_worker.lock';
$fp = fopen($lockFile, 'c');
if (!flock($fp, LOCK_EX | LOCK_NB)) {
    echo "Content worker is already running. Exiting.\n";
    exit(0);
}

try {
    $db = Database::getInstance();
    $service = new AiRewriteService($db);

    $items = $db->fetchAll(
        "SELECT * FROM ai_queue WHERE status = 'pending' ORDER BY id ASC LIMIT 10"
    );

    if (empty($items)) {
        echo "No pending AI queue tasks.\n";
        exit(0);
    }

    echo "Processing " . count($items) . " AI rewrite tasks...\n";

    foreach ($items as $item) {
        $db->execute(
            "UPDATE ai_queue SET status = 'processing', updated_at = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $item['id']]
        );

        $res = $service->rewriteProduct((int)$item['product_id']);

        if ($res['success']) {
            $db->execute(
                "UPDATE ai_queue SET status = 'completed', updated_at = ? WHERE id = ?",
                [date('Y-m-d H:i:s'), $item['id']]
            );
            echo "Product #{$item['product_id']} rewritten successfully -> ai_draft\n";
        } else {
            $db->execute(
                "UPDATE ai_queue SET status = 'failed', error_message = ?, updated_at = ? WHERE id = ?",
                [$res['error'] ?? 'Rewrite error', date('Y-m-d H:i:s'), $item['id']]
            );
            echo "Product #{$item['product_id']} failed: " . ($res['error'] ?? '') . "\n";
        }
    }

    echo "Content worker finished successfully.\n";
} finally {
    flock($fp, LOCK_UN);
    fclose($fp);
}
