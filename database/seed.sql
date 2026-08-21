-- ═══════════════════════════════════════════════════════════════════════════
-- ASR FORM — Master Database Seed Data
-- Platform Form Builder & Word Document Generator
-- Version: 1.0 Final
-- ═══════════════════════════════════════════════════════════════════════════

SET FOREIGN_KEY_CHECKS = 0;

-- ─── 1. System Roles ───
INSERT INTO `roles` (`id`, `name`, `permissions`) VALUES
(1, 'Super Admin', '"*"'),
(2, 'Admin', '["forms","templates","documents","responses","users","applicants","settings","audit"]'),
(3, 'Editor', '["forms","templates","documents","responses"]'),
(4, 'Operator', '["documents","responses"]'),
(5, 'Approver', '["documents","approval"]'),
(6, 'User', '["forms","templates","documents","responses"]')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `permissions` = VALUES(`permissions`);

-- ─── 2. Default Super Admin User ───
-- [PENTING]: Ganti password ini setelah login pertama kali!
-- Email: admin@asrform.app
-- Password Default: admin123 (bcrypt hash)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `status`, `plan`) VALUES
(1, 'Administrator', 'admin@asrform.app', '$2y$10$L0fzbd.MYelfV0cxmO5eDuOE.1aJGoosIkUXSFkXjkeFTI/uunm62', 1, 'active', 'Enterprise')
ON DUPLICATE KEY UPDATE `role_id` = 1, `status` = 'active';

-- ─── 3. Default System & Page Settings ───
INSERT INTO `settings` (`key`, `value`) VALUES
-- Basic & Document Config
('app_name', 'ASR FORM'),
('app_timezone', 'Asia/Jakarta'),
('app_date_format', 'd/m/Y'),
('doc_paper_size', 'A4'),
('doc_default_font', 'Times New Roman'),
('upload_max_size', '10'),
('upload_extensions', 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip'),

-- Site Identity
('site_name', 'ASR FORM'),
('site_tagline', 'Platform Form Builder & Document Generator'),
('site_description', 'ASR FORM adalah platform untuk membuat formulir digital dan mengubah data menjadi dokumen secara otomatis. Buat form custom, kumpulkan data, dan generate dokumen dengan mudah.'),
('site_contact_email', 'admin@asrform.app'),
('site_contact_phone', ''),
('site_contact_address', ''),
('site_footer_text', '© 2026 ASR FORM. All rights reserved.'),
('site_og_image', ''),

-- AdSense Settings
('adsense_enabled', '0'),
('adsense_publisher_id', ''),
('adsense_auto_ads', '0'),
('adsense_form_top', '0'),
('adsense_form_bottom', '1'),
('adsense_form_success', '1'),
('adsense_public_page', '1'),
('adsense_dashboard', '0'),

-- Page: About
('page_about_enabled', '1'),
('page_about_title', 'Tentang ASR FORM'),
('page_about_content', '<h2>Apa itu ASR FORM?</h2>\n<p>ASR FORM adalah platform berbasis web untuk membuat formulir digital dan mengubah data yang terkumpul menjadi dokumen resmi secara otomatis. Dibangun dengan PHP Native modern, ASR FORM dirancang untuk instansi, organisasi, sekolah, dan bisnis yang membutuhkan sistem pengumpulan data dan pembuatan dokumen yang efisien.</p>\n\n<h2>Fitur Unggulan</h2>\n<ul>\n<li>Form Builder interaktif dengan drag & drop dan 18+ tipe field dinamis</li>\n<li>Template dokumen Microsoft Word (.docx) dengan variabel otomatis</li>\n<li>Generator PDF dan DOCX instan</li>\n<li>QR Code untuk verifikasi keabsahan dokumen publik</li>\n<li>Nomor surat otomatis dengan format kustom</li>\n<li>Manajemen dan ekspor data respons ke Excel</li>\n</ul>'),
('page_about_vision', 'Menjadi platform form builder dan document generator yang paling mudah digunakan untuk berbagai kebutuhan administrasi digital di Indonesia.'),

-- Page: Privacy Policy
('page_privacy_enabled', '1'),
('page_privacy_title', 'Kebijakan Privasi'),
('page_privacy_last_updated', '2026-08-20'),
('page_privacy_content', '<h2>1. Data yang Kami Kumpulkan</h2>\n<p>Saat mendaftar, kami mengumpulkan nama dan alamat email. Data pengisian formulir dan berkas yang diunggah disimpan dengan aman untuk kebutuhan pembuatan dokumen resmi.</p>\n<h2>2. Keamanan Data</h2>\n<p>Kami menerapkan standar keamanan enkripsi password bcrypt, proteksi CSRF, dan prepared statements pada database.</p>'),

-- Page: Terms of Service
('page_terms_enabled', '1'),
('page_terms_title', 'Syarat dan Ketentuan'),
('page_terms_last_updated', '2026-08-20'),
('page_terms_content', '<h2>1. Penggunaan Layanan</h2>\n<p>ASR FORM menyediakan layanan pembuatan formulir digital dan pembuatan dokumen otomatis. Pengguna bertanggung jawab atas konten dan keabsahan formulir yang dibuat.</p>'),

-- Page: Features
('page_features_enabled', '1'),
('page_features_title', 'Fitur Lengkap ASR FORM'),
('page_features_subtitle', 'Semua yang Anda butuhkan untuk membuat formulir digital dan menghasilkan dokumen secara otomatis.'),
('page_features_items', '[{"icon":"📝","title":"Form Builder","desc":"Buat formulir online dengan antarmuka visual drag-and-drop tanpa perlu coding."},{"icon":"🔧","title":"18+ Tipe Field Dinamis","desc":"Teks, email, nomor, tanggal, waktu, dropdown, radio, checkbox, file upload, tanda tangan digital, dll."},{"icon":"📄","title":"Word Template Engine","desc":"Upload template Microsoft Word (.docx) dengan variabel seperti {{nama}}, {{tanggal}}, {{nomor_surat}}."},{"icon":"⚡","title":"Document Generator","desc":"Ubah data respons formulir menjadi dokumen resmi secara otomatis dalam format Word dan PDF."},{"icon":"🔍","title":"QR Code Verification","desc":"Setiap dokumen dilengkapi token unik dan QR Code untuk verifikasi keabsahan secara publik."},{"icon":"📊","title":"Ekspor Excel","desc":"Rekapitulasi seluruh data respon formulir dapat diunduh ke format Excel dengan satu klik."}]'),

-- Page: Pricing
('page_pricing_enabled', '1'),
('page_pricing_title', 'Paket & Harga'),
('page_pricing_subtitle', 'Pilih paket yang sesuai dengan kebutuhan Anda.'),
('page_pricing_items', '[{"name":"Gratis","price":"Rp 0","period":"selamanya","desc":"Untuk individu dan penggunaan dasar","features":["Buat formulir tanpa batas","Kumpulkan respons","Template dokumen dasar","QR Code verifikasi"],"cta":"Mulai Gratis","highlighted":false},{"name":"Pro","price":"Hubungi Kami","period":"","desc":"Untuk tim dan organisasi","features":["Semua fitur Gratis","Template dokumen lanjutan","Ekspor data Excel","Nomor surat otomatis","Dukungan prioritas"],"cta":"Hubungi Kami","highlighted":true},{"name":"Enterprise","price":"Custom","period":"","desc":"Untuk instansi dan kebutuhan khusus","features":["Semua fitur Pro","Kustomisasi penuh","Instalasi mandiri (self-hosted)","Integrasi API","SLA khusus"],"cta":"Hubungi Kami","highlighted":false}]'),

-- Page: Contact
('page_contact_enabled', '1'),
('page_contact_title', 'Hubungi Kami'),
('page_contact_subtitle', 'Ada pertanyaan atau masukan? Kirim pesan kepada kami dan kami akan merespons secepat mungkin.'),
('page_contact_success_message', 'Pesan Anda telah berhasil dikirim. Kami akan menghubungi Anda secepatnya.'),

-- Payment Methods
('payment_qris_enabled', '1'),
('payment_qris_merchant', 'ASR FORM DIGITAL'),
('payment_qris_image', ''),
('payment_qris_instructions', 'Pindai kode QRIS di atas melalui mobile banking atau e-wallet (BCA, GoPay, OVO, DANA, ShopeePay), masukkan nominal sesuai paket, lalu simpan dan unggah bukti transfer.'),
('payment_transfer_enabled', '1'),
('payment_bank_accounts', '[{"bank":"BCA","number":"1234567890","holder":"ASR FORM DIGITAL"},{"bank":"Mandiri","number":"0987654321","holder":"ASR FORM DIGITAL"},{"bank":"BRI","number":"555566667777","holder":"ASR FORM DIGITAL"}]'),
('payment_transfer_instructions', 'Silakan lakukan transfer ke salah satu rekening resmi kami di atas. Pastikan nominal sesuai dengan paket yang dipilih. Simpan struk/bukti transfer lalu unggah pada form konfirmasi.'),

-- Gateways (WhatsApp & SMTP)
('wa_enabled', '0'),
('wa_api_key', ''),
('wa_sender', ''),
('wa_admin_number', ''),
('wa_footer', 'Sent by ASR FORM System'),
('wa_notify_admin_on_register', '1'),
('wa_notify_admin_on_payment', '1'),
('wa_notify_user_on_approval', '1'),
('smtp_enabled', '0'),
('smtp_host', 'smtp.gmail.com'),
('smtp_port', '465'),
('smtp_encryption', 'ssl'),
('smtp_username', ''),
('smtp_password', ''),
('smtp_from_name', 'ASR FORM Notification'),
('smtp_from_email', ''),
('smtp_admin_email', ''),
('smtp_notify_admin_on_register', '1'),
('smtp_notify_admin_on_payment', '1'),
('smtp_notify_user_on_approval', '1'),

-- GitHub Webhook Auto Deploy
('github_webhook_enabled', '1'),
('github_repo_name', 'Asrorulhuda/form'),
('github_webhook_secret', ''),
('github_webhook_branch', 'main'),
('github_webhook_auto_pull', '1'),
('github_webhook_custom_command', '')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- ─── 4. Default Ad Slots ───
INSERT INTO `ad_slots` (`slot_key`, `name`, `description`, `enabled`, `position`) VALUES
('FORM_TOP', 'Form Top', 'Bagian atas form publik (sebelum field)', 0, 1),
('FORM_BOTTOM', 'Form Bottom', 'Bagian bawah form publik (setelah tombol kirim)', 1, 2),
('FORM_SUCCESS', 'Form Success', 'Halaman sukses setelah submit form', 1, 3),
('PUBLIC_PAGE', 'Public Page', 'Halaman publik (landing, about, features, dll)', 1, 4),
('DASHBOARD', 'Dashboard', 'Dashboard pengguna yang login', 0, 5)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ─── 5. Default Numbering Configs ───
INSERT INTO `numbering_configs` (`name`, `format`, `code`, `current_sequence`, `reset_period`) VALUES
('Surat Keterangan', '{{sequence}}/SK/{{roman_month}}/{{year}}', 'SK', 0, 'yearly'),
('Surat Tugas', '{{sequence}}/ST/{{roman_month}}/{{year}}', 'ST', 0, 'yearly')
ON DUPLICATE KEY UPDATE `format` = VALUES(`format`);

SET FOREIGN_KEY_CHECKS = 1;
