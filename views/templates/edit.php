<?php
use App\Core\CSRF;
?>

<div style="max-width: 900px; margin: 0 auto;">
    
    <!-- ─── Word (.docx) Upload Dropzone Card with Animated Progress Bar ─── -->
    <div class="card mb-4" id="docx-dropzone-card" style="background: #ffffff; border: 2px dashed #6366f1; box-shadow: var(--shadow-sm); transition: all 0.3s ease;">
        <div class="card-body" style="padding: 20px; text-align: center;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">
                Ganti / Unggah Ulang dari Microsoft Word (.docx)
            </h3>
            <p class="text-sm text-muted" style="max-width: 540px; margin: 0 auto 12px;">
                Ingin memperbarui template dengan format dari berkas Word baru? Unggah berkas <strong>.docx</strong> di bawah.
            </p>

            <input type="file" id="docx-file-input" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" style="display: none;" onchange="handleDocxUpload(this)">
            
            <div id="docx-upload-btn-wrapper">
                <button type="button" class="btn btn-soft-primary btn-sm" onclick="document.getElementById('docx-file-input').click()" id="btn-upload-docx">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <span id="btn-upload-docx-text">Unggah Berkas Word (.docx)</span>
                </button>
            </div>

            <!-- ─── Interactive Loading / Conversion Progress Bar ─── -->
            <div id="docx-progress-container" style="display: none; max-width: 500px; margin: 14px auto 0; text-align: left;" class="fade-in">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span id="docx-progress-spinner" class="spinner-inline"></span>
                        <strong id="docx-progress-status" style="font-size: 13px; color: var(--primary-700);">Memulai konversi...</strong>
                    </div>
                    <span id="docx-progress-percent" style="font-size: 13px; font-weight: 800; color: var(--primary-700);">0%</span>
                </div>

                <!-- Bar Container -->
                <div style="width: 100%; height: 9px; background: #e0e7ff; border-radius: 99px; overflow: hidden; position: relative;">
                    <div id="docx-progress-bar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #6366f1, #4f46e5); border-radius: 99px; transition: width 0.35s ease;"></div>
                </div>

                <div id="docx-progress-filename" class="text-sm text-muted mt-2" style="font-size: 11px; text-align: center;">
                    Memproses file...
                </div>
            </div>

            <!-- Detected Tags Notification -->
            <div id="docx-tags-notification" class="mt-3 fade-in" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 14px; border-radius: 10px; text-align: left;">
                <div class="flex items-center gap-2 mb-1">
                    <span style="font-size: 16px;">✅</span>
                    <strong style="color: #166534; font-size: 13px;">Format Word Berhasil Dikonversi ke Editor!</strong>
                </div>
                <div class="text-sm" id="docx-tags-list" style="color: #15803d; line-height: 1.6;"></div>
            </div>
        </div>
    </div>

    <div class="card" id="template-editor-card">
        <div class="card-header" style="padding: 20px 24px;">
            <div class="flex items-center justify-between">
                <div>
                    <h2 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin: 0;">
                        Edit Template: <?= e($template->name) ?>
                    </h2>
                </div>
                <a href="<?= url('templates') ?>" class="btn btn-secondary btn-sm">&larr; Kembali</a>
            </div>
        </div>

        <div class="card-body" style="padding: 24px;">
            <form method="POST" action="<?= url("templates/{$template->id}/update") ?>">
                <?= CSRF::field() ?>

                <div class="grid-2 mb-4">
                    <div class="form-group mb-0">
                        <label class="form-label">Nama Template <span class="required">*</span></label>
                        <input type="text" name="name" id="template-name-input" class="form-control" value="<?= e($template->name) ?>" required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-control">
                            <option value="Pendaftaran & Event" <?= $template->category === 'Pendaftaran & Event' ? 'selected' : '' ?>>Pendaftaran & Event</option>
                            <option value="Surat & Legal" <?= $template->category === 'Surat & Legal' ? 'selected' : '' ?>>Surat & Legal</option>
                            <option value="Sertifikat" <?= $template->category === 'Sertifikat' ? 'selected' : '' ?>>Sertifikat</option>
                            <option value="Invoice & Kwitansi" <?= $template->category === 'Invoice & Kwitansi' ? 'selected' : '' ?>>Invoice & Kwitansi</option>
                            <option value="Umum" <?= $template->category === 'Umum' ? 'selected' : '' ?>>Umum</option>
                        </select>
                    </div>
                </div>

                <!-- Variable Chips Helper -->
                <div class="card mb-4" style="background: #f8fafc; border-color: var(--border-subtle);">
                    <div class="card-body" style="padding: 16px;">
                        <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">
                            💡 Tag Variabel Dinamis (Klik untuk menyisipkan ke dalam dokumen):
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            <button type="button" class="btn btn-sm btn-soft-primary" onclick="insertTag('{{nama_lengkap}}')"><code>{{nama_lengkap}}</code></button>
                            <button type="button" class="btn btn-sm btn-soft-primary" onclick="insertTag('{{email}}')"><code>{{email}}</code></button>
                            <button type="button" class="btn btn-sm btn-soft-primary" onclick="insertTag('{{no_hp}}')"><code>{{no_hp}}</code></button>
                            <button type="button" class="btn btn-sm btn-soft-primary" onclick="insertTag('{{nomor_dokumen}}')"><code>{{nomor_dokumen}}</code></button>
                            <button type="button" class="btn btn-sm btn-soft-primary" onclick="insertTag('{{tanggal_submit}}')"><code>{{tanggal_submit}}</code></button>
                            <button type="button" class="btn btn-sm btn-soft-primary" onclick="insertTag('{{tanda_tangan}}')"><code>{{tanda_tangan}}</code></button>
                            <button type="button" class="btn btn-sm btn-soft-primary" onclick="insertTag('{{token_verifikasi}}')"><code>{{token_verifikasi}}</code></button>
                        </div>
                    </div>
                </div>

                <!-- Template Content -->
                <div class="form-group mb-4">
                    <label class="form-label">Format / Isi Dokumen (HTML / Teks) <span class="required">*</span></label>
                    <textarea name="content" id="template-editor" class="form-control" style="font-family: monospace; font-size: 13px; min-height: 340px;" required><?= e($template->content) ?></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Perbarui Template
                    </button>
                    <a href="<?= url('templates') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.spinner-inline {
    width: 14px;
    height: 14px;
    border: 2px solid #c7d2fe;
    border-top-color: #4f46e5;
    border-radius: 50%;
    display: inline-block;
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
function insertTag(tag) {
    const editor = document.getElementById('template-editor');
    const start = editor.selectionStart;
    const end = editor.selectionEnd;
    const text = editor.value;
    editor.value = text.substring(0, start) + tag + text.substring(end);
    editor.focus();
    editor.selectionStart = editor.selectionEnd = start + tag.length;
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function handleDocxUpload(input) {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];

    const btnWrapper = document.getElementById('docx-upload-btn-wrapper');
    const progressContainer = document.getElementById('docx-progress-container');
    const progressBar = document.getElementById('docx-progress-bar');
    const progressPercent = document.getElementById('docx-progress-percent');
    const progressStatus = document.getElementById('docx-progress-status');
    const progressFilename = document.getElementById('docx-progress-filename');
    const notif = document.getElementById('docx-tags-notification');
    const tagsList = document.getElementById('docx-tags-list');

    btnWrapper.style.display = 'none';
    notif.style.display = 'none';
    progressContainer.style.display = 'block';

    const updateProgress = (pct, statusText) => {
        progressBar.style.width = pct + '%';
        progressPercent.textContent = pct + '%';
        progressStatus.textContent = statusText;
    };

    progressFilename.textContent = `Berkas: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
    updateProgress(15, 'Membaca berkas Word...');
    await sleep(250);

    const formData = new FormData();
    formData.append('docx_file', file);
    formData.append('_token', getCSRFToken());

    updateProgress(45, 'Mengekstrak paragraf, perataan teks & tabel...');

    try {
        const responsePromise = fetch('<?= url("api/templates/upload-docx") ?>', {
            method: 'POST',
            body: formData
        });

        await sleep(350);
        updateProgress(75, 'Memindai tag variabel {{...}}...');

        const response = await responsePromise;
        const result = await response.json();

        if (result.success) {
            updateProgress(100, 'Konversi Selesai!');
            await sleep(300);

            document.getElementById('template-editor').value = result.data.html;

            progressContainer.style.display = 'none';
            btnWrapper.style.display = 'block';
            notif.style.display = 'block';

            if (result.data.detected_tags && result.data.detected_tags.length > 0) {
                tagsList.innerHTML = 'Tag variabel yang terdeteksi dari Word: ' + 
                    result.data.detected_tags.map(t => `<code style="background: #ffffff; border: 1px solid #86efac; color: #166534; padding: 2px 6px; border-radius: 4px; font-weight: bold; margin: 0 3px;">{{${t}}}</code>`).join(' ');
            } else {
                tagsList.innerHTML = '💡 Format teks & tabel berhasil dimuat ke editor.';
            }

            showToast('success', 'Format dokumen dari Word berhasil dimuat!');

            setTimeout(() => {
                document.getElementById('template-editor-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 300);
        } else {
            throw new Error(result.message || 'Gagal membaca berkas Word.');
        }
    } catch (err) {
        progressContainer.style.display = 'none';
        btnWrapper.style.display = 'block';
        showToast('error', err.message || 'Terjadi kesalahan saat upload berkas.');
    } finally {
        input.value = '';
    }
}
</script>
