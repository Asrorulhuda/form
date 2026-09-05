<?php

namespace App\Core;

/**
 * CSRF Protection
 * Generates and validates CSRF tokens for all POST/PUT/DELETE requests.
 */
class CSRF
{
    /**
     * Generate a CSRF token and store in session
     */
    public static function generate(): string
    {
        if (!Session::has('_csrf_token')) {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf_token', $token);
        }
        return Session::get('_csrf_token');
    }

    /**
     * Get the hidden input field HTML
     */
    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Validate the CSRF token
     */
    public static function validate(?string $token = null): bool
    {
        $token = $token ?? ($_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        $sessionToken = Session::get('_csrf_token', '');

        if (empty($token) || empty($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Validate and throw exception if invalid
     */
    public static function check(): void
    {
        if (!self::validate()) {
            http_response_code(403);

            $isAjax = (
                (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
                str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') ||
                str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')
            );

            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'message' => 'Token keamanan (CSRF) kedaluwarsa atau tidak valid. Silakan muat ulang halaman.',
                ]);
                exit;
            }

            if (!empty($_SERVER['HTTP_REFERER'])) {
                Session::flash('error', 'Sesi keamanan formulir Anda telah berakhir. Silakan muat ulang halaman dan coba lagi.');
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }

            echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><title>403 - Akses Ditolak</title><style>body{font-family:sans-serif;background:#f8fafc;color:#1e293b;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}.card{background:#fff;padding:32px;border-radius:12px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);max-width:480px;text-align:center;}h1{color:#ef4444;font-size:20px;margin-top:0;}p{color:#64748b;font-size:14px;line-height:1.6;}.btn{display:inline-block;margin-top:16px;background:#4f46e5;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;}</style></head><body><div class='card'><h1>Sesi Keamanan Kedaluwarsa (403)</h1><p>Token keamanan (CSRF) tidak valid atau sesi Anda telah kedaluwarsa karena terlalu lama tidak aktif.</p><a href='javascript:history.back()' class='btn'>Kembali & Muat Ulang</a></div></body></html>";
            exit;
        }
    }

    /**
     * Regenerate the CSRF token
     */
    public static function regenerate(): string
    {
        Session::remove('_csrf_token');
        return self::generate();
    }

    /**
     * Get meta tag for AJAX requests
     */
    public static function meta(): string
    {
        return '<meta name="csrf-token" content="' . htmlspecialchars(self::generate()) . '">';
    }
}
