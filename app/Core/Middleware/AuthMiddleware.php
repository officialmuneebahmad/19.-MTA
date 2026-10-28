<?php
/**
 * Multan Tech Accessories — Admin Authentication Middleware
 */

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(Request $request): ?Response
    {
        $adminPath = mta_config('app.admin_path', mta_setting('admin_path', 'mta-control-9872'));
        $user = Session::get('admin_user');

        if (!$user) {
            $resp = new Response();
            Session::flash('error', 'Please log in to access the administration dashboard.');
            return $resp->redirect('/' . $adminPath . '/login');
        }

        // Force password change on first login
        if (!empty($user['must_change_password']) && $request->getPath() !== '/' . $adminPath . '/change-password' && $request->getPath() !== '/' . $adminPath . '/logout') {
            $resp = new Response();
            Session::flash('warning', 'Security policy requires you to change your initial password before continuing.');
            return $resp->redirect('/' . $adminPath . '/change-password');
        }

        return null;
    }
}
