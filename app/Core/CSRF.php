<?php

namespace App\Core;

/**
 * CSRF Protection
 * Generates and validates CSRF tokens for all POST/PUT/DELETE requests.
 */
class CSRF
{
    /**
     * Generate a CSRF token and store in session
     */
    public static function generate(): string
    {
        if (!Session::has('_csrf_token')) {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf_token', $token);
        }
        return Session::get('_csrf_token');
    }

    /**
     * Get the hidden input field HTML
     */
    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Validate the CSRF token
     */
    public static function validate(?string $token = null): bool
    {
        $token = $token ?? ($_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        $sessionToken = Session::get('_csrf_token', '');

        if (empty($token) || empty($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Validate and throw exception if invalid
     */
    public static function check(): void
    {
        if (!self::validate()) {
            http_response_code(403);
            die('CSRF token validation failed.');
        }
    }

    /**
     * Regenerate the CSRF token
     */
    public static function regenerate(): string
    {
        Session::remove('_csrf_token');
        return self::generate();
    }

    /**
     * Get meta tag for AJAX requests
     */
    public static function meta(): string
    {
        return '<meta name="csrf-token" content="' . htmlspecialchars(self::generate()) . '">';
    }
}
