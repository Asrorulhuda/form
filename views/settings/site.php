<?php use App\Core\CSRF; ?>

<div class="bento-grid">
    <!-- 1. Header Bento Hero Card -->
    <div class="bento-col-12 bento-hero fade-in" style="background: #ffffff; border: 1px solid var(--border-subtle);">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                🌐
            </div>
            <div>
                <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-primary);">
                    Pengaturan Identitas &amp; SEO Situs
                </h2>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Konfigurasi informasi publik situs, metadata SEO Open Graph, dan kontak layanan pengguna.
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Site Settings Form Bento Card -->
    <div class="bento-col-12 bento-card fade-in" style="max-width: 860px; padding: 24px 28px;">
        <form method="POST" action="<?= url('settings/site/update') ?>">
            <?= CSRF::field() ?>

            <!-- Identitas Situs -->
            <div style="margin-bottom: 24px;">
                <div class="flex items-center gap-2 mb-3 pb-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="font-size: 16px;">🌐</span>
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0;">Identitas Situs &amp; Branding</h3>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="site_name" style="font-size: 13px; color: #334155;">Nama Situs</label>
                    <input type="text" id="site_name" name="site_name" class="form-control"
                           value="<?= e($settings['site_name'] ?? 'ASR FORM') ?>" placeholder="ASR FORM" required>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="site_tagline" style="font-size: 13px; color: #334155;">Tagline / Slogan</label>
                    <input type="text" id="site_tagline" name="site_tagline" class="form-control"
                           value="<?= e($settings['site_tagline'] ?? '') ?>" placeholder="Platform Form Builder & Document Generator">
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="site_description" style="font-size: 13px; color: #334155;">Deskripsi (SEO Meta Description)</label>
                    <textarea id="site_description" name="site_description" class="form-control" rows="3" placeholder="Deskripsi singkat situs untuk mesin pencari..."><?= e($settings['site_description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label font-semibold" for="site_url" style="font-size: 13px; color: #334155;">URL Situs (Canonical &amp; Sitemap)</label>
                    <input type="url" id="site_url" name="site_url" class="form-control"
                           value="<?= e($settings['site_url'] ?? env('APP_URL', '')) ?>" placeholder="https://example.com/form">
                    <small style="color: var(--text-muted); font-size: 12px;">Kosongkan untuk otomatis menggunakan domain aktif.</small>
                </div>
            </div>

            <!-- Kontak -->
            <div style="margin-bottom: 24px;">
                <div class="flex items-center gap-2 mb-3 pb-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="font-size: 16px;">📬</span>
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0;">Informasi Kontak &amp; Alamat</h3>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="site_contact_email" style="font-size: 13px; color: #334155;">Email Kontak Resmi</label>
                    <input type="email" id="site_contact_email" name="site_contact_email" class="form-control"
                           value="<?= e($settings['site_contact_email'] ?? '') ?>" placeholder="support@instansi.go.id">
                </div>

                <div class="grid-2 gap-3">
                    <div class="form-group">
                        <label class="form-label font-semibold" for="site_contact_phone" style="font-size: 13px; color: #334155;">No. Telepon / WhatsApp</label>
                        <input type="text" id="site_contact_phone" name="site_contact_phone" class="form-control"
                               value="<?= e($settings['site_contact_phone'] ?? '') ?>" placeholder="+62 812 xxxx xxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label font-semibold" for="site_contact_address" style="font-size: 13px; color: #334155;">Alamat Kantor</label>
                        <input type="text" id="site_contact_address" name="site_contact_address" class="form-control"
                               value="<?= e($settings['site_contact_address'] ?? '') ?>" placeholder="Jl. Sudirman No. 1, Jakarta">
                    </div>
                </div>
            </div>

            <!-- Footer & Social Sharing -->
            <div style="margin-bottom: 24px;">
                <div class="flex items-center gap-2 mb-3 pb-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="font-size: 16px;">🔧</span>
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0;">Footer &amp; Media Sosial</h3>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="site_footer_text" style="font-size: 13px; color: #334155;">Teks Hak Cipta (Footer)</label>
                    <input type="text" id="site_footer_text" name="site_footer_text" class="form-control"
                           value="<?= e($settings['site_footer_text'] ?? '') ?>" placeholder="© 2026 ASR FORM. All rights reserved.">
                </div>

                <div class="form-group">
                    <label class="form-label font-semibold" for="site_og_image" style="font-size: 13px; color: #334155;">Open Graph Banner URL</label>
                    <input type="url" id="site_og_image" name="site_og_image" class="form-control"
                           value="<?= e($settings['site_og_image'] ?? '') ?>" placeholder="https://example.com/og-banner.jpg">
                    <small style="color: var(--text-muted); font-size: 12px;">Gambar preview ketika link portal dibagikan ke WhatsApp, LinkedIn, &amp; Twitter (1200x630px).</small>
                </div>
            </div>

            <div class="flex gap-3 pt-3" style="border-top: 1px solid var(--border-subtle);">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-weight: 700; box-shadow: 0 4px 14px rgba(79,70,229,0.25);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Simpan Pengaturan Situs
                </button>
            </div>
        </form>
    </div>
</div>
