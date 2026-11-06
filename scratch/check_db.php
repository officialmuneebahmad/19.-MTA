<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = App\Core\Database::getInstance();
$row = $db->fetch("SELECT id, supplier_id, created_at, updated_at, last_seen_at FROM products ORDER BY id DESC LIMIT 1");
print_r($row);

$rows = $db->fetchAll("SELECT id, supplier_id, title, created_at FROM products ORDER BY CAST(supplier_id AS INTEGER) DESC LIMIT 10");
echo "\nTop 10 newest by supplier_id:\n";
foreach ($rows as $r) {
    echo "ID: {$r['id']} | SuppID: {$r['supplier_id']} | Title: {$r['title']}\n";
}
