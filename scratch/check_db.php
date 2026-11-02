<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = App\Core\Database::getInstance();
$cols = $db->fetchAll("PRAGMA table_info(orders)");
foreach ($cols as $c) {
    echo $c['name'] . " (" . $c['type'] . ")\n";
}
