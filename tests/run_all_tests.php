<?php
/**
 * Multan Tech Accessories — Master Test Suite Runner
 * Executes all automated tests and reports verification status
 */

declare(strict_types=1);

echo "======================================================================\n";
echo "   MULTAN TECH ACCESSORIES — AUTOMATED VERIFICATION TEST SUITE       \n";
echo "   Founded & Developed by Muneeb Ahmad | MultanTech.store            \n";
echo "======================================================================\n";

$tests = [
    'tests/SyncTest.php',
    'tests/PricingDeliveryTest.php',
    'tests/SeoTest.php',
    'tests/SecurityTest.php',
];

$passedCount = 0;
$failedCount = 0;

foreach ($tests as $testFile) {
    $fullPath = __DIR__ . '/../' . $testFile;
    if (!file_exists($fullPath)) {
        echo "ERROR: Test file not found: {$testFile}\n";
        $failedCount++;
        continue;
    }

    $cmd = 'php -c "C:\Users\home\AppData\Local\Programs\PHP\php.ini" ' . escapeshellarg($fullPath);
    $output = [];
    $returnCode = 0;
    exec($cmd, $output, $returnCode);

    echo implode("\n", $output) . "\n";

    if ($returnCode === 0) {
        $passedCount++;
    } else {
        $failedCount++;
        echo "FAILED: {$testFile} (Exit Code: {$returnCode})\n";
    }
}

echo "\n----------------------------------------------------------------------\n";
echo "TEST SUMMARY: {$passedCount} passed, {$failedCount} failed.\n";
if ($failedCount === 0) {
    echo "STATUS: ALL TESTS PASSED! APPLICATION VERIFIED FOR PRODUCTION.\n";
} else {
    echo "STATUS: SOME TESTS FAILED. PLEASE REVIEW LOGS ABOVE.\n";
}
echo "======================================================================\n";

exit($failedCount > 0 ? 1 : 0);
