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
$cleanProductUrl = mta_url('/products/' . ($product['slug'] ?? ''));
$brandName = !empty($product['brand_name']) ? $product['brand_name'] : (!empty($product['brand_title']) ? $product['brand_title'] : 'Tech Gear');
$title = $product['title'] ?? 'Tech Accessory';

$waOrderMsg = "Hi Multan Tech Accessories! I want to order: " . $title . " (" . ($sellPrice > 0 ? mta_money($sellPrice) : 'Price on WhatsApp') . "). Is it available for delivery?";
$waOrderUrl = mta_whatsapp_url('923070655275', $waOrderMsg);
$shareWaUrl = mta_share_whatsapp_url($title, $sellPrice, $cleanProductUrl);
$showWaOnCards = (mta_setting('show_wa_on_product_cards', '0') === '1');
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
        <span class="price-on-whatsapp">Ask Price</span>
      <?php endif; ?>
    </div>

    <div class="product-card-actions">
      <?php if ($status === 1 && $sellPrice > 0): ?>
        <div class="product-card-actions-row">
          <form action="/cart/add" method="POST" class="card-action-form" style="flex:1; margin:0; min-width:0;">
            <?= mta_csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)($product['id'] ?? 0) ?>">
            <button type="submit" class="btn btn-primary btn-card-btn" aria-label="Add <?= e($title) ?> to Cart">
              Add to Cart
            </button>
          </form>
          <?php if ($showWaOnCards): ?>
            <a href="<?= e($waOrderUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-card-btn" style="flex:1;" aria-label="Order <?= e($title) ?> on WhatsApp">
              <span>WhatsApp</span>
            </a>
          <?php endif; ?>
          <a href="<?= e($shareWaUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-icon" onclick="event.stopPropagation(); if (window.shareOnWhatsApp) window.shareOnWhatsApp('<?= e(addslashes($cleanProductUrl)) ?>', '<?= e(addslashes($title)) ?>');" title="Share" aria-label="Share <?= e($title) ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
          </a>
        </div>
      <?php elseif ($status === 3 || $sellPrice <= 0): ?>
        <div class="product-card-actions-row">
          <a href="<?= e(mta_whatsapp_url('923070655275', 'Hi Multan Tech Accessories! Please tell me price / availability for: ' . $title)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-card-btn" style="flex:1; gap:6px;" aria-label="Ask Price for <?= e($title) ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <span>Ask Price</span>
          </a>
          <a href="<?= e($shareWaUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-icon" onclick="event.stopPropagation(); if (window.shareOnWhatsApp) window.shareOnWhatsApp('<?= e(addslashes($cleanProductUrl)) ?>', '<?= e(addslashes($title)) ?>');" title="Share" aria-label="Share <?= e($title) ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
          </a>
        </div>
      <?php else: ?>
        <div class="product-card-actions-row">
          <a href="<?= e(mta_whatsapp_url('923070655275', 'Hi Multan Tech Accessories! Is this item returning in stock soon: ' . $title)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-card-btn" style="flex:1; gap:6px;" aria-label="Ask Availability for <?= e($title) ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <span>Ask Availability</span>
          </a>
          <a href="<?= e($shareWaUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-icon" onclick="event.stopPropagation(); if (window.shareOnWhatsApp) window.shareOnWhatsApp('<?= e(addslashes($cleanProductUrl)) ?>', '<?= e(addslashes($title)) ?>');" title="Share" aria-label="Share <?= e($title) ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
