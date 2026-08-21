<?php
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/app/Helpers/helpers.php';
\App\Core\Env::load(BASE_PATH . '/.env');
\App\Core\Database::init(require BASE_PATH . '/config/database.php');

$db = \App\Core\Database::getInstance();

echo "Running Migration 005...\n";
$sql = file_get_contents(__DIR__ . '/migrations/005_github_webhook.sql');

try {
    $db->getPdo()->exec($sql);
    echo "Migration 005 completed successfully!\n";
} catch (\Throwable $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}
