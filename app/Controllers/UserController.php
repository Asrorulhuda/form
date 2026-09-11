<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;
use App\Core\View;
use App\Core\Response;
use App\Core\Validator;
use App\Models\User;
use App\Models\AuditLog;

/**
 * User Management Controller — Multi-SaaS
 * Admin manages users that belong to their tenant.
 * Super Admin can see all users globally.
 */
class UserController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * List users (scoped to admin's tenant, or global for Super Admin)
     */
    public function index(): void
    {
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
        ];

        if (Auth::isSuperAdmin()) {
            $filters['admin_id'] = $_GET['admin_id'] ?? '';
            $result = $this->userModel->getAllGlobal($filters, $page);
        } else {
            $result = $this->userModel->getAll(Auth::adminId(), $filters, $page);
        }

        $roles = \App\Core\Database::getInstance()->fetchAll("SELECT * FROM roles ORDER BY id ASC");

        View::page('users.index', [
            'title'     => 'Kelola User',
            'pageTitle' => Auth::isSuperAdmin() ? 'Semua User' : 'Kelola User',
            'users'     => $result['data'],
            'total'     => $result['total'],
            'page'      => $result['page'],
            'lastPage'  => $result['lastPage'],
            'filters'   => $filters,
            'roles'     => $roles,
        ]);
    }

    /**
     * Show create user form
     */
    public function create(): void
    {
        View::page('users.create', [
            'title'     => 'Tambah User',
            'pageTitle' => 'Tambah User Baru',
        ]);
    }

    /**
     * Store new user
     */
    public function store(): void
    {
        CSRF::check();

        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'phone'    => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'status'   => $_POST['status'] ?? 'active',
        ];

        $validator = new Validator([
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if (!$validator->validate($data)) {
            Session::flash('errors', $validator->errors());
            Session::setOld($data);
            Response::redirect(url('users/create'));
            return;
        }

        // User belongs to current admin
        $data['admin_id'] = Auth::adminId();
        $data['role_id']  = 3; // User role

        $this->userModel->create($data);
        AuditLog::log('create', 'users', null, "Admin membuat user baru: {$data['name']}");

        Response::redirectWith(url('users'), 'success', 'User berhasil ditambahkan.');
    }

    /**
     * Show edit user form
     */
    public function edit(string $id): void
    {
        $user = $this->userModel->find((int) $id);
        if (!$user) {
            Response::redirectWith(url('users'), 'error', 'User tidak ditemukan.');
            return;
        }

        // Verify tenant ownership (Admin can only edit their own users)
        if (!Auth::isSuperAdmin() && (int)$user->admin_id !== Auth::adminId()) {
            Response::redirectWith(url('users'), 'error', 'Akses ditolak.');
            return;
        }

        $roles = \App\Core\Database::getInstance()->fetchAll("SELECT * FROM roles ORDER BY id ASC");

        View::page('users.edit', [
            'title'     => 'Edit User',
            'pageTitle' => 'Edit User',
            'user'      => $user,
            'roles'     => $roles,
        ]);
    }

    /**
     * Update user
     */
    public function update(string $id): void
    {
        CSRF::check();

        $userId = (int) $id;
        $user = $this->userModel->find($userId);
        if (!$user) {
            Response::redirectWith(url('users'), 'error', 'User tidak ditemukan.');
            return;
        }

        // Verify tenant ownership
        if (!Auth::isSuperAdmin() && (int)$user->admin_id !== Auth::adminId()) {
            Response::redirectWith(url('users'), 'error', 'Akses ditolak.');
            return;
        }

        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'phone'    => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'status'   => $_POST['status'] ?? 'active',
        ];

        $rules = [
            'name'  => 'required|min:2|max:100',
            'email' => "required|email|unique:users,email,{$userId}",
        ];

        if (!empty($data['password'])) {
            $rules['password'] = 'min:6';
        }

        $validator = new Validator($rules);
        if (!$validator->validate($data)) {
            Session::flash('errors', $validator->errors());
            Session::setOld($data);
            Response::redirect(url("users/{$userId}/edit"));
            return;
        }

        // Check if Super Admin requested to promote this user to Admin (role_id = 2)
        $targetRoleId = (int) ($_POST['role_id'] ?? 3);
        if (Auth::isSuperAdmin() && $targetRoleId === 2) {
            $this->promoteUserToAdmin($userId, $targetRoleId, $data);
            return;
        }

        $this->userModel->update($userId, $data);
        AuditLog::log('update', 'users', $userId, "Admin mengubah user: {$data['name']}");

        Response::redirectWith(url('users'), 'success', 'User berhasil diperbarui.');
    }

    /**
     * Quick change role from User to Admin (Super Admin only)
     */
    public function changeRole(string $id): void
    {
        CSRF::check();

        if (!Auth::isSuperAdmin()) {
            Response::redirectWith(url('users'), 'error', 'Hanya Super Admin yang dapat mengubah role pengguna.');
            return;
        }

        $targetRoleId = (int) ($_POST['role_id'] ?? 2);
        $this->promoteUserToAdmin((int) $id, $targetRoleId);
    }

    /**
     * Promote a user to Admin in the `admins` table
     */
    private function promoteUserToAdmin(int $userId, int $targetRoleId, array $updatedData = []): void
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            Response::redirectWith(url('users'), 'error', 'Pengguna tidak ditemukan.');
            return;
        }

        $targetEmail = !empty($updatedData['email']) ? $updatedData['email'] : $user->email;
        $targetName  = !empty($updatedData['name']) ? $updatedData['name'] : $user->name;
        $targetPhone = isset($updatedData['phone']) ? $updatedData['phone'] : ($user->phone ?? '');
        $targetStatus = !empty($updatedData['status']) ? $updatedData['status'] : ($user->status ?? 'active');

        // Check if an admin with the same email already exists
        $adminModel = new \App\Models\Admin();
        $existingAdmin = $adminModel->findByEmail($targetEmail);
        if ($existingAdmin) {
            Response::redirectWith(url('users'), 'error', "Email '{$targetEmail}' sudah terdaftar sebagai Admin di sistem.");
            return;
        }

        // Determine password hash
        $passwordHash = !empty($updatedData['password'])
            ? password_hash($updatedData['password'], PASSWORD_DEFAULT)
            : $user->password;

        $db = \App\Core\Database::getInstance();

        // 1. Insert into admins table
        $newAdminId = $db->insert('admins', [
            'name'       => $targetName,
            'email'      => $targetEmail,
            'phone'      => $targetPhone,
            'password'   => $passwordHash,
            'role_id'    => 2, // Admin
            'status'     => $targetStatus,
            'plan'       => 'Gratis',
            'created_at' => $user->created_at ?? date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // 2. Remove user from users table (Multi-SaaS role separation)
        $this->userModel->delete($userId);

        AuditLog::log('promote', 'users', (int)$newAdminId, "Super Admin mengubah role pengguna '{$targetName}' ({$targetEmail}) dari User menjadi Admin");

        Response::redirectWith(
            url('admins'), 
            'success', 
            "Berhasil mengubah role '{$targetName}' menjadi Admin! Akun kini terdaftar sebagai Admin tenant mandiri."
        );
    }

    /**
     * Delete user
     */
    public function destroy(string $id): void
    {
        CSRF::check();

        $userId = (int) $id;
        $user = $this->userModel->find($userId);

        if (!$user) {
            Response::redirectWith(url('users'), 'error', 'User tidak ditemukan.');
            return;
        }

        // Verify tenant ownership
        if (!Auth::isSuperAdmin() && (int)$user->admin_id !== Auth::adminId()) {
            Response::redirectWith(url('users'), 'error', 'Akses ditolak.');
            return;
        }

        $this->userModel->delete($userId);
        AuditLog::log('delete', 'users', $userId, "Admin menghapus user: {$user->name}");

        Response::redirectWith(url('users'), 'success', 'User berhasil dihapus.');
    }
}
