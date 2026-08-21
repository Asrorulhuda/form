<?php use App\Core\CSRF; ?>

<div style="max-width: 860px;">
    <!-- ═══════════════════════════════════════════
         1. WHATSAPP GATEWAY (asr-desain.my.id)
         ═══════════════════════════════════════════ -->
    <div class="card fade-in mb-4">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title">📱 WhatsApp Gateway</h3>
                    <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">
                        Integrasi API WhatsApp Gateway resmi (<code>https://gateway.asr-desain.my.id</code>) untuk notifikasi realtime.
                    </p>
                </div>
                <span class="badge <?= ($waSettings['wa_enabled'] ?? '0') === '1' ? 'badge-success' : 'badge-secondary' ?>">
                    <?= ($waSettings['wa_enabled'] ?? '0') === '1' ? 'Aktif' : 'Nonaktif' ?>
                </span>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('settings/gateway/update') ?>">
                <?= CSRF::field() ?>

                <div class="flex items-center justify-between mb-4 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                    <div>
                        <div style="font-weight: 700; font-size: 14px;">Aktifkan WhatsApp Gateway</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Kirim notifikasi otomatis ke WhatsApp Admin & Pengguna</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="wa_enabled" value="1" <?= ($waSettings['wa_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="grid-2 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="wa_api_key">API Key <span class="required">*</span></label>
                        <input type="password" id="wa_api_key" name="wa_api_key" class="form-control" value="<?= e($waSettings['wa_api_key'] ?? '') ?>" placeholder="Masukkan API Key WhatsApp Gateway Anda">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="wa_sender">Sender Device Number <span class="required">*</span></label>
                        <input type="text" id="wa_sender" name="wa_sender" class="form-control" value="<?= e($waSettings['wa_sender'] ?? '') ?>" placeholder="Contoh: 62888xxxx atau 0888xxxx">
                        <small style="color: var(--text-muted); font-size: 11px;">Nomor HP device Anda yang terhubung di gateway.</small>
                    </div>
                </div>

                <div class="grid-2 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="wa_admin_number">Nomor WhatsApp Admin (Penerima Alert)</label>
                        <input type="text" id="wa_admin_number" name="wa_admin_number" class="form-control" value="<?= e($waSettings['wa_admin_number'] ?? '') ?>" placeholder="Contoh: 6281234567890">
                        <small style="color: var(--text-muted); font-size: 11px;">Nomor admin untuk menerima notifikasi pendaftaran & pembayaran baru.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="wa_footer">Footer Pesan</label>
                        <input type="text" id="wa_footer" name="wa_footer" class="form-control" value="<?= e($waSettings['wa_footer'] ?? 'Sent by ASR FORM System') ?>">
                    </div>
                </div>

                <!-- Notification Triggers -->
                <div class="p-3 mb-4" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                    <div style="font-weight: 700; font-size: 13px; margin-bottom: 10px;">🔔 Kejadian Otomatis WhatsApp (Event Triggers):</div>
                    <div class="flex flex-col gap-2">
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="wa_notify_admin_on_payment" value="1" <?= ($waSettings['wa_notify_admin_on_payment'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span>Kirim pesan WA ke Admin saat ada <strong>bukti pembayaran baru</strong> diunggah</span>
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="wa_notify_user_on_approval" value="1" <?= ($waSettings['wa_notify_user_on_approval'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span>Kirim pesan WA ke Pengguna saat akun/pembayaran <strong>diverifikasi & aktif (ACC)</strong></span>
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="wa_notify_on_form_response" value="1" <?= ($waSettings['wa_notify_on_form_response'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span>Kirim pesan WA ke Admin saat ada <strong>respons formulir publik baru</strong> masuk</span>
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="wa_notify_respondent_on_submit" value="1" <?= ($waSettings['wa_notify_respondent_on_submit'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span>Kirim pesan WA ke <strong>Responden</strong> berisi tanda terima & link berkas dokumen yang terbit</span>
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="wa_notify_on_contact" value="1" <?= ($waSettings['wa_notify_on_contact'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span>Kirim pesan WA ke Admin saat ada pesan baru dari <strong>halaman Kontak website</strong></span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3" style="border-top: 1px solid var(--border-subtle);">
                    <button type="submit" class="btn btn-primary">
                        Simpan Pengaturan WhatsApp
                    </button>
                </div>
            </form>

            <!-- Test WhatsApp Live Form -->
            <div class="mt-4 p-3" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: var(--radius-md);">
                <div style="font-weight: 700; font-size: 13px; color: #166534; margin-bottom: 6px;">🧪 Uji Coba Pengiriman Pesan WhatsApp</div>
                <form method="POST" action="<?= url('settings/gateway/test-wa') ?>" class="flex gap-2 items-center flex-wrap">
                    <?= CSRF::field() ?>
                    <input type="text" name="test_wa_number" class="form-control" style="max-width: 320px;" placeholder="Nomor WhatsApp tujuan (misal: 0812xxx)" required>
                    <button type="submit" class="btn btn-success btn-sm" style="font-weight: 700;">
                        🚀 Kirim Pesan Uji Coba
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════
         2. GMAIL / SMTP GATEWAY
         ═══════════════════════════════════════════ -->
    <div class="card fade-in">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title">✉️ Gmail / SMTP Email Gateway</h3>
                    <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">
                        Kirim notifikasi email otomatis menggunakan akun Gmail (Google App Password) atau server SMTP Anda.
                    </p>
                </div>
                <span class="badge <?= ($smtpSettings['smtp_enabled'] ?? '0') === '1' ? 'badge-success' : 'badge-secondary' ?>">
                    <?= ($smtpSettings['smtp_enabled'] ?? '0') === '1' ? 'Aktif' : 'Nonaktif' ?>
                </span>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('settings/gateway/update') ?>">
                <?= CSRF::field() ?>

                <div class="flex items-center justify-between mb-4 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                    <div>
                        <div style="font-weight: 700; font-size: 14px;">Aktifkan Email Gateway (SMTP)</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Kirim notifikasi email ke Admin & Pengguna</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="smtp_enabled" value="1" <?= ($smtpSettings['smtp_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="grid-3 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="smtp_host">SMTP Host</label>
                        <input type="text" id="smtp_host" name="smtp_host" class="form-control" value="<?= e($smtpSettings['smtp_host'] ?? 'smtp.gmail.com') ?>" placeholder="smtp.gmail.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="smtp_port">Port</label>
                        <input type="number" id="smtp_port" name="smtp_port" class="form-control" value="<?= e($smtpSettings['smtp_port'] ?? '465') ?>" placeholder="465 / 587">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="smtp_encryption">Enkripsi</label>
                        <select id="smtp_encryption" name="smtp_encryption" class="form-control">
                            <option value="ssl" <?= ($smtpSettings['smtp_encryption'] ?? 'ssl') === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                            <option value="tls" <?= ($smtpSettings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS / STARTTLS (Port 587)</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="smtp_username">Username / Email Akun Gmail <span class="required">*</span></label>
                        <input type="email" id="smtp_username" name="smtp_username" class="form-control" value="<?= e($smtpSettings['smtp_username'] ?? '') ?>" placeholder="your-email@gmail.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="smtp_password">Password / Google App Password <span class="required">*</span></label>
                        <input type="password" id="smtp_password" name="smtp_password" class="form-control" value="<?= e($smtpSettings['smtp_password'] ?? '') ?>" placeholder="xxxx xxxx xxxx xxxx">
                        <small style="color: var(--text-muted); font-size: 11px;">Gunakan 16 digit Sandi Aplikasi (Google App Password) dari akun Google Anda.</small>
                    </div>
                </div>

                <div class="grid-2 mb-3">
                    <div class="form-group">
                        <label class="form-label" for="smtp_from_name">Nama Pengirim Email</label>
                        <input type="text" id="smtp_from_name" name="smtp_from_name" class="form-control" value="<?= e($smtpSettings['smtp_from_name'] ?? 'ASR FORM Notification') ?>" placeholder="ASR FORM Notification">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="smtp_admin_email">Email Admin (Penerima Notifikasi)</label>
                        <input type="email" id="smtp_admin_email" name="smtp_admin_email" class="form-control" value="<?= e($smtpSettings['smtp_admin_email'] ?? '') ?>" placeholder="admin@domain.com">
                    </div>
                </div>

                <!-- Event Triggers -->
                <div class="p-3 mb-4" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                    <div style="font-weight: 700; font-size: 13px; margin-bottom: 10px;">🔔 Notifikasi Email Otomatis:</div>
                    <div class="flex flex-col gap-2">
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="smtp_notify_admin_on_payment" value="1" <?= ($smtpSettings['smtp_notify_admin_on_payment'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span>Kirim email ke Admin saat ada <strong>bukti pembayaran masuk</strong></span>
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="smtp_notify_user_on_approval" value="1" <?= ($smtpSettings['smtp_notify_user_on_approval'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span>Kirim email konfirmasi ke Pengguna saat akun/pembayaran <strong>diverifikasi & aktif (ACC)</strong></span>
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="smtp_notify_on_form_response" value="1" <?= ($smtpSettings['smtp_notify_on_form_response'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span>Kirim email ke Admin saat ada <strong>respons formulir publik baru</strong> masuk</span>
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="smtp_notify_respondent_on_submit" value="1" <?= ($smtpSettings['smtp_notify_respondent_on_submit'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span>Kirim email ke <strong>Responden</strong> berisi tanda terima & link berkas dokumen yang terbit</span>
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="smtp_notify_on_contact" value="1" <?= ($smtpSettings['smtp_notify_on_contact'] ?? '1') === '1' ? 'checked' : '' ?>>
                            <span>Kirim email ke Admin saat ada pesan baru dari <strong>halaman Kontak website</strong></span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3" style="border-top: 1px solid var(--border-subtle);">
                    <button type="submit" class="btn btn-primary">
                        Simpan Pengaturan Gmail / SMTP
                    </button>
                </div>
            </form>

            <!-- Test Mail Live Form -->
            <div class="mt-4 p-3" style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: var(--radius-md);">
                <div style="font-weight: 700; font-size: 13px; color: #1e40af; margin-bottom: 6px;">🧪 Uji Coba Pengiriman Email SMTP</div>
                <form method="POST" action="<?= url('settings/gateway/test-mail') ?>" class="flex gap-2 items-center flex-wrap">
                    <?= CSRF::field() ?>
                    <input type="email" name="test_email" class="form-control" style="max-width: 320px;" placeholder="Alamat email penerima (misal: your@email.com)" required>
                    <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 700;">
                        ✉️ Kirim Email Uji Coba
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
