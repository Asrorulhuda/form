<?php
/**
 * ASR FORM — Web Database Migrator & Schema Updater
 * 
 * Akses file ini langsung di browser untuk memperbarui struktur database hosting:
 * Contoh: https://domain-anda.com/update_db.php  atau  http://localhost/form/update_db.php
 */

define('BASE_PATH', dirname(__DIR__));

// Increase limit
@ini_set('memory_limit', '256M');
@ini_set('max_execution_time', '300');

// Load environment (.env)
if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            $_ENV[$name] = $value;
            putenv("{$name}={$value}");
        }
    }
}

// Database Connection Config
$dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbPort = $_ENV['DB_PORT'] ?? '3306';
$dbName = $_ENV['DB_DATABASE'] ?? 'asr_form';
$dbUser = $_ENV['DB_USERNAME'] ?? 'root';
$dbPass = $_ENV['DB_PASSWORD'] ?? '';

$results = [];
$hasErrors = false;
$pdo = null;

try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $results[] = [
        'type' => 'success',
        'title' => 'Koneksi Database Berhasil',
        'desc' => "Terhubung ke database <strong>`{$dbName}`</strong> pada host <code>{$dbHost}:{$dbPort}</code>"
    ];
} catch (PDOException $e) {
    $hasErrors = true;
    $results[] = [
        'type' => 'danger',
        'title' => 'Gagal Terhubung ke Database',
        'desc' => "Error: " . htmlspecialchars($e->getMessage()) . "<br><small>Pastikan file <code>.env</code> sudah terisi konfigurasi database yang benar.</small>"
    ];
}

// If connected, execute migrations
if ($pdo) {
    // 1. Run Core Tables from database/migration.sql
    $migrationSqlPath = BASE_PATH . '/database/migration.sql';
    if (file_exists($migrationSqlPath)) {
        try {
            $sqlContent = file_get_contents($migrationSqlPath);
            $pdo->exec($sqlContent);
            $results[] = [
                'type' => 'success',
                'title' => 'Skema Tabel Utama (migration.sql)',
                'desc' => 'Seluruh struktur tabel utama (Users, Roles, Forms, Templates, Documents, dsb) telah diperiksa & dipastikan ada (CREATE TABLE IF NOT EXISTS).'
            ];
        } catch (\Throwable $e) {
            $results[] = [
                'type' => 'warning',
                'title' => 'Catatan Skema migration.sql',
                'desc' => htmlspecialchars($e->getMessage())
            ];
        }
    }

    // 2. Ensure Column: document_templates.content
    try {
        $checkCol = $pdo->query("SHOW COLUMNS FROM `document_templates` LIKE 'content'")->fetch();
        if (!$checkCol) {
            $pdo->exec("ALTER TABLE `document_templates` ADD COLUMN `content` LONGTEXT DEFAULT NULL COMMENT 'HTML template content' AFTER `version`");
            $results[] = [
                'type' => 'success',
                'title' => 'Kolom `content` pada `document_templates`',
                'desc' => 'Kolom <code>content</code> (LONGTEXT) berhasil ditambahkan untuk menyimpan isi Editor Surat & Kop Surat.'
            ];
        } else {
            $results[] = [
                'type' => 'success',
                'title' => 'Kolom `content` pada `document_templates`',
                'desc' => 'Kolom <code>content</code> sudah ada dan siap digunakan.'
            ];
        }
    } catch (\Throwable $e) {
        $results[] = [
            'type' => 'warning',
            'title' => 'Pemeriksaan Kolom document_templates.content',
            'desc' => htmlspecialchars($e->getMessage())
        ];
    }

    // 3. Ensure Table: document_template_variables
    try {
        $checkVarTable = $pdo->query("SHOW TABLES LIKE 'document_template_variables'")->fetch();
        if (!$checkVarTable) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS `document_template_variables` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `template_id` INT UNSIGNED NOT NULL,
                `variable_name` VARCHAR(100) NOT NULL,
                `label` VARCHAR(255) NOT NULL,
                `source_type` ENUM('system', 'user', 'form_response', 'setting', 'custom') DEFAULT 'form_response',
                `source_key` VARCHAR(100) DEFAULT NULL,
                `default_value` TEXT DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_dtv_template` (`template_id`),
                INDEX `idx_dtv_var_name` (`variable_name`),
                CONSTRAINT `fk_dtv_template` FOREIGN KEY (`template_id`) REFERENCES `document_templates` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            $results[] = [
                'type' => 'success',
                'title' => 'Tabel `document_template_variables`',
                'desc' => 'Tabel mapping variabel template berhasil dibuat.'
            ];
        } else {
            $results[] = [
                'type' => 'success',
                'title' => 'Tabel `document_template_variables`',
                'desc' => 'Tabel mapping variabel sudah siap.'
            ];
        }
    } catch (\Throwable $e) {
        $results[] = [
            'type' => 'warning',
            'title' => 'Pemeriksaan Tabel document_template_variables',
            'desc' => htmlspecialchars($e->getMessage())
        ];
    }

    // 4. Ensure Table: document_template_versions
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `document_template_versions` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `template_id` INT UNSIGNED NOT NULL,
            `version` INT UNSIGNED NOT NULL DEFAULT 1,
            `file_path` VARCHAR(255) NOT NULL,
            `variables_json` JSON DEFAULT NULL,
            `created_by` INT UNSIGNED DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_dtvers_template` (`template_id`),
            CONSTRAINT `fk_dtvers_template` FOREIGN KEY (`template_id`) REFERENCES `document_templates` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $results[] = [
            'type' => 'success',
            'title' => 'Tabel `document_template_versions`',
            'desc' => 'Tabel riwayat versi dokumen Word siap digunakan.'
        ];
    } catch (\Throwable $e) {
        // Ignore
    }

    // 5. Ensure Storage & Upload Directories
    $dirs = [
        BASE_PATH . '/storage',
        BASE_PATH . '/storage/app',
        BASE_PATH . '/storage/documents',
        BASE_PATH . '/storage/cache',
        BASE_PATH . '/uploads',
        BASE_PATH . '/uploads/templates',
        BASE_PATH . '/uploads/images',
    ];
    $dirSuccess = 0;
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (is_dir($dir) && is_writable($dir)) {
            $dirSuccess++;
        }
    }
    $results[] = [
        'type' => 'success',
        'title' => 'Direktori Penyimpanan & Upload Dokumen',
        'desc' => "{$dirSuccess} dari " . count($dirs) . " folder penyimpanan (storage/ & uploads/) siap dan memiliki izin tulis (writable)."
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Database — ASR FORM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .updater-container {
            width: 100%;
            max-width: 680px;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
            overflow: hidden;
        }
        .updater-header {
            background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);
            padding: 32px 28px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .updater-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.15);
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 800;
            color: #c7d2fe;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .updater-title {
            font-size: 22px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .updater-desc {
            font-size: 13px;
            color: #cbd5e1;
            line-height: 1.5;
        }
        .updater-body {
            padding: 28px;
        }
        .status-card {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: all 0.2s ease;
        }
        .status-card:hover {
            border-color: #6366f1;
            transform: translateY(-1px);
        }
        .status-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }
        .icon-success { background: #064e3b; color: #34d399; }
        .icon-warning { background: #78350f; color: #fbbf24; }
        .icon-danger { background: #7f1d1d; color: #f87171; }
        .status-info h4 {
            font-size: 14px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 3px;
        }
        .status-info p {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.45;
        }
        .status-info code {
            background: #1e293b;
            color: #818cf8;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
        }
        .updater-footer {
            padding: 20px 28px;
            background: #182234;
            border-top: 1px solid #334155;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-primary {
            background: #4f46e5;
            color: #ffffff;
            border: 1px solid #6366f1;
            box-shadow: 0 4px 14px rgba(79,70,229,0.4);
        }
        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #334155;
            color: #f8fafc;
            border: 1px solid #475569;
        }
        .btn-secondary:hover {
            background: #475569;
        }
    </style>
</head>
<body>
    <div class="updater-container">
        
        <!-- Header -->
        <div class="updater-header">
            <div class="updater-badge">🚀 ASR FORM — Database Updater</div>
            <h1 class="updater-title">Pembaruan Skema Database Selesai</h1>
            <p class="updater-desc">
                Pemeriksaan struktur tabel, penambahan kolom baru (Kop Surat &amp; Editor), dan izin folder penyimpanan telah dijalankan.
            </p>
        </div>

        <!-- Body Logs -->
        <div class="updater-body">
            <?php foreach ($results as $res): ?>
                <div class="status-card">
                    <div class="status-icon icon-<?= $res['type'] ?>">
                        <?= $res['type'] === 'success' ? '✓' : ($res['type'] === 'warning' ? '!' : '✕') ?>
                    </div>
                    <div class="status-info">
                        <h4><?= $res['title'] ?></h4>
                        <p><?= $res['desc'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Footer Actions -->
        <div class="updater-footer">
            <a href="update_db.php" class="btn btn-secondary">
                🔄 Jalankan Ulang Pemeriksaan
            </a>
            <a href="templates" class="btn btn-primary">
                📜 Masuk ke Editor &amp; Template Surat &rarr;
            </a>
        </div>

    </div>
</body>
</html>
