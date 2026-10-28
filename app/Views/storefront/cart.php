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
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 2rem;">
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
                <form action="/cart/update" method="POST" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                  <?= mta_csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                  <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="10" style="width: 50px; padding: 0.3rem 0.4rem; border: 1px solid var(--border); border-radius: 4px; text-align: center;">
                  <button type="submit" class="btn btn-secondary btn-sm" title="Update Quantity">&#8635;</button>
                </form>
              </td>
              <td style="padding: 1.25rem; text-align: right; font-weight: 700; color: var(--accent-strong);">
                <?= mta_money($itemTotal) ?>
              </td>
              <td style="padding: 1.25rem; text-align: center;">
                <form action="/cart/remove" method="POST">
                  <?= mta_csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                  <button type="submit" style="color: var(--danger); font-size: 0.85rem; text-decoration: underline;" title="Remove Item">Remove</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Subtotal & Checkout Call to Action -->
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.75rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <div style="font-size: 0.9rem; color: var(--text-muted);">Cart Subtotal:</div>
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--accent-strong); font-family: var(--font-heading);">
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

      <div style="display: flex; gap: 1rem;">
        <a href="/" class="btn btn-secondary">Continue Shopping</a>
        <a href="/checkout" class="btn btn-primary btn-lg">Proceed to Checkout &rarr;</a>
      </div>
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
