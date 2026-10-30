<?php
/**
 * Multan Tech Accessories — Master Audit Logging Engine
 * Tracks all administrative events, authentication attempts, exports, deletions, and settings changes.
 */

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Session;

class AuditLogger
{
    public static function log(string $action, ?string $entityType = null, ?string $entityId = null, mixed $details = null): void
    {
        try {
            $db = Database::getInstance();
            $user = Session::get('admin_user', []);
            $userId = isset($user['id']) ? (int)$user['id'] : null;

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'), 0, 255);

            $detailsStr = is_string($details) ? $details : (is_array($details) || is_object($details) ? json_encode($details) : null);

            $db->execute(
                "INSERT INTO audit_log (user_id, action, entity_type, entity_id, details, ip_address, user_agent, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)",
                [$userId, $action, $entityType, $entityId, $detailsStr, $ip, $ua]
            );
        } catch (\Throwable) {
            // Never break runtime execution if audit logging encounters a transient DB issue
        }
    }
}

