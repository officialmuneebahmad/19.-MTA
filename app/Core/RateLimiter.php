<?php
/**
 * Multan Tech Accessories — File-based Rate Limiter
 */

declare(strict_types=1);

namespace App\Core;

class RateLimiter
{
    public static function check(string $action, mixed $identifierOrMax = null, ?int $maxAttempts = null, int $decaySeconds = 60): bool
    {
        $dir = MTA_STORAGE . '/rate_limits';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (is_int($identifierOrMax)) {
            $key = md5($action);
            $max = $identifierOrMax;
            $decay = $maxAttempts ?? 60;
        } else {
            $identifier = (string)($identifierOrMax ?? '');
            $key = md5("{$action}_{$identifier}");
            $max = $maxAttempts ?? 60;
            $decay = $decaySeconds;
        }

        $file = "{$dir}/{$key}.json";
        $now = time();

        $data = ['hits' => 0, 'expires_at' => $now + $decay];

        if (file_exists($file)) {
            $content = file_get_contents($file);
            if ($content !== false) {
                $parsed = json_decode($content, true);
                if (is_array($parsed) && isset($parsed['expires_at']) && $parsed['expires_at'] > $now) {
                    $data = $parsed;
                }
            }
        }

        if ($data['hits'] >= $max) {
            return false;
        }

        $data['hits']++;
        file_put_contents($file, json_encode($data), LOCK_EX);
        return true;
    }

    public static function clear(string $action, ?string $identifier = null): void
    {
        $dir = MTA_STORAGE . '/rate_limits';
        $key = $identifier !== null ? md5("{$action}_{$identifier}") : md5($action);
        $file = "{$dir}/{$key}.json";
        if (file_exists($file)) {
            @unlink($file);
        }
    }
}
