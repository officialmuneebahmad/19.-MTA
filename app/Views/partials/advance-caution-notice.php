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
    <span><strong>Rs. 100/- in Advance for booking Order.</strong> (For Security Purposes Only)</span>
  </div>
<?php else: ?>
  <div class="advance-caution-box" role="alert">
    <div class="advance-caution-icon">&#9888;&#65039;</div>
    <div class="advance-caution-content">
      <div class="advance-caution-title">
        Rs. 100/- in Advance for booking Order. (For Security Purposes Only)
      </div>
      <div class="advance-caution-desc">
        To prevent fake bookings and confirm delivery dispatch, a security advance of <strong>Rs. 100/-</strong> is required via JazzCash, EasyPaisa, or Bank. The remaining balance will be collected as Cash on Delivery (COD) upon parcel arrival at your doorstep.
      </div>
    </div>
  </div>
<?php endif; ?>
