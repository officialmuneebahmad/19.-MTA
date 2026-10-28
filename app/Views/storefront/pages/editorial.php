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
      Transparency is fundamental to our business philosophy. This document explains how product information, technical specifications, and retail pricing are updated on <strong>MultanTech.store</strong>.
    </p>

    <h2 style="font-size: 1.4rem; margin: 2rem 0 0.75rem;">1. Catalog & Stock Synchronization</h2>
    <p style="margin-bottom: 1.25rem;">
      Multan Tech Accessories operates an automated, hourly synchronization pipeline connected directly to authorized wholesale distributor feeds. Every product status (In Stock, Out of Stock, Coming Soon) is verified regularly to prevent misleading stock claims.
    </p>

    <h2 style="font-size: 1.4rem; margin: 2rem 0 0.75rem;">2. Transparent Pricing Formula</h2>
    <p style="margin-bottom: 1.25rem;">
      Retail prices in Pakistani Rupees (PKR) are calculated using a fixed formula based on real wholesale acquisition costs plus local import duties, warehousing, and quality inspection margins:
    </p>
    <div style="background: var(--surface-2); padding: 1rem 1.25rem; border-radius: 8px; font-family: monospace; font-size: 0.95rem; margin-bottom: 1.25rem;">
      Selling Price = Ceil((Wholesale Cost &times; (1 + Markup %) + Handling Fee) / Rounding Multiple) &times; Rounding Multiple
    </div>
    <p style="margin-bottom: 1.25rem;">
      We never artificially inflate retail prices to show false discounts or promotional badges. Where manual price overrides exist, they reflect volume discounts negotiated for our customers.
    </p>

    <h2 style="font-size: 1.4rem; margin: 2rem 0 0.75rem;">3. Original Editorial Content & Anti-Duplication Standards</h2>
    <p style="margin-bottom: 1.25rem;">
      To prevent the publication of generic, unhelpful, or low-quality scraped content, our technical guides and product descriptions undergo strict editorial validation:
    </p>
    <ul style="margin-left: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
      <li>No unverified wattage claims, battery capacity exaggerations, or fake certifications.</li>
      <li>All technical buying guides are authored by Muneeb Ahmad or our hardware specialists and reviewed before publishing.</li>
      <li>Raw supplier text is sanitized and retained as internal reference material, not published as original editorial work.</li>
    </ul>
  </div>
</div>
