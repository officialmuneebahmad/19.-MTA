<?php
/**
 * Multan Tech Accessories — Contact Us View
 * Includes Click-to-Load Google Maps facade and Anti-Spam Protected Form
 */
?>
<div class="container" style="padding: 2rem 1.25rem; max-width: 950px;">
  <?php require MTA_APP . '/Views/partials/breadcrumb.php'; ?>

  <h1 style="font-size: 2.25rem; font-weight: 800; margin: 1rem 0 0.5rem;">Contact Us</h1>
  <p style="font-size: 0.95rem; color: var(--text-muted); margin-bottom: 2rem;">
    Have a question about product compatibility or placing a bulk order? Reach out directly to our Multan store.
  </p>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem;" class="contact-grid">
    
    <!-- Left: Store Contact Information -->
    <div>
      <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem;">Store Details</h2>
        
        <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.95rem; line-height: 1.6;">
          <div>
            <strong>Physical Address:</strong><br>
            <span style="color: var(--text-muted);"><?= e(mta_setting('store_address', 'Al-Quresh Phase 3, Sher Shah Road, Multan, Punjab, Pakistan')) ?></span>
          </div>

          <div>
            <strong>Phone & WhatsApp:</strong><br>
            <a href="tel:+923070655275" style="color: var(--accent-strong); font-weight: 700;">0307-0655275</a>
          </div>

          <div>
            <strong>Store Founder:</strong><br>
            <span>Muneeb Ahmad</span>
          </div>

          <div>
            <strong>Working Hours:</strong><br>
            <span style="color: var(--text-muted);">Monday &mdash; Saturday: 10:00 AM &ndash; 9:00 PM (PKT)<br>Sunday: WhatsApp assistance open</span>
          </div>

          <div>
            <strong>Follow Us:</strong><br>
            <div style="display:flex; gap:0.75rem; margin-top:0.35rem;">
              <a href="<?= e(mta_setting('social_facebook', 'https://www.facebook.com/profile.php?id=61590214397707')) ?>" target="_blank" rel="noopener noreferrer me" style="color:var(--text); font-weight:600; font-size:0.875rem; text-decoration:underline;">Facebook</a>
              &bull;
              <a href="<?= e(mta_setting('social_instagram', 'https://www.instagram.com/multantechaccessories/')) ?>" target="_blank" rel="noopener noreferrer me" style="color:var(--text); font-weight:600; font-size:0.875rem; text-decoration:underline;">Instagram</a>
              <?php
              $tiktok = trim((string)mta_setting('social_tiktok', ''));
              if ($tiktok !== '' && !str_contains($tiktok, 'TODO')):
              ?>
              &bull;
              <a href="<?= e($tiktok) ?>" target="_blank" rel="noopener noreferrer me" style="color:var(--text); font-weight:600; font-size:0.875rem; text-decoration:underline;">TikTok</a>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div style="margin-top: 1.5rem;">
          <a href="<?= e(mta_whatsapp_url('923070655275', 'Hello Multan Tech Accessories, I would like to inquire about an accessory.')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp" style="width: 100%;">
            Chat with Muneeb on WhatsApp &rarr;
          </a>
        </div>
      </div>

      <!-- Click-to-Load Google Maps Facade (Zero third-party render penalty) -->
      <div style="background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; height: 240px; display: flex; align-items: center; justify-content: center; position: relative; text-align: center; padding: 1.5rem;" id="map-facade">
        <div>
          <div style="font-size: 1rem; font-weight: 700; margin-bottom: 0.35rem;">Sher Shah Road, Multan</div>
          <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">Click below to load the interactive Google Map</p>
          <button type="button" class="btn btn-secondary btn-sm" onclick="loadGoogleMap()">
            Load Interactive Map
          </button>
        </div>
      </div>
    </div>

    <!-- Right: Message Form -->
    <div>
      <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.25rem;">Send a Message</h2>

        <form action="/contact/send" method="POST">
          <?= mta_csrf_field() ?>
          
          <!-- Anti-Spam Honeypot -->
          <div style="display:none;" aria-hidden="true">
            <input type="text" name="_website_contact_hp" tabindex="-1" autocomplete="off">
          </div>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="contact_name" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">Your Name *</label>
            <input type="text" id="contact_name" name="name" required placeholder="Ali Raza" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem;">
          </div>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="contact_phone" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">Mobile / WhatsApp *</label>
            <input type="tel" id="contact_phone" name="phone" required placeholder="03001234567" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem;">
          </div>

          <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="contact_email" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">Email Address (Optional)</label>
            <input type="email" id="contact_email" name="email" placeholder="ali@example.com" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem;">
          </div>

          <div class="form-group" style="margin-bottom: 1.5rem;">
            <label for="contact_message" style="display:block; font-weight:600; font-size:0.875rem; margin-bottom:0.35rem;">Your Message *</label>
            <textarea id="contact_message" name="message" rows="4" required placeholder="Inquire about product availability, bulk quotation, or store visit" style="width:100%; padding:0.65rem 0.85rem; border:1px solid var(--border); border-radius:6px; font-size:0.95rem;"></textarea>
          </div>

          <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
            Send Message &rarr;
          </button>
        </form>
      </div>
    </div>

  </div>
</div>

<script>
function loadGoogleMap() {
  var facade = document.getElementById('map-facade');
  facade.innerHTML = '<iframe src="https://maps.google.com/maps?q=Al-Quresh+Phase+3+Sher+Shah+Road+Multan&t=&z=15&ie=UTF8&iwloc=&output=embed" width="100%" height="240" style="border:0;" allowfullscreen="" loading="lazy"></iframe>';
}
</script>
