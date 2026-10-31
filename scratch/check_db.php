<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = App\Core\Database::getInstance();
$u = $db->fetch("SELECT id, name, email, role, failed_logins, locked_until, must_change_password, password_hash FROM users WHERE id = 1");
echo json_encode($u, JSON_PRETTY_PRINT);
