<?php

namespace App\Models;

use App\Core\Database;

/**
 * User Model
 */
class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all approved users (active/inactive) with role info and optional filters
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $where = "u.status != 'pending'";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['role_id'])) {
            $where .= " AND u.role_id = ?";
            $params[] = $filters['role_id'];
        }

        if (!empty($filters['status'])) {
            $where .= " AND u.status = ?";
            $params[] = $filters['status'];
        }

        // Count total
        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM users u WHERE {$where}",
            $params
        );

        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $users = $this->db->fetchAll(
            "SELECT u.*, r.name as role_name 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE {$where} 
             ORDER BY u.created_at DESC 
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data'     => $users,
            'total'    => $total,
            'page'     => $page,
            'perPage'  => $perPage,
            'lastPage' => max(1, ceil($total / $perPage)),
        ];
    }

    /**
     * Get all pending applicants awaiting approval
     */
    public function getPendingApplicants(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $where = "u.status = 'pending'";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM users u WHERE {$where}",
            $params
        );

        $offset = ($page - 1) * $perPage;
        $applicants = $this->db->fetchAll(
            "SELECT u.*, r.name as role_name 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE {$where} 
             ORDER BY u.created_at DESC 
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
     * Count pending applicants
     */
    public function countPending(): int
    {
        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE status = 'pending'");
    }

    /**
     * Approve an applicant (ACC)
     */
    public function approve(int $id, ?int $roleId = null): int
    {
        $data = [
            'status'     => 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($roleId) {
            $data['role_id'] = $roleId;
        }
        return $this->db->update('users', $data, 'id = ?', [$id]);
    }

    /**
     * Reject an applicant
     */
    public function reject(int $id): int
    {
        return $this->db->update('users', [
            'status'     => 'rejected',
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);
    }

    /**
     * Find user by ID
     */
    public function find(int $id): object|false
    {
        return $this->db->fetch(
            "SELECT u.*, r.name as role_name 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.id = ?",
            [$id]
        );
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): object|false
    {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    /**
     * Create a new user
     */
    public function create(array $data): string|false
    {
        $data['password']   = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->insert('users', $data);
    }

    /**
     * Update a user
     */
    public function update(int $id, array $data): int
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->update('users', $data, 'id = ?', [$id]);
    }

    /**
     * Delete a user
     */
    public function delete(int $id): int
    {
        return $this->db->delete('users', 'id = ?', [$id]);
    }

    /**
     * Count users
     */
    public function count(string $where = '1=1', array $params = []): int
    {
        return $this->db->count('users', $where, $params);
    }
}
