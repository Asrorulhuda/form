<?php

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Services\MailService;
use App\Services\WhatsAppService;

/**
 * Gateway Controller
 * Manages WhatsApp Gateway & Gmail SMTP Gateway configurations and live test sendings.
 */
class GatewayController
{
    private Setting $settingModel;

    public function __construct()
    {
        $this->settingModel = new Setting();
    }

    /**
     * Show Gateway Settings Page
     */
    public function index(): void
    {
        $waSettings   = $this->settingModel->getGroup('wa_');
        $smtpSettings = $this->settingModel->getGroup('smtp_');

        View::page('settings.gateway', [
            'title'        => 'Gateway & Notifikasi',
            'pageTitle'    => 'Pengaturan WhatsApp & Gmail Gateway',
            'waSettings'   => $waSettings,
            'smtpSettings' => $smtpSettings,
        ]);
    }

    /**
     * Update Gateway Settings
     */
    public function update(): void
    {
        CSRF::check();

        $data = $_POST;
        unset($data['_csrf_token'], $data['_method']);

        // Checkbox toggles
        $toggles = [
            'wa_enabled', 'wa_notify_admin_on_register', 'wa_notify_admin_on_payment', 'wa_notify_user_on_approval',
            'wa_notify_on_form_response', 'wa_notify_respondent_on_submit', 'wa_notify_on_contact',
            'smtp_enabled', 'smtp_notify_admin_on_register', 'smtp_notify_admin_on_payment', 'smtp_notify_user_on_approval',
            'smtp_notify_on_form_response', 'smtp_notify_respondent_on_submit', 'smtp_notify_on_contact',
        ];

        foreach ($toggles as $t) {
            $data[$t] = isset($data[$t]) ? '1' : '0';
        }

        $this->settingModel->bulkUpdate($data);
        AuditLog::log('update', 'settings', null, 'Mengubah konfigurasi WhatsApp & Gmail Gateway');

        Response::redirectWith(url('settings/gateway'), 'success', 'Pengaturan Gateway berhasil disimpan.');
    }

    /**
     * Test WhatsApp Gateway sending live message
     */
    public function testWhatsApp(): void
    {
        CSRF::check();

        $targetNumber = trim($_POST['test_wa_number'] ?? '');
        if (empty($targetNumber)) {
            Response::redirectWith(url('settings/gateway'), 'error', 'Nomor WhatsApp tujuan uji coba harus diisi.');
            return;
        }

        $wa = WhatsAppService::getInstance();
        $siteName = $this->settingModel->get('site_name', 'ASR FORM');
        $testMsg = "🧪 *TEST NOTIFIKASI WHATSAPP GATEWAY*\n\n"
                 . "Halo! Ini adalah pesan uji coba dari sistem *{$siteName}*.\n"
                 . "Waktu kirim: " . date('d/m/Y H:i:s') . " WIB\n\n"
                 . "Jika Anda menerima pesan ini, koneksi WhatsApp Gateway Anda berfungsi dengan sangat baik! ✅";

        $res = $wa->sendMessage($targetNumber, $testMsg);

        if ($res['success']) {
            Response::redirectWith(url('settings/gateway'), 'success', 'Berhasil! Pesan WhatsApp uji coba telah terkirim ke ' . $targetNumber);
        } else {
            Response::redirectWith(url('settings/gateway'), 'error', 'Gagal mengirim WhatsApp: ' . $res['message']);
        }
    }

    /**
     * Test Gmail SMTP sending live email
     */
    public function testMail(): void
    {
        CSRF::check();

        $targetEmail = trim($_POST['test_email'] ?? '');
        if (empty($targetEmail) || !filter_var($targetEmail, FILTER_VALIDATE_EMAIL)) {
            Response::redirectWith(url('settings/gateway'), 'error', 'Alamat email tujuan uji coba tidak valid.');
            return;
        }

        $mail = MailService::getInstance();
        $siteName = $this->settingModel->get('site_name', 'ASR FORM');
        $subject = "🧪 [Uji Coba] Test Email SMTP dari {$siteName}";
        $htmlBody = "<h2>Uji Coba Pengiriman Email SMTP</h2>"
                  . "<p>Halo,</p>"
                  . "<p>Ini adalah pesan email uji coba dari sistem <strong>{$siteName}</strong>.</p>"
                  . "<p>Waktu pengiriman: <strong>" . date('d/m/Y H:i:s') . " WIB</strong></p>"
                  . "<p style='color:green;font-weight:bold;'>Jika Anda membaca email ini, integrasi Gmail SMTP Anda telah berhasil dan siap digunakan! ✅</p>";

        $res = $mail->send($targetEmail, $subject, $htmlBody);

        if ($res['success']) {
            Response::redirectWith(url('settings/gateway'), 'success', 'Berhasil! Email uji coba telah terkirim ke ' . $targetEmail);
        } else {
            Response::redirectWith(url('settings/gateway'), 'error', 'Gagal mengirim email: ' . $res['message']);
        }
    }
}
