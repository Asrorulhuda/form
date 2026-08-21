<?php
/**
 * ASR FORM - Entry Point
 * All requests are routed through this file.
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Autoload
require_once BASE_PATH . '/vendor/autoload.php';

// Load environment
App\Core\Env::load(BASE_PATH . '/.env');

// Load Helpers
require_once BASE_PATH . '/app/Helpers/helpers.php';

// Load config
$config = require BASE_PATH . '/config/app.php';

// Configure error reporting based on environment
$isDebug = (bool) ($config['debug'] ?? false);
if ($isDebug) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Global Exception Handler
set_exception_handler(function (\Throwable $e) use ($isDebug) {
    http_response_code(500);
    error_log("[ASR FORM Exception] " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    
    if (
        (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
        str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') ||
        str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')
    ) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => $isDebug ? $e->getMessage() : 'Terjadi kesalahan pada sistem server.',
            'error'   => $isDebug ? [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ] : null
        ]);
        exit;
    }

    $error500View = BASE_PATH . '/views/errors/500.php';
    if (file_exists($error500View)) {
        $exception = $e;
        $debug = $isDebug;
        include $error500View;
    } else {
        echo "<h1>500 - Terjadi Kesalahan Sistem</h1>";
        if ($isDebug) {
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
    }
    exit;
});

// Set timezone
date_default_timezone_set($config['timezone'] ?? 'Asia/Jakarta');

// Start session
App\Core\Session::start();

// Initialize database with maintenance fallback
try {
    App\Core\Database::init(require BASE_PATH . '/config/database.php');
} catch (\Throwable $e) {
    http_response_code(503);
    error_log("[Database Connection Error] " . $e->getMessage());
    $maintView = BASE_PATH . '/views/errors/maintenance.php';
    if (file_exists($maintView)) {
        $dbError = $isDebug ? $e->getMessage() : null;
        include $maintView;
    } else {
        echo "<h1>503 Service Unavailable</h1><p>Koneksi database sedang mengalami kendala. Silakan coba beberapa saat lagi.</p>";
    }
    exit;
}

// Load routes
$router = new App\Core\Router();
require BASE_PATH . '/routes/web.php';

// Smart URL extraction for both VirtualHost and Subfolder (e.g. localhost/form)
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = str_replace('\\', '/', dirname($scriptName));
$rootDir = str_replace('\\', '/', dirname($scriptDir));

$url = $requestPath;
if ($scriptDir !== '/' && $scriptDir !== '.' && $scriptDir !== '' && str_starts_with($url, $scriptDir)) {
    $url = substr($url, strlen($scriptDir));
} elseif ($rootDir !== '/' && $rootDir !== '.' && $rootDir !== '' && str_starts_with($url, $rootDir)) {
    $url = substr($url, strlen($rootDir));
}

// Strip /public or /index.php if still present
$url = preg_replace('#^/public#', '', $url);
$url = preg_replace('#^/index\.php#', '', $url);
$url = trim($url, '/');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($url, $method);
