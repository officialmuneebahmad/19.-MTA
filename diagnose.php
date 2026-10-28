<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>PHP Environment Diagnosis</h1>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>PDO:</strong> " . (extension_loaded('pdo') ? 'Installed' : 'MISSING') . "</p>";
echo "<p><strong>PDO SQLite:</strong> " . (extension_loaded('pdo_sqlite') ? 'Installed' : 'MISSING') . "</p>";
echo "<p><strong>PDO MySQL:</strong> " . (extension_loaded('pdo_mysql') ? 'Installed' : 'MISSING') . "</p>";
echo "<p><strong>cURL:</strong> " . (extension_loaded('curl') ? 'Installed' : 'MISSING') . "</p>";
echo "<p><strong>MBString:</strong> " . (extension_loaded('mbstring') ? 'Installed' : 'MISSING') . "</p>";
echo "<p><strong>App Folder:</strong> " . (is_dir(__DIR__ . '/app') ? 'Found at ' . __DIR__ . '/app' : (is_dir(dirname(__DIR__) . '/app') ? 'Found at ' . dirname(__DIR__) . '/app' : 'NOT FOUND')) . "</p>";
echo "<p><strong>Config file:</strong> " . (file_exists(__DIR__ . '/app/config.php') ? 'Found' : (file_exists(dirname(__DIR__) . '/app/config.php') ? 'Found in parent' : 'NOT FOUND')) . "</p>";
echo "<p><strong>Install lock:</strong> " . (file_exists(__DIR__ . '/app/storage/install.lock') ? 'Found' : (file_exists(dirname(__DIR__) . '/app/storage/install.lock') ? 'Found in parent' : 'NOT FOUND')) . "</p>";
