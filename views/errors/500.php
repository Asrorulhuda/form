<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Terjadi Kesalahan Sistem</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23ef4444'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>!</text></svg>">
    <link rel="stylesheet" href="<?= function_exists('asset') ? asset('css/app.css') : '/assets/css/app.css' ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #fef2f2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1e293b;
        }
        .error-card {
            max-width: 600px;
            width: 100%;
            text-align: center;
            padding: 48px 32px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid #fee2e2;
        }
        .error-code {
            font-size: 100px;
            font-weight: 900;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 8px;
        }
        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .error-desc {
            font-size: 15px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .debug-box {
            text-align: left;
            background: #0f172a;
            color: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            overflow-x: auto;
            max-height: 240px;
            margin-bottom: 24px;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #4f46e5;
            color: #ffffff;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-action:hover {
            background: #4338ca;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">500</div>
        <h1 class="error-title">Terjadi Kesalahan pada Server</h1>
        <p class="error-desc">Maaf, sistem sedang mengalami kendala teknis internal. Administrator telah diberitahu dan sedang menanganinya.</p>
        
        <?php if (!empty($debug) && !empty($exception)): ?>
            <div class="debug-box">
                <strong>Exception:</strong> <?= htmlspecialchars($exception->getMessage()) ?><br><br>
                <strong>File:</strong> <?= htmlspecialchars($exception->getFile()) ?> : <?= $exception->getLine() ?><br><br>
                <strong>Trace:</strong><br>
                <?= nl2br(htmlspecialchars($exception->getTraceAsString())) ?>
            </div>
        <?php endif; ?>

        <a href="<?= function_exists('url') ? url() : '/' ?>" class="btn-action">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
