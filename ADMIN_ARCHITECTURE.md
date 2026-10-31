# MTA Admin Portal Architecture & Engineering Specification

## 1. Executive Summary & Design System

The Multan Tech Accessories (MTA) Admin Operations Console (`/mta-control-9872`) is a high-performance, mobile-first, single-pane-of-glass management console engineered for Pakistan e-commerce operations.

### Typography
- **Headings & Key Metrics:** `Space Grotesk`, sans-serif (700/800 weight, -0.02em letter spacing).
- **Body Text & Microcopy:** `Inter`, sans-serif (400/500/600 weight).
- Loaded with `preconnect` and fallback system fonts.

### Color Tokens (CSS Custom Properties)
| Token | Day Mode (`:root`) | Night Mode (`[data-theme="dark"]`) | Usage |
|---|---|---|---|
| `--admin-bg` | `#FAF9F5` (Warm Cream) | `#262624` (Deep Charcoal) | Application canvas |
| `--admin-card` | `#FFFFFF` | `#30302E` | Card surface |
| `--admin-card-border` | `#E8E6DC` | `#4A4944` | Subtle borders |
| `--admin-surface-muted` | `#F5F3ED` | `#2A2A28` | Hover states, pill containers |
| `--admin-text` | `#141413` | `#FAF9F5` | Primary text |
| `--admin-text-muted` | `#5E5D59` | `#B7B5A9` | Secondary / subtext |
| `--mta-orange` | `#FF6B2C` | `#FF8A57` | Primary brand accent |
| `--mta-orange-btn` | `#E8501A` | `#FF6B2C` | WCAG AA compliant interactive buttons |
| `--mta-teal` | `#14B8A6` | `#2DD4BF` | Secondary accent / verified badges |
| Status: Amber | `#FEF3C7` / `#92400E` | `#FBBF24` (Alpha 0.18) | Pending approvals |
| Status: Sky | `#E0F2FE` / `#0369A1` | `#38BDF8` (Alpha 0.18) | Processing |
| Status: Violet | `#EDE9FE` / `#6D28D9` | `#C4B5FD` (Alpha 0.18) | Shipped / In Transit |
| Status: Green | `#DCFCE7` / `#15803D` | `#4ADE80` (Alpha 0.18) | Delivered / Paid |
| Status: Rose | `#FFE4E6` / `#BE123C` | `#FB7185` (Alpha 0.18) | Cancelled / Warning |

*Zero dark blue is used anywhere in the admin console.*

---

## 2. App Shell & Layout Specification

### Desktop ($\ge 1024\text{ px}$)
- **Left Sidebar:** `264px` fixed width, `position: sticky; top: 0; height: 100vh; overflow-y: auto`.
- **Close Button:** `.sidebar-close-btn` is strictly hidden via `display: none !important;`.
- **Top Navigation Bar:** `64px` height, sticky with `z-index: 40`. Contains global search input, quick JSON export, Day/Night toggle, storefront quick link, user profile pill (Owner), and sign-out button.
- **Content Canvas:** Maximum width `1440px`, centered with `margin: 0 auto; padding: 2rem;`.

### Mobile & Tablet ($< 1024\text{ px}$)
- **Off-Canvas Drawer:** The sidebar transforms into an off-canvas drawer with `transform: translateX(-100%)`.
- **Backdrop & Focus Trap:** Tapping the hamburger (`#sidebar-toggle-btn`) activates `.admin-sidebar.open` and `#sidebar-backdrop.open`, applying body scroll lock (`document.body.style.overflow = 'hidden'`).
- **Dismissal:** Drawer closes on backdrop tap, close button (`#sidebar-close-btn`), or pressing `Escape`.
- **Input Zoom Prevention:** All inputs (`.form-control`, `input`, `select`) use `16px` font size on touch devices to prevent mobile Safari/Chrome viewport auto-zooming.

---

## 3. Defensive Safety Net (Root Cause Prevention)

To guarantee that no missing class or future template change can render oversized elements:
1. **SVG Bounds:**
   ```css
   svg { max-width: 100%; height: auto; vertical-align: middle; flex-shrink: 0; }
   .sidebar-header svg, .sidebar-nav svg, .admin-topbar svg, .btn svg { width: 20px; height: 20px; min-width: 20px; max-width: 20px; }
   ```
2. **Image Constraints:**
   ```css
   img { max-width: 100%; height: auto; display: block; }
   .top-product-img, .table-thumb, .admin-table img {
     width: 48px; height: 48px; min-width: 48px; max-width: 48px; object-fit: cover; border-radius: 8px; flex-shrink: 0;
   }
   ```
3. **Cache-Busting Asset Pipeline:**
   Admin layout loads assets with `MTA_BUILD_VERSION`:
   ```html
   <link rel="stylesheet" href="/assets/css/admin.css?v=<?= e($buildVersion) ?>">
   <script src="/assets/js/admin.js?v=<?= e($buildVersion) ?>" defer></script>
   ```
   Admin assets are isolated from storefront stylesheets (`app.css`), and storefront never loads `admin.css`.

---

## 4. Single Authoritative Data Reconciliation Model

All numbers displayed in the admin console are computed by `App\Services\DashboardService::getCatalogReconciliation()`:

$$\text{Supplier Feed (991)} = \text{Imported Catalog (991)} + \text{Skipped Invalid (0)}$$
$$\text{Imported Catalog (991)} = \text{In Stock (833)} + \text{Out of Stock (158)} + \text{Coming Soon (0)}$$

### Data Definitions Glossary:
- **Supplier Feed Total (991):** Raw catalog items parsed from the automated supplier XML/JSON feed.
- **MTA Active Catalog (991):** Total product records present in the MTA database.
- **In Stock (833):** Items with supplier inventory available for immediate fulfillment.
- **Out of Stock (158):** Catalog items temporarily out of stock at supplier. Prices continue to track formula rules.
- **Live Published (991):** Catalog records with `is_published = 1 AND is_active = 1`.
- **Top Selling Products Rule:** Only products with `units_sold > 0` are ranked as top selling. When verified orders are 0, a clean empty state is shown, followed by engagement data ("Most Viewed" and "WhatsApp Inquiries") clearly labeled as catalog engagement, not sales.

### Sync Heartbeat & Cron Telemetry:
- **Last Sync:** Finished timestamp from `sync_runs` table.
- **Health Badges:**
  - `Age < 2 hours`: Healthy (Green badge)
  - `2 hours <= Age <= 6 hours`: Delayed (Amber badge)
  - `Age > 6 hours`: Stale (Red badge with cPanel/cron hint)

---

## 5. How to Add a New Admin Page

1. **Create Controller Method:**
   ```php
   namespace App\Admin;
   use App\Core\Request;
   use App\Core\Response;
   use App\Core\View;

   class CustomModuleController {
       public function index(Request $request): Response {
           $content = View::render('admin/custom/index', [
               'data' => ...
           ]);
           return (new Response())->html(View::render('layouts/admin', [
               'content' => $content,
               'page_title' => 'Custom Module',
           ]));
       }
   }
   ```
2. **Add Route in `app/routes.php`:**
   ```php
   $router->get("/{$adminBase}/custom", [AdminCustomModuleController::class, 'index'], [AdminAuthMiddleware::class]);
   ```
3. **Add Navigation Item in `app/Views/layouts/admin.php`:**
   ```php
   <a href="<?= e($adminPath) ?>/custom" class="nav-item <?= $isActive('custom') ? 'active' : '' ?>">
     <svg ...></svg>
     <span>Custom Module</span>
   </a>
   ```
4. **Use Standard UI Components:**
   Wrap view in `.admin-panel` or `.admin-card`, use `.admin-table` for data rows, and standard `.btn .btn-primary` actions.
