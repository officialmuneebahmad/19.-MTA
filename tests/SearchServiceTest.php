<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Services\SearchService;

echo "=== MTA SEARCH SERVICE AUTOMATED VERIFICATION SUITE ===\n\n";

$passCount = 0;
$failCount = 0;

function assertCondition(bool $condition, string $msg): void
{
    global $passCount, $failCount;
    if ($condition) {
        echo " [PASS] {$msg}\n";
        $passCount++;
    } else {
        echo " [FAIL] {$msg}\n";
        $failCount++;
    }
}

// 1. Unit Test Normalization & Tokenization
assertCondition(SearchService::normalize('SPEEDX SP-S188L (22.5W)') === 'speedxsps188l225w', 'Normalization strips spaces, dashes, parentheses');
assertCondition(SearchService::normalize('speed x sp') === 'speedxsp', 'Normalized "speed x sp" equals "speedxsp"');
assertCondition(SearchService::normalize('Type-C Fast Charger') === 'typecfastcharger', 'Normalized "Type-C" equals "typec"');

// 2. Token Set Generation
$tokens = SearchService::tokenize('speed x sp');
$sets = SearchService::generateTokenSets($tokens);
$flatSets = array_map(fn($s) => implode(',', $s), $sets);
assertCondition(in_array('speedx,sp', $flatSets, true), 'TokenSets merges adjacent short tokens "speed" + "x" -> "speedx"');
assertCondition(in_array('speedxsp', $flatSets, true), 'TokenSets includes full compact variation "speedxsp"');

// 3. Test Multi-Word and Space-Insensitive Search on Simulated/Mock Product
$reflection = new ReflectionClass(SearchService::class);
$rankMethod = $reflection->getMethod('rankCandidates');
$rankMethod->setAccessible(true);

$mockProduct = [
    'id' => 9901,
    'title' => 'SPEEDX SP-S188L 3IN1 (22.5W) 10000MAH FAST CHARGING POWER BANK',
    'slug' => 'speedx-sp-s188l-3in1-22-5w-10000mah-fast-charging-power-bank-01962',
    'sku' => '01962',
    'brand_name' => 'SpeedX',
    'sell_price' => 1799,
    'status' => 1,
    'popularity_score' => 95,
    'is_active' => 1,
    'is_published' => 1
];

$unrelatedProduct = [
    'id' => 9902,
    'title' => 'Kingston DataTraveler Exodia 64GB USB 3.2 Flash Drive with Cap',
    'slug' => 'kingston-datatraveler-exodia-64gb-01500',
    'sku' => '01500',
    'brand_name' => 'Kingston',
    'sell_price' => 1299,
    'status' => 1,
    'popularity_score' => 80,
    'is_active' => 1,
    'is_published' => 1
];

$ankerPowerBank = [
    'id' => 9903,
    'title' => 'Anker PowerCore 10000mAh Ultra-Compact High-Speed Power Bank',
    'slug' => 'anker-powercore-10000mah-01600',
    'sku' => '01600',
    'brand_name' => 'Anker',
    'sell_price' => 4599,
    'status' => 1,
    'popularity_score' => 90,
    'is_active' => 1,
    'is_published' => 1
];

$pool = [$mockProduct, $unrelatedProduct, $ankerPowerBank];

// Test Requirement 1: User types "speed x sp" instead of "speedx sp"
$resSpeedXSp = $rankMethod->invoke(null, $pool, 'speed x sp', false);
assertCondition(!empty($resSpeedXSp), 'Query "speed x sp" returns results');
assertCondition(($resSpeedXSp[0]['id'] ?? 0) === 9901, 'Query "speed x sp" top result is SPEEDX SP-S188L');
assertCondition(!in_array(9902, array_column($resSpeedXSp, 'id')), 'Query "speed x sp" excludes unrelated Kingston Exodia');

// Test Requirement 2: User types "speedx power bank" for "SPEEDX SP-S188L 3IN1 (22.5W) 10000MAH FAST CHARGING POWER BANK"
$resSpeedXPowerBank = $rankMethod->invoke(null, $pool, 'speedx power bank', false);
assertCondition(!empty($resSpeedXPowerBank), 'Query "speedx power bank" returns results');
assertCondition(($resSpeedXPowerBank[0]['id'] ?? 0) === 9901, 'Query "speedx power bank" top result is SPEEDX SP-S188L');
assertCondition(!in_array(9903, array_column($resSpeedXPowerBank, 'id')), 'Query "speedx power bank" excludes Anker PowerCore (strictly prioritizes 100% full match)');

// Test Requirement 3: Space resilience for other accessories
$resTypeC = SearchService::search('type c fast charger', 5);
assertCondition($resTypeC['total'] > 0, 'Query "type c fast charger" finds matching accessories in catalog');

$resPowerBank = SearchService::search('power bank 20000', 5);
assertCondition($resPowerBank['total'] > 0, 'Query "power bank 20000" finds 20000mAh power banks in catalog');

// Test Suggestions Endpoint structure
$suggestions = SearchService::suggestions('power bank', 4);
assertCondition(!empty($suggestions), 'SearchService::suggestions returns items');
assertCondition(isset($suggestions[0]['image'], $suggestions[0]['title'], $suggestions[0]['sell_price'], $suggestions[0]['slug']), 'Suggestions items include image, title, sell_price, and slug');

echo "\n============================================\n";
echo "TOTAL: " . ($passCount + $failCount) . " | PASSED: {$passCount} | FAILED: {$failCount}\n";
echo "============================================\n";

if ($failCount > 0) {
    exit(1);
}

