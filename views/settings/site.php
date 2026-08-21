<?php use App\Core\CSRF; ?>

<div style="max-width: 720px;">
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">Pengaturan Situs</h3>
            <p style="font-size: 13px; color: var(--text-secondary); margin-top: 4px;">Konfigurasi informasi situs, SEO, dan kontak yang ditampilkan di halaman publik.</p>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('settings/site/update') ?>">
                <?= CSRF::field() ?>

                <!-- Identitas Situs -->
                <h4 style="font-size: 15px; font-weight: 600; color: var(--primary-400); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border-subtle);">
                    🌐 Identitas Situs
                </h4>

                <div class="form-group">
                    <label class="form-label" for="site_name">Nama Situs</label>
                    <input type="text" id="site_name" name="site_name" class="form-control"
                           value="<?= e($settings['site_name'] ?? 'ASR FORM') ?>" placeholder="ASR FORM">
                </div>

                <div class="form-group">
                    <label class="form-label" for="site_tagline">Tagline</label>
                    <input type="text" id="site_tagline" name="site_tagline" class="form-control"
                           value="<?= e($settings['site_tagline'] ?? '') ?>" placeholder="Platform Form Builder & Document Generator">
                </div>

                <div class="form-group">
                    <label class="form-label" for="site_description">Deskripsi (SEO Meta Description)</label>
                    <textarea id="site_description" name="site_description" class="form-control" rows="3" placeholder="Deskripsi singkat situs untuk mesin pencari..."><?= e($settings['site_description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="site_url">URL Situs (untuk Sitemap & Canonical)</label>
                    <input type="url" id="site_url" name="site_url" class="form-control"
                           value="<?= e($settings['site_url'] ?? env('APP_URL', '')) ?>" placeholder="https://example.com/form">
                    <small style="color: var(--text-muted); font-size: 12px;">Kosongkan untuk menggunakan APP_URL dari .env</small>
                </div>

                <!-- Kontak -->
                <h4 style="font-size: 15px; font-weight: 600; color: var(--primary-400); margin: 28px 0 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border-subtle);">
                    📬 Informasi Kontak
                </h4>

                <div class="form-group">
                    <label class="form-label" for="site_contact_email">Email Kontak</label>
                    <input type="email" id="site_contact_email" name="site_contact_email" class="form-control"
                           value="<?= e($settings['site_contact_email'] ?? '') ?>" placeholder="admin@example.com">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="site_contact_phone">Telepon</label>
                        <input type="text" id="site_contact_phone" name="site_contact_phone" class="form-control"
                               value="<?= e($settings['site_contact_phone'] ?? '') ?>" placeholder="+62 812 xxxx xxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="site_contact_address">Alamat</label>
                        <input type="text" id="site_contact_address" name="site_contact_address" class="form-control"
                               value="<?= e($settings['site_contact_address'] ?? '') ?>" placeholder="Jl. Contoh No. 123">
                    </div>
                </div>

                <!-- Footer & SEO -->
                <h4 style="font-size: 15px; font-weight: 600; color: var(--primary-400); margin: 28px 0 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border-subtle);">
                    🔧 Footer & SEO
                </h4>

                <div class="form-group">
                    <label class="form-label" for="site_footer_text">Teks Footer</label>
                    <input type="text" id="site_footer_text" name="site_footer_text" class="form-control"
                           value="<?= e($settings['site_footer_text'] ?? '') ?>" placeholder="© 2026 ASR FORM. All rights reserved.">
                </div>

                <div class="form-group">
                    <label class="form-label" for="site_og_image">Open Graph Image URL</label>
                    <input type="url" id="site_og_image" name="site_og_image" class="form-control"
                           value="<?= e($settings['site_og_image'] ?? '') ?>" placeholder="https://example.com/og-image.jpg">
                    <small style="color: var(--text-muted); font-size: 12px;">Gambar yang ditampilkan saat link dibagikan di media sosial (minimal 1200x630px)</small>
                </div>

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
</div>
