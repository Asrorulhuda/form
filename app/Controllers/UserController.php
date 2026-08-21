<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Session;
use App\Core\View;
use App\Core\Response;
use App\Core\Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;

/**
 * User Management Controller
 */
class UserController
{
    private User $userModel;
    private Role $roleModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    /**
     * List users
     */
    public function index(): void
    {
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $filters = [
            'search'  => $_GET['search'] ?? '',
            'role_id' => $_GET['role_id'] ?? '',
            'status'  => $_GET['status'] ?? '',
        ];

        $result = $this->userModel->getAll($filters, $page);
        $roles  = $this->roleModel->getAll();

        View::page('users.index', [
            'title'     => 'Kelola Pengguna',
            'pageTitle' => 'Kelola Pengguna',
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
        $roles = $this->roleModel->getAll();
        $settingModel = new \App\Models\Setting();
        $plans = json_decode($settingModel->get('page_pricing_items', '[]'), true) ?: [];

        View::page('users.create', [
            'title'     => 'Tambah Pengguna',
            'pageTitle' => 'Tambah Pengguna',
            'roles'     => $roles,
            'plans'     => $plans,
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
            'password' => $_POST['password'] ?? '',
            'role_id'  => (int) ($_POST['role_id'] ?? 0),
            'plan'     => trim($_POST['plan'] ?? 'Gratis'),
            'status'   => $_POST['status'] ?? 'active',
        ];

        $validator = new Validator([
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id'  => 'required|numeric',
        ]);

        if (!$validator->validate($data)) {
            Session::flash('errors', $validator->errors());
            Session::setOld($data);
            Response::redirect(url('users/create'));
            return;
        }

        $this->userModel->create($data);
        AuditLog::log('create', 'users', null, "Membuat pengguna [Paket: {$data['plan']}]: {$data['name']}");

        Response::redirectWith(url('users'), 'success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Show edit user form
     */
    public function edit(string $id): void
    {
        $user = $this->userModel->find((int) $id);
        if (!$user) {
            Response::redirectWith(url('users'), 'error', 'Pengguna tidak ditemukan.');
            return;
        }

        $roles = $this->roleModel->getAll();
        $settingModel = new \App\Models\Setting();
        $plans = json_decode($settingModel->get('page_pricing_items', '[]'), true) ?: [];

        View::page('users.edit', [
            'title'     => 'Edit Pengguna',
            'pageTitle' => 'Edit Pengguna',
            'user'      => $user,
            'roles'     => $roles,
            'plans'     => $plans,
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
            Response::redirectWith(url('users'), 'error', 'Pengguna tidak ditemukan.');
            return;
        }

        $data = [
            'name'     => trim($_POST['name'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role_id'  => (int) ($_POST['role_id'] ?? 0),
            'plan'     => trim($_POST['plan'] ?? ($user->plan ?? 'Gratis')),
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
        AuditLog::log('update', 'users', $userId, "Mengubah pengguna: {$data['name']}");

        Response::redirectWith(url('users'), 'success', 'Pengguna berhasil diperbarui.');
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
            Response::redirectWith(url('users'), 'error', 'Pengguna tidak ditemukan.');
            return;
        }

        // Prevent deleting yourself
        if ($userId === Auth::id()) {
            Response::redirectWith(url('users'), 'error', 'Tidak dapat menghapus akun sendiri.');
            return;
        }

        $this->userModel->delete($userId);
        AuditLog::log('delete', 'users', $userId, "Menghapus pengguna: {$user->name}");

        Response::redirectWith(url('users'), 'success', 'Pengguna berhasil dihapus.');
    }
}
