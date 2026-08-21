<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keabsahan Dokumen Resmi — ASR FORM</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%2316a34a'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>✓</text></svg>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>
        body {
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px 60px;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .verify-card {
            max-width: 640px;
            width: 100%;
            background: #ffffff;
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
        }
        .verify-header {
            background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);
            color: #ffffff;
            padding: 32px 28px 28px;
            text-align: center;
        }
        .seal-badge {
            width: 72px;
            height: 72px;
            background: #ffffff;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            font-weight: 900;
            margin: 0 auto 16px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: var(--text-secondary);
            font-weight: 600;
        }
        .detail-val {
            color: var(--text-primary);
            font-weight: 700;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="verify-card fade-in">
        <!-- ─── Official Header Seal ─── -->
        <div class="verify-header">
            <div class="seal-badge">✓</div>
            <h1 style="font-size: 22px; font-weight: 800; margin: 0 0 6px;">
                DOKUMEN RESMI TERVERIFIKASI
            </h1>
            <p style="font-size: 13px; opacity: 0.92; margin: 0; line-height: 1.5;">
                Dokumen ini sah, valid, dan tercatat resmi di database sistem informasi <strong>ASR FORM</strong>.
            </p>
        </div>

        <!-- ─── Verification Details ─── -->
        <div style="padding: 28px;">
            <div class="card mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 18px 20px; border-radius: var(--radius-lg);">
                <div class="detail-row">
                    <span class="detail-label">Status Keabsahan</span>
                    <span class="detail-val">
                        <span class="badge badge-success" style="font-size: 12px; padding: 4px 10px;">
                            ✓ SAH & VALID
                        </span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Nomor Dokumen</span>
                    <span class="detail-val" style="font-family: monospace; color: var(--primary-700); font-size: 14px;">
                        <?= e($document->document_number) ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Judul Dokumen</span>
                    <span class="detail-val">
                        <?= e($document->title) ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Token Keamanan</span>
                    <span class="detail-val" style="font-family: monospace; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 4px;">
                        <?= e($document->verification_token) ?>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Tanggal Terbit</span>
                    <span class="detail-val">
                        <?= date('d F Y, H:i', strtotime($document->created_at)) ?> WIB
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Format Master</span>
                    <span class="detail-val">
                        Microsoft Word (.DOCX) v<?= (int)$document->template_version_id ?>
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 flex-wrap mb-3">
                <a href="<?= url("document/{$document->verification_token}") ?>" class="btn btn-primary w-full" style="justify-content: center; font-weight: 700; padding: 12px;">
                    👁️ Lihat & Cetak Dokumen Resmi
                </a>

                <?php if (!empty($document->file_path_docx)): ?>
                    <a href="<?= url("document/{$document->verification_token}/download-docx") ?>" class="btn btn-secondary w-full" style="justify-content: center; font-weight: 600;">
                        📥 Unduh Berkas Word Asli (.DOCX)
                    </a>
                <?php endif; ?>
            </div>

            <div class="text-center" style="font-size: 12px; color: var(--text-muted); margin-top: 18px; line-height: 1.5;">
                Diverifikasi secara aman &bull; Sistem Generator Surat Elektronik <strong style="color: var(--primary-600);">ASR FORM</strong><br>
                Hak Cipta Dilindungi Undang-Undang.
            </div>
        </div>
    </div>

</body>
</html>
