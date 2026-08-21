<?php
use App\Core\Auth;
use App\Core\CSRF;

$publicUrl = url($form->slug);
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

<!-- ─── TAB 1: FORM FIELDS BUILDER ─── -->
<div id="builder-tab-fields" style="display: grid; grid-template-columns: 280px 1fr 340px; gap: 20px; align-items: start;">
    
    <!-- ─── LEFT: Palette Field Types ─── -->
    <div class="card" style="position: sticky; top: 88px; max-height: calc(100vh - 110px); overflow-y: auto;">
        <div class="card-header" style="padding: 14px 16px;">
            <h3 style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-secondary); margin: 0;">
                ➕ Tambah Pertanyaan
            </h3>
        </div>
        <div class="card-body" style="padding: 12px; display: flex; flex-direction: column; gap: 6px;">
            <div style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; padding: 4px 6px;">Input Teks</div>
            <button type="button" class="palette-item" onclick="addField('text')">📝 Teks Singkat</button>
            <button type="button" class="palette-item" onclick="addField('textarea')">📄 Paragraf / Teks Panjang</button>
            <button type="button" class="palette-item" onclick="addField('number')">🔢 Angka / Nilai</button>
            <button type="button" class="palette-item" onclick="addField('email')">✉️ Alamat Email</button>
            <button type="button" class="palette-item" onclick="addField('phone')">📞 No. Telepon / WA</button>

            <div style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; padding: 8px 6px 4px;">Pilihan & Opsi</div>
            <button type="button" class="palette-item" onclick="addField('dropdown')">🔻 Menu Dropdown</button>
            <button type="button" class="palette-item" onclick="addField('radio')">🔘 Pilihan Tunggal (Radio)</button>
            <button type="button" class="palette-item" onclick="addField('checkbox')">☑️ Kotak Centang (Multi)</button>

            <div style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; padding: 8px 6px 4px;">Tanggal & Waktu</div>
            <button type="button" class="palette-item" onclick="addField('date')">📅 Pemilih Tanggal</button>
            <button type="button" class="palette-item" onclick="addField('time')">⏰ Pemilih Jam</button>

            <div style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; padding: 8px 6px 4px;">Media & Tanda Tangan</div>
            <button type="button" class="palette-item" onclick="addField('signature')">✍️ Tanda Tangan Digital</button>
            <button type="button" class="palette-item" onclick="addField('file')">📎 Unggah Berkas Dokumen</button>
            <button type="button" class="palette-item" onclick="addField('image')">🖼️ Unggah Gambar / Foto</button>

            <div style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; padding: 8px 6px 4px;">Struktur & Judul</div>
            <button type="button" class="palette-item" onclick="addField('heading')">🏷️ Judul Bagian</button>
            <button type="button" class="palette-item" onclick="addField('description')">ℹ️ Teks Keterangan</button>
        </div>
    </div>

    <!-- ─── CENTER: Form Canvas ─── -->
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <!-- Header Card -->
        <div class="card" style="border-top: 4px solid var(--primary-600); box-shadow: var(--shadow-md);">
            <div class="card-body" style="padding: 24px;">
                <div class="form-group mb-3">
                    <input type="text" id="form-title-input" class="form-control" style="font-size: 20px; font-weight: 800; border: none; padding: 6px 0; background: transparent; box-shadow: none;" value="<?= e($form->title) ?>" placeholder="Judul Formulir">
                </div>
                <div class="form-group mb-2">
                    <textarea id="form-desc-input" class="form-control" style="font-size: 14px; color: var(--text-secondary); border: none; padding: 4px 0; background: transparent; box-shadow: none; min-height: 45px;" placeholder="Tuliskan deskripsi atau instruksi formulir di sini..."><?= e($form->description) ?></textarea>
                </div>
                <div class="flex items-center gap-2 pt-2" style="border-top: 1px dashed #e2e8f0;">
                    <span class="text-sm text-muted" style="font-size: 11px; font-weight: 700; text-transform: uppercase;">Link Singkat:</span>
                    <div style="font-family: monospace; font-size: 12px; color: #4f46e5; background: #eef2ff; padding: 3px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 2px;">
                        <span><?= url() ?>/</span>
                        <input type="text" id="form-slug-input" value="<?= e($form->slug) ?>" style="border: none; background: transparent; font-family: monospace; font-weight: bold; color: #3730a3; outline: none; width: 170px;" title="Ubah link singkat formulir">
                    </div>
                </div>
            </div>
        </div>

        <!-- Canvas Fields Container -->
        <div id="fields-container" style="display: flex; flex-direction: column; gap: 14px;">
            <!-- Rendered dynamically by JavaScript -->
        </div>

        <!-- Empty Canvas Placeholder -->
        <div id="empty-canvas" class="card text-center" style="display: none; padding: 40px 20px; border: 2px dashed #cbd5e1; background: #f8fafc;">
            <p style="color: var(--text-secondary); font-weight: 600; margin-bottom: 8px;">Formulir masih kosong</p>
            <p class="text-sm text-muted" style="margin: 0;">Klik salah satu tipe field di panel kiri untuk mulai menambahkan pertanyaan.</p>
        </div>
    </div>

    <!-- ─── RIGHT: Property Inspector ─── -->
    <div class="card" style="position: sticky; top: 88px; max-height: calc(100vh - 110px); overflow-y: auto;">
        <div class="card-header" style="padding: 14px 16px;">
            <h3 style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-secondary); margin: 0;">
                ⚙️ Pengaturan Field
            </h3>
        </div>
        <div class="card-body" id="inspector-body" style="padding: 16px;">
            <p class="text-sm text-muted text-center" style="margin: 30px 0;">
                Pilih salah satu field di tengah untuk mengubah pengaturannya.
            </p>
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
.palette-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
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
.field-card {
    background: #ffffff;
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    padding: 20px;
    transition: all 0.2s ease;
    cursor: pointer;
    position: relative;
}
.field-card:hover {
    border-color: rgba(99, 102, 241, 0.4);
    box-shadow: var(--shadow-md);
}
.field-card.selected {
    border-color: var(--primary-600);
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2), var(--shadow-md);
}
.field-toolbar {
    display: flex;
    align-items: center;
    gap: 6px;
    position: absolute;
    top: 14px;
    right: 14px;
}
.field-btn {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 4px 8px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.15s ease;
}
.field-btn:hover {
    background: #e2e8f0;
}
.field-btn.delete:hover {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fca5a5;
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

document.addEventListener('DOMContentLoaded', () => {
    renderCanvas();
    if (selectedTemplateId) {
        onTemplateSelected(selectedTemplateId);
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

// ─── Canvas & Inspector Functions ───
function renderCanvas() {
    renderCanvasOnly();
    renderInspector();
}

function renderCanvasOnly() {
    const container = document.getElementById('fields-container');
    const emptyNotice = document.getElementById('empty-canvas');
    container.innerHTML = '';

    if (fields.length === 0) {
        emptyNotice.style.display = 'block';
        return;
    } else {
        emptyNotice.style.display = 'none';
    }

    if (selectedIndex >= fields.length) {
        selectedIndex = fields.length - 1;
    }

    fields.forEach((f, idx) => {
        const card = document.createElement('div');
        card.className = `field-card ${idx === selectedIndex ? 'selected' : ''}`;
        card.onclick = () => selectField(idx);

        let previewHtml = '';
        const reqBadge = `<span class="field-req-star" style="color: var(--danger-600); margin-left: 2px; display: ${f.is_required ? 'inline' : 'none'};">*</span>`;

        if (f.field_type === 'heading') {
            previewHtml = `<h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;"><span class="field-label-text">${escapeHtml(f.label)}</span></h3>`;
        } else if (f.field_type === 'description') {
            previewHtml = `<p class="text-sm text-muted" style="margin: 0; line-height: 1.5;"><span class="field-desc-text">${escapeHtml(f.description || f.label)}</span></p>`;
        } else if (f.field_type === 'signature') {
            previewHtml = `
                <label class="form-label" style="font-weight: 700;"><span class="field-label-text">${escapeHtml(f.label)}</span> ${reqBadge}</label>
                <div style="border: 2px dashed #cbd5e1; border-radius: 8px; height: 75px; background: #fafafa; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 13px;">
                    ✍️ Area Tanda Tangan Digital
                </div>
            `;
        } else if (f.field_type === 'textarea') {
            previewHtml = `
                <label class="form-label" style="font-weight: 700;"><span class="field-label-text">${escapeHtml(f.label)}</span> ${reqBadge}</label>
                <textarea class="form-control" placeholder="${escapeHtml(f.placeholder)}" disabled style="min-height: 60px;"></textarea>
            `;
        } else if (f.field_type === 'dropdown') {
            previewHtml = `
                <label class="form-label" style="font-weight: 700;"><span class="field-label-text">${escapeHtml(f.label)}</span> ${reqBadge}</label>
                <select class="form-control" disabled>
                    <option>${escapeHtml(f.placeholder || '-- Pilih Opsi --')}</option>
                    ${f.options.map(opt => `<option>${escapeHtml(opt)}</option>`).join('')}
                </select>
            `;
        } else if (f.field_type === 'radio') {
            previewHtml = `
                <label class="form-label" style="font-weight: 700;"><span class="field-label-text">${escapeHtml(f.label)}</span> ${reqBadge}</label>
                <div class="flex flex-col gap-2">
                    ${f.options.map(opt => `
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" disabled> <span>${escapeHtml(opt)}</span>
                        </label>
                    `).join('')}
                </div>
            `;
        } else if (f.field_type === 'checkbox') {
            previewHtml = `
                <label class="form-label" style="font-weight: 700;"><span class="field-label-text">${escapeHtml(f.label)}</span> ${reqBadge}</label>
                <div class="flex flex-col gap-2">
                    ${f.options.map(opt => `
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" disabled> <span>${escapeHtml(opt)}</span>
                        </label>
                    `).join('')}
                </div>
            `;
        } else {
            previewHtml = `
                <label class="form-label" style="font-weight: 700;"><span class="field-label-text">${escapeHtml(f.label)}</span> ${reqBadge}</label>
                <input type="text" class="form-control" placeholder="${escapeHtml(f.placeholder)}" disabled>
            `;
        }

        // Conditional Logic Badge on Card
        const cond = (f.settings && f.settings.conditional_logic && f.settings.conditional_logic.enabled && f.settings.conditional_logic.target_field) ? f.settings.conditional_logic : null;
        let condBadgeHtml = '';
        if (cond) {
            condBadgeHtml = `<div class="mt-2" style="font-size: 11px; color: #92400e; background: #fef3c7; border: 1px solid #fde68a; padding: 3px 8px; border-radius: 5px; display: inline-flex; align-items: center; gap: 4px;">
                <span>🔀</span>
                <span><strong>${cond.action === 'show' ? 'Tampil' : 'Sembunyi'}</strong> jika <code>{{${escapeHtml(cond.target_field)}}}</code> ${cond.operator === 'equals' ? '==' : (cond.operator === 'not_equals' ? '!=' : cond.operator)} <strong>"${escapeHtml(cond.value || '')}"</strong></span>
            </div>`;
        }

        card.innerHTML = `
            <div class="field-toolbar">
                <button type="button" class="field-btn" onclick="event.stopPropagation(); moveField(${idx}, -1)" title="Pindah ke Atas">▲</button>
                <button type="button" class="field-btn" onclick="event.stopPropagation(); moveField(${idx}, 1)" title="Pindah ke Bawah">▼</button>
                <button type="button" class="field-btn" onclick="event.stopPropagation(); duplicateField(${idx})" title="Duplikasi">📋</button>
                <button type="button" class="field-btn delete" onclick="event.stopPropagation(); removeField(${idx})" title="Hapus">🗑️</button>
            </div>
            ${previewHtml}
            <div class="flex items-center gap-2 flex-wrap mt-2">
                <div class="text-sm text-muted" style="font-size: 11px;">
                    Tag: <code class="field-tag-var">{{${f.field_name}}}</code>
                </div>
                ${condBadgeHtml}
            </div>
        `;
        container.appendChild(card);
    });
}

function selectField(idx) {
    selectedIndex = idx;
    renderCanvas();
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
        is_required: true,
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
    showToast('success', `Field '${label}' ditambahkan!`);
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
    showToast('success', 'Field berhasil diduplikasi!');
}

function removeField(idx) {
    fields.splice(idx, 1);
    if (selectedIndex >= fields.length) selectedIndex = Math.max(0, fields.length - 1);
    renderCanvas();
    showToast('info', 'Field dihapus.');
}

function renderInspector() {
    const body = document.getElementById('inspector-body');
    if (fields.length === 0 || !fields[selectedIndex]) {
        body.innerHTML = `<p class="text-sm text-muted text-center" style="margin: 30px 0;">Belum ada field yang dipilih.</p>`;
        return;
    }

    const f = fields[selectedIndex];
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

    // Trigger fields dropdown (other fields)
    const triggerFields = fields.filter((item, i) => i !== selectedIndex && item.field_type !== 'heading' && item.field_type !== 'description');
    let triggerOptionsHtml = '';
    triggerFields.forEach(tf => {
        const isSel = (condLogic.target_field === tf.field_name);
        triggerOptionsHtml += `<option value="${escapeHtml(tf.field_name)}" ${isSel ? 'selected' : ''}>[${escapeHtml(tf.label)}] ({{${escapeHtml(tf.field_name)}}})</option>`;
    });

    let optionsEditorHtml = '';
    if (['dropdown', 'radio', 'checkbox'].includes(f.field_type)) {
        optionsEditorHtml = `
            <div class="form-group mb-3">
                <label class="form-label" style="font-weight: 700;">Daftar Pilihan Opsi</label>
                <div class="flex flex-col gap-2" id="inspector-options-list">
                    ${f.options.map((opt, optIdx) => `
                        <div class="flex items-center gap-2">
                            <input type="text" class="form-control" style="font-size: 13px; padding: 6px 10px;" value="${escapeHtml(opt)}" oninput="updateOption(${optIdx}, this.value)">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="removeOption(${optIdx})" style="padding: 4px 8px;">&times;</button>
                        </div>
                    `).join('')}
                </div>
                <button type="button" class="btn btn-soft-primary btn-sm w-full mt-2" onclick="addOption()">
                    + Tambah Pilihan
                </button>
            </div>
        `;
    }

    body.innerHTML = `
        <div class="form-group mb-3">
            <label class="form-label" style="font-weight: 700;">Label Pertanyaan</label>
            <input type="text" class="form-control" value="${escapeHtml(f.label)}" oninput="updateFieldProp('label', this.value)">
        </div>

        <div class="form-group mb-3">
            <label class="form-label" style="font-weight: 700;">
                Kunci Variable <code>{{...}}</code>
            </label>
            <input type="text" class="form-control" value="${escapeHtml(f.field_name)}" oninput="updateFieldProp('field_name', this.value)">
            <div class="form-help">Digunakan untuk menghubungkan pertanyaan ini ke template Word.</div>
        </div>

        ${f.field_type !== 'heading' && f.field_type !== 'description' && f.field_type !== 'signature' ? `
            <div class="form-group mb-3">
                <label class="form-label" style="font-weight: 700;">Placeholder / Teks Bantuan</label>
                <input type="text" class="form-control" value="${escapeHtml(f.placeholder)}" oninput="updateFieldProp('placeholder', this.value)">
            </div>
        ` : ''}

        ${optionsEditorHtml}

        <div class="form-group mb-3 pt-2" style="border-top: 1px solid var(--border-subtle);">
            <label class="flex items-center gap-2" style="cursor: pointer;">
                <input type="checkbox" ${f.is_required ? 'checked' : ''} onchange="updateFieldProp('is_required', this.checked)">
                <span style="font-size: 13px; font-weight: 700; color: var(--text-primary);">Wajib Diisi (Required)</span>
            </label>
        </div>

        <!-- ─── Logika Bersyarat (Conditional Logic) ─── -->
        <div class="form-group mb-0 pt-3" style="border-top: 1px solid var(--border-subtle);">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <label class="form-label mb-0" style="font-weight: 800; font-size: 13px; color: var(--text-primary);">
                        🔀 Logika Bersyarat
                    </label>
                    <div class="text-xs text-muted" style="font-size: 10px;">Tampilkan / sembunyikan pertanyaan ini</div>
                </div>
                <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 12px;">
                    <input type="checkbox" ${condLogic.enabled ? 'checked' : ''} onchange="toggleCondLogic(this.checked)">
                    <span style="font-weight: 700; color: ${condLogic.enabled ? '#4f46e5' : '#64748b'};">${condLogic.enabled ? 'Aktif' : 'Nonaktif'}</span>
                </label>
            </div>

            <div id="cond-logic-box" style="display: ${condLogic.enabled ? 'block' : 'none'}; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px;">
                <div class="alert alert-info mb-2" style="font-size: 11px; padding: 6px 10px; line-height: 1.4; margin-bottom: 10px;">
                    💡 Pertanyaan <strong>"${escapeHtml(f.label)}"</strong> ini akan otomatis:
                </div>

                <div class="form-group mb-2">
                    <label class="form-label" style="font-size: 11px; font-weight: 700;">Aksi untuk Pertanyaan Ini:</label>
                    <select class="form-control" style="font-size: 12px; padding: 5px 8px;" onchange="updateCondProp('action', this.value)">
                        <option value="show" ${condLogic.action === 'show' ? 'selected' : ''}>👁️ Ditampilkan (Show) — default tersembunyi</option>
                        <option value="hide" ${condLogic.action === 'hide' ? 'selected' : ''}>🚫 Disembunyikan (Hide) — default tampil</option>
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label class="form-label" style="font-size: 11px; font-weight: 700;">HANYA JIKA pertanyaan berikut:</label>
                    <select class="form-control" style="font-size: 12px; padding: 5px 8px;" onchange="updateCondProp('target_field', this.value)">
                        <option value="">-- Pilih Pertanyaan Pemicu --</option>
                        ${triggerOptionsHtml}
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label class="form-label" style="font-size: 11px; font-weight: 700;">Memiliki Jawaban:</label>
                    <select class="form-control" style="font-size: 12px; padding: 5px 8px;" onchange="updateCondProp('operator', this.value)">
                        <option value="equals" ${condLogic.operator === 'equals' ? 'selected' : ''}>Sama persis / mengandung ( = )</option>
                        <option value="not_equals" ${condLogic.operator === 'not_equals' ? 'selected' : ''}>Tidak sama dengan ( != )</option>
                        <option value="contains" ${condLogic.operator === 'contains' ? 'selected' : ''}>Mengandung kata (contains)</option>
                        <option value="not_empty" ${condLogic.operator === 'not_empty' ? 'selected' : ''}>Diisi / Tidak Kosong</option>
                        <option value="empty" ${condLogic.operator === 'empty' ? 'selected' : ''}>Kosong / Belum Diisi</option>
                    </select>
                </div>

                ${condLogic.operator !== 'not_empty' && condLogic.operator !== 'empty' ? `
                    <div class="form-group mb-0">
                        <label class="form-label" style="font-size: 11px; font-weight: 700;">Nilai Jawaban Pemicu:</label>
                        <input type="text" class="form-control" style="font-size: 12px; padding: 5px 8px;" placeholder="Contoh: Ya / Punya / Guru" value="${escapeHtml(condLogic.value || '')}" oninput="updateCondProp('value', this.value)">
                        <div class="form-help" style="font-size: 10px;">Masukkan kata kunci jawaban (misal: "Ya").</div>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
}

function toggleCondLogic(enabled) {
    if (!fields[selectedIndex]) return;
    if (!fields[selectedIndex].settings) fields[selectedIndex].settings = {};
    if (!fields[selectedIndex].settings.conditional_logic) {
        fields[selectedIndex].settings.conditional_logic = { action: 'show', target_field: '', operator: 'equals', value: 'Ya' };
    }
    fields[selectedIndex].settings.conditional_logic.enabled = enabled;
    renderInspector();
    renderCanvasOnly();
}

function updateCondProp(prop, value) {
    if (!fields[selectedIndex]) return;
    if (!fields[selectedIndex].settings) fields[selectedIndex].settings = {};
    if (!fields[selectedIndex].settings.conditional_logic) {
        fields[selectedIndex].settings.conditional_logic = { enabled: true, action: 'show', target_field: '', operator: 'equals', value: 'Ya' };
    }
    fields[selectedIndex].settings.conditional_logic[prop] = value;
    if (prop === 'operator') {
        renderInspector();
    }
    renderCanvasOnly();
}

function updateFieldProp(prop, value) {
    if (!fields[selectedIndex]) return;
    fields[selectedIndex][prop] = value;
    updateSelectedCardPreview();
}

function updateSelectedCardPreview() {
    if (!fields[selectedIndex]) return;
    const f = fields[selectedIndex];
    const card = document.querySelectorAll('.field-card')[selectedIndex];
    if (!card) {
        renderCanvas();
        return;
    }

    const labelElem = card.querySelector('.field-label-text');
    if (labelElem) {
        labelElem.textContent = f.label;
    }

    const tagElem = card.querySelector('.field-tag-var');
    if (tagElem) {
        tagElem.textContent = '{{' + f.field_name + '}}';
    }

    const inputElem = card.querySelector('.form-control');
    if (inputElem && f.placeholder !== undefined) {
        inputElem.placeholder = f.placeholder;
    }

    const descElem = card.querySelector('.field-desc-text');
    if (descElem) {
        descElem.textContent = f.description || f.label;
    }

    const reqElem = card.querySelector('.field-req-star');
    if (reqElem) {
        reqElem.style.display = f.is_required ? 'inline' : 'none';
    }
}

function addOption() {
    if (!fields[selectedIndex]) return;
    fields[selectedIndex].options.push(`Pilihan ${fields[selectedIndex].options.length + 1}`);
    renderCanvas();
}

function updateOption(optIndex, value) {
    if (!fields[selectedIndex]) return;
    fields[selectedIndex].options[optIndex] = value;
    // Update preview in canvas without rebuilding inspector
    renderCanvasOnly();
}

function removeOption(optIndex) {
    if (!fields[selectedIndex]) return;
    if (fields[selectedIndex].options.length <= 1) {
        showToast('warning', 'Minimal harus ada 1 pilihan.');
        return;
    }
    fields[selectedIndex].options.splice(optIndex, 1);
    renderCanvas();
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
    document.getElementById('share-modal-backdrop').style.display = 'block';
    document.getElementById('share-modal').style.display = 'block';
}

function closeShareModal() {
    document.getElementById('share-modal-backdrop').style.display = 'none';
    document.getElementById('share-modal').style.display = 'none';
}

function copyShareUrl() {
    const input = document.getElementById('share-url-input');
    input.select();
    navigator.clipboard.writeText(input.value);
    showToast('success', 'Link formulir berhasil disalin ke clipboard!');
}

function escapeHtml(text) {
    if (!text) return '';
    return text.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
</script>
