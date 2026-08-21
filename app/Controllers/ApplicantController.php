<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Database;
use App\Core\Session;
use App\Core\View;
use App\Core\Response;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;

/**
 * Applicant Management Controller (Approval Pendaftar Baru)
 */
class ApplicantController
{
    private User $userModel;
    private Role $roleModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    /**
     * List all pending applicants
     */
    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $filters = [
            'search' => trim($_GET['search'] ?? ''),
        ];

        $result = $this->userModel->getPendingApplicants($filters, $page, 15);
        $roles  = $this->roleModel->getAll();

        View::page('applicants.index', [
            'title'      => 'Pendaftar Akun Baru',
            'pageTitle'  => 'Pendaftar Akun',
            'applicants' => $result['data'],
            'total'      => $result['total'],
            'page'       => $result['page'],
            'lastPage'   => $result['lastPage'],
            'filters'    => $filters,
            'roles'      => $roles,
        ]);
    }

    /**
     * Approve applicant (ACC)
     */
    public function approve(string $id): void
    {
        CSRF::check();

        $userId = (int) $id;
        $user = $this->userModel->find($userId);

        if (!$user || $user->status !== 'pending') {
            Response::redirectWith(url('applicants'), 'error', 'Data pendaftar tidak ditemukan atau sudah diproses.');
            return;
        }

        $roleId = isset($_POST['role_id']) && !empty($_POST['role_id']) ? (int) $_POST['role_id'] : null;

        $this->userModel->approve($userId, $roleId);
        AuditLog::log('approve', 'users', $userId, "Menyetujui pendaftaran user: {$user->name} ({$user->email})");

        Response::redirectWith(url('applicants'), 'success', "Akun pendaftar '{$user->name}' berhasil disetujui (ACC) dan aktif.");
    }

    /**
     * Reject applicant
     */
    public function reject(string $id): void
    {
        CSRF::check();

        $userId = (int) $id;
        $user = $this->userModel->find($userId);

        if (!$user || $user->status !== 'pending') {
            Response::redirectWith(url('applicants'), 'error', 'Data pendaftar tidak ditemukan atau sudah diproses.');
            return;
        }

        $this->userModel->reject($userId);
        AuditLog::log('reject', 'users', $userId, "Menolak pendaftaran user: {$user->name} ({$user->email})");

        Response::redirectWith(url('applicants'), 'success', "Pendaftaran '{$user->name}' berhasil ditolak.");
    }
}
