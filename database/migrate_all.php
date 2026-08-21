<?php
/**
 * ASR FORM - Sequential Database Migration Runner
 * Usage: php database/migrate_all.php
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/app/Helpers/helpers.php';
\App\Core\Env::load(BASE_PATH . '/.env');
\App\Core\Database::init(require BASE_PATH . '/config/database.php');

$db = \App\Core\Database::getInstance();
$pdo = $db->getPdo();

echo "===============================================\n";
echo "       ASR FORM - Database Migration Runner    \n";
echo "===============================================\n\n";

$migrationsDir = __DIR__ . '/migrations';
$files = glob($migrationsDir . '/*.sql');
sort($files);

if (empty($files)) {
    echo "No migration files found in {$migrationsDir}\n";
    exit(0);
}

foreach ($files as $file) {
    $filename = basename($file);
    echo "Executing migration: {$filename}... ";
    
    try {
        $sql = file_get_contents($file);
        
        // Remove USE statement to avoid issues if database name is different on hosting
        $sql = preg_replace('/^USE\s+[`\w]+;/mi', '', $sql);
        
        $pdo->exec($sql);
        echo "[ OK ]\n";
    } catch (\Throwable $e) {
        // Check if error is 'table already exists' or 'column already exists'
        $msg = $e->getMessage();
        if (str_contains($msg, 'already exists') || str_contains($msg, 'Duplicate column')) {
            echo "[ SKIPPED - ALREADY APPLIED ]\n";
        } else {
            echo "[ FAILED ]\n";
            echo "  Error: {$msg}\n";
        }
    }
}

echo "\nAll migrations processed!\n";
