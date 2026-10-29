<?php
/**
 * Multan Tech Accessories — Reusable Product Card Component
 * @var array $product
 */

$sellPrice = (float)($product['sell_price'] ?? 0);
$status = (int)($product['status'] ?? 1); // 1 InStock, 2 OutOfStock, 3 ComingSoon

$badgeClass = match ($status) {
    1 => 'badge-in-stock',
    2 => 'badge-out-of-stock',
    3 => 'badge-coming-soon',
    default => 'badge-in-stock',
};

$badgeText = match ($status) {
    1 => 'In Stock',
    2 => 'Out of Stock',
    3 => 'Coming Soon',
    default => 'In Stock',
};

$imageUrl = !empty($product['image_400']) ? $product['image_400'] : (!empty($product['primary_image_url']) ? $product['primary_image_url'] : '/assets/images/placeholder.webp');
$productUrl = '/products/' . e($product['slug'] ?? '');
$brandName = !empty($product['brand_name']) ? $product['brand_name'] : (!empty($product['brand_title']) ? $product['brand_title'] : 'Tech Gear');
$title = $product['title'] ?? 'Tech Accessory';

$waMsg = "Hi Multan Tech Accessories! I want to order: " . $title . " (" . mta_money($sellPrice) . "). Is it available for delivery?";
$waUrl = mta_whatsapp_url('923070655275', $waMsg);
?>
<div class="product-card" data-product-id="<?= (int)($product['id'] ?? 0) ?>">
  <div class="product-img-wrap">
    <div class="product-badge-wrap">
      <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
    </div>
    <a href="<?= $productUrl ?>" tabindex="-1" aria-hidden="true">
      <img src="<?= e($imageUrl) ?>" alt="<?= e($title) ?>" class="product-img" loading="lazy" width="300" height="300">
    </a>
  </div>

  <div class="product-card-body">
    <div class="product-brand"><?= e($brandName) ?></div>
    <h3 class="product-title">
      <a href="<?= $productUrl ?>"><?= e($title) ?></a>
    </h3>

    <div class="product-price">
      <?php if ($sellPrice > 0): ?>
        <?= mta_money($sellPrice) ?>
      <?php else: ?>
        <span style="font-size:0.9rem; color:var(--text-muted);">Ask Price on WhatsApp</span>
      <?php endif; ?>
    </div>

    <div class="product-card-actions">
      <?php if ($status === 1 && $sellPrice > 0): ?>
        <div class="product-card-actions-grid">
          <form action="/cart/add" method="POST" style="margin:0;">
            <?= mta_csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 0) ?>">
            <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;">
              Add to Cart
            </button>
          </form>
          <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-sm" style="width:100%; display:inline-flex; align-items:center; justify-content:center; gap:0.35rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12.01 2.002c-5.518 0-9.998 4.48-9.998 9.998 0 1.76.459 3.475 1.332 4.992L2.054 22l5.133-1.346a9.948 9.948 0 004.823 1.248h.004c5.517 0 9.997-4.48 9.997-9.998 0-2.67-1.04-5.18-2.93-7.07A9.928 9.928 0 0012.01 2.002zm5.82 14.156c-.24.674-1.398 1.288-1.928 1.348-.5.056-1.15.08-3.71-.978-2.56-1.058-4.2-3.666-4.33-3.838-.13-.172-1.03-1.37-1.03-2.613 0-1.242.648-1.854.88-2.106.23-.25.5-.314.67-.314.17 0 .34.002.49.01.16.008.37-.06.58.444.22.528.74 1.808.81 1.944.07.136.11.296.02.476-.09.18-.14.29-.27.444-.14.156-.29.35-.41.47-.14.138-.28.288-.12.564.16.276.71 1.17 1.52 1.894 1.05.934 1.93 1.224 2.21 1.36.27.136.43.116.59-.068.16-.184.69-.804.87-1.08.18-.276.36-.23.6-.138.24.092 1.52.716 1.78.846.26.13.43.196.49.304.07.108.07.624-.17 1.298z"/></svg>
            WhatsApp
          </a>
        </div>
      <?php elseif ($status === 3): ?>
        <a href="<?= e(mta_whatsapp_url('923070655275', "Hi, please inform me when this item arrives: " . $title)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="width:100%;">
          Inquire Coming Soon
        </a>
      <?php else: ?>
        <a href="<?= e(mta_whatsapp_url('923070655275', "Hi, is this item returning soon: " . $title)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="width:100%;">
          Check Availability
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>
