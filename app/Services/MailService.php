<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Mail Service
 * Native PHP SMTP Client with SSL/TLS socket support for Gmail and standard SMTP servers.
 */
class MailService
{
    private static ?self $instance = null;
    private Setting $settings;

    public function __construct()
    {
        $this->settings = new Setting();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function isEnabled(): bool
    {
        return (int) $this->settings->get('smtp_enabled', '0') === 1;
    }

    public function getHost(): string
    {
        return trim($this->settings->get('smtp_host', 'smtp.gmail.com'));
    }

    public function getPort(): int
    {
        return (int) $this->settings->get('smtp_port', 465);
    }

    public function getEncryption(): string
    {
        return strtolower(trim($this->settings->get('smtp_encryption', 'ssl')));
    }

    public function getUsername(): string
    {
        return trim($this->settings->get('smtp_username', ''));
    }

    public function getPassword(): string
    {
        return trim($this->settings->get('smtp_password', ''));
    }

    public function getFromName(): string
    {
        return trim($this->settings->get('smtp_from_name', 'ASR FORM Notification'));
    }

    public function getFromEmail(): string
    {
        $from = trim($this->settings->get('smtp_from_email', ''));
        return !empty($from) ? $from : $this->getUsername();
    }

    public function getAdminEmail(): string
    {
        return trim($this->settings->get('smtp_admin_email', ''));
    }

    /**
     * Send an email via SMTP
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $htmlBody HTML body content
     * @param string|null $altBody Plain text alternative
     * @return array [success => bool, message => string]
     */
    public function send(string $to, string $subject, string $htmlBody, ?string $altBody = null): array
    {
        $host       = $this->getHost();
        $port       = $this->getPort();
        $encryption = $this->getEncryption();
        $username   = $this->getUsername();
        $password   = $this->getPassword();
        $fromEmail  = $this->getFromEmail();
        $fromName   = $this->getFromName();

        if (empty($host) || empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Konfigurasi SMTP / Gmail belum lengkap (Host, Username/Email, atau Password kosong).',
            ];
        }

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Alamat email penerima tidak valid: ' . $to,
            ];
        }

        try {
            $socketHost = $host;
            if ($encryption === 'ssl') {
                $socketHost = 'ssl://' . $host;
            }

            $socket = @fsockopen($socketHost, $port, $errno, $errstr, 15);
            if (!$socket) {
                return [
                    'success' => false,
                    'message' => "Gagal membuka koneksi ke {$host}:{$port} ($errstr [{$errno}])",
                ];
            }

            stream_set_timeout($socket, 15);

            $this->readResponse($socket); // Read banner

            // EHLO
            $this->sendCommand($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));

            // STARTTLS if port 587
            if ($encryption === 'tls' || ($port === 587 && $encryption !== 'ssl')) {
                $this->sendCommand($socket, "STARTTLS");
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    fclose($socket);
                    return ['success' => false, 'message' => 'Gagal negosiasi TLS encryption dengan server SMTP.'];
                }
                $this->sendCommand($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
            }

            // AUTH LOGIN
            $res = $this->sendCommand($socket, "AUTH LOGIN");
            if (!str_starts_with($res, '334')) {
                fclose($socket);
                return ['success' => false, 'message' => 'Server tidak merespons perintah AUTH LOGIN: ' . $res];
            }

            $res = $this->sendCommand($socket, base64_encode($username));
            if (!str_starts_with($res, '334')) {
                fclose($socket);
                return ['success' => false, 'message' => 'Username/Email SMTP ditolak server: ' . $res];
            }

            $res = $this->sendCommand($socket, base64_encode($password));
            if (!str_starts_with($res, '235')) {
                fclose($socket);
                return ['success' => false, 'message' => 'Password / App Password SMTP salah: ' . $res];
            }

            // MAIL FROM
            $res = $this->sendCommand($socket, "MAIL FROM: <{$fromEmail}>");
            if (!str_starts_with($res, '250')) {
                fclose($socket);
                return ['success' => false, 'message' => 'MAIL FROM ditolak: ' . $res];
            }

            // RCPT TO
            $res = $this->sendCommand($socket, "RCPT TO: <{$to}>");
            if (!str_starts_with($res, '250')) {
                fclose($socket);
                return ['success' => false, 'message' => 'Penerima (' . $to . ') ditolak oleh server: ' . $res];
            }

            // DATA
            $res = $this->sendCommand($socket, "DATA");
            if (!str_starts_with($res, '354')) {
                fclose($socket);
                return ['success' => false, 'message' => 'Perintah DATA ditolak: ' . $res];
            }

            // Build Email Message
            $boundary = "----=_NextPart_" . md5(uniqid(time()));
            $encodedSubject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
            $encodedFromName = "=?UTF-8?B?" . base64_encode($fromName) . "?=";

            $headers  = "From: {$encodedFromName} <{$fromEmail}>\r\n";
            $headers .= "To: <{$to}>\r\n";
            $headers .= "Subject: {$encodedSubject}\r\n";
            $headers .= "Date: " . date('r') . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
            $headers .= "X-Mailer: ASR-FORM-Mailer/1.0\r\n\r\n";

            $plain = $altBody ?? strip_tags($htmlBody);

            $body  = "--{$boundary}\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= chunk_split(base64_encode($plain)) . "\r\n";

            $body .= "--{$boundary}\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= chunk_split(base64_encode($htmlBody)) . "\r\n";

            $body .= "--{$boundary}--\r\n";

            $messageData = $headers . $body . "\r\n.\r\n";
            fwrite($socket, $messageData);

            $dataRes = $this->readResponse($socket);
            $this->sendCommand($socket, "QUIT");
            fclose($socket);

            if (str_starts_with($dataRes, '250')) {
                return [
                    'success' => true,
                    'message' => 'Email berhasil dikirim ke ' . $to,
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $dataRes,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan pengiriman email: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send email notification to Admin
     */
    public function notifyAdmin(string $subject, string $htmlBody): array
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'SMTP Gateway nonaktif.'];
        }

        $adminEmail = $this->getAdminEmail();
        if (empty($adminEmail)) {
            $adminEmail = $this->getUsername();
        }

        if (empty($adminEmail)) {
            return ['success' => false, 'message' => 'Email Admin belum dikonfigurasi.'];
        }

        return $this->send($adminEmail, $subject, $htmlBody);
    }

    /**
     * Send email notification to User
     */
    public function notifyUser(string $userEmail, string $subject, string $htmlBody): array
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'SMTP Gateway nonaktif.'];
        }

        return $this->send($userEmail, $subject, $htmlBody);
    }

    private function sendCommand($socket, string $command): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->readResponse($socket);
    }

    private function readResponse($socket): string
    {
        $response = "";
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) === " ") {
                break;
            }
        }
        return trim($response);
    }
}
