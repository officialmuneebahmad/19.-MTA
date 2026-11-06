<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$posts = $db->fetchAll("SELECT id, title, slug, status FROM posts");
print_r($posts);
