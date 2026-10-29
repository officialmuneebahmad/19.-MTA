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
      
      <!-- Mobile Hamburger (Larger touch target and icon) -->
      <button class="action-btn mobile-hamburger" id="mobile-drawer-open" aria-label="Open Navigation Menu">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
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
        <!-- Cart Link (Larger 24px icon) -->
        <?php $cartCount = count(\App\Core\Session::get('cart', [])); ?>
        <a href="/cart" class="action-btn" aria-label="Shopping Cart">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          <span class="cart-count" id="header-cart-count"><?= $cartCount ?></span>
        </a>
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

      <!-- Store Info Tag -->
      <div style="font-size: 0.825rem; font-weight: 600; color: var(--accent-strong); display: flex; align-items: center; gap: 0.5rem;">
        <span>&#9889; Fast Delivery Across Pakistan</span>
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

    <!-- WhatsApp Quick Contact (Official Handset + Bubble Icon) -->
    <a href="<?= e(mta_whatsapp_url('923070655275', 'Hi Multan Tech Accessories, I have an inquiry.')) ?>" target="_blank" rel="noopener noreferrer" class="btn-float-wa" aria-label="Chat on WhatsApp" title="WhatsApp 0307-0655275">
      <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12.01 2.002c-5.518 0-9.998 4.48-9.998 9.998 0 1.76.459 3.475 1.332 4.992L2.054 22l5.133-1.346a9.948 9.948 0 004.823 1.248h.004c5.517 0 9.997-4.48 9.997-9.998 0-2.67-1.04-5.18-2.93-7.07A9.928 9.928 0 0012.01 2.002zm5.82 14.156c-.24.674-1.398 1.288-1.928 1.348-.5.056-1.15.08-3.71-.978-2.56-1.058-4.2-3.666-4.33-3.838-.13-.172-1.03-1.37-1.03-2.613 0-1.242.648-1.854.88-2.106.23-.25.5-.314.67-.314.17 0 .34.002.49.01.16.008.37-.06.58.444.22.528.74 1.808.81 1.944.07.136.11.296.02.476-.09.18-.14.29-.27.444-.14.156-.29.35-.41.47-.14.138-.28.288-.12.564.16.276.71 1.17 1.52 1.894 1.05.934 1.93 1.224 2.21 1.36.27.136.43.116.59-.068.16-.184.69-.804.87-1.08.18-.276.36-.23.6-.138.24.092 1.52.716 1.78.846.26.13.43.196.49.304.07.108.07.624-.17 1.298z"/>
      </svg>
    </a>
  </div>

  <!-- Mobile Off-Canvas Drawer (Side Menu) -->
  <div class="mobile-drawer-backdrop" id="mobile-drawer-backdrop"></div>
  <div class="mobile-drawer" id="mobile-drawer" aria-label="Mobile Navigation">
    <div class="drawer-header">
      <div style="display:flex; align-items:center;">
        <img src="/assets/images/logo-day.png" alt="Multan Tech Accessories" class="mta-logo mta-logo-day" width="130" height="34">
        <img src="/assets/images/logo-night.png" alt="Multan Tech Accessories" class="mta-logo mta-logo-night" width="130" height="34">
      </div>
      <button id="mobile-drawer-close" aria-label="Close Menu" style="padding: 0.5rem; color: var(--text);">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    </div>
    <div class="drawer-body">
      <!-- Day / Night Theme Switch in Side Menu -->
      <div style="display:flex; align-items:center; justify-content:space-between; padding:0.75rem 1rem; background:var(--surface-2); border-radius:var(--radius-md); border:1px solid var(--border); margin-bottom:1.25rem;">
        <span style="font-size:0.9rem; font-weight:700; color:var(--text); display:flex; align-items:center; gap:0.5rem;">
          Theme Mode
        </span>
        <button class="action-btn theme-toggle" id="theme-toggle-btn" aria-label="Toggle Day and Night mode" style="width:40px; height:40px;">
          <!-- Sun Icon -->
          <svg class="icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
          <!-- Moon Icon -->
          <svg class="icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
      </div>

      <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.65rem;">
        <li><a href="/" style="font-weight: 700; font-size: 1rem; color: var(--text);">Home</a></li>
        <li><a href="/best-sellers" style="font-weight: 700; font-size: 1rem; color: var(--text);">Best Sellers</a></li>
        
        <li style="font-size:0.75rem; font-weight:800; text-transform:uppercase; color:var(--text-muted); letter-spacing:0.5px; margin-top:0.5rem;">Categories</li>
        <li><a href="/categories/mobile-accessories" style="display:flex; align-items:center; gap:0.6rem; color:var(--text);"><?= mta_category_icon('mobile-accessories') ?> Mobile Accessories</a></li>
        <li><a href="/categories/computer-accessories" style="display:flex; align-items:center; gap:0.6rem; color:var(--text);"><?= mta_category_icon('computer-accessories') ?> Computer Accessories</a></li>
        <li><a href="/categories/audio-and-speakers" style="display:flex; align-items:center; gap:0.6rem; color:var(--text);"><?= mta_category_icon('audio-and-speakers') ?> Audio & Speakers</a></li>
        <li><a href="/categories/power-and-charging" style="display:flex; align-items:center; gap:0.6rem; color:var(--text);"><?= mta_category_icon('power-and-charging') ?> Power & Charging</a></li>
        <li><a href="/categories/gaming" style="display:flex; align-items:center; gap:0.6rem; color:var(--text);"><?= mta_category_icon('gaming') ?> Gaming Accessories</a></li>
        <li><a href="/categories/storage-and-memory" style="display:flex; align-items:center; gap:0.6rem; color:var(--text);"><?= mta_category_icon('storage-and-memory') ?> Storage & Memory</a></li>
        <li><a href="/categories/networking" style="display:flex; align-items:center; gap:0.6rem; color:var(--text);"><?= mta_category_icon('networking') ?> Networking Devices</a></li>
        <li><a href="/categories/cables-and-adapters" style="display:flex; align-items:center; gap:0.6rem; color:var(--text);"><?= mta_category_icon('cables-and-adapters') ?> Cables & Adapters</a></li>
        <li><a href="/categories/smart-and-cctv" style="display:flex; align-items:center; gap:0.6rem; color:var(--text);"><?= mta_category_icon('smart-and-cctv') ?> Smart & CCTV</a></li>

        <li style="border-top: 1px solid var(--border); padding-top: 0.75rem; margin-top: 0.5rem;"><a href="/brands" style="color:var(--text); font-weight:600;">Brands Directory</a></li>
        <li><a href="/guides" style="color:var(--text); font-weight:600;">Tech Buying Guides</a></li>
        <li><a href="/track-order" style="color:var(--text); font-weight:600;">Track Your Order</a></li>
        <li><a href="/contact" style="color:var(--text); font-weight:600;">Contact & Store Location</a></li>
        <li><a href="/about" style="color:var(--text); font-weight:600;">About Store</a></li>
      </ul>
    </div>
  </div>

  <!-- Site Footer -->
  <footer class="site-footer" role="contentinfo">
    <div class="container">
      
      <!-- Store Policy Highlights Strip -->
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1.25rem; padding:1.5rem; background:var(--surface-2); border:1px solid var(--border); border-radius:var(--radius-lg); margin-bottom:2.5rem;" class="footer-policies-strip">
        <div style="display:flex; align-items:flex-start; gap:0.75rem;">
          <div style="width:36px; height:36px; border-radius:8px; background:rgba(255, 98, 35, 0.12); color:var(--accent-strong); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.1rem;">
            &#128737;
          </div>
          <div>
            <strong style="display:block; font-size:0.9rem; color:var(--text);">3 Days Check Warranty Only</strong>
            <span style="font-size:0.785rem; color:var(--text-muted);">Thoroughly inspect your accessories within 72 hours of receiving.</span>
          </div>
        </div>

        <div style="display:flex; align-items:flex-start; gap:0.75rem;">
          <div style="width:36px; height:36px; border-radius:8px; background:rgba(14, 180, 169, 0.12); color:var(--teal); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.1rem;">
            &#128260;
          </div>
          <div>
            <strong style="display:block; font-size:0.9rem; color:var(--text);">No Refund — Exchange Only</strong>
            <span style="font-size:0.785rem; color:var(--text-muted);">Exchanges granted exclusively under eligible technical defect conditions.</span>
          </div>
        </div>

        <div style="display:flex; align-items:flex-start; gap:0.75rem;">
          <div style="width:36px; height:36px; border-radius:8px; background:rgba(255, 98, 35, 0.12); color:var(--accent-strong); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.1rem;">
            &#128230;
          </div>
          <div>
            <strong style="display:block; font-size:0.9rem; color:var(--text);">Customer Return Courier</strong>
            <span style="font-size:0.785rem; color:var(--text-muted);">Customer has to return parcel with their own money & courier service.</span>
          </div>
        </div>

        <div style="display:flex; align-items:flex-start; gap:0.75rem;">
          <div style="width:36px; height:36px; border-radius:8px; background:rgba(37, 211, 102, 0.12); color:var(--whatsapp); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.1rem;">
            &#128666;
          </div>
          <div>
            <strong style="display:block; font-size:0.9rem; color:var(--text);">Cash on Delivery</strong>
            <span style="font-size:0.785rem; color:var(--text-muted);">Fast, secure doorstep delivery across Multan and all Pakistan.</span>
          </div>
        </div>
      </div>

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
            <li><a href="/returns-and-refunds" class="footer-link">Returns & Refunds Policy</a></li>
            <li><a href="/editorial-and-pricing-policy" class="footer-link">Pricing & Stock Policy</a></li>
            <li><a href="/track-order" class="footer-link">Track Your Order</a></li>
            <li><a href="/faq" class="footer-link">Frequently Asked Questions</a></li>
            <li><a href="/privacy-policy" class="footer-link">Privacy Policy</a></li>
            <li><a href="/terms-and-conditions" class="footer-link">Terms & Conditions</a></li>
            <li><a href="<?= e(mta_admin_path()) ?>" class="footer-link" rel="nofollow" style="color:var(--accent-strong); font-weight:700;">Admin Portal &rarr;</a></li>
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
          <span>&bull; <a href="<?= e(mta_admin_path()) ?>" style="color:var(--text-muted); text-decoration:underline;" rel="nofollow">Admin Portal</a></span>
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
