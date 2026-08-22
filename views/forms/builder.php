<?php
use App\Core\Auth;
use App\Core\CSRF;

$publicUrl = url("form/{$form->slug}");
?>

<!-- ─── Builder Top Bar ─── -->
<div class="card mb-4" style="background: #ffffff; border-bottom: 2px solid var(--border-subtle);">
    <div class="card-body" style="padding: 14px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div class="flex items-center gap-3">
            <a href="<?= url('forms') ?>" class="btn btn-secondary btn-sm" title="Kembali ke Daftar Form">
                &larr; Keluar
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">
                        <?= e($form->title) ?>
                    </h1>
                    <span class="badge badge-<?= $form->status === 'published' ? 'success' : 'muted' ?>" style="font-size: 11px;">
                        <?= ucfirst($form->status) ?>
                    </span>
                </div>
                <div class="text-sm text-muted" style="font-size: 12px;">
                    Link Publik: <a href="<?= $publicUrl ?>" target="_blank" style="color: var(--primary-600); font-weight: 600;"><?= $publicUrl ?></a>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Share Button -->
            <button type="button" class="btn btn-secondary btn-sm" onclick="openShareModal()">
                🔗 Bagikan Link
            </button>
            <a href="<?= $publicUrl ?>" target="_blank" class="btn btn-secondary btn-sm">
                👁️ Preview Form
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="saveBuilder()" id="btn-save-builder">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <span id="btn-save-text">Simpan Perubahan</span>
            </button>
        </div>
    </div>

    <!-- Builder Navigation Tabs -->
    <div style="display: flex; gap: 4px; padding: 0 24px; border-top: 1px solid var(--border-subtle); background: #f8fafc;">
        <button type="button" class="tab-btn active" id="tab-btn-fields" onclick="switchBuilderTab('fields')">
            🎨 1. Desain Pertanyaan Formulir
        </button>
        <button type="button" class="tab-btn" id="tab-btn-doc" onclick="switchBuilderTab('doc')">
            📄 2. Template Surat Word Terhubung
            <span class="badge badge-<?= $form->template_id ? 'success' : 'muted' ?>" id="doc-status-badge" style="margin-left: 6px; font-size: 10px;">
                <?= $form->template_id ? 'Terhubung' : 'Opsional' ?>
            </span>
        </button>
    </div>
</div>

<!-- ─── Mobile Builder Segmented Switcher (Visible on Mobile/Tablet <= 992px) ─── -->
<div class="builder-mobile-nav" id="builder-mobile-nav">
    <button type="button" class="builder-mobile-tab active" id="mob-tab-canvas" onclick="switchMobileBuilderPanel('canvas')">
        <span class="tab-icon">📝</span>
        <span class="tab-text">Pertanyaan (<span id="mob-field-count">0</span>)</span>
    </button>
    <button type="button" class="builder-mobile-tab" id="mob-tab-palette" onclick="switchMobileBuilderPanel('palette')">
        <span class="tab-icon">➕</span>
        <span class="tab-text">Tambah Field</span>
    </button>
</div>

<!-- ─── TAB 1: FORM FIELDS BUILDER ─── -->
<div id="builder-tab-fields" class="builder-grid">
    
    <!-- ─── LEFT: Palette Field Types ─── -->
    <div class="card builder-panel builder-palette-panel" id="builder-panel-palette">
        <div class="card-header" style="padding: 14px 16px; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-secondary); margin: 0;">
                ➕ Tambah Pertanyaan
            </h3>
            <button type="button" class="btn btn-secondary btn-sm mobile-only-btn" onclick="switchMobileBuilderPanel('canvas')" style="display: none;">
                &larr; Selesai
            </button>
        </div>
        <div class="card-body builder-palette-body" style="padding: 12px; display: flex; flex-direction: column; gap: 6px;">
            <div class="palette-section-title">Input Teks</div>
            <div class="palette-group-grid">
                <button type="button" class="palette-item" onclick="addField('text')">📝 Teks Singkat</button>
                <button type="button" class="palette-item" onclick="addField('textarea')">📄 Paragraf Panjang</button>
                <button type="button" class="palette-item" onclick="addField('number')">🔢 Angka / Nilai</button>
                <button type="button" class="palette-item" onclick="addField('email')">✉️ Alamat Email</button>
                <button type="button" class="palette-item" onclick="addField('phone')">📞 No. WhatsApp</button>
            </div>

            <div class="palette-section-title" style="margin-top: 6px;">Pilihan & Opsi</div>
            <div class="palette-group-grid">
                <button type="button" class="palette-item" onclick="addField('dropdown')">🔻 Dropdown</button>
                <button type="button" class="palette-item" onclick="addField('radio')">🔘 Pilihan Tunggal</button>
                <button type="button" class="palette-item" onclick="addField('checkbox')">☑️ Kotak Centang</button>
            </div>

            <div class="palette-section-title" style="margin-top: 6px;">Tanggal & Waktu</div>
            <div class="palette-group-grid">
                <button type="button" class="palette-item" onclick="addField('date')">📅 Pemilih Tanggal</button>
                <button type="button" class="palette-item" onclick="addField('time')">⏰ Pemilih Jam</button>
            </div>

            <div class="palette-section-title" style="margin-top: 6px;">Media & Tanda Tangan</div>
            <div class="palette-group-grid">
                <button type="button" class="palette-item" onclick="addField('signature')">✍️ Tanda Tangan</button>
                <button type="button" class="palette-item" onclick="addField('file')">📎 Unggah Dokumen</button>
                <button type="button" class="palette-item" onclick="addField('image')">🖼️ Unggah Foto</button>
            </div>

            <div class="palette-section-title" style="margin-top: 6px;">Struktur & Judul</div>
            <div class="palette-group-grid">
                <button type="button" class="palette-item" onclick="addField('heading')">🏷️ Judul Bagian</button>
                <button type="button" class="palette-item" onclick="addField('description')">ℹ️ Keterangan</button>
            </div>
        </div>
    </div>

    <!-- ─── CENTER: Form Canvas ─── -->
    <div class="builder-panel builder-canvas-panel" id="builder-panel-canvas" style="display: flex; flex-direction: column; gap: 16px;">
        <!-- Header Card -->
        <div class="card" style="border-top: 4px solid var(--primary-600); box-shadow: var(--shadow-md);">
            <div class="card-body" style="padding: 20px 24px;">
                <div class="form-group mb-3">
                    <input type="text" id="form-title-input" class="form-control" style="font-size: 18px; font-weight: 800; border: none; padding: 6px 0; background: transparent; box-shadow: none;" value="<?= e($form->title) ?>" placeholder="Judul Formulir">
                </div>
                <div class="form-group mb-2">
                    <textarea id="form-desc-input" class="form-control" style="font-size: 14px; color: var(--text-secondary); border: none; padding: 4px 0; background: transparent; box-shadow: none; min-height: 45px;" placeholder="Tuliskan deskripsi atau instruksi formulir di sini..."><?= e($form->description) ?></textarea>
                </div>
                <div class="flex items-center gap-2 pt-2 flex-wrap" style="border-top: 1px dashed #e2e8f0;">
                    <span class="text-sm text-muted" style="font-size: 11px; font-weight: 700; text-transform: uppercase;">Link Singkat:</span>
                    <div style="font-family: monospace; font-size: 12px; color: #4f46e5; background: #eef2ff; padding: 3px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 2px; max-width: 100%; overflow-x: auto;">
                        <span><?= url() ?>/</span>
                        <input type="text" id="form-slug-input" value="<?= e($form->slug) ?>" style="border: none; background: transparent; font-family: monospace; font-weight: bold; color: #3730a3; outline: none; width: 140px;" title="Ubah link singkat formulir">
                    </div>
                </div>
            </div>
        </div>

        <!-- Canvas Fields Container -->
        <div id="fields-container" style="display: flex; flex-direction: column; gap: 14px;">
            <!-- Rendered dynamically by JavaScript -->
        </div>

        <!-- Add Field Quick Button on Mobile -->
        <div class="mobile-add-btn-wrapper" style="margin-top: 8px;">
            <button type="button" class="btn btn-primary w-full" onclick="switchMobileBuilderPanel('palette')" style="padding: 12px; font-weight: 700; border-radius: 12px;">
                ➕ Tambah Pertanyaan Baru
            </button>
        </div>

        <!-- Empty Canvas Placeholder -->
        <div id="empty-canvas" class="card text-center" style="display: none; padding: 40px 20px; border: 2px dashed #cbd5e1; background: #f8fafc;">
            <p style="color: var(--text-secondary); font-weight: 600; margin-bottom: 8px;">Formulir masih kosong</p>
            <p class="text-sm text-muted" style="margin: 0 0 16px 0;">Pilih tipe pertanyaan di panel kiri untuk mulai membuat formulir Anda.</p>
            <button type="button" class="btn btn-primary btn-sm" onclick="switchMobileBuilderPanel('palette')">➕ Tambah Pertanyaan</button>
        </div>
    </div>

</div>

<!-- ─── TAB 2: TEMPLATE SURAT WORD INTEGRATION ─── -->
<div id="builder-tab-doc" style="display: none; max-width: 900px; margin: 0 auto;">
    <div class="card mb-4">
        <div class="card-header" style="padding: 20px 24px;">
            <div class="flex items-center justify-between">
                <div>
                    <h3 style="font-size: 17px; font-weight: 800; color: var(--text-primary); margin: 0;">
                        📄 Hubungkan Formulir dengan Template Surat Microsoft Word (.DOCX)
                    </h3>
                    <p class="text-sm text-muted" style="margin: 4px 0 0;">
                        Saat responden mengisi formulir, sistem akan langsung menghasilkan berkas surat resmi berformat Word (.docx) & PDF sesuai template pilihan Anda.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="card-body" style="padding: 24px;">
            
            <!-- Template Selection Dropdown -->
            <div class="form-group mb-4">
                <label class="form-label" style="font-size: 14px; font-weight: 700;">
                    Pilih Template Surat Word (.DOCX)
                </label>
                <div class="flex gap-2">
                    <select id="form-template-select" class="form-control" onchange="onTemplateSelected(this.value)">
                        <option value="">-- Tanpa Generator Surat Otomatis --</option>
                        <?php foreach ($templates as $tmpl): ?>
                            <option value="<?= $tmpl->id ?>" <?= $form->template_id == $tmpl->id ? 'selected' : '' ?>>
                                [<?= e($tmpl->category) ?>] <?= e($tmpl->name) ?> (v<?= (int)$tmpl->version ?> &bull; <?= (int)$tmpl->variable_count ?> variable)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="<?= url('templates/create') ?>" target="_blank" class="btn btn-soft-primary" style="white-space: nowrap;">
                        + Upload Template Word
                    </a>
                </div>
            </div>

            <!-- Connected Template Details Card -->
            <div id="template-connected-card" style="display: none; margin-bottom: 24px;">
                <div class="card" style="background: #f8fafc; border: 1px solid #c7d2fe;">
                    <div class="card-body" style="padding: 18px 20px;">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-3">
                                <span style="font-size: 32px;">📄</span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <strong id="tmpl-name" style="font-size: 15px; color: var(--text-primary);">-</strong>
                                        <span id="tmpl-category-badge" class="badge badge-primary">-</span>
                                        <span id="tmpl-version-badge" class="badge badge-success">v1</span>
                                    </div>
                                    <div class="text-sm text-muted mt-1">
                                        Berkas Master: <code id="tmpl-filename" style="color: #4338ca;">template.docx</code> &bull; <span id="tmpl-var-count">0</span> variable terdeteksi
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <a href="#" id="tmpl-mapping-link" target="_blank" class="btn btn-secondary btn-sm">
                                    🧩 Edit Mapping Master &nearr;
                                </a>
                                <a href="#" id="tmpl-download-link" class="btn btn-secondary btn-sm">
                                    📥 Unduh .DOCX
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactive Mapping Table between Form Questions & Word Variables -->
            <div id="template-mapping-wrapper" style="display: none;">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0;">
                            🧩 Pemetaan Pertanyaan Form ➔ Variable Dokumen Word
                        </h4>
                        <p class="text-sm text-muted" style="margin: 2px 0 0;">
                            Pilih pertanyaan formulir mana yang akan mengisi setiap tag variable di dokumen Word.
                        </p>
                    </div>

                    <button type="button" class="btn btn-soft-primary btn-sm" onclick="autoMatchVariables()">
                        ⚡ Cocokkan Otomatis (Auto Match)
                    </button>
                </div>

                <div class="table-container mb-4">
                    <table class="table" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th style="width: 220px;">Variable di Word</th>
                                <th>Keterangan / Label</th>
                                <th style="width: 320px;">Sumber Pertanyaan Formulir Ini</th>
                            </tr>
                        </thead>
                        <tbody id="mapping-table-body">
                            <!-- Populated dynamically by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Generation Workflow Explanation -->
                <div class="card mb-4" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                    <div class="card-body" style="padding: 16px 20px;">
                        <div class="flex items-start gap-3">
                            <span style="font-size: 24px;">🚀</span>
                            <div class="text-sm" style="color: #166534; line-height: 1.6;">
                                <strong>Alur Penerbitan Surat Otomatis:</strong><br>
                                Responden mengisi formulir online &bull; Sistem mengambil jawaban &bull; Memasukkan data ke berkas Word <code>.docx</code> master &bull; Menghasilkan surat resmi yang dapat langsung diunduh responden!
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-4">
                <button type="button" class="btn btn-primary btn-lg" onclick="saveBuilder()">
                    💾 Simpan Formulir & Pengaturan Dokumen
                </button>
                <button type="button" class="btn btn-secondary" onclick="switchBuilderTab('fields')">
                    &larr; Kembali ke Desain Pertanyaan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ─── Share Modal ─── -->
<div class="modal-backdrop" id="share-modal-backdrop"></div>
<div class="modal" id="share-modal" style="max-width: 520px;">
    <div class="modal-header">
        <h3 class="modal-title">🔗 Bagikan Formulir Publik</h3>
        <button class="modal-close" onclick="closeShareModal()">&times;</button>
    </div>
    <div class="modal-body">
        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 16px;">
            Siapa saja yang memiliki link ini dapat mengisi formulir <strong>tanpa perlu login</strong>.
        </p>

        <div class="form-group">
            <label class="form-label">Link Formulir Publik</label>
            <div class="input-group">
                <input type="text" id="share-url-input" class="form-control" value="<?= $publicUrl ?>" readonly>
                <button type="button" class="btn btn-primary" onclick="copyShareUrl()">
                    Salin Link
                </button>
            </div>
        </div>

        <div class="flex gap-2 mt-4">
            <a href="https://api.whatsapp.com/send?text=Silakan%20isi%20formulir%20berikut:%20<?= urlencode($publicUrl) ?>" target="_blank" class="btn btn-success btn-sm w-full">
                📲 Kirim via WhatsApp
            </a>
            <a href="<?= $publicUrl ?>" target="_blank" class="btn btn-secondary btn-sm w-full">
                🌐 Buka di Tab Baru
            </a>
        </div>
    </div>
</div>

<style>
/* ─── 2-Column Builder Grid ─── */
.builder-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 20px;
    align-items: start;
}

.builder-palette-panel {
    position: sticky;
    top: 20px;
}

/* ─── Builder Mobile Navigation Bar ─── */
.builder-mobile-nav {
    display: none;
    position: sticky;
    top: 70px;
    z-index: 90;
    background: #ffffff;
    padding: 6px;
    border-radius: var(--radius-lg);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
    border: 1px solid var(--border-subtle);
    margin-bottom: 16px;
    gap: 6px;
}

.builder-mobile-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 8px;
    border-radius: var(--radius-md);
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 700;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.builder-mobile-tab.active {
    background: var(--primary-600);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.35);
}

.tab-btn {
    padding: 12px 20px;
    border: none;
    background: transparent;
    font-size: 14px;
    font-weight: 700;
    color: var(--text-secondary);
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.2s ease;
}
.tab-btn.active {
    color: var(--primary-600);
    border-bottom-color: var(--primary-600);
    background: #ffffff;
}

.palette-section-title {
    font-size: 11px;
    font-weight: 800;
    color: var(--text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 4px 2px;
}

.palette-group-grid {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.palette-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    border-radius: 8px;
    border: 1px solid var(--border-subtle);
    background: #ffffff;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    cursor: pointer;
    text-align: left;
    transition: all 0.15s ease;
}
.palette-item:hover {
    background: var(--primary-50);
    color: var(--primary-600);
    border-color: var(--primary-300);
    transform: translateX(2px);
}

/* ─── Field Card & Direct Inline Editing ─── */
.field-card {
    background: #ffffff;
    border: 1.5px solid var(--border-subtle);
    border-left: 4px solid #cbd5e1;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    padding: 18px 20px;
    transition: all 0.2s ease;
    position: relative;
}
.field-card:hover {
    border-color: #cbd5e1;
    border-left-color: var(--primary-400);
    box-shadow: var(--shadow-md);
}
.field-card.selected {
    border-color: var(--primary-400);
    border-left-color: var(--primary-600);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12), var(--shadow-md);
    background: #ffffff;
}

.field-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}

.field-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    min-width: 0;
}

.field-number-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 26px;
    height: 26px;
    padding: 0 6px;
    border-radius: 6px;
    background: #f1f5f9;
    color: var(--text-secondary);
    font-size: 12px;
    font-weight: 800;
    flex-shrink: 0;
}
.field-card.selected .field-number-pill {
    background: var(--primary-100);
    color: var(--primary-700);
}

.field-input-title-wrapper {
    display: flex;
    align-items: center;
    gap: 6px;
    flex: 1;
    min-width: 0;
}

.inline-field-title {
    width: 100%;
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    border: 1.5px solid transparent;
    border-radius: 6px;
    padding: 6px 10px;
    background: transparent;
    transition: all 0.15s ease;
    line-height: 1.4;
}
.inline-field-title:hover {
    background: #f8fafc;
    border-color: #e2e8f0;
}
.inline-field-title:focus {
    background: #ffffff;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    outline: none;
}
.inline-field-title.heading-style {
    font-size: 17px;
    font-weight: 800;
    color: var(--text-primary);
    border-bottom: 2px solid #e2e8f0;
    border-radius: 0;
    padding: 6px 4px;
}
.inline-field-title.heading-style:focus {
    border-bottom-color: var(--primary-600);
    box-shadow: none;
}

.inline-field-desc {
    width: 100%;
    font-size: 13.5px;
    color: var(--text-secondary);
    border: 1.5px solid transparent;
    border-radius: 6px;
    padding: 6px 10px;
    background: transparent;
    min-height: 50px;
    resize: vertical;
    line-height: 1.5;
    transition: all 0.15s ease;
}
.inline-field-desc:hover {
    background: #f8fafc;
    border-color: #e2e8f0;
}
.inline-field-desc:focus {
    background: #ffffff;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    outline: none;
}

.inline-placeholder-input {
    width: 100%;
    font-size: 13px;
    color: var(--text-secondary);
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
    padding: 7px 12px;
    background: #f8fafc;
    transition: all 0.15s ease;
    margin-top: 4px;
}
.inline-placeholder-input:focus {
    background: #ffffff;
    border-style: solid;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.12);
    outline: none;
}

/* ─── Inline Options List ─── */
.inline-options-container {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 8px;
}
.inline-option-row {
    display: flex;
    align-items: center;
    gap: 8px;
}
.inline-option-marker {
    font-size: 14px;
    color: #94a3b8;
    user-select: none;
    flex-shrink: 0;
}
.inline-option-input {
    flex: 1;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-primary);
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 6px 10px;
    background: #ffffff;
    transition: all 0.15s ease;
}
.inline-option-input:focus {
    border-color: var(--primary-500);
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15);
    outline: none;
}
.inline-option-del {
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.15s ease;
}
.inline-option-del:hover {
    background: #fee2e2;
    color: #dc2626;
}
.inline-add-opt-btn {
    align-self: flex-start;
    background: transparent;
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 700;
    color: var(--primary-600);
    cursor: pointer;
    transition: all 0.15s ease;
    margin-top: 4px;
}
.inline-add-opt-btn:hover {
    background: var(--primary-50);
    border-color: var(--primary-400);
}

/* ─── Field Toolbar & Card Footer ─── */
.field-toolbar {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}
.field-btn {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 5px 8px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
}
.field-btn:hover {
    background: #e2e8f0;
    color: var(--text-primary);
}
.field-btn.delete:hover {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fca5a5;
}

.field-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 14px;
    padding-top: 10px;
    border-top: 1px dashed #e2e8f0;
}

.inline-var-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-secondary);
}
.inline-var-box {
    display: inline-flex;
    align-items: center;
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    border-radius: 6px;
    padding: 2px 6px;
    font-family: monospace;
    font-size: 11.5px;
    font-weight: 700;
    color: #4338ca;
}
.inline-var-input {
    border: none;
    background: transparent;
    font-family: monospace;
    font-size: 11.5px;
    font-weight: 700;
    color: #4338ca;
    outline: none;
    min-width: 60px;
    max-width: 130px;
    padding: 0 2px;
}
.inline-var-input:focus {
    background: #ffffff;
    border-radius: 3px;
}

.inline-req-toggle {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    user-select: none;
}
.inline-req-toggle input {
    cursor: pointer;
}

.btn-cond-toggle {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: var(--text-secondary);
    transition: all 0.15s ease;
}
.btn-cond-toggle:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: var(--text-primary);
}
.btn-cond-toggle.active {
    background: #fef3c7;
    border-color: #fde68a;
    color: #92400e;
}

/* ─── Inline Conditional Logic Box ─── */
.inline-cond-box {
    margin-top: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #f59e0b;
    border-radius: 8px;
    padding: 14px;
    animation: fadeIn 0.2s ease;
}
.inline-cond-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 8px;
    margin-top: 8px;
}
@media (max-width: 768px) {
    .inline-cond-grid {
        grid-template-columns: 1fr;
    }
}

/* ─── Mobile Media Query for Builder ─── */
@media (max-width: 992px) {
    .builder-grid {
        grid-template-columns: 1fr;
    }
    .builder-mobile-nav {
        display: flex;
    }
    .mobile-only-btn {
        display: inline-flex !important;
    }
    .palette-group-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .builder-panel {
        display: none;
    }
    .builder-panel.mobile-active {
        display: block !important;
    }
    #builder-panel-canvas.mobile-active {
        display: flex !important;
    }
    .field-card {
        padding: 14px 12px;
    }
    .field-card-header {
        flex-direction: column;
        gap: 8px;
    }
    .field-toolbar {
        width: 100%;
        justify-content: flex-end;
        border-bottom: 1px dashed #e2e8f0;
        padding-bottom: 8px;
        margin-bottom: 4px;
    }
    .field-btn {
        padding: 6px 10px;
        font-size: 13px;
    }
}
</style>

<script>
// ─── Initial Form State ───
let fields = <?= json_encode(array_map(function($f) {
    return [
        'id'          => $f->id,
        'field_type'  => $f->field_type,
        'field_name'  => $f->field_name,
        'label'       => $f->label,
        'placeholder' => $f->placeholder ?? '',
        'description' => $f->description ?? '',
        'is_required' => (bool)$f->is_required,
        'options'     => json_decode($f->options_json ?? '[]', true) ?: ['Pilihan 1', 'Pilihan 2', 'Pilihan 3'],
        'settings'    => json_decode($f->settings_json ?? '{}', true) ?: [],
    ];
}, $fields)) ?>;

let allTemplates = <?= json_encode($templates) ?>;
let templateVariables = <?= json_encode($templateVariables) ?>;
let selectedTemplateId = <?= json_encode($form->template_id ? (int)$form->template_id : null) ?>;
let selectedIndex = 0;

let currentMobilePanel = 'canvas';

function switchMobileBuilderPanel(panelName) {
    currentMobilePanel = panelName;
    
    // Update tab button states
    ['canvas', 'palette'].forEach(name => {
        const btn = document.getElementById('mob-tab-' + name);
        if (btn) btn.classList.toggle('active', name === panelName);
    });

    if (window.innerWidth <= 992) {
        document.querySelectorAll('.builder-panel').forEach(p => p.classList.remove('mobile-active'));
        const target = document.getElementById('builder-panel-' + panelName);
        if (target) {
            target.classList.add('mobile-active');
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    renderCanvas();
    if (selectedTemplateId) {
        onTemplateSelected(selectedTemplateId);
    }
    if (window.innerWidth <= 992) {
        switchMobileBuilderPanel('canvas');
    }
});

// Update on resize
window.addEventListener('resize', () => {
    if (window.innerWidth > 992) {
        document.querySelectorAll('.builder-panel').forEach(p => {
            p.classList.remove('mobile-active');
            p.style.display = '';
        });
    } else {
        switchMobileBuilderPanel(currentMobilePanel);
    }
});

function switchBuilderTab(tab) {
    document.getElementById('builder-tab-fields').style.display = (tab === 'fields') ? 'grid' : 'none';
    document.getElementById('builder-tab-doc').style.display = (tab === 'doc') ? 'block' : 'none';
    document.getElementById('tab-btn-fields').classList.toggle('active', tab === 'fields');
    document.getElementById('tab-btn-doc').classList.toggle('active', tab === 'doc');

    if (tab === 'doc' && selectedTemplateId) {
        renderTemplateMappingTable();
    }
}

function onTemplateSelected(templateId) {
    selectedTemplateId = templateId ? parseInt(templateId) : null;
    const card = document.getElementById('template-connected-card');
    const wrapper = document.getElementById('template-mapping-wrapper');
    const badge = document.getElementById('doc-status-badge');

    if (!selectedTemplateId) {
        card.style.display = 'none';
        wrapper.style.display = 'none';
        badge.textContent = 'Opsional';
        badge.className = 'badge badge-muted';
        return;
    }

    const tmpl = allTemplates.find(t => t.id == selectedTemplateId);
    if (!tmpl) return;

    // Populate Template Overview Info
    document.getElementById('tmpl-name').textContent = tmpl.name;
    document.getElementById('tmpl-category-badge').textContent = tmpl.category;
    document.getElementById('tmpl-version-badge').textContent = 'v' + (tmpl.version || 1);
    document.getElementById('tmpl-filename').textContent = tmpl.original_filename || 'template.docx';
    document.getElementById('tmpl-var-count').textContent = tmpl.variable_count || 0;
    document.getElementById('tmpl-mapping-link').href = '<?= url("templates/") ?>/' + tmpl.id + '/mapping';
    document.getElementById('tmpl-download-link').href = '<?= url("templates/") ?>/' + tmpl.id + '/download';

    card.style.display = 'block';
    wrapper.style.display = 'block';
    badge.textContent = 'Terhubung';
    badge.className = 'badge badge-success';

    renderTemplateMappingTable();
}

function renderTemplateMappingTable() {
    if (!selectedTemplateId) return;
    const vars = templateVariables[selectedTemplateId] || [];
    const tbody = document.getElementById('mapping-table-body');
    tbody.innerHTML = '';

    if (vars.length === 0) {
        tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted" style="padding: 20px;">Tidak ada variable terdaftar pada template ini.</td></tr>`;
        return;
    }

    vars.forEach(v => {
        const tr = document.createElement('tr');
        
        let selectOptionsHtml = `<option value="">-- Abaikan / Kosong --</option>`;
        
        // Group 1: Form Questions
        selectOptionsHtml += `<optgroup label="📝 Pertanyaan dari Formulir Ini">`;
        fields.forEach(f => {
            if (f.field_type !== 'heading' && f.field_type !== 'description') {
                const isSelected = (v.source_type === 'form_response' && (v.source_key === f.field_name || v.source_key === f.label));
                selectOptionsHtml += `<option value="form:${f.field_name}" ${isSelected ? 'selected' : ''}>[${f.label}] (key: ${f.field_name})</option>`;
            }
        });
        selectOptionsHtml += `</optgroup>`;

        // Group 2: System / Instansi
        selectOptionsHtml += `<optgroup label="⚙️ Otomatis Sistem & Instansi">`;
        selectOptionsHtml += `<option value="system:tanggal_surat" ${v.source_type === 'system' && v.source_key === 'tanggal_surat' ? 'selected' : ''}>⚙️ Tanggal Surat Resmi</option>`;
        selectOptionsHtml += `<option value="system:nomor_surat" ${v.source_type === 'system' && v.source_key === 'nomor_surat' ? 'selected' : ''}>⚙️ Nomor Dokumen Otomatis</option>`;
        selectOptionsHtml += `<option value="setting:nama_instansi" ${v.source_type === 'setting' && v.source_key === 'nama_instansi' ? 'selected' : ''}>🏢 Nama Instansi (Settings)</option>`;
        selectOptionsHtml += `<option value="user:user_name" ${v.source_type === 'user' ? 'selected' : ''}>👤 Nama Pembuat Form</option>`;
        selectOptionsHtml += `</optgroup>`;

        tr.innerHTML = `
            <td>
                <div style="font-family: monospace; font-weight: 700; color: #4338ca; background: #e0e7ff; padding: 4px 8px; border-radius: 6px; display: inline-block; font-size: 12px;">
                    {{${v.variable_name}}}
                </div>
            </td>
            <td>
                <strong style="color: var(--text-primary); font-size: 13px;">${escapeHtml(v.label || v.variable_name)}</strong>
            </td>
            <td>
                <select class="form-control var-mapping-select" data-var-id="${v.id}" data-var-name="${v.variable_name}" style="font-size: 13px; padding: 6px 10px;">
                    ${selectOptionsHtml}
                </select>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function autoMatchVariables() {
    const selects = document.querySelectorAll('.var-mapping-select');
    let matchedCount = 0;

    selects.forEach(sel => {
        const varName = sel.dataset.varName.toLowerCase().replace(/[^a-z0-9]/g, '');
        
        for (let i = 0; i < sel.options.length; i++) {
            const opt = sel.options[i];
            const optVal = opt.value.toLowerCase().replace(/[^a-z0-9]/g, '');
            const optText = opt.text.toLowerCase().replace(/[^a-z0-9]/g, '');

            if (optVal.includes(varName) || optText.includes(varName)) {
                sel.selectedIndex = i;
                matchedCount++;
                break;
            }
        }
    });

    showToast('success', `Berhasil mencocokkan ${matchedCount} variable dengan pertanyaan formulir!`);
}

function getFieldTypeBadge(type) {
    const labels = {
        text: '📝 Teks Singkat',
        textarea: '📄 Paragraf Panjang',
        number: '🔢 Angka',
        email: '✉️ Email',
        phone: '📞 WhatsApp / No. HP',
        dropdown: '🔻 Dropdown',
        radio: '🔘 Pilihan Tunggal',
        checkbox: '☑️ Kotak Centang',
        date: '📅 Tanggal',
        time: '⏰ Jam',
        signature: '✍️ Tanda Tangan',
        file: '📎 Unggah Dokumen',
        image: '🖼️ Unggah Foto',
        heading: '🏷️ Judul Bagian',
        description: 'ℹ️ Keterangan'
    };
    return labels[type] || '📝 Pertanyaan';
}

// ─── Direct In-Card Canvas Rendering & Controls ───
function renderCanvas() {
    const container = document.getElementById('fields-container');
    const emptyNotice = document.getElementById('empty-canvas');
    const mobCountEl = document.getElementById('mob-field-count');
    if (mobCountEl) mobCountEl.textContent = fields.length;

    container.innerHTML = '';

    if (fields.length === 0) {
        emptyNotice.style.display = 'block';
        return;
    } else {
        emptyNotice.style.display = 'none';
    }

    if (selectedIndex >= fields.length) {
        selectedIndex = Math.max(0, fields.length - 1);
    }

    fields.forEach((f, idx) => {
        if (!f.settings) f.settings = {};
        if (!f.settings.conditional_logic) {
            f.settings.conditional_logic = {
                enabled: false,
                action: 'show',
                target_field: '',
                operator: 'equals',
                value: 'Ya'
            };
        }
        const condLogic = f.settings.conditional_logic;

        const card = document.createElement('div');
        card.className = `field-card ${idx === selectedIndex ? 'selected' : ''}`;
        card.id = `field-card-${idx}`;
        card.onclick = () => selectField(idx);

        const reqBadge = `<span class="field-req-star" style="color: var(--danger-600); font-weight: 800; font-size: 16px; margin-left: 2px; display: ${f.is_required ? 'inline' : 'none'};" title="Wajib Diisi">*</span>`;

        let headerHtml = '';
        let bodyHtml = '';

        if (f.field_type === 'heading') {
            headerHtml = `
                <div class="field-title-row">
                    <span class="field-number-pill" title="Judul Bagian">🏷️</span>
                    <div class="field-input-title-wrapper">
                        <input type="text" 
                               class="inline-field-title heading-style" 
                               value="${escapeHtml(f.label)}" 
                               placeholder="Judul Bagian / Header..." 
                               oninput="handleDirectLabelInput(${idx}, this.value)" 
                               onclick="event.stopPropagation()" 
                               onfocus="selectField(${idx})">
                    </div>
                </div>
            `;
            bodyHtml = ``;
        } else if (f.field_type === 'description') {
            headerHtml = `
                <div class="field-title-row">
                    <span class="field-number-pill" title="Keterangan Formulir">ℹ️</span>
                    <div class="field-input-title-wrapper">
                        <textarea class="inline-field-desc" 
                                  placeholder="Tulis petunjuk atau keterangan formulir di sini..." 
                                  oninput="handleDirectDescInput(${idx}, this.value)" 
                                  onclick="event.stopPropagation()" 
                                  onfocus="selectField(${idx})">${escapeHtml(f.description || f.label)}</textarea>
                    </div>
                </div>
            `;
            bodyHtml = ``;
        } else {
            // General Input Question Card
            headerHtml = `
                <div class="field-title-row">
                    <span class="field-number-pill">${idx + 1}</span>
                    <div class="field-input-title-wrapper">
                        <input type="text" 
                               class="inline-field-title" 
                               value="${escapeHtml(f.label)}" 
                               placeholder="Tulis pertanyaan di sini..." 
                               oninput="handleDirectLabelInput(${idx}, this.value)" 
                               onclick="event.stopPropagation()" 
                               onfocus="selectField(${idx})">
                        ${reqBadge}
                        <span class="badge badge-muted" style="font-size: 10.5px; font-weight: 700; margin-left: 4px; white-space: nowrap;">
                            ${getFieldTypeBadge(f.field_type)}
                        </span>
                    </div>
                </div>
            `;

            if (f.field_type === 'signature') {
                bodyHtml = `
                    <div style="border: 2px dashed #cbd5e1; border-radius: 8px; height: 75px; background: #fafafa; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 13px; margin-top: 4px;">
                        ✍️ Area Tanda Tangan Digital Responden
                    </div>
                `;
            } else if (f.field_type === 'textarea') {
                bodyHtml = `
                    <textarea class="form-control" placeholder="${escapeHtml(f.placeholder || 'Teks jawaban panjang...')}" disabled style="min-height: 60px; background: #f8fafc; margin-top: 4px;"></textarea>
                    <input type="text" 
                           class="inline-placeholder-input" 
                           value="${escapeHtml(f.placeholder || '')}" 
                           placeholder="✏️ Ubah teks petunjuk (placeholder)..." 
                           oninput="handleDirectPlaceholderChange(${idx}, this.value)"
                           onclick="event.stopPropagation()">
                `;
            } else if (f.field_type === 'dropdown') {
                bodyHtml = `
                    <div class="inline-options-container">
                        ${f.options.map((opt, optIdx) => `
                            <div class="inline-option-row">
                                <span class="inline-option-marker">🔻</span>
                                <input type="text" 
                                       class="inline-option-input" 
                                       value="${escapeHtml(opt)}" 
                                       placeholder="Pilihan ${optIdx + 1}"
                                       oninput="handleDirectOptionChange(${idx}, ${optIdx}, this.value)" 
                                       onclick="event.stopPropagation()" 
                                       onfocus="selectField(${idx})">
                                <button type="button" class="inline-option-del" onclick="event.stopPropagation(); removeOptionInline(${idx}, ${optIdx})" title="Hapus Pilihan">&times;</button>
                            </div>
                        `).join('')}
                        <button type="button" class="inline-add-opt-btn" onclick="event.stopPropagation(); addOptionInline(${idx})">
                            ➕ Tambah Pilihan Dropdown
                        </button>
                    </div>
                `;
            } else if (f.field_type === 'radio') {
                bodyHtml = `
                    <div class="inline-options-container">
                        ${f.options.map((opt, optIdx) => `
                            <div class="inline-option-row">
                                <span class="inline-option-marker">🔘</span>
                                <input type="text" 
                                       class="inline-option-input" 
                                       value="${escapeHtml(opt)}" 
                                       placeholder="Pilihan ${optIdx + 1}"
                                       oninput="handleDirectOptionChange(${idx}, ${optIdx}, this.value)" 
                                       onclick="event.stopPropagation()" 
                                       onfocus="selectField(${idx})">
                                <button type="button" class="inline-option-del" onclick="event.stopPropagation(); removeOptionInline(${idx}, ${optIdx})" title="Hapus Pilihan">&times;</button>
                            </div>
                        `).join('')}
                        <button type="button" class="inline-add-opt-btn" onclick="event.stopPropagation(); addOptionInline(${idx})">
                            ➕ Tambah Pilihan Radio
                        </button>
                    </div>
                `;
            } else if (f.field_type === 'checkbox') {
                bodyHtml = `
                    <div class="inline-options-container">
                        ${f.options.map((opt, optIdx) => `
                            <div class="inline-option-row">
                                <span class="inline-option-marker">☑️</span>
                                <input type="text" 
                                       class="inline-option-input" 
                                       value="${escapeHtml(opt)}" 
                                       placeholder="Pilihan ${optIdx + 1}"
                                       oninput="handleDirectOptionChange(${idx}, ${optIdx}, this.value)" 
                                       onclick="event.stopPropagation()" 
                                       onfocus="selectField(${idx})">
                                <button type="button" class="inline-option-del" onclick="event.stopPropagation(); removeOptionInline(${idx}, ${optIdx})" title="Hapus Pilihan">&times;</button>
                            </div>
                        `).join('')}
                        <button type="button" class="inline-add-opt-btn" onclick="event.stopPropagation(); addOptionInline(${idx})">
                            ➕ Tambah Pilihan Centang
                        </button>
                    </div>
                `;
            } else if (f.field_type === 'file' || f.field_type === 'image') {
                bodyHtml = `
                    <div style="border: 1px dashed #cbd5e1; border-radius: 8px; padding: 12px 16px; background: #f8fafc; display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 13px; margin-top: 4px;">
                        <span>${f.field_type === 'image' ? '🖼️' : '📎'}</span>
                        <span>Area unggah berkas oleh responden</span>
                    </div>
                `;
            } else {
                bodyHtml = `
                    <input type="text" 
                           class="inline-placeholder-input" 
                           value="${escapeHtml(f.placeholder || '')}" 
                           placeholder="✏️ Tulis teks petunjuk isian (placeholder)..." 
                           oninput="handleDirectPlaceholderChange(${idx}, this.value)"
                           onclick="event.stopPropagation()">
                `;
            }
        }

        // Trigger fields dropdown (other fields for conditional logic)
        const triggerFields = fields.filter((item, i) => i !== idx && item.field_type !== 'heading' && item.field_type !== 'description');
        let triggerOptionsHtml = '';
        triggerFields.forEach(tf => {
            const isSel = (condLogic.target_field === tf.field_name);
            triggerOptionsHtml += `<option value="${escapeHtml(tf.field_name)}" ${isSel ? 'selected' : ''}>[${escapeHtml(tf.label)}] ({{${escapeHtml(tf.field_name)}}})</option>`;
        });

        // Inline Conditional Logic Box
        const condBoxHtml = `
            <div id="inline-cond-box-${idx}" class="inline-cond-box" style="display: ${condLogic.enabled ? 'block' : 'none'};">
                <div class="flex items-center justify-between">
                    <div style="font-size: 12px; font-weight: 800; color: #92400e;">
                        🔀 Aturan Logika Bersyarat (Tampilkan / Sembunyikan Pertanyaan Ini)
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="event.stopPropagation(); toggleInlineCondLogic(${idx}, false)" style="font-size: 11px; padding: 2px 8px;">
                        ✕ Matikan Logika
                    </button>
                </div>

                <div class="inline-cond-grid">
                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 3px;">Aksi Pertanyaan Ini:</label>
                        <select class="form-control" style="font-size: 12px; padding: 5px 8px;" onchange="updateInlineCondProp(${idx}, 'action', this.value)" onclick="event.stopPropagation()">
                            <option value="show" ${condLogic.action === 'show' ? 'selected' : ''}>👁️ Ditampilkan (Show)</option>
                            <option value="hide" ${condLogic.action === 'hide' ? 'selected' : ''}>🚫 Disembunyikan (Hide)</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 3px;">Jika Pertanyaan Pemicu:</label>
                        <select class="form-control" style="font-size: 12px; padding: 5px 8px;" onchange="updateInlineCondProp(${idx}, 'target_field', this.value)" onclick="event.stopPropagation()">
                            <option value="">-- Pilih Pertanyaan --</option>
                            ${triggerOptionsHtml}
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 3px;">Kondisi Jawaban:</label>
                        <select class="form-control" style="font-size: 12px; padding: 5px 8px;" onchange="updateInlineCondProp(${idx}, 'operator', this.value)" onclick="event.stopPropagation()">
                            <option value="equals" ${condLogic.operator === 'equals' ? 'selected' : ''}>Sama persis ( = )</option>
                            <option value="not_equals" ${condLogic.operator === 'not_equals' ? 'selected' : ''}>Tidak sama dengan ( != )</option>
                            <option value="contains" ${condLogic.operator === 'contains' ? 'selected' : ''}>Mengandung kata (contains)</option>
                            <option value="not_empty" ${condLogic.operator === 'not_empty' ? 'selected' : ''}>Diisi / Tidak Kosong</option>
                            <option value="empty" ${condLogic.operator === 'empty' ? 'selected' : ''}>Kosong / Belum Diisi</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: var(--text-secondary); display: block; margin-bottom: 3px;">Nilai Jawaban Pemicu:</label>
                        <input type="text" class="form-control" style="font-size: 12px; padding: 5px 8px;" placeholder="Contoh: Ya" value="${escapeHtml(condLogic.value || '')}" oninput="updateInlineCondProp(${idx}, 'value', this.value)" onclick="event.stopPropagation()" ${condLogic.operator === 'not_empty' || condLogic.operator === 'empty' ? 'disabled' : ''}>
                    </div>
                </div>
            </div>
        `;

        // Required switch for input questions
        let reqToggleHtml = '';
        if (f.field_type !== 'heading' && f.field_type !== 'description') {
            reqToggleHtml = `
                <label class="inline-req-toggle" onclick="event.stopPropagation()">
                    <input type="checkbox" ${f.is_required ? 'checked' : ''} onchange="handleDirectRequiredToggle(${idx}, this.checked)">
                    <span>Wajib Diisi</span>
                </label>
            `;
        }

        // Tag variable for Word
        const varTagHtml = `
            <div class="inline-var-wrapper" onclick="event.stopPropagation()">
                <span style="font-size: 11px; font-weight: 700;">Tag Word:</span>
                <div class="inline-var-box" title="Kunci variable untuk menghubungkan pertanyaan ini ke template Word">
                    <span>{{</span>
                    <input type="text" 
                           class="inline-var-input" 
                           value="${escapeHtml(f.field_name)}" 
                           oninput="handleDirectFieldNameChange(${idx}, this.value)" 
                           placeholder="nama_variable"
                           onclick="event.stopPropagation()">
                    <span>}}</span>
                </div>
            </div>
        `;

        // Conditional Logic Toggle Button
        const condBtnHtml = `
            <button type="button" 
                    id="cond-btn-${idx}" 
                    class="btn-cond-toggle ${condLogic.enabled ? 'active' : ''}" 
                    onclick="event.stopPropagation(); toggleInlineCondLogic(${idx})">
                🔀 ${condLogic.enabled ? 'Logika Aktif' : '+ Logika Bersyarat'}
            </button>
        `;

        card.innerHTML = `
            <div class="field-card-header">
                ${headerHtml}
                <div class="field-toolbar">
                    <button type="button" class="field-btn" onclick="event.stopPropagation(); moveField(${idx}, -1)" title="Pindah ke Atas">▲</button>
                    <button type="button" class="field-btn" onclick="event.stopPropagation(); moveField(${idx}, 1)" title="Pindah ke Bawah">▼</button>
                    <button type="button" class="field-btn" onclick="event.stopPropagation(); duplicateField(${idx})" title="Duplikasi">📋</button>
                    <button type="button" class="field-btn delete" onclick="event.stopPropagation(); removeField(${idx})" title="Hapus">🗑️</button>
                </div>
            </div>
            ${bodyHtml}
            <div class="field-card-footer">
                <div class="flex items-center gap-3 flex-wrap">
                    ${varTagHtml}
                    ${condBtnHtml}
                </div>
                <div>
                    ${reqToggleHtml}
                </div>
            </div>
            ${condBoxHtml}
        `;
        container.appendChild(card);
    });
}

function selectField(idx) {
    selectedIndex = idx;
    document.querySelectorAll('.field-card').forEach((card, i) => {
        card.classList.toggle('selected', i === idx);
    });
}

// ─── Direct In-Card Handlers ───
function handleDirectLabelInput(idx, value) {
    if (!fields[idx]) return;
    fields[idx].label = value;
}

function handleDirectDescInput(idx, value) {
    if (!fields[idx]) return;
    fields[idx].description = value;
    fields[idx].label = value;
}

function handleDirectPlaceholderChange(idx, value) {
    if (!fields[idx]) return;
    fields[idx].placeholder = value;
}

function handleDirectFieldNameChange(idx, value) {
    if (!fields[idx]) return;
    fields[idx].field_name = value;
}

function handleDirectOptionChange(fieldIdx, optIdx, value) {
    if (!fields[fieldIdx] || !fields[fieldIdx].options) return;
    fields[fieldIdx].options[optIdx] = value;
}

function addOptionInline(fieldIdx) {
    if (!fields[fieldIdx]) return;
    if (!fields[fieldIdx].options) fields[fieldIdx].options = [];
    fields[fieldIdx].options.push(`Pilihan ${fields[fieldIdx].options.length + 1}`);
    renderCanvas();
}

function removeOptionInline(fieldIdx, optIdx) {
    if (!fields[fieldIdx] || !fields[fieldIdx].options) return;
    if (fields[fieldIdx].options.length <= 1) {
        showToast('warning', 'Minimal harus ada 1 pilihan.');
        return;
    }
    fields[fieldIdx].options.splice(optIdx, 1);
    renderCanvas();
}

function handleDirectRequiredToggle(idx, isRequired) {
    if (!fields[idx]) return;
    fields[idx].is_required = isRequired;
    
    // Update star indicator on card
    const card = document.getElementById(`field-card-${idx}`);
    if (card) {
        const star = card.querySelector('.field-req-star');
        if (star) star.style.display = isRequired ? 'inline' : 'none';
    }
}

function toggleInlineCondLogic(idx, forceState = null) {
    if (!fields[idx]) return;
    if (!fields[idx].settings) fields[idx].settings = {};
    if (!fields[idx].settings.conditional_logic) {
        fields[idx].settings.conditional_logic = { action: 'show', target_field: '', operator: 'equals', value: 'Ya' };
    }
    
    const currentState = fields[idx].settings.conditional_logic.enabled || false;
    const newState = (forceState !== null) ? forceState : !currentState;
    fields[idx].settings.conditional_logic.enabled = newState;
    
    const box = document.getElementById(`inline-cond-box-${idx}`);
    const btn = document.getElementById(`cond-btn-${idx}`);
    
    if (box) box.style.display = newState ? 'block' : 'none';
    if (btn) {
        btn.classList.toggle('active', newState);
        btn.innerHTML = `🔀 ${newState ? 'Logika Aktif' : '+ Logika Bersyarat'}`;
    }
}

function updateInlineCondProp(idx, prop, value) {
    if (!fields[idx]) return;
    if (!fields[idx].settings) fields[idx].settings = {};
    if (!fields[idx].settings.conditional_logic) {
        fields[idx].settings.conditional_logic = { enabled: true, action: 'show', target_field: '', operator: 'equals', value: 'Ya' };
    }
    fields[idx].settings.conditional_logic[prop] = value;
    if (prop === 'operator') {
        renderCanvas();
    }
}

function addField(type) {
    const order = fields.length + 1;
    const defaultLabels = {
        text: 'Nama Lengkap', textarea: 'Keterangan Tambahan', number: 'Nomor Induk / NISN',
        email: 'Alamat Email', phone: 'Nomor Telepon / WhatsApp', dropdown: 'Pilihan Kategori',
        radio: 'Pilihan Jawaban', checkbox: 'Pilihan Minat', date: 'Tanggal Lahir / Kegiatan',
        time: 'Waktu Pelaksanaan', signature: 'Tanda Tangan Digital', file: 'Unggah Berkas',
        image: 'Unggah Pas Foto', heading: 'Informasi Pribadi', description: 'Silakan isi data dengan benar.'
    };

    const label = defaultLabels[type] || 'Pertanyaan Baru';
    const fieldName = type + '_' + order;

    fields.push({
        id: null,
        field_type: type,
        field_name: fieldName,
        label: label,
        placeholder: 'Masukkan ' + label.toLowerCase(),
        description: '',
        is_required: (type !== 'heading' && type !== 'description'),
        options: ['Pilihan 1', 'Pilihan 2', 'Pilihan 3'],
        settings: {
            conditional_logic: {
                enabled: false,
                action: 'show',
                target_field: '',
                operator: 'equals',
                value: 'Ya'
            }
        },
    });

    selectedIndex = fields.length - 1;
    renderCanvas();
    
    // Auto-focus the newly added question input
    setTimeout(() => {
        const newCard = document.getElementById(`field-card-${selectedIndex}`);
        if (newCard) {
            const input = newCard.querySelector('.inline-field-title, .inline-field-desc');
            if (input) {
                input.focus();
                input.select();
            }
        }
    }, 50);

    if (window.innerWidth <= 992) {
        switchMobileBuilderPanel('canvas');
    }
    showToast('success', `Pertanyaan '${label}' ditambahkan! Silakan langsung ketik judulnya.`);
}

function moveField(idx, direction) {
    const targetIdx = idx + direction;
    if (targetIdx < 0 || targetIdx >= fields.length) return;
    const temp = fields[idx];
    fields[idx] = fields[targetIdx];
    fields[targetIdx] = temp;
    selectedIndex = targetIdx;
    renderCanvas();
}

function duplicateField(idx) {
    const src = fields[idx];
    const clone = JSON.parse(JSON.stringify(src));
    clone.id = null;
    clone.label += ' (Salinan)';
    clone.field_name = clone.field_name + '_copy';
    fields.splice(idx + 1, 0, clone);
    selectedIndex = idx + 1;
    renderCanvas();
    showToast('success', 'Pertanyaan berhasil diduplikasi!');
}

function removeField(idx) {
    fields.splice(idx, 1);
    if (selectedIndex >= fields.length) selectedIndex = Math.max(0, fields.length - 1);
    renderCanvas();
    showToast('info', 'Pertanyaan dihapus.');
}

async function saveBuilder() {
    const btn = document.getElementById('btn-save-builder');
    const btnText = document.getElementById('btn-save-text');
    const title = document.getElementById('form-title-input').value;
    const description = document.getElementById('form-desc-input').value;
    const slug = document.getElementById('form-slug-input') ? document.getElementById('form-slug-input').value : '';

    btnText.textContent = 'Menyimpan...';
    btn.disabled = true;

    // Collect mappings
    const mappings = {};
    const selects = document.querySelectorAll('.var-mapping-select');
    selects.forEach(sel => {
        const varId = sel.dataset.varId;
        const val = sel.value;
        if (val) {
            const parts = val.split(':');
            mappings[varId] = {
                source_type: parts[0] === 'form' ? 'form_response' : parts[0],
                source_key: parts[1] || '',
            };
        }
    });

    try {
        const res = await fetchAPI('<?= url("api/forms/{$form->id}/save") ?>', {
            method: 'POST',
            body: JSON.stringify({
                title: title,
                slug: slug,
                description: description,
                status: '<?= $form->status ?>',
                template_id: selectedTemplateId,
                fields: fields,
                mappings: mappings,
            }),
        });

        if (res.success) {
            if (res.data && res.data.public_url) {
                const shareInput = document.getElementById('share-url-input');
                if (shareInput) shareInput.value = res.data.public_url;
            }
            showToast('success', 'Formulir & Hubungan Template Word berhasil disimpan!');
        }
    } catch (e) {
        showToast('error', e.message || 'Gagal menyimpan formulir.');
    } finally {
        btnText.textContent = 'Simpan Perubahan';
        btn.disabled = false;
    }
}

function openShareModal() {
    const modal = document.getElementById('share-modal');
    const backdrop = document.getElementById('share-modal-backdrop');
    if (modal && backdrop) {
        modal.classList.add('active');
        backdrop.classList.add('active');
        modal.style.display = 'block';
        backdrop.style.display = 'block';
    }
}

function closeShareModal() {
    const modal = document.getElementById('share-modal');
    const backdrop = document.getElementById('share-modal-backdrop');
    if (modal && backdrop) {
        modal.classList.remove('active');
        backdrop.classList.remove('active');
        modal.style.display = 'none';
        backdrop.style.display = 'none';
    }
}

async function copyShareUrl() {
    const input = document.getElementById('share-url-input');
    if (!input) return;
    
    const text = input.value;
    let copied = false;

    // 1. Try modern navigator.clipboard
    if (navigator.clipboard && window.isSecureContext) {
        try {
            await navigator.clipboard.writeText(text);
            copied = true;
        } catch (err) {
            copied = false;
        }
    }

    // 2. Fallback to execCommand (works everywhere on mobile & desktop)
    if (!copied) {
        try {
            input.focus();
            input.select();
            input.setSelectionRange(0, 99999);
            copied = document.execCommand('copy');
        } catch (e) {
            copied = false;
        }
    }

    if (copied) {
        showToast('success', 'Link formulir berhasil disalin ke clipboard!');
    } else {
        // Fallback prompt
        window.prompt('Silakan salin link formulir secara manual:', text);
    }
}

function escapeHtml(text) {
    if (!text) return '';
    return text.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
</script>

