<?php use App\Core\CSRF; ?>

<div class="bento-grid">
    <!-- 1. Header Bento Hero Card -->
    <div class="bento-col-12 bento-hero fade-in" style="background: #ffffff; border: 1px solid var(--border-subtle);">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: var(--primary-50); color: var(--primary-700); border: 1px solid var(--primary-200);">
                ⚙️
            </div>
            <div>
                <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-primary);">
                    Pengaturan Sistem &amp; Preferensi
                </h2>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Konfigurasikan parameter umum instansi, format penerbitan dokumen resmi, dan kuota penyimpanan.
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Settings Form Bento Card -->
    <div class="bento-col-12 bento-card fade-in" style="max-width: 860px; padding: 24px 28px;">
        <form method="POST" action="<?= url('settings/update') ?>">
            <?= CSRF::field() ?>

            <!-- General Settings Section -->
            <div style="margin-bottom: 24px;">
                <div class="flex items-center gap-2 mb-3 pb-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="font-size: 16px;">🏢</span>
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0;">Pengaturan Umum Instansi</h3>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label font-semibold" for="app_name" style="font-size: 13px; color: #334155;">Nama Aplikasi / Portal Instansi</label>
                    <input type="text" id="app_name" name="app_name" class="form-control"
                           value="<?= e($settings['app_name'] ?? 'ASR FORM') ?>" required>
                </div>

                <div class="grid-2 gap-3">
                    <div class="form-group">
                        <label class="form-label font-semibold" for="app_timezone" style="font-size: 13px; color: #334155;">Zona Waktu (Timezone)</label>
                        <select id="app_timezone" name="app_timezone" class="form-control">
                            <option value="Asia/Jakarta" <?= ($settings['app_timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' ?>>Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar" <?= ($settings['app_timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' ?>>Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura" <?= ($settings['app_timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' ?>>Asia/Jayapura (WIT)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label font-semibold" for="app_date_format" style="font-size: 13px; color: #334155;">Format Penulisan Tanggal</label>
                        <select id="app_date_format" name="app_date_format" class="form-control">
                            <option value="d/m/Y" <?= ($settings['app_date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY (Contoh: 22/08/2026)</option>
                            <option value="Y-m-d" <?= ($settings['app_date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD (Standar ISO)</option>
                            <option value="d-m-Y" <?= ($settings['app_date_format'] ?? '') === 'd-m-Y' ? 'selected' : '' ?>>DD-MM-YYYY</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Document Generation Section -->
            <div style="margin-bottom: 24px;">
                <div class="flex items-center gap-2 mb-3 pb-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="font-size: 16px;">📄</span>
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0;">Konfigurasi Dokumen &amp; Cetak</h3>
                </div>

                <div class="grid-2 gap-3">
                    <div class="form-group">
                        <label class="form-label font-semibold" for="doc_paper_size" style="font-size: 13px; color: #334155;">Ukuran Kertas Standar</label>
                        <select id="doc_paper_size" name="doc_paper_size" class="form-control">
                            <option value="A4" <?= ($settings['doc_paper_size'] ?? 'A4') === 'A4' ? 'selected' : '' ?>>A4 (210 x 297 mm) — Standar Instansi</option>
                            <option value="F4" <?= ($settings['doc_paper_size'] ?? '') === 'F4' ? 'selected' : '' ?>>F4 / Folio (215 x 330 mm)</option>
                            <option value="letter" <?= ($settings['doc_paper_size'] ?? '') === 'letter' ? 'selected' : '' ?>>Letter</option>
                            <option value="legal" <?= ($settings['doc_paper_size'] ?? '') === 'legal' ? 'selected' : '' ?>>Legal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label font-semibold" for="doc_default_font" style="font-size: 13px; color: #334155;">Font Resmi Default</label>
                        <select id="doc_default_font" name="doc_default_font" class="form-control">
                            <option value="Times New Roman" <?= ($settings['doc_default_font'] ?? '') === 'Times New Roman' ? 'selected' : '' ?>>Times New Roman (Standar Surat Dinas)</option>
                            <option value="Arial" <?= ($settings['doc_default_font'] ?? '') === 'Arial' ? 'selected' : '' ?>>Arial (Modern Sans)</option>
                            <option value="Calibri" <?= ($settings['doc_default_font'] ?? '') === 'Calibri' ? 'selected' : '' ?>>Calibri</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Storage & Upload Limits Section -->
            <div style="margin-bottom: 24px;">
                <div class="flex items-center gap-2 mb-3 pb-2" style="border-bottom: 1px solid var(--border-subtle);">
                    <span style="font-size: 16px;">💾</span>
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0;">Penyimpanan &amp; Batas Upload</h3>
                </div>

                <div class="grid-2 gap-3">
                    <div class="form-group">
                        <label class="form-label font-semibold" for="upload_max_size" style="font-size: 13px; color: #334155;">Maksimal Ukuran Upload Berkas (MB)</label>
                        <input type="number" id="upload_max_size" name="upload_max_size" class="form-control"
                               value="<?= e($settings['upload_max_size'] ?? '5') ?>" min="1" max="50">
                    </div>
                    <div class="form-group">
                        <label class="form-label font-semibold" for="upload_extensions" style="font-size: 13px; color: #334155;">Ekstensi File Diizinkan</label>
                        <input type="text" id="upload_extensions" name="upload_extensions" class="form-control"
                               value="<?= e($settings['upload_extensions'] ?? 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx') ?>"
                               placeholder="jpg,png,pdf,docx,...">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-3" style="border-top: 1px solid var(--border-subtle);">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-weight: 700; box-shadow: 0 4px 14px rgba(79,70,229,0.25);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Simpan Perubahan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
