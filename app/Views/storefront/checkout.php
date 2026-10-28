<?php
/**
 * Multan Tech Accessories — Checkout View
 */
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['sell_price'] * $item['quantity']);
}
?>
<div class="container" style="padding-top: 1.5rem; max-width: 980px;">
  
  <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;">Fast Checkout</h1>
  <p style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 2rem;">No registration or password needed. Provide delivery details to confirm your order.</p>

  <form action="/checkout/place-order" method="POST" id="checkout-form">
    <?= mta_csrf_field() ?>
    
    <!-- Anti-bot honeypot field (hidden from humans, bots fill it) -->
    <div style="display:none;" aria-hidden="true">
      <input type="text" name="_website_url_hp" tabindex="-1" autocomplete="off">
    </div>

    <div style="display: grid; grid-template-columns: 1.25fr 1fr; gap: 2.5rem;" class="checkout-grid">
      
      <!-- Left: Customer Details -->
      <div>
        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 1.5rem;">
          <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem;">1. Delivery Address</h2>
          
          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="customer_name" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">Full Name *</label>
            <input type="text" id="customer_name" name="customer_name" required placeholder="e.g. Muhammad Ali" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem;">
          </div>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="customer_phone" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">Mobile Number (Calling & WhatsApp) *</label>
            <input type="tel" id="customer_phone" name="customer_phone" required placeholder="03001234567" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem;">
            <span style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem; display:block;">Our team confirms all orders via phone call or WhatsApp message before dispatch.</span>
          </div>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="city" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">City *</label>
            <select id="city" name="city" required onchange="calculateDelivery(this.value)" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem; background:#fff;">
              <option value="Multan" selected>Multan (Minimum Local Delivery: Rs. 100)</option>
              <option value="Lahore">Lahore</option>
              <option value="Karachi">Karachi</option>
              <option value="Islamabad">Islamabad</option>
              <option value="Rawalpindi">Rawalpindi</option>
              <option value="Faisalabad">Faisalabad</option>
              <option value="Gujranwala">Gujranwala</option>
              <option value="Peshawar">Peshawar</option>
              <option value="Quetta">Quetta</option>
              <option value="Sialkot">Sialkot</option>
              <option value="Bahawalpur">Bahawalpur</option>
              <option value="Other">Other Pakistani City / Town</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="shipping_address" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">Complete Shipping Address (House/Shop #, Street, Area) *</label>
            <textarea id="shipping_address" name="shipping_address" rows="3" required placeholder="House #, Street #, Sector / Area, Landmark" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem;"></textarea>
          </div>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="customer_email" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">Email Address (Optional)</label>
            <input type="email" id="customer_email" name="customer_email" placeholder="ali@example.com" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem;">
          </div>

          <div class="form-group">
            <label for="customer_notes" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">Order Notes (Optional)</label>
            <input type="text" id="customer_notes" name="customer_notes" placeholder="Special delivery instructions or preferred calling time" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem;">
          </div>
        </div>

        <!-- Payment Method -->
        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.75rem 2rem;">
          <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">2. Payment Method</h2>
          <div style="border: 2px solid var(--accent-strong); border-radius: 8px; padding: 1rem 1.25rem; background: var(--surface-2); display: flex; align-items: center; justify-content: space-between;">
            <div>
              <div style="font-weight: 700; font-size: 1rem;">&#128666; Cash on Delivery (COD)</div>
              <p style="font-size: 0.825rem; color: var(--text-muted); margin-top: 0.25rem;">
                Pay with cash to courier rider upon receiving your package at your doorstep.
              </p>
            </div>
            <input type="radio" name="payment_method" value="cod" checked style="accent-color: var(--accent-strong); transform: scale(1.3);">
          </div>
          <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.75rem;">
            * Advance payment via JazzCash, Easypaisa, or online bank transfer can also be arranged on WhatsApp upon request after placing this order.
          </p>
        </div>
      </div>

      <!-- Right: Order Summary -->
      <div>
        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; position: sticky; top: 90px;">
          <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem;">Order Summary</h2>

          <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; max-height: 280px; overflow-y: auto;">
            <?php foreach ($cartItems as $item): ?>
              <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; border-bottom: 1px solid var(--border); padding-bottom: 0.75rem;">
                <div>
                  <div style="font-weight: 600; line-height: 1.3;"><?= e($item['title']) ?></div>
                  <div style="font-size: 0.8rem; color: var(--text-muted);">Qty: <?= (int)$item['quantity'] ?> &times; <?= mta_money($item['sell_price']) ?></div>
                </div>
                <div style="font-weight: 700;">
                  <?= mta_money($item['sell_price'] * $item['quantity']) ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div style="border-top: 1px solid var(--border); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.925rem;">
            <div style="display: flex; justify-content: space-between;">
              <span style="color: var(--text-muted);">Subtotal:</span>
              <span style="font-weight: 600;"><?= mta_money($subtotal) ?></span>
            </div>

            <div style="display: flex; justify-content: space-between;">
              <span style="color: var(--text-muted);">Delivery Charge:</span>
              <span id="summary-delivery-charge" style="font-weight: 600; color: var(--accent-strong);">
                <?= $subtotal >= 5000 ? 'FREE' : 'Rs. 100' ?>
              </span>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; border-top: 2px solid var(--border); padding-top: 0.75rem; margin-top: 0.5rem;">
              <span>Total Payable:</span>
              <span id="summary-total-payable" style="color: var(--accent-strong); font-family: var(--font-heading);">
                <?= mta_money($subtotal + ($subtotal >= 5000 ? 0 : 100)) ?>
              </span>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1.75rem;">
            Confirm Order &rarr;
          </button>

          <p style="font-size: 0.75rem; text-align: center; color: var(--text-muted); margin-top: 0.75rem;">
            By placing your order, you agree to our <a href="/terms-and-conditions" style="text-decoration: underline;">Terms</a> and <a href="/returns-and-refunds" style="text-decoration: underline;">Return Policy</a>.
          </p>
        </div>
      </div>

    </div>
  </form>

</div>

<script>
function calculateDelivery(city) {
  var subtotal = <?= $subtotal ?>;
  var charge = 0;
  if (subtotal >= 5000) {
    charge = 0;
    document.getElementById('summary-delivery-charge').innerText = 'FREE';
  } else if (city.toLowerCase() === 'multan') {
    charge = 100;
    document.getElementById('summary-delivery-charge').innerText = 'Rs. 100';
  } else {
    charge = 250;
    document.getElementById('summary-delivery-charge').innerText = 'Rs. 250';
  }
  var total = subtotal + charge;
  document.getElementById('summary-total-payable').innerText = 'Rs. ' + total.toLocaleString();
}
</script>
