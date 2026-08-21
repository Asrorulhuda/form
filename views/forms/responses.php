<?php 
use App\Core\View;
use App\Core\CSRF;
?>

<!-- Header -->
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <a href="<?= url("forms/{$form->id}/builder") ?>" class="btn btn-secondary btn-sm">&larr; Buka Builder</a>
            <h2 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin: 0;">
                Data Responden: <?= e($form->title) ?>
            </h2>
        </div>
        <p class="text-sm text-muted">
            Total: <strong><?= count($responses) ?></strong> responden telah mengisi formulir ini.
        </p>
    </div>

    <div class="flex gap-2 flex-wrap items-center">
        <!-- Open Public Form -->
        <a href="<?= url($form->slug) ?>" target="_blank" class="btn btn-secondary btn-sm" title="Buka Form Publik">
            🔗 Form Publik
        </a>

        <!-- Print -->
        <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()" title="Cetak Tabel">
            🖨️ Cetak
        </button>

        <!-- Export to Excel -->
        <?php if (!empty($responses)): ?>
            <a href="<?= url("forms/{$form->id}/responses/export") ?>" class="btn btn-primary btn-sm" style="font-weight: 700; background: #16a34a; border-color: #16a34a;" title="Unduh data dalam format Excel">
                📊 Export ke Excel (.xlsx)
            </a>

            <!-- Clear All Responses -->
            <form action="<?= url("forms/{$form->id}/responses/clear") ?>" method="POST" style="display: inline;" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS SEMUA data responden pada form ini? Tindakan ini tidak dapat dibatalkan!');">
                <?= CSRF::field() ?>
                <button type="submit" class="btn btn-danger btn-sm" title="Hapus semua data responden">
                    🗑️ Kosongkan Data
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <?php if (!empty($responses)): ?>
        <div class="card-header" style="padding: 12px 18px; background: #fafafa; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div class="flex items-center gap-2">
                <span class="badge badge-primary"><?= count($responses) ?> Data Tersimpan</span>
                <span class="badge badge-secondary"><?= count($fields) ?> Pertanyaan</span>
            </div>

            <!-- Quick Filter Input -->
            <div style="max-width: 260px; width: 100%;">
                <input type="text" id="table-search" class="form-control form-control-sm" placeholder="🔍 Cari data responden..." oninput="filterResponseTable(this.value)" style="font-size: 12px;">
            </div>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table class="table" id="response-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">#</th>
                    <th style="width: 130px;">Waktu Submit</th>
                    <?php foreach ($fields as $field): ?>
                        <?php if (!in_array($field->field_type, ['heading', 'description'])): ?>
                            <th><?= e($field->label) ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (!empty($form->template_id) || !empty($docsMap)): ?>
                        <th style="width: 140px; text-align: center;">Berkas Dokumen</th>
                    <?php endif; ?>
                    <th style="width: 110px;">IP Address</th>
                    <th style="width: 80px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($responses)): ?>
                    <?php $no = 1; foreach ($responses as $r): ?>
                        <tr class="response-row">
                            <td style="text-align: center;"><strong><?= $no++ ?></strong></td>
                            <td style="white-space: nowrap;">
                                <div style="font-weight: 600; font-size: 13px;"><?= date('d/m/Y', strtotime($r->submitted_at)) ?></div>
                                <div class="text-sm text-muted" style="font-size: 11px;"><?= date('H:i:s', strtotime($r->submitted_at)) ?> WIB</div>
                            </td>

                            <?php foreach ($fields as $field): ?>
                                <?php if (!in_array($field->field_type, ['heading', 'description'])): ?>
                                    <td>
                                        <?php 
                                        $val = $valuesMap[$r->id][$field->id] ?? '-';
                                        if ($field->field_type === 'signature' && str_starts_with($val, 'data:image')): ?>
                                            <img src="<?= $val ?>" alt="Signature" style="max-height: 38px; border: 1px solid #e2e8f0; border-radius: 4px; background: white; padding: 2px;">
                                        <?php elseif (in_array($field->field_type, ['file', 'image']) && (str_starts_with($val, 'http') || str_starts_with($val, 'storage/'))): ?>
                                            <a href="<?= str_starts_with($val, 'http') ? $val : asset($val) ?>" target="_blank" class="badge badge-primary" style="font-size: 11px;">
                                                📎 Lihat Berkas &nearr;
                                            </a>
                                        <?php else: ?>
                                            <span style="font-size: 13px; color: var(--text-primary);"><?= e($val) ?></span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>

                            <?php if (!empty($form->template_id) || !empty($docsMap)): ?>
                                <td style="text-align: center;">
                                    <?php $doc = $docsMap[$r->id] ?? null; ?>
                                    <?php if ($doc): ?>
                                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                            <a href="<?= url("document/{$doc->verification_token}") ?>" target="_blank" class="btn btn-primary btn-sm" style="padding: 3px 8px; font-size: 11px; text-decoration: none; font-weight: 700; width: 100%; justify-content: center;">
                                                📄 Buka Berkas
                                            </a>
                                            <span class="text-muted" style="font-size: 10px; font-family: monospace;" title="Nomor Dokumen">
                                                <?= e($doc->document_number) ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 11px;">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>

                            <td><code style="font-size: 11px; color: var(--text-muted);"><?= e($r->ip_address) ?></code></td>

                            <!-- Action Column: Delete Row -->
                            <td style="text-align: center;">
                                <form action="<?= url("forms/{$form->id}/responses/{$r->id}/delete") ?>" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus baris respons ini?');">
                                    <?= CSRF::field() ?>
                                    <button type="submit" class="btn btn-secondary btn-sm" style="padding: 4px 8px; color: var(--danger-600); border-color: #fecdd3;" title="Hapus respons ini">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= count($fields) + 4 ?>">
                            <div class="empty-state" style="padding: 48px 20px;">
                                <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--primary-400); margin-bottom: 12px;">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                <p class="empty-state-title">Belum ada respons masuk</p>
                                <p class="empty-state-desc">Bagikan link form publik Anda kepada responden untuk mulai mengumpulkan data.</p>
                                <div class="flex justify-center gap-2 mt-3">
                                    <a href="<?= url("forms/{$form->id}/builder") ?>" class="btn btn-primary btn-sm">Buka Builder</a>
                                    <a href="<?= url($form->slug) ?>" target="_blank" class="btn btn-secondary btn-sm">Buka Form Publik</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Table Live Search Filter Script -->
<script>
function filterResponseTable(query) {
    const q = query.toLowerCase().trim();
    const rows = document.querySelectorAll('.response-row');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>
