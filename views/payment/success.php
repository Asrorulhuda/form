<div style="min-height: 100vh; background: #f8fafc; padding: 40px 16px; display: flex; align-items: center; justify-content: center;">
    <div style="max-width: 520px; width: 100%; background: #ffffff; border: 1px solid var(--border-subtle); border-radius: 20px; padding: 36px 28px; box-shadow: var(--shadow-md); text-align: center;" class="fade-in">
        
        <div style="width: 72px; height: 72px; background: #ecfdf5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 36px; border: 4px solid #d1fae5; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.2);">
            ✓
        </div>

        <h1 style="font-size: 22px; font-weight: 900; color: var(--text-primary); margin: 0 0 8px; letter-spacing: -0.3px;">
            Bukti Pembayaran Berhasil Dikirim!
        </h1>

        <p style="font-size: 13.5px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
            Terima kasih <strong><?= e($user->name) ?></strong>. Bukti transaksi pembayaran untuk <strong>Paket <?= e($user->plan) ?></strong> telah kami terima dan sedang diproses verifikasi oleh tim Administrator.
        </p>

        <div style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: 14px; padding: 16px; text-align: left; font-size: 13px; margin-bottom: 20px;">
            <div style="font-weight: 800; color: var(--text-primary); margin-bottom: 10px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">📋 Detail Pengajuan:</div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span style="color: var(--text-muted);">Paket Langganan:</span>
                <strong style="color: var(--primary-600);"><?= e($user->plan) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span style="color: var(--text-muted);">Email Akun:</span>
                <strong style="color: var(--text-primary);"><?= e($user->email) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 6px; border-top: 1px dashed var(--border-subtle);">
                <span style="color: var(--text-muted);">Status Verifikasi:</span>
                <span class="badge badge-warning" style="font-size: 11px; font-weight: 700;">⏳ Menunggu Persetujuan Admin</span>
            </div>
        </div>

        <div style="font-size: 12.5px; color: var(--text-muted); line-height: 1.5; margin-bottom: 24px; background: #eff6ff; padding: 10px 14px; border-radius: 10px; border: 1px solid #bfdbfe; color: #1e40af;">
            💡 Notifikasi konfirmasi dan aktivasi akun akan otomatis dikirimkan ke <strong>WhatsApp</strong> dan <strong>Email</strong> Anda setelah disetujui.
        </div>

        <div class="flex flex-col gap-2">
            <a href="<?= url('login') ?>" class="btn btn-primary btn-lg" style="width: 100%; border-radius: 12px; font-weight: 800; padding: 12px; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);">
                Ke Halaman Login
            </a>
            <a href="<?= url() ?>" style="font-size: 13px; color: var(--text-muted); margin-top: 8px; text-decoration: none; font-weight: 600;">
                &larr; Kembali ke Halaman Utama
            </a>
        </div>

    </div>
</div>
