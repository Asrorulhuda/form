<?php

namespace App\Models;

use App\Core\Database;

/**
 * Payment Model
 * Manages payments and proof verification records.
 */
class Payment
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all payments with user info (paginated)
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND p.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where .= " AND (u.name LIKE ? OR u.email LIKE ? OR p.sender_name LIKE ? OR p.sender_phone LIKE ?)";
            $s = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$s, $s, $s, $s]);
        }

        if (!empty($filters['method'])) {
            $where .= " AND p.payment_method = ?";
            $params[] = $filters['method'];
        }

        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM payments p JOIN users u ON p.user_id = u.id WHERE {$where}",
            $params
        );

        $offset = ($page - 1) * $perPage;
        $items = $this->db->fetchAll(
            "SELECT p.*, u.name as user_name, u.email as user_email, u.plan as user_plan, v.name as verifier_name
             FROM payments p 
             JOIN users u ON p.user_id = u.id 
             LEFT JOIN users v ON p.verified_by = v.id
             WHERE {$where} 
             ORDER BY p.created_at DESC 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        ) ?: [];

        return [
            'data'        => $items,
            'total'       => $total,
            'page'        => $page,
            'perPage'     => $perPage,
            'lastPage'    => (int) ceil($total / $perPage) ?: 1,
        ];
    }

    /**
     * Count pending payments for sidebar badge
     */
    public function countPending(): int
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM payments WHERE status = 'pending'") ?: 0;
    }

    /**
     * Find payment by ID with user info
     */
    public function find(int $id): object|false
    {
        return $this->db->fetch(
            "SELECT p.*, u.name as user_name, u.email as user_email, u.plan as user_plan, u.status as user_status
             FROM payments p 
             JOIN users u ON p.user_id = u.id 
             WHERE p.id = ?",
            [$id]
        );
    }

    /**
     * Find latest payment by User ID
     */
    public function findByUser(int $userId): object|false
    {
        return $this->db->fetch(
            "SELECT * FROM payments WHERE user_id = ? ORDER BY id DESC LIMIT 1",
            [$userId]
        );
    }

    /**
     * Create payment record
     */
    public function create(array $data): string|false
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('payments', $data);
    }

    /**
     * Verify payment (ACC)
     */
    public function verify(int $id, int $verifierId): bool
    {
        $payment = $this->find($id);
        if (!$payment) return false;

        $this->db->update('payments', [
            'status'      => 'verified',
            'verified_by' => $verifierId,
            'verified_at' => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);

        // Activate user account and update user plan
        $this->db->update('users', [
            'status'     => 'active',
            'plan'       => $payment->plan,
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$payment->user_id]);

        return true;
    }

    /**
     * Reject payment
     */
    public function reject(int $id, int $verifierId, ?string $notes = null): bool
    {
        $this->db->update('payments', [
            'status'      => 'rejected',
            'verified_by' => $verifierId,
            'notes'       => $notes,
            'verified_at' => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);

        return true;
    }
}
