<?php
/**
 * Multan Tech Accessories — Human HTML Sitemap View
 * Guarantees all indexable site pages are discoverable within 3 clicks
 */
?>
<div class="container" style="padding: 2rem 1.25rem; max-width: 900px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <h1 style="font-size: 2.25rem; font-weight: 800; margin: 1rem 0 1rem;">Multan Tech Accessories &mdash; HTML Sitemap</h1>
  <p style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 2.5rem;">
    Direct navigational directory for search engines and shoppers.
  </p>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem;" class="sitemap-grid">
    
    <!-- Main Store & Categories -->
    <div>
      <h2 style="font-size: 1.25rem; font-weight: 700; border-bottom: 2px solid var(--accent-strong); padding-bottom: 0.5rem; margin-bottom: 1rem;">
        Categories
      </h2>
      <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.95rem;">
        <li><a href="/" style="font-weight: 600; color: var(--accent-strong);">Storefront Homepage</a></li>
        <li><a href="/best-sellers" style="font-weight: 600;">Best Selling Accessories</a></li>
        <li><a href="/categories" style="font-weight: 600;">All Categories Directory</a></li>
        <?php foreach ($categories as $cat): ?>
          <li style="margin-left: 1rem;">
            <a href="/categories/<?= e($cat['slug']) ?>">&bull; <?= e($cat['name']) ?> (<?= (int)$cat['product_count'] ?>)</a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Brands, Guides & Policies -->
    <div>
      <h2 style="font-size: 1.25rem; font-weight: 700; border-bottom: 2px solid var(--accent-strong); padding-bottom: 0.5rem; margin-bottom: 1rem;">
        Brands, Guides & Policies
      </h2>
      <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.95rem;">
        <li><a href="/brands" style="font-weight: 600;">All Featured Brands</a></li>
        <li><a href="/guides" style="font-weight: 600;">Tech Buying Guides</a></li>
        <li><a href="/author/muneeb-ahmad" style="font-weight: 600;">Author: Muneeb Ahmad</a></li>
        <li><a href="/about">About Store</a></li>
        <li><a href="/contact">Contact & Store Location</a></li>
        <li><a href="/shipping-and-delivery">Shipping & Delivery Rules</a></li>
        <li><a href="/returns-and-refunds">7-Day Checking Warranty & Returns</a></li>
        <li><a href="/editorial-and-pricing-policy">Editorial & Pricing Policy</a></li>
        <li><a href="/track-order">Order Tracking</a></li>
        <li><a href="/faq">Frequently Asked Questions</a></li>
        <li><a href="/privacy-policy">Privacy Policy</a></li>
        <li><a href="/terms-and-conditions">Terms & Conditions</a></li>
        <li><a href="/cookie-policy">Cookie Policy</a></li>
      </ul>
    </div>

  </div>
</div>
