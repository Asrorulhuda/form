<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;

/**
 * Role Middleware
 * Ensures user has the required role (Super Admin) for admin routes.
 */
class RoleMiddleware
{
    public function handle(): bool
    {
        if (!Auth::check()) {
            Response::redirect(url('login'));
            return false;
        }

        // Super Admin and Admin have full access to management pages
        if (!Auth::hasRole('Super Admin', 'Admin')) {
            Response::redirect(url('dashboard'));
            return false;
        }

        return true;
    }
}
