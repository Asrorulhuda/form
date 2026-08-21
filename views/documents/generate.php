<?php
use App\Core\CSRF;
use App\Core\Session;
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header" style="padding: 24px 28px;">
            <div class="flex items-center justify-between">
                <div>
                    <h2 style="font-size: 20px; font-weight: 800; color: var(--text-primary); margin: 0;">
                        🚀 Generate Surat dari Template Word
                    </h2>
                    <p class="text-sm text-muted" style="margin: 4px 0 0;">
                        Pilih template Word (.docx), isi nilai data variable, dan sistem akan menghasilkan surat resmi.
                    </p>
                </div>
                <a href="<?= url('documents') ?>" class="btn btn-secondary btn-sm">&larr; Kembali</a>
            </div>
        </div>

        <div class="card-body" style="padding: 28px;">
            
            <!-- Step 1: Template Selection -->
            <div class="form-group mb-4">
                <label class="form-label" style="font-weight: 700;">1. Pilih Template Surat Word (.DOCX) <span class="required">*</span></label>
                <select id="template_id_select" class="form-control" onchange="window.location.href='<?= url('documents/create') ?>?template_id=' + this.value">
                    <option value="">-- Pilih Template Surat --</option>
                    <?php foreach ($templates as $t): ?>
                        <option value="<?= $t->id ?>" <?= ($selectedTemplate && $selectedTemplate->id == $t->id) ? 'selected' : '' ?>>
                            [<?= e($t->category) ?>] <?= e($t->name) ?> (v<?= (int)$t->version ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($selectedTemplate): ?>
                <form method="POST" action="<?= url('documents/store') ?>">
                    <?= CSRF::field() ?>
                    <input type="hidden" name="template_id" value="<?= $selectedTemplate->id ?>">

                    <div class="card mb-4" style="background: #f8fafc; border-color: var(--border-subtle);">
                        <div class="card-body" style="padding: 16px 20px;">
                            <div class="flex items-center justify-between">
                                <div>
                                    <strong style="color: var(--primary-700); font-size: 14px;">Template: <?= e($selectedTemplate->name) ?></strong>
                                    <div class="text-sm text-muted">Berkas: <?= e($selectedTemplate->original_filename) ?> (Versi <?= (int)$selectedTemplate->version ?>)</div>
                                </div>
                                <a href="<?= url("templates/{$selectedTemplate->id}/mapping") ?>" target="_blank" class="btn btn-secondary btn-sm">
                                    🧩 Edit Mapping
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Variable Inputs -->
                    <div style="margin-bottom: 24px;">
                        <h4 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin-bottom: 14px;">
                            2. Isi Data Variable Dokumen
                        </h4>

                        <?php if (!empty($variables)): ?>
                            <div style="display: flex; flex-direction: column; gap: 14px;">
                                <?php foreach ($variables as $v): ?>
                                    <div class="form-group mb-0">
                                        <label class="form-label" style="font-size: 13px;">
                                            <?= e($v->label ?: $v->variable_name) ?>
                                            <code style="font-size: 11px; margin-left: 4px; color: var(--primary-600);">{{<?= e($v->variable_name) ?>}}</code>
                                        </label>

                                        <?php if ($v->source_type === 'system'): ?>
                                            <input type="text" name="variables[<?= e($v->variable_name) ?>]" class="form-control" value="<?= date('d F Y') ?>" placeholder="Otomatis sistem">
                                            <div class="form-help">Sumber: Otomatis Sistem (Tanggal / Nomor). Anda dapat mengubahnya jika perlu.</div>
                                        <?php elseif ($v->source_type === 'setting'): ?>
                                            <input type="text" name="variables[<?= e($v->variable_name) ?>]" class="form-control" value="<?= e($v->default_value) ?>" placeholder="Data instansi...">
                                        <?php else: ?>
                                            <input type="text" name="variables[<?= e($v->variable_name) ?>]" class="form-control" value="<?= e($v->default_value) ?>" placeholder="Masukkan nilai data...">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Tidak ada variable terdaftar pada template ini.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center justify-between mt-5" style="padding-top: 16px; border-top: 1px solid var(--border-subtle);">
                        <button type="submit" class="btn btn-primary btn-lg" style="min-width: 200px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            Generate Surat Word (.DOCX)
                        </button>
                        <a href="<?= url('documents') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="empty-state" style="padding: 40px 20px;">
                    <p class="empty-state-title">Silakan Pilih Template Terlebih Dahulu</p>
                    <p class="empty-state-desc">Pilih salah satu template surat Word di dropdown atas untuk menampilkan form isian data variable.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
