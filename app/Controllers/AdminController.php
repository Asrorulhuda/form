<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;
use App\Core\View;
use App\Core\Response;
use App\Core\Validator;
use App\Models\Admin;
use App\Models\AuditLog;

/**
 * Admin Management Controller — Super Admin Only
 * CRUD operations for Admin accounts (tenant owners).
 */
class AdminController
{
    private Admin $adminModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
    }

    /**
     * List all admins (including pending applicants)
     */
    public function index(): void
    {
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $tab     = $_GET['tab'] ?? 'active';
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
        ];

        if ($tab === 'pending') {
            $result = $this->adminModel->getPendingApplicants($filters, $page);
        } else {
            $filters['status'] = $filters['status'] ?: ''; // Don't force status for active tab
            $result = $this->adminModel->getAll($filters, $page);
        }

        $pendingCount = $this->adminModel->countPending();

        View::page('admins.index', [
            'title'        => 'Kelola Admin',
            'pageTitle'    => 'Kelola Admin',
            'admins'       => $result['data'],
            'total'        => $result['total'],
            'page'         => $result['page'],
            'lastPage'     => $result['lastPage'],
            'filters'      => $filters,
            'tab'          => $tab,
            'pendingCount' => $pendingCount,
        ]);
    }

    /**
     * Show create admin form
     */
    public function create(): void
    {
        View::page('admins.create', [
            'title'     => 'Tambah Admin',
            'pageTitle' => 'Tambah Admin Baru',
        ]);
    }

    /**
     * Store new admin
     */
    public function store(): void
    {
        CSRF::check();

        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'phone'    => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'plan'     => trim($_POST['plan'] ?? 'Gratis'),
            'status'   => $_POST['status'] ?? 'active',
        ];

        $validator = new Validator([
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
        ]);

        if (!$validator->validate($data)) {
            Session::flash('errors', $validator->errors());
            Session::setOld($data);
            Response::redirect(url('admins/create'));
            return;
        }

        $data['role_id'] = 2; // Admin role
        $this->adminModel->create($data);
        AuditLog::log('create', 'admins', null, "Super Admin membuat admin baru: {$data['name']}");

        Response::redirectWith(url('admins'), 'success', 'Admin berhasil ditambahkan.');
    }

    /**
     * Show edit admin form
     */
    public function edit(string $id): void
    {
        $admin = $this->adminModel->find((int) $id);
        if (!$admin) {
            Response::redirectWith(url('admins'), 'error', 'Admin tidak ditemukan.');
            return;
        }

        View::page('admins.edit', [
            'title'     => 'Edit Admin',
            'pageTitle' => 'Edit Admin',
            'admin'     => $admin,
        ]);
    }

    /**
     * Update admin
     */
    public function update(string $id): void
    {
        CSRF::check();

        $adminId = (int) $id;
        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            Response::redirectWith(url('admins'), 'error', 'Admin tidak ditemukan.');
            return;
        }

        $data = [
            'name'       => trim($_POST['name'] ?? ''),
            'email'      => trim($_POST['email'] ?? ''),
            'phone'      => trim($_POST['phone'] ?? ''),
            'password'   => $_POST['password'] ?? '',
            'plan'       => trim($_POST['plan'] ?? ($admin->plan ?? 'Gratis')),
            'status'     => $_POST['status'] ?? 'active',
            'wa_sender'  => trim($_POST['wa_sender'] ?? ''),
            'wa_api_key' => trim($_POST['wa_api_key'] ?? ''),
        ];

        $rules = [
            'name'  => 'required|min:2|max:100',
            'email' => "required|email|unique:admins,email,{$adminId}",
        ];

        if (!empty($data['password'])) {
            $rules['password'] = 'min:6';
        }

        $validator = new Validator($rules);
        if (!$validator->validate($data)) {
            Session::flash('errors', $validator->errors());
            Session::setOld($data);
            Response::redirect(url("admins/{$adminId}/edit"));
            return;
        }

        $this->adminModel->update($adminId, $data);
        AuditLog::log('update', 'admins', $adminId, "Super Admin mengubah admin: {$data['name']}");

        Response::redirectWith(url('admins'), 'success', 'Admin berhasil diperbarui.');
    }

    /**
     * Approve admin applicant
     */
    public function approve(string $id): void
    {
        CSRF::check();

        $adminId = (int) $id;
        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            Response::redirectWith(url('admins?tab=pending'), 'error', 'Admin tidak ditemukan.');
            return;
        }

        $this->adminModel->approve($adminId);
        AuditLog::log('approve', 'admins', $adminId, "Super Admin menyetujui pendaftaran admin: {$admin->name}");

        // Notify admin via WA
        $settingModel = new \App\Models\Setting();
        $wa = \App\Services\WhatsAppService::getInstance();
        if ($wa->isEnabled() && !empty($admin->phone)) {
            $siteName = $settingModel->get('site_name', 'ASR FORM');
            $msg = "🎉 *Selamat, {$admin->name}!*\n\n"
                 . "Akun admin Anda di *{$siteName}* telah disetujui dan aktif.\n\n"
                 . "Silakan login di:\n" . url('login');
            $wa->sendMessage($admin->phone, $msg);
        }

        Response::redirectWith(url('admins?tab=pending'), 'success', "Admin {$admin->name} berhasil disetujui.");
    }

    /**
     * Reject admin applicant
     */
    public function reject(string $id): void
    {
        CSRF::check();

        $adminId = (int) $id;
        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            Response::redirectWith(url('admins?tab=pending'), 'error', 'Admin tidak ditemukan.');
            return;
        }

        $this->adminModel->reject($adminId);
        AuditLog::log('reject', 'admins', $adminId, "Super Admin menolak pendaftaran admin: {$admin->name}");

        Response::redirectWith(url('admins?tab=pending'), 'success', "Pendaftaran admin {$admin->name} ditolak.");
    }

    /**
     * Delete admin
     */
    public function destroy(string $id): void
    {
        CSRF::check();

        $adminId = (int) $id;
        $admin = $this->adminModel->find($adminId);

        if (!$admin) {
            Response::redirectWith(url('admins'), 'error', 'Admin tidak ditemukan.');
            return;
        }

        // Prevent deleting yourself
        if ($adminId === Auth::id() && Auth::isSuperAdmin()) {
            Response::redirectWith(url('admins'), 'error', 'Tidak dapat menghapus akun sendiri.');
            return;
        }

        // Prevent deleting Super Admin
        if ((int)$admin->role_id === 1) {
            Response::redirectWith(url('admins'), 'error', 'Tidak dapat menghapus Super Admin.');
            return;
        }

        $this->adminModel->delete($adminId);
        AuditLog::log('delete', 'admins', $adminId, "Super Admin menghapus admin: {$admin->name}");

        Response::redirectWith(url('admins'), 'success', 'Admin berhasil dihapus.');
    }

    /**
     * Show WhatsApp Gateway Settings for logged-in Admin
     */
    public function waSettings(): void
    {
        $adminId = Auth::adminId() ?? Auth::id();
        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            Response::redirectWith(url('dashboard'), 'error', 'Data admin tidak ditemukan.');
            return;
        }

        $settingModel = new \App\Models\Setting();
        $globalGatewayUrl = $settingModel->get('wa_gateway_url', 'https://gateway.asr-desain.my.id');
        $isGlobalEnabled = (int) $settingModel->get('wa_enabled', '0') === 1;

        View::page('admins.wa_settings', [
            'title'            => 'Pengaturan WhatsApp Gateway',
            'pageTitle'        => 'Pengaturan WhatsApp Tenant',
            'admin'            => $admin,
            'globalGatewayUrl' => $globalGatewayUrl,
            'isGlobalEnabled'  => $isGlobalEnabled,
        ]);
    }

    /**
     * Update WhatsApp Gateway Settings for logged-in Admin
     */
    public function updateWaSettings(): void
    {
        CSRF::check();

        $adminId = Auth::adminId() ?? Auth::id();
        $admin = $this->adminModel->find($adminId);
        if (!$admin) {
            Response::redirectWith(url('dashboard'), 'error', 'Data admin tidak ditemukan.');
            return;
        }

        $waSender = trim($_POST['wa_sender'] ?? '');
        $waApiKey = trim($_POST['wa_api_key'] ?? '');

        $this->adminModel->update($adminId, [
            'wa_sender'  => $waSender,
            'wa_api_key' => $waApiKey,
        ]);

        AuditLog::log('update', 'admins', $adminId, "Admin {$admin->name} memperbarui konfigurasi WhatsApp pengirim");

        Response::redirectWith(url('settings/wa'), 'success', 'Pengaturan WhatsApp berhasil disimpan.');
    }

    /**
     * Test WhatsApp sending using Admin's credentials
     */
    public function testWa(): void
    {
        CSRF::check();

        $adminId = Auth::adminId() ?? Auth::id();
        $targetNumber = trim($_POST['test_wa_number'] ?? '');
        if (empty($targetNumber)) {
            Response::redirectWith(url('settings/wa'), 'error', 'Nomor WhatsApp tujuan uji coba harus diisi.');
            return;
        }

        $wa = \App\Services\WhatsAppService::forAdmin($adminId);
        $settingModel = new \App\Models\Setting();
        $siteName = $settingModel->get('site_name', 'ASR FORM');

        $testMsg = "🧪 *TEST NOTIFIKASI WHATSAPP TENANT*\n\n"
                 . "Halo! Ini adalah pesan uji coba dari sistem *{$siteName}*.\n"
                 . "Pengirim: Device Admin (ID: {$adminId})\n"
                 . "Waktu kirim: " . date('d/m/Y H:i:s') . " WIB\n\n"
                 . "Koneksi WhatsApp Gateway Anda dengan sender pribadi berhasil! ✅";

        $res = $wa->sendMessage($targetNumber, $testMsg);

        if ($res['success']) {
            Response::redirectWith(url('settings/wa'), 'success', 'Berhasil! Pesan WhatsApp uji coba telah terkirim ke ' . $targetNumber);
        } else {
            Response::redirectWith(url('settings/wa'), 'error', 'Gagal mengirim WhatsApp: ' . $res['message']);
        }
    }

    /**
     * Impersonate an Admin account (Super Admin only)
     */
    public function impersonate(string $id): void
    {
        CSRF::check();

        if (!Auth::isSuperAdmin()) {
            Response::redirectWith(url('dashboard'), 'error', 'Hanya Super Admin yang dapat melakukan impersonasi.');
            return;
        }

        $targetId = (int) $id;
        $targetAdmin = $this->adminModel->find($targetId);

        if (!$targetAdmin) {
            Response::redirectWith(url('admins'), 'error', 'Akun admin tidak ditemukan.');
            return;
        }

        if ((int) $targetAdmin->role_id === 1) {
            Response::redirectWith(url('admins'), 'error', 'Tidak dapat login sebagai sesama Super Admin.');
            return;
        }

        // Save original Super Admin state
        Session::set('impersonator_id', Auth::id());
        Session::set('impersonator_name', Auth::name());
        Session::set('impersonator_email', Auth::user()->email ?? '');

        // Switch active session to target admin
        Session::set('user_id', $targetAdmin->id);
        Session::set('user_name', $targetAdmin->name);
        Session::set('user_email', $targetAdmin->email);
        Session::set('user_role', 'Admin');
        Session::set('user_role_id', 2);
        Session::set('user_permissions', json_encode(["forms","templates","documents","responses","users","settings"]));
        Session::set('user_type', 'admin');
        Session::set('admin_id', $targetAdmin->id);

        AuditLog::log('impersonate', 'auth', $targetAdmin->id, "Super Admin (" . Session::get('impersonator_name') . ") login sebagai Admin: {$targetAdmin->name}");

        Response::redirectWith(url('dashboard'), 'success', "Berhasil masuk sebagai Admin: {$targetAdmin->name}. Anda dapat mengelola form, template, dan data milik admin ini.");
    }

    /**
     * Leave impersonation and return to Super Admin session
     */
    public function leaveImpersonate(): void
    {
        if (!Auth::isImpersonating()) {
            Response::redirect(url('dashboard'));
            return;
        }

        $superAdminId = (int) Session::get('impersonator_id');
        $superAdmin = $this->adminModel->find($superAdminId);

        if (!$superAdmin || (int) $superAdmin->role_id !== 1) {
            Session::destroy();
            Response::redirectWith(url('login'), 'error', 'Sesi Super Admin tidak valid. Silakan login kembali.');
            return;
        }

        $impersonatedName = Auth::name();

        // Restore Super Admin session
        Session::set('user_id', $superAdmin->id);
        Session::set('user_name', $superAdmin->name);
        Session::set('user_email', $superAdmin->email);
        Session::set('user_role', 'Super Admin');
        Session::set('user_role_id', 1);
        Session::set('user_permissions', '"*"');
        Session::set('user_type', 'admin');
        Session::set('admin_id', $superAdmin->id);

        // Clear impersonator flags
        Session::remove('impersonator_id');
        Session::remove('impersonator_name');
        Session::remove('impersonator_email');

        AuditLog::log('leave_impersonate', 'auth', $superAdminId, "Super Admin kembali dari impersonasi Admin: {$impersonatedName}");

        Response::redirectWith(url('admins'), 'success', "Kembali ke sesi Super Admin ({$superAdmin->name}).");
    }
}
