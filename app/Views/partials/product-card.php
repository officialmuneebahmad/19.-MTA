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
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M18.403 5.638A8.955 8.955 0 0 0 12.053 3c-4.968 0-9.01 4.042-9.01 9.013 0 1.587.413 3.134 1.198 4.492L3 21l4.634-1.216a8.972 8.972 0 0 0 4.417 1.156h.004c4.968 0 9.007-4.042 9.007-9.014 0-2.408-.937-4.672-2.659-6.288zm-6.35 13.785h-.004a7.466 7.466 0 0 1-3.805-1.042l-.273-.162-2.827.741.755-2.756-.177-.282a7.448 7.448 0 0 1-1.144-3.909c0-4.135 3.364-7.499 7.502-7.499 2.004 0 3.887.782 5.301 2.2 1.415 1.417 2.193 3.303 2.193 5.308 0 4.136-3.365 7.499-7.498 7.499zm4.113-5.618c-.225-.113-1.334-.659-1.541-.734-.206-.075-.356-.113-.506.113-.15.225-.581.734-.712.884-.131.15-.262.169-.487.056-.225-.113-.951-.351-1.811-1.118-.67-.598-1.122-1.337-1.253-1.562-.131-.225-.014-.347.098-.459.102-.101.225-.263.338-.395.112-.131.15-.225.225-.375.075-.15.037-.281-.019-.394-.056-.113-.506-1.219-.694-1.67-.182-.439-.368-.379-.506-.386l-.431-.007c-.15 0-.394.056-.6.281-.206.225-.788.77-.788 1.876 0 1.107.806 2.176.919 2.326.112.15 1.586 2.423 3.843 3.398.537.232.956.371 1.283.475.54.172 1.031.148 1.419.09.432-.065 1.334-.545 1.522-1.07.188-.526.188-.977.131-1.07-.056-.094-.206-.15-.431-.263z" fill="#FFFFFF"/></svg>
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
