<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Auth;

/**
 * Audit Log Model
 */
class AuditLog
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Log an activity
     */
    public static function log(string $action, string $module, ?int $recordId = null, string $description = ''): void
    {
        $db = Database::getInstance();
        $db->insert('audit_logs', [
            'user_id'     => Auth::id(),
            'action'      => $action,
            'module'      => $module,
            'record_id'   => $recordId,
            'description' => $description,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get all logs with pagination & filters
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = '1=1';
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (a.description LIKE ? OR a.action LIKE ? OR a.module LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$search, $search, $search]);
        }

        if (!empty($filters['module'])) {
            $where .= " AND a.module = ?";
            $params[] = $filters['module'];
        }

        if (!empty($filters['action'])) {
            $where .= " AND a.action = ?";
            $params[] = $filters['action'];
        }

        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM audit_logs a WHERE {$where}",
            $params
        );

        $offset = ($page - 1) * $perPage;
        $logs = $this->db->fetchAll(
            "SELECT a.*, u.name as user_name 
             FROM audit_logs a 
             LEFT JOIN users u ON a.user_id = u.id 
             WHERE {$where} 
             ORDER BY a.created_at DESC 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data'     => $logs,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => max(1, ceil($total / $perPage)),
        ];
    }

    /**
     * Get recent logs (for dashboard)
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT a.*, u.name as user_name 
             FROM audit_logs a 
             LEFT JOIN users u ON a.user_id = u.id 
             ORDER BY a.created_at DESC 
             LIMIT ?",
            [$limit]
        );
    }
}
