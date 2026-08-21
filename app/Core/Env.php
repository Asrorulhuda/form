<?php

namespace App\Core;

/**
 * Environment Variable Loader
 * Parses .env file and provides env() helper function.
 */
class Env
{
    private static array $vars = [];

    /**
     * Load .env file
     */
    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key   = trim($key);
                $value = trim($value);

                // Remove quotes
                $value = trim($value, '"\'');

                self::$vars[$key] = $value;
                $_ENV[$key]       = $value;
                putenv("$key=$value");
            }
        }
    }

    /**
     * Get env variable
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$vars[$key] ?? $_ENV[$key] ?? $default;
    }
}

/**
 * Global helper function
 */
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return \App\Core\Env::get($key, $default);
    }
}

