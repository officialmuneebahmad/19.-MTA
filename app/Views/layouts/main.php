<!DOCTYPE html>
<html lang="en-PK" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Blocking Theme Script (Prevents flash of light/dark mode) -->
  <script>
    (function(){
      var t = localStorage.getItem('mta_theme');
      var d = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      var theme = t || (d ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', theme);
      document.documentElement.style.colorScheme = theme;
    })();
  </script>

  <title><?= e($meta_title ?? 'Multan Tech Accessories — Authentic Tech & Accessories in Pakistan') ?></title>
  <meta name="description" content="<?= e($meta_description ?? 'Buy authentic computer and mobile accessories in Pakistan. Cash on Delivery across Multan, Lahore, Karachi & all cities. Free delivery over Rs. 5,000.') ?>">
  <meta name="theme-color" content="#FAF9F5">
  <link rel="canonical" href="<?= e($canonical_url ?? mta_url($_SERVER['REQUEST_URI'] ?? '')) ?>">

  <!-- Favicon (Rounded) -->
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png">
  <link rel="shortcut icon" href="/favicon.ico">

  <!-- Google Fonts: Space Grotesk & Sora Bold (Geometric Tech Sans-Serif) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

  <?php if (!empty($noindex)): ?>
    <meta name="robots" content="noindex, follow">
  <?php else: ?>
    <meta name="robots" content="index, follow">
  <?php endif; ?>

  <link rel="alternate" hreflang="en-PK" href="<?= e($canonical_url ?? mta_url()) ?>">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="<?= e($og_type ?? 'website') ?>">
  <meta property="og:site_name" content="Multan Tech Accessories">
  <meta property="og:title" content="<?= e($meta_title ?? 'Multan Tech Accessories') ?>">
  <meta property="og:description" content="<?= e($meta_description ?? 'Computer and mobile accessories in Pakistan.') ?>">
  <meta property="og:url" content="<?= e($canonical_url ?? mta_url()) ?>">
  <meta property="og:image" content="<?= e($og_image ?? mta_url('/assets/images/og-banner.webp')) ?>">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($meta_title ?? 'Multan Tech Accessories') ?>">
  <meta name="twitter:description" content="<?= e($meta_description ?? 'Computer and mobile accessories in Pakistan.') ?>">
  <meta name="twitter:image" content="<?= e($og_image ?? mta_url('/assets/images/og-banner.webp')) ?>">

  <?php if (!empty($product_id)): ?>
    <meta name="product-id" content="<?= (int)$product_id ?>">
  <?php endif; ?>

  <!-- Google Search Console & Bing Verification -->
  <?php if (!empty(mta_setting('gsc_verification_tag'))): ?>
    <meta name="google-site-verification" content="<?= e(mta_setting('gsc_verification_tag')) ?>">
  <?php endif; ?>
  <?php if (!empty(mta_setting('bing_verification_tag'))): ?>
    <meta name="msvalidate.01" content="<?= e(mta_setting('bing_verification_tag')) ?>">
  <?php endif; ?>

  <!-- Critical CSS Inlined -->
  <style>
    <?php require MTA_PUBLIC . '/assets/css/critical.css'; ?>
  </style>

  <!-- Main App Stylesheet -->
  <link rel="stylesheet" href="/assets/css/app.css">

  <!-- JSON-LD Structured Data -->
  <?php if (!empty($structured_data)): ?>
    <script type="application/ld+json">
      <?= is_array($structured_data) ? json_encode($structured_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $structured_data ?>
    </script>
  <?php else: ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ComputerStore",
      "name": "Multan Tech Accessories",
      "url": "https://multantech.store",
      "telephone": "+923070655275",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Al-Quresh Phase 3, Sher Shah Road",
        "addressLocality": "Multan",
        "addressRegion": "Punjab",
        "addressCountry": "PK"
      },
      "priceRange": "PKR",
      "paymentAccepted": "Cash on Delivery",
      "currenciesAccepted": "PKR"
    }
    </script>
  <?php endif; ?>
</head>
<body>

  <!-- Top Announcement Strip (Exact default text, Marquee) -->
  <div class="announcement-bar" role="region" aria-label="Store Announcement">
    <div class="announcement-track">
      <span><?= e(mta_setting('announcement_strip', 'FREE DELIVERY ABOVE 5000 - FREE DELIVERY ON SECOND ORDER - MINIMUM DELIVERY CHARGES ONLY FOR MULTANIS')) ?></span>
      &nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;&nbsp;
      <span><?= e(mta_setting('announcement_strip', 'FREE DELIVERY ABOVE 5000 - FREE DELIVERY ON SECOND ORDER - MINIMUM DELIVERY CHARGES ONLY FOR MULTANIS')) ?></span>
    </div>
  </div>

  <!-- Header -->
  <header class="site-header" role="banner">
    <div class="header-container">
      
      <!-- Mobile Hamburger -->
      <button class="action-btn mobile-hamburger" id="mobile-drawer-open" aria-label="Open Navigation Menu">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
      </button>

      <!-- Brand Logo (Day & Night Adaptive) -->
      <a href="/" class="header-logo" aria-label="Multan Tech Accessories Homepage">
        <img src="/assets/images/logo-day.png" alt="Multan Tech Accessories" class="mta-logo mta-logo-day" width="165" height="42">
        <img src="/assets/images/logo-night.png" alt="Multan Tech Accessories" class="mta-logo mta-logo-night" width="165" height="42">
      </a>

      <!-- Desktop Search -->
      <div class="header-search">
        <div class="search-input-wrap">
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          <form action="/search" method="GET">
            <input type="search" name="q" class="search-input" placeholder="Search chargers, cables, speakers, gaming gear..." value="<?= e($_GET['q'] ?? '') ?>" autocomplete="off" aria-label="Search accessories">
          </form>
          <div class="search-suggestions"></div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="header-actions">
        <!-- WhatsApp Direct -->
        <a href="<?= e(mta_whatsapp_url('923070655275', 'Hello Multan Tech Accessories, I would like to inquire about products.')) ?>" target="_blank" rel="noopener noreferrer" class="action-btn action-btn-whatsapp" aria-label="Chat on WhatsApp" title="WhatsApp 0307-0655275">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.04 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67Z"/></svg>
        </a>

        <!-- Cart Link -->
        <?php $cartCount = count(\App\Core\Session::get('cart', [])); ?>
        <a href="/cart" class="action-btn" aria-label="Shopping Cart">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          <span class="cart-count" id="header-cart-count"><?= $cartCount ?></span>
        </a>

        <!-- Day / Night Theme Button -->
        <button class="action-btn theme-toggle" id="theme-toggle-btn" aria-label="Toggle Day and Night mode" aria-pressed="false">
          <!-- Sun Icon -->
          <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
          <!-- Moon Icon -->
          <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
      </div>

    </div>
  </header>

  <!-- Mobile Search Second Row -->
  <div class="mobile-search-bar">
    <div class="search-input-wrap">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <form action="/search" method="GET">
        <input type="search" name="q" class="search-input" placeholder="Search accessories..." value="<?= e($_GET['q'] ?? '') ?>" autocomplete="off">
      </form>
    </div>
  </div>

  <!-- Navigation Bar (Only What Matters + "More" Dropdown) -->
  <nav class="main-nav" aria-label="Main Navigation">
    <div class="container nav-container">
      <ul class="nav-links">
        <li><a href="/" class="nav-link <?= ($_SERVER['REQUEST_URI'] ?? '/') === '/' ? 'active' : '' ?>">Home</a></li>
        
        <!-- Shop by Category Dropdown -->
        <li class="dropdown">
          <button class="nav-link dropdown-trigger" type="button" aria-haspopup="true">
            Shop by Category
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </button>
          <ul class="dropdown-menu">
            <li><a href="/categories/mobile-accessories" class="dropdown-item">Mobile Accessories</a></li>
            <li><a href="/categories/computer-accessories" class="dropdown-item">Computer Accessories</a></li>
            <li><a href="/categories/audio-and-speakers" class="dropdown-item">Audio & Speakers</a></li>
            <li><a href="/categories/power-and-charging" class="dropdown-item">Power & Charging</a></li>
            <li><a href="/categories/gaming" class="dropdown-item">Gaming Accessories</a></li>
            <li><a href="/categories/storage-and-memory" class="dropdown-item">Storage & Memory</a></li>
            <li><a href="/categories/networking" class="dropdown-item">Networking</a></li>
            <li><a href="/categories/cables-and-adapters" class="dropdown-item">Cables & Adapters</a></li>
            <li><a href="/categories/smart-and-cctv" class="dropdown-item">Smart & CCTV</a></li>
          </ul>
        </li>

        <li><a href="/best-sellers" class="nav-link">Best Sellers</a></li>
        <li><a href="/brands" class="nav-link">Brands</a></li>
        <li><a href="/guides" class="nav-link">Guides</a></li>
        <li><a href="/track-order" class="nav-link">Track Order</a></li>
        <li><a href="/contact" class="nav-link">Contact</a></li>

        <!-- "More" Dropdown -->
        <li class="dropdown">
          <button class="nav-link dropdown-trigger" type="button" aria-haspopup="true">
            More
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </button>
          <ul class="dropdown-menu">
            <li><a href="/about" class="dropdown-item">About Store</a></li>
            <li><a href="/shipping-and-delivery" class="dropdown-item">Shipping & Delivery</a></li>
            <li><a href="/returns-and-refunds" class="dropdown-item">Returns & Refunds</a></li>
            <li><a href="/editorial-and-pricing-policy" class="dropdown-item">Editorial & Pricing</a></li>
            <li><a href="/faq" class="dropdown-item">FAQ</a></li>
            <li><a href="/privacy-policy" class="dropdown-item">Privacy Policy</a></li>
            <li><a href="/terms-and-conditions" class="dropdown-item">Terms & Conditions</a></li>
            <li><a href="/sitemap" class="dropdown-item">HTML Sitemap</a></li>
          </ul>
        </li>
      </ul>

      <!-- Store Contact & City Tag -->
      <div style="font-size: 0.825rem; font-weight: 600; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem;">
        <span>Multan Store: <strong>0307-0655275</strong></span>
      </div>
    </div>
  </nav>

  <!-- Flash Messages -->
  <?php if (\App\Core\Session::hasFlash('success')): ?>
    <div class="container" style="margin-top: 1.5rem;">
      <div style="background: var(--success-bg); color: var(--success); padding: 1rem 1.25rem; border-radius: var(--radius-md); font-weight: 600;">
        <?= e(\App\Core\Session::getFlash('success')) ?>
      </div>
    </div>
  <?php endif; ?>
  <?php if (\App\Core\Session::hasFlash('error')): ?>
    <div class="container" style="margin-top: 1.5rem;">
      <div style="background: var(--danger-bg); color: var(--danger); padding: 1rem 1.25rem; border-radius: var(--radius-md); font-weight: 600;">
        <?= e(\App\Core\Session::getFlash('error')) ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Main Body Content Slot -->
  <main class="main-content" role="main">
    <?= $content ?>
  </main>

  <!-- Floating Buttons (Bottom-Right) -->
  <div class="floating-actions" aria-label="Quick Actions">
    <!-- Back to top with real-time SVG progress ring -->
    <button class="btn-back-to-top" id="btn-back-to-top" aria-label="Back to top">
      <svg class="progress-ring" width="48" height="48" viewBox="0 0 48 48">
        <circle cx="24" cy="24" r="22.28" fill="none" stroke="var(--border)" stroke-width="3"></circle>
        <circle class="progress-ring-circle" cx="24" cy="24" r="22.28" fill="none" stroke-width="3"></circle>
      </svg>
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
    </button>

    <!-- WhatsApp Quick Contact (56px) -->
    <a href="<?= e(mta_whatsapp_url('923070655275', 'Hi Multan Tech Accessories, I have an inquiry.')) ?>" target="_blank" rel="noopener noreferrer" class="btn-float-wa" aria-label="Chat on WhatsApp" title="WhatsApp 0307-0655275">
      <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.04 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67Z"/></svg>
    </a>
  </div>

  <!-- Mobile Off-Canvas Drawer -->
  <div class="mobile-drawer-backdrop" id="mobile-drawer-backdrop"></div>
  <div class="mobile-drawer" id="mobile-drawer" aria-label="Mobile Navigation">
    <div class="drawer-header">
      <div style="display:flex; align-items:center;">
        <img src="/assets/images/logo-day.png" alt="Multan Tech Accessories" class="mta-logo mta-logo-day" width="130" height="34">
        <img src="/assets/images/logo-night.png" alt="Multan Tech Accessories" class="mta-logo mta-logo-night" width="130" height="34">
      </div>
      <button id="mobile-drawer-close" aria-label="Close Menu" style="padding: 0.5rem;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    </div>
    <div class="drawer-body">
      <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.85rem;">
        <li><a href="/" style="font-weight: 600;">Home</a></li>
        <li><a href="/best-sellers" style="font-weight: 600;">Best Sellers</a></li>
        <li><a href="/categories/mobile-accessories">Mobile Accessories</a></li>
        <li><a href="/categories/computer-accessories">Computer Accessories</a></li>
        <li><a href="/categories/audio-and-speakers">Audio & Speakers</a></li>
        <li><a href="/categories/power-and-charging">Power & Charging</a></li>
        <li><a href="/categories/gaming">Gaming Accessories</a></li>
        <li><a href="/categories/storage-and-memory">Storage & Memory</a></li>
        <li><a href="/categories/networking">Networking Devices</a></li>
        <li><a href="/categories/cables-and-adapters">Cables & Adapters</a></li>
        <li><a href="/categories/smart-and-cctv">Smart & CCTV</a></li>
        <li style="border-top: 1px solid var(--border); padding-top: 0.75rem;"><a href="/brands">Brands Directory</a></li>
        <li><a href="/guides">Tech Buying Guides</a></li>
        <li><a href="/track-order">Track Your Order</a></li>
        <li><a href="/contact">Contact & Store Location</a></li>
        <li><a href="/about">About Store</a></li>
      </ul>
    </div>
  </div>

  <!-- Site Footer -->
  <footer class="site-footer" role="contentinfo">
    <div class="container">
      <div class="footer-grid">
        
        <!-- About Blurb -->
        <div>
          <div style="margin-bottom: 1rem;">
            <img src="/assets/images/logo-day.png" alt="Multan Tech Accessories" class="mta-logo mta-logo-day" width="150" height="38">
            <img src="/assets/images/logo-night.png" alt="Multan Tech Accessories" class="mta-logo mta-logo-night" width="150" height="38">
          </div>
          <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1rem;">
            Your trusted destination for genuine computer hardware, mobile accessories, chargers, cables, and gaming gear in Pakistan. Shipped across Multan and nationwide with Cash on Delivery.
          </p>
          <div class="footer-socials">
            <a href="<?= e(mta_setting('social_facebook', 'https://facebook.com/TODO-OWNER')) ?>" target="_blank" rel="noopener me" class="social-icon-btn" aria-label="Facebook">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
            </a>
            <a href="<?= e(mta_setting('social_instagram', 'https://instagram.com/TODO-OWNER')) ?>" target="_blank" rel="noopener me" class="social-icon-btn" aria-label="Instagram">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
            </a>
            <a href="<?= e(mta_setting('social_tiktok', 'https://tiktok.com/@TODO-OWNER')) ?>" target="_blank" rel="noopener me" class="social-icon-btn" aria-label="TikTok">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.29 0 .58.04.86.13V9.4a6.33 6.33 0 0 0-.86-.06A6.34 6.34 0 0 0 3 15.68a6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V9.07a8.16 8.16 0 0 0 4.91 1.63V7.28a4.81 4.81 0 0 1-1-.59z"/></svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="footer-col-title">Shop Categories</h4>
          <ul class="footer-links">
            <li><a href="/categories/mobile-accessories" class="footer-link">Mobile Accessories</a></li>
            <li><a href="/categories/computer-accessories" class="footer-link">Computer Accessories</a></li>
            <li><a href="/categories/audio-and-speakers" class="footer-link">Audio & Speakers</a></li>
            <li><a href="/categories/power-and-charging" class="footer-link">Fast Chargers & Banks</a></li>
            <li><a href="/categories/gaming" class="footer-link">Gaming Peripherals</a></li>
            <li><a href="/categories/storage-and-memory" class="footer-link">Flash Drives & Memory</a></li>
          </ul>
        </div>

        <!-- Store Policies -->
        <div>
          <h4 class="footer-col-title">Customer Care</h4>
          <ul class="footer-links">
            <li><a href="/shipping-and-delivery" class="footer-link">Shipping & Delivery</a></li>
            <li><a href="/returns-and-refunds" class="footer-link">Returns & Refunds</a></li>
            <li><a href="/editorial-and-pricing-policy" class="footer-link">Pricing & Stock Policy</a></li>
            <li><a href="/track-order" class="footer-link">Track Your Order</a></li>
            <li><a href="/faq" class="footer-link">Frequently Asked Questions</a></li>
            <li><a href="/privacy-policy" class="footer-link">Privacy Policy</a></li>
            <li><a href="/terms-and-conditions" class="footer-link">Terms & Conditions</a></li>
          </ul>
        </div>

        <!-- Physical Contact Info -->
        <div>
          <h4 class="footer-col-title">Multan Store Location</h4>
          <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 0.85rem;">
            <?= e(mta_setting('store_address', 'Al-Quresh Phase 3, Sher Shah Road, Multan, Punjab, Pakistan')) ?>
          </p>
          <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 0.5rem;">
            <strong>Phone / WhatsApp:</strong> <a href="tel:+923070655275" style="color:var(--text);font-weight:600;">0307-0655275</a>
          </p>
          <div style="margin-top: 1rem; display: inline-flex; align-items: center; gap: 0.5rem; background: var(--surface-2); padding: 0.4rem 0.8rem; border-radius: var(--radius-sm); font-size: 0.8rem; font-weight: 600;">
            <span>&#128666; Cash on Delivery Available</span>
          </div>
        </div>

      </div>

      <!-- Bottom Credits Strip -->
      <div class="footer-bottom">
        <div>
          &copy; <?= date('Y') ?> Multan Tech Accessories. All rights reserved.
          <span class="footer-credit">&bull; <?= e(mta_setting('founder_credit', 'Founded & Developed by Muneeb Ahmad')) ?></span>
        </div>
        <div>
          Currency: <strong>PKR (Rs.)</strong> &bull; Serving Multan & All Pakistan
        </div>
      </div>
    </div>
  </footer>

  <script src="/assets/js/main.js" defer></script>
</body>
</html>
