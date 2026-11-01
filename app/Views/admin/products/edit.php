<?php
/**
 * Multan Tech Accessories — Admin Product Edit View
 * Professional day/night UI, decent spacing, mobile-responsive grid, and Cloudinary gallery.
 */
$adminPath = mta_admin_path();
$existingPrimary = $product['primary_image_url'] ?? '';
$initialImages = [];
if (!empty($productImages)) {
    foreach ($productImages as $pImg) {
        if (!empty($pImg['url'])) {
            $initialImages[] = [
                'url' => $pImg['url'],
                'is_primary' => (!empty($pImg['is_primary']) || $pImg['url'] === $existingPrimary),
            ];
        }
    }
}
if (empty($initialImages) && !empty($existingPrimary)) {
    $initialImages[] = [
        'url' => $existingPrimary,
        'is_primary' => true,
    ];
}
?>
<style>
  .admin-edit-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  @media (min-width: 992px) {
    .admin-edit-grid {
      grid-template-columns: 2fr 1fr;
      gap: 2rem;
    }
  }
  .admin-panel-card {
    background: var(--admin-card-bg);
    border: 1px solid var(--admin-card-border);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    color: var(--admin-text);
  }
  .form-group {
    margin-bottom: 1.35rem;
  }
  .form-group label {
    display: block;
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.015em;
    line-height: 1.5;
    margin-bottom: 0.45rem;
    color: var(--admin-text);
  }
  .form-control {
    width: 100%;
    min-height: 44px;
    padding: 0.65rem 0.9rem;
    border-radius: 8px;
    font-size: 0.95rem;
    line-height: 1.5;
    letter-spacing: 0.01em;
    border: 1px solid var(--admin-card-border);
    background: var(--admin-card-bg);
    color: var(--admin-text);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .form-control:focus {
    outline: none;
    border-color: var(--mta-orange-btn, #FF6B2C);
    box-shadow: 0 0 0 3px rgba(255, 107, 44, 0.2);
  }
  .form-control[disabled] {
    background: var(--admin-card-header-bg, rgba(255, 255, 255, 0.05));
    opacity: 0.75;
    cursor: not-allowed;
    color: var(--admin-text-muted);
  }
  .form-help {
    font-size: 0.775rem;
    color: var(--admin-text-muted);
    margin-top: 0.35rem;
    line-height: 1.45;
    letter-spacing: 0.01em;
  }
  .cloudinary-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 1rem;
    margin-top: 1.25rem;
  }
  .cloudinary-thumb-card {
    position: relative;
    border: 2px solid var(--admin-card-border);
    border-radius: 12px;
    padding: 8px;
    background: var(--admin-card-header-bg, rgba(255, 255, 255, 0.03));
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .cloudinary-thumb-card.is-primary {
    border-color: #0EA5E9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.25);
  }
  .cloudinary-thumb-img {
    width: 100%;
    aspect-ratio: 1;
    object-fit: contain;
    background: #FFFFFF;
    border-radius: 8px;
    padding: 4px;
  }
  .thumb-primary-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #0284C7;
    color: #FFFFFF;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
  }
  .thumb-actions {
    display: flex;
    gap: 0.35rem;
    margin-top: 0.6rem;
    width: 100%;
    justify-content: space-between;
  }
  .btn-thumb-action {
    flex: 1;
    font-size: 0.72rem;
    padding: 0.35rem 0.4rem;
    border-radius: 6px;
    border: 1px solid var(--admin-card-border);
    background: var(--admin-card-bg);
    color: var(--admin-text);
    cursor: pointer;
    font-weight: 600;
    text-align: center;
    transition: all 0.15s ease;
  }
  .btn-thumb-action:hover {
    background: var(--admin-hover-bg, rgba(255, 255, 255, 0.1));
  }
  .btn-thumb-delete {
    color: #EF4444;
    border-color: rgba(239, 68, 68, 0.3);
  }
  .btn-thumb-delete:hover {
    background: rgba(239, 68, 68, 0.15);
  }
</style>

<div class="admin-card">
  <div class="admin-card-header" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
    <div>
      <div class="admin-card-title" style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.01em;">
        Edit Product #<?= (int)$product['id'] ?>
      </div>
      <div style="font-size: 0.85rem; color: var(--admin-text-muted); margin-top: 0.2rem;">
        SKU: <strong style="color: var(--admin-text);"><?= e($product['sku']) ?></strong> &bull; Status: <span style="text-transform: capitalize; color: var(--status-green-text);"><?= str_replace('_', ' ', e($product['stock_status'])) ?></span>
      </div>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
      <a href="/products/<?= e($product['slug']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
        <span>View Live Page</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
      </a>
      <button type="button" onclick="window.location.reload();" class="btn btn-secondary btn-sm" title="Refresh Product Details">
        🔄 Refresh
      </button>
      <a href="<?= e($adminPath) ?>/products" class="btn btn-secondary btn-sm">
        &larr; Back to Catalog
      </a>
    </div>
  </div>

  <form action="<?= e($adminPath) ?>/products/<?= (int)$product['id'] ?>" method="POST" id="edit-product-form">
    <?= mta_csrf_field() ?>

    <input type="hidden" name="primary_image_url" id="primary-image-input" value="<?= e($existingPrimary) ?>">
    <div id="hidden-gallery-inputs"></div>

    <div class="admin-edit-grid">
      
      <!-- Left Column: Core Fields & Cloudinary Media -->
      <div>
        
        <div class="form-group">
          <label for="title">Product Title *</label>
          <input type="text" id="title" name="title" required value="<?= e($product['title']) ?>" class="form-control" style="font-weight: 600;">
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
          <div class="form-group">
            <label for="category_id">Site Category</label>
            <select id="category_id" name="category_id" class="form-control">
              <option value="">-- None --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= (int)$product['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                  <?= e($cat['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="brand_id">Brand</label>
            <select id="brand_id" name="brand_id" class="form-control">
              <option value="">-- Generic / None --</option>
              <?php foreach ($brands as $brand): ?>
                <option value="<?= (int)$brand['id'] ?>" <?= (int)$product['brand_id'] === (int)$brand['id'] ? 'selected' : '' ?>>
                  <?= e($brand['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Cloudinary Product Images Section (Requested Feature) -->
        <div class="admin-panel-card" style="margin-top: 0.5rem; margin-bottom: 1.5rem; border: 2px solid var(--admin-card-border);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
            <div>
              <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0; color: var(--admin-text);">Cloudinary Product Images</h3>
              <div style="font-size: 0.775rem; color: var(--admin-text-muted); margin-top: 0.2rem;">Upload to Cloudinary, then paste the URL below to attach to this product.</div>
            </div>
            <span style="font-size: 0.75rem; background: var(--status-sky-bg, rgba(14, 165, 233, 0.15)); color: var(--status-sky-text, #0EA5E9); padding: 0.25rem 0.6rem; border-radius: 999px; font-weight: 700;">
              Cloudinary Media
            </span>
          </div>

          <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <input type="url" id="cloudinary-url-input" placeholder="https://res.cloudinary.com/... or image URL" class="form-control" style="flex: 1; min-width: 240px;">
            <button type="button" id="cloudinary-add-btn" class="btn" style="background: #0EA5E9; color: #FFFFFF; font-weight: 700; padding: 0.65rem 1.35rem; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; white-space: nowrap;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              <span>+ Add Image</span>
            </button>
          </div>

          <div id="cloudinary-gallery" class="cloudinary-gallery-grid">
            <!-- Dynamically populated by JS -->
          </div>
        </div>

        <div class="form-group">
          <label for="description_html">Description (Sanitized HTML)</label>
          <textarea id="description_html" name="description_html" rows="9" class="form-control" style="font-family: Consolas, monospace; font-size: 0.85rem; line-height: 1.6;"><?= e($product['description_html']) ?></textarea>
          <div class="form-help">HTML formatting is sanitized automatically to prevent XSS attacks while keeping headings, lists, and tables intact.</div>
        </div>

        <!-- SEO Override Section -->
        <div class="admin-panel-card" style="margin-top: 1.5rem;">
          <h3 style="font-size: 0.95rem; font-weight: 800; margin-bottom: 1rem; color: var(--admin-text); letter-spacing: -0.01em;">
            SEO Metadata Override
          </h3>

          <div class="form-group">
            <label for="meta_title">Custom Meta Title (&le; 60 chars)</label>
            <input type="text" id="meta_title" name="meta_title" maxlength="60" value="<?= e($product['meta_title'] ?? '') ?>" class="form-control" placeholder="Leave empty for auto-generated title">
            <div class="form-help">Appears in Google search results and browser tab. Recommended &le; 60 characters.</div>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="meta_description">Custom Meta Description (140-160 chars)</label>
            <textarea id="meta_description" name="meta_description" rows="3" maxlength="255" class="form-control" placeholder="Leave empty for auto-generated description"><?= e($product['meta_description'] ?? '') ?></textarea>
            <div class="form-help">Summary snippet for Google and social previews. Optimal length 140-160 characters.</div>
          </div>
        </div>

      </div>

      <!-- Right Column: Pricing & Controls (Theme Aware for Day/Night) -->
      <div>
        
        <!-- Pricing Card -->
        <div class="admin-panel-card">
          <h3 style="font-size: 0.95rem; font-weight: 800; margin-bottom: 1.15rem; color: var(--admin-text); letter-spacing: -0.01em;">
            Pricing Controls
          </h3>
          
          <div class="form-group">
            <label>Supplier Cost Price</label>
            <input type="text" disabled value="<?= mta_money($product['supplier_price']) ?>" class="form-control">
            <div class="form-help">Read-only cost from Computer Zone feed. Never shown to visitors.</div>
          </div>

          <div class="form-group">
            <label for="sell_price">Calculated Selling Price (PKR)</label>
            <input type="number" id="sell_price" name="sell_price" step="1" value="<?= (int)$product['sell_price'] ?>" class="form-control" style="font-weight: 700;">
            <div class="form-help">Automatically recalculated by formula (<3000: +100+99, &ge;3000: +300+99).</div>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="price_override" style="color: var(--status-rose-text, #F43F5E);">
              Manual Price Override (PKR)
            </label>
            <input type="number" id="price_override" name="price_override" step="1" value="<?= $product['price_override'] ? (int)$product['price_override'] : '' ?>" placeholder="e.g. 4999" class="form-control" style="border-color: rgba(244, 63, 94, 0.4);">
            <div class="form-help">Sync will preserve this exact price until cleared. Auto-snaps to next 99.</div>
          </div>
        </div>

        <!-- Status & Publishing Card -->
        <div class="admin-panel-card">
          <h3 style="font-size: 0.95rem; font-weight: 800; margin-bottom: 1.15rem; color: var(--admin-text); letter-spacing: -0.01em;">
            Status & Publishing
          </h3>

          <div class="form-group">
            <label for="stock_status">Stock Availability</label>
            <select id="stock_status" name="stock_status" class="form-control">
              <option value="in_stock" <?= $product['stock_status'] === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
              <option value="out_of_stock" <?= $product['stock_status'] === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
              <option value="coming_soon" <?= $product['stock_status'] === 'coming_soon' ? 'selected' : '' ?>>Coming Soon</option>
            </select>
          </div>

          <div class="form-group">
            <label for="content_state">SEO Content Gate</label>
            <select id="content_state" name="content_state" class="form-control">
              <option value="approved" <?= $product['content_state'] === 'approved' ? 'selected' : '' ?>>Approved (Indexable & in Sitemap)</option>
              <option value="ai_draft" <?= $product['content_state'] === 'ai_draft' ? 'selected' : '' ?>>AI Draft (Needs Review)</option>
              <option value="supplier_raw" <?= $product['content_state'] === 'supplier_raw' ? 'selected' : '' ?>>Supplier Raw (Noindex)</option>
            </select>
          </div>

          <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; user-select: none;">
              <input type="checkbox" name="is_active" value="1" <?= !empty($product['is_active']) ? 'checked' : '' ?> style="accent-color: #0EA5E9; transform: scale(1.2);">
              <span>Active in Store (unchecked = 410 Gone)</span>
            </label>
          </div>

          <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.65rem; cursor: pointer; user-select: none;">
              <input type="checkbox" name="pinned" value="1" <?= !empty($product['pinned']) ? 'checked' : '' ?> style="accent-color: #0EA5E9; transform: scale(1.2);">
              <span>Pin to Homepage / Category Top</span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1.25rem; min-height: 48px; font-size: 1rem; font-weight: 700; letter-spacing: 0.02em;">
            Save All Changes
          </button>
        </div>

      </div>

    </div>
  </form>
</div>

<script>
(function() {
  var images = <?= json_encode($initialImages, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?> || [];
  var galleryContainer = document.getElementById('cloudinary-gallery');
  var primaryInput = document.getElementById('primary-image-input');
  var hiddenInputsContainer = document.getElementById('hidden-gallery-inputs');
  var urlInput = document.getElementById('cloudinary-url-input');
  var addBtn = document.getElementById('cloudinary-add-btn');

  function renderGallery() {
    galleryContainer.innerHTML = '';
    hiddenInputsContainer.innerHTML = '';

    if (images.length === 0) {
      galleryContainer.innerHTML = '<div style="grid-column: 1 / -1; padding: 1.5rem; text-align: center; color: var(--admin-text-muted); font-size: 0.85rem; border: 1px dashed var(--admin-card-border); border-radius: 8px;">No images attached. Paste a Cloudinary URL above and click "+ Add Image".</div>';
      primaryInput.value = '';
      return;
    }

    // Ensure at least one image is primary
    var hasPrimary = images.some(function(item) { return item.is_primary; });
    if (!hasPrimary && images.length > 0) {
      images[0].is_primary = true;
    }

    images.forEach(function(item, idx) {
      var isPrimary = item.is_primary;
      if (isPrimary) {
        primaryInput.value = item.url;
      }

      // Add hidden input for form submission
      var hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = 'gallery_images[]';
      hidden.value = item.url;
      hiddenInputsContainer.appendChild(hidden);

      // Card element
      var card = document.createElement('div');
      card.className = 'cloudinary-thumb-card' + (isPrimary ? ' is-primary' : '');

      var badgeHtml = isPrimary ? '<div class="thumb-primary-badge">Primary</div>' : '';

      card.innerHTML = badgeHtml +
        '<img src="' + item.url + '" class="cloudinary-thumb-img" alt="Thumbnail" referrerpolicy="no-referrer" onerror="this.onerror=null;this.src=\'/assets/images/placeholder.svg\';">' +
        '<div class="thumb-actions">' +
          (isPrimary
            ? '<button type="button" class="btn-thumb-action" style="background:#0EA5E9; color:#fff; border-color:#0EA5E9;" disabled>Active</button>'
            : '<button type="button" class="btn-thumb-action btn-set-primary" data-idx="' + idx + '" title="Set as primary product photo">Set Primary</button>'
          ) +
          '<button type="button" class="btn-thumb-action btn-thumb-delete" data-idx="' + idx + '" title="Remove image">&times;</button>' +
        '</div>';

      galleryContainer.appendChild(card);
    });

    // Attach click events
    galleryContainer.querySelectorAll('.btn-set-primary').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        var index = parseInt(this.getAttribute('data-idx'), 10);
        images.forEach(function(item, i) {
          item.is_primary = (i === index);
        });
        renderGallery();
      });
    });

    galleryContainer.querySelectorAll('.btn-thumb-delete').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        var index = parseInt(this.getAttribute('data-idx'), 10);
        images.splice(index, 1);
        renderGallery();
      });
    });
  }

  function addImageUrl() {
    var url = (urlInput.value || '').trim();
    if (!url) {
      urlInput.focus();
      return;
    }
    if (!/^https?:\/\//i.test(url)) {
      alert('Please enter a valid HTTP/HTTPS image URL (e.g. https://res.cloudinary.com/...)');
      urlInput.focus();
      return;
    }

    // Check duplicate
    var exists = images.some(function(item) { return item.url === url; });
    if (exists) {
      alert('This image URL is already added to the gallery.');
      urlInput.select();
      return;
    }

    images.push({
      url: url,
      is_primary: (images.length === 0),
    });

    urlInput.value = '';
    renderGallery();
  }

  addBtn.addEventListener('click', addImageUrl);
  urlInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      addImageUrl();
    }
  });

  // Initial render
  renderGallery();
})();
</script>
