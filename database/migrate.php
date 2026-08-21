<?php
/**
 * ASR FORM - Simple Database Migration & Seeder Runner
 * 
 * Usage:
 *   php database/migrate.php          (Runs migration.sql + seed.sql)
 *   php database/migrate.php --fresh  (Runs fresh migration and seed)
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/app/Helpers/helpers.php';
\App\Core\Env::load(BASE_PATH . '/.env');
\App\Core\Database::init(require BASE_PATH . '/config/database.php');

$db = \App\Core\Database::getInstance();
$pdo = $db->getPdo();

echo "===============================================\n";
echo "      ASR FORM — Database Setup Runner         \n";
echo "===============================================\n\n";

// 1. Run Master Migration Schema
$migrationFile = __DIR__ . '/migration.sql';
if (file_exists($migrationFile)) {
    echo "1. Menjalankan migration.sql (Struktur Tabel)... ";
    try {
        $sql = file_get_contents($migrationFile);
        $pdo->exec($sql);
        echo "[ OK - BERHASIL ]\n";
    } catch (\Throwable $e) {
        echo "[ GAGAL ]\n  Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "[!] File database/migration.sql tidak ditemukan!\n";
}

// 2. Run Master Seed Data
$seedFile = __DIR__ . '/seed.sql';
if (file_exists($seedFile)) {
    echo "2. Menjalankan seed.sql (Data Awal & Roles)... ";
    try {
        $sql = file_get_contents($seedFile);
        $pdo->exec($sql);
        echo "[ OK - BERHASIL ]\n";
    } catch (\Throwable $e) {
        echo "[ GAGAL ]\n  Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "[!] File database/seed.sql tidak ditemukan!\n";
}

echo "\nDatabase ASR FORM siap digunakan!\n";
