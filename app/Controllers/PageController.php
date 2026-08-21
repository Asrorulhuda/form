<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Models\Setting;

/**
 * Public Pages Controller
 * All content is loaded from settings — nothing hardcoded.
 */
class PageController
{
    private Setting $settings;

    public function __construct()
    {
        $this->settings = new Setting();
    }

    /**
     * Get common data for all public pages
     */
    private function getPublicData(): array
    {
        return [
            'isLoggedIn'   => Auth::check(),
            'user'         => Auth::user(),
            'siteName'     => $this->settings->get('site_name', 'ASR FORM'),
            'siteTagline'  => $this->settings->get('site_tagline', ''),
            'siteDesc'     => $this->settings->get('site_description', ''),
            'contactEmail' => $this->settings->get('site_contact_email', ''),
            'contactPhone' => $this->settings->get('site_contact_phone', ''),
            'contactAddr'  => $this->settings->get('site_contact_address', ''),
            'footerText'   => $this->settings->get('site_footer_text', ''),
            // Page enabled flags for nav/footer
            'featuresEnabled' => (int) $this->settings->get('page_features_enabled', '1') === 1,
            'aboutEnabled'    => (int) $this->settings->get('page_about_enabled', '1') === 1,
            'contactEnabled'  => (int) $this->settings->get('page_contact_enabled', '1') === 1,
            'pricingEnabled'  => (int) $this->settings->get('page_pricing_enabled', '1') === 1,
            'privacyEnabled'  => (int) $this->settings->get('page_privacy_enabled', '1') === 1,
            'termsEnabled'    => (int) $this->settings->get('page_terms_enabled', '1') === 1,
        ];
    }

    /**
     * Features page
     */
    public function features(): void
    {
        if ((int) $this->settings->get('page_features_enabled', '1') !== 1) {
            $this->show404();
            return;
        }

        $items = json_decode($this->settings->get('page_features_items', '[]'), true) ?: [];

        if (empty($items)) {
            $items = [
                [
                    'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
                    'title' => '18+ Dynamic Field Types',
                    'desc' => 'Mendukung input teks, email, number, tanggal/waktu, dropdown, file upload, rating, hingga canvas digital signature.'
                ],
                [
                    'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
                    'title' => 'Smart Template Generator',
                    'desc' => 'Buat template surat dengan tag variabel dinamis seperti {{nama}}, {{tanggal}}, dan {{nomor_surat}} secara otomatis.'
                ],
                [
                    'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h3v3H7z"/><path d="M14 7h3v3h-3z"/><path d="M7 14h3v3H7z"/><path d="M14 14h3v3h-3z"/></svg>',
                    'title' => 'QR Code Verification',
                    'desc' => 'Setiap dokumen resmi yang terbit dilengkapi token unik dan QR Code untuk verifikasi keabsahan secara publik.'
                ],
                [
                    'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                    'title' => 'Nomor Surat Otomatis',
                    'desc' => 'Penomoran surat fleksibel dengan format romawi, kode seksi, dan reset sequence tahunan/bulanan otomatis.'
                ],
                [
                    'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>',
                    'title' => 'Approval Workflow',
                    'desc' => 'Mekanisme persetujuan dokumen berjenjang dari Operator, Editor, Approver hingga diterbitkan dan diarsipkan.'
                ],
                [
                    'icon' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                    'title' => 'Aman & Terverifikasi',
                    'desc' => 'Dilengkapi proteksi CSRF, enkripsi password bcrypt, PDO prepared statements, dan pencatatan audit log lengkap.'
                ],
            ];
        }

        View::render('pages.features', array_merge($this->getPublicData(), [
            'title'        => $this->settings->get('page_features_title', 'Fitur Lengkap') . ' — ' . $this->settings->get('site_name', 'ASR FORM'),
            'pageTitle'    => $this->settings->get('page_features_title', 'Fitur Lengkap'),
            'pageSubtitle' => $this->settings->get('page_features_subtitle', 'Solusi terintegrasi untuk pembuatan formulir online dan otomasi dokumen resmi.'),
            'featureItems' => $items,
        ]), 'public');
    }

    /**
     * About page
     */
    public function about(): void
    {
        if ((int) $this->settings->get('page_about_enabled', '1') !== 1) {
            $this->show404();
            return;
        }

        $content = $this->settings->get('page_about_content', '');
        if (empty($content)) {
            $content = "ASR FORM adalah platform digital yang dirancang khusus untuk mempermudah instansi, lembaga pendidikan, perusahaan, dan organisasi dalam mengumpulkan data melalui formulir digital dinamis serta mengonversinya secara otomatis menjadi dokumen resmi.\n\nDengan arsitektur modern berbasis PHP Native, sistem kami menghadirkan kecepatan loading maksimal, keamanan tinggi dengan enkripsi tingkat lanjut, dan kemudahan kustomisasi tanpa batas.";
        }

        $vision = $this->settings->get('page_about_vision', '');
        if (empty($vision)) {
            $vision = "Menjadi platform nomor satu untuk digitalisasi dokumen dan otomasi administrasi yang andal, cepat, dan aman bagi seluruh instansi di Indonesia.";
        }

        View::render('pages.about', array_merge($this->getPublicData(), [
            'title'       => $this->settings->get('page_about_title', 'Tentang Kami') . ' — ' . $this->settings->get('site_name', 'ASR FORM'),
            'pageTitle'   => $this->settings->get('page_about_title', 'Tentang Kami'),
            'pageContent' => $content,
            'pageVision'  => $vision,
        ]), 'public');
    }

    /**
     * Contact page
     */
    public function contact(): void
    {
        if ((int) $this->settings->get('page_contact_enabled', '1') !== 1) {
            $this->show404();
            return;
        }

        View::render('pages.contact', array_merge($this->getPublicData(), [
            'title'        => $this->settings->get('page_contact_title', 'Kontak') . ' — ' . $this->settings->get('site_name', 'ASR FORM'),
            'pageTitle'    => $this->settings->get('page_contact_title', 'Hubungi Kami'),
            'pageSubtitle' => $this->settings->get('page_contact_subtitle', ''),
            'successMsg'   => $this->settings->get('page_contact_success_message', ''),
        ]), 'public');
    }

    /**
     * Process contact form submission
     */
    public function sendContact(): void
    {
        CSRF::check();

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Server-side validation
        $errors = [];
        if (empty($name) || mb_strlen($name) < 2) {
            $errors[] = 'Nama harus diisi (minimal 2 karakter).';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Alamat email tidak valid.';
        }
        if (empty($subject) || mb_strlen($subject) < 3) {
            $errors[] = 'Subjek harus diisi (minimal 3 karakter).';
        }
        if (empty($message) || mb_strlen($message) < 10) {
            $errors[] = 'Pesan harus diisi (minimal 10 karakter).';
        }

        if (!empty($errors)) {
            Session::flash('contact_errors', $errors);
            Session::flash('contact_old', [
                'name' => $name, 'email' => $email,
                'subject' => $subject, 'message' => $message,
            ]);
            Response::redirect(url('contact'));
            return;
        }

        // Save to database
        $db = Database::getInstance();
        $db->insert('contact_messages', [
            'name'       => $name,
            'email'      => $email,
            'subject'    => $subject,
            'message'    => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $siteName = $this->settings->get('site_name', 'ASR FORM');

        // ─── WhatsApp Alert to Admin ───
        $wa = \App\Services\WhatsAppService::getInstance();
        if ($wa->isEnabled() && (int)$this->settings->get('wa_notify_on_contact', '1') === 1) {
            $waMsg = "📬 *PESAN KONTAK BARU — {$siteName}*\n\n"
                   . "👤 *Nama:* {$name}\n"
                   . "📧 *Email:* {$email}\n"
                   . "📌 *Subjek:* {$subject}\n"
                   . "💬 *Pesan:* {$message}\n"
                   . "📅 *Waktu:* " . date('d/m/Y H:i') . " WIB";
            $wa->notifyAdmin($waMsg);
        }

        // ─── Email Alert to Admin ───
        $mail = \App\Services\MailService::getInstance();
        if ($mail->isEnabled() && (int)$this->settings->get('smtp_notify_on_contact', '1') === 1) {
            $emailSubj = "[Kontak Masuk] {$subject} - {$name}";
            $emailBody = "<h2>Pesan Kontak Baru Masuk</h2>"
                       . "<p>Ada pesan baru dari halaman kontak website {$siteName}:</p>"
                       . "<ul>"
                       . "<li><strong>Nama:</strong> {$name}</li>"
                       . "<li><strong>Email:</strong> {$email}</li>"
                       . "<li><strong>Subjek:</strong> {$subject}</li>"
                       . "<li><strong>Pesan:</strong> " . nl2br(htmlspecialchars($message)) . "</li>"
                       . "<li><strong>Waktu:</strong> " . date('d/m/Y H:i') . " WIB</li>"
                       . "</ul>";
            $mail->notifyAdmin($emailSubj, $emailBody);
        }

        Session::flash('contact_success', true);
        Response::redirect(url('contact'));
    }

    /**
     * Pricing page
     */
    public function pricing(): void
    {
        if ((int) $this->settings->get('page_pricing_enabled', '1') !== 1) {
            $this->show404();
            return;
        }

        $items = json_decode($this->settings->get('page_pricing_items', '[]'), true) ?: [];

        if (empty($items)) {
            $items = [
                [
                    'name' => 'Starter',
                    'price' => 'Rp 0',
                    'period' => 'selamanya',
                    'desc' => 'Cocok untuk pengguna individual atau uji coba awal pembuatan formulir.',
                    'features' => [
                        'Hingga 5 Formulir Aktif',
                        '100 Respons per Formulir',
                        'Semua Tipe Field Dinamis',
                        'Export Data CSV & Excel',
                    ],
                    'cta' => 'Mulai Gratis',
                    'highlighted' => false,
                ],
                [
                    'name' => 'Profesional',
                    'price' => 'Rp 99.000',
                    'period' => 'bulan',
                    'desc' => 'Solusi lengkap untuk instansi, UMKM, dan organisasi modern.',
                    'features' => [
                        'Unlimited Formulir & Respons',
                        'Smart Document Generator Otomatis',
                        'Nomor Surat Dinamis & QR Verify',
                        'Approval Workflow Berjenjang',
                        'Digital Signature & Export PDF Resmi',
                    ],
                    'cta' => 'Pilih Paket Pro',
                    'highlighted' => true,
                ],
                [
                    'name' => 'Enterprise',
                    'price' => 'Rp 299.000',
                    'period' => 'bulan',
                    'desc' => 'Untuk organisasi skala besar dengan volume tinggi dan integrasi khusus.',
                    'features' => [
                        'Semua Fitur Profesional',
                        'Multi User & 6 Role Fleksibel',
                        'Integrasi Webhook & API Kustom',
                        'Audit Trail & Keamanan Maksimal',
                        'Prioritas Support WhatsApp 24/7',
                    ],
                    'cta' => 'Hubungi Sales',
                    'highlighted' => false,
                ],
            ];
        }

        View::render('pages.pricing', array_merge($this->getPublicData(), [
            'title'        => $this->settings->get('page_pricing_title', 'Paket & Harga') . ' — ' . $this->settings->get('site_name', 'ASR FORM'),
            'pageTitle'    => $this->settings->get('page_pricing_title', 'Paket & Harga'),
            'pageSubtitle' => $this->settings->get('page_pricing_subtitle', 'Pilih paket yang paling sesuai dengan kebutuhan formulir dan otomasi dokumen Anda.'),
            'pricingItems' => $items,
        ]), 'public');
    }

    /**
     * Privacy Policy page
     */
    public function privacyPolicy(): void
    {
        if ((int) $this->settings->get('page_privacy_enabled', '1') !== 1) {
            $this->show404();
            return;
        }

        View::render('pages.privacy', array_merge($this->getPublicData(), [
            'title'       => $this->settings->get('page_privacy_title', 'Privacy Policy') . ' — ' . $this->settings->get('site_name', 'ASR FORM'),
            'pageTitle'   => $this->settings->get('page_privacy_title', 'Kebijakan Privasi'),
            'pageContent' => $this->settings->get('page_privacy_content', ''),
            'lastUpdated' => $this->settings->get('page_privacy_last_updated', ''),
        ]), 'public');
    }

    /**
     * Terms of Service page
     */
    public function terms(): void
    {
        if ((int) $this->settings->get('page_terms_enabled', '1') !== 1) {
            $this->show404();
            return;
        }

        View::render('pages.terms', array_merge($this->getPublicData(), [
            'title'       => $this->settings->get('page_terms_title', 'Terms of Service') . ' — ' . $this->settings->get('site_name', 'ASR FORM'),
            'pageTitle'   => $this->settings->get('page_terms_title', 'Syarat dan Ketentuan'),
            'pageContent' => $this->settings->get('page_terms_content', ''),
            'lastUpdated' => $this->settings->get('page_terms_last_updated', ''),
        ]), 'public');
    }

    /**
     * Dynamic XML Sitemap
     */
    public function sitemap(): void
    {
        $baseUrl = rtrim($this->settings->get('site_url', env('APP_URL', 'http://localhost')), '/');

        header('Content-Type: application/xml; charset=utf-8');
        
        $urls = [
            ['loc' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
        ];

        $pages = [
            ['path' => '/features', 'enabled' => 'page_features_enabled'],
            ['path' => '/pricing', 'enabled' => 'page_pricing_enabled'],
            ['path' => '/about', 'enabled' => 'page_about_enabled'],
            ['path' => '/contact', 'enabled' => 'page_contact_enabled'],
            ['path' => '/privacy-policy', 'enabled' => 'page_privacy_enabled'],
            ['path' => '/terms', 'enabled' => 'page_terms_enabled'],
        ];

        foreach ($pages as $page) {
            if ((int) $this->settings->get($page['enabled'], '1') === 1) {
                $urls[] = [
                    'loc' => $baseUrl . $page['path'],
                    'priority' => '0.8',
                    'changefreq' => 'monthly',
                ];
            }
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $url) {
            echo '  <url>' . "\n";
            echo '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
            echo '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            echo '    <priority>' . $url['priority'] . '</priority>' . "\n";
            echo '  </url>' . "\n";
        }
        echo '</urlset>';
        exit;
    }

    /**
     * Show 404 page
     */
    private function show404(): void
    {
        http_response_code(404);
        if (file_exists(BASE_PATH . '/views/errors/404.php')) {
            include BASE_PATH . '/views/errors/404.php';
        } else {
            echo '<h1>404 - Halaman tidak ditemukan</h1>';
        }
    }
}
