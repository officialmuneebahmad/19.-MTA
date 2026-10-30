<?php
/**
 * Multan Tech Accessories — Master Admin Settings View (Part B)
 * MultanTech.store
 */
$adminPath = mta_admin_path();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <h2 style="font-family: var(--font-heading); font-size: 1.35rem; font-weight: 700; color: var(--admin-text);">
      Store Settings & Global Parameters
    </h2>
    <p style="font-size: 0.85rem; color: var(--admin-text-muted);">
      Configure pricing engine rules, delivery charges, Pakistani social links, and integrations.
    </p>
  </div>

  <div style="display: flex; gap: 0.75rem;">
    <form action="<?= e($adminPath) ?>/settings/cache-clear" method="POST" style="display: inline;">
      <?= mta_csrf_field() ?>
      <button type="submit" class="btn btn-secondary btn-sm" data-confirm="Flush entire full-page cache?">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
        <span>Clear Page Cache</span>
      </button>
    </form>

    <form action="<?= e($adminPath) ?>/settings/reprice" method="POST" style="display: inline;">
      <?= mta_csrf_field() ?>
      <button type="submit" class="btn btn-primary btn-sm" data-confirm="Re-price all 991 products using the current pricing engine formulas?">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        <span>Re-Price All Products</span>
      </button>
    </form>
  </div>
</div>

<form action="<?= e($adminPath) ?>/settings" method="POST">
  <?= mta_csrf_field() ?>

  <!-- 1. Master Pricing Engine Settings (Part A3) -->
  <div class="admin-panel" style="margin-bottom: 1.75rem; border-left: 4px solid var(--mta-orange);">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">1. Master Pricing Engine (Single Source of Truth)</h4>
        <span class="panel-badge" style="background:var(--mta-orange-light); color:var(--mta-orange);">Part A3 Rule</span>
      </div>
    </div>
    <div style="padding: 1.5rem;">
      <p style="font-size:0.875rem; color:var(--admin-text-muted); margin-bottom:1.25rem;">
        All selling prices on product cards, detail pages, cart, checkout, schema JSON-LD, and WhatsApp messages are computed using these exact formula tiers. All prices automatically end in <strong>99</strong> or <strong>999</strong>.
      </p>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
        <div class="form-group">
          <label class="form-label">Tier Threshold (PKR)</label>
          <input type="number" name="pricing_threshold" value="<?= (int)($settings['pricing_threshold'] ?? 2000) ?>" class="form-control" required>
          <div class="form-help">Split point between Tier 1 and Tier 2 (default 2,000)</div>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
        <!-- Tier 1 -->
        <div style="background:var(--admin-surface-muted); padding:1.25rem; border-radius:var(--radius-md); border:1px solid var(--admin-card-border);">
          <div style="font-weight:700; color:var(--admin-text); margin-bottom:0.75rem; font-size:0.95rem;">
            Tier 1: Supplier Price &lt; Threshold (&lt; Rs. 2,000)
          </div>
          <div class="form-group" style="margin-bottom:0.75rem;">
            <label class="form-label">Add Amount (PKR)</label>
            <input type="number" name="pricing_tier1_add" value="<?= (int)($settings['pricing_tier1_add'] ?? 100) ?>" class="form-control" required>
          </div>
          <div class="form-group" style="margin-bottom:0.75rem;">
            <label class="form-label">Rounding Mode</label>
            <select name="pricing_tier1_mode" class="form-control">
              <option value="nearest" <?= ($settings['pricing_tier1_mode'] ?? 'nearest') === 'nearest' ? 'selected' : '' ?>>Nearest Hundred (Half rounds up)</option>
              <option value="up" <?= ($settings['pricing_tier1_mode'] ?? '') === 'up' ? 'selected' : '' ?>>Round Up to Next Hundred</option>
              <option value="down" <?= ($settings['pricing_tier1_mode'] ?? '') === 'down' ? 'selected' : '' ?>>Round Down</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Price Ending</label>
            <input type="number" name="pricing_tier1_ending" value="<?= (int)($settings['pricing_tier1_ending'] ?? 99) ?>" class="form-control" required>
          </div>
        </div>

        <!-- Tier 2 -->
        <div style="background:var(--admin-surface-muted); padding:1.25rem; border-radius:var(--radius-md); border:1px solid var(--admin-card-border);">
          <div style="font-weight:700; color:var(--admin-text); margin-bottom:0.75rem; font-size:0.95rem;">
            Tier 2: Supplier Price &gt;= Threshold (&gt;= Rs. 2,000)
          </div>
          <div class="form-group" style="margin-bottom:0.75rem;">
            <label class="form-label">Add Amount (PKR)</label>
            <input type="number" name="pricing_tier2_add" value="<?= (int)($settings['pricing_tier2_add'] ?? 300) ?>" class="form-control" required>
          </div>
          <div class="form-group" style="margin-bottom:0.75rem;">
            <label class="form-label">Rounding Mode</label>
            <select name="pricing_tier2_mode" class="form-control">
              <option value="up" <?= ($settings['pricing_tier2_mode'] ?? 'up') === 'up' ? 'selected' : '' ?>>Round Up to Next Hundred</option>
              <option value="nearest" <?= ($settings['pricing_tier2_mode'] ?? '') === 'nearest' ? 'selected' : '' ?>>Nearest Hundred</option>
              <option value="down" <?= ($settings['pricing_tier2_mode'] ?? '') === 'down' ? 'selected' : '' ?>>Round Down</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Price Ending</label>
            <input type="number" name="pricing_tier2_ending" value="<?= (int)($settings['pricing_tier2_ending'] ?? 99) ?>" class="form-control" required>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. Delivery Charges & Thresholds -->
  <div class="admin-panel" style="margin-bottom: 1.75rem;">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">2. Delivery Charges & City Rules</h4>
      </div>
    </div>
    <div style="padding: 1.5rem;">
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem;">
        <div class="form-group">
          <label class="form-label">Free Delivery Threshold (PKR)</label>
          <input type="number" name="delivery_free_threshold" value="<?= (int)($settings['delivery_free_threshold'] ?? 5000) ?>" class="form-control" required>
          <div class="form-help">Orders above this amount qualify for zero delivery fee.</div>
        </div>

        <div class="form-group">
          <label class="form-label">Multan Local Delivery (PKR)</label>
          <input type="number" name="delivery_multan_charge" value="<?= (int)($settings['delivery_multan_charge'] ?? 100) ?>" class="form-control" required>
          <div class="form-help">Special rate for local Multan city residents.</div>
        </div>

        <div class="form-group">
          <label class="form-label">Nationwide Delivery (PKR)</label>
          <input type="number" name="delivery_standard_charge" value="<?= (int)($settings['delivery_standard_charge'] ?? 250) ?>" class="form-control" required>
          <div class="form-help">Standard rate for Lahore, Karachi, Islamabad, etc.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. Business Identity, Contacts & Pakistani Social Profiles -->
  <div class="admin-panel" style="margin-bottom: 1.75rem;">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">3. Business Identity & Pakistani Social Profiles</h4>
      </div>
    </div>
    <div style="padding: 1.5rem;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1rem;">
        <div class="form-group">
          <label class="form-label">Store Brand Name</label>
          <input type="text" name="site_name" value="<?= e($settings['site_name'] ?? 'Multan Tech Accessories') ?>" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Physical Store Address</label>
          <input type="text" name="store_address" value="<?= e($settings['store_address'] ?? 'Al-Quresh Phase 3, Sher Shah Road, Multan, Punjab, Pakistan') ?>" class="form-control">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1rem;">
        <div class="form-group">
          <label class="form-label">Customer Support Phone</label>
          <input type="text" name="store_phone" value="<?= e($settings['store_phone'] ?? '0307-0655275') ?>" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">WhatsApp Ordering Number (With country code)</label>
          <input type="text" name="store_whatsapp" value="<?= e($settings['store_whatsapp'] ?? '923070655275') ?>" class="form-control">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1rem;">
        <div class="form-group">
          <label class="form-label">Facebook Page URL</label>
          <input type="url" name="social_facebook" value="<?= e($settings['social_facebook'] ?? 'https://www.facebook.com/profile.php?id=61590214397707') ?>" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Instagram Profile URL</label>
          <input type="url" name="social_instagram" value="<?= e($settings['social_instagram'] ?? 'https://www.instagram.com/multantechaccessories/') ?>" class="form-control">
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 1rem;">
        <label class="form-label">Top Header Marquee Announcement</label>
        <input type="text" name="announcement_marquee" value="<?= e($settings['announcement_marquee'] ?? 'FREE DELIVERY ABOVE 5000 - FREE DELIVERY ON SECOND ORDER - MINIMUM DELIVERY CHARGES ONLY FOR MULTANIS') ?>" class="form-control">
      </div>

      <div class="form-group">
        <label class="form-label">Show WhatsApp Order Button on Product Cards</label>
        <select name="show_wa_on_product_cards" class="form-control">
          <option value="0" <?= ($settings['show_wa_on_product_cards'] ?? '0') === '0' ? 'selected' : '' ?>>Disabled (Default: [Add to Cart] + [Share] buttons only)</option>
          <option value="1" <?= ($settings['show_wa_on_product_cards'] ?? '') === '1' ? 'selected' : '' ?>>Enabled (Show [Add to Cart] + [WhatsApp] + [Share])</option>
        </select>
        <div class="form-help">When disabled, product cards show [Add to Cart] and neutral [Share] button. Unavailable products show neutral [Ask Price] or [Ask Availability].</div>
      </div>
    </div>
  </div>

  <!-- 4. Advance Payment Rules -->
  <div class="admin-panel" style="margin-bottom: 1.75rem;">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">4. Advance Payment Rules (COD Risk Reduction)</h4>
      </div>
    </div>
    <div style="padding: 1.5rem;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
        <div class="form-group">
          <label class="form-label">Require Advance Payment for Dispatch</label>
          <select name="advance_payment_required" class="form-control">
            <option value="0" <?= ($settings['advance_payment_required'] ?? '0') === '0' ? 'selected' : '' ?>>Optional (Pure COD accepted)</option>
            <option value="1" <?= ($settings['advance_payment_required'] ?? '') === '1' ? 'selected' : '' ?>>Mandatory for High-Value Orders</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Standard Minimum Advance (PKR)</label>
          <input type="number" name="advance_minimum_amount" value="<?= (int)($settings['advance_minimum_amount'] ?? 500) ?>" class="form-control">
        </div>
      </div>
    </div>
  </div>

  <!-- 5. Integrations & SMTP -->
  <div class="admin-panel" style="margin-bottom: 1.75rem;">
    <div class="panel-header">
      <div class="panel-title-wrap">
        <h4 class="panel-title">5. Analytics, Google AdSense & SMTP Configuration</h4>
      </div>
    </div>
    <div style="padding: 1.5rem;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1rem;">
        <div class="form-group">
          <label class="form-label">Google AdSense Publisher ID</label>
          <input type="text" name="adsense_client_id" value="<?= e($settings['adsense_client_id'] ?? '') ?>" placeholder="ca-pub-XXXXXXXXXXXXXXXX" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label">Google Analytics 4 Measurement ID</label>
          <input type="text" name="google_analytics_id" value="<?= e($settings['google_analytics_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX" class="form-control">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
        <div class="form-group">
          <label class="form-label">SMTP Host</label>
          <input type="text" name="smtp_host" value="<?= e($settings['smtp_host'] ?? 'mail.multantech.store') ?>" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">SMTP Port</label>
          <input type="number" name="smtp_port" value="<?= (int)($settings['smtp_port'] ?? 465) ?>" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">SMTP Username</label>
          <input type="text" name="smtp_user" value="<?= e($settings['smtp_user'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">SMTP Encryption</label>
          <select name="smtp_secure" class="form-control">
            <option value="ssl" <?= ($settings['smtp_secure'] ?? 'ssl') === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
            <option value="tls" <?= ($settings['smtp_secure'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div style="margin-bottom: 3rem;">
    <button type="submit" class="btn btn-primary" style="height: 48px; padding: 0 2rem; font-size: 1rem;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
      <span>Save All Store Settings</span>
    </button>
  </div>

</form>
