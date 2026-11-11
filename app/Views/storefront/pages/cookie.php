<?php
/**
 * Multan Tech Accessories — Cookie Policy View
 * Aligned with real Google Consent Mode v2 & localStorage/cookie implementation
 */
?>
<div class="container" style="padding: 2rem 1.25rem; max-width: 880px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <h1 class="page-title" style="font-size: 2.25rem; font-weight: 800; margin: 1rem 0 0.5rem;">Cookie Policy</h1>
  <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 2rem;">
    <strong>Effective Date:</strong> November 11, 2026 &bull; <strong>Last Updated:</strong> November 11, 2026 &bull; <strong>Website:</strong> https://multantech.pk/
  </p>

  <div style="font-size: 1rem; line-height: 1.8; color: var(--text);">

    <p style="margin-bottom: 1.5rem;">
      This Cookie Policy explains how <strong>Multan Tech Accessories</strong> (&ldquo;multantech.pk&rdquo;, &ldquo;we&rdquo;, &ldquo;our&rdquo;) uses cookies, local browser storage, and consent signals when you browse our online catalog, read buying guides, or place an order.
    </p>

    <div style="background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 2rem;">
      <strong style="display: block; margin-bottom: 0.4rem;">Manage Your Consent Preferences</strong>
      <p style="font-size: 0.92rem; color: var(--text-muted); margin: 0 0 0.75rem;">
        Analytics and Advertising storage are <strong>denied by default</strong> until you choose to enable them. Update your choices anytime below:
      </p>
      <button type="button" class="btn btn-primary btn-sm" data-open-cookie-settings>Open Privacy Preference Center</button>
    </div>

    <h2 style="font-size: 1.4rem; margin: 2rem 0 0.75rem;">1. What Are Cookies &amp; Browser Storage?</h2>
    <p style="margin-bottom: 1.25rem;">
      Cookies are small text files stored in your web browser, and <code>localStorage</code> is browser-side key-value storage used to remember settings on your device. They keep your shopping cart active, protect forms against Cross-Site Request Forgery (CSRF), remember light/dark theme preferences, and save your privacy consent choices.
    </p>

    <h2 style="font-size: 1.4rem; margin: 2rem 0 0.75rem;">2. How Google Consent Mode v2 Works on This Site</h2>
    <p style="margin-bottom: 1rem;">
      Before optional tags run, our page header initializes <strong>Google Consent Mode v2</strong> via Google Tag Manager (container <code>GTM-NSR7DDL8</code>) by reading your saved preferences from <code>mta_cookie_consent_v2</code> (or fallback cookie <code>mta_cookie_consent</code>).
    </p>
    <p style="margin-bottom: 1.5rem;">
      If you have not made a choice or selected <em>Reject Optional</em>, the signals <code>analytics_storage</code>, <code>ad_storage</code>, <code>ad_user_data</code>, and <code>ad_personalization</code> remain set to <code>denied</code>. When you click <em>Accept All</em> or enable specific optional categories inside the Preference Center, <code>gtag('consent', 'update', ...)</code> updates only the categories you permitted.
    </p>

    <h2 style="font-size: 1.4rem; margin: 2rem 0 0.75rem;">3. Categories of Storage &amp; Cookies We Use</h2>

    <h3 style="font-size: 1.15rem; font-weight: 700; margin: 1.5rem 0 0.5rem;">3.1 Strictly Necessary Cookies &amp; Storage (Always Active)</h3>
    <p style="margin-bottom: 0.75rem;">
      Required for core shopping cart state, security validation, theme display, and saving your privacy preferences. These do not track you across third-party websites.
    </p>
    <ul style="margin-left: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.45rem;">
      <li><code>mta_session</code> (Cookie): Maintains your active shopping cart, flash alerts, and session state.</li>
      <li><code>csrf_token</code> / session CSRF state: Protects checkout, cart, and contact forms against unauthorized submissions.</li>
      <li><code>mta_cookie_consent_v2</code> (localStorage) &amp; <code>mta_cookie_consent</code> (Cookie): Stores your consent selections for up to 365 days so the banner does not repeatedly interrupt you.</li>
      <li><code>mta_theme</code> (localStorage): Remembers your Day or Night display mode preference.</li>
    </ul>

    <h3 style="font-size: 1.15rem; font-weight: 700; margin: 1.5rem 0 0.5rem;">3.2 Analytics Cookies (Optional &mdash; Consent Required)</h3>
    <p style="margin-bottom: 0.75rem;">
      Enabled only when <code>analytics_storage</code> is granted by your choice. Used via Google Tag Manager / Google Analytics 4 to understand popular pages, device types, and overall traffic.
    </p>
    <ul style="margin-left: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.45rem;">
      <li><code>_ga</code> / <code>_ga_*</code> (Google Analytics 4): Distinguishes aggregated site visits and session counts (typically up to 2 years when consented).</li>
    </ul>

    <h3 style="font-size: 1.15rem; font-weight: 700; margin: 1.5rem 0 0.5rem;">3.3 Advertising Cookies (Optional &amp; Currently Inactive)</h3>
    <p style="margin-bottom: 0.75rem;">
      <strong>Current Status:</strong> Google AdSense is <strong>not currently active</strong> on <strong>multantech.pk</strong>, so advertising cookies are dormant at this time. If and when Google AdSense is enabled on our site and you grant Advertising consent (<code>ad_storage</code>, <code>ad_user_data</code>, <code>ad_personalization</code>), Google and its advertising partners may set cookies or read identifiers such as:
    </p>
    <ul style="margin-left: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.45rem;">
      <li><code>__gads</code> / <code>__gpi</code> / <code>IDE</code> (Google AdSense / DoubleClick): Used when AdSense is active to serve ads, cap frequency, measure performance, and&mdash;where consented&mdash;personalize advertising.</li>
    </ul>

    <h2 style="font-size: 1.4rem; margin: 2rem 0 0.75rem;">4. Managing Cookies &amp; Ad Personalization</h2>
    <ul style="margin-left: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
      <li><strong>On-Site Preference Center:</strong> Click <a href="javascript:void(0)" data-open-cookie-settings style="color: var(--accent-strong); font-weight: 600; text-decoration: underline;">Open Privacy Preference Center</a> at any time to allow or reject Analytics and Advertising categories.</li>
      <li><strong>Google Ads Settings:</strong> Control ad personalization across Google services via <a href="https://adssettings.google.com/" target="_blank" rel="noopener noreferrer" style="color: var(--accent-strong); text-decoration: underline;">https://adssettings.google.com/</a>.</li>
      <li><strong>Google Partner Sites Information:</strong> Review how Google handles information from partner websites at <a href="https://policies.google.com/technologies/partner-sites" target="_blank" rel="noopener noreferrer" style="color: var(--accent-strong); text-decoration: underline;">https://policies.google.com/technologies/partner-sites</a>.</li>
      <li><strong>Browser Controls:</strong> You can delete or block cookies directly inside Chrome, Firefox, Safari, or Edge settings.</li>
    </ul>

    <p style="font-size: 0.92rem; color: var(--text-muted);">
      See also our full <a href="/privacy-policy" style="color: var(--accent-strong); text-decoration: underline;">Privacy Policy</a> or reach out via our <a href="/contact" style="color: var(--accent-strong); text-decoration: underline;">Contact Page</a> if you have questions.
    </p>

  </div>
</div>
