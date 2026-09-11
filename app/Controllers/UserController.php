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

        View::page('users.index', [
            'title'     => 'Kelola User',
            'pageTitle' => 'Kelola User',
            'users'     => $result['data'],
            'total'     => $result['total'],
            'page'      => $result['page'],
            'lastPage'  => $result['lastPage'],
            'filters'   => $filters,
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

        View::page('users.edit', [
            'title'     => 'Edit User',
            'pageTitle' => 'Edit User',
            'user'      => $user,
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

        $this->userModel->update($userId, $data);
        AuditLog::log('update', 'users', $userId, "Admin mengubah user: {$data['name']}");

        Response::redirectWith(url('users'), 'success', 'User berhasil diperbarui.');
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
