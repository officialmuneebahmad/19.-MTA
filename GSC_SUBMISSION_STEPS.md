# Google Search Console — Sitemap Submission Steps

**Domain**: `https://multantech.pk`  
**Master Sitemap Index**: `https://multantech.pk/sitemap.xml`  

---

## Step 1: Open Google Search Console
1. Navigate to the Google Search Console Sitemaps tool:  
   👉 [Google Search Console Sitemaps Manager](https://search.google.com/search-console/sitemaps?resource_id=https%3A%2F%2Fmultantech.pk%2F)
2. Ensure you have selected the verified property for `https://multantech.pk/` (or Domain property `multantech.pk`).

---

## Step 2: Remove Retired or Legacy Sitemaps (If Present)
If any older sitemaps with the old domain (`multantech.store`) or malformed paths exist under "Submitted sitemaps":
1. Click on the old sitemap entry.
2. Click the three vertical dots (`⋮`) in the top-right corner.
3. Select **Remove sitemap**.

---

## Step 3: Submit the Master Sitemap Index
1. In the **Add a new sitemap** text box, enter:
   ```text
   sitemap.xml
   ```
   *(The prefix `https://multantech.pk/` is automatically prepended by Google).*
2. Click **Submit**.

---

## Step 4: Verify Processing Status
1. Wait a few moments and refresh the page.
2. Under "Submitted sitemaps", verify that `sitemap.xml` shows:
   - **Type**: `Sitemap index`
   - **Status**: `Success` (Green)
   - **Discovered pages**: `658` (or number of child URLs)
3. Click on `sitemap.xml` to view the 6 registered child sitemaps:
   - `https://multantech.pk/sitemap-pages.xml` (17 pages)
   - `https://multantech.pk/sitemap-categories.xml` (8 categories)
   - `https://multantech.pk/sitemap-brands.xml` (21 brands)
   - `https://multantech.pk/sitemap-guides.xml` (0 guides)
   - `https://multantech.pk/sitemap-products-1.xml` (500 products)
   - `https://multantech.pk/sitemap-products-2.xml` (112 products)

---

## Step 5: (Optional) URL Inspection Test
1. In the top search bar of Google Search Console, test the master URL:
   ```text
   https://multantech.pk/sitemap.xml
   ```
2. Click **Test Live URL**.
3. Confirm that Googlebot retrieves the file with HTTP 200 and parses the XML structure without errors.
