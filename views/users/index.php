<?php
use App\Models\User;
use App\Core\View;
use App\Core\CSRF;
$pendingApplicantsCount = (new User())->countPending();
?>

<!-- Header -->
<div class="flex items-center justify-between mb-4">
    <div>
        <p class="text-sm text-muted">Total: <?= number_format($total) ?> pengguna aktif & nonaktif</p>
    </div>
    <a href="<?= url('users/create') ?>" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah Pengguna
    </a>
</div>

<?php if ($pendingApplicantsCount > 0): ?>
    <div class="card mb-4" style="background: var(--warning-50); border-color: rgba(245, 158, 11, 0.3);">
        <div class="card-body" style="padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div class="flex items-center gap-3">
                <span style="font-size: 20px;">⏳</span>
                <div>
                    <strong style="color: var(--warning-text); font-size: 14px;">Ada <?= $pendingApplicantsCount ?> Pendaftar Baru Menunggu Persetujuan</strong>
                    <p class="text-sm text-muted" style="margin: 0;">Pendaftar akun baru tidak dimasukkan ke daftar ini sebelum di-ACC.</p>
                </div>
            </div>
            <a href="<?= url('applicants') ?>" class="btn btn-sm" style="background: var(--warning-500); color: white; font-weight: 700;">
                Buka Menu Pendaftar (ACC) &rarr;
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body" style="padding: 16px 24px;">
        <form method="GET" action="<?= url('users') ?>" class="filter-bar">
            <div class="search-input-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari nama atau email..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <select name="role_id" class="form-control">
                <option value="">Semua Role</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role->id ?>" <?= ($filters['role_id'] ?? '') == $role->id ? 'selected' : '' ?>>
                        <?= e($role->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktif</option>
                <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
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
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Paket</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="user-avatar" style="width:34px;height:34px;font-size:13px;border-radius:8px;">
                                        <?= strtoupper(substr($u->name, 0, 1)) ?>
                                    </div>
                                    <strong style="color: var(--text-primary);"><?= e($u->name) ?></strong>
                                </div>
                            </td>
                            <td>
                                <div><?= e($u->email) ?></div>
                                <?php if (!empty($u->phone)): ?>
                                    <div style="margin-top: 2px;">
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $u->phone) ?>" target="_blank" style="color: #16a34a; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 3px;" title="Chat WhatsApp">
                                            <span>💬</span> <?= e($u->phone) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-primary"><?= e($u->role_name) ?></span></td>
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
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-muted">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($u->created_at)) ?></td>
                            <td style="text-align: right;">
                                <div class="flex justify-end gap-2">
                                    <a href="<?= url("users/{$u->id}/edit") ?>" class="btn btn-secondary btn-sm" title="Edit">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <form method="POST" action="<?= url("users/{$u->id}/delete") ?>" style="display:inline;">
                                        <?= \App\Core\CSRF::field() ?>
                                        <button type="button" class="btn btn-danger btn-sm" data-confirm="Yakin ingin menghapus pengguna <?= e($u->name) ?>?">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                </svg>
                                <p class="empty-state-title">Tidak ada pengguna</p>
                                <p class="empty-state-desc">Belum ada pengguna yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="card-footer">
        <?php View::component('pagination', [
            'page'     => $page,
            'lastPage' => $lastPage,
            'total'    => $total,
            'baseUrl'  => 'users',
        ]); ?>
    </div>
</div>
