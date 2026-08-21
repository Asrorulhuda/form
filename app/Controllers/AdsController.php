<?php

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\View;
use App\Core\Response;
use App\Models\Setting;
use App\Models\AdSlot;
use App\Models\AuditLog;

/**
 * Ads Manager Controller
 * Admin interface for managing AdSense configuration and ad slots.
 */
class AdsController
{
    private Setting $settingModel;
    private AdSlot $adSlotModel;

    public function __construct()
    {
        $this->settingModel = new Setting();
        $this->adSlotModel = new AdSlot();
    }

    /**
     * Show Ads Manager page
     */
    public function index(): void
    {
        $adsenseSettings = $this->settingModel->getGroup('adsense_');
        $adSlots = $this->adSlotModel->getAll();

        // Determine status
        $publisherId = trim($adsenseSettings['adsense_publisher_id'] ?? '');
        $isEnabled = (int) ($adsenseSettings['adsense_enabled'] ?? 0) === 1;
        
        if (empty($publisherId)) {
            $status = 'not_configured';
            $statusLabel = 'Not Configured';
            $statusColor = 'warning';
        } elseif (!$isEnabled) {
            $status = 'configured_disabled';
            $statusLabel = 'Configured (Disabled)';
            $statusColor = 'secondary';
        } else {
            $status = 'configured_enabled';
            $statusLabel = 'Configured (Enabled)';
            $statusColor = 'success';
        }

        View::page('settings.ads', [
            'title'           => 'Iklan & AdSense',
            'pageTitle'       => 'Iklan & AdSense',
            'settings'        => $adsenseSettings,
            'adSlots'         => $adSlots,
            'status'          => $status,
            'statusLabel'     => $statusLabel,
            'statusColor'     => $statusColor,
        ]);
    }

    /**
     * Update AdSense settings
     */
    public function update(): void
    {
        CSRF::check();

        $data = [
            'adsense_enabled'      => isset($_POST['adsense_enabled']) ? '1' : '0',
            'adsense_publisher_id' => trim($_POST['adsense_publisher_id'] ?? ''),
            'adsense_auto_ads'     => isset($_POST['adsense_auto_ads']) ? '1' : '0',
            'adsense_form_top'     => isset($_POST['adsense_form_top']) ? '1' : '0',
            'adsense_form_bottom'  => isset($_POST['adsense_form_bottom']) ? '1' : '0',
            'adsense_form_success' => isset($_POST['adsense_form_success']) ? '1' : '0',
            'adsense_public_page'  => isset($_POST['adsense_public_page']) ? '1' : '0',
            'adsense_dashboard'    => isset($_POST['adsense_dashboard']) ? '1' : '0',
        ];

        // Validate Publisher ID format if provided
        $pubId = $data['adsense_publisher_id'];
        if (!empty($pubId) && !preg_match('/^ca-pub-\d+$/', $pubId)) {
            Response::redirectWith(url('settings/ads'), 'error', 'Format Publisher ID tidak valid. Gunakan format: ca-pub-XXXXXXXXXXXXXXXX');
            return;
        }

        $this->settingModel->bulkUpdate($data);
        AuditLog::log('update', 'settings', null, 'Mengubah pengaturan AdSense');

        Response::redirectWith(url('settings/ads'), 'success', 'Pengaturan iklan berhasil disimpan.');
    }

    /**
     * Toggle ad slot enabled/disabled
     */
    public function toggleSlot(string $id): void
    {
        CSRF::check();

        $slot = $this->adSlotModel->getById((int) $id);
        if (!$slot) {
            Response::redirectWith(url('settings/ads'), 'error', 'Slot iklan tidak ditemukan.');
            return;
        }

        $this->adSlotModel->toggleEnabled((int) $id);
        $newState = !((int) $slot->enabled);
        AuditLog::log('update', 'ad_slots', (int) $id, 'Toggle slot iklan: ' . $slot->name . ' → ' . ($newState ? 'ON' : 'OFF'));

        Response::redirectWith(url('settings/ads'), 'success', 'Slot "' . $slot->name . '" berhasil di' . ($newState ? 'aktifkan' : 'nonaktifkan') . '.');
    }
}
