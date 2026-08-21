<?php

namespace App\Models;

use App\Core\Database;

/**
 * WebhookLog Model
 * Records and retrieves GitHub Webhook delivery logs.
 */
class WebhookLog
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureTable();
    }

    /**
     * Ensure table exists in case migration wasn't run manually
     */
    private function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `webhook_logs` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `event` VARCHAR(50) NOT NULL DEFAULT 'push',
            `delivery_id` VARCHAR(100) DEFAULT NULL,
            `sender` VARCHAR(100) DEFAULT NULL,
            `repository` VARCHAR(150) DEFAULT NULL,
            `branch` VARCHAR(100) DEFAULT NULL,
            `commit_id` VARCHAR(50) DEFAULT NULL,
            `commit_message` TEXT DEFAULT NULL,
            `status` ENUM('success', 'failed', 'ignored', 'ping') DEFAULT 'success',
            `output` LONGTEXT DEFAULT NULL,
            `ip_address` VARCHAR(50) DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        try {
            $this->db->getPdo()->exec($sql);
        } catch (\Throwable $e) {
            // Ignore if already exists
        }
    }

    /**
     * Record a new webhook event log
     */
    public function record(array $data): int|string
    {
        return $this->db->insert('webhook_logs', [
            'event'          => $data['event'] ?? 'push',
            'delivery_id'    => $data['delivery_id'] ?? null,
            'sender'         => $data['sender'] ?? null,
            'repository'     => $data['repository'] ?? null,
            'branch'         => $data['branch'] ?? null,
            'commit_id'      => $data['commit_id'] ?? null,
            'commit_message' => $data['commit_message'] ?? null,
            'status'         => $data['status'] ?? 'success',
            'output'         => $data['output'] ?? null,
            'ip_address'     => $data['ip_address'] ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'),
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get paginated logs with optional status/search filters
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $where = '1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND `status` = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where .= " AND (`commit_message` LIKE ? OR `sender` LIKE ? OR `commit_id` LIKE ? OR `repository` LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$search, $search, $search, $search]);
        }

        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM `webhook_logs` WHERE {$where}",
            $params
        );

        $offset = ($page - 1) * $perPage;
        $logs = $this->db->fetchAll(
            "SELECT * FROM `webhook_logs` WHERE {$where} ORDER BY `created_at` DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data'     => $logs,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    /**
     * Get recent logs (e.g. for preview)
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `webhook_logs` ORDER BY `created_at` DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Clear all logs
     */
    public function clearAll(): int
    {
        return $this->db->delete('webhook_logs', '1=1');
    }

    /**
     * Count total successful deploys
     */
    public function countByStatus(string $status): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM `webhook_logs` WHERE `status` = ?",
            [$status]
        );
    }
}
