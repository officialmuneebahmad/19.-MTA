<?php
/**
 * Multan Tech Accessories — Live Supplier API Diagnostic & Immediate Seeder
 * Access via: https://multantech.store/api-check.php
 */

declare(strict_types=1);

// Error reporting for diagnostic visibility
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Root path detection
$appRoot = is_dir(__DIR__ . '/app') ? __DIR__ : dirname(__DIR__);
require_once $appRoot . '/app/bootstrap.php';

use App\Core\Database;
use App\Services\SupplierSyncService;

$token = 'f1f3b0294c3f7c2328e41d42e59d2877';
$apiUrl = 'https://api.computerzone.pk/products';
$adminPath = trim(mta_admin_path(), '/');

$message = null;
$messageType = 'info';

// -------------------------------------------------------------
// Handle Database Actions
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['action'])) {
    $db = Database::getInstance();

    if ($_POST['action'] === 'purge_and_sync') {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        try {
            // Force update database settings to active registered token
            $db->execute("UPDATE settings SET value = ? WHERE key = 'supplier_token'", [$token]);
            $db->execute("UPDATE settings SET value = ? WHERE key = 'supplier_api_url'", [$apiUrl]);
            $db->execute("UPDATE settings SET value = 'raw' WHERE key = 'supplier_auth_header_format'");

            // 1. Wipe all existing products
            $db->execute("DELETE FROM products");
            if ($db->getDriver() === 'sqlite') {
                try { $db->execute("DELETE FROM sqlite_sequence WHERE name='products'"); } catch (Throwable) {}
            }
            try { $db->execute("UPDATE categories SET product_count = 0"); } catch (Throwable) {}

            // Flush cache immediately
            \App\Core\Cache::flush();

            // 2. Fetch fresh live feed strictly from Computer Zone API
            $syncService = new SupplierSyncService();
            $result = $syncService->run($apiUrl, $token);
            if ($result['status'] === 'success') {
                $db->execute("UPDATE products SET is_active = 1, is_published = 1 WHERE is_active = 1");
                $syncService->boostConsumerPopularity();
                \App\Core\Cache::flush();

                $message = "SUCCESS! All old products removed. {$result['feed_count']} genuine Computer Zone products imported directly from the live API! Storefront cache cleared.";
                $messageType = 'success';
            } else {
                $message = "Database cleared, but live Computer Zone sync returned: " . ($result['error_message'] ?? 'Unknown error');
                $messageType = 'error';
            }
        } catch (Throwable $e) {
            $message = "Exception during purge & sync: " . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($_POST['action'] === 'purge_all_products') {
        try {
            $db->execute("DELETE FROM products");
            if ($db->getDriver() === 'sqlite') {
                try { $db->execute("DELETE FROM sqlite_sequence WHERE name='products'"); } catch (Throwable) {}
            }
            try { $db->execute("UPDATE categories SET product_count = 0"); } catch (Throwable) {}
            \App\Core\Cache::flush();
            $message = "SUCCESS: All products have been deleted. Database and storefront cache are now completely clean.";
            $messageType = 'success';
        } catch (Throwable $e) {
            $message = "Error cleaning database: " . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($_POST['action'] === 'recalculate_all_prices') {
        @set_time_limit(300);
        try {
            $syncService = new SupplierSyncService();
            $rows = $db->fetchAll("SELECT id, supplier_price, price_override FROM products");
            $updated = 0;
            foreach ($rows as $r) {
                if (!empty($r['price_override']) && (float)$r['price_override'] > 0) {
                    $newPrice = (float)$r['price_override'];
                } else {
                    $newPrice = $syncService->calculateSellingPrice((float)$r['supplier_price']);
                }
                $db->execute("UPDATE products SET sell_price = ? WHERE id = ?", [$newPrice, $r['id']]);
                $updated++;
            }
            $syncService->boostConsumerPopularity();
            \App\Core\Cache::flush();
            $message = "SUCCESS! Recalculated prices for all {$updated} products according to the exact tiered formula (<3000: +100 round+99, >=3000: +300 round+99) and updated Best Sellers popularity rankings. Cache flushed.";
            $messageType = 'success';
        } catch (Throwable $e) {
            $message = "Error recalculating prices: " . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($_POST['action'] === 'run_live_sync') {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        try {
            // Force update database settings to active registered token
            $db->execute("UPDATE settings SET value = ? WHERE key = 'supplier_token'", [$token]);
            $db->execute("UPDATE settings SET value = ? WHERE key = 'supplier_api_url'", [$apiUrl]);
            $db->execute("UPDATE settings SET value = 'raw' WHERE key = 'supplier_auth_header_format'");

            $syncService = new SupplierSyncService();
            $result = $syncService->run($apiUrl, $token);
            if ($result['status'] === 'success') {
                $db->execute("UPDATE products SET is_active = 1, is_published = 1 WHERE is_active = 1");
                $syncService->boostConsumerPopularity();
                \App\Core\Cache::flush();
                $message = "SUCCESS! Live sync completed with {$result['feed_count']} products imported/updated from Computer Zone.";
                $messageType = 'success';
            } else {
                $message = "Live sync returned error: " . ($result['error_message'] ?? 'Unknown error');
                $messageType = 'error';
            }
        } catch (Throwable $e) {
            $message = "Live sync exception: " . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($_POST['action'] === 'reset_admin_password') {
        try {
            $adminEmail = 'admin@multantech.store';
            $hash = password_hash('MultanTech@2026!', PASSWORD_DEFAULT);
            $existing = $db->fetch("SELECT id FROM users WHERE email = ?", [$adminEmail]);
            if ($existing) {
                $db->execute("UPDATE users SET password_hash = ?, failed_logins = 0, locked_until = NULL, must_change_password = 0 WHERE id = ?", [$hash, $existing['id']]);
            } else {
                $db->execute("INSERT INTO users (name, email, password_hash, role, must_change_password) VALUES (?, ?, ?, 'owner', 0)", ['Muneeb Ahmad', $adminEmail, $hash]);
            }
            $message = "SUCCESS! Admin credentials verified / reset. Login: admin@multantech.store / Password: MultanTech@2026!";
            $messageType = 'success';
        } catch (Throwable $e) {
            $message = "Error configuring admin user: " . $e->getMessage();
            $messageType = 'error';
        }
    } elseif ($_POST['action'] === 'git_pull_update') {
        try {
            $output = [];
            $code = 0;
            exec('git pull origin master 2>&1', $output, $code);
            \App\Core\Cache::flush();
            $log = implode("\n", $output);
            if ($code === 0) {
                $message = "SUCCESS! Website updated from GitHub:\n" . $log;
                $messageType = 'success';
            } else {
                $message = "Git Pull Output (Return Code: {$code}):\n" . $log . "\n(If exec() is disabled in PHP or Git needs cPanel credentials, update via cPanel Git Version Control).";
                $messageType = 'warning';
            }
        } catch (Throwable $e) {
            $message = "Error running git pull: " . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Ensure default owner admin exists
try {
    $db = Database::getInstance();
    $adminEmail = 'admin@multantech.store';
    $existing = $db->fetch("SELECT id FROM users WHERE email = ?", [$adminEmail]);
    if (!$existing) {
        $db->execute("INSERT INTO users (name, email, password_hash, role, must_change_password) VALUES (?, ?, ?, 'owner', 0)", ['Muneeb Ahmad', $adminEmail, password_hash('MultanTech@2026!', PASSWORD_DEFAULT)]);
    }
} catch (Throwable) {}

// -------------------------------------------------------------
// 1. Check Server Outbound IP
// -------------------------------------------------------------
$outboundIp = 'Unknown';
if (function_exists('curl_init')) {
    try {
        $ch = curl_init('https://api.ipify.org');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        if ($res) $outboundIp = trim((string)$res);
        curl_close($ch);
    } catch (Throwable) {}
}

// -------------------------------------------------------------
// 2. Perform API Handshake Tests with Token Variations
// -------------------------------------------------------------
function testCurl(string $url, array $headers = []): array {
    if (!function_exists('curl_init')) {
        return [
            'code' => 0,
            'duration' => 0,
            'error' => 'cURL extension is not enabled in this PHP environment.',
            'body' => '',
        ];
    }
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $start = microtime(true);
    $body = curl_exec($ch);
    $duration = round(microtime(true) - $start, 3);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'duration' => $duration,
        'error' => $error,
        'body' => $body ?: '',
    ];
}

$tests = [
    'Test A1: Raw Authorization Header with Accept: */*' => [
        'url' => $apiUrl,
        'headers' => [
            "Authorization: {$token}",
            "Accept: */*",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
        ],
    ],
    'Test A2: Raw Authorization Header (No Accept Header)' => [
        'url' => $apiUrl,
        'headers' => [
            "Authorization: {$token}",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
        ],
    ],
    'Test B1: Bearer Header with Accept: */*' => [
        'url' => $apiUrl,
        'headers' => [
            "Authorization: Bearer {$token}",
            "Accept: */*",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
        ],
    ],
    'Test C1: URL Query Parameter (?token=...) with Accept: */*' => [
        'url' => $apiUrl . '?token=' . $token,
        'headers' => [
            "Accept: */*",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
        ],
    ],
    'Test D: X-API-KEY Header' => [
        'url' => $apiUrl,
        'headers' => [
            "X-API-KEY: {$token}",
            "Accept: */*",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
        ],
    ],
];

$testResults = [];
foreach ($tests as $title => $cfg) {
    $testResults[$title] = testCurl($cfg['url'], $cfg['headers']);
}

// -------------------------------------------------------------
// 3. Current Database Stats & Schema Verification
// -------------------------------------------------------------
$db = Database::getInstance();

// Auto-migrate stock_status if absent in SQLite/MySQL
try {
    if ($db->getDriver() === 'sqlite') {
        $cols = $db->fetchAll("PRAGMA table_info(products)");
        $colNames = array_column($cols, 'name');
        if (!empty($colNames) && !in_array('stock_status', $colNames, true)) {
            $db->getPdo()->exec("ALTER TABLE products ADD COLUMN stock_status TEXT NOT NULL DEFAULT 'in_stock'");
        }
    } elseif ($db->getDriver() === 'mysql') {
        $check = $db->fetch("SHOW COLUMNS FROM products LIKE 'stock_status'");
        if (!$check) {
            $db->getPdo()->exec("ALTER TABLE products ADD COLUMN stock_status VARCHAR(20) NOT NULL DEFAULT 'in_stock'");
        }
    }
} catch (Throwable $e) {}

$productCount = 0;
$activeProductCount = 0;
$categoryCount = 0;
$brandCount = 0;
$sampleProducts = [];

try {
    $productCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM products");
    $activeProductCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM products WHERE is_active = 1");
    $categoryCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM categories");
    $brandCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM brands");
    $sampleProducts = $db->fetchAll("SELECT id, title, sell_price, stock_status, is_active FROM products ORDER BY id DESC LIMIT 5");
} catch (Throwable $e) {
    try {
        $sampleProducts = $db->fetchAll("SELECT id, title, sell_price, is_active FROM products ORDER BY id DESC LIMIT 5");
    } catch (Throwable $e2) {
        $sampleProducts = [];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>API Diagnostics & Product Seeder — Multan Tech Accessories</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #FAF9F5; color: #141413; padding: 2rem 1rem; line-height: 1.5; }
    .container { max-width: 960px; margin: 0 auto; }
    .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; border-bottom: 2px solid #E8E6DC; padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem; }
    h1 { font-size: 1.6rem; color: #141413; font-weight: 800; }
    .brand-tag { background: #FF6223; color: #fff; font-size: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 999px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .card { background: #FFFFFF; border: 1px solid #E8E6DC; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .card-title { font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
    .metric { background: #F9F8F5; border: 1px solid #E8E6DC; padding: 1rem; border-radius: 8px; }
    .metric-label { font-size: 0.75rem; text-transform: uppercase; color: #5E5D59; font-weight: 700; }
    .metric-value { font-size: 1.5rem; font-weight: 800; color: #141413; margin-top: 0.25rem; }
    .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 700; font-size: 0.95rem; cursor: pointer; text-decoration: none; border: none; transition: transform 0.15s, opacity 0.15s; }
    .btn:hover { transform: translateY(-1px); opacity: 0.95; }
    .btn-orange { background: #FF6223; color: #FFFFFF; }
    .btn-teal { background: #0EB4A9; color: #FFFFFF; }
    .alert { padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 600; font-size: 0.95rem; }
    .alert-success { background: #E6F8F6; color: #0E7064; border: 1px solid #A7F3D0; }
    .alert-error { background: #FDE8E8; color: #9B1C1C; border: 1px solid #F8B4B4; }
    .test-box { border: 1px solid #E8E6DC; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; background: #FAFAFA; }
    .badge { display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; }
    .badge-pass { background: #E6F8F6; color: #0E7064; }
    .badge-fail { background: #FDE8E8; color: #9B1C1C; }
    pre { background: #141413; color: #FAF9F5; padding: 0.75rem; border-radius: 6px; font-size: 0.8rem; overflow-x: auto; max-height: 160px; margin-top: 0.5rem; font-family: Consolas, monospace; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.875rem; }
    th, td { padding: 0.65rem 0.75rem; text-align: left; border-bottom: 1px solid #E8E6DC; }
    th { background: #F9F8F5; font-weight: 700; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div>
        <h1>Multan Tech Accessories — API & Catalog Status</h1>
        <div style="font-size: 0.85rem; color: #5E5D59; margin-top: 0.25rem;">Live Diagnostics for Supplier API, IP Whitelist, and Catalog Sync</div>
      </div>
      <div>
        <span class="brand-tag">Diagnostic Tool</span>
      </div>
    </div>

    <?php if ($message): ?>
      <div class="alert <?= $messageType === 'success' ? 'alert-success' : 'alert-error' ?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <!-- Action Controls: Strict Computer Zone Sync & Purge -->
    <div class="card" style="border: 2px solid #FF6223; background: #FFFBF9;">
      <div class="card-title" style="color: #FF6223;">
        <span>🎯 Computer Zone Live Catalog Management</span>
      </div>
      <p style="font-size: 0.95rem; color: #374151; margin-bottom: 1.25rem;">
        Your store is connected directly to <strong>api.computerzone.pk</strong>. Choose an action below:
      </p>
      <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <form method="POST">
          <input type="hidden" name="action" value="purge_and_sync">
          <button type="submit" class="btn btn-orange" onclick="return confirm('This will delete any old products and pull fresh 100% genuine products from Computer Zone. Proceed?');">
            🧹 Delete Old & Sync Fresh from Computer Zone &rarr;
          </button>
        </form>

        <form method="POST">
          <input type="hidden" name="action" value="run_live_sync">
          <button type="submit" class="btn btn-teal">
            🔄 Update / Sync Products from Computer Zone &rarr;
          </button>
        </form>

        <form method="POST">
          <input type="hidden" name="action" value="recalculate_all_prices">
          <button type="submit" class="btn" style="background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D;" title="Recalculate all prices using tiered formula">
            💰 Recalculate All Prices (&lt;3000: +100+99, &gt;=3000: +300+99) &rarr;
          </button>
        </form>

        <form method="POST">
          <input type="hidden" name="action" value="purge_all_products">
          <button type="submit" class="btn" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;" onclick="return confirm('Are you sure you want to delete ALL products from the database?');">
            🗑️ Delete All Products
          </button>
        </form>

        <a href="/" target="_blank" class="btn" style="background: #FFFFFF; border: 1px solid #E8E6DC; color: #141413;">
          View Storefront &rarr;
        </a>
      </div>
    </div>

    <!-- Admin Portal Credentials & Quick Access -->
    <div class="card" style="border: 2px solid #0EB4A9; background: #F0FDF4;">
      <div class="card-title" style="color: #0E7064;">
        <span>🔐 Admin Portal Access Credentials</span>
      </div>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
        <div style="background: #FFFFFF; padding: 1rem; border-radius: 8px; border: 1px solid #BBF7D0;">
          <div style="font-size: 0.75rem; text-transform: uppercase; color: #5E5D59; font-weight: 700;">Admin Portal URL</div>
          <div style="font-size: 1rem; font-weight: 800; margin-top: 0.25rem;">
            <a href="/<?= e($adminPath) ?>/login" target="_blank" style="color: #0E7064; text-decoration: underline;">
              https://multantech.store/<?= e($adminPath) ?>/login
            </a>
          </div>
        </div>
        <div style="background: #FFFFFF; padding: 1rem; border-radius: 8px; border: 1px solid #BBF7D0;">
          <div style="font-size: 0.75rem; text-transform: uppercase; color: #5E5D59; font-weight: 700;">Admin Email</div>
          <div style="font-size: 1.1rem; font-weight: 800; color: #141413; margin-top: 0.25rem;">admin@multantech.store</div>
        </div>
        <div style="background: #FFFFFF; padding: 1rem; border-radius: 8px; border: 1px solid #BBF7D0;">
          <div style="font-size: 0.75rem; text-transform: uppercase; color: #5E5D59; font-weight: 700;">Admin Password</div>
          <div style="font-size: 1.1rem; font-weight: 800; color: #FF6223; margin-top: 0.25rem;">MultanTech@2026!</div>
        </div>
      </div>
      <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <a href="/<?= e($adminPath) ?>/login" target="_blank" class="btn btn-teal">
          Open Admin Portal (New Tab) &rarr;
        </a>
        <form method="POST">
          <input type="hidden" name="action" value="git_pull_update">
          <button type="submit" class="btn" style="background: #0B0E14; color: #FFFFFF; border: 1px solid #1E2536;" onclick="return confirm('Pull latest updates from GitHub origin/master and flush cache?');">
            🚀 Pull Latest Code from GitHub (git pull)
          </button>
        </form>
        <form method="POST">
          <input type="hidden" name="action" value="reset_admin_password">
          <button type="submit" class="btn" style="background: #FFFFFF; border: 1px solid #0EB4A9; color: #0E7064;">
            Reset Admin Password to Default (MultanTech@2026!)
          </button>
        </form>
      </div>
    </div>

    <!-- 2. Current Storefront State -->
    <div class="card">
      <div class="card-title">
        <span>Current Database Inventory</span>
        <span style="font-size: 0.85rem; color: #5E5D59; font-weight: normal;">Live count from database.sqlite</span>
      </div>
      <div class="grid">
        <div class="metric">
          <div class="metric-label">Total Products</div>
          <div class="metric-value" style="color: <?= $productCount > 0 ? '#0E7064' : '#DC2626' ?>;"><?= $productCount ?></div>
        </div>
        <div class="metric">
          <div class="metric-label">Active For Sale</div>
          <div class="metric-value" style="color: <?= $activeProductCount > 0 ? '#0E7064' : '#DC2626' ?>;"><?= $activeProductCount ?></div>
        </div>
        <div class="metric">
          <div class="metric-label">Store Categories</div>
          <div class="metric-value"><?= $categoryCount ?></div>
        </div>
        <div class="metric">
          <div class="metric-label">Brands</div>
          <div class="metric-value"><?= $brandCount ?></div>
        </div>
      </div>

      <?php if (!empty($sampleProducts)): ?>
        <h4 style="font-size: 0.9rem; font-weight: 700; margin-top: 1rem;">Recent Products in Database:</h4>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Product Title</th>
              <th>Price (PKR)</th>
              <th>Status</th>
              <th>Live</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sampleProducts as $sp): ?>
              <tr>
                <td>#<?= (int)$sp['id'] ?></td>
                <td><?= htmlspecialchars($sp['title']) ?></td>
                <td><strong>Rs. <?= number_format((float)$sp['sell_price']) ?></strong></td>
                <td><?= htmlspecialchars($sp['stock_status'] ?? 'in_stock') ?></td>
                <td><?= $sp['is_active'] ? '✅ Yes' : '❌ No' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <!-- 3. Server Outbound IP & Computer Zone API Results -->
    <div class="card">
      <div class="card-title">
        <span>Computer Zone API Live Handshake Test</span>
        <span class="badge <?= $outboundIp !== 'Unknown' ? 'badge-pass' : 'badge-fail' ?>">Server Outbound IP: <?= htmlspecialchars($outboundIp) ?></span>
      </div>
      <div style="background: #F3F4F6; padding: 0.75rem 1rem; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1rem;">
        <div><strong>Registered Whitelist Host IP:</strong> <code>185.151.30.200</code></div>
        <div><strong>Live Outbound cURL IP:</strong> <code><?= htmlspecialchars($outboundIp) ?></code>
          <?php if ($outboundIp === '185.151.30.200' || str_starts_with($outboundIp, '185.151.30.')): ?>
            <span style="color: #0E7064; font-weight: 700; margin-left: 0.5rem;">✅ IP Match Confirmed</span>
          <?php else: ?>
            <span style="color: #D97706; font-weight: 600; margin-left: 0.5rem;">⚠️ Different Outbound IP</span>
          <?php endif; ?>
        </div>
        <div><strong>Registered Token:</strong> <code><?= htmlspecialchars(substr($token, 0, 8) . '...' . substr($token, -4)) ?></code></div>
      </div>
      <p style="font-size: 0.85rem; color: #5E5D59; margin-bottom: 1rem;">
        Computer Zone API requires requests to originate from your server's whitelisted IP. Below are the raw live results of contacting <code><?= htmlspecialchars($apiUrl) ?></code>:
      </p>

      <?php foreach ($testResults as $testName => $r): ?>
        <div class="test-box">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
            <strong><?= htmlspecialchars($testName) ?></strong>
            <span class="badge <?= ($r['code'] === 200) ? 'badge-pass' : 'badge-fail' ?>">
              HTTP <?= (int)$r['code'] ?> (<?= $r['duration'] ?>s)
            </span>
          </div>
          <?php if (!empty($r['error'])): ?>
            <div style="color: #DC2626; font-size: 0.8rem; margin-bottom: 0.25rem;">
              <strong>cURL Error:</strong> <?= htmlspecialchars($r['error']) ?>
            </div>
          <?php endif; ?>
          <pre><?= htmlspecialchars($r['body'] ?: '[Empty response]') ?></pre>
        </div>
      <?php endforeach; ?>

      <form method="POST" style="margin-top: 1rem;">
        <input type="hidden" name="action" value="run_live_sync">
        <button type="submit" class="btn btn-orange">
          🔄 Retry Live API Sync From Computer Zone
        </button>
      </form>
    </div>

  </div>
</body>
</html>

