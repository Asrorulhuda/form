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
     * Send notification to both User and Admin simultaneously in parallel (Ultra-Fast)
     * Total wait time equals 1 single request instead of 2 sequential requests.
     */
    public function notifyBoth(?string $userPhone, ?string $userMessage, ?string $adminMessage): array
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'WhatsApp Gateway nonaktif.'];
        }

        $tasks = [];

        if (!empty($userPhone) && !empty($userMessage)) {
            $tasks[] = [
                'recipient' => $userPhone,
                'message'   => $userMessage,
            ];
        }

        $adminNum = $this->getAdminNumber();
        if (!empty($adminNum) && !empty($adminMessage)) {
            $tasks[] = [
                'recipient' => $adminNum,
                'message'   => $adminMessage,
            ];
        }

        if (empty($tasks)) {
            return ['success' => true, 'message' => 'Tidak ada pesan yang dikirim.'];
        }

        return $this->sendBatch($tasks);
    }

    /**
     * Send multiple messages in parallel using curl_multi for near-instant dispatch
     * 
     * @param array $messages Array of ['recipient' => string, 'message' => string, 'footer' => ?string]
     * @return array
     */
    public function sendBatch(array $messages): array
    {
        $apiKey = $this->getApiKey();
        $sender = $this->getSender();

        if (empty($apiKey) || empty($sender)) {
            return ['success' => false, 'message' => 'API Key / Sender belum dikonfigurasi.'];
        }

        if (empty($messages)) {
            return ['success' => true, 'message' => 'Batch kosong.'];
        }

        $mh = curl_multi_init();
        $handles = [];

        foreach ($messages as $idx => $item) {
            $recip = $this->formatNumber($item['recipient'] ?? '');
            $msg   = $item['message'] ?? '';
            if (empty($recip) || empty($msg)) {
                continue;
            }

            $payload = json_encode([
                'api_key' => $apiKey,
                'sender'  => $sender,
                'number'  => $recip,
                'message' => $msg,
                'footer'  => $item['footer'] ?? $this->getFooter(),
                'full'    => 1,
            ]);

            $ch = curl_init($this->endpointMessage);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($payload),
                    'Accept: application/json',
                ],
                CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
                CURLOPT_TCP_NODELAY    => 1,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_TIMEOUT        => 4,
                CURLOPT_NOSIGNAL       => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            ]);

            curl_multi_add_handle($mh, $ch);
            $handles[$idx] = $ch;
        }

        if (empty($handles)) {
            curl_multi_close($mh);
            return ['success' => false, 'message' => 'Tidak ada penerima valid.'];
        }

        // Execute all requests concurrently
        $running = null;
        do {
            $status = curl_multi_exec($mh, $running);
            if ($running > 0) {
                curl_multi_select($mh, 0.05);
            }
        } while ($running > 0 && $status === CURLM_OK);

        $results = [];
        foreach ($handles as $idx => $ch) {
            $res = curl_multi_getcontent($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $results[$idx] = [
                'code'    => $code,
                'success' => ($code >= 200 && $code < 300),
                'data'    => json_decode($res, true) ?: $res,
            ];
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }

        curl_multi_close($mh);

        return [
            'success' => true,
            'message' => count($results) . ' pesan dikirim secara paralel.',
            'batch'   => $results,
        ];
    }

    /**
     * Execute HTTP POST JSON request to Gateway with optimized low-latency settings
     */
    private function sendRequest(string $url, array $data): array
    {
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonData,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData),
                'Accept: application/json',
            ],
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_TCP_NODELAY    => 1,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT        => 4,
            CURLOPT_NOSIGNAL       => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        ]);

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
