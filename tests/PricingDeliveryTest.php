<?php
/**
 * Multan Tech Accessories — Pricing & Delivery Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Services\DeliveryCalculator;

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

        $this->testFreeDeliveryAboveThreshold();
        $this->testMultanLocalDelivery();
        $this->testNationwideDelivery();
        $this->testSecondOrderFreeDelivery();
        $this->testMarkupCalculation();

        echo "All Pricing & Delivery tests passed successfully!\n";
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
        
        // Ensure test order exists for this phone
        $this->db->execute(
            "INSERT INTO orders (order_number, customer_name, customer_phone, city, shipping_address, subtotal, delivery_charge, total, status, created_at)
             VALUES ('MTA-TEST-001', 'Test Customer', ?, 'Lahore', 'Gulberg III', 1500, 250, 1750, 'delivered', ?)",
            [$testPhone, date('Y-m-d H:i:s')]
        );

        $result = $this->calculator->calculate(2000.0, 'Lahore', $testPhone);
        assert((float)$result['charge'] === 0.0, '2nd order must have free delivery charge');
        assert($result['is_free'] === true, '2nd order is_free must be true');
        assert($result['is_second_order'] === true, 'is_second_order must be true');

        // Cleanup test order
        $this->db->execute("DELETE FROM orders WHERE order_number = 'MTA-TEST-001'");
        echo "PASSED\n";
    }

    private function testMarkupCalculation(): void
    {
        echo "Testing Pricing: Tiered Rounding + 99 Formula... ";
        $syncService = new \App\Services\SupplierSyncService();
        
        // Under 3000 (+100, round to nearest hundred, +99)
        assert($syncService->calculateSellingPrice(160.0) === 399.0, '160 must yield 399');
        assert($syncService->calculateSellingPrice(250.0) === 499.0, '250 must yield 499');
        assert($syncService->calculateSellingPrice(2420.0) === 2599.0, '2420 must yield 2599');
        
        // 3000 and above (+300, round to nearest hundred, +99)
        assert($syncService->calculateSellingPrice(3690.0) === 4099.0, '3690 must yield 4099');
        assert($syncService->calculateSellingPrice(5000.0) === 5399.0, '5000 must yield 5399');
        
        echo "PASSED\n";
    }
}

$test = new PricingDeliveryTest();
$test->run();
