-- ═══════════════════════════════════════════════════════════════════════════
-- ASR FORM — Multi-SaaS Database Migration Schema
-- Platform Form Builder & Word Document Generator
-- Version: 2.0 (Multi-SaaS Refactor)
-- ═══════════════════════════════════════════════════════════════════════════

SET FOREIGN_KEY_CHECKS = 0;

-- ─── 1. Roles Table (Simplified: 3 roles only) ───
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `permissions` TEXT DEFAULT NULL COMMENT 'JSON array or * for all',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 2. Admins Table (Tenant Owners + Super Admin) ───
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(30) DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT UNSIGNED NOT NULL DEFAULT 2 COMMENT '1=Super Admin, 2=Admin',
    `status` ENUM('pending', 'active', 'inactive', 'rejected') DEFAULT 'pending',
    `plan` VARCHAR(50) DEFAULT 'Gratis',
    `wa_sender` VARCHAR(30) DEFAULT NULL COMMENT 'WA sender number per tenant',
    `wa_api_key` VARCHAR(255) DEFAULT NULL COMMENT 'WA API key per tenant',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_admins_role` (`role_id`),
    INDEX `idx_admins_status` (`status`),
    CONSTRAINT `fk_admins_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 3. Users Table (End-users belong to an Admin) ───
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT UNSIGNED NOT NULL COMMENT 'Belongs to which admin/tenant',
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(30) DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role_id` INT UNSIGNED NOT NULL DEFAULT 3 COMMENT '3=User',
    `status` ENUM('pending', 'active', 'inactive') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_admin` (`admin_id`),
    INDEX `idx_users_role` (`role_id`),
    INDEX `idx_users_status` (`status`),
    CONSTRAINT `fk_users_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 4. Document Templates Table (Tenant-scoped) ───
CREATE TABLE IF NOT EXISTS `document_templates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT UNSIGNED NOT NULL COMMENT 'Tenant owner',
    `user_id` INT UNSIGNED DEFAULT NULL COMMENT 'Created by which user (legacy compat)',
    `name` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `file_path` VARCHAR(255) DEFAULT NULL,
    `original_filename` VARCHAR(255) DEFAULT NULL,
    `version` INT UNSIGNED DEFAULT 1,
    `content` LONGTEXT DEFAULT NULL COMMENT 'HTML template content with {{variables}}',
    `settings_json` JSON DEFAULT NULL,
    `status` ENUM('active', 'archived', 'inactive') DEFAULT 'active',
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_templates_admin` (`admin_id`),
    CONSTRAINT `fk_templates_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 5. Document Template Variables Table ───
CREATE TABLE IF NOT EXISTS `document_template_variables` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 6. Document Template Versions Table ───
CREATE TABLE IF NOT EXISTS `document_template_versions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `template_id` INT UNSIGNED NOT NULL,
    `version` INT UNSIGNED NOT NULL DEFAULT 1,
    `file_path` VARCHAR(255) NOT NULL,
    `variables_json` JSON DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_dtvers_template` (`template_id`),
    CONSTRAINT `fk_dtvers_template` FOREIGN KEY (`template_id`) REFERENCES `document_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 7. Forms Table (Tenant-scoped) ───
CREATE TABLE IF NOT EXISTS `forms` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT UNSIGNED NOT NULL COMMENT 'Tenant owner',
    `user_id` INT UNSIGNED DEFAULT NULL COMMENT 'Legacy compat',
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `template_id` INT UNSIGNED DEFAULT NULL,
    `status` ENUM('draft', 'published', 'closed', 'archived') DEFAULT 'draft',
    `settings_json` JSON DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_forms_slug` (`slug`),
    INDEX `idx_forms_status` (`status`),
    INDEX `idx_forms_admin` (`admin_id`),
    CONSTRAINT `fk_forms_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 8. Form Fields Table ───
CREATE TABLE IF NOT EXISTS `form_fields` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `form_id` INT UNSIGNED NOT NULL,
    `field_type` VARCHAR(30) NOT NULL COMMENT 'text, textarea, number, email, phone, date, time, datetime, dropdown, radio, checkbox, file, image, rating, scale, signature, heading, description, section',
    `field_name` VARCHAR(100) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `placeholder` VARCHAR(255) DEFAULT NULL,
    `options_json` JSON DEFAULT NULL,
    `validation_json` JSON DEFAULT NULL,
    `conditional_json` JSON DEFAULT NULL,
    `settings_json` JSON DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_required` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_fields_form` (`form_id`),
    INDEX `idx_fields_order` (`form_id`, `sort_order`),
    CONSTRAINT `fk_fields_form` FOREIGN KEY (`form_id`) REFERENCES `forms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 9. Form Responses Table ───
CREATE TABLE IF NOT EXISTS `form_responses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `form_id` INT UNSIGNED NOT NULL,
    `respondent_id` INT UNSIGNED DEFAULT NULL,
    `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    INDEX `idx_responses_form` (`form_id`),
    INDEX `idx_responses_date` (`submitted_at`),
    CONSTRAINT `fk_responses_form` FOREIGN KEY (`form_id`) REFERENCES `forms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 10. Form Response Values Table (EAV Pattern) ───
CREATE TABLE IF NOT EXISTS `form_response_values` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `response_id` INT UNSIGNED NOT NULL,
    `field_id` INT UNSIGNED NOT NULL,
    `value_text` TEXT DEFAULT NULL,
    `value_json` JSON DEFAULT NULL,
    INDEX `idx_values_response` (`response_id`),
    INDEX `idx_values_field` (`field_id`),
    CONSTRAINT `fk_values_response` FOREIGN KEY (`response_id`) REFERENCES `form_responses`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_values_field` FOREIGN KEY (`field_id`) REFERENCES `form_fields`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 11. Documents Table (Tenant-scoped) ───
CREATE TABLE IF NOT EXISTS `documents` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT UNSIGNED NOT NULL COMMENT 'Tenant owner',
    `template_id` INT UNSIGNED DEFAULT NULL,
    `template_version_id` INT UNSIGNED DEFAULT NULL,
    `form_response_id` INT UNSIGNED DEFAULT NULL,
    `document_number` VARCHAR(100) DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `file_path_docx` VARCHAR(255) DEFAULT NULL,
    `file_path_pdf` VARCHAR(255) DEFAULT NULL,
    `content` LONGTEXT DEFAULT NULL COMMENT 'Rendered HTML content',
    `status` ENUM('draft', 'submitted', 'pending', 'approved', 'rejected', 'generated', 'archived') DEFAULT 'draft',
    `verification_token` VARCHAR(64) DEFAULT NULL UNIQUE,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `approved_by` INT UNSIGNED DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_documents_admin` (`admin_id`),
    INDEX `idx_documents_status` (`status`),
    INDEX `idx_documents_token` (`verification_token`),
    CONSTRAINT `fk_documents_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_documents_template` FOREIGN KEY (`template_id`) REFERENCES `document_templates`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_documents_response` FOREIGN KEY (`form_response_id`) REFERENCES `form_responses`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 12. Document Variables Table ───
CREATE TABLE IF NOT EXISTS `document_variables` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `template_id` INT UNSIGNED NOT NULL,
    `variable_name` VARCHAR(100) NOT NULL,
    `label` VARCHAR(255) DEFAULT NULL,
    `source_type` ENUM('system', 'user', 'form', 'custom') DEFAULT 'custom',
    `source_key` VARCHAR(255) DEFAULT NULL,
    `default_value` TEXT DEFAULT NULL,
    CONSTRAINT `fk_doc_variables_template` FOREIGN KEY (`template_id`) REFERENCES `document_templates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 13. Approvals Table ───
CREATE TABLE IF NOT EXISTS `approvals` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `document_id` INT UNSIGNED NOT NULL,
    `approver_id` INT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `notes` TEXT DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    CONSTRAINT `fk_approvals_document` FOREIGN KEY (`document_id`) REFERENCES `documents`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 14. File Uploads Table ───
CREATE TABLE IF NOT EXISTS `file_uploads` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT UNSIGNED DEFAULT NULL,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `stored_filename` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) DEFAULT NULL,
    `size` INT UNSIGNED DEFAULT 0,
    `path` VARCHAR(500) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 15. Audit Logs Table ───
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `user_type` VARCHAR(20) DEFAULT 'admin' COMMENT 'Which table the user_id refers to',
    `user_name` VARCHAR(100) DEFAULT NULL,
    `action` VARCHAR(50) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `record_id` INT UNSIGNED DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_audit_user` (`user_id`),
    INDEX `idx_audit_module` (`module`),
    INDEX `idx_audit_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 16. Settings Table (Global — managed by Super Admin) ───
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 17. Numbering Configs Table (Tenant-scoped) ───
CREATE TABLE IF NOT EXISTS `numbering_configs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT UNSIGNED NOT NULL COMMENT 'Tenant owner',
    `name` VARCHAR(100) NOT NULL,
    `format` VARCHAR(255) NOT NULL COMMENT 'e.g., {{sequence}}/{{code}}/{{roman_month}}/{{year}}',
    `code` VARCHAR(20) DEFAULT NULL,
    `current_sequence` INT UNSIGNED DEFAULT 0,
    `reset_period` ENUM('yearly', 'monthly', 'never') DEFAULT 'yearly',
    `last_reset` DATE DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_numbering_admin` (`admin_id`),
    CONSTRAINT `fk_numbering_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 18. Ad Slots Table ───
CREATE TABLE IF NOT EXISTS `ad_slots` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slot_key` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `ad_code` TEXT DEFAULT NULL,
    `enabled` TINYINT(1) DEFAULT 1,
    `position` INT UNSIGNED DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 19. Contact Messages Table ───
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 20. Payments Table ───
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT UNSIGNED NOT NULL COMMENT 'Admin paying for plan',
    `plan` VARCHAR(50) NOT NULL,
    `amount` DECIMAL(12, 2) DEFAULT 0.00,
    `payment_method` ENUM('qris', 'transfer') NOT NULL,
    `bank_name` VARCHAR(50) DEFAULT NULL,
    `account_number` VARCHAR(50) DEFAULT NULL,
    `sender_name` VARCHAR(100) DEFAULT NULL,
    `sender_phone` VARCHAR(30) DEFAULT NULL,
    `proof_file` VARCHAR(255) NOT NULL,
    `notes` TEXT DEFAULT NULL,
    `status` ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    `verified_by` INT UNSIGNED DEFAULT NULL,
    `verified_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_payments_status` (`status`),
    INDEX `idx_payments_admin` (`admin_id`),
    CONSTRAINT `fk_payments_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── 21. Webhook Logs Table ───
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

SET FOREIGN_KEY_CHECKS = 1;
