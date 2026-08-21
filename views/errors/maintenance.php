<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 — Layanan Sedang Pemeliharaan</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23f59e0b'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>⚙</text></svg>">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #fffbeb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
        }
        .card {
            max-width: 520px;
            width: 100%;
            text-align: center;
            padding: 48px 32px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid #fef3c7;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px auto;
            background: #fef3c7;
            color: #d97706;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 12px;
        }
        p {
            font-size: 15px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .debug-alert {
            text-align: left;
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 24px;
            font-family: monospace;
            word-break: break-all;
        }
        .btn-reload {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #d97706;
            color: #ffffff;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn-reload:hover {
            background: #b45309;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-circle">⚙️</div>
        <h1>Sistem Sedang Pemeliharaan</h1>
        <p>Layanan kami sedang dalam proses sinkronisasi database atau pemeliharaan berkala. Silakan muat ulang halaman dalam beberapa saat.</p>
        
        <?php if (!empty($dbError)): ?>
            <div class="debug-alert">
                <strong>Debug Info:</strong> <?= htmlspecialchars($dbError) ?>
            </div>
        <?php endif; ?>

        <button onclick="window.location.reload()" class="btn-reload">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
            Muat Ulang Halaman
        </button>
    </div>
</body>
</html>
