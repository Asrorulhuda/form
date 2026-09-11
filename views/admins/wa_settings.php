<?php 
use App\Core\CSRF;
use App\Core\Session;
?>

<div style="max-width: 860px;">
    <!-- Info Hero Banner -->
    <div class="card fade-in mb-4" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; border: none;">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 style="font-size: 20px; font-weight: 800; margin: 0 0 6px 0; color: white;">📱 WhatsApp Gateway Multi-Tenant</h2>
                    <p style="font-size: 13px; margin: 0; opacity: 0.9; max-width: 600px; line-height: 1.5;">
                        Kirim notifikasi otomatis ke nomor WhatsApp responden atau pengguna menggunakan perangkat WhatsApp Anda sendiri.
                    </p>
                </div>
                <span class="badge" style="background: rgba(255,255,255,0.25); color: white; border: 1px solid rgba(255,255,255,0.4); font-size: 12px; padding: 6px 14px;">
                    <?= $isGlobalEnabled ? '🟢 Gateway Aktif' : '🟡 Menunggu Konfigurasi Super Admin' ?>
                </span>
            </div>
        </div>
    </div>

    <!-- 1. Gateway Infrastructure Info (from Super Admin) -->
    <div class="card fade-in mb-4">
        <div class="card-header">
            <h3 class="card-title">🌐 Infrastruktur Gateway (Super Admin)</h3>
        </div>
        <div class="card-body">
            <div class="flex items-center justify-between p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                <div>
                    <div style="font-size: 13px; font-weight: 700; color: var(--text-primary);">API Gateway Base URL</div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                        Server pengiriman terpusat yang disediakan oleh platform.
                    </div>
                </div>
                <code style="font-size: 13px; background: var(--bg-surface); padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border-subtle); color: var(--primary);">
                    <?= e($globalGatewayUrl) ?>
                </code>
            </div>
        </div>
    </div>

    <!-- 2. Tenant Credentials Form -->
    <div class="card fade-in mb-4">
        <div class="card-header">
            <h3 class="card-title">🔑 Kredensial Perangkat Pengirim Anda</h3>
            <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">
                Masukkan nomor WhatsApp device dan API key milik Anda yang sudah terhubung di gateway.
            </p>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('settings/wa/update') ?>">
                <?= CSRF::field() ?>

                <div class="grid-2 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="wa_sender">Nomor WA Pengirim (Sender) <span class="required">*</span></label>
                        <input type="text" id="wa_sender" name="wa_sender" class="form-control"
                               placeholder="Contoh: 08123456789 atau 628123456789"
                               value="<?= e($admin->wa_sender ?? '') ?>" required>
                        <small style="color: var(--text-muted); font-size: 11px;">Nomor HP device WhatsApp Anda yang bertindak sebagai pengirim.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="wa_api_key">API Key WhatsApp Anda <span class="required">*</span></label>
                        <input type="text" id="wa_api_key" name="wa_api_key" class="form-control"
                               placeholder="Masukkan API Key dari dashboard gateway"
                               value="<?= e($admin->wa_api_key ?? '') ?>" required>
                        <small style="color: var(--text-muted); font-size: 11px;">Kunci otentikasi device Anda di gateway.</small>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Simpan Kredensial WhatsApp
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Test WhatsApp Sending -->
    <div class="card fade-in mb-4">
        <div class="card-header">
            <h3 class="card-title">🧪 Uji Coba Pengiriman Pesan</h3>
            <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">
                Pastikan perangkat Anda aktif dengan mengirim pesan uji coba ke nomor tujuan di bawah.
            </p>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('settings/wa/test') ?>">
                <?= CSRF::field() ?>

                <div class="form-group mb-3">
                    <label class="form-label" for="test_wa_number">Nomor WhatsApp Tujuan Uji Coba <span class="required">*</span></label>
                    <div style="display: flex; gap: 8px; max-width: 480px;">
                        <input type="text" id="test_wa_number" name="test_wa_number" class="form-control"
                               placeholder="Contoh: 081234567890" required>
                        <button type="submit" class="btn btn-secondary" style="white-space: nowrap;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                            Kirim Uji Coba
                        </button>
                    </div>
                    <small style="color: var(--text-muted); font-size: 11px;">Nomor tujuan yang dapat menerima pesan WhatsApp.</small>
                </div>
            </form>
        </div>
    </div>
</div>
