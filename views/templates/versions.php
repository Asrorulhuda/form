<?php
use App\Core\CSRF;
use App\Core\View;
?>

<div style="max-width: 900px; margin: 0 auto;">
    
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-body" style="padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="badge badge-primary"><?= e($template->category) ?></span>
                    <span class="badge badge-success">Versi Aktif: v<?= (int)$template->version ?></span>
                </div>
                <h2 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin: 0;">
                    Riwayat Versi: <?= e($template->name) ?>
                </h2>
                <div class="text-sm text-muted mt-1">
                    Dokumen lama yang telah terbit tetap aman dan tidak akan berubah ketika template diperbarui.
                </div>
            </div>

            <div class="flex gap-2">
                <a href="<?= url("templates/{$template->id}/mapping") ?>" class="btn btn-secondary btn-sm">
                    🧩 Mapping Variable
                </a>
                <a href="<?= url('templates') ?>" class="btn btn-secondary btn-sm">
                    &larr; Daftar Template
                </a>
            </div>
        </div>
    </div>

    <!-- Upload New Version Form -->
    <div class="card mb-4" style="border: 2px dashed var(--primary-400); background: #f8fafc;">
        <div class="card-body" style="padding: 24px;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0 0 6px;">
                🚀 Unggah Versi Baru (Bumpto v<?= (int)$template->version + 1 ?>)
            </h3>
            <p class="text-sm text-muted" style="margin: 0 0 16px;">
                Punya revisi atau tata letak Word baru untuk surat ini? Unggah file <strong>.docx</strong> baru untuk memperbarui template ke versi selanjutnya.
            </p>

            <form method="POST" action="<?= url("templates/{$template->id}/versions") ?>" enctype="multipart/form-data">
                <?= CSRF::field() ?>
                
                <div class="flex items-center gap-3 flex-wrap">
                    <input type="file" name="template_file" accept=".docx" class="form-control" style="max-width: 400px; padding: 7px 12px;" required>
                    <button type="submit" class="btn btn-primary">
                        Unggah Versi Baru (v<?= (int)$template->version + 1 ?>)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Version History Table -->
    <div class="card">
        <div class="card-header" style="padding: 16px 24px;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--text-primary); margin: 0;">
                📜 Daftar Riwayat Versi Template
            </h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Versi</th>
                        <th>File Storage</th>
                        <th>Jumlah Variable</th>
                        <th>Diunggah Oleh</th>
                        <th>Tanggal Rilis</th>
                        <th style="text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($versions)): ?>
                        <?php foreach ($versions as $ver): ?>
                            <?php $isCurrent = ($ver->version == $template->version); ?>
                            <tr style="<?= $isCurrent ? 'background: rgba(79, 70, 229, 0.03);' : '' ?>">
                                <td>
                                    <strong style="color: <?= $isCurrent ? 'var(--primary-600)' : 'var(--text-primary)' ?>; font-size: 14px;">
                                        Version <?= (int)$ver->version ?>
                                    </strong>
                                </td>
                                <td>
                                    <code style="font-size: 11px;"><?= basename($ver->file_path) ?></code>
                                </td>
                                <td>
                                    <?php $varList = json_decode($ver->variables_json ?? '[]', true) ?: []; ?>
                                    <span class="badge badge-muted"><?= count($varList) ?> variable</span>
                                </td>
                                <td><?= e($ver->creator_name ?? 'Admin') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($ver->created_at)) ?> WIB</td>
                                <td style="text-align: right;">
                                    <?php if ($isCurrent): ?>
                                        <span class="badge badge-success">Aktif Saat Ini</span>
                                    <?php else: ?>
                                        <span class="badge badge-muted">Arsip Historis</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
