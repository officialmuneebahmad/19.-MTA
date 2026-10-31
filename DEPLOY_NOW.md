# Deploy Now — Multan Tech Accessories (Build: `2026.10.31-v2.4.0`)

This document provides exact deployment instructions for pushing updates to the live production server at `https://multantech.store`.

---

## Method 1: Automated Push (Recommended)

Since the repository is configured with `.github/workflows/deploy.yml`, pushing master will automatically sync modified files via FTPS to `public_html/` and immediately trigger the web post-deploy runner.

```bash
git add .
git commit -m "feat: dual-stage pricing ending in 99, official brand logos, admin link in footer, social links, and zero broken links"
git push origin master
```

---

## Method 2: Manual FTP Upload List

If uploading directly through an FTP client (FileZilla / WinSCP / Cyberduck):
Target Remote Directory: `/public_html/` (or web root)

### Core Files Modified:
- `index.php` (Cache Gatekeeper, Cache-Control header, and X-MTA-Build)
- `deploy-runner.php` (NEW: Web runner for migrations, re-pricing, and web OPcache reset)
- `.github/workflows/deploy.yml` (Automated webhook invocation)

### Application Directory (`app/`):
- `app/bootstrap.php` (Defined `MTA_BUILD_VERSION`)
- `app/routes.php` (Internal link aliases & sitemap route fix)
- `app/Core/Database.php` (Schema integrity, settings migration, brand logo records)
- `app/Core/Session.php` (Session cache limiter fix)
- `app/Core/Middleware/SecurityHeadersMiddleware.php` (`X-MTA-Build` and `Cache-Control` headers)
- `app/Services/PricingService.php` (Dual-stage margin + rounding UP to 99 engine)
- `app/cli/post_deploy.php` (NEW: Post-deploy automation script)
- `app/Controllers/HomeController.php` (Social links in Organization schema `sameAs`)
- `app/Views/layouts/main.php` (`mta-build` meta, announcement strip, footer socials, trademark line, admin link)
- `app/Views/storefront/home.php` (Featured Brands: strict logo-only tiles, zero text fallback)
- `app/Views/storefront/pages/contact.php` (Social links integration)

### Assets Directory (`assets/`):
- `assets/brands/hp.svg`
- `assets/brands/joyroom.webp`
- `assets/brands/ldnio.webp`
- `assets/brands/lenovo.svg`
- `assets/brands/logitech.svg`
- `assets/brands/orico.webp`
- `assets/brands/remax.webp`
- `assets/brands/romoss.webp`
- `assets/brands/xbox.svg`
- `assets/brands/xiaomi.svg`
- `assets/brands/xtrike-me.svg`

---

## Post-Deploy Execution (Crucial)

After files are transferred to the server, execute the post-deploy sequence using **either** of the two methods below to update MySQL, recalculate all 991 prices to end in 99, purge the HTML cache, and reset OPcache:

### Option A: Web URL Runner (Instant from browser or curl)
Open this URL in your browser or run via terminal:
```bash
curl -i "https://multantech.store/deploy-runner.php?token=mta_deploy_2026_secret_9872"
```

### Option B: One-Time Cron Job (Hosting Panel without SSH)
In your StackCP Hosting Panel under **Cron Jobs**, add this one-time command:
```bash
/usr/bin/php /home/sites/multantech.store/public_html/app/cli/post_deploy.php
```
*(Path may vary depending on your StackCP account root, e.g. `/var/sites/m/multantech.store/public_html/app/cli/post_deploy.php`)*

---

## Hosting Panel CDN Purge Reminder

> [!IMPORTANT]
> **StackCP StackCDN Cache Purge**:
> StackCP uses edge caching (StackCDN). Because CDN edge nodes may still hold older HTML snapshots, log into your **StackCP Control Panel**:
> 1. Navigate to **Edge Caching / CDN**.
> 2. Click **Purge All Cache**.
> 3. Verify immediately by checking `curl -I https://multantech.store` to confirm `X-MTA-Build: 2026.10.31-v2.4.0`.
