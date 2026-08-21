<?php use App\Core\CSRF; ?>

<div style="max-width: 860px;">
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">Pengaturan Metode Pembayaran</h3>
            <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">
                Kelola kode QRIS, daftar nomor rekening bank, dan instruksi pembayaran paket berbayar.
            </p>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('settings/payment/update') ?>" enctype="multipart/form-data">
                <?= CSRF::field() ?>

                <!-- ═══════════════════════════════════════════
                     1. METODE QRIS
                     ═══════════════════════════════════════════ -->
                <h4 style="font-size: 15px; font-weight: 700; color: var(--primary-600); margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; gap: 8px;">
                    📱 Metode Pembayaran QRIS
                </h4>

                <div class="flex items-center justify-between mb-4 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                    <div>
                        <div style="font-weight: 700; font-size: 14px;">Aktifkan Pembayaran QRIS</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Tampilkan pilihan scan QRIS di halaman checkout pengguna</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="payment_qris_enabled" value="1" <?= ($settings['payment_qris_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="grid-2 mb-3">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Nama Merchant QRIS (NMID)</label>
                        <input type="text" name="payment_qris_merchant" class="form-control" value="<?= e($settings['payment_qris_merchant'] ?? 'ASR FORM DIGITAL') ?>" placeholder="Contoh: ASR FORM DIGITAL">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Unggah Gambar QR Code QRIS</label>
                        <input type="file" name="payment_qris_image_file" class="form-control" accept="image/*">
                        <small style="color: var(--text-muted); font-size: 11px;">Format: PNG, JPG, WEBP.</small>
                    </div>
                </div>

                <?php if (!empty($settings['payment_qris_image'])): ?>
                    <div class="mb-3 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); display: flex; align-items: center; gap: 14px;">
                        <img src="<?= asset($settings['payment_qris_image']) ?>" alt="QRIS Preview" style="width: 70px; height: 70px; object-fit: contain; background: white; border-radius: 6px; border: 1px solid var(--border-subtle); padding: 4px;">
                        <div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--text-primary);">QRIS Saat Ini Aktif:</div>
                            <code style="font-size: 11px; color: var(--text-muted);"><?= e($settings['payment_qris_image']) ?></code>
                            <input type="hidden" name="payment_qris_image_url" value="<?= e($settings['payment_qris_image']) ?>">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group mb-4">
                    <label class="form-label">Instruksi Pembayaran QRIS</label>
                    <textarea name="payment_qris_instructions" class="form-control" rows="3"><?= e($settings['payment_qris_instructions'] ?? '') ?></textarea>
                </div>

                <!-- ═══════════════════════════════════════════
                     2. METODE TRANSFER REKENING BANK
                     ═══════════════════════════════════════════ -->
                <h4 style="font-size: 15px; font-weight: 700; color: var(--primary-600); margin: 32px 0 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; gap: 8px;">
                    🏦 Metode Transfer Rekening Bank
                </h4>

                <div class="flex items-center justify-between mb-4 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                    <div>
                        <div style="font-weight: 700; font-size: 14px;">Aktifkan Transfer Rekening</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Tampilkan pilihan transfer bank manual di halaman checkout</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="payment_transfer_enabled" value="1" <?= ($settings['payment_transfer_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between mb-3">
                    <label class="form-label" style="font-weight: 700; margin-bottom: 0;">Daftar Rekening Bank</label>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addBankAccount()">
                        ➕ Tambah Rekening
                    </button>
                </div>

                <div id="bank-accounts-container" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
                    <?php foreach ($bankAccounts as $idx => $acc): ?>
                        <div class="bank-account-item p-3" style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); box-shadow: var(--shadow-xs); display: grid; grid-template-columns: 140px 1fr 1fr 40px; gap: 10px; align-items: center;">
                            <div>
                                <label class="form-label" style="font-size: 10px; margin-bottom: 2px;">Bank</label>
                                <input type="text" name="bank_accounts[<?= $idx ?>][bank]" class="form-control form-control-sm" value="<?= e($acc['bank'] ?? '') ?>" placeholder="BCA / Mandiri">
                            </div>
                            <div>
                                <label class="form-label" style="font-size: 10px; margin-bottom: 2px;">Nomor Rekening</label>
                                <input type="text" name="bank_accounts[<?= $idx ?>][number]" class="form-control form-control-sm" value="<?= e($acc['number'] ?? '') ?>" placeholder="1234567890">
                            </div>
                            <div>
                                <label class="form-label" style="font-size: 10px; margin-bottom: 2px;">Atas Nama (Holder)</label>
                                <input type="text" name="bank_accounts[<?= $idx ?>][holder]" class="form-control form-control-sm" value="<?= e($acc['holder'] ?? '') ?>" placeholder="PT ASR FORM DIGITAL">
                            </div>
                            <div style="padding-top: 14px;">
                                <button type="button" class="btn btn-sm" style="color: var(--danger-500); background: transparent; padding: 4px;" onclick="this.closest('.bank-account-item').remove();" title="Hapus">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Instruksi Transfer Rekening</label>
                    <textarea name="payment_transfer_instructions" class="form-control" rows="3"><?= e($settings['payment_transfer_instructions'] ?? '') ?></textarea>
                </div>

                <div class="flex gap-3 mt-4" style="padding-top: 16px; border-top: 1px solid var(--border-subtle);">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Simpan Pengaturan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addBankAccount() {
    const container = document.getElementById('bank-accounts-container');
    const idx = container.querySelectorAll('.bank-account-item').length;

    const div = document.createElement('div');
    div.className = 'bank-account-item p-3 fade-in';
    div.style.cssText = 'background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); box-shadow: var(--shadow-xs); display: grid; grid-template-columns: 140px 1fr 1fr 40px; gap: 10px; align-items: center;';
    div.innerHTML = `
        <div>
            <label class="form-label" style="font-size: 10px; margin-bottom: 2px;">Bank</label>
            <input type="text" name="bank_accounts[${idx}][bank]" class="form-control form-control-sm" placeholder="BCA / BRI">
        </div>
        <div>
            <label class="form-label" style="font-size: 10px; margin-bottom: 2px;">Nomor Rekening</label>
            <input type="text" name="bank_accounts[${idx}][number]" class="form-control form-control-sm" placeholder="1234567890">
        </div>
        <div>
            <label class="form-label" style="font-size: 10px; margin-bottom: 2px;">Atas Nama (Holder)</label>
            <input type="text" name="bank_accounts[${idx}][holder]" class="form-control form-control-sm" placeholder="Atas Nama Rekening">
        </div>
        <div style="padding-top: 14px;">
            <button type="button" class="btn btn-sm" style="color: var(--danger-500); background: transparent; padding: 4px;" onclick="this.closest('.bank-account-item').remove();" title="Hapus">
                🗑️
            </button>
        </div>
    `;
    container.appendChild(div);
}
</script>
