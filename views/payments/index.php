<?php 
use App\Core\CSRF;
use App\Core\View; 
?>

<!-- Header -->
<div class="flex items-center justify-between mb-5">
    <div>
        <p class="text-sm text-muted">Total: <?= number_format($total) ?> transaksi pembayaran</p>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body" style="padding: 16px 24px;">
        <form method="GET" action="<?= url('payments') ?>" class="filter-bar">
            <div class="search-input-wrapper" style="max-width: 320px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari user, email, no WA..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>

            <select name="status" class="form-control" style="max-width: 180px;">
                <option value="">Semua Status</option>
                <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>⏳ Menunggu Verifikasi</option>
                <option value="verified" <?= ($filters['status'] ?? '') === 'verified' ? 'selected' : '' ?>>✅ Diverifikasi (ACC)</option>
                <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>❌ Ditolak</option>
            </select>

            <select name="method" class="form-control" style="max-width: 160px;">
                <option value="">Semua Metode</option>
                <option value="qris" <?= ($filters['method'] ?? '') === 'qris' ? 'selected' : '' ?>>📱 QRIS</option>
                <option value="transfer" <?= ($filters['method'] ?? '') === 'transfer' ? 'selected' : '' ?>>🏦 Transfer Bank</option>
            </select>

            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>
</div>

<!-- Payments Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Pengguna</th>
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
                                    <strong style="color: var(--text-primary);"><?= e($p->user_name) ?></strong>
                                </div>
                                <div class="text-sm text-muted"><?= e($p->user_email) ?></div>
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
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $p->sender_phone) ?>" target="_blank" style="color: #10b981; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 4px;">
                                        💬 <?= e($p->sender_phone) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= asset($p->proof_file) ?>" target="_blank" class="btn btn-sm btn-secondary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                                    🖼️ Lihat Bukti &nearr;
                                </a>
                            </td>
                            <td>
                                <div><?= date('d/m/Y', strtotime($p->created_at)) ?></div>
                                <div class="text-sm text-muted"><?= date('H:i', strtotime($p->created_at)) ?> WIB</div>
                            </td>
                            <td>
                                <?php if ($p->status === 'verified'): ?>
                                    <span class="badge badge-success">✅ ACC</span>
                                <?php elseif ($p->status === 'rejected'): ?>
                                    <span class="badge badge-danger">❌ Ditolak</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">⏳ Pending</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <?php if ($p->status === 'pending'): ?>
                                    <div class="flex justify-end gap-2">
                                        <!-- Approve / Verify -->
                                        <form method="POST" action="<?= url("payments/{$p->id}/verify") ?>" style="display:inline;" onsubmit="return confirm('Verifikasi pembayaran ini dan aktifkan akun user <?= e($p->user_name) ?>?');">
                                            <?= CSRF::field() ?>
                                            <button type="submit" class="btn btn-success btn-sm">
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
                                    <span class="text-sm text-muted">
                                        <?= $p->status === 'verified' ? 'Diverifikasi' : 'Ditolak' ?> 
                                        <?= !empty($p->verifier_name) ? 'oleh ' . e($p->verifier_name) : '' ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 40px; color: var(--text-muted);">
                            Belum ada riwayat pembayaran yang sesuai dengan filter.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($lastPage > 1): ?>
        <div class="card-footer pagination-wrapper">
            <div class="pagination-info">
                Halaman <?= $page ?> dari <?= $lastPage ?> (Total <?= number_format($total) ?> data)
            </div>
            <div class="pagination-buttons">
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
