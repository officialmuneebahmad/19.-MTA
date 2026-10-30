<?php
/**
 * Multan Tech Accessories — Shopping Cart View
 */
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += ($item['sell_price'] * $item['quantity']);
}
?>
<div class="container" style="padding-top: 1.5rem; max-width: 900px;">
  
  <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 1.5rem;">Your Shopping Cart</h1>

  <?php if (!empty($cartItems)): ?>
    <!-- Desktop Table View -->
    <div class="cart-desktop-wrap" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 2rem;">
      <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
        <thead style="background: var(--surface-2); border-bottom: 1px solid var(--border);">
          <tr>
            <th style="padding: 1rem 1.25rem;">Product</th>
            <th style="padding: 1rem 1.25rem; text-align: center;">Price</th>
            <th style="padding: 1rem 1.25rem; text-align: center;">Qty</th>
            <th style="padding: 1rem 1.25rem; text-align: right;">Total</th>
            <th style="padding: 1rem 1.25rem; text-align: center;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cartItems as $item): ?>
            <?php $itemTotal = $item['sell_price'] * $item['quantity']; ?>
            <tr style="border-bottom: 1px solid var(--border);">
              <td style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
                <img src="<?= e($item['primary_image_url'] ?? '/assets/images/placeholder.webp') ?>" alt="<?= e($item['title']) ?>" width="60" height="60" style="object-fit: contain; border-radius: 6px; background: var(--surface-2);">
                <div>
                  <a href="/products/<?= e($item['slug']) ?>" style="font-weight: 600; color: var(--text); line-height: 1.4;">
                    <?= e($item['title']) ?>
                  </a>
                  <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">SKU: <?= e($item['sku']) ?></div>
                </div>
              </td>
              <td style="padding: 1.25rem; text-align: center; font-weight: 600;">
                <?= mta_money($item['sell_price']) ?>
              </td>
              <td style="padding: 1.25rem; text-align: center;">
                <form action="/cart/update" method="POST" style="display: inline-flex; align-items: center; background: var(--surface-2); border: 1.5px solid var(--border); border-radius: 12px; overflow: hidden; min-height: 44px;">
                  <?= mta_csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                  <button type="submit" name="quantity" value="<?= max(0, $item['quantity'] - 1) ?>" style="width: 38px; height: 44px; background: transparent; border: none; color: var(--text); font-weight: 700; font-size: 1.25rem; cursor: pointer; display: flex; align-items: center; justify-content: center;" aria-label="Decrease">&minus;</button>
                  <span style="width: 36px; text-align: center; font-weight: 700; font-size: 0.95rem; color: var(--text);"><?= (int)$item['quantity'] ?></span>
                  <button type="submit" name="quantity" value="<?= min(10, $item['quantity'] + 1) ?>" style="width: 38px; height: 44px; background: transparent; border: none; color: var(--text); font-weight: 700; font-size: 1.25rem; cursor: pointer; display: flex; align-items: center; justify-content: center;" aria-label="Increase">&plus;</button>
                </form>
              </td>
              <td style="padding: 1.25rem; text-align: right; font-weight: 700; color: var(--accent-strong); white-space: nowrap;">
                <?= mta_money($itemTotal) ?>
              </td>
              <td style="padding: 1.25rem; text-align: center;">
                <form action="/cart/remove" method="POST" style="margin: 0; display: inline-block;">
                  <?= mta_csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                  <button type="submit" class="btn-icon" style="width: 44px; height: 44px; border-radius: 12px; border: 1.5px solid var(--danger); background: var(--danger-bg); color: var(--danger); margin: 0 auto;" title="Remove Item" aria-label="Remove item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Mobile Card View (Shows on <768px screens) -->
    <div class="cart-mobile-list" style="display: none; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
      <?php foreach ($cartItems as $item): ?>
        <?php $itemTotal = $item['sell_price'] * $item['quantity']; ?>
        <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1rem; display: flex; flex-direction: column; gap: 0.85rem;">
          <div style="display: flex; gap: 0.85rem;">
            <img src="<?= e($item['primary_image_url'] ?? '/assets/images/placeholder.webp') ?>" alt="<?= e($item['title']) ?>" width="64" height="64" style="object-fit: contain; border-radius: 6px; background: var(--surface-2); flex-shrink: 0;">
            <div style="flex: 1; min-width: 0;">
              <a href="/products/<?= e($item['slug']) ?>" style="font-weight: 700; font-size: 0.95rem; color: var(--text); line-height: 1.35; display: block; margin-bottom: 0.25rem;">
                <?= e($item['title']) ?>
              </a>
              <div style="font-size: 0.8rem; color: var(--text-muted);">Unit Price: <?= mta_money($item['sell_price']) ?></div>
            </div>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 0.75rem; gap: 0.75rem;">
            <!-- Stepper [-] Qty [+] -->
            <form action="/cart/update" method="POST" style="display: inline-flex; align-items: center; background: var(--surface-2); border: 1.5px solid var(--border); border-radius: 12px; overflow: hidden; margin: 0; min-height: 44px;">
              <?= mta_csrf_field() ?>
              <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
              <button type="submit" name="quantity" value="<?= max(0, $item['quantity'] - 1) ?>" style="width: 44px; height: 44px; background: transparent; border: none; color: var(--text); font-weight: 700; font-size: 1.3rem; cursor: pointer; display: flex; align-items: center; justify-content: center;" aria-label="Decrease quantity">&minus;</button>
              <span style="width: 36px; text-align: center; font-weight: 800; font-size: 1rem; color: var(--text);"><?= (int)$item['quantity'] ?></span>
              <button type="submit" name="quantity" value="<?= min(10, $item['quantity'] + 1) ?>" style="width: 44px; height: 44px; background: transparent; border: none; color: var(--text); font-weight: 700; font-size: 1.3rem; cursor: pointer; display: flex; align-items: center; justify-content: center;" aria-label="Increase quantity">&plus;</button>
            </form>

            <div style="display: flex; align-items: center; gap: 0.85rem;">
              <div style="font-weight: 800; font-size: 1.1rem; color: var(--accent-strong); white-space: nowrap;">
                <?= mta_money($itemTotal) ?>
              </div>
              <!-- Dustbin Icon Button (44px touch area) -->
              <form action="/cart/remove" method="POST" style="margin: 0;">
                <?= mta_csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                <button type="submit" class="btn-icon" style="width: 44px; height: 44px; border-radius: 12px; border: 1.5px solid var(--danger); background: var(--danger-bg); color: var(--danger);" title="Remove Item" aria-label="Remove item">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Subtotal & Checkout Call to Action -->
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.75rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;" class="cart-summary-box">
      <div>
        <div style="font-size: 0.9rem; color: var(--text-muted);">Cart Subtotal:</div>
        <div style="font-size: 1.85rem; font-weight: 800; color: var(--accent-strong); font-family: var(--font-heading);">
          <?= mta_money($subtotal) ?>
        </div>
        <div style="font-size: 0.85rem; color: var(--success); font-weight: 600; margin-top: 0.35rem;">
          <?php if ($subtotal >= 5000): ?>
            &#10003; Qualified for <strong>FREE DELIVERY</strong> across Pakistan!
          <?php else: ?>
            Add <?= mta_money(5000 - $subtotal) ?> more for <strong>FREE DELIVERY</strong>!
          <?php endif; ?>
        </div>
      </div>

      <div class="cart-actions-group" style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="/" class="btn btn-secondary" style="flex: 1; text-align: center;">Continue Shopping</a>
        <a href="/checkout" class="btn btn-primary btn-lg" style="flex: 1.5; text-align: center;">Proceed to Checkout &rarr;</a>
      </div>
    </div>

    <!-- Policy Reminder -->
    <div style="margin-top: 1.5rem; background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1rem 1.25rem; font-size: 0.85rem; color: var(--text-muted); line-height: 1.6;">
      <div>&#10003; <strong>3 Days Check Warranty Only:</strong> Please test your items within 3 days of receiving.</div>
      <div>&#10003; <strong>Exchange Policy:</strong> No Refund, Exchange can be made only for certain technical defect circumstances.</div>
      <div>&#10003; <strong>Courier Fee:</strong> The Customer has to Return parcel with their own money & courier.</div>
    </div>

  <?php else: ?>
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 4rem 2rem; text-align: center;">
      <h2 style="font-size: 1.5rem; margin-bottom: 0.75rem;">Your cart is currently empty</h2>
      <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
        You have not added any tech accessories to your cart yet. Explore our high-demand chargers, cables, and audio equipment.
      </p>
      <a href="/categories" class="btn btn-primary btn-lg">Start Shopping &rarr;</a>
    </div>
  <?php endif; ?>

</div>
