<?php
require __DIR__ . '/../app/bootstrap.php';
$router = new \App\Core\Router();
require MTA_APP . '/routes.php';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$resp = $router->dispatch(new \App\Core\Request());
$html = $resp->getBody();
$pos = 0;
while (($pos = strpos($html, 'M18.403 5.638', $pos)) !== false) {
    echo "Found at offset $pos:\n";
    echo substr($html, max(0, $pos - 150), 300) . "\n---\n";
    $pos += 14;
}

