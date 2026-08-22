<?php use App\Core\View; ?>

<div class="bento-grid">
    <!-- 1. Header Bento Hero Card -->
    <div class="bento-col-12 bento-hero fade-in" style="background: #ffffff; border: 1px solid var(--border-subtle);">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;">
                🛡️
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-primary);">
                        Audit Trail &amp; Log Keamanan Sistem
                    </h2>
                    <span class="badge badge-primary" style="font-size: 11px; font-weight: 700;">
                        Total: <?= number_format($total) ?> Entri Log
                    </span>
                </div>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Rekam jejak setiap aktivitas login, perubahan formulir, dokumen terbit, dan konfigurasi keamanan.
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Bento Filter Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 16px 20px;">
        <form method="GET" action="<?= url('audit-log') ?>" class="filter-bar" style="margin: 0; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div class="search-input-wrapper" style="flex: 1; min-width: 220px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari kata kunci aktivitas..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <select name="module" class="form-control" style="width: auto; min-width: 140px;">
                <option value="">Semua Modul</option>
                <option value="auth" <?= ($filters['module'] ?? '') === 'auth' ? 'selected' : '' ?>>Auth</option>
                <option value="users" <?= ($filters['module'] ?? '') === 'users' ? 'selected' : '' ?>>Users</option>
                <option value="forms" <?= ($filters['module'] ?? '') === 'forms' ? 'selected' : '' ?>>Forms</option>
                <option value="documents" <?= ($filters['module'] ?? '') === 'documents' ? 'selected' : '' ?>>Documents</option>
                <option value="settings" <?= ($filters['module'] ?? '') === 'settings' ? 'selected' : '' ?>>Settings</option>
            </select>
            <select name="action" class="form-control" style="width: auto; min-width: 130px;">
                <option value="">Semua Aksi</option>
                <option value="login" <?= ($filters['action'] ?? '') === 'login' ? 'selected' : '' ?>>Login</option>
                <option value="logout" <?= ($filters['action'] ?? '') === 'logout' ? 'selected' : '' ?>>Logout</option>
                <option value="create" <?= ($filters['action'] ?? '') === 'create' ? 'selected' : '' ?>>Create</option>
                <option value="update" <?= ($filters['action'] ?? '') === 'update' ? 'selected' : '' ?>>Update</option>
                <option value="delete" <?= ($filters['action'] ?? '') === 'delete' ? 'selected' : '' ?>>Delete</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;">Filter Log</button>
            <?php if (!empty($filters['search']) || !empty($filters['module']) || !empty($filters['action'])): ?>
                <a href="<?= url('audit-log') ?>" class="btn btn-ghost btn-sm" style="color: var(--text-muted);">&times; Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- 3. Bento Table Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="margin: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 150px;">Waktu &amp; Tanggal</th>
                        <th>Pengguna / Aktor</th>
                        <th>Aksi</th>
                        <th>Modul</th>
                        <th>Deskripsi Aktivitas</th>
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
                                <td style="white-space: nowrap; font-size: 13px;">
                                    <?= date('d/m/Y H:i', strtotime($log->created_at)) ?>
                                </td>
                                <td><strong style="color: var(--text-primary); font-size: 13.5px;"><?= e($log->user_name ?? 'System') ?></strong></td>
                                <td><span class="badge <?= $badgeClass ?>" style="font-weight: 700; font-size: 10.5px;"><?= e(strtoupper($log->action)) ?></span></td>
                                <td><span class="badge badge-muted" style="font-size: 11px;"><?= e($log->module) ?></span></td>
                                <td class="truncate" style="max-width: 320px; font-size: 13px; color: var(--text-secondary);"><?= e($log->description) ?></td>
                                <td style="font-family: monospace; font-size: 12px; color: var(--text-muted);"><?= e($log->ip_address) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state" style="padding: 48px 20px; text-align: center;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 12px;">
                                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                    </svg>
                                    <p class="empty-state-title" style="font-size: 16px; font-weight: 800; margin-bottom: 4px;">Tidak Ada Log Tercatat</p>
                                    <p class="empty-state-desc" style="font-size: 13px; color: var(--text-muted);">Belum ada aktivitas yang sesuai dengan filter pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($logs)): ?>
            <div style="padding: 12px 20px; border-top: 1px solid var(--border-subtle);">
                <?php View::component('pagination', [
                    'page'     => $page,
                    'lastPage' => $lastPage,
                    'total'    => $total,
                    'baseUrl'  => 'audit-log',
                    'perPage'  => 20,
                ]); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
