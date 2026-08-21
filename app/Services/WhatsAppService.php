<?php

namespace App\Services;

use App\Models\Setting;

/**
 * WhatsApp Gateway Service
 * Integrated with custom gateway: https://gateway.asr-desain.my.id
 */
class WhatsAppService
{
    private static ?self $instance = null;
    private Setting $settings;

    private string $endpointMessage = 'https://gateway.asr-desain.my.id/send-message';
    private string $endpointMedia   = 'https://gateway.asr-desain.my.id/send-media';

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
        return (int) $this->settings->get('wa_enabled', '0') === 1;
    }

    public function getApiKey(): string
    {
        return trim($this->settings->get('wa_api_key', ''));
    }

    public function getSender(): string
    {
        return trim($this->settings->get('wa_sender', ''));
    }

    public function getAdminNumber(): string
    {
        return trim($this->settings->get('wa_admin_number', ''));
    }

    public function getFooter(): string
    {
        return trim($this->settings->get('wa_footer', 'Sent by ASR FORM System'));
    }

    /**
     * Format phone number to international standard (e.g., 0812xxx -> 62812xxx)
     */
    public function formatNumber(string $number): string
    {
        $num = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($num, '0')) {
            $num = '62' . substr($num, 1);
        } elseif (str_starts_with($num, '+62')) {
            $num = substr($num, 1);
        }
        return $num;
    }

    /**
     * Send text message via WhatsApp Gateway
     * 
     * @param string $recipient Target phone number (e.g. 62812xxx)
     * @param string $message Message text
     * @param string|null $footer Optional footer override
     * @return array Response [success => bool, message => string, data => mixed]
     */
    public function sendMessage(string $recipient, string $message, ?string $footer = null): array
    {
        $apiKey = $this->getApiKey();
        $sender = $this->getSender();
        $recipient = $this->formatNumber($recipient);

        if (empty($apiKey) || empty($sender)) {
            return [
                'success' => false,
                'message' => 'API Key atau nomor Sender WhatsApp Gateway belum dikonfigurasi.',
            ];
        }

        if (empty($recipient)) {
            return [
                'success' => false,
                'message' => 'Nomor tujuan tidak valid.',
            ];
        }

        $payload = [
            'api_key' => $apiKey,
            'sender'  => $sender,
            'number'  => $recipient,
            'message' => $message,
            'footer'  => $footer ?? $this->getFooter(),
            'full'    => 1,
        ];

        return $this->sendRequest($this->endpointMessage, $payload);
    }

    /**
     * Send media message via WhatsApp Gateway
     * 
     * @param string $recipient Target phone number
     * @param string $mediaUrl Direct URL of the media (must not be cloud storage share link)
     * @param string $mediaType image | video | audio | document
     * @param string $caption Optional caption
     * @param string|null $footer Optional footer
     * @return array
     */
    public function sendMedia(string $recipient, string $mediaUrl, string $mediaType = 'image', string $caption = '', ?string $footer = null): array
    {
        $apiKey = $this->getApiKey();
        $sender = $this->getSender();
        $recipient = $this->formatNumber($recipient);

        if (empty($apiKey) || empty($sender)) {
            return [
                'success' => false,
                'message' => 'API Key atau nomor Sender WhatsApp Gateway belum dikonfigurasi.',
            ];
        }

        $payload = [
            'api_key'    => $apiKey,
            'sender'     => $sender,
            'number'     => $recipient,
            'media_type' => $mediaType,
            'caption'    => $caption,
            'footer'     => $footer ?? $this->getFooter(),
            'url'        => $mediaUrl,
            'full'       => 1,
        ];

        return $this->sendRequest($this->endpointMedia, $payload);
    }

    /**
     * Send notification to Admin (if configured and enabled)
     */
    public function notifyAdmin(string $message): array
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'WhatsApp Gateway nonaktif.'];
        }

        $adminNum = $this->getAdminNumber();
        if (empty($adminNum)) {
            return ['success' => false, 'message' => 'Nomor WhatsApp Admin belum diisi.'];
        }

        return $this->sendMessage($adminNum, $message);
    }

    /**
     * Send notification to User (if WA is enabled)
     */
    public function notifyUser(string $userPhone, string $message): array
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'WhatsApp Gateway nonaktif.'];
        }

        if (empty($userPhone)) {
            return ['success' => false, 'message' => 'Nomor telepon pengguna kosong.'];
        }

        return $this->sendMessage($userPhone, $message);
    }

    /**
     * Execute HTTP POST JSON request to Gateway
     */
    private function sendRequest(string $url, array $data): array
    {
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke WhatsApp Gateway: ' . $err,
            ];
        }

        $decoded = json_decode($result, true) ?: [];

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'message' => $decoded['message'] ?? 'Pesan berhasil dikirim via WhatsApp.',
                'data'    => $decoded,
            ];
        }

        return [
            'success' => false,
            'message' => $decoded['message'] ?? $decoded['msg'] ?? ("HTTP Error {$httpCode}: " . substr($result, 0, 150)),
            'data'    => $decoded,
        ];
    }
}
