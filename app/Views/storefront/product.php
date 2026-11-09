<?php
/**
 * Multan Tech Accessories — Single Product Detail View
 * Section 8 & Part A Compliant:
 * - Single H1, gallery, price, status badge
 * - WhatsApp-first share button + Copy Link button with toast (A1)
 * - City delivery charge calculator
 * - Add to Cart + WhatsApp Order using unified button components (A2)
 * - Single source of truth pricing (A3)
 * - Product description & details spacing (.prose-mta) with clean HTML (A4)
 * - FAQ accordion (<details>) & Trust Strip
 * - Breadcrumbs, related products
 */

$sellPrice = (float)($product['sell_price'] ?? 0);
$supplierPrice = (float)($product['supplier_price'] ?? 0);
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

$imageUrl = mta_product_image($product, 800);
if (empty($og_image)) {
    $og_image = $imageUrl;
    if (str_starts_with($og_image, '/')) {
        $og_image = mta_url($og_image);
    }
}
$brandName = !empty($product['brand_name']) ? $product['brand_name'] : 'Authentic Brand';
$title = $product['title'] ?? 'Tech Accessory';
$sku = $product['sku'] ?? ('MTA-' . $product['id']);

$cleanProductUrl = mta_url('/products/' . ($product['slug'] ?? ''));
$waMsg = "Hi Multan Tech Accessories! I want to order:\n*" . $title . "*\nPrice: " . ($sellPrice > 0 ? mta_money($sellPrice) : 'Price on WhatsApp') . "\nLink: " . $cleanProductUrl . "\nPlease confirm availability and delivery address.";
$waUrl = mta_whatsapp_url('923070655275', $waMsg);
$shareWaUrl = mta_share_whatsapp_url($title, $sellPrice, $cleanProductUrl);
?>

<div class="container" style="padding-top: 1rem;">
  
  <!-- Breadcrumbs -->
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <!-- Main Product Detail Layout -->
  <div class="product-layout-grid">
    
    <!-- Left: Image Gallery -->
    <div class="product-gallery-wrap">
      <div class="product-gallery-card">
        <img id="main-product-img" src="<?= e($imageUrl) ?>" alt="<?= e($title) ?>" width="600" height="600" style="max-height:480px; width:auto; margin:0 auto; object-fit:contain;" fetchpriority="high" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src='/assets/images/placeholder.svg';">
        
        <?php if (!empty($images) && count($images) > 1): ?>
          <div style="display:flex; gap:0.75rem; margin-top:1.5rem; justify-content:center; overflow-x:auto;">
            <?php foreach ($images as $img): ?>
              <button type="button" onclick="document.getElementById('main-product-img').src='<?= e($img['url']) ?>'" style="width:60px; height:60px; border-radius:12px; border:1px solid var(--border); overflow:hidden; padding:4px; background:var(--surface-2); cursor:pointer;">
                <img src="<?= e($img['url']) ?>" alt="Thumbnail" width="60" height="60" referrerpolicy="no-referrer" style="object-fit:cover; width:100%; height:100%;">
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Right: Product Information & Purchase Actions -->
    <div>
      
      <!-- Brand & Status -->
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem;">
        <span style="font-size:0.85rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); letter-spacing:0.5px; display:inline-flex; align-items:center; gap:0.4rem;">
          Brand: 
          <?php if (!empty($product['brand_logo_path'])): ?>
            <img src="<?= e($product['brand_logo_path']) ?>" alt="<?= e($product['brand_logo_alt'] ?: ($brandName . ' logo')) ?>" style="max-height:20px; max-width:60px; object-fit:contain; background:#FFFFFF; padding:2px 4px; border-radius:4px; border:1px solid var(--border);" loading="lazy">
          <?php endif; ?>
          <a href="/brands/<?= e($product['brand_slug'] ?? '') ?>" style="color:var(--accent-strong);"><?= e($brandName) ?></a>
        </span>
        <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
      </div>

      <!-- Title & Share Buttons Row (A1) -->
      <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:0.75rem;">
        <h1 style="font-size:1.85rem; line-height:1.3; margin:0; flex:1;"><?= e($title) ?></h1>
        <div style="display:flex; align-items:center; gap:0.5rem; flex-shrink:0;">
          <a href="<?= e($shareWaUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-icon" onclick="if(window.shareOnWhatsApp) window.shareOnWhatsApp('<?= e(addslashes($cleanProductUrl)) ?>', '<?= e(addslashes($title)) ?>');" title="Share" aria-label="Share <?= e($title) ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
          </a>
          <button type="button" class="btn btn-icon" onclick="copyProductLink('<?= e(addslashes($cleanProductUrl)) ?>');" title="Copy Link" aria-label="Copy Link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
          </button>
        </div>
      </div>

      <!-- SKU -->
      <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1.25rem;">
        SKU: <strong><?= e($sku) ?></strong>
      </div>

      <!-- Price Box (Neat & Professional, Never wraps Rs. from amount) -->
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.25rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem;">
          <div style="display:flex; align-items:baseline; gap:0.5rem; white-space:nowrap;">
            <?php if ($sellPrice > 0): ?>
              <span style="font-size:2rem; font-weight:800; color:var(--accent-strong); font-family:var(--font-heading); white-space:nowrap; letter-spacing:-0.5px;">
                <?= mta_money($sellPrice) ?>
              </span>
            <?php else: ?>
              <span style="font-size:1.3rem; font-weight:700; color:var(--text);">Ask Price on WhatsApp</span>
            <?php endif; ?>
          </div>
          <span style="font-size:0.8rem; font-weight:600; color:var(--text-muted); background:var(--surface-2); padding:0.35rem 0.65rem; border-radius:8px; border:1px solid var(--border); white-space:nowrap;">
            PKR &bull; Incl. all local taxes
          </span>
        </div>
      </div>

      <!-- City-Based Delivery Charge Preview -->
      <div style="background:var(--surface-2); border:1px solid var(--border); border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.5rem;">
        <label for="city-calc" style="font-size:0.85rem; font-weight:700; display:block; margin-bottom:0.35rem;">
          Estimate Delivery Charges for Your City:
        </label>
        <div style="display:flex; gap:0.75rem; align-items:center;">
          <select id="city-calc" onchange="updateDeliveryEstimate(this.value)" style="padding:0.6rem 0.85rem; border-radius:8px; border:1px solid var(--border); background:var(--surface); color:var(--text); font-size:0.9rem; font-weight:600; flex:1; -webkit-appearance:menulist; appearance:menulist;">
            <option value="multan" selected>Multan (Local Delivery - Rs. 100)</option>
            <option value="lahore">Lahore (Rs. 250)</option>
            <option value="karachi">Karachi (Rs. 250)</option>
            <option value="islamabad">Islamabad / Rawalpindi (Rs. 250)</option>
            <option value="faisalabad">Faisalabad (Rs. 250)</option>
            <option value="other">Other Pakistani City (Rs. 250)</option>
          </select>
        </div>
        <div id="delivery-calc-result" style="font-size:0.85rem; margin-top:0.6rem; color:var(--success); font-weight:600;">
          <?php if ($sellPrice >= 5000): ?>
            &#10003; <strong>FREE DELIVERY</strong> (Cart value is Rs. 5,000 or above!)
          <?php else: ?>
            Multan Delivery: <strong>Rs. 100</strong> (Cash on Delivery). Free on 2nd order!
          <?php endif; ?>
        </div>
      </div>

      <!-- Action Buttons (A2 Component System) -->
      <div class="product-actions-group" style="display:flex; flex-direction:column; gap:0.85rem; margin-bottom:2rem;">
        <?php if ($status === 1 && $sellPrice > 0): ?>
          <form action="/cart/add" method="POST" style="margin:0; display:flex; flex-direction:column; gap:0.75rem;">
            <?= mta_csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

            <!-- Quantity Selector -->
            <div style="display:flex; align-items:center; justify-content:space-between; background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:0.55rem 0.85rem;">
              <span style="font-size:0.9rem; font-weight:700; color:var(--text);">Quantity:</span>
              <div style="display:flex; align-items:center; gap:0.4rem;">
                <button type="button" onclick="const q=document.getElementById('product-qty'); if(parseInt(q.value)>1) q.value=parseInt(q.value)-1;" style="width:36px; height:36px; border-radius:8px; background:var(--surface-2); border:1px solid var(--border); color:var(--text); font-size:1.15rem; font-weight:700; display:flex; align-items:center; justify-content:center; cursor:pointer;" aria-label="Decrease quantity">&minus;</button>
                <input type="number" id="product-qty" name="quantity" value="1" min="1" max="10" readonly style="width:44px; height:36px; text-align:center; font-weight:800; font-size:1rem; border:1px solid var(--border); border-radius:8px; background:var(--surface-2); color:var(--text);">
                <button type="button" onclick="const q=document.getElementById('product-qty'); if(parseInt(q.value)<10) q.value=parseInt(q.value)+1;" style="width:36px; height:36px; border-radius:8px; background:var(--surface-2); border:1px solid var(--border); color:var(--text); font-size:1.15rem; font-weight:700; display:flex; align-items:center; justify-content:center; cursor:pointer;" aria-label="Increase quantity">&plus;</button>
              </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
              <span>Add to Shopping Cart</span>
            </button>
          </form>

          <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="width:100%; gap:8px;" aria-label="Order <?= e($title) ?> on WhatsApp">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <span>Order on WhatsApp &rarr;</span>
          </a>

          <a href="tel:+923070655275" class="btn btn-secondary" style="width:100%;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            <span>Call Us for Inquiry (0307-0655275)</span>
          </a>
        <?php elseif ($status === 3): ?>
          <div class="alert alert-warning" style="background:#FEF3C7; color:#92400E; padding:1rem; border-radius:12px;">
            <strong>Coming Soon:</strong> This product is currently in transit. Message us on WhatsApp to get notified immediately upon arrival.
          </div>
          <a href="<?= e(mta_whatsapp_url('923070655275', "Hi, please notify me when this item arrives: " . $title)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="width:100%; gap:8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <span>Notify Me on WhatsApp</span>
          </a>
        <?php else: ?>
          <div class="alert alert-danger" style="background:var(--danger-bg); color:var(--danger); padding:1rem; border-radius:12px;">
            <strong>Out of Stock:</strong> This item is temporarily out of stock. Contact us on WhatsApp for expected restock date or recommended alternatives.
          </div>
          <a href="<?= e(mta_whatsapp_url('923070655275', "Hi, do you have alternatives or restock date for: " . $title)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="width:100%; gap:8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <span>Ask for Availability on WhatsApp</span>
          </a>
        <?php endif; ?>
      </div>

      <!-- Advance Booking Security Notice -->
      <?php require MTA_APP . '/Views/partials/advance-caution-notice.php'; ?>

      <!-- Trust Badges & Policies -->
      <div style="border-top:1px solid var(--border); padding-top:1.25rem; font-size:0.875rem; color:var(--text-muted); line-height:1.8;">
        <div>&#10003; <strong>Warranty:</strong> <span style="color:var(--text); font-weight:700;">We Offer 3 Days Check Warranty Only</span> (Test upon arrival).</div>
        <div>&#10003; <strong>Returns & Refunds:</strong> <span style="color:var(--text); font-weight:700;">No Refund, Exchange can be made only for certain circumstances</span>.</div>
        <div>&#10003; <strong>Courier Charges:</strong> <span style="color:var(--text); font-weight:700;">The Customer has to Return parcel with their own money & courier</span>.</div>
        <div>&#10003; <strong>Payment:</strong> Cash on Delivery (COD) &bull; <strong style="color: var(--danger, #DC2626);">Rs. 100/- Advance for Booking</strong> (Security Purpose).</div>
        <div>&#10003; <strong>Store Pickup:</strong> Al-Quresh Phase 3, Sher Shah Road, Multan.</div>
      </div>

    </div>
  </div>

  <!-- Detailed Specifications, Key Features & Description (A4) -->
  <section class="section product-page-section" style="border-top:1px solid var(--border);">
    <div style="max-width:850px;">
      
      <h2 style="font-size:1.6rem; margin-bottom:1.5rem; font-family:var(--font-heading);">Product Description & Details</h2>
      
      <div class="product-card-padded" style="margin-bottom:2.5rem;">
        <div class="prose-mta">
          <?php 
            $rawDesc = !empty($product['description_html']) ? $product['description_html'] : (!empty($product['supplier_description_html']) ? $product['supplier_description_html'] : '');
            $cleanDesc = mta_clean_description_html($rawDesc, $title);
          ?>
          <?php if (!empty($cleanDesc)): ?>
            <?= $cleanDesc ?>
          <?php else: ?>
            <p>High-demand tech accessory engineered for reliable daily performance, maximum device compatibility, and long-lasting durability.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Key Specifications & Authenticity Table -->
      <h3 style="font-size:1.3rem; margin-bottom:1rem; font-family:var(--font-heading);">Key Specifications & Authenticity</h3>
      <div class="product-card-padded" style="margin-bottom:2.5rem; overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.9rem; text-align:left;">
          <tbody>
            <tr style="border-bottom:1px solid var(--border);">
              <th style="padding:0.75rem 1rem; width:35%; color:var(--text-muted); font-weight:600; background:var(--surface-2);">Product Condition</th>
              <td style="padding:0.75rem 1rem; font-weight:700; color:var(--accent-strong);">100% Brand New, Sealed Box</td>
            </tr>
            <tr style="border-bottom:1px solid var(--border);">
              <th style="padding:0.75rem 1rem; color:var(--text-muted); font-weight:600; background:var(--surface-2);">Brand / Manufacturer</th>
              <td style="padding:0.75rem 1rem; font-weight:600;"><?= e($product['brand_name'] ?? 'Multan Tech Accessories') ?></td>
            </tr>
            <tr style="border-bottom:1px solid var(--border);">
              <th style="padding:0.75rem 1rem; color:var(--text-muted); font-weight:600; background:var(--surface-2);">Category</th>
              <td style="padding:0.75rem 1rem;"><?= e($product['category_name'] ?? 'Computer & Mobile Accessories') ?></td>
            </tr>
            <tr style="border-bottom:1px solid var(--border);">
              <th style="padding:0.75rem 1rem; color:var(--text-muted); font-weight:600; background:var(--surface-2);">SKU / Item Code</th>
              <td style="padding:0.75rem 1rem; font-family:monospace;"><?= e($product['sku'] ?? ('MTA-' . $product['id'])) ?></td>
            </tr>
            <tr style="border-bottom:1px solid var(--border);">
              <th style="padding:0.75rem 1rem; color:var(--text-muted); font-weight:600; background:var(--surface-2);">Store Warranty</th>
              <td style="padding:0.75rem 1rem; font-weight:700;">3 Days Check Warranty (Strict 72-Hour Inspection)</td>
            </tr>
            <tr style="border-bottom:1px solid var(--border);">
              <th style="padding:0.75rem 1rem; color:var(--text-muted); font-weight:600; background:var(--surface-2);">Package Contents</th>
              <td style="padding:0.75rem 1rem;">Original Factory Retail Packaging &bull; <?= e($title) ?> &bull; Standard Accessories / Documentation</td>
            </tr>
            <tr>
              <th style="padding:0.75rem 1rem; color:var(--text-muted); font-weight:600; background:var(--surface-2);">Delivery & Payment</th>
              <td style="padding:0.75rem 1rem;">Cash on Delivery (COD) Nationwide &bull; Rs. 100 Advance Booking Fee &bull; 24-48h Multan, 2-4 Days Nationwide</td>
            </tr>
          </tbody>
        </table>
      </div>

      <?php
        $faqsData = $faqs ?? \App\Services\SeoService::productFaqs($product);
        $prodFaqs = $faqsData['product_faqs'] ?? [];
        $storeFaqs = $faqsData['store_faqs'] ?? [];
      ?>

      <!-- Dynamic Product-Specific Technical & Compatibility FAQs -->
      <?php if (!empty($prodFaqs)): ?>
        <h3 style="font-size:1.3rem; margin-bottom:1rem; font-family:var(--font-heading);">Product Specifications & Compatibility FAQs</h3>
        <div style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom:2.5rem;">
          <?php foreach ($prodFaqs as $idx => $f): ?>
            <details class="product-card-padded" style="padding:1rem 1.25rem;" <?= $idx === 0 ? 'open' : '' ?>>
              <summary style="font-weight:700; cursor:pointer; font-size:0.95rem; color:var(--text);"><?= e($f['q']) ?></summary>
              <p style="margin-top:0.6rem; font-size:0.875rem; color:var(--text-muted); line-height:1.6;">
                <?= e($f['a']) ?>
              </p>
            </details>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Store-Level Ordering, Warranty & Delivery FAQs (Clearly Separated) -->
      <?php if (!empty($storeFaqs)): ?>
        <h3 style="font-size:1.3rem; margin-bottom:1rem; font-family:var(--font-heading);">MultanTech Ordering, Warranty & Delivery FAQs</h3>
        <div style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom:3rem;">
          <?php foreach ($storeFaqs as $idx => $f): ?>
            <details class="product-card-padded" style="padding:1rem 1.25rem;">
              <summary style="font-weight:700; cursor:pointer; font-size:0.95rem; color:var(--text);"><?= e($f['q']) ?></summary>
              <p style="margin-top:0.6rem; font-size:0.875rem; color:var(--text-muted); line-height:1.6;">
                <?= e($f['a']) ?>
              </p>
            </details>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </section>

  <!-- Related Products (Same Category) -->
  <?php if (!empty($relatedProducts)): ?>
    <?php $mainProduct = $product; ?>
    <section class="section product-page-section" style="border-top:1px solid var(--border);">
      <h2 style="font-size:1.5rem; margin-bottom:1.5rem; font-family:var(--font-heading);">Related Accessories</h2>
      <div class="product-grid">
        <?php foreach ($relatedProducts as $rel): ?>
          <?php 
            try {
              $product = $rel;
              require MTA_APP . '/Views/partials/product-card.php'; 
            } catch (\Throwable $e) {
              // Silently ignore corrupt single item to prevent 500 error display
            }
          ?>
        <?php endforeach; ?>
      </div>
    </section>
    <?php $product = $mainProduct; ?>
  <?php endif; ?>

  <!-- Mobile Sticky Bottom Bar (Thumb-Zone, Task 4) -->
  <div class="product-mobile-sticky-bar" id="product-mobile-sticky-bar" aria-label="Quick Purchase Bar">
    <div style="display:flex; flex-direction:column; line-height:1.2;">
      <span style="font-size:0.75rem; color:var(--text-muted); font-weight:600;">Total Price:</span>
      <span style="font-size:1.25rem; font-weight:800; color:var(--accent-strong); font-family:var(--font-heading); white-space:nowrap;">
        <?= $sellPrice > 0 ? mta_money($sellPrice) : 'Ask Price' ?>
      </span>
    </div>
    <?php if ($status === 1 && $sellPrice > 0): ?>
      <form action="/cart/add" method="POST" style="margin:0;">
        <?= mta_csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
        <input type="hidden" name="quantity" value="1">
        <button type="submit" class="btn btn-primary" style="min-height:44px; padding:0 1.25rem; font-size:0.95rem; font-weight:700;">
          Add to Cart
        </button>
      </form>
    <?php elseif ($status === 3 || $sellPrice <= 0): ?>
      <a href="<?= e(mta_whatsapp_url('923070655275', 'Hi Multan Tech Accessories! Please tell me price / availability for: ' . $title)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="min-height:44px; padding:0 1rem; font-size:0.9rem; font-weight:700;">
        Ask Price
      </a>
    <?php else: ?>
      <a href="<?= e(mta_whatsapp_url('923070655275', 'Hi Multan Tech Accessories! Is this item returning in stock soon: ' . $title)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="min-height:44px; padding:0 1rem; font-size:0.9rem; font-weight:700;">
        Ask Availability
      </a>
    <?php endif; ?>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  if (window.innerWidth <= 768) {
    document.body.classList.add('has-product-sticky-bar');
  }
  window.addEventListener('resize', function() {
    if (window.innerWidth <= 768) {
      document.body.classList.add('has-product-sticky-bar');
    } else {
      document.body.classList.remove('has-product-sticky-bar');
    }
  }, { passive: true });
});
function updateDeliveryEstimate(city) {
  const resultDiv = document.getElementById('delivery-calc-result');
  const price = <?= (float)$sellPrice ?>;
  
  if (price >= 5000) {
    resultDiv.innerHTML = '&#10003; <strong>FREE DELIVERY</strong> (Cart value is Rs. 5,000 or above!)';
    return;
  }
  
  if (city === 'multan') {
    resultDiv.innerHTML = 'Multan Delivery: <strong>Rs. 100</strong> (Cash on Delivery). Free on 2nd order!';
  } else {
    resultDiv.innerHTML = 'Nationwide Courier Delivery: <strong>Rs. 250</strong> (Cash on Delivery). Free over Rs. 5,000.';
  }
}
</script>
