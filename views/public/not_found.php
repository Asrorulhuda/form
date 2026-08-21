<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Tidak Ditemukan</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%234f46e5'/><text x='50' y='68' font-size='55' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial'>A</text></svg>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>
        body { background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .box { max-width: 500px; width: 100%; background: #ffffff; border-radius: var(--radius-xl); padding: 40px 32px; text-align: center; box-shadow: var(--shadow-xl); border: 1px solid var(--border-subtle); }
    </style>
</head>
<body>
    <div class="box fade-in">
        <div style="font-size: 48px; margin-bottom: 16px;">🔍</div>
        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px;">Formulir Tidak Ditemukan</h1>
        <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 24px;">Link formulir yang Anda tuju mungkin sudah dihapus, diubah slug-nya, atau tidak tersedia.</p>
        <a href="<?= url() ?>" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
</body>
</html>
