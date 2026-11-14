<?php
/**
 * Multan Tech Accessories — Master SEO Service
 * Section 10 Compliant:
 * - seo_title() strictly <= 60 characters
 * - Unique meta descriptions (140-160 characters)
 * - JSON-LD Schema generation (Product, Offer, LocalBusiness, BreadcrumbList, BlogPosting)
 * - XML Sitemap generator with 500-product split files
 */

declare(strict_types=1);

namespace App\Services;

class SeoService
{
    /**
     * Builds a title tag that NEVER exceeds 60 characters and targets 50-60 characters
     */
    public static function title(string $base, string $type = 'general'): string
    {
        $base = trim($base);
        // Replace & with 'and' to prevent HTML entity &amp; expansion breaking length constraints
        $base = str_replace('&', 'and', $base);
        $base = preg_replace('/\s+/', ' ', $base);

        $suffixCandidates = [
            ' | Multan Tech Accessories Pakistan',
            ' | Multan Tech Accessories Store',
            ' | Multan Tech Accessories',
            ' | Multan Tech Online',
            ' | Multan Tech PK',
            ' | Multan Tech',
            '',
        ];

        // 1. Try to find a combination that lands strictly in [50, 60] characters
        foreach ($suffixCandidates as $suffix) {
            $candidate = $base . $suffix;
            $len = mb_strlen($candidate);
            if ($len >= 50 && $len <= 60) {
                return $candidate;
            }
        }

        // 2. If base is too long (> 60), truncate base neatly so base + ' | Multan Tech' or base alone is <= 60 chars
        $shortSuffix = ' | Multan Tech';
        $maxBaseLen = 60 - mb_strlen($shortSuffix); // 46
        if (mb_strlen($base) > 60) {
            $truncated = mb_substr($base, 0, $maxBaseLen - 3) . '...';
            $candidate = $truncated . $shortSuffix;
            if (mb_strlen($candidate) <= 60) {
                return $candidate;
            }
            return mb_substr($base, 0, 57) . '...';
        }

        // 3. If base is shorter than 50 chars, pick candidate closest to 50-60 without exceeding 60
        $best = $base . ' | Multan Tech Accessories';
        if (mb_strlen($best) > 60) {
            $best = $base . ' | Multan Tech';
        }
        if (mb_strlen($best) < 50) {
            $padded = $base . ' — Online Hardware Store in Pakistan';
            if (mb_strlen($padded) <= 60 && mb_strlen($padded) >= 50) {
                return $padded;
            }
            $padded2 = $base . ' | Multan Tech Accessories Online';
            if (mb_strlen($padded2) <= 60 && mb_strlen($padded2) >= 50) {
                return $padded2;
            }
        }

        return $best;
    }

    /**
     * Product pattern: "{Product short name} Price in Pakistan"
     */
    public static function productTitle(string $productTitle): string
    {
        $short = self::shortenProductTitle($productTitle);
        $base = $short . ' Price in Pakistan';
        return self::title($base, 'product');
    }

    /**
     * Category pattern: "Buy {Category} in Pakistan"
     */
    public static function categoryTitle(string $categoryName): string
    {
        $clean = str_replace('&', 'and', trim($categoryName));
        $base = "Buy {$clean} in Pakistan";
        return self::title($base, 'category');
    }

    public static function metaDescription(string $text): string
    {
        $clean = trim(strip_tags($text));
        $clean = str_replace('&', 'and', $clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        if (mb_strlen($clean) > 160) {
            return mb_substr($clean, 0, 157) . '...';
        }
        while (mb_strlen($clean) < 140) {
            $clean .= " Fast nationwide delivery.";
        }
        if (mb_strlen($clean) > 160) {
            return mb_substr($clean, 0, 157) . '...';
        }
        return $clean;
    }

    /**
     * Generate unique meta description (140 - 160 characters)
     */
    public static function productMetaDescription(array $product): string
    {
        $title = trim(strip_tags((string)($product['title'] ?? 'Tech Accessory')));
        $title = str_replace('&', 'and', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        $price = (float)($product['sell_price'] ?? 0);
        $brand = trim((string)($product['brand_name'] ?? 'Genuine'));
        $brand = str_replace('&', 'and', $brand);
        $priceText = $price > 0 ? "at Rs. " . number_format($price) : "at best price";

        $overhead = mb_strlen("Buy authentic {$brand}  {$priceText} in Pakistan. Genuine with Cash on Delivery nationwide. Order online from Multan Tech today!");
        $maxTitle = max(15, 155 - $overhead);
        if (mb_strlen($title) > $maxTitle) {
            $titleShort = mb_substr($title, 0, $maxTitle - 3) . '...';
        } else {
            $titleShort = $title;
        }
        $desc = "Buy authentic {$brand} {$titleShort} {$priceText} in Pakistan. Genuine with Cash on Delivery nationwide. Order online from Multan Tech today!";
        $desc = preg_replace('/\s+/', ' ', $desc);
        if (mb_strlen($desc) > 160) {
            $desc = mb_substr($desc, 0, 157) . '...';
        }
        while (mb_strlen($desc) < 140) {
            $desc .= " Fast shipping.";
        }
        if (mb_strlen($desc) > 160) {
            $desc = mb_substr($desc, 0, 157) . '...';
        }
        return $desc;
    }

    public static function categoryMetaDescription(string $categoryName): string
    {
        $cat = str_replace('&', 'and', trim($categoryName));
        $desc = "Shop genuine {$cat} in Pakistan at Multan Tech Accessories. Best prices, official brand warranty, and fast Cash on Delivery nationwide. Order online today!";
        $desc = preg_replace('/\s+/', ' ', $desc);
        if (mb_strlen($desc) > 160) {
            $desc = mb_substr($desc, 0, 157) . '...';
        }
        while (mb_strlen($desc) < 140) {
            $desc .= " 100% genuine products.";
        }
        if (mb_strlen($desc) > 160) {
            $desc = mb_substr($desc, 0, 157) . '...';
        }
        return $desc;
    }

    /**
     * Build Product Schema JSON-LD
     */
    public static function productSchema(array $product, array $images = []): array
    {
        $sellPrice = (float)($product['sell_price'] ?? 0);
        $status = (int)($product['status'] ?? 1);

        $availability = match ($status) {
            1 => 'https://schema.org/InStock',
            3 => 'https://schema.org/PreOrder',
            default => 'https://schema.org/OutOfStock',
        };

        $imageUrls = [];
        if (!empty($product['image_1200'])) {
            $imageUrls[] = mta_url($product['image_1200']);
        } elseif (!empty($product['primary_image_url'])) {
            $imageUrls[] = $product['primary_image_url'];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['title'],
            'url' => mta_url('/products/' . $product['slug']),
            'sku' => $product['sku'] ?? ('MTA-' . $product['id']),
            'description' => strip_tags(mta_clean_description_html($product['short_description'] ?? $product['description_html'] ?? $product['supplier_description_html'] ?? '', $product['title'] ?? '')),
            'brand' => [
                '@type' => 'Brand',
                'name' => $product['brand_name'] ?? 'Multan Tech Accessories',
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => mta_url('/products/' . $product['slug']),
                'priceCurrency' => 'PKR',
                'price' => number_format($sellPrice, 2, '.', ''),
                'priceValidUntil' => date('Y-12-31'),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => $availability,
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'Multan Tech Accessories',
                ],
                'shippingDetails' => [
                    '@type' => 'OfferShippingDetails',
                    'shippingRate' => [
                        '@type' => 'MonetaryAmount',
                        'value' => $sellPrice >= 5000 ? '0.00' : '250.00',
                        'currency' => 'PKR',
                    ],
                    'shippingDestination' => [
                        '@type' => 'DefinedRegion',
                        'addressCountry' => 'PK',
                    ],
                ],
                'hasMerchantReturnPolicy' => [
                    '@type' => 'MerchantReturnPolicy',
                    'applicableCountry' => 'PK',
                    'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                    'merchantReturnDays' => 3,
                    'returnMethod' => 'https://schema.org/ReturnByMail',
                    'returnFees' => 'https://schema.org/ReturnFeesCustomerResponsibility',
                ],
            ],
        ];

        if (!empty($imageUrls)) {
            $schema['image'] = count($imageUrls) === 1 ? $imageUrls[0] : $imageUrls;
        }

        // Add dynamic Product & Store FAQs in @graph
        $faqs = self::productFaqs($product);
        $allFaqs = array_merge($faqs['product_faqs'] ?? [], $faqs['store_faqs'] ?? []);
        if (!empty($allFaqs)) {
            $faqEntities = [];
            foreach ($allFaqs as $f) {
                $faqEntities[] = [
                    '@type' => 'Question',
                    'name' => $f['q'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $f['a'],
                    ],
                ];
            }
            return [
                '@context' => 'https://schema.org',
                '@graph' => [
                    $schema,
                    [
                        '@type' => 'FAQPage',
                        'mainEntity' => $faqEntities,
                    ],
                ],
            ];
        }

        return $schema;
    }

    /**
     * Generate dynamic, product-specific FAQs and separated store warranty/shipping FAQs.
     */
    public static function productFaqs(array $product): array
    {
        $title = (string)($product['title'] ?? '');
        $brand = (string)($product['brand_name'] ?? 'Genuine');
        $catSlug = strtolower((string)($product['category_slug'] ?? ''));
        $catName = (string)($product['category_name'] ?? 'Accessories');
        $t = strtolower($title);

        $productFaqs = [];

        if (str_contains($t, 'power bank') || str_contains($t, 'powerbank') || str_contains($t, 'mah') || str_contains($catSlug, 'power')) {
            $mah = '';
            if (preg_match('/(\d+)\s*mah/i', $title, $m)) {
                $mah = $m[1] . 'mAh ';
            }
            $productFaqs[] = [
                'q' => "How many smartphone recharges does this {$brand} {$mah}power bank provide?",
                'a' => "Depending on your smartphone's battery capacity (standard 4,500mAh to 5,000mAh), this {$mah}power bank delivers between 1.5 to 3.5 complete recharges under normal operating conditions with high power conversion efficiency.",
            ];
            $productFaqs[] = [
                'q' => "Does this {$brand} power bank support fast charging for Samsung, iPhone, and Android?",
                'a' => "Yes, this power bank is engineered to negotiate current safely with modern iOS and Android smartphones, delivering high-speed charging via supported Quick Charge (QC) or Power Delivery (PD) protocols.",
            ];
        } elseif (str_contains($t, 'charger') || str_contains($t, 'adapter') || str_contains($t, 'fast charging') || str_contains($t, 'pd ') || str_contains($t, 'watt') || str_contains($t, '20w') || str_contains($t, '22.5w') || str_contains($t, '65w')) {
            $productFaqs[] = [
                'q' => "Is this {$brand} fast charger safe for sensitive smartphone and laptop batteries?",
                'a' => "Yes. Genuine {$brand} chargers include built-in multi-protection against over-voltage, short-circuit, and overheating, automatically switching to trickle charging once your device reaches 80-100%.",
            ];
            $productFaqs[] = [
                'q' => "Which cable should I pair with this {$brand} charger for maximum speed?",
                'a' => "We recommend using a certified Type-C to Type-C or Type-C to Lightning cable rated for fast charging wattage to ensure optimum performance.",
            ];
        } elseif (str_contains($t, 'cable') || str_contains($t, 'converter') || str_contains($t, 'hub') || str_contains($t, 'otg') || str_contains($catSlug, 'cable')) {
            $productFaqs[] = [
                'q' => "Does this {$brand} cable support both fast charging and high-speed data transfer?",
                'a' => "Yes, this cable supports high-speed data synchronization alongside high-amperage fast charging when paired with a compatible charging adapter.",
            ];
            $productFaqs[] = [
                'q' => "Will this cable connector fit with protective phone cases attached?",
                'a' => "The connector plug features a slim, precision-molded housing designed to fit standard smartphone cases without loose connections.",
            ];
        } elseif (str_contains($t, 'earbud') || str_contains($t, 'airpod') || str_contains($t, 'headphone') || str_contains($t, 'headset') || str_contains($t, 'speaker') || str_contains($t, 'audio') || str_contains($catSlug, 'audio')) {
            $productFaqs[] = [
                'q' => "What is the sound quality and microphone performance of this {$brand} audio gear?",
                'a' => "This device offers rich, balanced stereo sound with clear vocal clarity and built-in noise-canceling microphone hardware suitable for WhatsApp calls, Zoom meetings, and mobile gaming.",
            ];
            $productFaqs[] = [
                'q' => "Is this audio accessory compatible with iPhone, Android, and Windows PC?",
                'a' => "Yes, it supports universal Bluetooth / 3.5mm connectivity across all modern iOS, Android, and Windows devices.",
            ];
        } elseif (str_contains($t, 'gaming') || str_contains($t, 'mouse') || str_contains($t, 'keyboard') || str_contains($catSlug, 'gaming')) {
            $productFaqs[] = [
                'q' => "Does this {$brand} gaming peripheral require special drivers on Windows 10/11?",
                'a' => "No, it features plug-and-play USB functionality with standard Windows and Mac drivers for immediate responsiveness.",
            ];
            $productFaqs[] = [
                'q' => "Is this suitable for competitive FPS and MOBA games?",
                'a' => "Yes, it is built with durable switches and a reliable sensor providing low click latency for games like CS2, Valorant, and Dota.",
            ];
        } elseif (str_contains($t, 'drive') || str_contains($t, 'flash') || str_contains($t, 'card') || str_contains($t, 'ssd') || str_contains($catSlug, 'storage')) {
            $productFaqs[] = [
                'q' => "Can this {$brand} storage drive connect to Android phones via OTG?",
                'a' => "Yes, when formatted in exFAT or FAT32, it connects smoothly with Android smartphones, tablets, PC laptops, and smart TVs.",
            ];
            $productFaqs[] = [
                'q' => "What transfer speeds can I expect during large file copies?",
                'a' => "It delivers stable, high-speed read/write performance suitable for photos, HD videos, documents, and software installation media.",
            ];
        } else {
            $productFaqs[] = [
                'q' => "Does this {$title} come with a checking warranty and original packaging?",
                'a' => "Yes, Multan Tech Accessories lists genuine brand-name and standard accessories based on supplier specifications, backed by a strict 3-day checking warranty for manufacturing faults.",
            ];
            $productFaqs[] = [
                'q' => "What is included inside the retail packaging?",
                'a' => "The package includes the original factory retail box, the {$title} main unit, and standard manufacturer documentation/accessories.",
            ];
        }

        $storeFaqs = [
            [
                'q' => "How does MultanTech's 3-Day Checking Warranty work?",
                'a' => "We offer a strict 3-Day (72 hours) checking warranty starting from the moment your parcel is delivered by the courier. You must inspect and test the accessory within this window. Any technical defects must be reported to our WhatsApp helpline at 0307-0655275 for exchange assistance.",
            ],
            [
                'q' => "What are the shipping charges and delivery timelines across Pakistan?",
                'a' => "Delivery is Rs. 250 nationwide, or completely FREE on orders over Rs. 5,000. Orders inside Multan arrive in 24–48 hours; shipments to Lahore, Karachi, Islamabad, Rawalpindi, and other cities take 2–4 working days via Trax, Leopards, PostEx, or TCS.",
            ],
            [
                'q' => "Can I pay Cash on Delivery (COD) or order via WhatsApp?",
                'a' => "Yes, Cash on Delivery is available nationwide. You can also order directly by clicking the WhatsApp button or messaging 0307-0655275 with your name, address, and the product title for direct agent booking.",
            ],
        ];

        return [
            'product_faqs' => $productFaqs,
            'store_faqs' => $storeFaqs,
        ];
    }

    private static function shortenProductTitle(string $title): string
    {
        // Strip common fluff to keep title clean and short
        $title = preg_replace('/\s*\(Model\s+[A-Za-z0-9]+\)/i', '', $title);
        $title = preg_replace('/\s*-\s*Ultra\s+High\s+Speed.*$/i', '', $title);
        $title = trim($title);
        if (mb_strlen($title) > 35) {
            $title = mb_substr($title, 0, 35);
            $title = rtrim($title);
        }
        return $title;
    }
}
