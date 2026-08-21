<?php use App\Core\CSRF; ?>

<div style="max-width: 720px;">
    <div class="card fade-in">
        <div class="card-header">
            <h3 class="card-title">Pengaturan Aplikasi</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('settings/update') ?>">
                <?= CSRF::field() ?>

                <!-- General -->
                <h4 style="font-size: 15px; font-weight: 600; color: var(--primary-400); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border-subtle);">
                    🏢 Umum
                </h4>

                <div class="form-group">
                    <label class="form-label" for="app_name">Nama Aplikasi</label>
                    <input type="text" id="app_name" name="app_name" class="form-control"
                           value="<?= e($settings['app_name'] ?? 'ASR FORM') ?>">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="app_timezone">Timezone</label>
                        <select id="app_timezone" name="app_timezone" class="form-control">
                            <option value="Asia/Jakarta" <?= ($settings['app_timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' ?>>Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar" <?= ($settings['app_timezone'] ?? '') === 'Asia/Makassar' ? 'selected' : '' ?>>Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura" <?= ($settings['app_timezone'] ?? '') === 'Asia/Jayapura' ? 'selected' : '' ?>>Asia/Jayapura (WIT)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="app_date_format">Format Tanggal</label>
                        <select id="app_date_format" name="app_date_format" class="form-control">
                            <option value="d/m/Y" <?= ($settings['app_date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                            <option value="Y-m-d" <?= ($settings['app_date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                            <option value="d-m-Y" <?= ($settings['app_date_format'] ?? '') === 'd-m-Y' ? 'selected' : '' ?>>DD-MM-YYYY</option>
                        </select>
                    </div>
                </div>

                <!-- Document -->
                <h4 style="font-size: 15px; font-weight: 600; color: var(--primary-400); margin: 28px 0 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border-subtle);">
                    📄 Dokumen
                </h4>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="doc_paper_size">Ukuran Kertas</label>
                        <select id="doc_paper_size" name="doc_paper_size" class="form-control">
                            <option value="A4" <?= ($settings['doc_paper_size'] ?? 'A4') === 'A4' ? 'selected' : '' ?>>A4</option>
                            <option value="letter" <?= ($settings['doc_paper_size'] ?? '') === 'letter' ? 'selected' : '' ?>>Letter</option>
                            <option value="legal" <?= ($settings['doc_paper_size'] ?? '') === 'legal' ? 'selected' : '' ?>>Legal</option>
                            <option value="F4" <?= ($settings['doc_paper_size'] ?? '') === 'F4' ? 'selected' : '' ?>>F4 / Folio</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="doc_default_font">Font Default</label>
                        <select id="doc_default_font" name="doc_default_font" class="form-control">
                            <option value="Times New Roman" <?= ($settings['doc_default_font'] ?? '') === 'Times New Roman' ? 'selected' : '' ?>>Times New Roman</option>
                            <option value="Arial" <?= ($settings['doc_default_font'] ?? '') === 'Arial' ? 'selected' : '' ?>>Arial</option>
                            <option value="Calibri" <?= ($settings['doc_default_font'] ?? '') === 'Calibri' ? 'selected' : '' ?>>Calibri</option>
                        </select>
                    </div>
                </div>

                <!-- Storage -->
                <h4 style="font-size: 15px; font-weight: 600; color: var(--primary-400); margin: 28px 0 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border-subtle);">
                    💾 Penyimpanan
                </h4>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="upload_max_size">Max Upload (MB)</label>
                        <input type="number" id="upload_max_size" name="upload_max_size" class="form-control"
                               value="<?= e($settings['upload_max_size'] ?? '5') ?>" min="1" max="50">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="upload_extensions">Ekstensi Diizinkan</label>
                        <input type="text" id="upload_extensions" name="upload_extensions" class="form-control"
                               value="<?= e($settings['upload_extensions'] ?? 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx') ?>"
                               placeholder="jpg,png,pdf,...">
                    </div>
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
