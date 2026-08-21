<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Respons Terkirim') ?></title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%234f46e5'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>A</text></svg>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <?= adsenseHead() ?>
    <style>
        body {
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .success-card {
            max-width: 560px;
            width: 100%;
            background: #ffffff;
            border-radius: var(--radius-xl);
            border-top: 8px solid var(--success-500);
            padding: 40px 32px;
            text-align: center;
            box-shadow: var(--shadow-xl);
            border-left: 1px solid var(--border-subtle);
            border-right: 1px solid var(--border-subtle);
            border-bottom: 1px solid var(--border-subtle);
        }
    </style>
</head>
<body>

    <div class="success-card fade-in">
        <div style="width: 64px; height: 64px; background: var(--success-50); color: var(--success-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 32px;">
            ✓
        </div>

        <h1 style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">
            Respons Anda Telah Tercatat!
        </h1>

        <p style="font-size: 15px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px;">
            Terima kasih telah mengisi formulir <strong><?= e($form->title) ?></strong>. Data Anda telah berhasil dikirimkan ke sistem.
        </p>

        <?php if (!empty($docToken)): ?>
            <!-- Auto-Generated Document Callout -->
            <div class="card mb-4" style="background: #f0fdf4; border: 1px solid #bbf7d0; text-align: left; padding: 20px; border-radius: var(--radius-lg);">
                <div class="flex items-center gap-3 mb-3">
                    <span style="font-size: 28px;">📄</span>
                    <div>
                        <strong style="color: #166534; font-size: 15px;">Surat / Dokumen Resmi Telah Diterbitkan!</strong>
                        <div style="font-size: 12px; color: #15803d; margin-top: 2px;">
                            Nomor: <strong><?= e($docNumber) ?></strong> &bull; Token: <code><?= e($docToken) ?></code>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 flex-wrap mt-3">
                    <a href="<?= url("document/{$docToken}/download-docx") ?>" class="btn btn-primary btn-sm" style="flex: 1; min-width: 160px; font-weight: 700;">
                        📥 Unduh Berkas Word (.DOCX)
                    </a>
                    <a href="<?= url("document/{$docToken}") ?>" target="_blank" class="btn btn-secondary btn-sm" style="flex: 1; min-width: 140px; font-weight: 700;">
                        🖨️ Pratinjau & Cetak &nearr;
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- ─── Ad Placement ─── -->
        <?= renderAd('FORM_SUCCESS') ?>

        <div class="flex flex-col gap-3">
            <a href="<?= url($form->slug) ?>" class="btn btn-secondary w-full">
                Kirim Respons Lainnya
            </a>
            <a href="<?= url() ?>" class="text-sm" style="color: var(--text-muted); margin-top: 8px;">
                &larr; Kunjungi Beranda ASR FORM
            </a>
        </div>
    </div>

</body>
</html>
