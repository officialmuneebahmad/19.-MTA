<?php
/**
 * Multan Tech Accessories — Full-Page & Data Cache Engine
 */

declare(strict_types=1);

namespace App\Core;

class Cache
{
    private static string $cacheDir = MTA_STORAGE . '/cache';

    public static function init(): void
    {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
    }

    public static function key(string $url): string
    {
        return md5(strtolower(trim($url, '/')));
    }

    public static function getPage(string $url): ?string
    {
        self::init();
        $file = self::$cacheDir . '/' . self::key($url) . '.html';
        if (file_exists($file)) {
            $mtime = filemtime($file);
            // Default 1 hour cache lifetime
            if (time() - $mtime < 3600) {
                return file_get_contents($file);
            }
            unlink($file);
        }
        return null;
    }

    public static function setPage(string $url, string $html): void
    {
        self::init();
        $file = self::$cacheDir . '/' . self::key($url) . '.html';
        file_put_contents($file, $html, LOCK_EX);
    }

    public static function forget(string $url): void
    {
        self::init();
        $file = self::$cacheDir . '/' . self::key($url) . '.html';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public static function flush(): void
    {
        self::init();
        $files = glob(self::$cacheDir . '/*.html');
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}
