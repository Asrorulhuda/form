<?php use App\Core\View; ?>

<!-- Header -->
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <div>
        <p class="text-sm text-muted">Total: <?= number_format($total) ?> template surat Microsoft Word (.DOCX) aktif</p>
    </div>
    <a href="<?= url('templates/create') ?>" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Upload Template Word (.DOCX)
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body" style="padding: 16px 24px;">
        <form method="GET" action="<?= url('templates') ?>" class="filter-bar">
            <div class="search-input-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari nama template, kategori, atau deskripsi..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <select name="category" class="form-control">
                <option value="">Semua Kategori</option>
                <option value="Surat Keterangan" <?= ($filters['category'] ?? '') === 'Surat Keterangan' ? 'selected' : '' ?>>Surat Keterangan</option>
                <option value="Surat Tugas" <?= ($filters['category'] ?? '') === 'Surat Tugas' ? 'selected' : '' ?>>Surat Tugas</option>
                <option value="Surat Pernyataan" <?= ($filters['category'] ?? '') === 'Surat Pernyataan' ? 'selected' : '' ?>>Surat Pernyataan</option>
                <option value="Surat Rekomendasi" <?= ($filters['category'] ?? '') === 'Surat Rekomendasi' ? 'selected' : '' ?>>Surat Rekomendasi</option>
                <option value="Sertifikat & Piagam" <?= ($filters['category'] ?? '') === 'Sertifikat & Piagam' ? 'selected' : '' ?>>Sertifikat & Piagam</option>
                <option value="Kwitansi & Invoice" <?= ($filters['category'] ?? '') === 'Kwitansi & Invoice' ? 'selected' : '' ?>>Kwitansi & Invoice</option>
                <option value="Umum" <?= ($filters['category'] ?? '') === 'Umum' ? 'selected' : '' ?>>Umum</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>
</div>

<!-- Templates Grid -->
<?php if (!empty($templates)): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px; margin-bottom: 24px;">
        <?php foreach ($templates as $t): ?>
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s, box-shadow 0.2s;">
                <div class="card-body" style="padding: 24px;">
                    <div class="flex items-center justify-between mb-3">
                        <span class="badge badge-primary"><?= e($t->category ?? 'Umum') ?></span>
                        <span class="badge badge-success">v<?= (int)($t->version ?? 1) ?></span>
                    </div>

                    <h3 style="font-size: 17px; font-weight: 800; color: var(--text-primary); margin-bottom: 6px; line-height: 1.3;">
                        <?= e($t->name) ?>
                    </h3>

                    <?php if (!empty($t->description)): ?>
                        <p class="text-sm text-muted" style="margin-bottom: 14px; line-height: 1.5; max-height: 40px; overflow: hidden; text-overflow: ellipsis;">
                            <?= e($t->description) ?>
                        </p>
                    <?php endif; ?>

                    <!-- File & Variable Stats Info Box -->
                    <div style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: 8px; padding: 12px 14px; margin-bottom: 16px;">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm" style="color: var(--text-secondary);">📁 Berkas Asli:</span>
                            <span style="font-family: monospace; font-size: 11px; color: #4f46e5; font-weight: 700; max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?= e($t->original_filename ?: 'template.docx') ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm" style="color: var(--text-secondary);">🧩 Variable:</span>
                            <span class="badge badge-primary" style="font-size: 11px;">
                                <?= (int)$t->variable_count ?> Terdeteksi
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm text-muted">
                        <span>🔗 Digunakan di <strong><?= (int)$t->linked_forms_count ?></strong> Form</span>
                        <span>Dibuat oleh <?= e($t->creator_name ?? 'Admin') ?></span>
                    </div>
                </div>

                <div class="card-footer" style="padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; background: #ffffff; border-top: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 8px;">
                    <div class="flex gap-2 flex-wrap">
                        <a href="<?= url("templates/{$t->id}/mapping") ?>" class="btn btn-primary btn-sm" title="Atur Mapping Sumber Data Variable">
                            🧩 Mapping Variable
                        </a>
                        <a href="<?= url("templates/{$t->id}/download") ?>" class="btn btn-secondary btn-sm" title="Download File Word .DOCX">
                            📥 .DOCX
                        </a>
                    </div>
                    
                    <div class="flex gap-1">
                        <!-- Duplicate Button -->
                        <form method="POST" action="<?= url("templates/{$t->id}/duplicate") ?>" style="display:inline;">
                            <?= \App\Core\CSRF::field() ?>
                            <button type="submit" class="btn btn-secondary btn-sm" title="Duplikasi Template">
                                📋
                            </button>
                        </form>

                        <!-- Versions Button -->
                        <a href="<?= url("templates/{$t->id}/versions") ?>" class="btn btn-secondary btn-sm" title="Riwayat Versi">
                            📜
                        </a>

                        <!-- Delete Button -->
                        <form method="POST" action="<?= url("templates/{$t->id}/delete") ?>" style="display:inline;">
                            <?= \App\Core\CSRF::field() ?>
                            <button type="button" class="btn btn-danger btn-sm" data-confirm="Yakin ingin menghapus template '<?= e($t->name) ?>'?" title="Hapus Template">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="card">
        <div class="card-footer">
            <?php View::component('pagination', [
                'page'     => $page,
                'lastPage' => $lastPage,
                'total'    => $total,
                'baseUrl'  => 'templates',
            ]); ?>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="empty-state" style="padding: 48px 20px;">
            <div style="font-size: 48px; margin-bottom: 12px;">📄</div>
            <p class="empty-state-title">Belum Ada Template Surat Word</p>
            <p class="empty-state-desc">Upload berkas Microsoft Word (.docx) pertama Anda dengan format variable <code>{{nama_variable}}</code>.</p>
            <a href="<?= url('templates/create') ?>" class="btn btn-primary btn-sm">Upload Template Word Sekarang</a>
        </div>
    </div>
<?php endif; ?>
