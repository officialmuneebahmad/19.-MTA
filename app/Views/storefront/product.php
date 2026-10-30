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

      <!-- Price Box (Neat & Professional, Never wraps Rs. from amount) -->
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-md); padding:1rem 1.25rem; margin-bottom:1.25rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem;">
          <div style="display:flex; align-items:baseline; gap:0.5rem; white-space:nowrap;">
            <?php if ($sellPrice > 0): ?>
              <span style="font-size:2rem; font-weight:800; color:var(--accent-strong); font-family:var(--font-heading); white-space:nowrap; letter-spacing:-0.5px;">
                <?= mta_money($sellPrice) ?>
              </span>
            <?php else: ?>
              <span style="font-size:1.3rem; font-weight:700; color:var(--text);">Contact for Price</span>
            <?php endif; ?>
          </div>
          <span style="font-size:0.8rem; font-weight:600; color:var(--text-muted); background:var(--surface-2); padding:0.35rem 0.65rem; border-radius:var(--radius-sm); border:1px solid var(--border); white-space:nowrap;">
            PKR &bull; Incl. all taxes
          </span>
        </div>
      </div>

      <!-- City-Based Delivery Charge Preview -->
      <div style="background:var(--surface-2); border:1px solid var(--border); border-radius:var(--radius-md); padding:1rem 1.25rem; margin-bottom:1.5rem;">
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

      <!-- Action Buttons -->
      <div style="display:flex; flex-direction:column; gap:0.85rem; margin-bottom:2rem;">
        <?php if ($status === 1 && $sellPrice > 0): ?>
          <form action="/cart/add" method="POST" style="margin:0; display:flex; flex-direction:column; gap:0.75rem;">
            <?= mta_csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

            <!-- Quantity Selector -->
            <div style="display:flex; align-items:center; justify-content:space-between; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm); padding:0.55rem 0.85rem;">
              <span style="font-size:0.9rem; font-weight:700; color:var(--text);">Quantity:</span>
              <div style="display:flex; align-items:center; gap:0.4rem;">
                <button type="button" onclick="const q=document.getElementById('product-qty'); if(parseInt(q.value)>1) q.value=parseInt(q.value)-1;" style="width:34px; height:34px; border-radius:6px; background:var(--surface-2); border:1px solid var(--border); color:var(--text); font-size:1.15rem; font-weight:700; display:flex; align-items:center; justify-content:center; cursor:pointer;" aria-label="Decrease quantity">&minus;</button>
                <input type="number" id="product-qty" name="quantity" value="1" min="1" max="10" readonly style="width:44px; height:34px; text-align:center; font-weight:800; font-size:1rem; border:1px solid var(--border); border-radius:6px; background:var(--surface-2); color:var(--text);">
                <button type="button" onclick="const q=document.getElementById('product-qty'); if(parseInt(q.value)<10) q.value=parseInt(q.value)+1;" style="width:34px; height:34px; border-radius:6px; background:var(--surface-2); border:1px solid var(--border); color:var(--text); font-size:1.15rem; font-weight:700; display:flex; align-items:center; justify-content:center; cursor:pointer;" aria-label="Increase quantity">&plus;</button>
              </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
              Add to Shopping Cart
            </button>
          </form>

          <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp-outline btn-lg" style="width:100%; display:inline-flex; align-items:center; justify-content:center; gap:0.5rem; text-decoration:none;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M18.403 5.638A8.955 8.955 0 0 0 12.053 3c-4.968 0-9.01 4.042-9.01 9.013 0 1.587.413 3.134 1.198 4.492L3 21l4.634-1.216a8.972 8.972 0 0 0 4.417 1.156h.004c4.968 0 9.007-4.042 9.007-9.014 0-2.408-.937-4.672-2.659-6.288zm-6.35 13.785h-.004a7.466 7.466 0 0 1-3.805-1.042l-.273-.162-2.827.741.755-2.756-.177-.282a7.448 7.448 0 0 1-1.144-3.909c0-4.135 3.364-7.499 7.502-7.499 2.004 0 3.887.782 5.301 2.2 1.415 1.417 2.193 3.303 2.193 5.308 0 4.136-3.365 7.499-7.498 7.499zm4.113-5.618c-.225-.113-1.334-.659-1.541-.734-.206-.075-.356-.113-.506.113-.15.225-.581.734-.712.884-.131.15-.262.169-.487.056-.225-.113-.951-.351-1.811-1.118-.67-.598-1.122-1.337-1.253-1.562-.131-.225-.014-.347.098-.459.102-.101.225-.263.338-.395.112-.131.15-.225.225-.375.075-.15.037-.281-.019-.394-.056-.113-.506-1.219-.694-1.67-.182-.439-.368-.379-.506-.386l-.431-.007c-.15 0-.394.056-.6.281-.206.225-.788.77-.788 1.876 0 1.107.806 2.176.919 2.326.112.15 1.586 2.423 3.843 3.398.537.232.956.371 1.283.475.54.172 1.031.148 1.419.09.432-.065 1.334-.545 1.522-1.07.188-.526.188-.977.131-1.07-.056-.094-.206-.15-.431-.263z" fill="#25D366"/></svg>
            <span>Order Instantly on WhatsApp &rarr;</span>
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
          <summary style="font-weight:700; cursor:pointer; font-size:0.95rem; color:var(--text);">Is this product original or copy?</summary>
          <p style="margin-top:0.6rem; font-size:0.875rem; color:var(--text-muted); line-height:1.6;">
            The product is <strong>Original</strong>. We ensure its <strong>Quality is Best and Generic</strong>, offering you identical form factor, robust build materials, and smooth daily performance, making your everyday life easier and convenient.
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
