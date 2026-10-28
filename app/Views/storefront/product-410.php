<?php
/**
 * Multan Tech Accessories — 410 Gone Product Page
 * Returned when a product has been permanently discontinued or removed from supplier feed
 */
?>
<div class="container" style="padding: 4rem 1.25rem; text-align: center; max-width: 680px;">
  <span class="badge badge-out-of-stock" style="font-size: 0.9rem; padding: 0.4rem 0.9rem; margin-bottom: 1rem;">
    410 Gone &mdash; Discontinued
  </span>

  <h1 style="font-size: 2rem; margin-bottom: 1rem;">This Product is No Longer Available</h1>
  
  <p style="font-size: 1rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
    The item <strong><?= e($product['title'] ?? '') ?></strong> has been permanently removed from our active catalog and is no longer being supplied. Please browse our active categories below or search for similar accessories.
  </p>

  <!-- Search Box -->
  <div style="max-width: 450px; margin: 0 auto 2.5rem;">
    <form action="/search" method="GET">
      <div class="search-input-wrap">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <input type="search" name="q" class="search-input" placeholder="Search alternatives..." autocomplete="off">
      </div>
    </form>
  </div>

  <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
    <a href="/categories" class="btn btn-primary">Browse All Categories &rarr;</a>
    <a href="/best-sellers" class="btn btn-secondary">View Best Sellers</a>
  </div>
</div>
