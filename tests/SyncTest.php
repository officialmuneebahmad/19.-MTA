<?php
/**
 * Multan Tech Accessories — Supplier Sync Test Suite
 * Tests 991 product import, idempotency, price override preservation,
 * truncation abort, and missing field handling.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;
use App\Services\SupplierSyncService;

class SyncTest
{
    private Database $db;
    private SupplierSyncService $service;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->service = new SupplierSyncService($this->db);
    }

    public function run(): void
    {
        echo "====================================================\n";
        echo " RUNNING SUPPLIER SYNC AUTOMATED TESTS\n";
        echo "====================================================\n\n";

        $mockJsonFile = MTA_ROOT . '/tools/mock_supplier/products.json';
        if (!file_exists($mockJsonFile)) {
            throw new RuntimeException("Mock supplier products.json missing. Run tools/mock_supplier/generate_products.php first.");
        }

        // Test 1: Import all 991 mock products
        $this->testInitialImport();

        // Test 2: Idempotency (second run changes nothing, unchanged = 991)
        $this->testIdempotency();

        // Test 3: Manual price override preservation
        $this->testPriceOverridePreserved();

        // Test 4: Truncated feed safety guard (<50% aborts without changes)
        $this->testTruncationAbort();

        // Test 5: Missing fields row skip and logging
        $this->testMissingFieldsSkipped();

        echo "\n[ALL SYNC TESTS PASSED SUCCESSFULLY!]\n\n";
    }

    private function testInitialImport(): void
    {
        echo "1. Testing Initial Import of 991 Products...\n";
        
        // Clean supplier products for isolated test
        $this->db->execute("DELETE FROM products WHERE source = 'supplier'");
        $this->db->execute("DELETE FROM supplier_category_map");

        // Use direct JSON file loading or data injection
        $mockData = json_decode(file_get_contents(MTA_ROOT . '/tools/mock_supplier/products.json'), true);
        $totalFeed = count($mockData['Products']);
        
        $this->assertEquals(991, $totalFeed, "Mock feed contains exactly 991 products");

        // Run sync using data URL or local mock endpoint
        $stats = $this->runSyncWithData($mockData);

        $this->assertEquals('success', $stats['status'], "Sync status is success");
        $this->assertEquals(991, $stats['imported_count'], "Imported exactly 991 products");
        $this->assertEquals(0, $stats['skipped_invalid'], "Skipped 0 invalid products");

        $dbCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM products WHERE source = 'supplier' AND is_active = 1");
        $this->assertEquals(991, $dbCount, "Database contains exactly 991 active supplier products");

        echo "   -> PASS: 991 of 991 products imported into database.\n\n";
    }

    private function testIdempotency(): void
    {
        echo "2. Testing Sync Idempotency (Second run without changes)...\n";

        $mockData = json_decode(file_get_contents(MTA_ROOT . '/tools/mock_supplier/products.json'), true);
        
        // Record updated_at timestamp of first product
        $sampleBefore = $this->db->fetch("SELECT id, updated_at FROM products WHERE supplier_id = '00001' LIMIT 1");

        // Small sleep to ensure timestamp difference would be visible if updated
        sleep(1);

        $stats = $this->runSyncWithData($mockData);

        $this->assertEquals('success', $stats['status'], "Second sync status is success");
        $this->assertEquals(0, $stats['imported_count'], "0 new products imported");
        $this->assertEquals(0, $stats['updated_count'], "0 products updated");
        $this->assertEquals(991, $stats['unchanged_count'], "All 991 products marked unchanged");

        $sampleAfter = $this->db->fetch("SELECT id, updated_at FROM products WHERE supplier_id = '00001' LIMIT 1");
        $this->assertEquals($sampleBefore['updated_at'], $sampleAfter['updated_at'], "updated_at untouched on unchanged products");

        echo "   -> PASS: Idempotency verified. 991 unchanged, updated_at preserved.\n\n";
    }

    private function testPriceOverridePreserved(): void
    {
        echo "3. Testing Manual Price Override Preservation...\n";

        // Admin overrides price of item 00001 to Rs. 4,999
        $this->db->execute(
            "UPDATE products SET price_override = 4999.00, sell_price = 4999.00 WHERE supplier_id = '00001'"
        );

        $mockData = json_decode(file_get_contents(MTA_ROOT . '/tools/mock_supplier/products.json'), true);
        
        // Feed supplier has different price for 00001
        $mockData['Products'][0]['ProductPrice'] = "1500"; // Raw supplier price

        $stats = $this->runSyncWithData($mockData);

        $product = $this->db->fetch("SELECT sell_price, price_override FROM products WHERE supplier_id = '00001' LIMIT 1");
        $this->assertEquals(4999.00, (float)$product['sell_price'], "sell_price preserved at override price 4999");
        $this->assertEquals(4999.00, (float)$product['price_override'], "price_override column retained");

        echo "   -> PASS: Manual price override 4,999 strictly preserved across sync.\n\n";
    }

    private function testTruncationAbort(): void
    {
        echo "4. Testing Safety Guard Against Truncated Supplier Feed...\n";

        // Feed with only 100 products (< 50% of 991 active items)
        $mockData = json_decode(file_get_contents(MTA_ROOT . '/tools/mock_supplier/products.json'), true);
        $mockData['Products'] = array_slice($mockData['Products'], 0, 100);

        $stats = $this->runSyncWithData($mockData);

        $this->assertEquals('failed', $stats['status'], "Sync safely aborted");
        $this->assertContains("Safety abort", $stats['error_message'], "Error mentions safety abort");

        // Verify active product count was NOT touched or deleted
        $dbCount = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM products WHERE source = 'supplier' AND is_active = 1");
        $this->assertEquals(991, $dbCount, "Database product count unchanged at 991");

        echo "   -> PASS: Truncated feed safely aborted without changing catalogue.\n\n";
    }

    private function testMissingFieldsSkipped(): void
    {
        echo "5. Testing Missing Fields Row Skip & Logging...\n";

        $mockData = json_decode(file_get_contents(MTA_ROOT . '/tools/mock_supplier/products.json'), true);
        
        // Insert 2 invalid items
        array_unshift($mockData['Products'], [
            'ProductId' => '', // Empty ID
            'ProductTitle' => 'Invalid Product No ID',
            'ProductPrice' => '1000'
        ]);
        array_unshift($mockData['Products'], [
            'ProductId' => '99998',
            'ProductTitle' => '', // Empty Title
            'ProductPrice' => '1000'
        ]);

        $stats = $this->runSyncWithData($mockData);

        $this->assertEquals('success', $stats['status'], "Sync status success");
        $this->assertEquals(2, $stats['skipped_invalid'], "Skipped exactly 2 invalid products");

        echo "   -> PASS: Invalid rows skipped and recorded in sync run stats.\n\n";
    }

    private function runSyncWithData(array $feedData): array
    {
        // Write temporary feed file
        $tempFile = MTA_STORAGE . '/temp_test_feed.json';
        file_put_contents($tempFile, json_encode($feedData));

        // Use php:// data or file URI with custom method
        $stats = $this->service->sync('file://' . str_replace('\\', '/', $tempFile));
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
        return $stats;
    }

    private function assertEquals(mixed $expected, mixed $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException("Assertion Failed: {$message}. Expected: " . var_export($expected, true) . ", Got: " . var_export($actual, true));
        }
    }

    private function assertContains(string $needle, ?string $haystack, string $message): void
    {
        if ($haystack === null || !str_contains($haystack, $needle)) {
            throw new RuntimeException("Assertion Failed: {$message}. '{$needle}' not found in '{$haystack}'");
        }
    }
}

$test = new SyncTest();
$test->run();
