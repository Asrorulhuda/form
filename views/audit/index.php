<?php use App\Core\View; ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body" style="padding: 16px 24px;">
        <form method="GET" action="<?= url('audit-log') ?>" class="filter-bar">
            <div class="search-input-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari aktivitas..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <select name="module" class="form-control">
                <option value="">Semua Modul</option>
                <option value="auth" <?= ($filters['module'] ?? '') === 'auth' ? 'selected' : '' ?>>Auth</option>
                <option value="users" <?= ($filters['module'] ?? '') === 'users' ? 'selected' : '' ?>>Users</option>
                <option value="forms" <?= ($filters['module'] ?? '') === 'forms' ? 'selected' : '' ?>>Forms</option>
                <option value="documents" <?= ($filters['module'] ?? '') === 'documents' ? 'selected' : '' ?>>Documents</option>
                <option value="settings" <?= ($filters['module'] ?? '') === 'settings' ? 'selected' : '' ?>>Settings</option>
            </select>
            <select name="action" class="form-control">
                <option value="">Semua Aksi</option>
                <option value="login" <?= ($filters['action'] ?? '') === 'login' ? 'selected' : '' ?>>Login</option>
                <option value="logout" <?= ($filters['action'] ?? '') === 'logout' ? 'selected' : '' ?>>Logout</option>
                <option value="create" <?= ($filters['action'] ?? '') === 'create' ? 'selected' : '' ?>>Create</option>
                <option value="update" <?= ($filters['action'] ?? '') === 'update' ? 'selected' : '' ?>>Update</option>
                <option value="delete" <?= ($filters['action'] ?? '') === 'delete' ? 'selected' : '' ?>>Delete</option>
            </select>
            <button type="submit" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                Filter
            </button>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Pengguna</th>
                    <th>Aksi</th>
                    <th>Modul</th>
                    <th>Deskripsi</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <?php
                        $badgeClass = match($log->action) {
                            'create' => 'badge-success',
                            'update' => 'badge-info',
                            'delete' => 'badge-danger',
                            'login'  => 'badge-warning',
                            'logout' => 'badge-muted',
                            default  => 'badge-primary',
                        };
                        ?>
                        <tr>
                            <td style="white-space: nowrap;"><?= date('d/m/Y H:i', strtotime($log->created_at)) ?></td>
                            <td><strong style="color: var(--text-primary);"><?= e($log->user_name ?? 'System') ?></strong></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= e(strtoupper($log->action)) ?></span></td>
                            <td><span class="badge badge-muted"><?= e($log->module) ?></span></td>
                            <td class="truncate" style="max-width: 300px;"><?= e($log->description) ?></td>
                            <td style="font-family: monospace; font-size: 12px; color: var(--text-tertiary);"><?= e($log->ip_address) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                </svg>
                                <p class="empty-state-title">Tidak ada log</p>
                                <p class="empty-state-desc">Belum ada aktivitas yang tercatat.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        <?php View::component('pagination', [
            'page'     => $page,
            'lastPage' => $lastPage,
            'total'    => $total,
            'baseUrl'  => 'audit-log',
            'perPage'  => 20,
        ]); ?>
    </div>
</div>
