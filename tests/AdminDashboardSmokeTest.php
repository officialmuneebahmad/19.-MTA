<?php
/**
 * Multan Tech Accessories — Admin Portal Automated Smoke Test Suite
 * MultanTech.store
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Admin\DashboardController;
use App\Core\Request;
use App\Core\Session;
use App\Services\DashboardService;

echo "=== MTA ADMIN PORTAL AUTOMATED SMOKE TEST ===\n\n";
$passCount = 0;
$failCount = 0;

function assert_test(bool $condition, string $title, string $details = ''): void {
    global $passCount, $failCount;
    if ($condition) {
        $passCount++;
        echo " [PASS] " . $title . "\n";
    } else {
        $failCount++;
        echo " [FAIL] " . $title . ($details ? " - " . $details : "") . "\n";
    }
}

// -----------------------------------------------------------------------------
// Test 1: CSS Safety Net & Rule Validation
// -----------------------------------------------------------------------------
$cssContent = file_get_contents(MTA_ROOT . '/assets/css/admin.css');
assert_test(
    str_contains($cssContent, 'svg {') && str_contains($cssContent, 'max-width: 100%'),
    "CSS Rule: Global SVG Safety Net defined"
);
assert_test(
    str_contains($cssContent, '.top-product-img,') && str_contains($cssContent, 'width: 48px;'),
    "CSS Rule: Table and Product thumbnails strictly constrained to 48x48 px"
);
assert_test(
    str_contains($cssContent, '.sidebar-close-btn {') && str_contains($cssContent, 'display: none !important;'),
    "CSS Rule: Mobile close button hidden on desktop"
);
assert_test(
    str_contains($cssContent, '--admin-bg: #FAF9F5;') && str_contains($cssContent, '--admin-bg: #262624;'),
    "CSS Rule: Day (#FAF9F5) and Night (#262624) tokens present with zero dark blue"
);
assert_test(
    !str_contains($cssContent, '#000080') && !str_contains($cssContent, '#001f3f'),
    "CSS Rule: Zero dark blue palette verified"
);

// -----------------------------------------------------------------------------
// Test 2: Catalog Health Reconciliation
// -----------------------------------------------------------------------------
$recon = DashboardService::getCatalogReconciliation();
assert_test(
    $recon['imported_total'] === 991,
    "Catalog Health: Imported total matches exactly 991 products",
    "Got: " . $recon['imported_total']
);
assert_test(
    ($recon['in_stock'] + $recon['out_of_stock']) === $recon['imported_total'],
    "Catalog Health: In-stock (" . $recon['in_stock'] . ") + Out-of-stock (" . $recon['out_of_stock'] . ") = " . $recon['imported_total']
);
assert_test(
    $recon['has_discrepancy'] === false,
    "Catalog Health: No catalog discrepancy detected"
);

// -----------------------------------------------------------------------------
// Test 3: Top Selling Logic (Strict units_sold > 0)
// -----------------------------------------------------------------------------
$perf = DashboardService::getProductPerformance('1970-01-01 00:00:00');
assert_test(
    is_array($perf['top_sold']) && count($perf['top_sold']) === 0,
    "Product Intelligence: 0 sold products strictly renders 0 top sellers (no unsold items labeled as top selling)"
);
assert_test(
    count($perf['most_popular']) > 0,
    "Product Intelligence: Fallback engagement products populated for display"
);

// -----------------------------------------------------------------------------
// Test 4: Dashboard View & Layout Rendering
// -----------------------------------------------------------------------------
Session::set('admin_user', [
    'id' => 1,
    'name' => 'Muneeb Ahmad',
    'email' => 'admin@multantech.store',
    'role' => 'owner'
]);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/mta-control-9872';

$req = new Request();
$controller = new DashboardController();
$res = $controller->index($req);
$html = $res->getBody();

assert_test(
    $res->getStatusCode() === 200,
    "Controller Response: HTTP 200 OK"
);
assert_test(
    str_contains($html, 'Welcome back, Muneeb!'),
    "Greeting: Uses database first name 'Muneeb'"
);
assert_test(
    !str_contains($html, 'Muhammad Tayyab'),
    "Greeting: Hardcoded 'Muhammad Tayyab' completely eradicated"
);
assert_test(
    !str_contains($html, '2FA Active') && !str_contains($html, '2fa active'),
    "Security UI: Zero mention of '2FA Active' in topbar or layout"
);
assert_test(
    str_contains($html, 'admin.css?v=' . MTA_BUILD_VERSION),
    "Asset Pipeline: admin.css loaded with cache-busting version " . MTA_BUILD_VERSION
);
assert_test(
    str_contains($html, 'admin.js?v=' . MTA_BUILD_VERSION),
    "Asset Pipeline: admin.js loaded with cache-busting version " . MTA_BUILD_VERSION
);
assert_test(
    str_contains($html, 'No verified sales yet'),
    "Dashboard UI: Unsold products show clean empty state"
);
assert_test(
    str_contains($html, 'Catalog Health &amp; Reconciliation') || str_contains($html, 'Catalog Health & Reconciliation'),
    "Dashboard UI: Reconciliation card present"
);

// -----------------------------------------------------------------------------
// Summary
// -----------------------------------------------------------------------------
echo "\n============================================\n";
echo "TOTAL: " . ($passCount + $failCount) . " | PASSED: " . $passCount . " | FAILED: " . $failCount . "\n";
echo "============================================\n";

if ($failCount > 0) {
    exit(1);
}

