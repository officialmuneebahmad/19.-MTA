<?php
/**
 * Multan Tech Accessories — Master Application Routes
 * MultanTech.store
 */

declare(strict_types=1);

use App\Core\Router;
use App\Core\Response;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\CsrfMiddleware;

// Storefront Controllers
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CategoryController;
use App\Controllers\BrandController;
use App\Controllers\SearchController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\OrderController;
use App\Controllers\GuideController;
use App\Controllers\PageController;
use App\Controllers\BeaconController;
use App\Controllers\SeoController;

// Admin Controllers
use App\Admin\AuthController as AdminAuthController;
use App\Admin\DashboardController as AdminDashboardController;
use App\Admin\ProductController as AdminProductController;
use App\Admin\ImportController as AdminImportController;
use App\Admin\CategoryController as AdminCategoryController;
use App\Admin\OrderController as AdminOrderController;
use App\Admin\BannerController as AdminBannerController;
use App\Admin\GuideController as AdminGuideController;
use App\Admin\ContentQueueController as AdminContentQueueController;
use App\Admin\SeoAuditController as AdminSeoAuditController;
use App\Admin\SyncController as AdminSyncController;
use App\Admin\SettingsController as AdminSettingsController;

/** @var Router $router */

// ======================================================================
// 1. STOREFRONT CATALOG & DISCOVERY
// ======================================================================
$router->get('/', [HomeController::class, 'index']);
$router->get('/best-sellers', [HomeController::class, 'bestSellers']);

$router->get('/categories', [CategoryController::class, 'index']);
$router->get('/categories/{slug}', [CategoryController::class, 'show']);

$router->get('/brands', [BrandController::class, 'index']);
$router->get('/brands/{slug}', [BrandController::class, 'show']);

$router->get('/products/{slug}', [ProductController::class, 'show']);

$router->get('/search', [SearchController::class, 'index']);
$router->get('/api/search-suggestions', [SearchController::class, 'suggestions']);
$router->post('/api/beacon', [BeaconController::class, 'track']);
$router->get('/api/beacon', [BeaconController::class, 'track']);

// ======================================================================
// 2. SHOPPING CART & COD CHECKOUT
// ======================================================================
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add'], [CsrfMiddleware::class]);
$router->post('/cart/update', [CartController::class, 'update'], [CsrfMiddleware::class]);
$router->post('/cart/remove', [CartController::class, 'remove'], [CsrfMiddleware::class]);

$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout/place-order', [CheckoutController::class, 'placeOrder'], [CsrfMiddleware::class]);

$router->get('/order/summary/{order_number}', [OrderController::class, 'summary']);
$router->get('/track-order', [OrderController::class, 'track']);

// ======================================================================
// 3. EDITORIAL GUIDES & AUTHOR
// ======================================================================
$router->get('/guides', [GuideController::class, 'index']);
$router->get('/guides/{slug}', [GuideController::class, 'show']);
$router->get('/author/{slug}', [GuideController::class, 'author']);

// ======================================================================
// 4. STATIC & POLICY PAGES
// ======================================================================
$router->get('/about', [PageController::class, 'about']);
$router->get('/contact', [PageController::class, 'contact']);
$router->post('/contact', [PageController::class, 'sendContact'], [CsrfMiddleware::class]);
$router->get('/shipping-and-delivery', [PageController::class, 'shipping']);
$router->get('/returns-and-refunds', [PageController::class, 'returns']);
$router->get('/editorial-and-pricing-policy', [PageController::class, 'editorial']);
$router->get('/faq', [PageController::class, 'faq']);
$router->get('/privacy-policy', [PageController::class, 'privacy']);
$router->get('/terms-and-conditions', [PageController::class, 'terms']);
$router->get('/cookie-policy', [PageController::class, 'cookie']);
$router->get('/sitemap', [PageController::class, 'sitemap']);

// ======================================================================
// 5. TECHNICAL SEO & SITEMAPS
// ======================================================================
$router->get('/sitemap.xml', [SeoController::class, 'index']);
$router->get('/sitemap-products-1.xml', [SeoController::class, 'products']);
$router->get('/sitemap-categories.xml', [SeoController::class, 'categories']);
$router->get('/sitemap-brands.xml', [SeoController::class, 'brands']);
$router->get('/sitemap-guides.xml', [SeoController::class, 'guides']);
$router->get('/sitemap-pages.xml', [SeoController::class, 'pages']);

// ======================================================================
// 6. SECURE ADMIN DASHBOARD (Obfuscated Configurable Path)
// ======================================================================
$adminBase = trim((string)mta_config('app.admin_path', mta_setting('admin_path', 'mta-control-9872')), '/');

// Admin Shortcuts / Convenience Redirects
$router->get('/admin', [AdminAuthController::class, 'redirectToDashboard']);
$router->get('/admin/login', [AdminAuthController::class, 'redirectToLogin']);
$router->get('/login', [AdminAuthController::class, 'redirectToLogin']);
$router->get('/admin-login', [AdminAuthController::class, 'redirectToLogin']);

// Admin Auth Routes (No auth middleware)
$router->get("/{$adminBase}/login", [AdminAuthController::class, 'showLogin']);
$router->post("/{$adminBase}/login", [AdminAuthController::class, 'login'], [CsrfMiddleware::class]);
$router->post("/{$adminBase}/logout", [AdminAuthController::class, 'logout'], [CsrfMiddleware::class]);
$router->get("/{$adminBase}/change-password", [AdminAuthController::class, 'showChangePassword']);
$router->post("/{$adminBase}/change-password", [AdminAuthController::class, 'changePassword'], [CsrfMiddleware::class]);

// Protected Admin Management Routes
$adminMw = [AuthMiddleware::class];
$adminCsrfMw = [AuthMiddleware::class, CsrfMiddleware::class];

$router->get("/{$adminBase}", [AdminDashboardController::class, 'index'], $adminMw);

// Products
$router->get("/{$adminBase}/products", [AdminProductController::class, 'index'], $adminMw);
$router->get("/{$adminBase}/products/{id}/edit", [AdminProductController::class, 'edit'], $adminMw);
$router->post("/{$adminBase}/products/{id}", [AdminProductController::class, 'update'], $adminCsrfMw);
$router->post("/{$adminBase}/products/quick-override", [AdminProductController::class, 'quickOverride'], $adminCsrfMw);

// Multi-Source Import
$router->get("/{$adminBase}/import", [AdminImportController::class, 'index'], $adminMw);
$router->post("/{$adminBase}/import/csv", [AdminImportController::class, 'importCsv'], $adminCsrfMw);
$router->post("/{$adminBase}/import/url", [AdminImportController::class, 'importUrl'], $adminCsrfMw);

// Categories & Mappings
$router->get("/{$adminBase}/categories", [AdminCategoryController::class, 'index'], $adminMw);
$router->post("/{$adminBase}/categories", [AdminCategoryController::class, 'store'], $adminCsrfMw);
$router->get("/{$adminBase}/categories/mappings", [AdminCategoryController::class, 'mappings'], $adminMw);
$router->post("/{$adminBase}/categories/mappings", [AdminCategoryController::class, 'updateMapping'], $adminCsrfMw);

// Orders
$router->get("/{$adminBase}/orders", [AdminOrderController::class, 'index'], $adminMw);
$router->get("/{$adminBase}/orders/{id}", [AdminOrderController::class, 'show'], $adminMw);
$router->post("/{$adminBase}/orders/{id}", [AdminOrderController::class, 'update'], $adminCsrfMw);

// Banners
$router->get("/{$adminBase}/banners", [AdminBannerController::class, 'index'], $adminMw);
$router->post("/{$adminBase}/banners", [AdminBannerController::class, 'store'], $adminCsrfMw);
$router->post("/{$adminBase}/banners/{id}/delete", [AdminBannerController::class, 'delete'], $adminCsrfMw);

// Buying Guides
$router->get("/{$adminBase}/guides", [AdminGuideController::class, 'index'], $adminMw);
$router->get("/{$adminBase}/guides/create", [AdminGuideController::class, 'create'], $adminMw);
$router->post("/{$adminBase}/guides", [AdminGuideController::class, 'store'], $adminCsrfMw);
$router->get("/{$adminBase}/guides/{id}/edit", [AdminGuideController::class, 'edit'], $adminMw);
$router->post("/{$adminBase}/guides/{id}", [AdminGuideController::class, 'update'], $adminCsrfMw);
$router->post("/{$adminBase}/guides/{id}/delete", [AdminGuideController::class, 'delete'], $adminCsrfMw);

// AI Content Queue
$router->get("/{$adminBase}/queue", [AdminContentQueueController::class, 'index'], $adminMw);
$router->post("/{$adminBase}/queue/approve/{id}", [AdminContentQueueController::class, 'approve'], $adminCsrfMw);
$router->post("/{$adminBase}/queue/bulk-approve", [AdminContentQueueController::class, 'bulkApprove'], $adminCsrfMw);
$router->post("/{$adminBase}/queue/bulk-enqueue", [AdminContentQueueController::class, 'bulkEnqueue'], $adminCsrfMw);

// SEO Audit & Redirects
$router->get("/{$adminBase}/seo-audit", [AdminSeoAuditController::class, 'index'], $adminMw);
$router->get("/{$adminBase}/seo-audit/redirects", [AdminSeoAuditController::class, 'redirects'], $adminMw);
$router->post("/{$adminBase}/seo-audit/redirects", [AdminSeoAuditController::class, 'storeRedirect'], $adminCsrfMw);
$router->post("/{$adminBase}/seo-audit/redirects/{id}/delete", [AdminSeoAuditController::class, 'deleteRedirect'], $adminCsrfMw);
$router->get("/{$adminBase}/seo-audit/log-404", [AdminSeoAuditController::class, 'log404'], $adminMw);
$router->post("/{$adminBase}/seo-audit/log-404/clear", [AdminSeoAuditController::class, 'clear404'], $adminCsrfMw);

// Supplier Sync
$router->get("/{$adminBase}/sync", [AdminSyncController::class, 'index'], $adminMw);
$router->post("/{$adminBase}/sync/test", [AdminSyncController::class, 'test'], $adminCsrfMw);
$router->post("/{$adminBase}/sync/trigger", [AdminSyncController::class, 'trigger'], $adminCsrfMw);
$router->post("/{$adminBase}/sync/seed-backup", [AdminSyncController::class, 'seedFallback'], $adminCsrfMw);

// Settings
$router->get("/{$adminBase}/settings", [AdminSettingsController::class, 'index'], $adminMw);
$router->post("/{$adminBase}/settings", [AdminSettingsController::class, 'update'], $adminCsrfMw);
