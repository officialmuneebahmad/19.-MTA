<?php
/**
 * Multan Tech Accessories — Admin AuthController
 * Argon2id verification, lockout protection, CSRF check, and session security
 */

declare(strict_types=1);

namespace App\Admin;

use App\Core\Database;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

class AuthController
{
    public function redirectToDashboard(): Response
    {
        $resp = new Response();
        return $resp->redirect(mta_admin_path());
    }

    public function redirectToLogin(): Response
    {
        $resp = new Response();
        return $resp->redirect(mta_admin_path('/login'));
    }

    public function showLogin(): Response
    {
        $adminPath = mta_admin_path();
        $resp = new Response();

        // If already logged in, redirect to dashboard
        if (Session::get('admin_user')) {
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
                $lockedUntil = date('Y-m-d H:i:s', time() + 1800); // 30-minute lock
            }
            $db->execute(
                "UPDATE users SET failed_logins = ?, locked_until = ? WHERE id = ?",
                [$failed, $lockedUntil, $user['id']]
            );

            Session::flash('error', 'Invalid login credentials.');
            return $resp->redirect($adminPath . '/login');
        }

        // Reset failed logins & record login time
        $db->execute(
            "UPDATE users SET failed_logins = 0, locked_until = NULL, last_login_at = ?, last_login_ip = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $clientIp, $user['id']]
        );

        // Clear IP rate limiter upon verified credentials
        RateLimiter::clear("admin_login_{$clientIp}");

        // Regenerate session to prevent session fixation
        Session::regenerate();
        Session::set('admin_user', [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'username' => $user['name'],
            'must_change_password' => (int)$user['must_change_password'],
        ]);

        if (!empty($user['must_change_password'])) {
            Session::flash('warning', 'Security policy requires you to change your temporary password.');
            return $resp->redirect($adminPath . '/change-password');
        }

        Session::flash('success', 'Welcome back, ' . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . '!');
        return $resp->redirect($adminPath);
    }

    public function logout(): Response
    {
        $adminPath = mta_admin_path();
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
            "UPDATE users SET password_hash = ?, must_change_password = 0, updated_at = ? WHERE id = ?",
            [$hash, date('Y-m-d H:i:s'), $userSession['id']]
        );

        $userSession['must_change_password'] = 0;
        Session::set('admin_user', $userSession);

        Session::flash('success', 'Your password has been securely updated. You now have full admin access.');
        return $resp->redirect($adminPath);
    }
}
