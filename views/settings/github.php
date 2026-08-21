<?php
use App\Core\CSRF;

$webhookUrl = url('webhook/github');
$isGitRepo = !empty($gitInfo['is_git_repo']);
$gitAvailable = !empty($gitInfo['git_available']);
$isWebhookActive = ($settings['github_webhook_enabled'] ?? '1') === '1';
?>

<div style="max-width: 1000px;">
    <!-- Header Notification / Status Summary -->
    <div class="grid-4 mb-4">
        <div class="card p-3 fade-in flex items-center gap-3">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(99, 102, 241, 0.1); color: var(--primary-500); display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
                </svg>
            </div>
            <div>
                <div style="font-size: 11px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700;">Status Webhook</div>
                <div style="font-size: 15px; font-weight: 700; margin-top: 2px;">
                    <span class="badge <?= $isWebhookActive ? 'badge-success' : 'badge-secondary' ?>">
                        <?= $isWebhookActive ? '● Aktif' : '○ Nonaktif' ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="card p-3 fade-in flex items-center gap-3">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div>
                <div style="font-size: 11px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700;">Deploy Berhasil</div>
                <div style="font-size: 18px; font-weight: 800; color: #10b981; margin-top: 2px;"><?= number_format($successCount) ?></div>
            </div>
        </div>

        <div class="card p-3 fade-in flex items-center gap-3">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
            <div>
                <div style="font-size: 11px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700;">Deploy Gagal</div>
                <div style="font-size: 18px; font-weight: 800; color: #ef4444; margin-top: 2px;"><?= number_format($failedCount) ?></div>
            </div>
        </div>

        <div class="card p-3 fade-in flex items-center gap-3">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="6" y1="3" x2="6" y2="15"></line>
                    <circle cx="18" cy="6" r="3"></circle>
                    <circle cx="6" cy="18" r="3"></circle>
                    <path d="M18 9a9 9 0 0 1-9 9"></path>
                </svg>
            </div>
            <div>
                <div style="font-size: 11px; color: var(--text-tertiary); text-transform: uppercase; font-weight: 700;">Target Branch</div>
                <div style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin-top: 2px;">
                    <code style="background: var(--bg-subtle); padding: 2px 6px; border-radius: 4px;"><?= e($settings['github_webhook_branch'] ?? 'main') ?></code>
                </div>
            </div>
        </div>
    </div>

    <!-- Webhook Endpoint Copy Banner -->
    <div class="card fade-in mb-4" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.08) 100%); border: 1px solid rgba(99, 102, 241, 0.2);">
        <div class="card-body">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div style="flex: 1; min-width: 280px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 18px;">🔗</span>
                        <h4 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin: 0;">Payload URL Webhook GitHub</h4>
                    </div>
                    <p style="font-size: 13px; color: var(--text-secondary); margin: 6px 0 0 0;">
                        Salin URL ini dan tempelkan pada menu <strong>Settings &gt; Webhooks &gt; Add webhook</strong> di repositori GitHub Anda.
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; width: 100%; max-width: 520px;">
                    <input type="text" id="webhook_url_input" class="form-control" value="<?= e($webhookUrl) ?>" readonly style="font-family: monospace; font-size: 13px; background: var(--bg-surface); font-weight: 600; color: var(--primary-500);">
                    <button type="button" class="btn btn-primary" onclick="copyWebhookUrl()" style="white-space: nowrap; display: flex; align-items: center; gap: 6px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span id="copy_btn_text">Salin URL</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Configuration Form -->
    <div class="card fade-in mb-4">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title">⚙️ Konfigurasi GitHub Webhook & Auto Deploy</h3>
                    <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">
                        Atur nama repositori GitHub, Secret Key untuk verifikasi keamanan HMAC SHA-256, dan opsi auto git pull.
                    </p>
                </div>
                <span class="badge <?= $isWebhookActive ? 'badge-success' : 'badge-secondary' ?>">
                    <?= $isWebhookActive ? 'Enabled' : 'Disabled' ?>
                </span>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('settings/github/update') ?>">
                <?= CSRF::field() ?>

                <!-- Master Toggle -->
                <div class="flex items-center justify-between mb-4 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                    <div>
                        <div style="font-weight: 700; font-size: 14px;">Aktifkan Penerima Webhook GitHub</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Izinkan sistem menerima payload event (push / ping) dari GitHub</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="github_webhook_enabled" value="1" <?= $isWebhookActive ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="grid-2 mb-3">
                    <!-- Repo Name -->
                    <div class="form-group">
                        <label class="form-label" for="github_repo_name">
                            Nama Repositori GitHub
                        </label>
                        <input type="text" id="github_repo_name" name="github_repo_name" class="form-control"
                               value="<?= e($settings['github_repo_name'] ?? '') ?>"
                               placeholder="contoh: username/nama-repo atau nama-repo">
                        <small style="color: var(--text-muted); font-size: 11px;">
                            Bisa diisi <code>username/repository</code> atau nama repo saja. Kosongkan jika ingin menerima dari semua repo yang memiliki secret valid.
                        </small>
                    </div>

                    <!-- Target Branch -->
                    <div class="form-group">
                        <label class="form-label" for="github_webhook_branch">
                            Target Branch untuk Auto-Deploy <span class="required">*</span>
                        </label>
                        <input type="text" id="github_webhook_branch" name="github_webhook_branch" class="form-control"
                               value="<?= e($settings['github_webhook_branch'] ?? 'main') ?>"
                               placeholder="main atau master" required>
                        <small style="color: var(--text-muted); font-size: 11px;">
                            Hanya event push ke branch ini yang akan memicu eksekusi <code>git pull</code>.
                        </small>
                    </div>
                </div>

                <!-- Webhook Secret -->
                <div class="form-group mb-3">
                    <label class="form-label" for="github_webhook_secret">
                        Webhook Secret (HMAC SHA-256)
                    </label>
                    <div style="display: flex; gap: 8px;">
                        <div style="position: relative; flex: 1;">
                            <input type="password" id="github_webhook_secret" name="github_webhook_secret" class="form-control"
                                   value="<?= e($settings['github_webhook_secret'] ?? '') ?>"
                                   placeholder="Masukkan secret key yang sama persis dengan yang diisi di GitHub"
                                   style="padding-right: 40px; font-family: monospace;">
                            <button type="button" onclick="toggleSecretVisibility()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-tertiary);" title="Lihat/Sembunyikan Secret">
                                <svg id="eye_icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="generateRandomSecret()" title="Generate Secret Baru Acak" style="white-space: nowrap;">
                            🎲 Acak Secret
                        </button>
                    </div>
                    <small style="color: var(--text-muted); font-size: 11px;">
                        Kunci rahasia untuk memvalidasi bahwa webhook benar-benar dikirim dari GitHub resmi.
                    </small>
                </div>

                <!-- Auto Git Pull Switch -->
                <div class="flex items-center justify-between mb-3 p-3" style="background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                    <div>
                        <div style="font-weight: 700; font-size: 14px;">⚡ Jalankan Otomatis "git pull" (Auto-Deploy)</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Sistem akan langsung mengeksekusi <code>git pull origin [branch]</code> di server setiap kali Anda push commit baru</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="github_webhook_auto_pull" value="1" <?= ($settings['github_webhook_auto_pull'] ?? '1') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <!-- Optional Custom Command -->
                <div class="form-group mb-4">
                    <label class="form-label" for="github_webhook_custom_command">
                        Perintah Tambahan Pasca-Deploy (Opsional)
                    </label>
                    <input type="text" id="github_webhook_custom_command" name="github_webhook_custom_command" class="form-control"
                           value="<?= e($settings['github_webhook_custom_command'] ?? '') ?>"
                           placeholder="contoh: composer dump-autoload -o"
                           style="font-family: monospace;">
                    <small style="color: var(--text-muted); font-size: 11px;">
                        Perintah command line yang akan dijalankan setelah <code>git pull</code> selesai dengan sukses.
                    </small>
                </div>

                <div class="flex items-center justify-between pt-3" style="border-top: 1px solid var(--border-subtle);">
                    <button type="submit" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Simpan Pengaturan GitHub Webhook
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Git Environment & Manual Pull Panel -->
    <div class="grid-2 mb-4">
        <!-- Git Info -->
        <div class="card fade-in">
            <div class="card-header">
                <h3 class="card-title" style="font-size: 15px;">📁 Status Lingkungan Git Lokal</h3>
            </div>
            <div class="card-body" style="font-size: 13px;">
                <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="color: var(--text-secondary);">Binary Git:</span>
                    <span>
                        <?php if ($gitAvailable): ?>
                            <span class="badge badge-success">✓ Terpasang di PATH</span>
                        <?php else: ?>
                            <span class="badge badge-danger">✕ Tidak Ditemukan</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="color: var(--text-secondary);">Folder .git:</span>
                    <span>
                        <?php if ($isGitRepo): ?>
                            <span class="badge badge-success">✓ Repositori Git Valid</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Tidak ada folder .git</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="color: var(--text-secondary);">Branch Aktif Lokal:</span>
                    <strong style="font-family: monospace;"><?= e($gitInfo['current_branch'] ?? '-') ?></strong>
                </div>
                <div class="flex items-center justify-between py-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="color: var(--text-secondary);">Commit Terakhir:</span>
                    <span style="font-family: monospace; font-size: 12px; max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?= e($gitInfo['latest_commit'] ?? '-') ?>">
                        <?= e($gitInfo['latest_commit'] ?? '-') ?>
                    </span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span style="color: var(--text-secondary);">Remote Origin:</span>
                    <span style="font-family: monospace; font-size: 11px; max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?= e($gitInfo['remote_url'] ?? '-') ?>">
                        <?= e($gitInfo['remote_url'] ?? '-') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Manual Pull Trigger -->
        <div class="card fade-in flex flex-col justify-between">
            <div class="card-header">
                <h3 class="card-title" style="font-size: 15px;">🚀 Uji Coba Manual Git Pull</h3>
            </div>
            <div class="card-body">
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">
                    Anda dapat memicu eksekusi <code>git pull origin <?= e($settings['github_webhook_branch'] ?? 'main') ?></code> langsung dari dashboard admin untuk memastikan koneksi git server dan hak akses berjalan lancar.
                </p>
                <form method="POST" action="<?= url('settings/github/pull') ?>">
                    <?= CSRF::field() ?>
                    <button type="submit" class="btn btn-secondary w-full" style="display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 700;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"></polyline>
                            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                        </svg>
                        Tarik Kode Sekarang (Manual Git Pull)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Step-by-Step GitHub Setup Guide -->
    <div class="card fade-in mb-4" style="border-left: 4px solid var(--primary-500);">
        <div class="card-header">
            <h3 class="card-title" style="font-size: 15px;">📖 Panduan Langkah Demi Langkah di GitHub</h3>
        </div>
        <div class="card-body">
            <div class="flex flex-col gap-3" style="font-size: 13px; color: var(--text-secondary); line-height: 1.6;">
                <div class="flex gap-3 items-start">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary-500); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0;">1</div>
                    <div>Buka repositori Anda di GitHub, lalu klik tab <strong>Settings</strong> di bagian atas.</div>
                </div>
                <div class="flex gap-3 items-start">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary-500); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0;">2</div>
                    <div>Di menu sebelah kiri, pilih <strong>Webhooks</strong> lalu klik tombol <strong>Add webhook</strong> di kanan atas.</div>
                </div>
                <div class="flex gap-3 items-start">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary-500); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0;">3</div>
                    <div>
                        Isi form Webhook GitHub sebagai berikut:
                        <ul style="margin-top: 6px; padding-left: 20px; list-style-type: disc;">
                            <li><strong>Payload URL:</strong> <code><?= e($webhookUrl) ?></code></li>
                            <li><strong>Content type:</strong> Pilih <code>application/json</code> (Sangat Penting!)</li>
                            <li><strong>Secret:</strong> Isi sama persis dengan Webhook Secret di atas</li>
                            <li><strong>Which events would you like to trigger this webhook?</strong> Pilih <em>Just the push event</em></li>
                            <li><strong>Active:</strong> Centang pilihan ini (Aktif)</li>
                        </ul>
                    </div>
                </div>
                <div class="flex gap-3 items-start">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--primary-500); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0;">4</div>
                    <div>Klik tombol hijau <strong>Add webhook</strong> di GitHub. GitHub akan otomatis mengirimkan test ping event ke aplikasi Anda.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Webhook Delivery Logs Table -->
    <div class="card fade-in">
        <div class="card-header">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h3 class="card-title">📜 Riwayat Pengiriman &amp; Log Webhook</h3>
                    <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Daftar aktivitas webhook GitHub yang masuk ke server.</p>
                </div>
                <?php if (!empty($logsData['data'])): ?>
                    <form method="POST" action="<?= url('settings/github/clear-logs') ?>" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua riwayat log webhook?');">
                        <?= CSRF::field() ?>
                        <button type="submit" class="btn btn-sm btn-danger" style="display: flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            Bersihkan Log
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="p-3" style="background: var(--bg-subtle); border-bottom: 1px solid var(--border-subtle);">
            <form method="GET" action="<?= url('settings/github') ?>" class="flex gap-2 flex-wrap items-center">
                <input type="text" name="q" class="form-control form-control-sm" style="max-width: 260px;" placeholder="Cari pesan commit/sender..." value="<?= e($search ?? '') ?>">
                <select name="status" class="form-control form-control-sm" style="max-width: 160px;">
                    <option value="">Semua Status</option>
                    <option value="success" <?= ($statusFilter === 'success') ? 'selected' : '' ?>>Success</option>
                    <option value="failed" <?= ($statusFilter === 'failed') ? 'selected' : '' ?>>Failed</option>
                    <option value="ignored" <?= ($statusFilter === 'ignored') ? 'selected' : '' ?>>Ignored</option>
                    <option value="ping" <?= ($statusFilter === 'ping') ? 'selected' : '' ?>>Ping Test</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <?php if (!empty($search) || !empty($statusFilter)): ?>
                    <a href="<?= url('settings/github') ?>" class="btn btn-sm btn-secondary">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Waktu</th>
                            <th>Event / Pengirim</th>
                            <th>Branch &amp; Commit</th>
                            <th>Pesan Commit</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center; width: 100px;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logsData['data'])): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-tertiary);">
                                    <div style="font-size: 32px; margin-bottom: 8px;">📭</div>
                                    Belum ada riwayat webhook yang tercatat. Lakukan push atau uji coba ping dari GitHub!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logsData['data'] as $log): ?>
                                <tr>
                                    <td style="font-size: 12px; color: var(--text-secondary); white-space: nowrap;">
                                        <?= date('d/m/Y H:i:s', strtotime($log->created_at)) ?>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; font-size: 13px;"><?= e(strtoupper($log->event)) ?></div>
                                        <div style="font-size: 11px; color: var(--text-tertiary);">oleh: <?= e($log->sender ?: 'N/A') ?></div>
                                    </td>
                                    <td>
                                        <?php if (!empty($log->branch)): ?>
                                            <code style="font-size: 11px; background: var(--bg-subtle); padding: 1px 5px; border-radius: 4px;"><?= e($log->branch) ?></code>
                                        <?php endif; ?>
                                        <?php if (!empty($log->commit_id)): ?>
                                            <div style="font-size: 11px; color: var(--primary-500); font-family: monospace; margin-top: 2px;">#<?= e($log->commit_id) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-size: 13px; max-width: 260px;">
                                        <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= e($log->commit_message ?? '') ?>">
                                            <?= e($log->commit_message ?: '-') ?>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php if ($log->status === 'success'): ?>
                                            <span class="badge badge-success">Success</span>
                                        <?php elseif ($log->status === 'failed'): ?>
                                            <span class="badge badge-danger">Failed</span>
                                        <?php elseif ($log->status === 'ping'): ?>
                                            <span class="badge badge-info" style="background: #0284c7; color: white;">Ping</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?= e(ucfirst($log->status)) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <button type="button" class="btn btn-sm btn-secondary" onclick='showLogDetail(<?= json_encode($log, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' title="Lihat Output Terminal & Detail">
                                            🔍 Log
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (!empty($logsData['lastPage']) && $logsData['lastPage'] > 1): ?>
                <div class="flex items-center justify-between p-3" style="border-top: 1px solid var(--border-subtle);">
                    <div style="font-size: 12px; color: var(--text-secondary);">
                        Menampilkan halaman <?= $logsData['page'] ?> dari <?= $logsData['lastPage'] ?> (Total: <?= $logsData['total'] ?> logs)
                    </div>
                    <div class="flex gap-1">
                        <?php for ($i = 1; $i <= $logsData['lastPage']; $i++): ?>
                            <a href="<?= url('settings/github?page=' . $i . ($statusFilter ? '&status=' . urlencode($statusFilter) : '') . ($search ? '&q=' . urlencode($search) : '')) ?>" 
                               class="btn btn-sm <?= ($i === (int)$logsData['page']) ? 'btn-primary' : 'btn-secondary' ?>" style="min-width: 32px; text-align: center;">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Output Terminal Log Detail -->
<div class="modal-backdrop" id="log-detail-modal-backdrop" onclick="closeLogModal()"></div>
<div class="modal" id="log-detail-modal" style="max-width: 700px; width: 90%;">
    <div class="modal-header">
        <h3 class="modal-title" id="log-modal-title">Detail Eksekusi Webhook</h3>
        <button class="modal-close" onclick="closeLogModal()">&times;</button>
    </div>
    <div class="modal-body">
        <div class="grid-2 mb-3" style="font-size: 13px;">
            <div><strong>Waktu:</strong> <span id="modal-log-time">-</span></div>
            <div><strong>Status:</strong> <span id="modal-log-status">-</span></div>
            <div><strong>Event:</strong> <span id="modal-log-event">-</span></div>
            <div><strong>Pengirim:</strong> <span id="modal-log-sender">-</span></div>
            <div><strong>Repository:</strong> <span id="modal-log-repo">-</span></div>
            <div><strong>Branch &amp; Commit:</strong> <span id="modal-log-commit">-</span></div>
        </div>
        <div class="form-group mb-2">
            <label class="form-label" style="font-size: 12px;">Pesan Commit:</label>
            <div id="modal-log-message" style="background: var(--bg-subtle); padding: 8px 12px; border-radius: var(--radius-md); font-size: 13px; color: var(--text-primary); border: 1px solid var(--border-subtle);"></div>
        </div>
        <div class="form-group">
            <label class="form-label" style="font-size: 12px;">Output Terminal / Pesan Sistem:</label>
            <pre id="modal-log-output" style="background: #0f172a; color: #38bdf8; padding: 12px; border-radius: var(--radius-md); font-size: 12px; font-family: monospace; max-height: 240px; overflow-y: auto; white-space: pre-wrap; word-break: break-all; margin: 0;"></pre>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeLogModal()">Tutup</button>
    </div>
</div>

<script>
function copyWebhookUrl() {
    const input = document.getElementById('webhook_url_input');
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        const btnText = document.getElementById('copy_btn_text');
        const oldText = btnText.innerText;
        btnText.innerText = '✓ Tersalin!';
        setTimeout(() => {
            btnText.innerText = oldText;
        }, 2500);
    });
}

function toggleSecretVisibility() {
    const input = document.getElementById('github_webhook_secret');
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}

function generateRandomSecret() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+';
    let result = '';
    const length = 32;
    const array = new Uint32Array(length);
    window.crypto.getRandomValues(array);
    for (let i = 0; i < length; i++) {
        result += chars[array[i] % chars.length];
    }
    const input = document.getElementById('github_webhook_secret');
    input.type = 'text';
    input.value = result;
    alert('Secret acak 32 karakter berhasil dibuat! Jangan lupa klik "Simpan Pengaturan" dan salin secret ini ke repositori GitHub Anda.');
}

function showLogDetail(log) {
    document.getElementById('modal-log-time').innerText = log.created_at || '-';
    document.getElementById('modal-log-status').innerHTML = log.status === 'success' 
        ? '<span class="badge badge-success">Success</span>' 
        : (log.status === 'failed' ? '<span class="badge badge-danger">Failed</span>' : '<span class="badge badge-secondary">' + log.status + '</span>');
    document.getElementById('modal-log-event').innerText = (log.event || '-').toUpperCase();
    document.getElementById('modal-log-sender').innerText = log.sender || '-';
    document.getElementById('modal-log-repo').innerText = log.repository || '-';
    document.getElementById('modal-log-commit').innerText = (log.branch || '-') + (log.commit_id ? ' (#' + log.commit_id + ')' : '');
    document.getElementById('modal-log-message').innerText = log.commit_message || 'Tidak ada pesan commit.';
    document.getElementById('modal-log-output').innerText = log.output || 'Tidak ada output terminal yang dicatat.';

    document.getElementById('log-detail-modal').classList.add('active');
    document.getElementById('log-detail-modal-backdrop').classList.add('active');
}

function closeLogModal() {
    document.getElementById('log-detail-modal').classList.remove('active');
    document.getElementById('log-detail-modal-backdrop').classList.remove('active');
}
</script>
