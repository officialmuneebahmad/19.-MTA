<?php
/**
 * Multan Tech Accessories — Admin Guide Editor View
 */
$adminPath = mta_admin_path();
$isEdit = !empty($guide);
$actionUrl = $isEdit ? "{$adminPath}/guides/{$guide['id']}" : "{$adminPath}/guides";
?>
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title"><?= $isEdit ? 'Edit Guide' : 'Write New Buying Guide' ?></div>
    <a href="<?= e($adminPath) ?>/guides" class="btn btn-secondary btn-sm">&larr; Back to Guides</a>
  </div>

  <form action="<?= e($actionUrl) ?>" method="POST">
    <?= mta_csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
      
      <!-- Main Content -->
      <div>
        <div class="form-group">
          <label for="title">Guide Title *</label>
          <input type="text" id="title" name="title" required value="<?= e($guide['title'] ?? '') ?>" class="form-control" placeholder="e.g. Best GaN Fast Chargers in Pakistan (2026 Buying Guide)">
        </div>

        <div class="form-group">
          <label for="excerpt">Brief Summary / Excerpt</label>
          <textarea id="excerpt" name="excerpt" rows="3" class="form-control" placeholder="1-2 sentences summarizing key recommendations..."><?= e($guide['excerpt'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label for="content_html">Full Article Content (HTML) *</label>
          <textarea id="content_html" name="content_html" rows="18" required class="form-control" style="font-family: monospace; font-size: 0.9rem; line-height: 1.5;"><?= e($guide['content_html'] ?? '') ?></textarea>
        </div>

        <h3 style="font-size: 1rem; font-weight: 700; margin: 1.5rem 0 0.75rem; border-top: 1px solid var(--admin-border); padding-top: 1rem;">
          SEO Metadata Override
        </h3>

        <div class="form-group">
          <label for="meta_title">Custom Meta Title (&le; 60 chars)</label>
          <input type="text" id="meta_title" name="meta_title" maxlength="60" value="<?= e($guide['meta_title'] ?? '') ?>" class="form-control" placeholder="Auto-generated if empty">
        </div>

        <div class="form-group">
          <label for="meta_description">Meta Description (140-160 chars)</label>
          <textarea id="meta_description" name="meta_description" rows="2" maxlength="255" class="form-control" placeholder="Auto-generated if empty"><?= e($guide['meta_description'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- Publishing Sidebar -->
      <div>
        <div style="background: #F9F9F7; border: 1px solid var(--admin-border); border-radius: 8px; padding: 1.25rem;">
          <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem;">Publishing Settings</h3>

          <div class="form-group">
            <label for="status">Publication Status</label>
            <select id="status" name="status" class="form-control">
              <option value="draft" <?= ($guide['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
              <option value="published" <?= ($guide['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published (Live)</option>
            </select>
          </div>

          <div class="form-group">
            <label for="author_id">Author Byline</label>
            <select id="author_id" name="author_id" class="form-control">
              <?php foreach ($authors as $a): ?>
                <option value="<?= (int)$a['id'] ?>" <?= ((int)($guide['author_id'] ?? 1) === (int)$a['id']) ? 'selected' : '' ?>>
                  <?= e($a['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
            <?= $isEdit ? 'Update Guide' : 'Publish / Save Guide' ?>
          </button>
        </div>
      </div>

    </div>
  </form>
</div>
