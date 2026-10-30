<?php
require __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$longest = $db->fetch("SELECT * FROM products ORDER BY LENGTH(title) DESC LIMIT 1");
$router = new \App\Core\Router();
require MTA_APP . '/routes.php';
$req = new \App\Core\Request('GET', '/products/' . $longest['slug'], [], [], [], ['HTTP_HOST' => '127.0.0.1:8000', 'REQUEST_URI' => '/products/' . $longest['slug']]);
$resp = $router->dispatch($req);
$body = $resp->getBody();
echo "Title tag: " . (preg_match('/<title>(.*?)<\/title>/', $body, $t) ? $t[1] : 'none') . PHP_EOL;
echo "Length: " . strlen($body) . PHP_EOL;
if (str_contains($body, 'Full-Page Cache Hit')) {
    echo "Full-Page Cache Hit!\n";
} else {
    echo "Dynamic generation\n";
}
