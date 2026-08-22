<?php
use App\Models\User;
use App\Core\View;
use App\Core\CSRF;
$pendingApplicantsCount = (new User())->countPending();
?>

<div class="bento-grid">
    <!-- 1. Header Bento Hero Card -->
    <div class="bento-col-12 bento-hero fade-in" style="background: #ffffff; border: 1px solid var(--border-subtle);">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff;">
                👥
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-primary);">
                        Manajemen Pengguna &amp; Instansi
                    </h2>
                    <span class="badge badge-primary" style="font-size: 11px; font-weight: 700;">
                        Total: <?= number_format($total) ?> Pengguna
                    </span>
                </div>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Kelola hak akses role, status keanggotaan akun instansi, dan paket lisensi berlangganan.
                </div>
            </div>
        </div>
        <div class="bento-hero-actions">
            <a href="<?= url('users/create') ?>" class="btn btn-primary btn-sm" style="box-shadow: 0 4px 12px rgba(79,70,229,0.25); font-weight: 600;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Pengguna Baru
            </a>
        </div>
    </div>

    <!-- Alert Pending Approval Bento Card (if any) -->
    <?php if ($pendingApplicantsCount > 0): ?>
        <div class="bento-col-12 bento-card fade-in" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; padding: 14px 20px;">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-3">
                    <span style="font-size: 24px;">⏳</span>
                    <div>
                        <strong style="color: #92400e; font-size: 14px; font-weight: 800;">Ada <?= $pendingApplicantsCount ?> Pendaftar Baru Menunggu Persetujuan (ACC)</strong>
                        <p style="font-size: 12px; color: #b45309; margin: 2px 0 0;">Pendaftar akun baru memerlukan persetujuan administrator sebelum dapat mengakses panel.</p>
                    </div>
                </div>
                <a href="<?= url('applicants') ?>" class="btn btn-sm" style="background: #f59e0b; color: white; font-weight: 800; border-radius: 8px;">
                    Buka Menu Pendaftar &rarr;
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- 2. Bento Filter Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 16px 20px;">
        <form method="GET" action="<?= url('users') ?>" class="filter-bar" style="margin: 0; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div class="search-input-wrapper" style="flex: 1; min-width: 220px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari nama, email, atau kontak..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <select name="role_id" class="form-control" style="width: auto; min-width: 140px;">
                <option value="">Semua Role</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role->id ?>" <?= ($filters['role_id'] ?? '') == $role->id ? 'selected' : '' ?>>
                        <?= e($role->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="form-control" style="width: auto; min-width: 130px;">
                <option value="">Semua Status</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktif</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;">Filter Pengguna</button>
            <?php if (!empty($filters['search']) || !empty($filters['role_id']) || !empty($filters['status'])): ?>
                <a href="<?= url('users') ?>" class="btn btn-ghost btn-sm" style="color: var(--text-muted);">&times; Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- 3. Bento Table Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="margin: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Email &amp; WhatsApp</th>
                        <th>Peran (Role)</th>
                        <th>Paket Langganan</th>
                        <th>Status Akun</th>
                        <th>Terdaftar</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="user-avatar" style="width:34px;height:34px;font-size:13px;border-radius:10px;background: linear-gradient(135deg, #4f46e5, #312e81); color: white; font-weight: 800;">
                                            <?= strtoupper(substr($u->name, 0, 1)) ?>
                                        </div>
                                        <strong style="color: var(--text-primary); font-size: 13.5px;"><?= e($u->name) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 13px; color: var(--text-primary);"><?= e($u->email) ?></div>
                                    <?php if (!empty($u->phone)): ?>
                                        <div style="margin-top: 2px;">
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $u->phone) ?>" target="_blank" style="color: #16a34a; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 3px; text-decoration: none;" title="Chat WhatsApp">
                                                <span>💬</span> <?= e($u->phone) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-primary" style="font-weight: 600;"><?= e($u->role_name) ?></span></td>
                                <td>
                                    <?php 
                                        $pName = $u->plan ?? 'Gratis';
                                        $pBadge = match(strtolower($pName)) {
                                            'pro' => 'badge-primary',
                                            'enterprise' => 'badge-warning',
                                            default => 'badge-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $pBadge ?>" style="font-weight: 700;">
                                        <?= e($pName) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($u->status === 'active'): ?>
                                        <span class="badge badge-success" style="font-weight: 700;">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-muted">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="text-sm text-muted"><?= date('d/m/Y', strtotime($u->created_at)) ?></span></td>
                                <td style="text-align: right;">
                                    <div class="flex justify-end gap-2">
                                        <a href="<?= url("users/{$u->id}/edit") ?>" class="btn btn-secondary btn-sm" style="font-size: 12px;" title="Edit Pengguna">
                                            ✏️ Edit
                                        </a>
                                        <form method="POST" action="<?= url("users/{$u->id}/delete") ?>" style="display:inline;">
                                            <?= \App\Core\CSRF::field() ?>
                                            <button type="button" class="btn btn-danger btn-sm" data-confirm="Yakin ingin menghapus pengguna <?= e($u->name) ?>?" style="padding: 4px 8px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state" style="padding: 48px 20px; text-align: center;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 12px;">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                    </svg>
                                    <p class="empty-state-title" style="font-size: 16px; font-weight: 800; margin-bottom: 4px;">Tidak Ada Pengguna Ditemukan</p>
                                    <p class="empty-state-desc" style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">Tambahkan pengguna atau ubah kriteria filter pencarian.</p>
                                    <a href="<?= url('users/create') ?>" class="btn btn-primary btn-sm">Tambah Pengguna Baru</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($users)): ?>
            <div style="padding: 12px 20px; border-top: 1px solid var(--border-subtle);">
                <?php View::component('pagination', [
                    'page'     => $page,
                    'lastPage' => $lastPage,
                    'total'    => $total,
                    'baseUrl'  => 'users',
                ]); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
