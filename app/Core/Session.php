<?php

namespace App\Core;

/**
 * Session Manager
 * Handles secure session management with flash messages.
 */
class Session
{
    /**
     * Start the session
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $config = require BASE_PATH . '/config/app.php';
            
            session_name($config['session']['name'] ?? 'asr_form_session');
            
            $isHttps = (
                (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1')) ||
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
                (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') ||
                (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            );
            
            session_set_cookie_params([
                'lifetime' => ($config['session']['lifetime'] ?? 120) * 60,
                'path'     => '/',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            session_start();

            // Check session timeout
            $lifetime = ($config['session']['lifetime'] ?? 120) * 60;
            if (isset($_SESSION['_last_activity']) && (time() - $_SESSION['_last_activity']) > $lifetime) {
                self::destroy();
                session_start();
            }
            $_SESSION['_last_activity'] = time();
        }
    }

    /**
     * Get a session value
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set a session value
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check if a session key exists
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session key
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the session
     */
    public static function destroy(): void
    {
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
    }

    /**
     * Regenerate session ID (prevents session fixation)
     */
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Set a flash message (available only for the next request)
     */
    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Get and remove a flash message
     */
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    /**
     * Check if a flash message exists
     */
    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    /**
     * Set old input (for form repopulation after validation errors)
     */
    public static function setOld(array $data): void
    {
        $_SESSION['_old_input'] = $data;
    }

    /**
     * Get old input value
     */
    public static function old(string $key, mixed $default = ''): mixed
    {
        $value = $_SESSION['_old_input'][$key] ?? $default;
        return $value;
    }

    /**
     * Clear old input
     */
    public static function clearOld(): void
    {
        unset($_SESSION['_old_input']);
    }
}
