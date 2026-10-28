<?php
/**
 * Multan Tech Accessories — About Us View
 */
?>
<div class="container" style="padding: 2rem 1.25rem; max-width: 850px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <h1 style="font-size: 2.25rem; font-weight: 800; margin: 1rem 0 1.5rem;">About Multan Tech Accessories</h1>

  <div style="font-size: 1rem; line-height: 1.8; color: var(--text);">
    <p style="margin-bottom: 1.25rem;">
      Welcome to <strong>Multan Tech Accessories</strong> (operating at <code>https://multantech.store</code>). Founded and developed by <strong>Muneeb Ahmad</strong>, our e-commerce platform provides authentic computer hardware, fast charging devices, durable cables, sound systems, and gaming accessories directly to Pakistani consumers.
    </p>

    <h2 style="font-size: 1.5rem; margin: 2rem 0 1rem;">Our Origin & Mission</h2>
    <p style="margin-bottom: 1.25rem;">
      Pakistani consumers often face counterfeit accessories, exaggerated wattage ratings on chargers, and unverified specifications. Multan Tech Accessories was created to solve this trust deficit. Every accessory listed in our catalog is sourced from authorized distribution feeds, thoroughly inspected for build quality, and verified for real-world compatibility.
    </p>

    <h2 style="font-size: 1.5rem; margin: 2rem 0 1rem;">Physical Store Location</h2>
    <p style="margin-bottom: 1.25rem;">
      We are proud to serve our local community and all Pakistani tech enthusiasts from our physical headquarters:
    </p>
    <div style="background: var(--surface); border: 1px solid var(--border); border-left: 4px solid var(--accent-strong); border-radius: var(--radius-sm); padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
      <strong>Physical Address:</strong><br>
      <?= e(mta_setting('store_address', 'Al-Quresh Phase 3, Sher Shah Road, Multan, Punjab, Pakistan')) ?><br><br>
      <strong>Direct Phone / WhatsApp:</strong> 0307-0655275 (Intl: +923070655275)<br>
      <strong>Founder & Developer:</strong> Muneeb Ahmad
    </div>

    <h2 style="font-size: 1.5rem; margin: 2rem 0 1rem;">Customer First Guarantees</h2>
    <ul style="margin-left: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
      <li><strong>Cash on Delivery:</strong> No advance payments required for standard orders across Pakistan. Pay only when your package arrives.</li>
      <li><strong>Delivery Benefits:</strong> Free delivery on all orders above Rs. 5,000, and 100% free delivery on your second order.</li>
      <li><strong>Multan Local Rates:</strong> Customers in Multan receive our lowest local delivery charge (Rs. 100) or can opt for direct store pickup.</li>
      <li><strong>7-Day Checking Warranty:</strong> Defective items are replaced promptly without hassle.</li>
    </ul>
  </div>
</div>
