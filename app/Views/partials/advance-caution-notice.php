<?php
/**
 * Multan Tech Accessories — Advance Booking Security Notice
 * Red box with caution sign
 */
$isCompact = !empty($isCompact);
?>
<?php if ($isCompact): ?>
  <div class="advance-caution-inline" role="alert">
    <span class="advance-caution-icon" style="font-size: 1.15rem;">&#9888;&#65039;</span>
    <span><strong>Rs. 100/- Advance Security Payment required to confirm and reserve your order.</strong></span>
  </div>
<?php else: ?>
  <div class="advance-caution-box" role="alert">
    <div class="advance-caution-icon">&#9888;&#65039;</div>
    <div class="advance-caution-content">
      <div class="advance-caution-title">
        Rs. 100/- Advance Security Payment Required for Order Confirmation
      </div>
      <div class="advance-caution-desc">
        To confirm and reserve your order and prevent fake bookings, an <strong>Rs. 100/- Advance Security Payment</strong> is required via JazzCash, EasyPaisa, or Bank Transfer. The remaining amount is payable per your selected delivery arrangement (Cash on Delivery upon parcel arrival).
      </div>
    </div>
  </div>
<?php endif; ?>

