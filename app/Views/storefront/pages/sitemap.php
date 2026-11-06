<?php
/**
 * Multan Tech Accessories — Human HTML Sitemap View
 * Guarantees all indexable site pages are discoverable within 2-3 clicks
 */
?>
<div class="container" style="padding: 2rem 1.25rem; max-width: 1000px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <h1 class="page-title" style="font-size: 2.25rem; font-weight: 800; margin: 1rem 0 0.5rem;">Multan Tech Accessories &mdash; HTML Sitemap</h1>
  <p style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 2.5rem;">
    Comprehensive navigational directory for search engines, shoppers, and site crawlers.
  </p>

  <div class="sitemap-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
    
    <!-- 1. Categories -->
    <div>
      <h2 style="font-size: 1.25rem; font-weight: 700; border-bottom: 2px solid var(--accent-strong); padding-bottom: 0.5rem; margin-bottom: 1rem;">
        Product Categories
      </h2>
      <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.95rem; padding-left: 0;">
        <li><a href="/" style="font-weight: 600; color: var(--accent-strong);">Storefront Homepage</a></li>
        <li><a href="/best-sellers" style="font-weight: 600;">Best Selling Accessories</a></li>
        <li><a href="/categories" style="font-weight: 600;">All Categories Directory</a></li>
        <?php foreach ($categories as $cat): ?>
          <li style="margin-left: 0.75rem;">
            <a href="/categories/<?= e($cat['slug']) ?>">&bull; <?= e($cat['name']) ?> <span style="font-size: 0.8rem; color: var(--text-muted);">(<?= (int)$cat['product_count'] ?> items)</span></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- 2. Brands -->
    <div>
      <h2 style="font-size: 1.25rem; font-weight: 700; border-bottom: 2px solid var(--accent-strong); padding-bottom: 0.5rem; margin-bottom: 1rem;">
        Featured Brand Hubs
      </h2>
      <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.95rem; padding-left: 0;">
        <li><a href="/brands" style="font-weight: 600;">All Featured Brands Directory</a></li>
        <?php foreach ($brands as $brand): ?>
          <li style="margin-left: 0.75rem;">
            <a href="/brands/<?= e($brand['slug']) ?>">&bull; <?= e($brand['name']) ?> <span style="font-size: 0.8rem; color: var(--text-muted);">(<?= (int)$brand['product_count'] ?> items)</span></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- 3. Guides & Information -->
    <div>
      <h2 style="font-size: 1.25rem; font-weight: 700; border-bottom: 2px solid var(--accent-strong); padding-bottom: 0.5rem; margin-bottom: 1rem;">
        Tech Guides & Articles
      </h2>
      <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.95rem; padding-left: 0;">
        <li><a href="/guides" style="font-weight: 600;">Buying Guides & Advice</a></li>
        <?php if (!empty($guides)): ?>
          <?php foreach ($guides as $g): ?>
            <li style="margin-left: 0.75rem;">
              <a href="/guides/<?= e($g['slug']) ?>">&bull; <?= e($g['title']) ?></a>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li style="margin-left: 0.75rem; color: var(--text-muted); font-size: 0.85rem;">
            &bull; Curated accessory buying guides regularly updated
          </li>
        <?php endif; ?>
        <li><a href="/author/muneeb-ahmad" style="font-weight: 600;">Author Profile: Muneeb Ahmad</a></li>
        <li><a href="/sitemap.xml" style="font-weight: 600; color: #0369A1;">XML Sitemap Index (Search Engines)</a></li>
      </ul>
    </div>

    <!-- 4. Policies & Support -->
    <div>
      <h2 style="font-size: 1.25rem; font-weight: 700; border-bottom: 2px solid var(--accent-strong); padding-bottom: 0.5rem; margin-bottom: 1rem;">
        Customer Policies & Help
      </h2>
      <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.95rem; padding-left: 0;">
        <li><a href="/about">About Multan Tech Accessories</a></li>
        <li><a href="/contact">Contact Store & WhatsApp Support</a></li>
        <li><a href="/shipping-and-delivery">Shipping & Delivery Rates</a></li>
        <li><a href="/returns-and-refunds">3-Day Checking Warranty & Returns</a></li>
        <li><a href="/editorial-and-pricing-policy">Editorial & Supplier Pricing Policy</a></li>
        <li><a href="/track-order">Consignment & Order Tracking</a></li>
        <li><a href="/faq">Frequently Asked Questions</a></li>
        <li><a href="/privacy-policy">Customer Privacy Policy</a></li>
        <li><a href="/terms-and-conditions">Terms & Conditions of Sale</a></li>
        <li><a href="/cookie-policy">Cookie Policy</a></li>
      </ul>
    </div>

  </div>
</div>
