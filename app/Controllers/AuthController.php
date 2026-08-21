<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;
use App\Core\Response;
use App\Core\Validator;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Authentication Controller
 */
class AuthController
{
    /**
     * Show login page
     */
    public function showLogin(): void
    {
        // Redirect if already logged in
        if (Auth::check()) {
            Response::redirect(url('dashboard'));
            return;
        }

        View::guest('auth.login', [
            'title' => 'Login — ASR FORM',
        ]);
    }

    /**
     * Process login
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
     * Show registration page
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
     * Process registration
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
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|min:9|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!$validator->validate($data)) {
            Session::flash('errors', $validator->errors());
            Session::setOld($data);
            Response::redirect(url('register' . (!empty($data['plan']) ? '?plan=' . urlencode($data['plan']) : '')));
            return;
        }

        // Default role: User (role_id 6)
        $db = Database::getInstance();
        $userRole = $db->fetch("SELECT id FROM roles WHERE name = 'User'");
        $roleId = $userRole ? (int)$userRole->id : 6;

        $userModel = new User();
        $userId = $userModel->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => $data['password'],
            'role_id'  => $roleId,
            'plan'     => $data['plan'],
            'status'   => 'pending', // Pending approval by Administrator
        ]);

        AuditLog::log('register', 'auth', (int)$userId, "Pendaftaran user baru [Paket: {$data['plan']}]: {$data['name']} ({$data['phone']})");

        $settingModel = new \App\Models\Setting();
        $siteName = $settingModel->get('site_name', 'ASR FORM');

        // ─── 1. WhatsApp Alert to Admin ───
        $wa = \App\Services\WhatsAppService::getInstance();
        if ($wa->isEnabled() && (int)$settingModel->get('wa_notify_admin_on_register', '1') === 1) {
            $waMsg = "👤 *PENDAFTARAN PENGGUNA BARU — {$siteName}*\n\n"
                   . "📋 *Nama:* {$data['name']}\n"
                   . "📧 *Email:* {$data['email']}\n"
                   . "📱 *Nomor WhatsApp:* {$data['phone']}\n"
                   . "📦 *Paket Dipilih:* {$data['plan']}\n"
                   . "📅 *Waktu Daftar:* " . date('d/m/Y H:i') . " WIB\n\n"
                   . "👉 Silakan tinjau dan aktifkan akun pemohon di Dashboard Admin.";
            $wa->notifyAdmin($waMsg);
        }

        // ─── 2. WhatsApp Welcome to User ───
        if ($wa->isEnabled() && !empty($data['phone'])) {
            $userWelcome = "Halo *{$data['name']}*, terima kasih telah mendaftar di *{$siteName}*!\n\n"
                         . "Pendaftaran Anda untuk *Paket {$data['plan']}* telah kami terima dan sedang dalam proses verifikasi tim admin kami.\n\n"
                         . "🌐 Kunjungi website: " . url();
            $wa->notifyUser($data['phone'], $userWelcome);
        }

        // ─── 3. Email Alert to Admin ───
        $mail = \App\Services\MailService::getInstance();
        if ($mail->isEnabled() && (int)$settingModel->get('smtp_notify_admin_on_register', '1') === 1) {
            $emailSubj = "[Pendaftar Baru] {$data['name']} - Paket {$data['plan']}";
            $emailBody = "<h2>Pendaftaran Pengguna Baru Masuk</h2>"
                       . "<p>Ada pendaftaran akun baru pada platform {$siteName}:</p>"
                       . "<ul>"
                       . "<li><strong>Nama:</strong> {$data['name']}</li>"
                       . "<li><strong>Email:</strong> {$data['email']}</li>"
                       . "<li><strong>No WhatsApp:</strong> {$data['phone']}</li>"
                       . "<li><strong>Paket:</strong> {$data['plan']}</li>"
                       . "<li><strong>Waktu:</strong> " . date('d/m/Y H:i') . " WIB</li>"
                       . "</ul>"
                       . "<p><a href='" . url('users/applicants') . "'>Klik di sini untuk melihat daftar pemohon akun</a></p>";
            $mail->notifyAdmin($emailSubj, $emailBody);
        }

        // If paid plan, redirect to payment checkout
        if (strcasecmp($data['plan'], 'Gratis') !== 0) {
            Session::flash('toast_type', 'success');
            Session::flash('toast_message', 'Akun berhasil dibuat! Silakan selesaikan pembayaran untuk paket ' . $data['plan'] . '.');
            Response::redirect(url("payment/{$userId}"));
            return;
        }

        Session::flash('toast_type', 'success');
        Session::flash('toast_message', 'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan dari Administrator.');
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
