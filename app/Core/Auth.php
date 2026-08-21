<?php

namespace App\Core;

/**
 * Authentication Helper
 * Manages login, logout, and session-based user state.
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
     * Attempt to log in a user
     */
    public static function attempt(string $email, string $password): bool
    {
        self::$lastError = null;
        $db = Database::getInstance();

        $user = $db->fetch(
            "SELECT u.*, r.name as role_name, r.permissions as role_permissions 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.email = ?",
            [$email]
        );

        if (!$user || !password_verify($password, $user->password)) {
            self::$lastError = 'Email atau password salah.';
            return false;
        }

        // Check account status
        if ($user->status === 'pending') {
            self::$lastError = 'Akun Anda sedang menunggu persetujuan (approval) dari Administrator.';
            return false;
        }

        if ($user->status === 'rejected') {
            self::$lastError = 'Pendaftaran akun Anda ditolak oleh Administrator.';
            return false;
        }

        if ($user->status === 'inactive') {
            self::$lastError = 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator.';
            return false;
        }

        // Regenerate session ID to prevent fixation
        Session::regenerate();

        // Store user data in session
        Session::set('user_id', $user->id);
        Session::set('user_name', $user->name);
        Session::set('user_email', $user->email);
        Session::set('user_role', $user->role_name);
        Session::set('user_role_id', $user->role_id);
        Session::set('user_permissions', $user->role_permissions);
        Session::set('logged_in', true);

        // Update last login
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
        if ($permissions === '*') {
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
}
