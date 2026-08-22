<?php use App\Core\View; ?>

<div class="bento-grid">
    <!-- 1. Header Bento Hero Card -->
    <div class="bento-col-12 bento-hero fade-in" style="background: #ffffff; border: 1px solid var(--border-subtle);">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: var(--primary-50); color: var(--primary-700); border: 1px solid var(--primary-200);">
                📋
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-primary);">
                        Kelola Formulir Digital
                    </h2>
                    <span class="badge badge-primary" style="font-size: 11px; font-weight: 700;">
                        Total: <?= number_format($total) ?> Form
                    </span>
                </div>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Rancang formulir interaktif, bagikan tautan publik, dan terima data respons seketika.
                </div>
            </div>
        </div>
        <div class="bento-hero-actions">
            <a href="<?= url('forms/create') ?>" class="btn btn-primary btn-sm" style="box-shadow: 0 4px 12px rgba(79,70,229,0.25); font-weight: 600;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Buat Formulir Baru
            </a>
        </div>
    </div>

    <!-- 2. Bento Filter Card (Span 12) -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 16px 20px;">
        <form method="GET" action="<?= url('forms') ?>" class="filter-bar" style="margin: 0; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div class="search-input-wrapper" style="flex: 1; min-width: 240px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari judul formulir, slug, atau deskripsi..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <select name="status" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">Semua Status</option>
                <option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="closed" <?= ($filters['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;">Filter Data</button>
            <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
                <a href="<?= url('forms') ?>" class="btn btn-ghost btn-sm" style="color: var(--text-muted);">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- 3. Bento Table Card (Span 12) -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="margin: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul Formulir</th>
                        <th>Slug URL Publik</th>
                        <th>Status</th>
                        <th>Field Input</th>
                        <th>Respons Masuk</th>
                        <th>Pembuat</th>
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
                                    <code style="background: var(--primary-50); color: var(--primary-700); padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
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
                                    <a href="<?= url("responses?form_id={$f->id}") ?>" class="badge badge-primary" style="text-decoration: none;">
                                        📊 <?= (int)$f->response_count ?> respons
                                    </a>
                                </td>
                                <td><span class="text-sm text-muted"><?= e($f->creator_name ?? 'Admin') ?></span></td>
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
                                <div class="empty-state" style="padding: 48px 20px; text-align: center;">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--text-muted); margin-bottom: 12px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <p class="empty-state-title" style="font-weight: 800; font-size: 16px; margin-bottom: 4px;">Belum ada formulir</p>
                                    <p class="empty-state-desc" style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">Buat formulir digital kustom pertama Anda sekarang.</p>
                                    <a href="<?= url('forms/create') ?>" class="btn btn-primary btn-sm">Buat Formulir Baru</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
