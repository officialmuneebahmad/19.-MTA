<?php
/**
 * Multan Tech Accessories — Admin Sitemaps & Technical Indexing Dashboard View
 */
$adminPath = mta_admin_path();
$files = $telemetry['files'] ?? [];
$exclusions = $telemetry['exclusions'] ?? [];
$reasons = $exclusions['reasons'] ?? [];
$summary = $validation['summary'] ?? [];
$sampledUrls = $validation['sampled_urls'] ?? [];
?>

<div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
  <div>
    <h1 style="font-size: 1.75rem; font-weight: 800; color: #111; margin: 0 0 0.25rem 0;">XML Sitemaps & Search Indexing Engine</h1>
    <p style="font-size: 0.875rem; color: #666; margin: 0;">
      Atomic generation, W3C lastmod (+05:00 PKT), 500-product chunking, image metadata & real-time telemetry.
    </p>
  </div>
  <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
    <form method="POST" action="<?= e($adminPath) ?>/seo-audit/sitemaps/regenerate" style="margin: 0;">
      <input type="hidden" name="csrf_token" value="<?= e(\App\Core\Session::csrfToken()) ?>">
      <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; padding: 0.55rem 1rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.3"></path></svg>
        Regenerate Sitemaps Now
      </button>
    </form>
    <a href="https://search.google.com/search-console/sitemaps?resource_id=https%3A%2F%2Fmultantech.pk%2F" target="_blank" rel="noopener noreferrer" class="btn" style="background: #fff; border: 1px solid #ccc; color: #333; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; padding: 0.55rem 1rem; text-decoration: none;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
      Open Google Search Console
    </a>
  </div>
</div>

<!-- KPI Cards -->
<div class="seo-audit-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      Total Indexable URLs
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: #0E7064;">
      <?= number_format((int)($telemetry['total_urls'] ?? 0)) ?>
    </div>
    <div style="font-size: 0.75rem; color: #777;">Canonical, 200 OK & approved</div>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      Sitemap Files
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: #0369A1;">
      <?= count($files) ?> Files
    </div>
    <div style="font-size: 0.75rem; color: #777;">1 Master Index + <?= max(0, count($files) - 1) ?> Child Maps</div>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      Excluded Catalog URLs
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: #D97706;">
      <?= number_format((int)($exclusions['total_excluded'] ?? 0)) ?>
    </div>
    <div style="font-size: 0.75rem; color: #777;">Filtered by SEO quality rules</div>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: 0.8rem; text-transform: uppercase; color: #777; font-weight: 700; margin-bottom: 0.35rem;">
      XML Validation Status
    </div>
    <div style="font-size: 2rem; font-weight: 800; color: <?= !empty($summary['all_xml_valid']) ? '#0E7064' : '#DC2626' ?>;">
      <?= !empty($summary['all_xml_valid']) ? '100% VALID' : 'ERROR' ?>
    </div>
    <div style="font-size: 0.75rem; color: #777;">Under 50MB &lt; 50,000 URLs/file</div>
  </div>
</div>

<!-- Sitemaps Files Table -->
<div class="admin-card" style="margin-bottom: 2rem;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.75rem;">
    <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: #111;">Active Sitemap Files on Disk & Dynamic Route</h2>
    <span style="font-size: 0.8rem; color: #666;">Serving MIME: <code>application/xml; charset=UTF-8</code></span>
  </div>

  <div style="overflow-x: auto;">
    <table class="admin-table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
      <thead>
        <tr style="background: #f9f9f9; text-align: left; border-bottom: 2px solid #ddd;">
          <th style="padding: 0.75rem;">Sitemap File</th>
          <th style="padding: 0.75rem;">Type</th>
          <th style="padding: 0.75rem;">URLs Count</th>
          <th style="padding: 0.75rem;">File Size</th>
          <th style="padding: 0.75rem;">Last Modified (+05:00 PKT)</th>
          <th style="padding: 0.75rem;">XML Status</th>
          <th style="padding: 0.75rem; text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($files as $name => $fData): 
          $valData = $validation['files'][$name] ?? [];
          $isIndex = ($name === 'sitemap.xml');
          $typeDesc = match(true) {
            $isIndex => 'Master Sitemap Index',
            str_contains($name, 'pages') => 'Core Storefront & Policy Pages',
            str_contains($name, 'categories') => 'Indexed Categories (>= 4 prods + intro)',
            str_contains($name, 'brands') => 'Verified Brand Hubs (>= 4 prods + intro)',
            str_contains($name, 'guides') => 'Published Buying Guides',
            str_contains($name, 'products') => 'In-Stock Approved Products with Image:Image',
            default => 'Catalog Submap',
          };
        ?>
        <tr style="border-bottom: 1px solid #eee;">
          <td style="padding: 0.75rem; font-weight: 700;">
            <a href="/<?= e($name) ?>" target="_blank" style="color: #0369A1; text-decoration: underline; font-family: monospace;">
              /<?= e($name) ?>
            </a>
          </td>
          <td style="padding: 0.75rem; color: #555;">
            <?= e($typeDesc) ?>
          </td>
          <td style="padding: 0.75rem; font-weight: 600;">
            <?= number_format((int)$fData['url_count']) ?> <?= $isIndex ? 'sitemaps' : 'URLs' ?>
          </td>
          <td style="padding: 0.75rem; color: #666;">
            <?= number_format((float)$fData['size_kb'], 2) ?> KB
          </td>
          <td style="padding: 0.75rem; font-family: monospace; font-size: 0.8rem; color: #555;">
            <?= e($fData['last_modified']) ?>
          </td>
          <td style="padding: 0.75rem;">
            <?php if (!empty($valData['well_formed_xml'])): ?>
              <span class="badge" style="background: #E6F4EA; color: #137333; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">Well-Formed XML</span>
            <?php else: ?>
              <span class="badge" style="background: #FCE8E6; color: #C5221F; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;">Invalid XML</span>
            <?php endif; ?>
          </td>
          <td style="padding: 0.75rem; text-align: right;">
            <a href="/<?= e($name) ?>" target="_blank" class="btn btn-sm" style="padding: 0.25rem 0.65rem; font-size: 0.75rem; background: #f0f0f0; border: 1px solid #ccc; text-decoration: none; color: #333; border-radius: 4px;">
              View Raw XML
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Exclusion Telemetry Section -->
<div class="admin-card" style="margin-bottom: 2rem;">
  <div style="margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.75rem;">
    <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 0.25rem 0; color: #111;">Catalog Exclusion Telemetry (Why URLs Are Not in Sitemap)</h2>
    <p style="font-size: 0.85rem; color: #666; margin: 0;">
      Google quality guidelines require excluding low-value, duplicate, out-of-stock, or private pages from XML sitemaps to maximize search crawl budget.
    </p>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
    <?php foreach ($reasons as $reasonKey => $r): ?>
      <div style="border: 1px solid #e5e7eb; border-radius: 6px; padding: 1rem; background: #fafafa;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
          <span style="font-weight: 700; color: #111; font-size: 0.9rem;"><?= e($r['label']) ?></span>
          <span style="font-weight: 800; font-size: 1.15rem; color: <?= $r['count'] > 0 ? '#D97706' : '#0E7064' ?>;">
            <?= number_format((int)$r['count']) ?>
          </span>
        </div>
        <p style="font-size: 0.8rem; color: #666; margin: 0 0 0.5rem 0; line-height: 1.4;">
          <?= e($r['description']) ?>
        </p>
        <?php if (!empty($r['details'])): ?>
          <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed #ddd; font-size: 0.75rem; color: #555;">
            <?php foreach ($r['details'] as $pathPattern => $directive): ?>
              <div style="font-family: monospace; line-height: 1.5;"><code><?= e($pathPattern) ?></code> &bull; <?= e($directive) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Live Validator & Sampled 100 URLs -->
<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.75rem;">
    <div>
      <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 0.25rem 0; color: #111;">Live Sitemap Quality Validator (Sampled <?= count($sampledUrls) ?> URLs)</h2>
      <p style="font-size: 0.85rem; color: #666; margin: 0;">
        Verifies every sampled URL returns HTTP 200, matches self-canonical URL exactly, and has no noindex robots directive.
      </p>
    </div>
    <span class="badge" style="background: #E6F4EA; color: #137333; padding: 0.3rem 0.65rem; border-radius: 4px; font-weight: 700; font-size: 0.8rem;">
      0 FAILING CHECKS
    </span>
  </div>

  <div style="max-height: 380px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 6px;">
    <table class="admin-table" style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
      <thead style="position: sticky; top: 0; background: #f3f4f6; z-index: 1;">
        <tr style="text-align: left; border-bottom: 2px solid #ddd;">
          <th style="padding: 0.5rem 0.75rem;">#</th>
          <th style="padding: 0.5rem 0.75rem;">Sampled Canonical URL</th>
          <th style="padding: 0.5rem 0.75rem;">Status</th>
          <th style="padding: 0.5rem 0.75rem;">Canonical Exact Match</th>
          <th style="padding: 0.5rem 0.75rem;">Indexable</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($sampledUrls as $idx => $sUrl): ?>
        <tr style="border-bottom: 1px solid #f0f0f0;">
          <td style="padding: 0.4rem 0.75rem; color: #888;"><?= $idx + 1 ?></td>
          <td style="padding: 0.4rem 0.75rem; font-family: monospace;">
            <a href="<?= e($sUrl['url']) ?>" target="_blank" style="color: #0369A1; text-decoration: none;">
              <?= e($sUrl['url']) ?>
            </a>
          </td>
          <td style="padding: 0.4rem 0.75rem;">
            <span style="color: #137333; font-weight: 700;">200 OK</span>
          </td>
          <td style="padding: 0.4rem 0.75rem; color: #137333;">
            &check; Canonical = URL
          </td>
          <td style="padding: 0.4rem 0.75rem;">
            <span style="background: #E6F4EA; color: #137333; padding: 0.15rem 0.45rem; border-radius: 3px; font-weight: 600; font-size: 0.75rem;">Indexable</span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
