<?php
use App\Core\CSRF;
use App\Core\Session;
?>

<div style="max-width: 760px; margin: 0 auto;">
    <div class="card">
        <div class="card-header" style="padding: 24px 28px;">
            <div class="flex items-center justify-between">
                <div>
                    <h2 style="font-size: 20px; font-weight: 800; color: var(--text-primary); margin: 0;">
                        📄 Tambah Template Surat Word (.DOCX)
                    </h2>
                    <p class="text-sm text-muted" style="margin: 4px 0 0;">
                        Upload file template dari Microsoft Word. Sistem akan membaca seluruh variable <code>{{...}}</code> secara otomatis.
                    </p>
                </div>
                <a href="<?= url('templates') ?>" class="btn btn-secondary btn-sm">&larr; Kembali</a>
            </div>
        </div>

        <div class="card-body" style="padding: 28px;">
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

                <!-- ─── Modern Drag & Drop File Upload Box ─── -->
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

                <!-- Guidance Info Box -->
                <div class="card mb-5" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                    <div class="card-body" style="padding: 16px 20px;">
                        <div class="flex items-start gap-3">
                            <span style="font-size: 20px;">💡</span>
                            <div class="text-sm" style="color: #166534; line-height: 1.6;">
                                <strong>Tips Penulisan Variable di Word:</strong><br>
                                Gunakan format <code>{{nama_variable}}</code> (contoh: <code>{{nama_siswa}}</code>, <code>{{nomor_surat}}</code>, <code>{{tanggal_surat}}</code>). Desain, font, logo, kop surat, dan tabel di Word Anda akan dipertahankan 100%!
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="btn btn-primary btn-lg" id="btn-submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Simpan Template & Lanjut Mapping
                    </button>
                    <a href="<?= url('templates') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
