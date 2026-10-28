<?php
/**
 * Multan Tech Accessories — Mock Supplier Server
 * Simulates api.computerzone.pk with verified response shape and scenarios
 */

declare(strict_types=1);

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH) ?: '/';
$scenario = $_GET['scenario'] ?? 'normal';

// Check Auth Header (configurable raw or bearer)
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

header('Content-Type: application/json; charset=UTF-8');

if ($path !== '/products') {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'code' => 404, 'message' => 'Endpoint Not Found.']);
    exit;
}

if ($scenario === 'invalid_json') {
    http_response_code(200);
    echo '{ "status": "success", "code": 200, "Products": [ incomplete_json...';
    exit;
}

if ($scenario === 'error_status') {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'code' => 500, 'message' => 'Supplier server database failure.']);
    exit;
}

$dataFile = __DIR__ . '/products.json';
if (!file_exists($dataFile)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'code' => 500, 'message' => 'products.json not generated.']);
    exit;
}

$json = file_get_contents($dataFile);
$data = json_decode($json, true);

if ($scenario === 'truncated') {
    // Return only 100 products to trigger < 50% safety guard
    $data['Products'] = array_slice($data['Products'], 0, 100);
} elseif ($scenario === 'missing_fields') {
    // Inject two invalid rows that must be skipped and logged
    array_unshift($data['Products'], [
        'ProductId' => null, // Missing ID
        'ProductTitle' => 'Invalid Product Missing ID',
        'ProductPrice' => '1000',
    ]);
    array_unshift($data['Products'], [
        'ProductId' => '99999',
        'ProductTitle' => '', // Empty Title
        'ProductPrice' => '2000',
    ]);
}

echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
