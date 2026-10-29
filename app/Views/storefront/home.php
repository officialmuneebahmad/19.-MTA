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
      
      <!-- Slide 1 (Micro SD Cards) -->
      <a href="/categories/storage-and-memory" class="carousel-slide" aria-label="Shop Micro SD and Storage Cards">
        <img src="https://res.cloudinary.com/dyrncibsu/image/upload/v1793282104/Micro_SD_card_product_advertisement_9d33082f_2K_hujze7.jpg" alt="High-Speed Micro SD Cards" class="carousel-img" width="2752" height="1536" fetchpriority="high">
      </a>

      <!-- Slide 2 (Keyboards) -->
      <a href="/categories/gaming" class="carousel-slide" aria-label="Shop RGB Gaming Keyboards">
        <img src="https://res.cloudinary.com/dyrncibsu/image/upload/v1793282102/Keyboard_product_advertisement_l__d263c809_2K_ojl0mo.jpg" alt="RGB Gaming and Office Keyboards" class="carousel-img" width="2752" height="1536" loading="lazy">
      </a>

      <!-- Slide 3 (WiFi Repeaters) -->
      <a href="/categories/networking" class="carousel-slide" aria-label="Shop WiFi Repeaters and Extenders">
        <img src="https://res.cloudinary.com/dyrncibsu/image/upload/v1793282107/WiFi_repeater_product_advertisement_fb0e0390_2K_xbo6gs.jpg" alt="WiFi Signal Repeaters and Extenders" class="carousel-img" width="2752" height="1536" loading="lazy">
      </a>

    </div>

    <!-- Carousel Indicator Dots -->
    <div class="carousel-dots">
      <button class="carousel-dot active" aria-label="Slide 1"></button>
      <button class="carousel-dot" aria-label="Slide 2"></button>
      <button class="carousel-dot" aria-label="Slide 3"></button>
    </div>
  </div>
</div>

<div class="container">

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

    <div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:center;">
      <?php foreach ($featuredBrands as $b): ?>
        <a href="/brands/<?= e($b['slug']) ?>" class="btn btn-secondary" style="font-weight:700;">
          <?= e($b['name']) ?>
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

      <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:1.5rem;">
        <?php foreach ($latestGuides as $guide): ?>
          <article style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:1.5rem; display:flex; flex-direction:column;">
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
