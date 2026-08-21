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
            'plan'                  => trim($_POST['plan'] ?? 'Gratis'),
            'password'              => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
        ];

        $validator = new Validator([
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|unique:users,email',
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
            'password' => $data['password'],
            'role_id'  => $roleId,
            'plan'     => $data['plan'],
            'status'   => 'pending', // Pending approval by Administrator
        ]);

        AuditLog::log('register', 'auth', (int)$userId, "Pendaftaran user baru [Paket: {$data['plan']}]: {$data['name']}");

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
