<?php
/**
 * Multan Tech Accessories — Database Layer
 * PDO Prepared Statements with MySQL & SQLite local dev fallback
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?self $instance = null;
    private PDO $pdo;
    private string $driver = 'mysql';

    private function __construct()
    {
        $dbConfig = mta_config('db', []);
        $configuredDriver = $dbConfig['driver'] ?? 'mysql';

        if ($configuredDriver === 'sqlite') {
            $this->connectSqlite($dbConfig['sqlite_path'] ?? (MTA_STORAGE . '/database.sqlite'));
            return;
        }

        // Attempt MySQL connection if extension is loaded
        try {
            if (!extension_loaded('pdo_mysql')) {
                throw new \RuntimeException('pdo_mysql extension is not loaded');
            }

            $host = $dbConfig['host'] ?? '127.0.0.1';
            $port = (int)($dbConfig['port'] ?? 3306);
            $dbName = $dbConfig['database'] ?? 'multantech';
            $user = $dbConfig['username'] ?? 'root';
            $pass = $dbConfig['password'] ?? '';
            $charset = $dbConfig['charset'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
                $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$charset} COLLATE {$charset}_unicode_ci";
            }

            $this->pdo = new PDO($dsn, $user, $pass, $options);
            $this->driver = 'mysql';
            $this->ensureSchemaIntegrity();
        } catch (\Throwable $e) {
            // If MySQL is down, extension is missing, or in local development without MySQL server, fallback to SQLite
            $sqlitePath = $dbConfig['sqlite_path'] ?? (MTA_STORAGE . '/database.sqlite');
            $this->connectSqlite($sqlitePath);
        }
    }

    private function connectSqlite(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $isNew = !file_exists($path) || filesize($path) === 0;

        $this->pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $this->driver = 'sqlite';

        // Enable foreign keys and WAL mode for SQLite
        $this->pdo->exec('PRAGMA foreign_keys = ON;');
        $this->pdo->exec('PRAGMA journal_mode = WAL;');

        if ($isNew) {
            $schemaFile = MTA_APP . '/database/sqlite_schema.sql';
            if (file_exists($schemaFile)) {
                $this->pdo->exec(file_get_contents($schemaFile));
            }
            $seedSettings = MTA_APP . '/database/seed_settings.sql';
            if (file_exists($seedSettings)) {
                $sql = file_get_contents($seedSettings);
                $sql = str_replace(['`settings`', '`authors`'], ['settings', 'authors'], $sql);
                try {
                    $this->pdo->exec($sql);
                } catch (\Throwable) {}
            }
            $seedCats = MTA_APP . '/database/seed_categories.sql';
            if (file_exists($seedCats)) {
                $sql = file_get_contents($seedCats);
                $sql = str_replace('`categories`', 'categories', $sql);
                try {
                    $this->pdo->exec($sql);
                } catch (\Throwable) {}
            }
        }

        // Always ensure schema integrity for existing databases
        $this->ensureSchemaIntegrity();
    }

    /**
     * Auto-migrate missing columns for existing SQLite / MySQL installations
     */
    private function ensureSchemaIntegrity(): void
    {
        try {
            if ($this->driver === 'sqlite') {
                // Products
                $pCols = array_column($this->pdo->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_ASSOC), 'name');
                if (!empty($pCols) && !in_array('stock_status', $pCols, true)) {
                    $this->pdo->exec("ALTER TABLE products ADD COLUMN stock_status TEXT NOT NULL DEFAULT 'in_stock';");
                }

                // Orders
                $oCols = array_column($this->pdo->query("PRAGMA table_info(orders)")->fetchAll(PDO::FETCH_ASSOC), 'name');
                if (!empty($oCols)) {
                    if (!in_array('advance_amount', $oCols, true)) {
                        $this->pdo->exec("ALTER TABLE orders ADD COLUMN advance_amount REAL NOT NULL DEFAULT 0.00;");
                    }
                    if (!in_array('advance_verified_at', $oCols, true)) {
                        $this->pdo->exec("ALTER TABLE orders ADD COLUMN advance_verified_at DATETIME NULL;");
                    }
                    if (!in_array('cod_due', $oCols, true)) {
                        $this->pdo->exec("ALTER TABLE orders ADD COLUMN cod_due REAL NOT NULL DEFAULT 0.00;");
                    }
                }

                // Users
                $uCols = array_column($this->pdo->query("PRAGMA table_info(users)")->fetchAll(PDO::FETCH_ASSOC), 'name');
                if (!empty($uCols)) {
                    if (!in_array('totp_secret', $uCols, true)) {
                        $this->pdo->exec("ALTER TABLE users ADD COLUMN totp_secret TEXT NULL;");
                    }
                    if (!in_array('backup_codes_json', $uCols, true)) {
                        $this->pdo->exec("ALTER TABLE users ADD COLUMN backup_codes_json TEXT NULL;");
                    }
                    if (!in_array('is_2fa_enabled', $uCols, true)) {
                        $this->pdo->exec("ALTER TABLE users ADD COLUMN is_2fa_enabled INTEGER NOT NULL DEFAULT 0;");
                    }
                    if (!in_array('must_change_password', $uCols, true)) {
                        $this->pdo->exec("ALTER TABLE users ADD COLUMN must_change_password INTEGER NOT NULL DEFAULT 0;");
                    }
                    if (!in_array('failed_logins', $uCols, true)) {
                        $this->pdo->exec("ALTER TABLE users ADD COLUMN failed_logins INTEGER NOT NULL DEFAULT 0;");
                    }
                    if (!in_array('locked_until', $uCols, true)) {
                        $this->pdo->exec("ALTER TABLE users ADD COLUMN locked_until DATETIME NULL;");
                    }
                    if (!in_array('last_reauth_at', $uCols, true)) {
                        $this->pdo->exec("ALTER TABLE users ADD COLUMN last_reauth_at DATETIME NULL;");
                    }
                }

                // WhatsApp Inquiries Table
                $this->pdo->exec("CREATE TABLE IF NOT EXISTS whatsapp_inquiries (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    product_id INTEGER NULL,
                    product_title TEXT NULL,
                    page_url TEXT NULL,
                    ip_address TEXT NULL,
                    user_agent TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                );");

                // Brands columns for logos and featured status (Task 3)
                $bCols = array_column($this->pdo->query("PRAGMA table_info(brands)")->fetchAll(PDO::FETCH_ASSOC), 'name');
                if (!empty($bCols)) {
                    if (!in_array('logo_path', $bCols, true)) {
                        $this->pdo->exec("ALTER TABLE brands ADD COLUMN logo_path TEXT NULL;");
                    }
                    if (!in_array('logo_dark_path', $bCols, true)) {
                        $this->pdo->exec("ALTER TABLE brands ADD COLUMN logo_dark_path TEXT NULL;");
                    }
                    if (!in_array('logo_alt', $bCols, true)) {
                        $this->pdo->exec("ALTER TABLE brands ADD COLUMN logo_alt TEXT NULL;");
                    }
                    if (!in_array('is_featured', $bCols, true)) {
                        $this->pdo->exec("ALTER TABLE brands ADD COLUMN is_featured INTEGER NOT NULL DEFAULT 0;");
                    }
                    if (!in_array('featured_sort', $bCols, true)) {
                        $this->pdo->exec("ALTER TABLE brands ADD COLUMN featured_sort INTEGER NOT NULL DEFAULT 0;");
                    }
                }

                // Audit Log Table
                $this->pdo->exec("CREATE TABLE IF NOT EXISTS audit_log (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NULL,
                    action TEXT NOT NULL,
                    entity_type TEXT NULL,
                    entity_id TEXT NULL,
                    details TEXT NULL,
                    ip_address TEXT NULL,
                    user_agent TEXT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                );");

            } elseif ($this->driver === 'mysql') {
                $check = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'stock_status'")->fetch();
                if (!$check) {
                    $this->pdo->exec("ALTER TABLE products ADD COLUMN stock_status VARCHAR(20) NOT NULL DEFAULT 'in_stock';");
                }

                // Orders
                $chkAdv = $this->pdo->query("SHOW COLUMNS FROM orders LIKE 'advance_amount'")->fetch();
                if (!$chkAdv) {
                    $this->pdo->exec("ALTER TABLE orders ADD COLUMN advance_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00;");
                    $this->pdo->exec("ALTER TABLE orders ADD COLUMN advance_verified_at DATETIME NULL;");
                    $this->pdo->exec("ALTER TABLE orders ADD COLUMN cod_due DECIMAL(12,2) NOT NULL DEFAULT 0.00;");
                }

                // Users
                $chkTotp = $this->pdo->query("SHOW COLUMNS FROM users LIKE 'totp_secret'")->fetch();
                if (!$chkTotp) {
                    $this->pdo->exec("ALTER TABLE users ADD COLUMN totp_secret VARCHAR(64) NULL;");
                    $this->pdo->exec("ALTER TABLE users ADD COLUMN backup_codes_json TEXT NULL;");
                    $this->pdo->exec("ALTER TABLE users ADD COLUMN is_2fa_enabled TINYINT(1) NOT NULL DEFAULT 0;");
                    $this->pdo->exec("ALTER TABLE users ADD COLUMN must_change_password TINYINT(1) NOT NULL DEFAULT 0;");
                    $this->pdo->exec("ALTER TABLE users ADD COLUMN failed_logins INT NOT NULL DEFAULT 0;");
                    $this->pdo->exec("ALTER TABLE users ADD COLUMN locked_until DATETIME NULL;");
                    $this->pdo->exec("ALTER TABLE users ADD COLUMN last_reauth_at DATETIME NULL;");
                }

                // WhatsApp Inquiries Table
                $this->pdo->exec("CREATE TABLE IF NOT EXISTS whatsapp_inquiries (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    product_id INT NULL,
                    product_title VARCHAR(255) NULL,
                    page_url VARCHAR(500) NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent VARCHAR(255) NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    KEY idx_inquiry_created (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

                // Audit Log Table
                $this->pdo->exec("CREATE TABLE IF NOT EXISTS audit_log (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NULL,
                    action VARCHAR(100) NOT NULL,
                    entity_type VARCHAR(50) NULL,
                    entity_id VARCHAR(50) NULL,
                    details TEXT NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent VARCHAR(255) NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    KEY idx_audit_created (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

                // Brands columns for MySQL (Task 3)
                $chkBrandLogo = $this->pdo->query("SHOW COLUMNS FROM brands LIKE 'logo_path'")->fetch();
                if (!$chkBrandLogo) {
                    $this->pdo->exec("ALTER TABLE brands ADD COLUMN logo_path VARCHAR(255) NULL;");
                    $this->pdo->exec("ALTER TABLE brands ADD COLUMN logo_dark_path VARCHAR(255) NULL;");
                    $this->pdo->exec("ALTER TABLE brands ADD COLUMN logo_alt VARCHAR(255) NULL;");
                    $this->pdo->exec("ALTER TABLE brands ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0;");
                    $this->pdo->exec("ALTER TABLE brands ADD COLUMN featured_sort INT NOT NULL DEFAULT 0;");
                }
            }

            // Ensure show_wa_on_product_cards setting exists (default 0 / OFF)
            try {
                $chkSetting = $this->pdo->query("SELECT `key` FROM settings WHERE `key` = 'show_wa_on_product_cards' LIMIT 1")->fetch();
                if (!$chkSetting) {
                    $this->pdo->exec("INSERT INTO settings (`key`, value, description, group_name, updated_at) VALUES ('show_wa_on_product_cards', '0', 'Show WhatsApp order button on product cards', 'storefront', CURRENT_TIMESTAMP);");
                }
            } catch (\Throwable) {}

            // Sync cod_due on orders where default is 0
            $this->pdo->exec("UPDATE orders SET cod_due = MAX(0, total - advance_amount) WHERE cod_due = 0 AND total > 0;");

            // Ensure obsolete placeholder tokens in settings table are replaced with registered token
            $this->pdo->exec("UPDATE settings SET value = 'f1f3b0294c3f7c2328e41d42e59d2877' WHERE key = 'supplier_token' AND (value = 'd65090a5f72172720b1806a9d37aa9a4' OR value LIKE 'TODO%' OR value = '');");
            $this->pdo->exec("UPDATE settings SET value = 'https://api.computerzone.pk/products' WHERE key = 'supplier_api_url' AND value LIKE '%127.0.0.1%';");
            $this->pdo->exec("UPDATE settings SET value = 'raw' WHERE key = 'supplier_auth_header_format' AND value != 'raw';");
        } catch (\Throwable) {
            // Table may not exist yet on fresh installations prior to schema creation
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function fetchColumn(string $sql, array $params = [], int $column = 0): mixed
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($column);
    }

    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    /**
     * Reusable pagination helper
     */
    public function paginate(string $baseSql, array $params = [], int $page = 1, int $perPage = 24): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        // Clean count query
        $countSql = "SELECT COUNT(*) FROM (" . preg_replace('/ORDER BY\s+[\w\.\,\s`]+$/i', '', $baseSql) . ") AS _sub_count";
        $totalItems = (int)$this->fetchColumn($countSql, $params);

        $totalPages = (int)ceil($totalItems / $perPage);

        $limitSql = "{$baseSql} LIMIT {$perPage} OFFSET {$offset}";
        $items = $this->fetchAll($limitSql, $params);

        return [
            'data' => $items,
            'total' => $totalItems,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, $totalPages),
            'has_more' => $page < $totalPages,
        ];
    }
}
