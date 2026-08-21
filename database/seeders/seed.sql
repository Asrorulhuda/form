-- ═══════════════════════════════════════════
-- ASR FORM — Default Data Seeder
-- ═══════════════════════════════════════════

USE `asr_form`;

-- ─── Roles ───
INSERT INTO `roles` (`id`, `name`, `permissions`) VALUES
(1, 'Super Admin', '"*"'),
(2, 'Admin', '["forms","templates","documents","responses","users","applicants","settings","audit"]'),
(3, 'Editor', '["forms","templates","documents","responses"]'),
(4, 'Operator', '["documents","responses"]'),
(5, 'Approver', '["documents","approval"]'),
(6, 'User', '["forms","templates","documents","responses"]')
ON DUPLICATE KEY UPDATE `permissions` = VALUES(`permissions`);

-- ─── Super Admin User ───
-- [IMPORTANT PRODUCTION NOTICE]: Ganti password ini segera setelah instalasi pertama kali!
-- Email: admin@asrform.app
-- Default Password: admin123 (bcrypt hash)
INSERT INTO `users` (`name`, `email`, `password`, `role_id`, `status`) VALUES
('Administrator', 'admin@asrform.app', '$2y$10$L0fzbd.MYelfV0cxmO5eDuOE.1aJGoosIkUXSFkXjkeFTI/uunm62', 1, 'active')
ON DUPLICATE KEY UPDATE `role_id` = 1, `status` = 'active';

-- ─── Default Settings ───
INSERT INTO `settings` (`key`, `value`) VALUES
('app_name', 'ASR FORM'),
('app_timezone', 'Asia/Jakarta'),
('app_date_format', 'd/m/Y'),
('doc_paper_size', 'A4'),
('doc_default_font', 'Times New Roman'),
('upload_max_size', '5'),
('upload_extensions', 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx');

-- ─── Default Numbering Config ───
INSERT INTO `numbering_configs` (`name`, `format`, `code`, `current_sequence`, `reset_period`) VALUES
('Surat Keterangan', '{{sequence}}/SK/{{roman_month}}/{{year}}', 'SK', 0, 'yearly'),
('Surat Tugas', '{{sequence}}/ST/{{roman_month}}/{{year}}', 'ST', 0, 'yearly');
