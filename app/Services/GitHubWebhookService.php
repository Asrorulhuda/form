<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\WebhookLog;

/**
 * GitHub Webhook Service
 * Handles signature verification, payload parsing, branch checks, and automated Git deployment.
 */
class GitHubWebhookService
{
    private static ?self $instance = null;
    private Setting $settingModel;
    private WebhookLog $logModel;

    private function __construct()
    {
        $this->settingModel = new Setting();
        $this->logModel     = new WebhookLog();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Process incoming GitHub webhook request
     */
    public function handle(string $rawPayload, array $headers): array
    {
        $enabled = ($this->settingModel->get('github_webhook_enabled', '1') === '1');
        if (!$enabled) {
            return [
                'success' => false,
                'status'  => 403,
                'message' => 'GitHub Webhook is currently disabled in system settings.',
            ];
        }

        // 1. Identify GitHub Event & Delivery ID
        $event      = $headers['x-github-event'] ?? $headers['http_x_github_event'] ?? 'push';
        $deliveryId = $headers['x-github-delivery'] ?? $headers['http_x_github_delivery'] ?? null;
        $signature  = $headers['x-hub-signature-256'] ?? $headers['http_x_hub_signature_256'] 
                   ?? $headers['x-hub-signature'] ?? $headers['http_x_hub_signature'] ?? null;

        // 2. Secret Verification
        $secret = trim($this->settingModel->get('github_webhook_secret', '') ?? '');
        if (!empty($secret)) {
            if (empty($signature)) {
                $this->logModel->record([
                    'event'       => $event,
                    'delivery_id' => $deliveryId,
                    'status'      => 'failed',
                    'output'      => 'Missing X-Hub-Signature-256 header while secret verification is required.',
                ]);

                return [
                    'success' => false,
                    'status'  => 401,
                    'message' => 'Unauthorized: Missing webhook signature.',
                ];
            }

            if (!$this->verifySignature($rawPayload, $signature, $secret)) {
                $this->logModel->record([
                    'event'       => $event,
                    'delivery_id' => $deliveryId,
                    'status'      => 'failed',
                    'output'      => 'Invalid signature verification. Secret mismatch.',
                ]);

                return [
                    'success' => false,
                    'status'  => 401,
                    'message' => 'Unauthorized: Invalid webhook signature.',
                ];
            }
        }

        // 3. Parse JSON Payload
        $data = json_decode($rawPayload, true);
        if ($rawPayload !== '' && $data === null) {
            // Check if form-urlencoded (payload={json})
            if (isset($_POST['payload'])) {
                $data = json_decode($_POST['payload'], true);
            }
        }

        // 4. Handle 'ping' event (GitHub webhook test on setup)
        if ($event === 'ping' || !empty($data['zen'])) {
            $zen = $data['zen'] ?? 'GitHub Webhook Ping Received';
            $repo = $data['repository']['full_name'] ?? ($data['repository']['name'] ?? 'Unknown');
            $sender = $data['sender']['login'] ?? 'GitHub';

            $this->logModel->record([
                'event'          => 'ping',
                'delivery_id'    => $deliveryId,
                'sender'         => $sender,
                'repository'     => $repo,
                'status'         => 'ping',
                'commit_message' => 'Zen: ' . $zen,
                'output'         => "GitHub Ping test event successfully verified!\nHook ID: " . ($data['hook_id'] ?? 'N/A'),
            ]);

            return [
                'success' => true,
                'status'  => 200,
                'message' => 'Pong! Webhook connection verified successfully.',
                'zen'     => $zen,
            ];
        }

        if (empty($data)) {
            return [
                'success' => false,
                'status'  => 400,
                'message' => 'Bad Request: Empty or invalid JSON payload.',
            ];
        }

        // 5. Repository Name Verification (if configured in settings)
        $configuredRepo = strtolower(trim($this->settingModel->get('github_repo_name', '') ?? ''));
        $payloadRepoFullName = strtolower($data['repository']['full_name'] ?? '');
        $payloadRepoName     = strtolower($data['repository']['name'] ?? '');

        if (!empty($configuredRepo)) {
            $repoMatches = ($payloadRepoFullName === $configuredRepo) 
                        || ($payloadRepoName === $configuredRepo)
                        || (str_ends_with($payloadRepoFullName, '/' . $configuredRepo));

            if (!$repoMatches) {
                $this->logModel->record([
                    'event'          => $event,
                    'delivery_id'    => $deliveryId,
                    'sender'         => $data['sender']['login'] ?? null,
                    'repository'     => $data['repository']['full_name'] ?? $data['repository']['name'] ?? null,
                    'status'         => 'ignored',
                    'output'         => "Repository mismatch. Configured: '{$configuredRepo}', Received: '{$payloadRepoFullName}'",
                ]);

                return [
                    'success' => false,
                    'status'  => 422,
                    'message' => "Repository mismatch. Expected: {$configuredRepo}",
                ];
            }
        }

        // 6. Branch Verification
        $targetBranch = trim($this->settingModel->get('github_webhook_branch', 'main') ?? 'main');
        if (empty($targetBranch)) {
            $targetBranch = 'main';
        }

        $ref = $data['ref'] ?? '';
        $branch = str_replace('refs/heads/', '', $ref);

        $sender        = $data['sender']['login'] ?? ($data['pusher']['name'] ?? 'Unknown');
        $repoName      = $data['repository']['full_name'] ?? ($data['repository']['name'] ?? 'Unknown');
        $headCommit    = $data['head_commit'] ?? ($data['commits'][0] ?? []);
        $commitId      = substr($headCommit['id'] ?? ($data['after'] ?? ''), 0, 7);
        $commitMessage = $headCommit['message'] ?? 'No commit message';
        $authorName    = $headCommit['author']['name'] ?? $sender;

        // Check if pushed branch matches target branch
        if ($branch !== $targetBranch && !empty($ref)) {
            $this->logModel->record([
                'event'          => $event,
                'delivery_id'    => $deliveryId,
                'sender'         => $sender,
                'repository'     => $repoName,
                'branch'         => $branch,
                'commit_id'      => $commitId,
                'commit_message' => $commitMessage,
                'status'         => 'ignored',
                'output'         => "Pushed to branch '{$branch}', but target deployment branch is '{$targetBranch}'. Ignored.",
            ]);

            return [
                'success' => true,
                'status'  => 200,
                'message' => "Event ignored. Push was to branch '{$branch}', but configured branch is '{$targetBranch}'.",
            ];
        }

        // 7. Auto Git Pull Execution (if enabled)
        $autoPull = ($this->settingModel->get('github_webhook_auto_pull', '1') === '1');
        $pullOutput = '';
        $deploySuccess = true;

        if ($autoPull) {
            $pullResult = $this->executeGitPull($targetBranch);
            $pullOutput = $pullResult['output'];
            $deploySuccess = $pullResult['success'];

            // Optional custom post-deploy commands
            $customCmd = trim($this->settingModel->get('github_webhook_custom_command', '') ?? '');
            if ($deploySuccess && !empty($customCmd)) {
                $cmdResult = $this->executeCustomCommand($customCmd);
                $pullOutput .= "\n\n--- Post-Deploy Output ---\n" . $cmdResult['output'];
            }
        } else {
            $pullOutput = "Auto git pull is disabled in settings. Webhook event recorded.";
        }

        // 8. Record Webhook Log & Audit Log
        $status = $deploySuccess ? 'success' : 'failed';
        $this->logModel->record([
            'event'          => $event,
            'delivery_id'    => $deliveryId,
            'sender'         => $sender,
            'repository'     => $repoName,
            'branch'         => $branch ?: $targetBranch,
            'commit_id'      => $commitId,
            'commit_message' => $commitMessage,
            'status'         => $status,
            'output'         => $pullOutput,
        ]);

        AuditLog::log(
            'webhook',
            'github',
            null,
            "GitHub Push event [{$repoName}@{$branch}]: {$commitId} by {$authorName} (" . ($deploySuccess ? 'Success' : 'Failed') . ")"
        );

        return [
            'success' => $deploySuccess,
            'status'  => $deploySuccess ? 200 : 500,
            'message' => $deploySuccess ? 'Deployment completed successfully.' : 'Deployment encountered errors.',
            'branch'  => $branch ?: $targetBranch,
            'commit'  => $commitId,
            'output'  => $pullOutput,
        ];
    }

    /**
     * Execute Git Pull command safely on the repository
     * Automatically protects local .env and database configurations from being overwritten.
     */
    public function executeGitPull(string $branch = 'main'): array
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);

        // Verify git exists
        if (!$this->isGitAvailable()) {
            return [
                'success' => false,
                'output'  => 'Git binary is not available or not in system PATH.',
            ];
        }

        // 1. Preserve local .env content before git pull
        $envFile = $basePath . '/.env';
        $envBackupContent = file_exists($envFile) ? file_get_contents($envFile) : null;

        // 2. Execute git pull
        $command = "git pull origin " . escapeshellarg($branch) . " 2>&1";
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes, $basePath);
        if (!is_resource($process)) {
            return [
                'success' => false,
                'output'  => 'Failed to open git process.',
            ];
        }

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);
        $fullOutput = trim($output . ($errors ? "\n" . $errors : ''));

        // 3. Restore original local .env to ensure server environment variables are never overwritten
        if ($envBackupContent !== null) {
            file_put_contents($envFile, $envBackupContent);
        }

        return [
            'success'  => ($exitCode === 0),
            'output'   => $fullOutput ?: 'Git pull executed (no output).',
            'exitCode' => $exitCode,
        ];
    }

    /**
     * Execute safe custom post-deploy commands
     */
    private function executeCustomCommand(string $command): array
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command . " 2>&1", $descriptors, $pipes, $basePath);
        if (!is_resource($process)) {
            return [
                'success' => false,
                'output'  => "Could not execute custom command: {$command}",
            ];
        }

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        return [
            'success' => ($exitCode === 0),
            'output'  => trim($output),
        ];
    }

    /**
     * Check if Git command is available in system
     */
    public function isGitAvailable(): bool
    {
        $isWindows = (DIRECTORY_SEPARATOR === '\\');
        $checkCmd = $isWindows ? 'where git 2>NUL' : 'which git 2>/dev/null';
        $output = @shell_exec($checkCmd);
        return !empty(trim($output ?? ''));
    }

    /**
     * Check git status and current info
     */
    public function getGitInfo(): array
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        $gitDir = $basePath . '/.git';

        $info = [
            'is_git_repo'    => is_dir($gitDir),
            'git_available'  => $this->isGitAvailable(),
            'current_branch' => '-',
            'latest_commit'  => '-',
            'commit_date'    => '-',
            'remote_url'     => '-',
        ];

        if ($info['is_git_repo'] && $info['git_available']) {
            $branch = @shell_exec("cd /d " . escapeshellarg($basePath) . " && git rev-parse --abbrev-ref HEAD 2>NUL");
            $commit = @shell_exec("cd /d " . escapeshellarg($basePath) . " && git log -1 --pretty=format:\"%h - %s (%cr)\" 2>NUL");
            $date   = @shell_exec("cd /d " . escapeshellarg($basePath) . " && git log -1 --pretty=format:\"%cd\" --date=format:\"%d/%m/%Y %H:%M\" 2>NUL");
            $remote = @shell_exec("cd /d " . escapeshellarg($basePath) . " && git config --get remote.origin.url 2>NUL");

            if ($branch) $info['current_branch'] = trim($branch);
            if ($commit) $info['latest_commit']  = trim($commit);
            if ($date)   $info['commit_date']    = trim($date);
            if ($remote) $info['remote_url']     = trim($remote);
        }

        return $info;
    }

    /**
     * Verify HMAC SHA256 / SHA1 signature
     */
    private function verifySignature(string $payload, string $signatureHeader, string $secret): bool
    {
        if (str_starts_with($signatureHeader, 'sha256=')) {
            $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
            return hash_equals($expected, $signatureHeader);
        }

        if (str_starts_with($signatureHeader, 'sha1=')) {
            $expected = 'sha1=' . hash_hmac('sha1', $payload, $secret);
            return hash_equals($expected, $signatureHeader);
        }

        // Direct compare if raw
        $expected256 = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected256, $signatureHeader);
    }
}
