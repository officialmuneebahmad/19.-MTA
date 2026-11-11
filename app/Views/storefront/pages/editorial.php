<?php
/**
 * Multan Tech Accessories — Editorial and Pricing Policy View
 */
?>
<div class="container" style="padding: 2rem 1.25rem; max-width: 850px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <h1 style="font-size: 2.25rem; font-weight: 800; margin: 1rem 0 1.5rem;">Editorial & Pricing Policy</h1>

  <div style="font-size: 1rem; line-height: 1.8; color: var(--text);">
    
    <p style="margin-bottom: 1.25rem;">
      Transparency is fundamental to our business philosophy. This document explains how product information, technical specifications, and retail pricing are updated on <strong>MultanTech.pk</strong>.
    </p>

    <h2 style="font-size: 1.4rem; margin: 2rem 0 0.75rem;">1. Catalog &amp; Stock Synchronization</h2>
    <p style="margin-bottom: 1.25rem;">
      Multan Tech Accessories operates an automated synchronization pipeline connected to wholesale distributor feeds. Product stock statuses (In Stock, Out of Stock, Coming Soon) and base specifications originate from supplier feeds and are formatted and edited by Multan Tech Accessories for clarity. We do not claim independent laboratory testing of every catalog item.
    </p>

    <h2 style="font-size: 1.4rem; margin: 2rem 0 0.75rem;">2. Transparent Pricing Formula</h2>
    <p style="margin-bottom: 1.25rem;">
      Retail prices in Pakistani Rupees (PKR) are calculated using a fixed formula based on wholesale acquisition costs plus handling, packaging, and operational expenses:
    </p>
    <div style="background: var(--surface-2); padding: 1rem 1.25rem; border-radius: 8px; font-family: monospace; font-size: 0.95rem; margin-bottom: 1.25rem;">
      Selling Price = Ceil((Wholesale Cost &times; (1 + Markup %) + Handling Fee) / Rounding Multiple) &times; Rounding Multiple
    </div>
    <p style="margin-bottom: 1.25rem;">
      We never artificially inflate retail prices to show false discounts or promotional badges. Where manual price overrides exist, they reflect volume discounts negotiated for our customers.
    </p>

    <h2 style="font-size: 1.4rem; margin: 2rem 0 0.75rem;">3. Authentic Catalog &amp; Editorial Standards</h2>
    <p style="margin-bottom: 1.25rem;">
      To keep product listings useful and trustworthy, our catalog and editorial guides follow these principles:
    </p>
    <ul style="margin-left: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
      <li>We exclude known replica, &ldquo;high copy,&rdquo; or imitation products from active storefront listings.</li>
      <li>All buying guides are written and maintained by founder Muneeb Ahmad using published manufacturer specifications and technical standards.</li>
      <li>Supplier descriptions are cleaned, structured, and edited for readability while preserving factual model details.</li>
    </ul>
  </div>
</div>
