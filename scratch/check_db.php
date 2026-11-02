<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = App\Core\Database::getInstance();
$users = $db->fetchAll("SELECT id, name, email, role, must_change_password FROM users");
print_r($users);
