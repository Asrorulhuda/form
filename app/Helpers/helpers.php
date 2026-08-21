<?php

use App\Core\Env;
use App\Core\View;

// PHP 8 Compatibility Polyfills
if (!function_exists('str_starts_with')) {
    function str_starts_with(?string $haystack, ?string $needle): bool
    {
        if ($haystack === null || $needle === null) return false;
        if ($needle === '') return true;
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(?string $haystack, ?string $needle): bool
    {
        if ($haystack === null || $needle === null) return false;
        if ($needle === '' || $needle === $haystack) return true;
        if ($haystack === '') return false;
        $len = strlen($needle);
        return $len <= strlen($haystack) && substr_compare($haystack, $needle, -$len) === 0;
    }
}

if (!function_exists('str_contains')) {
    function str_contains(?string $haystack, ?string $needle): bool
    {
        if ($haystack === null || $needle === null) return false;
        if ($needle === '') return true;
        return strpos($haystack, $needle) !== false;
    }
}

/**
 * Global Helper Functions
 */

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $appUrl = env('APP_URL');
        $appEnv = env('APP_ENV', 'production');

        // In production, if APP_URL is explicitly configured and not localhost, prioritize it
        if ($appEnv === 'production' && !empty($appUrl) && !str_contains($appUrl, 'localhost')) {
            $baseUrl = rtrim($appUrl, '/');
        } elseif (!empty($_SERVER['HTTP_HOST'])) {
            $isHttps = (
                (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1')) ||
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
                (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            );
            $scheme = $isHttps ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            
            // Check request URI to detect if we're under /form or /form/public or root
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $scriptDir = str_replace('\\', '/', dirname($scriptName)); // e.g. /form/public
            $rootDir = str_replace('\\', '/', dirname($scriptDir));     // e.g. /form

            if (str_starts_with($requestUri, $scriptDir) && $scriptDir !== '/' && $scriptDir !== '.') {
                $subfolder = $scriptDir;
            } elseif (str_starts_with($requestUri, $rootDir) && $rootDir !== '/' && $rootDir !== '.') {
                $subfolder = $rootDir;
            } else {
                $subfolder = ($scriptDir === '/' || $scriptDir === '.' || $scriptDir === '\\') ? '' : $scriptDir;
            }

            $subfolder = rtrim($subfolder, '/');
            $baseUrl = $scheme . '://' . $host . $subfolder;
        } else {
            $baseUrl = rtrim($appUrl ?: 'http://localhost', '/');
        }

        $path = ltrim($path, '/');
        return $path ? rtrim($baseUrl, '/') . '/' . $path : rtrim($baseUrl, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return base_url($path);
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return View::e($value);
    }
}

// Global class aliases for view templates
if (!class_exists('View', false)) {
    class_alias(\App\Core\View::class, 'View');
}
if (!class_exists('CSRF', false)) {
    class_alias(\App\Core\CSRF::class, 'CSRF');
}
if (!class_exists('Auth', false)) {
    class_alias(\App\Core\Auth::class, 'Auth');
}
if (!class_exists('Session', false)) {
    class_alias(\App\Core\Session::class, 'Session');
}

/**
 * Render an ad for a specific slot key.
 * Returns empty string if ads are disabled or not configured.
 */
if (!function_exists('renderAd')) {
    function renderAd(string $slotKey): string
    {
        try {
            return \App\Services\AdSenseService::getInstance()->renderAd($slotKey);
        } catch (\Throwable $e) {
            return '';
        }
    }
}

/**
 * Get AdSense head script tag.
 * Should be called once in <head>.
 */
if (!function_exists('adsenseHead')) {
    function adsenseHead(): string
    {
        try {
            return \App\Services\AdSenseService::getInstance()->getHeadScript();
        } catch (\Throwable $e) {
            return '';
        }
    }
}

/**
 * Get a setting value with caching.
 */
if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        static $model = null;
        if ($model === null) {
            $model = new \App\Models\Setting();
        }
        return $model->get($key, $default);
    }
}

