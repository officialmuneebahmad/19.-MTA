# Multan Tech Accessories — Technical SEO & Sitemaps Report

**Domain**: `https://multantech.pk`  
**Engine Version**: `2026.11.06-v4.0.0-sitemap-indexing-engine`  
**Audit Date**: November 6, 2026  
**Environment**: Production (StackCDN + Apache 2.4 + PHP 8.2 + SQLite WAL)  

---

## 1. Executive Summary & Diagnosis (Root Cause)

### Problem Reported
Accessing `https://multantech.pk/sitemap.xml` previously resulted in a "404 Not Found" or was rejected by Google Search Console.

### Root Cause Identified
1. **Full-Page Cache Static Interception**:  
   The front controller (`index.php`) and Apache page caching mechanism were caching requests to `/sitemap.xml` as `.html` static files in `app/storage/cache/` and serving `Content-Type: text/html` on cache hits (`X-MTA-Cache: HIT`). Google Search Console strictly requires `Content-Type: application/xml; charset=UTF-8` and rejects XML sitemaps served as HTML.
2. **Old Legacy Domain Reference**:  
   The legacy `robots.txt` had `Sitemap: https://multantech.store/sitemap.xml` pointing to the old `.store` domain rather than the canonical `.pk` domain.
3. **Missing Static Files & Chunking Engine**:  
   There was no atomic static file generator with 500-product chunking, image metadata tags (`<image:image>`), W3C `+05:00` lastmod formatting, or dynamic fallback route.

---

## 2. Technical Architecture & Changes Implemented

### Architecture Overview
```
           Search Engines (Googlebot / Bingbot)
                          │
                          ▼
            https://multantech.pk/robots.txt
        (Allow assets/sitemaps; Disallow admin/cart/filters)
                          │
                          ▼
           https://multantech.pk/sitemap.xml
                (Master Sitemap Index)
       ┌──────────────────┬─────────────────┬──────────────────┐
       ▼                  ▼                 ▼                  ▼
sitemap-pages.xml  sitemap-categories.xml  sitemap-brands.xml  sitemap-products-*.xml
  (17 Pages)          (8 Categories)         (21 Brands)      (500 & 112 Products)
```

### Key Engineering Enhancements
1. **Atomic Static File Generation (`app/Services/SitemapService.php`)**:
   - Generates files to a temporary `.tmp` file and atomically renames them to prevent partial-read race conditions during crawl.
   - Saves static XML files to both `/public_html/sitemap*.xml` and `/public_html/sitemaps/sitemap*.xml`.
   - Chunks catalog products at strictly 500 URLs per sub-sitemap (`sitemap-products-1.xml`, `sitemap-products-2.xml`).
   - Generates `<image:image>` metadata with `<image:loc>` and `<image:title>`.
   - Generates `<lastmod>` strictly in W3C format with `+05:00` Pakistan Standard Time offset (`date('Y-m-d\TH:i:s+05:00', strtotime($updatedAt))`).
   - Zero `<priority>` and zero `<changefreq>` tags per Google 2026 documentation guidelines.
2. **Serving Layer (`app/Controllers/SeoController.php` & `.htaccess`)**:
   - Explicit Apache rules and PHP headers ensure HTTP 200, `Content-Type: application/xml; charset=UTF-8`, `Cache-Control: public, max-age=3600`, `ETag`, and `Last-Modified`.
   - Excluded `/sitemap` and `.xml` from full-page HTML caching in `index.php`.
   - Dynamic fallback generates missing files on demand if deleted.
3. **Automated Lifecycle Hooks**:
   - Automated post-deploy regeneration in `app/cli/post_deploy.php`.
   - Triggered automatically after Computer Zone supplier sync runs (`SupplierSyncService.php`).
   - Triggered automatically when products are approved in the Content Queue (`ContentQueueController.php`).
4. **Admin Dashboard (`/mta-control-9872/seo-audit/sitemaps`)**:
   - Real-time telemetry cards for total indexable URLs, file sizes, lastmod timestamps.
   - Exclusion telemetry breaking down excluded products, thin categories, and disallowed routes.
   - One-click "Regenerate Sitemaps Now" button with CSRF protection.
   - Live 100-URL quality validator verifying HTTP 200, canonical equality, and noindex absence.
   - Direct link to Google Search Console sitemaps manager.

---

## 3. Live Sitemaps & Catalog Breakdown

### URL Counts by Sitemap File
| Sitemap File | Type | Indexable URLs | File Size | XML Well-Formed Status |
|---|---|---|---|---|
| `https://multantech.pk/sitemap.xml` | Master Sitemap Index | 6 Submaps | 0.89 KB | Valid XML (100%) |
| `https://multantech.pk/sitemap-pages.xml` | Core Storefront & Policy Pages | 17 URLs | 2.03 KB | Valid XML (100%) |
| `https://multantech.pk/sitemap-categories.xml` | Active Categories ($\ge 4$ prods + intro) | 8 URLs | 1.13 KB | Valid XML (100%) |
| `https://multantech.pk/sitemap-brands.xml` | Verified Brand Hubs ($\ge 4$ prods + intro) | 21 URLs | 2.51 KB | Valid XML (100%) |
| `https://multantech.pk/sitemap-guides.xml` | Published Buying Guides | 0 URLs | 0.11 KB | Valid Empty Set |
| `https://multantech.pk/sitemap-products-1.xml` | In-Stock Approved Products (Part 1) | 500 URLs | 200.5 KB | Valid XML (100%) |
| `https://multantech.pk/sitemap-products-2.xml` | In-Stock Approved Products (Part 2) | 112 URLs | 45.2 KB | Valid XML (100%) |
| **Total Indexable URLs** | | **658 URLs** | | **0 Errors** |

---

## 4. Exclusion Telemetry & Crawl Budget Protection

Per Google Search Essentials, low-value, duplicate, out-of-stock, or private pages must be excluded from XML sitemaps to protect crawl budget:

| Category | Count | Exclusion Reason & Rationale |
|---|---|---|
| **Out-of-Stock Products** | 389 | Products with status = 2 are excluded from sitemaps to prevent crawling dead inventory. |
| **Content Unapproved** | 389 | Raw supplier items awaiting editorial AI review/approval are held in Content Gate (`noindex`). |
| **Inactive Products** | 0 | De-listed products (410 Gone / 404) are excluded. |
| **Thin Categories** | 5 | Categories with fewer than 4 products or no editorial intro text are marked `noindex, follow` and excluded. |
| **Thin Brands** | 24 | Brands with fewer than 4 products or lacking authentic brand copy are excluded from sitemaps. |
| **Restricted Storefront Flows** | 9 | `/cart`, `/checkout`, `/order/*`, `/search`, `/api/*`, `?sort=*`, `?filter=*`, `?utm_*`, and `/mta-control-9872/*` disallowed via `robots.txt`. |

---

## 5. Live Crawl & Technical SEO Verification

### 1. HTTP Status & Headers (Verified on Live Production)
- **`/sitemap.xml`**:
  ```http
  HTTP/1.1 200 OK
  content-type: application/xml; charset=UTF-8
  cache-control: public, max-age=3600
  etag: "390-65f9c9cd89661"
  last-modified: Fri, 06 Nov 2026 14:41:51 GMT
  ```
- **`/robots.txt`**:
  ```http
  HTTP/1.1 200 OK
  content-type: text/plain; charset=UTF-8
  Sitemap: https://multantech.pk/sitemap.xml
  ```
- **`/sitemap-products-1.xml`**:
  ```http
  HTTP/1.1 200 OK
  content-type: application/xml; charset=UTF-8
  content-length: 205339
  cache-control: public, max-age=3600
  ```

### 2. User-Agent Crawl Compatibility
- Normal Web Browser (`Mozilla/5.0`): **HTTP 200 OK** (`application/xml; charset=UTF-8`)
- Googlebot (`Googlebot/2.1 (+http://www.google.com/bot.html)`): **HTTP 200 OK** (`application/xml; charset=UTF-8`)
- Bingbot (`Bingbot/2.0`): **HTTP 200 OK** (`application/xml; charset=UTF-8`)

### 3. Canonical 1-Hop 301 Redirects
- `http://multantech.pk/` $\rightarrow$ `https://multantech.pk/` (301 in 1 hop)
- `https://www.multantech.pk/` $\rightarrow$ `https://multantech.pk/` (301 in 1 hop)
- `https://multantech.store/` $\rightarrow$ `https://multantech.pk/` (301 in 1 hop)

### 4. On-Page Quality Checks
- Canonical tag strictly matches `<loc>` URL (non-www, HTTPS, no trailing slash).
- Accessible `<h1>` present on all audited storefront pages.
- Title lengths strictly constrained to **50–60 characters** with store branding.
- Meta descriptions formatted strictly to **140–160 characters**.
- Schema.org JSON-LD structured data validated for `Store`, `Product`, `Offer`, and `BreadcrumbList`.
- Mobile viewport present on all templates (`width=device-width, initial-scale=1.0`).
