<?php
/**
 * Multan Tech Accessories — Pricing & Delivery Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Services\DeliveryCalculator;
use App\Services\PricingService;
use App\Services\SupplierSyncService;

class PricingDeliveryTest
{
    private Database $db;
    private DeliveryCalculator $calculator;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->calculator = new DeliveryCalculator($this->db);
    }

    public function run(): void
    {
        echo "\n=== Running Pricing & Delivery Tests ===\n";

        $this->testPricingEngineUnitCases();
        $this->testManualPriceSnap();
        $this->testTotpService();
        $this->testFreeDeliveryAboveThreshold();
        $this->testMultanLocalDelivery();
        $this->testNationwideDelivery();
        $this->testSecondOrderFreeDelivery();
        $this->testAllProductsRepriced();

        echo "\nAll Pricing & Delivery tests passed successfully!\n";
    }

    private function testPricingEngineUnitCases(): void
    {
        echo "Testing PricingService::sellPrice() required test cases... ";

        $cases = [
            160 => 399,
            250 => 499,
            1000 => 1199,
            1999 => 2199,
            2000 => 2399,
            2001 => 2499,
            3690 => 4099,
            4130 => 4599,
            4990 => 5399,
            5890 => 6299,
            0 => 0,
        ];

        foreach ($cases as $supplierPrice => $expectedSellPrice) {
            $actual = PricingService::sellPrice($supplierPrice);
            if ($actual !== $expectedSellPrice) {
                throw new \RuntimeException(
                    "Pricing failure for supplier price {$supplierPrice}: Expected {$expectedSellPrice}, got {$actual}"
                );
            }

            if ($actual > 0) {
                if ($actual % 100 !== 99) {
                    throw new \RuntimeException("Result {$actual} does not end in 99");
                }
            }
        }

        echo "PASSED (all 11 unit test cases verified)\n";
    }

    private function testManualPriceSnap(): void
    {
        echo "Testing PricingService::stage2() / snapPrice() required test cases... ";

        $stage2Cases = [
            3420 => 3499,
            250 => 299,
            1200 => 1299,
            890 => 899,
            1299 => 1299,
            1300 => 1399,
            99 => 99,
            100 => 199,
            0 => 0,
        ];

        foreach ($stage2Cases as $input => $expected) {
            $actual = PricingService::snapPrice($input);
            if ($actual !== $expected) {
                throw new \RuntimeException(
                    "Stage 2 failure for input {$input}: Expected {$expected}, got {$actual}"
                );
            }
            $stage2Direct = PricingService::stage2($input);
            if ($stage2Direct !== $expected) {
                throw new \RuntimeException(
                    "stage2() direct failure for input {$input}: Expected {$expected}, got {$stage2Direct}"
                );
            }
        }

        echo "PASSED (all 9 Stage 2 test cases verified)\n";
    }

    private function testTotpService(): void
    {
        echo "Testing TotpService RFC 6238 TOTP & Backup Codes... ";
        $secret = \App\Services\TotpService::generateSecret();
        $code = \App\Services\TotpService::calculateCode($secret);
        assert(strlen($code) === 6, 'Code must be 6 digits');
        assert(\App\Services\TotpService::verifyCode($secret, $code) === true, 'Valid code must verify');
        assert(\App\Services\TotpService::verifyCode($secret, '000000') === false, 'Invalid code must fail');
        $backups = \App\Services\TotpService::generateBackupCodes(10);
        assert(count($backups['plain']) === 10, 'Must generate 10 backup codes');
        assert(count($backups['hashed']) === 10, 'Must generate 10 hashed backup codes');
        echo "PASSED\n";
    }

    private function testFreeDeliveryAboveThreshold(): void
    {
        echo "Testing Rule 1: Free Delivery above threshold (Rs. 5,000)... ";
        $result = $this->calculator->calculate(5500.0, 'Multan', '03001234567');
        assert((float)$result['charge'] === 0.0, 'Subtotal >= 5000 must have 0 delivery charge');
        assert($result['is_free'] === true, 'is_free must be true');

        $result2 = $this->calculator->calculate(5000.0, 'Lahore', '03001234567');
        assert((float)$result2['charge'] === 0.0, 'Exact Rs. 5000 must have 0 delivery charge');
        echo "PASSED\n";
    }

    private function testMultanLocalDelivery(): void
    {
        echo "Testing Rule 3: Multan Local Delivery (Rs. 100 below Rs. 5,000)... ";
        $result = $this->calculator->calculate(2500.0, 'Multan', '03119999999');
        assert((float)$result['charge'] === 100.0, 'Multan delivery below threshold must be Rs. 100');
        assert($result['is_free'] === false, 'is_free must be false');
        echo "PASSED\n";
    }

    private function testNationwideDelivery(): void
    {
        echo "Testing Rule 4: Other Cities Nationwide Delivery (Rs. 250 below Rs. 5,000)... ";
        $resultKarachi = $this->calculator->calculate(3200.0, 'Karachi', '03119999999');
        assert((float)$resultKarachi['charge'] === 250.0, 'Karachi delivery below threshold must be Rs. 250');

        $resultIslamabad = $this->calculator->calculate(1200.0, 'Islamabad', '03119999999');
        assert((float)$resultIslamabad['charge'] === 250.0, 'Islamabad delivery below threshold must be Rs. 250');
        echo "PASSED\n";
    }

    private function testSecondOrderFreeDelivery(): void
    {
        echo "Testing Rule 2: Free Delivery on 2nd Order... ";
        $testPhone = '03078888888';
        
        $this->db->execute(
            "INSERT INTO orders (order_number, customer_name, customer_phone, city, shipping_address, subtotal, delivery_charge, total, status, created_at)
             VALUES ('MTA-TEST-001', 'Test Customer', ?, 'Lahore', 'Gulberg III', 1500, 250, 1750, 'delivered', ?)",
            [$testPhone, date('Y-m-d H:i:s')]
        );

        $result = $this->calculator->calculate(2000.0, 'Lahore', $testPhone);
        assert((float)$result['charge'] === 0.0, '2nd order must have free delivery charge');
        assert($result['is_free'] === true, '2nd order is_free must be true');
        assert($result['is_second_order'] === true, 'is_second_order must be true');

        $this->db->execute("DELETE FROM orders WHERE order_number = 'MTA-TEST-001'");
        echo "PASSED\n";
    }

    private function testAllProductsRepriced(): void
    {
        echo "Testing Re-pricing all catalog products in database... ";
        $count = PricingService::repriceAllProducts();
        assert($count === 991, "All 991 products must be re-priced, got {$count}");

        $invalidPrices = $this->db->fetchAll(
            "SELECT id, sku, title, sell_price FROM products WHERE sell_price > 0 AND (CAST(sell_price AS INTEGER) % 100) != 99"
        );
        assert(empty($invalidPrices), "Found products with sell_price not ending in 99: " . count($invalidPrices));

        echo "PASSED (all 991 products re-priced and verified ending in 99)\n";
    }
}

$test = new PricingDeliveryTest();
$test->run();
