<?php
/**
 * Multan Tech Accessories — Security Test Suite
 */

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Security;
use App\Core\Session;

class SecurityTest
{
    public function run(): void
    {
        echo "\n=== Running Application Security Tests ===\n";

        $this->testHtmlAllowlistSanitizer();
        $this->testPakistaniPhoneNormalizer();
        $this->testSsrfUrlValidation();
        $this->testCsrfTokenValidation();
        $this->testSupplierWholesalePriceStripping();

        echo "All Security tests passed successfully!\n";
    }

    private function testSupplierWholesalePriceStripping(): void
    {
        echo "Testing Security: Supplier Wholesale Price & Metadata Stripping... ";

        // 1. Exact Razer Goliathus sample from live site
        $razerRaw = "Price 300&nbsp;Wt=0.5Description:Lab-engineered by Razer's scientists and field-tested by the biggest names in esports.";
        $cleaned1 = mta_clean_description_html($razerRaw);
        assert(!str_contains($cleaned1, 'Price 300'), 'Must remove "Price 300"');
        assert(!str_contains($cleaned1, 'Wt=0.5'), 'Must remove "Wt=0.5"');
        assert(!str_contains($cleaned1, 'Description:'), 'Must remove "Description:"');
        assert(str_starts_with($cleaned1, "Lab-engineered by Razer's scientists"), 'Must start with clean product copy');

        // 2. HTML paragraph wrapping
        $wrapped = "<p>Price 300&nbsp;Wt=0.5Description:Lab-engineered by Razer's scientists</p>";
        $cleaned2 = mta_clean_description_html($wrapped);
        assert(!str_contains($cleaned2, 'Price 300'));
        assert(str_contains($cleaned2, "<p>Lab-engineered by Razer's scientists</p>"));

        // 3. Security::sanitizeSupplierHtml
        $cleaned3 = Security::sanitizeSupplierHtml($razerRaw);
        assert(!str_contains($cleaned3, 'Price 300'));
        assert(!str_contains($cleaned3, 'Wt=0.5'));
        assert(!str_contains($cleaned3, 'Description:'));

        // 4. Preserve legitimate editorial text mentioning price and weight
        $legit = "<p>Premium gaming accessory offering top quality at an affordable price in Pakistan. Weight: lightweight and durable.</p>";
        $cleaned4 = mta_clean_description_html($legit);
        assert(str_contains($cleaned4, 'affordable price in Pakistan'));
        assert(str_contains($cleaned4, 'Weight: lightweight'));

        echo "PASSED\n";
    }

    private function testHtmlAllowlistSanitizer(): void
    {
        echo "Testing Security: Strict HTML Allowlist Sanitizer... ";
        
        $malicious = '<p>Good text</p><script>alert("hacked")</script><a href="javascript:alert(1)">Click</a><img src="x" onerror="alert(2)">';
        $cleaned = Security::sanitizeHtml($malicious);

        assert(!str_contains($cleaned, '<script>'), 'Must strip <script> tag');
        assert(!str_contains($cleaned, 'javascript:'), 'Must strip javascript: pseudo-protocol');
        assert(!str_contains($cleaned, 'onerror'), 'Must strip onerror attribute');
        assert(str_contains($cleaned, '<p>Good text</p>'), 'Must preserve safe paragraph tag');

        echo "PASSED\n";
    }

    private function testPakistaniPhoneNormalizer(): void
    {
        echo "Testing Security: Pakistani Mobile Phone Normalizer... ";

        // Valid formats
        $t1 = Security::normalizePakistaniPhone('03070655275');
        assert($t1['valid'] === true);
        assert($t1['local'] === '03070655275');
        assert($t1['international'] === '+923070655275');

        $t2 = Security::normalizePakistaniPhone('+92 307 0655275');
        assert($t2['valid'] === true);
        assert($t2['local'] === '03070655275');

        $t3 = Security::normalizePakistaniPhone('923001234567');
        assert($t3['valid'] === true);
        assert($t3['local'] === '03001234567');

        // Invalid formats
        $t4 = Security::normalizePakistaniPhone('123456');
        assert($t4['valid'] === false, 'Too short must be invalid');

        $t5 = Security::normalizePakistaniPhone('+15551234567');
        assert($t5['valid'] === false, 'Non-Pakistani number must be invalid');

        echo "PASSED\n";
    }

    private function testSsrfUrlValidation(): void
    {
        echo "Testing Security: SSRF Private/Loopback Network Shield... ";

        // Private / Loopback IPs must be blocked
        assert(Security::isSafeUrl('http://127.0.0.1/admin') === false, '127.0.0.1 must be blocked');
        assert(Security::isSafeUrl('http://localhost:8080/data') === false, 'localhost must be blocked');
        assert(Security::isSafeUrl('http://10.0.0.1/secret') === false, '10.0.0.0/8 must be blocked');
        assert(Security::isSafeUrl('http://192.168.1.1/router') === false, '192.168.0.0/16 must be blocked');
        assert(Security::isSafeUrl('http://169.254.169.254/latest/meta-data') === false, 'AWS/Cloud metadata must be blocked');
        assert(Security::isSafeUrl('file:///etc/passwd') === false, 'file:// scheme must be blocked');

        // Legitimate public domain
        assert(Security::isSafeUrl('https://example.com/product.html') === true, 'Public HTTPS domain must be allowed');

        echo "PASSED\n";
    }

    private function testCsrfTokenValidation(): void
    {
        echo "Testing Security: CSRF Token Generation & Validation... ";
        
        Session::start();
        $token = Session::csrfToken();
        assert(strlen($token) === 64, 'CSRF token must be 64-character hex string');
        assert(Session::validateCsrf($token) === true, 'Valid token must pass');
        assert(Session::validateCsrf('invalid-tampered-token') === false, 'Invalid token must fail');
        assert(Session::validateCsrf('') === false, 'Empty token must fail');

        echo "PASSED\n";
    }
}

$test = new SecurityTest();
$test->run();
