<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = App\Core\Database::getInstance();
$rows = $db->fetchAll("SELECT id, title, sku FROM products WHERE LOWER(title) LIKE '%speedx%' OR LOWER(title) LIKE '%sp-%' LIMIT 10");
foreach ($rows as $r) {
    echo "ID: {$r['id']} | Title: {$r['title']}\n";
}
