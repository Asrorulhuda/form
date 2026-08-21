<?php use App\Core\View; ?>

<!-- Header -->
<div class="page-header-actions">
    <div>
        <h2 style="font-size: 20px; font-weight: 800; color: var(--text-primary); margin: 0 0 4px 0;">Daftar Formulir</h2>
        <p class="text-sm text-muted" style="margin: 0;">Total: <?= number_format($total) ?> formulir</p>
    </div>
    <a href="<?= url('forms/create') ?>" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Buat Formulir Baru
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body" style="padding: 16px 24px;">
        <form method="GET" action="<?= url('forms') ?>" class="filter-bar">
            <div class="search-input-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari judul formulir..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="closed" <?= ($filters['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul Form</th>
                    <th>Slug URL</th>
                    <th>Status</th>
                    <th>Field</th>
                    <th>Respons</th>
                    <th>Dibuat Oleh</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($forms)): ?>
                    <?php foreach ($forms as $f): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--text-primary); font-size: 14px;"><?= e($f->title) ?></strong>
                                <?php if ($f->description): ?>
                                    <p class="text-sm text-muted" style="margin: 2px 0 0; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= e($f->description) ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code style="background: var(--primary-50); color: var(--primary-700); padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                                    /<?= e($f->slug) ?>
                                </code>
                            </td>
                            <td>
                                <?php if ($f->status === 'published'): ?>
                                    <span class="badge badge-success">Published</span>
                                <?php elseif ($f->status === 'draft'): ?>
                                    <span class="badge badge-warning">Draft</span>
                                <?php else: ?>
                                    <span class="badge badge-muted"><?= e(ucfirst($f->status)) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge badge-muted"><?= (int)$f->field_count ?> field</span></td>
                            <td>
                                <a href="<?= url("responses?form_id={$f->id}") ?>" class="badge badge-primary">
                                    <?= (int)$f->response_count ?> respons
                                </a>
                            </td>
                            <td><?= e($f->creator_name ?? 'Admin') ?></td>
                            <td style="text-align: right;">
                                <div class="flex justify-end gap-2 items-center" style="white-space: nowrap;">
                                    <a href="<?= url("forms/{$f->id}/builder") ?>" class="btn btn-primary btn-sm" title="Edit di Visual Builder">
                                        🛠️ Builder
                                    </a>
                                    <a href="<?= url($f->slug) ?>" target="_blank" class="btn btn-secondary btn-sm" title="Buka Form Publik">
                                        🔗 Publik
                                    </a>
                                    <a href="<?= url("forms/{$f->id}/responses") ?>" class="btn btn-secondary btn-sm" title="Lihat Data Respons">
                                        📊 (<?= (int)$f->response_count ?>)
                                    </a>
                                    <form method="POST" action="<?= url("forms/{$f->id}/delete") ?>" style="display:inline;">
                                        <?= \App\Core\CSRF::field() ?>
                                        <button type="button" class="btn btn-danger btn-sm" data-confirm="Yakin ingin menghapus form '<?= e($f->title) ?>'?">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                <p class="empty-state-title">Belum ada formulir</p>
                                <p class="empty-state-desc">Buat formulir digital kustom pertama Anda sekarang.</p>
                                <a href="<?= url('forms/create') ?>" class="btn btn-primary btn-sm">Buat Formulir</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
