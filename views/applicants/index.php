<?php 
use App\Core\CSRF;
use App\Core\View; 
?>

<div class="bento-grid">
    <!-- 1. Header Bento Hero Card -->
    <div class="bento-col-12 bento-hero fade-in" style="background: #ffffff; border: 1px solid var(--border-subtle);">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: var(--warning-50); color: var(--warning-700); border: 1px solid rgba(245,158,11,0.3);">
                ⏳
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-primary);">
                        Persetujuan Pendaftar (Approval)
                    </h2>
                    <span class="badge badge-warning" style="font-size: 11px; font-weight: 700;">
                        Total: <?= number_format($total) ?> Menunggu ACC
                    </span>
                </div>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Verifikasi dan aktifkan akun instansi/pengguna baru yang telah melakukan pendaftaran atau pembayaran.
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Bento Filter Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 16px 20px;">
        <form method="GET" action="<?= url('applicants') ?>" class="filter-bar" style="margin: 0; display: flex; gap: 12px; align-items: center;">
            <div class="search-input-wrapper" style="flex: 1; max-width: 420px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari nama, email, atau nomor WhatsApp..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;">Cari Data</button>
            <?php if (!empty($filters['search'])): ?>
                <a href="<?= url('applicants') ?>" class="btn btn-ghost btn-sm" style="color: var(--text-muted);">&times; Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- 3. Bento Table Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="margin: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Pendaftar</th>
                        <th>Kontak &amp; WhatsApp</th>
                        <th>Paket Dipilih</th>
                        <th>Waktu Mendaftar</th>
                        <th>Status</th>
                        <th style="text-align: right; min-width: 190px;">Aksi Persetujuan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applicants)): ?>
                        <?php foreach ($applicants as $applicant): ?>
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="user-avatar" style="width:36px;height:36px;font-size:14px;background:linear-gradient(135deg, var(--warning-500), var(--warning-600)); color: white; font-weight: 800;">
                                            <?= strtoupper(substr($applicant->name, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong style="color: var(--text-primary); font-size: 13.5px;"><?= e($applicant->name) ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 13px; color: var(--text-primary);"><?= e($applicant->email) ?></div>
                                    <?php if (!empty($applicant->phone)): ?>
                                        <div style="margin-top: 3px;">
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $applicant->phone) ?>" target="_blank" style="color: #16a34a; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;" title="Chat WhatsApp Pemohon">
                                                <span>💬</span> <?= e($applicant->phone) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $pName = $applicant->plan ?? 'Gratis';
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
                                    <div style="font-size: 13px; font-weight: 600;"><?= date('d/m/Y', strtotime($applicant->created_at)) ?></div>
                                    <div class="text-sm text-muted" style="font-size: 11px;"><?= date('H:i', strtotime($applicant->created_at)) ?> WIB</div>
                                </td>
                                <td>
                                    <span class="badge badge-warning" style="font-weight: 700;">
                                        ⏳ Menunggu ACC
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="flex justify-end gap-2">
                                        <!-- Approve Button -->
                                        <form method="POST" action="<?= url("applicants/{$applicant->id}/approve") ?>" style="display:inline;">
                                            <?= CSRF::field() ?>
                                            <input type="hidden" name="role_id" value="2">
                                            <button type="submit" class="btn btn-success btn-sm" style="font-weight: 700;">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                                Setujui (ACC)
                                            </button>
                                        </form>

                                        <!-- Reject Button -->
                                        <form method="POST" action="<?= url("applicants/{$applicant->id}/reject") ?>" style="display:inline;">
                                            <?= CSRF::field() ?>
                                            <button type="button" class="btn btn-danger btn-sm" data-confirm="Yakin ingin menolak pendaftaran '<?= e($applicant->name) ?>'?">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state" style="padding: 48px 20px; text-align: center;">
                                    <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--success-500); margin-bottom: 12px;">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                    </svg>
                                    <p class="empty-state-title" style="font-size: 16px; font-weight: 800; margin-bottom: 4px;">Semua Pendaftaran Telah Diproses</p>
                                    <p class="empty-state-desc" style="font-size: 13px; color: var(--text-muted);">Tidak ada pendaftar baru yang sedang menunggu persetujuan (approval).</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($applicants)): ?>
            <div style="padding: 12px 20px; border-top: 1px solid var(--border-subtle);">
                <?php View::component('pagination', [
                    'page'     => $page,
                    'lastPage' => $lastPage,
                    'total'    => $total,
                    'baseUrl'  => 'applicants',
                ]); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
