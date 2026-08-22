<?php
use App\Core\CSRF;
use App\Core\Session;
?>

<div class="bento-grid">
    
    <!-- 1. Bento Hero Banner (Span 12) -->
    <div class="bento-col-12 bento-hero fade-in">
        <div class="bento-hero-left">
            <div class="bento-hero-icon">📄</div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h2 class="bento-hero-title" style="margin: 0;">
                        Tambah Template Surat Word (.DOCX)
                    </h2>
                    <span class="badge badge-primary" style="font-size: 11px; font-weight: 700;">
                        Pilih Metode Pembuatan
                    </span>
                </div>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Rancang dan buat surat resmi di Editor Visual Profesional atau unggah berkas Microsoft Word (.docx) yang sudah Anda miliki.
                </div>
            </div>
        </div>
        <div class="bento-hero-actions">
            <a href="<?= url('templates') ?>" class="btn btn-secondary btn-sm" style="font-weight: 600;">
                &larr; Kembali ke Daftar Template
            </a>
        </div>
    </div>

    <!-- 2. Bento Card: Option A — Visual Letter Editor (Span 6) -->
    <div class="bento-col-6 bento-card fade-in" style="border: 2px solid #4f46e5; background: linear-gradient(135deg, #ffffff 0%, #f5f3ff 100%); justify-content: space-between;">
        <div>
            <div class="flex items-center justify-between mb-3">
                <span style="font-size: 38px;">✍️</span>
                <span class="badge badge-primary" style="font-weight: 800; padding: 4px 12px;">Fitur Baru &amp; Disarankan</span>
            </div>
            <h3 style="font-size: 18px; font-weight: 900; color: #1e1b4b; margin-bottom: 8px;">
                Buat Surat di Editor Visual Profesional
            </h3>
            <p class="text-sm text-muted" style="margin-bottom: 18px; line-height: 1.6;">
                Tulis dan rancang surat langsung di browser dengan standar surat resmi Indonesia (Kop Surat, Format A4, Font Times New Roman, Tabel, Tanda Tangan, QR Code, dan Tag Variabel). Otomatis dikonversi ke Microsoft Word (.docx)!
            </p>

            <div style="background: rgba(255,255,255,0.9); border: 1px solid #c7d2fe; border-radius: 12px; padding: 12px 14px; margin-bottom: 20px;">
                <div style="font-size: 11.5px; font-weight: 800; color: #3730a3; margin-bottom: 8px;">⚡ Pilihan Preset Cepat Siap Pakai:</div>
                <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                    <a href="<?= url('templates/editor?preset=surat-keterangan') ?>" class="btn btn-xs btn-white shadow-sm">📜 Surat Keterangan</a>
                    <a href="<?= url('templates/editor?preset=surat-tugas') ?>" class="btn btn-xs btn-white shadow-sm">🚗 Surat Tugas</a>
                    <a href="<?= url('templates/editor?preset=surat-rekomendasi') ?>" class="btn btn-xs btn-white shadow-sm">⭐ Surat Rekomendasi</a>
                    <a href="<?= url('templates/editor?preset=surat-pernyataan') ?>" class="btn btn-xs btn-white shadow-sm">✍️ Surat Pernyataan</a>
                    <a href="<?= url('templates/editor?preset=kwitansi') ?>" class="btn btn-xs btn-white shadow-sm">🧾 Kwitansi</a>
                </div>
            </div>
        </div>

        <div style="padding-top: 10px;">
            <a href="<?= url('templates/editor') ?>" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center; font-weight: 800; box-shadow: 0 4px 14px rgba(79,70,229,0.35);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Buka Editor Surat Sekarang
            </a>
        </div>
    </div>

    <!-- 3. Bento Card: Option B — Upload Word File (Span 6) -->
    <div class="bento-col-6 bento-card fade-in" style="justify-content: space-between; border: 2px solid #e2e8f0; background: #ffffff;">
        <div>
            <div class="flex items-center justify-between mb-3">
                <span style="font-size: 38px;">📤</span>
                <span class="badge badge-secondary" style="font-weight: 700;">Upload File Asli</span>
            </div>
            <h3 style="font-size: 18px; font-weight: 900; color: #0f172a; margin-bottom: 8px;">
                Upload Berkas Word (.DOCX)
            </h3>
            <p class="text-sm text-muted" style="margin-bottom: 18px; line-height: 1.6;">
                Sudah memiliki file dokumen Microsoft Word di komputer? Unggah file <strong>.docx</strong> Anda, sistem akan mengekstrak seluruh tag <code>{{variabel}}</code> dan tata letak secara otomatis.
            </p>

            <div style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: 12px; padding: 12px 14px; margin-bottom: 20px;">
                <div style="font-size: 11.5px; font-weight: 800; color: var(--text-secondary); margin-bottom: 6px;">Ketentuan Berkas Word:</div>
                <ul style="margin: 0; padding-left: 18px; font-size: 12px; color: var(--text-secondary); line-height: 1.5;">
                    <li>Format berkas: <strong>.docx</strong> (Maksimal 10 MB)</li>
                    <li>Format tag variabel: <code>{{nama_variable}}</code></li>
                    <li>Format font, tabel, margin, dan logo dipertahankan 100%</li>
                </ul>
            </div>
        </div>

        <div style="padding-top: 10px;">
            <button type="button" class="btn btn-secondary btn-lg" style="width: 100%; justify-content: center; font-weight: 700;" onclick="toggleUploadForm()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Unggah Berkas .DOCX
            </button>
        </div>
    </div>

    <!-- 4. Bento Card: Upload Form (Span 12, Expandable) -->
    <div class="bento-col-12 bento-card fade-in" id="upload-form-card" style="display: none; border-radius: 20px; border: 1px solid #c7d2fe; background: #ffffff;">
        <div class="flex items-center justify-between mb-4 pb-3" style="border-bottom: 1px solid var(--border-subtle);">
            <h3 style="font-size: 16px; font-weight: 800; margin: 0; color: var(--text-primary);">
                📤 Form Upload Berkas Template Word (.DOCX)
            </h3>
            <button type="button" class="btn btn-ghost btn-sm" onclick="toggleUploadForm()">&times; Tutup Form</button>
        </div>

        <form method="POST" action="<?= url('templates/store') ?>" enctype="multipart/form-data" id="form-upload-template">
            <?= CSRF::field() ?>

            <div class="form-group mb-4">
                <label class="form-label" for="name">Nama Template <span class="required">*</span></label>
                <input type="text" id="name" name="name" class="form-control" 
                       placeholder="Contoh: Surat Keterangan Siswa / Surat Tugas Resmi" 
                       value="<?= Session::old('name') ?>" required>
                <div class="form-help">Beri nama yang jelas untuk mempermudah pemilihan template saat generate surat.</div>
            </div>

            <div class="grid-2 mb-4">
                <div class="form-group mb-0">
                    <label class="form-label" for="category">Kategori Surat <span class="required">*</span></label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="Surat Keterangan">Surat Keterangan</option>
                        <option value="Surat Tugas">Surat Tugas</option>
                        <option value="Surat Pernyataan">Surat Pernyataan</option>
                        <option value="Surat Rekomendasi">Surat Rekomendasi</option>
                        <option value="Sertifikat & Piagam">Sertifikat & Piagam</option>
                        <option value="Kwitansi & Invoice">Kwitansi & Invoice</option>
                        <option value="Umum">Umum</option>
                    </select>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label" for="status">Status Template</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active">Aktif (Siap Digunakan)</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Drag & Drop File Upload Box -->
            <div class="form-group mb-4">
                <label class="form-label">Berkas Template Word (.DOCX) <span class="required">*</span></label>
                
                <div id="dropzone" style="border: 2px dashed #6366f1; border-radius: 12px; padding: 36px 20px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.2s ease;" onclick="document.getElementById('file-input').click()">
                    <div style="font-size: 42px; margin-bottom: 12px;">📄</div>
                    <h4 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0 0 6px;">
                        Upload Template Microsoft Word
                    </h4>
                    <p class="text-sm text-muted" style="margin: 0 0 16px;">
                        Drag & drop file <strong>.docx</strong> di sini atau klik untuk memilih file dari komputer
                    </p>
                    <button type="button" class="btn btn-soft-primary btn-sm" style="pointer-events: none;">
                        Pilih File .DOCX
                    </button>
                    <div style="font-size: 11px; color: var(--text-tertiary); margin-top: 12px;">
                        Maksimal ukuran file: <strong>10 MB</strong> &bull; Format: <strong>.docx</strong>
                    </div>
                </div>

                <input type="file" id="file-input" name="template_file" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" style="display: none;" onchange="handleFileSelect(this)" required>

                <!-- Selected File Preview Badge -->
                <div id="file-preview-card" style="display: none; margin-top: 12px; background: #eef2ff; border: 1px solid #c7d2fe; padding: 12px 16px; border-radius: 8px;">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span style="font-size: 24px;">📝</span>
                            <div>
                                <strong id="file-name" style="font-size: 14px; color: #3730a3;">nama_file.docx</strong>
                                <div id="file-size" style="font-size: 12px; color: #6366f1;">0 KB</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="removeSelectedFile(event)">Ganti File</button>
                    </div>
                </div>
            </div>

            <div class="form-group mb-5">
                <label class="form-label" for="description">Deskripsi / Catatan Tambahan (Opsional)</label>
                <textarea id="description" name="description" class="form-control" style="min-height: 80px;" placeholder="Tuliskan keterangan mengenai format atau penggunaan surat ini..."><?= Session::old('description') ?></textarea>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="btn btn-primary btn-lg" id="btn-submit">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Template &amp; Lanjut Mapping
                </button>
                <button type="button" class="btn btn-secondary" onclick="toggleUploadForm()">Batal</button>
            </div>
        </form>
    </div>

</div>

<script>
function toggleUploadForm() {
    const card = document.getElementById('upload-form-card');
    if (card.style.display === 'none') {
        card.style.display = 'block';
        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        card.style.display = 'none';
    }
}

const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('file-input');
const filePreview = document.getElementById('file-preview-card');
const fileName = document.getElementById('file-name');
const fileSize = document.getElementById('file-size');
const nameInput = document.getElementById('name');

// Drag & Drop handlers
['dragenter', 'dragover'].forEach(eventName => {
    dropzone.addEventListener(eventName, (e) => {
        e.preventDefault();
        dropzone.style.background = '#eef2ff';
        dropzone.style.borderColor = '#4f46e5';
    }, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, (e) => {
        e.preventDefault();
        dropzone.style.background = '#f8fafc';
        dropzone.style.borderColor = '#6366f1';
    }, false);
});

dropzone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    if (files.length > 0) {
        fileInput.files = files;
        handleFileSelect(fileInput);
    }
});

function handleFileSelect(input) {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];

    if (!file.name.toLowerCase().endsWith('.docx')) {
        showToast('error', 'Hanya berkas berekstensi .docx yang didukung.');
        input.value = '';
        return;
    }

    fileName.textContent = file.name;
    fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
    filePreview.style.display = 'block';
    dropzone.style.display = 'none';

    // Auto-suggest template name if empty
    if (!nameInput.value || nameInput.value.trim() === '') {
        const rawName = file.name.replace(/\.docx$/i, '').replace(/[-_]+/g, ' ');
        nameInput.value = rawName.replace(/\b\w/g, l => l.toUpperCase());
    }
}

function removeSelectedFile(e) {
    e.stopPropagation();
    fileInput.value = '';
    filePreview.style.display = 'none';
    dropzone.style.display = 'block';
}
</script>
