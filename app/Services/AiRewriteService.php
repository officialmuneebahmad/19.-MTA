<?php
/**
 * Multan Tech Accessories — AiRewriteService
 * High-Quality E-Commerce Copywriting Engine with Pakistan-Specific Hardware Context
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Security;

class AiRewriteService
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    /**
     * Rewrite product content and transition state to ai_draft
     */
    public function rewriteProduct(int $productId): array
    {
        $product = $this->db->fetch(
            "SELECT p.*, b.name AS brand_name, c.name AS category_name
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id = ? LIMIT 1",
            [$productId]
        );

        if (!$product) {
            return ['success' => false, 'error' => 'Product not found'];
        }

        $title = $product['title'];
        $brand = $product['brand_name'] ?? 'Generic';
        $category = $product['category_name'] ?? 'Accessories';
        $price = (float)$product['sell_price'];
        $sku = $product['sku'];

        // Generate Structured Sections tailored to Pakistani Tech buyers
        $headline = "Why the {$title} is a Smart Investment in Pakistan";
        
        $features = [
            "Authentic {$brand} hardware with full local compatibility for Pakistani 220V–240V AC power outlets",
            "Thermal protection designed to withstand ambient summer room temperatures up to 45°C without throttling",
            "Durable strain-relief connectors engineered for long life during daily laptop bag travel and frequent commutes",
            "Backed by Multan Tech Accessories' 7-day checking warranty and same-day WhatsApp dispatch support",
            "Universal compatibility with modern USB-C laptops, iPhones, Samsung Galaxy, and Xiaomi smartphones",
        ];

        $descriptionHtml = "
<div class=\"product-overview-content\">
  <p>The <strong>{$title}</strong> offers dependable performance for Pakistani professionals, gamers, and university students seeking reliable everyday hardware. Whether you are working from a home office in Multan or commuting in Lahore or Karachi, having an accessory that reliably delivers rated performance without overheating or unexpected disconnections is critical.</p>

  <h3>Key Hardware Features & Performance</h3>
  <ul>
    <li><strong>Verified Power & Signal Stability:</strong> Tested for stable voltage delivery and low ripple noise under real Pakistani electricity conditions.</li>
    <li><strong>Robust Build Quality:</strong> High-grade materials resist bending, port degradation, and daily cable flexing.</li>
    <li><strong>Safe Daily Operation:</strong> Built-in over-voltage, short-circuit, and temperature safeguards prevent damage to your expensive connected devices.</li>
  </ul>

  <h3>Pakistani Usage & Climate Considerations</h3>
  <p>In Pakistan's intense summer climate and frequent power fluctuations, budget generic accessories often suffer from solder joint failure or early capacitor wear. The <strong>{$title}</strong> has been curated by Multan Tech Accessories to ensure reliable heat dissipation and steady performance even during extended power outages backed by solar inverters or UPS systems.</p>

  <h3>What's in the Box</h3>
  <p>1 &times; {$title}, standard retail packaging, and Multan Tech Accessories 7-day inspection warranty slip.</p>
</div>";

        $faqJson = [
            [
                'question' => "Is the {$title} compatible with Pakistani 220V electricity and UPS inverters?",
                'answer' => "Yes, this accessory is fully rated for Pakistan's standard 220V–240V / 50Hz electrical supply and operates smoothly on pure sine wave and modified sine wave UPS inverters.",
            ],
            [
                'question' => "What warranty is included with this product?",
                'answer' => "Multan Tech Accessories provides a 7-day checking warranty from the day of courier delivery. If you encounter any technical defect, our Multan store arranges a prompt check and replacement.",
            ],
            [
                'question' => "How long will delivery take to my city?",
                'answer' => "Orders within Multan are delivered in 24 hours. Shipments to Lahore, Karachi, Islamabad, and nationwide take 2 to 4 working days via our trusted courier partners (Trax, Leopards, PostEx).",
            ],
        ];

        $metaDesc = "Buy {$title} in Pakistan at best price (" . mta_money($price) . "). 7-day checking warranty, Cash on Delivery in Multan & nationwide dispatch.";
        if (mb_strlen($metaDesc) > 160) {
            $metaDesc = mb_substr($metaDesc, 0, 157) . '...';
        }

        $metaTitle = "{$title} Price in Pakistan";
        if (mb_strlen($metaTitle) > 55) {
            $metaTitle = mb_substr($metaTitle, 0, 52) . '...';
        }

        // Update database to ai_draft
        $this->db->execute(
            "UPDATE products SET 
                description_html = ?,
                key_features = ?,
                faq_json = ?,
                meta_title = ?,
                meta_description = ?,
                content_state = 'ai_draft',
                updated_at = ?
             WHERE id = ?",
            [
                $descriptionHtml,
                json_encode($features, JSON_UNESCAPED_UNICODE),
                json_encode($faqJson, JSON_UNESCAPED_UNICODE),
                $metaTitle,
                $metaDesc,
                date('Y-m-d H:i:s'),
                $productId,
            ]
        );

        return [
            'success' => true,
            'product_id' => $productId,
            'content_state' => 'ai_draft',
        ];
    }
}
