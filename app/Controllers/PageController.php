<?php
/**
 * Multan Tech Accessories — PageController
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Response;
use App\Core\Security;
use App\Core\Session;
use App\Core\View;
use App\Services\SeoService;

class PageController
{
    public function about(): Response
    {
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'About Us', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/pages/about', [
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('About Multan Tech Accessories'),
            'meta_description' => 'Learn about Multan Tech Accessories, founded by Muneeb Ahmad in Multan, Pakistan. 100% authentic computer accessories, transparent prices and fast delivery.',
            'canonical' => mta_url('/about'),
        ]));
    }

    public function contact(): Response
    {
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Contact Us', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/pages/contact', [
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Contact Us and Store Location'),
            'meta_description' => 'Contact Multan Tech Accessories on Sher Shah Road, Multan. Fast customer support via WhatsApp at 0307-0655275 and nationwide Cash on Delivery service.',
            'canonical' => mta_url('/contact'),
        ]));
    }

    public function sendContact(Request $request): Response
    {
        $resp = new Response();
        $session = Session::getInstance();

        // 1. Honeypot check
        $honeypot = (string)$request->post('_website_url_hp', '');
        if (!empty($honeypot)) {
            return $resp->redirect('/contact');
        }

        // 2. Rate limit: 3 messages per 10 minutes per IP
        $clientIp = $request->ip();
        if (!RateLimiter::check("contact_{$clientIp}", 3, 600)) {
            $session->setFlash('error', 'Too many messages sent. Please wait before contacting us again or message on WhatsApp.');
            return $resp->redirect('/contact');
        }

        $name = trim((string)$request->post('name', ''));
        $phone = trim((string)$request->post('phone', ''));
        $message = trim((string)$request->post('message', ''));

        if (empty($name) || empty($phone) || empty($message)) {
            $session->setFlash('error', 'Please fill in all required fields.');
            return $resp->redirect('/contact');
        }

        // Clean and log message
        $db = Database::getInstance();
        try {
            $db->execute(
                "INSERT INTO audit_log (action, user_id, details, ip_address, created_at)
                 VALUES ('contact_form_message', NULL, ?, ?, ?)",
                [
                    json_encode([
                        'name' => Security::sanitizeHtml($name),
                        'phone' => $phone,
                        'message' => Security::sanitizeHtml($message),
                    ]),
                    $clientIp,
                    date('Y-m-d H:i:s'),
                ]
            );
        } catch (\Throwable $e) {
            // Ignore if audit table has slight difference
        }

        $session->setFlash('success', 'Thank you! Your message has been received. Our team will contact you shortly.');
        return $resp->redirect('/contact');
    }

    public function shipping(): Response
    {
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Shipping & Delivery', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/pages/shipping', [
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Shipping and Delivery Policy'),
            'meta_description' => 'Delivery rates: Rs. 100 in Multan, Rs. 250 across Pakistan. Enjoy free shipping on orders above Rs. 5,000 and second orders with fast courier delivery.',
            'canonical' => mta_url('/shipping-and-delivery'),
        ]));
    }

    public function returns(): Response
    {
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Returns & Warranty', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/pages/returns', [
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('3-Day Checking Warranty and Returns'),
            'meta_description' => 'Read our 3-day checking warranty and hassle-free returns policy for tech accessories at Multan Tech Accessories. Easy returns and customer satisfaction.',
            'canonical' => mta_url('/returns-and-refunds'),
        ]));
    }

    public function editorial(): Response
    {
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Editorial & Pricing Policy', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/pages/editorial', [
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Editorial and Pricing Policy'),
            'meta_description' => 'Explore our editorial integrity, supplier pricing transparency, and honest testing standards at Multan Tech Accessories. Reliable tech shopping in Pakistan.',
            'canonical' => mta_url('/editorial-and-pricing-policy'),
        ]));
    }

    public function faq(): Response
    {
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'FAQ', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/pages/faq', [
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Frequently Asked Questions FAQ'),
            'meta_description' => 'Find clear answers to common questions about delivery times, Cash on Delivery, warranty claims, and authentic computer accessories at Multan Tech Accessories.',
            'canonical' => mta_url('/faq'),
        ]));
    }

    public function privacy(): Response
    {
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Privacy Policy', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/pages/privacy', [
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Privacy and Data Protection Policy'),
            'meta_description' => 'Learn how Multan Tech Accessories protects your customer privacy, contact details, phone numbers, and delivery data with strict security and confidentiality.',
            'canonical' => mta_url('/privacy-policy'),
        ]));
    }

    public function terms(): Response
    {
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Terms & Conditions', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/pages/terms', [
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Customer Terms and Conditions Policy'),
            'meta_description' => 'Read the official terms of service and customer conditions for purchasing computer and smartphone accessories at Multan Tech Accessories with confidence.',
            'canonical' => mta_url('/terms-and-conditions'),
        ]));
    }

    public function cookie(): Response
    {
        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Cookie Policy', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/pages/cookie', [
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('Cookie Policy & Privacy Preferences'),
            'meta_description' => 'Learn how Multan Tech Accessories uses cookies, Google Consent Mode v2, and privacy controls to deliver a safe e-commerce shopping experience in Pakistan.',
            'canonical' => mta_url('/cookie-policy'),
        ]));
    }

    public function sitemap(): Response
    {
        $db = Database::getInstance();
        $categories = $db->fetchAll(
            "SELECT c.id, c.slug, c.name, COUNT(p.id) AS product_count 
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
             WHERE c.is_active = 1 
             GROUP BY c.id
             HAVING product_count >= 1
             ORDER BY c.sort_order ASC, c.name ASC"
        );

        $brands = $db->fetchAll(
            "SELECT b.id, b.slug, b.name, COUNT(p.id) AS product_count 
             FROM brands b
             LEFT JOIN products p ON p.brand_id = b.id AND p.is_active = 1
             WHERE b.is_active = 1
             GROUP BY b.id
             HAVING product_count >= 4
             ORDER BY product_count DESC, b.name ASC"
        );

        $guides = $db->fetchAll(
            "SELECT id, slug, title, updated_at 
             FROM posts 
             WHERE status = 'published' 
             ORDER BY id DESC"
        );

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'HTML Sitemap', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/pages/sitemap', [
            'categories' => $categories,
            'brands' => $brands,
            'guides' => $guides,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('HTML Sitemap and Complete Directory'),
            'meta_description' => 'Browse the complete navigational directory of all verified products, hardware categories, brand hubs, buying guides, and policies at Multan Tech Accessories.',
            'canonical' => mta_url('/sitemap'),
        ]));
    }
}
