<?php
/**
 * Multan Tech Accessories — Delivery Rules Calculator
 * Rules:
 * 1. Free delivery if subtotal >= Rs. 5,000 (configurable)
 * 2. Free delivery on customer's 2nd order (matched by normalized phone with prior non-cancelled orders)
 * 3. Multan local charge = Rs. 100 (configurable)
 * 4. Other Pakistani cities = Rs. 250 (configurable)
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Security;

class DeliveryCalculator
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function calculate(float $subtotal, string $city, string $rawPhone = ''): array
    {
        $freeThreshold = (float)mta_setting('delivery_free_threshold', mta_config('delivery.free_threshold', 5000));
        $multanCharge = (float)mta_setting('delivery_multan_charge', mta_config('delivery.multan_charge', 100));
        $standardCharge = (float)mta_setting('delivery_standard_charge', mta_config('delivery.standard_charge', 250));

        $cleanCity = strtolower(trim($city));
        $isSecondOrder = false;

        // Check 2nd order rule if phone is provided
        if (!empty($rawPhone)) {
            $phoneData = Security::normalizePakistaniPhone($rawPhone);
            if ($phoneData['valid']) {
                $priorOrdersCount = (int)$this->db->fetchColumn(
                    "SELECT COUNT(*) FROM orders WHERE customer_phone = ? AND status != 'cancelled'",
                    [$phoneData['local']]
                );
                if ($priorOrdersCount >= 1) {
                    $isSecondOrder = true;
                }
            }
        }

        // Rule 1: Free above threshold
        if ($subtotal >= $freeThreshold && $subtotal > 0) {
            return [
                'charge' => 0.00,
                'reason' => 'Free delivery on orders above ' . mta_money($freeThreshold),
                'is_free' => true,
                'is_second_order' => $isSecondOrder,
            ];
        }

        // Rule 2: Free on 2nd order
        if ($isSecondOrder) {
            return [
                'charge' => 0.00,
                'reason' => 'Special promotion: Free delivery on your second order!',
                'is_free' => true,
                'is_second_order' => true,
            ];
        }

        // Rule 3 & 4: City based
        if ($cleanCity === 'multan') {
            return [
                'charge' => $multanCharge,
                'reason' => 'Minimum delivery charge for Multan local delivery',
                'is_free' => false,
                'is_second_order' => false,
            ];
        }

        return [
            'charge' => $standardCharge,
            'reason' => 'Standard courier delivery across Pakistan',
            'is_free' => false,
            'is_second_order' => false,
        ];
    }
}
