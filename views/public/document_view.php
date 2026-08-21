<?php
use App\Services\DocxParser;
use App\Services\QrCodeService;

$htmlContent = $document->content;

// If content in DB is empty, parse on-the-fly from the generated DOCX file
if (empty($htmlContent) && !empty($document->file_path_docx)) {
    $fullDocx = BASE_PATH . '/' . $document->file_path_docx;
    if (file_exists($fullDocx)) {
        try {
            $htmlContent = DocxParser::parseToHtml($fullDocx);
        } catch (\Exception $e) {
            $htmlContent = '<div class="alert alert-warning">Dokumen Word siap diunduh. Klik tombol "Unduh Berkas Word" di atas untuk membuka berkas asli.</div>';
        }
    }
}

$verifyUrl = url("verify/{$document->verification_token}");
$qrCodeImgTag = QrCodeService::getSvg($verifyUrl, 52);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dokumen Resmi') ?> — ASR FORM</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%2316a34a'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>✓</text></svg>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>
        body {
            background-color: #f1f5f9;
            min-height: 100vh;
            padding: 24px 16px 60px;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .document-wrapper {
            max-width: 820px;
            margin: 0 auto;
        }
        .document-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-subtle);
            padding: 40px 48px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 24px;
            min-height: 480px;
            color: #0f172a;
            position: relative;
        }
        .verification-seal-box {
            margin-top: 18px;
            padding: 6px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .verification-seal-box img,
        .verification-seal-box svg {
            width: 50px !important;
            height: 50px !important;
            display: block;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .document-card {
                border: none !important;
                box-shadow: none !important;
                padding: 15px 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .verification-seal-box {
                border: 1px solid #000000 !important;
                background: #ffffff !important;
                page-break-inside: avoid;
            }
            @page {
                size: auto;
                margin: 15mm 20mm;
            }
        }
    </style>
</head>
<body>

    <div class="document-wrapper">
        <!-- ─── Action Navigation Bar (Hidden when printing) ─── -->
        <div class="card mb-4 no-print" style="background: #ffffff; border-color: var(--border-subtle); box-shadow: var(--shadow-sm);">
            <div class="card-body" style="padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="badge badge-success" style="font-size: 11px;">✓ Dokumen Sah & Terbit</span>
                        <strong style="color: var(--primary-700); font-family: monospace; font-size: 13px;"><?= e($document->document_number) ?></strong>
                    </div>
                    <div class="text-sm text-muted" style="font-size: 11px;">
                        Token: <code style="color: #4338ca; font-weight: bold;"><?= e($document->verification_token) ?></code> &bull; Tanggal: <?= date('d/m/Y H:i', strtotime($document->created_at)) ?> WIB
                    </div>
                </div>

                <div class="flex gap-2 flex-wrap">
                    <!-- Print Button -->
                    <button type="button" class="btn btn-primary btn-sm" onclick="window.print()" style="font-weight: 700;">
                        🖨️ Cetak Dokumen
                    </button>

                    <!-- Verify Link -->
                    <a href="<?= $verifyUrl ?>" target="_blank" class="btn btn-soft-primary btn-sm" title="Cek Halaman Verifikasi">
                        🛡️ Verifikasi
                    </a>

                    <!-- Download DOCX Button -->
                    <?php if (!empty($document->file_path_docx)): ?>
                        <a href="<?= url("document/{$document->verification_token}/download-docx") ?>" class="btn btn-secondary btn-sm" title="Unduh berkas Word .docx asli">
                            📥 Unduh Word (.DOCX)
                        </a>
                    <?php endif; ?>

                    <!-- Download PDF Button if exists -->
                    <?php if (!empty($document->file_path_pdf)): ?>
                        <a href="<?= url("document/{$document->verification_token}/download-pdf") ?>" target="_blank" class="btn btn-secondary btn-sm" title="Buka berkas PDF">
                            📄 PDF
                        </a>
                    <?php endif; ?>

                    <a href="<?= url() ?>" class="btn btn-secondary btn-sm">
                        Beranda
                    </a>
                </div>
            </div>
        </div>

        <!-- ─── Rendered Document Printable Content ─── -->
        <div class="document-card fade-in" id="printable-doc">
            <?php if (!empty($htmlContent)): ?>
                <?= $htmlContent ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px 20px;">
                    <div style="font-size: 40px; margin-bottom: 12px;">📄</div>
                    <h3 style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">
                        <?= e($document->title) ?>
                    </h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px;">
                        Nomor Dokumen: <strong><?= e($document->document_number) ?></strong>
                    </p>
                </div>
            <?php endif; ?>

            <!-- ─── Compact Official Digital Verification & QR Code Footer ─── -->
            <div class="verification-seal-box">
                <div style="flex: 1; line-height: 1.35;">
                    <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                        <span style="font-size: 11px; font-weight: 800; color: #15803d; background: #dcfce7; padding: 1px 6px; border-radius: 3px;">
                            ✓ TERVERIFIKASI RESMI
                        </span>
                        <span style="font-size: 10px; font-weight: 700; color: #334155; font-family: monospace;">
                            <?= e($document->document_number) ?>
                        </span>
                        <span style="font-size: 9px; color: #64748b; font-family: monospace;">
                            (Token: <?= e($document->verification_token) ?>)
                        </span>
                    </div>
                    <div style="font-size: 9.5px; color: #64748b;">
                        Keaslian berkas dapat dibuktikan dengan memindai QR Code atau tautan:
                        <a href="<?= $verifyUrl ?>" target="_blank" style="color: #4338ca; text-decoration: none; font-weight: 600; font-family: monospace;"><?= $verifyUrl ?></a>
                    </div>
                </div>

                <div style="flex-shrink: 0; text-align: center; display: flex; flex-direction: column; align-items: center;">
                    <?= $qrCodeImgTag ?>
                    <div style="font-size: 8px; color: #64748b; margin-top: 2px; font-weight: 700; letter-spacing: 0.5px;">VERIFIKASI</div>
                </div>
            </div>

        </div>

        <!-- Verification Footer Notice (Hidden when printing) -->
        <div class="text-center no-print" style="font-size: 12px; color: var(--text-muted); margin-top: 16px;">
            Diterbitkan secara resmi melalui sistem <strong style="color: var(--primary-600);">ASR FORM</strong> &bull; Token Verifikasi: <code><?= e($document->verification_token) ?></code>
        </div>
    </div>

</body>
</html>
