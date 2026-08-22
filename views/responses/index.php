<?php 
use App\Core\View;
use App\Core\CSRF;
?>

<div class="bento-grid">
    <!-- 1. Header Bento Hero Card -->
    <div class="bento-col-12 bento-hero fade-in" style="background: #ffffff; border: 1px solid var(--border-subtle);">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: var(--success-50); color: var(--success-700); border: 1px solid rgba(16,185,129,0.3);">
                📊
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-primary);">
                        <?= e($title ?? 'Data Respons Formulir') ?>
                    </h2>
                    <span class="badge badge-success" style="font-size: 11px; font-weight: 700;">
                        Total: <?= number_format($total) ?> Data Masuk
                    </span>
                </div>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Pantau pengisian formulir online, kirim notifikasi WhatsApp, dan unduh data ke Excel (.xlsx).
                </div>
            </div>
        </div>
        <div class="bento-hero-actions">
            <a href="<?= url('responses/export' . ($selectedForm ? "?form_id={$selectedForm}" : '')) ?>" class="btn btn-primary btn-sm" style="background: #16a34a; border-color: #16a34a; font-weight: 700; box-shadow: 0 4px 12px rgba(22,163,74,0.25);" title="Unduh data respons dalam format Excel (.xlsx)">
                📊 Export ke Excel (.xlsx)
            </a>
            <?php if ($total > 0): ?>
                <form action="<?= url('responses/clear') ?>" method="POST" style="display: inline;" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS SEMUA data respons <?= $selectedForm ? 'pada formulir yang dipilih' : 'di seluruh sistem' ?>? Tindakan ini tidak dapat dibatalkan!');">
                    <?= CSRF::field() ?>
                    <?php if ($selectedForm): ?>
                        <input type="hidden" name="form_id" value="<?= e($selectedForm) ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--danger-600); font-weight: 600;" title="Hapus seluruh data respons">
                        🗑️ Kosongkan
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. Bento Filter Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 16px 20px;">
        <form method="GET" action="<?= url('responses') ?>" class="flex items-center gap-3 flex-wrap" style="margin: 0;">
            <label class="text-xs text-muted" style="font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Filter Berdasarkan Formulir:</label>
            <select name="form_id" class="form-control form-control-sm" style="max-width: 320px; font-size: 13px; border-radius: 10px;">
                <option value="">-- Semua Formulir --</option>
                <?php foreach ($formsList as $f): ?>
                    <option value="<?= $f->id ?>" <?= $selectedForm == $f->id ? 'selected' : '' ?>>
                        <?= e($f->title) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;">Terapkan Filter</button>
            <?php if ($selectedForm): ?>
                <a href="<?= url('responses') ?>" class="btn btn-ghost btn-sm" style="color: var(--text-muted);">&times; Reset Filter</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- 3. Bento Table Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="margin: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 110px;">ID Respons</th>
                        <th>Judul Formulir</th>
                        <th>Nama Responden</th>
                        <th style="width: 150px;">Waktu Submit</th>
                        <th style="width: 160px; text-align: center;">Berkas Dokumen</th>
                        <th style="width: 110px;">IP Address</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($responses)): ?>
                        <?php foreach ($responses as $r): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--primary-600); font-family: monospace; font-size: 13px;">
                                        #RESP-<?= $r->id ?>
                                    </strong>
                                </td>
                                <td>
                                    <div style="font-weight: 700; font-size: 13.5px; color: var(--text-primary);">
                                        <?= e($r->form_title) ?>
                                    </div>
                                    <div class="flex gap-2 text-xs text-muted mt-1">
                                        <a href="<?= url("forms/{$r->form_id}/responses") ?>" style="color: var(--primary-600); text-decoration: none; font-size: 11px; font-weight: 600;">
                                            👁️ Lihat Semua Jawaban Form Ini &rarr;
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 13px; font-weight: 600; color: var(--text-primary);">
                                        <?= e($r->respondent_name ?? 'Responden Publik') ?>
                                    </div>
                                    <span class="badge badge-secondary" style="font-size: 10px; margin-top: 2px;">
                                        <?= !empty($r->respondent_id) ? 'User Terdaftar' : 'Anonim' ?>
                                    </span>
                                </td>
                                <td style="white-space: nowrap;">
                                    <div style="font-weight: 600; font-size: 13px;"><?= date('d/m/Y', strtotime($r->submitted_at)) ?></div>
                                    <div class="text-sm text-muted" style="font-size: 11px;"><?= date('H:i:s', strtotime($r->submitted_at)) ?> WIB</div>
                                </td>

                                <!-- Generated Document Info -->
                                <td style="text-align: center;">
                                    <?php if (!empty($r->document_number)): ?>
                                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                            <a href="<?= url("document/{$r->verification_token}") ?>" target="_blank" class="btn btn-primary btn-sm" style="padding: 3px 8px; font-size: 11px; font-weight: 700; width: 100%; justify-content: center; text-decoration: none;">
                                                📄 Buka Berkas
                                            </a>
                                            <span class="text-muted" style="font-size: 10px; font-family: monospace;" title="Nomor Dokumen">
                                                <?= e($r->document_number) ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 11px;">-</span>
                                    <?php endif; ?>
                                </td>

                                <td><code style="font-size: 11px; color: var(--text-muted);"><?= e($r->ip_address) ?></code></td>

                                <!-- Action Column -->
                                <td style="text-align: center;">
                                    <div class="flex items-center justify-center gap-1">
                                        <button type="button" class="btn btn-secondary btn-sm" style="padding: 4px 8px; color: #10b981; border-color: #a7f3d0;" title="Kirim tanda terima / dokumen ke WhatsApp" onclick="openWaModal('<?= $r->id ?>', '<?= e(addslashes($r->form_title)) ?>', '<?= e($r->document_number ?? '') ?>')">
                                            💬
                                        </button>

                                        <a href="<?= url("forms/{$r->form_id}/responses") ?>" class="btn btn-secondary btn-sm" style="padding: 4px 8px;" title="Lihat detail jawaban formulir ini">
                                            👁️
                                        </a>

                                        <form action="<?= url("responses/{$r->id}/delete") ?>" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data respons #RESP-<?= $r->id ?> ini?');">
                                            <?= CSRF::field() ?>
                                            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 4px 8px; color: var(--danger-600); border-color: #fecdd3;" title="Hapus respons ini">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state" style="padding: 48px 20px; text-align: center;">
                                    <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--primary-400); margin-bottom: 12px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    <p class="empty-state-title" style="font-size: 16px; font-weight: 800; margin-bottom: 4px;">Belum ada respons masuk</p>
                                    <p class="empty-state-desc" style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">Respons dari pengguna yang mengisi formulir publik akan otomatis tampil di sini.</p>
                                    <div class="flex justify-center gap-2">
                                        <a href="<?= url('forms') ?>" class="btn btn-primary btn-sm">Lihat Daftar Formulir</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($lastPage > 1): ?>
            <div style="padding: 12px 20px; background: #fafafa; border-top: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between;">
                <div class="text-xs text-muted">
                    Halaman <strong><?= $page ?></strong> dari <strong><?= $lastPage ?></strong> (Total: <?= $total ?> data)
                </div>
                <div class="flex gap-1">
                    <?php if ($page > 1): ?>
                        <a href="<?= url('responses?page=' . ($page - 1) . ($selectedForm ? "&form_id={$selectedForm}" : '')) ?>" class="btn btn-secondary btn-sm">&larr; Sebelumnya</a>
                    <?php endif; ?>
                    <?php if ($page < $lastPage): ?>
                        <a href="<?= url('responses?page=' . ($page + 1) . ($selectedForm ? "&form_id={$selectedForm}" : '')) ?>" class="btn btn-secondary btn-sm">Berikutnya &rarr;</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Kirim WhatsApp -->
<div id="wa-modal" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div class="modal-dialog" style="background: white; border-radius: var(--radius-lg); max-width: 440px; width: 100%; padding: 24px; box-shadow: var(--shadow-lg);">
        <div class="flex justify-between items-center mb-3">
            <h3 style="font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
                💬 Kirim via WhatsApp Gateway
            </h3>
            <button type="button" onclick="closeWaModal()" style="border: none; background: transparent; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form method="POST" id="wa-send-form" action="">
            <?= CSRF::field() ?>
            <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 14px;">
                Kirimkan tautan tanda terima &amp; berkas dokumen formulir <strong id="wa-form-title"></strong> ke nomor WhatsApp responden.
            </p>

            <div class="form-group mb-4">
                <label class="form-label" style="font-weight: 600; font-size: 13px;">Nomor WhatsApp Tujuan <span class="required">*</span></label>
                <input type="text" name="phone" id="wa-phone-input" class="form-control" placeholder="Contoh: 081234567890 / 62812xxx" required>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="closeWaModal()">Batal</button>
                <button type="submit" class="btn btn-success" style="font-weight: 700;">
                    🚀 Kirim WhatsApp
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openWaModal(respId, formTitle, docNumber) {
    document.getElementById('wa-form-title').innerText = formTitle;
    document.getElementById('wa-send-form').action = '<?= url("responses") ?>/' + respId + '/send-wa';
    document.getElementById('wa-phone-input').value = '';
    const modal = document.getElementById('wa-modal');
    modal.style.display = 'flex';
}

function closeWaModal() {
    document.getElementById('wa-modal').style.display = 'none';
}
</script>
