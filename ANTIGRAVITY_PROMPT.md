# ANTIGRAVITY BUILD PROMPT: "Multan Tech Accessories"

You are a senior full-stack PHP engineer, technical SEO specialist and UI/UX designer. Build the complete, production-ready e-commerce website described below from scratch. Work autonomously and ask no questions. Where information is missing, use a clearly marked placeholder (`TODO-OWNER`) that the owner can edit from the admin panel, and never invent facts, credentials, reviews, awards, statistics or legal claims.

Priority order when requirements conflict: 1) security, 2) every supplier product imported correctly, 3) Core Web Vitals and speed, 4) SEO and indexing correctness, 5) visual polish.

---

## 1. Business facts (use exactly)

- Site name: **Multan Tech Accessories**. Domain: `https://multantech.store` (canonical = HTTPS, non-www; 301 from http and www).
- Footer and About page credit: **Founded & Developed by Muneeb Ahmad**.
- Primary phone / WhatsApp: **0307-0655275** (display) / `tel:+923070655275` / `https://wa.me/923070655275`.
- Address: **Al-Quresh Phase 3, Sher Shah Road, Multan, Punjab, Pakistan**.
- Market: Pakistani customers only. Currency PKR shown as `Rs. 1,400`. Language: English (`<html lang="en-PK">`) with friendly, simple wording that Pakistani shoppers understand.
- Products: computer and mobile accessories (speakers, cables, chargers, power banks, memory cards, gaming accessories, networking, storage, audio, etc.).
- Sales model: Cash on Delivery plus WhatsApp ordering. No online payment gateway in v1.
- Social links in footer (icons only, URLs editable in admin, placeholders until the owner fills them): Instagram, Facebook, TikTok.
- Top announcement strip, exact default text (editable in admin):
  `FREE DELIVERY ABOVE 5000 - FREE DELIVERY ON SECOND ORDER - MINIMUM DELIVERY CHARGES ONLY FOR MULTANIS`

## 2. Hosting constraints and the chosen tech stack

Hosting is a shared-style UK cloud plan: Apache + PHP + MySQL, control panel, FTP, cron jobs, free SSL, built-in CDN, 15 GB NVMe, 3 MySQL databases, 1 domain + 2 subdomains. **No Node.js, SSH or Composer can be assumed at runtime.** Therefore:

- **PHP 8.2+** (keep code compatible with 8.1), **MySQL/MariaDB** (utf8mb4, InnoDB), **PDO prepared statements only**.
- Custom lightweight MVC: front controller, router, plain PHP templates with an escaping helper, small service classes. No heavy framework. If Composer packages are used, commit `vendor/` so the host needs nothing installed.
- **Tailwind CSS built locally** with the CLI into one purged, minified CSS file (target under 25 KB gzipped, hashed filename, immutable cache). Critical CSS inlined in `<head>`.
- **Vanilla JS (ES modules)**. No jQuery, React or large libraries on the public site. Total public JS under 30 KB gzipped. Admin may use small helpers and a lightweight rich-text editor (Trix or Quill), loaded only in admin.
- **Server-side rendering for everything that matters to Google.** All product, category, brand, guide and policy content must be in the initial HTML. JavaScript only enhances (carousel, drawer, search suggestions, cart).
- Project layout: `/app` (outside web root: code, config, storage, cron scripts) and `/public_html` (web root: `index.php`, `.htaccess`, hashed assets, uploads). Secrets live only in `/app/config.php`, never in the web root and never in git.
- Self-hosted WOFF2 fonts (no Google Fonts requests):
  - Headings: **Space Grotesk** (600, 700). Body/UI: **Inter** (400, 500, 600). Latin subset, `font-display: swap`, preload the two critical files, add `size-adjust` fallback metrics to prevent layout shift. Include the OFL license files.

## 3. Supplier API sync (MUST import ALL 991 products)

This is the core of the catalog. Build `/app/cron/sync.php` (CLI only, `flock` lock file, no overlapping runs).

**Endpoint:** `GET https://api.computerzone.pk/products`
**Auth header:** `Authorization: <token>` (raw token, no "Bearer"). Make the header format configurable and fall back to `Bearer <token>` only if the config says so.
**Response shape (verified):**
```json
{"status":"success","code":200,"message":"Record Found.","Products":[ {...}, {...} ]}
```
One call currently returns **every product (991 at the time of writing)**, with no pagination fields. Each product has:
`ProductId` (string such as "01596", keep leading zeros), `ProductCode` (usually null), `ProductTitle`, `ProductType`, `ProductPrice` (string integer in PKR, "0" means no price), `ProductImage` (URL), `ProductLink` (supplier store URL), `ProductCategoryId`, `ProductCategoryTitle`, `ProductSubCategoryId` and `ProductSubCategoryTitle` (nullable), `ProductDetail` (HTML), `ProductBrandId`, `ProductBrandTitle`, `ProductStatusId` (1 In Stock, 2 Out of Stock, 3 Coming Soon), `ProductStatusTitle`, `ProductUpdate` (supplier timestamp, unreliable: do not use it as a change signal).

**Requirements:**
1. Import **all** products. No `LIMIT`, no default page size, no filtering by status or price. Out-of-stock, coming-soon and price-0 items are imported too. If the response ever contains pagination keys (`page`, `total_pages`, `next`, `total`), loop until finished.
2. After each run assert `imported + skipped_invalid == feed_count`. Rows missing `ProductId` or `ProductTitle` are skipped and logged. The admin dashboard must show **"Supplier feed: N / Imported: N / Skipped: N"** and turn red if they differ.
3. One transaction per run, 3 retries with backoff, 120 s timeout. **Abort without changing anything** if the response is not `status=success`, `Products` is not an array, or the count is below 50% of the active supplier products already stored.
4. Change detection by `md5` of the normalized fields plus the markup settings. Only real changes update `updated_at` (this feeds sitemap `lastmod`). Unchanged rows only get `last_seen_at`.
5. Slugs are generated once from the title plus the supplier id (`kisonli-x11-rgb-gamer-pc-mini-usb-speaker-with-6w-power-01596`) and never change.
6. Products missing from a complete successful feed become `is_active = 0` (not deleted) and the storefront returns **410 Gone** with a helpful page and noindex.
7. `ProductLink` is stored in an admin-only column and **never output publicly**.
8. Supplier categories and brands are upserted into their own tables (see section 4).
9. Schedule: cron hourly. Admin "Sync now" writes a request row that a 1-minute cron picks up (web requests must never run the sync directly).
10. **The supplier API only accepts calls from the production server IP (185.151.30.200).** Local machines cannot call it. Therefore build `tools/mock_supplier/`, a tiny PHP server that returns **991 generated products in the exact shape above**, including edge cases (price "0", all three statuses, null subcategory, HTML entities, very long titles, duplicate titles, missing brand). `api_url` in config points to the mock during development. The real token is a placeholder in `config.sample.php`.
11. Pricing: `sell_price = ceil((supplier_price * (1 + percent/100) + fixed) / round_to) * round_to`. Placeholder defaults 10 / 0 / 10, configurable globally, per category and per product (`price_override` is never overwritten by sync). `sell_price = 0` shows "Ask price on WhatsApp" and hides Add to Cart.
12. Images: download each supplier image once into `/storage`, generate WebP at 400, 800 and 1200 px wide (GD or Imagick, whichever exists), and serve from the site. A cron worker processes 40 images per run. Until a product's image is processed, use the supplier URL with width and height attributes set. Re-fetch only if the URL changes.
13. The supplier's `ProductDetail` HTML is stored sanitized in `supplier_description_html` (allow only p, br, strong, b, ul, ol, li, h3, h4, table elements, no attributes). It is **raw material, not the published description** (see section 11).

## 4. Data model (essentials)

`products` (id, `source` ENUM supplier/manual/import, `source_name`, supplier_id unique nullable, sku, slug unique, title, category_id, subcategory_title, brand_id, supplier_price, sell_price, price_override, status 1/2/3, image fields, supplier_description_html, description_html (original), short_description, key_features JSON, specs JSON, meta_title, meta_description, target_keyword, content_state ENUM supplier_raw/ai_draft/approved, is_active, is_published, pinned, popularity_score, supplier_link (admin-only), content_hash, last_seen_at, created_at, updated_at).
Plus: `categories` (site tree with parent_id, slug, name, intro_html, meta fields, sort, pinned), `supplier_category_map` (supplier category id to site category id), `brands`, `product_images`, `orders`, `order_items`, `banners`, `pages`, `posts` (guides), `authors`, `redirects`, `log_404`, `settings` (key/value), `daily_hits` (aggregated counters), `sync_runs`, `users`, `audit_log`.

Manual/imported products (`source` manual or import) are **never touched by the supplier sync**.

## 5. Categories and "most used products first"

- Build a **clean site category tree** (max 2 levels), e.g. Mobile Accessories, Computer Accessories, Audio and Speakers, Gaming, Power and Charging, Storage and Memory, Networking, Cables and Adapters, Smart and CCTV. Auto-map supplier categories to these on first import by keyword rules, and give the admin a **Category Mapping screen** to merge and rename. Unmapped supplier categories stay visible under their own name and appear in a "needs mapping" list.
- Category and brand pages are indexable only if they have at least 4 products and unique intro text; otherwise `noindex, follow`.
- **Popularity ranking:** track views, add-to-cart, WhatsApp clicks and orders per product with a lightweight beacon endpoint (bot filtering, per-session de-duplication, rate limiting). Nightly cron computes `popularity_score = orders*10 + whatsapp_clicks*4 + add_to_cart*3 + views` over the last 30 days with decay, plus an admin `pinned` boost. Default sort everywhere is **Popular**; homepage "Best Sellers" and top-of-category rows use it. Seed with admin-pinned products until data accumulates.
- Other sorts (price low/high, newest) use `?sort=` and canonicalize to the clean URL.

## 6. Pages and URLs (clean, descriptive, lowercase, no trailing slash)

`/` home, `/categories/{slug}`, `/products/{slug}`, `/brands/{slug}`, `/best-sellers`, `/search?q=` (noindex), `/cart`, `/checkout`, `/order/{number}` (noindex), `/track-order`, `/guides` and `/guides/{slug}`, `/author/muneeb-ahmad`, `/about`, `/contact`, `/shipping-and-delivery`, `/returns-and-refunds`, `/privacy-policy`, `/terms-and-conditions`, `/cookie-policy`, `/editorial-and-pricing-policy`, `/faq`, `/sitemap` (human HTML sitemap), `/sitemap.xml`, `/robots.txt`, `/ads.txt`, custom 404 and 410 pages.

Home page order: top strip, header, hero carousel, shop-by-category tiles, Best Sellers, new arrivals, brands row, trust strip (Cash on Delivery, delivery rules, WhatsApp support), latest guides, short original "about the store" text block.

## 7. Design system

Light-hearted but professional, warm and friendly, rounded (16 px cards), soft shadows, generous spacing, no clutter. Mobile first, fully responsive from **320 px** up (test 320, 360, 390, 768, 1024, 1280, 1536). No horizontal scroll at any width.

Color tokens as CSS variables (approximate the Claude palette; do not copy Claude branding or logos). Verify every text/background pair reaches WCAG AA (4.5:1 for normal text) and adjust shades if not.
- **Light:** bg `#FAF9F5`, surface `#FFFFFF`, surface-2 `#F0EEE6`, text `#141413`, muted `#5E5D59`, border `#E8E6DC`, accent `#C96442` (decorative), accent-strong `#B8532F` (buttons and links), success `#2E7D4F`, danger `#B3261E`.
- **Dark:** bg `#262624`, surface `#30302E`, surface-2 `#3A3A37`, text `#FAF9F5`, muted `#B7B5A9`, border `#4A4944`, accent `#D97757` (use dark text `#141413` on it), success `#5FB985`, danger `#F2766B`.

**Day/night toggle** (header, always visible): animated sun/moon SVG morph (rotation and scale, about 300 ms), colors cross-fade by transitioning only `background-color`, `color` and `border-color` (about 250 ms). Default to the system preference, persist the choice in `localStorage`, apply it with a tiny blocking inline script before CSS to prevent a flash, update `<meta name="theme-color">` and `color-scheme`, honor `prefers-reduced-motion`. `aria-pressed` and a clear label.

Logo: a simple SVG wordmark "Multan Tech" with a small accessories icon, editable in admin.

## 8. Layout components

1. **Top announcement strip** above the header: the exact text from section 1, as a slow marquee ticker (pause on hover, focus and tap; static and centered if `prefers-reduced-motion`). Text editable in admin.
2. **Header:** logo, search with live suggestions (debounced, JSON endpoint, cached), phone/WhatsApp link, cart with count, theme toggle, hamburger on mobile. Search is a full-width second row on mobile. Sticky, hides on scroll down and returns on scroll up (no layout shift).
3. **Navigation bar, only what matters:** Home, Shop by Category (mega menu on desktop), Best Sellers, Brands, Guides, Track Order, Contact. Everything else (About, Shipping and Delivery, Returns, FAQ, Privacy, Terms) goes in a **"More" dropdown** (keyboard accessible, closes on outside click and Escape).
4. **Hero carousel:** reserved space with `aspect-ratio` (so no CLS), slides managed in admin with separate desktop and mobile images, auto-advance every 5 s, pause on hover, focus and touch, dots, arrows, swipe (CSS scroll-snap plus minimal JS), no auto-advance for reduced motion, first slide image preloaded with `fetchpriority="high"` (never lazy-loaded), `aria-roledescription="carousel"`.
5. **Mobile drawer/sidebar:** off-canvas category menu with backdrop, focus trap, Escape to close, body scroll lock, smooth transition, back buttons for sub-levels. Category pages: desktop shows a left filter sidebar (brand, price range, availability); mobile shows the filters in a bottom sheet opened by a "Filter and Sort" button. Filter combinations are `noindex, follow` with a canonical to the base category.
6. **Floating buttons, bottom-right** (respect `env(safe-area-inset-*)`, never overlap the footer content or the cookie banner):
   - **WhatsApp** (brand green, 56 px) opening `wa.me/923070655275` with a prefilled message (on product pages include the product name and URL).
   - **Back-to-top** (48 px) placed directly **above** the WhatsApp button, appears after 300 px of scroll, with an **SVG ring border that fills in real time with scroll progress** (`stroke-dashoffset`, updated with `requestAnimationFrame` and a passive listener), smooth scroll to top, accessible label.
   - On mobile product pages with the sticky bottom CTA bar, hide the floating WhatsApp button to avoid overlap.
7. **Footer:** about blurb, quick links, policies, contact (address, phone, WhatsApp, opening hours placeholder), Instagram / Facebook / TikTok icons (inline SVG, `rel="noopener me"`, aria-labels), Cash on Delivery badge, copyright with dynamic year, and **"Founded & Developed by Muneeb Ahmad"**.
8. **Product card:** image (fixed aspect ratio, width/height set), brand, title (2 lines), price, status badge (In Stock / Out of Stock / Coming Soon), quick WhatsApp order, Add to cart. Hover zoom on desktop only.
9. **Product page:** gallery, H1, price, status, city-based delivery charge preview, "Add to cart" and "Order on WhatsApp", key features, specs table, original description, FAQ accordion (`<details>`, content stays in the DOM), delivery and return summary, breadcrumbs, related products and "Popular in this category". Out-of-stock and coming-soon products show "Message us on WhatsApp for availability".

Empty states, loading states and error states must be designed and friendly.

## 9. Performance (the site must be as fast as possible)

Targets on a mid-range Android phone over 4G: **LCP under 2.5 s, INP under 200 ms, CLS under 0.1, Lighthouse mobile at least 95 for Performance, and 100 for SEO, with at least 95 for Accessibility and Best Practices.**

- **Full-page HTML cache** for anonymous visitors: files in `/storage/cache` keyed by URL, checked at the very top of `index.php` before the app boots. Invalidate per-URL when a product, category or setting changes, and on sync changes. Never cache cart, checkout, admin or POST requests.
- The server is in the UK and customers are in Pakistan, so cache aggressively: long `Cache-Control` with hashed filenames for CSS, JS, fonts and images, `ETag`/`Last-Modified`, `stale-while-revalidate` for HTML, gzip/Brotli via `.htaccess` (`mod_deflate`/`mod_brotli`), keep-alive, OPcache-friendly code, and make sure the host's built-in CDN is allowed to cache static assets and cacheable HTML.
- Images: WebP responsive `srcset`/`sizes`, explicit `width` and `height`, `loading="lazy"` and `decoding="async"` below the fold, LCP image eager with `fetchpriority="high"`. No layout shift from banners, ads, fonts or the announcement strip (reserve their heights).
- Third-party scripts (GA4, AdSense) load only after consent and after the page is idle or first interaction. The Google Maps embed on the Contact page is a click-to-load facade.
- Database: proper indexes (category + active + status + popularity, slug, FULLTEXT on title/brand), no N+1 queries, prepared statements reused, paginate listings (24 per page).
- Add a Core Web Vitals report to GA4 using the small `web-vitals` library (about 2 KB).

## 10. Technical SEO (implement all of these)

1. **Indexing:** one canonical host (HTTPS, non-www), consistent no-trailing-slash redirects, `robots.txt` (allow crawling of the storefront and assets; disallow `/admin-path`, `/api`, `/cart`, `/checkout`, `/order`, `/search`, tracking parameters; list the sitemap), and `<meta name="robots">` rules per page type. Staging is noindex.
2. **Canonical tags:** absolute, self-referencing on every indexable page. Sort, filter, search and tracking parameters canonicalize to the clean URL. Paginated pages (`?page=2`) self-canonicalize. No site-wide fallback canonical in the layout.
3. **Sitemaps:** `sitemap.xml` index with split files (products 500 per file, categories, brands, guides, pages). Only indexable 200 URLs. `<lastmod>` comes from the real `updated_at`. Pages that are noindex, inactive or filtered never appear.
4. **Breadcrumbs:** visible HTML breadcrumbs on every non-home page plus `BreadcrumbList` JSON-LD.
5. **Schema (JSON-LD, valid and matching visible content):** `Organization` and `WebSite` (with SearchAction) on home; `ComputerStore`/`LocalBusiness` with the address and phone above (geo and opening hours only if the owner fills them in); `Product` with `Offer` (price in PKR, availability InStock/OutOfStock/PreOrder, itemCondition New, brand, sku, image, seller) plus shipping and return policy objects; `ItemList` on categories; `BlogPosting` with `Person` author for guides; `Person` for the author page. **Never output fake `aggregateRating` or reviews.** Do not add FAQPage rich-result markup (the FAQ stays as visible content only).
6. **Heading structure:** exactly one H1 per page, logical H2/H3 hierarchy, headings never used for styling.
7. **Titles 50 to 60 characters:** a `seo_title()` builder composes the title and tries the pattern with the full brand suffix, then the short suffix `| Multan Tech`, then no suffix, and never exceeds 60 characters. Admin fields show live character counters. Product pattern: `{Product short name} Price in Pakistan`. Category pattern: `Buy {Category} in Pakistan | {Suffix}`.
8. **Unique meta descriptions (140 to 160 characters):** generated from templates using real data (brand, key feature, price, "Cash on Delivery"), overridable in admin. The admin SEO audit flags duplicates and missing ones.
9. **Open Graph and Twitter cards** with product or category images, favicon set, web manifest.
10. **Internal linking and orphan pages:** every indexable page must be reachable within 3 clicks from the home page through menus, category listings, related products, brand pages, guides and the HTML sitemap. A cron `audit.php` crawls the site and reports orphan pages, broken internal links, redirect chains, missing alt text, duplicate titles and thin pages to the admin dashboard.
11. **404 and redirects:** real 404 status for unknown URLs, 410 for removed products, both with `noindex` and helpful content (search box and popular categories). Admin has a **Redirect manager** (301s, CSV import for old-site URLs) and a **404 log**.
12. **Keyword cannibalization:** each indexable page has a `target_keyword` field. Admin warns when two indexable pages share the same primary keyword. Categories target commercial intent ("buy X in Pakistan"), guides target informational intent ("how to choose X"), products target the exact model. Merge or noindex thin overlapping pages.
13. **JS crawlability:** no content or links that exist only after JavaScript. Verify by checking the raw HTML of each template.
14. **Search intent:** category pages show products first with a short unique intro; guides answer the question first; product pages lead with price, availability and a clear CTA.
15. `hreflang="en-PK"` self-reference; `html lang="en-PK"`.

## 11. Original content, E-E-A-T and AdSense readiness

AdSense and Google both penalize copied or thin product feeds, so the site must not publish the supplier's text.

- **Content gate:** a product page is `index, follow` and in the sitemap only when `content_state = approved`. Otherwise it is still viewable but `noindex, follow`. Products start as `supplier_raw`.
- **AI-assisted rewrite queue (admin):** the owner picks a provider (Gemini, OpenRouter or Anthropic) and pastes an API key (stored encrypted, never exposed to the browser). A cron worker processes a configurable number of products per run with a daily cap. Input is **structured facts only** (title, brand, category, spec lines parsed from the supplier HTML). Output JSON: `short_description`, `long_description` (150 to 250 words), `key_features` (5), `meta_description`, `faq` (3). Rules in the prompt: no invented specs, warranty, prices or claims; Pakistani buyer-friendly tone; no mention of any supplier. A validator rejects output containing numbers not present in the input facts, or with more than 30% word-shingle overlap with the supplier text. Results are saved as `ai_draft` and the admin approves them individually or in bulk. Prioritize products by `popularity_score`.
- Admin can also write descriptions by hand (rich-text editor).
- **Guides section:** generate **12 starter guides** as drafts (`status = draft_needs_owner_review`, not published automatically), each 900 to 1,500 words, Pakistan-specific (for example choosing a power bank, USB-C cable guide, TWS earbuds buying guide, how to spot fake chargers, router setup basics, microSD card speed classes). No fabricated statistics, benchmarks or prices; pull any prices live from the database. Include author byline, publish date, updated date, and internal links to relevant categories.
- **Trust and E-E-A-T pages:** About (real store details, address, phone, "Founded & Developed by Muneeb Ahmad", editable story), Author page for Muneeb Ahmad (editable bio fields; do not invent credentials), Contact (address, phone, WhatsApp, map facade, form with honeypot and rate limit), Editorial and Pricing Policy (how prices and stock are updated), Shipping and Delivery, Returns and Refunds, Privacy Policy (cover orders, contact forms, Google Analytics, AdSense cookies, retention), Terms, Cookie Policy. Draft sensible defaults and flag business-specific terms (warranty period, return window) as `TODO-OWNER` in the admin. Footer links to all of them.
- **AdSense readiness (the owner will apply in 2027):** `AdSlot` component with reserved `min-height`, labelled "Advertisement", disabled by default and switched on from admin settings (publisher ID field). `ads.txt` generated from the setting. Placement only on guides and category pages (never on checkout, cart, order, 404/410, thin pages or near the floating buttons). Auto ads off by default. Cookie consent banner with **Google Consent Mode v2** (default denied until the visitor chooses, lightweight and accessible).

## 12. Analytics and Search Console

Admin settings fields (no IDs hardcoded): GA4 measurement ID, Search Console verification meta tag, Bing Webmaster verification meta tag, AdSense publisher ID. GA4 loads after consent and idle. Send e-commerce events: `view_item_list`, `view_item`, `add_to_cart`, `begin_checkout`, `purchase` (value in PKR), `whatsapp_click`, `search`. Write `SEO_LAUNCH_CHECKLIST.md` with the exact steps to verify the property, submit `sitemap.xml`, inspect key URLs and monitor Page indexing and Core Web Vitals reports.

## 13. Cart, checkout, orders and delivery

- Guest cart (server session plus localStorage backup). No customer accounts in v1.
- Checkout fields: full name, mobile (validate `03XXXXXXXXX` or `+923XXXXXXXXX`, normalize), city (dropdown of major Pakistani cities plus "Other"), full address, optional email, notes. Payment: **Cash on Delivery** with admin-editable text for advance payment via JazzCash, Easypaisa or bank transfer confirmed on WhatsApp.
- **Delivery rules (all editable in admin settings):** free delivery when the order total is above `Rs. 5,000`; free delivery on a customer's second order (match by normalized phone number with at least one earlier non-cancelled order); Multan gets the minimum delivery charge (placeholder `Rs. 100`), other cities the standard charge (placeholder `Rs. 250`, `TODO-OWNER`). Show the applicable charge in the cart and on product pages after a city is chosen.
- Order number format like `MTA-241028-7K3Q` (non-sequential). The thank-you page shows the summary and a prefilled "Confirm on WhatsApp" button. The owner gets a dashboard notification and an email (SMTP config). Anti-abuse: honeypot, rate limit, duplicate-submit guard, CSRF.
- Statuses: Pending, Confirmed, Dispatched (courier name, tracking number and tracking link, with Leopards, TCS, M&P, PostEx, Trax as options), Delivered, Cancelled, Returned.
- `/track-order`: order number plus phone number lookup.

## 14. Admin dashboard (`/{random-admin-path}`, not `/admin`)

**Authentication:** the installer creates the owner account from the email the owner types in and a **randomly generated 20-character password shown once on the final install screen**, and forces a password change on first login. **No default or hardcoded credentials anywhere.** Argon2id hashing, login rate limiting with lockout, secure session cookies, optional TOTP 2FA, roles (Owner, Staff), full audit log.

**Modules:**
1. **Dashboard:** supplier feed count vs imported count, last sync status and time, products by content state, orders today and pending, top products, SEO audit summary, 404 log summary.
2. **Products:** searchable table (filters by source, category, status, content state, price 0, no image), inline price override and pin, bulk actions (publish, unpublish, change category, price rule, approve content, export CSV).
3. **Add / import products from other sites (must be easy):**
   - **Quick Add form:** title, brand, category, price, stock status, multi-image drag-and-drop upload (auto-resize to WebP, required alt text), description editor, spec key/value repeater, SEO fields, Draft or Publish.
   - **Bulk CSV/XLSX import:** downloadable template, column-mapping step, dry-run preview with row-level validation errors, images by URL (downloaded and optimized) or from an uploaded ZIP, upsert by SKU, per-source markup rules. Imported products get `source = import` and are never touched by the supplier sync.
   - **Import from product URL:** fetch the page, read JSON-LD and Open Graph data (title, price, images, specs) into a **Draft only**, with a required checkbox "I have the right to use this content". Images are downloaded and stored locally. Descriptions must be rewritten or AI-rewritten and approved before the page can be indexed. Block private and internal IP ranges (SSRF protection), limit size and time.
4. **Categories** (tree, intro text, SEO fields) and **Category Mapping**; **Brands**.
5. **Orders:** list, detail, status updates, courier and tracking, WhatsApp click-to-chat to the customer, CSV export.
6. **Banners/Carousel:** upload desktop and mobile images, link, alt text, order, schedule start/end.
7. **Pages and Guides:** editors with SEO fields, author, status, `target_keyword`.
8. **Content Queue:** AI rewrite queue, review and bulk approve.
9. **SEO Audit:** duplicate or missing titles and descriptions, title length, thin pages, orphan pages, broken links, cannibalization warnings, redirect and 404 managers, sitemap preview.
10. **Sync:** run now, history, diff report (new, changed, deactivated), error log.
11. **Settings:** announcement text, contact details, social URLs, delivery rules, markup rules, analytics and verification IDs, AdSense, SMTP, AI provider key, cache clear.

## 15. Security

PDO prepared statements everywhere; output escaping by default; CSRF tokens on every form; strict `Content-Security-Policy` with nonces (allow only the needed Google domains after consent); HSTS, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`; secure, HttpOnly, SameSite cookies; upload validation (finfo MIME check, re-encode images, random filenames, no PHP execution in upload or storage directories via `.htaccess`); rate limiting on login, search, contact and checkout; SSRF protection on URL import; config and storage outside the web root; `display_errors` off and errors logged; the supplier token never leaves the server and is never logged.

## 16. Installer, deployment and cron

- `/public_html/install.php` wizard: checks PHP version and extensions (pdo_mysql, curl, gd or imagick, mbstring, intl optional, fileinfo), tests the DB connection, creates tables, creates the owner account (see section 14), writes `/app/config.php`, then locks and deletes itself.
- Deliver an upload-ready folder structure and `DEPLOY.md` with exact steps for this host: create the MySQL DB, upload via FTP, set the PHP version, enable SSL and force HTTPS, add these cron jobs (using the PHP binary path shown in the control panel): sync hourly, request-runner every minute, image worker every 5 minutes, content worker every 10 minutes, nightly popularity and cache jobs, nightly SEO audit.
- **Staging first:** use a subdomain (for example `staging.multantech.store`) with `noindex` and HTTP basic auth. Include a go-live checklist: switch DNS, remove staging protection, enable indexing, import the old-site URL redirect CSV, submit the sitemap.

## 17. Testing and acceptance criteria

Write automated tests (PHPUnit or plain PHP scripts) and run them against the mock supplier:
- Sync imports **exactly 991 of 991** mock products; a second run changes nothing and leaves `updated_at` untouched; a price change updates only that row; a removed product becomes inactive and returns 410; a truncated or failed response changes nothing.
- `sell_price` math, slug stability, `seo_title()` never exceeds 60 characters, meta descriptions are unique, sanitizer strips scripts and attributes, delivery rules (above 5,000, second order, Multan vs other cities), phone validation.
- Crawl check: all indexable pages return 200 with a single H1, canonical, unique title and description, valid JSON-LD, breadcrumbs; no broken internal links; no orphan pages; `robots.txt` and the sitemaps are valid.
- Manual checks: layout at 320, 360, 390, 768, 1024, 1280 px; keyboard-only navigation; screen reader landmarks; day/night toggle without a flash on reload; carousel reserves space (CLS 0); the WhatsApp and back-to-top ring buttons do not overlap; the announcement strip text is exact.

## 18. Documents to deliver

`README.md` (architecture and local setup), `DEPLOY.md`, `SEO_LAUNCH_CHECKLIST.md`, `KEYWORDS.md` (a starter list of Pakistan-focused high-intent, low-competition keyword ideas per category, such as "{product} price in Pakistan", "buy {product} in Multan", "{product} cash on delivery". It is **unverified**, so also write the steps for validating volume and difficulty with Google Keyword Planner, Search Console and Ahrefs), and `BACKLINKS.md` (an ethical link-building plan for a Multan accessories store: Google Business Profile and Bing Places with the exact address and phone, consistent business listings in reputable Pakistani directories, active Facebook, Instagram and TikTok profiles linking back, guest posts and product feedback pitches to Pakistani tech blogs, local Multan business and community sites, supplier and brand "where to buy" listings if the brands agree, unlinked brand mentions. No paid link schemes, link farms or comment spam, since these violate Google's spam policies).

## 19. Out of scope for v1

Customer accounts, reviews and ratings, wishlist, product comparison, online payment gateways, multi-language, mobile app. Do not add them. Do not use any placeholder lorem ipsum in shipped pages.

Begin by creating the project plan and file tree, then build in this order: data model and installer, supplier sync with the 991-product mock and tests, storefront templates and design system, SEO layer, checkout and orders, admin dashboard, content and AI queue, performance pass, tests and documents.
