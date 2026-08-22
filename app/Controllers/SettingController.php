<?php

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\View;
use App\Core\Response;
use App\Models\Setting;
use App\Models\AuditLog;

/**
 * Settings Controller
 */
class SettingController
{
    private Setting $settingModel;

    public function __construct()
    {
        $this->settingModel = new Setting();
    }

    /**
     * Show main settings page (existing)
     */
    public function index(): void
    {
        $settings = $this->settingModel->getAll();

        View::page('settings.index', [
            'title'     => 'Pengaturan',
            'pageTitle' => 'Pengaturan Aplikasi',
            'settings'  => $settings,
        ]);
    }

    /**
     * Update main settings (existing)
     */
    public function update(): void
    {
        CSRF::check();

        $data = $_POST;
        unset($data['_csrf_token'], $data['_method']);

        $this->settingModel->bulkUpdate($data);
        AuditLog::log('update', 'settings', null, 'Mengubah pengaturan aplikasi');

        Response::redirectWith(url('settings'), 'success', 'Pengaturan berhasil disimpan.');
    }

    /**
     * Show site configuration page
     */
    public function site(): void
    {
        $settings = $this->settingModel->getGroup('site_');

        View::page('settings.site', [
            'title'     => 'Pengaturan Situs',
            'pageTitle' => 'Pengaturan Situs',
            'settings'  => $settings,
        ]);
    }

    /**
     * Update site settings
     */
    public function updateSite(): void
    {
        CSRF::check();

        $fields = [
            'site_name', 'site_tagline', 'site_description',
            'site_contact_email', 'site_contact_phone', 'site_contact_address',
            'site_footer_text', 'site_og_image', 'site_url',
        ];

        $data = [];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $data[$field] = trim($_POST[$field]);
            }
        }

        $this->settingModel->bulkUpdate($data);
        AuditLog::log('update', 'settings', null, 'Mengubah pengaturan situs');

        Response::redirectWith(url('settings/site'), 'success', 'Pengaturan situs berhasil disimpan.');
    }

    /**
     * Show page content management
     */
    public function pages(): void
    {
        // Load all page settings
        $aboutSettings    = $this->settingModel->getGroup('page_about_');
        $privacySettings  = $this->settingModel->getGroup('page_privacy_');
        $termsSettings    = $this->settingModel->getGroup('page_terms_');
        $featuresSettings = $this->settingModel->getGroup('page_features_');
        $pricingSettings  = $this->settingModel->getGroup('page_pricing_');
        $contactSettings  = $this->settingModel->getGroup('page_contact_');

        View::page('settings.pages', [
            'title'           => 'Kelola Halaman',
            'pageTitle'       => 'Kelola Halaman Publik',
            'aboutSettings'   => $aboutSettings,
            'privacySettings' => $privacySettings,
            'termsSettings'   => $termsSettings,
            'featuresSettings'=> $featuresSettings,
            'pricingSettings' => $pricingSettings,
            'contactSettings' => $contactSettings,
        ]);
    }

    /**
     * Update page content settings
     */
    public function updatePages(): void
    {
        CSRF::check();

        $data = $_POST;
        unset($data['_csrf_token'], $data['_method']);

        // Handle checkboxes (enabled flags) — set to 0 if not sent
        $enabledFlags = [
            'page_about_enabled', 'page_privacy_enabled', 'page_terms_enabled',
            'page_features_enabled', 'page_pricing_enabled', 'page_contact_enabled',
        ];
        foreach ($enabledFlags as $flag) {
            $data[$flag] = isset($data[$flag]) ? '1' : '0';
        }

        // Process Features Array into JSON if provided
        if (isset($_POST['features']) && is_array($_POST['features'])) {
            $features = [];
            foreach ($_POST['features'] as $f) {
                $icon = trim($f['icon'] ?? '📝');
                $title = trim($f['title'] ?? '');
                $desc = trim($f['desc'] ?? '');
                if ($title !== '') {
                    $features[] = [
                        'icon'  => $icon ?: '📝',
                        'title' => $title,
                        'desc'  => $desc,
                    ];
                }
            }
            $data['page_features_items'] = json_encode($features, JSON_UNESCAPED_UNICODE);
            unset($data['features']);
        }

        // Process Pricing Array into JSON if provided
        if (isset($_POST['pricing']) && is_array($_POST['pricing'])) {
            $plans = [];
            foreach ($_POST['pricing'] as $p) {
                $name = trim($p['name'] ?? '');
                if ($name !== '') {
                    $featuresList = [];
                    if (!empty($p['features'])) {
                        if (is_string($p['features'])) {
                            $lines = explode("\n", str_replace("\r", "", $p['features']));
                            foreach ($lines as $line) {
                                $line = trim($line);
                                if ($line !== '') {
                                    $featuresList[] = $line;
                                }
                            }
                        } elseif (is_array($p['features'])) {
                            $featuresList = array_values(array_filter(array_map('trim', $p['features'])));
                        }
                    }

                    $plans[] = [
                        'name'        => $name,
                        'price'       => trim($p['price'] ?? ''),
                        'period'      => trim($p['period'] ?? ''),
                        'desc'        => trim($p['desc'] ?? ''),
                        'features'    => $featuresList,
                        'cta'         => trim($p['cta'] ?? 'Pilih Paket'),
                        'highlighted' => !empty($p['highlighted']),
                    ];
                }
            }
            $data['page_pricing_items'] = json_encode($plans, JSON_UNESCAPED_UNICODE);
            unset($data['pricing']);
        }

        $this->settingModel->bulkUpdate($data);
        AuditLog::log('update', 'settings', null, 'Mengubah konten halaman publik');

        Response::redirectWith(url('settings/pages'), 'success', 'Konten halaman berhasil disimpan.');
    }

    /**
     * Show Payment Settings (QRIS & Bank Accounts)
     */
    public function payment(): void
    {
        $settings = $this->settingModel->getGroup('payment_');
        $bankAccounts = json_decode($settings['payment_bank_accounts'] ?? '[]', true) ?: [];

        View::page('settings.payment', [
            'title'        => 'Pengaturan Pembayaran',
            'pageTitle'    => 'Pengaturan Pembayaran & Rekening',
            'settings'     => $settings,
            'bankAccounts' => $bankAccounts,
        ]);
    }

    /**
     * Update Payment Settings
     */
    public function updatePayment(): void
    {
        CSRF::check();

        $data = [
            'payment_qris_enabled'          => isset($_POST['payment_qris_enabled']) ? '1' : '0',
            'payment_qris_merchant'         => trim($_POST['payment_qris_merchant'] ?? ''),
            'payment_qris_instructions'     => trim($_POST['payment_qris_instructions'] ?? ''),
            'payment_transfer_enabled'      => isset($_POST['payment_transfer_enabled']) ? '1' : '0',
            'payment_transfer_instructions' => trim($_POST['payment_transfer_instructions'] ?? ''),
        ];

        // Handle QRIS Image Upload
        if (!empty($_FILES['payment_qris_image_file']['name']) && $_FILES['payment_qris_image_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['payment_qris_image_file'];
            $uploadDir = BASE_PATH . '/public/uploads/qris';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'qris_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
                $data['payment_qris_image'] = 'uploads/qris/' . $filename;
            }
        } elseif (!empty($_POST['payment_qris_image_url'])) {
            $data['payment_qris_image'] = trim($_POST['payment_qris_image_url']);
        }

        // Handle Bank Accounts
        if (isset($_POST['bank_accounts']) && is_array($_POST['bank_accounts'])) {
            $accounts = [];
            foreach ($_POST['bank_accounts'] as $acc) {
                $bank = trim($acc['bank'] ?? '');
                $number = trim($acc['number'] ?? '');
                $holder = trim($acc['holder'] ?? '');
                if ($bank !== '' && $number !== '') {
                    $accounts[] = [
                        'bank'   => $bank,
                        'number' => $number,
                        'holder' => $holder,
                    ];
                }
            }
            $data['payment_bank_accounts'] = json_encode($accounts, JSON_UNESCAPED_UNICODE);
        }

        $this->settingModel->bulkUpdate($data);
        AuditLog::log('update', 'settings', null, 'Mengubah pengaturan metode pembayaran (QRIS & Bank)');

        Response::redirectWith(url('settings/payment'), 'success', 'Pengaturan metode pembayaran berhasil disimpan.');
    }
}
