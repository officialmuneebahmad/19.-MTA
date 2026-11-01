<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = App\Core\Database::getInstance();
$cols = $db->fetchAll("PRAGMA table_info(product_images)");
print_r($cols);
