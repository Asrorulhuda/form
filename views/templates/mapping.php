<?php
use App\Core\CSRF;
use App\Core\View;
?>

<!-- ─── Template Header Card ─── -->
<div class="card mb-4" style="background: #ffffff; border-left: 5px solid var(--primary-600);">
    <div class="card-body" style="padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="badge badge-primary"><?= e($template->category) ?></span>
                <span class="badge badge-success">Version <?= (int)$template->version ?></span>
            </div>
            <h2 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin: 0;">
                <?= e($template->name) ?>
            </h2>
            <div class="text-sm text-muted mt-1">
                Berkas Asli: <strong><?= e($template->original_filename ?: 'template.docx') ?></strong> &bull; Total <strong><?= count($variables) ?></strong> variable terdeteksi
            </div>
        </div>

        <div class="flex gap-2">
            <a href="<?= url("templates/{$template->id}/download") ?>" class="btn btn-secondary btn-sm" title="Download File Word Asli">
                📥 Unduh .DOCX
            </a>
            <a href="<?= url("templates/{$template->id}/versions") ?>" class="btn btn-secondary btn-sm" title="Lihat Riwayat Versi">
                📜 Versi Template
            </a>
            <a href="<?= url('templates') ?>" class="btn btn-secondary btn-sm">
                &larr; Daftar Template
            </a>
        </div>
    </div>
</div>

<!-- ─── Variable Mapping Table ─── -->
<div class="card">
    <div class="card-header" style="padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">
                🧩 Pengaturan Sumber Data Variable
            </h3>
            <p class="text-sm text-muted" style="margin: 2px 0 0;">
                Tentukan dari mana data setiap variable di dokumen Word akan diambil saat surat digenerate.
            </p>
        </div>

        <button type="button" class="btn btn-primary" onclick="document.getElementById('form-mapping').submit()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Simpan Pengaturan Mapping
        </button>
    </div>

    <div class="card-body" style="padding: 0;">
        <form method="POST" action="<?= url("templates/{$template->id}/mapping") ?>" id="form-mapping">
            <?= CSRF::field() ?>

            <div class="table-container">
                <table class="table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="width: 220px;">Variable di Word</th>
                            <th style="width: 200px;">Label Tampilan</th>
                            <th style="width: 200px;">Sumber Data</th>
                            <th>Kunci Sumber / Parameter</th>
                            <th style="width: 180px;">Nilai Default</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($variables)): ?>
                            <?php foreach ($variables as $v): ?>
                                <tr>
                                    <!-- Variable Tag -->
                                    <td>
                                        <div style="font-family: monospace; font-weight: 700; color: #4338ca; background: #e0e7ff; padding: 4px 10px; border-radius: 6px; display: inline-block; font-size: 13px;">
                                            {{<?= e($v->variable_name) ?>}}
                                        </div>
                                    </td>

                                    <!-- Label -->
                                    <td>
                                        <input type="text" name="mappings[<?= $v->id ?>][label]" class="form-control" style="font-size: 13px; padding: 6px 10px;" value="<?= e($v->label) ?>" required>
                                    </td>

                                    <!-- Source Type -->
                                    <td>
                                        <select name="mappings[<?= $v->id ?>][source_type]" class="form-control source-type-select" data-var-id="<?= $v->id ?>" style="font-size: 13px; padding: 6px 10px;" onchange="handleSourceTypeChange(<?= $v->id ?>, this.value)">
                                            <option value="form_response" <?= $v->source_type === 'form_response' ? 'selected' : '' ?>>📝 Form Response (Isian)</option>
                                            <option value="system" <?= $v->source_type === 'system' ? 'selected' : '' ?>>⚙️ System (Tanggal/No)</option>
                                            <option value="user" <?= $v->source_type === 'user' ? 'selected' : '' ?>>👤 User Pembuat</option>
                                            <option value="setting" <?= $v->source_type === 'setting' ? 'selected' : '' ?>>🏢 Pengaturan Instansi</option>
                                            <option value="custom" <?= $v->source_type === 'custom' ? 'selected' : '' ?>>✏️ Nilai Kustom</option>
                                        </select>
                                    </td>

                                    <!-- Source Key -->
                                    <td>
                                        <div id="wrapper-key-<?= $v->id ?>">
                                            <input type="text" name="mappings[<?= $v->id ?>][source_key]" id="input-key-<?= $v->id ?>" class="form-control" style="font-size: 13px; padding: 6px 10px;" value="<?= e($v->source_key ?: $v->variable_name) ?>" placeholder="Nama field / parameter...">
                                        </div>
                                    </td>

                                    <!-- Default Value -->
                                    <td>
                                        <input type="text" name="mappings[<?= $v->id ?>][default_value]" class="form-control" style="font-size: 13px; padding: 6px 10px;" value="<?= e($v->default_value) ?>" placeholder="Nilai fallback...">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center" style="padding: 40px;">
                                    <div class="empty-state">
                                        <p class="empty-state-title">Tidak Ada Variable Terdeteksi</p>
                                        <p class="empty-state-desc">Pastikan berkas Word Anda memiliki pola penulisan <code>{{nama_variable}}</code>.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer" style="padding: 16px 24px; background: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                <span class="text-sm text-muted">
                    💡 <strong>Tips:</strong> Mapping ini akan digunakan secara otomatis ketika template dipanggil oleh Generator Surat atau Form Response.
                </span>
                <button type="submit" class="btn btn-primary">
                    Simpan Pengaturan Mapping
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function handleSourceTypeChange(varId, type) {
    const inputKey = document.getElementById('input-key-' + varId);
    if (!inputKey) return;

    if (type === 'system') {
        if (!inputKey.value || inputKey.value.includes('nama') || inputKey.value.includes('siswa')) {
            inputKey.value = 'tanggal_surat';
        }
    } else if (type === 'setting') {
        if (!inputKey.value.startsWith('nama_instansi')) {
            inputKey.value = 'nama_instansi';
        }
    }
}
</script>
