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
  <meta name="mta-build" content="<?= defined('MTA_BUILD_VERSION') ? MTA_BUILD_VERSION : '2026.10.31-v2.4.0' ?>">
  <link rel="canonical" href="<?= e($canonical_url ?? mta_url($_SERVER['REQUEST_URI'] ?? '')) ?>">

  <!-- Favicon (Circular) -->
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32.png?v=3">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16.png?v=3">
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png?v=3">
  <link rel="shortcut icon" href="/favicon.ico?v=3">

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
  <link rel="stylesheet" href="/assets/css/app.css?v=<?= @filemtime(MTA_ROOT . '/assets/css/app.css') ?: time() ?>">

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
      "url": "<?= e(mta_url()) ?>",
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

  <!-- Admin Storefront Toolbar (Shown ONLY to logged-in administrator, never cached in full-page cache) -->
  <?php if (\App\Core\Session::isAdmin()): ?>
    <div style="background:#0F172A; color:#F8FAFC; padding:0.5rem 1rem; font-size:0.8rem; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:9999; border-bottom:1px solid #334155; font-family:-apple-system,BlinkMacSystemFont,sans-serif;">
      <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
        <span style="background:#FF6B2C; color:#FFFFFF; font-weight:800; font-size:0.7rem; padding:0.15rem 0.5rem; border-radius:4px; text-transform:uppercase;">Admin</span>
        <span>Logged in as <strong><?= e(\App\Core\Session::get('admin_user')['email'] ?? 'Administrator') ?></strong></span>
        <?php if (!empty($product['id'])): ?>
          <a href="<?= e(mta_admin_path('/products/' . (int)$product['id'] . '/edit')) ?>" target="_blank" rel="noopener noreferrer" style="color:#38BDF8; font-weight:600; text-decoration:underline;">
            Edit this product in Admin &rarr;
          </a>
        <?php endif; ?>
      </div>
      <a href="<?= e(mta_admin_path()) ?>" target="_blank" rel="noopener noreferrer" style="background:#1E293B; color:#F8FAFC; padding:0.3rem 0.75rem; border-radius:6px; font-weight:600; border:1px solid #475569; text-decoration:none;">
        Open Admin Console (New Tab) &rarr;
      </a>
    </div>
  <?php endif; ?>

  <!-- Top Announcement Strip (Exact default text, Marquee) -->
  <div class="announcement-bar" role="region" aria-label="Store Announcement">
    <div class="announcement-track">
      <span><?= e(mta_setting('announcement_text', mta_setting('announcement_strip', 'FREE DELIVERY ABOVE 5000 - FREE DELIVERY ON SECOND ORDER - MINIMUM DELIVERY ACROSS MULTAN RS. 100 - RS. 250 NATIONWIDE'))) ?></span>
      &nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;&nbsp;
      <span><?= e(mta_setting('announcement_text', mta_setting('announcement_strip', 'FREE DELIVERY ABOVE 5000 - FREE DELIVERY ON SECOND ORDER - MINIMUM DELIVERY ACROSS MULTAN RS. 100 - RS. 250 NATIONWIDE'))) ?></span>
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
        <!-- Day / Night Mode Toggle (Desktop only) -->
        <button class="action-btn theme-toggle desktop-theme-btn" id="theme-toggle-btn-desktop" aria-label="Toggle Day and Night mode" aria-pressed="false" title="Toggle Day / Night Mode">
          <!-- Sun Icon -->
          <svg class="icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
          <!-- Moon Icon -->
          <svg class="icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>

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
      <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M18.403 5.638A8.955 8.955 0 0 0 12.053 3c-4.968 0-9.01 4.042-9.01 9.013 0 1.587.413 3.134 1.198 4.492L3 21l4.634-1.216a8.972 8.972 0 0 0 4.417 1.156h.004c4.968 0 9.007-4.042 9.007-9.014 0-2.408-.937-4.672-2.659-6.288zm-6.35 13.785h-.004a7.466 7.466 0 0 1-3.805-1.042l-.273-.162-2.827.741.755-2.756-.177-.282a7.448 7.448 0 0 1-1.144-3.909c0-4.135 3.364-7.499 7.502-7.499 2.004 0 3.887.782 5.301 2.2 1.415 1.417 2.193 3.303 2.193 5.308 0 4.136-3.365 7.499-7.498 7.499zm4.113-5.618c-.225-.113-1.334-.659-1.541-.734-.206-.075-.356-.113-.506.113-.15.225-.581.734-.712.884-.131.15-.262.169-.487.056-.225-.113-.951-.351-1.811-1.118-.67-.598-1.122-1.337-1.253-1.562-.131-.225-.014-.347.098-.459.102-.101.225-.263.338-.395.112-.131.15-.225.225-.375.075-.15.037-.281-.019-.394-.056-.113-.506-1.219-.694-1.67-.182-.439-.368-.379-.506-.386l-.431-.007c-.15 0-.394.056-.6.281-.206.225-.788.77-.788 1.876 0 1.107.806 2.176.919 2.326.112.15 1.586 2.423 3.843 3.398.537.232.956.371 1.283.475.54.172 1.031.148 1.419.09.432-.065 1.334-.545 1.522-1.07.188-.526.188-.977.131-1.07-.056-.094-.206-.15-.431-.263z" fill="#FFFFFF"/>
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

      <!-- Main Navigation Links -->
      <div style="display:flex; flex-direction:column; gap:0.4rem; margin-bottom:1.25rem;">
        <a href="/" style="font-weight:700; font-size:1rem; color:var(--text); padding:0.6rem 0.75rem; border-radius:var(--radius-sm); background:var(--surface-2); display:flex; align-items:center; justify-content:space-between;">
          <span>Home</span>
          <span style="font-size:0.8rem; color:var(--accent-strong);">&rarr;</span>
        </a>
        <a href="/best-sellers" style="font-weight:700; font-size:1rem; color:var(--text); padding:0.6rem 0.75rem; border-radius:var(--radius-sm); background:var(--surface-2); display:flex; align-items:center; justify-content:space-between;">
          <span>Best Sellers</span>
          <span style="font-size:0.8rem; color:var(--accent-strong);">&rarr;</span>
        </a>
      </div>
      
      <!-- Category Tiles with Generous Touch Target & Spacing -->
      <div style="margin-bottom:1.5rem;">
        <div style="font-size:0.75rem; font-weight:800; text-transform:uppercase; color:var(--text-muted); letter-spacing:0.75px; margin-bottom:0.75rem; padding-left:0.25rem;">
          Shop by Category
        </div>
        <div class="drawer-category-grid" style="display:flex; flex-direction:column; gap:0.65rem;">
          <a href="/categories/mobile-accessories" class="drawer-category-item">
            <span class="drawer-cat-icon"><?= mta_category_icon('mobile-accessories') ?></span>
            <span class="drawer-cat-name">Mobile Accessories</span>
          </a>
          <a href="/categories/computer-accessories" class="drawer-category-item">
            <span class="drawer-cat-icon"><?= mta_category_icon('computer-accessories') ?></span>
            <span class="drawer-cat-name">Computer Accessories</span>
          </a>
          <a href="/categories/audio-and-speakers" class="drawer-category-item">
            <span class="drawer-cat-icon"><?= mta_category_icon('audio-and-speakers') ?></span>
            <span class="drawer-cat-name">Audio &amp; Speakers</span>
          </a>
          <a href="/categories/power-and-charging" class="drawer-category-item">
            <span class="drawer-cat-icon"><?= mta_category_icon('power-and-charging') ?></span>
            <span class="drawer-cat-name">Power &amp; Charging</span>
          </a>
          <a href="/categories/gaming" class="drawer-category-item">
            <span class="drawer-cat-icon"><?= mta_category_icon('gaming') ?></span>
            <span class="drawer-cat-name">Gaming Accessories</span>
          </a>
          <a href="/categories/storage-and-memory" class="drawer-category-item">
            <span class="drawer-cat-icon"><?= mta_category_icon('storage-and-memory') ?></span>
            <span class="drawer-cat-name">Storage &amp; Memory</span>
          </a>
          <a href="/categories/networking" class="drawer-category-item">
            <span class="drawer-cat-icon"><?= mta_category_icon('networking') ?></span>
            <span class="drawer-cat-name">Networking Devices</span>
          </a>
          <a href="/categories/cables-and-adapters" class="drawer-category-item">
            <span class="drawer-cat-icon"><?= mta_category_icon('cables-and-adapters') ?></span>
            <span class="drawer-cat-name">Cables &amp; Adapters</span>
          </a>
          <a href="/categories/smart-and-cctv" class="drawer-category-item">
            <span class="drawer-cat-icon"><?= mta_category_icon('smart-and-cctv') ?></span>
            <span class="drawer-cat-name">Smart &amp; CCTV</span>
          </a>
        </div>
      </div>

      <!-- Secondary Info Links -->
      <div style="border-top:1px solid var(--border); padding-top:1rem; display:flex; flex-direction:column; gap:0.55rem;">
        <a href="/brands" style="color:var(--text); font-weight:600; font-size:0.925rem; padding:0.35rem 0.25rem;">Brands Directory</a>
        <a href="/guides" style="color:var(--text); font-weight:600; font-size:0.925rem; padding:0.35rem 0.25rem;">Tech Buying Guides</a>
        <a href="/track-order" style="color:var(--text); font-weight:600; font-size:0.925rem; padding:0.35rem 0.25rem;">Track Your Order</a>
        <a href="/contact" style="color:var(--text); font-weight:600; font-size:0.925rem; padding:0.35rem 0.25rem;">Contact &amp; Store Location</a>
        <a href="/about" style="color:var(--text); font-weight:600; font-size:0.925rem; padding:0.35rem 0.25rem;">About Store</a>
      </div>
    </div>
  </div>

  <!-- Site Footer -->
  <footer class="site-footer" role="contentinfo">
    <div class="container">
      
      <!-- Store Policy Highlights Strip (Bordered Icons, Zero Emojis) -->
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1.25rem; padding:1.5rem; background:var(--surface-2); border:1px solid var(--border); border-radius:var(--radius-lg); margin-bottom:2.5rem;" class="footer-policies-strip">
        <div style="display:flex; align-items:flex-start; gap:0.75rem;">
          <div class="policy-icon-box" style="width:38px; height:38px; border-radius:8px; border:1.5px solid var(--accent-strong); background:rgba(255, 98, 35, 0.1); color:var(--accent-strong); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
          </div>
          <div>
            <strong style="display:block; font-size:0.9rem; color:var(--text);">3 Days Check Warranty Only</strong>
            <span style="font-size:0.785rem; color:var(--text-muted);">Thoroughly inspect your accessories within 72 hours of receiving.</span>
          </div>
        </div>

        <div style="display:flex; align-items:flex-start; gap:0.75rem;">
          <div class="policy-icon-box" style="width:38px; height:38px; border-radius:8px; border:1.5px solid var(--teal); background:rgba(14, 180, 169, 0.1); color:var(--teal); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
          </div>
          <div>
            <strong style="display:block; font-size:0.9rem; color:var(--text);">No Refund — Exchange Only</strong>
            <span style="font-size:0.785rem; color:var(--text-muted);">Exchanges granted exclusively under eligible technical defect conditions.</span>
          </div>
        </div>

        <div style="display:flex; align-items:flex-start; gap:0.75rem;">
          <div class="policy-icon-box" style="width:38px; height:38px; border-radius:8px; border:1.5px solid var(--accent-strong); background:rgba(255, 98, 35, 0.1); color:var(--accent-strong); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
          </div>
          <div>
            <strong style="display:block; font-size:0.9rem; color:var(--text);">Customer Return Courier</strong>
            <span style="font-size:0.785rem; color:var(--text-muted);">Customer has to return parcel with their own money & courier service.</span>
          </div>
        </div>

        <div style="display:flex; align-items:flex-start; gap:0.75rem;">
          <div class="policy-icon-box" style="width:38px; height:38px; border-radius:8px; border:1.5px solid var(--whatsapp); background:rgba(37, 211, 102, 0.1); color:var(--whatsapp); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
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
            <a href="<?= e(mta_setting('social_facebook', 'https://www.facebook.com/profile.php?id=61590214397707')) ?>" target="_blank" rel="noopener noreferrer me" class="social-icon-btn" aria-label="Facebook">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
            </a>
            <a href="<?= e(mta_setting('social_instagram', 'https://www.instagram.com/multantechaccessories/')) ?>" target="_blank" rel="noopener noreferrer me" class="social-icon-btn" aria-label="Instagram">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
            </a>
            <?php
            $tiktok = trim((string)mta_setting('social_tiktok', ''));
            if ($tiktok !== '' && !str_contains($tiktok, 'TODO')):
            ?>
            <a href="<?= e($tiktok) ?>" target="_blank" rel="noopener noreferrer me" class="social-icon-btn" aria-label="TikTok">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.29 0 .58.04.86.13V9.4a6.33 6.33 0 0 0-.86-.06A6.34 6.34 0 0 0 3 15.68a6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.34-6.34V9.07a8.16 8.16 0 0 0 4.91 1.63V7.28a4.81 4.81 0 0 1-1-.59z"/></svg>
            </a>
            <?php endif; ?>
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
          <div style="margin-top: 1rem; display: inline-flex; align-items: center; gap: 0.5rem; background: var(--surface-2); padding: 0.4rem 0.8rem; border-radius: var(--radius-sm); font-size: 0.8rem; font-weight: 600; border: 1px solid var(--border);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--whatsapp)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
            <span>Cash on Delivery Available</span>
          </div>
        </div>

      </div>

      <!-- Trademark Notice -->
      <div style="font-size:0.75rem; color:var(--text-muted); text-align:center; margin-top:2rem; padding-top:1rem; border-top:1px solid var(--border);">
        Brand names and logos are trademarks of their respective owners and are used to identify products we sell.
      </div>

      <!-- Bottom Credits Strip -->
      <div class="footer-bottom">
        <div>
          &copy; <?= date('Y') ?> Multan Tech Accessories. All rights reserved.
          <span class="footer-credit">&bull; <?= e(mta_setting('founder_credit', 'Founded & Developed by Muneeb Ahmad')) ?></span>
          <?php if (mta_setting('show_admin_link_in_footer', '1') === '1'): ?>
            &bull; <a href="<?= e(mta_setting('admin_portal_url', '/mta-control-9872')) ?>" target="_blank" rel="noopener noreferrer nofollow" aria-label="Admin login (opens in a new tab)" style="color:var(--text-muted); text-decoration:underline;">Admin</a>
          <?php endif; ?>
        </div>
        <div>
          Currency: <strong>PKR (Rs.)</strong> &bull; Serving Multan & All Pakistan
        </div>
      </div>
    </div>
  </footer>

  <script src="/assets/js/main.js?v=<?= @filemtime(MTA_ROOT . '/assets/js/main.js') ?: time() ?>" defer></script>
</body>
</html>
