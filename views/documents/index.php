<?php use App\Core\View; ?>

<!-- Header -->
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <div>
        <p class="text-sm text-muted">Total: <?= number_format($total) ?> surat & dokumen resmi yang telah digenerate</p>
    </div>
    <div class="flex gap-2">
        <a href="<?= url('templates') ?>" class="btn btn-secondary btn-sm">
            📄 Template Word (.DOCX)
        </a>
        <a href="<?= url('documents/create') ?>" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Generate Surat Baru
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body" style="padding: 16px 24px;">
        <form method="GET" action="<?= url('documents') ?>" class="filter-bar">
            <div class="search-input-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari nomor dokumen, judul, atau token..." 
                       value="<?= e($filters['search'] ?? '') ?>">
            </div>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="generated" <?= ($filters['status'] ?? '') === 'generated' ? 'selected' : '' ?>>Generated</option>
                <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
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
                    <th style="width: 170px;">Nomor Dokumen</th>
                    <th>Judul Surat / Dokumen</th>
                    <th>Template Word</th>
                    <th>Dibuat Oleh</th>
                    <th>Tanggal Terbit</th>
                    <th style="text-align: right; min-width: 200px;">Unduh Hasil</th>
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
                                <strong style="color: var(--text-primary);"><?= e($d->title) ?></strong>
                                <div class="text-sm text-muted" style="font-size: 11px;">
                                    Token: <code><?= e($d->verification_token) ?></code>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-primary"><?= e($d->template_name ?? 'Template Kustom') ?></span>
                                <span class="badge badge-muted" style="font-size: 10px;">v<?= (int)($d->template_version ?? 1) ?></span>
                            </td>
                            <td><?= e($d->creator_name ?? 'Admin') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($d->created_at)) ?></td>
                            <td style="text-align: right;">
                                <div class="flex justify-end gap-2">
                                    <!-- Download DOCX -->
                                    <?php if (!empty($d->file_path_docx)): ?>
                                        <a href="<?= url("documents/{$d->id}/download-docx") ?>" class="btn btn-primary btn-sm" title="Unduh File Word Asli (.docx)">
                                            📥 .DOCX
                                        </a>
                                    <?php endif; ?>

                                    <!-- Download PDF -->
                                    <?php if (!empty($d->file_path_pdf)): ?>
                                        <a href="<?= url("documents/{$d->id}/download-pdf") ?>" target="_blank" class="btn btn-secondary btn-sm" title="Unduh / Cetak PDF">
                                            📄 PDF
                                        </a>
                                    <?php endif; ?>

                                    <!-- Delete Button -->
                                    <form method="POST" action="<?= url("documents/{$d->id}/delete") ?>" style="display:inline;">
                                        <?= \App\Core\CSRF::field() ?>
                                        <button type="button" class="btn btn-danger btn-sm" data-confirm="Yakin ingin menghapus dokumen '<?= e($d->document_number) ?>'?" title="Hapus Dokumen">
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
                            <div class="empty-state" style="padding: 48px 20px;">
                                <div style="font-size: 48px; margin-bottom: 12px;">📄</div>
                                <p class="empty-state-title">Belum Ada Surat / Dokumen yang Digenerate</p>
                                <p class="empty-state-desc">Pilih template Word (.docx) Anda untuk mulai menghasilkan surat otomatis dengan data yang rapi.</p>
                                <a href="<?= url('documents/create') ?>" class="btn btn-primary btn-sm">Generate Surat Sekarang</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="card-footer">
        <?php View::component('pagination', [
            'page'     => $page,
            'lastPage' => $lastPage,
            'total'    => $total,
            'baseUrl'  => 'documents',
        ]); ?>
    </div>
</div>
