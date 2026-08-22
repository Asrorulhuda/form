<?php
use App\Core\Auth;
use App\Core\View;
?>

<div class="bento-grid">
    <!-- 1. Bento Hero Banner (Span 12) -->
    <div class="bento-col-12 bento-hero fade-in">
        <div class="bento-hero-content">
            <div class="bento-hero-icon">📄</div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h2 class="bento-hero-title" style="margin: 0;">
                        Template Dokumen Word (.DOCX)
                    </h2>
                    <span class="badge badge-primary" style="font-size: 11px; font-weight: 700;">
                        Total: <?= number_format($total) ?> Template
                    </span>
                </div>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Rancang dan buat surat resmi di Editor Visual atau unggah file master Word ber-tag variable <code>{{nama_variable}}</code> untuk otomasi cetak instan.
                </div>
            </div>
        </div>
        <div class="bento-hero-actions flex items-center gap-2 flex-wrap">
            <a href="<?= url('templates/editor') ?>" class="btn btn-primary btn-sm" style="box-shadow: 0 4px 14px rgba(79,70,229,0.3); font-weight: 700;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                ✍️ Buat Surat di Editor
            </a>
            <a href="<?= url('templates/create') ?>" class="btn btn-secondary btn-sm" style="font-weight: 600;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload Word (.DOCX)
            </a>
        </div>
    </div>

    <!-- 2. Bento Filter Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 16px 20px;">
        <form method="GET" action="<?= url('templates') ?>" class="filter-bar" style="margin: 0; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div class="search-input-wrapper" style="flex: 1; min-width: 240px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari nama template, kategori, atau berkas..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <select name="category" class="form-control" style="width: auto; min-width: 170px;">
                <option value="">Semua Kategori</option>
                <option value="Surat Keterangan" <?= ($filters['category'] ?? '') === 'Surat Keterangan' ? 'selected' : '' ?>>Surat Keterangan</option>
                <option value="Surat Tugas" <?= ($filters['category'] ?? '') === 'Surat Tugas' ? 'selected' : '' ?>>Surat Tugas</option>
                <option value="Surat Pernyataan" <?= ($filters['category'] ?? '') === 'Surat Pernyataan' ? 'selected' : '' ?>>Surat Pernyataan</option>
                <option value="Surat Rekomendasi" <?= ($filters['category'] ?? '') === 'Surat Rekomendasi' ? 'selected' : '' ?>>Surat Rekomendasi</option>
                <option value="Sertifikat & Piagam" <?= ($filters['category'] ?? '') === 'Sertifikat & Piagam' ? 'selected' : '' ?>>Sertifikat & Piagam</option>
                <option value="Kwitansi & Invoice" <?= ($filters['category'] ?? '') === 'Kwitansi & Invoice' ? 'selected' : '' ?>>Kwitansi & Invoice</option>
                <option value="Umum" <?= ($filters['category'] ?? '') === 'Umum' ? 'selected' : '' ?>>Umum</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;">Filter Template</button>
            <?php if (!empty($filters['search']) || !empty($filters['category'])): ?>
                <a href="<?= url('templates') ?>" class="btn btn-ghost btn-sm" style="color: var(--text-muted);">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- 3. Templates Bento Grid Cards (Span 12) -->
    <div class="bento-col-12">
        <?php if (!empty($templates)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; margin-bottom: 20px;">
                <?php foreach ($templates as $t): ?>
                    <div class="bento-card fade-in" style="display: flex; flex-direction: column; justify-content: space-between; padding: 20px; transition: transform 0.2s, box-shadow 0.2s; border: 1px solid var(--border-subtle);">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="badge badge-primary" style="font-weight: 700;"><?= e($t->category ?? 'Umum') ?></span>
                                <span class="badge badge-success" style="font-size: 10.5px; font-weight: 800;">v<?= (int)($t->version ?? 1) ?></span>
                            </div>

                            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin-bottom: 6px; line-height: 1.3;">
                                <?= e($t->name) ?>
                            </h3>

                            <?php if (!empty($t->description)): ?>
                                <p class="text-sm text-muted" style="margin-bottom: 12px; line-height: 1.4; max-height: 38px; overflow: hidden; text-overflow: ellipsis;">
                                    <?= e($t->description) ?>
                                </p>
                            <?php endif; ?>

                            <!-- File & Variable Stats Info Box -->
                            <div style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: 10px; padding: 10px 12px; margin-bottom: 14px;">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm" style="color: var(--text-secondary); font-size: 12px;">📁 File Master:</span>
                                    <span style="font-family: monospace; font-size: 11px; color: #4f46e5; font-weight: 700; max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= e($t->original_filename ?: 'template.docx') ?>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm" style="color: var(--text-secondary); font-size: 12px;">🧩 Variable Tag:</span>
                                    <span class="badge badge-primary" style="font-size: 11px;">
                                        <?= (int)$t->variable_count ?> Terdeteksi
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-sm text-muted" style="font-size: 12px; margin-bottom: 16px;">
                                <span>🔗 <strong><?= (int)$t->linked_forms_count ?></strong> Form Terkait</span>
                                <span>Oleh <?= e($t->creator_name ?? 'Admin') ?></span>
                            </div>
                        </div>

                        <div style="padding-top: 12px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 8px;">
                            <div class="flex gap-1 flex-wrap">
                                <a href="<?= url("templates/{$t->id}/edit") ?>" class="btn btn-soft-primary btn-sm" style="font-size: 12px; font-weight: 700;" title="Edit Surat di Editor Visual">
                                    ✏️ Edit
                                </a>
                                <a href="<?= url("templates/{$t->id}/mapping") ?>" class="btn btn-primary btn-sm" style="font-size: 12px; font-weight: 600;" title="Atur Mapping Sumber Data Variable">
                                    🧩 Mapping
                                </a>
                                <a href="<?= url("templates/{$t->id}/download") ?>" class="btn btn-secondary btn-sm" style="font-size: 12px;" title="Download File Word .DOCX">
                                    📥 .DOCX
                                </a>
                            </div>
                            
                            <div class="flex gap-1">
                                <form method="POST" action="<?= url("templates/{$t->id}/duplicate") ?>" style="display:inline;">
                                    <?= \App\Core\CSRF::field() ?>
                                    <button type="submit" class="btn btn-secondary btn-sm" title="Duplikasi Template" style="padding: 4px 8px;">
                                        📋
                                    </button>
                                </form>

                                <a href="<?= url("templates/{$t->id}/versions") ?>" class="btn btn-secondary btn-sm" title="Riwayat Versi" style="padding: 4px 8px;">
                                    📜
                                </a>

                                <form method="POST" action="<?= url("templates/{$t->id}/delete") ?>" style="display:inline;">
                                    <?= \App\Core\CSRF::field() ?>
                                    <button type="button" class="btn btn-danger btn-sm" data-confirm="Yakin ingin menghapus template '<?= e($t->name) ?>'?" title="Hapus Template" style="padding: 4px 8px;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="bento-card" style="padding: 12px 20px;">
                <?php View::component('pagination', [
                    'page'     => $page,
                    'lastPage' => $lastPage,
                    'total'    => $total,
                    'baseUrl'  => 'templates',
                ]); ?>
            </div>
        <?php else: ?>
            <div class="bento-card" style="padding: 48px 20px; text-align: center;">
                <div style="font-size: 48px; margin-bottom: 12px;">📄</div>
                <p class="empty-state-title" style="font-size: 16px; font-weight: 800; margin-bottom: 4px;">Belum Ada Template Dokumen Word</p>
                <p class="empty-state-desc" style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
                    Rancang surat Anda di Editor Visual Profesional atau unggah berkas Microsoft Word (.docx) pertama Anda.
                </p>
                <div class="flex items-center justify-center gap-2">
                    <a href="<?= url('templates/editor') ?>" class="btn btn-primary btn-sm">✍️ Buat Surat di Editor</a>
                    <a href="<?= url('templates/create') ?>" class="btn btn-secondary btn-sm">Upload Berkas .DOCX</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
