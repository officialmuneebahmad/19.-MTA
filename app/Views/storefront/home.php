<?php
/**
 * Multan Tech Accessories — Homepage View
 * Exactly matching Section 6 ordering:
 * 1. Hero Carousel (reserved aspect ratio)
 * 2. Shop-by-Category Tiles
 * 3. Best Sellers (Popularity score ranked)
 * 4. New Arrivals
 * 5. Brands Row
 * 6. Trust Strip (Cash on Delivery, Delivery Rules, WhatsApp Support)
 * 7. Latest Guides
 * 8. Short Original About the Store text block
 */
?>

<!-- 1. Hero Carousel -->
<div class="hero-carousel-wrap">
  <div class="hero-carousel" role="region" aria-roledescription="carousel" aria-label="Featured Accessories">
    <div class="carousel-track">
      
      <!-- Slide 1 (Store Migration & New Products Announcement) -->
      <a href="/about" class="carousel-slide" aria-label="multantech.store is now multantech.pk - 500+ New Products">
        <img src="/assets/images/banners/banner-store-to-pk-migration.webp" alt="multantech.store is now officially multantech.pk - 500+ New Exciting Products" class="carousel-img" width="1024" height="571" fetchpriority="high" loading="eager">
      </a>

      <!-- Slide 2 (One-Stop Tech Accessories Store) -->
      <a href="/categories" class="carousel-slide" aria-label="Your One-Stop Tech Accessories Store">
        <img src="/assets/images/banners/banner-one-stop-tech-store.webp" alt="Multan Tech Accessories - Your One-Stop Tech Accessories Store in Pakistan" class="carousel-img" width="1024" height="571" loading="eager">
      </a>

      <!-- Slide 3 (Xiaomi Mi WiFi Repeater Pro 300M) -->
      <a href="/categories/networking" class="carousel-slide" aria-label="Xiaomi Mi WiFi Repeater Pro 300M - Rs. 3,399">
        <img src="/assets/images/banners/banner-xiaomi-wifi-repeater.webp" alt="Xiaomi Mi WiFi Repeater Pro 300M - 300 Mbps Range Extender in Stock - Rs. 3,399" class="carousel-img" width="1024" height="571" loading="eager">
      </a>

      <!-- Slide 4 (Xtrike Me KB-512 Keyboard) -->
      <a href="/categories/gaming" class="carousel-slide" aria-label="Xtrike Me KB-512 Mechanical Feel Keyboard - Rs. 2,999">
        <img src="/assets/images/banners/banner-xtrike-me-kb512-keyboard.webp" alt="Xtrike Me KB-512 Mechanical Feel Keyboard - 108 Keys USB-C Wired - Rs. 2,999" class="carousel-img" width="1024" height="571" loading="eager">
      </a>

      <!-- Slide 5 (Aspor 8GB Class 10 Micro SD Card) -->
      <a href="/categories/storage-and-memory" class="carousel-slide" aria-label="Aspor 8GB Class 10 Micro SD Card - Rs. 999">
        <img src="/assets/images/banners/banner-aspor-8gb-microsd.webp" alt="Aspor 8GB Class 10 High Speed Micro SD Card with SD Adapter - Rs. 999" class="carousel-img" width="1024" height="571" loading="eager">
      </a>

    </div>

    <!-- Carousel Indicator Dots -->
    <div class="carousel-dots">
      <button class="carousel-dot active" aria-label="Slide 1"></button>
      <button class="carousel-dot" aria-label="Slide 2"></button>
      <button class="carousel-dot" aria-label="Slide 3"></button>
      <button class="carousel-dot" aria-label="Slide 4"></button>
      <button class="carousel-dot" aria-label="Slide 5"></button>
    </div>
  </div>
</div>

<div class="container">

  <!-- Main Accessible H1 for Technical SEO Crawlers -->
  <h1 class="visually-hidden" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0; white-space: nowrap;">
    Multan Tech Accessories &mdash; Original Computer &amp; Mobile Accessories in Pakistan
  </h1>

  <!-- Domain Migration Highlight Announcement -->
  <aside class="domain-migration-banner" aria-label="Official Domain Migration Announcement">
    <div class="domain-migration-badge">
      <span class="domain-migration-pulse" aria-hidden="true"></span>
      <span>Official Store Announcement</span>
    </div>
    <div class="domain-migration-headline">
      <span class="domain-old-name">multantech.store</span>
      <span class="domain-migration-arrow" aria-hidden="true">&rarr;</span>
      <span class="domain-new-name">multantech.pk</span>
    </div>
    <div class="domain-migration-callout">
      <strong class="domain-migration-highlight-text">multantech.store is now multantech.pk.</strong>
    </div>
    <p class="domain-migration-sub">
      We have officially moved to Pakistan’s national domain (<strong>.pk</strong>). All products, customer orders, warranty protections, and WhatsApp support continue seamlessly at <strong>multantech.pk</strong>.
    </p>
  </aside>

  <!-- 2. Shop-by-Category Tiles -->
  <section class="section" aria-labelledby="cat-heading">
    <div class="section-header">
      <div>
        <h2 id="cat-heading" class="section-title">Shop by Category</h2>
        <p style="font-size:0.9rem; color:var(--text-muted); margin-top:0.25rem;">Quality computer and mobile accessories tailored for Pakistani users</p>
      </div>
      <a href="/categories" class="section-link">All Categories &rarr;</a>
    </div>

    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
        <a href="/categories/<?= e($cat['slug']) ?>" class="category-tile">
          <div class="category-tile-icon">
            <?= mta_category_icon($cat['slug']) ?>
          </div>
          <div class="category-tile-name"><?= e($cat['name']) ?></div>
          <div class="category-tile-count"><?= (int)$cat['product_count'] ?> Products</div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- 3. Best Sellers (Ranked by Popularity Score) -->
  <section class="section" aria-labelledby="bestsellers-heading">
    <div class="section-header">
      <div>
        <h2 id="bestsellers-heading" class="section-title">Best Sellers</h2>
        <p style="font-size:0.9rem; color:var(--text-muted); margin-top:0.25rem;">Most popular accessories based on verified orders and customer demand</p>
      </div>
      <a href="/best-sellers" class="section-link">View All Best Sellers &rarr;</a>
    </div>

    <div class="product-grid">
      <?php foreach ($bestSellers as $product): ?>
        <?php require MTA_APP . '/Views/partials/product-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- 4. New Arrivals -->
  <section class="section" aria-labelledby="new-heading">
    <div class="section-header">
      <div>
        <h2 id="new-heading" class="section-title">New Arrivals</h2>
        <p style="font-size:0.9rem; color:var(--text-muted); margin-top:0.25rem;">Freshly stocked accessories and newly released tech gadgets</p>
      </div>
      <a href="/categories?sort=newest" class="section-link">Browse New Gear &rarr;</a>
    </div>

    <div class="product-grid">
      <?php foreach ($newArrivals as $product): ?>
        <?php require MTA_APP . '/Views/partials/product-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- 5. Brands Row -->
  <section class="section" aria-labelledby="brands-heading">
    <div class="section-header">
      <div>
        <h2 id="brands-heading" class="section-title">Featured Brands</h2>
        <p style="font-size:0.9rem; color:var(--text-muted); margin-top:0.25rem;">Trusted manufacturers with tested durability and performance</p>
      </div>
      <a href="/brands" class="section-link">All Brands &rarr;</a>
    </div>

    <div class="brands-grid" role="list">
      <?php foreach ($featuredBrands as $b): ?>
        <?php 
          $logoCheck = mta_verify_brand_logo($b['logo_path'] ?? null);
          if ($logoCheck['status'] !== 'ok') {
              continue;
          }
        ?>
        <a href="/brands/<?= e($b['slug']) ?>" class="brand-tile" aria-label="<?= e($b['name']) ?> products" role="listitem">
          <img src="<?= e($b['logo_path']) ?>" alt="<?= e($b['logo_alt'] ?: ($b['name'] . ' logo')) ?>" class="brand-tile-logo" width="120" height="48" loading="lazy">
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- 6. Trust Strip -->
  <section class="trust-strip" aria-label="Store Guarantees">
    <div class="trust-item">
      <div class="trust-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
      </div>
      <div>
        <h3 class="trust-title">Cash on Delivery (COD)</h3>
        <p class="trust-desc">Pay safely at your doorstep across Multan, Lahore, Karachi, Islamabad & all Pakistani cities.</p>
      </div>
    </div>

    <div class="trust-item">
      <div class="trust-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
      </div>
      <div>
        <h3 class="trust-title">Free Delivery Rules</h3>
        <p class="trust-desc">Free delivery on orders above Rs. 5,000 and 100% free delivery on your second order.</p>
      </div>
    </div>

    <div class="trust-item">
      <div class="trust-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
      </div>
      <div>
        <h3 class="trust-title">Instant WhatsApp Support</h3>
        <p class="trust-desc">Got questions about compatibility? Chat directly with Muneeb Ahmad at 0307-0655275.</p>
      </div>
    </div>
  </section>

  <!-- 7. Latest Guides -->
  <?php if (!empty($latestGuides)): ?>
    <section class="section" aria-labelledby="guides-heading">
      <div class="section-header">
        <div>
          <h2 id="guides-heading" class="section-title">Tech Buying Guides & Advice</h2>
          <p style="font-size:0.9rem; color:var(--text-muted); margin-top:0.25rem;">Practical tips on choosing the right chargers, cables, and accessories</p>
        </div>
        <a href="/guides" class="section-link">View All Guides &rarr;</a>
      </div>

      <div class="home-guides-grid">
        <?php foreach ($latestGuides as $guide): ?>
          <article class="home-guide-card">
            <span style="font-size:0.75rem; font-weight:700; color:var(--accent-strong); text-transform:uppercase; margin-bottom:0.5rem;">Buying Guide</span>
            <h3 style="font-size:1.1rem; margin-bottom:0.5rem; line-height:1.35;">
              <a href="/guides/<?= e($guide['slug']) ?>"><?= e($guide['title']) ?></a>
            </h3>
            <p style="font-size:0.875rem; color:var(--text-muted); line-height:1.6; margin-bottom:1rem; flex:1;">
              <?= e($guide['excerpt'] ?? substr(strip_tags($guide['content_html']), 0, 120) . '...') ?>
            </p>
            <a href="/guides/<?= e($guide['slug']) ?>" style="font-weight:600; color:var(--accent-strong); font-size:0.875rem;">Read Guide &rarr;</a>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- 8. Short Original About the Store Text Block -->
  <section class="section" style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:2.5rem; margin-bottom:2rem;">
    <h2 style="font-size:1.5rem; margin-bottom:1rem;">About Multan Tech Accessories</h2>
    <div style="font-size:0.95rem; line-height:1.8; color:var(--text-muted); max-width:850px;">
      <p style="margin-bottom:1rem;">
        <strong>Multan Tech Accessories</strong> was founded and developed by <strong>Muneeb Ahmad</strong> with a single mission: to provide authentic, high-quality computer and mobile accessories to Pakistani consumers without the guesswork or inflated prices.
      </p>
      <p style="margin-bottom:1rem;">
        Operating from our physical location at <em>Al-Quresh Phase 3, Sher Shah Road, Multan</em>, we source authentic hardware and accessories including GaN fast chargers, heavy-duty braided USB-C cables, Bluetooth speakers, gaming peripherals, and high-speed storage memory. Every product undergoes strict quality inspection to verify authentic specifications and durability.
      </p>
      <p>
        Whether you are setting up an office workstation, building a gaming battle station, or looking for reliable mobile accessories, we deliver promptly with <strong>Cash on Delivery</strong> across all of Pakistan. For orders in Multan, we provide the lowest local delivery charges and direct store pickup.
      </p>
    </div>
  </section>

</div>
