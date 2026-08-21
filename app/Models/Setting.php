<?php

namespace App\Models;

use App\Core\Database;

/**
 * Setting Model
 * Key-value settings stored in database.
 */
class Setting
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get a setting value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $row = $this->db->fetch("SELECT value FROM settings WHERE `key` = ?", [$key]);
        return $row ? $row->value : $default;
    }

    /**
     * Set a setting value
     */
    public function set(string $key, string $value): void
    {
        $exists = $this->db->fetchColumn("SELECT COUNT(*) FROM settings WHERE `key` = ?", [$key]);
        
        if ($exists) {
            $this->db->update('settings', ['value' => $value, 'updated_at' => date('Y-m-d H:i:s')], '`key` = ?', [$key]);
        } else {
            $this->db->insert('settings', [
                'key'        => $key,
                'value'      => $value,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Get all settings
     */
    public function getAll(): array
    {
        $rows = $this->db->fetchAll("SELECT * FROM settings ORDER BY `key` ASC");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row->key] = $row->value;
        }
        return $settings;
    }

    /**
     * Get settings by group prefix
     */
    public function getGroup(string $prefix): array
    {
        $rows = $this->db->fetchAll("SELECT * FROM settings WHERE `key` LIKE ? ORDER BY `key`", [$prefix . '%']);
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row->key] = $row->value;
        }
        return $settings;
    }

    /**
     * Bulk update settings
     */
    public function bulkUpdate(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value ?? '');
        }
    }
}
