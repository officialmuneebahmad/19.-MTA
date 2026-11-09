<?php
/**
 * Multan Tech Accessories — Cookie Consent Management System
 * Google Consent Mode v2 Compliant (analytics_storage, ad_storage, ad_user_data, ad_personalization)
 */
?>
<!-- MultanTech Cookie Consent Banner & Preferences Modal -->
<div id="mta-cookie-banner" class="mta-cookie-banner" role="region" aria-label="Cookie consent banner" style="display: none;">
  <div class="mta-cookie-banner-inner">
    <div class="mta-cookie-header">
      <div class="mta-cookie-icon-wrap" aria-hidden="true">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"></path>
          <path d="M8.5 8.5v.01"></path>
          <path d="M7 13.5v.01"></path>
          <path d="M11 16.5v.01"></path>
          <path d="M15.5 13.5v.01"></path>
        </svg>
      </div>
      <h3 class="mta-cookie-title">We Value Your Privacy</h3>
    </div>
    
    <p class="mta-cookie-text">
      We use cookies and similar technologies to improve your browsing experience, understand website traffic, and support advertising. You can manage your preferences or reject optional cookies at any time. Read our <a href="/privacy-policy" class="mta-cookie-link">Privacy Policy</a> and <a href="/cookie-policy" class="mta-cookie-link">Cookie Policy</a> to learn more.
    </p>

    <div class="mta-cookie-actions">
      <button type="button" id="mta-cookie-accept-all" class="mta-cookie-btn mta-cookie-btn-primary">
        Accept All
      </button>
      <button type="button" id="mta-cookie-reject-optional" class="mta-cookie-btn mta-cookie-btn-secondary">
        Reject Optional
      </button>
      <button type="button" id="mta-cookie-customize" class="mta-cookie-btn mta-cookie-btn-outline">
        Customize Preferences
      </button>
    </div>
  </div>
</div>

<!-- Preferences Modal Drawer -->
<div id="mta-cookie-modal" class="mta-cookie-modal" role="dialog" aria-modal="true" aria-labelledby="mta-modal-title" style="display: none;">
  <div class="mta-cookie-modal-backdrop" id="mta-modal-backdrop"></div>
  <div class="mta-cookie-modal-content">
    <div class="mta-cookie-modal-header">
      <div>
        <h3 id="mta-modal-title" class="mta-cookie-modal-title">Privacy Preference Center</h3>
        <p class="mta-cookie-modal-subtitle">Manage your consent preferences for cookies and tracking technologies.</p>
      </div>
      <button type="button" class="mta-cookie-modal-close" id="mta-modal-close" aria-label="Close preferences modal">&times;</button>
    </div>

    <div class="mta-cookie-modal-body">
      <!-- Category 1: Necessary -->
      <div class="mta-cookie-category">
        <div class="mta-cookie-category-header">
          <div>
            <div class="mta-cookie-category-title-row">
              <h4 class="mta-cookie-category-title">1. Necessary Cookies</h4>
              <span class="mta-cookie-badge-always">Always Active</span>
            </div>
            <p class="mta-cookie-category-desc">
              Required for core website security, session persistence, CSRF protection, and shopping cart functions. The website cannot function properly without these cookies.
            </p>
          </div>
          <div class="mta-cookie-switch-wrap">
            <input type="checkbox" checked disabled id="mta-toggle-necessary" class="mta-cookie-toggle" aria-label="Necessary Cookies (Always Active)">
          </div>
        </div>
      </div>

      <!-- Category 2: Analytics -->
      <div class="mta-cookie-category">
        <div class="mta-cookie-category-header">
          <div>
            <div class="mta-cookie-category-title-row">
              <h4 class="mta-cookie-category-title">2. Analytics Cookies</h4>
              <span class="mta-cookie-badge-optional">Optional</span>
            </div>
            <p class="mta-cookie-category-desc">
              Allow us to count visits and traffic sources via Google Analytics 4 (GA4) so we can measure and improve the performance of our hardware store. All data is aggregated and processed with privacy protections.
            </p>
          </div>
          <div class="mta-cookie-switch-wrap">
            <label class="mta-switch">
              <input type="checkbox" id="mta-toggle-analytics" class="mta-cookie-toggle" aria-label="Toggle Analytics Cookies">
              <span class="mta-slider"></span>
            </label>
          </div>
        </div>
      </div>

      <!-- Category 3: Advertising -->
      <div class="mta-cookie-category">
        <div class="mta-cookie-category-header">
          <div>
            <div class="mta-cookie-category-title-row">
              <h4 class="mta-cookie-category-title">3. Advertising Cookies</h4>
              <span class="mta-cookie-badge-optional">Optional</span>
            </div>
            <p class="mta-cookie-category-desc">
              Used by Google and advertising partners to measure campaign effectiveness and deliver relevant ads when enabled. (Note: Advertising cookies are strictly dormant and will only activate once Google AdSense integration is live).
            </p>
          </div>
          <div class="mta-cookie-switch-wrap">
            <label class="mta-switch">
              <input type="checkbox" id="mta-toggle-advertising" class="mta-cookie-toggle" aria-label="Toggle Advertising Cookies">
              <span class="mta-slider"></span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <div class="mta-cookie-modal-footer">
      <button type="button" id="mta-modal-reject-all" class="mta-cookie-btn mta-cookie-btn-secondary">
        Reject Optional
      </button>
      <button type="button" id="mta-modal-save-preferences" class="mta-cookie-btn mta-cookie-btn-primary">
        Save My Preferences
      </button>
    </div>
  </div>
</div>

<style>
  /* --------------------------------------------------------------------------
     MTA Cookie Consent System Styles
     Minimalist, Off-White (#FAF9F5), MultanTech Green (#0E7064), Accessible
     -------------------------------------------------------------------------- */
  .mta-cookie-banner {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    max-width: 480px;
    width: calc(100% - 3rem);
    z-index: 99998;
    background: #FAF9F5;
    border: 1px solid #E8E6DC;
    border-radius: 16px;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.12), 0 2px 6px rgba(0, 0, 0, 0.04);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #141413;
    animation: mtaSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }

  [data-theme="dark"] .mta-cookie-banner {
    background: #1C1C1A;
    border-color: #2E2E2A;
    color: #F5F5F0;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.5);
  }

  .mta-cookie-banner-inner {
    padding: 1.5rem;
  }

  .mta-cookie-header {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    margin-bottom: 0.75rem;
  }

  .mta-cookie-icon-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: rgba(14, 112, 100, 0.1);
    color: #0E7064;
    flex-shrink: 0;
  }

  [data-theme="dark"] .mta-cookie-icon-wrap {
    background: rgba(14, 180, 169, 0.15);
    color: #0EB4A9;
  }

  .mta-cookie-title {
    font-size: 1.125rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.3;
    color: inherit;
  }

  .mta-cookie-text {
    font-size: 0.875rem;
    line-height: 1.55;
    color: #4A4A46;
    margin: 0 0 1.25rem 0;
  }

  [data-theme="dark"] .mta-cookie-text {
    color: #B5B5AC;
  }

  .mta-cookie-link {
    color: #0E7064;
    text-decoration: underline;
    font-weight: 600;
  }

  [data-theme="dark"] .mta-cookie-link {
    color: #0EB4A9;
  }

  .mta-cookie-actions {
    display: flex;
    gap: 0.6rem;
    flex-wrap: wrap;
  }

  .mta-cookie-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.65rem 1.1rem;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.18s ease;
    border: 1px solid transparent;
    text-decoration: none;
    line-height: 1.2;
    flex: 1 1 auto;
    text-align: center;
  }

  .mta-cookie-btn-primary {
    background: #0E7064;
    color: #FFFFFF;
    border-color: #0E7064;
  }

  .mta-cookie-btn-primary:hover {
    background: #0C5D53;
    border-color: #0C5D53;
  }

  .mta-cookie-btn-secondary {
    background: #E8E6DC;
    color: #2D2D2A;
    border-color: #D6D3C7;
  }

  .mta-cookie-btn-secondary:hover {
    background: #DCD8CD;
  }

  [data-theme="dark"] .mta-cookie-btn-secondary {
    background: #2E2E2A;
    color: #F0F0E8;
    border-color: #3D3D38;
  }

  [data-theme="dark"] .mta-cookie-btn-secondary:hover {
    background: #3B3B36;
  }

  .mta-cookie-btn-outline {
    background: transparent;
    color: #4A4A46;
    border-color: #D6D3C7;
    width: 100%;
    margin-top: 0.2rem;
  }

  .mta-cookie-btn-outline:hover {
    background: rgba(0, 0, 0, 0.04);
    color: #141413;
  }

  [data-theme="dark"] .mta-cookie-btn-outline {
    color: #B5B5AC;
    border-color: #3D3D38;
  }

  [data-theme="dark"] .mta-cookie-btn-outline:hover {
    background: rgba(255, 255, 255, 0.05);
    color: #F5F5F0;
  }

  /* Modal Preferences Drawer */
  .mta-cookie-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    box-sizing: border-box;
  }

  .mta-cookie-modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(2px);
  }

  .mta-cookie-modal-content {
    position: relative;
    background: #FAF9F5;
    border: 1px solid #E8E6DC;
    border-radius: 16px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 48px rgba(0, 0, 0, 0.2);
    animation: mtaScaleIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    overflow: hidden;
  }

  [data-theme="dark"] .mta-cookie-modal-content {
    background: #1C1C1A;
    border-color: #2E2E2A;
    color: #F5F5F0;
  }

  .mta-cookie-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1.5rem 1.75rem 1rem 1.75rem;
    border-bottom: 1px solid #E8E6DC;
  }

  [data-theme="dark"] .mta-cookie-modal-header {
    border-bottom-color: #2E2E2A;
  }

  .mta-cookie-modal-title {
    font-size: 1.25rem;
    font-weight: 800;
    margin: 0 0 0.25rem 0;
  }

  .mta-cookie-modal-subtitle {
    font-size: 0.85rem;
    color: #666;
    margin: 0;
  }

  [data-theme="dark"] .mta-cookie-modal-subtitle {
    color: #999;
  }

  .mta-cookie-modal-close {
    background: transparent;
    border: none;
    font-size: 1.8rem;
    cursor: pointer;
    color: #888;
    line-height: 1;
    padding: 0 0.25rem;
  }

  .mta-cookie-modal-close:hover {
    color: #141413;
  }

  [data-theme="dark"] .mta-cookie-modal-close:hover {
    color: #FFF;
  }

  .mta-cookie-modal-body {
    padding: 1.5rem 1.75rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
  }

  .mta-cookie-category {
    background: #FFFFFF;
    border: 1px solid #E8E6DC;
    border-radius: 12px;
    padding: 1.25rem;
  }

  [data-theme="dark"] .mta-cookie-category {
    background: #252522;
    border-color: #33332E;
  }

  .mta-cookie-category-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
  }

  .mta-cookie-category-title-row {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
    margin-bottom: 0.4rem;
  }

  .mta-cookie-category-title {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
  }

  .mta-cookie-badge-always {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    background: rgba(14, 112, 100, 0.12);
    color: #0E7064;
    text-transform: uppercase;
  }

  [data-theme="dark"] .mta-cookie-badge-always {
    background: rgba(14, 180, 169, 0.2);
    color: #0EB4A9;
  }

  .mta-cookie-badge-optional {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.06);
    color: #666;
    text-transform: uppercase;
  }

  [data-theme="dark"] .mta-cookie-badge-optional {
    background: rgba(255, 255, 255, 0.1);
    color: #BBB;
  }

  .mta-cookie-category-desc {
    font-size: 0.85rem;
    line-height: 1.5;
    color: #5E5D59;
    margin: 0;
  }

  [data-theme="dark"] .mta-cookie-category-desc {
    color: #A0A096;
  }

  /* Toggle Switch */
  .mta-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
  }

  .mta-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .mta-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #D1D5DB;
    transition: 0.2s;
    border-radius: 24px;
  }

  .mta-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.2s;
    border-radius: 50%;
  }

  .mta-switch input:checked + .mta-slider {
    background-color: #0E7064;
  }

  .mta-switch input:checked + .mta-slider:before {
    transform: translateX(20px);
  }

  .mta-cookie-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1.25rem 1.75rem;
    border-top: 1px solid #E8E6DC;
    background: #F4F3ED;
  }

  [data-theme="dark"] .mta-cookie-modal-footer {
    background: #181816;
    border-top-color: #2E2E2A;
  }

  @keyframes mtaSlideUp {
    from {
      opacity: 0;
      transform: translateY(24px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes mtaScaleIn {
    from {
      opacity: 0;
      transform: scale(0.96);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  /* Mobile responsiveness */
  @media (max-width: 640px) {
    .mta-cookie-banner {
      bottom: 0;
      right: 0;
      left: 0;
      width: 100%;
      max-width: 100%;
      border-radius: 16px 16px 0 0;
      border-bottom: none;
      padding-bottom: env(safe-area-inset-bottom, 0.5rem);
    }
    .mta-cookie-actions {
      flex-direction: column;
    }
    .mta-cookie-btn {
      width: 100%;
    }
    .mta-cookie-modal {
      padding: 0;
      align-items: flex-end;
    }
    .mta-cookie-modal-content {
      max-height: 85vh;
      border-radius: 16px 16px 0 0;
    }
  }
</style>

<script>
/**
 * MultanTech Cookie Consent Engine with Google Consent Mode v2
 */
(function() {
  var STORAGE_KEY = 'mta_cookie_consent_v2';
  var COOKIE_NAME = 'mta_cookie_consent';
  var CONSENT_MAX_AGE_MS = 365 * 24 * 60 * 60 * 1000; // 1 year

  function getSavedConsent() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (raw) {
        var parsed = JSON.parse(raw);
        if (parsed && parsed.timestamp && (Date.now() - parsed.timestamp < CONSENT_MAX_AGE_MS)) {
          return parsed;
        }
      }
    } catch(e) {}
    return null;
  }

  function saveConsent(analytics, advertising) {
    var data = {
      analytics: !!analytics,
      advertising: !!advertising,
      timestamp: Date.now(),
      version: '2026.11'
    };

    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
      // Also set a document cookie for server-side awareness if needed
      var date = new Date();
      date.setTime(date.getTime() + CONSENT_MAX_AGE_MS);
      document.cookie = COOKIE_NAME + '=' + encodeURIComponent(JSON.stringify(data)) + '; expires=' + date.toUTCString() + '; path=/; SameSite=Lax';
    } catch(e) {}

    // Update Google Consent Mode v2
    if (typeof window.gtag === 'function') {
      window.gtag('consent', 'update', {
        'analytics_storage': data.analytics ? 'granted' : 'denied',
        'ad_storage': data.advertising ? 'granted' : 'denied',
        'ad_user_data': data.advertising ? 'granted' : 'denied',
        'ad_personalization': data.advertising ? 'granted' : 'denied'
      });
    }

    // Hide banner & modal
    hideBanner();
    hideModal();
  }

  function showBanner() {
    var banner = document.getElementById('mta-cookie-banner');
    if (banner) banner.style.display = 'block';
  }

  function hideBanner() {
    var banner = document.getElementById('mta-cookie-banner');
    if (banner) banner.style.display = 'none';
  }

  function showModal() {
    var modal = document.getElementById('mta-cookie-modal');
    if (modal) {
      // Sync toggle inputs with current saved state
      var saved = getSavedConsent() || { analytics: false, advertising: false };
      var tAnalytics = document.getElementById('mta-toggle-analytics');
      var tAdvertising = document.getElementById('mta-toggle-advertising');
      if (tAnalytics) tAnalytics.checked = !!saved.analytics;
      if (tAdvertising) tAdvertising.checked = !!saved.advertising;
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
  }

  function hideModal() {
    var modal = document.getElementById('mta-cookie-modal');
    if (modal) {
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }
  }

  // Public API to re-open preferences from footer or policy page
  window.mtaCookieConsent = {
    openPreferences: function() {
      showModal();
    },
    acceptAll: function() {
      saveConsent(true, true);
    },
    rejectOptional: function() {
      saveConsent(false, false);
    },
    getPreferences: function() {
      return getSavedConsent();
    }
  };

  document.addEventListener('DOMContentLoaded', function() {
    var saved = getSavedConsent();

    // If no decision recorded or expired, show banner
    if (!saved) {
      // Brief delay to prevent CLS / interference with initial paint
      setTimeout(showBanner, 500);
    }

    // Button event listeners
    var btnAcceptAll = document.getElementById('mta-cookie-accept-all');
    if (btnAcceptAll) {
      btnAcceptAll.addEventListener('click', function() {
        saveConsent(true, true);
      });
    }

    var btnRejectOptional = document.getElementById('mta-cookie-reject-optional');
    if (btnRejectOptional) {
      btnRejectOptional.addEventListener('click', function() {
        saveConsent(false, false);
      });
    }

    var btnCustomize = document.getElementById('mta-cookie-customize');
    if (btnCustomize) {
      btnCustomize.addEventListener('click', function() {
        showModal();
      });
    }

    var btnModalClose = document.getElementById('mta-modal-close');
    if (btnModalClose) {
      btnModalClose.addEventListener('click', hideModal);
    }

    var modalBackdrop = document.getElementById('mta-modal-backdrop');
    if (modalBackdrop) {
      modalBackdrop.addEventListener('click', hideModal);
    }

    var btnModalReject = document.getElementById('mta-modal-reject-all');
    if (btnModalReject) {
      btnModalReject.addEventListener('click', function() {
        saveConsent(false, false);
      });
    }

    var btnModalSave = document.getElementById('mta-modal-save-preferences');
    if (btnModalSave) {
      btnModalSave.addEventListener('click', function() {
        var a = document.getElementById('mta-toggle-analytics');
        var ad = document.getElementById('mta-toggle-advertising');
        saveConsent(a ? a.checked : false, ad ? ad.checked : false);
      });
    }

    // Handle any elements with data-open-cookie-settings
    document.querySelectorAll('[data-open-cookie-settings]').forEach(function(el) {
      el.addEventListener('click', function(e) {
        e.preventDefault();
        showModal();
      });
    });
  });
})();
</script>
