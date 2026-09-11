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

$isFresh = in_array('--fresh', $argv ?? []);

if ($isFresh) {
    echo "0. Menghapus tabel lama (--fresh)... ";
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS `{$table}`");
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
        echo "[ OK - " . count($tables) . " tabel dibersihkan ]\n";
    } catch (\Throwable $e) {
        echo "[ GAGAL ]\n  Error: " . $e->getMessage() . "\n";
    }
}

// Disable FK checks during migration & seeding
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

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

// 1.5 Auto-upgrade existing tables with Multi-SaaS columns if missing
echo "1.5 Memeriksa & Mengupgrade Kolom Multi-SaaS... ";
try {
    $columnUpgrades = [
        ['numbering_configs', 'admin_id', "INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Tenant owner' AFTER `id`"],
        ['users', 'admin_id', "INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Belongs to which admin/tenant' AFTER `id`"],
        ['forms', 'admin_id', "INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Tenant owner' AFTER `id`"],
        ['document_templates', 'admin_id', "INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Tenant owner' AFTER `id`"],
        ['document_templates', 'content', "LONGTEXT DEFAULT NULL COMMENT 'HTML template content' AFTER `version`"],
        ['documents', 'admin_id', "INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Tenant owner' AFTER `id`"],
        ['file_uploads', 'admin_id', "INT UNSIGNED DEFAULT NULL AFTER `id`"],
        ['payments', 'admin_id', "INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Admin tenant' AFTER `id`"],
        ['audit_logs', 'user_type', "VARCHAR(20) DEFAULT 'admin' AFTER `user_id`"],
        ['audit_logs', 'user_name', "VARCHAR(100) DEFAULT NULL AFTER `user_type`"],
    ];

    foreach ($columnUpgrades as [$tbl, $col, $def]) {
        $tableExists = $pdo->query("SHOW TABLES LIKE '{$tbl}'")->fetch();
        if ($tableExists) {
            $colExists = $pdo->query("SHOW COLUMNS FROM `{$tbl}` LIKE '{$col}'")->fetch();
            if (!$colExists) {
                $pdo->exec("ALTER TABLE `{$tbl}` ADD COLUMN `{$col}` {$def}");
            }
        }
    }

    // Drop legacy FK on audit_logs if still attached
    try {
        $pdo->exec("ALTER TABLE `audit_logs` DROP FOREIGN KEY `audit_logs_ibfk_1`");
    } catch (\Throwable $ignored) {}

    echo "[ OK - BERHASIL ]\n";
} catch (\Throwable $e) {
    echo "[ GAGAL ]\n  Error: " . $e->getMessage() . "\n";
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

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

echo "\nDatabase ASR FORM siap digunakan!\n";
