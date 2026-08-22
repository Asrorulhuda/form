<?php 
use App\Core\CSRF;
use App\Core\View; 
?>

<div class="bento-grid">
    <!-- 1. Header Bento Hero Card -->
    <div class="bento-col-12 bento-hero fade-in" style="background: #ffffff; border: 1px solid var(--border-subtle);">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                💳
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-primary);">
                        Riwayat Transaksi &amp; Verifikasi Pembayaran
                    </h2>
                    <span class="badge badge-success" style="font-size: 11px; font-weight: 700;">
                        Total: <?= number_format($total) ?> Transaksi
                    </span>
                </div>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Pantau konfirmasi transfer bank & QRIS, periksa bukti transfer, dan verifikasi langganan instansi.
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Bento Filter Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 16px 20px;">
        <form method="GET" action="<?= url('payments') ?>" class="filter-bar" style="margin: 0; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div class="search-input-wrapper" style="flex: 1; min-width: 220px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari user, email, no WhatsApp..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>

            <select name="status" class="form-control" style="width: auto; min-width: 170px;">
                <option value="">Semua Status</option>
                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>⏳ Menunggu Verifikasi</option>
                <option value="verified" <?= ($filters['status'] ?? '') === 'verified' ? 'selected' : '' ?>>✅ Diverifikasi (ACC)</option>
                <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>❌ Ditolak</option>
            </select>

            <select name="method" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">Semua Metode</option>
                <option value="qris" <?= ($filters['method'] ?? '') === 'qris' ? 'selected' : '' ?>>📱 QRIS</option>
                <option value="transfer" <?= ($filters['method'] ?? '') === 'transfer' ? 'selected' : '' ?>>🏦 Transfer Bank</option>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;">Filter Transaksi</button>
            <?php if (!empty($filters['search']) || !empty($filters['status']) || !empty($filters['method'])): ?>
                <a href="<?= url('payments') ?>" class="btn btn-ghost btn-sm" style="color: var(--text-muted);">&times; Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- 3. Bento Table Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="margin: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pengguna / Instansi</th>
                        <th>Paket</th>
                        <th>Metode</th>
                        <th>No. WhatsApp</th>
                        <th>Bukti Transfer</th>
                        <th>Waktu Submit</th>
                        <th>Status</th>
                        <th style="text-align: right; min-width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td>
                                    <div>
                                        <strong style="color: var(--text-primary); font-size: 13.5px;"><?= e($p->user_name) ?></strong>
                                    </div>
                                    <div class="text-sm text-muted" style="font-size: 12px;"><?= e($p->user_email) ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-primary" style="font-weight: 700;">
                                        <?= e($p->plan) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary" style="font-weight: 600;">
                                        <?= strtoupper(e($p->payment_method)) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($p->sender_phone)): ?>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $p->sender_phone) ?>" target="_blank" style="color: #10b981; font-weight: 700; font-size: 12.5px; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
                                            💬 <?= e($p->sender_phone) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= asset($p->proof_file) ?>" target="_blank" class="btn btn-sm btn-secondary" style="font-size: 11px; padding: 4px 10px; font-weight: 700;">
                                        🖼️ Lihat Bukti &nearr;
                                    </a>
                                </td>
                                <td>
                                    <div style="font-size: 13px; font-weight: 600;"><?= date('d/m/Y', strtotime($p->created_at)) ?></div>
                                    <div class="text-sm text-muted" style="font-size: 11px;"><?= date('H:i', strtotime($p->created_at)) ?> WIB</div>
                                </td>
                                <td>
                                    <?php if ($p->status === 'verified'): ?>
                                        <span class="badge badge-success" style="font-weight: 700;">✅ Diverifikasi</span>
                                    <?php elseif ($p->status === 'rejected'): ?>
                                        <span class="badge badge-danger" style="font-weight: 700;">❌ Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning" style="font-weight: 700;">⏳ Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($p->status === 'pending'): ?>
                                        <div class="flex justify-end gap-2">
                                            <!-- Approve / Verify -->
                                            <form method="POST" action="<?= url("payments/{$p->id}/verify") ?>" style="display:inline;" onsubmit="return confirm('Verifikasi pembayaran ini dan aktifkan akun user <?= e($p->user_name) ?>?');">
                                                <?= CSRF::field() ?>
                                                <button type="submit" class="btn btn-success btn-sm" style="font-weight: 700;">
                                                    ✓ Setujui (ACC)
                                                </button>
                                            </form>

                                            <!-- Reject -->
                                            <form method="POST" action="<?= url("payments/{$p->id}/reject") ?>" style="display:inline;" onsubmit="return confirm('Tolak pembayaran ini?');">
                                                <?= CSRF::field() ?>
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    ✕ Tolak
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-sm text-muted" style="font-size: 11.5px;">
                                            <?= $p->status === 'verified' ? 'Diverifikasi' : 'Ditolak' ?> 
                                            <?= !empty($p->verifier_name) ? 'oleh ' . e($p->verifier_name) : '' ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state" style="padding: 48px 20px; text-align: center;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: 12px;">
                                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                        <line x1="1" y1="10" x2="23" y2="10"/>
                                    </svg>
                                    <p class="empty-state-title" style="font-size: 16px; font-weight: 800; margin-bottom: 4px;">Belum Ada Riwayat Pembayaran</p>
                                    <p class="empty-state-desc" style="font-size: 13px; color: var(--text-muted);">Tidak ada transaksi pembayaran yang cocok dengan filter saat ini.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($lastPage > 1): ?>
            <div style="padding: 12px 20px; background: #fafafa; border-top: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between;">
                <div class="text-xs text-muted">
                    Halaman <strong><?= $page ?></strong> dari <strong><?= $lastPage ?></strong> (Total: <?= number_format($total) ?> data)
                </div>
                <div class="flex gap-1">
                    <?php if ($page > 1): ?>
                        <a href="<?= url('payments?' . http_build_query(array_merge($filters, ['page' => $page - 1]))) ?>" class="btn btn-sm btn-secondary">
                            &larr; Sebelumnya
                        </a>
                    <?php endif; ?>
                    <?php if ($page < $lastPage): ?>
                        <a href="<?= url('payments?' . http_build_query(array_merge($filters, ['page' => $page + 1]))) ?>" class="btn btn-sm btn-secondary">
                            Selanjutnya &rarr;
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
