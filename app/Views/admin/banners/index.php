<?php
/**
 * Multan Tech Accessories — Admin Banners View
 */
$adminPath = mta_admin_path();
?>
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">

  <!-- Banners List -->
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Active Carousel Slides</div>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Badge & Title</th>
          <th>Link</th>
          <th>Order</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($banners)): ?>
          <?php foreach ($banners as $b): ?>
            <tr>
              <td>
                <?php if (!empty($b['badge'])): ?>
                  <span class="badge badge-warning"><?= e($b['badge']) ?></span>
                <?php endif; ?>
                <div style="font-weight: 700; margin-top: 0.25rem;"><?= e($b['title']) ?></div>
                <div style="font-size: 0.8rem; color: #777;"><?= e($b['subtitle'] ?? '') ?></div>
              </td>
              <td><code><?= e($b['link']) ?></code></td>
              <td><?= (int)$b['sort_order'] ?></td>
              <td>
                <span class="badge <?= $b['is_active'] ? 'badge-success' : 'badge-neutral' ?>">
                  <?= $b['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
              </td>
              <td>
                <form action="<?= e($adminPath) ?>/banners/<?= (int)$b['id'] ?>/delete" method="POST" onsubmit="return confirm('Delete this banner slide?');">
                  <?= mta_csrf_field() ?>
                  <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                    Delete
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" style="text-align: center; padding: 2rem; color: #777;">
              No banners found. Default template banners will be shown on homepage.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Create Banner Form -->
  <div class="admin-card">
    <div class="admin-card-header">
      <div class="admin-card-title">Add Carousel Slide</div>
    </div>

    <form action="<?= e($adminPath) ?>/banners" method="POST">
      <?= mta_csrf_field() ?>

      <div class="form-group">
        <label for="badge">Slide Badge (e.g. MULTAN EXCLUSIVE)</label>
        <input type="text" id="badge" name="badge" class="form-control" placeholder="NEW ARRIVAL">
      </div>

      <div class="form-group">
        <label for="title">Headline Title *</label>
        <input type="text" id="title" name="title" required class="form-control" placeholder="GaN Fast Chargers & USB-C Power Gear">
      </div>

      <div class="form-group">
        <label for="subtitle">Subtitle / Promotion Text</label>
        <textarea id="subtitle" name="subtitle" rows="2" class="form-control" placeholder="Authentic 65W and 100W laptop chargers with 7-day checking warranty across Pakistan."></textarea>
      </div>

      <div class="form-group">
        <label for="link">Destination URL</label>
        <input type="text" id="link" name="link" value="/categories/chargers-and-cables" class="form-control">
      </div>

      <div class="form-group">
        <label for="image_url">Background / Side Image URL</label>
        <input type="text" id="image_url" name="image_url" value="/assets/images/banner1.webp" class="form-control">
      </div>

      <div class="form-group">
        <label for="sort_order">Sort Order (0 = first)</label>
        <input type="number" id="sort_order" name="sort_order" value="0" class="form-control">
      </div>

      <div class="form-group">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" name="is_active" value="1" checked>
          <span>Active & Visible</span>
        </label>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%;">
        Save Carousel Slide
      </button>
    </form>
  </div>

</div>
