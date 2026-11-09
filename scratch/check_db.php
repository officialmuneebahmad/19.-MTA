<?php
require_once __DIR__ . '/../app/bootstrap.php';
$db = \App\Core\Database::getInstance();
$g = $db->fetch("SELECT slug, title, meta_title, meta_description, published_at, length(content_html) as len FROM posts WHERE slug = 'fast-chargers-buying-guide-pakistan'");
print_r($g);
