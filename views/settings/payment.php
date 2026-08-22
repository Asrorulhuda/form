<?php use App\Core\CSRF; ?>

<div class="bento-grid">
    <!-- 1. Header Bento Hero Card -->
    <div class="bento-col-12 bento-hero fade-in" style="background: #ffffff; border: 1px solid var(--border-subtle);">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                🏦
            </div>
            <div>
                <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-primary);">
                    Pengaturan Metode Pembayaran &amp; Gateway
                </h2>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Kelola integrasi QRIS statis/dinamis, daftar rekening bank instansi, dan panduan checkout pengguna.
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Form Bento Card -->
    <div class="bento-col-12 bento-card fade-in" style="max-width: 900px; padding: 24px 28px;">
        <form method="POST" action="<?= url('settings/payment/update') ?>" enctype="multipart/form-data">
            <?= CSRF::field() ?>

            <!-- 1. METODE QRIS -->
            <div style="margin-bottom: 28px;">
                <div class="flex items-center gap-2 mb-3 pb-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="font-size: 16px;">📱</span>
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0;">Metode Pembayaran QRIS</h3>
                </div>

                <div class="flex items-center justify-between mb-4 p-3" style="background: #f8fafc; border-radius: 12px; border: 1px solid var(--border-subtle);">
                    <div>
                        <div style="font-weight: 700; font-size: 13.5px; color: var(--text-primary);">Aktifkan Pembayaran QRIS</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Tampilkan pilihan scan QRIS di halaman checkout langganan pengguna</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="payment_qris_enabled" value="1" <?= ($settings['payment_qris_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="grid-2 gap-3 mb-3">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label font-semibold" style="font-size: 13px; color: #334155;">Nama Merchant QRIS (NMID)</label>
                        <input type="text" name="payment_qris_merchant" class="form-control" value="<?= e($settings['payment_qris_merchant'] ?? 'ASR FORM DIGITAL') ?>" placeholder="Contoh: ASR FORM DIGITAL">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label font-semibold" style="font-size: 13px; color: #334155;">Unggah Gambar QR Code QRIS</label>
                        <input type="file" id="qris-file-input" name="payment_qris_image_file" class="form-control" accept="image/*" onchange="previewQrisFile(this)">
                        <small style="color: var(--text-muted); font-size: 11px;">Format: PNG, JPG, WEBP.</small>
                    </div>
                </div>

                <!-- QRIS Preview Card -->
                <div id="qris-preview-container" class="mb-3 p-3" style="background: #f8fafc; border-radius: 12px; display: <?= !empty($settings['payment_qris_image']) ? 'flex' : 'none' ?>; align-items: center; gap: 16px; border: 1px solid var(--border-subtle);">
                    <div style="background: #ffffff; padding: 6px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: var(--shadow-xs); display: flex; align-items: center; justify-content: center; min-width: 90px; min-height: 90px;">
                        <img id="qris-preview-img" src="<?= !empty($settings['payment_qris_image']) ? asset($settings['payment_qris_image']) : '' ?>" alt="QRIS Preview" style="width: 80px; height: 80px; object-fit: contain; border-radius: 6px; display: block;" onerror="this.style.display='none'; document.getElementById('qris-fallback-icon').style.display='block';">
                        <div id="qris-fallback-icon" style="display: none; text-align: center; font-size: 28px;">📱</div>
                    </div>
                    <div>
                        <div style="font-size: 13px; font-weight: 700; color: var(--text-primary);" id="qris-preview-title">Gambar QRIS Saat Ini:</div>
                        <code id="qris-preview-filename" style="font-size: 11.5px; color: var(--primary-600); background: #e0e7ff; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-top: 4px; word-break: break-all;"><?= e($settings['payment_qris_image'] ?? '') ?></code>
                        <input type="hidden" id="qris-image-url" name="payment_qris_image_url" value="<?= e($settings['payment_qris_image'] ?? '') ?>">
                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Pindai barcode ini untuk memastikan QRIS dapat diproses dengan benar.</div>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label font-semibold" style="font-size: 13px; color: #334155;">Instruksi Pembayaran QRIS</label>
                    <textarea name="payment_qris_instructions" class="form-control" rows="3"><?= e($settings['payment_qris_instructions'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- 2. METODE TRANSFER REKENING BANK -->
            <div style="margin-bottom: 28px;">
                <div class="flex items-center gap-2 mb-3 pb-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="font-size: 16px;">🏦</span>
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0;">Metode Transfer Rekening Bank</h3>
                </div>

                <div class="flex items-center justify-between mb-4 p-3" style="background: #f8fafc; border-radius: 12px; border: 1px solid var(--border-subtle);">
                    <div>
                        <div style="font-weight: 700; font-size: 13.5px; color: var(--text-primary);">Aktifkan Transfer Rekening</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Tampilkan pilihan transfer bank manual di halaman checkout</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="payment_transfer_enabled" value="1" <?= ($settings['payment_transfer_enabled'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between mb-3">
                    <label class="form-label font-semibold" style="font-size: 13px; color: #334155; margin-bottom: 0;">Daftar Rekening Bank</label>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addBankAccount()" style="font-weight: 600;">
                        ➕ Tambah Rekening
                    </button>
                </div>

                <div id="bank-accounts-container" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
                    <?php foreach ($bankAccounts as $idx => $acc): ?>
                        <div class="bank-account-item p-3" style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: 10px; box-shadow: var(--shadow-xs); display: grid; grid-template-columns: 140px 1fr 1fr 40px; gap: 10px; align-items: center;">
                            <div>
                                <label class="form-label" style="font-size: 10.5px; margin-bottom: 2px; font-weight: 700;">Bank</label>
                                <input type="text" name="bank_accounts[<?= $idx ?>][bank]" class="form-control form-control-sm" value="<?= e($acc['bank'] ?? '') ?>" placeholder="BCA / Mandiri">
                            </div>
                            <div>
                                <label class="form-label" style="font-size: 10.5px; margin-bottom: 2px; font-weight: 700;">Nomor Rekening</label>
                                <input type="text" name="bank_accounts[<?= $idx ?>][number]" class="form-control form-control-sm" value="<?= e($acc['number'] ?? '') ?>" placeholder="1234567890">
                            </div>
                            <div>
                                <label class="form-label" style="font-size: 10.5px; margin-bottom: 2px; font-weight: 700;">Atas Nama (Holder)</label>
                                <input type="text" name="bank_accounts[<?= $idx ?>][holder]" class="form-control form-control-sm" value="<?= e($acc['holder'] ?? '') ?>" placeholder="PT ASR FORM DIGITAL">
                            </div>
                            <div style="padding-top: 14px; text-align: center;">
                                <button type="button" class="btn btn-sm" style="color: var(--danger-500); background: transparent; padding: 4px; border: none; cursor: pointer;" onclick="this.closest('.bank-account-item').remove();" title="Hapus">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label font-semibold" style="font-size: 13px; color: #334155;">Instruksi Transfer Rekening</label>
                    <textarea name="payment_transfer_instructions" class="form-control" rows="3"><?= e($settings['payment_transfer_instructions'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-3" style="border-top: 1px solid var(--border-subtle);">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-weight: 700; box-shadow: 0 4px 14px rgba(79,70,229,0.25);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Simpan Pengaturan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewQrisFile(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('qris-preview-container');
            const img = document.getElementById('qris-preview-img');
            const fallback = document.getElementById('qris-fallback-icon');
            const title = document.getElementById('qris-preview-title');
            const filename = document.getElementById('qris-preview-filename');

            img.src = e.target.result;
            img.style.display = 'block';
            if (fallback) fallback.style.display = 'none';
            if (title) title.innerText = 'Preview Gambar Baru Terpilih:';
            if (filename) filename.innerText = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
            container.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }
}

function addBankAccount() {
    const container = document.getElementById('bank-accounts-container');
    const idx = container.querySelectorAll('.bank-account-item').length;

    const div = document.createElement('div');
    div.className = 'bank-account-item p-3 fade-in';
    div.style.cssText = 'background: #ffffff; border: 1px solid var(--border-subtle); border-radius: 10px; box-shadow: var(--shadow-xs); display: grid; grid-template-columns: 140px 1fr 1fr 40px; gap: 10px; align-items: center;';
    div.innerHTML = `
        <div>
            <label class="form-label" style="font-size: 10.5px; margin-bottom: 2px; font-weight: 700;">Bank</label>
            <input type="text" name="bank_accounts[${idx}][bank]" class="form-control form-control-sm" placeholder="BCA / BRI">
        </div>
        <div>
            <label class="form-label" style="font-size: 10.5px; margin-bottom: 2px; font-weight: 700;">Nomor Rekening</label>
            <input type="text" name="bank_accounts[${idx}][number]" class="form-control form-control-sm" placeholder="1234567890">
        </div>
        <div>
            <label class="form-label" style="font-size: 10.5px; margin-bottom: 2px; font-weight: 700;">Atas Nama (Holder)</label>
            <input type="text" name="bank_accounts[${idx}][holder]" class="form-control form-control-sm" placeholder="Atas Nama Rekening">
        </div>
        <div style="padding-top: 14px; text-align: center;">
            <button type="button" class="btn btn-sm" style="color: var(--danger-500); background: transparent; padding: 4px; border: none; cursor: pointer;" onclick="this.closest('.bank-account-item').remove();" title="Hapus">
                🗑️
            </button>
        </div>
    `;
    container.appendChild(div);
}
</script>
