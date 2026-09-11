<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;
use App\Core\Response;
use App\Core\Validator;
use App\Models\Admin;
use App\Models\AuditLog;

/**
 * Authentication Controller — Multi-SaaS
 * Handles login for both admins and users, and registration for new admins.
 */
class AuthController
{
    /**
     * Show login page
     */
    public function showLogin(): void
    {
        if (Auth::check()) {
            Response::redirect(url('dashboard'));
            return;
        }

        View::guest('auth.login', [
            'title' => 'Login — ASR FORM',
        ]);
    }

    /**
     * Process login (checks admins table first, then users table)
     */
    public function login(): void
    {
        CSRF::check();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email dan password wajib diisi.');
            Session::setOld(['email' => $email]);
            Response::redirect(url('login'));
            return;
        }

        if (Auth::attempt($email, $password)) {
            AuditLog::log('login', 'auth', null, 'User logged in');
            Response::redirect(url('dashboard'));
        } else {
            $errorMessage = Auth::getLastError() ?? 'Email atau password salah.';
            Session::flash('error', $errorMessage);
            Session::setOld(['email' => $email]);
            Response::redirect(url('login'));
        }
    }

    /**
     * Show registration page (for new Admin accounts)
     */
    public function showRegister(): void
    {
        if (Auth::check()) {
            Response::redirect(url('dashboard'));
            return;
        }

        $settingModel = new \App\Models\Setting();
        $plans = json_decode($settingModel->get('page_pricing_items', '[]'), true) ?: [];
        if (empty($plans)) {
            $plans = [
                ['name' => 'Gratis', 'price' => 'Rp 0', 'desc' => 'Untuk individu dan penggunaan dasar', 'highlighted' => false],
                ['name' => 'Pro', 'price' => 'Hubungi Kami', 'desc' => 'Untuk tim dan organisasi', 'highlighted' => true],
                ['name' => 'Enterprise', 'price' => 'Custom', 'desc' => 'Untuk instansi dan kebutuhan khusus', 'highlighted' => false],
            ];
        }

        $selectedPlan = trim($_GET['plan'] ?? Session::old('plan') ?? $plans[0]['name'] ?? 'Gratis');

        View::guest('auth.register', [
            'title'        => 'Daftar Akun Baru — ASR FORM',
            'plans'        => $plans,
            'selectedPlan' => $selectedPlan,
        ]);
    }

    /**
     * Process registration (creates a new Admin account, pending approval)
     */
    public function register(): void
    {
        CSRF::check();

        $data = [
            'name'                  => trim($_POST['name'] ?? ''),
            'email'                 => trim($_POST['email'] ?? ''),
            'phone'                 => trim($_POST['phone'] ?? ''),
            'plan'                  => trim($_POST['plan'] ?? 'Gratis'),
            'password'              => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
        ];

        $validator = new Validator([
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|unique:admins,email',
            'phone'    => 'required|min:9|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!$validator->validate($data)) {
            Session::flash('errors', $validator->errors());
            Session::setOld($data);
            Response::redirect(url('register' . (!empty($data['plan']) ? '?plan=' . urlencode($data['plan']) : '')));
            return;
        }

        // Create new Admin (role_id = 2, status = pending)
        $adminModel = new Admin();
        $adminId = $adminModel->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => $data['password'],
            'role_id'  => 2,
            'plan'     => $data['plan'],
            'status'   => 'pending',
        ]);

        AuditLog::log('register', 'auth', (int)$adminId, "Pendaftaran admin baru [Paket: {$data['plan']}]: {$data['name']} ({$data['phone']})");

        $settingModel = new \App\Models\Setting();
        $siteName = $settingModel->get('site_name', 'ASR FORM');

        // WhatsApp notification to Super Admin
        $wa = \App\Services\WhatsAppService::getInstance();
        if ($wa->isEnabled()) {
            $adminWaMsg = null;
            if ((int)$settingModel->get('wa_notify_admin_on_register', '1') === 1) {
                $adminWaMsg = "👤 *PENDAFTARAN ADMIN BARU — {$siteName}*\n\n"
                            . "📋 *Nama:* {$data['name']}\n"
                            . "📧 *Email:* {$data['email']}\n"
                            . "📱 *Nomor WhatsApp:* {$data['phone']}\n"
                            . "📦 *Paket Dipilih:* {$data['plan']}\n"
                            . "📅 *Waktu Daftar:* " . date('d/m/Y H:i') . " WIB\n\n"
                            . "👉 Silakan tinjau dan aktifkan akun di Dashboard Super Admin:\n" . url('admins');
            }

            $userWelcome = null;
            if (!empty($data['phone'])) {
                $userWelcome = "Halo *{$data['name']}*, terima kasih telah mendaftar di *{$siteName}*!\n\n"
                             . "Pendaftaran Anda untuk *Paket {$data['plan']}* telah kami terima dan sedang dalam proses verifikasi.\n\n"
                             . "🌐 Kunjungi website: " . url();
            }

            if ($adminWaMsg || $userWelcome) {
                $wa->notifyBoth($data['phone'], $userWelcome, $adminWaMsg);
            }
        }

        // Email notification
        $mail = \App\Services\MailService::getInstance();
        if ($mail->isEnabled() && (int)$settingModel->get('smtp_notify_admin_on_register', '1') === 1) {
            $emailSubj = "[Pendaftar Baru] {$data['name']} - Paket {$data['plan']}";
            $emailBody = "<h2>Pendaftaran Admin Baru Masuk</h2>"
                       . "<p>Ada pendaftaran admin baru pada platform {$siteName}:</p>"
                       . "<ul>"
                       . "<li><strong>Nama:</strong> {$data['name']}</li>"
                       . "<li><strong>Email:</strong> {$data['email']}</li>"
                       . "<li><strong>No WhatsApp:</strong> {$data['phone']}</li>"
                       . "<li><strong>Paket:</strong> {$data['plan']}</li>"
                       . "<li><strong>Waktu:</strong> " . date('d/m/Y H:i') . " WIB</li>"
                       . "</ul>"
                       . "<p><a href='" . url('admins') . "'>Klik di sini untuk melihat daftar pendaftar</a></p>";
            $mail->notifyAdmin($emailSubj, $emailBody);
        }

        // If paid plan, redirect to payment checkout
        if (strcasecmp($data['plan'], 'Gratis') !== 0) {
            Session::flash('toast_type', 'success');
            Session::flash('toast_message', 'Akun berhasil dibuat! Silakan selesaikan pembayaran untuk paket ' . $data['plan'] . '.');
            Response::redirect(url("payment/{$adminId}"));
            return;
        }

        Session::flash('toast_type', 'success');
        Session::flash('toast_message', 'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan dari Super Admin.');
        Response::redirect(url('login'));
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        AuditLog::log('logout', 'auth', null, 'User logged out');
        Auth::logout();
        Response::redirect(url('login'));
    }
}
