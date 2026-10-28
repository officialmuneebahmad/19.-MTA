<?php
/**
 * Multan Tech Accessories — 991 Mock Products Generator
 * Generates exact verified shape from api.computerzone.pk with all required edge cases
 */

declare(strict_types=1);

$brands = [
    ['id' => '101', 'title' => 'Anker'],
    ['id' => '102', 'title' => 'Baseus'],
    ['id' => '103', 'title' => 'Ugreen'],
    ['id' => '104', 'title' => 'A4Tech'],
    ['id' => '105', 'title' => 'Bloody'],
    ['id' => '106', 'title' => 'Logitech'],
    ['id' => '107', 'title' => 'Redragon'],
    ['id' => '108', 'title' => 'Fantech'],
    ['id' => '109', 'title' => 'Kingston'],
    ['id' => '110', 'title' => 'SanDisk'],
    ['id' => '111', 'title' => 'TP-Link'],
    ['id' => '112', 'title' => 'Kisonli'],
    ['id' => '113', 'title' => 'Faster'],
    ['id' => '114', 'title' => 'Audionic'],
    ['id' => '115', 'title' => 'Ronin'],
    ['id' => '116', 'title' => 'Dany'],
    ['id' => '117', 'title' => 'Remax'],
    ['id' => '118', 'title' => 'Joyroom'],
    ['id' => '119', 'title' => 'Havit'],
    ['id' => '120', 'title' => 'Micropack'],
];

$categories = [
    [
        'cat_id' => '201', 'cat_title' => 'Speakers & Audio',
        'subcats' => [
            ['sub_id' => '301', 'sub_title' => 'Bluetooth Speakers'],
            ['sub_id' => '302', 'sub_title' => '2.1 & 4.1 Home Speakers'],
            ['sub_id' => '303', 'sub_title' => 'USB Desktop Speakers'],
            ['sub_id' => '304', 'sub_title' => 'Wireless Soundbars'],
        ]
    ],
    [
        'cat_id' => '202', 'cat_title' => 'Power & Chargers',
        'subcats' => [
            ['sub_id' => '305', 'sub_title' => 'Fast Wall Chargers'],
            ['sub_id' => '306', 'sub_title' => 'GaN Type-C Chargers'],
            ['sub_id' => '307', 'sub_title' => 'Power Banks 10000mAh'],
            ['sub_id' => '308', 'sub_title' => 'Power Banks 20000mAh+'],
            ['sub_id' => '309', 'sub_title' => 'Wireless Charging Pads'],
        ]
    ],
    [
        'cat_id' => '203', 'cat_title' => 'Cables & Converters',
        'subcats' => [
            ['sub_id' => '310', 'sub_title' => 'Type-C Fast Charging Cables'],
            ['sub_id' => '311', 'sub_title' => 'Lightning Cables for iPhone'],
            ['sub_id' => '312', 'sub_title' => 'HDMI 4K 60Hz Cables'],
            ['sub_id' => '313', 'sub_title' => 'OTG Adapters & Hubs'],
            ['sub_id' => '314', 'sub_title' => 'Aux & Audio Splitters'],
        ]
    ],
    [
        'cat_id' => '204', 'cat_title' => 'Gaming Accessories',
        'subcats' => [
            ['sub_id' => '315', 'sub_title' => 'RGB Mechanical Keyboards'],
            ['sub_id' => '316', 'sub_title' => 'High-DPI Gaming Mice'],
            ['sub_id' => '317', 'sub_title' => '7.1 Surround Gaming Headsets'],
            ['sub_id' => '318', 'sub_title' => 'Laptop Cooling Pads'],
            ['sub_id' => '319', 'sub_title' => 'Speed Gaming Mousepads'],
        ]
    ],
    [
        'cat_id' => '205', 'cat_title' => 'Computer Peripherals',
        'subcats' => [
            ['sub_id' => '320', 'sub_title' => 'Wireless Mouse & Keyboard Combos'],
            ['sub_id' => '321', 'sub_title' => '1080p FHD Webcams'],
            ['sub_id' => '322', 'sub_title' => 'USB 3.0 Multiport Hubs'],
            ['sub_id' => '323', 'sub_title' => 'External HDD/SSD Enclosures'],
        ]
    ],
    [
        'cat_id' => '206', 'cat_title' => 'Storage & Memory',
        'subcats' => [
            ['sub_id' => '324', 'sub_title' => 'USB 3.2 Flash Drives (32GB-256GB)'],
            ['sub_id' => '325', 'sub_title' => 'Class 10 MicroSD Cards'],
            ['sub_id' => '326', 'sub_title' => 'High-Speed Card Readers'],
        ]
    ],
    [
        'cat_id' => '207', 'cat_title' => 'Networking Devices',
        'subcats' => [
            ['sub_id' => '327', 'sub_title' => 'Dual Band Wi-Fi Routers'],
            ['sub_id' => '328', 'sub_title' => 'USB Wi-Fi Adapters with Antenna'],
            ['sub_id' => '329', 'sub_title' => 'Cat6 Gigabit Patch Cables'],
        ]
    ],
    [
        'cat_id' => '208', 'cat_title' => 'Mobile Gear & CCTV',
        'subcats' => [
            ['sub_id' => '330', 'sub_title' => 'Magnetic Car Phone Mounts'],
            ['sub_id' => '331', 'sub_title' => 'TWS Wireless Earbuds with ANC'],
            ['sub_id' => '332', 'sub_title' => 'Smart Wi-Fi Security Cameras'],
        ]
    ],
];

$productTemplates = [
    'Speaker' => [
        'Kisonli X11 RGB Gamer PC Mini USB Speaker with 6W Power',
        'Audionic Sugar 40 Bluetooth Portable Speaker with Mic Support',
        'Baseus AeQur V2 Wireless Outdoor Waterproof Speaker',
        'Faster FS-22 Soundbar Bluetooth 5.0 with Rich Bass Subwoofer',
        'Ronin R-7700 Heavy Bass Party Speaker with Dynamic LED Light',
    ],
    'Charger' => [
        'Anker 735 GaNPrime 65W 3-Port Fast Wall Charger Type-C',
        'Baseus GaN5 Pro 100W Dual Type-C + USB Fast Charger with 100W Cable',
        'Ugreen Nexode 65W GaN Fast Charger for MacBook iPhone Galaxy',
        'Faster FC-66 20W PD Type-C Quick Charger with Cable',
        'Joyroom 30W Dual Port PD+QC3.0 Mini Fast Wall Plug',
    ],
    'PowerBank' => [
        'Baseus Bipow 20000mAh 20W Digital Display Fast Power Bank',
        'Anker PowerCore 10000mAh Ultra-Compact High-Speed Power Bank',
        'Remax RPP-296 20000mAh Dual USB Output Fast Charging Power Bank',
        'Ronin R-97 10000mAh Wireless + PD Fast Power Bank',
        'Faster W-20 20000mAh 22.5W Super Fast Charging Power Bank',
    ],
    'Cable' => [
        'Baseus Cafule 2M 100W PD 5A Nylon Braided Type-C to Type-C Cable',
        'Ugreen 2-in-1 USB-C to Lightning Fast Charging Cable MFi Certified',
        'Anker PowerLine III 6ft Heavy Duty USB-C to USB-C Cable',
        'Faster FC-09 60W Type-C Braided Fast Data Sync Cable',
        'Joyroom S-1230K4 3A Fast Charging Micro USB Tough Cable',
    ],
    'Gaming' => [
        'Redragon K552 Kumara RGB Mechanical Gaming Keyboard Blue Switches',
        'A4Tech Bloody A70 Light Strike Optical Gaming Mouse 4000 DPI',
        'Fantech HG11 Captain 7.1 Virtual Surround Sound RGB Headset',
        'Logitech G102 Lightsync Optical Gaming Mouse 8000 DPI',
        'Havit HV-F2056 Ultra-Slim 3-Fan Laptop Cooling Pad 17 Inch',
    ],
    'Storage' => [
        'Kingston DataTraveler Exodia 64GB USB 3.2 Flash Drive with Cap',
        'SanDisk Ultra 128GB MicroSDXC UHS-I Card 100MB/s for Smartphones',
        'Kingston Canvas Select Plus 64GB MicroSD Card with SD Adapter',
        'SanDisk Cruzer Blade 32GB High Speed USB 2.0 Pen Drive',
        'Baseus Full Speed Type-C M.2 NVMe SSD Enclosure 10Gbps',
    ],
    'Networking' => [
        'TP-Link Archer C6 AC1200 Wireless MU-MIMO Gigabit Wi-Fi Router',
        'TP-Link TL-WN722N 150Mbps High Gain Wireless USB Adapter with Antenna',
        'TP-Link TL-WR840N 300Mbps Wireless N Speed Router 2 Antenna',
        'D-Link DWA-131 Wireless N Nano USB Adapter 300Mbps',
    ],
];

$allProducts = [];
$totalCount = 991;

// Deterministic seed
mt_srand(20261028);

for ($i = 1; $i <= $totalCount; $i++) {
    $productId = str_pad((string)$i, 5, '0', STR_PAD_LEFT); // e.g. "00001", "01596"
    
    // Pick category & subcategory
    $catIndex = ($i - 1) % count($categories);
    $category = $categories[$catIndex];
    $catId = $category['cat_id'];
    $catTitle = $category['cat_title'];
    
    $subcats = $category['subcats'];
    $subcat = $subcats[($i - 1) % count($subcats)];
    $subId = $subcat['sub_id'];
    $subTitle = $subcat['sub_title'];
    
    // Pick Brand
    $brand = $brands[($i - 1) % count($brands)];
    $brandId = $brand['id'];
    $brandTitle = $brand['title'];

    // Generate Title
    $keys = array_keys($productTemplates);
    $typeKey = $keys[($i - 1) % count($keys)];
    $templates = $productTemplates[$typeKey];
    $baseTitle = $templates[($i - 1) % count($templates)];
    $title = $baseTitle . ' (Model ' . strtoupper(substr(md5((string)$i), 0, 4)) . ')';

    // Status: 1=In Stock (~80%), 2=Out of Stock (~12%), 3=Coming Soon (~8%)
    $statusRoll = $i % 25;
    if ($statusRoll === 0 || $statusRoll === 7) {
        $statusId = 2; // Out of stock
        $statusTitle = 'Out of Stock';
    } elseif ($statusRoll === 13 || $statusRoll === 21) {
        $statusId = 3; // Coming soon
        $statusTitle = 'Coming Soon';
    } else {
        $statusId = 1; // In stock
        $statusTitle = 'In Stock';
    }

    // Pricing: realistic PKR prices between 450 and 24,500
    // Edge case: Price "0" for ~15 items
    if ($i % 65 === 0) {
        $price = "0";
    } else {
        $basePrice = 450 + (($i * 73) % 22000);
        $price = (string)(ceil($basePrice / 50) * 50);
    }

    // HTML Detail with specs and formatting
    $htmlDetail = "<p>Authentic <strong>{$brandTitle}</strong> tech accessory engineered for reliable daily performance. Perfect companion for your smartphone, PC, or gaming workstation in Pakistan.</p>"
        . "<h3>Key Specifications:</h3>"
        . "<ul>"
        . "<li>Brand: <strong>{$brandTitle}</strong></li>"
        . "<li>Category: {$catTitle} &mdash; {$subTitle}</li>"
        . "<li>Build Material: High-durability flame-retardant finish</li>"
        . "<li>Compatibility: Universal support across Android, iOS, Windows, Mac</li>"
        . "<li>Official Supplier Warranty Status: Standard supplier checked</li>"
        . "</ul>"
        . "<p>Shipped directly across Multan and nationwide Pakistan with Cash on Delivery support.</p>";

    // Edge Cases explicitly introduced as per prompt section 3 & 10:
    // 1. Edge case: null subcategory (~30 items)
    if ($i % 33 === 0) {
        $subId = null;
        $subTitle = null;
    }
    // 2. Edge case: missing/empty brand (~20 items)
    if ($i % 47 === 0) {
        $brandId = null;
        $brandTitle = 'Generic';
    }
    // 3. Edge case: HTML entities in title (&amp;, &quot;, &#39;)
    if ($i % 19 === 0) {
        $title = str_replace(' with ', ' &amp; ', $title);
        $title .= ' "Pro Edition"';
    }
    // 4. Edge case: Very long title (>120 chars)
    if ($i % 41 === 0) {
        $title .= ' - Ultra High Speed Heavy Duty Premium Grade Certified Compatible with All Modern Devices and Accessories';
    }

    $imageNum = ($i % 30) + 1;
    $imageUrl = "https://cdn.computerzone.pk/products/img_{$imageNum}.webp";
    $productLink = "https://www.computerzone.pk/products/{$productId}/" . rawurlencode(strtolower(str_replace(' ', '-', $title)));

    $allProducts[] = [
        'ProductId' => $productId,
        'ProductCode' => ($i % 5 === 0) ? 'MTA-' . strtoupper(substr(md5((string)$i), 0, 6)) : null,
        'ProductTitle' => $title,
        'ProductType' => 'Hardware',
        'ProductPrice' => $price,
        'ProductImage' => $imageUrl,
        'ProductLink' => $productLink,
        'ProductCategoryId' => $catId,
        'ProductCategoryTitle' => $catTitle,
        'ProductSubCategoryId' => $subId,
        'ProductSubCategoryTitle' => $subTitle,
        'ProductDetail' => $htmlDetail,
        'ProductBrandId' => $brandId,
        'ProductBrandTitle' => $brandTitle,
        'ProductStatusId' => $statusId,
        'ProductStatusTitle' => $statusTitle,
        'ProductUpdate' => '2026-10-28 12:00:00',
    ];
}

$output = [
    'status' => 'success',
    'code' => 200,
    'message' => 'Record Found.',
    'Products' => $allProducts,
];

file_put_contents(__DIR__ . '/products.json', json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "Successfully generated exactly " . count($allProducts) . " products in " . __DIR__ . "/products.json\n";
