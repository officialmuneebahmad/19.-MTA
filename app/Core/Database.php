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
                $cols = $this->pdo->query("PRAGMA table_info(products)")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($cols)) {
                    $names = array_column($cols, 'name');
                    if (!in_array('stock_status', $names, true)) {
                        $this->pdo->exec("ALTER TABLE products ADD COLUMN stock_status TEXT NOT NULL DEFAULT 'in_stock';");
                    }
                }
            } elseif ($this->driver === 'mysql') {
                $check = $this->pdo->query("SHOW COLUMNS FROM products LIKE 'stock_status'")->fetch();
                if (!$check) {
                    $this->pdo->exec("ALTER TABLE products ADD COLUMN stock_status VARCHAR(20) NOT NULL DEFAULT 'in_stock';");
                }
            }

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
