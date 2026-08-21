<?php

namespace App\Core;

/**
 * HTTP Response Helper
 */
class Response
{
    /**
     * Send JSON response
     */
    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect to a URL
     */
    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Redirect back to previous page
     */
    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        self::redirect($referer);
    }

    /**
     * Redirect with flash message
     */
    public static function redirectWith(string $url, string $type, string $message): void
    {
        Session::flash('toast_type', $type);
        Session::flash('toast_message', $message);
        self::redirect($url);
    }

    /**
     * Success JSON response
     */
    public static function success(string $message = 'Success', mixed $data = null, int $status = 200): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Error JSON response
     */
    public static function error(string $message = 'Error', mixed $errors = null, int $status = 400): void
    {
        self::json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
