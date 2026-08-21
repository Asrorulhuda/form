<div class="login-page" style="padding: 40px 16px;">
    <div class="login-card" style="max-width: 540px; text-align: center;">
        
        <div style="width: 64px; height: 64px; background: #ecfdf5; color: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 32px;">
            ✓
        </div>

        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">
            Bukti Pembayaran Berhasil Dikirim!
        </h1>

        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px;">
            Terima kasih <strong><?= e($user->name) ?></strong>. Bukti transfer pembayaran untuk paket <strong><?= e($user->plan) ?></strong> telah kami terima dan sedang dalam proses verifikasi oleh tim Administrator.
        </p>

        <div class="p-4 mb-4" style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); text-align: left; font-size: 13px;">
            <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">📋 Detail Pengajuan:</div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                <span style="color: var(--text-muted);">Paket:</span>
                <strong><?= e($user->plan) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                <span style="color: var(--text-muted);">Email Akun:</span>
                <strong><?= e($user->email) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                <span style="color: var(--text-muted);">Status:</span>
                <span class="badge badge-warning" style="font-size: 11px;">⏳ Menunggu Verifikasi Admin</span>
            </div>
        </div>

        <div style="font-size: 13px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px;">
            💡 Notifikasi konfirmasi dan status akun aktif akan otomatis dikirimkan ke <strong>WhatsApp</strong> dan <strong>Email</strong> Anda setelah diverifikasi.
        </div>

        <div class="flex flex-col gap-2">
            <a href="<?= url('login') ?>" class="btn btn-primary btn-lg login-btn">
                Ke Halaman Login
            </a>
            <a href="<?= url() ?>" style="font-size: 13px; color: var(--text-muted); margin-top: 8px;">
                &larr; Kembali ke Beranda
            </a>
        </div>

    </div>
</div>
