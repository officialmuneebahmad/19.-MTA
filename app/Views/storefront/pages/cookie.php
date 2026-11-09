<?php
/**
 * Multan Tech Accessories — Cookie Policy View
 * Comprehensive, transparent cookie and privacy disclosure.
 */
?>
<div class="container" style="padding: 2.5rem 1.25rem 4rem; max-width: 900px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <div style="margin: 1.5rem 0 2.5rem;">
    <h1 style="font-size: 2.25rem; font-weight: 800; color: var(--text); line-height: 1.25; margin-bottom: 0.75rem;">
      Cookie Policy
    </h1>
    <p style="font-size: 0.95rem; color: var(--text-muted);">
      Last Updated: November 2026 &bull; Effective for all visitors of <strong>https://multantech.pk</strong>
    </p>
  </div>

  <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 1.75rem 2rem; margin-bottom: 2.5rem; display: flex; flex-direction: column; gap: 1rem; box-shadow: var(--shadow-sm);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--text); margin-bottom: 0.25rem;">
          Your Active Privacy Controls
        </h2>
        <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0;">
          You can review, modify, or withdraw your cookie consent preferences at any time.
        </p>
      </div>
      <button type="button" class="btn btn-primary" data-open-cookie-settings onclick="if(window.mtaCookieConsent){window.mtaCookieConsent.openPreferences();}return false;" style="font-weight: 600; padding: 0.65rem 1.25rem;">
        Open Cookie Settings
      </button>
    </div>
  </div>

  <div class="prose" style="font-size: 1rem; line-height: 1.85; color: var(--text);">
    
    <section style="margin-bottom: 2.5rem;">
      <h2 style="font-size: 1.45rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text); border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">
        1. What Are Cookies?
      </h2>
      <p style="margin-bottom: 1rem;">
        Cookies are small text files placed on your computer, smartphone, or tablet when you visit websites. They are widely used to make websites work properly, improve user navigation, preserve shopping cart contents across page loads, and provide website owners with aggregated traffic telemetry.
      </p>
      <p>
        In addition to standard HTTP cookies, modern web browsers utilize related storage technologies such as <code>localStorage</code>, <code>sessionStorage</code>, and secure tokens. Throughout this policy, references to "cookies" encompass all such web storage technologies.
      </p>
    </section>

    <section style="margin-bottom: 2.5rem;">
      <h2 style="font-size: 1.45rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text); border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">
        2. Why Does MultanTech.pk Use Cookies?
      </h2>
      <p style="margin-bottom: 1rem;">
        <strong>Multan Tech Accessories</strong> uses cookies and browser storage strictly to:
      </p>
      <ul style="padding-left: 1.5rem; margin-bottom: 1rem;">
        <li><strong>Ensure Essential E-Commerce Operations:</strong> Maintain your active shopping session, persist products in your shopping basket, process checkout securely, and defend against Cross-Site Request Forgery (CSRF).</li>
        <li><strong>Preserve Visual Preferences:</strong> Remember your selected Light Mode or Dark Mode theme choice via local browser storage.</li>
        <li><strong>Understand Website Performance:</strong> Measure traffic volume, identify popular hardware categories, and optimize store loading speed through Google Analytics 4 (only when consented).</li>
        <li><strong>Support Advertising (When Active):</strong> Measure advertisement effectiveness and serve relevant tech hardware promotions when Google AdSense or advertising partner networks are enabled.</li>
      </ul>
    </section>

    <section style="margin-bottom: 2.5rem;">
      <h2 style="font-size: 1.45rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text); border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">
        3. First-Party vs. Third-Party Cookies
      </h2>
      <p style="margin-bottom: 1rem;">
        <strong>First-Party Cookies:</strong> Set directly by <code>multantech.pk</code>. These are strictly used for your secure shopping session, CSRF security verification, and remembering your consent status. No third party can read or access these cookies.
      </p>
      <p>
        <strong>Third-Party Cookies:</strong> Set by trusted third-party service providers such as Google (for Google Analytics 4 and Google AdSense). These third parties may process device identifiers, IP addresses, and browsing activity subject to their independent privacy policies and your explicit consent choice on our website.
      </p>
    </section>

    <section style="margin-bottom: 2.5rem;">
      <h2 style="font-size: 1.45rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text); border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">
        4. Cookie Categories on MultanTech.pk
      </h2>
      <p style="margin-bottom: 1.25rem;">
        We group the cookies and storage mechanisms used on our website into three distinct categories:
      </p>

      <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Category A -->
        <div style="background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1.25rem 1.5rem;">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text); margin: 0;">A. Strictly Necessary Cookies</h3>
            <span style="background: var(--primary); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 999px;">Always Active</span>
          </div>
          <p style="font-size: 0.925rem; color: var(--text-muted); margin-bottom: 0.75rem;">
            These cookies and storage items are fundamental to the operation of the e-commerce store. They enable you to move around the website, add tech accessories to your cart, and place Cash on Delivery orders safely. Without them, the shopping service cannot be provided.
          </p>
          <ul style="font-size: 0.875rem; color: var(--text); padding-left: 1.25rem; margin: 0;">
            <li><strong>mta_session:</strong> Secure PHP session identifier used to maintain active shopping cart items and user state (Duration: Session / 2 hours).</li>
            <li><strong>csrf_token:</strong> Cryptographic token preventing unauthorized cross-site requests and protecting checkout forms (Duration: Session).</li>
            <li><strong>mta_cookie_consent_v1:</strong> Stores your chosen privacy preferences so you are not prompted repeatedly (Duration: 365 days).</li>
            <li><strong>mta_theme:</strong> Local storage setting storing your preference between light and dark theme (Duration: Persistent).</li>
          </ul>
        </div>

        <!-- Category B -->
        <div style="background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1.25rem 1.5rem;">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text); margin: 0;">B. Analytics Cookies</h3>
            <span style="background: var(--surface); color: var(--text); border: 1px solid var(--border); font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 999px;">Optional</span>
          </div>
          <p style="font-size: 0.925rem; color: var(--text-muted); margin-bottom: 0.75rem;">
            Analytics cookies allow us to collect aggregated, anonymous statistics about how visitors interact with our product pages, categories, and buying guides. This data helps us discover broken links, speed up slow product listings, and understand which computer accessories are most popular in Pakistan.
          </p>
          <ul style="font-size: 0.875rem; color: var(--text); padding-left: 1.25rem; margin: 0;">
            <li><strong>_ga, _ga_*:</strong> Google Analytics 4 cookies utilized to distinguish unique visitors and measure page sessions without recording personally identifiable information (Duration: Up to 2 years, subject to consent).</li>
          </ul>
        </div>

        <!-- Category C -->
        <div style="background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1.25rem 1.5rem;">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text); margin: 0;">C. Advertising Cookies</h3>
            <span style="background: var(--surface); color: var(--text); border: 1px solid var(--border); font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 999px;">Optional</span>
          </div>
          <p style="font-size: 0.925rem; color: var(--text-muted); margin-bottom: 0.75rem;">
            These cookies are deployed by Google AdSense and authorized advertising networks when advertising integrations are active. They help measure ad performance, prevent repetitive ad impressions, and display advertisements relevant to tech enthusiasts.
          </p>
          <ul style="font-size: 0.875rem; color: var(--text); padding-left: 1.25rem; margin: 0;">
            <li><strong>__gads, __gpi, IDE:</strong> Third-party Google advertising cookies used to measure ad impressions and personalize ads where consented (Duration: Up to 13 months).</li>
            <li><em>Note:</em> In strict compliance with transparency standards, advertising storage remains dormant unless you grant explicit Advertising consent and Google AdSense is fully activated on our domain.</li>
          </ul>
        </div>
      </div>
    </section>

    <section style="margin-bottom: 2.5rem;">
      <h2 style="font-size: 1.45rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text); border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">
        5. Google Consent Mode v2 Integration
      </h2>
      <p style="margin-bottom: 1rem;">
        MultanTech.pk has integrated <strong>Google Consent Mode v2</strong>. When you visit our website for the first time, all optional consent parameters (<code>analytics_storage</code>, <code>ad_storage</code>, <code>ad_user_data</code>, and <code>ad_personalization</code>) are configured to <strong>denied</strong> by default before any Google tags are triggered.
      </p>
      <p>
        Only when you select "Accept All" or selectively enable categories in our Privacy Preference Center are these signals updated to <strong>granted</strong>. If you click "Reject Optional", all optional storage parameters remain strictly denied.
      </p>
    </section>

    <section style="margin-bottom: 2.5rem;">
      <h2 style="font-size: 1.45rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text); border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">
        6. How You Can Manage or Change Your Cookie Consent
      </h2>
      <p style="margin-bottom: 1rem;">
        You maintain complete control over how cookies are used during your visits to MultanTech.pk:
      </p>
      <ol style="padding-left: 1.5rem; margin-bottom: 1.25rem;">
        <li style="margin-bottom: 0.75rem;">
          <strong>On-Site Privacy Preference Center:</strong> Click the <a href="#" data-open-cookie-settings onclick="if(window.mtaCookieConsent){window.mtaCookieConsent.openPreferences();}return false;" style="color:var(--primary);font-weight:600;text-decoration:underline;">Cookie Settings</a> link located in our website footer or the button at the top of this page to adjust your preferences at any time.
        </li>
        <li style="margin-bottom: 0.75rem;">
          <strong>Google Personalized Ads Opt-Out:</strong> You can manage or disable personalized advertising across Google services by visiting <a href="https://adssettings.google.com" target="_blank" rel="noopener noreferrer" style="color:var(--primary);text-decoration:underline;">Google Ads Settings</a>.
        </li>
        <li style="margin-bottom: 0.75rem;">
          <strong>Industry Opt-Out Tools:</strong> You can opt out of collection and use of information for ad targeting from participating companies via <a href="https://www.aboutads.info/choices/" target="_blank" rel="noopener noreferrer" style="color:var(--primary);text-decoration:underline;">AboutAds Choices</a> or <a href="https://www.youronlinechoices.com/" target="_blank" rel="noopener noreferrer" style="color:var(--primary);text-decoration:underline;">Your Online Choices</a>.
        </li>
        <li>
          <strong>Browser Controls:</strong> Most modern web browsers allow you to manage cookie settings directly in their options:
          <ul style="padding-left: 1.25rem; margin-top: 0.5rem;">
            <li><strong>Google Chrome:</strong> Settings &gt; Privacy and Security &gt; Third-party cookies.</li>
            <li><strong>Mozilla Firefox:</strong> Settings &gt; Privacy &amp; Security &gt; Enhanced Tracking Protection.</li>
            <li><strong>Apple Safari:</strong> Preferences &gt; Privacy &gt; Manage Website Data.</li>
            <li><strong>Microsoft Edge:</strong> Settings &gt; Cookies and site permissions.</li>
          </ul>
        </li>
      </ol>
      <p style="font-size: 0.9rem; color: var(--text-muted);">
        Please note that disabling strictly necessary cookies in your browser settings will prevent you from adding items to your shopping cart or completing checkout on MultanTech.pk.
      </p>
    </section>

    <section style="margin-bottom: 2rem;">
      <h2 style="font-size: 1.45rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text); border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">
        7. Contact Us Regarding Cookies & Privacy
      </h2>
      <p style="margin-bottom: 1rem;">
        If you have questions about our use of cookies or privacy practices, please contact our team:
      </p>
      <div style="background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 1.25rem 1.5rem;">
        <p style="margin: 0 0 0.5rem;"><strong>Business Name:</strong> Multan Tech Accessories</p>
        <p style="margin: 0 0 0.5rem;"><strong>Address:</strong> <?= e(mta_setting('store_address', 'Al-Quresh Phase 3, Sher Shah Road, Multan, Punjab, Pakistan')) ?></p>
        <p style="margin: 0 0 0.5rem;"><strong>Phone / WhatsApp:</strong> <a href="https://wa.me/923070655275" target="_blank" rel="noopener noreferrer" style="color:var(--primary);font-weight:600;">0307-0655275</a></p>
        <p style="margin: 0;"><strong>Founder / Developer:</strong> <?= e(mta_setting('founder_credit', 'Muneeb Ahmad')) ?></p>
      </div>
    </section>

  </div>
</div>
