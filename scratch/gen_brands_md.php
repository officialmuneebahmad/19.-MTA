<?php
require __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$brands = $db->fetchAll(
    "SELECT b.id, b.name, b.slug, b.logo_path, COUNT(p.id) AS product_count
     FROM brands b
     LEFT JOIN products p ON p.brand_id = b.id AND p.is_active = 1
     WHERE b.is_active = 1
     GROUP BY b.id, b.name, b.slug, b.logo_path
     ORDER BY product_count DESC, b.name ASC"
);

$md = "# Brand Logos Needed — Multan Tech Accessories\n\n";
$md .= "This document lists all active brands in the MTA catalog sorted by active product count, along with the expected logo file names for single and bulk ZIP uploads.\n\n";
$md .= "> **Important Note for Store Owner:**\n";
$md .= "> Always use official, high-resolution logo files directly from brand press kits, distributor assets, or vector repositories (SVG preferred, or transparent PNG/WebP with at least 400px width). Never scrape or hotlink low-quality third-party thumbnails.\n\n";
$md .= "## Bulk Upload Instructions\n";
$md .= "1. Create a `.zip` file containing vector SVGs or transparent PNGs matching the **Expected File Name** column below (e.g., `faster.svg`, `baseus.png`).\n";
$md .= "2. Navigate to **Admin Console &rarr; Brand Logos & Featured &rarr; Bulk Logo Import (ZIP Archive)**.\n";
$md .= "3. Upload the archive. The system automatically sanitizes SVGs, optimizes rasters to crisp WebP at 2x resolution, and assigns each logo to its respective brand.\n\n";
$md .= "## Catalog Brands & Required Logos (" . count($brands) . " Brands Total)\n\n";
$md .= "| # | Brand Name | Slug | Active Products | Expected File Name (SVG / WebP / PNG) | Current Status |\n";
$md .= "|---|------------|------|-----------------|----------------------------------------|----------------|\n";

$i = 1;
foreach ($brands as $b) {
    $count = (int)$b['product_count'];
    $slug = $b['slug'];
    $status = !empty($b['logo_path']) ? "✓ Configured (`{$b['logo_path']}`)" : "⚠️ Logo Needed";
    $md .= "| {$i} | **{$b['name']}** | `{$slug}` | {$count} | `{$slug}.svg` / `{$slug}.webp` / `{$slug}.png` | {$status} |\n";
    $i++;
}

file_put_contents(MTA_ROOT . '/BRAND_LOGOS_NEEDED.md', $md);
echo "BRAND_LOGOS_NEEDED.md generated successfully with " . count($brands) . " brands." . PHP_EOL;

