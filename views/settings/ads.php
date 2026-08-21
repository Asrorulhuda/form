<?php use App\Core\CSRF; ?>

<div style="max-width: 720px;">
    <div class="card fade-in">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="card-title">Iklan & AdSense</h3>
                    <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Kelola konfigurasi Google AdSense dan penempatan iklan.</p>
                </div>
                <span class="badge badge-<?= $statusColor ?>" style="font-size: 12px; padding: 6px 12px;">
                    <?= e($statusLabel) ?>
                </span>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('settings/ads/update') ?>">
                <?= CSRF::field() ?>

                <!-- Master Toggle -->
                <h4 style="font-size: 15px; font-weight: 600; color: var(--primary-400); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border-subtle);">
                    📡 Google AdSense
                </h4>

                <div class="flex items-center gap-3 mb-4" style="padding: 16px; background: var(--bg-subtle); border-radius: var(--radius-lg); border: 1px solid var(--border-subtle);">
                    <label class="toggle-switch">
                        <input type="checkbox" name="adsense_enabled" value="1" <?= ($settings['adsense_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                    <div>
                        <div style="font-size: 14px; font-weight: 700;">Aktifkan AdSense</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Master switch — semua iklan dinonaktifkan jika OFF</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="adsense_publisher_id">Publisher ID</label>
                    <input type="text" id="adsense_publisher_id" name="adsense_publisher_id" class="form-control"
                           value="<?= e($settings['adsense_publisher_id'] ?? '') ?>"
                           placeholder="ca-pub-XXXXXXXXXXXXXXXX"
                           pattern="^$|^ca-pub-\d+$"
                           style="font-family: monospace;">
                    <small style="color: var(--text-muted); font-size: 12px;">
                        Publisher ID dari akun Google AdSense Anda. Format: ca-pub-XXXXXXXXXXXXXXXX
                    </small>
                </div>

                <div class="flex items-center gap-3 mb-4">
                    <label class="toggle-switch">
                        <input type="checkbox" name="adsense_auto_ads" value="1" <?= ($settings['adsense_auto_ads'] ?? '0') === '1' ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                    <div>
                        <div style="font-size: 14px; font-weight: 600;">Auto Ads</div>
                        <div style="font-size: 12px; color: var(--text-muted);">Biarkan Google otomatis menempatkan iklan (opsional)</div>
                    </div>
                </div>

                <!-- Placement Toggles -->
                <h4 style="font-size: 15px; font-weight: 600; color: var(--primary-400); margin: 28px 0 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border-subtle);">
                    📍 Penempatan Iklan
                </h4>

                <?php
                $placements = [
                    ['key' => 'adsense_form_top', 'label' => 'Form Top', 'desc' => 'Bagian atas form publik (sebelum field)'],
                    ['key' => 'adsense_form_bottom', 'label' => 'Form Bottom', 'desc' => 'Bagian bawah form (setelah tombol kirim)'],
                    ['key' => 'adsense_form_success', 'label' => 'Submission Success', 'desc' => 'Halaman sukses setelah submit'],
                    ['key' => 'adsense_public_page', 'label' => 'Public Pages', 'desc' => 'Halaman publik (landing, about, features, dll)'],
                    ['key' => 'adsense_dashboard', 'label' => 'Dashboard', 'desc' => 'Dashboard pengguna yang login'],
                ];
                foreach ($placements as $p):
                ?>
                    <div class="flex items-center justify-between mb-3" style="padding: 12px 16px; background: var(--bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                        <div>
                            <div style="font-size: 14px; font-weight: 600;"><?= $p['label'] ?></div>
                            <div style="font-size: 12px; color: var(--text-muted);"><?= $p['desc'] ?></div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="<?= $p['key'] ?>" value="1" <?= ($settings[$p['key']] ?? '0') === '1' ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                <?php endforeach; ?>

                <div class="flex gap-3 mt-4" style="padding-top: 16px; border-top: 1px solid var(--border-subtle);">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ad Slots Table -->
    <div class="card fade-in mt-4">
        <div class="card-header">
            <h3 class="card-title">Ad Slots</h3>
            <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Kelola slot iklan individual. Setiap slot dapat diaktifkan atau dinonaktifkan secara terpisah.</p>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Slot</th>
                            <th>Key</th>
                            <th>Deskripsi</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adSlots as $slot): ?>
                            <tr>
                                <td><strong><?= e($slot->name) ?></strong></td>
                                <td><code style="font-size: 12px; color: var(--primary-600);"><?= e($slot->slot_key) ?></code></td>
                                <td style="font-size: 13px; color: var(--text-secondary);"><?= e($slot->description ?? '-') ?></td>
                                <td style="text-align: center;">
                                    <?php if ((int) $slot->enabled): ?>
                                        <span class="badge badge-success">ON</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">OFF</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <form method="POST" action="<?= url('settings/ads/slots/' . $slot->id . '/toggle') ?>" style="display: inline;">
                                        <?= CSRF::field() ?>
                                        <button type="submit" class="btn btn-sm <?= (int) $slot->enabled ? 'btn-secondary' : 'btn-primary' ?>" title="Toggle">
                                            <?= (int) $slot->enabled ? 'Nonaktifkan' : 'Aktifkan' ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="card fade-in mt-4" style="border-left: 4px solid var(--primary-400);">
        <div class="card-body">
            <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">ℹ️ Informasi Penting</h4>
            <ul style="font-size: 13px; color: var(--text-secondary); line-height: 1.8; padding-left: 18px;">
                <li>Iklan hanya ditampilkan jika <strong>AdSense aktif</strong>, <strong>Publisher ID terisi</strong>, <strong>penempatan aktif</strong>, dan <strong>slot aktif</strong>.</li>
                <li>Formulir publik tetap berfungsi normal meskipun AdSense tidak dikonfigurasi.</li>
                <li>Publisher ID yang digunakan harus dari akun Google AdSense yang sudah disetujui.</li>
                <li>Iklan tidak ditempatkan di antara field formulir untuk menjaga pengalaman pengguna.</li>
            </ul>
        </div>
    </div>
</div>
