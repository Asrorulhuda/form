<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;

/**
 * Authentication Middleware
 * Ensures user is logged in before accessing protected routes.
 */
class AuthMiddleware
{
    public function handle(): bool
    {
        if (!Auth::check()) {
            if (
                (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
                str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') ||
                str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') ||
                str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/')
            ) {
                Response::error('Sesi login telah berakhir. Silakan muat ulang halaman dan login kembali.', null, 401);
                return false;
            }

            Response::redirect(url('login'));
            return false;
        }
        return true;
    }
}
