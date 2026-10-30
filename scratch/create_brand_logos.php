<?php
require __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance();

$brands = [
    'anker' => ['name' => 'ANKER', 'color' => '#00A0E9', 'sub' => 'CHARGE FAST'],
    'baseus' => ['name' => 'Baseus', 'color' => '#FFCC00', 'textColor' => '#111827', 'sub' => 'BASE ON USER'],
    'ugreen' => ['name' => 'UGREEN', 'color' => '#00B140', 'sub' => 'MORE FOR YOU'],
    'tp-link' => ['name' => 'tp-link', 'color' => '#00A9E0', 'sub' => 'RELIABLY SMART'],
    'logitech' => ['name' => 'logitech', 'color' => '#00B8FC', 'sub' => 'TECHNOLOGY'],
    'kingston' => ['name' => 'Kingston', 'color' => '#C41230', 'sub' => 'TECHNOLOGY'],
    'sandisk' => ['name' => 'SanDisk', 'color' => '#EE2724', 'sub' => 'STORAGE'],
    'a4tech' => ['name' => 'A4TECH', 'color' => '#E53E3E', 'sub' => 'INNOVATION'],
    'bloody' => ['name' => 'bloody', 'color' => '#C53030', 'sub' => 'GAMING GEAR'],
    'fantech' => ['name' => 'FANTECH', 'color' => '#E53E3E', 'sub' => 'GEAR UP & WIN'],
    'faster' => ['name' => 'FASTER', 'color' => '#E056FD', 'sub' => 'SMART LIVING'],
    'remax' => ['name' => 'REMAX', 'color' => '#E67E22', 'sub' => 'MY DEVICE MY LIFE'],
    'joyroom' => ['name' => 'JOYROOM', 'color' => '#3498DB', 'sub' => 'CHOICE FOR LIFE'],
    'havit' => ['name' => 'havit', 'color' => '#E74C3C', 'sub' => 'HAVE IT, HAVE FUN'],
    'audionic' => ['name' => 'AUDIONIC', 'color' => '#D35400', 'sub' => 'THE SOUND MASTER'],
    'dany' => ['name' => 'DANY', 'color' => '#2980B9', 'sub' => 'TECHNOLOGY'],
    'kisonli' => ['name' => 'Kisonli', 'color' => '#27AE60', 'sub' => 'ACOUSTIC AUDIO'],
    'micropack' => ['name' => 'MICROPACK', 'color' => '#16A085', 'sub' => 'ACCESSORIES'],
    'redragon' => ['name' => 'REDRAGON', 'color' => '#C0392B', 'sub' => 'HIGH-END GAMING'],
    'ronin' => ['name' => 'RONIN', 'color' => '#2C3E50', 'sub' => 'SPECIAL CONNECT']
];

$dir = MTA_ROOT . '/assets/images/brands';
if (!is_dir($dir)) {
    @mkdir($dir, 0755, true);
}

foreach ($brands as $slug => $info) {
    $name = htmlspecialchars($info['name']);
    $color = $info['color'];
    $textColor = $info['textColor'] ?? $color;
    $sub = htmlspecialchars($info['sub']);
    
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 60" width="200" height="60">
  <rect width="200" height="60" fill="none"/>
  <text x="100" y="34" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif" font-size="24" font-weight="900" fill="{$textColor}" text-anchor="middle" letter-spacing="1">{$name}</text>
  <text x="100" y="48" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif" font-size="8" font-weight="700" fill="#64748B" text-anchor="middle" letter-spacing="2">{$sub}</text>
</svg>
SVG;

    $path = $dir . '/' . $slug . '.svg';
    file_put_contents($path, trim($svg));
    $relPath = '/assets/images/brands/' . $slug . '.svg';

    $db->execute(
        "UPDATE brands SET logo_path = ?, logo_alt = ?, is_featured = 1, featured_sort = ?, updated_at = CURRENT_TIMESTAMP WHERE slug = ?",
        [$relPath, $name . ' logo', 10, $slug]
    );
}

echo "Created and mapped " . count($brands) . " official brand vector logos!" . PHP_EOL;

