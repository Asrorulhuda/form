<?php use App\Core\View; ?>

<div class="bento-grid">
    <!-- 1. Header Bento Hero Card -->
    <div class="bento-col-12 bento-hero fade-in" style="background: #ffffff; border: 1px solid var(--border-subtle);">
        <div class="bento-hero-left">
            <div class="bento-hero-avatar" style="background: var(--success-50); color: var(--success-700); border: 1px solid rgba(16,185,129,0.3);">
                🔏
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <h2 class="bento-hero-title" style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-primary);">
                        Surat &amp; Dokumen Resmi Sah
                    </h2>
                    <span class="badge badge-success" style="font-size: 11px; font-weight: 700;">
                        Total: <?= number_format($total) ?> Dokumen
                    </span>
                </div>
                <div class="bento-hero-desc" style="font-size: 13px; color: var(--text-muted);">
                    Arsip penerbitan surat otomatis berpenomoran romawi instan, berkas .DOCX / PDF, dan QR Code verifikasi.
                </div>
            </div>
        </div>
        <div class="bento-hero-actions">
            <a href="<?= url('templates') ?>" class="btn btn-secondary btn-sm" style="font-weight: 600;">
                📄 Master Template
            </a>
            <a href="<?= url('documents/create') ?>" class="btn btn-primary btn-sm" style="box-shadow: 0 4px 12px rgba(79,70,229,0.25); font-weight: 600;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Generate Dokumen Baru
            </a>
        </div>
    </div>

    <!-- 2. Bento Filter Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 16px 20px;">
        <form method="GET" action="<?= url('documents') ?>" class="filter-bar" style="margin: 0; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div class="search-input-wrapper" style="flex: 1; min-width: 240px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari nomor dokumen, judul, atau token..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <select name="status" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">Semua Status</option>
                <option value="generated" <?= ($filters['status'] ?? '') === 'generated' ? 'selected' : '' ?>>Generated</option>
                <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;">Filter Data</button>
            <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
                <a href="<?= url('documents') ?>" class="btn btn-ghost btn-sm" style="color: var(--text-muted);">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- 3. Bento Table Card -->
    <div class="bento-col-12 bento-card fade-in" style="padding: 0; overflow: hidden;">
        <div class="table-container" style="margin: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 180px;">Nomor Dokumen</th>
                        <th>Perihal / Judul Surat</th>
                        <th>Template Word Terkait</th>
                        <th>Diterbitkan Oleh</th>
                        <th>Tanggal Terbit</th>
                        <th style="text-align: right; min-width: 190px;">Unduh Berkas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($documents)): ?>
                        <?php foreach ($documents as $d): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--primary-700); font-family: monospace; font-size: 13px;">
                                        <?= e($d->document_number ?? 'DRAFT') ?>
                                    </strong>
                                </td>
                                <td>
                                    <strong style="color: var(--text-primary); font-size: 13.5px;"><?= e($d->title) ?></strong>
                                    <div class="text-sm text-muted" style="font-size: 11px; margin-top: 2px;">
                                        Token: <code><?= e($d->verification_token) ?></code>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-primary" style="font-weight: 600;"><?= e($d->template_name ?? 'Template Kustom') ?></span>
                                    <span class="badge badge-muted" style="font-size: 10px;">v<?= (int)($d->template_version ?? 1) ?></span>
                                </td>
                                <td><span class="text-sm text-muted"><?= e($d->creator_name ?? 'Admin') ?></span></td>
                                <td><span class="text-sm"><?= date('d/m/Y H:i', strtotime($d->created_at)) ?></span></td>
                                <td style="text-align: right;">
                                    <div class="flex justify-end gap-2">
                                        <?php if (!empty($d->file_path_docx)): ?>
                                            <a href="<?= url("documents/{$d->id}/download-docx") ?>" class="btn btn-primary btn-sm" title="Unduh File Word Asli (.docx)" style="font-size: 12px; font-weight: 600;">
                                                📥 .DOCX
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($d->file_path_pdf)): ?>
                                            <a href="<?= url("documents/{$d->id}/download-pdf") ?>" target="_blank" class="btn btn-secondary btn-sm" title="Unduh / Cetak PDF" style="font-size: 12px; font-weight: 600;">
                                                📄 PDF
                                            </a>
                                        <?php endif; ?>

                                        <form method="POST" action="<?= url("documents/{$d->id}/delete") ?>" style="display:inline;">
                                            <?= \App\Core\CSRF::field() ?>
                                            <button type="button" class="btn btn-danger btn-sm" data-confirm="Yakin ingin menghapus dokumen '<?= e($d->document_number) ?>'?" title="Hapus Dokumen" style="padding: 4px 8px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state" style="padding: 48px 20px; text-align: center;">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--text-muted); margin-bottom: 12px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <p class="empty-state-title" style="font-size: 16px; font-weight: 800; margin-bottom: 4px;">Belum Ada Dokumen Resmi Diterbitkan</p>
                                    <p class="empty-state-desc" style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">Generate surat atau sertifikat resmi pertama Anda dari template Word yang telah terpasang.</p>
                                    <a href="<?= url('documents/create') ?>" class="btn btn-primary btn-sm">Generate Surat Baru</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($documents)): ?>
            <div style="padding: 12px 20px; border-top: 1px solid var(--border-subtle);">
                <?php View::component('pagination', [
                    'page'     => $page,
                    'lastPage' => $lastPage,
                    'total'    => $total,
                    'baseUrl'  => 'documents',
                ]); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
