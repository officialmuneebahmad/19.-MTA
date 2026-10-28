<?php
/**
 * Multan Tech Accessories — Admin Guides List View
 */
$adminPath = mta_admin_path();
?>
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title">Hardware Guides & Articles</div>
    <a href="<?= e($adminPath) ?>/guides/create" class="btn btn-primary btn-sm">+ Write New Guide</a>
  </div>

  <table class="admin-table">
    <thead>
      <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Status</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($guides)): ?>
        <?php foreach ($guides as $g): ?>
          <tr>
            <td style="font-weight: 600;">
              <a href="/guides/<?= e($g['slug']) ?>" target="_blank">
                <?= e($g['title']) ?> ↗
              </a>
              <div style="font-size: 0.75rem; color: #777;"><code>/guides/<?= e($g['slug']) ?></code></div>
            </td>
            <td><?= e($g['author_name'] ?? 'Muneeb Ahmad') ?></td>
            <td>
              <span class="badge <?= $g['status'] === 'published' ? 'badge-success' : 'badge-warning' ?>">
                <?= e($g['status']) ?>
              </span>
            </td>
            <td style="color: #777; font-size: 0.8rem;">
              <?= date('d M Y', strtotime($g['created_at'])) ?>
            </td>
            <td style="display: flex; gap: 0.5rem; align-items: center;">
              <a href="<?= e($adminPath) ?>/guides/<?= (int)$g['id'] ?>/edit" class="btn btn-secondary btn-sm">Edit</a>
              <form action="<?= e($adminPath) ?>/guides/<?= (int)$g['id'] ?>/delete" method="POST" onsubmit="return confirm('Delete this guide?');">
                <?= mta_csrf_field() ?>
                <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" style="text-align: center; padding: 2rem; color: #777;">
            No guides written yet. Click above to write your first buying guide.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
