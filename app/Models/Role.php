<?php

namespace App\Models;

use App\Core\Database;

/**
 * Role Model
 */
class Role
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all roles
     */
    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM roles ORDER BY id ASC");
    }

    /**
     * Find role by ID
     */
    public function find(int $id): object|false
    {
        return $this->db->fetch("SELECT * FROM roles WHERE id = ?", [$id]);
    }

    /**
     * Find role by name
     */
    public function findByName(string $name): object|false
    {
        return $this->db->fetch("SELECT * FROM roles WHERE name = ?", [$name]);
    }

    /**
     * Count users in a role
     */
    public function countUsers(int $roleId): int
    {
        return (int) Database::getInstance()->fetchColumn(
            "SELECT COUNT(*) FROM users WHERE role_id = ?",
            [$roleId]
        );
    }
}
