<?php

namespace App\Core;

/**
 * View Engine
 * Renders PHP views with layout support and data passing.
 */
class View
{
    /**
     * Render a view file
     *
     * @param string $view  View path (dot notation: 'dashboard.index')
     * @param array  $data  Data to pass to view
     * @param string|null $layout  Layout to use (null = no layout)
     */
    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        $viewPath = BASE_PATH . '/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$view} ({$viewPath})");
        }

        // Extract data to make variables available in views
        extract($data);

        if ($layout) {
            // Start output buffering for the content
            ob_start();
            include $viewPath;
            $content = ob_get_clean();

            // Render the layout
            $layoutPath = BASE_PATH . '/views/layouts/' . $layout . '.php';
            if (!file_exists($layoutPath)) {
                throw new \RuntimeException("Layout not found: {$layout}");
            }
            include $layoutPath;
        } else {
            include $viewPath;
        }
    }

    /**
     * Render a view with the main app layout
     */
    public static function page(string $view, array $data = []): void
    {
        self::render($view, $data, 'app');
    }

    /**
     * Render a view with the guest layout
     */
    public static function guest(string $view, array $data = []): void
    {
        self::render($view, $data, 'guest');
    }

    /**
     * Render a view with the public layout
     */
    public static function public(string $view, array $data = []): void
    {
        self::render($view, $data, 'public');
    }

    /**
     * Include a partial/component
     */
    public static function component(string $name, array $data = []): void
    {
        $path = BASE_PATH . '/views/components/' . str_replace('.', '/', $name) . '.php';
        if (file_exists($path)) {
            extract($data);
            include $path;
        }
    }

    /**
     * Escape output for XSS protection
     */
    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Global shortcut for escaping output
 */
if (!function_exists('e')) {
    function e(?string $value): string
    {
        return View::e($value);
    }
}

/**
 * Global shortcut for asset URL
 */
if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return rtrim(env('APP_URL', ''), '/') . '/assets/' . ltrim($path, '/');
    }
}

/**
 * Global shortcut for generating URL
 */
if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return rtrim(env('APP_URL', ''), '/') . '/' . ltrim($path, '/');
    }
}
