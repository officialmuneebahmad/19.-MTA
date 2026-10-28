<?php
/**
 * Multan Tech Accessories — Production Installation Wizard
 * MultanTech.store
 * 
 * Checks environment, provisions database schema, seeds default settings,
 * creates the owner account with a cryptographically secure 20-character password,
 * and generates /app/config.php.
 */

declare(strict_types=1);

define('MTA_INSTALLER', true);
define('MTA_APP_DIR', is_dir(__DIR__ . '/app') ? (__DIR__ . '/app') : (dirname(__DIR__) . '/app'));
define('MTA_STORAGE_DIR', MTA_APP_DIR . '/storage');
define('MTA_LOCK_FILE', MTA_STORAGE_DIR . '/install.lock');
define('MTA_CONFIG_FILE', MTA_APP_DIR . '/config.php');

// Security Guard: Prevent re-running if locked
if (file_exists(MTA_LOCK_FILE)) {
    http_response_code(403);
    die('<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Installer Locked</title><style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;background:#FAF9F5;color:#141413;text-align:center;} .box{background:#fff;padding:2rem;border-radius:12px;border:1px solid #E8E6DC;max-width:500px;}</style></head><body><div class="box"><h2>Installer Locked</h2><p>Multan Tech Accessories has already been installed. To reinstall, delete <code>/app/storage/install.lock</code>.</p><a href="/" style="display:inline-block;margin-top:1rem;color:#B8532F;font-weight:600;">Visit Storefront &rarr;</a></div></body></html>');
}

// -------------------------------------------------------------
// System Requirements Checklist
// -------------------------------------------------------------
$requirements = [
    'PHP Version (>= 8.1)' => [
        'pass' => version_compare(PHP_VERSION, '8.1.0', '>='),
        'current' => PHP_VERSION,
        'required' => '8.1.0+',
    ],
    'PDO Extension' => [
        'pass' => extension_loaded('pdo'),
        'current' => extension_loaded('pdo') ? 'Installed' : 'Missing',
        'required' => 'Required',
    ],
    'PDO MySQL / SQLite' => [
        'pass' => extension_loaded('pdo_mysql') || extension_loaded('pdo_sqlite'),
        'current' => (extension_loaded('pdo_mysql') ? 'pdo_mysql ' : '') . (extension_loaded('pdo_sqlite') ? 'pdo_sqlite' : ''),
        'required' => 'pdo_mysql or pdo_sqlite',
    ],
    'cURL Extension' => [
        'pass' => extension_loaded('curl'),
        'current' => extension_loaded('curl') ? 'Installed' : 'Missing',
        'required' => 'Required for API sync',
    ],
    'GD / WebP or Imagick' => [
        'pass' => (extension_loaded('gd') && function_exists('imagewebp')) || extension_loaded('imagick'),
        'current' => extension_loaded('gd') ? 'GD (WebP enabled)' : (extension_loaded('imagick') ? 'Imagick' : 'Missing'),
        'required' => 'Required for image resizing',
    ],
    'mbstring Extension' => [
        'pass' => extension_loaded('mbstring'),
        'current' => extension_loaded('mbstring') ? 'Installed' : 'Missing',
        'required' => 'Required',
    ],
    'fileinfo Extension' => [
        'pass' => extension_loaded('fileinfo'),
        'current' => extension_loaded('fileinfo') ? 'Installed' : 'Missing',
        'required' => 'Required for upload MIME check',
    ],
    'Storage Write Permission' => [
        'pass' => is_dir(MTA_STORAGE_DIR) && is_writable(MTA_STORAGE_DIR),
        'current' => is_writable(MTA_STORAGE_DIR) ? 'Writable' : 'Not Writable',
        'required' => 'Writable (/app/storage)',
    ],
];

$allRequirementsPassed = !in_array(false, array_column($requirements, 'pass'), true);

$error = null;
$success = null;
$generatedPassword = null;
$adminUrl = null;

// -------------------------------------------------------------
// Handle Installation Form Submission
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install') {
    if (!$allRequirementsPassed) {
        $error = 'Please resolve all server requirements before proceeding.';
    } else {
        $dbDriver = trim($_POST['db_driver'] ?? 'mysql');
        $dbHost = trim($_POST['db_host'] ?? '127.0.0.1');
        $dbPort = (int)($_POST['db_port'] ?? 3306);
        $dbName = trim($_POST['db_name'] ?? 'multantech');
        $dbUser = trim($_POST['db_user'] ?? 'root');
        $dbPass = $_POST['db_pass'] ?? '';
        $sqlitePath = trim($_POST['sqlite_path'] ?? MTA_STORAGE_DIR . '/database.sqlite');
        
        $ownerName = trim($_POST['owner_name'] ?? 'Muneeb Ahmad');
        $ownerEmail = trim($_POST['owner_email'] ?? '');
        $siteUrl = rtrim(trim($_POST['site_url'] ?? 'https://multantech.store'), '/');

        if (empty($ownerEmail) || !filter_var($ownerEmail, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please provide a valid administrator email address.';
        } else {
            try {
                // Test DB Connection
                $pdo = null;
                if ($dbDriver === 'sqlite') {
                    $dbDir = dirname($sqlitePath);
                    if (!is_dir($dbDir)) {
                        mkdir($dbDir, 0755, true);
                    }
                    $dsn = "sqlite:" . $sqlitePath;
                    $pdo = new PDO($dsn, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    $schemaFile = MTA_APP_DIR . '/database/sqlite_schema.sql';
                } else {
                    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
                    $pdo = new PDO($dsn, $dbUser, $dbPass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    $schemaFile = MTA_APP_DIR . '/database/schema.sql';
                }

                // Execute Database Schema
                if (!file_exists($schemaFile)) {
                    throw new RuntimeException("Schema file not found at: {$schemaFile}");
                }
                $schemaSql = file_get_contents($schemaFile);
                if ($dbDriver === 'sqlite') {
                    $pdo->exec($schemaSql);
                } else {
                    $pdo->exec($schemaSql);
                }

                // Seed Default Settings
                $seedSettingsFile = MTA_APP_DIR . '/database/seed_settings.sql';
                if (file_exists($seedSettingsFile)) {
                    $seedSql = file_get_contents($seedSettingsFile);
                    if ($dbDriver === 'sqlite') {
                        $seedSql = str_replace(['`settings`', '`authors`'], ['settings', 'authors'], $seedSql);
                    }
                    try {
                        $pdo->exec($seedSql);
                    } catch (\Throwable $e) {
                        // Ignore duplicate key if re-running
                    }
                }

                // Seed Default Categories
                $seedCatsFile = MTA_APP_DIR . '/database/seed_categories.sql';
                if (file_exists($seedCatsFile)) {
                    $catsSql = file_get_contents($seedCatsFile);
                    if ($dbDriver === 'sqlite') {
                        $catsSql = str_replace('`categories`', 'categories', $catsSql);
                    }
                    try {
                        $pdo->exec($catsSql);
                    } catch (\Throwable $e) {
                        // Ignore duplicate key if re-running
                    }
                }

                // Generate Cryptographically Secure 20-character Password
                $chars = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ!@#$%&*';
                $passwordLength = 20;
                $generatedPassword = '';
                $maxIndex = strlen($chars) - 1;
                for ($i = 0; $i < $passwordLength; $i++) {
                    $generatedPassword .= $chars[random_int(0, $maxIndex)];
                }

                // Hash using Argon2id (or BCRYPT fallback if Argon2id is unsupported)
                $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
                $passwordHash = password_hash($generatedPassword, $algo);

                // Insert Owner Account
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, must_change_password) VALUES (?, ?, ?, 'owner', 1)");
                $stmt->execute([$ownerName, $ownerEmail, $passwordHash]);

                // Generate Obfuscated Admin Path
                $randomAdminPath = 'mta-control-' . bin2hex(random_bytes(4));
                $stmtUpdateAdmin = $pdo->prepare("UPDATE settings SET value = ? WHERE key = 'admin_path'");
                $stmtUpdateAdmin->execute([$randomAdminPath]);

                // Update Site URL in settings
                $stmtUpdateUrl = $pdo->prepare("UPDATE settings SET value = ? WHERE key = 'site_url'");
                $stmtUpdateUrl->execute([$siteUrl]);

                // Generate APP_KEY
                $appKey = 'base64:' . base64_encode(random_bytes(32));

                // Write /app/config.php
                $configContent = "<?php\n"
                    . "/**\n"
                    . " * Multan Tech Accessories — Master Production Configuration\n"
                    . " * Generated automatically by install.php on " . date('Y-m-d H:i:s') . "\n"
                    . " */\n\n"
                    . "declare(strict_types=1);\n\n"
                    . "return [\n"
                    . "    'app' => [\n"
                    . "        'name' => 'Multan Tech Accessories',\n"
                    . "        'url' => '{$siteUrl}',\n"
                    . "        'key' => '{$appKey}',\n"
                    . "        'env' => 'production',\n"
                    . "        'debug' => false,\n"
                    . "        'admin_path' => '{$randomAdminPath}',\n"
                    . "    ],\n"
                    . "    'db' => [\n"
                    . "        'driver' => '{$dbDriver}',\n"
                    . "        'host' => '{$dbHost}',\n"
                    . "        'port' => {$dbPort},\n"
                    . "        'database' => '{$dbName}',\n"
                    . "        'username' => '{$dbUser}',\n"
                    . "        'password' => " . var_export($dbPass, true) . ",\n"
                    . "        'sqlite_path' => " . var_export($sqlitePath, true) . ",\n"
                    . "        'charset' => 'utf8mb4',\n"
                    . "    ],\n"
                    . "    'supplier' => [\n"
                    . "        'api_url' => 'http://127.0.0.1:8080/products',\n"
                    . "        'auth_header_format' => 'raw',\n"
                    . "        'token' => 'TODO-OWNER',\n"
                    . "        'timeout' => 120,\n"
                    . "    ],\n"
                    . "    'pricing' => [\n"
                    . "        'default_percent' => 10,\n"
                    . "        'default_fixed' => 0,\n"
                    . "        'round_to' => 10,\n"
                    . "    ],\n"
                    . "    'delivery' => [\n"
                    . "        'free_threshold' => 5000,\n"
                    . "        'multan_charge' => 100,\n"
                    . "        'standard_charge' => 250,\n"
                    . "    ],\n"
                    . "];\n";

                file_put_contents(MTA_CONFIG_FILE, $configContent, LOCK_EX);

                // Write Lock File
                file_put_contents(MTA_LOCK_FILE, json_encode([
                    'installed_at' => date('c'),
                    'owner_email' => $ownerEmail,
                    'admin_path' => $randomAdminPath,
                ], JSON_PRETTY_PRINT));

                $adminUrl = '/' . $randomAdminPath . '/login';
                $success = true;

            } catch (\Throwable $e) {
                $error = 'Installation failed: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en-PK">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup Wizard — Multan Tech Accessories</title>
  <style>
    :root {
      --bg: #FAF9F5;
      --surface: #FFFFFF;
      --border: #E8E6DC;
      --text: #141413;
      --muted: #5E5D59;
      --accent: #B8532F;
      --success: #2E7D4F;
      --danger: #B3261E;
      --radius: 12px;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: var(--bg); color: var(--text); padding: 2rem 1rem; line-height: 1.5; }
    .container { max-width: 680px; margin: 0 auto; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 2.5rem 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    h1 { font-size: 1.75rem; margin-bottom: 0.5rem; font-weight: 700; }
    p.lead { color: var(--muted); margin-bottom: 1.75rem; font-size: 0.95rem; }
    .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; }
    .alert-danger { background: #FDE8E8; color: var(--danger); border: 1px solid #F8B4B4; }
    .alert-success { background: #DEF7EC; color: var(--success); border: 1px solid #BCF0DA; }
    .req-table { width: 100%; border-collapse: collapse; margin-bottom: 1.75rem; font-size: 0.875rem; }
    .req-table th, .req-table td { padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid var(--border); }
    .req-table th { background: var(--bg); font-weight: 600; }
    .badge-pass { color: var(--success); font-weight: 600; }
    .badge-fail { color: var(--danger); font-weight: 600; }
    .form-group { margin-bottom: 1.25rem; }
    label { display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem; }
    input[type="text"], input[type="email"], input[type="password"], input[type="number"], select {
      width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.95rem; background: #fff;
    }
    input:focus, select:focus { outline: none; border-color: var(--accent); ring: 2px var(--accent); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .btn { display: inline-block; background: var(--accent); color: #fff; font-weight: 600; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; font-size: 1rem; width: 100%; text-align: center; }
    .btn:hover { opacity: 0.95; }
    .password-card { background: #F0EEE6; border: 2px dashed var(--accent); border-radius: 8px; padding: 1.5rem; text-align: center; margin: 1.5rem 0; }
    .password-display { font-family: monospace; font-size: 1.35rem; font-weight: 700; color: #141413; letter-spacing: 1px; user-select: all; padding: 0.5rem; background: #fff; border-radius: 6px; display: inline-block; margin-top: 0.5rem; }
  </style>
</head>
<body>

<div class="container">
  <h1>Multan Tech Accessories</h1>
  <p class="lead">Production Setup Wizard &mdash; Founded & Developed by Muneeb Ahmad</p>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success">
      <strong>Installation Completed Successfully!</strong><br>
      The database has been provisioned and the owner credentials have been established.
    </div>

    <div class="password-card">
      <p style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: var(--accent);">
        Generated Owner Password (Shown Once Only)
      </p>
      <div class="password-display" id="pwd"><?= htmlspecialchars($generatedPassword) ?></div>
      <p style="font-size: 0.8rem; color: var(--muted); margin-top: 0.75rem;">
        &bull; Copy and save this password immediately in a password manager.<br>
        &bull; You will be required to change this password on first login.<br>
        &bull; For security, <code>/app/storage/install.lock</code> has been created.
      </p>
    </div>

    <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
      <a href="<?= htmlspecialchars($adminUrl) ?>" class="btn">Proceed to Admin Login &rarr;</a>
      <a href="/" style="text-align: center; color: var(--muted); text-decoration: none; font-size: 0.9rem;">View Public Storefront</a>
    </div>

  <?php else: ?>

    <h2 style="font-size: 1.15rem; margin-bottom: 0.75rem;">1. Server & Extension Check</h2>
    <table class="req-table">
      <thead>
        <tr>
          <th>Requirement</th>
          <th>Status</th>
          <th>Result</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($requirements as $name => $req): ?>
          <tr>
            <td><strong><?= htmlspecialchars($name) ?></strong></td>
            <td><?= htmlspecialchars($req['current']) ?></td>
            <td>
              <?php if ($req['pass']): ?>
                <span class="badge-pass">&#10003; Pass</span>
              <?php else: ?>
                <span class="badge-fail">&#10007; Missing</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <form method="POST" action="">
      <input type="hidden" name="action" value="install">

      <h2 style="font-size: 1.15rem; margin-bottom: 0.75rem;">2. Database Settings</h2>
      <div class="form-group">
        <label for="db_driver">Database Engine</label>
        <select name="db_driver" id="db_driver" onchange="toggleDbFields(this.value)">
          <option value="mysql" selected>MySQL / MariaDB (Recommended for Production)</option>
          <option value="sqlite">SQLite (Local Dev & Testing)</option>
        </select>
      </div>

      <div id="mysql_fields">
        <div class="form-row">
          <div class="form-group">
            <label for="db_host">DB Host</label>
            <input type="text" id="db_host" name="db_host" value="127.0.0.1" required>
          </div>
          <div class="form-group">
            <label for="db_port">DB Port</label>
            <input type="number" id="db_port" name="db_port" value="3306" required>
          </div>
        </div>

        <div class="form-group">
          <label for="db_name">Database Name</label>
          <input type="text" id="db_name" name="db_name" value="multantech" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="db_user">DB Username</label>
            <input type="text" id="db_user" name="db_user" value="root" required>
          </div>
          <div class="form-group">
            <label for="db_pass">DB Password</label>
            <input type="password" id="db_pass" name="db_pass" value="">
          </div>
        </div>
      </div>

      <div id="sqlite_fields" style="display: none;">
        <div class="form-group">
          <label for="sqlite_path">SQLite Database File Path</label>
          <input type="text" id="sqlite_path" name="sqlite_path" value="<?= htmlspecialchars(MTA_STORAGE_DIR . '/database.sqlite') ?>">
        </div>
      </div>

      <h2 style="font-size: 1.15rem; margin: 1.5rem 0 0.75rem;">3. Administrator & Store Setup</h2>
      <div class="form-row">
        <div class="form-group">
          <label for="owner_name">Owner Name</label>
          <input type="text" id="owner_name" name="owner_name" value="Muneeb Ahmad" required>
        </div>
        <div class="form-group">
          <label for="owner_email">Owner Email</label>
          <input type="email" id="owner_email" name="owner_email" placeholder="owner@multantech.store" required>
        </div>
      </div>

      <div class="form-group">
        <label for="site_url">Canonical Storefront URL</label>
        <input type="text" id="site_url" name="site_url" value="https://multantech.store" required>
      </div>

      <p style="font-size: 0.8rem; color: var(--muted); margin-bottom: 1.5rem;">
        * Upon installation, an unguessable 20-character password will be generated and displayed once. The installer will also configure an obfuscated admin URL (e.g. <code>/mta-control-xxxx/</code>) instead of standard paths.
      </p>

      <button type="submit" class="btn" <?= !$allRequirementsPassed ? 'disabled style="opacity:0.6; cursor:not-allowed;"' : '' ?>>
        Install Multan Tech Accessories
      </button>
    </form>

  <?php endif; ?>
</div>

<script>
function toggleDbFields(driver) {
  var mysqlDiv = document.getElementById('mysql_fields');
  var sqliteDiv = document.getElementById('sqlite_fields');
  if (driver === 'sqlite') {
    mysqlDiv.style.display = 'none';
    sqliteDiv.style.display = 'block';
  } else {
    mysqlDiv.style.display = 'block';
    sqliteDiv.style.display = 'none';
  }
}
</script>

</body>
</html>
