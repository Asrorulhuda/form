<?php

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Response;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\WebhookLog;
use App\Services\GitHubWebhookService;

/**
 * Webhook Controller
 * Handles incoming GitHub Webhooks and Admin GitHub settings & delivery logs.
 */
class WebhookController
{
    private Setting $settingModel;
    private WebhookLog $logModel;
    private GitHubWebhookService $service;

    public function __construct()
    {
        $this->settingModel = new Setting();
        $this->logModel     = new WebhookLog();
        $this->service      = GitHubWebhookService::getInstance();
    }

    /**
     * Public GitHub Webhook Handler Endpoint (POST /webhook/github)
     * No CSRF / Session required. Authenticated via HMAC SHA-256 signature.
     */
    public function github(): void
    {
        // Read raw body
        $rawPayload = file_get_contents('php://input') ?: '';

        // Extract headers
        $headers = [];
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $key => $value) {
                $headers[strtolower($key)] = $value;
            }
        }
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = strtolower(str_replace('_', '-', substr($key, 5)));
                if (!isset($headers[$headerName])) {
                    $headers[$headerName] = $value;
                }
            }
        }

        $result = $this->service->handle($rawPayload, $headers);

        // Send JSON response
        http_response_code($result['status'] ?? 200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Admin Settings & Webhook Management Page (GET /settings/github)
     */
    public function settings(): void
    {
        $settings = $this->settingModel->getGroup('github_');

        // Defaults if not set
        if (!isset($settings['github_webhook_enabled'])) {
            $settings['github_webhook_enabled'] = '1';
        }
        if (!isset($settings['github_webhook_branch'])) {
            $settings['github_webhook_branch'] = 'main';
        }
        if (!isset($settings['github_webhook_auto_pull'])) {
            $settings['github_webhook_auto_pull'] = '1';
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $statusFilter = trim($_GET['status'] ?? '');
        $search = trim($_GET['q'] ?? '');

        $logsData = $this->logModel->getAll([
            'status' => $statusFilter,
            'search' => $search,
        ], $page, 10);

        $gitInfo = $this->service->getGitInfo();
        $successCount = $this->logModel->countByStatus('success');
        $failedCount  = $this->logModel->countByStatus('failed');

        View::page('settings.github', [
            'title'        => 'GitHub Webhook & Auto Deploy',
            'pageTitle'    => 'Pengaturan GitHub Webhook & Auto Deploy',
            'settings'     => $settings,
            'logsData'     => $logsData,
            'gitInfo'      => $gitInfo,
            'successCount' => $successCount,
            'failedCount'  => $failedCount,
            'statusFilter' => $statusFilter,
            'search'       => $search,
        ]);
    }

    /**
     * Update GitHub Webhook Settings (POST /settings/github/update)
     */
    public function updateSettings(): void
    {
        CSRF::check();

        $data = [
            'github_webhook_enabled'        => isset($_POST['github_webhook_enabled']) ? '1' : '0',
            'github_repo_name'              => trim($_POST['github_repo_name'] ?? ''),
            'github_webhook_secret'         => trim($_POST['github_webhook_secret'] ?? ''),
            'github_webhook_branch'         => trim($_POST['github_webhook_branch'] ?? 'main') ?: 'main',
            'github_webhook_auto_pull'      => isset($_POST['github_webhook_auto_pull']) ? '1' : '0',
            'github_webhook_custom_command' => trim($_POST['github_webhook_custom_command'] ?? ''),
        ];

        $this->settingModel->bulkUpdate($data);
        AuditLog::log('update', 'settings', null, 'Mengubah konfigurasi GitHub Webhook & Auto Deploy');

        Response::redirectWith(url('settings/github'), 'success', 'Pengaturan GitHub Webhook berhasil disimpan.');
    }

    /**
     * Manual Git Pull Trigger (POST /settings/github/pull)
     */
    public function manualPull(): void
    {
        CSRF::check();

        $targetBranch = trim($this->settingModel->get('github_webhook_branch', 'main') ?? 'main') ?: 'main';
        $result = $this->service->executeGitPull($targetBranch);

        $status = $result['success'] ? 'success' : 'failed';
        $this->logModel->record([
            'event'          => 'manual_pull',
            'sender'         => \App\Core\Auth::user()->name ?? 'Admin',
            'repository'     => $this->settingModel->get('github_repo_name', 'Local Workspace'),
            'branch'         => $targetBranch,
            'status'         => $status,
            'commit_message' => 'Manual Git Pull triggered from Admin Panel',
            'output'         => $result['output'],
        ]);

        AuditLog::log('deploy', 'github', null, "Manual Git pull pada branch {$targetBranch} (" . ($result['success'] ? 'Success' : 'Failed') . ")");

        if ($result['success']) {
            Response::redirectWith(url('settings/github'), 'success', 'Git pull berhasil dijalankan! Output: ' . substr($result['output'], 0, 200));
        } else {
            Response::redirectWith(url('settings/github'), 'error', 'Git pull gagal: ' . $result['output']);
        }
    }

    /**
     * Clear all webhook logs (POST /settings/github/clear-logs)
     */
    public function clearLogs(): void
    {
        CSRF::check();

        $this->logModel->clearAll();
        AuditLog::log('delete', 'webhook_logs', null, 'Membersihkan seluruh riwayat log GitHub Webhook');

        Response::redirectWith(url('settings/github'), 'success', 'Riwayat log webhook berhasil dibersihkan.');
    }
}
