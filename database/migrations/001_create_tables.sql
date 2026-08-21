-- ═══════════════════════════════════════════
-- ASR FORM — Database Schema
-- Version: 1.0
-- ═══════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS `asr_form` 
    DEFAULT CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `asr_form`;

-- ─── Roles ───
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `permissions` TEXT DEFAULT NULL COMMENT 'JSON array or * for all',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Users ───
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'active', 'inactive', 'rejected') DEFAULT 'pending',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Forms ───
CREATE TABLE IF NOT EXISTS `forms` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `status` ENUM('draft', 'published', 'closed', 'archived') DEFAULT 'draft',
    `settings_json` JSON DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_forms_slug` (`slug`),
    INDEX `idx_forms_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Form Fields ───
CREATE TABLE IF NOT EXISTS `form_fields` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `form_id` INT UNSIGNED NOT NULL,
    `field_type` VARCHAR(30) NOT NULL COMMENT 'text, textarea, number, email, phone, date, time, datetime, dropdown, radio, checkbox, file, image, rating, scale, signature, heading, description, section',
    `field_name` VARCHAR(100) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `placeholder` VARCHAR(255) DEFAULT NULL,
    `options_json` JSON DEFAULT NULL COMMENT 'For dropdown, radio, checkbox',
    `validation_json` JSON DEFAULT NULL COMMENT 'min, max, pattern, etc.',
    `conditional_json` JSON DEFAULT NULL COMMENT 'Show/hide logic',
    `settings_json` JSON DEFAULT NULL COMMENT 'Additional field settings',
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_required` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`form_id`) REFERENCES `forms`(`id`) ON DELETE CASCADE,
    INDEX `idx_fields_form` (`form_id`),
    INDEX `idx_fields_order` (`form_id`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Form Responses ───
CREATE TABLE IF NOT EXISTS `form_responses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `form_id` INT UNSIGNED NOT NULL,
    `respondent_id` INT UNSIGNED DEFAULT NULL,
    `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    FOREIGN KEY (`form_id`) REFERENCES `forms`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`respondent_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_responses_form` (`form_id`),
    INDEX `idx_responses_date` (`submitted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Form Response Values (EAV pattern) ───
CREATE TABLE IF NOT EXISTS `form_response_values` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `response_id` INT UNSIGNED NOT NULL,
    `field_id` INT UNSIGNED NOT NULL,
    `value_text` TEXT DEFAULT NULL,
    `value_json` JSON DEFAULT NULL,
    FOREIGN KEY (`response_id`) REFERENCES `form_responses`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`field_id`) REFERENCES `form_fields`(`id`) ON DELETE CASCADE,
    INDEX `idx_values_response` (`response_id`),
    INDEX `idx_values_field` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Document Templates ───
CREATE TABLE IF NOT EXISTS `document_templates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) DEFAULT NULL,
    `content` LONGTEXT DEFAULT NULL COMMENT 'HTML template content with {{variables}}',
    `settings_json` JSON DEFAULT NULL,
    `status` ENUM('active', 'archived') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Documents ───
CREATE TABLE IF NOT EXISTS `documents` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `template_id` INT UNSIGNED DEFAULT NULL,
    `form_response_id` INT UNSIGNED DEFAULT NULL,
    `document_number` VARCHAR(100) DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `content` LONGTEXT DEFAULT NULL COMMENT 'Rendered HTML content',
    `status` ENUM('draft', 'submitted', 'pending', 'approved', 'rejected', 'generated', 'archived') DEFAULT 'draft',
    `verification_token` VARCHAR(64) DEFAULT NULL UNIQUE,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `approved_by` INT UNSIGNED DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`template_id`) REFERENCES `document_templates`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`form_response_id`) REFERENCES `form_responses`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_documents_status` (`status`),
    INDEX `idx_documents_token` (`verification_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Document Variables ───
CREATE TABLE IF NOT EXISTS `document_variables` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `template_id` INT UNSIGNED NOT NULL,
    `variable_name` VARCHAR(100) NOT NULL,
    `label` VARCHAR(255) DEFAULT NULL,
    `source_type` ENUM('system', 'user', 'form', 'custom') DEFAULT 'custom',
    `source_key` VARCHAR(255) DEFAULT NULL,
    `default_value` TEXT DEFAULT NULL,
    FOREIGN KEY (`template_id`) REFERENCES `document_templates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Approvals ───
CREATE TABLE IF NOT EXISTS `approvals` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `document_id` INT UNSIGNED NOT NULL,
    `approver_id` INT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `notes` TEXT DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    FOREIGN KEY (`document_id`) REFERENCES `documents`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`approver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── File Uploads ───
CREATE TABLE IF NOT EXISTS `file_uploads` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `stored_filename` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) DEFAULT NULL,
    `size` INT UNSIGNED DEFAULT 0,
    `path` VARCHAR(500) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Audit Logs ───
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(50) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `record_id` INT UNSIGNED DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_audit_user` (`user_id`),
    INDEX `idx_audit_module` (`module`),
    INDEX `idx_audit_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Settings ───
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Document Numbering Config ───
CREATE TABLE IF NOT EXISTS `numbering_configs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `format` VARCHAR(255) NOT NULL COMMENT 'e.g., {{sequence}}/{{code}}/{{roman_month}}/{{year}}',
    `code` VARCHAR(20) DEFAULT NULL,
    `current_sequence` INT UNSIGNED DEFAULT 0,
    `reset_period` ENUM('yearly', 'monthly', 'never') DEFAULT 'yearly',
    `last_reset` DATE DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
