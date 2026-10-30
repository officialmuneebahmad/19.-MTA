<?php
require __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$sCols = array_column($db->fetchAll("PRAGMA table_info(settings)"), 'name');
echo "Settings columns: " . implode(', ', $sCols) . PHP_EOL;
$rows = $db->fetchAll("SELECT * FROM settings LIMIT 5");
print_r($rows);
