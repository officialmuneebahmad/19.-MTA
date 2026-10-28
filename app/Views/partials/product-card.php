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
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 0.4rem;">
          <form action="/cart/add" method="POST">
            <?= mta_csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 0) ?>">
            <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;">
              Add to Cart
            </button>
          </form>
          <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-sm" style="width:100%; display:inline-flex; align-items:center; justify-content:center; gap:0.35rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.04 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67Z"/></svg>
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
