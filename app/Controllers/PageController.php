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
            'meta_description' => 'Learn about Multan Tech Accessories, founded and developed by Muneeb Ahmad in Multan, Pakistan. Authentic hardware, transparent pricing.',
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
            'meta_title' => SeoService::title('Contact Us & Store Location'),
            'meta_description' => 'Contact Multan Tech Accessories on Sher Shah Road, Multan. Direct WhatsApp: 0307-0655275. Fast customer support for Pakistani buyers.',
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
            'meta_title' => SeoService::title('Shipping & Delivery Policy'),
            'meta_description' => 'Delivery details: Rs. 100 in Multan, Rs. 250 across Pakistan. Free delivery on orders above Rs. 5,000 and on second orders.',
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
            'meta_title' => SeoService::title('7-Day Checking Warranty & Returns'),
            'meta_description' => 'Our 7-day checking warranty and hassle-free returns policy for tech accessories at Multan Tech Accessories.',
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
            'meta_title' => SeoService::title('Editorial & Pricing Policy'),
            'meta_description' => 'Editorial integrity, supplier pricing transparency, and honest testing standards at Multan Tech Accessories.',
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
            'meta_title' => SeoService::title('Frequently Asked Questions (FAQ)'),
            'meta_description' => 'Common questions about delivery times, COD, warranty, and authentic tech accessories in Pakistan.',
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
            'meta_title' => SeoService::title('Privacy Policy'),
            'meta_description' => 'How Multan Tech Accessories protects your customer privacy, phone numbers, and delivery data.',
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
            'meta_title' => SeoService::title('Terms & Conditions'),
            'meta_description' => 'Terms of service and customer conditions for purchasing tech accessories at Multan Tech Accessories.',
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
            'meta_title' => SeoService::title('Cookie Policy'),
            'meta_description' => 'Our minimal, privacy-first cookie policy at Multan Tech Accessories. No intrusive third-party ad trackers.',
            'canonical' => mta_url('/cookie-policy'),
        ]));
    }

    public function sitemap(): Response
    {
        $db = Database::getInstance();
        $categories = $db->fetchAll(
            "SELECT id, slug, name, product_count 
             FROM categories 
             WHERE parent_id IS NULL AND is_active = 1 
             ORDER BY sort_order ASC, name ASC"
        );

        $breadcrumbs = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'HTML Sitemap', 'url' => null],
        ];

        $resp = new Response();
        return $resp->html(View::render('storefront/pages/sitemap', [
            'categories' => $categories,
            'breadcrumbs' => $breadcrumbs,
            'meta_title' => SeoService::title('HTML Sitemap — All Categories & Pages'),
            'meta_description' => 'Complete navigational directory of all products, categories, guides, and store policies at Multan Tech Accessories.',
            'canonical' => mta_url('/sitemap'),
        ]));
    }
}
