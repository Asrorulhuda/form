<?php

namespace App\Models;

use App\Core\Database;

/**
 * AdSlot Model
 * Manages ad placement slots for AdSense integration.
 */
class AdSlot
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all ad slots ordered by position
     */
    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM ad_slots ORDER BY position ASC") ?: [];
    }

    /**
     * Get ad slot by key
     */
    public function getByKey(string $key): ?object
    {
        return $this->db->fetch("SELECT * FROM ad_slots WHERE slot_key = ?", [$key]);
    }

    /**
     * Check if a slot is enabled
     */
    public function isEnabled(string $key): bool
    {
        $slot = $this->getByKey($key);
        return $slot && (int) $slot->enabled === 1;
    }

    /**
     * Toggle slot enabled/disabled
     */
    public function toggleEnabled(int $id): void
    {
        $this->db->query(
            "UPDATE ad_slots SET enabled = NOT enabled, updated_at = NOW() WHERE id = ?",
            [$id]
        );
    }

    /**
     * Update ad slot
     */
    public function update(int $id, array $data): void
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->update('ad_slots', $data, 'id = ?', [$id]);
    }

    /**
     * Get ad slot by ID
     */
    public function getById(int $id): ?object
    {
        return $this->db->fetch("SELECT * FROM ad_slots WHERE id = ?", [$id]);
    }
}
