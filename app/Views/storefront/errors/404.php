<?php
/**
 * Multan Tech Accessories — 404 Not Found View
 */
?>
<div class="container" style="padding: 4.5rem 1.25rem; text-align: center; max-width: 680px;">
  <span class="badge" style="background: var(--surface-2); color: var(--accent-strong); font-size: 1rem; padding: 0.4rem 1rem; margin-bottom: 1rem;">
    404 Error
  </span>

  <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 1rem;">Page Not Found</h1>
  <p style="font-size: 1rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 2rem;">
    The page you are looking for may have been moved, renamed, or is temporarily unavailable. Use the search bar or explore our popular categories below.
  </p>

  <div style="max-width: 450px; margin: 0 auto 2.5rem;">
    <form action="/search" method="GET">
      <div class="search-input-wrap">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <input type="search" name="q" class="search-input" placeholder="Search tech accessories..." autocomplete="off">
      </div>
    </form>
  </div>

  <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
    <a href="/" class="btn btn-primary">Return to Homepage</a>
    <a href="/categories" class="btn btn-secondary">Browse All Categories</a>
  </div>
</div>
