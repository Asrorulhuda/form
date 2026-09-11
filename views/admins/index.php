<?php 
use App\Core\CSRF;
use App\Core\View; 
?>

<div class="bento-grid">
    <!-- 1. Header Bento Hero Card -->
    <div class="bento-col-12 bento-hero fade-in" style="background: #ffffff; border: 1px solid var(--border-subtle);">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: var(--primary-50); color: var(--primary-700); border: 1px solid rgba(79,70,229,0.3);">
                👥
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-primary);">
                        Kelola Admin
                    </h2>
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge badge-warning" style="font-size: 11px; font-weight: 700;">
                            <?= $pendingCount ?> Menunggu Approval
                        </span>
                    <?php endif; ?>
                </div>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Kelola semua akun Admin (tenant owner). Setiap Admin memiliki form, template, dokumen, dan user sendiri.
                </div>
            </div>
        </div>
        <a href="<?= url('admins/create') ?>" class="btn btn-primary" style="white-space: nowrap;">
            + Tambah Admin
        </a>
    </div>

    <!-- 2. Tab Switcher -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 16px 20px;">
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <a href="<?= url('admins?tab=active') ?>" 
               class="btn <?= ($tab ?? 'active') === 'active' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                Admin Aktif
            </a>
            <a href="<?= url('admins?tab=pending') ?>" 
               class="btn <?= ($tab ?? '') === 'pending' ? 'btn-warning' : 'btn-secondary' ?> btn-sm">
                Pendaftar Baru
                <?php if ($pendingCount > 0): ?>
                    <span style="background: white; color: var(--warning-700); padding: 1px 6px; border-radius: 99px; font-size: 10px; font-weight: 800; margin-left: 4px;"><?= $pendingCount ?></span>
                <?php endif; ?>
            </a>

            <form method="GET" action="<?= url('admins') ?>" style="margin-left: auto; display: flex; gap: 8px;">
                <input type="hidden" name="tab" value="<?= e($tab ?? 'active') ?>">
                <div class="search-input-wrapper" style="max-width: 320px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari admin..." 
                           value="<?= e($filters['search'] ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
            </form>
        </div>
    </div>

    <!-- 3. Table -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="margin: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Kontak</th>
                        <th>Paket</th>
                        <th>User / Form</th>
                        <th>WA Config</th>
                        <th>Status</th>
                        <th style="text-align: right; min-width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($admins)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <?= ($tab ?? 'active') === 'pending' ? 'Tidak ada pendaftar baru.' : 'Belum ada admin.' ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-primary);"><?= e($admin->name) ?></div>
                                    <div style="font-size: 12px; color: var(--text-muted);"><?= e($admin->email) ?></div>
                                </td>
                                <td>
                                    <div style="font-size: 13px;"><?= e($admin->phone ?? '-') ?></div>
                                </td>
                                <td>
                                    <span class="badge" style="font-weight: 600;"><?= e($admin->plan ?? 'Gratis') ?></span>
                                </td>
                                <td>
                                    <?php if (isset($admin->user_count)): ?>
                                        <div style="font-size: 13px;">👤 <?= $admin->user_count ?> user</div>
                                        <div style="font-size: 13px;">📝 <?= $admin->form_count ?> form</div>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($admin->wa_sender)): ?>
                                        <span style="font-size: 12px; color: var(--success-600);">✓ Configured</span>
                                    <?php else: ?>
                                        <span style="font-size: 12px; color: var(--text-muted);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusColors = [
                                        'active' => 'badge-success',
                                        'pending' => 'badge-warning',
                                        'inactive' => 'badge-secondary',
                                        'rejected' => 'badge-danger',
                                    ];
                                    $statusLabels = [
                                        'active' => 'Aktif',
                                        'pending' => 'Menunggu',
                                        'inactive' => 'Nonaktif',
                                        'rejected' => 'Ditolak',
                                    ];
                                    ?>
                                    <span class="badge <?= $statusColors[$admin->status] ?? 'badge-secondary' ?>">
                                        <?= $statusLabels[$admin->status] ?? ucfirst($admin->status) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($admin->status === 'pending'): ?>
                                        <form method="POST" action="<?= url("admins/{$admin->id}/approve") ?>" style="display: inline;">
                                            <?= CSRF::field() ?>
                                            <button type="submit" class="btn btn-success btn-sm" style="font-weight: 600;">✓ ACC</button>
                                        </form>
                                        <form method="POST" action="<?= url("admins/{$admin->id}/reject") ?>" style="display: inline; margin-left: 4px;">
                                            <?= CSRF::field() ?>
                                            <button type="submit" class="btn btn-danger btn-sm" style="font-weight: 600;">✗ Tolak</button>
                                        </form>
                                    <?php else: ?>
                                        <a href="<?= url("admins/{$admin->id}/edit") ?>" class="btn btn-secondary btn-sm" style="font-weight: 600;">Edit</a>
                                        <?php if ((int)$admin->role_id !== 1): ?>
                                            <?php if ($admin->status === 'active'): ?>
                                                <form method="POST" action="<?= url("admins/{$admin->id}/impersonate") ?>" style="display: inline; margin-left: 4px;">
                                                    <?= CSRF::field() ?>
                                                    <button type="submit" class="btn btn-sm" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-weight: 700; border-radius: 6px; padding: 5px 9px;" title="Masuk &amp; kelola data sebagai Admin ini">
                                                        🔑 Login Admin
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" action="<?= url("admins/{$admin->id}/delete") ?>" style="display: inline; margin-left: 4px;"
                                                  onsubmit="return confirm('Hapus admin ini beserta semua datanya?')">
                                                <?= CSRF::field() ?>
                                                <button type="submit" class="btn btn-danger btn-sm" style="font-weight: 600;">Hapus</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Pagination -->
    <?php if ($lastPage > 1): ?>
        <div class="bento-col-12" style="display: flex; justify-content: center;">
            <?php View::component('pagination', [
                'page' => $page, 
                'lastPage' => $lastPage, 
                'baseUrl' => url('admins') . '?tab=' . e($tab ?? 'active') . '&search=' . urlencode($filters['search'] ?? '')
            ]); ?>
        </div>
    <?php endif; ?>
</div>
