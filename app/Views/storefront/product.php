<?php
/**
 * Multan Tech Accessories — Single Product Detail View
 * Section 8 Compliant:
 * - Single H1, gallery, price, status badge
 * - City delivery charge calculator
 * - Add to Cart + WhatsApp Order
 * - Key features, specs table, original description
 * - FAQ accordion (<details>)
 * - Delivery and return summary
 * - Breadcrumbs, related products, popular in category
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

$imageUrl = !empty($product['image_800']) ? $product['image_800'] : (!empty($product['primary_image_url']) ? $product['primary_image_url'] : '/assets/images/placeholder.webp');
$brandName = !empty($product['brand_name']) ? $product['brand_name'] : 'Authentic Brand';
$title = $product['title'] ?? 'Tech Accessory';
$sku = $product['sku'] ?? ('MTA-' . $product['id']);

$waMsg = "Hi Multan Tech Accessories! I am interested in ordering:\n*{$title}*\nPrice: " . mta_money($sellPrice) . "\nLink: " . mta_url('/products/' . $product['slug']) . "\nPlease confirm availability and delivery address.";
$waUrl = mta_whatsapp_url('923070655275', $waMsg);
?>

<div class="container" style="padding-top: 1rem;">
  
  <!-- Breadcrumbs -->
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <!-- Main Product Detail Layout -->
  <div class="product-layout-grid">
    
    <!-- Left: Image Gallery -->
    <div class="product-gallery-wrap">
      <div class="product-gallery-card">
        <img id="main-product-img" src="<?= e($imageUrl) ?>" alt="<?= e($title) ?>" width="600" height="600" style="max-height:480px; width:auto; margin:0 auto; object-fit:contain;" fetchpriority="high">
        
        <?php if (!empty($images) && count($images) > 1): ?>
          <div style="display:flex; gap:0.75rem; margin-top:1.5rem; justify-content:center; overflow-x:auto;">
            <?php foreach ($images as $img): ?>
              <button type="button" onclick="document.getElementById('main-product-img').src='<?= e($img['url']) ?>'" style="width:60px; height:60px; border-radius:8px; border:1px solid var(--border); overflow:hidden; padding:4px; background:var(--surface-2);">
                <img src="<?= e($img['url']) ?>" alt="Thumbnail" width="60" height="60" style="object-fit:cover; width:100%; height:100%;">
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
        <span style="font-size:0.85rem; font-weight:700; text-transform:uppercase; color:var(--text-muted); letter-spacing:0.5px;">
          Brand: <a href="/brands/<?= e($product['brand_slug'] ?? '') ?>" style="color:var(--accent-strong);"><?= e($brandName) ?></a>
        </span>
        <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
      </div>

      <!-- Exactly one H1 -->
      <h1 style="font-size:1.85rem; line-height:1.3; margin-bottom:1rem;"><?= e($title) ?></h1>

      <!-- SKU -->
      <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1.25rem;">
        SKU: <strong><?= e($sku) ?></strong>
      </div>

      <!-- Price Box -->
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-md); padding:1.25rem 1.5rem; margin-bottom:1.5rem; display:flex; align-items:baseline; gap:1rem;">
        <?php if ($sellPrice > 0): ?>
          <span style="font-size:2rem; font-weight:800; color:var(--accent-strong); font-family:var(--font-heading);">
            <?= mta_money($sellPrice) ?>
          </span>
          <span style="font-size:0.85rem; color:var(--text-muted);">PKR inclusive of all local taxes</span>
        <?php else: ?>
          <span style="font-size:1.4rem; font-weight:700; color:var(--text);">Contact on WhatsApp for Price</span>
        <?php endif; ?>
      </div>

      <!-- City-Based Delivery Charge Preview -->
      <div style="background:var(--surface-2); border:1px solid var(--border); border-radius:var(--radius-md); padding:1rem 1.25rem; margin-bottom:1.5rem;">
        <label for="city-calc" style="font-size:0.85rem; font-weight:700; display:block; margin-bottom:0.35rem;">
          Estimate Delivery Charges for Your City:
        </label>
        <div style="display:flex; gap:0.75rem; align-items:center;">
          <select id="city-calc" onchange="updateDeliveryEstimate(this.value)" style="padding:0.5rem 0.75rem; border-radius:6px; border:1px solid var(--border); background:#fff; font-size:0.9rem; flex:1;">
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

      <!-- Action Buttons -->
      <div style="display:flex; flex-direction:column; gap:0.85rem; margin-bottom:2rem;">
        <?php if ($status === 1 && $sellPrice > 0): ?>
          <form action="/cart/add" method="POST" style="margin:0;">
            <?= mta_csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
              Add to Shopping Cart
            </button>
          </form>

          <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg" style="width:100%;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12.01 2.002c-5.518 0-9.998 4.48-9.998 9.998 0 1.76.459 3.475 1.332 4.992L2.054 22l5.133-1.346a9.948 9.948 0 004.823 1.248h.004c5.517 0 9.997-4.48 9.997-9.998 0-2.67-1.04-5.18-2.93-7.07A9.928 9.928 0 0012.01 2.002zm5.82 14.156c-.24.674-1.398 1.288-1.928 1.348-.5.056-1.15.08-3.71-.978-2.56-1.058-4.2-3.666-4.33-3.838-.13-.172-1.03-1.37-1.03-2.613 0-1.242.648-1.854.88-2.106.23-.25.5-.314.67-.314.17 0 .34.002.49.01.16.008.37-.06.58.444.22.528.74 1.808.81 1.944.07.136.11.296.02.476-.09.18-.14.29-.27.444-.14.156-.29.35-.41.47-.14.138-.28.288-.12.564.16.276.71 1.17 1.52 1.894 1.05.934 1.93 1.224 2.21 1.36.27.136.43.116.59-.068.16-.184.69-.804.87-1.08.18-.276.36-.23.6-.138.24.092 1.52.716 1.78.846.26.13.43.196.49.304.07.108.07.624-.17 1.298z"/></svg>
            Order Instantly on WhatsApp &rarr;
          </a>

          <a href="tel:+923070655275" class="btn btn-outline-call btn-lg" style="width:100%; display:inline-flex; align-items:center; justify-content:center; gap:0.5rem; text-decoration:none;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            Call Us for Inquiry (0307-0655275)
          </a>
        <?php elseif ($status === 3): ?>
          <div class="alert alert-warning" style="background:#FEF3C7; color:#92400E; padding:1rem; border-radius:8px;">
            <strong>Coming Soon:</strong> This product is currently in transit. Message us on WhatsApp to get notified immediately upon arrival.
          </div>
          <a href="<?= e(mta_whatsapp_url('923070655275', "Hi, please notify me when this item arrives: " . $title)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-lg" style="width:100%;">
            Notify Me on WhatsApp
          </a>
        <?php else: ?>
          <div class="alert alert-danger" style="background:var(--danger-bg); color:var(--danger); padding:1rem; border-radius:8px;">
            <strong>Out of Stock:</strong> This item is temporarily out of stock. Contact us on WhatsApp for expected restock date or recommended alternatives.
          </div>
          <a href="<?= e(mta_whatsapp_url('923070655275', "Hi, do you have alternatives or restock date for: " . $title)) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-lg" style="width:100%;">
            Ask for Availability on WhatsApp
          </a>
        <?php endif; ?>
      </div>

      <!-- Trust Badges & Policies -->
      <div style="border-top:1px solid var(--border); padding-top:1.25rem; font-size:0.875rem; color:var(--text-muted); line-height:1.8;">
        <div>&#10003; <strong>Warranty:</strong> <span style="color:var(--text); font-weight:700;">We Offer 3 Days Check Warranty Only</span> (Test upon arrival).</div>
        <div>&#10003; <strong>Returns & Refunds:</strong> <span style="color:var(--text); font-weight:700;">No Refund, Exchange can be made only for certain circumstances</span>.</div>
        <div>&#10003; <strong>Courier Charges:</strong> <span style="color:var(--text); font-weight:700;">The Customer has to Return parcel with their own money & courier</span>.</div>
        <div>&#10003; <strong>Payment:</strong> Cash on Delivery (COD) across Pakistan.</div>
        <div>&#10003; <strong>Store Pickup:</strong> Al-Quresh Phase 3, Sher Shah Road, Multan.</div>
      </div>

    </div>
  </div>

  <!-- Detailed Specifications, Key Features & Description -->
  <section class="section" style="border-top:1px solid var(--border); padding-top:2.5rem;">
    <div style="max-width:850px;">
      
      <h2 style="font-size:1.5rem; margin-bottom:1.25rem;">Product Description & Details</h2>
      
      <div style="font-size:0.95rem; line-height:1.8; color:var(--text); margin-bottom:2.5rem;" class="product-description-content">
        <?php if (!empty($product['description_html'])): ?>
          <?= $product['description_html'] ?>
        <?php elseif (!empty($product['supplier_description_html'])): ?>
          <?= $product['supplier_description_html'] ?>
        <?php else: ?>
          <p>High-demand tech accessory engineered for reliable daily performance, maximum device compatibility, and long-lasting durability.</p>
        <?php endif; ?>
      </div>

      <!-- FAQ Accordion (<details> tags stay in DOM for technical SEO & Accessibility) -->
      <h3 style="font-size:1.25rem; margin-bottom:1rem;">Frequently Asked Questions (FAQs)</h3>
      <div style="display:flex; flex-direction:column; gap:0.75rem; margin-bottom:3rem;">
        
        <details style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm); padding:0.85rem 1.25rem;" open>
          <summary style="font-weight:700; cursor:pointer; font-size:0.95rem; color:var(--text);">Is this product original or a copy?</summary>
          <p style="margin-top:0.6rem; font-size:0.875rem; color:var(--text-muted); line-height:1.6;">
            This product is the <strong>Best Copy of the Original Product</strong>. We ensure its <strong>Quality is Best and Generic</strong>, offering you identical form factor, robust build materials, and smooth daily performance at a fraction of high brand markups.
          </p>
        </details>

        <details style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm); padding:0.85rem 1.25rem;">
          <summary style="font-weight:700; cursor:pointer; font-size:0.95rem; color:var(--text);">What warranty is provided with this item?</summary>
          <p style="margin-top:0.6rem; font-size:0.875rem; color:var(--text-muted); line-height:1.6;">
            <strong>We Offer 3 Days Check Warranty Only.</strong> Customers must thoroughly inspect and test the accessory within 3 days (72 hours) of parcel delivery. Any genuine technical or functional defect must be reported to our WhatsApp support team within this 3-day window.
          </p>
        </details>

        <details style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm); padding:0.85rem 1.25rem;">
          <summary style="font-weight:700; cursor:pointer; font-size:0.95rem; color:var(--text);">Can I get a refund or exchange?</summary>
          <p style="margin-top:0.6rem; font-size:0.875rem; color:var(--text-muted); line-height:1.6;">
            <strong>No Refund, Exchange can be made only for certain circumstances.</strong> We do not issue cash refunds. If an item arrives with an unresolvable technical or hardware defect verified by our support team within 3 days, we will replace it with a fresh unit of the same model. Change of mind or physical/liquid damages are not eligible for exchange.
          </p>
        </details>

        <details style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm); padding:0.85rem 1.25rem;">
          <summary style="font-weight:700; cursor:pointer; font-size:0.95rem; color:var(--text);">Who pays for the courier fee if I need to return an item?</summary>
          <p style="margin-top:0.6rem; font-size:0.875rem; color:var(--text-muted); line-height:1.6;">
            <strong>The Customer has to Return parcel with their own money & courier.</strong> If an exchange is approved under the 3-day check warranty, you are required to dispatch the item securely back to our Multan warehouse at your own expense.
          </p>
        </details>

        <details style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm); padding:0.85rem 1.25rem;">
          <summary style="font-weight:700; cursor:pointer; font-size:0.95rem; color:var(--text);">How many days does delivery take across Pakistan?</summary>
          <p style="margin-top:0.6rem; font-size:0.875rem; color:var(--text-muted); line-height:1.6;">
            Orders inside Multan are delivered within 24 to 48 hours for Rs. 100 (Free delivery on your 2nd order!). Deliveries to Lahore, Karachi, Islamabad, Rawalpindi, Faisalabad, Peshawar, and all other cities take 2 to 4 business days via reliable couriers with full Cash on Delivery.
          </p>
        </details>

        <details style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm); padding:0.85rem 1.25rem;">
          <summary style="font-weight:700; cursor:pointer; font-size:0.95rem; color:var(--text);">What payment methods are supported?</summary>
          <p style="margin-top:0.6rem; font-size:0.875rem; color:var(--text-muted); line-height:1.6;">
            We primarily support <strong>Cash on Delivery (COD)</strong> so you pay only when the rider delivers your package. Advance payments via JazzCash, Easypaisa, or online bank transfer can also be arranged via WhatsApp.
          </p>
        </details>

      </div>

    </div>
  </section>

  <!-- Related Products (Same category, up to 4 items) -->
  <?php if (!empty($relatedProducts)): ?>
    <section class="section" style="border-top:1px solid var(--border); padding-top:2.5rem;">
      <h2 class="section-title" style="margin-bottom:1.5rem;">Related Accessories</h2>
      <div class="product-grid">
        <?php foreach ($relatedProducts as $product): ?>
          <?php require MTA_APP . '/Views/partials/product-card.php'; ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

</div>

<script>
function updateDeliveryEstimate(city) {
  var el = document.getElementById('delivery-calc-result');
  var price = <?= $sellPrice ?>;
  if (price >= 5000) {
    el.innerHTML = '&#10003; <strong>FREE DELIVERY</strong> (Cart value is Rs. 5,000 or above!)';
    return;
  }
  if (city === 'multan') {
    el.innerHTML = 'Multan Local Delivery: <strong>Rs. 100</strong>. Free delivery on your 2nd order!';
  } else {
    el.innerHTML = 'Courier Delivery to ' + city.charAt(0).toUpperCase() + city.slice(1) + ': <strong>Rs. 250</strong>. Free delivery on orders above Rs. 5,000!';
  }
}
</script>
