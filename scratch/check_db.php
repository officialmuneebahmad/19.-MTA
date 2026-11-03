<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = App\Core\Database::getInstance();
$rows = $db->fetchAll("SELECT * FROM products LIMIT 5");
foreach ($rows as $r) {
    echo $r['title'] . " => " . mta_product_image($r, 400) . "\n";
}
