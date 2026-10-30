<?php
/**
 * Multan Tech Accessories — Admin AuthController (Part B)
 * Argon2id verification, lockout protection, pure PHP RFC 6238 TOTP 2FA,
 * backup codes, re-authentication gates, and audit logging.
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\AuditLogger;
use App\Services\TotpService;

class AuthController
{
    public function showLogin(): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        // If already logged in, redirect to dashboard
        if (Session::isAdmin()) {
            return $resp->redirect($adminPath);
        }

        return $resp->html(View::render('admin/auth/login', [
            'meta_title' => 'Admin Authentication — Multan Tech Accessories',
        ]));
    }

    public function login(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $clientIp = $request->ip();

        // 1. IP brute-force limiter (max 5 failed attempts per 15 mins)
        if (!RateLimiter::check("admin_login_{$clientIp}", 5, 900)) {
            AuditLogger::log('login_lockout_ip', null, null, ['ip' => $clientIp]);
            Session::flash('error', 'Too many failed login attempts. Your IP has been temporarily locked out for 15 minutes.');
            return $resp->redirect($adminPath . '/login');
        }

        $email = trim((string)$request->post('email', ''));
        $password = (string)$request->post('password', '');

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Please enter both your email address and password.');
            return $resp->redirect($adminPath . '/login');
        }

        $db = Database::getInstance();
        $user = $db->fetch(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            [$email]
        );

        if (!$user) {
            AuditLogger::log('failed_login_unknown_email', null, null, ['email' => $email, 'ip' => $clientIp]);
            Session::flash('error', 'Invalid login credentials.');
            return $resp->redirect($adminPath . '/login');
        }

        // Check account lock
        if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            $lockMin = ceil((strtotime($user['locked_until']) - time()) / 60);
            Session::flash('error', "Account is locked due to repeated failed logins. Please retry in {$lockMin} minutes.");
            return $resp->redirect($adminPath . '/login');
        }

        // Verify Password
        if (!password_verify($password, $user['password_hash'])) {
            $failed = (int)$user['failed_logins'] + 1;
            $lockedUntil = null;
            if ($failed >= 5) {
                $lockedUntil = date('Y-m-d H:i:s', time() + 900); // 15-minute lock
                AuditLogger::log('account_locked', 'user', (string)$user['id'], ['email' => $email, 'failed' => $failed]);
            } else {
                AuditLogger::log('failed_login_bad_password', 'user', (string)$user['id'], ['email' => $email, 'failed' => $failed]);
            }
            $db->execute(
                "UPDATE users SET failed_logins = ?, locked_until = ? WHERE id = ?",
                [$failed, $lockedUntil, $user['id']]
            );

            Session::flash('error', 'Invalid login credentials.');
            return $resp->redirect($adminPath . '/login');
        }

        // Reset failed attempts upon correct password
        $db->execute(
            "UPDATE users SET failed_logins = 0, locked_until = NULL WHERE id = ?",
            [$user['id']]
        );

        // Check 2FA requirement (Mandatory for owner)
        if (empty($user['is_2fa_enabled']) || empty($user['totp_secret'])) {
            // Initiate mandatory 2FA enrollment
            Session::set('2fa_pending', [
                'user_id' => (int)$user['id'],
                'time' => time(),
            ]);
            return $resp->redirect($adminPath . '/2fa/setup');
        }

        // User has 2FA enabled -> proceed to 2FA verification prompt
        Session::set('2fa_pending', [
            'user_id' => (int)$user['id'],
            'time' => time(),
        ]);
        return $resp->redirect($adminPath . '/2fa/verify');
    }

    public function show2faSetup(): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $pending = Session::get('2fa_pending');

        if (!$pending || empty($pending['user_id'])) {
            return $resp->redirect($adminPath . '/login');
        }

        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE id = ? LIMIT 1", [$pending['user_id']]);
        if (!$user) {
            return $resp->redirect($adminPath . '/login');
        }

        // Generate or retrieve session setup credentials
        $secret = Session::get('2fa_setup_secret');
        $backupCodes = Session::get('2fa_setup_backup_codes');

        if (!$secret || !is_string($secret)) {
            $secret = TotpService::generateSecret(16);
            $backupCodes = TotpService::generateBackupCodes(10);
            Session::set('2fa_setup_secret', $secret);
            Session::set('2fa_setup_backup_codes', $backupCodes);
        }

        $otpAuthUri = TotpService::getOtpAuthUri($user['email'], $secret, 'Multan Tech Accessories');

        return $resp->html(View::render('admin/auth/2fa-setup', [
            'secret' => $secret,
            'backupCodes' => $backupCodes,
            'otpAuthUri' => $otpAuthUri,
        ]));
    }

    public function process2faSetup(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $pending = Session::get('2fa_pending');

        if (!$pending || empty($pending['user_id'])) {
            return $resp->redirect($adminPath . '/login');
        }

        $secret = (string)Session::get('2fa_setup_secret');
        $backupCodes = Session::get('2fa_setup_backup_codes');
        $code = trim((string)$request->post('totp_code', ''));

        if (empty($secret) || empty($code) || !is_array($backupCodes)) {
            Session::flash('error', 'Setup session expired. Please re-enter your authenticator code.');
            return $resp->redirect($adminPath . '/2fa/setup');
        }

        if (!TotpService::verifyCode($secret, $code)) {
            AuditLogger::log('failed_2fa_setup', 'user', (string)$pending['user_id']);
            Session::flash('error', 'Invalid 6-digit code. Please verify the code displayed in your authenticator app.');
            return $resp->redirect($adminPath . '/2fa/setup');
        }

        // Persist 2FA secret and hashed backup codes
        $db = Database::getInstance();
        $db->execute(
            "UPDATE users SET 
                totp_secret = ?, 
                backup_codes_json = ?, 
                is_2fa_enabled = 1, 
                last_login_at = CURRENT_TIMESTAMP, 
                last_login_ip = ?,
                last_reauth_at = CURRENT_TIMESTAMP
             WHERE id = ?",
            [
                $secret,
                json_encode($backupCodes['hashed']),
                $request->ip(),
                $pending['user_id'],
            ]
        );

        $user = $db->fetch("SELECT * FROM users WHERE id = ? LIMIT 1", [$pending['user_id']]);

        // Complete authentication
        Session::regenerate();
        Session::remove('2fa_pending');
        Session::remove('2fa_setup_secret');
        Session::remove('2fa_setup_backup_codes');

        $_SESSION['admin_user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'username' => $user['name'],
            'must_change_password' => (int)$user['must_change_password'],
        ];
        $_SESSION['admin_login_time'] = time();
        $_SESSION['admin_last_activity'] = time();
        $_SESSION['admin_reauth_at'] = time();

        AuditLogger::log('2fa_enabled_login', 'user', (string)$user['id'], ['email' => $user['email']]);
        Session::flash('success', 'Two-Factor Authentication activated successfully! Welcome back, ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . '.');
        return $resp->redirect($adminPath);
    }

    public function show2faVerify(): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $pending = Session::get('2fa_pending');

        if (!$pending || empty($pending['user_id'])) {
            return $resp->redirect($adminPath . '/login');
        }

        return $resp->html(View::render('admin/auth/2fa-verify', [
            'meta_title' => 'Two-Factor Verification — Multan Tech Accessories',
        ]));
    }

    public function process2faVerify(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $pending = Session::get('2fa_pending');

        if (!$pending || empty($pending['user_id'])) {
            return $resp->redirect($adminPath . '/login');
        }

        $code = trim((string)$request->post('totp_code', ''));
        if (empty($code)) {
            Session::flash('error', 'Please enter your 6-digit authenticator code or 8-character backup code.');
            return $resp->redirect($adminPath . '/2fa/verify');
        }

        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE id = ? LIMIT 1", [$pending['user_id']]);
        if (!$user) {
            return $resp->redirect($adminPath . '/login');
        }

        $verified = false;

        // 1. Try 6-digit TOTP code
        if (strlen($code) === 6 && ctype_digit($code)) {
            if (!empty($user['totp_secret']) && TotpService::verifyCode($user['totp_secret'], $code)) {
                $verified = true;
            }
        }

        // 2. Try single-use backup code
        if (!$verified && !empty($user['backup_codes_json'])) {
            $hashedCode = hash('sha256', strtoupper(str_replace('-', '', $code)));
            $codes = json_decode((string)$user['backup_codes_json'], true);
            if (is_array($codes) && in_array($hashedCode, $codes, true)) {
                $verified = true;
                // Remove used code from pool
                $codes = array_values(array_diff($codes, [$hashedCode]));
                $db->execute("UPDATE users SET backup_codes_json = ? WHERE id = ?", [json_encode($codes), $user['id']]);
                AuditLogger::log('backup_code_consumed', 'user', (string)$user['id']);
            }
        }

        if (!$verified) {
            AuditLogger::log('failed_2fa_verify', 'user', (string)$user['id'], ['ip' => $request->ip()]);
            Session::flash('error', 'Invalid code. Please verify your authenticator app time and enter the latest 6-digit code.');
            return $resp->redirect($adminPath . '/2fa/verify');
        }

        // Authentication Successful
        Session::regenerate();
        Session::remove('2fa_pending');

        $db->execute(
            "UPDATE users SET last_login_at = CURRENT_TIMESTAMP, last_login_ip = ?, last_reauth_at = CURRENT_TIMESTAMP WHERE id = ?",
            [$request->ip(), $user['id']]
        );

        $_SESSION['admin_user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'username' => $user['name'],
            'must_change_password' => (int)$user['must_change_password'],
        ];
        $_SESSION['admin_login_time'] = time();
        $_SESSION['admin_last_activity'] = time();
        $_SESSION['admin_reauth_at'] = time();

        AuditLogger::log('login_success', 'user', (string)$user['id'], ['email' => $user['email']]);
        Session::flash('success', 'Authentication verified. Welcome back, ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . '!');
        return $resp->redirect($adminPath);
    }

    public function showReauth(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        if (!Session::isAdmin()) {
            return $resp->redirect($adminPath . '/login');
        }

        $returnTo = (string)$request->get('return_to', $adminPath);

        return $resp->html(View::render('admin/auth/reauth', [
            'returnTo' => $returnTo,
            'meta_title' => 'Re-Authentication Required — Multan Tech Accessories',
        ]));
    }

    public function processReauth(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        if (!Session::isAdmin()) {
            return $resp->redirect($adminPath . '/login');
        }

        $userSession = Session::get('admin_user');
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE id = ? LIMIT 1", [$userSession['id']]);

        $password = (string)$request->post('password', '');
        $totpCode = trim((string)$request->post('totp_code', ''));
        $returnTo = (string)$request->post('return_to', $adminPath);

        $authenticated = false;

        if (!empty($password) && password_verify($password, $user['password_hash'])) {
            $authenticated = true;
        } elseif (!empty($totpCode) && !empty($user['totp_secret']) && TotpService::verifyCode($user['totp_secret'], $totpCode)) {
            $authenticated = true;
        }

        if (!$authenticated) {
            AuditLogger::log('reauth_failed', 'user', (string)$user['id']);
            Session::flash('error', 'Authentication failed. Please enter the correct password or 2FA code.');
            return $resp->redirect($adminPath . '/reauth?return_to=' . urlencode($returnTo));
        }

        Session::setReauthenticated();
        $db->execute("UPDATE users SET last_reauth_at = CURRENT_TIMESTAMP WHERE id = ?", [$user['id']]);
        AuditLogger::log('reauth_success', 'user', (string)$user['id']);

        Session::flash('success', 'Identity confirmed. You may now proceed.');
        return $resp->redirect($returnTo ?: $adminPath);
    }

    public function logoutAllDevices(): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $user = Session::get('admin_user');

        if ($user) {
            AuditLogger::log('logout_all_devices', 'user', (string)$user['id']);
        }

        Session::destroy();
        Session::flash('info', 'All active administration sessions have been terminated.');
        return $resp->redirect($adminPath . '/login');
    }

    public function logout(): Response
    {
        $adminPath = mta_admin_path();
        $user = Session::get('admin_user');
        if ($user) {
            AuditLogger::log('logout', 'user', (string)$user['id']);
        }
        Session::destroy();
        $resp = new Response();
        return $resp->redirect($adminPath . '/login');
    }

    public function showChangePassword(): Response
    {
        $resp = new Response();
        return $resp->html(View::render('admin/auth/change-password', [
            'meta_title' => 'Change Initial Password — Multan Tech Accessories',
        ]));
    }

    public function changePassword(Request $request): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();
        $userSession = Session::get('admin_user');

        if (!$userSession) {
            return $resp->redirect($adminPath . '/login');
        }

        $newPassword = (string)$request->post('new_password', '');
        $confirmPassword = (string)$request->post('confirm_password', '');

        if (strlen($newPassword) < 12) {
            Session::flash('error', 'Password must be at least 12 characters long for store security.');
            return $resp->redirect($adminPath . '/change-password');
        }

        if ($newPassword !== $confirmPassword) {
            Session::flash('error', 'New passwords do not match. Please re-enter carefully.');
            return $resp->redirect($adminPath . '/change-password');
        }

        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
        $hash = password_hash($newPassword, $algo);

        $db = Database::getInstance();
        $db->execute(
            "UPDATE users SET password_hash = ?, must_change_password = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
            [$hash, $userSession['id']]
        );

        $userSession['must_change_password'] = 0;
        Session::set('admin_user', $userSession);

        AuditLogger::log('password_changed', 'user', (string)$userSession['id']);
        Session::flash('success', 'Your password has been securely updated. You now have full admin access.');
        return $resp->redirect($adminPath);
    }
}
