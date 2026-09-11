<?php

namespace App\Models;

use App\Core\Database;

/**
 * Admin Model — Tenant Owner
 * Manages admin accounts (Super Admin + Admin) in the `admins` table.
 */
class Admin
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all admins with optional filters and pagination
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $where = "a.role_id = 2"; // Only regular Admins, not Super Admin
        $params = [];

        if (!empty($filters['include_super'])) {
            $where = "1=1";
        }

        if (!empty($filters['search'])) {
            $where .= " AND (a.name LIKE ? OR a.email LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['status'])) {
            $where .= " AND a.status = ?";
            $params[] = $filters['status'];
        }

        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM admins a WHERE {$where}",
            $params
        );

        $offset = ($page - 1) * $perPage;
        $admins = $this->db->fetchAll(
            "SELECT a.*, r.name as role_name,
                    (SELECT COUNT(*) FROM users u WHERE u.admin_id = a.id) as user_count,
                    (SELECT COUNT(*) FROM forms f WHERE f.admin_id = a.id) as form_count
             FROM admins a 
             JOIN roles r ON a.role_id = r.id 
             WHERE {$where} 
             ORDER BY a.created_at DESC 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data'     => $admins,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => max(1, ceil($total / $perPage)),
        ];
    }

    /**
     * Get all pending admin applicants
     */
    public function getPendingApplicants(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $where = "a.status = 'pending' AND a.role_id = 2";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (a.name LIKE ? OR a.email LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM admins a WHERE {$where}",
            $params
        );

        $offset = ($page - 1) * $perPage;
        $applicants = $this->db->fetchAll(
            "SELECT a.*, r.name as role_name 
             FROM admins a 
             JOIN roles r ON a.role_id = r.id 
             WHERE {$where} 
             ORDER BY a.created_at DESC 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data'     => $applicants,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => max(1, ceil($total / $perPage)),
        ];
    }

    /**
     * Count pending admin applicants
     */
    public function countPending(): int
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM admins WHERE status = 'pending' AND role_id = 2");
    }

    /**
     * Find admin by ID
     */
    public function find(int $id): object|false
    {
        return $this->db->fetch(
            "SELECT a.*, r.name as role_name 
             FROM admins a 
             JOIN roles r ON a.role_id = r.id 
             WHERE a.id = ?",
            [$id]
        );
    }

    /**
     * Find admin by email
     */
    public function findByEmail(string $email): object|false
    {
        return $this->db->fetch("SELECT * FROM admins WHERE email = ?", [$email]);
    }

    /**
     * Create a new admin
     */
    public function create(array $data): string|false
    {
        $data['password']   = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->insert('admins', $data);
    }

    /**
     * Update an admin
     */
    public function update(int $id, array $data): int
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->update('admins', $data, 'id = ?', [$id]);
    }

    /**
     * Approve an admin applicant
     */
    public function approve(int $id): int
    {
        return $this->db->update('admins', [
            'status'     => 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);
    }

    /**
     * Reject an admin applicant
     */
    public function reject(int $id): int
    {
        return $this->db->update('admins', [
            'status'     => 'rejected',
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);
    }

    /**
     * Delete an admin
     */
    public function delete(int $id): int
    {
        return $this->db->delete('admins', 'id = ?', [$id]);
    }

    /**
     * Count admins
     */
    public function count(string $where = '1=1', array $params = []): int
    {
        return $this->db->count('admins', $where, $params);
    }

    /**
     * Get WA sender for a specific admin
     */
    public function getWaSender(int $adminId): string
    {
        $admin = $this->find($adminId);
        return $admin ? trim($admin->wa_sender ?? '') : '';
    }

    /**
     * Get WA API key for a specific admin
     */
    public function getWaApiKey(int $adminId): string
    {
        $admin = $this->find($adminId);
        return $admin ? trim($admin->wa_api_key ?? '') : '';
    }
}
