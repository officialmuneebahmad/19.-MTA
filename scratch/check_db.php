<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = App\Core\Database::getInstance();
$cols = $db->fetchAll("PRAGMA table_info(users)");
echo json_encode($cols, JSON_PRETTY_PRINT);
