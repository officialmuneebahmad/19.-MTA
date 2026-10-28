<?php
/**
 * Multan Tech Accessories — Admin Settings View
 */
$adminPath = mta_admin_path();
?>
<div class="admin-card" style="max-width: 900px;">
  <div class="admin-card-header">
    <div class="admin-card-title">Business Identity, Delivery Rules & Integrations</div>
  </div>

  <form action="<?= e($adminPath) ?>/settings" method="POST">
    <?= mta_csrf_field() ?>

    <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; color: #9B3828;">
      1. Business Identity & Founder Details
    </h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
      <div class="form-group">
        <label for="site_name">Brand & Store Name</label>
        <input type="text" id="site_name" name="site_name" value="<?= e($settings['site_name'] ?? 'Multan Tech Accessories') ?>" class="form-control">
      </div>

      <div class="form-group">
        <label>Founder & Developer (Fixed Credit)</label>
        <input type="text" disabled value="Muneeb Ahmad" class="form-control" style="background: #eee;">
      </div>
    </div>

    <div class="form-group">
      <label for="store_address">Physical Store Address</label>
      <input type="text" id="store_address" name="store_address" value="<?= e($settings['store_address'] ?? 'Al-Quresh Phase 3, Sher Shah Road, Multan, Punjab, Pakistan') ?>" class="form-control">
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
      <div class="form-group">
        <label for="store_phone">Primary Calling Phone</label>
        <input type="text" id="store_phone" name="store_phone" value="<?= e($settings['store_phone'] ?? '0307-0655275') ?>" class="form-control">
      </div>

      <div class="form-group">
        <label for="store_whatsapp">WhatsApp Ordering Number (With country code)</label>
        <input type="text" id="store_whatsapp" name="store_whatsapp" value="<?= e($settings['store_whatsapp'] ?? '923070655275') ?>" class="form-control">
      </div>
    </div>

    <div class="form-group">
      <label for="announcement_marquee">Top Header Marquee Announcement</label>
      <input type="text" id="announcement_marquee" name="announcement_marquee" value="<?= e($settings['announcement_marquee'] ?? 'FREE DELIVERY ABOVE 5000 - FREE DELIVERY ON SECOND ORDER - MINIMUM DELIVERY CHARGES ONLY FOR MULTANIS') ?>" class="form-control">
    </div>

    <h3 style="font-size: 1.05rem; font-weight: 700; margin: 2rem 0 1rem; padding-top: 1.25rem; border-top: 1px solid var(--admin-border); color: #9B3828;">
      2. Delivery Rules & Pricing Markup
    </h3>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem;">
      <div class="form-group">
        <label for="delivery_free_threshold">Free Delivery Threshold (PKR)</label>
        <input type="number" id="delivery_free_threshold" name="delivery_free_threshold" value="<?= (int)($settings['delivery_free_threshold'] ?? 5000) ?>" class="form-control">
      </div>

      <div class="form-group">
        <label for="delivery_multan_charge">Multan Local Delivery (PKR)</label>
        <input type="number" id="delivery_multan_charge" name="delivery_multan_charge" value="<?= (int)($settings['delivery_multan_charge'] ?? 100) ?>" class="form-control">
      </div>

      <div class="form-group">
        <label for="delivery_standard_charge">Nationwide Delivery (PKR)</label>
        <input type="number" id="delivery_standard_charge" name="delivery_standard_charge" value="<?= (int)($settings['delivery_standard_charge'] ?? 250) ?>" class="form-control">
      </div>
    </div>

    <div class="form-group" style="max-width: 320px;">
      <label for="pricing_default_markup_percent">Default Supplier Markup (%)</label>
      <input type="number" id="pricing_default_markup_percent" name="pricing_default_markup_percent" value="<?= (int)($settings['pricing_default_markup_percent'] ?? 10) ?>" class="form-control">
      <div class="form-help">Applied to supplier price when no manual override is set.</div>
    </div>

    <h3 style="font-size: 1.05rem; font-weight: 700; margin: 2rem 0 1rem; padding-top: 1.25rem; border-top: 1px solid var(--admin-border); color: #9B3828;">
      3. AdSense & Analytics Integrations
    </h3>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
      <div class="form-group">
        <label for="adsense_client_id">Google AdSense Publisher ID</label>
        <input type="text" id="adsense_client_id" name="adsense_client_id" value="<?= e($settings['adsense_client_id'] ?? '') ?>" placeholder="ca-pub-XXXXXXXXXXXXXXXX" class="form-control">
      </div>

      <div class="form-group">
        <label for="google_analytics_id">Google Analytics 4 Measurement ID</label>
        <input type="text" id="google_analytics_id" name="google_analytics_id" value="<?= e($settings['google_analytics_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX" class="form-control">
      </div>
    </div>

    <div style="margin-top: 2rem; border-top: 1px solid var(--admin-border); padding-top: 1.5rem;">
      <button type="submit" class="btn btn-primary btn-lg">
        Save All Store Settings
      </button>
    </div>
  </form>
</div>
