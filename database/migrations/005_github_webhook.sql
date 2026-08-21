-- ═══════════════════════════════════════════
-- ASR FORM — GitHub Webhook & Auto Deploy Migration
-- Version: 1.3
-- ═══════════════════════════════════════════

USE `asr_form`;

-- ─── Webhook Logs Table ───
CREATE TABLE IF NOT EXISTS `webhook_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `event` VARCHAR(50) NOT NULL DEFAULT 'push',
    `delivery_id` VARCHAR(100) DEFAULT NULL,
    `sender` VARCHAR(100) DEFAULT NULL,
    `repository` VARCHAR(150) DEFAULT NULL,
    `branch` VARCHAR(100) DEFAULT NULL,
    `commit_id` VARCHAR(50) DEFAULT NULL,
    `commit_message` TEXT DEFAULT NULL,
    `status` ENUM('success', 'failed', 'ignored', 'ping') DEFAULT 'success',
    `output` LONGTEXT DEFAULT NULL,
    `ip_address` VARCHAR(50) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Seed Default GitHub Webhook Settings ───
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('github_webhook_enabled', '1', NOW(), NOW()),
('github_repo_name', '', NOW(), NOW()),
('github_webhook_secret', '', NOW(), NOW()),
('github_webhook_branch', 'main', NOW(), NOW()),
('github_webhook_auto_pull', '1', NOW(), NOW()),
('github_webhook_custom_command', '', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key`=`key`;
