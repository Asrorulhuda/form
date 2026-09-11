<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;

/**
 * Super Admin Middleware
 * Ensures only Super Admin can access certain routes (e.g., admin management, global settings).
 */
class SuperAdminMiddleware
{
    public function handle(): bool
    {
        if (!Auth::check()) {
            Response::redirect(url('login'));
            return false;
        }

        if (!Auth::isSuperAdmin()) {
            Response::redirect(url('dashboard'));
            return false;
        }

        return true;
    }
}
