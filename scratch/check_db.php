<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = App\Core\Database::getInstance();
$counts = $db->fetchAll("SELECT count(*) as cnt, source FROM products GROUP BY source");
print_r($counts);
$samples = $db->fetchAll("SELECT id, supplier_id, title, primary_image_url FROM products WHERE supplier_id IS NOT NULL AND supplier_id != '' LIMIT 5");
print_r($samples);
