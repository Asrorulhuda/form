-- ═══════════════════════════════════════════
-- ASR FORM — AdSense & Public Pages Migration
-- Version: 1.1
-- ═══════════════════════════════════════════

USE `asr_form`;

-- ─── Ad Slots ───
CREATE TABLE IF NOT EXISTS `ad_slots` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slot_key` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `ad_code` TEXT DEFAULT NULL COMMENT 'Custom ad code override (optional)',
    `enabled` TINYINT(1) DEFAULT 1,
    `position` INT UNSIGNED DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Users Table Extension (Plan) ───
-- Add plan column if not exists
SET @exist := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'asr_form' AND TABLE_NAME = 'users' AND COLUMN_NAME = 'plan');
SET @sqlstmt := IF(@exist = 0, 'ALTER TABLE `users` ADD COLUMN `plan` VARCHAR(50) DEFAULT "Gratis" AFTER `status`', 'SELECT 1');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Default ad slots
INSERT INTO `ad_slots` (`slot_key`, `name`, `description`, `enabled`, `position`) VALUES
('FORM_TOP', 'Form Top', 'Bagian atas form publik (sebelum field)', 0, 1),
('FORM_BOTTOM', 'Form Bottom', 'Bagian bawah form publik (setelah tombol kirim)', 1, 2),
('FORM_SUCCESS', 'Form Success', 'Halaman sukses setelah submit form', 1, 3),
('PUBLIC_PAGE', 'Public Page', 'Halaman publik (landing, about, features, dll)', 1, 4),
('DASHBOARD', 'Dashboard', 'Dashboard pengguna yang login', 0, 5)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ─── Contact Messages ───
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

-- ─── Seed Default Settings ───

-- Site Settings
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('site_name', 'ASR FORM', NOW(), NOW()),
('site_tagline', 'Platform Form Builder & Document Generator', NOW(), NOW()),
('site_description', 'ASR FORM adalah platform untuk membuat formulir digital dan mengubah data menjadi dokumen secara otomatis. Buat form custom, kumpulkan data, dan generate dokumen dengan mudah.', NOW(), NOW()),
('site_contact_email', '', NOW(), NOW()),
('site_contact_phone', '', NOW(), NOW()),
('site_contact_address', '', NOW(), NOW()),
('site_footer_text', '© 2026 ASR FORM. All rights reserved.', NOW(), NOW()),
('site_og_image', '', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key`=`key`;

-- AdSense Settings
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('adsense_enabled', '0', NOW(), NOW()),
('adsense_publisher_id', '', NOW(), NOW()),
('adsense_auto_ads', '0', NOW(), NOW()),
('adsense_form_top', '0', NOW(), NOW()),
('adsense_form_bottom', '1', NOW(), NOW()),
('adsense_form_success', '1', NOW(), NOW()),
('adsense_public_page', '1', NOW(), NOW()),
('adsense_dashboard', '0', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key`=`key`;

-- Page: About
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('page_about_enabled', '1', NOW(), NOW()),
('page_about_title', 'Tentang ASR FORM', NOW(), NOW()),
('page_about_content', '<h2>Apa itu ASR FORM?</h2>\n<p>ASR FORM adalah platform berbasis web untuk membuat formulir digital dan mengubah data yang terkumpul menjadi dokumen resmi secara otomatis. Dibangun dengan PHP Native, ASR FORM dirancang untuk instansi, organisasi, dan bisnis yang membutuhkan sistem pengumpulan data dan pembuatan dokumen yang efisien.</p>\n\n<h2>Tujuan</h2>\n<p>Menyederhanakan proses pembuatan formulir online dan otomatisasi dokumen sehingga pengguna tidak perlu membuat sistem dari awal. Dengan ASR FORM, Anda dapat membuat formulir custom, mengumpulkan respons, dan menghasilkan surat atau dokumen resmi lengkap dengan nomor surat otomatis dan QR Code verifikasi.</p>\n\n<h2>Siapa yang Dapat Menggunakan?</h2>\n<ul>\n<li>Instansi pemerintah untuk surat keterangan dan formulir layanan</li>\n<li>Lembaga pendidikan untuk pendaftaran dan administrasi</li>\n<li>Organisasi dan komunitas untuk pendataan anggota</li>\n<li>Bisnis untuk formulir pemesanan dan survei</li>\n<li>Siapa saja yang membutuhkan form digital dan dokumen otomatis</li>\n</ul>\n\n<h2>Fitur Utama</h2>\n<ul>\n<li>Form Builder dengan 18+ tipe field dinamis</li>\n<li>Template dokumen Word dengan variabel otomatis</li>\n<li>Generator PDF dan DOCX</li>\n<li>QR Code untuk verifikasi dokumen</li>\n<li>Nomor surat otomatis dengan format kustom</li>\n<li>Manajemen respons dan ekspor data</li>\n</ul>', NOW(), NOW()),
('page_about_vision', 'Menjadi platform form builder dan document generator yang paling mudah digunakan untuk berbagai kebutuhan administrasi digital di Indonesia.', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key`=`key`;

-- Page: Privacy Policy
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('page_privacy_enabled', '1', NOW(), NOW()),
('page_privacy_title', 'Kebijakan Privasi', NOW(), NOW()),
('page_privacy_last_updated', '2026-08-20', NOW(), NOW()),
('page_privacy_content', '<h2>1. Data yang Kami Kumpulkan</h2>\n<h3>Data Akun</h3>\n<p>Saat mendaftar, kami mengumpulkan nama, alamat email, dan kata sandi (disimpan dalam bentuk terenkripsi). Data ini diperlukan untuk mengidentifikasi dan mengautentikasi akun Anda.</p>\n\n<h3>Data Formulir</h3>\n<p>Pengguna yang membuat formulir menyimpan konfigurasi field, judul, dan deskripsi formulir. Data ini disimpan di server kami untuk menampilkan formulir kepada responden.</p>\n\n<h3>Data Respons</h3>\n<p>Ketika seseorang mengisi formulir publik, data yang dimasukkan (termasuk teks, pilihan, file upload, dan tanda tangan digital) disimpan dalam database kami. Data ini hanya dapat diakses oleh pemilik formulir dan administrator.</p>\n\n<h3>File Upload</h3>\n<p>File yang diunggah melalui formulir disimpan di server kami. File ini hanya dapat diakses oleh pemilik formulir dan administrator sistem.</p>\n\n<h3>Data Teknis</h3>\n<p>Kami mencatat alamat IP dan informasi user agent browser saat respons formulir dikirimkan, untuk keperluan keamanan dan audit.</p>\n\n<h2>2. Penggunaan Data</h2>\n<p>Data yang kami kumpulkan digunakan untuk:</p>\n<ul>\n<li>Menyediakan layanan formulir dan pembuatan dokumen</li>\n<li>Mengelola akun pengguna</li>\n<li>Menghasilkan dokumen berdasarkan template dan data respons</li>\n<li>Keamanan sistem dan pencegahan penyalahgunaan</li>\n<li>Audit dan pencatatan aktivitas sistem</li>\n</ul>\n\n<h2>3. Cookies</h2>\n<p>ASR FORM menggunakan cookie sesi (session cookie) untuk menjaga autentikasi pengguna dan token keamanan CSRF. Cookie ini bersifat esensial untuk fungsi aplikasi dan tidak digunakan untuk pelacakan.</p>\n\n<h2>4. Layanan Pihak Ketiga</h2>\n<p>Jika fitur iklan diaktifkan, layanan pihak ketiga (seperti Google AdSense) dapat menggunakan cookie atau teknologi serupa untuk menampilkan iklan yang relevan sesuai dengan kebijakan dan pengaturan yang berlaku pada layanan tersebut. Anda dapat mengelola preferensi iklan melalui pengaturan browser atau melalui halaman pengaturan iklan Google.</p>\n\n<h2>5. Keamanan Data</h2>\n<p>Kami menerapkan langkah-langkah keamanan termasuk:</p>\n<ul>\n<li>Enkripsi kata sandi menggunakan bcrypt</li>\n<li>Proteksi CSRF pada semua formulir</li>\n<li>Prepared statements untuk mencegah SQL injection</li>\n<li>Validasi dan sanitasi input</li>\n<li>Pembatasan akses berdasarkan peran pengguna</li>\n</ul>\n\n<h2>6. Penyimpanan Data</h2>\n<p>Data disimpan di server selama akun aktif atau selama data diperlukan untuk menyediakan layanan. Pemilik formulir dapat menghapus respons dan data terkait kapan saja melalui dashboard.</p>\n\n<h2>7. Hak Pengguna</h2>\n<p>Anda berhak untuk:</p>\n<ul>\n<li>Mengakses data akun Anda</li>\n<li>Memperbarui informasi akun</li>\n<li>Menghapus respons formulir yang Anda miliki</li>\n<li>Meminta penghapusan akun dengan menghubungi administrator</li>\n</ul>\n\n<h2>8. Perubahan Kebijakan</h2>\n<p>Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Perubahan akan dipublikasikan di halaman ini dengan tanggal pembaruan terbaru.</p>\n\n<h2>9. Kontak</h2>\n<p>Jika Anda memiliki pertanyaan tentang kebijakan privasi ini, silakan hubungi kami melalui halaman Kontak.</p>', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key`=`key`;

-- Page: Terms of Service
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('page_terms_enabled', '1', NOW(), NOW()),
('page_terms_title', 'Syarat dan Ketentuan', NOW(), NOW()),
('page_terms_last_updated', '2026-08-20', NOW(), NOW()),
('page_terms_content', '<h2>1. Penggunaan Layanan</h2>\n<p>ASR FORM menyediakan layanan pembuatan formulir digital dan pembuatan dokumen otomatis. Dengan menggunakan layanan ini, Anda menyetujui syarat dan ketentuan yang berlaku.</p>\n\n<h2>2. Akun Pengguna</h2>\n<p>Untuk menggunakan fitur lengkap ASR FORM, Anda perlu mendaftarkan akun. Anda bertanggung jawab untuk menjaga kerahasiaan informasi akun Anda, termasuk kata sandi. Akun baru memerlukan persetujuan administrator sebelum dapat digunakan.</p>\n\n<h2>3. Konten Pengguna</h2>\n<p>Anda bertanggung jawab penuh atas konten yang Anda buat melalui ASR FORM, termasuk formulir, template dokumen, dan data yang dikumpulkan. Pastikan konten Anda tidak melanggar hukum yang berlaku.</p>\n\n<h2>4. Larangan Penyalahgunaan</h2>\n<p>Anda dilarang menggunakan ASR FORM untuk:</p>\n<ul>\n<li>Mengumpulkan data secara ilegal atau tanpa persetujuan</li>\n<li>Membuat formulir yang menyesatkan atau menipu</li>\n<li>Mendistribusikan malware atau konten berbahaya</li>\n<li>Mengganggu atau merusak sistem atau layanan</li>\n<li>Melanggar hak kekayaan intelektual pihak lain</li>\n<li>Aktivitas spam atau pengiriman massal yang tidak diminta</li>\n</ul>\n\n<h2>5. Keamanan Akun</h2>\n<p>Anda wajib menjaga keamanan akun Anda. Jangan membagikan kredensial akun kepada pihak lain. Segera laporkan jika Anda mencurigai akses tidak sah ke akun Anda.</p>\n\n<h2>6. Penggunaan Formulir</h2>\n<p>Formulir yang dibuat melalui ASR FORM harus digunakan untuk tujuan yang sah. Pemilik formulir bertanggung jawab untuk menginformasikan responden tentang pengumpulan dan penggunaan data mereka.</p>\n\n<h2>7. Dokumen</h2>\n<p>Dokumen yang dihasilkan oleh ASR FORM berdasarkan template dan data yang Anda berikan. Keakuratan dan keabsahan isi dokumen menjadi tanggung jawab pengguna. ASR FORM menyediakan alat bantu pembuatan dokumen, bukan jaminan keabsahan hukum.</p>\n\n<h2>8. Pembatasan Layanan</h2>\n<p>ASR FORM disediakan \"sebagaimana adanya\". Kami berupaya menjaga ketersediaan layanan, namun tidak menjamin layanan akan selalu tersedia tanpa gangguan. Kami berhak melakukan pemeliharaan yang dapat menyebabkan gangguan sementara.</p>\n\n<h2>9. Penghentian Akun</h2>\n<p>Kami berhak menonaktifkan atau menghapus akun yang melanggar syarat dan ketentuan ini. Pengguna juga dapat meminta penghentian akun dengan menghubungi administrator.</p>\n\n<h2>10. Perubahan Layanan</h2>\n<p>Kami berhak mengubah, memperbarui, atau menghentikan fitur layanan kapan saja. Perubahan signifikan akan diinformasikan melalui platform.</p>\n\n<h2>11. Tanggung Jawab Pengguna</h2>\n<p>Pengguna bertanggung jawab atas semua aktivitas yang dilakukan melalui akun mereka, termasuk data yang dikumpulkan, dokumen yang dibuat, dan kepatuhan terhadap peraturan yang berlaku.</p>\n\n<h2>12. Kontak</h2>\n<p>Untuk pertanyaan mengenai syarat dan ketentuan ini, silakan hubungi kami melalui halaman Kontak.</p>', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key`=`key`;

-- Page: Features
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('page_features_enabled', '1', NOW(), NOW()),
('page_features_title', 'Fitur ASR FORM', NOW(), NOW()),
('page_features_subtitle', 'Semua yang Anda butuhkan untuk membuat formulir digital dan menghasilkan dokumen secara otomatis.', NOW(), NOW()),
('page_features_items', '[{"icon":"📝","title":"Form Builder","desc":"Buat formulir online dengan antarmuka visual drag-and-drop tanpa perlu coding. Atur field, validasi, dan tampilan sesuai kebutuhan."},{"icon":"🔧","title":"18+ Tipe Field Dinamis","desc":"Mendukung teks, email, nomor, tanggal, waktu, dropdown, radio button, checkbox, file upload, gambar, rating, skala, tanda tangan digital, heading, deskripsi, dan lainnya."},{"icon":"📊","title":"Conditional Logic","desc":"Tampilkan atau sembunyikan field berdasarkan jawaban responden. Buat formulir yang dinamis dan interaktif sesuai kebutuhan."},{"icon":"📥","title":"Response Management","desc":"Kelola semua respons yang masuk dalam satu dashboard. Filter, cari, dan ekspor data ke Excel dengan mudah."},{"icon":"📄","title":"Word Template Engine","desc":"Upload template Microsoft Word (.docx) dengan variabel seperti {{nama}}, {{tanggal}}, {{nomor_surat}}. Data dari formulir akan mengisi variabel secara otomatis."},{"icon":"⚡","title":"Document Generator","desc":"Ubah data respons formulir menjadi dokumen resmi secara otomatis. Satu klik untuk menghasilkan surat, sertifikat, atau dokumen lainnya."},{"icon":"📑","title":"PDF Generator","desc":"Konversi dokumen yang dihasilkan menjadi format PDF untuk distribusi dan arsip digital."},{"icon":"🔍","title":"QR Code Verification","desc":"Setiap dokumen yang diterbitkan dilengkapi token unik dan QR Code. Siapa saja dapat memindai QR Code untuk memverifikasi keabsahan dokumen."},{"icon":"🔢","title":"Nomor Surat Otomatis","desc":"Sistem penomoran surat fleksibel dengan format kustom: nomor urut, kode seksi, bulan romawi, dan tahun. Reset otomatis per tahun atau bulan."},{"icon":"👥","title":"Multi-Role Access","desc":"Sistem peran pengguna yang fleksibel. Super Admin, Admin, dan pengguna biasa dengan hak akses yang berbeda-beda."},{"icon":"📋","title":"Audit Log","desc":"Pencatatan lengkap semua aktivitas sistem: siapa melakukan apa, kapan, dan dari mana. Untuk keamanan dan akuntabilitas."},{"icon":"🔒","title":"Keamanan","desc":"Proteksi CSRF, enkripsi password bcrypt, PDO prepared statements, validasi input, dan sanitasi output untuk keamanan maksimal."}]', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key`=`key`;

-- Page: Pricing
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('page_pricing_enabled', '1', NOW(), NOW()),
('page_pricing_title', 'Paket & Harga', NOW(), NOW()),
('page_pricing_subtitle', 'Pilih paket yang sesuai dengan kebutuhan Anda.', NOW(), NOW()),
('page_pricing_items', '[{"name":"Gratis","price":"Rp 0","period":"selamanya","desc":"Untuk individu dan penggunaan dasar","features":["Buat formulir tanpa batas","Kumpulkan respons","Template dokumen dasar","QR Code verifikasi","Iklan ditampilkan"],"cta":"Mulai Gratis","highlighted":false},{"name":"Pro","price":"Hubungi Kami","period":"","desc":"Untuk tim dan organisasi","features":["Semua fitur Gratis","Template dokumen lanjutan","Ekspor data Excel","Nomor surat otomatis","Tanpa iklan","Dukungan prioritas"],"cta":"Hubungi Kami","highlighted":true},{"name":"Enterprise","price":"Custom","period":"","desc":"Untuk instansi dan kebutuhan khusus","features":["Semua fitur Pro","Kustomisasi penuh","Instalasi mandiri (self-hosted)","Integrasi API","Dukungan teknis dedicateed","SLA khusus"],"cta":"Hubungi Kami","highlighted":false}]', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key`=`key`;

-- Page: Contact
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('page_contact_enabled', '1', NOW(), NOW()),
('page_contact_title', 'Hubungi Kami', NOW(), NOW()),
('page_contact_subtitle', 'Ada pertanyaan atau masukan? Kirim pesan kepada kami dan kami akan merespons secepat mungkin.', NOW(), NOW()),
('page_contact_success_message', 'Pesan Anda telah berhasil dikirim. Kami akan menghubungi Anda secepatnya.', NOW(), NOW())
ON DUPLICATE KEY UPDATE `key`=`key`;
