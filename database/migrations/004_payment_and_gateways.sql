-- ═══════════════════════════════════════════
-- ASR FORM — Payment & Gateway Migration
-- Version: 1.2
-- ═══════════════════════════════════════════

USE `asr_form`;

-- ─── Payments Table ───
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
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
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Seed Default Payment Settings ───
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('payment_qris_enabled', '1', NOW(), NOW()),
('payment_qris_merchant', 'ASR FORM DIGITAL', NOW(), NOW()),
('payment_qris_image', '', NOW(), NOW()),
('payment_qris_instructions', 'Silakan buka aplikasi BCA, GoPay, OVO, DANA, ShopeePay, atau Mobile Banking Anda. Pindai (scan) kode QRIS di atas, masukkan nominal sesuai paket, lalu simpan bukti transfer dan unggah pada form di bawah.', NOW(), NOW()),

('payment_transfer_enabled', '1', NOW(), NOW()),
('payment_bank_accounts', '[{"bank":"BCA","number":"1234567890","holder":"ASR FORM DIGITAL"},{"bank":"Mandiri","number":"0987654321","holder":"ASR FORM DIGITAL"},{"bank":"BRI","number":"555566667777","holder":"ASR FORM DIGITAL"}]', NOW(), NOW()),
('payment_transfer_instructions', 'Silakan lakukan transfer ke salah satu rekening resmi kami di atas. Pastikan nominal transfer sesuai dengan harga paket yang dipilih. Simpan struk/bukti transfer lalu unggah pada formulir konfirmasi di bawah.', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key`=`key`;

-- ─── Seed Default Gateway Settings (WhatsApp & Gmail) ───
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
-- WhatsApp Gateway (asr-desain.my.id)
('wa_enabled', '0', NOW(), NOW()),
('wa_api_key', '', NOW(), NOW()),
('wa_sender', '', NOW(), NOW()),
('wa_admin_number', '', NOW(), NOW()),
('wa_footer', 'Sent by ASR FORM System', NOW(), NOW()),
('wa_notify_admin_on_register', '1', NOW(), NOW()),
('wa_notify_admin_on_payment', '1', NOW(), NOW()),
('wa_notify_user_on_approval', '1', NOW(), NOW()),

-- Gmail / SMTP Gateway
('smtp_enabled', '0', NOW(), NOW()),
('smtp_host', 'smtp.gmail.com', NOW(), NOW()),
('smtp_port', '465', NOW(), NOW()),
('smtp_encryption', 'ssl', NOW(), NOW()),
('smtp_username', '', NOW(), NOW()),
('smtp_password', '', NOW(), NOW()),
('smtp_from_name', 'ASR FORM Notification', NOW(), NOW()),
('smtp_from_email', '', NOW(), NOW()),
('smtp_admin_email', '', NOW(), NOW()),
('smtp_notify_admin_on_register', '1', NOW(), NOW()),
('smtp_notify_admin_on_payment', '1', NOW(), NOW()),
('smtp_notify_user_on_approval', '1', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key`=`key`;
