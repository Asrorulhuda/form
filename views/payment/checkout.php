<?php
use App\Core\CSRF;
use App\Core\Session;
?>

<div class="login-page" style="padding: 40px 16px;">
    <div class="login-card" style="max-width: 680px; text-align: left;">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 mb-4" style="border-bottom: 1px solid var(--border-subtle);">
            <div class="flex items-center gap-3">
                <div class="login-logo" style="width: 36px; height: 36px; font-size: 16px;">A</div>
                <div>
                    <h2 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin: 0;">Pembayaran & Konfirmasi</h2>
                    <p style="font-size: 12px; color: var(--text-secondary); margin: 0;">Selesaikan pembayaran untuk mengaktifkan akun Anda</p>
                </div>
            </div>
            <span class="badge badge-primary" style="font-size: 12px; padding: 4px 10px;">
                Paket: <?= e($user->plan ?? 'Pro') ?>
            </span>
        </div>

        <?php if (Session::hasFlash('error')): ?>
            <div class="alert alert-error mb-4">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span><?= e(Session::getFlash('error')) ?></span>
            </div>
        <?php endif; ?>

        <!-- Order Summary Card -->
        <div class="mb-4 p-4" style="background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); border-radius: var(--radius-lg); border: 1px solid #ddd6fe;">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--primary-700); letter-spacing: 0.5px;">Rincian Pesanan</div>
                    <div style="font-size: 18px; font-weight: 900; color: var(--text-primary); margin-top: 2px;">
                        Paket <?= e($user->plan ?? 'Pro') ?>
                    </div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-top: 2px;">
                        Atas nama: <strong><?= e($user->name) ?></strong> (<?= e($user->email) ?>)
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 11px; color: var(--text-muted);">Biaya Langganan</div>
                    <div style="font-size: 22px; font-weight: 900; color: var(--primary-600);">
                        <?= e($planInfo['price'] ?? 'Sesuai Tagihan') ?>
                    </div>
                    <?php if (!empty($planInfo['period'])): ?>
                        <div style="font-size: 11px; color: var(--text-secondary);"><?= e($planInfo['period']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Payment Method Selection Tabs -->
        <div class="form-group mb-4">
            <label class="form-label" style="font-weight: 700; font-size: 14px; margin-bottom: 8px;">Pilih Metode Pembayaran</label>
            
            <div class="flex gap-2 mb-3">
                <?php if ($qrisEnabled): ?>
                    <button type="button" class="pay-tab-btn active" data-tab="method-qris" onclick="switchPayMethod('method-qris', this)" style="flex: 1; padding: 10px 14px; border: 2px solid var(--primary-600); background: #ffffff; border-radius: var(--radius-md); font-weight: 700; font-size: 13px; color: var(--primary-600); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        📱 QRIS (Semua E-Wallet & Bank)
                    </button>
                <?php endif; ?>
                
                <?php if ($tfEnabled): ?>
                    <button type="button" class="pay-tab-btn <?= !$qrisEnabled ? 'active' : '' ?>" data-tab="method-transfer" onclick="switchPayMethod('method-transfer', this)" style="flex: 1; padding: 10px 14px; border: 2px solid <?= !$qrisEnabled ? 'var(--primary-600)' : 'var(--border-subtle)' ?>; background: #ffffff; border-radius: var(--radius-md); font-weight: 700; font-size: 13px; color: <?= !$qrisEnabled ? 'var(--primary-600)' : 'var(--text-secondary)' ?>; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        🏦 Transfer Bank
                    </button>
                <?php endif; ?>
            </div>

            <!-- ─── QRIS Method Details ─── -->
            <?php if ($qrisEnabled): ?>
                <div id="method-qris" class="pay-content-panel active p-4" style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); text-align: center;">
                    <div class="badge badge-success mb-2" style="font-size: 11px;">QRIS Merchant: <?= e($qrisMerchant) ?></div>
                    <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">
                        Pindai QRIS di bawah menggunakan GoPay, OVO, DANA, ShopeePay, BCA, Mandiri, BRI, atau aplikasi pembayaran lainnya:
                    </div>

                    <div style="display: inline-block; padding: 14px; background: #ffffff; border: 2px solid #e2e8f0; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); margin-bottom: 14px;">
                        <?php if (!empty($qrisImage)): ?>
                            <img src="<?= asset($qrisImage) ?>" alt="QRIS Code" style="max-width: 240px; width: 100%; height: auto; border-radius: 4px; display: block;">
                        <?php else: ?>
                            <div style="width: 220px; height: 220px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); font-size: 12px; padding: 16px;">
                                <span style="font-size: 36px; margin-bottom: 8px;">📱</span>
                                <strong>Kode QRIS</strong>
                                <span><?= e($qrisMerchant) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($qrisInstr)): ?>
                        <div style="font-size: 12px; color: var(--text-muted); line-height: 1.5; max-width: 480px; margin: 0 auto;">
                            <?= nl2br(e($qrisInstr)) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- ─── Bank Transfer Method Details ─── -->
            <?php if ($tfEnabled): ?>
                <div id="method-transfer" class="pay-content-panel <?= !$qrisEnabled ? 'active' : '' ?> p-4" style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); <?= $qrisEnabled ? 'display: none;' : '' ?>">
                    <div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 14px;">
                        Silakan lakukan transfer ke salah satu nomor rekening resmi berikut:
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;">
                        <?php foreach ($bankAccounts as $acc): ?>
                            <div class="p-3" style="background: var(--bg-subtle); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                <div>
                                    <div style="font-weight: 800; font-size: 14px; color: var(--primary-600);"><?= e($acc['bank'] ?? 'Bank') ?></div>
                                    <div style="font-family: monospace; font-size: 16px; font-weight: 700; color: var(--text-primary); margin-top: 2px;" id="acc-<?= e($acc['number'] ?? '') ?>">
                                        <?= e($acc['number'] ?? '') ?>
                                    </div>
                                    <div style="font-size: 12px; color: var(--text-secondary);">a.n. <?= e($acc['holder'] ?? '') ?></div>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="copyAccount('<?= e($acc['number'] ?? '') ?>', this)" style="font-size: 11px;">
                                    📋 Salin Nomor
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!empty($tfInstr)): ?>
                        <div style="font-size: 12px; color: var(--text-muted); line-height: 1.5;">
                            <?= nl2br(e($tfInstr)) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ─── Proof Upload Form ─── -->
        <div style="border-top: 2px solid var(--border-subtle); padding-top: 20px; margin-top: 20px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">
                📤 Formulir Konfirmasi Pembayaran
            </h3>
            <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 16px;">
                Setelah melakukan pembayaran, unggah bukti transfer Anda untuk diverifikasi oleh admin.
            </p>

            <form method="POST" action="<?= url('payment/submit') ?>" enctype="multipart/form-data" id="proof-form">
                <?= CSRF::field() ?>
                <input type="hidden" name="user_id" value="<?= $user->id ?>">
                <input type="hidden" name="payment_method" id="selected-method-input" value="<?= $qrisEnabled ? 'qris' : 'transfer' ?>">

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="sender_name">Nama Pengirim / Pemilik Rekening <span class="required">*</span></label>
                        <input type="text" id="sender_name" name="sender_name" class="form-control" placeholder="Nama pengirim di bukti transfer" value="<?= e($user->name) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sender_phone">Nomor WhatsApp Pengirim <span class="required">*</span></label>
                        <input type="tel" id="sender_phone" name="sender_phone" class="form-control" placeholder="Contoh: 081234567890" required>
                        <small style="color: var(--text-muted); font-size: 11px;">Notifikasi status approval akan dikirimkan ke nomor ini.</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="proof_file">Unggah Bukti Transfer / Struk Pembayaran <span class="required">*</span></label>
                    <input type="file" id="proof_file" name="proof_file" class="form-control" accept="image/*,application/pdf" required>
                    <small style="color: var(--text-muted); font-size: 11px;">Format: JPG, PNG, WEBP, atau PDF (Maksimal 5MB).</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="notes">Catatan Tambahan (Opsional)</label>
                    <input type="text" id="notes" name="notes" class="form-control" placeholder="Misal: Transfer via BCA Mobile pukul 10:30">
                </div>

                <button type="submit" class="btn btn-primary btn-lg login-btn mt-2" style="font-size: 15px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Kirim Bukti Pembayaran
                </button>
            </form>
        </div>

    </div>
</div>

<script>
function switchPayMethod(targetId, btn) {
    document.querySelectorAll('.pay-tab-btn').forEach(b => {
        b.classList.remove('active');
        b.style.borderColor = 'var(--border-subtle)';
        b.style.color = 'var(--text-secondary)';
    });
    document.querySelectorAll('.pay-content-panel').forEach(p => p.style.display = 'none');
    
    btn.classList.add('active');
    btn.style.borderColor = 'var(--primary-600)';
    btn.style.color = 'var(--primary-600)';

    const panel = document.getElementById(targetId);
    if (panel) panel.style.display = 'block';

    document.getElementById('selected-method-input').value = (targetId === 'method-qris') ? 'qris' : 'transfer';
}

function copyAccount(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalText = btn.innerHTML;
        btn.innerHTML = '✅ Tersalin!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-secondary');
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');
        }, 2000);
    });
}
</script>
