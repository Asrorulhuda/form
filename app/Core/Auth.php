<?php

namespace App\Core;

/**
 * Authentication Helper — Multi-SaaS
 * Manages login, logout, and session-based user state.
 * Supports dual-table authentication: admins + users.
 */
class Auth
{
    private static ?string $lastError = null;

    /**
     * Get last login failure reason
     */
    public static function getLastError(): ?string
    {
        return self::$lastError;
    }

    /**
     * Attempt to log in a user (checks admins table first, then users table)
     */
    public static function attempt(string $email, string $password): bool
    {
        self::$lastError = null;
        $db = Database::getInstance();

        // 1. Try admins table first (Super Admin + Admin)
        $admin = $db->fetch(
            "SELECT a.*, r.name as role_name, r.permissions as role_permissions 
             FROM admins a 
             JOIN roles r ON a.role_id = r.id 
             WHERE a.email = ?",
            [$email]
        );

        if ($admin && password_verify($password, $admin->password)) {
            return self::loginAsAdmin($admin, $db);
        }

        // 2. Try users table (end-users)
        $user = $db->fetch(
            "SELECT u.*, r.name as role_name, r.permissions as role_permissions 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.email = ?",
            [$email]
        );

        if ($user && password_verify($password, $user->password)) {
            return self::loginAsUser($user, $db);
        }

        // Neither found or password wrong
        self::$lastError = 'Email atau password salah.';
        return false;
    }

    /**
     * Complete login for an admin account
     */
    private static function loginAsAdmin(object $admin, Database $db): bool
    {
        if ($admin->status === 'pending') {
            self::$lastError = 'Akun Anda sedang menunggu persetujuan (approval) dari Super Admin.';
            return false;
        }
        if ($admin->status === 'rejected') {
            self::$lastError = 'Pendaftaran akun Anda ditolak oleh Super Admin.';
            return false;
        }
        if ($admin->status === 'inactive') {
            self::$lastError = 'Akun Anda sedang dinonaktifkan. Silakan hubungi Super Admin.';
            return false;
        }

        Session::regenerate();

        Session::set('user_id', $admin->id);
        Session::set('user_name', $admin->name);
        Session::set('user_email', $admin->email);
        Session::set('user_role', $admin->role_name);
        Session::set('user_role_id', $admin->role_id);
        Session::set('user_permissions', $admin->role_permissions);
        Session::set('user_type', 'admin');
        // For Super Admin, admin_id is their own id. For Admin, same thing.
        Session::set('admin_id', $admin->id);
        Session::set('logged_in', true);

        $db->update('admins', ['updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$admin->id]);

        return true;
    }

    /**
     * Complete login for an end-user account
     */
    private static function loginAsUser(object $user, Database $db): bool
    {
        if ($user->status === 'pending') {
            self::$lastError = 'Akun Anda sedang menunggu aktivasi oleh Admin.';
            return false;
        }
        if ($user->status === 'inactive') {
            self::$lastError = 'Akun Anda sedang dinonaktifkan. Silakan hubungi Admin Anda.';
            return false;
        }

        Session::regenerate();

        Session::set('user_id', $user->id);
        Session::set('user_name', $user->name);
        Session::set('user_email', $user->email);
        Session::set('user_role', $user->role_name);
        Session::set('user_role_id', $user->role_id);
        Session::set('user_permissions', $user->role_permissions);
        Session::set('user_type', 'user');
        Session::set('admin_id', $user->admin_id);
        Session::set('logged_in', true);

        $db->update('users', ['updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$user->id]);

        return true;
    }

    /**
     * Log out the current user
     */
    public static function logout(): void
    {
        Session::destroy();
    }

    /**
     * Check if user is authenticated
     */
    public static function check(): bool
    {
        return Session::get('logged_in', false) === true;
    }

    /**
     * Get current user ID
     */
    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    /**
     * Get the admin_id (tenant scope) for the current session
     */
    public static function adminId(): ?int
    {
        return Session::get('admin_id');
    }

    /**
     * Get current user type ('admin' or 'user')
     */
    public static function userType(): string
    {
        $type = Session::get('user_type');
        if (!$type) {
            $roleId = (int) Session::get('user_role_id');
            $roleName = Session::get('user_role');
            if ($roleId === 1 || $roleId === 2 || $roleName === 'Super Admin' || $roleName === 'Admin') {
                $type = 'admin';
                Session::set('user_type', 'admin');
                if (!Session::get('admin_id')) {
                    Session::set('admin_id', Session::get('user_id'));
                }
            } else {
                $type = 'user';
                Session::set('user_type', 'user');
            }
        }
        return $type;
    }

    /**
     * Check if current user is Super Admin
     */
    public static function isSuperAdmin(): bool
    {
        $roleId = (int) Session::get('user_role_id');
        $roleName = Session::get('user_role');
        return $roleId === 1 || $roleName === 'Super Admin';
    }

    /**
     * Check if current user is Admin (tenant owner, not Super Admin)
     */
    public static function isAdmin(): bool
    {
        $roleId = (int) Session::get('user_role_id');
        $roleName = Session::get('user_role');
        return ($roleId === 2 || $roleName === 'Admin') && !self::isSuperAdmin();
    }

    /**
     * Check if current user is an end-user
     */
    public static function isUser(): bool
    {
        return !self::isSuperAdmin() && !self::isAdmin();
    }

    /**
     * Check if current user is any admin type (Super Admin OR Admin)
     */
    public static function isAnyAdmin(): bool
    {
        return self::isSuperAdmin() || self::isAdmin();
    }

    /**
     * Get current user info
     */
    public static function user(): ?object
    {
        if (!self::check()) {
            return null;
        }

        return (object) [
            'id'          => Session::get('user_id'),
            'name'        => Session::get('user_name'),
            'email'       => Session::get('user_email'),
            'role'        => Session::get('user_role'),
            'role_id'     => Session::get('user_role_id'),
            'permissions' => Session::get('user_permissions'),
            'user_type'   => Session::get('user_type'),
            'admin_id'    => Session::get('admin_id'),
        ];
    }

    /**
     * Check if user has a specific role
     */
    public static function hasRole(string ...$roles): bool
    {
        $userRole = Session::get('user_role', '');
        return in_array($userRole, $roles);
    }

    /**
     * Check if user has a specific permission
     */
    public static function can(string $permission): bool
    {
        $permissions = Session::get('user_permissions', '');
        if ($permissions === '*' || $permissions === '"*"') {
            return true; // Super Admin can do everything
        }

        $permArray = json_decode($permissions, true) ?? [];
        return in_array($permission, $permArray);
    }

    /**
     * Get user's display name
     */
    public static function name(): string
    {
        return Session::get('user_name', 'Guest');
    }

    /**
     * Get user's role name
     */
    public static function role(): string
    {
        return Session::get('user_role', '');
    }

    /**
     * Check if currently in an impersonation session
     */
    public static function isImpersonating(): bool
    {
        return Session::has('impersonator_id');
    }

    /**
     * Get the name of the impersonator (original Super Admin)
     */
    public static function impersonatorName(): ?string
    {
        return Session::get('impersonator_name');
    }
}
