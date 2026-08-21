-- Migration 002: Word Template Engine Schema
USE `asr_form`;

-- 1. Update document_templates table
ALTER TABLE `document_templates`
ADD COLUMN `description` TEXT NULL AFTER `category`,
ADD COLUMN `file_path` VARCHAR(255) NULL AFTER `description`,
ADD COLUMN `original_filename` VARCHAR(255) NULL AFTER `file_path`,
ADD COLUMN `version` INT UNSIGNED DEFAULT 1 AFTER `original_filename`,
ADD COLUMN `created_by` INT UNSIGNED NULL AFTER `status`;

-- 2. Create document_template_variables table
CREATE TABLE IF NOT EXISTS `document_template_variables` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `template_id` INT UNSIGNED NOT NULL,
    `variable_name` VARCHAR(100) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `source_type` ENUM('system', 'user', 'form_response', 'setting', 'custom') DEFAULT 'form_response',
    `source_key` VARCHAR(100) NULL,
    `default_value` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_dtv_template` (`template_id`),
    INDEX `idx_dtv_var_name` (`variable_name`),
    CONSTRAINT `fk_dtv_template` FOREIGN KEY (`template_id`) REFERENCES `document_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create document_template_versions table
CREATE TABLE IF NOT EXISTS `document_template_versions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `template_id` INT UNSIGNED NOT NULL,
    `version` INT UNSIGNED NOT NULL DEFAULT 1,
    `file_path` VARCHAR(255) NOT NULL,
    `variables_json` JSON NULL,
    `created_by` INT UNSIGNED NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_dtvers_template` (`template_id`),
    CONSTRAINT `fk_dtvers_template` FOREIGN KEY (`template_id`) REFERENCES `document_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Update documents table
ALTER TABLE `documents`
ADD COLUMN `template_version_id` INT UNSIGNED NULL AFTER `template_id`,
ADD COLUMN `file_path_docx` VARCHAR(255) NULL AFTER `title`,
ADD COLUMN `file_path_pdf` VARCHAR(255) NULL AFTER `file_path_docx`;
