<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%234f46e5'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>A</text></svg>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .error-card {
            max-width: 480px;
            width: 100%;
            text-align: center;
            padding: 48px 32px;
        }
        .error-code {
            font-size: 120px;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary-600), var(--primary-400));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 8px;
        }
        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
        }
        .error-desc {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 32px;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">404</div>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        <p class="error-desc">Halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
        <a href="<?= url() ?>" class="btn btn-primary btn-lg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali ke ASR FORM
        </a>
    </div>
</body>
</html>
